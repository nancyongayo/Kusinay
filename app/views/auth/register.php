<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old']    ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<?php include __DIR__ . '/../templates/header.php'; ?>

<div class="page-card p-4 p-md-5">

    <!-- Brand -->
    <div class="brand-logo mb-4">
        <div class="logo-circle"><img src="public/images/logo.png" alt="KusiNay Logo"></div>
        <div class="logo-tagline">Create your account</div>
    </div>

    <!-- Errors -->
    <?php if ($errors): ?>
        <div class="alert-kn-error p-3 mb-3">
            <?php foreach ($errors as $e): ?>
                <div>⚠ <?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=register" method="POST" novalidate id="registerForm">
        <?= \Security::csrfField() ?>

        <!-- Name row -->
        <div class="row g-2 mb-3">
            <div class="col-12 col-sm-4">
                <label class="form-label" for="last_name">Last Name <span style="color:var(--kn-orange)">*</span></label>
                <input type="text" id="last_name" name="last_name" class="form-control"
                       placeholder="dela Cruz" required
                       value="<?= htmlspecialchars($old['lastName'] ?? '') ?>">
            </div>
            <div class="col-12 col-sm-4">
                <label class="form-label" for="first_name">First Name <span style="color:var(--kn-orange)">*</span></label>
                <input type="text" id="first_name" name="first_name" class="form-control"
                       placeholder="Juan" required
                       value="<?= htmlspecialchars($old['firstName'] ?? '') ?>">
            </div>
            <div class="col-12 col-sm-4">
                <label class="form-label" for="middle_name">Middle Name</label>
                <input type="text" id="middle_name" name="middle_name" class="form-control"
                       placeholder="(optional)"
                       value="<?= htmlspecialchars($old['middleName'] ?? '') ?>">
            </div>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label" for="email">Email Address <span style="color:var(--kn-orange)">*</span></label>
            <input type="email" id="email" name="email" class="form-control"
                   placeholder="you@example.com" required autocomplete="email"
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>">
        </div>

        <!-- Password -->
        <div class="mb-2">
            <label class="form-label" for="password">Password <span style="color:var(--kn-orange)">*</span></label>
            <div class="input-group">
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="Min. 8 chars" required autocomplete="new-password"
                       oninput="checkStrength(this.value)">
                <button type="button" class="btn btn-outline-secondary" id="togglePwd"
                        style="border-color:rgba(107,122,58,0.22)" aria-label="Toggle password">👁</button>
            </div>
            <!-- Strength bar -->
            <div class="mt-1" style="background:rgba(107,122,58,0.12);border-radius:2px;height:4px">
                <div id="strengthBar" class="strength-bar" style="width:0%;background:var(--kn-orange)"></div>
            </div>
            <small id="strengthLabel" style="color:var(--kn-muted);font-size:.78rem"></small>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label class="form-label" for="confirm_password">Confirm Password <span style="color:var(--kn-orange)">*</span></label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                   placeholder="Repeat password" required autocomplete="new-password">
            <small id="matchMsg" style="font-size:.78rem"></small>
        </div>

        <!-- Password policy hint -->
        <div class="mb-4 p-3" style="background:rgba(107,122,58,0.07);border-radius:.75rem;font-size:.82rem;color:var(--kn-muted)">
            Password must contain: uppercase, lowercase, number, and special character (min. 8 characters).
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-lg">Create Account</button>
    </form>

    <p class="text-center mt-3 mb-0" style="font-size:.9rem;color:var(--kn-muted)">
        Already have an account?
        <a href="index.php?action=login" class="kn-link">Sign in</a>
    </p>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
<script>
// Toggle password visibility
document.getElementById('togglePwd').addEventListener('click', function () {
    const pwd = document.getElementById('password');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
    this.textContent = pwd.type === 'password' ? '👁' : '🙈';
});

// Password strength checker
function checkStrength(val) {
    let score = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[a-z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[\W_]/.test(val))        score++;

    const bar   = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');
    const pct   = (score / 5) * 100;
    bar.style.width = pct + '%';

    const levels = ['', 'Very Weak', 'Weak', 'Fair', 'Strong', 'Very Strong'];
    const colors = ['', '#e74c3c', '#e67e22', '#f1c40f', '#6B7A3A', '#27ae60'];
    bar.style.background = colors[score] || 'var(--kn-orange)';
    label.textContent    = levels[score] || '';
    label.style.color    = colors[score] || 'var(--kn-muted)';
}

// Confirm password match
document.getElementById('confirm_password').addEventListener('input', function () {
    const msg = document.getElementById('matchMsg');
    if (this.value === document.getElementById('password').value) {
        msg.textContent = '✓ Passwords match';
        msg.style.color = 'var(--kn-green)';
    } else {
        msg.textContent = '✗ Passwords do not match';
        msg.style.color = '#e74c3c';
    }
});
</script>
