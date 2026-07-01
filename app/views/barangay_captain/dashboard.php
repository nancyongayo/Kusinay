<?php
/**
 * Barangay Captain Dashboard — Process 14: Validating Program Proposals
 */
$pageTitle = 'Proposals Dashboard';
$activeNav = 'dashboard';
include __DIR__ . '/../templates/barangay_captain_layout.php';
include __DIR__ . '/../templates/button_styles.php';

$statusColors = [
    'Draft'          => 'bg-secondary',
    'For Review'     => 'bg-warning text-dark',
    'Approved'       => 'bg-success',
    'Rejected'       => 'bg-danger',
    'Needs Revision' => 'bg-info text-dark',
];
?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Pending Review</div>
                <div style="font-size:2rem;font-weight:700;color:<?= count($pendingProposals ?? []) > 0 ? '#ffc107' : 'var(--kn-dark)' ?>">
                    <?= count($pendingProposals ?? []) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Approved</div>
                <div style="font-size:2rem;font-weight:700;color:#198754">
                    <?= count(array_filter($allProposals ?? [], fn($p) => $p['status'] === 'Approved')) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Total Proposals</div>
                <div style="font-size:2rem;font-weight:700;color:var(--kn-dark)">
                    <?= count($allProposals ?? []) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Review -->
<?php if (!empty($pendingProposals)): ?>
<div class="card border-0 shadow-sm mb-4" style="border-left:4px solid #ffc107 !important">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0" style="font-weight:700;color:var(--kn-dark)">
                <i class="bi bi-hourglass-split text-warning me-2"></i>Awaiting Your Validation
            </h6>
            <span class="badge bg-warning text-dark"><?= count($pendingProposals) ?> pending</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:rgba(255,193,7,0.06)">
                    <tr>
                        <th style="font-size:.8rem;font-weight:600">Proposal Title</th>
                        <th style="font-size:.8rem;font-weight:600">Program Type</th>
                        <th style="font-size:.8rem;font-weight:600">Beneficiaries</th>
                        <th style="font-size:.8rem;font-weight:600">Budget</th>
                        <th style="font-size:.8rem;font-weight:600">Submitted</th>
                        <th style="font-size:.8rem;font-weight:600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingProposals as $p): ?>
                    <tr>
                        <td style="font-weight:600;color:var(--kn-dark)"><?= htmlspecialchars($p['proposal_title']) ?></td>
                        <td style="font-size:.85rem"><?= htmlspecialchars($p['program_type']) ?></td>
                        <td style="font-size:.85rem"><?= (int)$p['num_beneficiaries'] ?> children</td>
                        <td style="font-size:.85rem">₱<?= number_format($p['estimated_budget'], 2) ?></td>
                        <td style="font-size:.85rem"><?= $p['submitted_at'] ? date('M j, Y', strtotime($p['submitted_at'])) : '—' ?></td>
                        <td>
                            <a href="index.php?action=validationForm&proposal_id=<?= $p['proposal_id'] ?>"
                               class="btn-kn-primary btn-kn-sm">
                                <i class="bi bi-pen-fill"></i>Validate
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- BNC Meeting Minutes (View-Only for Transparency) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0" style="font-weight:600">
                    <i class="bi bi-journal-text text-primary me-2"></i>BNC Meeting Minutes
                </h6>
                <small class="text-muted">View-only access for transparency</small>
            </div>
            <span class="badge" style="background:var(--kn-primary)"><?= count($allMinutes ?? []) ?> minutes</span>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($allMinutes)): ?>
            <div class="text-center py-5">
                <div style="font-size:3rem;opacity:.3">📝</div>
                <p class="text-muted mb-0">No meeting minutes recorded yet.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:rgba(107,122,58,0.05)">
                        <tr>
                            <th style="font-size:.8rem;font-weight:600">Meeting Subject</th>
                            <th style="font-size:.8rem;font-weight:600">Date & Time</th>
                            <th style="font-size:.8rem;font-weight:600">Venue</th>
                            <th style="font-size:.8rem;font-weight:600">Attendees</th>
                            <th style="font-size:.8rem;font-weight:600">Recorded By</th>
                            <th style="font-size:.8rem;font-weight:600">Chair Review</th>
                            <th style="font-size:.8rem;font-weight:600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allMinutes as $m): ?>
                        <tr>
                            <td style="font-weight:600;color:var(--kn-dark);max-width:250px">
                                <?= htmlspecialchars($m['agenda']) ?>
                            </td>
                            <td style="font-size:.85rem">
                                <?= date('M j, Y', strtotime($m['meeting_date'])) ?>
                                <div class="text-muted" style="font-size:.75rem">
                                    <?= date('g:i A', strtotime($m['meeting_time'])) ?>
                                </div>
                            </td>
                            <td style="font-size:.85rem"><?= htmlspecialchars($m['venue']) ?></td>
                            <td class="text-center"><?= (int)$m['num_attendees'] ?></td>
                            <td style="font-size:.85rem">
                                <?= htmlspecialchars($m['recorder_first_name'] . ' ' . $m['recorder_last_name']) ?>
                                <div class="text-muted" style="font-size:.75rem">Committee Secretary</div>
                            </td>
                            <td>
                                <?php if ($m['is_reviewed']): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle-fill"></i> Reviewed
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="index.php?action=viewMinutes&minute_id=<?= $m['minute_id'] ?>"
                                   class="btn-kn-outline btn-kn-sm">
                                    <i class="bi bi-eye"></i>View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- All Proposals -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0" style="font-weight:600">All Proposals</h6>
    </div>
    <div class="card-body p-0">
        <?php if (empty($allProposals)): ?>
            <div class="text-center py-5">
                <div style="font-size:3rem;opacity:.3">📋</div>
                <p class="text-muted mb-0">No proposals yet.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:rgba(107,122,58,0.05)">
                        <tr>
                            <th style="font-size:.8rem;font-weight:600">Proposal Title</th>
                            <th style="font-size:.8rem;font-weight:600">Program Type</th>
                            <th style="font-size:.8rem;font-weight:600">Beneficiaries</th>
                            <th style="font-size:.8rem;font-weight:600">Status</th>
                            <th style="font-size:.8rem;font-weight:600">Date</th>
                            <th style="font-size:.8rem;font-weight:600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allProposals as $p): ?>
                        <tr>
                            <td style="font-weight:600;color:var(--kn-dark)"><?= htmlspecialchars($p['proposal_title']) ?></td>
                            <td style="font-size:.85rem"><?= htmlspecialchars($p['program_type']) ?></td>
                            <td style="font-size:.85rem"><?= (int)$p['num_beneficiaries'] ?></td>
                            <td>
                                <span class="badge <?= $statusColors[$p['status']] ?? 'bg-secondary' ?>">
                                    <?= htmlspecialchars($p['status']) ?>
                                </span>
                            </td>
                            <td style="font-size:.85rem;color:var(--kn-muted)"><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
                            <td>
                                <?php if ($p['status'] === 'For Review'): ?>
                                    <a href="index.php?action=validationForm&proposal_id=<?= $p['proposal_id'] ?>"
                                       class="btn-kn-primary btn-kn-sm">
                                        <i class="bi bi-pen-fill"></i>Validate
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?action=viewProposal&proposal_id=<?= $p['proposal_id'] ?>"
                                       class="btn-kn-outline btn-kn-sm">
                                        <i class="bi bi-eye"></i>View
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../templates/barangay_captain_layout_end.php'; ?>
