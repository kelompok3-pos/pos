INSERT IGNORE INTO settings (store_id, setting_key, setting_value)
SELECT id, 'store_name', name FROM tenants;

INSERT IGNORE INTO settings (store_id, setting_key, setting_value)
SELECT tenants.id, config.setting_key, config.setting_value
FROM tenants
CROSS JOIN (
    SELECT 'store_address' AS setting_key, '' AS setting_value
    UNION ALL SELECT 'store_logo', ''
    UNION ALL SELECT 'currency_symbol', 'Rp'
    UNION ALL SELECT 'tax_percentage', '0'
    UNION ALL SELECT 'receipt_footer', 'Terima kasih sudah berbelanja.'
) config;

DELETE FROM settings WHERE setting_key = 'system_timezone';
