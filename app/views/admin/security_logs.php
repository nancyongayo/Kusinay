<?php
$pageTitle = 'Security Logs';
$activeNav = 'security_logs';
require_once __DIR__ . '/../templates/admin_layout.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Security.php';

$db = getDBConnection();

// Filters
$filterAction = $_GET['action_type'] ?? '';
$filterUser   = trim($_GET['search']      ?? '');
$page         = max(1, (int) ($_GET['page'] ?? 1));
$perPage      = 20;
$offset       = ($page - 1) * $perPage;

// Build query
$where  = [];
$params = [];
if ($filterAction) { $where[] = 'sl.action_type = ?'; $params[] = $filterAction; }
if ($filterUser)   { $where[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
                     $params[] = "%$filterUser%"; $params[] = "%$filterUser%"; $params[] = "%$filterUser%"; }
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$totalStmt = $db->prepare("SELECT COUNT(*) FROM system_logs sl LEFT JOIN users u ON sl.user_id = u.user_id $whereSQL");
$totalStmt->execute($params);
$total = (int) $totalStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

$stmt = $db->prepare("
    SELECT sl.log_id, sl.action_type, sl.description, sl.ip_address, sl.created_at,
           u.first_name, u.last_name, u.email
    FROM system_logs sl
    LEFT JOIN users u ON sl.user_id = u.user_id
    $whereSQL
    ORDER BY sl.created_at DESC
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$logs = $stmt->fetchAll();

// Distinct action types for filter dropdown
$actionTypes = $db->query("SELECT DISTINCT action_type FROM system_logs ORDER BY action_type")->fetchAll(PDO::FETCH_COLUMN);

// Badge color map
$badgeMap = [
    'LOGIN_SUCCESS'   => 'success',
    'GOOGLE_LOGIN'    => 'success',
    'LOGOUT'          => 'secondary',
    'LOGIN_FAILED'    => 'warning',
    'LOGIN_LOCKED'    => 'danger',
    'OTP_SENT'        => 'info',
    'OTP_RESENT'      => 'info',
    'GOOGLE_OTP_SENT' => 'info',
    'OTP_FAILED'      => 'warning',
    'REGISTER'        => 'primary',
    'ROLE_SET'        => 'primary',
    'PASSWORD_RESET'  => 'secondary',
    'PASSWORD_RESET_REQUEST' => 'secondary',
];
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0" style="color:var(--kn-dark)">🔐 Security Logs</h4>
    <div class="d-flex align-items-center gap-2">
        <?= Security::classificationBadge('RESTRICTED') ?>
        <span class="text-muted small"><?= number_format($total) ?> total records</span>
    </div>
</div>

<!-- Filters -->
<form method="GET" action="index.php" class="row g-2 mb-3">
    <input type="hidden" name="action" value="securityLogs">
    <div class="col-12 col-md-5">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Search by name or email…" value="<?= htmlspecialchars($filterUser) ?>">
    </div>
    <div class="col-12 col-md-4">
        <select name="action_type" class="form-select form-select-sm">
            <option value="">All action types</option>
            <?php foreach ($actionTypes as $at): ?>
                <option value="<?= htmlspecialchars($at) ?>" <?= $filterAction === $at ? 'selected' : '' ?>>
                    <?= htmlspecialchars($at) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        <a href="index.php?action=securityLogs" class="btn btn-sm btn-outline-secondary">Reset</a>
    </div>
</form>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0 align-middle">
            <thead style="background:var(--kn-green);color:var(--kn-cream)">
                <tr>
                    <th class="ps-3">#</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                    <th>Date &amp; Time</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$logs): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No logs found.</td></tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td class="ps-3 text-muted small"><?= $log['log_id'] ?></td>
                    <td>
                        <?php if ($log['email']): ?>
                            <div class="fw-semibold small dlp-no-select"><?= htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) ?></div>
                            <div class="text-muted dlp-no-select" style="font-size:.75rem"><?= htmlspecialchars($log['email']) ?></div>
                        <?php else: ?>
                            <span class="text-muted small">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php $badge = $badgeMap[$log['action_type']] ?? 'secondary'; ?>
                        <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($log['action_type']) ?></span>
                    </td>
                    <td class="small"><?= htmlspecialchars($log['description']) ?></td>
                    <td class="small text-muted"><?= htmlspecialchars($log['ip_address']) ?></td>
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
                <a class="page-link" href="index.php?action=securityLogs&page=<?= $i ?>&action_type=<?= urlencode($filterAction) ?>&search=<?= urlencode($filterUser) ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
