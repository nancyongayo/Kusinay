<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Mother') {
    header('Location: index.php?action=login'); exit;
}
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../models/NotificationModel.php';
require_once __DIR__ . '/../../../core/Security.php';

$userName   = $_SESSION['user_name'] ?? 'User';
$pageTitle  = $pageTitle ?? 'Home';
$activeNav  = $activeNav ?? 'home';
$flash      = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$_notifModel  = new NotificationModel(getDBConnection());
$unreadCount  = $_notifModel->getUnreadCount($_SESSION['user_id']);

// Determine gender-based labels
$_genderStmt = getDBConnection()->prepare("SELECT gender FROM users WHERE user_id = ?");
$_genderStmt->execute([$_SESSION['user_id']]);
$_userGender = $_genderStmt->fetchColumn() ?: 'Female';
$_isMale     = strtolower($_userGender) === 'male';
$_badgeLabel = $_isMale ? 'DAD' : 'MOM';
$_roleLabel  = $_isMale ? 'Father' : 'Mother';
$_badgeColor = $_isMale ? '#2563eb' : '#C4722A';

$nav = [
    ['key'=>'home',           'label'=>'Home',            'icon'=>'bi-house-fill',      'url'=>'index.php?action=home'],
    ['key'=>'family_profile', 'label'=>'Family Profile',  'icon'=>'bi-people-fill',     'url'=>'index.php?action=motherWizard'],
    ['key'=>'nutrition_education', 'label'=>'Nutrition Education', 'icon'=>'bi-book-fill', 'url'=>'index.php?action=upcomingSessions'],
    ['key'=>'notifications',  'label'=>'Notifications',   'icon'=>'bi-bell-fill',       'url'=>'index.php?action=notifications', 'badge'=>$unreadCount],
    ['key'=>'meal_plans',     'label'=>'Meal Plans',      'icon'=>'bi-journal-richtext', 'url'=>'#', 'disabled'=>true],
    ['key'=>'records',        'label'=>'My Records',      'icon'=>'bi-clipboard2-pulse', 'url'=>'#', 'disabled'=>true],
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
        <a href="index.php?action=home" class="brand-text">🍲 Kusi<em>Nay</em></a>
        <span class="brand-badge" style="background:<?= $_badgeColor ?>"><?= $_badgeLabel ?></span>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Menu</div>
        <?php foreach ($nav as $item): ?>
        <a href="<?= $item['url'] ?>" class="nav-item <?= $activeNav === $item['key'] ? 'active' : '' ?> <?= !empty($item['disabled']) ? 'disabled' : '' ?>">
            <i class="bi <?= $item['icon'] ?>"></i> <?= $item['label'] ?>
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
                <div class="role"><?= $_roleLabel ?></div>
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
