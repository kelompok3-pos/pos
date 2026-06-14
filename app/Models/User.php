<?php
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
    public function getAll(?int $storeId = null): array
    {
        $sql = "SELECT u.id, u.name, u.email, u.role, u.store_id, u.created_by, u.assigned_admin_id,
                       u.created_at, u.updated_at, u.deleted_at,
                       creator.name AS created_by_name, manager.name AS assigned_admin_name,
                       tenant.name AS tenant_name
                FROM users u
                LEFT JOIN users creator ON u.created_by = creator.id
                LEFT JOIN users manager ON u.assigned_admin_id = manager.id
                LEFT JOIN tenants tenant ON u.store_id = tenant.id
                WHERE 1 = 1";
        $params = [];

        if ($storeId !== null) {
            $sql .= " AND u.store_id = ?";
            $params[] = $storeId;
        }

        $stmt = $this->pdo->prepare($sql . " ORDER BY u.id DESC");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getCashiersByAdmin(int $adminId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT u.id, u.name, u.email, u.role, u.store_id, u.created_by, u.assigned_admin_id,
                    u.created_at, u.updated_at, u.deleted_at,
                    creator.name AS created_by_name, manager.name AS assigned_admin_name,
                    tenant.name AS tenant_name
             FROM users u
             LEFT JOIN users creator ON u.created_by = creator.id
             LEFT JOIN users manager ON u.assigned_admin_id = manager.id
             LEFT JOIN tenants tenant ON u.store_id = tenant.id
             WHERE u.role = 'kasir' AND u.assigned_admin_id = ?
             ORDER BY u.id DESC"
        );
        $stmt->execute([$adminId]);
        return $stmt->fetchAll();
    }

    public function getActiveAdmins(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT u.id, u.name, u.email, u.store_id, tenant.name AS tenant_name
             FROM users u
             LEFT JOIN tenants tenant ON u.store_id = tenant.id
             WHERE u.role = 'admin' AND u.deleted_at IS NULL AND u.store_id IS NOT NULL
             ORDER BY tenant.name ASC, u.name ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ambil 1 user berdasarkan ID
     *
     * @param int $id
     * @return array|false
     */
    public function getById(int $id, ?int $storeId = null): array|false
    {
        $sql = "SELECT id, name, email, role, store_id, created_by, assigned_admin_id, created_at, updated_at
                FROM users WHERE id = ? AND deleted_at IS NULL";
        $params = [$id];

        if ($storeId !== null) {
            $sql .= " AND store_id = ?";
            $params[] = $storeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
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
                "INSERT INTO users (name, email, password, role, store_id, created_by, assigned_admin_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            return $stmt->execute([
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['role'] ?? 'kasir',
                $data['store_id'] ?? null,
                $data['created_by'] ?? null,
                $data['assigned_admin_id'] ?? null,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function createAdminWithTenant(array $data, string $tenantName, int $createdBy): bool
    {
        try {
            $this->pdo->beginTransaction();
            $tenantStmt = $this->pdo->prepare(
                "INSERT INTO tenants (name, created_by) VALUES (?, ?)"
            );
            $tenantStmt->execute([$tenantName, $createdBy]);
            $tenantId = (int) $this->pdo->lastInsertId();

            $created = $this->create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => $data['password'],
                'role'              => 'admin',
                'store_id'          => $tenantId,
                'created_by'        => $createdBy,
                'assigned_admin_id' => null,
            ]);

            if (!$created) {
                throw new RuntimeException('Failed to create tenant admin.');
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
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
            "UPDATE users SET name = ?, email = ?, role = ?, store_id = ?, assigned_admin_id = ? WHERE id = ?"
        );
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['role'] ?? 'kasir',
            $data['store_id'] ?? null,
            $data['assigned_admin_id'] ?? null,
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

    public function touchLastLogin(int $id): void
    {
        // The canonical schema does not persist last-login timestamps.
    }

    /**
     * Hitung total user aktif
     *
     * @return int
     */
    public function count(?int $storeId = null): int
    {
        $sql = "SELECT COUNT(*) FROM users WHERE deleted_at IS NULL";
        $params = [];

        if ($storeId !== null) {
            $sql .= " AND store_id = ?";
            $params[] = $storeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function activate(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET deleted_at = NULL WHERE id = ?");
        return $stmt->execute([$id]);
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
