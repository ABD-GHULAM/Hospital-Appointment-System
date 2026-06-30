<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_admin();
$id = (int)($_GET['id'] ?? 0);
$userModel = new UserModel();
$user = $userModel->findById($id);
if (!$user) { flash('error', 'Not found.'); redirect(base_url('admin/users/index.php')); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    // Sanitize all scalar values
    $data = [];
    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            $data[$key] = array_map('sanitize', $value);
        } else {
            $data[$key] = sanitize($value);
        }
    }
    $updateData = ['full_name' => $data['full_name'], 'email' => $data['email'], 'role' => $data['role']];
    if (!empty($_POST['password'])) $updateData['password'] = $_POST['password'];
    if (!empty($_FILES['profile_image']['name'])) {
        delete_profile_image($user['profile_image']);
        $updateData['profile_image'] = upload_profile_image($_FILES['profile_image']);
    }
    $userModel->update($id, $updateData);
    flash('success', 'User updated.');
    redirect(base_url('admin/users/index.php'));
}

$pageTitle = 'Edit User';
ob_start(); $isEdit = true; include __DIR__ . '/_form.php';
$content = ob_get_clean(); include APP_ROOT . '/layouts/dashboard.php';
