<?php

final class ActorContext
{
    public string $name;

    public function __construct(
        public int $user_id,
        public ?int $store_id,
        public string $role,
        public string $user_name,
        public ?string $store_name = null,
    ) {
        $this->name = $user_name;
    }

    public static function fromSession(): self
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            throw new UnauthorizedException('Authentication required.');
        }

        $pdo = getConnection();
        $storeTable = self::tableExists($pdo, 'stores') ? 'stores' : 'tenants';
        $hasUserStatus = self::columnExists($pdo, 'users', 'status');
        $hasDeletedAt = self::columnExists($pdo, 'users', 'deleted_at');
        $activeUser = $hasUserStatus ? " AND u.status = 'active'" : '';
        $notDeleted = $hasDeletedAt ? ' AND u.deleted_at IS NULL' : '';
        $stmt = $pdo->prepare(
            "SELECT u.id, u.name, u.role, u.store_id, s.name AS store_name, s.status AS store_status
             FROM users u
             LEFT JOIN {$storeTable} s ON s.id = u.store_id
             WHERE u.id = ?{$activeUser}{$notDeleted}
             LIMIT 1"
        );
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            self::invalidateSession();
            throw new UnauthorizedException('Session is no longer valid.');
        }

        $role = (string) $user['role'];
        $storeId = $user['store_id'] === null ? null : (int) $user['store_id'];

        if (self::normalizeRole($role) === 'superadmin') {
            $storeId = null;
        } elseif ($storeId === null || $storeId <= 0 || ($user['store_status'] ?? null) !== 'active') {
            self::invalidateSession();
            throw new UnauthorizedException('Store access is inactive or invalid.');
        }

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['name'] = (string) $user['name'];
        $_SESSION['user_name'] = (string) $user['name'];
        $_SESSION['store_name'] = $user['store_name'] ?? null;
        $_SESSION['role'] = $role;
        $_SESSION['store_id'] = $storeId;

        return new self(
            (int) $user['id'],
            $storeId,
            $role,
            (string) $user['name'],
            isset($user['store_name']) ? (string) $user['store_name'] : null
        );
    }

    public function isSuperAdmin(): bool
    {
        return self::normalizeRole($this->role) === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    public function requireRole(string ...$roles): void
    {
        $allowed = array_map([self::class, 'normalizeRole'], $roles);
        if (!in_array(self::normalizeRole($this->role), $allowed, true)) {
            throw new UnauthorizedException('Access denied.');
        }
    }

    public function getStoreId(): ?int
    {
        return $this->store_id;
    }

    public function requireStoreId(): int
    {
        if ($this->store_id === null || $this->store_id <= 0) {
            throw new UnauthorizedException('A store context is required.');
        }
        return $this->store_id;
    }

    private static function invalidateSession(): void
    {
        unset(
            $_SESSION['user_id'],
            $_SESSION['name'],
            $_SESSION['user_name'],
            $_SESSION['store_name'],
            $_SESSION['role'],
            $_SESSION['store_id'],
            $_SESSION['user'],
            $_SESSION['cart']
        );
    }

    private static function normalizeRole(string $role): string
    {
        return $role === 'super_admin' ? 'superadmin' : $role;
    }

    private static function tableExists(PDO $pdo, string $table): bool
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?'
        );
        $stmt->execute([$table]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private static function columnExists(PDO $pdo, string $table, string $column): bool
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?'
        );
        $stmt->execute([$table, $column]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
