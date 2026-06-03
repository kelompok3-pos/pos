<?php

/**
 * =================================================================
 * MODEL: TRANSACTION
 * =================================================================
 * Class to access `transactions` and `transaction_items` tables.
 *
 * Usage:
 *   $transaction = new Transaction();
 *   $today = $transaction->getToday();
 * =================================================================
 */

class Transaction
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    /**
     * Get all transaction headers (joined with users as cashier)
     *
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            "SELECT t.*, u.name AS cashier_name
             FROM transactions t
             LEFT JOIN users u ON t.cashier_id = u.id
             ORDER BY t.id DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Get today's transactions
     *
     * @return array
     */
    public function getToday(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT t.*, u.name AS cashier_name
             FROM transactions t
             LEFT JOIN users u ON t.cashier_id = u.id
             WHERE DATE(t.created_at) = CURDATE()
             ORDER BY t.id DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get a single transaction by ID
     *
     * @param int $id
     * @return array|false
     */
    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT t.*, u.name AS cashier_name
             FROM transactions t
             LEFT JOIN users u ON t.cashier_id = u.id
             WHERE t.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get transaction items by transaction header ID
     *
     * @param int $transactionId
     * @return array
     */
    public function getDetails(int $transactionId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM transaction_items WHERE transaction_id = ?"
        );
        $stmt->execute([$transactionId]);
        return $stmt->fetchAll();
    }

    /**
     * Save new transaction, items, and stock updates atomically
     *
     * @param int   $cashierId   Cashier ID from session
     * @param array $items       [['product_id' => 1, 'name' => 'Kopi', 'price' => 25000, 'quantity' => 2, 'subtotal' => 50000], ...]
     * @param float $paidAmount  Amount paid by customer
     * @return int|false         New transaction ID, or false on failure
     */
    public function create(int $cashierId, array $items, float $paidAmount): int|false
    {
        if (empty($items)) {
            return false;
        }

        // Calculate total price
        $totalPrice = array_reduce($items, fn($sum, $item) => $sum + $item['subtotal'], 0);
        $changeAmount = $paidAmount - $totalPrice;

        if ($paidAmount < $totalPrice) {
            return false;
        }

        // Generate transaction code: TRX-YYYYMM-NNN
        $code = $this->generateTransactionCode();

        try {
            $this->pdo->beginTransaction();

            $stmtStock = $this->pdo->prepare(
                "SELECT stock FROM products WHERE id = ? AND deleted_at IS NULL FOR UPDATE"
            );
            $stmtReduce = $this->pdo->prepare(
                "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?"
            );

            foreach ($items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity  = (int) ($item['quantity'] ?? 0);

                if ($productId <= 0 || $quantity <= 0) {
                    throw new RuntimeException('Item transaksi tidak valid.');
                }

                $stmtStock->execute([$productId]);
                $stock = $stmtStock->fetchColumn();

                if ($stock === false || (int) $stock < $quantity) {
                    throw new RuntimeException('Stok produk tidak mencukupi.');
                }
            }

            // Insert transaction header
            $stmt = $this->pdo->prepare(
                "INSERT INTO transactions (transaction_code, cashier_id, total_price, paid_amount, change_amount)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$code, $cashierId, $totalPrice, $paidAmount, $changeAmount]);
            $transactionId = (int) $this->pdo->lastInsertId();

            // Insert transaction items
            $stmtItem = $this->pdo->prepare(
                "INSERT INTO transaction_items (transaction_id, product_name, quantity, subtotal) VALUES (?, ?, ?, ?)"
            );
            foreach ($items as $item) {
                $stmtReduce->execute([
                    $item['quantity'],
                    $item['product_id'],
                    $item['quantity'],
                ]);

                if ($stmtReduce->rowCount() !== 1) {
                    throw new RuntimeException('Gagal mengurangi stok produk.');
                }

                $stmtItem->execute([
                    $transactionId,
                    $item['name'],
                    $item['quantity'],
                    $item['subtotal'],
                ]);
            }

            $this->pdo->commit();
            return $transactionId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Generate unique transaction code: TRX-YYYYMM-NNN
     *
     * @return string
     */
    private function generateTransactionCode(): string
    {
        $prefix = date('Ym');
        $stmt = $this->pdo->prepare(
            "SELECT transaction_code FROM transactions WHERE transaction_code LIKE ? ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute(["TRX-{$prefix}-%"]);
        $last = $stmt->fetch();

        if ($last && preg_match('/TRX-\d+-(\d+)/', $last['transaction_code'], $m)) {
            $next = (int) $m[1] + 1;
        } else {
            $next = 1;
        }

        return sprintf('TRX-%s-%03d', $prefix, $next);
    }

    /**
     * Calculate today's total revenue
     *
     * @return float
     */
    public function todayRevenue(): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(total_price), 0) FROM transactions WHERE DATE(created_at) = CURDATE()"
        );
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }

    /**
     * Count today's transactions
     *
     * @return int
     */
    public function todayCount(): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM transactions WHERE DATE(created_at) = CURDATE()"
        );
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Count total items sold today
     *
     * @return int
     */
    public function todayItemsSold(): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(ti.quantity), 0)
             FROM transaction_items ti
             INNER JOIN transactions t ON ti.transaction_id = t.id
             WHERE DATE(t.created_at) = CURDATE()"
        );
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Total uang diterima hari ini
     *
     * @return float
     */
    public function todayPaidAmount(): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(paid_amount), 0) FROM transactions WHERE DATE(created_at) = CURDATE()"
        );
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }

    /**
     * Total kembalian hari ini
     *
     * @return float
     */
    public function todayChangeAmount(): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(change_amount), 0) FROM transactions WHERE DATE(created_at) = CURDATE()"
        );
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }

    /**
     * Produk terlaris hari ini
     *
     * @param int $limit
     * @return array
     */
    public function todayTopProducts(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT ti.product_name,
                    SUM(ti.quantity) AS total_quantity,
                    SUM(ti.subtotal) AS total_sales
             FROM transaction_items ti
             INNER JOIN transactions t ON ti.transaction_id = t.id
             WHERE DATE(t.created_at) = CURDATE()
             GROUP BY ti.product_name
             ORDER BY total_quantity DESC, total_sales DESC
             LIMIT ?"
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
