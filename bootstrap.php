<?php
/**
 * Application bootstrap - include this at the top of every page
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/helpers/validation.php';
require_once __DIR__ . '/helpers/csrf.php';

init_session();

// Compute application base URL path (works from any subdirectory)
$_baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
foreach (['/admin', '/doctor', '/patient', '/auth', '/api'] as $_suffix) {
    if (str_ends_with($_baseDir, $_suffix)) {
        $_baseDir = substr($_baseDir, 0, -strlen($_suffix));
        break;
    }
}
define('BASE_PATH', $_baseDir === '/' ? '' : $_baseDir);
