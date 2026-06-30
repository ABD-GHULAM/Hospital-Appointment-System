<?php
require_once dirname(__DIR__) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_doctor();

$doctorModel = new DoctorModel();
$appointmentModel = new AppointmentModel();
$doctor = $doctorModel->findByUserId(current_user()['id']);

$filters = [
    'doctor_id' => $doctor['id'],
    'status'    => sanitize($_GET['status'] ?? ''),
    'date'      => sanitize($_GET['date'] ?? ''),
    'search'    => sanitize($_GET['search'] ?? ''),
];
$page = max(1, (int)($_GET['page'] ?? 1));
$total = $appointmentModel->count($filters);
$pagination = paginate($total, $page);
$appointments = $appointmentModel->getAll($filters, $pagination['per_page'], $pagination['offset']);

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_csrf();
    $id = (int)$_POST['appointment_id'];
    $apt = $appointmentModel->findById($id);
    if ($apt && $apt['doctor_id'] == $doctor['id']) {
        if ($_POST['action'] === 'complete') {
            $appointmentModel->updateStatus($id, 'completed', sanitize($_POST['notes'] ?? ''));
            flash('success', 'Appointment marked as completed.');
        } elseif ($_POST['action'] === 'add_notes') {
            $appointmentModel->update($id, ['notes' => sanitize($_POST['notes'] ?? '')]);
            flash('success', 'Notes saved.');
        }
    }
    redirect(base_url('doctor/appointments.php'));
}

$pageTitle = 'Appointments';
ob_start();
render_page_header('My Appointments', 'Manage your patient appointments');
render_search_filter_form(base_url('doctor/appointments.php'), [
    'search' => $filters['search'], 'status' => $filters['status'], 'date' => $filters['date'],
    'status_options' => ['pending','approved','completed','cancelled'],
]);
?>

<div class="glass-card rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full data-table">
            <thead class="bg-gray-50 dark:bg-slate-800/50">
                <tr><th>Patient</th><th>Date</th><th>Time</th><th>Reason</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($appointments)): ?>
                <tr><td colspan="6"><?php render_empty_state('No appointments', 'No appointments found.'); ?></td></tr>
                <?php else: foreach ($appointments as $apt): ?>
                <tr>
                    <td class="font-medium"><?= e($apt['patient_name']) ?></td>
                    <td><?= format_date($apt['appointment_date']) ?></td>
                    <td><?= format_time($apt['appointment_time']) ?></td>
                    <td class="max-w-xs truncate"><?= e($apt['reason']) ?></td>
                    <td><?php render_status_badge($apt['status']); ?></td>
                    <td>
                        <?php if ($apt['status'] === 'approved'): ?>
                        <form method="POST" class="inline-flex gap-1"><?= csrf_field() ?>
                            <input type="hidden" name="appointment_id" value="<?= $apt['id'] ?>">
                            <input type="hidden" name="action" value="complete">
                            <input type="text" name="notes" placeholder="Notes (optional)" class="px-2 py-1 text-xs border rounded-lg w-32">
                            <button class="px-3 py-1.5 text-xs bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Complete</button>
                        </form>
                        <?php elseif ($apt['notes']): ?>
                        <span class="text-xs text-gray-500" title="<?= e($apt['notes']) ?>">Has notes</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php render_pagination($pagination); ?>
</div>

<?php $content = ob_get_clean(); include APP_ROOT . '/layouts/dashboard.php'; ?>
