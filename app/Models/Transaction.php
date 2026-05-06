<?php

/**
 * =================================================================
 * MODEL: TRANSACTION
 * =================================================================
 * Class untuk mengakses tabel `transactions` di database.
 *
 * Cara pakai:
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
     * Ambil semua transaksi
     *
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM transactions ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    /**
     * Ambil transaksi hari ini
     *
     * @return array
     */
    public function getToday(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM transactions WHERE DATE(created_at) = CURDATE() ORDER BY id DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ambil 1 transaksi berdasarkan ID
     *
     * @param int $id
     * @return array|false
     */
    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM transactions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Simpan transaksi baru
     *
     * @param array $data ['product_id', 'product_name', 'quantity', 'price', 'total_price']
     * @return bool
     */
    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO transactions (product_id, product_name, quantity, price, total_price) 
             VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['product_id'],
            $data['product_name'],
            $data['quantity'],
            $data['price'],
            $data['total_price'],
        ]);
    }

    /**
     * Hitung total pendapatan hari ini
     *
     * @return int
     */
    public function todayRevenue(): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(total_price), 0) FROM transactions WHERE DATE(created_at) = CURDATE()"
        );
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Hitung jumlah transaksi hari ini
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
}
