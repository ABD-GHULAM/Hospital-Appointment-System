<?php
$isEdit = isset($doctor);
$formAction = $isEdit ? base_url('admin/doctors/edit.php?id=' . $doctor['id']) : base_url('admin/doctors/create.php');
$selectedDays = $isEdit ? explode(',', $doctor['available_days']) : (isset($_SESSION['old_input']['available_days']) ? explode(',', $_SESSION['old_input']['available_days']) : []);
?>
<?php render_page_header($isEdit ? 'Edit Doctor' : 'Add Doctor', $isEdit ? 'Update doctor information' : 'Add a new doctor to the clinic'); ?>

<div class="glass-card rounded-2xl p-8 max-w-3xl">
    <form method="POST" action="<?= $formAction ?>" enctype="multipart/form-data" onsubmit="return validateForm(this)" class="space-y-5">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">Full Name *</label>
                <input type="text" name="full_name" required value="<?= $isEdit ? e($doctor['full_name']) : old('full_name') ?>"
                       class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Email *</label>
                <input type="email" name="email" required value="<?= $isEdit ? e($doctor['email']) : old('email') ?>"
                       class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Password <?= $isEdit ? '(leave blank to keep)' : '*' ?></label>
                <input type="password" name="password" <?= $isEdit ? '' : 'required minlength="6"' ?>
                       class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Specialization *</label>
                <input type="text" name="specialization" required value="<?= $isEdit ? e($doctor['specialization']) : old('specialization') ?>"
                       class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Phone *</label>
                <input type="tel" name="phone" required value="<?= $isEdit ? e($doctor['phone']) : old('phone') ?>"
                       class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Experience (years) *</label>
                <input type="number" name="experience" required min="0" value="<?= $isEdit ? e($doctor['experience']) : old('experience') ?>"
                       class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Consultation Fee (IDR) *</label>
                <input type="number" name="consultation_fee" required min="0" step="1000" value="<?= $isEdit ? e($doctor['consultation_fee']) : old('consultation_fee') ?>"
                       class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Profile Image</label>
                <input type="file" name="profile_image" accept="image/*" onchange="previewImage(this, 'doc-preview')"
                       class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-primary-50 file:text-primary-700">
                <img id="doc-preview" src="<?= $isEdit ? profile_image($doctor['profile_image'], $doctor['full_name']) : '' ?>" class="upload-preview mt-3 <?= $isEdit ? '' : 'hidden' ?>" alt="">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1.5">Qualification *</label>
            <input type="text" name="qualification" required value="<?= $isEdit ? e($doctor['qualification']) : old('qualification') ?>"
                   class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">Available Days *</label>
            <div class="flex flex-wrap gap-3">
                <?php foreach ($days as $day): ?>
                <label class="flex items-center gap-2 px-4 py-2 bg-gray-50 dark:bg-slate-700/50 rounded-xl cursor-pointer hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors">
                    <input type="checkbox" name="available_days[]" value="<?= $day ?>" <?= in_array($day, $selectedDays) ? 'checked' : '' ?> class="rounded text-primary-600 focus:ring-primary-500">
                    <span class="text-sm"><?= $day ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="flex gap-3 pt-4">
            <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl shadow-lg shadow-primary-500/25 transition-colors">
                <?= $isEdit ? 'Update Doctor' : 'Add Doctor' ?>
            </button>
            <a href="<?= base_url('admin/doctors/index.php') ?>" class="px-6 py-2.5 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-xl transition-colors">Cancel</a>
        </div>
    </form>
</div>
