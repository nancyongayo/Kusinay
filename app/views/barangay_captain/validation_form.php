<?php
/**
 * Process 14: Barangay Captain Validates Feeding Program Proposal
 * Reviews full proposal + meeting minutes, then signs
 */
$pageTitle = 'Validate Proposal';
$activeNav = 'dashboard';
include __DIR__ . '/../templates/barangay_captain_layout.php';
?>

<style>
    #signatureCanvas {
        border: 2px dashed #adb5bd;
        border-radius: .5rem;
        cursor: crosshair;
        background: #fff;
        display: block;
        width: 100%;
        max-width: 600px;
    }
    .info-label { font-size:.75rem;text-transform:uppercase;color:var(--kn-muted);font-weight:600;letter-spacing:.05em; }
    .info-value  { font-size:.95rem;color:var(--kn-dark);font-weight:500; }
    .section-title { font-weight:700;color:var(--kn-dark);border-bottom:2px solid var(--kn-primary);padding-bottom:.4rem;margin-bottom:1rem; }
</style>

<div class="row g-4">

    <!-- LEFT: Full Proposal Review -->
    <div class="col-lg-7">

        <!-- Proposal Header -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;font-weight:600">Project Proposal</div>
                        <h5 class="mb-1 mt-1" style="font-weight:700;color:var(--kn-dark)">
                            <?= htmlspecialchars($proposal['proposal_title']) ?>
                        </h5>
                        <small class="text-muted">
                            Submitted by <?= htmlspecialchars($proposal['creator_first_name'] . ' ' . $proposal['creator_last_name']) ?>
                            on <?= $proposal['submitted_at'] ? date('F j, Y', strtotime($proposal['submitted_at'])) : 'N/A' ?>
                        </small>
                    </div>
                    <span class="badge bg-warning text-dark" style="font-size:.85rem;padding:.4rem .8rem">
                        For Review
                    </span>
                </div>
            </div>
        </div>

        <!-- I. Identifying Information -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 section-title">I. Identifying Information</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-label">Project Title</div>
                        <div class="info-value"><?= htmlspecialchars($proposal['proposal_title']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Proponent</div>
                        <div class="info-value"><?= htmlspecialchars($proposal['proponent'] ?? 'Committee on Health, Sangguniang Barangay') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Location</div>
                        <div class="info-value"><?= htmlspecialchars($proposal['location'] ?? $proposal['barangay_code']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Target Beneficiaries</div>
                        <div class="info-value"><?= htmlspecialchars($proposal['target_beneficiaries']) ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Implementation Period</div>
                        <div class="info-value"><?= htmlspecialchars($proposal['implementation_days'] ?? '120') ?> Days</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Start Date</div>
                        <div class="info-value"><?= date('F j, Y', strtotime($proposal['start_date'])) ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">End Date</div>
                        <div class="info-value"><?= date('F j, Y', strtotime($proposal['end_date'])) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Funding Source</div>
                        <div class="info-value"><?= htmlspecialchars($proposal['funding_source'] ?? 'N/A') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Program Type</div>
                        <div class="info-value"><?= htmlspecialchars($proposal['program_type']) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- II. Background and Rationale -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 section-title">II. Background and Rationale</h6>
            </div>
            <div class="card-body">
                <p style="font-size:.9rem;line-height:1.7"><?= nl2br(htmlspecialchars($proposal['rationale'])) ?></p>
            </div>
        </div>

        <!-- III. Project Description -->
        <?php if (!empty($proposal['implementation_plan'])): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 section-title">III. Project Description</h6>
            </div>
            <div class="card-body">
                <p style="font-size:.9rem;line-height:1.7"><?= nl2br(htmlspecialchars($proposal['implementation_plan'])) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- IV. Goals and Objectives -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 section-title">IV. Project Goals and Objectives</h6>
            </div>
            <div class="card-body">
                <p style="font-size:.9rem;line-height:1.7"><?= nl2br(htmlspecialchars($proposal['objectives'])) ?></p>
            </div>
        </div>

        <!-- V. Budgetary Requirements -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 section-title">V. Budgetary Requirements</h6>
            </div>
            <div class="card-body">
                <?php
                $budgetItems = [];
                if (!empty($proposal['budget_items'])) {
                    $budgetItems = json_decode($proposal['budget_items'], true) ?? [];
                }
                ?>
                <?php if (!empty($budgetItems)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered" style="font-size:.9rem">
                        <thead style="background:rgba(107,122,58,0.08)">
                            <tr>
                                <th>Item Description</th>
                                <th class="text-center">Daily Cost per Child</th>
                                <th class="text-center">Computation</th>
                                <th class="text-center">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($budgetItems as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['item']) ?></td>
                                <td class="text-center">₱<?= number_format($item['daily_cost'], 2) ?></td>
                                <td class="text-center" style="font-size:.8rem;color:var(--kn-muted)">
                                    ₱<?= number_format($item['daily_cost'], 2) ?> × <?= (int)$proposal['num_beneficiaries'] ?> × <?= htmlspecialchars($proposal['implementation_days'] ?? '120') ?>
                                </td>
                                <td class="text-center fw-bold">₱<?= number_format($item['total'] ?? 0, 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot style="background:rgba(107,122,58,0.05)">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">TOTAL:</td>
                                <td class="text-center fw-bold" style="color:var(--kn-primary)">
                                    ₱<?= number_format($proposal['estimated_budget'], 2) ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-3">
                    <div class="text-muted">Estimated Budget: <strong>₱<?= number_format($proposal['estimated_budget'], 2) ?></strong></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Meeting Minutes (Optional - for reference only) -->
        <?php if (!empty($meetingMinutes)): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 section-title">📝 Minutes of the Meeting (<?= count($meetingMinutes) ?>)</h6>
            </div>
            <?php foreach ($meetingMinutes as $minute): ?>
            <div class="card-body border-bottom">
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <div class="info-label">Date</div>
                        <div class="info-value"><?= date('F j, Y', strtotime($minute['meeting_date'])) ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Time</div>
                        <div class="info-value"><?= date('g:i A', strtotime($minute['meeting_time'])) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Venue</div>
                        <div class="info-value"><?= htmlspecialchars($minute['venue']) ?></div>
                    </div>
                </div>

                <?php if (!empty($minute['attendees'])): ?>
                <div class="mb-3">
                    <div class="info-label mb-1">Attendance</div>
                    <?php
                    $attendees = json_decode($minute['attendees'], true) ?? [];
                    if (!empty($attendees)):
                    ?>
                    <ul class="mb-0" style="font-size:.9rem">
                        <?php foreach ($attendees as $att): ?>
                            <li><?= htmlspecialchars($att['name'] ?? '') ?> — <em><?= htmlspecialchars($att['role'] ?? '') ?></em></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="mb-2">
                    <div class="info-label">Discussion Summary</div>
                    <p style="font-size:.9rem;line-height:1.6"><?= nl2br(htmlspecialchars($minute['discussion_summary'])) ?></p>
                </div>
                <div class="mb-2">
                    <div class="info-label">Decisions Made</div>
                    <p style="font-size:.9rem;line-height:1.6"><?= nl2br(htmlspecialchars($minute['decisions_made'])) ?></p>
                </div>
                <div class="text-muted" style="font-size:.8rem">
                    Prepared by: <?= htmlspecialchars($minute['recorder_first_name'] . ' ' . $minute['recorder_last_name']) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div><!-- end col-lg-7 -->

    <!-- RIGHT: Validation Panel -->
    <div class="col-lg-5">
        <div class="sticky-top" style="top:80px">

            <!-- Previous Validations -->
            <?php if (!empty($validations)): ?>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0" style="font-weight:600">Previous Decisions</h6>
                </div>
                <div class="card-body">
                    <?php foreach ($validations as $v): ?>
                    <div class="mb-2 pb-2 border-bottom">
                        <?php
                        $dc = ['Approved'=>'bg-success','Rejected'=>'bg-danger','Needs Revision'=>'bg-warning text-dark'];
                        $dc = $dc[$v['decision']] ?? 'bg-secondary';
                        ?>
                        <span class="badge <?= $dc ?>"><?= htmlspecialchars($v['decision']) ?></span>
                        <span class="text-muted ms-2" style="font-size:.8rem">
                            <?= date('M j, Y', strtotime($v['validated_at'])) ?>
                        </span>
                        <?php if ($v['feedback']): ?>
                            <p class="mb-0 mt-1" style="font-size:.85rem"><?= nl2br(htmlspecialchars($v['feedback'])) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Validation Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0" style="font-weight:700;color:var(--kn-dark)">
                        ✍️ Validation — Process 14
                    </h6>
                    <small class="text-muted">Feedback/Signature by Barangay Captain</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?action=submitValidation" id="validationForm">
                        <?= \Security::csrfField() ?>
                        <input type="hidden" name="proposal_id" value="<?= $proposal['proposal_id'] ?>">
                        <input type="hidden" name="signature_data" id="signatureData">
                        <input type="hidden" name="signature_type" value="drawn">

                        <!-- Decision -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Decision <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="decision" id="decApprove" value="Approved" required>
                                    <label class="form-check-label text-success fw-semibold" for="decApprove">✅ Approve</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="decision" id="decRevise" value="Needs Revision">
                                    <label class="form-check-label text-warning fw-semibold" for="decRevise">🔄 Needs Revision</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="decision" id="decReject" value="Rejected">
                                    <label class="form-check-label text-danger fw-semibold" for="decReject">❌ Reject</label>
                                </div>
                            </div>
                        </div>

                        <!-- Feedback -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Feedback / Comments
                                <span class="text-danger" id="feedbackRequired" style="display:none">*</span>
                            </label>
                            <textarea class="form-control" name="feedback" id="feedbackField" rows="3"
                                      placeholder="Provide your feedback or reason for decision..."></textarea>
                            <div class="invalid-feedback" id="feedbackError">
                                Feedback is required when rejecting or requesting revision.
                            </div>
                        </div>

                        <!-- Digital Signature — hidden when Rejected -->
                        <div class="mb-3" id="signatureSection">
                            <label class="form-label fw-semibold">
                                Digital Signature <span class="text-danger">*</span>
                            </label>
                            <p class="text-muted mb-2" style="font-size:.8rem">
                                Sign below using your mouse or finger (touchscreen):
                            </p>
                            <canvas id="signatureCanvas" width="500" height="150"></canvas>
                            <div id="sigError" class="text-danger mt-1" style="font-size:.85rem;display:none">
                                <i class="bi bi-exclamation-circle me-1"></i>Please provide your digital signature before submitting.
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSig">
                                    🗑️ Clear
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">
                                Signature is recorded with your IP and timestamp for audit.
                            </small>
                        </div>

                        <!-- Rejection note — shown only when Rejected -->
                        <div class="alert alert-warning mb-3" id="rejectNote" style="display:none">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Rejection</strong> — No signature required. The proposal will be returned to the Committee Chair for revision or re-submission.
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-check-circle-fill me-2"></i>Submit Validation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div><!-- sticky-top -->
    </div><!-- col-lg-5 -->

</div><!-- row -->

<script>
// ── Signature Pad ──────────────────────────────────────────────────────────
const canvas = document.getElementById('signatureCanvas');
const ctx    = canvas.getContext('2d');
let drawing  = false, hasSig = false;

ctx.strokeStyle = '#1a1a2e';
ctx.lineWidth   = 2.5;
ctx.lineCap     = 'round';
ctx.lineJoin    = 'round';

function getPos(e) {
    const r = canvas.getBoundingClientRect();
    const src = e.touches ? e.touches[0] : e;
    return { x: src.clientX - r.left, y: src.clientY - r.top };
}

canvas.addEventListener('mousedown',  e => { drawing = true; ctx.beginPath(); const p = getPos(e); ctx.moveTo(p.x, p.y); });
canvas.addEventListener('mousemove',  e => { if (!drawing) return; hasSig = true; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
canvas.addEventListener('mouseup',    () => drawing = false);
canvas.addEventListener('mouseleave', () => drawing = false);
canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing = true; ctx.beginPath(); const p = getPos(e); ctx.moveTo(p.x, p.y); }, {passive:false});
canvas.addEventListener('touchmove',  e => { e.preventDefault(); if (!drawing) return; hasSig = true; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); }, {passive:false});
canvas.addEventListener('touchend',   () => drawing = false);

document.getElementById('clearSig').addEventListener('click', () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    hasSig = false;
    document.getElementById('signatureData').value = '';
    document.getElementById('sigError').style.display = 'none';
});

// ── Decision change handler ────────────────────────────────────────────────
document.querySelectorAll('input[name="decision"]').forEach(r => {
    r.addEventListener('change', () => onDecisionChange(r.value));
});

function onDecisionChange(val) {
    const sigSection     = document.getElementById('signatureSection');
    const rejectNote     = document.getElementById('rejectNote');
    const feedbackReq    = document.getElementById('feedbackRequired');
    const submitBtn      = document.getElementById('submitBtn');

    // Signature: ONLY required for Approve
    if (val === 'Approved') {
        sigSection.style.display      = 'block';
        rejectNote.style.display      = 'none';
        feedbackReq.style.display     = 'none';
        submitBtn.className = 'btn btn-success btn-lg';
        submitBtn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Approve Proposal';

    } else if (val === 'Needs Revision') {
        sigSection.style.display      = 'none';
        rejectNote.style.display      = 'none';
        feedbackReq.style.display     = 'inline';
        submitBtn.className = 'btn btn-warning btn-lg text-dark';
        submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Request Revision';

    } else if (val === 'Rejected') {
        sigSection.style.display      = 'none';
        rejectNote.style.display      = 'block';
        feedbackReq.style.display     = 'inline';
        submitBtn.className = 'btn btn-danger btn-lg';
        submitBtn.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i>Confirm Rejection';
    }
}

// ── Form submit ────────────────────────────────────────────────────────────
document.getElementById('validationForm').addEventListener('submit', function(e) {
    const decision  = document.querySelector('input[name="decision"]:checked')?.value;
    const feedback  = document.getElementById('feedbackField').value.trim();
    const sigError  = document.getElementById('sigError');
    let valid = true;

    // Feedback required for Reject and Needs Revision
    if ((decision === 'Rejected' || decision === 'Needs Revision') && !feedback) {
        document.getElementById('feedbackField').classList.add('is-invalid');
        document.getElementById('feedbackError').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('feedbackField').classList.remove('is-invalid');
        document.getElementById('feedbackError').style.display = 'none';
    }

    // Signature required ONLY for Approve
    if (decision === 'Approved' && !hasSig) {
        sigError.style.display = 'block';
        canvas.style.borderColor = '#dc3545';
        canvas.scrollIntoView({ behavior: 'smooth', block: 'center' });
        valid = false;
    } else {
        sigError.style.display = 'none';
        canvas.style.borderColor = '';
    }

    if (!valid) { e.preventDefault(); return false; }

    // Attach signature only for Approve
    document.getElementById('signatureData').value =
        decision === 'Approved' ? canvas.toDataURL('image/png') : '';
});
</script>

<?php include __DIR__ . '/../templates/barangay_captain_layout_end.php'; ?>
