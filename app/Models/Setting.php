<?php

class Setting
{
    private PDO $pdo;
    private ActorContext $actor;

    public function __construct(?PDO $pdo = null, ?ActorContext $actor = null)
    {
        $this->pdo = $pdo ?? getConnection();
        $this->actor = $actor ?? ActorContext::fromSession();
    }

    public function all(): array
    {
        $storeId = $this->actor->requireStoreId();
        $stmt = $this->pdo->prepare("SELECT setting_key, setting_value FROM settings WHERE store_id = ?");
        $stmt->execute([$storeId]);
        $rows = $stmt->fetchAll();
        $stored = array_intersect_key(
            array_column($rows, 'setting_value', 'setting_key'),
            array_flip(StoreSettingDefaults::KEYS)
        );
        return array_replace(
            StoreSettingDefaults::forStore($this->actor->store_name ?? 'Toko'),
            $stored
        );
    }

    public function updateMany(array $settings): bool
    {
        $unknownKeys = array_diff(array_keys($settings), StoreSettingDefaults::KEYS);
        if ($unknownKeys !== []) {
            throw new InvalidArgumentException('Setting toko tidak valid.');
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO settings (store_id, setting_key, setting_value) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
        );

        $storeId = $this->actor->requireStoreId();
        $ownsTransaction = !$this->pdo->inTransaction();
        if ($ownsTransaction) {
            $this->pdo->beginTransaction();
        }
        try {
            foreach ($settings as $key => $value) {
                $stmt->execute([$storeId, $key, $value]);
            }
            AuditLogger::log($this->actor, 'update', 'settings', $storeId, null, array_keys($settings), $this->pdo);
            if ($ownsTransaction) {
                $this->pdo->commit();
            }
        } catch (Throwable $exception) {
            if ($ownsTransaction && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }

        return true;
    }
}
