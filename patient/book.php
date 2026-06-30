<?php
require_once dirname(__DIR__) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_patient();

$patientModel = new PatientModel();
$doctorModel = new DoctorModel();
$appointmentModel = new AppointmentModel();

$patient = $patientModel->findByUserId(current_user()['id']);
$doctors = $doctorModel->getAll('', '', 100, 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $data = [
        'patient_id' => $patient['id'],
        'doctor_id'  => (int)($_POST['doctor_id'] ?? 0),
        'appointment_date' => sanitize($_POST['appointment_date'] ?? ''),
        'appointment_time' => sanitize($_POST['appointment_time'] ?? ''),
        'reason' => sanitize($_POST['reason'] ?? ''),
        'status' => 'pending',
    ];

    $validator = new Validator($data);
    $validator->required('doctor_id')->required('appointment_date')->required('appointment_time')
              ->required('reason')->date('appointment_date')->future_date('appointment_date');

    if ($validator->fails()) {
        store_errors($validator->errors());
        flash('error', 'Please fix the errors.');
        redirect(base_url('patient/book.php'));
    }

    if (!$appointmentModel->isSlotAvailable($data['doctor_id'], $data['appointment_date'], $data['appointment_time'])) {
        flash('error', 'This time slot is already booked. Please choose another.');
        redirect(base_url('patient/book.php'));
    }

    $appointmentModel->create($data);
    flash('success', 'Appointment booked! Waiting for approval.');
    redirect(base_url('patient/appointments.php'));
}

$errors = get_errors();
$preselectedDoctor = (int)($_GET['doctor_id'] ?? 0);
$pageTitle = 'Book Appointment';
ob_start();
render_page_header('Book Appointment', 'Schedule a visit with our doctors');
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 glass-card rounded-2xl p-8">
        <form method="POST" onsubmit="return validateForm(this)" class="space-y-5">
            <?= csrf_field() ?>
            <div>
                <label class="block text-sm font-medium mb-1.5">Select Doctor *</label>
                <select name="doctor_id" required id="doctor-select" onchange="updateDoctorInfo()"
                        class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                    <option value="">Choose a doctor</option>
                    <?php foreach ($doctors as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= $preselectedDoctor === (int)$d['id'] ? 'selected' : '' ?> data-fee="<?= $d['consultation_fee'] ?>" data-spec="<?= e($d['specialization']) ?>" data-days="<?= e($d['available_days']) ?>">
                        <?= e($d['full_name']) ?> - <?= e($d['specialization']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="doctor-info" class="hidden p-4 bg-primary-50 dark:bg-primary-900/20 rounded-xl">
                <p class="text-sm"><span class="font-medium">Specialization:</span> <span id="info-spec"></span></p>
                <p class="text-sm mt-1"><span class="font-medium">Fee:</span> <span id="info-fee"></span></p>
                <p class="text-sm mt-1"><span class="font-medium">Available:</span> <span id="info-days"></span></p>
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div><label class="block text-sm font-medium mb-1.5">Date *</label>
                    <input type="date" name="appointment_date" required min="<?= date('Y-m-d') ?>"
                           class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
                <div><label class="block text-sm font-medium mb-1.5">Time *</label>
                    <input type="time" name="appointment_time" required min="08:00" max="17:00"
                           class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
            </div>
            <div><label class="block text-sm font-medium mb-1.5">Reason for Visit *</label>
                <textarea name="reason" required rows="4" placeholder="Describe your symptoms or reason for visit..."
                          class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></textarea></div>
            <button type="submit" class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl shadow-lg shadow-primary-500/25 transition-colors">
                Book Appointment
            </button>
        </form>
    </div>
    <div class="space-y-4">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="font-semibold mb-3">Booking Tips</h3>
            <ul class="text-sm text-gray-500 space-y-2">
                <li class="flex gap-2"><i data-lucide="check" class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5"></i> Appointments need admin approval</li>
                <li class="flex gap-2"><i data-lucide="check" class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5"></i> Clinic hours: 8 AM - 5 PM</li>
                <li class="flex gap-2"><i data-lucide="check" class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5"></i> Cancel up to 24h before visit</li>
            </ul>
        </div>
    </div>
</div>

<script>
function updateDoctorInfo() {
    const sel = document.getElementById('doctor-select');
    const opt = sel.options[sel.selectedIndex];
    const info = document.getElementById('doctor-info');
    if (!opt.value) { info.classList.add('hidden'); return; }
    info.classList.remove('hidden');
    document.getElementById('info-spec').textContent = opt.dataset.spec;
    document.getElementById('info-fee').textContent = 'Rp ' + Number(opt.dataset.fee).toLocaleString('id-ID');
    document.getElementById('info-days').textContent = opt.dataset.days;
}
document.addEventListener('DOMContentLoaded', updateDoctorInfo);
</script>

<?php $content = ob_get_clean(); include APP_ROOT . '/layouts/dashboard.php'; ?>
