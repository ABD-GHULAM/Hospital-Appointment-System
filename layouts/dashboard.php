<?php
/**
 * Dashboard layout wrapper
 * Usage: set $pageTitle, $content (or use ob_start), then include this
 */
require_once APP_ROOT . '/includes/models.php';
require_auth();
?>
<?php include APP_ROOT . '/layouts/head.php'; ?>

<div class="flex h-full min-h-screen">
    <?php include APP_ROOT . '/layouts/sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0 lg:ml-0">
        <?php include APP_ROOT . '/layouts/header.php'; ?>

        <main class="flex-1 p-4 lg:p-8 overflow-auto">
            <div class="animate-fade-in max-w-7xl mx-auto">
                <?php if (isset($content)) echo $content; ?>
            </div>
        </main>
    </div>
</div>

<?php include APP_ROOT . '/layouts/footer.php'; ?>
