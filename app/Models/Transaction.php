<?php

class Transaction
{
    private PDO $pdo;
    private ?int $storeId;
    private ActorContext $actor;

    public function __construct()
    {
        $this->pdo = getConnection();
        $this->actor = ActorContext::fromSession();
        $this->storeId = $this->actor->store_id;
    }

    /**
     * Get all transaction headers (joined with users as cashier)
     *
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT t.*, u.name AS cashier_name
             FROM transactions t
             LEFT JOIN users u ON t.cashier_id = u.id
             WHERE (? IS NULL OR t.store_id = ?)
             ORDER BY t.id DESC"
        );
        $stmt->execute([$this->storeId, $this->storeId]);
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
               AND (? IS NULL OR t.store_id = ?)
             ORDER BY t.id DESC"
        );
        $stmt->execute([$this->storeId, $this->storeId]);
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
             WHERE t.id = ?
               AND (? IS NULL OR t.store_id = ?)"
        );
        $stmt->execute([$id, $this->storeId, $this->storeId]);
        return $stmt->fetch();
    }

    public function getDetails(int $transactionId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT ti.*
             FROM transaction_items ti
             INNER JOIN transactions t ON ti.transaction_id = t.id
             WHERE ti.transaction_id = ?
               AND (? IS NULL OR t.store_id = ?)"
        );
        $stmt->execute([$transactionId, $this->storeId, $this->storeId]);
        return $stmt->fetchAll();
    }
    
    public function getDashboardStats(): array
    {
        $scope = $this->storeId === null ? '' : ' WHERE store_id = ?';
        $params = $this->storeId === null ? [] : [$this->storeId];
        $stmtItems = $this->pdo->prepare("SELECT COALESCE(SUM(quantity), 0) FROM transaction_items{$scope}");
        $stmtItems->execute($params);
        $stmtSales = $this->pdo->prepare("SELECT COALESCE(SUM(total_price), 0) FROM transactions{$scope}");
        $stmtSales->execute($params);
        $stmtCount = $this->pdo->prepare("SELECT COUNT(*) FROM transactions{$scope}");
        $stmtCount->execute($params);
        $totalProduk = $stmtItems->fetchColumn();
        $totalPenjualan = $stmtSales->fetchColumn();
        $totalTransaksi = $stmtCount->fetchColumn();

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
        $stmt = $this->pdo->prepare("
            SELECT
                DATE_FORMAT(t.created_at, '%b %Y') AS bulan_label,
                DATE_FORMAT(t.created_at, '%Y-%m') AS bulan_sort,
                SUM(t.total_price) AS total,
                COUNT(*) AS jumlah_transaksi
            FROM transactions t
            INNER JOIN users u ON t.cashier_id = u.id
            WHERE t.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
              AND (? IS NULL OR t.store_id = ?)
            GROUP BY bulan_sort, bulan_label
            ORDER BY bulan_sort ASC
        ");
        $stmt->execute([$this->storeId, $this->storeId]);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil data grafik penjualan harian beberapa hari terakhir
     */
    public function getDailySalesChartData(int $days = 7): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                DATE(transactions.created_at) AS date_sort,
                DATE_FORMAT(transactions.created_at, '%d %b') AS date_label,
                SUM(transactions.total_price) AS total,
                COUNT(*) AS jumlah_transaksi,
                COALESCE(SUM(transactions.paid_amount), 0) AS total_dibayar,
                COALESCE(SUM(transactions.change_amount), 0) AS total_kembalian
            FROM transactions
            INNER JOIN users u ON transactions.cashier_id = u.id
            WHERE transactions.created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
              AND (? IS NULL OR transactions.store_id = ?)
            GROUP BY date_sort, date_label
            ORDER BY date_sort ASC
        ");
        $stmt->execute([$days - 1, $this->storeId, $this->storeId]);
        return $stmt->fetchAll();
    }

    public function getByDateRange(string $from, string $to): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT t.*, u.name AS cashier_name
             FROM transactions t
             INNER JOIN users u ON t.cashier_id = u.id
             WHERE DATE(t.created_at) BETWEEN ? AND ?
               AND (? IS NULL OR t.store_id = ?)
             ORDER BY t.id DESC"
        );
        $stmt->execute([$from, $to, $this->storeId, $this->storeId]);
        return $stmt->fetchAll();
    }

    public function revenueByDateRange(string $from, string $to): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(t.total_price), 0)
             FROM transactions t
             INNER JOIN users u ON t.cashier_id = u.id
             WHERE DATE(t.created_at) BETWEEN ? AND ?
               AND (? IS NULL OR t.store_id = ?)"
        );
        $stmt->execute([$from, $to, $this->storeId, $this->storeId]);
        return (float) $stmt->fetchColumn();
    }

    public function getByCashier(int $cashierId, string $date = ''): array
    {
        $sql = "SELECT t.*, u.name AS cashier_name
                FROM transactions t
                INNER JOIN users u ON t.cashier_id = u.id
                WHERE t.cashier_id = ?
                  AND (? IS NULL OR t.store_id = ?)";
        $params = [$cashierId, $this->storeId, $this->storeId];
        if ($date !== '') {
            $sql .= " AND DATE(t.created_at) = ?";
            $params[] = $date;
        }
        $stmt = $this->pdo->prepare($sql . " ORDER BY t.id DESC");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getCashierPerformance(string $from, string $to): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT u.name, COUNT(t.id) AS transaction_count, COALESCE(SUM(t.total_price), 0) AS revenue
             FROM users u
             LEFT JOIN transactions t ON t.cashier_id = u.id AND DATE(t.created_at) BETWEEN ? AND ?
             WHERE u.role = 'kasir' AND u.deleted_at IS NULL
               AND (? IS NULL OR u.store_id = ?)
             GROUP BY u.id, u.name ORDER BY revenue DESC"
        );
        $stmt->execute([$from, $to, $this->storeId, $this->storeId]);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil 5 log transaksi terbaru masuk
     */
    public function getRecentTransactions(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT t.id, t.transaction_code, t.total_price, t.paid_amount,
                   t.change_amount, t.created_at, u.name AS cashier_name
            FROM transactions t
            JOIN users u ON t.cashier_id = u.id
            WHERE (? IS NULL OR t.store_id = ?)
            ORDER BY t.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$this->storeId, $this->storeId]);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil 5 produk dengan penjualan terlaris
     */
    public function getTopSellingProducts(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT ti.product_name, SUM(ti.quantity) AS total_terjual, SUM(ti.subtotal) AS total_omzet
            FROM transaction_items ti
            INNER JOIN transactions t ON ti.transaction_id = t.id
            INNER JOIN users u ON t.cashier_id = u.id
            WHERE (? IS NULL OR t.store_id = ?)
            GROUP BY ti.product_name
            ORDER BY total_terjual DESC
            LIMIT 5
        ");
        $stmt->execute([$this->storeId, $this->storeId]);
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
    public function create(int $cashierId, array $items, float $paidAmount, float $taxPercentage = 0, string $paymentMethod = 'cash'): int|false
    {
        if (empty($items)) {
            return false;
        }
        $storeId = $this->actor->requireStoreId();

        try {
            $this->pdo->beginTransaction();

            $stmtCashier = $this->pdo->prepare(
                "SELECT id FROM users
                 WHERE id = ? AND store_id = ? AND role IN ('admin', 'kasir') AND deleted_at IS NULL
                 FOR UPDATE"
            );
            $stmtCashier->execute([$cashierId, $storeId]);
            if (!$stmtCashier->fetchColumn()) {
                throw new UnauthorizedException('Cashier does not belong to the current store.');
            }

            $stmtShift = $this->pdo->prepare(
                "SELECT id FROM cashier_shifts
                 WHERE store_id = ? AND kasir_id = ? AND status = 'open'
                 ORDER BY id DESC LIMIT 1 FOR UPDATE"
            );
            $stmtShift->execute([$storeId, $cashierId]);
            $shiftId = (int) $stmtShift->fetchColumn();
            if ($shiftId <= 0) {
                throw new RuntimeException('Buka shift sebelum memproses transaksi.');
            }

            $stmtStock = $this->pdo->prepare(
                "SELECT id, name, price, stock FROM products
                 WHERE id = ? AND store_id = ? AND status = 'active' AND deleted_at IS NULL
                 FOR UPDATE"
            );
            $stmtReduce = $this->pdo->prepare(
                "UPDATE products SET stock = stock - ?
                 WHERE id = ? AND store_id = ? AND stock >= ? AND deleted_at IS NULL"
            );
            $stmtMovement = $this->pdo->prepare(
                "INSERT INTO stock_movements
                 (store_id, product_id, user_id, movement_type, quantity, stock_before, stock_after, note)
                 VALUES (?, ?, ?, 'sale', ?, ?, ?, ?)"
            );

            $stockBefore = [];
            $trustedItems = [];
            foreach ($items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity  = (int) ($item['quantity'] ?? 0);

                if ($productId <= 0 || $quantity <= 0) {
                    throw new RuntimeException('Item transaksi tidak valid.');
                }

                $stmtStock->execute([$productId, $storeId]);
                $product = $stmtStock->fetch();

                if (!$product || (int) $product['stock'] < $quantity) {
                    throw new RuntimeException('Stok produk tidak mencukupi.');
                }
                $stockBefore[$productId] = (int) $product['stock'];
                $trustedItems[] = [
                    'product_id' => $productId,
                    'name' => (string) $product['name'],
                    'price' => (float) $product['price'],
                    'quantity' => $quantity,
                    'subtotal' => (float) $product['price'] * $quantity,
                ];
            }

            $subtotal = array_reduce($trustedItems, fn(float $sum, array $item): float => $sum + $item['subtotal'], 0.0);
            $taxAmount = round($subtotal * max($taxPercentage, 0) / 100, 2);
            $totalPrice = $subtotal + $taxAmount;
            if ($paymentMethod !== 'cash') {
                $paidAmount = $totalPrice;
            }
            if ($paidAmount < $totalPrice) {
                throw new RuntimeException('Uang dibayar kurang.');
            }
            $changeAmount = $paidAmount - $totalPrice;
            $code = $this->generateTransactionCode($storeId);

            $stmt = $this->pdo->prepare(
                "INSERT INTO transactions
                 (store_id, transaction_code, cashier_id, subtotal, tax_amount, total_price, paid_amount, change_amount, payment_method)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$storeId, $code, $cashierId, $subtotal, $taxAmount, $totalPrice, $paidAmount, $changeAmount, $paymentMethod]);
            $transactionId = (int) $this->pdo->lastInsertId();

            $stmtItem = $this->pdo->prepare(
                "INSERT INTO transaction_items (store_id, transaction_id, product_name, quantity, subtotal)
                 VALUES (?, ?, ?, ?, ?)"
            );
            foreach ($trustedItems as $item) {
                $stmtReduce->execute([
                    $item['quantity'],
                    $item['product_id'],
                    $storeId,
                    $item['quantity'],
                ]);

                if ($stmtReduce->rowCount() !== 1) {
                    throw new RuntimeException('Gagal mengurangi stok produk.');
                }
                $before = $stockBefore[(int) $item['product_id']];

                $stmtItem->execute([
                    $storeId,
                    $transactionId,
                    $item['name'],
                    $item['quantity'],
                    $item['subtotal'],
                ]);
                $stmtMovement->execute([
                    $storeId,
                    $item['product_id'],
                    $cashierId,
                    $item['quantity'],
                    $before,
                    $before - (int) $item['quantity'],
                    $code,
                ]);
            }

            $stmtShiftUpdate = $this->pdo->prepare(
                "UPDATE cashier_shifts
                 SET total_transactions = total_transactions + 1
                 WHERE id = ? AND store_id = ? AND kasir_id = ? AND status = 'open'"
            );
            $stmtShiftUpdate->execute([$shiftId, $storeId, $cashierId]);
            if ($stmtShiftUpdate->rowCount() !== 1) {
                throw new RuntimeException('Shift tidak lagi aktif.');
            }

            $this->pdo->commit();
            try {
                AuditLogger::log($this->actor, 'checkout', 'transactions', $transactionId, null, [
                    'transaction_code' => $code,
                    'total_price' => $totalPrice,
                    'item_count' => count($trustedItems),
                ], $this->pdo);
            } catch (Throwable $auditException) {
                error_log($auditException->__toString());
            }
            return $transactionId;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return false;
        }
    }

    /**
     * Generate unique transaction code: TRX-YYYYMM-NNN
     *
     * @return string
     */
    private function generateTransactionCode(int $storeId): string
    {
        $prefix = date('Ymd');
        $stmt = $this->pdo->prepare(
            "SELECT transaction_code FROM transactions
             WHERE store_id = ? AND transaction_code LIKE ? ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([$storeId, "INV-{$prefix}-%"]);
        $last = $stmt->fetch();

        if ($last && preg_match('/INV-\d+-(\d+)/', $last['transaction_code'], $m)) {
            $next = (int) $m[1] + 1;
        } else {
            $next = 1;
        }

        return sprintf('INV-%s-%04d', $prefix, $next);
    }

    /**
     * Calculate today's total revenue
     *
     * @return float
     */
    public function todayRevenue(): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(t.total_price), 0)
             FROM transactions t
             INNER JOIN users u ON t.cashier_id = u.id
             WHERE DATE(t.created_at) = CURDATE()
               AND (? IS NULL OR t.store_id = ?)"
        );
        $stmt->execute([$this->storeId, $this->storeId]);
        return (float) $stmt->fetchColumn();
    }

    public function monthRevenue(): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(t.total_price), 0)
             FROM transactions t INNER JOIN users u ON t.cashier_id = u.id
             WHERE YEAR(t.created_at) = YEAR(CURDATE()) AND MONTH(t.created_at) = MONTH(CURDATE())
               AND (? IS NULL OR t.store_id = ?)"
        );
        $stmt->execute([$this->storeId, $this->storeId]);
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
            "SELECT COUNT(*)
             FROM transactions t
             INNER JOIN users u ON t.cashier_id = u.id
             WHERE DATE(t.created_at) = CURDATE()
               AND (? IS NULL OR t.store_id = ?)"
        );
        $stmt->execute([$this->storeId, $this->storeId]);
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
             INNER JOIN users u ON t.cashier_id = u.id
             WHERE DATE(t.created_at) = CURDATE()
               AND (? IS NULL OR t.store_id = ?)"
        );
        $stmt->execute([$this->storeId, $this->storeId]);
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
            "SELECT COALESCE(SUM(t.paid_amount), 0)
             FROM transactions t
             INNER JOIN users u ON t.cashier_id = u.id
             WHERE DATE(t.created_at) = CURDATE()
               AND (? IS NULL OR t.store_id = ?)"
        );
        $stmt->execute([$this->storeId, $this->storeId]);
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
            "SELECT COALESCE(SUM(t.change_amount), 0)
             FROM transactions t
             INNER JOIN users u ON t.cashier_id = u.id
             WHERE DATE(t.created_at) = CURDATE()
               AND (? IS NULL OR t.store_id = ?)"
        );
        $stmt->execute([$this->storeId, $this->storeId]);
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
             INNER JOIN users u ON t.cashier_id = u.id
             WHERE DATE(t.created_at) = CURDATE()
               AND (? IS NULL OR t.store_id = ?)
             GROUP BY ti.product_name
             ORDER BY total_quantity DESC, total_sales DESC
             LIMIT ?"
        );
        $stmt->bindValue(1, $this->storeId, $this->storeId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(2, $this->storeId, $this->storeId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
