<?php
require_once dirname(__DIR__) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_doctor();

$doctorModel = new DoctorModel();
$appointmentModel = new AppointmentModel();
$dashboardModel = new DashboardModel();

$doctor = $doctorModel->findByUserId(current_user()['id']);
if (!$doctor) {
    // Auto-create doctor record if not exists
    $doctorModel->createForExistingUser(
        current_user()['id'],
        ['specialization' => 'General Physician', 'phone' => '0000000000', 'experience' => 0, 'qualification' => 'MBBS', 'available_days' => 'Mon-Fri', 'consultation_fee' => 500]
    );
    $doctor = $doctorModel->findByUserId(current_user()['id']);
}

$stats = $dashboardModel->getDoctorStats($doctor['id']);
$todayAppointments = $appointmentModel->getAll(['doctor_id' => $doctor['id'], 'today' => true], 10, 0, 'a.appointment_time ASC');
$upcomingAppointments = $appointmentModel->getAll(['doctor_id' => $doctor['id'], 'upcoming' => true], 5, 0);

$pageTitle = 'Dashboard';
$pageSubtitle = 'Welcome, Dr. ' . explode(' ', $doctor['full_name'])[count(explode(' ', $doctor['full_name'])) > 1 ? 1 : 0];

ob_start();
render_page_header('Doctor Dashboard', "Today's schedule and upcoming appointments");
?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php render_stat_card("Today's Appointments", $stats['today'], 'calendar', 'blue'); ?>
    <?php render_stat_card('Upcoming', $stats['upcoming'], 'clock', 'amber'); ?>
    <?php render_stat_card('Completed', $stats['completed'], 'check-circle', 'emerald'); ?>
    <?php render_stat_card('Total', $stats['total'], 'calendar-days', 'primary'); ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-slate-700/50">
            <h3 class="text-lg font-semibold">Today's Schedule</h3>
        </div>
        <div class="p-4 space-y-3">
            <?php if (empty($todayAppointments)): ?>
            <p class="text-sm text-gray-500 text-center py-8">No appointments today</p>
            <?php else: foreach ($todayAppointments as $apt): ?>
            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-slate-700/30 rounded-xl">
                <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                    <span class="text-sm font-bold text-primary-700 dark:text-primary-300"><?= format_time($apt['appointment_time']) ?></span>
                </div>
                <div class="flex-1">
                    <p class="font-medium"><?= e($apt['patient_name']) ?></p>
                    <p class="text-xs text-gray-500 truncate"><?= e($apt['reason']) ?></p>
                </div>
                <?php render_status_badge($apt['status']); ?>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-slate-700/50 flex justify-between items-center">
            <h3 class="text-lg font-semibold">Upcoming</h3>
            <a href="<?= base_url('doctor/appointments.php') ?>" class="text-sm text-primary-600 font-medium">View All</a>
        </div>
        <div class="p-4 space-y-3">
            <?php if (empty($upcomingAppointments)): ?>
            <p class="text-sm text-gray-500 text-center py-8">No upcoming appointments</p>
            <?php else: foreach ($upcomingAppointments as $apt): ?>
            <div class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-slate-700/30 rounded-xl transition-colors">
                <div>
                    <p class="font-medium"><?= e($apt['patient_name']) ?></p>
                    <p class="text-xs text-gray-500"><?= format_date($apt['appointment_date']) ?> at <?= format_time($apt['appointment_time']) ?></p>
                </div>
                <?php render_status_badge($apt['status']); ?>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); include APP_ROOT . '/layouts/dashboard.php'; ?>
