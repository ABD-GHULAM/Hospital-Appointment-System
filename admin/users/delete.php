<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';

require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $id = (int)($_GET['id'] ?? 0);
    $userModel = new UserModel();
    $user = $userModel->findById($id);
    if ($user && $user['role'] !== 'admin') {
        delete_profile_image($user['profile_image']);
        $userModel->delete($id);
        flash('success', 'User deleted.');
    }
}
redirect(base_url('admin/users/index.php'));
