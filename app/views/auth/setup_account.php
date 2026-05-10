<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
// Token comes from GET on first load, or from POST hidden field on re-render after error
$token = $_GET['token'] ?? $_POST['token'] ?? '';
?>
<?php include __DIR__ . '/../templates/header.php'; ?>

<div class="page-card p-4 p-md-5">

    <div class="brand-logo mb-4">
        <div class="logo-circle"><img src="public/images/logo.png" alt="KusiNay Logo"></div>
        <div class="logo-tagline">Smart Meal Planning &amp; Nutrition</div>
    </div>

    <div class="text-center mb-4">
        <div style="font-size:2.4rem">🔐</div>
        <h2 class="h5 fw-bold mb-1" style="color:var(--kn-dark)">Set Up Your Password</h2>
        <p style="font-size:.88rem;color:var(--kn-muted);margin:0">
            Welcome to KusiNay! Create a password to secure your account.
        </p>
    </div>

    <?php if ($errors): ?>
        <div class="alert-kn-error p-3 mb-3">
            <?php foreach ($errors as $e): ?>
                <div>⚠ <?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Password policy -->
    <div style="background:rgba(107,122,58,.08);border:1px solid rgba(107,122,58,.2);border-radius:.6rem;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.82rem;color:var(--kn-dark)">
        <strong>Password requirements:</strong>
        <ul style="margin:.35rem 0 0 1rem;padding:0">
            <li>At least 8 characters</li>
            <li>At least one uppercase letter (A–Z)</li>
            <li>At least one lowercase letter (a–z)</li>
            <li>At least one number (0–9)</li>
            <li>At least one special character (!@#$%^&amp;* etc.)</li>
        </ul>
    </div>

    <form action="index.php?action=setupAccount&token=<?= urlencode($token) ?>" method="POST" novalidate>
        <?= \Security::csrfField() ?>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <div class="mb-3">
            <label class="form-label" for="password">New Password</label>
            <div class="input-group">
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="Min. 8 characters" required autocomplete="new-password"
                       oninput="checkStrength(this.value)">
                <button type="button" class="btn btn-outline-secondary" id="togglePwd"
                        aria-label="Toggle password" style="border-color:rgba(107,122,58,0.22)">👁</button>
            </div>
            <div class="mt-1" style="background:rgba(107,122,58,.12);border-radius:2px;height:4px">
                <div id="strengthBar" style="width:0%;height:100%;border-radius:2px;background:var(--kn-orange);transition:width .2s,background .2s"></div>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label" for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                   placeholder="Repeat your password" required autocomplete="new-password">
            <small id="matchMsg" style="font-size:.78rem"></small>
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-lg">
            <i class="bi bi-shield-lock-fill me-1"></i> Set Password &amp; Enter KusiNay
        </button>
    </form>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
<script>
document.getElementById('togglePwd').addEventListener('click', function () {
    const pwd = document.getElementById('password');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
    this.textContent = pwd.type === 'password' ? '👁' : '🙈';
});
function checkStrength(val) {
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[a-z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[\W_]/.test(val)) score++;
    const bar    = document.getElementById('strengthBar');
    const colors = ['','#e74c3c','#e67e22','#f1c40f','#6B7A3A','#27ae60'];
    bar.style.width      = (score / 5 * 100) + '%';
    bar.style.background = colors[score] || 'var(--kn-orange)';
}
document.getElementById('confirm_password').addEventListener('input', function () {
    const msg = document.getElementById('matchMsg');
    if (this.value === document.getElementById('password').value) {
        msg.textContent = '✓ Passwords match'; msg.style.color = 'var(--kn-green)';
    } else {
        msg.textContent = '✗ Passwords do not match'; msg.style.color = '#e74c3c';
    }
});
</script>
