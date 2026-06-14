<?php

final class TransactionService
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
        $sql = "SELECT id, name, CONCAT('PRD-', id) AS sku, price AS selling_price, stock, 'pcs' AS unit
            FROM products WHERE id = ? AND store_id = ? AND status = 'active' AND deleted_at IS NULL" .
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
            $paymentType = strtolower(trim((string) ($payload['payment_method'] ?? 'cash')));
            if (!in_array($paymentType, ['cash', 'qris', 'card'], true)) {
                throw new InvalidArgumentException('Metode pembayaran tidak valid.');
            }
            $calculation = $this->calculate($payload, true);
            if ($paymentType === 'cash' && $calculation['cash_received'] < $calculation['total']) {
                throw new RuntimeException('Uang diterima kurang dari total transaksi.');
            }
            $cashReceived = $paymentType === 'cash' ? $calculation['cash_received'] : $calculation['total'];
            $changeAmount = $paymentType === 'cash' ? $calculation['change_amount'] : 0;
            $transactionCode = $this->generateTransactionCode($storeId);
            $stmt = $this->pdo->prepare(
                'INSERT INTO transactions
                 (store_id, transaction_code, cashier_id, subtotal, tax_amount, total_price,
                  paid_amount, change_amount, payment_method)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $storeId, $transactionCode, $this->actor->user_id, $calculation['subtotal'],
                $calculation['tax_amount'], $calculation['total'], $cashReceived, $changeAmount, $paymentType,
            ]);
            $transactionId = (int) $this->pdo->lastInsertId();
            $itemStmt = $this->pdo->prepare(
                'INSERT INTO transaction_items
                 (store_id, transaction_id, product_name, quantity, subtotal)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stockStmt = $this->pdo->prepare(
                'UPDATE products SET stock = stock - ? WHERE id = ? AND store_id = ? AND stock >= ?'
            );
            $movementStmt = $this->pdo->prepare(
                'INSERT INTO stock_movements
                 (store_id, product_id, user_id, movement_type, quantity, stock_before, stock_after, note)
                 VALUES (?, ?, ?, "sale", ?, ?, ?, ?)'
            );
            foreach ($calculation['items_validated'] as $item) {
                $itemStmt->execute([
                    $storeId, $transactionId, $item['name'], $item['quantity'], $item['subtotal'],
                ]);
                $stockStmt->execute([$item['quantity'], $item['product_id'], $storeId, $item['quantity']]);
                if ($stockStmt->rowCount() !== 1) {
                    throw new RuntimeException('Stok berubah saat checkout. Silakan hitung ulang.');
                }
                $movementStmt->execute([
                    $storeId, $item['product_id'], $this->actor->user_id, $item['quantity'],
                    $item['stock_before'], $item['stock_before'] - $item['quantity'], $transactionCode,
                ]);
            }
            $shiftUpdate = $this->pdo->prepare(
                'UPDATE cashier_shifts SET total_transactions = total_transactions + 1
                 WHERE id = ? AND store_id = ? AND status = "open"'
            );
            $shiftUpdate->execute([$shiftId, $storeId]);
            if ($shiftUpdate->rowCount() !== 1) {
                throw new RuntimeException('Shift tidak lagi aktif.');
            }
            $this->pdo->commit();
            try {
                AuditLogger::log($this->actor, 'CREATE', 'transactions', $transactionId, null, $calculation, $this->pdo);
            } catch (Throwable $auditException) {
                error_log($auditException->__toString());
            }
            return $transactionId;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    private function generateTransactionCode(int $storeId): string
    {
        $stmt = $this->pdo->prepare(
            'SELECT transaction_code FROM transactions
             WHERE store_id = ? AND transaction_code LIKE ? ORDER BY id DESC LIMIT 1'
        );
        $prefix = date('Ymd');
        $stmt->execute([$storeId, "INV-{$prefix}-%"]);
        $last = (string) $stmt->fetchColumn();
        preg_match('/^INV-\d{8}-(\d+)$/', $last, $matches);
        return sprintf('INV-%s-%04d', $prefix, ((int) ($matches[1] ?? 0)) + 1);
    }
}
