<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    // Sanitize all scalar values
    $data = [];
    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            $data[$key] = array_map('sanitize', $value);
        } else {
            $data[$key] = sanitize($value);
        }
    }
    $validator = new Validator(array_merge($data, ['password' => $_POST['password'] ?? '']));
    $validator->required('full_name')->required('email')->email('email')->unique('email', 'users')
              ->required('password')->min('password', 6)->required('role')->in('role', ['admin','doctor','patient']);

    if ($validator->fails()) { flash('error', 'Fix errors.'); redirect(base_url('admin/users/create.php')); }

    $profileImage = !empty($_FILES['profile_image']['name']) ? upload_profile_image($_FILES['profile_image']) : null;
    
    $userData = ['full_name' => $data['full_name'], 'email' => $data['email'], 'password' => $_POST['password'], 'role' => $data['role'], 'profile_image' => $profileImage];
    
    if ($data['role'] === 'patient') {
        $patientModel = new PatientModel();
        $patientModel->create(
            $userData,
            ['gender' => 'other', 'age' => 30, 'blood_group' => null, 'phone' => '0000000000', 'address' => '']
        );
    } elseif ($data['role'] === 'doctor') {
        $doctorModel = new DoctorModel();
        $doctorModel->create(
            $userData,
            ['specialization' => 'General Physician', 'phone' => '0000000000', 'experience' => 0, 'qualification' => 'MBBS', 'available_days' => 'Mon-Fri', 'consultation_fee' => 500]
        );
    } else {
        (new UserModel())->create($userData);
    }
    
    flash('success', 'User created.');
    redirect(base_url('admin/users/index.php'));
}

$pageTitle = 'Add User';
ob_start();
$user = null; $isEdit = false;
include __DIR__ . '/_form.php';
$content = ob_get_clean();
include APP_ROOT . '/layouts/dashboard.php';
