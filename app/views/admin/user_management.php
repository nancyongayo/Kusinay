<?php
$pageTitle = 'User Management';
$activeNav = 'users';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Security.php';

Security::requirePermission('userManagement');

$db = getDBConnection();

// Handle role update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    Security::verifyCsrf();
    $targetId = (int) ($_POST['target_user_id'] ?? 0);
    $newRole  = (int) ($_POST['new_role_id']    ?? 0);
    if ($targetId && in_array($newRole, [1,2,3,4])) {
        $db->prepare("UPDATE users SET role_id = ? WHERE user_id = ?")
           ->execute([$newRole, $targetId]);
        // Log it
        $db->prepare("INSERT INTO system_logs (user_id, action_type, description, ip_address) VALUES (?,?,?,?)")
           ->execute([$_SESSION['user_id'], 'ADMIN_ROLE_CHANGE', "Changed role of user_id={$targetId} to role_id={$newRole}", $_SERVER['REMOTE_ADDR'] ?? '']);
        $_SESSION['flash'] = 'User role updated.';
    }
    header('Location: index.php?action=userManagement');
    exit;
}

// Handle toggle lock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_lock'])) {
    Security::verifyCsrf();
    $targetId = (int) ($_POST['target_user_id'] ?? 0);
    $action   = $_POST['lock_action'] ?? '';
    if ($targetId && in_array($action, ['lock','unlock'])) {
        if ($action === 'lock') {
            $db->prepare("UPDATE user_auth SET locked_until = DATE_ADD(NOW(), INTERVAL 999 DAY) WHERE user_id = ?")
               ->execute([$targetId]);
            $logAction = 'ADMIN_LOCK_USER';
        } else {
            $db->prepare("UPDATE user_auth SET locked_until = NULL, failed_attempts = 0 WHERE user_id = ?")
               ->execute([$targetId]);
            $logAction = 'ADMIN_UNLOCK_USER';
        }
        $db->prepare("INSERT INTO system_logs (user_id, action_type, description, ip_address) VALUES (?,?,?,?)")
           ->execute([$_SESSION['user_id'], $logAction, "{$action} user_id={$targetId}", $_SERVER['REMOTE_ADDR'] ?? '']);
        $_SESSION['flash'] = 'User ' . $action . 'ed successfully.';
    }
    header('Location: index.php?action=userManagement');
    exit;
}

// Now safe to output HTML
require_once __DIR__ . '/../templates/admin_layout.php';

// Fetch users
$search = trim($_GET['search'] ?? '');
$roleFilter = (int) ($_GET['role_id'] ?? 0);
$where = []; $params = [];
if ($search) {
    $where[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
    $params  = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}
if ($roleFilter) { $where[] = 'u.role_id = ?'; $params[] = $roleFilter; }
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$users = $db->prepare("
    SELECT u.user_id, u.first_name, u.last_name, u.email, u.is_verified, u.created_at,
           r.role_name, r.role_id,
           a.failed_attempts, a.locked_until
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.role_id
    LEFT JOIN user_auth a ON u.user_id = a.user_id
    $whereSQL
    ORDER BY u.created_at DESC
");
$users->execute($params);
$users = $users->fetchAll();

$roles = $db->query("SELECT role_id, role_name FROM roles ORDER BY role_id")->fetchAll();
?>

<?= \Security::classificationBadge('CONFIDENTIAL') ?> &nbsp;
<span class="text-muted small">User data is confidential — handle with care.</span>

<div class="d-flex justify-content-between align-items-center mt-3 mb-3">
    <h4 class="mb-0" style="color:var(--kn-dark)">👥 User Management</h4>
    <span class="text-muted small"><?= count($users) ?> users</span>
</div>

<!-- Filters -->
<form method="GET" action="index.php" class="row g-2 mb-3">
    <input type="hidden" name="action" value="userManagement">
    <div class="col-12 col-md-5">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Search by name or email…" value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-12 col-md-3">
        <select name="role_id" class="form-select form-select-sm">
            <option value="">All roles</option>
            <?php foreach ($roles as $r): ?>
                <option value="<?= $r['role_id'] ?>" <?= $roleFilter === (int)$r['role_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($r['role_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        <a href="index.php?action=userManagement" class="btn btn-sm btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0 align-middle">
            <thead style="background:var(--kn-green);color:var(--kn-cream)">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Verified</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$users): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No users found.</td></tr>
            <?php else: ?>
            <?php foreach ($users as $u):
                $isLocked = $u['locked_until'] && strtotime($u['locked_until']) > time();
                $isSelf   = (int)$u['user_id'] === (int)$_SESSION['user_id'];
            ?>
            <tr>
                <td class="ps-3 text-muted small"><?= $u['user_id'] ?></td>
                <td class="fw-semibold small dlp-no-select">
                    <?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?>
                    <?php if ($isSelf): ?><span class="badge bg-secondary ms-1" style="font-size:.6rem">You</span><?php endif; ?>
                </td>
                <td class="small dlp-no-select"><?= htmlspecialchars($u['email']) ?></td>
                <td>
                    <!-- Role change form -->
                    <?php if (!$isSelf): ?>
                    <form method="POST" action="index.php?action=userManagement" class="d-flex gap-1 align-items-center">
                        <input type="hidden" name="update_role" value="1">
                        <input type="hidden" name="target_user_id" value="<?= $u['user_id'] ?>">
                        <?= Security::csrfField() ?>
                        <select name="new_role_id" class="form-select form-select-sm" style="min-width:140px">
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['role_id'] ?>" <?= (int)$r['role_id'] === (int)$u['role_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['role_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-xs btn-outline-primary" style="font-size:.72rem;padding:.2rem .5rem">Save</button>
                    </form>
                    <?php else: ?>
                        <span class="badge" style="background:var(--kn-green)"><?= htmlspecialchars($u['role_name'] ?? '—') ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($u['is_verified']): ?>
                        <span class="badge bg-success">✓ Verified</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($isLocked): ?>
                        <span class="badge bg-danger">🔒 Locked</span>
                    <?php else: ?>
                        <span class="badge bg-success">Active</span>
                    <?php endif; ?>
                </td>
                <td class="small text-muted text-nowrap"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                <td>
                    <?php if (!$isSelf): ?>
                    <div class="d-flex gap-1 flex-wrap">
                        <a href="index.php?action=userActivity&user_id=<?= $u['user_id'] ?>"
                           class="btn btn-xs btn-outline-primary"
                           style="font-size:.72rem;padding:.2rem .5rem">
                            <i class="bi bi-clock-history"></i> Activity
                        </a>
                        <form method="POST" action="index.php?action=userManagement" class="d-inline">
                            <input type="hidden" name="toggle_lock" value="1">
                            <input type="hidden" name="target_user_id" value="<?= $u['user_id'] ?>">
                            <input type="hidden" name="lock_action" value="<?= $isLocked ? 'unlock' : 'lock' ?>">
                            <?= Security::csrfField() ?>
                            <button type="submit" class="btn btn-xs <?= $isLocked ? 'btn-outline-success' : 'btn-outline-danger' ?>"
                                    style="font-size:.72rem;padding:.2rem .5rem"
                                    onclick="return confirm('<?= $isLocked ? 'Unlock' : 'Lock' ?> this user?')">
                                <?= $isLocked ? '🔓 Unlock' : '🔒 Lock' ?>
                            </button>
                        </form>
                    </div>
                    <?php else: ?>
                        <span class="text-muted small">—</span>
                    <?php endif; ?>                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
