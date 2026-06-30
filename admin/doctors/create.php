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
    $data['experience'] = (int)($_POST['experience'] ?? 0);
    $data['consultation_fee'] = (float)($_POST['consultation_fee'] ?? 0);
    $data['available_days'] = implode(',', $_POST['available_days'] ?? []);

    $validator = new Validator(array_merge($data, ['password' => $_POST['password'] ?? '']));
    $validator->required('full_name')->required('email')->email('email')->unique('email', 'users')
              ->required('password')->min('password', 6)
              ->required('specialization')->required('phone')->required('qualification')
              ->required('available_days')->min_value('consultation_fee', 0);

    if ($validator->fails()) {
        store_errors($validator->errors());
        store_old_input($data);
        flash('error', 'Please fix the errors.');
        redirect(base_url('admin/doctors/create.php'));
    }

    try {
        $profileImage = !empty($_FILES['profile_image']['name']) ? upload_profile_image($_FILES['profile_image']) : null;
        $doctorModel = new DoctorModel();
        $doctorModel->create(
            ['full_name' => $data['full_name'], 'email' => $data['email'], 'password' => $_POST['password'], 'profile_image' => $profileImage],
            ['specialization' => $data['specialization'], 'phone' => $data['phone'], 'experience' => $data['experience'],
             'qualification' => $data['qualification'], 'available_days' => $data['available_days'], 'consultation_fee' => $data['consultation_fee']]
        );
        flash('success', 'Doctor added successfully.');
        redirect(base_url('admin/doctors/index.php'));
    } catch (Exception $e) {
        flash('error', $e->getMessage());
        redirect(base_url('admin/doctors/create.php'));
    }
}

$errors = get_errors();
$pageTitle = 'Add Doctor';
$days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];

ob_start();
include __DIR__ . '/_form.php';
$content = ob_get_clean();
include APP_ROOT . '/layouts/dashboard.php';
