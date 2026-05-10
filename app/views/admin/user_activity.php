<?php
// ── User Activity Detail — Admin only ─────────────────────────────────────────
// Shows all system_logs for a specific user + summary stats.
// Accessed via index.php?action=userActivity&user_id=X

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Security.php';

Security::requirePermission('userActivity');

$db       = getDBConnection();
$targetId = (int) ($_GET['user_id'] ?? 0);

if (!$targetId) {
    header('Location: index.php?action=userManagement');
    exit;
}

// Fetch target user info
$userStmt = $db->prepare("
    SELECT u.user_id, u.first_name, u.last_name, u.email, u.is_verified, u.created_at,
           r.role_name,
           a.failed_attempts, a.locked_until
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.role_id
    LEFT JOIN user_auth a ON u.user_id = a.user_id
    WHERE u.user_id = ?
");
$userStmt->execute([$targetId]);
$targetUser = $userStmt->fetch();

if (!$targetUser) {
    $_SESSION['errors'] = ['User not found.'];
    header('Location: index.php?action=userManagement');
    exit;
}

// Summary stats
$stats = $db->prepare("
    SELECT
        COUNT(*) as total_events,
        SUM(action_type = 'LOGIN_SUCCESS') as login_count,
        SUM(action_type = 'LOGIN_FAILED')  as failed_count,
        SUM(action_type = 'LOGOUT')        as logout_count,
        MAX(CASE WHEN action_type = 'LOGIN_SUCCESS' THEN created_at END) as last_login
    FROM system_logs
    WHERE user_id = ?
");
$stats->execute([$targetId]);
$stats = $stats->fetch();

// Activity logs — paginated
$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 20;
$offset  = ($page - 1) * $perPage;

$totalStmt = $db->prepare("SELECT COUNT(*) FROM system_logs WHERE user_id = ?");
$totalStmt->execute([$targetId]);
$total      = (int) $totalStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

$logsStmt = $db->prepare("
    SELECT log_id, action_type, description, ip_address, created_at
    FROM system_logs
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT $perPage OFFSET $offset
");
$logsStmt->execute([$targetId]);
$logs = $logsStmt->fetchAll();

$isLocked = $targetUser['locked_until'] && strtotime($targetUser['locked_until']) > time();

$pageTitle = 'User Activity';
$activeNav = 'users';
require_once __DIR__ . '/../templates/admin_layout.php';

$badgeMap = [
    'LOGIN_SUCCESS'          => 'success',
    'GOOGLE_LOGIN'           => 'success',
    'LOGOUT'                 => 'secondary',
    'LOGIN_FAILED'           => 'warning',
    'LOGIN_LOCKED'           => 'danger',
    'OTP_SENT'               => 'info',
    'OTP_RESENT'             => 'info',
    'OTP_FAILED'             => 'warning',
    'REGISTER'               => 'primary',
    'ROLE_SET'               => 'primary',
    'PASSWORD_RESET'         => 'secondary',
    'PASSWORD_RESET_REQUEST' => 'secondary',
    'ACCOUNT_SETUP_COMPLETE' => 'primary',
    'PASSWORD_FORCE_CHANGED' => 'secondary',
];
?>

<!-- Back button -->
<div class="mb-3">
    <a href="index.php?action=userManagement" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to User Management
    </a>
</div>

<!-- User info card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="avatar" style="width:52px;height:52px;border-radius:50%;background:var(--kn-green);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.4rem">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <div class="fw-bold fs-6 dlp-no-select">
                    <?= htmlspecialchars($targetUser['first_name'] . ' ' . $targetUser['last_name']) ?>
                    <?php if ($isLocked): ?>
                        <span class="badge bg-danger ms-2">🔒 Locked</span>
                    <?php else: ?>
                        <span class="badge bg-success ms-2">Active</span>
                    <?php endif; ?>
                </div>
                <div class="text-muted small dlp-no-select"><?= htmlspecialchars($targetUser['email']) ?></div>
                <div class="mt-1">
                    <span class="badge" style="background:var(--kn-green)"><?= htmlspecialchars($targetUser['role_name'] ?? '—') ?></span>
                    <?php if ($targetUser['is_verified']): ?>
                        <span class="badge bg-success ms-1">✓ Verified</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark ms-1">Unverified</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="ms-auto text-muted small">
                Registered: <?= date('M d, Y', strtotime($targetUser['created_at'])) ?>
            </div>
        </div>
    </div>
</div>

<!-- Summary stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold fs-4" style="color:var(--kn-green)"><?= (int)$stats['login_count'] ?></div>
            <div class="text-muted small">Successful Logins</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold fs-4 text-danger"><?= (int)$stats['failed_count'] ?></div>
            <div class="text-muted small">Failed Attempts</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold fs-4 text-secondary"><?= (int)$stats['logout_count'] ?></div>
            <div class="text-muted small">Logouts</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold" style="font-size:.9rem;color:var(--kn-dark)">
                <?= $stats['last_login'] ? date('M d, Y H:i', strtotime($stats['last_login'])) : '—' ?>
            </div>
            <div class="text-muted small">Last Login</div>
        </div>
    </div>
</div>

<!-- Activity log table -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="mb-0 fw-bold" style="color:var(--kn-dark)">
        <i class="bi bi-clock-history me-1"></i> Activity History
        <span class="text-muted fw-normal small">(<?= number_format($total) ?> events)</span>
    </h6>
    <?= Security::classificationBadge('RESTRICTED') ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0 align-middle">
            <thead style="background:var(--kn-green);color:var(--kn-cream)">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                    <th>Date &amp; Time</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$logs): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No activity found.</td></tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td class="ps-3 text-muted small"><?= $log['log_id'] ?></td>
                    <td>
                        <?php $badge = $badgeMap[$log['action_type']] ?? 'secondary'; ?>
                        <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($log['action_type']) ?></span>
                    </td>
                    <td class="small"><?= htmlspecialchars($log['description']) ?></td>
                    <td class="small text-muted"><?= htmlspecialchars($log['ip_address'] ?? '—') ?></td>
                    <td class="small text-muted text-nowrap"><?= date('M d, Y H:i:s', strtotime($log['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<nav class="mt-3">
    <ul class="pagination pagination-sm justify-content-center mb-0">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="index.php?action=userActivity&user_id=<?= $targetId ?>&page=<?= $i ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
