<?php
require_once dirname(__DIR__) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_patient();

$patientModel = new PatientModel();
$appointmentModel = new AppointmentModel();
$dashboardModel = new DashboardModel();

$patient = $patientModel->findByUserId(current_user()['id']);
if (!$patient) {
    // Auto-create patient record if not exists
    $patientModel->createForExistingUser(
        current_user()['id'],
        ['gender' => 'other', 'age' => 30, 'blood_group' => null, 'phone' => '0000000000', 'address' => '']
    );
    $patient = $patientModel->findByUserId(current_user()['id']);
}
$stats = $dashboardModel->getPatientStats($patient['id']);
$upcoming = $appointmentModel->getAll(['patient_id' => $patient['id'], 'upcoming' => true], 5, 0);
$recent = $appointmentModel->getAll(['patient_id' => $patient['id']], 5, 0, 'a.created_at DESC');

$pageTitle = 'Dashboard';
ob_start();
render_page_header('Welcome, ' . e(explode(' ', $patient['full_name'])[0]), 'Manage your health appointments');
?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php render_stat_card('Upcoming', $stats['upcoming'], 'calendar', 'blue'); ?>
    <?php render_stat_card('Completed', $stats['completed'], 'check-circle', 'emerald'); ?>
    <?php render_stat_card('Pending', $stats['pending'], 'clock', 'amber'); ?>
    <?php render_stat_card('Total Visits', $stats['total'], 'heart-pulse', 'primary'); ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <a href="<?= base_url('patient/book.php') ?>" class="glass-card rounded-2xl p-6 hover:shadow-lg transition-all group stat-card">
        <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
            <i data-lucide="calendar-plus" class="w-6 h-6 text-primary-600"></i>
        </div>
        <h3 class="font-semibold">Book Appointment</h3>
        <p class="text-sm text-gray-500 mt-1">Schedule a visit with a doctor</p>
    </a>
    <a href="<?= base_url('patient/doctors.php') ?>" class="glass-card rounded-2xl p-6 hover:shadow-lg transition-all group stat-card">
        <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
            <i data-lucide="stethoscope" class="w-6 h-6 text-emerald-600"></i>
        </div>
        <h3 class="font-semibold">Find Doctors</h3>
        <p class="text-sm text-gray-500 mt-1">Browse available specialists</p>
    </a>
    <a href="<?= base_url('patient/appointments.php') ?>" class="glass-card rounded-2xl p-6 hover:shadow-lg transition-all group stat-card">
        <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
            <i data-lucide="history" class="w-6 h-6 text-amber-600"></i>
        </div>
        <h3 class="font-semibold">My Appointments</h3>
        <p class="text-sm text-gray-500 mt-1">View appointment history</p>
    </a>
</div>

<div class="glass-card rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-gray-100 dark:border-slate-700/50 flex justify-between">
        <h3 class="text-lg font-semibold">Upcoming Appointments</h3>
        <a href="<?= base_url('patient/appointments.php') ?>" class="text-sm text-primary-600 font-medium">View All</a>
    </div>
    <div class="p-4">
        <?php if (empty($upcoming)): ?>
        <p class="text-sm text-gray-500 text-center py-8">No upcoming appointments. <a href="<?= base_url('patient/book.php') ?>" class="text-primary-600">Book one now</a></p>
        <?php else: foreach ($upcoming as $apt): ?>
        <div class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-slate-700/30 rounded-xl">
            <div class="flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($apt['doctor_name']) ?>&background=6366f1&color=fff" class="w-10 h-10 rounded-full" alt="">
                <div>
                    <p class="font-medium"><?= e($apt['doctor_name']) ?></p>
                    <p class="text-xs text-gray-500"><?= e($apt['specialization']) ?> · <?= format_date($apt['appointment_date']) ?> <?= format_time($apt['appointment_time']) ?></p>
                </div>
            </div>
            <?php render_status_badge($apt['status']); ?>
        </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); include APP_ROOT . '/layouts/dashboard.php'; ?>
