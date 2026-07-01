<?php
/**
 * Barangay Captain Layout
 * Usage: include at top of any Barangay Captain view AFTER setting $pageTitle and $activeNav
 */
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Barangay Captain') {
    header('Location: index.php?action=login'); exit;
}
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../models/NotificationModel.php';
require_once __DIR__ . '/../../../core/Security.php';

$userName    = $_SESSION['user_name'] ?? 'Barangay Captain';
$pageTitle   = $pageTitle ?? 'Dashboard';
$activeNav   = $activeNav ?? 'dashboard';
$flash       = $_SESSION['flash'] ?? null;
$formErrors  = $_SESSION['errors'] ?? [];
unset($_SESSION['flash'], $_SESSION['errors']);

$_notifModel = new NotificationModel(getDBConnection());
$unreadCount = $_notifModel->getUnreadCount($_SESSION['user_id']);

$nav = [
    ['key'=>'dashboard',       'label'=>'Dashboard',         'icon'=>'bi-speedometer2',    'url'=>'index.php?action=captainDashboard'],
    ['key'=>'notifications',   'label'=>'Notifications',     'icon'=>'bi-bell-fill',       'url'=>'index.php?action=notifications', 'badge'=>$unreadCount],
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

<!-- Mobile toggle -->
<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
    <i class="bi bi-list"></i>
</button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ── Sidebar ── -->
<aside class="kn-sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="index.php?action=captainDashboard" class="brand-text">🍲 Kusi<em>Nay</em></a>
        <span class="brand-badge">Captain</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main Menu</div>
        <?php foreach ($nav as $item): ?>
        <a href="<?= $item['url'] ?>"
           class="nav-item <?= $activeNav === $item['key'] ? 'active' : '' ?>">
            <i class="bi <?= $item['icon'] ?>"></i>
            <?= $item['label'] ?>
            <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
                <span class="nav-badge bg-danger"><?= (int)$item['badge'] ?></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar"><i class="bi bi-person-fill"></i></div>
            <div class="info">
                <div class="name"><?= htmlspecialchars($userName) ?></div>
                <div class="role">Barangay Captain</div>
            </div>
            <a href="index.php?action=logout" class="signout-icon" title="Sign Out">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</aside>

<!-- ── Topbar ── -->
<div class="kn-topbar">
    <span class="page-title"><?= htmlspecialchars($pageTitle) ?></span>
    <div class="user-chip d-none d-md-flex">
        <i class="bi bi-person-circle"></i>
        <?= htmlspecialchars($userName) ?>
    </div>
    <a href="index.php?action=logout" class="btn-signout">
        <i class="bi bi-box-arrow-right me-1"></i> Sign Out
    </a>
</div>

<!-- ── Main ── -->
<main class="kn-main">
<div class="kn-content">

<?php if ($flash): ?>
<div class="kn-flash">
    <div class="alert alert-success alert-dismissible fade show mb-0">
        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($flash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

<?php if ($formErrors): ?>
<div class="kn-flash">
    <div class="alert alert-danger alert-dismissible fade show mb-0">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?php foreach ($formErrors as $e): ?><?= htmlspecialchars($e) ?><br><?php endforeach; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>
