<!-- Top Header -->
<header class="sticky top-0 z-30 bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border-b border-gray-200/50 dark:border-slate-700/50">
    <div class="flex items-center justify-between h-16 px-4 lg:px-8">
        <!-- Mobile menu button -->
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
            <i data-lucide="menu" class="w-5 h-5"></i>
        </button>

        <!-- Page title -->
        <div class="hidden lg:block">
            <h2 class="text-lg font-semibold"><?= e($pageTitle ?? 'Dashboard') ?></h2>
            <?php if (!empty($pageSubtitle)): ?>
            <p class="text-sm text-gray-500 dark:text-gray-400"><?= e($pageSubtitle) ?></p>
            <?php endif; ?>
        </div>

        <!-- Right actions -->
        <div class="flex items-center gap-3">
            <!-- Search (desktop) -->
            <?php if (!empty($showSearch)): ?>
            <div class="hidden md:flex items-center">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="text" placeholder="Search..." 
                           class="pl-10 pr-4 py-2 w-64 text-sm bg-gray-100 dark:bg-slate-700 border-0 rounded-xl focus:ring-2 focus:ring-primary-500 transition-all">
                </div>
            </div>
            <?php endif; ?>

            <!-- Theme toggle -->
            <button @click="darkMode = !darkMode" 
                    class="p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition-all duration-300 group">
                <i data-lucide="sun" class="w-5 h-5 hidden dark:block text-amber-400 group-hover:rotate-45 transition-transform"></i>
                <i data-lucide="moon" class="w-5 h-5 block dark:hidden text-gray-600 group-hover:-rotate-12 transition-transform"></i>
            </button>

            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="relative p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <span id="notification-badge" class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white dark:ring-slate-800"></span>
                </button>
                <!-- Notification Panel -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     @click.outside="open = false"
                     class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-gray-200 dark:border-slate-700 z-50">
                    <div class="p-4 border-b border-gray-200 dark:border-slate-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Notifications</h3>
                    </div>
                    <div id="notifications-list" class="max-h-80 overflow-y-auto">
                        <!-- Notifications will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
