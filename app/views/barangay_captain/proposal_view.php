<?php
/**
 * Barangay Captain — View Proposal Details (read-only)
 */
$pageTitle = $pageTitle ?? 'Proposal Details';
$activeNav = 'dashboard';
include __DIR__ . '/../templates/barangay_captain_layout.php';

$statusColors = [
    'Draft'      => 'bg-secondary',
    'For Review' => 'bg-warning text-dark',
    'Approved'   => 'bg-success',
    'Rejected'   => 'bg-danger',
    'Needs Revision' => 'bg-info',
];
$statusColor = $statusColors[$proposal['status']] ?? 'bg-secondary';
?>

<style>
    .info-label { font-size:.75rem;text-transform:uppercase;color:var(--kn-muted);font-weight:600;letter-spacing:.05em }
    .info-value  { font-size:.95rem;color:var(--kn-dark);font-weight:500 }
</style>

<!-- Back -->
<div class="mb-1">
    <a href="index.php?action=captainDashboard" class="text-muted text-decoration-none" style="font-size:.85rem">
        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
    </a>
</div>

<!-- Header -->
<div class="d-flex justify-content-between align-items-start mb-3">
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
            Submitted by <?= htmlspecialchars($proposal['creator_first_name'] . ' ' . $proposal['creator_last_name']) ?>
            &nbsp;·&nbsp; <?= $proposal['submitted_at'] ? date('F j, Y', strtotime($proposal['submitted_at'])) : date('F j, Y', strtotime($proposal['created_at'])) ?>
        </p>
    </div>
    <?php if ($proposal['status'] === 'For Review'): ?>
    <a href="index.php?action=validationForm&proposal_id=<?= $proposal['proposal_id'] ?>"
       class="btn btn-primary btn-sm flex-shrink-0 ms-3">
        <i class="bi bi-pen-fill me-1"></i>Validate This Proposal
    </a>
    <?php endif; ?>
</div>

