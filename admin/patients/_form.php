<?php
$isEdit = isset($patient);
$formAction = $isEdit ? base_url('admin/patients/edit.php?id=' . $patient['id']) : base_url('admin/patients/create.php');
?>
<?php render_page_header($isEdit ? 'Edit Patient' : 'Add Patient'); ?>

<div class="glass-card rounded-2xl p-8 max-w-3xl">
    <form method="POST" action="<?= $formAction ?>" enctype="multipart/form-data" onsubmit="return validateForm(this)" class="space-y-5">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div><label class="block text-sm font-medium mb-1.5">Full Name *</label>
                <input type="text" name="full_name" required value="<?= $isEdit ? e($patient['full_name']) : old('full_name') ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
            <div><label class="block text-sm font-medium mb-1.5">Email *</label>
                <input type="email" name="email" required value="<?= $isEdit ? e($patient['email']) : old('email') ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
            <div><label class="block text-sm font-medium mb-1.5">Password <?= $isEdit ? '(leave blank)' : '*' ?></label>
                <input type="password" name="password" <?= $isEdit ? '' : 'required minlength="6"' ?> class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
            <div><label class="block text-sm font-medium mb-1.5">Gender *</label>
                <select name="gender" required class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                    <option value="">Select</option>
                    <?php foreach (['male','female','other'] as $g): ?>
                    <option value="<?= $g ?>" <?= ($isEdit ? $patient['gender'] : old('gender')) === $g ? 'selected' : '' ?>><?= ucfirst($g) ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div><label class="block text-sm font-medium mb-1.5">Age *</label>
                <input type="number" name="age" required min="1" value="<?= $isEdit ? e($patient['age']) : old('age') ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
            <div><label class="block text-sm font-medium mb-1.5">Blood Group</label>
                <select name="blood_group" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                    <option value="">Select</option>
                    <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                    <option value="<?= $bg ?>" <?= ($isEdit ? $patient['blood_group'] : old('blood_group')) === $bg ? 'selected' : '' ?>><?= $bg ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div><label class="block text-sm font-medium mb-1.5">Phone *</label>
                <input type="tel" name="phone" required value="<?= $isEdit ? e($patient['phone']) : old('phone') ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
            <div><label class="block text-sm font-medium mb-1.5">Profile Image</label>
                <input type="file" name="profile_image" accept="image/*" onchange="previewImage(this,'pat-preview')" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-primary-50 file:text-primary-700">
                <img id="pat-preview" src="<?= $isEdit ? profile_image($patient['profile_image'], $patient['full_name']) : '' ?>" class="upload-preview mt-3 <?= $isEdit ? '' : 'hidden' ?>" alt=""></div>
        </div>
        <div><label class="block text-sm font-medium mb-1.5">Address *</label>
            <textarea name="address" required rows="2" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"><?= $isEdit ? e($patient['address']) : old('address') ?></textarea></div>
        <div class="flex gap-3 pt-4">
            <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl shadow-lg shadow-primary-500/25"><?= $isEdit ? 'Update' : 'Add Patient' ?></button>
            <a href="<?= base_url('admin/patients/index.php') ?>" class="px-6 py-2.5 text-sm text-gray-600 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-xl">Cancel</a>
        </div>
    </form>
</div>
