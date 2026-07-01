<?php
/**
 * Committee Chair — All Meeting Minutes List
 * Chair reviews minutes from Secretary and decides to create proposals
 */
$pageTitle = 'Meeting Minutes';
$activeNav = 'minutes_review';
include __DIR__ . '/../templates/committee_chair_layout.php';
include __DIR__ . '/../templates/button_styles.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1" style="color:var(--kn-dark);font-weight:700">
            <i class="bi bi-journal-text me-2"></i>Meeting Minutes
        </h4>
        <p class="text-muted mb-0" style="font-size:.9rem">
            Minutes recorded by the Committee Secretary. Review and create a proposal if needed.
        </p>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <?php
    $pending  = array_filter($allMinutes ?? [], fn($m) => !$m['is_reviewed']);
    $reviewed = array_filter($allMinutes ?? [], fn($m) =>  $m['is_reviewed']);
    ?>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Total</div>
                <div style="font-size:2rem;font-weight:700;color:var(--kn-dark)"><?= count($allMinutes ?? []) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Pending Review</div>
                <div style="font-size:2rem;font-weight:700;color:<?= count($pending) > 0 ? '#ffc107' : 'var(--kn-dark)' ?>">
                    <?= count($pending) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Reviewed</div>
                <div style="font-size:2rem;font-weight:700;color:#198754"><?= count($reviewed) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Minutes Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0" style="font-weight:600">All Meeting Minutes</h6>
    </div>
    <div class="card-body p-0">
        <?php if (empty($allMinutes)): ?>
            <div class="text-center py-5">
                <div style="font-size:3rem;opacity:.3"><i class="bi bi-journal-x"></i></div>
                <p class="text-muted mb-0 mt-2">No meeting minutes recorded yet.</p>
                <small class="text-muted">The Committee Secretary will record minutes after BNC meetings.</small>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:rgba(107,122,58,0.05)">
                        <tr>
                            <th style="font-size:.8rem;font-weight:600">Meeting Subject</th>
                            <th style="font-size:.8rem;font-weight:600">Date</th>
                            <th style="font-size:.8rem;font-weight:600">Venue</th>
                            <th style="font-size:.8rem;font-weight:600">Attendees</th>
                            <th style="font-size:.8rem;font-weight:600">Linked Proposal</th>
                            <th style="font-size:.8rem;font-weight:600">Status</th>
                            <th style="font-size:.8rem;font-weight:600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allMinutes as $m): ?>
                        <tr>
                            <td style="font-weight:600;color:var(--kn-dark)">
                                <?= htmlspecialchars($m['agenda']) ?>
                            </td>
                            <td style="font-size:.85rem">
                                <?= date('M j, Y', strtotime($m['meeting_date'])) ?>
                                <div class="text-muted" style="font-size:.75rem">
                                    <?= date('g:i A', strtotime($m['meeting_time'])) ?>
                                </div>
                            </td>
                            <td style="font-size:.85rem"><?= htmlspecialchars($m['venue']) ?></td>
                            <td class="text-center" style="font-size:.85rem"><?= (int)$m['num_attendees'] ?></td>
                            <td>
                                <?php if (!empty($m['proposal_id'])): ?>
                                    <a href="index.php?action=viewProposal&proposal_id=<?= $m['proposal_id'] ?>"
                                       class="badge bg-success text-decoration-none" style="font-size:.75rem">
                                        <i class="bi bi-file-earmark-check me-1"></i>View Proposal
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size:.8rem">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($m['is_reviewed']): ?>
                                    <span class="badge bg-success" style="font-size:.75rem">
                                        <i class="bi bi-check-circle-fill me-1"></i>Reviewed
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark" style="font-size:.75rem">
                                        <i class="bi bi-clock me-1"></i>Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="index.php?action=viewMinutes&minute_id=<?= $m['minute_id'] ?>"
                                   class="<?= $m['is_reviewed'] ? 'btn-kn-outline' : 'btn-kn-primary' ?> btn-kn-sm">
                                    <i class="bi bi-eye"></i><?= $m['is_reviewed'] ? 'View' : 'Review' ?>
                                </a>
                                <?php if (!$m['proposal_id']): ?>
                                    <a href="index.php?action=proposalForm&from_minute=<?= $m['minute_id'] ?>"
                                       class="btn-kn-secondary btn-kn-sm">
                                        <i class="bi bi-plus"></i>Create Proposal
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

<?php include __DIR__ . '/../templates/committee_chair_layout_end.php'; ?>
