<?php
/**
 * Application configuration
 */

define('APP_NAME', 'MediCare Clinic');
define('APP_URL', 'http://localhost/clinic-management');
define('APP_ROOT', dirname(__DIR__));
define('UPLOAD_PATH', APP_ROOT . '/uploads/profiles/');
define('UPLOAD_URL', APP_URL . '/uploads/profiles/');
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ITEMS_PER_PAGE', 10);

// Session settings
define('SESSION_LIFETIME', 3600 * 8); // 8 hours

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
