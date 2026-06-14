<?php
$user = $user ?? [];
$admins = $admins ?? [];
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

.sa-form-input:disabled {
    background: #f8fafc;
    color: var(--color-text-secondary, #6b7280);
    cursor: not-allowed;
}

.sa-form-help {
    margin-top: .4rem;
    font-size: .72rem;
    color: var(--color-text-secondary, #6b7280);
    line-height: 1.45;
}

.sa-info-box {
    margin-bottom: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    border: 1px solid rgba(245, 158, 11, .22);
    background: rgba(245, 158, 11, .075);
    display: flex;
    gap: .8rem;
}

.sa-info-icon {
    width: 38px;
    height: 38px;
    border-radius: .8rem;
    background: rgba(245, 158, 11, .16);
    color: #ba7517;
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

.sa-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: .75rem;
    padding-top: 1.1rem;
    margin-top: 1rem;
    border-top: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
}

.sa-save-button {
    min-width: 170px;
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
            Kembali
        </a>
    </div>

    <div class="sa-header">
        <div class="sa-title">
            <span class="sa-eyebrow">Admin</span>
            <h2>
                <i class="ti ti-edit" aria-hidden="true"></i>
                Edit Data User
            </h2>
            <p>Ubah informasi akun atau perbarui hak akses pengguna sistem.</p>
        </div>
    </div>

    <div class="sa-layout">
        <div>
            <div class="sa-info-box">
                <div class="sa-info-icon">
                    <i class="ti ti-alert-circle" aria-hidden="true"></i>
                </div>

                <div>
                    <div class="sa-info-title">Informasi perubahan akun</div>
                    <div class="sa-info-text">
                        Kamu dapat mengubah nama, email, password, dan admin pengelola kasir.
                        Role akun tidak dapat diubah setelah akun dibuat.
                    </div>
                </div>
            </div>

            <div class="sa-panel">
                <div class="sa-panel-header">
                    <div class="sa-panel-title">
                        <i class="ti ti-user-cog" aria-hidden="true"></i>
                        Form Edit User
                    </div>
                    <div class="sa-panel-subtitle">
                        Pastikan data user yang diperbarui sudah benar sebelum disimpan.
                    </div>
                </div>

                <form method="POST" action="<?= url('/admin/user/update') ?>">
                    <div class="sa-form-body">
                        <?= csrf_field() ?>

                        <input type="hidden" name="id" value="<?= e((string) ($user['id'] ?? '')) ?>">

                        <div class="sa-form-grid">

                            <div class="sa-form-group">
                                <label for="name" class="sa-form-label">Nama Lengkap</label>

                                <div class="sa-input-wrap">
                                    <i class="ti ti-user sa-input-icon" aria-hidden="true"></i>
                                    <input
                                        type="text"
                                        class="sa-form-input"
                                        id="name"
                                        name="name"
                                        value="<?= e($user['name'] ?? '') ?>"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="sa-form-group">
                                <label for="email" class="sa-form-label">Alamat Email</label>

                                <div class="sa-input-wrap">
                                    <i class="ti ti-mail sa-input-icon" aria-hidden="true"></i>
                                    <input
                                        type="email"
                                        class="sa-form-input"
                                        id="email"
                                        name="email"
                                        value="<?= e($user['email'] ?? '') ?>"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="sa-form-group">
                                <label for="password" class="sa-form-label">Password Baru</label>

                                <div class="sa-input-wrap">
                                    <i class="ti ti-lock sa-input-icon" aria-hidden="true"></i>
                                    <input
                                        type="password"
                                        class="sa-form-input"
                                        id="password"
                                        name="password"
                                        placeholder="Kosongkan jika tidak ingin mengubah password"
                                    >
                                </div>

                                <div class="sa-form-help">
                                    Abaikan kolom ini jika user tidak ingin mengganti password lamanya.
                                </div>
                            </div>

                            <div class="sa-form-group">
                                <label class="sa-form-label">Role Jabatan</label>

                                <div class="sa-input-wrap">
                                    <i class="ti ti-shield sa-input-icon" aria-hidden="true"></i>
                                    <input
                                        type="text"
                                        class="sa-form-input"
                                        value="<?= e(roleLabel($user['role'] ?? '')) ?>"
                                        disabled
                                    >
                                </div>

                                <div class="sa-form-help">
                                    Role tidak dapat diubah setelah akun dibuat.
                                </div>
                            </div>

                            <?php if ($isSuperAdminView && ($user['role'] ?? '') === 'kasir'): ?>
                                <div class="sa-form-group">
                                    <label for="assigned_admin_id" class="sa-form-label">
                                        Admin Pengelola Kasir
                                    </label>

                                    <div class="sa-input-wrap">
                                        <i class="ti ti-user-cog sa-input-icon" aria-hidden="true"></i>
                                        <select
                                            class="sa-form-input"
                                            id="assigned_admin_id"
                                            name="assigned_admin_id"
                                            required
                                        >
                                            <?php foreach ($admins as $admin): ?>
                                                <option
                                                    value="<?= (int) $admin['id'] ?>"
                                                    <?= (int) ($user['assigned_admin_id'] ?? 0) === (int) $admin['id'] ? 'selected' : '' ?>
                                                >
                                                    <?= e($admin['tenant_name'] ?? 'Tenant') ?>
                                                    -
                                                    <?= e($admin['name'] ?? '-') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="sa-form-help">
                                        Pilih admin tenant yang akan mengelola akun kasir ini.
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>

                        <div class="sa-form-actions">
                            <button type="submit" class="sa-save-button">
                                <i class="ti ti-device-floppy" aria-hidden="true"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>