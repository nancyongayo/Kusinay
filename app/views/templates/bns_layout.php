<?php
/**
 * BNS Sidebar Layout
 * Usage: include at top of any BNS view AFTER setting $pageTitle and $activeNav
 * $activeNav values: 'dashboard', 'family_profiles', 'data_encoding', 'reports'
 */
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BNS Staff') {
    header('Location: index.php?action=login'); exit;
}
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../models/NotificationModel.php';
require_once __DIR__ . '/../../../core/Security.php';

$userName    = $_SESSION['user_name'] ?? 'BNS Staff';
$pageTitle   = $pageTitle ?? 'Dashboard';
$activeNav   = $activeNav ?? 'dashboard';
$flash       = $_SESSION['flash'] ?? null;
$flashError  = $_SESSION['flash_error'] ?? null;
$formErrors  = $_SESSION['errors'] ?? [];
unset($_SESSION['flash'], $_SESSION['flash_error'], $_SESSION['errors']);

$_notifModel = new NotificationModel(getDBConnection());
$unreadCount = $_notifModel->getUnreadCount($_SESSION['user_id']);

$nav = [
    ['key'=>'dashboard',       'label'=>'Dashboard',         'icon'=>'bi-speedometer2',    'url'=>'index.php?action=bnsDashboard'],
    ['key'=>'family_profiles', 'label'=>'Family Profiles',   'icon'=>'bi-people-fill',     'url'=>'index.php?action=familyProfiles'],
    ['key'=>'register_resident','label'=>'Register Resident','icon'=>'bi-person-plus-fill', 'url'=>'index.php?action=registerResident'],
    ['key'=>'validation',      'label'=>'Profile Validation','icon'=>'bi-patch-check-fill', 'url'=>'index.php?action=bnsValidationList'],
    ['key'=>'data_encoding',   'label'=>'Resident Assessment',  'icon'=>'bi-clipboard2-data', 'url'=>'index.php?action=dataEncoding'],
    ['key'=>'nutrition_education', 'label'=>'Nutrition Education', 'icon'=>'bi-book-fill', 'url'=>'index.php?action=nutritionEducationList'],
    ['key'=>'feeding_program', 'label'=>'Feeding Programs',  'icon'=>'bi-heart-pulse-fill', 'url'=>'index.php?action=feedingProgramList'],
    ['key'=>'meal_plans',      'label'=>'Meal Plans',        'icon'=>'bi-journal-richtext', 'url'=>'index.php?action=bnsMealPlansList'],
    ['key'=>'accomplishment',  'label'=>'Monthly Report',     'icon'=>'bi-file-earmark-text','url'=>'index.php?action=accomplishmentReport'],
    ['key'=>'notifications',   'label'=>'Notifications',     'icon'=>'bi-bell-fill',       'url'=>'index.php?action=notifications', 'badge'=>$unreadCount],
    ['key'=>'settings',        'label'=>'Profile Settings',  'icon'=>'bi-gear-fill',       'url'=>'index.php?action=bnsSettings'],
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
        <a href="index.php?action=bnsDashboard" class="brand-text">🍲 Kusi<em>Nay</em></a>
        <span class="brand-badge">BNS</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main Menu</div>
        <?php foreach ($nav as $item): ?>
        <a href="<?= $item['url'] ?>"
           class="nav-item <?= $activeNav === $item['key'] ? 'active' : '' ?> <?= !empty($item['disabled']) ? 'disabled' : '' ?>">
            <i class="bi <?= $item['icon'] ?>"></i>
            <?= $item['label'] ?>
            <?php if (!empty($item['disabled'])): ?>
                <span class="nav-badge">Soon</span>
            <?php elseif (!empty($item['badge']) && $item['badge'] > 0): ?>
                <span class="nav-badge bg-danger" id="notif-badge"><?= (int)$item['badge'] ?></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar"><i class="bi bi-person-fill"></i></div>
            <div class="info">
                <div class="name"><?= htmlspecialchars($userName) ?></div>
                <div class="role">BNS Staff</div>
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

<?php if ($flashError): ?>
<div class="kn-flash">
    <div class="alert alert-danger alert-dismissible fade show mb-0">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($flashError) ?>
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
