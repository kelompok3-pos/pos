<?php

/**
 * =================================================================
 * ADMIN USER CONTROLLER
 * =================================================================
 * User management with role hierarchy and store-scope enforcement.
 * =================================================================
 */

require_once __DIR__ . '/../Controller.php';
require_once BASE_PATH . '/app/Models/User.php';

class AdminUserController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        allowOnly([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        $this->userModel = new User();
    }

    public function index(): void
    {
        $storeId = currentStoreId();
        $users = isSuperAdmin()
            ? $this->userModel->getAll()
            : $this->userModel->getCashiersByAdmin((int) $_SESSION['user_id']);

        $roleFilter = $_GET['role'] ?? '';
        $statusFilter = $_GET['status'] ?? '';
        if (isSuperAdmin() && in_array($roleFilter, ['super_admin', 'admin', 'kasir'], true)) {
            $users = array_values(array_filter($users, fn(array $user): bool => $user['role'] === $roleFilter));
        }
        if (isSuperAdmin() && in_array($statusFilter, ['active', 'inactive'], true)) {
            $users = array_values(array_filter($users, fn(array $user): bool => ($user['deleted_at'] === null ? 'active' : 'inactive') === $statusFilter));
        }

        $this->view('admin/user/index', [
            'title'      => 'Manajemen User',
            'users'      => $users,
            'totalUser'  => $this->userModel->count($storeId),
            'totalAdmin' => count(array_filter($users, fn(array $user): bool => $user['role'] === 'admin')),
            'totalKasir' => count(array_filter($users, fn(array $user): bool => $user['role'] === 'kasir')),
            'storeId'    => $storeId,
        ]);
    }

    public function create(): void
    {
        $this->view('admin/user/create', [
            'title'          => 'Tambah User Baru',
            'availableRoles' => creatableRoles(),
            'admins'         => isSuperAdmin() ? $this->userModel->getActiveAdmins() : [],
        ]);
    }

    public function store(): void
    {
        verifyCsrf();
        keepOldInput();

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';
        $tenantName = trim($_POST['tenant_name'] ?? '');
        $assignedAdminId = isSuperAdmin()
            ? (int) ($_POST['assigned_admin_id'] ?? 0)
            : (int) $_SESSION['user_id'];

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            flash('error', 'Nama, email valid, dan password minimal 8 karakter wajib diisi.');
            $this->redirect('/admin/user/create');
        }

        if (!in_array($role, creatableRoles(), true)) {
            flash('error', 'Anda tidak diizinkan membuat role tersebut.');
            $this->redirect('/admin/user/create');
        }

        if (isSuperAdmin() && $role === 'admin') {
            if ($tenantName === '') {
                flash('error', 'Nama tenant/perusahaan wajib diisi untuk admin baru.');
                $this->redirect('/admin/user/create');
            }
            $created = $this->userModel->createAdminWithTenant(
                ['name' => $name, 'email' => $email, 'password' => $password],
                $tenantName,
                (int) $_SESSION['user_id']
            );
        } else {
            $manager = isSuperAdmin()
                ? $this->userModel->getById($assignedAdminId)
                : $this->userModel->getById((int) $_SESSION['user_id']);

            if (!$manager || $manager['role'] !== 'admin' || (int) $manager['store_id'] <= 0) {
                flash('error', 'Pilih admin tenant yang valid untuk mengelola kasir.');
                $this->redirect('/admin/user/create');
            }

            $created = $this->userModel->create([
                'name'              => $name,
                'email'             => $email,
                'password'          => $password,
                'role'              => 'kasir',
                'store_id'          => (int) $manager['store_id'],
                'created_by'        => (int) $_SESSION['user_id'],
                'assigned_admin_id' => (int) $manager['id'],
            ]);
        }

        flash($created ? 'success' : 'error', $created
            ? 'User berhasil ditambahkan!'
            : 'User gagal ditambahkan. Pastikan email belum digunakan.');
        $this->redirect('/admin/user');
    }

    public function edit(): void
    {
        $user = $this->findManageableUser((int) ($_GET['id'] ?? 0));

        $this->view('admin/user/edit', [
            'title'          => 'Edit User',
            'user'           => $user,
            'availableRoles' => creatableRoles(),
            'admins'         => isSuperAdmin() && $user['role'] === 'kasir'
                ? $this->userModel->getActiveAdmins()
                : [],
        ]);
    }

    public function update(): void
    {
        verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $targetUser = $this->findManageableUser($id);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $targetUser['role'];
        $password = $_POST['password'] ?? '';

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Nama dan email wajib diisi dengan benar.');
            $this->redirect('/admin/user/edit?id=' . $id);
        }

        if ($password !== '' && strlen($password) < 8) {
            flash('error', 'Password minimal 8 karakter.');
            $this->redirect('/admin/user/edit?id=' . $id);
        }

        $storeId = (int) $targetUser['store_id'];
        $assignedAdminId = $targetUser['assigned_admin_id'] === null ? null : (int) $targetUser['assigned_admin_id'];
        if (isSuperAdmin() && $targetUser['role'] === 'kasir') {
            $manager = $this->userModel->getById((int) ($_POST['assigned_admin_id'] ?? 0));
            if (!$manager || $manager['role'] !== 'admin' || (int) $manager['store_id'] <= 0) {
                flash('error', 'Pilih admin tenant yang valid.');
                $this->redirect('/admin/user/edit?id=' . $id);
            }
            $storeId = (int) $manager['store_id'];
            $assignedAdminId = (int) $manager['id'];
        }

        $updated = $this->userModel->update($id, [
            'name'              => $name,
            'email'             => $email,
            'role'              => $role,
            'store_id'          => $storeId,
            'assigned_admin_id' => $assignedAdminId,
        ]);

        if ($updated && $password !== '') {
            $updated = $this->userModel->updatePassword($id, $password);
        }

        flash($updated ? 'success' : 'error', $updated
            ? 'Data user berhasil diperbarui!'
            : 'Data user gagal diperbarui.');
        $this->redirect('/admin/user');
    }

    public function delete(): void
    {
        verifyCsrf();

        $targetUser = $this->findManageableUser((int) ($_POST['id'] ?? 0));

        if ((int) $targetUser['id'] === (int) $_SESSION['user_id']) {
            flash('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
            $this->redirect('/admin/user');
        }

        $deleted = $this->userModel->delete((int) $targetUser['id']);
        flash($deleted ? 'success' : 'error', $deleted ? 'User berhasil dinonaktifkan.' : 'User gagal dinonaktifkan.');
        $this->redirect('/admin/user');
    }

    public function resetPassword(): void
    {
        verifyCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        $password = $_POST['password'] ?? '';
        $targetUser = $this->findManageableUser($id);
        if (strlen($password) < 8) {
            flash('error', 'Password baru minimal 8 karakter.');
        } else {
            $this->userModel->updatePassword((int) $targetUser['id'], $password);
            flash('success', 'Password user berhasil direset.');
        }
        $this->redirect('/admin/user');
    }

    /**
     * Find a target inside the actor's store and below the actor's role.
     */
    private function findManageableUser(int $id): array
    {
        $targetUser = $this->userModel->getById($id);

        if (!$targetUser || !canManageUser($targetUser)) {
            flash('error', 'User tidak ditemukan atau tidak boleh Anda kelola.');
            $this->redirect('/admin/user');
        }

        return $targetUser;
    }
}
