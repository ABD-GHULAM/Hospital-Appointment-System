<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';

require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $id = (int)($_GET['id'] ?? 0);
    (new AppointmentModel())->delete($id);
    flash('success', 'Appointment deleted.');
}
redirect(base_url('admin/appointments/index.php'));
