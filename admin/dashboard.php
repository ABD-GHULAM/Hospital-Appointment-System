<?php
require_once dirname(__DIR__) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_admin();

$dashboardModel = new DashboardModel();
$appointmentModel = new AppointmentModel();
$stats = $dashboardModel->getAdminStats();
$recentAppointments = $appointmentModel->getRecent(5);
$monthlyStats = $appointmentModel->getMonthlyStats();
$statusDist = $appointmentModel->getStatusDistribution();

$pageTitle = 'Dashboard';
$pageSubtitle = 'Overview of clinic operations';

ob_start();
?>

<?php render_page_header('Dashboard', 'Welcome back! Here\'s what\'s happening today.'); ?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php render_stat_card('Total Patients', $stats['total_patients'], 'users', 'primary'); ?>
    <?php render_stat_card('Total Doctors', $stats['total_doctors'], 'stethoscope', 'emerald'); ?>
    <?php render_stat_card("Today's Appointments", $stats['today_appointments'], 'calendar', 'blue'); ?>
    <?php render_stat_card('Pending', $stats['pending'], 'clock', 'amber'); ?>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <?php render_stat_card('Completed', $stats['completed'], 'check-circle', 'emerald'); ?>
    <?php render_stat_card('Cancelled', $stats['cancelled'], 'x-circle', 'red'); ?>
    <?php render_stat_card('Total Appointments', $stats['total_appointments'], 'calendar-days', 'purple'); ?>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-semibold mb-4">Monthly Appointments</h3>
        <canvas id="monthlyChart" height="250"></canvas>
    </div>
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-semibold mb-4">Status Distribution</h3>
        <canvas id="statusChart" height="250"></canvas>
    </div>
</div>

<!-- Recent Appointments -->
<div class="glass-card rounded-2xl overflow-hidden">
    <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-slate-700/50">
        <h3 class="text-lg font-semibold">Recent Appointments</h3>
        <a href="<?= base_url('admin/appointments/index.php') ?>" class="text-sm text-primary-600 hover:text-primary-700 font-medium">View All</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full data-table">
            <thead class="bg-gray-50 dark:bg-slate-800/50">
                <tr>
                    <th class="data-table">Patient</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentAppointments)): ?>
                <tr><td colspan="5"><?php render_empty_state('No appointments', 'No recent appointments found.'); ?></td></tr>
                <?php else: foreach ($recentAppointments as $apt): ?>
                <tr>
                    <td class="font-medium"><?= e($apt['patient_name']) ?></td>
                    <td><?= e($apt['doctor_name']) ?></td>
                    <td><?= format_date($apt['appointment_date']) ?></td>
                    <td><?= format_time($apt['appointment_time']) ?></td>
                    <td><?php render_status_badge($apt['status']); ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    createChart('monthlyChart', {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($monthlyStats, 'month')) ?>,
            datasets: [{
                label: 'Total',
                data: <?= json_encode(array_column($monthlyStats, 'total')) ?>,
                borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.1)', fill: true, tension: 0.4
            }, {
                label: 'Completed',
                data: <?= json_encode(array_column($monthlyStats, 'completed')) ?>,
                borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', fill: true, tension: 0.4
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
    });

    createChart('statusChart', {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_map('ucfirst', array_column($statusDist, 'status'))) ?>,
            datasets: [{
                data: <?= json_encode(array_column($statusDist, 'count')) ?>,
                backgroundColor: ['#f59e0b','#10b981','#ef4444','#3b82f6','#6b7280']
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
});
</script>

<?php
$content = ob_get_clean();
include APP_ROOT . '/layouts/dashboard.php';
