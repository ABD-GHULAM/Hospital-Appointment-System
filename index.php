<?php
require_once __DIR__ . '/bootstrap.php';

if (is_logged_in()) {
    redirect(dashboard_url());
}
redirect(base_url('auth/login.php'));
