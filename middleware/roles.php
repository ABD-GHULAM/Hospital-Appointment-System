<?php
/**
 * Role-based authorization middleware
 */
require_once __DIR__ . '/auth.php';

function require_role(string ...$roles): void
{
    require_auth();
    if (!in_array($_SESSION['user_role'], $roles)) {
        flash('error', 'You do not have permission to access this page.');
        redirect(dashboard_url());
    }
}

function require_admin(): void
{
    require_role('admin');
}

function require_doctor(): void
{
    require_role('doctor');
}

function require_patient(): void
{
    require_role('patient');
}

function guest_only(): void
{
    if (is_logged_in()) {
        redirect(dashboard_url());
    }
}
