<?php
require_once dirname(__DIR__) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_doctor();

$doctorModel = new DoctorModel();
$appointmentModel = new AppointmentModel();
$doctor = $doctorModel->findByUserId(current_user()['id']);

$search = sanitize($_GET['search'] ?? '');
$appointments = $appointmentModel->getAll([
    'doctor_id' => $doctor['id'],
    'search' => $search,
    'status' => 'completed',
], 50, 0);

// Group by patient
$patientHistory = [];
foreach ($appointments as $apt) {
    $patientHistory[$apt['patient_id']]['name'] = $apt['patient_name'];
    $patientHistory[$apt['patient_id']]['appointments'][] = $apt;
}

$pageTitle = 'Patient History';
ob_start();
render_page_header('Patient History', 'View completed appointment records');
?>

<form method="GET" class="mb-6">
    <div class="relative max-w-md">
        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
        <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search patients..."
               class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
    </div>
</form>

<?php if (empty($patientHistory)): ?>
<div class="glass-card rounded-2xl"><?php render_empty_state('No history', 'No completed appointments yet.', 'history'); ?></div>
<?php else: foreach ($patientHistory as $patientId => $data): ?>
<div class="glass-card rounded-2xl mb-6 overflow-hidden">
    <div class="p-6 border-b border-gray-100 dark:border-slate-700/50 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
            <span class="text-lg font-bold text-primary-700"><?= strtoupper(substr($data['name'], 0, 1)) ?></span>
        </div>
        <div><h3 class="font-semibold"><?= e($data['name']) ?></h3>
        <p class="text-sm text-gray-500"><?= count($data['appointments']) ?> visit(s)</p></div>
    </div>
    <div class="divide-y divide-gray-100 dark:divide-slate-700/50">
        <?php foreach ($data['appointments'] as $apt): ?>
        <div class="p-4 flex items-start justify-between">
            <div>
                <p class="text-sm font-medium"><?= format_date($apt['appointment_date']) ?> - <?= format_time($apt['appointment_time']) ?></p>
                <p class="text-sm text-gray-500 mt-1"><?= e($apt['reason']) ?></p>
                <?php if ($apt['notes']): ?><p class="text-xs text-gray-400 mt-2 italic">Notes: <?= e($apt['notes']) ?></p><?php endif; ?>
            </div>
            <?php render_status_badge($apt['status']); ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; endif; ?>

<?php $content = ob_get_clean(); include APP_ROOT . '/layouts/dashboard.php'; ?>
