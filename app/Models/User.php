<?php

/**
 * =================================================================
 * MODEL: USER
 * =================================================================
 * Class untuk mengakses tabel `users` di database.
 * Digunakan untuk authentication sederhana (login/register).
 *
 * Cara pakai:
 *   $user = new User();
 *   $data = $user->findByEmail('admin@pos.com');
 * =================================================================
 */

class User
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    /**
     * Ambil semua user (tanpa yang soft-deleted)
     *
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT id, name, email, role, created_at, updated_at FROM users WHERE deleted_at IS NULL ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    /**
     * Ambil 1 user berdasarkan ID
     *
     * @param int $id
     * @return array|false
     */
    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT id, name, email, role, created_at, updated_at FROM users WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Cari user berdasarkan email (untuk login)
     *
     * @param string $email
     * @return array|false
     */
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ? AND deleted_at IS NULL");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Buat user baru (register)
     *
     * @param array $data ['name', 'email', 'password', 'role']
     * @return bool
     */
    public function create(array $data): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)"
            );
            return $stmt->execute([
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['role'] ?? 'kasir',
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update data user berdasarkan ID
     *
     * @param int   $id
     * @param array $data ['name', 'email', 'role']
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?"
        );
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['role'] ?? 'kasir',
            $id,
        ]);
    }

    /**
     * Update password user
     *
     * @param int    $id
     * @param string $password
     * @return bool
     */
    public function updatePassword(int $id, string $password): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([
            password_hash($password, PASSWORD_DEFAULT),
            $id,
        ]);
    }

    /**
     * Soft delete user berdasarkan ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Verifikasi password user (untuk login)
     *
     * @param string $email
     * @param string $password
     * @return array|false  Data user jika valid, false jika gagal
     */
    public function authenticate(string $email, string $password): array|false
    {
        $user = $this->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']); // Jangan expose password
            return $user;
        }

        return false;
    }

    /**
     * Hitung total user aktif
     *
     * @return int
     */
    public function count(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Hitung total user aktif berdasarkan daftar role.
     *
     * @param array $roles
     * @return int
     */
    public function countByRoles(array $roles): int
    {
        if (empty($roles)) {
            return 0;
        }

        $placeholders = implode(',', array_fill(0, count($roles), '?'));
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM users WHERE deleted_at IS NULL AND role IN ({$placeholders})"
        );
        $stmt->execute($roles);

        return (int) $stmt->fetchColumn();
    }
}
