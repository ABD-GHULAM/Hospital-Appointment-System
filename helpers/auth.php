<?php
/**
 * Authentication helper functions
 */

/**
 * Start secure session
 */
function init_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

/**
 * Check if user is logged in
 */
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Get current logged-in user data from session
 */
function current_user(): ?array
{
    if (!is_logged_in()) return null;
    return [
        'id'            => $_SESSION['user_id'],
        'full_name'     => $_SESSION['user_name'],
        'email'         => $_SESSION['user_email'],
        'role'          => $_SESSION['user_role'],
        'profile_image' => $_SESSION['user_image'] ?? null,
    ];
}

/**
 * Login user - set session data
 */
function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role']    = $user['role'];
    $_SESSION['user_image']   = $user['profile_image'];
}

/**
 * Logout user - destroy session
 */
function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

/**
 * Check if current user has a specific role
 */
function has_role(string $role): bool
{
    return is_logged_in() && $_SESSION['user_role'] === $role;
}

/**
 * Get dashboard URL based on role
 */
function dashboard_url(): string
{
    $role = $_SESSION['user_role'] ?? '';
    return match ($role) {
        'admin'   => base_url('admin/dashboard.php'),
        'doctor'  => base_url('doctor/dashboard.php'),
        'patient' => base_url('patient/dashboard.php'),
        default   => base_url('auth/login.php'),
    };
}
