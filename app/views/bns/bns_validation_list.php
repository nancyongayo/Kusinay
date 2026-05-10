<?php
$pageTitle = 'Profile Validation';
$activeNav = 'validation';
include __DIR__ . '/../templates/bns_layout.php';

$flashWarning = $_SESSION['flash_warning'] ?? null;
unset($_SESSION['flash_warning']);
?>
<style>
:root { --kn-green:#6B7A3A; --kn-green-d:#556030; --kn-orange:#C4722A; --kn-cream:#F5EDD6; --kn-dark:#3D4A1E; --kn-muted:rgba(61,74,30,.55); }
.table tbody tr:hover { background: rgba(107,122,58,.04); }
</style>

<?php if ($flashWarning): ?>
<div class="alert alert-warning alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($flashWarning) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div style="background:#fff;border:1.5px solid rgba(196,114,42,.25);border-radius:1rem;padding:1.25rem;text-align:center">
            <div style="font-size:2rem;font-weight:800;color:var(--kn-orange)"><?= (int)($counts['pending'] ?? 0) ?></div>
            <div style="font-size:.82rem;color:var(--kn-muted);text-transform:uppercase;letter-spacing:.04em">Pending</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div style="background:#fff;border:1.5px solid rgba(107,122,58,.25);border-radius:1rem;padding:1.25rem;text-align:center">
            <div style="font-size:2rem;font-weight:800;color:var(--kn-green)"><?= (int)($counts['validated'] ?? 0) ?></div>
            <div style="font-size:.82rem;color:var(--kn-muted);text-transform:uppercase;letter-spacing:.04em">Validated</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div style="background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:1rem;padding:1.25rem;text-align:center">
            <div style="font-size:2rem;font-weight:800;color:var(--kn-dark)"><?= (int)($counts['total'] ?? 0) ?></div>
            <div style="font-size:.82rem;color:var(--kn-muted);text-transform:uppercase;letter-spacing:.04em">Total Assigned</div>
        </div>
    </div>
</div>

<!-- Search Bar -->
<form method="GET" action="index.php" class="mb-4 d-flex gap-2">
    <input type="hidden" name="action" value="bnsValidationList">
    <input type="text" name="search"
           style="border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.45rem .85rem;font-size:.95rem;flex:1"
           placeholder="Search by Mother name, household code, or purok…"
           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <button type="submit" style="background:var(--kn-green);color:#fff;border:none;border-radius:8px;padding:.45rem 1.1rem;font-weight:600">
        <i class="bi bi-search"></i>
    </button>
    <?php if (!empty($_GET['search'])): ?>
    <a href="index.php?action=bnsValidationList"
       style="background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.45rem .9rem;text-decoration:none;font-weight:600">Clear</a>
    <?php endif; ?>
</form>

<!-- Profiles Table -->
<div style="background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:1rem;overflow:hidden">
    <div style="padding:.85rem 1.25rem;border-bottom:1.5px solid rgba(107,122,58,.1);font-weight:600;color:var(--kn-dark)">
        Submitted Profiles
        <?php if (!empty($_GET['search'])): ?>
            <span style="color:var(--kn-muted);font-weight:400;font-size:.88rem"> — filtered by "<?= htmlspecialchars($_GET['search']) ?>"</span>
        <?php endif; ?>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr style="background:var(--kn-green);color:var(--kn-cream)">
                    <th style="border:none;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;padding:.75rem 1rem">Name</th>
                    <th style="border:none;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em">Household Code</th>
                    <th style="border:none;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em">Purok</th>
                    <th style="border:none;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em">Submitted At</th>
                    <th style="border:none;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em">Status</th>
                    <th style="border:none;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;text-align:right;padding-right:1rem">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($profiles)): ?>
                <tr>
                    <td colspan="6" class="text-center py-5" style="color:var(--kn-muted)">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        No submitted profiles found.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($profiles as $p): ?>
                <tr>
                    <td style="padding:.7rem 1rem;font-weight:600"><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></td>
                    <td>
                        <?php if ($p['household_code']): ?>
                            <code style="font-size:.88rem;color:var(--kn-green)"><?= htmlspecialchars($p['household_code']) ?></code>
                        <?php else: ?>
                            <span style="color:var(--kn-muted)">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['purok'] ?? '—') ?></td>
                    <td style="font-size:.88rem;color:var(--kn-muted)">
                        <?= $p['submitted_at'] ? date('M j, Y g:i A', strtotime($p['submitted_at'])) : '—' ?>
                    </td>
                    <td>
                        <?php
                        $badgeBg  = $p['profile_status'] === 'Returned'
                            ? 'rgba(196,114,42,.18)' : 'rgba(196,114,42,.12)';
                        $badgeTxt = 'var(--kn-orange)';
                        $icon     = $p['profile_status'] === 'Returned'
                            ? '<i class="bi bi-arrow-counterclockwise me-1"></i>'
                            : '<i class="bi bi-clock me-1"></i>';
                        ?>
                        <span style="background:<?= $badgeBg ?>;color:<?= $badgeTxt ?>;font-size:.78rem;font-weight:700;padding:.2em .6em;border-radius:6px">
                            <?= $icon ?><?= htmlspecialchars($p['profile_status']) ?>
                        </span>
                    </td>
                    <td style="text-align:right;padding-right:1rem">
                        <a href="index.php?action=bnsValidationDetail&profile_id=<?= (int)$p['profile_id'] ?>"
                           style="background:var(--kn-green);color:#fff;border:none;border-radius:7px;padding:.3rem .85rem;font-size:.88rem;font-weight:600;text-decoration:none">
                            <i class="bi bi-eye me-1"></i> Review
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Recently Auto-Validated (BNS-registered residents, last 30 days) -->
<?php if (!empty($recentValidated)): ?>
<div style="background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:1rem;overflow:hidden;margin-top:1.5rem">
    <div style="padding:.85rem 1.25rem;border-bottom:1.5px solid rgba(107,122,58,.1);font-weight:600;color:var(--kn-dark);display:flex;align-items:center;gap:.5rem">
        <i class="bi bi-check-circle-fill" style="color:var(--kn-green)"></i>
        Recently Completed Profiles
        <span style="font-size:.8rem;font-weight:400;color:var(--kn-muted)">(auto-validated · last 30 days)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr style="background:rgba(107,122,58,.06)">
                    <th style="border:none;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;padding:.65rem 1rem;color:var(--kn-muted)">Name</th>
                    <th style="border:none;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--kn-muted)">Household Code</th>
                    <th style="border:none;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--kn-muted)">Purok</th>
                    <th style="border:none;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--kn-muted)">Validated At</th>
                    <th style="border:none;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--kn-muted)">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentValidated as $p): ?>
                <tr>
                    <td style="padding:.65rem 1rem;font-weight:600"><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></td>
                    <td>
                        <?php if ($p['household_code']): ?>
                            <code style="font-size:.88rem;color:var(--kn-green)"><?= htmlspecialchars($p['household_code']) ?></code>
                        <?php else: ?>
                            <span style="color:var(--kn-muted)">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['purok'] ?? '—') ?></td>
                    <td style="font-size:.88rem;color:var(--kn-muted)">
                        <?= $p['validated_at'] ? date('M j, Y g:i A', strtotime($p['validated_at'])) : '—' ?>
                    </td>
                    <td>
                        <span style="background:rgba(107,122,58,.12);color:var(--kn-green);font-size:.78rem;font-weight:700;padding:.2em .6em;border-radius:6px">
                            <i class="bi bi-check-circle me-1"></i>Validated
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../templates/bns_layout_end.php'; ?>
