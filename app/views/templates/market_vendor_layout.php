<?php
/**
 * Market Vendor Layout
 * Usage: include at top of any Market Vendor view AFTER setting $pageTitle and $activeNav
 */
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Market Vendor') {
    header('Location: index.php?action=login'); exit;
}
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../models/NotificationModel.php';
require_once __DIR__ . '/../../../core/Security.php';

$userName    = $_SESSION['user_name'] ?? 'Market Vendor';
$pageTitle   = $pageTitle ?? 'Dashboard';
$activeNav   = $activeNav ?? 'dashboard';
$flash       = $_SESSION['flash'] ?? null;
$formErrors  = $_SESSION['errors'] ?? [];
unset($_SESSION['flash'], $_SESSION['errors']);

$_notifModel = new NotificationModel(getDBConnection());
$unreadCount = $_notifModel->getUnreadCount($_SESSION['user_id']);

$nav = [
    ['key'=>'dashboard',       'label'=>'Dashboard',         'icon'=>'bi-speedometer2',    'url'=>'index.php?action=marketVendorDashboard'],
    ['key'=>'products',        'label'=>'My Products',       'icon'=>'bi-basket-fill',     'url'=>'index.php?action=vendorProducts'],
    ['key'=>'orders',          'label'=>'Orders',            'icon'=>'bi-cart-check-fill', 'url'=>'index.php?action=vendorOrders'],
    ['key'=>'sales',           'label'=>'Sales Reports',     'icon'=>'bi-graph-up',        'url'=>'index.php?action=vendorSalesReports'],
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
    <style>
        /* Market Vendor Muted Amber/Honey Theme */
        :root {
            --mv-orange-primary: #D4A574;
            --mv-orange-dark: #C09563;
            --mv-orange-darker: #AC8552;
            --mv-orange-light: #E8C9A8;
            --mv-orange-cream: #FAF6F0;
        }
        
        /* Sidebar - Muted amber gradient */
        .kn-sidebar {
            background: linear-gradient(180deg, #D4A574 0%, #C09563 50%, #AC8552 100%) !important;
        }
        
        /* Topbar - Soft amber */
        .kn-topbar {
            background: linear-gradient(135deg, #D4A574 0%, #C09563 100%) !important;
        }
        
        /* Nav item active state - Soft honey */
        .nav-item.active {
            background: linear-gradient(135deg, rgba(232,201,168,.2) 0%, rgba(232,201,168,.1) 100%) !important;
            border-color: rgba(232,201,168,.25) !important;
        }
        
        .nav-item.active::before {
            background: linear-gradient(180deg, #E8C9A8 0%, #D4A574 100%) !important;
            box-shadow: 0 0 12px rgba(212,165,116,.4) !important;
        }
        
        .nav-item:hover::before {
            background: linear-gradient(180deg, #E8C9A8 0%, #D4A574 100%) !important;
            box-shadow: 0 0 8px rgba(212,165,116,.3) !important;
        }
        
        .nav-item:hover::after {
            background: linear-gradient(135deg, rgba(212,165,116,.1) 0%, rgba(212,165,116,.03) 100%) !important;
        }
        
        /* Badge - Muted gold */
        .nav-badge {
            background: linear-gradient(135deg, #E8C9A8 0%, #D4A574 100%) !important;
            box-shadow: 0 2px 6px rgba(232,201,168,.25) !important;
        }
        
        .brand-badge {
            background: linear-gradient(135deg, #E8C9A8 0%, #D4A574 100%) !important;
            box-shadow: 0 2px 6px rgba(232,201,168,.25) !important;
        }
        
        /* Buttons - Muted amber theme */
        .btn-primary {
            background: linear-gradient(135deg, #D4A574 0%, #C09563 100%) !important;
            border-color: #C09563 !important;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #C09563 0%, #AC8552 100%) !important;
            border-color: #AC8552 !important;
        }
        
        /* Cards with soft amber accent */
        .card {
            border-left: 4px solid #D4A574 !important;
        }
        
        /* Links and accents */
        a:not(.btn):not(.nav-item) {
            color: #C09563;
        }
        
        a:not(.btn):not(.nav-item):hover {
            color: #AC8552;
        }
        
        /* Background - Soft cream */
        body {
            background: linear-gradient(135deg, #FAF6F0 0%, #F5EFE5 100%) !important;
        }
        
        /* Fix sidebar text visibility */
        .sidebar-brand .brand-text {
            color: #fff !important;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .sidebar-brand .brand-text em {
            color: #fff !important;
        }
        
        .sidebar-nav .nav-item {
            color: #fff !important;
        }
        
        .sidebar-nav .nav-item i {
            color: #fff !important;
        }
        
        .sidebar-footer .sidebar-user .name,
        .sidebar-footer .sidebar-user .role {
            color: #fff !important;
        }
        
        /* Mobile Toggle - Market Vendor Theme */
        .sidebar-toggle {
            background: #C09563 !important;
            z-index: 350 !important;
        }
        .sidebar-toggle:hover {
            background: #D4A574 !important;
        }
        
        /* Mobile Navigation Improvements */
        @media (max-width: 768px) {
            .kn-topbar {
                z-index: 300;
            }
            .kn-sidebar {
                z-index: 320;
            }
            .sidebar-overlay {
                z-index: 310;
            }
            .nav-item {
                position: relative;
                z-index: 1;
                pointer-events: all;
                touch-action: manipulation;
                -webkit-tap-highlight-color: rgba(232,201,168,.2);
            }
        }
    </style>
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
        <a href="index.php?action=marketVendorDashboard" class="brand-text">🍲 Kusi<em>Nay</em></a>
        <span class="brand-badge">Market Vendor</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main Menu</div>
        <?php foreach ($nav as $item): ?>
        <a href="<?= $item['url'] ?>"
           class="nav-item <?= $activeNav === $item['key'] ? 'active' : '' ?>">
            <i class="bi <?= $item['icon'] ?>"></i>
            <?= $item['label'] ?>
            <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
                <span class="nav-badge"><?= (int)$item['badge'] ?></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar"><i class="bi bi-person-fill"></i></div>
            <div class="info">
                <div class="name"><?= htmlspecialchars($userName) ?></div>
                <div class="role">Market Vendor</div>
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
