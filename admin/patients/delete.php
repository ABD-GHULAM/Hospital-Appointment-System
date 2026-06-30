<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';

require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $id = (int)($_GET['id'] ?? 0);
    $patientModel = new PatientModel();
    $patient = $patientModel->findById($id);
    if ($patient) { delete_profile_image($patient['profile_image']); $patientModel->delete($id); flash('success', 'Patient deleted.'); }
}
redirect(base_url('admin/patients/index.php'));
