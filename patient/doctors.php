<?php
require_once dirname(__DIR__) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_patient();

$doctorModel = new DoctorModel();
$search = sanitize($_GET['search'] ?? '');
$specialization = sanitize($_GET['specialization'] ?? '');
$doctors = $doctorModel->getAll($search, $specialization, 50, 0);
$specializations = $doctorModel->getSpecializations();

$pageTitle = 'Find Doctors';
ob_start();
render_page_header('Find Doctors', 'Browse our medical specialists');
render_search_filter_form(base_url('patient/doctors.php'), [
    'search' => $search, 'specialization' => $specialization, 'specialization_options' => $specializations,
]);
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($doctors)): ?>
    <div class="col-span-full glass-card rounded-2xl"><?php render_empty_state('No doctors found', 'Try adjusting your search filters.'); ?></div>
    <?php else: foreach ($doctors as $doc): ?>
    <div class="glass-card rounded-2xl p-6 stat-card">
        <div class="flex items-start gap-4">
            <img src="<?= profile_image($doc['profile_image'], $doc['full_name']) ?>" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-primary-500/20" alt="">
            <div class="flex-1">
                <h3 class="font-semibold"><?= e($doc['full_name']) ?></h3>
                <span class="inline-block mt-1 px-2.5 py-0.5 bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded-lg text-xs font-medium"><?= e($doc['specialization']) ?></span>
            </div>
        </div>
        <div class="mt-4 space-y-2 text-sm text-gray-500">
            <p class="flex items-center gap-2"><i data-lucide="award" class="w-4 h-4"></i> <?= e($doc['experience']) ?> years experience</p>
            <p class="flex items-center gap-2"><i data-lucide="banknote" class="w-4 h-4"></i> <?= format_currency((float)$doc['consultation_fee']) ?></p>
            <p class="flex items-center gap-2"><i data-lucide="calendar" class="w-4 h-4"></i> <?= e($doc['available_days']) ?></p>
        </div>
        <p class="text-xs text-gray-400 mt-3 line-clamp-2"><?= e($doc['qualification']) ?></p>
        <a href="<?= base_url('patient/book.php?doctor_id=' . $doc['id']) ?>" class="mt-4 block w-full text-center py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl transition-colors">
            Book Appointment
        </a>
    </div>
    <?php endforeach; endif; ?>
</div>

<?php $content = ob_get_clean(); include APP_ROOT . '/layouts/dashboard.php'; ?>
