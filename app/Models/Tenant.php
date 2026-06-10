<?php

class Tenant
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function getAllActive(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, name, status, created_at
             FROM tenants
             WHERE status = 'active'
             ORDER BY name ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tenants WHERE id = ? AND status = 'active'");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(string $name, int $createdBy): int|false
    {
        $stmt = $this->pdo->prepare("INSERT INTO tenants (name, created_by) VALUES (?, ?)");
        if (!$stmt->execute([$name, $createdBy])) {
            return false;
        }
        return (int) $this->pdo->lastInsertId();
    }
}
