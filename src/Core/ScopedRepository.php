<?php

abstract class ScopedRepository
{
    protected PDO $pdo;
    protected string $table;
    protected ?int $store_id;
    protected ActorContext $actor;

    public function __construct(PDO $pdo, ActorContext $actor)
    {
        $this->pdo = $pdo;
        $this->actor = $actor;
        $this->store_id = $actor->store_id;
        $this->assertIdentifier($this->table);
    }

    public function findAll(
        array $conditions = [],
        string $orderBy = 'id DESC',
        int $limit = 100,
        int $offset = 0
    ): array
    {
        [$where, $params] = $this->buildWhere($conditions);
        $orderBy = $this->safeOrderBy($orderBy);
        $limit = max(1, min($limit, 500));
        $offset = max(0, $offset);
        $stmt = $this->pdo->prepare(
            "SELECT * FROM {$this->table}{$where} ORDER BY {$orderBy} LIMIT {$limit} OFFSET {$offset}"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        [$where, $params] = $this->buildWhere(['id' => $id]);
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table}{$where} LIMIT 1");
        $stmt->execute($params);
        $row = $stmt->fetch();
        if ($row) {
            return $row;
        }
        if (!$this->actor->isSuperAdmin() && $this->existsOutsideScope($id)) {
            throw new UnauthorizedException('Record does not belong to the current store.');
        }
        return null;
    }

    public function insert(array $data): int
    {
        if (!$this->actor->isSuperAdmin()) {
            $data['store_id'] = $this->actor->requireStoreId();
        } elseif (array_key_exists('store_id', $data) && $data['store_id'] !== null) {
            $data['store_id'] = (int) $data['store_id'];
        }

        if ($data === []) {
            throw new InvalidArgumentException('Insert data cannot be empty.');
        }

        $columns = array_keys($data);
        foreach ($columns as $column) {
            $this->assertIdentifier($column);
        }
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $stmt = $this->pdo->prepare(
            "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));
        $id = (int) $this->pdo->lastInsertId();
        AuditLogger::log($this->actor, 'create', $this->table, $id, null, $data, $this->pdo);
        return $id;
    }

    public function update(int $id, array $data): bool
    {
        $old = $this->findById($id);
        if ($old === null) {
            throw new NotFoundException('Record not found.');
        }
        unset($data['id'], $data['store_id']);
        if ($data === []) {
            return true;
        }

        $sets = [];
        foreach (array_keys($data) as $column) {
            $this->assertIdentifier($column);
            $sets[] = "{$column} = ?";
        }
        [$where, $whereParams] = $this->buildWhere(['id' => $id]);
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET " . implode(', ', $sets) . $where);
        $stmt->execute([...array_values($data), ...$whereParams]);
        if ($stmt->rowCount() > 0) {
            AuditLogger::log($this->actor, 'update', $this->table, $id, $old, $data, $this->pdo);
        }
        return $stmt->rowCount() === 1;
    }

    public function delete(int $id): bool
    {
        $old = $this->findById($id);
        if ($old === null) {
            throw new NotFoundException('Record not found.');
        }
        [$where, $params] = $this->buildWhere(['id' => $id]);
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table}{$where}");
        $stmt->execute($params);
        if ($stmt->rowCount() === 1) {
            AuditLogger::log($this->actor, 'delete', $this->table, $id, $old, null, $this->pdo);
            return true;
        }
        return false;
    }

    protected function buildWhere(array $conditions): array
    {
        $parts = [];
        $params = [];
        if (!$this->actor->isSuperAdmin()) {
            $parts[] = 'store_id = ?';
            $params[] = $this->actor->requireStoreId();
        }
        foreach ($conditions as $column => $value) {
            $this->assertIdentifier((string) $column);
            $parts[] = "{$column} = ?";
            $params[] = $value;
        }
        return [$parts === [] ? '' : ' WHERE ' . implode(' AND ', $parts), $params];
    }

    public function count(array $conditions = []): int
    {
        [$where, $params] = $this->buildWhere($conditions);
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table}{$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    protected function assertIdentifier(string $identifier): void
    {
        if (!preg_match('/^[a-z_][a-z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException('Invalid SQL identifier.');
        }
    }

    protected function hasColumn(string $column): bool
    {
        $this->assertIdentifier($column);
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?'
        );
        $stmt->execute([$this->table, $column]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function safeOrderBy(string $orderBy): string
    {
        if (!preg_match('/^[a-z_][a-z0-9_]*(?:\s+(?:ASC|DESC))?$/i', trim($orderBy))) {
            throw new InvalidArgumentException('Invalid order clause.');
        }
        return trim($orderBy);
    }

    private function existsOutsideScope(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM {$this->table} WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return (bool) $stmt->fetchColumn();
    }
}
