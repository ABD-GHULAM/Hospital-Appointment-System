<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $status = sanitize($_POST['status'] ?? '');
    $notes = sanitize($_POST['notes'] ?? '');

    if (in_array($status, ['approved', 'rejected', 'completed', 'cancelled'])) {
        $appointmentModel = new AppointmentModel();
        $appointmentModel->updateStatus($id, $status, $notes ?: null);
        flash('success', 'Appointment ' . $status . ' successfully.');
    }
}
redirect(base_url('admin/appointments/index.php'));
