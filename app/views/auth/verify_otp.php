<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
if (!isset($_SESSION['temp_user_id'])) {
    header('Location: index.php?action=login');
    exit;
}
require_once __DIR__ . '/../../../config/google.php';
$errors  = $_SESSION['errors']  ?? [];
$devOtp  = $_SESSION['dev_otp'] ?? null;
$flash   = $_SESSION['flash']   ?? null;
unset($_SESSION['errors'], $_SESSION['dev_otp'], $_SESSION['flash']);
?>
<?php include __DIR__ . '/../templates/header.php'; ?>

<div class="page-card p-4 p-md-5 text-center">

    <div class="brand-logo mb-3">
        <div class="logo-circle"><img src="public/images/logo.png" alt="KusiNay Logo"></div>
    </div>

    <!-- Icon -->
    <div style="font-size:3rem;margin-bottom:.5rem">📧</div>
    <h2 class="h5 fw-bold mb-1" style="color:var(--kn-dark)">Check your email</h2>
    <p style="color:var(--kn-muted);font-size:.9rem;margin-bottom:.5rem">
        We sent a 6-digit OTP to your registered email address.
    </p>

    <!-- Countdown timer -->
    <div id="timerWrap" class="mb-3">
        <span style="font-size:.85rem;color:var(--kn-muted)">Expires in </span>
        <span id="countdown"
              style="font-weight:700;color:var(--kn-orange);font-size:1rem;font-variant-numeric:tabular-nums">
            <?= OTP_EXPIRY_MINUTES ?>:00
        </span>
    </div>

    <!-- Dev mode OTP hint -->
    <?php if ($devOtp): ?>
        <div class="alert-kn-success p-3 mb-3" style="font-size:.85rem;text-align:left">
            ⚙ <strong>Dev mode — SMTP not configured.</strong><br>
            Your OTP is: <strong style="letter-spacing:.25em;font-size:1.1rem;color:var(--kn-orange)"><?= htmlspecialchars($devOtp) ?></strong>
        </div>
    <?php endif; ?>

    <!-- Flash message -->
    <?php if ($flash): ?>
        <div class="alert-kn-success p-3 mb-3" style="font-size:.85rem">
            ✅ <?= htmlspecialchars($flash) ?>
        </div>
    <?php endif; ?>

    <!-- Errors -->
    <?php if ($errors): ?>
        <div class="alert-kn-error p-3 mb-3 text-start">
            <?php foreach ($errors as $e): ?>
                <div>⚠ <?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- OTP Form -->
    <form action="index.php?action=verifyOtp" method="POST" id="otpForm">
        <div class="mb-4">
            <label class="form-label" for="otp">Enter OTP Code</label>
            <input type="text" id="otp" name="otp"
                   class="form-control text-center fw-bold"
                   maxlength="6" pattern="\d{6}" placeholder="• • • • • •"
                   style="font-size:2rem;letter-spacing:.5em;border-color:rgba(107,122,58,0.3);
                          background:rgba(245,237,214,0.4);padding:.75rem"
                   required autocomplete="one-time-code" inputmode="numeric"
                   autofocus>
        </div>
        <button type="submit" class="btn btn-primary w-100 btn-lg" id="submitBtn">
            Verify OTP
        </button>
    </form>

    <p class="mt-3 mb-0" style="font-size:.85rem;color:var(--kn-muted)">
        Didn't receive it?
        <a href="index.php?action=resendOtp" class="kn-link">Resend OTP</a>
        &nbsp;·&nbsp;
        <a href="index.php?action=login" class="kn-link">Back to Login</a>
    </p>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>

<script>
// Countdown timer — OTP_EXPIRY_MINUTES × 60 seconds
let seconds = <?= OTP_EXPIRY_MINUTES * 60 ?>;
const display  = document.getElementById('countdown');
const submitBtn = document.getElementById('submitBtn');

const timer = setInterval(() => {
    seconds--;
    if (seconds <= 0) {
        clearInterval(timer);
        display.textContent = '0:00';
        display.style.color = '#e74c3c';
        submitBtn.disabled  = true;
        submitBtn.textContent = 'OTP Expired — Resend to continue';
        return;
    }
    const m = Math.floor(seconds / 60);
    const s = String(seconds % 60).padStart(2, '0');
    display.textContent = `${m}:${s}`;
    // Turn red in last 60 seconds
    display.style.color = seconds <= 60 ? '#e74c3c' : 'var(--kn-orange)';
}, 1000);

// Only allow digits in OTP input
document.getElementById('otp').addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 6);
});
</script>
