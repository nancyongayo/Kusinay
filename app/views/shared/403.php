<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KusiNay – Access Denied</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#fde8d8; display:flex; align-items:center; justify-content:center; min-height:100vh; font-family:'Segoe UI',system-ui,sans-serif; }
        .card-403 { background:#fff; border-radius:1rem; padding:3rem 2.5rem; text-align:center; max-width:420px; box-shadow:0 4px 24px rgba(0,0,0,.1); }
        .icon-403 { font-size:4rem; margin-bottom:1rem; }
        h1 { font-size:1.5rem; font-weight:800; color:#3D4A1E; }
        p  { color:#6c757d; font-size:.95rem; }
    </style>
</head>
<body>
<div class="card-403">
    <div class="icon-403">🚫</div>
    <h1>Access Denied</h1>
    <p>You don't have permission to access this page.<br>
       Your role (<strong><?= htmlspecialchars($_SESSION['role'] ?? 'Unknown') ?></strong>) is not authorized for this action.</p>
    <a href="javascript:history.back()" class="btn btn-outline-secondary me-2">← Go Back</a>
    <a href="index.php?action=logout" class="btn btn-danger">Sign Out</a>
</div>
</body>
</html>
