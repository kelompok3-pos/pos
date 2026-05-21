<?php

/**
 * =================================================================
 * MODEL: TRANSACTION
 * =================================================================
 * Class untuk mengakses tabel `transaksi` dan `detail_transaksi`
 * di database.
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
     * Ambil semua header transaksi (di-join dengan users sebagai kasir)
     *
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            "SELECT t.*, u.nama AS kasir_name
             FROM transaksi t
             LEFT JOIN users u ON t.id_kasir = u.id
             ORDER BY t.id DESC"
        );
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
            "SELECT t.*, u.nama AS kasir_name
             FROM transaksi t
             LEFT JOIN users u ON t.id_kasir = u.id
             WHERE DATE(t.created_at) = CURDATE()
             ORDER BY t.id DESC"
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
        $stmt = $this->pdo->prepare(
            "SELECT t.*, u.nama AS kasir_name
             FROM transaksi t
             LEFT JOIN users u ON t.id_kasir = u.id
             WHERE t.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Ambil detail transaksi berdasarkan ID header
     *
     * @param int $transactionId
     * @return array
     */
    public function getDetails(int $transactionId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM detail_transaksi WHERE id_transaksi = ?"
        );
        $stmt->execute([$transactionId]);
        return $stmt->fetchAll();
    }

    /**
     * Simpan transaksi baru (header + detail dalam 1 method)
     *
     * @param int   $kasirId    ID kasir dari session
     * @param array $items      [['product_id' => 1, 'name' => 'Kopi', 'price' => 25000, 'quantity' => 2, 'subtotal' => 50000], ...]
     * @return bool
     */
    public function create(int $kasirId, array $items): bool
    {
        if (empty($items)) {
            return false;
        }

        // Hitung total harga
        $totalPrice = array_reduce($items, fn($sum, $item) => $sum + $item['subtotal'], 0);

        // Generate kode transaksi: TRX-YYYYMM-NNN
        $kode = $this->generateTransactionCode();

        try {
            $this->pdo->beginTransaction();

            // Insert header transaksi
            $stmt = $this->pdo->prepare(
                "INSERT INTO transaksi (kode_transaksi, id_kasir, total_harga) VALUES (?, ?, ?)"
            );
            $stmt->execute([$kode, $kasirId, $totalPrice]);
            $transactionId = (int) $this->pdo->lastInsertId();

            // Insert detail transaksi
            $stmtDetail = $this->pdo->prepare(
                "INSERT INTO detail_transaksi (id_transaksi, nama_produk, jumlah, subtotal) VALUES (?, ?, ?, ?)"
            );
            foreach ($items as $item) {
                $stmtDetail->execute([
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
     * Generate kode transaksi unik: TRX-YYYYMM-NNN
     *
     * @return string
     */
    private function generateTransactionCode(): string
    {
        $prefix = date('Ym');
        $stmt = $this->pdo->prepare(
            "SELECT kode_transaksi FROM transaksi WHERE kode_transaksi LIKE ? ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute(["TRX-{$prefix}-%"]);
        $last = $stmt->fetch();

        if ($last && preg_match('/TRX-\d+-(\d+)/', $last['kode_transaksi'], $m)) {
            $next = (int) $m[1] + 1;
        } else {
            $next = 1;
        }

        return sprintf('TRX-%s-%03d', $prefix, $next);
    }

    /**
     * Hitung total pendapatan hari ini
     *
     * @return float
     */
    public function todayRevenue(): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(total_harga), 0) FROM transaksi WHERE DATE(created_at) = CURDATE()"
        );
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }

    /**
     * Hitung jumlah transaksi hari ini
     *
     * @return int
     */
    public function todayCount(): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM transaksi WHERE DATE(created_at) = CURDATE()"
        );
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Hitung total item terjual hari ini
     *
     * @return int
     */
    public function todayItemsSold(): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(dt.jumlah), 0)
             FROM detail_transaksi dt
             INNER JOIN transaksi t ON dt.id_transaksi = t.id
             WHERE DATE(t.created_at) = CURDATE()"
        );
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
