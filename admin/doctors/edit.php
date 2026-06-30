<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_admin();

$id = (int)($_GET['id'] ?? 0);
$doctorModel = new DoctorModel();
$doctor = $doctorModel->findById($id);

if (!$doctor) {
    flash('error', 'Doctor not found.');
    redirect(base_url('admin/doctors/index.php'));
}

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
    $data['experience'] = (int)($_POST['experience'] ?? 0);
    $data['consultation_fee'] = (float)($_POST['consultation_fee'] ?? 0);
    $data['available_days'] = implode(',', $_POST['available_days'] ?? []);

    $validator = new Validator(array_merge($data, ['password' => $_POST['password'] ?? '']));
    $validator->required('full_name')->required('email')->email('email')
              ->unique('email', 'users', $doctor['user_id'])
              ->required('specialization')->required('phone')->required('qualification');

    if ($validator->fails()) {
        store_errors($validator->errors());
        flash('error', 'Please fix the errors.');
        redirect(base_url('admin/doctors/edit.php?id=' . $id));
    }

    try {
        $userData = ['full_name' => $data['full_name'], 'email' => $data['email'], 'role' => 'doctor'];
        if (!empty($_POST['password'])) $userData['password'] = $_POST['password'];
        if (!empty($_FILES['profile_image']['name'])) {
            delete_profile_image($doctor['profile_image']);
            $userData['profile_image'] = upload_profile_image($_FILES['profile_image']);
        }

        $doctorModel->update($id, $userData, [
            'specialization' => $data['specialization'], 'phone' => $data['phone'],
            'experience' => $data['experience'], 'qualification' => $data['qualification'],
            'available_days' => $data['available_days'], 'consultation_fee' => $data['consultation_fee'],
        ]);

        flash('success', 'Doctor updated successfully.');
        redirect(base_url('admin/doctors/index.php'));
    } catch (Exception $e) {
        flash('error', $e->getMessage());
        redirect(base_url('admin/doctors/edit.php?id=' . $id));
    }
}

$pageTitle = 'Edit Doctor';
$days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];

ob_start();
include __DIR__ . '/_form.php';
$content = ob_get_clean();
include APP_ROOT . '/layouts/dashboard.php';
