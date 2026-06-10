<?php

class Product
{
    private ProductRepository $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository(getConnection(), ActorContext::fromSession());
    }

    /**
     * Ambil semua produk
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->repository->active();
    }

    public function getAllForManagement(): array
    {
        return $this->repository->management();
    }

    /**
     * Ambil 1 produk berdasarkan ID
     *
     * @param int $id
     * @return array|false
     */
    public function getById(int $id): array|false
    {
        return $this->repository->findActiveById($id) ?? false;
    }

    /**
     * Tambah produk baru
     *
     * @param array $data ['name', 'image', 'price', 'stock', 'description']
     * @return bool
     */
    public function create(array $data): bool
    {
        $payload = [
            'name' => $data['name'],
            'image' => $data['image'] ?? null,
            'stock' => $data['stock'],
        ];
        $pdo = getConnection();
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = 'products' AND column_name = 'selling_price'"
        );
        $stmt->execute();
        if ((int) $stmt->fetchColumn() > 0) {
            $payload += [
                'sku' => $data['sku'] ?? generateSKU(),
                'purchase_price' => $data['purchase_price'] ?? 0,
                'selling_price' => $data['price'],
                'min_stock' => $data['minimum_stock'] ?? 5,
                'unit' => $data['unit'] ?? 'pcs',
                'status' => 'active',
            ];
        } else {
            $payload += [
                'price' => $data['price'],
                'minimum_stock' => $data['minimum_stock'] ?? 5,
                'description' => $data['description'] ?? '',
            ];
        }
        return $this->repository->insert($payload) > 0;
    }

    /**
     * Update produk berdasarkan ID
     *
     * @param int   $id
     * @param array $data ['name', 'image', 'price', 'stock', 'description']
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $pdo = getConnection();
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = 'products' AND column_name = 'selling_price'"
        );
        $stmt->execute();
        if ((int) $stmt->fetchColumn() > 0) {
            $data['selling_price'] = $data['price'] ?? $data['selling_price'] ?? 0;
            $data['min_stock'] = $data['minimum_stock'] ?? $data['min_stock'] ?? 0;
            unset($data['price'], $data['minimum_stock'], $data['description']);
        }
        return $this->repository->update($id, $data);
    }

    /**
     * Update hanya gambar produk
     *
     * @param int         $id
     * @param string|null $image Nama file gambar
     * @return bool
     */
    public function updateImage(int $id, ?string $image): bool
    {
        return $this->repository->update($id, ['image' => $image]);
    }

    /**
     * Hapus produk berdasarkan ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->repository->softDelete($id);
    }

    public function setStatus(int $id, string $status): bool
    {
        return $this->repository->update($id, ['status' => $status]);
    }

    public function adjustStock(int $id, int $delta): bool
    {
        return $this->repository->adjustStock($id, $delta);
    }

    /**
     * Kurangi stok produk
     *
     * @param int $id
     * @param int $quantity
     * @return bool
     */
    public function reduceStock(int $id, int $quantity): bool
    {
        return $this->repository->adjustStock($id, -$quantity);
    }

    /**
     * Hitung total produk
     *
     * @return int
     */
    public function count(): int
    {
        return $this->repository->countActiveRecords();
    }

    /**
     * Hitung total semua stok
     *
     * @return int
     */
    public function totalStock(): int
    {
        return $this->repository->totalStock();
    }

    /**
     * Ambil produk dengan stok menipis
     *
     * @param int $threshold Batas stok rendah
     * @return array
     */
    public function getLowStock(int $threshold = 5): array
    {
        return $this->repository->lowStock($threshold);
    }
}
