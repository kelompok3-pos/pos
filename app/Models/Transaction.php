<?php

class Transaction
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Mengambil ringkasan data statistik untuk kartu dashboard
     */
    public function getDashboardStats(): array
    {
        $totalProduk = $this->pdo->query("SELECT SUM(quantity) AS total FROM transaction_items")->fetchColumn() ?? 0;
        $totalPenjualan = $this->pdo->query("SELECT SUM(total_price) AS total FROM transactions")->fetchColumn() ?? 0;
        $totalTransaksi = $this->pdo->query("SELECT COUNT(*) AS total FROM transactions")->fetchColumn() ?? 0;

        return [
            'total_produk'    => $totalProduk,
            'total_penjualan' => $totalPenjualan,
            'total_transaksi' => $totalTransaksi
        ];
    }

    /**
     * Mengambil data grafik penjualan 6 bulan terakhir
     */
    public function getSalesChartData(): array
    {
        return $this->pdo->query("
            SELECT
                DATE_FORMAT(created_at, '%b %Y') AS bulan_label,
                DATE_FORMAT(created_at, '%Y-%m') AS bulan_sort,
                SUM(total_price) AS total,
                COUNT(*) AS jumlah_transaksi
            FROM transactions
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY bulan_sort, bulan_label
            ORDER BY bulan_sort ASC
        ")->fetchAll();
    }

    /**
     * Mengambil 5 log transaksi terbaru masuk
     */
    public function getRecentTransactions(): array
    {
        return $this->pdo->query("
            SELECT t.transaction_code, t.total_price, t.created_at, u.name AS cashier
            FROM transactions t
            JOIN users u ON t.cashier_id = u.id
            ORDER BY t.created_at DESC
            LIMIT 5
        ")->fetchAll();
    }

    /**
     * Mengambil 5 produk dengan penjualan terlaris
     */
    public function getTopSellingProducts(): array
    {
        return $this->pdo->query("
            SELECT product_name, SUM(quantity) AS total_terjual, SUM(subtotal) AS total_omzet
            FROM transaction_items
            GROUP BY product_name
            ORDER BY total_terjual DESC
            LIMIT 5
        ")->fetchAll();
    }
}