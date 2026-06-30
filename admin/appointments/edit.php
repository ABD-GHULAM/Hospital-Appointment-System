<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_admin();

$id = (int)($_GET['id'] ?? 0);
$appointmentModel = new AppointmentModel();
$doctorModel = new DoctorModel();
$patientModel = new PatientModel();
$appointment = $appointmentModel->findById($id);
$doctors = $doctorModel->getAll('', '', 100, 0);
$patients = $patientModel->getAll('', 100, 0);

if (!$appointment) { flash('error', 'Not found.'); redirect(base_url('admin/appointments/index.php')); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $data = [
        'patient_id' => (int)$_POST['patient_id'],
        'doctor_id'  => (int)$_POST['doctor_id'],
        'appointment_date' => sanitize($_POST['appointment_date']),
        'appointment_time' => sanitize($_POST['appointment_time']),
        'reason' => sanitize($_POST['reason']),
        'status' => sanitize($_POST['status']),
        'notes'  => sanitize($_POST['notes'] ?? ''),
    ];

    if (!$appointmentModel->isSlotAvailable($data['doctor_id'], $data['appointment_date'], $data['appointment_time'], $id)) {
        flash('error', 'Time slot already booked.');
        redirect(base_url('admin/appointments/edit.php?id=' . $id));
    }

    $appointmentModel->update($id, $data);
    flash('success', 'Appointment updated.');
    redirect(base_url('admin/appointments/index.php'));
}

$pageTitle = 'Edit Appointment';
ob_start(); $isEdit = true; include __DIR__ . '/_form.php';
$content = ob_get_clean(); include APP_ROOT . '/layouts/dashboard.php';
