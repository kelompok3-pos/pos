<?php

final class StoreSettingDefaults
{
    public const KEYS = [
        'store_name',
        'store_address',
        'store_logo',
        'currency_symbol',
        'tax_percentage',
        'receipt_footer',
    ];

    public static function forStore(string $storeName): array
    {
        return [
            'store_name' => $storeName,
            'store_address' => '',
            'store_logo' => '',
            'currency_symbol' => 'Rp',
            'tax_percentage' => '0',
            'receipt_footer' => 'Terima kasih sudah berbelanja.',
        ];
    }

    public static function seed(PDO $pdo, int $storeId, string $storeName): void
    {
        $stmt = $pdo->prepare(
            'INSERT IGNORE INTO settings (store_id, setting_key, setting_value) VALUES (?, ?, ?)'
        );
        foreach (self::forStore($storeName) as $key => $value) {
            $stmt->execute([$storeId, $key, $value]);
        }
    }
}
