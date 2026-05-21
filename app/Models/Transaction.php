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
     * Save new transaction (header + items in one method)
     *
     * @param int   $cashierId   Cashier ID from session
     * @param array $items       [['name' => 'Kopi', 'price' => 25000, 'quantity' => 2, 'subtotal' => 50000], ...]
     * @return bool
     */
    public function create(int $cashierId, array $items): bool
    {
        if (empty($items)) {
            return false;
        }

        // Calculate total price
        $totalPrice = array_reduce($items, fn($sum, $item) => $sum + $item['subtotal'], 0);

        // Generate transaction code: TRX-YYYYMM-NNN
        $code = $this->generateTransactionCode();

        try {
            $this->pdo->beginTransaction();

            // Insert transaction header
            $stmt = $this->pdo->prepare(
                "INSERT INTO transactions (transaction_code, cashier_id, total_price) VALUES (?, ?, ?)"
            );
            $stmt->execute([$code, $cashierId, $totalPrice]);
            $transactionId = (int) $this->pdo->lastInsertId();

            // Insert transaction items
            $stmtItem = $this->pdo->prepare(
                "INSERT INTO transaction_items (transaction_id, product_name, quantity, subtotal) VALUES (?, ?, ?, ?)"
            );
            foreach ($items as $item) {
                $stmtItem->execute([
                    $transactionId,
                    $item['name'],
                    $item['quantity'],
                    $item['subtotal'],
                ]);
            }

            $this->pdo->commit();
            return true;
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
}