<?php
/**
 * General helper functions
 */

/**
 * Escape output for safe HTML displays
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a URL
 */
function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

/**
 * Get base URL path
 */
function base_url(string $path = ''): string
{
    // Use APP_URL from config as the base
    $base = rtrim(APP_URL, '/');
    
    // Clean path
    $path = ltrim($path, '/');
    
    if ($path === '') {
        return $base . '/';
    }
    
    return $base . '/' . $path;
}

/**
 * Flash message - set
 */
function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Flash message - get and clear
 */
function get_flash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Format date for display
 */
function format_date(?string $date, string $format = 'M d, Y'): string
{
    if (!$date) return '-';
    return date($format, strtotime($date));
}

/**
 * Format time for display
 */
function format_time(?string $time): string
{
    if (!$time) return '-';
    return date('h:i A', strtotime($time));
}

/**
 * Format currency (IDR)
 */
function format_currency(float $amount): string
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Get status badge HTML classes
 */
function status_badge(string $status): string
{
    $badges = [
        'pending'   => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'approved'  => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
        'rejected'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        'completed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        'cancelled' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
    ];
    return $badges[$status] ?? 'bg-gray-100 text-gray-600';
}

/**
 * Sanitize input string
 */
function sanitize(string $input): string
{
    return trim(strip_tags($input));
}

/**
 * Get profile image URL or default avatar
 */
function profile_image(?string $image, string $name = 'U'): string
{
    if ($image && file_exists(APP_ROOT . '/uploads/profiles/' . $image)) {
        return UPLOAD_URL . $image;
    }
    return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=6366f1&color=fff&size=128';
}

/**
 * Paginate results
 */
function paginate(int $total, int $page, int $perPage = ITEMS_PER_PAGE): array
{
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;

    return [
        'total'       => $total,
        'per_page'    => $perPage,
        'current'     => $page,
        'total_pages' => $totalPages,
        'offset'      => $offset,
    ];
}

/**
 * Build pagination URL with existing query params
 */
function pagination_url(int $page): string
{
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}

/**
 * Get old form input (for repopulating forms after validation errors)
 */
function old(string $key, string $default = ''): string
{
    return e($_SESSION['old_input'][$key] ?? $default);
}

/**
 * Store old input in session
 */
function store_old_input(array $data): void
{
    $_SESSION['old_input'] = $data;
}

/**
 * Clear old input
 */
function clear_old_input(): void
{
    unset($_SESSION['old_input']);
}

/**
 * Get validation errors
 */
function get_errors(): array
{
    $errors = $_SESSION['errors'] ?? [];
    unset($_SESSION['errors']);
    return $errors;
}

/**
 * Store validation errors
 */
function store_errors(array $errors): void
{
    $_SESSION['errors'] = $errors;
}

/**
 * Check if field has error
 */
function has_error(string $field, array $errors): bool
{
    return isset($errors[$field]);
}

/**
 * Get error message for field
 */
function error_message(string $field, array $errors): string
{
    return $errors[$field] ?? '';
}
