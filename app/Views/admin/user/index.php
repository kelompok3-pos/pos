<?php
$users = $users ?? [];
$isSuperAdminView = isSuperAdmin();
?>

<style>
.sa-user-page {
    width: 100%;
}

.sa-user-page * {
    box-sizing: border-box;
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

.sa-add-link {
    height: 42px;
    padding: 0 .9rem;
    border-radius: .75rem;
    border: 0;
    background: #378add;
    color: #fff;
    text-decoration: none;
    font-size: .84rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    white-space: nowrap;
    transition: background-color .18s ease, box-shadow .18s ease;
}

.sa-add-link:hover {
    background: #2f7bc6;
    color: #fff;
    text-decoration: none;
    box-shadow: 0 8px 18px rgba(55, 138, 221, .18);
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
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
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

.sa-filter-body {
    padding: 1rem 1.1rem;
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
}

.sa-filter-grid {
    display: grid;
    grid-template-columns: minmax(180px, 1fr) minmax(180px, 1fr) minmax(120px, .5fr);
    gap: .85rem;
    align-items: end;
}

.sa-form-group {
    min-width: 0;
}

.sa-form-label {
    display: block;
    margin-bottom: .4rem;
    font-size: .75rem;
    font-weight: 700;
    color: var(--color-text-secondary, #6b7280);
}

.sa-form-input {
    width: 100%;
    height: 42px;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .14));
    background: var(--color-background-primary, #fff);
    color: var(--color-text-primary, #111827);
    border-radius: .75rem;
    padding: .55rem .75rem;
    font-size: .85rem;
    outline: none;
    transition: border-color .16s ease, box-shadow .16s ease;
}

.sa-form-input:focus {
    border-color: rgba(55, 138, 221, .55);
    box-shadow: 0 0 0 3px rgba(55, 138, 221, .12);
}

.sa-filter-button {
    width: 100%;
    height: 42px;
    border: 1px solid rgba(55, 138, 221, .25);
    border-radius: .75rem;
    background: rgba(55, 138, 221, .08);
    color: #2f7bc6;
    font-size: .84rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    cursor: pointer;
    transition: background-color .18s ease, border-color .18s ease;
}

.sa-filter-button:hover {
    background: rgba(55, 138, 221, .14);
    border-color: rgba(55, 138, 221, .4);
}

.sa-search-wrap {
    position: relative;
    width: min(340px, 100%);
}

.sa-search-icon {
    position: absolute;
    left: .8rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-text-tertiary, #9ca3af);
    pointer-events: none;
}

.sa-search-input {
    width: 100%;
    height: 42px;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .14));
    background: var(--color-background-primary, #fff);
    color: var(--color-text-primary, #111827);
    border-radius: .75rem;
    padding: .55rem .75rem .55rem 2.35rem;
    font-size: .85rem;
    outline: none;
    transition: border-color .16s ease, box-shadow .16s ease;
}

.sa-search-input:focus {
    border-color: rgba(55, 138, 221, .55);
    box-shadow: 0 0 0 3px rgba(55, 138, 221, .12);
}

.sa-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.sa-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .825rem;
    min-width: <?= $isSuperAdminView ? '1180px' : '760px' ?>;
}

.sa-table th {
    padding: .75rem 1rem;
    text-align: left;
    font-size: .72rem;
    font-weight: 800;
    color: var(--color-text-secondary, #6b7280);
    background: var(--color-background-secondary, #f9fafb);
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
    white-space: nowrap;
}

.sa-table td {
    padding: .9rem 1rem;
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .06));
    color: var(--color-text-primary, #111827);
    vertical-align: middle;
    white-space: nowrap;
}

.sa-table tbody tr {
    transition: background-color .16s ease;
}

.sa-table tbody tr:hover td {
    background: rgba(55, 138, 221, .045);
}

.sa-table tbody tr:last-child td {
    border-bottom: none;
}

.sa-number {
    color: var(--color-text-secondary, #6b7280);
    font-weight: 700;
}

.sa-user-cell {
    display: flex;
    align-items: center;
    gap: .7rem;
    min-width: 0;
}

.sa-user-avatar {
    width: 38px;
    height: 38px;
    border-radius: .85rem;
    background: rgba(55, 138, 221, .11);
    color: #378add;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    font-size: 1rem;
    font-weight: 800;
}

.sa-user-name {
    font-weight: 750;
    color: var(--color-text-primary, #111827);
}

.sa-email,
.sa-date,
.sa-muted {
    color: var(--color-text-secondary, #6b7280);
}

.sa-role-pill,
.sa-status-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .32rem .65rem;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 800;
    line-height: 1;
    white-space: nowrap;
}

.sa-pill-neutral {
    background: #f3f4f6;
    color: #4b5563;
}

.sa-pill-primary {
    background: #e6f1fb;
    color: #185fa5;
}

.sa-pill-success {
    background: #eaf7ef;
    color: #247244;
}

.sa-pill-danger {
    background: #fef3f2;
    color: #b42318;
}

.sa-actions {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: .4rem;
}

.sa-icon-button {
    width: 34px;
    height: 34px;
    border-radius: .65rem;
    border: 1px solid rgba(0, 0, 0, .10);
    background: #fff;
    color: #4b5563;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    cursor: pointer;
    transition: background-color .16s ease, border-color .16s ease, color .16s ease, box-shadow .16s ease;
}

.sa-icon-button:hover {
    background: #f8fafc;
    border-color: rgba(55, 138, 221, .35);
    color: #2f7bc6;
    text-decoration: none;
}

.sa-icon-danger {
    border-color: rgba(180, 35, 24, .18);
    background: #fff;
    color: #b42318;
}

.sa-icon-danger:hover {
    background: #fef3f2;
    border-color: rgba(180, 35, 24, .35);
    color: #b42318;
}

.sa-inline-form {
    display: inline;
    margin: 0;
}

.sa-empty {
    padding: 2.5rem 1rem;
    text-align: center;
    color: var(--color-text-tertiary, #9ca3af);
}

.sa-empty i {
    display: block;
    margin-bottom: .5rem;
    font-size: 2rem;
    color: var(--color-text-tertiary, #9ca3af);
}

@media (max-width: 900px) {
    .sa-header,
    .sa-panel-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .sa-add-link,
    .sa-search-wrap {
        width: 100%;
    }

    .sa-filter-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="sa-user-page">

    <div class="sa-header">
        <div class="sa-title">
            <span class="sa-eyebrow">Admin</span>
            <h2>
                <i class="ti ti-users" aria-hidden="true"></i>
                Manajemen User
            </h2>
            <p>Kelola akun tim aktif sesuai hak akses.</p>
        </div>

        <a href="<?= url('/admin/user/create') ?>" class="sa-add-link">
            <i class="ti ti-user-plus" aria-hidden="true"></i>
            Tambah User
        </a>
    </div>

    <div class="sa-panel">

        <?php if ($isSuperAdminView): ?>
            <div class="sa-filter-body">
                <form method="GET" action="<?= url('/admin/user') ?>" class="sa-filter-grid">
                    <div class="sa-form-group">
                        <label class="sa-form-label">Role</label>
                        <select name="role" class="sa-form-input">
                            <option value="">All roles</option>
                            <?php foreach (['super_admin', 'admin', 'kasir'] as $role): ?>
                                <option value="<?= e($role) ?>" <?= ($_GET['role'] ?? '') === $role ? 'selected' : '' ?>>
                                    <?= e(roleLabel($role)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="sa-form-group">
                        <label class="sa-form-label">Status</label>
                        <select name="status" class="sa-form-input">
                            <option value="">All status</option>
                            <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>
                                Active
                            </option>
                            <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>
                                Inactive
                            </option>
                        </select>
                    </div>

                    <div class="sa-form-group">
                        <button type="submit" class="sa-filter-button">
                            <i class="ti ti-filter" aria-hidden="true"></i>
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <div class="sa-panel-header">
            <div>
                <div class="sa-panel-title">
                    <i class="ti ti-shield-lock" aria-hidden="true"></i>
                    Daftar Akun Aktif
                </div>
                <div class="sa-panel-subtitle">Cari, edit, nonaktifkan, atau reset password user.</div>
            </div>

            <div class="sa-search-wrap">
                <i class="ti ti-search sa-search-icon" aria-hidden="true"></i>
                <input
                    type="text"
                    class="sa-search-input"
                    id="searchUser"
                    placeholder="Cari nama atau email..."
                >
            </div>
        </div>

        <div class="sa-table-wrap">
            <table class="sa-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <?php if ($isSuperAdminView): ?>
                            <th>Role</th>
                        <?php endif; ?>
                        <th>Terdaftar</th>
                        <?php if ($isSuperAdminView): ?>
                            <th>Tenant</th>
                            <th>Admin Pengelola</th>
                            <th>Dibuat Oleh</th>
                            <th>Status</th>
                        <?php endif; ?>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>

                <tbody id="userTableBody">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="<?= $isSuperAdminView ? 10 : 5 ?>" class="sa-empty">
                                <i class="ti ti-users" aria-hidden="true"></i>
                                Belum ada data user aktif di database.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $index => $u): ?>
                            <?php
                            $isCurrentUser = (int) $u['id'] === (int) ($_SESSION['user_id'] ?? 0);
                            $canManageUser = canManageUser($u);
                            $canDeleteUser = !$isCurrentUser && $canManageUser && $u['deleted_at'] === null;

                            $role = $u['role'] ?? '';
                            $roleClass = 'sa-pill-success';
                            $roleIcon = 'ti-user';

                            if ($role === 'super_admin') {
                                $roleClass = 'sa-pill-neutral';
                                $roleIcon = 'ti-shield-lock';
                            } elseif ($role === 'admin') {
                                $roleClass = 'sa-pill-primary';
                                $roleIcon = 'ti-shield';
                            }

                            $isActive = $u['deleted_at'] === null;
                            $userInitial = strtoupper(substr((string) ($u['name'] ?? 'U'), 0, 1));
                            ?>
                            <tr>
                                <td class="sa-number"><?= $index + 1 ?></td>

                                <td>
                                    <div class="sa-user-cell">
                                        <div class="sa-user-avatar">
                                            <?= e($userInitial) ?>
                                        </div>

                                        <div>
                                            <div class="sa-user-name"><?= e($u['name'] ?? '-') ?></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="sa-email"><?= e($u['email'] ?? '-') ?></td>

                                <?php if ($isSuperAdminView): ?>
                                    <td>
                                        <span class="sa-role-pill <?= $roleClass ?>">
                                            <i class="ti <?= e($roleIcon) ?>" aria-hidden="true"></i>
                                            <?= e(roleLabel($role)) ?>
                                        </span>
                                    </td>
                                <?php endif; ?>

                                <td class="sa-date">
                                    <?= !empty($u['created_at']) ? date('d M Y', strtotime($u['created_at'])) : '-' ?>
                                </td>

                                <?php if ($isSuperAdminView): ?>
                                    <td class="sa-muted"><?= e($u['tenant_name'] ?? '-') ?></td>
                                    <td class="sa-muted"><?= e($u['assigned_admin_name'] ?? '-') ?></td>
                                    <td class="sa-muted"><?= e($u['created_by_name'] ?? '-') ?></td>
                                    <td>
                                        <span class="sa-status-pill <?= $isActive ? 'sa-pill-success' : 'sa-pill-neutral' ?>">
                                            <?= $isActive ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                <?php endif; ?>

                                <td style="text-align:center">
                                    <div class="sa-actions">
                                        <?php if ($canManageUser && $u['deleted_at'] === null): ?>
                                            <a
                                                href="<?= url('/admin/user/edit') ?>?id=<?= e((string) $u['id']) ?>"
                                                class="sa-icon-button"
                                                aria-label="Edit user"
                                                title="Edit user"
                                            >
                                                <i class="ti ti-edit" aria-hidden="true"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($canDeleteUser): ?>
                                            <form action="<?= url('/admin/user/delete') ?>" method="POST" class="sa-inline-form">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= e((string) $u['id']) ?>">

                                                <button
                                                    type="submit"
                                                    class="sa-icon-button sa-icon-danger"
                                                    aria-label="Deactivate user"
                                                    title="Deactivate user"
                                                    onclick="return confirm('Nonaktifkan user <?= e($u['name']) ?>?')"
                                                >
                                                    <i class="ti ti-trash" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($canManageUser && $u['deleted_at'] === null): ?>
                                            <form
                                                action="<?= url('/admin/user/reset-password') ?>"
                                                method="POST"
                                                class="sa-inline-form"
                                                onsubmit="return setResetPassword(this)"
                                            >
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= e((string) $u['id']) ?>">
                                                <input type="hidden" name="password">

                                                <button
                                                    type="submit"
                                                    class="sa-icon-button"
                                                    title="Reset Password"
                                                    aria-label="Reset password"
                                                >
                                                    <i class="ti ti-key" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
const searchUserInput = document.getElementById('searchUser');

if (searchUserInput) {
    searchUserInput.addEventListener('input', function () {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('#userTableBody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
        });
    });
}

function setResetPassword(form) {
    const password = prompt('Masukkan password baru (minimal 8 karakter):');

    if (!password) {
        return false;
    }

    if (password.length < 8) {
        alert('Password minimal 8 karakter.');
        return false;
    }

    form.elements.password.value = password;
    return true;
}
</script>