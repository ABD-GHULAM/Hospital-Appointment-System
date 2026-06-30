<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_admin();

$id = (int)($_GET['id'] ?? 0);
$patientModel = new PatientModel();
$patient = $patientModel->findById($id);

if (!$patient) { flash('error', 'Patient not found.'); redirect(base_url('admin/patients/index.php')); }

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

    $validator = new Validator($data);
    $validator->required('full_name')->required('email')->email('email')->unique('email', 'users', $patient['user_id'])
              ->required('gender')->required('age')->required('phone')->required('address');

    if ($validator->fails()) { flash('error', 'Please fix errors.'); redirect(base_url('admin/patients/edit.php?id=' . $id)); }

    $userData = ['full_name' => $data['full_name'], 'email' => $data['email'], 'role' => 'patient'];
    if (!empty($_POST['password'])) $userData['password'] = $_POST['password'];
    if (!empty($_FILES['profile_image']['name'])) {
        delete_profile_image($patient['profile_image']);
        $userData['profile_image'] = upload_profile_image($_FILES['profile_image']);
    }

    $patientModel->update($id, $userData, [
        'gender' => $data['gender'], 'age' => $data['age'], 'blood_group' => $data['blood_group'] ?? null,
        'phone' => $data['phone'], 'address' => $data['address'],
    ]);
    flash('success', 'Patient updated.');
    redirect(base_url('admin/patients/index.php'));
}

$pageTitle = 'Edit Patient';
ob_start(); $isEdit = true; include __DIR__ . '/_form.php';
$content = ob_get_clean(); include APP_ROOT . '/layouts/dashboard.php';
