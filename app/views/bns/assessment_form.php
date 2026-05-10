<?php
require_once __DIR__ . '/../templates/bns_layout.php';
require_once __DIR__ . '/../../../core/NutritionCalculator.php';

$isChild    = $type === 'child';
$isSenior   = $type === 'senior';
$isAdult    = !$isChild;
$ageMonths  = $isChild && !empty($subject['dob'])
    ? NutritionCalculator::ageInMonths($subject['dob'], date('Y-m-d'))
    : null;
$ageYears   = !$isChild && !empty($subject['dob'])
    ? NutritionCalculator::ageInYears($subject['dob'], date('Y-m-d'))
    : null;
?>
<style>
:root{--kn-green:#6B7A3A;--kn-orange:#C4722A;--kn-dark:#3D4A1E;--kn-muted:rgba(61,74,30,.55);}
.form-label{font-weight:600;font-size:.9rem;color:var(--kn-dark);}
.form-control,.form-select{border:1.5px solid rgba(107,122,58,.25);border-radius:8px;}
.form-control:focus,.form-select:focus{border-color:var(--kn-green);box-shadow:0 0 0 3px rgba(107,122,58,.12);}
.subject-card{background:rgba(107,122,58,.05);border:1.5px solid rgba(107,122,58,.15);border-radius:.85rem;padding:1.25rem;}
.result-box{background:#fff;border:1.5px solid rgba(107,122,58,.2);border-radius:.85rem;padding:1.25rem;display:none;}
.result-box.show{display:block;}
.status-pill{display:inline-block;padding:.25em .75em;border-radius:20px;font-weight:700;font-size:.88rem;}
</style>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="font-size:.88rem">
        <li class="breadcrumb-item"><a href="index.php?action=dataEncoding" style="color:var(--kn-green)">Resident Assessment</a></li>
        <li class="breadcrumb-item active">Assessment Form</li>
    </ol>
</nav>

<div class="row g-4">
    <!-- Left: Subject info + form -->
    <div class="col-lg-7">
        <!-- Subject card -->
        <div class="subject-card mb-4">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;background:var(--kn-green);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0">
                    <?= $isChild ? '👶' : ($type === 'senior' ? '👴' : '🤱') ?>
                </div>
                <div>
                    <h5 class="fw-bold mb-0"><?= htmlspecialchars($subject['full_name']) ?></h5>
                    <div style="font-size:.85rem;color:var(--kn-muted)">
                        <?= $subject['sex'] === 'M' ? '♂ Male' : '♀ Female' ?>
                        &nbsp;·&nbsp;
                        DOB: <?= $subject['dob'] ? date('M j, Y', strtotime($subject['dob'])) : '—' ?>
                        &nbsp;·&nbsp;
                        <?php if ($isChild): ?>
                            Age: <strong><?= $ageMonths ?> months</strong>
                        <?php else: ?>
                            Age: <strong><?= $ageYears ?> years</strong>
                        <?php endif; ?>
                        <?php if (!empty($subject['purok'])): ?>
                            &nbsp;·&nbsp; <?= htmlspecialchars($subject['purok']) ?>
                        <?php endif; ?>
                    </div>
                    <?php if ($isChild && !empty($subject['caregiver_name'])): ?>
                    <div style="font-size:.82rem;color:var(--kn-muted)">
                        Mother/Caregiver: <?= htmlspecialchars($subject['caregiver_name']) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Assessment form -->
        <form method="POST" action="index.php?action=saveAssessment" id="assessForm">
            <input type="hidden" name="assessed_type"  value="<?= htmlspecialchars($type) ?>">
            <input type="hidden" name="full_name"      value="<?= htmlspecialchars($subject['full_name']) ?>">
            <input type="hidden" name="sex"            value="<?= htmlspecialchars($subject['sex'] ?? 'F') ?>">
            <?php if (!empty($subject['dob'])): ?>
            <input type="hidden" name="dob"            value="<?= htmlspecialchars($subject['dob']) ?>">
            <?php endif; ?>
            <input type="hidden" name="caregiver_name" value="<?= htmlspecialchars($subject['caregiver_name'] ?? '') ?>">
            <input type="hidden" name="purok"          value="<?= htmlspecialchars($subject['purok'] ?? '') ?>">
            <?php if ($isChild): ?>
            <?php if (!empty($subject['child_id'])): ?>
            <input type="hidden" name="child_id" value="<?= (int)$subject['child_id'] ?>">
            <?php else: ?>
            <input type="hidden" name="fm_member_id" value="<?= (int)$subject['fm_member_id'] ?>">
            <?php endif; ?>
            <?php else: ?>
            <?php if (!empty($subject['user_id'])): ?>
            <input type="hidden" name="user_id"      value="<?= (int)$subject['user_id'] ?>">
            <?php elseif (!empty($subject['fm_member_id'])): ?>
            <input type="hidden" name="fm_member_id" value="<?= (int)$subject['fm_member_id'] ?>">
            <?php endif; ?>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-header fw-semibold">
                    <i class="bi bi-clipboard2-data me-2"></i>Measurements
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php if (empty($subject['dob'])): ?>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="dob" id="dob_input" class="form-control" required
                                   max="<?= date('Y-m-d') ?>">
                            <div style="font-size:.78rem;color:var(--kn-muted);margin-top:.2rem">
                                DOB not on record — please enter to calculate age.
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-6">
                            <label class="form-label">Assessment Date <span class="text-danger">*</span></label>
                            <input type="date" name="assessment_date" id="assessment_date"
                                   class="form-control" value="<?= date('Y-m-d') ?>" required
                                   max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Weight (kg) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="weight_kg" id="weight_kg"
                                       class="form-control" step="0.01" min="0.5" max="200"
                                       placeholder="e.g. 8.5" required>
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <?= $isChild ? 'Length/Height (cm)' : 'Height (cm)' ?>
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="height_cm" id="height_cm"
                                       class="form-control" step="0.1" min="30" max="220"
                                       placeholder="<?= $isChild ? 'e.g. 72.5' : 'e.g. 155.0' ?>" required>
                                <span class="input-group-text">cm</span>
                            </div>
                            <?php if ($isChild): ?>
                            <div style="font-size:.78rem;color:var(--kn-muted);margin-top:.2rem">
                                0–23 mos: measure lying down (length). 24–59 mos: measure standing (height).
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($isChild): ?>
                        <div class="col-md-6">
                            <label class="form-label">MUAC (cm) <span style="font-weight:400;color:var(--kn-muted)">(optional)</span></label>
                            <div class="input-group">
                                <input type="number" name="muac_cm" id="muac_cm"
                                       class="form-control" step="0.1" min="5" max="50"
                                       placeholder="e.g. 13.5">
                                <span class="input-group-text">cm</span>
                            </div>
                            <div style="font-size:.78rem;color:var(--kn-muted);margin-top:.2rem">
                                Mid-upper arm circumference
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($type === 'maternal' && !empty($subject['pregnancy_status']) && str_contains($subject['pregnancy_status'], 'Pregnant')): ?>
                        <!-- ── Pregnant-specific fields ── -->
                        <div class="col-12">
                            <hr style="border-color:rgba(107,122,58,.15)">
                            <div style="font-size:.82rem;font-weight:700;color:var(--kn-green);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.75rem">
                                <i class="bi bi-heart-pulse me-1"></i> Pregnancy Details
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">LMP <span style="font-weight:400;color:var(--kn-muted)">(Last Menstrual Period)</span></label>
                            <input type="date" name="lmp" id="lmp" class="form-control"
                                   max="<?= date('Y-m-d') ?>" onchange="calcAOG()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">EDC <span style="font-weight:400;color:var(--kn-muted)">(Expected Date of Confinement)</span></label>
                            <input type="date" name="edc" id="edc" class="form-control">
                            <div style="font-size:.78rem;color:var(--kn-muted);margin-top:.2rem">Auto-computed from LMP if blank</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Age of Gestation (months)</label>
                            <input type="number" name="aog_months" id="aog_months" class="form-control"
                                   min="1" max="9" placeholder="e.g. 5"
                                   style="background:rgba(107,122,58,.04)">
                            <div style="font-size:.78rem;color:var(--kn-muted);margin-top:.2rem">Auto-computed from LMP, or enter manually</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pre-pregnancy Weight (kg)</label>
                            <div class="input-group">
                                <input type="number" name="pre_preg_weight" id="pre_preg_weight"
                                       class="form-control" step="0.01" min="20" max="200"
                                       placeholder="e.g. 52.0" onchange="calcWeightGain()">
                                <span class="input-group-text">kg</span>
                            </div>
                            <div style="font-size:.78rem;color:var(--kn-muted);margin-top:.2rem">Weight before pregnancy</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Weight Gain</label>
                            <div id="weight_gain_display" style="padding:.55rem .85rem;background:rgba(107,122,58,.04);border:1.5px solid rgba(107,122,58,.15);border-radius:8px;font-size:.9rem;color:var(--kn-muted)">
                                — kg (enter pre-pregnancy weight)
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">PhilHealth Member</label>
                            <select name="philhealth" class="form-select">
                                <option value="">— Select —</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">4Ps Beneficiary</label>
                            <select name="is_4ps" class="form-select">
                                <option value="">— Select —</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="col-12">
                            <label class="form-label">Remarks <span style="font-weight:400;color:var(--kn-muted)">(optional)</span></label>
                            <textarea name="remarks" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-outline-secondary" onclick="previewResult()">
                    <i class="bi bi-eye me-1"></i> Preview Result
                </button>
                <button type="submit" class="btn fw-semibold" style="background:var(--kn-green);color:#fff">
                    <i class="bi bi-save me-1"></i> Save Assessment
                </button>
                <a href="index.php?action=dataEncoding&tab=<?= $type === 'child' ? 'children' : ($type === 'senior' ? 'seniors' : 'maternal') ?>"
                   class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Right: Live result preview -->
    <div class="col-lg-5">
        <div class="result-box" id="resultBox">
            <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-fill me-2"></i>Nutritional Status Preview</h6>
            <div id="resultContent"></div>
            <div class="mt-3 p-2 rounded" id="flagBox" style="display:none"></div>
        </div>

        <!-- Reference guide -->
        <div class="card shadow-sm mt-3">
            <div class="card-header fw-semibold" style="font-size:.88rem">
                <i class="bi bi-info-circle me-1"></i>
                <?= $isChild ? 'Z-score Classification Guide' : 'BMI Classification Guide' ?>
            </div>
            <div class="card-body p-3">
                <?php if ($isChild): ?>
                <table class="table table-sm mb-0" style="font-size:.8rem">
                    <thead><tr><th>Indicator</th><th>Code</th><th>Meaning</th></tr></thead>
                    <tbody>
                        <tr><td rowspan="4">WFA</td><td><span class="badge bg-danger">SUW</span></td><td>Severely Underweight (&lt;-3SD)</td></tr>
                        <tr><td><span class="badge bg-warning text-dark">UW</span></td><td>Underweight (-3SD to -2SD)</td></tr>
                        <tr><td><span class="badge bg-success">N</span></td><td>Normal (-2SD to +2SD)</td></tr>
                        <tr><td><span class="badge bg-info">OW</span></td><td>Overweight (&gt;+2SD)</td></tr>
                        <tr><td rowspan="3">HFA</td><td><span class="badge bg-danger">SSt</span></td><td>Severely Stunted (&lt;-3SD)</td></tr>
                        <tr><td><span class="badge bg-warning text-dark">St</span></td><td>Stunted (-3SD to -2SD)</td></tr>
                        <tr><td><span class="badge bg-success">N</span></td><td>Normal (-2SD to +3SD)</td></tr>
                        <tr><td rowspan="5">WFH</td><td><span class="badge bg-danger">SAM</span></td><td>Severe Acute Malnutrition (&lt;-3SD)</td></tr>
                        <tr><td><span class="badge bg-warning text-dark">MAM</span></td><td>Moderate Acute Malnutrition (-3SD to -2SD)</td></tr>
                        <tr><td><span class="badge bg-success">N</span></td><td>Normal (-2SD to +2SD)</td></tr>
                        <tr><td><span class="badge bg-info">OW</span></td><td>Overweight (+2SD to +3SD)</td></tr>
                        <tr><td><span class="badge bg-info">Ob</span></td><td>Obese (&gt;+3SD)</td></tr>
                    </tbody>
                </table>
                <?php else: ?>
                <table class="table table-sm mb-0" style="font-size:.8rem">
                    <thead><tr><th>BMI Range</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php if ($isSenior): ?>
                        <tr><td>&lt; 18.5</td><td><span class="badge bg-warning text-dark">Underweight</span></td></tr>
                        <tr><td>18.5 – 22.9</td><td><span class="badge bg-secondary">At Risk / Low Normal</span></td></tr>
                        <tr><td>23.0 – 27.9</td><td><span class="badge bg-success">Normal (Healthy for seniors)</span></td></tr>
                        <tr><td>28.0 – 31.9</td><td><span class="badge bg-info">Overweight</span></td></tr>
                        <tr><td>≥ 32.0</td><td><span class="badge bg-info">Obese</span></td></tr>
                    <?php else: ?>
                        <tr><td>&lt; 18.5</td><td><span class="badge bg-warning text-dark">Underweight</span></td></tr>
                        <tr><td>18.5 – 24.9</td><td><span class="badge bg-success">Normal</span></td></tr>
                        <tr><td>25.0 – 29.9</td><td><span class="badge bg-info">Overweight</span></td></tr>
                        <tr><td>≥ 30.0</td><td><span class="badge bg-info">Obese</span></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
const IS_CHILD = <?= $isChild ? 'true' : 'false' ?>;
const DOB      = '<?= $subject['dob'] ?? '' ?>';
const SEX      = '<?= $subject['sex'] ?? '' ?>';

// IOM recommended weight gain ranges by pre-pregnancy BMI
const IOM_RANGES = {
    underweight: { min: 12.7, max: 18.1 }, // 28-40 lbs
    normal:      { min: 11.3, max: 15.9 }, // 25-35 lbs
    overweight:  { min:  6.8, max: 11.3 }, // 15-25 lbs
    obese:       { min:  5.0, max:  9.1 }, // 11-20 lbs
};

function calcAOG() {
    const lmp = document.getElementById('lmp');
    const aog = document.getElementById('aog_months');
    const edc = document.getElementById('edc');
    if (!lmp || !lmp.value) return;
    const lmpDate = new Date(lmp.value);
    const today   = new Date();
    const diffMs  = today - lmpDate;
    const months  = Math.floor(diffMs / (1000 * 60 * 60 * 24 * 30.44));
    if (aog) aog.value = Math.min(Math.max(months, 1), 9);
    // Auto-compute EDC = LMP + 280 days
    if (edc && !edc.value) {
        const edcDate = new Date(lmpDate.getTime() + 280 * 24 * 60 * 60 * 1000);
        edc.value = edcDate.toISOString().split('T')[0];
    }
    calcWeightGain();
}

function calcWeightGain() {
    const prePregEl = document.getElementById('pre_preg_weight');
    const weightEl  = document.getElementById('weight_kg');
    const heightEl  = document.getElementById('height_cm');
    const display   = document.getElementById('weight_gain_display');
    if (!prePregEl || !weightEl || !display) return;

    const prePreg = parseFloat(prePregEl.value);
    const current = parseFloat(weightEl.value);
    const height  = parseFloat(heightEl ? heightEl.value : 0);

    if (!prePreg || !current) {
        display.textContent = '— kg (enter pre-pregnancy weight)';
        display.style.color = 'var(--kn-muted)';
        return;
    }

    const gain = (current - prePreg).toFixed(2);

    // Compute pre-pregnancy BMI to get recommended range
    let range = null;
    if (height > 0) {
        const hm = height / 100;
        const bmi = prePreg / (hm * hm);
        if (bmi < 18.5)       range = IOM_RANGES.underweight;
        else if (bmi < 25.0)  range = IOM_RANGES.normal;
        else if (bmi < 30.0)  range = IOM_RANGES.overweight;
        else                  range = IOM_RANGES.obese;
    }

    let status = '';
    let color  = 'var(--kn-dark)';
    if (range) {
        if (gain < range.min)       { status = ' — Low (below recommended)'; color = '#e67e22'; }
        else if (gain > range.max)  { status = ' — High (above recommended)'; color = '#e74c3c'; }
        else                        { status = ' — Normal'; color = 'var(--kn-green)'; }
    }

    display.textContent = gain + ' kg' + status;
    display.style.color = color;
}

// Bind weight input to recalculate gain
const weightInput = document.getElementById('weight_kg');
if (weightInput) weightInput.addEventListener('input', calcWeightGain);

function previewResult() {
    const weight = parseFloat(document.getElementById('weight_kg').value);
    const height = parseFloat(document.getElementById('height_cm').value);
    const date   = document.getElementById('assessment_date').value;
    // DOB: use hidden constant if available, otherwise read from visible input (for BNS-only subjects)
    const dobInput = document.getElementById('dob_input');
    const dob = DOB || (dobInput ? dobInput.value : '');

    if (!weight || !height || !date || !dob) {
        alert('Please enter weight, height, date of birth, and assessment date first.');
        return;
    }

    // Call server-side preview via AJAX
    fetch('index.php?action=previewAssessment', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            type: '<?= $type ?>',
            weight_kg: weight,
            height_cm: height,
            dob: dob,
            sex: SEX,
            assessment_date: date
        })
    })
    .then(r => r.json())
    .then(data => {
        const box = document.getElementById('resultBox');
        const content = document.getElementById('resultContent');
        const flagBox = document.getElementById('flagBox');
        box.classList.add('show');

        if (IS_CHILD) {
            content.innerHTML = `
                <div class="mb-2"><strong>Weight-for-Age (WFA):</strong>
                    <span class="status-pill bg-${data.wfa_color} text-${data.wfa_color==='warning'?'dark':'white'} ms-2">${data.wfa}</span>
                    <small class="text-muted ms-1">${data.wfa_label}</small>
                </div>
                <div class="mb-2"><strong>Height-for-Age (HFA):</strong>
                    <span class="status-pill bg-${data.hfa_color} text-${data.hfa_color==='warning'?'dark':'white'} ms-2">${data.hfa}</span>
                    <small class="text-muted ms-1">${data.hfa_label}</small>
                </div>
                <div class="mb-2"><strong>Weight-for-Height (WFH):</strong>
                    <span class="status-pill bg-${data.wfh_color} text-${data.wfh_color==='warning'?'dark':'white'} ms-2">${data.wfh}</span>
                    <small class="text-muted ms-1">${data.wfh_label}</small>
                </div>
                <div class="text-muted" style="font-size:.8rem">Age at assessment: ${data.age_months} months</div>
            `;
        } else {
            content.innerHTML = `
                <div class="mb-2"><strong>BMI:</strong> <span class="fw-bold">${data.bmi}</span></div>
                <div class="mb-2"><strong>Status:</strong>
                    <span class="status-pill bg-${data.bmi_color} text-${data.bmi_color==='warning'?'dark':'white'} ms-2">${data.bmi_status}</span>
                </div>
            `;
        }

        if (data.is_at_risk) {
            flagBox.style.display = 'block';
            flagBox.style.background = 'rgba(196,114,42,.08)';
            flagBox.style.border = '1.5px solid rgba(196,114,42,.3)';
            flagBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-warning me-2"></i><strong>At-risk:</strong> This individual will be added to Form C and the Monitoring List upon saving.';
        } else if (data.needs_monitoring) {
            flagBox.style.display = 'block';
            flagBox.style.background = 'rgba(255,193,7,.08)';
            flagBox.style.border = '1.5px solid rgba(255,193,7,.3)';
            flagBox.innerHTML = '<i class="bi bi-flag-fill text-warning me-2"></i><strong>Needs monitoring:</strong> Status is not Normal. Will be added to the monitoring list.';
        } else {
            flagBox.style.display = 'none';
        }
    })
    .catch(() => alert('Preview failed. Please check your inputs.'));
}
</script>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
