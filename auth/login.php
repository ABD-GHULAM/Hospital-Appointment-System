<?php
require_once dirname(__DIR__) . '/bootstrap.php';
require_once APP_ROOT . '/middleware/roles.php';

guest_only();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $validator = new Validator(['email' => $email, 'password' => $password]);
    $validator->required('email')->email('email')->required('password');

    if ($validator->fails()) {
        store_errors($validator->errors());
        store_old_input(['email' => $email]);
        flash('error', 'Please fix the errors below.');
        redirect(base_url('auth/login.php'));
    }

    require_once APP_ROOT . '/includes/models.php';
    $userModel = new UserModel();
    $user = $userModel->findByEmail($email);

    if ($user && password_verify($password, $user['password'])) {
        login_user($user);
        flash('success', 'Welcome back, ' . $user['full_name'] . '!');
        redirect(dashboard_url());
    }

    flash('error', 'Invalid email or password.');
    store_old_input(['email' => $email]);
    redirect(base_url('auth/login.php'));
}

$errors = get_errors();
$pageTitle = 'Login';
?>
<?php include APP_ROOT . '/layouts/head.php'; ?>

<div class="min-h-screen flex">
    <!-- Left panel - branding -->
    <div class="hidden lg:flex lg:w-1/2 auth-gradient relative overflow-hidden items-center justify-center p-12">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-20 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-20 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>
        <div class="relative z-10 text-white max-w-lg">
            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mb-8">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold mb-4"><?= APP_NAME ?></h1>
            <p class="text-lg text-white/80 leading-relaxed">Modern healthcare appointment management. Book, manage, and track appointments seamlessly.</p>
            <div class="mt-12 grid grid-cols-3 gap-6">
                <div class="text-center"><p class="text-3xl font-bold">500+</p><p class="text-sm text-white/60 mt-1">Patients</p></div>
                <div class="text-center"><p class="text-3xl font-bold">20+</p><p class="text-sm text-white/60 mt-1">Doctors</p></div>
                <div class="text-center"><p class="text-3xl font-bold">99%</p><p class="text-sm text-white/60 mt-1">Satisfaction</p></div>
            </div>
        </div>
    </div>

    <!-- Right panel - login form -->
    <div class="flex-1 flex items-center justify-center p-8">
        <div class="w-full max-w-md animate-slide-up">
            <div class="lg:hidden text-center mb-8">
                <h1 class="text-2xl font-bold bg-gradient-to-r from-primary-600 to-primary-400 bg-clip-text text-transparent"><?= APP_NAME ?></h1>
            </div>

            <h2 class="text-2xl font-bold mb-2">Welcome back</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8">Sign in to your account to continue</p>

            <form method="POST" action="" onsubmit="return validateForm(this)" class="space-y-5">
                <?= csrf_field() ?>

                <div class="form-group">
                    <input type="email" name="email" id="email" placeholder=" " required
                           value="<?= old('email') ?>"
                           class="form-input w-full px-4 pt-6 pb-2 text-sm bg-gray-50 dark:bg-slate-700/50 border <?= has_error('email', $errors) ? 'border-red-500' : 'border-gray-200 dark:border-slate-600' ?> rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                    <label for="email" class="form-label absolute left-4 top-4 text-sm text-gray-500 transition-all">Email Address</label>
                    <?php if (has_error('email', $errors)): ?>
                    <p class="text-xs text-red-500 mt-1"><?= e(error_message('email', $errors)) ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <input type="password" name="password" id="password" placeholder=" " required
                           class="form-input w-full px-4 pt-6 pb-2 text-sm bg-gray-50 dark:bg-slate-700/50 border <?= has_error('password', $errors) ? 'border-red-500' : 'border-gray-200 dark:border-slate-600' ?> rounded-xl focus:ring-2 focus:ring-primary-500 outline-none">
                    <label for="password" class="form-label absolute left-4 top-4 text-sm text-gray-500 transition-all">Password</label>
                </div>

                <button type="submit" class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-all shadow-lg shadow-primary-500/25 btn-ripple">
                    Sign In
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-8">
                Don't have an account?
                <a href="<?= base_url('auth/register.php') ?>" class="text-primary-600 hover:text-primary-700 font-medium">Register as Patient</a>
            </p>

            <div class="mt-8 p-4 bg-gray-50 dark:bg-slate-800 rounded-xl text-xs text-gray-500 dark:text-gray-400">
                <p class="font-semibold mb-2">Demo Credentials (run database/setup.php first):</p>
                <p>Admin: admin@clinic.com / admin123</p>
                <p>Doctor: sarah.mitchell@clinic.com / doctor123</p>
                <p>Patient: john.anderson@email.com / patient123</p>
            </div>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/layouts/footer.php'; ?>
