<?php

final class AuditLogger
{
    public static function log(
        ActorContext $actor,
        string $action,
        string $table,
        int|string|null $id,
        mixed $old = null,
        mixed $new = null,
        ?PDO $pdo = null
    ): void {
        $pdo ??= getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO audit_logs
             (store_id, user_id, action, target_table, target_id, old_value, new_value, ip_address)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $actor->store_id,
            $actor->user_id,
            $action,
            $table,
            $id === null ? null : (string) $id,
            self::encode($old),
            self::encode($new),
            $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }

    private static function encode(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        return json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
