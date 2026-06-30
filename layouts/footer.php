    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-[100] flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>

    <script src="<?= base_url('assets/js/app.js') ?>"></script>

    <?php $flash = get_flash(); if ($flash): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showToast('<?= e($flash['message']) ?>', '<?= e($flash['type']) ?>');
        });
    </script>
    <?php endif; ?>

    <?php
    // Load notifications based on user role
    $notifications = [];
    if (is_logged_in()) {
        $appointmentModel = new AppointmentModel();
        $user = current_user();
        if ($user['role'] === 'doctor') {
            $doctorModel = new DoctorModel();
            $doctor = $doctorModel->findByUserId($user['id']);
            if ($doctor) {
                $appointments = $appointmentModel->getAll(['doctor_id' => $doctor['id'], 'upcoming' => true], 5, 0, 'a.appointment_date ASC, a.appointment_time ASC');
                foreach ($appointments as $apt) {
                    $notifications[] = [
                        'type' => 'appointment',
                        'title' => "Appointment with {$apt['patient_name']}",
                        'message' => format_date($apt['appointment_date']) . ' at ' . format_time($apt['appointment_time']),
                        'status' => $apt['status']
                    ];
                }
                $pendingCount = count(array_filter($appointments, fn($a) => $a['status'] === 'pending'));
            }
        } elseif ($user['role'] === 'patient') {
            $patientModel = new PatientModel();
            $patient = $patientModel->findByUserId($user['id']);
            if ($patient) {
                $appointments = $appointmentModel->getAll(['patient_id' => $patient['id'], 'upcoming' => true], 5, 0, 'a.appointment_date ASC, a.appointment_time ASC');
                foreach ($appointments as $apt) {
                    $notifications[] = [
                        'type' => 'appointment',
                        'title' => "Appointment with Dr. {$apt['doctor_name']}",
                        'message' => format_date($apt['appointment_date']) . ' at ' . format_time($apt['appointment_time']),
                        'status' => $apt['status']
                    ];
                }
                $pendingCount = count(array_filter($appointments, fn($a) => $a['status'] === 'pending'));
            }
        } elseif ($user['role'] === 'admin') {
            $appointments = $appointmentModel->getAll(['status' => 'pending'], 5, 0, 'a.created_at DESC');
            foreach ($appointments as $apt) {
                $notifications[] = [
                    'type' => 'appointment',
                    'title' => "New Appointment Request",
                    'message' => "From {$apt['patient_name']} with Dr. {$apt['doctor_name']}",
                    'status' => $apt['status']
                ];
            }
            $pendingCount = count($appointments);
        }
    }
    ?>
    <script>
        window.notifications = <?= json_encode($notifications ?? []) ?>;
        window.pendingNotificationCount = <?= json_encode($pendingCount ?? 0) ?>;
    </script>
</body>
</html>
