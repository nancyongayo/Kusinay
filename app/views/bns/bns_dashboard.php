<?php
$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
require_once __DIR__ . '/../templates/bns_layout.php';
$hour = (int)date('H');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
?>

<div class="mb-4">
    <h4 class="fw-bold mb-1"><?= $greeting ?>, <?= htmlspecialchars(explode(' ', $userName)[0]) ?>!</h4>
    <p class="text-muted mb-0" style="font-size:.92rem"><?= date('l, F j, Y') ?> &mdash; BNS Staff Dashboard</p>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-6">
        <div style="background:#fff;border-radius:1rem;padding:1.5rem;border:1.5px solid rgba(107,122,58,.12);display:flex;align-items:center;gap:1rem;">
            <div style="width:52px;height:52px;background:rgba(107,122,58,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0">🏠</div>
            <div>
                <div style="font-size:1.9rem;font-weight:800;color:var(--kn-green);line-height:1"><?= number_format($totalFamilies) ?></div>
                <div style="font-size:.85rem;color:var(--kn-muted);margin-top:.15rem">Families Encoded</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-6">
        <div style="background:#fff;border-radius:1rem;padding:1.5rem;border:1.5px solid rgba(107,122,58,.12);display:flex;align-items:center;gap:1rem;">
            <div style="width:52px;height:52px;background:rgba(107,122,58,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0">👶</div>
            <div>
                <div style="font-size:1.9rem;font-weight:800;color:var(--kn-green);line-height:1"><?= number_format($totalChildren) ?></div>
                <div style="font-size:.85rem;color:var(--kn-muted);margin-top:.15rem">Children (0–60+ mos)</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access -->
<p style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--kn-muted);margin-bottom:.75rem">Quick Access</p>
<div class="row g-3">
    <div class="col-md-6 col-xl-4">
        <a href="index.php?action=familyProfiles" style="display:block;background:#fff;border:1.5px solid rgba(107,122,58,.12);border-radius:1rem;padding:1.5rem;text-decoration:none;color:var(--kn-dark);transition:all .15s" onmouseover="this.style.borderColor='var(--kn-green)';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='rgba(107,122,58,.12)';this.style.transform=''">
            <div style="font-size:1.75rem;margin-bottom:.6rem">👨‍👩‍👧‍👦</div>
            <div style="font-weight:700;font-size:1rem;color:var(--kn-green)">Family Profiles</div>
            <div style="font-size:.85rem;color:var(--kn-muted);margin-top:.2rem">View, add, and manage household profiles</div>
        </a>
    </div>
    <div class="col-md-6 col-xl-4">
        <a href="index.php?action=dataEncoding" style="display:block;background:#fff;border:1.5px solid rgba(107,122,58,.12);border-radius:1rem;padding:1.5rem;text-decoration:none;color:var(--kn-dark);transition:all .15s" onmouseover="this.style.borderColor='var(--kn-green)';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='rgba(107,122,58,.12)';this.style.transform=''">
            <div style="font-size:1.75rem;margin-bottom:.6rem">📋</div>
            <div style="font-weight:700;font-size:1rem;color:var(--kn-green)">Resident Assessment</div>
            <div style="font-size:.85rem;color:var(--kn-muted);margin-top:.2rem">OPT Plus — List of Recipients for Nutrition Assessment</div>
        </a>
    </div>
    <div class="col-md-6 col-xl-4">
        <a href="index.php?action=accomplishmentReport" style="display:block;background:#fff;border:1.5px solid rgba(107,122,58,.12);border-radius:1rem;padding:1.5rem;text-decoration:none;color:var(--kn-dark);transition:all .15s" onmouseover="this.style.borderColor='var(--kn-green)';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='rgba(107,122,58,.12)';this.style.transform=''">
            <div style="font-size:1.75rem;margin-bottom:.6rem">📊</div>
            <div style="font-weight:700;font-size:1rem;color:var(--kn-green)">Monthly Report</div>
            <div style="font-size:.85rem;color:var(--kn-muted);margin-top:.2rem">Generate and submit monthly accomplishment report</div>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
