<?php
require_once dirname(__DIR__) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_patient();

$patientModel = new PatientModel();
$patient = $patientModel->findByUserId(current_user()['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $data = array_map('sanitize', $_POST);
    $userData = ['full_name' => $data['full_name'], 'email' => $data['email'], 'role' => 'patient'];
    if (!empty($_POST['password'])) $userData['password'] = $_POST['password'];
    if (!empty($_FILES['profile_image']['name'])) {
        delete_profile_image($patient['profile_image']);
        $userData['profile_image'] = upload_profile_image($_FILES['profile_image']);
    }
    $patientModel->update($patient['id'], $userData, [
        'gender' => $data['gender'], 'age' => (int)$data['age'],
        'blood_group' => $data['blood_group'] ?? null, 'phone' => $data['phone'], 'address' => $data['address'],
    ]);
    $_SESSION['user_name'] = $data['full_name'];
    $_SESSION['user_email'] = $data['email'];
    flash('success', 'Profile updated successfully.');
    redirect(base_url('patient/profile.php'));
}

$pageTitle = 'My Profile';
ob_start();
render_page_header('My Profile', 'Manage your personal information');
?>

<div class="glass-card rounded-2xl p-8 max-w-2xl">
    <div class="flex items-center gap-6 mb-8">
        <img src="<?= profile_image($patient['profile_image'], $patient['full_name']) ?>" class="w-24 h-24 rounded-2xl object-cover ring-4 ring-primary-500/20" alt="">
        <div>
            <h2 class="text-xl font-bold"><?= e($patient['full_name']) ?></h2>
            <p class="text-gray-500"><?= e($patient['email']) ?></p>
            <div class="flex gap-2 mt-2">
                <span class="px-2 py-0.5 bg-gray-100 dark:bg-slate-700 rounded text-xs capitalize"><?= e($patient['gender']) ?></span>
                <?php if ($patient['blood_group']): ?><span class="px-2 py-0.5 bg-red-100 dark:bg-red-900/30 text-red-700 rounded text-xs"><?= e($patient['blood_group']) ?></span><?php endif; ?>
            </div>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm(this)" class="space-y-5">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div><label class="block text-sm font-medium mb-1.5">Full Name</label>
                <input type="text" name="full_name" required value="<?= e($patient['full_name']) ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
            <div><label class="block text-sm font-medium mb-1.5">Email</label>
                <input type="email" name="email" required value="<?= e($patient['email']) ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
            <div><label class="block text-sm font-medium mb-1.5">Gender</label>
                <select name="gender" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                    <?php foreach (['male','female','other'] as $g): ?>
                    <option value="<?= $g ?>" <?= $patient['gender'] === $g ? 'selected' : '' ?>><?= ucfirst($g) ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div><label class="block text-sm font-medium mb-1.5">Age</label>
                <input type="number" name="age" value="<?= e($patient['age']) ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
            <div><label class="block text-sm font-medium mb-1.5">Blood Group</label>
                <select name="blood_group" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                    <option value="">Select</option>
                    <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                    <option value="<?= $bg ?>" <?= $patient['blood_group'] === $bg ? 'selected' : '' ?>><?= $bg ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div><label class="block text-sm font-medium mb-1.5">Phone</label>
                <input type="tel" name="phone" value="<?= e($patient['phone']) ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
        </div>
        <div><label class="block text-sm font-medium mb-1.5">Address</label>
            <textarea name="address" rows="2" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"><?= e($patient['address']) ?></textarea></div>
        <div><label class="block text-sm font-medium mb-1.5">New Password (optional)</label>
            <input type="password" name="password" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
        <div><label class="block text-sm font-medium mb-1.5">Profile Image</label>
            <input type="file" name="profile_image" accept="image/*" onchange="previewImage(this,'pat-prof-preview')" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-primary-50 file:text-primary-700"></div>
        <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl shadow-lg shadow-primary-500/25">Save Changes</button>
    </form>
</div>

<?php $content = ob_get_clean(); include APP_ROOT . '/layouts/dashboard.php'; ?>
