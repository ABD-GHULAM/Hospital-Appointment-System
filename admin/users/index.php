<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_admin();

$userModel = new UserModel();
$search = sanitize($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));

$total = $userModel->count($search);
$pagination = paginate($total, $page);
$users = $userModel->getAll($search, $pagination['per_page'], $pagination['offset']);

$pageTitle = 'Users';
ob_start();
render_page_header('Users', 'Manage system users', base_url('admin/users/create.php'), 'Add User');
render_search_filter_form(base_url('admin/users/index.php'), ['search' => $search]);
?>

<div class="glass-card rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full data-table">
            <thead class="bg-gray-50 dark:bg-slate-800/50">
                <tr><th>User</th><th>Role</th><th>Joined</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <img src="<?= profile_image($u['profile_image'], $u['full_name']) ?>" class="w-10 h-10 rounded-full object-cover" alt="">
                            <div><p class="font-medium"><?= e($u['full_name']) ?></p><p class="text-xs text-gray-500"><?= e($u['email']) ?></p></div>
                        </div>
                    </td>
                    <td><span class="px-2.5 py-1 rounded-lg text-xs font-medium capitalize <?= $u['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : ($u['role'] === 'doctor' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700') ?>"><?= e($u['role']) ?></span></td>
                    <td><?= format_date($u['created_at']) ?></td>
                    <td>
                        <?php if ($u['role'] !== 'admin' || $u['id'] != current_user()['id']): ?>
                        <div class="flex gap-2">
                            <a href="<?= base_url('admin/users/edit.php?id=' . $u['id']) ?>" class="p-2 hover:bg-gray-100 rounded-lg"><i data-lucide="pencil" class="w-4 h-4 text-gray-500"></i></a>
                            <?php if ($u['role'] !== 'admin'): ?>
                            <button onclick="handleDelete('<?= base_url('admin/users/delete.php?id=' . $u['id']) ?>')" class="p-2 hover:bg-red-50 rounded-lg"><i data-lucide="trash-2" class="w-4 h-4 text-red-500"></i></button>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php render_pagination($pagination); ?>
</div>

<?php $content = ob_get_clean(); include APP_ROOT . '/layouts/dashboard.php'; ?>
