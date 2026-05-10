<?php
// Shared dashboard layout helper — include at top of each dashboard view
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}
$userName = $_SESSION['user_name'] ?? 'User';
$role     = $_SESSION['role']      ?? '';
$flash    = $_SESSION['flash']     ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KusiNay – <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --kn-green  : #6B7A3A;
            --kn-green-d: #556030;
            --kn-orange : #C4722A;
            --kn-cream  : #F5EDD6;
            --kn-dark   : #3D4A1E;
        }
        body { background: #fdf0e8; font-family: 'Segoe UI', system-ui, sans-serif; }
        .navbar-kn { background: var(--kn-green); }
        .navbar-kn .navbar-brand, .navbar-kn .nav-link, .navbar-kn .navbar-text {
            color: var(--kn-cream) !important;
        }
        .navbar-kn .nav-link:hover { color: #fff !important; }
        .navbar-kn .brand-em { color: var(--kn-orange); }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-kn px-3">
    <a class="navbar-brand fw-bold" href="index.php?action=home">🍲 Kusi<span class="brand-em">Nay</span></a>
    <div class="ms-auto d-flex align-items-center gap-3">
        <span class="navbar-text small"><?= htmlspecialchars($userName) ?> &mdash; <?= htmlspecialchars($role) ?></span>
        <a href="index.php?action=logout" class="btn btn-sm btn-outline-light">Logout</a>
    </div>
</nav>
<div class="container py-4">
<?php if ($flash): ?>
    <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>
