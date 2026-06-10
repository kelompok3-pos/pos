<?php

final class RetailTransactionService
{
    public function __construct(private PDO $pdo, private ActorContext $actor)
    {
    }

    public function calculate(array $payload, bool $lock = false): array
    {
        $storeId = $this->actor->requireStoreId();
        $items = is_array($payload['items'] ?? null) ? $payload['items'] : [];
        if ($items === []) {
            throw new InvalidArgumentException('Keranjang masih kosong.');
        }
        $priceColumn = $this->hasColumn('products', 'selling_price') ? 'selling_price' : 'price';
        $deletedFilter = $this->hasColumn('products', 'deleted_at') ? ' AND deleted_at IS NULL' : '';
        $sql = "SELECT id, name, " . ($this->hasColumn('products', 'sku') ? 'sku' : "CONCAT('PRD-', id) AS sku") .
            ", {$priceColumn} AS selling_price, stock, " .
            ($this->hasColumn('products', 'unit') ? 'unit' : "'pcs' AS unit") .
            " FROM products WHERE id = ? AND store_id = ? AND status = 'active'{$deletedFilter}" .
            ($lock ? ' FOR UPDATE' : '');
        $stmt = $this->pdo->prepare($sql);
        $validated = [];
        $grossSubtotal = 0.0;
        $itemDiscountTotal = 0.0;
        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['qty'] ?? $item['quantity'] ?? 0);
            if ($productId <= 0 || $quantity <= 0) {
                throw new InvalidArgumentException('Item transaksi tidak valid.');
            }
            $stmt->execute([$productId, $storeId]);
            $product = $stmt->fetch();
            if (!$product) {
                throw new UnauthorizedException('Produk tidak tersedia untuk toko ini.');
            }
            if ((int) $product['stock'] < $quantity) {
                throw new RuntimeException("Stok {$product['name']} tidak mencukupi.");
            }
            $gross = (float) $product['selling_price'] * $quantity;
            $discount = max(0, min((float) ($item['item_discount'] ?? 0), $gross));
            $grossSubtotal += $gross;
            $itemDiscountTotal += $discount;
            $validated[] = [
                'product_id' => (int) $product['id'],
                'name' => (string) $product['name'],
                'sku' => (string) $product['sku'],
                'unit' => (string) $product['unit'],
                'unit_price' => (float) $product['selling_price'],
                'stock_before' => (int) $product['stock'],
                'quantity' => $quantity,
                'item_discount' => $discount,
                'subtotal' => $gross - $discount,
            ];
        }
        $transactionDiscount = max(0, min((float) ($payload['transaction_discount'] ?? 0), $grossSubtotal - $itemDiscountTotal));
        $taxAmount = 0.0;
        $discountTotal = $itemDiscountTotal + $transactionDiscount;
        $total = max(0, $grossSubtotal - $discountTotal + $taxAmount);
        $cashReceived = max(0, (float) ($payload['cash_received'] ?? 0));
        return [
            'subtotal' => $grossSubtotal,
            'discount_total' => $discountTotal,
            'transaction_discount' => $transactionDiscount,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'change_amount' => max(0, $cashReceived - $total),
            'cash_received' => $cashReceived,
            'items_validated' => $validated,
            'errors' => [],
        ];
    }

    public function submit(array $payload): int
    {
        if (!$this->hasColumn('transactions', 'payment_method_id')) {
            throw new RuntimeException('API transaksi retail memerlukan migration retail terbaru.');
        }
        $storeId = $this->actor->requireStoreId();
        $this->pdo->beginTransaction();
        try {
            $shiftStmt = $this->pdo->prepare(
                "SELECT id FROM cashier_shifts
                 WHERE store_id = ? AND kasir_id = ? AND status = 'open'
                 ORDER BY id DESC LIMIT 1 FOR UPDATE"
            );
            $shiftStmt->execute([$storeId, $this->actor->user_id]);
            $shiftId = (int) $shiftStmt->fetchColumn();
            if ($shiftId <= 0) {
                throw new RuntimeException('Buka shift sebelum memproses transaksi.');
            }
            $paymentId = (int) ($payload['payment_method_id'] ?? 0);
            $paymentStmt = $this->pdo->prepare('SELECT type FROM payment_methods WHERE id = ? AND is_active = 1 LIMIT 1');
            $paymentStmt->execute([$paymentId]);
            $paymentType = $paymentStmt->fetchColumn();
            if (!$paymentType) {
                throw new InvalidArgumentException('Metode pembayaran tidak valid.');
            }
            $calculation = $this->calculate($payload, true);
            if ($paymentType === 'cash' && $calculation['cash_received'] < $calculation['total']) {
                throw new RuntimeException('Uang diterima kurang dari total transaksi.');
            }
            $cashReceived = $paymentType === 'cash' ? $calculation['cash_received'] : null;
            $changeAmount = $paymentType === 'cash' ? $calculation['change_amount'] : 0;
            $stmt = $this->pdo->prepare(
                'INSERT INTO transactions
                 (store_id, shift_id, kasir_id, payment_method_id, subtotal, discount_amount, tax_amount,
                  total, cash_received, change_amount, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "completed")'
            );
            $stmt->execute([
                $storeId, $shiftId, $this->actor->user_id, $paymentId, $calculation['subtotal'],
                $calculation['discount_total'], $calculation['tax_amount'], $calculation['total'],
                $cashReceived, $changeAmount,
            ]);
            $transactionId = (int) $this->pdo->lastInsertId();
            $itemStmt = $this->pdo->prepare(
                'INSERT INTO transaction_items
                 (store_id, transaction_id, product_id, product_name, product_sku, unit_price, quantity, discount_amount, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stockStmt = $this->pdo->prepare(
                'UPDATE products SET stock = stock - ? WHERE id = ? AND store_id = ? AND stock >= ?'
            );
            $movementStmt = $this->pdo->prepare(
                'INSERT INTO stock_movements
                 (store_id, product_id, user_id, type, quantity_before, quantity_change, quantity_after, reason, reference_id)
                 VALUES (?, ?, ?, "sale", ?, ?, ?, ?, ?)'
            );
            foreach ($calculation['items_validated'] as $item) {
                $itemStmt->execute([
                    $storeId, $transactionId, $item['product_id'], $item['name'], $item['sku'],
                    $item['unit_price'], $item['quantity'], $item['item_discount'], $item['subtotal'],
                ]);
                $stockStmt->execute([$item['quantity'], $item['product_id'], $storeId, $item['quantity']]);
                if ($stockStmt->rowCount() !== 1) {
                    throw new RuntimeException('Stok berubah saat checkout. Silakan hitung ulang.');
                }
                $movementStmt->execute([
                    $storeId, $item['product_id'], $this->actor->user_id, $item['stock_before'],
                    -$item['quantity'], $item['stock_before'] - $item['quantity'], 'Penjualan', $transactionId,
                ]);
            }
            $shiftUpdate = $this->pdo->prepare(
                'UPDATE cashier_shifts SET total_transactions = total_transactions + 1,
                 total_revenue = total_revenue + ? WHERE id = ? AND store_id = ? AND status = "open"'
            );
            $shiftUpdate->execute([$calculation['total'], $shiftId, $storeId]);
            $this->pdo->commit();
            AuditLogger::log($this->actor, 'CREATE', 'transactions', $transactionId, null, $calculation, $this->pdo);
            return $transactionId;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    private function hasColumn(string $table, string $column): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?'
        );
        $stmt->execute([$table, $column]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
