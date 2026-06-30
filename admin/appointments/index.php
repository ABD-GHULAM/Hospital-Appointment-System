<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_admin();

$appointmentModel = new AppointmentModel();
$doctorModel = new DoctorModel();

$filters = [
    'search'    => sanitize($_GET['search'] ?? ''),
    'status'    => sanitize($_GET['status'] ?? ''),
    'doctor_id' => (int)($_GET['doctor_id'] ?? 0) ?: null,
    'date'      => sanitize($_GET['date'] ?? ''),
];
$page = max(1, (int)($_GET['page'] ?? 1));

$total = $appointmentModel->count($filters);
$pagination = paginate($total, $page);
$appointments = $appointmentModel->getAll($filters, $pagination['per_page'], $pagination['offset']);
$doctors = $doctorModel->getAll('', '', 100, 0);

$pageTitle = 'Appointments';
ob_start();
render_page_header('Appointments', 'Manage all clinic appointments', base_url('admin/appointments/create.php'), 'New Appointment');
render_search_filter_form(base_url('admin/appointments/index.php'), array_merge($filters, [
    'status_options' => ['pending','approved','rejected','completed','cancelled'],
    'doctor_options' => $doctors,
]));
?>

<div class="glass-card rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full data-table">
            <thead class="bg-gray-50 dark:bg-slate-800/50">
                <tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Reason</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($appointments)): ?>
                <tr><td colspan="7"><?php render_empty_state('No appointments', 'No appointments match your filters.'); ?></td></tr>
                <?php else: foreach ($appointments as $apt): ?>
                <tr>
                    <td class="font-medium"><?= e($apt['patient_name']) ?></td>
                    <td><?= e($apt['doctor_name']) ?></td>
                    <td><?= format_date($apt['appointment_date']) ?></td>
                    <td><?= format_time($apt['appointment_time']) ?></td>
                    <td class="max-w-xs truncate"><?= e($apt['reason']) ?></td>
                    <td><?php render_status_badge($apt['status']); ?></td>
                    <td>
                        <div class="flex items-center gap-1" x-data="{ open: false }">
                            <?php if ($apt['status'] === 'pending'): ?>
                            <form method="POST" action="<?= base_url('admin/appointments/status.php') ?>" class="inline"><?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $apt['id'] ?>"><input type="hidden" name="status" value="approved">
                                <button class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg" title="Approve"><i data-lucide="check" class="w-4 h-4"></i></button>
                            </form>
                            <form method="POST" action="<?= base_url('admin/appointments/status.php') ?>" class="inline"><?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $apt['id'] ?>"><input type="hidden" name="status" value="rejected">
                                <button class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg" title="Reject"><i data-lucide="x" class="w-4 h-4"></i></button>
                            </form>
                            <?php endif; ?>
                            <a href="<?= base_url('admin/appointments/edit.php?id=' . $apt['id']) ?>" class="p-1.5 hover:bg-gray-100 rounded-lg"><i data-lucide="pencil" class="w-4 h-4 text-gray-500"></i></a>
                            <button onclick="handleDelete('<?= base_url('admin/appointments/delete.php?id=' . $apt['id']) ?>')" class="p-1.5 hover:bg-red-50 rounded-lg"><i data-lucide="trash-2" class="w-4 h-4 text-red-500"></i></button>
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
