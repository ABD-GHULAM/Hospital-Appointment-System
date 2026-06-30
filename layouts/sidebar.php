<?php
$user = current_user();
$role = $user['role'] ?? '';

$navItems = match ($role) {
    'admin' => [
        ['icon' => 'layout-dashboard', 'label' => 'Dashboard', 'url' => base_url('admin/dashboard.php')],
        ['icon' => 'calendar', 'label' => 'Appointments', 'url' => base_url('admin/appointments/index.php')],
        ['icon' => 'stethoscope', 'label' => 'Doctors', 'url' => base_url('admin/doctors/index.php')],
        ['icon' => 'users', 'label' => 'Patients', 'url' => base_url('admin/patients/index.php')],
        ['icon' => 'user-cog', 'label' => 'Users', 'url' => base_url('admin/users/index.php')],
    ],
    'doctor' => [
        ['icon' => 'layout-dashboard', 'label' => 'Dashboard', 'url' => base_url('doctor/dashboard.php')],
        ['icon' => 'calendar', 'label' => 'Appointments', 'url' => base_url('doctor/appointments.php')],
        ['icon' => 'history', 'label' => 'Patient History', 'url' => base_url('doctor/patient-history.php')],
        ['icon' => 'user', 'label' => 'My Profile', 'url' => base_url('doctor/profile.php')],
    ],
    'patient' => [
        ['icon' => 'layout-dashboard', 'label' => 'Dashboard', 'url' => base_url('patient/dashboard.php')],
        ['icon' => 'calendar-plus', 'label' => 'Book Appointment', 'url' => base_url('patient/book.php')],
        ['icon' => 'stethoscope', 'label' => 'Find Doctors', 'url' => base_url('patient/doctors.php')],
        ['icon' => 'calendar', 'label' => 'My Appointments', 'url' => base_url('patient/appointments.php')],
        ['icon' => 'user', 'label' => 'My Profile', 'url' => base_url('patient/profile.php')],
    ],
    default => [],
};

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Mobile sidebar backdrop -->
<div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden"></div>

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-72 bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border-r border-gray-200/50 dark:border-slate-700/50 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col">
    
    <!-- Logo -->
    <div class="flex items-center gap-3 px-6 h-16 border-b border-gray-200/50 dark:border-slate-700/50">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-lg shadow-primary-500/25">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </div>
        <div>
            <h1 class="font-bold text-lg bg-gradient-to-r from-primary-600 to-primary-400 bg-clip-text text-transparent"><?= APP_NAME ?></h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 capitalize"><?= e($role) ?> Panel</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <?php foreach ($navItems as $item): 
            $isActive = str_contains($_SERVER['REQUEST_URI'], basename(dirname($item['url']))) && 
                       (basename($item['url']) === $currentPage || 
                        (str_contains($item['url'], $currentPage)));
            // Simpler active check
            $isActive = str_contains($_SERVER['REQUEST_URI'], str_replace(base_url(''), '', $item['url']));
        ?>
        <a href="<?= $item['url'] ?>"
           class="sidebar-link group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                  <?= $isActive 
                      ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm' 
                      : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700/50 hover:text-gray-900 dark:hover:text-white' ?>">
            <i data-lucide="<?= $item['icon'] ?>" class="w-5 h-5 <?= $isActive ? 'text-primary-600 dark:text-primary-400' : '' ?>"></i>
            <?= e($item['label']) ?>
            <?php if ($isActive): ?>
            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-primary-500"></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </nav>

    <!-- User section -->
    <div class="p-4 border-t border-gray-200/50 dark:border-slate-700/50">
        <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-slate-700/30">
            <img src="<?= profile_image($user['profile_image'] ?? null, $user['full_name'] ?? 'U') ?>" 
                 alt="Profile" class="w-10 h-10 rounded-full object-cover ring-2 ring-primary-500/20">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold truncate"><?= e($user['full_name'] ?? '') ?></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?= e($user['email'] ?? '') ?></p>
            </div>
        </div>
        <a href="<?= base_url('auth/logout.php') ?>" 
           class="mt-2 flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            Logout
        </a>
    </div>
</aside>
