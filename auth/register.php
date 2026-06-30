<?php
require_once dirname(__DIR__) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';

guest_only();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $data = [
        'full_name' => sanitize($_POST['full_name'] ?? ''),
        'email'     => sanitize($_POST['email'] ?? ''),
        'password'  => $_POST['password'] ?? '',
        'password_confirmation' => $_POST['password_confirmation'] ?? '',
        'gender'    => sanitize($_POST['gender'] ?? ''),
        'age'       => $_POST['age'] ?? '',
        'blood_group' => sanitize($_POST['blood_group'] ?? ''),
        'phone'     => sanitize($_POST['phone'] ?? ''),
        'address'   => sanitize($_POST['address'] ?? ''),
    ];

    $validator = new Validator($data);
    $validator->required('full_name')->required('email')->email('email')
              ->required('password')->min('password', 6)->confirmed('password')
              ->unique('email', 'users')
              ->required('gender')->in('gender', ['male', 'female', 'other'])
              ->required('age')->numeric('age')->min_value('age', 1)
              ->required('phone')->required('address');

    if ($validator->fails()) {
        store_errors($validator->errors());
        store_old_input($data);
        flash('error', 'Please fix the errors below.');
        redirect(base_url('auth/register.php'));
    }

    require_once APP_ROOT . '/includes/models.php';

    try {
        $profileImage = null;
        if (!empty($_FILES['profile_image']['name'])) {
            $profileImage = upload_profile_image($_FILES['profile_image']);
        }

        $patientModel = new PatientModel();
        $patientModel->create(
            ['full_name' => $data['full_name'], 'email' => $data['email'], 'password' => $data['password'], 'profile_image' => $profileImage],
            ['gender' => $data['gender'], 'age' => (int)$data['age'], 'blood_group' => $data['blood_group'], 'phone' => $data['phone'], 'address' => $data['address']]
        );

        flash('success', 'Registration successful! Please login.');
        redirect(base_url('auth/login.php'));
    } catch (Exception $e) {
        flash('error', $e->getMessage());
        store_old_input($data);
        redirect(base_url('auth/register.php'));
    }
}

$errors = get_errors();
$pageTitle = 'Register';
?>
<?php include APP_ROOT . '/layouts/head.php'; ?>

<div class="min-h-screen flex items-center justify-center p-8 py-12">
    <div class="w-full max-w-2xl animate-slide-up">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-primary-600 to-primary-400 bg-clip-text text-transparent"><?= APP_NAME ?></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Create your patient account</p>
        </div>

        <div class="glass-card rounded-2xl p-8">
            <form method="POST" action="" enctype="multipart/form-data" onsubmit="return validateForm(this)" class="space-y-5">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Full Name *</label>
                        <input type="text" name="full_name" required value="<?= old('full_name') ?>"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                        <?php if (has_error('full_name', $errors)): ?><p class="text-xs text-red-500 mt-1"><?= e(error_message('full_name', $errors)) ?></p><?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Email *</label>
                        <input type="email" name="email" required value="<?= old('email') ?>"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                        <?php if (has_error('email', $errors)): ?><p class="text-xs text-red-500 mt-1"><?= e(error_message('email', $errors)) ?></p><?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Password *</label>
                        <input type="password" name="password" required minlength="6"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Gender *</label>
                        <select name="gender" required class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                            <option value="">Select gender</option>
                            <option value="male" <?= old('gender') === 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= old('gender') === 'female' ? 'selected' : '' ?>>Female</option>
                            <option value="other" <?= old('gender') === 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Age *</label>
                        <input type="number" name="age" required min="1" max="150" value="<?= old('age') ?>"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Blood Group</label>
                        <select name="blood_group" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                            <option value="">Select blood group</option>
                            <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                            <option value="<?= $bg ?>" <?= old('blood_group') === $bg ? 'selected' : '' ?>><?= $bg ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Phone *</label>
                        <input type="tel" name="phone" required value="<?= old('phone') ?>"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">Address *</label>
                    <textarea name="address" required rows="2" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none"><?= old('address') ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">Profile Image</label>
                    <input type="file" name="profile_image" accept="image/*" onchange="previewImage(this, 'reg-preview')"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-primary-50 file:text-primary-700">
                    <img id="reg-preview" class="upload-preview mt-3 hidden" alt="Preview">
                </div>

                <button type="submit" class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-all shadow-lg shadow-primary-500/25">
                    Create Account
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Already have an account? <a href="<?= base_url('auth/login.php') ?>" class="text-primary-600 font-medium">Sign In</a>
            </p>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/layouts/footer.php'; ?>
