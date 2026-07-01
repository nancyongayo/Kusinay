<?php
/**
 * Committee Secretary Dashboard
 * Process 13 (Step 1): Secretary records Minutes of the Meeting
 * Minutes are then sent to Committee Chair who decides to create a proposal
 */
$pageTitle = 'Meeting Minutes';
$activeNav = 'dashboard';
include __DIR__ . '/../templates/committee_secretary_layout.php';
include __DIR__ . '/../templates/button_styles.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1" style="color:var(--kn-dark);font-weight:700">📝 Meeting Minutes</h4>
                <p class="text-muted mb-0" style="font-size:.9rem">
                    Record minutes of BNC planning meetings. The Committee Chair will review and create a proposal based on these.
                </p>
            </div>
            <a href="index.php?action=minutesForm" class="btn-kn-primary">
                <i class="bi bi-plus-circle-fill"></i>Record New Minutes
            </a>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <?php
    $pendingReview = array_filter($allMinutes ?? [], fn($m) => !$m['is_reviewed']);
    $reviewed      = array_filter($allMinutes ?? [], fn($m) =>  $m['is_reviewed']);
    ?>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase">Total Minutes</div>
                <div style="font-size:2rem;font-weight:700;color:var(--kn-dark)"><?= count($allMinutes ?? []) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase">Pending Chair Review</div>
                <div style="font-size:2rem;font-weight:700;color:#ffc107"><?= count($pendingReview) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase">Reviewed by Chair</div>
                <div style="font-size:2rem;font-weight:700;color:#28a745"><?= count($reviewed) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Minutes List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0" style="font-weight:600">All Meeting Minutes</h6>
    </div>
    <div class="card-body p-0">
        <?php if (empty($allMinutes)): ?>
            <div class="text-center py-5">
                <div style="font-size:3rem;opacity:.3">📋</div>
                <p class="text-muted mb-2">No minutes recorded yet.</p>
                <a href="index.php?action=minutesForm" class="btn-kn-primary btn-kn-sm">Record First Minutes</a>
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
                            <th style="font-size:.8rem;font-weight:600">Chair Review</th>
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
                            <td class="text-center"><?= (int)$m['num_attendees'] ?></td>
                            <td>
                                <?php if ($m['is_reviewed']): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle-fill me-1"></i>Reviewed
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

<?php include __DIR__ . '/../templates/committee_secretary_layout_end.php'; ?>
