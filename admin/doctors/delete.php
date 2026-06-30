<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $id = (int)($_GET['id'] ?? 0);
    $doctorModel = new DoctorModel();
    $doctor = $doctorModel->findById($id);

    if ($doctor) {
        delete_profile_image($doctor['profile_image']);
        $doctorModel->delete($id);
        flash('success', 'Doctor deleted successfully.');
    } else {
        flash('error', 'Doctor not found.');
    }
}

redirect(base_url('admin/doctors/index.php'));
