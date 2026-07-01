<?php
$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
require_once __DIR__ . '/../templates/bns_layout.php';
$hour = (int)date('H');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
?>

<div class="mb-4">
    <h4 class="fw-bold mb-1" style="font-size:1.75rem;color:var(--kn-dark);letter-spacing:-0.02em"><?= $greeting ?>, <?= htmlspecialchars(explode(' ', $userName)[0]) ?>!</h4>
    <p class="text-muted mb-0" style="font-size:.92rem;color:var(--kn-muted)"><?= date('l, F j, Y') ?> &mdash; BNS Staff Dashboard</p>
</div>

<!-- Stats -->
<div class="row g-4 mb-5">
    <div class="col-sm-6 col-xl-6">
        <div class="modern-stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg, rgba(107,122,58,.15) 0%, rgba(107,122,58,.08) 100%);">🏠</div>
            <div>
                <div class="stat-number"><?= number_format($totalFamilies) ?></div>
                <div class="stat-label">Families Encoded</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-6">
        <div class="modern-stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg, rgba(196,114,42,.15) 0%, rgba(196,114,42,.08) 100%);">👶</div>
            <div>
                <div class="stat-number" style="color:var(--kn-orange)"><?= number_format($totalChildren) ?></div>
                <div class="stat-label">Children (0–60+ mos)</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access -->
<p style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:var(--kn-muted);margin-bottom:1rem">Quick Access</p>
<div class="row g-4">
    <div class="col-md-6 col-xl-4">
        <a href="index.php?action=familyProfiles" class="modern-quick-card">
            <div class="quick-icon">👨‍👩‍👧‍👦</div>
            <div class="quick-title">Family Profiles</div>
            <div class="quick-desc">View, add, and manage household profiles</div>
        </a>
    </div>
    <div class="col-md-6 col-xl-4">
        <a href="index.php?action=dataEncoding" class="modern-quick-card">
            <div class="quick-icon">📋</div>
            <div class="quick-title">Resident Assessment</div>
            <div class="quick-desc">OPT Plus — List of Recipients for Nutrition Assessment</div>
        </a>
    </div>
    <div class="col-md-6 col-xl-4">
        <a href="index.php?action=accomplishmentReport" class="modern-quick-card">
            <div class="quick-icon">📊</div>
            <div class="quick-title">Monthly Report</div>
            <div class="quick-desc">Generate and submit monthly accomplishment report</div>
        </a>
    </div>
</div>

<style>
.modern-stat-card {
    background: linear-gradient(135deg, #ffffff 0%, #fefefe 100%);
    border-radius:16px;
    padding:2rem;
    border:none;
    display:flex;
    align-items:center;
    gap:1.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,.08), 0 1px 4px rgba(0,0,0,.04);
    transition:all .25s ease;
    position:relative;
    overflow:hidden;
}
.modern-stat-card::before {
    content:'';
    position:absolute;
    top:0;
    left:0;
    right:0;
    height:3px;
    background: linear-gradient(90deg, var(--kn-green) 0%, var(--kn-orange) 100%);
    opacity:0;
    transition:opacity .25s;
}
.modern-stat-card:hover {
    transform:translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,.12), 0 4px 8px rgba(0,0,0,.06);
}
.modern-stat-card:hover::before {
    opacity:1;
}
.stat-icon {
    width:68px;
    height:68px;
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1.85rem;
    flex-shrink:0;
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
    position: relative;
    z-index: 1;
}
.stat-number {
    font-size:2.35rem;
    font-weight:800;
    color:var(--kn-green);
    line-height:1;
    letter-spacing:-0.03em;
}
.stat-label {
    font-size:.9rem;
    color:var(--kn-muted);
    margin-top:.4rem;
    font-weight:500;
}

.modern-quick-card {
    display:block;
    background: linear-gradient(135deg, #ffffff 0%, #fefefe 100%);
    border:none;
    border-radius:16px;
    padding:2rem;
    text-decoration:none;
    color:var(--kn-dark);
    transition:all .25s ease;
    box-shadow: 0 2px 12px rgba(0,0,0,.08), 0 1px 4px rgba(0,0,0,.04);
    position:relative;
    overflow:hidden;
    height:100%;
}
.modern-quick-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(107,122,58,.04) 0%, transparent 100%);
    opacity: 0;
    transition: opacity .25s;
}
.modern-quick-card::after {
    content:'→';
    position:absolute;
    bottom:1.75rem;
    right:1.75rem;
    font-size:1.5rem;
    font-weight: 600;
    color:var(--kn-green);
    opacity:0;
    transform:translateX(-10px);
    transition:all .25s ease;
}
.modern-quick-card:hover {
    transform:translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,.12), 0 6px 12px rgba(0,0,0,.08);
}
.modern-quick-card:hover::before {
    opacity: 1;
}
.modern-quick-card:hover::after {
    opacity:1;
    transform:translateX(0);
}
.quick-icon {
    font-size:2.25rem;
    margin-bottom:1rem;
    display:inline-block;
}
.quick-title {
    font-weight:700;
    font-size:1.15rem;
    color:var(--kn-green);
    margin-bottom:.4rem;
    letter-spacing:-0.01em;
}
.quick-desc {
    font-size:.9rem;
    color:var(--kn-muted);
    line-height:1.6;
}
</style>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
