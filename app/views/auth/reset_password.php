<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$errors = $_SESSION['errors'] ?? [];
$token  = $_GET['token'] ?? '';
unset($_SESSION['errors']);
?>
<?php include __DIR__ . '/../templates/header.php'; ?>

<div class="page-card p-4 p-md-5">
    <div class="brand-logo mb-4">
        <div class="logo-circle"><img src="public/images/logo.png" alt="KusiNay Logo"></div>
    </div>

    <div class="text-center mb-4">
        <div style="font-size:2.2rem">🔒</div>
        <h2 class="h5 fw-bold mb-1" style="color:var(--kn-dark)">Set a new password</h2>
    </div>

    <?php if ($errors): ?>
        <div class="alert-kn-error p-3 mb-3">
            <?php foreach ($errors as $e): ?><div>⚠ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=resetPassword" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <div class="mb-3">
            <label class="form-label" for="password">New Password</label>
            <input type="password" id="password" name="password" class="form-control"
                   placeholder="Min. 8 chars" required autocomplete="new-password"
                   oninput="checkStrength(this.value)">
            <div class="mt-1" style="background:rgba(107,122,58,0.12);border-radius:2px;height:4px">
                <div id="strengthBar" class="strength-bar" style="width:0%;background:var(--kn-orange)"></div>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label" for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                   placeholder="Repeat password" required autocomplete="new-password">
            <small id="matchMsg" style="font-size:.78rem"></small>
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-lg">Update Password</button>
    </form>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
<script>
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
