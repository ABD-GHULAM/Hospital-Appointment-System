<?php
$isEdit = isset($user);
$formAction = $isEdit ? base_url('admin/users/edit.php?id=' . $user['id']) : base_url('admin/users/create.php');
?>
<?php render_page_header($isEdit ? 'Edit User' : 'Add User'); ?>
<div class="glass-card rounded-2xl p-8 max-w-xl">
    <form method="POST" action="<?= $formAction ?>" enctype="multipart/form-data" onsubmit="return validateForm(this)" class="space-y-5">
        <?= csrf_field() ?>
        <div><label class="block text-sm font-medium mb-1.5">Full Name *</label>
            <input type="text" name="full_name" required value="<?= $isEdit ? e($user['full_name']) : '' ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
        <div><label class="block text-sm font-medium mb-1.5">Email *</label>
            <input type="email" name="email" required value="<?= $isEdit ? e($user['email']) : '' ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
        <div><label class="block text-sm font-medium mb-1.5">Password <?= $isEdit ? '(leave blank)' : '*' ?></label>
            <input type="password" name="password" <?= $isEdit ? '' : 'required minlength="6"' ?> class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
        <div><label class="block text-sm font-medium mb-1.5">Role *</label>
            <select name="role" required class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                <?php foreach (['admin','doctor','patient'] as $r): ?>
                <option value="<?= $r ?>" <?= ($isEdit && $user['role'] === $r) ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                <?php endforeach; ?>
            </select></div>
        <div><label class="block text-sm font-medium mb-1.5">Profile Image</label>
            <input type="file" name="profile_image" accept="image/*" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-primary-50 file:text-primary-700"></div>
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-primary-500/25"><?= $isEdit ? 'Update' : 'Create' ?></button>
            <a href="<?= base_url('admin/users/index.php') ?>" class="px-6 py-2.5 text-sm text-gray-600 hover:bg-gray-100 rounded-xl">Cancel</a>
        </div>
    </form>
</div>
