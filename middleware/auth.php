<?php
/**
 * Authentication middleware - require login
 */

function require_auth(): void
{
    if (!is_logged_in()) {
        flash('error', 'Please login to continue.');
        redirect(base_url('auth/login.php'));
    }
}
