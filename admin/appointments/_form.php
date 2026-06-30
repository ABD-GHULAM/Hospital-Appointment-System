<?php
$isEdit = isset($appointment);
$formAction = $isEdit ? base_url('admin/appointments/edit.php?id=' . $appointment['id']) : base_url('admin/appointments/create.php');
?>
<?php render_page_header($isEdit ? 'Edit Appointment' : 'New Appointment'); ?>

<div class="glass-card rounded-2xl p-8 max-w-2xl">
    <form method="POST" action="<?= $formAction ?>" onsubmit="return validateForm(this)" class="space-y-5">
        <?= csrf_field() ?>
        <div>
            <label class="block text-sm font-medium mb-1.5">Patient *</label>
            <select name="patient_id" required class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                <option value="">Select patient</option>
                <?php foreach ($patients as $p): ?>
                <option value="<?= $p['id'] ?>" <?= ($isEdit && $appointment['patient_id'] == $p['id']) ? 'selected' : '' ?>><?= e($p['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1.5">Doctor *</label>
            <select name="doctor_id" required class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                <option value="">Select doctor</option>
                <?php foreach ($doctors as $d): ?>
                <option value="<?= $d['id'] ?>" <?= ($isEdit && $appointment['doctor_id'] == $d['id']) ? 'selected' : '' ?>><?= e($d['full_name']) ?> - <?= e($d['specialization']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="grid grid-cols-2 gap-5">
            <div><label class="block text-sm font-medium mb-1.5">Date *</label>
                <input type="date" name="appointment_date" required min="<?= date('Y-m-d') ?>" value="<?= $isEdit ? e($appointment['appointment_date']) : '' ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
            <div><label class="block text-sm font-medium mb-1.5">Time *</label>
                <input type="time" name="appointment_time" required value="<?= $isEdit ? e(substr($appointment['appointment_time'], 0, 5)) : '' ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
        </div>
        <div><label class="block text-sm font-medium mb-1.5">Reason *</label>
            <textarea name="reason" required rows="3" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"><?= $isEdit ? e($appointment['reason']) : '' ?></textarea></div>
        <?php if ($isEdit): ?>
        <div><label class="block text-sm font-medium mb-1.5">Status</label>
            <select name="status" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                <?php foreach (['pending','approved','rejected','completed','cancelled'] as $s): ?>
                <option value="<?= $s ?>" <?= $appointment['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select></div>
        <div><label class="block text-sm font-medium mb-1.5">Notes</label>
            <textarea name="notes" rows="2" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"><?= e($appointment['notes'] ?? '') ?></textarea></div>
        <?php endif; ?>
        <div class="flex gap-3 pt-4">
            <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl shadow-lg shadow-primary-500/25"><?= $isEdit ? 'Update' : 'Create Appointment' ?></button>
            <a href="<?= base_url('admin/appointments/index.php') ?>" class="px-6 py-2.5 text-sm text-gray-600 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-xl">Cancel</a>
        </div>
    </form>
</div>
