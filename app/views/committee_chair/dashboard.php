<?php
/**
 * Committee Chair on Health Dashboard
 */
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = $pageTitle ?? 'Dashboard';
$activeNav = $activeNav ?? 'dashboard';
include __DIR__ . '/../templates/committee_chair_layout.php';
include __DIR__ . '/../templates/button_styles.php';

$draftCount          = count(array_filter($myProposals ?? [], fn($p) => $p['status'] === 'Draft'));
$reviewCount         = count(array_filter($myProposals ?? [], fn($p) => $p['status'] === 'For Review'));
$approvedCount       = count(array_filter($myProposals ?? [], fn($p) => $p['status'] === 'Approved'));
$pendingMinutesCount = count($pendingMinutes ?? []);

$statusColors = [
    'Draft'          => ['bg'=>'#f1f3f5', 'text'=>'#495057', 'label'=>'Draft'],
    'For Review'     => ['bg'=>'#fff3cd', 'text'=>'#856404', 'label'=>'For Review'],
    'Approved'       => ['bg'=>'#d1e7dd', 'text'=>'#0a3622', 'label'=>'Approved'],
    'Rejected'       => ['bg'=>'#f8d7da', 'text'=>'#58151c', 'label'=>'Rejected'],
    'Needs Revision' => ['bg'=>'#cff4fc', 'text'=>'#055160', 'label'=>'Needs Revision'],
];
?>

<style>
.stat-card {
    border-radius: 12px;
    border: 1px solid #eee;
    background: #fff;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    transition: box-shadow .15s;
}
.stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); }
.stat-card .label { font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;color:#6c757d;font-weight:600;margin-bottom:.35rem }
.stat-card .value { font-size:2rem;font-weight:700;line-height:1 }

.action-card {
    border-radius:12px;
    border:1px solid #eee;
    background:#fff;
    padding:1.25rem 1.5rem;
    box-shadow:0 1px 4px rgba(0,0,0,.06);
    display:flex;
    align-items:center;
    gap:1rem;
    text-decoration:none;
    color:inherit;
    transition:box-shadow .15s, transform .15s;
}
.action-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.1); transform:translateY(-2px); color:inherit; }
.action-card .icon-wrap {
    width:48px;height:48px;border-radius:12px;
    display:flex;align-items:center;justify-content:center;
    font-size:1.4rem;flex-shrink:0;
}

.section-title {
    font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;
    color:#6c757d;font-weight:700;margin-bottom:1rem;
}

.proposal-status {
    display:inline-flex;align-items:center;
    padding:.25rem .65rem;border-radius:20px;
    font-size:.75rem;font-weight:600;
}

.minutes-alert {
    border-radius:12px;
    border:1px solid #ffc107;
    background:linear-gradient(135deg,#fffdf0,#fff9e6);
    padding:1rem 1.25rem;
    margin-bottom:1.5rem;
}
</style>

<!-- ── Stat Cards ── -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="label">Minutes to Review</div>
            <div class="value" style="color:<?= $pendingMinutesCount > 0 ? '#f59e0b' : '#212529' ?>">
                <?= $pendingMinutesCount ?>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="label">Draft Proposals</div>
            <div class="value" style="color:#212529"><?= $draftCount ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="label">For Review</div>
            <div class="value" style="color:#f59e0b"><?= $reviewCount ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="label">Approved</div>
            <div class="value" style="color:#198754"><?= $approvedCount ?></div>
        </div>
    </div>
</div>

<!-- ── Pending Minutes Alert ── -->
<?php if (!empty($pendingMinutes)): ?>
<div class="minutes-alert mb-4">
    <div class="d-flex justify-content-between align-items-start mb-2">
        <div>
            <div style="font-weight:700;color:#92400e;font-size:.95rem">
                <i class="bi bi-journal-text me-2"></i>Meeting Minutes Awaiting Review
            </div>
            <div style="font-size:.82rem;color:#a16207;margin-top:.2rem">
                Recorded by the Committee Secretary — review and create a proposal if needed
            </div>
        </div>
        <span style="background:#ffc107;color:#000;font-size:.72rem;font-weight:700;padding:.25rem .6rem;border-radius:20px">
            <?= $pendingMinutesCount ?> pending
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0" style="background:transparent">
            <thead>
                <tr style="border-color:#fde68a">
                    <th style="font-size:.75rem;font-weight:600;color:#92400e;border-color:#fde68a">Meeting Subject</th>
                    <th style="font-size:.75rem;font-weight:600;color:#92400e;border-color:#fde68a">Date</th>
                    <th style="font-size:.75rem;font-weight:600;color:#92400e;border-color:#fde68a">Venue</th>
                    <th style="font-size:.75rem;font-weight:600;color:#92400e;border-color:#fde68a">Attendees</th>
                    <th style="font-size:.75rem;font-weight:600;color:#92400e;border-color:#fde68a"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingMinutes as $m): ?>
                <tr style="border-color:#fde68a">
                    <td style="font-weight:600;color:#78350f;font-size:.88rem;border-color:#fde68a">
                        <?= htmlspecialchars($m['agenda']) ?>
                    </td>
                    <td style="font-size:.82rem;color:#92400e;border-color:#fde68a">
                        <?= date('M j, Y', strtotime($m['meeting_date'])) ?>
                    </td>
                    <td style="font-size:.82rem;color:#92400e;border-color:#fde68a">
                        <?= htmlspecialchars($m['venue']) ?>
                    </td>
                    <td style="font-size:.82rem;color:#92400e;border-color:#fde68a;text-align:center">
                        <?= (int)$m['num_attendees'] ?>
                    </td>
                    <td style="border-color:#fde68a">
                        <a href="index.php?action=viewMinutes&minute_id=<?= $m['minute_id'] ?>"
                           class="btn-kn-secondary btn-kn-sm">
                            <i class="bi bi-eye-fill"></i>Review & Decide
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- ── Quick Actions ── -->
<div class="section-title">Quick Actions</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <a href="index.php?action=reviewAffectedChildren" class="action-card">
            <div class="icon-wrap" style="background:#ecfdf5">
                <i class="bi bi-clipboard-data-fill" style="color:#059669"></i>
            </div>
            <div>
                <div style="font-weight:700;font-size:.95rem;color:#111827">Review Affected Children</div>
                <div style="font-size:.82rem;color:#6b7280;margin-top:.15rem">View OPT Plus Form C data</div>
            </div>
            <i class="bi bi-chevron-right ms-auto" style="color:#d1d5db"></i>
        </a>
    </div>
    <div class="col-md-6">
        <a href="index.php?action=proposalForm" class="action-card">
            <div class="icon-wrap" style="background:#eff6ff">
                <i class="bi bi-plus-circle-fill" style="color:#2563eb"></i>
            </div>
            <div>
                <div style="font-weight:700;font-size:.95rem;color:#111827">Create New Proposal</div>
                <div style="font-size:.82rem;color:#6b7280;margin-top:.15rem">Plan a feeding program intervention</div>
            </div>
            <i class="bi bi-chevron-right ms-auto" style="color:#d1d5db"></i>
        </a>
    </div>
