<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$errors = $_SESSION['errors'] ?? [];
$flash  = $_SESSION['flash']  ?? '';
unset($_SESSION['errors'], $_SESSION['flash']);
?>
<?php include __DIR__ . '/../templates/header.php'; ?>

<div class="page-card p-4 p-md-5">
    <div class="brand-logo mb-4">
        <div class="logo-circle"><img src="public/images/logo.png" alt="KusiNay Logo"></div>
    </div>

    <div class="text-center mb-4">
        <div style="font-size:2.2rem">🔑</div>
        <h2 class="h5 fw-bold mb-1" style="color:var(--kn-dark)">Forgot your password?</h2>
        <p style="color:var(--kn-muted);font-size:.88rem">Enter your email and we'll send a reset link.</p>
    </div>

    <?php if ($flash): ?>
        <div class="alert-kn-success p-3 mb-3"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>
    <?php if ($errors): ?>
        <div class="alert-kn-error p-3 mb-3">
            <?php foreach ($errors as $e): ?><div>⚠ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=forgotPassword" method="POST">
        <div class="mb-4">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control"
                   placeholder="you@example.com" required autocomplete="email">
        </div>
        <button type="submit" class="btn btn-primary w-100 btn-lg">Send Reset Link</button>
    </form>

    <p class="text-center mt-3 mb-0" style="font-size:.9rem;color:var(--kn-muted)">
        <a href="index.php?action=login" class="kn-link">← Back to Login</a>
    </p>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
