<?php
require_once dirname(__DIR__) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';
require_once APP_ROOT . '/includes/models.php';
require_once APP_ROOT . '/includes/components.php';

require_doctor();

$doctorModel = new DoctorModel();
$doctor = $doctorModel->findByUserId(current_user()['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $data = array_map('sanitize', $_POST);
    $userData = ['full_name' => $data['full_name'], 'email' => $data['email'], 'role' => 'doctor'];
    if (!empty($_POST['password'])) $userData['password'] = $_POST['password'];
    if (!empty($_FILES['profile_image']['name'])) {
        delete_profile_image($doctor['profile_image']);
        $userData['profile_image'] = upload_profile_image($_FILES['profile_image']);
    }
    $doctorModel->update($doctor['id'], $userData, [
        'specialization' => $data['specialization'], 'phone' => $data['phone'],
        'experience' => (int)$data['experience'], 'qualification' => $data['qualification'],
        'available_days' => $doctor['available_days'], 'consultation_fee' => (float)$data['consultation_fee'],
    ]);
    // Update session
    $_SESSION['user_name'] = $data['full_name'];
    $_SESSION['user_email'] = $data['email'];
    flash('success', 'Profile updated.');
    redirect(base_url('doctor/profile.php'));
}

$pageTitle = 'My Profile';
ob_start();
render_page_header('My Profile', 'View and update your profile');
?>

<div class="glass-card rounded-2xl p-8 max-w-2xl">
    <div class="flex items-center gap-6 mb-8">
        <img src="<?= profile_image($doctor['profile_image'], $doctor['full_name']) ?>" class="w-24 h-24 rounded-2xl object-cover ring-4 ring-primary-500/20" alt="">
        <div>
            <h2 class="text-xl font-bold"><?= e($doctor['full_name']) ?></h2>
            <p class="text-primary-600 font-medium"><?= e($doctor['specialization']) ?></p>
            <p class="text-sm text-gray-500 mt-1"><?= e($doctor['qualification']) ?></p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" class="space-y-5">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div><label class="block text-sm font-medium mb-1.5">Full Name</label>
                <input type="text" name="full_name" required value="<?= e($doctor['full_name']) ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
            <div><label class="block text-sm font-medium mb-1.5">Email</label>
                <input type="email" name="email" required value="<?= e($doctor['email']) ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
            <div><label class="block text-sm font-medium mb-1.5">Phone</label>
                <input type="tel" name="phone" value="<?= e($doctor['phone']) ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
            <div><label class="block text-sm font-medium mb-1.5">Specialization</label>
                <input type="text" name="specialization" value="<?= e($doctor['specialization']) ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
            <div><label class="block text-sm font-medium mb-1.5">Experience (years)</label>
                <input type="number" name="experience" value="<?= e($doctor['experience']) ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
            <div><label class="block text-sm font-medium mb-1.5">Consultation Fee</label>
                <input type="number" name="consultation_fee" value="<?= e($doctor['consultation_fee']) ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
        </div>
        <div><label class="block text-sm font-medium mb-1.5">Qualification</label>
            <input type="text" name="qualification" value="<?= e($doctor['qualification']) ?>" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
        <div><label class="block text-sm font-medium mb-1.5">Available Days</label>
            <p class="text-sm text-gray-500"><?= e($doctor['available_days']) ?></p></div>
        <div><label class="block text-sm font-medium mb-1.5">New Password (optional)</label>
            <input type="password" name="password" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"></div>
        <div><label class="block text-sm font-medium mb-1.5">Profile Image</label>
            <input type="file" name="profile_image" accept="image/*" onchange="previewImage(this,'prof-preview')" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-primary-50 file:text-primary-700"></div>
        <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl shadow-lg shadow-primary-500/25">Save Changes</button>
    </form>
</div>

<?php $content = ob_get_clean(); include APP_ROOT . '/layouts/dashboard.php'; ?>
