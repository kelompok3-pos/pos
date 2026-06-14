<?php
$selectedRole = old('role', 'kasir');
$isSuperAdminView = isSuperAdmin();
?>

<style>
.sa-user-form-page {
    width: 100%;
}

.sa-user-form-page * {
    box-sizing: border-box;
}

.sa-back-wrap {
    margin-bottom: 1rem;
}

.sa-back-link {
    height: 38px;
    padding: 0 .8rem;
    border-radius: .7rem;
    border: 1px solid rgba(55, 138, 221, .25);
    background: rgba(55, 138, 221, .08);
    color: #2f7bc6;
    text-decoration: none;
    font-size: .82rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    transition: background-color .18s ease, border-color .18s ease;
}

.sa-back-link:hover {
    background: rgba(55, 138, 221, .14);
    border-color: rgba(55, 138, 221, .4);
    color: #2f7bc6;
    text-decoration: none;
}

.sa-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.sa-title .sa-eyebrow {
    display: inline-flex;
    margin-bottom: .4rem;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .08em;
    color: #378add;
    text-transform: uppercase;
}

.sa-title h2 {
    margin: 0;
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--color-text-primary, #111827);
    display: flex;
    align-items: center;
    gap: .5rem;
}

.sa-title p {
    margin: .35rem 0 0;
    font-size: .875rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-layout {
    display: grid;
    grid-template-columns: minmax(0, 680px);
    justify-content: center;
}

.sa-panel {
    background: var(--color-background-primary, #fff);
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 1rem;
    overflow: hidden;
    margin-bottom: 1rem;
}

.sa-panel-header {
    padding: 1rem 1.1rem;
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
}

.sa-panel-title {
    font-size: .95rem;
    font-weight: 700;
    color: var(--color-text-primary, #111827);
    display: flex;
    align-items: center;
    gap: .45rem;
}

.sa-panel-subtitle {
    margin-top: .2rem;
    font-size: .78rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-form-body {
    padding: 1.1rem;
}

.sa-form-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

.sa-form-group {
    min-width: 0;
}

.sa-form-label {
    display: block;
    margin-bottom: .45rem;
    font-size: .75rem;
    font-weight: 700;
    color: var(--color-text-secondary, #6b7280);
}

.sa-required {
    color: #b42318;
}

.sa-input-wrap {
    position: relative;
}

.sa-input-icon {
    position: absolute;
    left: .8rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-text-tertiary, #9ca3af);
    font-size: 1rem;
    pointer-events: none;
}

.sa-form-input {
    width: 100%;
    height: 44px;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .14));
    background: var(--color-background-primary, #fff);
    color: var(--color-text-primary, #111827);
    border-radius: .8rem;
    padding: .55rem .75rem .55rem 2.35rem;
    font-size: .85rem;
    outline: none;
    transition: border-color .16s ease, box-shadow .16s ease;
}

.sa-form-input:focus {
    border-color: rgba(55, 138, 221, .55);
    box-shadow: 0 0 0 3px rgba(55, 138, 221, .12);
}

.sa-form-help {
    margin-top: .4rem;
    font-size: .72rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: .75rem;
    padding-top: 1.1rem;
    margin-top: 1rem;
    border-top: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
}

.sa-save-button {
    min-width: 150px;
    height: 42px;
    border: 0;
    border-radius: .75rem;
    background: #378add;
    color: #fff;
    font-size: .84rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    cursor: pointer;
    transition: background-color .18s ease, box-shadow .18s ease;
}

.sa-save-button:hover {
    background: #2f7bc6;
    box-shadow: 0 8px 18px rgba(55, 138, 221, .18);
}

.sa-info-box {
    margin-bottom: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    border: 1px solid rgba(55, 138, 221, .16);
    background: rgba(55, 138, 221, .055);
    display: flex;
    gap: .8rem;
}

.sa-info-icon {
    width: 38px;
    height: 38px;
    border-radius: .8rem;
    background: rgba(55, 138, 221, .12);
    color: #378add;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    font-size: 1.1rem;
}

.sa-info-title {
    font-size: .88rem;
    font-weight: 800;
    color: var(--color-text-primary, #111827);
}

.sa-info-text {
    margin-top: .2rem;
    font-size: .78rem;
    color: var(--color-text-secondary, #6b7280);
    line-height: 1.45;
}

@media (max-width: 640px) {
    .sa-header {
        flex-direction: column;
    }

    .sa-form-actions {
        justify-content: stretch;
    }

    .sa-save-button,
    .sa-back-link {
        width: 100%;
        justify-content: center;
    }

    .sa-info-box {
        flex-direction: column;
    }
}
</style>

<div class="sa-user-form-page">

    <div class="sa-back-wrap">
        <a href="<?= url('/admin/user') ?>" class="sa-back-link">
            <i class="ti ti-arrow-left" aria-hidden="true"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="sa-header">
        <div class="sa-title">
            <span class="sa-eyebrow">Admin</span>
            <h2>
                <i class="ti ti-user-plus" aria-hidden="true"></i>
                Tambah User
            </h2>
            <p>
                Buat akun <?= $isSuperAdminView ? 'admin atau kasir' : 'kasir' ?> dengan hak akses yang sesuai.
            </p>
        </div>
    </div>

    <div class="sa-layout">
        <div>
            <div class="sa-info-box">
                <div class="sa-info-icon">
                    <i class="ti ti-info-circle" aria-hidden="true"></i>
                </div>

                <div>
                    <div class="sa-info-title">Informasi pembuatan akun</div>
                    <div class="sa-info-text">
                        Daftarkan akun administrator atau kasir baru ke dalam sistem aktif toko.
                        Pastikan email belum pernah digunakan dan password minimal 8 karakter.
                    </div>
                </div>
            </div>

            <div class="sa-panel">
                <div class="sa-panel-header">
                    <div class="sa-panel-title">
                        <i class="ti ti-id" aria-hidden="true"></i>
                        Informasi Akun
                    </div>
                    <div class="sa-panel-subtitle">
                        Lengkapi data user, role, dan pengelolaan tenant jika diperlukan.
                    </div>
                </div>

                <form action="<?= url('/admin/user/store') ?>" method="POST">
                    <div class="sa-form-body">
                        <?= csrf_field() ?>

                        <div class="sa-form-grid">

                            <div class="sa-form-group">
                                <label for="name" class="sa-form-label">
                                    Nama Lengkap <span class="sa-required">*</span>
                                </label>

                                <div class="sa-input-wrap">
                                    <i class="ti ti-user sa-input-icon" aria-hidden="true"></i>
                                    <input
                                        type="text"
                                        class="sa-form-input"
                                        id="name"
                                        name="name"
                                        value="<?= old('name') ?>"
                                        required
                                        placeholder="Contoh: John Doe"
                                    >
                                </div>
                            </div>

                            <div class="sa-form-group">
                                <label for="email" class="sa-form-label">
                                    Alamat Email <span class="sa-required">*</span>
                                </label>

                                <div class="sa-input-wrap">
                                    <i class="ti ti-mail sa-input-icon" aria-hidden="true"></i>
                                    <input
                                        type="email"
                                        class="sa-form-input"
                                        id="email"
                                        name="email"
                                        value="<?= old('email') ?>"
                                        required
                                        placeholder="Contoh: kasir@pos.com"
                                    >
                                </div>
                            </div>

                            <div class="sa-form-group">
                                <label for="password" class="sa-form-label">
                                    Password <span class="sa-required">*</span>
                                </label>

                                <div class="sa-input-wrap">
                                    <i class="ti ti-lock sa-input-icon" aria-hidden="true"></i>
                                    <input
                                        type="password"
                                        class="sa-form-input"
                                        id="password"
                                        name="password"
                                        required
                                        minlength="8"
                                        placeholder="Minimal 8 karakter"
                                    >
                                </div>
                            </div>

                            <div class="sa-form-group">
                                <label for="role" class="sa-form-label">
                                    Role / Jabatan <span class="sa-required">*</span>
                                </label>

                                <div class="sa-input-wrap">
                                    <i class="ti ti-shield sa-input-icon" aria-hidden="true"></i>
                                    <select class="sa-form-input" id="role" name="role" required>
                                        <?php foreach ($availableRoles as $role): ?>
                                            <option value="<?= e($role) ?>" <?= $selectedRole === $role ? 'selected' : '' ?>>
                                                <?= e(roleLabel($role)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <?php if ($isSuperAdminView): ?>
                                <div class="sa-form-group" id="tenantNameField">
                                    <label for="tenant_name" class="sa-form-label">
                                        Nama Tenant / Perusahaan
                                    </label>

                                    <div class="sa-input-wrap">
                                        <i class="ti ti-building-store sa-input-icon" aria-hidden="true"></i>
                                        <input
                                            type="text"
                                            class="sa-form-input"
                                            id="tenant_name"
                                            name="tenant_name"
                                            value="<?= old('tenant_name') ?>"
                                            placeholder="Contoh: PT Tenant Indonesia"
                                        >
                                    </div>

                                    <div class="sa-form-help">
                                        Tenant baru dibuat otomatis bersama akun admin.
                                    </div>
                                </div>

                                <div class="sa-form-group" id="assignedAdminField">
                                    <label for="assigned_admin_id" class="sa-form-label">
                                        Admin Pengelola Kasir
                                    </label>

                                    <div class="sa-input-wrap">
                                        <i class="ti ti-user-cog sa-input-icon" aria-hidden="true"></i>
                                        <select class="sa-form-input" id="assigned_admin_id" name="assigned_admin_id">
                                            <option value="">Pilih admin tenant</option>

                                            <?php foreach ($admins as $admin): ?>
                                                <option value="<?= (int) $admin['id'] ?>">
                                                    <?= e($admin['tenant_name'] ?? 'Tenant') ?>
                                                    -
                                                    <?= e($admin['name']) ?>
                                                    (<?= e($admin['email']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="sa-form-help">
                                        Kasir akan masuk ke tenant dan dikelola oleh admin ini.
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>

                        <div class="sa-form-actions">
                            <button type="submit" class="sa-save-button">
                                <i class="ti ti-check" aria-hidden="true"></i>
                                Simpan User
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?php if ($isSuperAdminView): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const role = document.getElementById('role');
    const tenantField = document.getElementById('tenantNameField');
    const tenantInput = document.getElementById('tenant_name');
    const adminField = document.getElementById('assignedAdminField');
    const adminInput = document.getElementById('assigned_admin_id');

    function syncFields() {
        const createsAdmin = role.value === 'admin';

        tenantField.hidden = !createsAdmin;
        tenantInput.required = createsAdmin;

        adminField.hidden = createsAdmin;
        adminInput.required = !createsAdmin;
    }

    role.addEventListener('change', syncFields);
    syncFields();
});
</script>
<?php endif; ?>