<?php
/**
 * Dashboard statistics model
 */

class DashboardModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAdminStats(): array
    {
        $appointmentModel = new AppointmentModel();
        $doctorModel = new DoctorModel();
        $patientModel = new PatientModel();

        return [
            'total_doctors'    => $doctorModel->countAll(),
            'total_patients'   => $patientModel->countAll(),
            'today_appointments' => $appointmentModel->countToday(),
            'pending'          => $appointmentModel->countByStatus('pending'),
            'approved'         => $appointmentModel->countByStatus('approved'),
            'completed'        => $appointmentModel->countByStatus('completed'),
            'cancelled'        => $appointmentModel->countByStatus('cancelled'),
            'rejected'         => $appointmentModel->countByStatus('rejected'),
            'total_appointments' => $appointmentModel->count(),
        ];
    }

    public function getDoctorStats(int $doctorId): array
    {
        $appointmentModel = new AppointmentModel();

        return [
            'today'     => $appointmentModel->count(['doctor_id' => $doctorId, 'today' => true]),
            'upcoming'  => $appointmentModel->count(['doctor_id' => $doctorId, 'upcoming' => true]),
            'completed' => $appointmentModel->count(['doctor_id' => $doctorId, 'status' => 'completed']),
            'pending'   => $appointmentModel->count(['doctor_id' => $doctorId, 'status' => 'pending']),
            'total'     => $appointmentModel->count(['doctor_id' => $doctorId]),
        ];
    }

    public function getPatientStats(int $patientId): array
    {
        $appointmentModel = new AppointmentModel();

        return [
            'upcoming'  => $appointmentModel->count(['patient_id' => $patientId, 'upcoming' => true]),
            'completed' => $appointmentModel->count(['patient_id' => $patientId, 'status' => 'completed']),
            'pending'   => $appointmentModel->count(['patient_id' => $patientId, 'status' => 'pending']),
            'cancelled' => $appointmentModel->count(['patient_id' => $patientId, 'status' => 'cancelled']),
            'total'     => $appointmentModel->count(['patient_id' => $patientId]),
        ];
    }
}
