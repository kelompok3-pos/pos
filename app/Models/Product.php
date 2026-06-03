<?php

/**
 * =================================================================
 * MODEL: PRODUCT
 * =================================================================
 * Class untuk mengakses tabel `products` di database.
 *
 * Cara pakai:
 *   $product = new Product();
 *   $semua   = $product->getAll();
 *   $satu    = $product->getById(1);
 * =================================================================
 */

class Product
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    /**
     * Ambil semua produk
     *
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM products WHERE deleted_at IS NULL ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    /**
     * Ambil 1 produk berdasarkan ID
     *
     * @param int $id
     * @return array|false
     */
    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Tambah produk baru
     *
     * @param array $data ['name', 'image', 'price', 'stock', 'description']
     * @return bool
     */
    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO products (name, image, price, stock, description) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['name'],
            $data['image'] ?? null,
            $data['price'],
            $data['stock'],
            $data['description'] ?? '',
        ]);
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
        if (array_key_exists('image', $data)) {
            $stmt = $this->pdo->prepare(
                "UPDATE products SET name = ?, image = ?, price = ?, stock = ?, description = ? WHERE id = ?"
            );
            return $stmt->execute([
                $data['name'],
                $data['image'],
                $data['price'],
                $data['stock'],
                $data['description'] ?? '',
                $id,
            ]);
        }

        $stmt = $this->pdo->prepare(
            "UPDATE products SET name = ?, price = ?, stock = ?, description = ? WHERE id = ?"
        );
        return $stmt->execute([
            $data['name'],
            $data['price'],
            $data['stock'],
            $data['description'] ?? '',
            $id,
        ]);
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
        $stmt = $this->pdo->prepare("UPDATE products SET image = ? WHERE id = ?");
        return $stmt->execute([$image, $id]);
    }

    /**
     * Hapus produk berdasarkan ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE products SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
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
        $stmt = $this->pdo->prepare(
            "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ? AND deleted_at IS NULL"
        );
        return $stmt->execute([$quantity, $id, $quantity]);
    }

    /**
     * Hitung total produk
     *
     * @return int
     */
    public function count(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM products WHERE deleted_at IS NULL");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Hitung total semua stok
     *
     * @return int
     */
    public function totalStock(): int
    {
        $stmt = $this->pdo->query("SELECT COALESCE(SUM(stock), 0) FROM products WHERE deleted_at IS NULL");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Ambil produk dengan stok menipis
     *
     * @param int $threshold Batas stok rendah
     * @return array
     */
    public function getLowStock(int $threshold = 5): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM products WHERE stock <= ? AND deleted_at IS NULL ORDER BY stock ASC, name ASC"
        );
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }
}
