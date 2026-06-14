ALTER TABLE users
    MODIFY role ENUM('super_admin', 'admin', 'kasir') NOT NULL DEFAULT 'kasir';

-- Super admin accounts must be created with database/seed_super_admin.php.