</div>

<!-- ── My Proposals ── -->
<div class="section-title">My Proposals</div>
<div style="border-radius:12px;border:1px solid #eee;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.06);overflow:hidden">
    <?php if (empty($myProposals)): ?>
        <div class="text-center py-5">
            <div style="font-size:2.5rem;opacity:.2"><i class="bi bi-file-earmark-text"></i></div>
            <p class="text-muted mb-3 mt-2" style="font-size:.9rem">No proposals yet.</p>
            <a href="index.php?action=proposalForm" class="btn-kn-primary btn-kn-sm">
                <i class="bi bi-plus-circle-fill"></i>Create First Proposal
            </a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:.88rem">
                <thead style="background:#f8f9fa">
                    <tr>
                        <th style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6c757d;padding:.85rem 1rem;border-bottom:1px solid #eee">Proposal Title</th>
                        <th style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6c757d;padding:.85rem 1rem;border-bottom:1px solid #eee">Program Type</th>
                        <th style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6c757d;padding:.85rem 1rem;border-bottom:1px solid #eee">Beneficiaries</th>
                        <th style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6c757d;padding:.85rem 1rem;border-bottom:1px solid #eee">Budget</th>
                        <th style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6c757d;padding:.85rem 1rem;border-bottom:1px solid #eee">Status</th>
                        <th style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6c757d;padding:.85rem 1rem;border-bottom:1px solid #eee">Created</th>
                        <th style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6c757d;padding:.85rem 1rem;border-bottom:1px solid #eee"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myProposals as $proposal): ?>
                    <?php $sc = $statusColors[$proposal['status']] ?? ['bg'=>'#f1f3f5','text'=>'#495057']; ?>
                    <tr style="border-bottom:1px solid #f3f4f6">
                        <td style="padding:.85rem 1rem;font-weight:600;color:#111827">
                            <?= htmlspecialchars($proposal['proposal_title']) ?>
                        </td>
                        <td style="padding:.85rem 1rem;color:#374151">
                            <?= htmlspecialchars($proposal['program_type']) ?>
                        </td>
                        <td style="padding:.85rem 1rem;color:#374151">
                            <?= (int)$proposal['num_beneficiaries'] ?>
                        </td>
                        <td style="padding:.85rem 1rem;color:#374151">
                            ₱<?= number_format($proposal['estimated_budget'], 2) ?>
                        </td>
                        <td style="padding:.85rem 1rem">
                            <span class="proposal-status"
                                  style="background:<?= $sc['bg'] ?>;color:<?= $sc['text'] ?>">
                                <?= htmlspecialchars($proposal['status']) ?>
                            </span>
                        </td>
                        <td style="padding:.85rem 1rem;color:#9ca3af;font-size:.82rem">
                            <?= date('M j, Y', strtotime($proposal['created_at'])) ?>
                        </td>
                        <td style="padding:.85rem 1rem">
                            <div class="d-flex gap-1">
                                <a href="index.php?action=viewProposal&proposal_id=<?= $proposal['proposal_id'] ?>"
                                   class="btn-kn-outline btn-kn-sm">
                                    <i class="bi bi-eye"></i>View
                                </a>
                                <?php if (in_array($proposal['status'], ['Draft', 'Rejected'])): ?>
                                <a href="index.php?action=proposalForm&proposal_id=<?= $proposal['proposal_id'] ?>"
                                   class="btn-kn-primary btn-kn-sm">
                                    <i class="bi bi-pencil"></i>Edit
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../templates/committee_chair_layout_end.php'; ?>
