<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/google.php';

// Build Google OAuth URL
require_once __DIR__ . '/../../../vendor/autoload.php';
$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope('email');
$client->addScope('profile');
$googleAuthUrl = $client->createAuthUrl();

$errors = $_SESSION['errors'] ?? [];
$flash  = $_SESSION['flash']  ?? '';
unset($_SESSION['errors'], $_SESSION['flash']);

// Reason-based messages
$reason = $_GET['reason'] ?? '';
if ($reason === 'timeout' && !$flash) {
    $flash = 'Your session has expired due to inactivity. Please log in again.';
} elseif ($reason === 'locked') {
    $errors[] = 'Your account has been locked by an administrator. Please contact support.';
}
?>
<?php include __DIR__ . '/../templates/header.php'; ?>

<div class="page-card p-4 p-md-5">

    <!-- Brand -->
    <div class="brand-logo mb-4">
        <div class="logo-circle"><img src="public/images/logo.png" alt="KusiNay Logo"></div>
        <div class="logo-tagline">Smart Meal Planning &amp; Nutrition</div>
    </div>

    <!-- Flash / Errors -->
    <?php if ($flash): ?>
        <div class="alert-kn-success p-3 mb-3"><?= $flash ?></div>
    <?php endif; ?>
    <?php if ($errors): ?>
        <div class="alert-kn-error p-3 mb-3">
            <?php foreach ($errors as $e): ?>
                <div>⚠ <?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h2 class="h5 fw-bold mb-1 text-center" style="color:var(--kn-dark)">Welcome</h2>
    <p class="text-center mb-4" style="color:var(--kn-muted);font-size:.9rem">Sign in to your KusiNay account</p>

    <!-- Login Form -->
    <form action="index.php?action=login" method="POST" novalidate>
        <?= \Security::csrfField() ?>
        <div class="mb-3">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control"
                   placeholder="you@example.com"
                   value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>"
                   required autocomplete="email">
        </div>
        <div class="mb-1">
            <label class="form-label" for="password">Password</label>
            <div class="input-group">
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="Enter your password" required autocomplete="current-password">
                <button type="button" class="btn btn-outline-secondary" id="togglePwd"
                        aria-label="Toggle password visibility"
                        style="border-color:rgba(107,122,58,0.22)">👁</button>
            </div>
        </div>
        <div class="text-end mb-4">
            <a href="index.php?action=forgotPassword" class="kn-link" style="font-size:.85rem">Forgot password?</a>
        </div>
        <button type="submit" class="btn btn-primary w-100 btn-lg">Sign In</button>
    </form>

    <!-- Divider -->
    <div class="divider-text my-4">or continue with</div>

    <!-- Google Sign-In -->
    <a href="<?= htmlspecialchars($googleAuthUrl) ?>" class="btn btn-google w-100 btn-lg mb-4">
        <svg width="20" height="20" viewBox="0 0 48 48" aria-hidden="true">
            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
        </svg>
        Sign in with Google
    </a>

    <p class="text-center mb-0" style="font-size:.9rem;color:var(--kn-muted)">
        Don't have an account?
        <a href="index.php?action=register" class="kn-link">Register</a>
    </p>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
<script>
document.getElementById('togglePwd').addEventListener('click', function () {
    const pwd = document.getElementById('password');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
    this.textContent = pwd.type === 'password' ? '👁' : '🙈';
});
</script>
