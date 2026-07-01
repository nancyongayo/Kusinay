<?php
/**
 * Process 13: Committee Chair Creates Feeding Program Proposal
 * Based on actual Project Proposal: Supplementary Feeding Program document
 */
$pageTitle = $isEdit ? 'Edit Proposal' : 'Create Feeding Program Proposal';
$activeNav = 'feeding_program';

$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

$proposalId = $proposal['proposal_id'] ?? 0;

// Decode budget items if editing
$budgetItems = [];
if (!empty($proposal['budget_items'])) {
    $budgetItems = json_decode($proposal['budget_items'], true) ?? [];
}
if (empty($budgetItems)) {
    $budgetItems = [
        ['item' => 'Rice / Malagkit',       'daily_cost' => ''],
        ['item' => 'Protein (Chicken/Egg/Meat)', 'daily_cost' => ''],
        ['item' => 'Vegetables & Condiments','daily_cost' => ''],
    ];
}

include __DIR__ . '/../templates/committee_chair_layout.php';
include __DIR__ . '/../templates/button_styles.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-1" style="color:var(--kn-dark);font-weight:700">
            📋 <?= $isEdit ? 'Edit' : 'Create' ?> Feeding Program Proposal
        </h4>
        <p class="text-muted mb-0" style="font-size:.9rem">
            Project Proposal: Supplementary Feeding Program — Office of the Sangguniang Barangay
        </p>
    </div>
</div>

