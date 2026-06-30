<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_admin();

$patientModel = new PatientModel();
$search = sanitize($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));

$total = $patientModel->count($search);
$pagination = paginate($total, $page);
$patients = $patientModel->getAll($search, $pagination['per_page'], $pagination['offset']);

$pageTitle = 'Patients';
ob_start();
render_page_header('Patients', 'Manage registered patients', base_url('admin/patients/create.php'), 'Add Patient');
render_search_filter_form(base_url('admin/patients/index.php'), ['search' => $search]);
?>

<div class="glass-card rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full data-table">
            <thead class="bg-gray-50 dark:bg-slate-800/50">
                <tr><th>Patient</th><th>Gender</th><th>Age</th><th>Blood Group</th><th>Phone</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($patients)): ?>
                <tr><td colspan="6"><?php render_empty_state('No patients found', 'No patients registered yet.', 'users'); ?></td></tr>
                <?php else: foreach ($patients as $p): ?>
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <img src="<?= profile_image($p['profile_image'], $p['full_name']) ?>" class="w-10 h-10 rounded-full object-cover" alt="">
                            <div><p class="font-medium"><?= e($p['full_name']) ?></p><p class="text-xs text-gray-500"><?= e($p['email']) ?></p></div>
                        </div>
                    </td>
                    <td class="capitalize"><?= e($p['gender']) ?></td>
                    <td><?= e($p['age']) ?></td>
                    <td><?= e($p['blood_group'] ?: '-') ?></td>
                    <td><?= e($p['phone']) ?></td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="<?= base_url('admin/patients/edit.php?id=' . $p['id']) ?>" class="p-2 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg"><i data-lucide="pencil" class="w-4 h-4 text-gray-500"></i></a>
                            <button onclick="handleDelete('<?= base_url('admin/patients/delete.php?id=' . $p['id']) ?>')" class="p-2 hover:bg-red-50 rounded-lg"><i data-lucide="trash-2" class="w-4 h-4 text-red-500"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php render_pagination($pagination); ?>
</div>

<?php $content = ob_get_clean(); include APP_ROOT . '/layouts/dashboard.php'; ?>
