<?php
/**
 * Committee Secretary — View Proposal Details (read-only)
 */
$pageTitle = $pageTitle ?? 'Proposal Details';
$activeNav = 'dashboard';
include __DIR__ . '/../templates/committee_secretary_layout.php';
include __DIR__ . '/../templates/button_styles.php';

$statusColors = [
    'Draft'          => 'bg-secondary',
    'For Review'     => 'bg-warning text-dark',
    'Approved'       => 'bg-success',
    'Rejected'       => 'bg-danger',
    'Needs Revision' => 'bg-info',
];
$statusColor = $statusColors[$proposal['status']] ?? 'bg-secondary';
?>

<style>
    .info-label { font-size:.75rem;text-transform:uppercase;color:var(--kn-muted);font-weight:600;letter-spacing:.05em }
    .info-value  { font-size:.95rem;color:var(--kn-dark);font-weight:500 }
</style>

<div class="mb-1">
    <a href="index.php?action=secretaryDashboard" class="text-muted text-decoration-none" style="font-size:.85rem">
        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
    </a>
</div>

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="mb-0" style="color:var(--kn-dark);font-weight:700">
                <?= htmlspecialchars($proposal['proposal_title']) ?>
            </h4>
            <span class="badge <?= $statusColor ?>" style="font-size:.8rem;padding:.35rem .75rem">
                <?= htmlspecialchars($proposal['status']) ?>
            </span>
        </div>
        <p class="text-muted mb-0" style="font-size:.85rem">
            Created by <?= htmlspecialchars($proposal['creator_first_name'] . ' ' . $proposal['creator_last_name']) ?>
            &nbsp;·&nbsp; <?= date('F j, Y', strtotime($proposal['created_at'])) ?>
        </p>
    </div>
    <a href="index.php?action=minutesForm&proposal_id=<?= $proposal['proposal_id'] ?>"
       class="btn-kn-primary btn-kn-sm flex-shrink-0 ms-3">
        <i class="bi bi-plus-circle-fill"></i>Add Minutes
    </a>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3"><h6 class="mb-0" style="font-weight:700">Basic Information</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><div class="info-label">Program Type</div><div class="info-value"><?= htmlspecialchars($proposal['program_type']) ?></div></div>
                    <div class="col-md-6"><div class="info-label">Beneficiaries</div><div class="info-value"><?= htmlspecialchars($proposal['target_beneficiaries']) ?></div></div>
                    <div class="col-md-4"><div class="info-label">Start Date</div><div class="info-value"><?= date('M j, Y', strtotime($proposal['start_date'])) ?></div></div>
                    <div class="col-md-4"><div class="info-label">End Date</div><div class="info-value"><?= date('M j, Y', strtotime($proposal['end_date'])) ?></div></div>
                    <div class="col-md-4"><div class="info-label">Budget</div><div class="info-value">₱<?= number_format($proposal['estimated_budget'], 2) ?></div></div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3"><h6 class="mb-0" style="font-weight:700">Rationale</h6></div>
            <div class="card-body"><p style="line-height:1.7"><?= nl2br(htmlspecialchars($proposal['rationale'])) ?></p></div>
        </div>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3"><h6 class="mb-0" style="font-weight:700">Objectives</h6></div>
            <div class="card-body"><p style="line-height:1.7"><?= nl2br(htmlspecialchars($proposal['objectives'])) ?></p></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0" style="font-weight:600">Meeting Minutes (<?= count($meetingMinutes ?? []) ?>)</h6>
                    <a href="index.php?action=minutesForm&proposal_id=<?= $proposal['proposal_id'] ?>" class="btn-kn-outline btn-kn-sm">
                        <i class="bi bi-plus-circle"></i>Add
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($meetingMinutes)): ?>
                    <p class="text-muted mb-0" style="font-size:.85rem">No minutes yet.</p>
                <?php else: ?>
                    <?php foreach ($meetingMinutes as $m): ?>
                    <div class="mb-2 pb-2 border-bottom">
                        <div style="font-weight:600;font-size:.9rem"><?= date('M j, Y', strtotime($m['meeting_date'])) ?></div>
                        <div style="font-size:.8rem;color:var(--kn-muted)"><?= htmlspecialchars($m['venue']) ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/committee_secretary_layout_end.php'; ?>
