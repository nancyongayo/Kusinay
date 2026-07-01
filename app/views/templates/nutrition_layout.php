<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Nutrition Officer II') {
    header('Location: index.php?action=login'); exit;
}
require_once __DIR__ . '/../../../core/Security.php';
$userName  = $_SESSION['user_name'] ?? 'User';
$pageTitle = $pageTitle ?? 'Dashboard';
$activeNav = $activeNav ?? 'validation';
$flash       = $_SESSION['flash'] ?? null;
$flashError  = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash'], $_SESSION['flash_error']);

$nav = [
    ['key'=>'validation', 'label'=>'Report Validation',    'icon'=>'bi-patch-check-fill', 'url'=>'index.php?action=reportValidation'],
    ['key'=>'recovery',   'label'=>'Recovery Validation',  'icon'=>'bi-heart-pulse-fill', 'url'=>'index.php?action=recoveryValidation'],
    ['key'=>'reports',    'label'=>'Reports',               'icon'=>'bi-bar-chart-line',   'url'=>'#', 'disabled'=>true],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KusiNay – <?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <?php include __DIR__ . '/layout_styles.php'; ?>
    <?= \Security::dlpHeadAssets() ?>
</head>
<body>
<?= \Security::dlpBodyScript() ?>
<button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="kn-sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="index.php?action=reportValidation" class="brand-text">🍲 Kusi<em>Nay</em></a>
        <span class="brand-badge" style="background:#2563eb">NO II</span>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Menu</div>
        <?php foreach ($nav as $item): ?>
        <a href="<?= $item['url'] ?>" class="nav-item <?= $activeNav === $item['key'] ? 'active' : '' ?> <?= !empty($item['disabled']) ? 'disabled' : '' ?>">
            <i class="bi <?= $item['icon'] ?>"></i> <?= $item['label'] ?>
            <?php if (!empty($item['disabled'])): ?><span class="nav-badge">Soon</span><?php endif; ?>
        </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar"><i class="bi bi-person-fill"></i></div>
            <div class="info">
                <div class="name"><?= htmlspecialchars($userName) ?></div>
                <div class="role">Nutrition Officer II</div>
            </div>
            <a href="index.php?action=logout" class="signout-icon" title="Sign Out"><i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>
</aside>
<div class="kn-topbar">
    <span class="page-title"><?= htmlspecialchars($pageTitle) ?></span>
    <div class="user-chip d-none d-md-flex"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($userName) ?></div>
    <a href="index.php?action=logout" class="btn-signout"><i class="bi bi-box-arrow-right me-1"></i> Sign Out</a>
</div>
<main class="kn-main"><div class="kn-content">
<?php if ($flash): ?>
<div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($flash) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($flashError): ?>
<div class="alert alert-danger alert-dismissible fade show mb-3">
    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($flashError) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
