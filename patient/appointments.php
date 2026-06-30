<?php
require_once dirname(__DIR__) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_patient();

$patientModel = new PatientModel();
$appointmentModel = new AppointmentModel();
$patient = $patientModel->findByUserId(current_user()['id']);

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
    require_csrf();
    $id = (int)$_POST['cancel_id'];
    $apt = $appointmentModel->findById($id);
    if ($apt && $apt['patient_id'] == $patient['id'] && in_array($apt['status'], ['pending', 'approved'])) {
        $appointmentModel->updateStatus($id, 'cancelled');
        flash('success', 'Appointment cancelled.');
    }
    redirect(base_url('patient/appointments.php'));
}

$filters = [
    'patient_id' => $patient['id'],
    'status' => sanitize($_GET['status'] ?? ''),
    'search' => sanitize($_GET['search'] ?? ''),
];
$page = max(1, (int)($_GET['page'] ?? 1));
$total = $appointmentModel->count($filters);
$pagination = paginate($total, $page);
$appointments = $appointmentModel->getAll($filters, $pagination['per_page'], $pagination['offset']);

$pageTitle = 'My Appointments';
ob_start();
render_page_header('My Appointments', 'View and manage your appointments');
render_search_filter_form(base_url('patient/appointments.php'), [
    'search' => $filters['search'], 'status' => $filters['status'],
    'status_options' => ['pending','approved','completed','cancelled','rejected'],
]);
?>

<div class="space-y-4">
    <?php if (empty($appointments)): ?>
    <div class="glass-card rounded-2xl"><?php render_empty_state('No appointments', 'You haven\'t booked any appointments yet.', 'calendar'); ?></div>
    <?php else: foreach ($appointments as $apt): ?>
    <div class="glass-card rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-primary-100 dark:bg-primary-900/30 flex flex-col items-center justify-center">
                <span class="text-xs font-bold text-primary-700"><?= date('M', strtotime($apt['appointment_date'])) ?></span>
                <span class="text-lg font-bold text-primary-700"><?= date('d', strtotime($apt['appointment_date'])) ?></span>
            </div>
            <div>
                <p class="font-semibold"><?= e($apt['doctor_name']) ?></p>
                <p class="text-sm text-gray-500"><?= e($apt['specialization']) ?> · <?= format_time($apt['appointment_time']) ?></p>
                <p class="text-xs text-gray-400 mt-1"><?= e($apt['reason']) ?></p>
                <?php if ($apt['notes']): ?><p class="text-xs text-gray-400 mt-1 italic">Doctor notes: <?= e($apt['notes']) ?></p><?php endif; ?>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <?php render_status_badge($apt['status']); ?>
            <?php if (in_array($apt['status'], ['pending', 'approved'])): ?>
            <form method="POST" onsubmit="event.preventDefault(); confirmAction('Cancel this appointment?', () => this.submit())">
                <?= csrf_field() ?>
                <input type="hidden" name="cancel_id" value="<?= $apt['id'] ?>">
                <button class="px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors">Cancel</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; endif; ?>
</div>
<?php render_pagination($pagination); ?>

<?php $content = ob_get_clean(); include APP_ROOT . '/layouts/dashboard.php'; ?>
