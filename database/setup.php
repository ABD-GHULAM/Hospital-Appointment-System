<?php
/**
 * One-time setup script
 * Run after importing schema.sql to set demo account passwords.
 *
 * Usage: php database/setup.php
 * Or visit: http://localhost:8000/database/setup.php (remove after use)
 */

require_once dirname(__DIR__) . '/bootstrap.php';
require_once APP_ROOT . '/includes/models.php';

$passwords = [
    'admin@clinic.com'              => 'admin123',
    'sarah.mitchell@clinic.com'     => 'doctor123',
    'james.wilson@clinic.com'       => 'doctor123',
    'emily.chen@clinic.com'         => 'doctor123',
    'michael.brown@clinic.com'      => 'doctor123',
    'john.anderson@email.com'       => 'patient123',
    'maria.garcia@email.com'        => 'patient123',
    'david.lee@email.com'           => 'patient123',
    'lisa.thompson@email.com'       => 'patient123',
    'robert.kim@email.com'          => 'patient123',
];

$db = Database::getConnection();
$stmt = $db->prepare('UPDATE users SET password = ? WHERE email = ?');

echo "<pre>Setting up demo passwords...\n\n";

foreach ($passwords as $email => $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt->execute([$hash, $email]);
    echo "Updated: {$email} => {$password}\n";
}

echo "\nSetup complete! Delete this file in production.\n";
echo "Login at: " . base_url('auth/login.php') . "\n</pre>";
