<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_admin();

$doctorModel = new DoctorModel();
$search = sanitize($_GET['search'] ?? '');
$specialization = sanitize($_GET['specialization'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));

$total = $doctorModel->count($search, $specialization);
$pagination = paginate($total, $page);
$doctors = $doctorModel->getAll($search, $specialization, $pagination['per_page'], $pagination['offset']);
$specializations = $doctorModel->getSpecializations();

$pageTitle = 'Doctors';

ob_start();
render_page_header('Doctors', 'Manage clinic doctors', base_url('admin/doctors/create.php'), 'Add Doctor');
render_search_filter_form(base_url('admin/doctors/index.php'), [
    'search' => $search,
    'specialization' => $specialization,
    'specialization_options' => $specializations,
]);
?>

<div class="glass-card rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full data-table">
            <thead class="bg-gray-50 dark:bg-slate-800/50">
                <tr>
                    <th>Doctor</th><th>Specialization</th><th>Experience</th><th>Fee</th><th>Phone</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($doctors)): ?>
                <tr><td colspan="6"><?php render_empty_state('No doctors found', 'Add your first doctor to get started.', 'stethoscope'); ?></td></tr>
                <?php else: foreach ($doctors as $doc): ?>
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <img src="<?= profile_image($doc['profile_image'], $doc['full_name']) ?>" class="w-10 h-10 rounded-full object-cover" alt="">
                            <div>
                                <p class="font-medium"><?= e($doc['full_name']) ?></p>
                                <p class="text-xs text-gray-500"><?= e($doc['email']) ?></p>
                            </div>
                        </div>
                    </td>
                    <td><span class="px-2.5 py-1 bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded-lg text-xs font-medium"><?= e($doc['specialization']) ?></span></td>
                    <td><?= e($doc['experience']) ?> years</td>
                    <td><?= format_currency((float)$doc['consultation_fee']) ?></td>
                    <td><?= e($doc['phone']) ?></td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="<?= base_url('admin/doctors/edit.php?id=' . $doc['id']) ?>" class="p-2 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Edit">
                                <i data-lucide="pencil" class="w-4 h-4 text-gray-500"></i>
                            </a>
                            <button onclick="handleDelete('<?= base_url('admin/doctors/delete.php?id=' . $doc['id']) ?>', 'Delete this doctor?')" class="p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Delete">
                                <i data-lucide="trash-2" class="w-4 h-4 text-red-500"></i>
                            </button>
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
