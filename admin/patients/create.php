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
    $data['age'] = (int)($_POST['age'] ?? 0);

    $validator = new Validator(array_merge($data, ['password' => $_POST['password'] ?? '']));
    $validator->required('full_name')->required('email')->email('email')->unique('email', 'users')
              ->required('password')->min('password', 6)
              ->required('gender')->in('gender', ['male','female','other'])
              ->required('age')->min_value('age', 1)->required('phone')->required('address');

    if ($validator->fails()) {
        store_errors($validator->errors());
        flash('error', 'Please fix the errors.');
        redirect(base_url('admin/patients/create.php'));
    }

    try {
        $profileImage = !empty($_FILES['profile_image']['name']) ? upload_profile_image($_FILES['profile_image']) : null;
        $patientModel = new PatientModel();
        $patientModel->create(
            ['full_name' => $data['full_name'], 'email' => $data['email'], 'password' => $_POST['password'], 'profile_image' => $profileImage],
            ['gender' => $data['gender'], 'age' => $data['age'], 'blood_group' => $data['blood_group'] ?? null, 'phone' => $data['phone'], 'address' => $data['address']]
        );
        flash('success', 'Patient added successfully.');
        redirect(base_url('admin/patients/index.php'));
    } catch (Exception $e) {
        flash('error', $e->getMessage());
        redirect(base_url('admin/patients/create.php'));
    }
}

$pageTitle = 'Add Patient';
ob_start();
$isEdit = false;
include __DIR__ . '/_form.php';
$content = ob_get_clean();
include APP_ROOT . '/layouts/dashboard.php';
