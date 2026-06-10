<?php

class Setting
{
    private PDO $pdo;
    private ActorContext $actor;

    public function __construct()
    {
        $this->pdo = getConnection();
        $this->actor = ActorContext::fromSession();
    }

    public function all(): array
    {
        $storeId = $this->actor->store_id ?? 1;
        $stmt = $this->pdo->prepare("SELECT setting_key, setting_value FROM settings WHERE store_id = ?");
        $stmt->execute([$storeId]);
        $rows = $stmt->fetchAll();
        return array_column($rows, 'setting_value', 'setting_key');
    }

    public function updateMany(array $settings): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO settings (store_id, setting_key, setting_value) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
        );

        $storeId = $this->actor->store_id ?? 1;
        foreach ($settings as $key => $value) {
            $stmt->execute([$storeId, $key, $value]);
        }

        AuditLogger::log($this->actor, 'update', 'settings', $storeId, null, array_keys($settings), $this->pdo);
        return true;
    }
}