<div class="row g-3">
    <div class="col-lg-8">

        <!-- I. Identifying Information -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0" style="font-weight:700">I. Identifying Information</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><div class="info-label">Program Type</div><div class="info-value"><?= htmlspecialchars($proposal['program_type']) ?></div></div>
                    <div class="col-md-6"><div class="info-label">Proponent</div><div class="info-value"><?= htmlspecialchars($proposal['proponent'] ?? 'Committee on Health') ?></div></div>
                    <div class="col-md-6"><div class="info-label">Location</div><div class="info-value"><?= htmlspecialchars($proposal['location'] ?? $proposal['barangay_code']) ?></div></div>
                    <div class="col-md-6"><div class="info-label">Target Beneficiaries</div><div class="info-value"><?= htmlspecialchars($proposal['target_beneficiaries']) ?></div></div>
                    <div class="col-md-4"><div class="info-label">Start Date</div><div class="info-value"><?= date('F j, Y', strtotime($proposal['start_date'])) ?></div></div>
                    <div class="col-md-4"><div class="info-label">End Date</div><div class="info-value"><?= date('F j, Y', strtotime($proposal['end_date'])) ?></div></div>
                    <div class="col-md-4"><div class="info-label">Duration</div><div class="info-value"><?= htmlspecialchars($proposal['implementation_days'] ?? '120') ?> days</div></div>
                    <div class="col-md-6"><div class="info-label">Funding Source</div><div class="info-value"><?= htmlspecialchars($proposal['funding_source'] ?? 'N/A') ?></div></div>
                    <div class="col-md-6"><div class="info-label">BNS Staff</div><div class="info-value"><?= htmlspecialchars($proposal['bns_first_name'] . ' ' . $proposal['bns_last_name']) ?></div></div>
                </div>
            </div>
        </div>

        <!-- II. Background and Rationale -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3"><h6 class="mb-0" style="font-weight:700">II. Background and Rationale</h6></div>
            <div class="card-body"><p style="line-height:1.7"><?= nl2br(htmlspecialchars($proposal['rationale'])) ?></p></div>
        </div>

        <!-- III. Project Description -->
        <?php if (!empty($proposal['implementation_plan'])): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3"><h6 class="mb-0" style="font-weight:700">III. Project Description</h6></div>
            <div class="card-body"><p style="line-height:1.7"><?= nl2br(htmlspecialchars($proposal['implementation_plan'])) ?></p></div>
        </div>
        <?php endif; ?>

        <!-- IV. Goals and Objectives -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3"><h6 class="mb-0" style="font-weight:700">IV. Goals and Objectives</h6></div>
            <div class="card-body"><p style="line-height:1.7"><?= nl2br(htmlspecialchars($proposal['objectives'])) ?></p></div>
        </div>

        <!-- V. Budget -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3"><h6 class="mb-0" style="font-weight:700">V. Budgetary Requirements</h6></div>
            <div class="card-body">
                <?php $budgetItems = !empty($proposal['budget_items']) ? json_decode($proposal['budget_items'], true) : []; ?>
                <?php if (!empty($budgetItems)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered" style="font-size:.9rem">
                        <thead style="background:rgba(107,122,58,0.08)">
                            <tr>
                                <th>Item Description</th>
                                <th class="text-center">Daily Cost/Child</th>
                                <th class="text-center">Computation</th>
                                <th class="text-center">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($budgetItems as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['item']) ?></td>
                                <td class="text-center">₱<?= number_format($item['daily_cost'], 2) ?></td>
                                <td class="text-center text-muted" style="font-size:.8rem">
                                    ₱<?= number_format($item['daily_cost'], 2) ?> × <?= (int)$proposal['num_beneficiaries'] ?> × <?= $proposal['implementation_days'] ?? 120 ?>
                                </td>
                                <td class="text-center fw-bold">₱<?= number_format($item['total'] ?? 0, 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot style="background:rgba(107,122,58,0.05)">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">TOTAL:</td>
                                <td class="text-center fw-bold" style="color:var(--kn-primary)">₱<?= number_format($proposal['estimated_budget'], 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php else: ?>
                    <div class="text-muted">Estimated Budget: <strong>₱<?= number_format($proposal['estimated_budget'], 2) ?></strong></div>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- col-lg-8 -->

    <!-- Sidebar -->
    <div class="col-lg-4">

        <!-- Meeting Minutes -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0" style="font-weight:600">Meeting Minutes (<?= count($meetingMinutes ?? []) ?>)</h6>
            </div>
            <div class="card-body">
                <?php if (empty($meetingMinutes)): ?>
                    <p class="text-muted mb-0" style="font-size:.85rem">No meeting minutes recorded yet.</p>
                <?php else: ?>
                    <?php foreach ($meetingMinutes as $m): ?>
                    <div class="mb-2 pb-2 border-bottom">
                        <div style="font-weight:600;font-size:.9rem"><?= date('M j, Y', strtotime($m['meeting_date'])) ?></div>
                        <div style="font-size:.8rem;color:var(--kn-muted)"><?= htmlspecialchars($m['venue']) ?> · <?= (int)$m['num_attendees'] ?> attendees</div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Validation History -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0" style="font-weight:600">Validation History (<?= count($validations ?? []) ?>)</h6>
            </div>
            <div class="card-body">
                <?php if (empty($validations)): ?>
                    <p class="text-muted mb-0" style="font-size:.85rem">No validations yet.</p>
                <?php else: ?>
                    <?php foreach ($validations as $v): ?>
                    <?php
                    $dc = ['Approved'=>'bg-success','Rejected'=>'bg-danger','Needs Revision'=>'bg-warning text-dark'];
                    $dc = $dc[$v['decision']] ?? 'bg-secondary';
                    ?>
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span style="font-weight:600;font-size:.9rem"><?= htmlspecialchars($v['validator_first_name'] . ' ' . $v['validator_last_name']) ?></span>
                            <span class="badge <?= $dc ?>" style="font-size:.7rem"><?= htmlspecialchars($v['decision']) ?></span>
                        </div>
                        <div style="font-size:.75rem;color:var(--kn-muted)"><?= date('M j, Y g:i A', strtotime($v['validated_at'])) ?></div>
                        <?php if ($v['feedback']): ?>
                            <div style="font-size:.85rem;margin-top:.4rem"><?= nl2br(htmlspecialchars($v['feedback'])) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../templates/barangay_captain_layout_end.php'; ?>
