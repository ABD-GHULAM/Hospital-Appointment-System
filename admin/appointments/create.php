<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_admin();

$appointmentModel = new AppointmentModel();
$doctorModel = new DoctorModel();
$patientModel = new PatientModel();
$doctors = $doctorModel->getAll('', '', 100, 0);
$patients = $patientModel->getAll('', 100, 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $data = [
        'patient_id' => (int)($_POST['patient_id'] ?? 0),
        'doctor_id'  => (int)($_POST['doctor_id'] ?? 0),
        'appointment_date' => sanitize($_POST['appointment_date'] ?? ''),
        'appointment_time' => sanitize($_POST['appointment_time'] ?? ''),
        'reason' => sanitize($_POST['reason'] ?? ''),
        'status' => sanitize($_POST['status'] ?? 'pending'),
    ];

    $validator = new Validator($data);
    $validator->required('patient_id')->required('doctor_id')->required('appointment_date')
              ->required('appointment_time')->required('reason')->date('appointment_date');

    if ($validator->fails()) {
        flash('error', 'Please fix the errors.');
        redirect(base_url('admin/appointments/create.php'));
    }

    if (!$appointmentModel->isSlotAvailable($data['doctor_id'], $data['appointment_date'], $data['appointment_time'])) {
        flash('error', 'This time slot is already booked.');
        redirect(base_url('admin/appointments/create.php'));
    }

    $appointmentModel->create($data);
    flash('success', 'Appointment created successfully.');
    redirect(base_url('admin/appointments/index.php'));
}

$pageTitle = 'New Appointment';
ob_start();
$isEdit = false;
include __DIR__ . '/_form.php';
$content = ob_get_clean();
include APP_ROOT . '/layouts/dashboard.php';
