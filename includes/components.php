<?php
/**
 * Reusable UI components
 */

function render_stat_card(string $title, int $value, string $icon, string $color = 'primary', ?string $subtitle = null): void
{
    $colors = [
        'primary' => 'from-primary-500 to-primary-600 shadow-primary-500/20',
        'emerald' => 'from-emerald-500 to-emerald-600 shadow-emerald-500/20',
        'amber'   => 'from-amber-500 to-amber-600 shadow-amber-500/20',
        'blue'    => 'from-blue-500 to-blue-600 shadow-blue-500/20',
        'red'     => 'from-red-500 to-red-600 shadow-red-500/20',
        'purple'  => 'from-purple-500 to-purple-600 shadow-purple-500/20',
    ];
    $gradient = $colors[$color] ?? $colors['primary'];
    ?>
    <div class="stat-card glass-card rounded-2xl p-6 animate-slide-up">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?= e($title) ?></p>
                <p class="text-3xl font-bold mt-2 count-animate" data-counter="<?= $value ?>">0</p>
                <?php if ($subtitle): ?>
                <p class="text-xs text-gray-400 mt-1"><?= e($subtitle) ?></p>
                <?php endif; ?>
            </div>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br <?= $gradient ?> flex items-center justify-center shadow-lg">
                <i data-lucide="<?= e($icon) ?>" class="w-6 h-6 text-white"></i>
            </div>
        </div>
    </div>
    <?php
}

function render_pagination(array $pagination): void
{
    if ($pagination['total_pages'] <= 1) return;
    ?>
    <div class="flex items-center justify-between mt-6 px-2">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Showing <?= ($pagination['offset'] + 1) ?> to <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> results
        </p>
        <div class="flex items-center gap-1">
            <?php if ($pagination['current'] > 1): ?>
            <a href="<?= pagination_url($pagination['current'] - 1) ?>" class="pagination-btn">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </a>
            <?php endif; ?>

            <?php for ($i = max(1, $pagination['current'] - 2); $i <= min($pagination['total_pages'], $pagination['current'] + 2); $i++): ?>
            <a href="<?= pagination_url($i) ?>" class="pagination-btn <?= $i === $pagination['current'] ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($pagination['current'] < $pagination['total_pages']): ?>
            <a href="<?= pagination_url($pagination['current'] + 1) ?>" class="pagination-btn">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function render_empty_state(string $title, string $message, string $icon = 'inbox'): void
{
    ?>
    <div class="empty-state">
        <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-slate-700 flex items-center justify-center mb-4">
            <i data-lucide="<?= e($icon) ?>" class="w-8 h-8 text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white"><?= e($title) ?></h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm"><?= e($message) ?></p>
    </div>
    <?php
}

function render_status_badge(string $status): void
{
    ?>
    <span class="status-badge <?= status_badge($status) ?>"><?= ucfirst(e($status)) ?></span>
    <?php
}

function render_page_header(string $title, ?string $subtitle = null, ?string $actionUrl = null, ?string $actionLabel = null): void
{
    ?>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold"><?= e($title) ?></h1>
            <?php if ($subtitle): ?>
            <p class="text-gray-500 dark:text-gray-400 mt-1"><?= e($subtitle) ?></p>
            <?php endif; ?>
        </div>
        <?php if ($actionUrl && $actionLabel): ?>
        <a href="<?= $actionUrl ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl transition-colors shadow-lg shadow-primary-500/25 btn-ripple">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <?= e($actionLabel) ?>
        </a>
        <?php endif; ?>
    </div>
    <?php
}

function render_search_filter_form(string $action, array $filters = []): void
{
    ?>
    <form method="GET" action="<?= $action ?>" class="glass-card rounded-2xl p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text" name="search" value="<?= e($filters['search'] ?? '') ?>" placeholder="Search..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
            </div>
            <?php if (isset($filters['status_options'])): ?>
            <select name="status" class="px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                <option value="">All Status</option>
                <?php foreach ($filters['status_options'] as $opt): ?>
                <option value="<?= $opt ?>" <?= ($filters['status'] ?? '') === $opt ? 'selected' : '' ?>><?= ucfirst($opt) ?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
            <?php if (isset($filters['doctor_options'])): ?>
            <select name="doctor_id" class="px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                <option value="">All Doctors</option>
                <?php foreach ($filters['doctor_options'] as $doc): ?>
                <option value="<?= $doc['id'] ?>" <?= ($filters['doctor_id'] ?? '') == $doc['id'] ? 'selected' : '' ?>><?= e($doc['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
            <?php if (isset($filters['specialization_options'])): ?>
            <select name="specialization" class="px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                <option value="">All Specializations</option>
                <?php foreach ($filters['specialization_options'] as $spec): ?>
                <option value="<?= e($spec) ?>" <?= ($filters['specialization'] ?? '') === $spec ? 'selected' : '' ?>><?= e($spec) ?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
            <input type="date" name="date" value="<?= e($filters['date'] ?? '') ?>"
                   class="px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
            <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl transition-colors">
                Filter
            </button>
            <a href="<?= $action ?>" class="px-5 py-2.5 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-xl transition-colors text-center">Reset</a>
        </div>
    </form>
    <?php
}
