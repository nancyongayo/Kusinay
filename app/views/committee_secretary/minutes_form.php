<?php
/**
 * Process 13 (Step 1): Committee Secretary Records Minutes of the Meeting
 * Standalone — no proposal needed. Chair reviews and creates proposal after.
 */
$pageTitle = 'Record Meeting Minutes';
$activeNav = 'record';
include __DIR__ . '/../templates/committee_secretary_layout.php';
include __DIR__ . '/../templates/button_styles.php';

$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

// If linked to a proposal (optional)
$linkedProposal = $proposal ?? null;
?>

<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-1" style="color:var(--kn-dark);font-weight:700">📝 Record Meeting Minutes</h4>
        <p class="text-muted mb-0" style="font-size:.9rem">
            Record the BNC planning meeting minutes. The Committee Chair will review these and create a feeding program proposal if needed.
        </p>
    </div>
</div>

<?php if ($linkedProposal): ?>
<!-- Linked to an existing proposal -->
<div class="alert alert-info mb-4">
    <i class="bi bi-link-45deg me-2"></i>
    These minutes will be linked to proposal: <strong><?= htmlspecialchars($linkedProposal['proposal_title']) ?></strong>
</div>
<?php endif; ?>

<form method="POST" action="index.php?action=saveMeetingMinutes">
    <?= \Security::csrfField() ?>
    <!-- proposal_id is optional — only set if linking to existing proposal -->
    <input type="hidden" name="proposal_id" value="<?= $linkedProposal['proposal_id'] ?? '' ?>">

    <!-- Meeting Header Info -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0" style="font-weight:700;color:var(--kn-dark)">Meeting Information</h6>
            <small class="text-muted">What / When / Where / Who</small>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-kn">Meeting About / Subject <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-kn" name="agenda"
                           value="<?= htmlspecialchars($formData['agenda'] ?? '') ?>"
                           placeholder="e.g., Regular nga Meeting sa Barangay Nutrition Council (BNC)"
                           required>
                </div>
                <div class="col-md-3">
                    <label class="form-label-kn">Meeting Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-kn" name="meeting_date"
                           value="<?= htmlspecialchars($formData['meeting_date'] ?? '') ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label-kn">Time <span class="text-danger">*</span></label>
                    <input type="time" class="form-control form-control-kn" name="meeting_time"
                           value="<?= htmlspecialchars($formData['meeting_time'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-kn">Venue / Where <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-kn" name="venue"
                           value="<?= htmlspecialchars($formData['venue'] ?? '') ?>"
                           placeholder="e.g., Barangay Bayabas Health Center"
                           required>
                </div>
                <div class="col-md-3">
                    <label class="form-label-kn">Meeting Type</label>
                    <select class="form-select form-select-kn" name="meeting_type">
                        <?php foreach (['Planning','Review','Monitoring','Evaluation','Other'] as $t): ?>
                            <option value="<?= $t ?>" <?= ($formData['meeting_type'] ?? 'Planning') === $t ? 'selected' : '' ?>>
                                <?= $t ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label-kn">Next Meeting Date</label>
                    <input type="date" class="form-control form-control-kn" name="next_meeting_date"
                           value="<?= htmlspecialchars($formData['next_meeting_date'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- I. Attendance -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0" style="font-weight:700;color:var(--kn-dark)">I. Attendance</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive mb-3">
                <table class="table table-bordered" id="attendanceTable">
                    <thead style="background:rgba(107,122,58,0.08)">
                        <tr>
                            <th style="font-size:.85rem">Name</th>
                            <th style="font-size:.85rem">Position / Role</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody id="attendanceRows">
                        <?php
                        $existingAttendees = [];
                        if (!empty($formData['attendee_name'])) {
                            foreach ($formData['attendee_name'] as $i => $name) {
                                $existingAttendees[] = [
                                    'name' => $name,
                                    'role' => $formData['attendee_role'][$i] ?? ''
                                ];
                            }
                        }
                        if (empty($existingAttendees)) {
                            $existingAttendees = [
                                ['name'=>'','role'=>'Punong Barangay / BNC Chairperson'],
                                ['name'=>'','role'=>'Committee on Health / BNC Vice-Chairperson'],
                                ['name'=>'','role'=>'Barangay Nutrition Scholar (BNS)'],
                            ];
                        }
                        foreach ($existingAttendees as $att): ?>
                        <tr class="attendee-row">
                            <td>
                                <input type="text" class="form-control form-control-sm"
                                       name="attendee_name[]"
                                       value="<?= htmlspecialchars($att['name']) ?>"
                                       placeholder="Full name">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm"
                                       name="attendee_role[]"
                                       value="<?= htmlspecialchars($att['role']) ?>"
                                       placeholder="Position or role">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-attendee">×</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn-kn-outline btn-kn-sm" id="addAttendee">
                <i class="bi bi-plus-circle"></i>Add Attendee
            </button>
            <input type="hidden" name="num_attendees" id="numAttendees" value="<?= count($existingAttendees) ?>">
        </div>
    </div>

    <!-- II. Agenda / Discussion -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0" style="font-weight:700;color:var(--kn-dark)">II. Agenda & Discussion <span class="text-danger">*</span></h6>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label-kn">Discussion Summary <span class="text-danger">*</span></label>
                <textarea class="form-control form-control-kn" name="discussion_summary" rows="6" required
                          placeholder="Summarize what was discussed during the meeting..."><?= htmlspecialchars($formData['discussion_summary'] ?? '') ?></textarea>
                <small class="text-muted">Include key points from the OPT screening results, budget discussion, and program plans.</small>
            </div>
        </div>
    </div>

    <!-- III. Decisions / Main Points -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0" style="font-weight:700;color:var(--kn-dark)">III. Main Points / Decisions Made <span class="text-danger">*</span></h6>
        </div>
        <div class="card-body">
            <textarea class="form-control form-control-kn" name="decisions_made" rows="5" required
                      placeholder="List the decisions and agreements made during the meeting..."><?= htmlspecialchars($formData['decisions_made'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- IV. Action Items -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0" style="font-weight:700;color:var(--kn-dark)">IV. Action Items</h6>
        </div>
        <div class="card-body">
            <textarea class="form-control form-control-kn" name="action_items" rows="4"
                      placeholder="Tasks assigned, responsible persons, and deadlines..."><?= htmlspecialchars($formData['action_items'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- V. Digital Signature -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0" style="font-weight:700;color:var(--kn-dark)">
                <i class="bi bi-pen-fill"></i> V. Digital Signature <span class="text-danger">*</span>
            </h6>
            <small class="text-muted">Sign below to certify these minutes</small>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label-kn">Committee Secretary Signature</label>
                <div style="border:2px solid var(--kn-primary);border-radius:8px;background:#fff;position:relative">
                    <canvas id="signatureCanvas" 
                            width="600" 
                            height="200" 
                            style="display:block;width:100%;max-width:600px;height:200px;cursor:crosshair;touch-action:none">
                    </canvas>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#ccc;pointer-events:none;font-size:.9rem" id="signaturePlaceholder">
                        Sign here using your mouse or touchscreen
                    </div>
                </div>
                <div class="d-flex gap-2 mt-2">
                    <button type="button" class="btn-kn-outline btn-kn-sm" id="clearSignature">
                        <i class="bi bi-x-circle"></i> Clear Signature
                    </button>
                    <small class="text-muted align-self-center">Your signature will be embedded in the printed minutes</small>
                </div>
                <input type="hidden" name="signature_data" id="signatureData" required>
                <div class="invalid-feedback" id="signatureError" style="display:none">
                    Please provide your signature before submitting.
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn-kn-primary">
            <i class="bi bi-save-fill"></i>Save Minutes
        </button>
        <a href="index.php?action=secretaryDashboard" class="btn-kn-outline">Cancel</a>
    </div>

<script>
// Add attendee row
document.getElementById('addAttendee').addEventListener('click', () => {
    const tbody = document.getElementById('attendanceRows');
    const row = document.querySelector('.attendee-row').cloneNode(true);
    row.querySelectorAll('input').forEach(i => i.value = '');
    tbody.appendChild(row);
    updateCount();
});

// Remove attendee row
document.addEventListener('click', e => {
    if (e.target.classList.contains('remove-attendee')) {
        const rows = document.querySelectorAll('.attendee-row');
        if (rows.length > 1) { e.target.closest('tr').remove(); updateCount(); }
    }
});

function updateCount() {
    document.getElementById('numAttendees').value =
        document.querySelectorAll('.attendee-row').length;
}

// ============================================================================
// Digital Signature Canvas
// ============================================================================
const canvas = document.getElementById('signatureCanvas');
const ctx = canvas.getContext('2d');
const signatureData = document.getElementById('signatureData');
const clearBtn = document.getElementById('clearSignature');
const placeholder = document.getElementById('signaturePlaceholder');
const signatureError = document.getElementById('signatureError');

let isDrawing = false;
let hasSignature = false;

// Set canvas resolution for crisp lines
const rect = canvas.getBoundingClientRect();
canvas.width = rect.width * 2;
canvas.height = rect.height * 2;
ctx.scale(2, 2);

// Drawing settings
ctx.strokeStyle = '#000';
ctx.lineWidth = 2;
ctx.lineCap = 'round';
ctx.lineJoin = 'round';

// Get coordinates relative to canvas
function getCoordinates(e) {
    const rect = canvas.getBoundingClientRect();
    const x = (e.clientX || e.touches[0].clientX) - rect.left;
    const y = (e.clientY || e.touches[0].clientY) - rect.top;
    return { x, y };
}

// Start drawing
function startDrawing(e) {
    e.preventDefault();
    isDrawing = true;
    const { x, y } = getCoordinates(e);
    ctx.beginPath();
    ctx.moveTo(x, y);
    placeholder.style.display = 'none';
}

// Draw
function draw(e) {
    if (!isDrawing) return;
    e.preventDefault();
    const { x, y } = getCoordinates(e);
    ctx.lineTo(x, y);
    ctx.stroke();
    hasSignature = true;
}

// Stop drawing and save
function stopDrawing(e) {
    if (!isDrawing) return;
    e.preventDefault();
    isDrawing = false;
    ctx.closePath();
    
    // Save signature as base64
    if (hasSignature) {
        signatureData.value = canvas.toDataURL('image/png');
        signatureError.style.display = 'none';
    }
}

// Clear signature
clearBtn.addEventListener('click', () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    signatureData.value = '';
    hasSignature = false;
    placeholder.style.display = 'block';
});

// Mouse events
canvas.addEventListener('mousedown', startDrawing);
canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseup', stopDrawing);
canvas.addEventListener('mouseleave', stopDrawing);

// Touch events for mobile
canvas.addEventListener('touchstart', startDrawing);
canvas.addEventListener('touchmove', draw);
canvas.addEventListener('touchend', stopDrawing);

// Form validation
document.querySelector('form').addEventListener('submit', (e) => {
    if (!hasSignature || !signatureData.value) {
        e.preventDefault();
        signatureError.style.display = 'block';
        canvas.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
});
</script>

<?php include __DIR__ . '/../templates/committee_secretary_layout_end.php'; ?>
