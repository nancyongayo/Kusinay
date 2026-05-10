<?php
$pageTitle = 'Home';
$activeNav = 'home';
require_once __DIR__ . '/../templates/mother_layout.php';
$hour = (int)date('H');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
?>

<div class="mb-4">
    <h4 class="fw-bold mb-1"><?= $greeting ?>, <?= htmlspecialchars(explode(' ', $userName)[0]) ?>!</h4>
    <p class="text-muted mb-0" style="font-size:.92rem"><?= date('l, F j, Y') ?></p>
</div>

<div class="row g-3 mb-4">
    <?php foreach ([['🥗','Meal Plans','—'],['📊','Records','—'],['✅','Validated','—'],['🏘️','Barangay','Active']] as [$icon,$label,$val]): ?>
    <div class="col-6 col-xl-3">
        <div style="background:#fff;border-radius:1rem;padding:1.5rem;border:1.5px solid rgba(107,122,58,.12);text-align:center">
            <div style="font-size:2rem;margin-bottom:.4rem"><?= $icon ?></div>
            <div style="font-size:.82rem;color:var(--kn-muted);text-transform:uppercase;letter-spacing:.04em"><?= $label ?></div>
            <div style="font-size:1.6rem;font-weight:800;color:var(--kn-green)"><?= $val ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div style="background:#fff;border-radius:1rem;padding:2rem;border:1.5px dashed rgba(107,122,58,.2);text-align:center;color:var(--kn-muted)">
    <div style="font-size:2rem;margin-bottom:.5rem">🚧</div>
    <p class="mb-0">Dashboard features are coming soon. Your authentication is fully working.</p>
    <div style="margin-top:1rem;font-size:.82rem;background:rgba(107,122,58,.07);display:inline-block;padding:.4rem 1rem;border-radius:8px">
        🔒 Session secured &nbsp;·&nbsp; OTP verified &nbsp;·&nbsp; <?= date('F j, Y') ?>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
