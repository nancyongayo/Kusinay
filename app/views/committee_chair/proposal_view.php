<?php
/**
 * View Feeding Program Proposal Details
 */
$pageTitle = 'Proposal Details';
$activeNav = 'dashboard';
include __DIR__ . '/../templates/committee_chair_layout.php';
include __DIR__ . '/../templates/button_styles.php';
?>

<style>
    .info-label { font-size: .75rem; text-transform: uppercase; color: var(--kn-muted); font-weight: 600; letter-spacing: .05em; }
    .info-value { font-size: 1rem; color: var(--kn-dark); font-weight: 500; }
    
    /* Print-only content - hidden on screen */
    .print-only { display: none !important; }
    
    @media print {
        /* Hide screen content */
        .kn-sidebar, .kn-topbar, nav, .btn, button, a.btn,
        .modal, .alert, .mb-1, .d-flex.gap-2,
        .d-flex.justify-content-between, .card, .row.g-3 {
            display: none !important;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif !important;
            font-size: 11pt !important;
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .kn-main, .kn-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        
        /* Show print content */
        .print-only { display: block !important; }
        
        @page { size: letter portrait; margin: 0; }
    }
</style>

<?php
$canEdit   = in_array($proposal['status'], ['Draft', 'Rejected']);
$canSubmit = in_array($proposal['status'], ['Draft', 'Rejected']);
$canDelete = in_array($proposal['status'], ['Draft', 'Rejected']);

$statusColors = [
    'Draft'      => 'bg-secondary',
    'For Review' => 'bg-warning text-dark',
    'Approved'   => 'bg-success',
    'Rejected'   => 'bg-danger',
];
$statusColor = $statusColors[$proposal['status']] ?? 'bg-secondary';

$validationList = $validations ?? [];
$lastValidation = end($validationList);
?>

<!-- ── Page Header ── -->
<div class="mb-1">
    <a href="index.php?action=committeeChairDashboard"
       class="text-muted text-decoration-none" style="font-size:.85rem">
        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
    </a>
</div>

<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="mb-0" style="color:var(--kn-dark);font-weight:700">
                <?= htmlspecialchars($proposal['proposal_title'] ?? 'Proposal') ?>
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

    <!-- Primary actions — top right -->
    <div class="d-flex gap-2 align-items-center flex-shrink-0 ms-3">
        <?php if ($canEdit): ?>
            <a href="index.php?action=proposalForm&proposal_id=<?= $proposal['proposal_id'] ?>"
               class="btn-kn-primary btn-kn-sm">
                <i class="bi bi-pencil-fill"></i>Edit
            </a>
        <?php endif; ?>

        <?php if ($canSubmit): ?>
            <button type="button" class="btn-kn-success btn-kn-sm"
                    data-bs-toggle="modal" data-bs-target="#submitModal">
                <i class="bi bi-send-fill"></i>
                <?= $proposal['status'] === 'Rejected' ? 'Resubmit' : 'Submit for Review' ?>
            </button>
        <?php endif; ?>

        <!-- Print / Save PDF -->
        <button type="button" onclick="window.print()"
                class="btn-kn-outline btn-kn-sm">
            <i class="bi bi-printer-fill"></i>Print / Save PDF
        </button>

        <?php if ($canDelete): ?>
            <button type="button" class="btn-kn-danger btn-kn-sm"
                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="bi bi-trash-fill"></i>
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Rejection banner — below header, full width, before content -->
<?php if ($proposal['status'] === 'Rejected'): ?>
<div class="alert alert-danger d-flex gap-3 align-items-start mb-4 py-3" role="alert">
    <i class="bi bi-x-octagon-fill fs-5 flex-shrink-0 mt-1"></i>
    <div>
        <div class="fw-semibold mb-1">Proposal Rejected by Barangay Captain</div>
        <?php if ($lastValidation && $lastValidation['feedback']): ?>
            <div style="font-size:.9rem">
                Reason: <em>"<?= htmlspecialchars($lastValidation['feedback']) ?>"</em>
            </div>
        <?php endif; ?>
        <div class="mt-2" style="font-size:.85rem">
            Click <strong>Edit</strong> to revise the proposal, then <strong>Resubmit</strong> for the Captain's approval.
        </div>
    </div>
</div>
<?php endif; ?>

        <!-- ── Submit Modal ── -->
        <div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="submitModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:40px;height:40px;background:rgba(25,135,84,0.12)">
                                <i class="bi bi-send-fill text-success"></i>
                            </div>
                            <h5 class="modal-title mb-0" id="submitModalLabel" style="font-weight:700">
                                Submit for Review
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <p class="mb-1">You are about to submit this proposal to the <strong>Barangay Captain</strong> for review and approval.</p>
                        <div class="rounded p-3 mt-3" style="background:rgba(25,135,84,0.06);border-left:3px solid #198754">
                            <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;font-weight:600">Proposal</div>
                            <div style="font-weight:600"><?= htmlspecialchars($proposal['proposal_title']) ?></div>
                            <div class="text-muted mt-1" style="font-size:.85rem">
                                <?= (int)$proposal['num_beneficiaries'] ?> beneficiaries &nbsp;•&nbsp;
                                ₱<?= number_format($proposal['estimated_budget'], 2) ?>
                            </div>
                        </div>
                        <p class="text-muted mt-3 mb-0" style="font-size:.85rem">
                            <i class="bi bi-info-circle me-1"></i>
                            Once submitted, you will not be able to edit this proposal until the Captain returns it for revision.
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-kn-outline" data-bs-dismiss="modal">Cancel</button>
                        <form method="POST" action="index.php?action=submitProposal" class="d-inline">
                            <?= \Security::csrfField() ?>
                            <input type="hidden" name="proposal_id" value="<?= $proposal['proposal_id'] ?>">
                            <button type="submit" class="btn-kn-success">
                                <i class="bi bi-send-fill"></i>Yes, Submit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Delete Modal ── -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:40px;height:40px;background:rgba(220,53,69,0.12)">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </div>
                            <h5 class="modal-title mb-0" id="deleteModalLabel" style="font-weight:700">
                                Delete Proposal
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <p class="mb-1">Are you sure you want to delete this proposal? <strong>This cannot be undone.</strong></p>
                        <div class="rounded p-3 mt-3" style="background:rgba(220,53,69,0.06);border-left:3px solid #dc3545">
                            <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;font-weight:600">Proposal</div>
                            <div style="font-weight:600"><?= htmlspecialchars($proposal['proposal_title']) ?></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-kn-outline" data-bs-dismiss="modal">Cancel</button>
                        <form method="POST" action="index.php?action=deleteProposal" class="d-inline">
                            <?= \Security::csrfField() ?>
                            <input type="hidden" name="proposal_id" value="<?= $proposal['proposal_id'] ?>">
                            <button type="submit" class="btn-kn-danger">
                                <i class="bi bi-trash-fill"></i>Yes, Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Basic Info -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0" style="font-weight:600">Basic Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-label">Program Type</div>
                                <div class="info-value"><?= htmlspecialchars($proposal['program_type']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Number of Beneficiaries</div>
                                <div class="info-value"><?= (int)$proposal['num_beneficiaries'] ?> children</div>
                            </div>
                            <div class="col-12">
                                <div class="info-label">Target Beneficiaries</div>
                                <div class="info-value"><?= nl2br(htmlspecialchars($proposal['target_beneficiaries'])) ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">BNS Staff</div>
                                <div class="info-value"><?= htmlspecialchars($proposal['bns_first_name'] . ' ' . $proposal['bns_last_name']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Barangay</div>
                                <div class="info-value"><?= htmlspecialchars($proposal['barangay_code']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0" style="font-weight:600">Schedule & Budget</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="info-label">Start Date</div>
                                <div class="info-value"><?= date('M j, Y', strtotime($proposal['start_date'])) ?></div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">End Date</div>
                                <div class="info-value"><?= date('M j, Y', strtotime($proposal['end_date'])) ?></div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Duration</div>
                                <div class="info-value">
                                    <?php
                                    $start = new DateTime($proposal['start_date']);
                                    $end = new DateTime($proposal['end_date']);
                                    $diff = $start->diff($end);
                                    echo $diff->days . ' days';
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Estimated Budget</div>
                                <div class="info-value">₱<?= number_format($proposal['estimated_budget'], 2) ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Funding Source</div>
                                <div class="info-value"><?= htmlspecialchars($proposal['funding_source'] ?? 'Not specified') ?></div>
                            </div>
                            <?php if ($proposal['feeding_schedule']): ?>
                                <div class="col-12">
                                    <div class="info-label">Feeding Schedule</div>
                                    <div class="info-value"><?= htmlspecialchars($proposal['feeding_schedule']) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0" style="font-weight:600">Program Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="info-label">Objectives</div>
                            <div class="info-value"><?= nl2br(htmlspecialchars($proposal['objectives'])) ?></div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Rationale</div>
                            <div class="info-value"><?= nl2br(htmlspecialchars($proposal['rationale'])) ?></div>
                        </div>
                        <?php if ($proposal['implementation_plan']): ?>
                            <div class="mb-3">
                                <div class="info-label">Implementation Plan</div>
                                <div class="info-value"><?= nl2br(htmlspecialchars($proposal['implementation_plan'])) ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if ($proposal['monitoring_plan']): ?>
                            <div class="mb-3">
                                <div class="info-label">Monitoring Plan</div>
                                <div class="info-value"><?= nl2br(htmlspecialchars($proposal['monitoring_plan'])) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Meeting Minutes -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0" style="font-weight:600">Meeting Minutes (<?= count($meetingMinutes ?? []) ?>)</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($meetingMinutes)): ?>
                            <p class="text-muted mb-0" style="font-size:.85rem">No meeting minutes recorded yet.</p>
                        <?php else: ?>
                            <?php foreach ($meetingMinutes as $minute): ?>
                                <div class="mb-2 pb-2 border-bottom">
                                    <div style="font-weight:600;font-size:.9rem">
                                        <?= date('M j, Y', strtotime($minute['meeting_date'])) ?>
                                    </div>
                                    <div style="font-size:.8rem;color:var(--kn-muted)">
                                        <?= htmlspecialchars($minute['meeting_type']) ?> - <?= $minute['num_attendees'] ?> attendees
                                    </div>
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
                            <?php foreach ($validations as $validation): ?>
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <span style="font-weight:600;font-size:.9rem">
                                            <?= htmlspecialchars($validation['validator_first_name'] . ' ' . $validation['validator_last_name']) ?>
                                        </span>
                                        <?php
                                        $decisionColors = [
                                            'Approved' => 'bg-success',
                                            'Rejected' => 'bg-danger',
                                            'Needs Revision' => 'bg-warning text-dark'
                                        ];
                                        $color = $decisionColors[$validation['decision']] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $color ?>" style="font-size:.7rem">
                                            <?= htmlspecialchars($validation['decision']) ?>
                                        </span>
                                    </div>
                                    <div style="font-size:.75rem;color:var(--kn-muted)">
                                        <?= date('M j, Y g:i A', strtotime($validation['validated_at'])) ?>
                                    </div>
                                    <?php if ($validation['feedback']): ?>
                                        <div style="font-size:.85rem;margin-top:.5rem">
                                            <?= nl2br(htmlspecialchars($validation['feedback'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

<!-- Print-only formal document (hidden on screen, shown when printing) -->
<div class="print-only" style="padding:.75in .85in">
    <?php
    $budgetItems = !empty($proposal['budget_items']) ? json_decode($proposal['budget_items'], true) ?? [] : [];
    $barangayName = $proposal['location'] ?? 'Bayabas';
    $numBen = (int)$proposal['num_beneficiaries'];
    $implDays = (int)($proposal['implementation_days'] ?? 120);
    ?>
    
    <!-- Letterhead -->
    <div style="display:flex;align-items:center;justify-content:center;gap:1rem;border-bottom:3px double #000;padding-bottom:.5rem;margin-bottom:1.2rem">
        <div style="width:70px;height:70px;border:2px solid #8B4513;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:9pt;text-align:center;color:#8B4513;font-weight:bold;flex-shrink:0">BRGY<br>SEAL</div>
        <div style="text-align:center;flex:1">
            <div style="font-size:10pt">Republic of the Philippines</div>
            <div style="font-size:11pt;font-weight:bold">City of Davao</div>
            <div style="font-size:14pt;font-weight:bold;text-transform:uppercase">Barangay <?= htmlspecialchars($barangayName) ?></div>
            <div style="font-size:10pt;font-weight:bold;text-transform:uppercase">Office of the Sangguniang Barangay</div>
        </div>
        <div style="width:70px;height:70px;border:2px solid #8B4513;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:9pt;text-align:center;color:#8B4513;font-weight:bold;flex-shrink:0">NNC<br>LOGO</div>
    </div>
    
    <div style="text-align:center;font-size:12pt;font-weight:bold;text-transform:uppercase;margin:1rem 0 1.2rem;text-decoration:underline">
        Project Proposal: <?= htmlspecialchars($proposal['program_type']) ?>
    </div>
    
    <div style="font-size:11pt;font-weight:bold;margin:1rem 0 .4rem">I. Identifying Information</div>
    <ul style="list-style:disc;padding-left:1.5rem;margin-bottom:.5rem;font-size:11pt">
        <li><strong>Project Title:</strong> <?= htmlspecialchars($proposal['proposal_title']) ?></li>
        <li><strong>Proponent:</strong> Committee on Health, Sangguniang Barangay</li>
        <li><strong>Target Beneficiaries:</strong> <?= htmlspecialchars($proposal['target_beneficiaries']) ?></li>
        <li><strong>Implementation Period:</strong> <?= $implDays ?> Days</li>
        <li><strong>Funding Source:</strong> <?= htmlspecialchars($proposal['funding_source'] ?? 'Barangay BCPC Fund') ?></li>
    </ul>
    
    <div style="font-size:11pt;font-weight:bold;margin:1rem 0 .4rem">II. Background and Rationale</div>
    <p style="font-size:11pt;line-height:1.6;text-align:justify"><?= nl2br(htmlspecialchars($proposal['rationale'])) ?></p>
    
    <div style="font-size:11pt;font-weight:bold;margin:1rem 0 .4rem">III. Project Goals and Objectives</div>
    <p style="font-size:11pt;line-height:1.6;text-align:justify"><?= nl2br(htmlspecialchars($proposal['objectives'])) ?></p>
    
    <div style="font-size:11pt;font-weight:bold;margin:1rem 0 .4rem">IV. Budgetary Requirements</div>
    
    <?php if (!empty($budgetItems)): ?>
    <!-- Budget Table -->
    <table style="width:100%;border-collapse:collapse;margin:1rem 0;font-size:10pt">
        <thead>
            <tr style="background:#f0f0f0">
                <th style="border:1px solid #000;padding:.4rem;text-align:left">Item Description</th>
                <th style="border:1px solid #000;padding:.4rem;text-align:center">Daily Cost per Child</th>
                <th style="border:1px solid #000;padding:.4rem;text-align:center">Computation (Rate x <?= $numBen ?> Children x <?= $implDays ?> Days)</th>
                <th style="border:1px solid #000;padding:.4rem;text-align:right">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($budgetItems as $item): ?>
            <tr>
                <td style="border:1px solid #000;padding:.4rem"><?= htmlspecialchars($item['item']) ?></td>
                <td style="border:1px solid #000;padding:.4rem;text-align:center">₱<?= number_format($item['daily_cost'], 2) ?></td>
                <td style="border:1px solid #000;padding:.4rem;text-align:center">₱<?= number_format($item['daily_cost'], 2) ?> x <?= $numBen ?> x <?= $implDays ?></td>
                <td style="border:1px solid #000;padding:.4rem;text-align:right">₱<?= number_format($item['total'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr style="font-weight:bold;background:#f8f8f8">
                <td colspan="3" style="border:1px solid #000;padding:.4rem;text-align:right">TOTAL:</td>
                <td style="border:1px solid #000;padding:.4rem;text-align:right">₱<?= number_format($proposal['estimated_budget'], 2) ?></td>
            </tr>
        </tbody>
    </table>
    <?php else: ?>
    <p style="font-size:11pt">Estimated Budget: <strong>₱<?= number_format($proposal['estimated_budget'], 2) ?></strong></p>
    <?php endif; ?>
    
    <!-- Signature Section -->
    <div style="margin-top:3rem;display:flex;gap:3rem;page-break-inside:avoid">
        <!-- Prepared by -->
        <div style="flex:1">
            <div style="font-size:11pt;font-weight:bold;margin-bottom:1rem">Prepared by:</div>
            <?php if (!empty($proposal['signature_data'])): ?>
                <img src="<?= htmlspecialchars($proposal['signature_data']) ?>" 
                     alt="Digital Signature" 
                     style="height:50px;display:block;margin-bottom:.3rem">
            <?php else: ?>
                <div style="height:50px"></div>
            <?php endif; ?>
            <div style="border-top:1px solid #000;padding-top:.3rem;margin-top:.5rem">
                <div style="font-size:11pt;font-weight:bold"><?= htmlspecialchars($proposal['creator_first_name'] . ' ' . $proposal['creator_last_name']) ?></div>
                <div style="font-size:10pt">Vice Chairperson, Committee on Health</div>
            </div>
        </div>
        
        <!-- Approved by -->
        <div style="flex:1">
            <div style="font-size:11pt;font-weight:bold;margin-bottom:1rem">Approved by:</div>
            <?php
            // Check if there's a validation with signature
            $captainSignature = null;
            $captainName = '';
            if (!empty($validations)) {
                foreach ($validations as $v) {
                    if ($v['decision'] === 'Approved' && !empty($v['signature_data'])) {
                        $captainSignature = $v['signature_data'];
                        $captainName = $v['validator_first_name'] . ' ' . $v['validator_last_name'];
                        break;
                    }
                }
            }
            ?>
            <?php if ($captainSignature): ?>
                <img src="<?= htmlspecialchars($captainSignature) ?>" 
                     alt="Captain Signature" 
                     style="height:50px;display:block;margin-bottom:.3rem">
            <?php else: ?>
                <div style="height:50px"></div>
            <?php endif; ?>
            <div style="border-top:1px solid #000;padding-top:.3rem;margin-top:.5rem">
                <div style="font-size:11pt;font-weight:bold"><?= $captainName ?: '_______________________________' ?></div>
                <div style="font-size:10pt">Chairperson, Punong Barangay</div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/committee_chair_layout_end.php'; ?>