<form method="POST" action="index.php?action=saveProposal" class="needs-validation" novalidate>
    <?= \Security::csrfField() ?>
    <input type="hidden" name="proposal_id" value="<?= $proposalId ?>">
    <input type="hidden" name="affected_children_data" value="<?= htmlspecialchars($_POST['affected_children_data'] ?? $proposal['affected_children_data'] ?? '') ?>">

    <!-- I. Identifying Information -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0" style="font-weight:700;color:var(--kn-dark)">I. Identifying Information</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Project Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="proposal_title"
                           value="<?= htmlspecialchars($proposal['proposal_title'] ?? $formData['proposal_title'] ?? '') ?>"
                           placeholder="e.g., Supplementary Feeding Program for Malnourished Children"
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Program Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="program_type" required>
                        <option value="">Select type...</option>
                        <?php foreach (['Supplementary Feeding','Therapeutic Feeding','School Feeding','Community Kitchen','Other'] as $t): ?>
                            <option value="<?= $t ?>" <?= ($proposal['program_type'] ?? $formData['program_type'] ?? '') === $t ? 'selected' : '' ?>>
                                <?= $t ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Proponent</label>
                    <input type="text" class="form-control" name="proponent"
                           value="<?= htmlspecialchars($proposal['proponent'] ?? $formData['proponent'] ?? 'Committee on Health, Sangguniang Barangay') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Location / Venue</label>
                    <input type="text" class="form-control" name="location"
                           value="<?= htmlspecialchars($proposal['location'] ?? $formData['location'] ?? '') ?>"
                           placeholder="e.g., Barangay Health Center, Session Hall">
                </div>

                <!-- BNS Staff field removed -->
                <!--
                <div class="col-md-6">
                    <label class="form-label fw-semibold">BNS Staff <span class="text-danger">*</span></label>
                    <select class="form-select" name="bns_id" required>
                        <option value="">Select BNS...</option>
                        <?php foreach ($bnsList ?? [] as $bns): ?>
                            <option value="<?= $bns['user_id'] ?>"
                                <?= ($proposal['bns_id'] ?? $formData['bns_id'] ?? '') == $bns['user_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($bns['first_name'] . ' ' . $bns['last_name']) ?>
                                <?= $bns['barangay_code'] ? ' — ' . htmlspecialchars($bns['barangay_code']) : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                -->

                <!-- Barangay Code field removed -->
                <!--
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Barangay Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="barangay_code"
                           value="<?= htmlspecialchars($proposal['barangay_code'] ?? $formData['barangay_code'] ?? $_SESSION['barangay_code'] ?? '') ?>"
                           required>
                </div>
                -->

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Target Beneficiaries (count) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="num_beneficiaries" id="numBeneficiaries"
                           value="<?= htmlspecialchars($proposal['num_beneficiaries'] ?? $formData['num_beneficiaries'] ?? '') ?>"
                           min="1" required>
                    <small class="text-muted">Total number of children</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Implementation Period (days) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="implementation_days" id="implementationDays"
                           value="<?= htmlspecialchars($proposal['implementation_days'] ?? $formData['implementation_days'] ?? '120') ?>"
                           min="1" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="start_date" id="startDate"
                           value="<?= htmlspecialchars($proposal['start_date'] ?? $formData['start_date'] ?? '') ?>"
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="end_date" id="endDate"
                           value="<?= htmlspecialchars($proposal['end_date'] ?? $formData['end_date'] ?? '') ?>"
                           required>
                    <small class="text-muted">Auto-calculated based on start date + implementation days</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Funding Source</label>
                    <input type="text" class="form-control" name="funding_source"
                           value="<?= htmlspecialchars($proposal['funding_source'] ?? $formData['funding_source'] ?? 'Barangay BCPC Fund') ?>"
                           placeholder="e.g., Barangay BCPC Fund">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Feeding Schedule</label>
                    <input type="text" class="form-control" name="feeding_schedule"
                           value="<?= htmlspecialchars($proposal['feeding_schedule'] ?? $formData['feeding_schedule'] ?? '') ?>"
                           placeholder="e.g., Mon–Fri, 10:00 AM – 11:00 AM">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Target Beneficiaries Description <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="target_beneficiaries"
                           value="<?= htmlspecialchars($proposal['target_beneficiaries'] ?? $formData['target_beneficiaries'] ?? '') ?>"
                           placeholder="e.g., 20 Children (11 Boys and 9 Girls)"
                           required>
                </div>
            </div>
        </div>
    </div>

    <!-- II. Background and Rationale -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0" style="font-weight:700;color:var(--kn-dark)">II. Background and Rationale <span class="text-danger">*</span></h6>
        </div>
        <div class="card-body">
            <textarea class="form-control" name="rationale" rows="5" required
                      placeholder="Describe the nutrition situation and why this program is needed..."><?= htmlspecialchars($proposal['rationale'] ?? $formData['rationale'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- III. Project Description -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0" style="font-weight:700;color:var(--kn-dark)">III. Project Description</h6>
        </div>
        <div class="card-body">
            <textarea class="form-control" name="implementation_plan" rows="5"
                      placeholder="Describe how the program will be implemented..."><?= htmlspecialchars($proposal['implementation_plan'] ?? $formData['implementation_plan'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- IV. Goals and Objectives -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0" style="font-weight:700;color:var(--kn-dark)">IV. Project Goals and Objectives <span class="text-danger">*</span></h6>
        </div>
        <div class="card-body">
            <textarea class="form-control" name="objectives" rows="5" required
                      placeholder="List the goals and objectives of the program..."><?= htmlspecialchars($proposal['objectives'] ?? $formData['objectives'] ?? '') ?></textarea>
            <small class="text-muted">Tip: Use bullet points, one objective per line.</small>
        </div>
    </div>

    <!-- V. Budgetary Requirements -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0" style="font-weight:700;color:var(--kn-dark)">V. Budgetary Requirements</h6>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3" style="font-size:.85rem">
                Daily cost per child × number of children × number of days = Total Amount
            </p>
            <div class="table-responsive">
                <table class="table table-bordered" id="budgetTable">
                    <thead style="background:rgba(107,122,58,0.08)">
                        <tr>
                            <th style="font-size:.85rem">Item Description</th>
                            <th style="font-size:.85rem;width:160px">Daily Cost per Child (₱)</th>
                            <th style="font-size:.85rem;width:220px">Computation</th>
                            <th style="font-size:.85rem;width:150px">Total Amount (₱)</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody id="budgetRows">
                        <?php foreach ($budgetItems as $i => $item): ?>
                        <tr class="budget-row">
                            <td>
                                <input type="text" class="form-control form-control-sm" 
                                       name="budget_item_desc[]"
                                       value="<?= htmlspecialchars($item['item']) ?>"
                                       placeholder="e.g., Rice / Malagkit">
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm daily-cost"
                                       name="budget_item_cost[]"
                                       value="<?= htmlspecialchars($item['daily_cost']) ?>"
                                       step="0.01" min="0" placeholder="0.00">
                            </td>
                            <td>
                                <span class="computation-label text-muted" style="font-size:.8rem">
                                    ₱<span class="cost-val">0.00</span> × <span class="ben-val">0</span> × <span class="days-val">0</span>
                                </span>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm row-total-display" readonly
                                       value="<?= htmlspecialchars($item['total'] ?? '') ?>">
                                <input type="hidden" class="row-total" name="budget_item_total[]" 
                                       value="<?= htmlspecialchars($item['total'] ?? '') ?>">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn-kn-danger btn-kn-sm remove-row">×</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background:rgba(107,122,58,0.05)">
                            <td colspan="3" class="text-end fw-bold">TOTAL:</td>
                            <td>
                                <input type="text" class="form-control form-control-sm fw-bold" id="grandTotalDisplay" readonly
                                       value="<?= htmlspecialchars($proposal['estimated_budget'] ?? '0') ?>">
                                <input type="hidden" id="grandTotal" name="estimated_budget" 
                                       value="<?= htmlspecialchars($proposal['estimated_budget'] ?? '0') ?>">
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <button type="button" class="btn-kn-primary btn-kn-sm" id="addBudgetRow">
                + Add Item
            </button>
        </div>
    </div>

    <!-- Digital Signature -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0" style="font-weight:700;color:var(--kn-dark)">Digital Signature (Committee Chair)</h6>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3" style="font-size:.9rem">
                <i class="bi bi-info-circle me-1"></i>
                Draw your signature below. This will appear on the printed proposal document.
            </p>
            <div style="border:2px solid #dee2e6;border-radius:8px;background:#fff">
                <canvas id="signatureCanvas" width="600" height="150" style="cursor:crosshair;display:block;width:100%;height:150px"></canvas>
            </div>
            <div class="mt-2">
                <button type="button" class="btn-kn-outline btn-kn-sm" onclick="clearSignature()">
                    <i class="bi bi-x-circle"></i>Clear Signature
                </button>
            </div>
            <input type="hidden" name="signature_data" id="signatureData" value="<?= htmlspecialchars($proposal['signature_data'] ?? '') ?>">
        </div>
    </div>

    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn-kn-primary">
            <i class="bi bi-save-fill"></i>Save Proposal
        </button>
        <a href="index.php?action=committeeChairDashboard" class="btn-kn-outline">Cancel</a>
    </div>
</form>

<script>
// ── Auto-calculate End Date ──
function calculateEndDate() {
    const startDateInput = document.getElementById('startDate');
    const implementationDaysInput = document.getElementById('implementationDays');
    const endDateInput = document.getElementById('endDate');
    
    const startDate = startDateInput.value;
    const implementationDays = parseInt(implementationDaysInput.value) || 0;
    
    if (startDate && implementationDays > 0) {
        const start = new Date(startDate);
        // Add implementation days to start date
        const end = new Date(start);
        end.setDate(end.getDate() + implementationDays);
        
        // Format as YYYY-MM-DD for date input
        const endDateStr = end.toISOString().split('T')[0];
        endDateInput.value = endDateStr;
    }
}

// Listen for changes on start date and implementation days
document.getElementById('startDate').addEventListener('change', calculateEndDate);
document.getElementById('implementationDays').addEventListener('input', calculateEndDate);

function recalculate() {
    const numBen  = parseFloat(document.getElementById('numBeneficiaries').value) || 0;
    const numDays = parseFloat(document.getElementById('implementationDays').value) || 0;
    let grand = 0;

    document.querySelectorAll('.budget-row').forEach(row => {
        const cost  = parseFloat(row.querySelector('.daily-cost').value) || 0;
        const total = cost * numBen * numDays;
        grand += total;

        row.querySelector('.cost-val').textContent = cost.toFixed(2);
        row.querySelector('.ben-val').textContent  = numBen;
        row.querySelector('.days-val').textContent = numDays;
        
        // Update both display and hidden input
        const displayInput = row.querySelector('.row-total-display');
        const hiddenInput = row.querySelector('.row-total');
        if (displayInput) displayInput.value = total.toFixed(2);
        if (hiddenInput) hiddenInput.value = total.toFixed(2);
    });

    // Update both grand total display and hidden input
    const grandTotalDisplay = document.getElementById('grandTotalDisplay');
    const grandTotalHidden = document.getElementById('grandTotal');
    if (grandTotalDisplay) grandTotalDisplay.value = grand.toFixed(2);
    if (grandTotalHidden) grandTotalHidden.value = grand.toFixed(2);
}

// Recalc on any input change
document.getElementById('numBeneficiaries').addEventListener('input', recalculate);
document.getElementById('implementationDays').addEventListener('input', () => {
    recalculate();
    calculateEndDate(); // Also recalculate end date
});
document.addEventListener('input', e => {
    if (e.target.classList.contains('daily-cost')) recalculate();
});

// Add row
document.getElementById('addBudgetRow').addEventListener('click', () => {
    const tbody = document.getElementById('budgetRows');
    const row = document.querySelector('.budget-row').cloneNode(true);
    row.querySelectorAll('input').forEach(i => i.value = '');
    row.querySelector('.cost-val').textContent  = '0.00';
    row.querySelector('.ben-val').textContent   = '0';
    row.querySelector('.days-val').textContent  = '0';
    tbody.appendChild(row);
});

// Remove row
document.addEventListener('click', e => {
    if (e.target.classList.contains('remove-row')) {
        const rows = document.querySelectorAll('.budget-row');
        if (rows.length > 1) { e.target.closest('tr').remove(); recalculate(); }
    }
});

// Initial calc
recalculate();

// ── Signature Canvas ──
const canvas = document.getElementById('signatureCanvas');
const ctx = canvas.getContext('2d');
let isDrawing = false;
let lastX = 0;
let lastY = 0;

// Set canvas actual size
canvas.width = 600;
canvas.height = 150;

// Load existing signature if any
const existingSig = document.getElementById('signatureData').value;
if (existingSig) {
    const img = new Image();
    img.onload = function() {
        ctx.drawImage(img, 0, 0);
    };
    img.src = existingSig;
}

// Drawing functions
function startDrawing(e) {
    isDrawing = true;
    const rect = canvas.getBoundingClientRect();
    lastX = (e.clientX || e.touches[0].clientX) - rect.left;
    lastY = (e.clientY || e.touches[0].clientY) - rect.top;
}

function draw(e) {
    if (!isDrawing) return;
    e.preventDefault();
    
    const rect = canvas.getBoundingClientRect();
    const x = (e.clientX || e.touches[0].clientX) - rect.left;
    const y = (e.clientY || e.touches[0].clientY) - rect.top;
    
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    
    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
    ctx.lineTo(x, y);
    ctx.stroke();
    
    lastX = x;
    lastY = y;
}

function stopDrawing() {
    if (isDrawing) {
        isDrawing = false;
        // Save signature as data URL
        document.getElementById('signatureData').value = canvas.toDataURL();
    }
}

// Mouse events
canvas.addEventListener('mousedown', startDrawing);
canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseup', stopDrawing);
canvas.addEventListener('mouseout', stopDrawing);

// Touch events
canvas.addEventListener('touchstart', startDrawing);
canvas.addEventListener('touchmove', draw);
canvas.addEventListener('touchend', stopDrawing);

function clearSignature() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById('signatureData').value = '';
}
</script>

<?php include __DIR__ . '/../templates/committee_chair_layout_end.php'; ?>
