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

// Get shopping cart count
$_cartStmt = getDBConnection()->prepare("SELECT COUNT(*) FROM shopping_cart WHERE user_id = ?");
$_cartStmt->execute([$_SESSION['user_id']]);
$_cartCount = (int)$_cartStmt->fetchColumn();

// Get active grocery list count (items not yet purchased)
$_groceryListStmt = getDBConnection()->prepare("
    SELECT COUNT(DISTINCT gli.item_id) 
    FROM grocery_list_items gli
    JOIN grocery_lists gl ON gli.grocery_list_id = gl.grocery_list_id
    WHERE gl.user_id = ? AND gl.status = 'Active' AND gli.is_purchased = 0
");
$_groceryListStmt->execute([$_SESSION['user_id']]);
$_groceryListCount = (int)$_groceryListStmt->fetchColumn();

// Debug: Log the count
error_log("Mother Layout - Grocery List Count for user " . $_SESSION['user_id'] . ": " . $_groceryListCount);

$nav = [
    ['key'=>'home',           'label'=>'Home',            'icon'=>'bi-house-fill',      'url'=>'index.php?action=home'],
    ['key'=>'family_profile', 'label'=>'Family Profile',  'icon'=>'bi-people-fill',     'url'=>'index.php?action=motherWizard'],
    ['key'=>'feeding_program', 'label'=>'Feeding Program', 'icon'=>'bi-egg-fried',       'url'=>'index.php?action=feedingDashboard'],
    ['key'=>'nutrition_education', 'label'=>'Nutrition Education', 'icon'=>'bi-book-fill', 'url'=>'index.php?action=upcomingSessions'],
    ['key'=>'meal_plans',     'label'=>'Meal Plans',      'icon'=>'bi-journal-richtext', 'url'=>'index.php?action=mealPlansList'],
    ['key'=>'pantry',         'label'=>'Pantry',          'icon'=>'bi-box-seam',        'url'=>'index.php?action=pantry'],
    ['key'=>'grocery',        'label'=>'Grocery',         'icon'=>'bi-cart-fill',       'url'=>'index.php?action=groceryMode'],
    ['key'=>'notifications',  'label'=>'Notifications',   'icon'=>'bi-bell-fill',       'url'=>'index.php?action=notifications', 'badge'=>$unreadCount],
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
    <style>
        /* Mother Layout - Orange Theme (#C4722A) */
        
        /* Enhanced Body Background */
        body {
            background: linear-gradient(135deg, #fde8d8 0%, #fef5e7 100%) !important;
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif !important;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Smooth Scrollbar */
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: rgba(196,114,42,.05); }
        ::-webkit-scrollbar-thumb { 
            background: rgba(196,114,42,.3); 
            border-radius: 5px;
            border: 2px solid transparent;
            background-clip: padding-box;
        }
        ::-webkit-scrollbar-thumb:hover { 
            background: rgba(196,114,42,.5); 
            background-clip: padding-box; 
        }
        
        /* Sidebar - Orange Gradient */
        .kn-sidebar {
            background: linear-gradient(180deg, #C4722A 0%, #A85E22 50%, #8C4A1A 100%) !important;
            box-shadow: 4px 0 32px rgba(0,0,0,.25), 8px 0 16px rgba(0,0,0,.15) !important;
        }
        
        /* Topbar - Orange */
        .kn-topbar {
            background: linear-gradient(135deg, #C4722A 0%, #A85E22 100%) !important;
            box-shadow: 0 4px 24px rgba(0,0,0,.2), 0 2px 8px rgba(0,0,0,.15) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        
        .kn-topbar .page-title {
            letter-spacing: -0.02em;
            text-shadow: 0 2px 4px rgba(0,0,0,.2);
        }
        
        .kn-topbar .user-chip {
            background: rgba(255,255,255,.12) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.15) !important;
            box-shadow: 0 2px 8px rgba(0,0,0,.12);
            transition: all .2s ease;
        }
        
        .kn-topbar .user-chip:hover {
            background: rgba(255,255,255,.18) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,.15);
        }
        
        .kn-topbar .user-chip i {
            color: #F5EDD6 !important;
        }
        
        .btn-signout {
            background: rgba(255,255,255,.1) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.15) !important;
            box-shadow: 0 2px 6px rgba(0,0,0,.1);
            font-weight: 500;
            transition: all .2s ease;
        }
        
        .btn-signout:hover {
            background: rgba(248,113,113,.15) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(248,113,113,.2);
            border-color: rgba(248,113,113,.25) !important;
        }
        
        /* Topbar Icon Button (Grocery List) */
        .topbar-icon-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255,255,255,.12);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
            text-decoration: none;
            transition: all .2s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,.1);
            position: relative;
        }
        
        .topbar-icon-btn:hover {
            background: rgba(255,255,255,.18);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0,0,0,.15);
            color: #fff;
        }
        
        .topbar-icon-btn .icon-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
            min-width: 20px;
            height: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
            font-weight: 700;
            padding: 0 5px;
            border: 2px solid #C4722A;
            box-shadow: 0 2px 6px rgba(239,68,68,.4);
            animation: pulse-badge 2s infinite;
        }
        
        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        /* Sidebar Brand */
        .sidebar-brand {
            position: relative;
        }
        
        .sidebar-brand::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1.25rem;
            right: 1.25rem;
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, rgba(245,237,214,.4) 50%, transparent 100%);
        }
        
        .sidebar-brand .brand-text {
            letter-spacing: -0.02em;
            text-shadow: 0 2px 8px rgba(0,0,0,.3);
        }
        
        .sidebar-brand .brand-text em {
            color: #F5EDD6 !important;
            background: linear-gradient(135deg, #F5EDD6 0%, #E8DCC0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .sidebar-brand .brand-badge {
            background: linear-gradient(135deg, #F5EDD6 0%, #E8DCC0 100%) !important;
            color: #8C4A1A !important;
            box-shadow: 0 2px 6px rgba(245,237,214,.3);
            text-shadow: none;
            font-weight: 800;
        }
        
        /* Sidebar Nav */
        .sidebar-nav::-webkit-scrollbar { width: 6px; }
        .sidebar-nav::-webkit-scrollbar-track { background: rgba(255,255,255,.05); }
        .sidebar-nav::-webkit-scrollbar-thumb { 
            background: rgba(255,255,255,.2); 
            border-radius: 3px; 
        }
        .sidebar-nav::-webkit-scrollbar-thumb:hover { 
            background: rgba(255,255,255,.3); 
        }
        
        /* Nav Items - Modern Effects */
        .nav-item {
            margin: 0 .75rem .35rem !important;
            border-radius: 12px !important;
            border-left: none !important;
            position: relative;
            overflow: hidden;
            transition: all .2s ease !important;
        }
        
        .nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 0;
            background: linear-gradient(180deg, #F5EDD6 0%, #E8DCC0 100%);
            border-radius: 0 3px 3px 0;
            transition: height .2s ease;
            box-shadow: 0 0 8px rgba(245,237,214,.4);
        }
        
        .nav-item::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(245,237,214,.12) 0%, rgba(245,237,214,.04) 100%);
            opacity: 0;
            transition: opacity .2s;
        }
        
        .nav-item:hover {
            background: rgba(255,255,255,.1) !important;
            transform: translateX(2px);
        }
        
        .nav-item:hover::before {
            height: 50%;
        }
        
        .nav-item:hover::after {
            opacity: 1;
        }
        
        .nav-item.active {
            background: linear-gradient(135deg, rgba(245,237,214,.18) 0%, rgba(245,237,214,.1) 100%) !important;
            box-shadow: 0 2px 8px rgba(0,0,0,.15);
            border: 1px solid rgba(245,237,214,.2) !important;
        }
        
        .nav-item.active::before {
            height: 60%;
            box-shadow: 0 0 12px rgba(245,237,214,.6);
        }
        
        .nav-item i {
            position: relative;
            z-index: 1;
        }
        
        .nav-item .nav-badge {
            background: linear-gradient(135deg, #F5EDD6 0%, #E8DCC0 100%) !important;
            color: #8C4A1A !important;
            box-shadow: 0 2px 6px rgba(245,237,214,.3);
            text-shadow: none;
            font-weight: 800;
            position: relative;
            z-index: 1;
        }
        
        /* Sidebar Footer */
        .sidebar-footer {
            background: linear-gradient(180deg, rgba(0,0,0,.15) 0%, rgba(0,0,0,.1) 100%);
            position: relative;
        }
        
        .sidebar-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 1.25rem;
            right: 1.25rem;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(245,237,214,.3) 50%, transparent 100%);
        }
        
        /* Sidebar User Avatar */
        .sidebar-user .avatar {
            background: linear-gradient(135deg, rgba(245,237,214,.25) 0%, rgba(245,237,214,.15) 100%) !important;
            border: 2px solid rgba(245,237,214,.4) !important;
            color: #F5EDD6 !important;
            box-shadow: 0 4px 12px rgba(0,0,0,.2), inset 0 1px 0 rgba(255,255,255,.1);
            position: relative;
        }
        
        .sidebar-user .avatar::after {
            content: '';
            position: absolute;
            top: -2px;
            right: -2px;
            width: 9px;
            height: 9px;
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            border: 2px solid #8C4A1A;
            border-radius: 50%;
            box-shadow: 0 0 8px rgba(74,222,128,.6);
        }
        
        .sidebar-user .name {
            letter-spacing: -0.01em;
            text-shadow: 0 1px 2px rgba(0,0,0,.2);
        }
        
        .sidebar-user .signout-icon {
            padding: .3rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .2s ease;
        }
        
        .sidebar-user .signout-icon:hover {
            background: rgba(248,113,113,.15);
            transform: scale(1.05);
        }
        
        /* Main Content Animation */
        .kn-content {
            animation: fadeInUp 0.5s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Focus States */
        button:focus-visible, a:focus-visible, input:focus-visible, select:focus-visible {
            outline: 3px solid rgba(196,114,42,.4);
            outline-offset: 2px;
        }
        
        /* Mobile Toggle */
        .sidebar-toggle {
            background: #A85E22 !important;
        }
    </style>
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
    <div class="d-flex align-items-center gap-3">
        <div class="user-chip d-none d-md-flex"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($userName) ?></div>
        <a href="index.php?action=logout" class="btn-signout"><i class="bi bi-box-arrow-right me-1"></i> Sign Out</a>
    </div>
</div>
<main class="kn-main"><div class="kn-content">
<?php 
// Flash Success Message
if (isset($_SESSION['flash_success'])): ?>
<div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_success']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<?php 
// Flash Error Message
if (isset($_SESSION['flash_error'])): ?>
<div class="alert alert-danger alert-dismissible fade show mb-3">
    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>

<?php 
// Flash Warning Message
if (isset($_SESSION['flash_warning'])): ?>
<div class="alert alert-warning alert-dismissible fade show mb-3">
    <i class="bi bi-exclamation-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_warning']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash_warning']); endif; ?>

<?php 
// Flash Info Message
if (isset($_SESSION['flash_info'])): ?>
<div class="alert alert-info alert-dismissible fade show mb-3">
    <i class="bi bi-info-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_info']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash_info']); endif; ?>

<?php 
// Legacy flash support
if ($flash): ?>
<div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($flash) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
