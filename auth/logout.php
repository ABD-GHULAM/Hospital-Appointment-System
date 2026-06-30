<?php
require_once dirname(__DIR__) . '/bootstrap.php';

logout_user();
flash('success', 'You have been logged out successfully.');
redirect(base_url('auth/login.php'));
