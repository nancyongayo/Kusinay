<?php
$pageTitle = 'Report Validation';
$activeNav = 'validation';
require_once __DIR__ . '/../templates/nutrition_layout.php';

$months = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
$statusColors = [
    'Draft'     => ['bg'=>'rgba(107,122,58,.1)',  'color'=>'var(--kn-green)'],
    'Submitted' => ['bg'=>'rgba(196,114,42,.1)',  'color'=>'var(--kn-orange)'],
    'Approved'  => ['bg'=>'rgba(107,122,58,.15)', 'color'=>'var(--kn-green)'],
    'Returned'  => ['bg'=>'rgba(220,53,69,.1)',   'color'=>'#dc3545'],
];
?>
<style>
:root{--kn-green:#6B7A3A;--kn-orange:#C4722A;--kn-dark:#3D4A1E;--kn-muted:rgba(61,74,30,.55);}
.kn-table th{background:rgba(107,122,58,.07);color:var(--kn-dark);font-size:.82rem;font-weight:700;border-bottom:2px solid rgba(107,122,58,.15);}
.kn-table td{font-size:.88rem;vertical-align:middle;border-bottom:1px solid rgba(107,122,58,.08);}
.status-chip{display:inline-block;padding:.2em .65em;border-radius:12px;font-weight:700;font-size:.75rem;}
</style>

<div class="mb-4">
    <h4 class="fw-bold mb-1">Report Validation</h4>
    <p class="text-muted mb-0" style="font-size:.88rem">Review and approve BNS Monthly Accomplishment Reports.</p>
</div>

<!-- Pending reports -->
<?php if (!empty($pending)): ?>
<div class="alert d-flex align-items-center gap-2 mb-4"
     style="background:rgba(196,114,42,.08);border:1.5px solid rgba(196,114,42,.3);border-radius:.75rem;padding:.75rem 1rem">
    <i class="bi bi-exclamation-triangle-fill" style="color:var(--kn-orange)"></i>
    <strong><?= count($pending) ?> report(s) pending your review.</strong>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table kn-table mb-0">
            <thead>
                <tr>
                    <th>BNS Name</th>
                    <th>Month</th>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($all)): ?>
            <tr><td colspan="6" class="text-center py-4 text-muted">No reports submitted yet.</td></tr>
            <?php else: ?>
            <?php foreach ($all as $r):
                $sc = $statusColors[$r['status']] ?? $statusColors['Draft'];
            ?>
            <tr>
                <td class="fw-semibold"><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
                <td><?= $months[$r['report_month']] ?></td>
                <td><?= $r['report_year'] ?></td>
                <td>
                    <span class="status-chip" style="background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>">
                        <?= $r['status'] ?>
                    </span>
                </td>
                <td><?= $r['submitted_at'] ? date('M j, Y', strtotime($r['submitted_at'])) : '—' ?></td>
                <td>
                    <a href="index.php?action=reportDetail&report_id=<?= (int)$r['report_id'] ?>"
                       style="display:inline-flex;align-items:center;gap:.3rem;background:var(--kn-green);color:#fff;border:none;border-radius:7px;padding:.25rem .75rem;font-size:.82rem;font-weight:600;text-decoration:none">
                        <i class="bi bi-eye"></i> View
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
