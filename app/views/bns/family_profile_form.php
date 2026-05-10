<?php
$isEdit    = !empty($family);
$title     = $isEdit ? 'Edit Family Profile' : 'Add Family Profile';
$pageTitle = $title;
$activeNav = 'family_profiles';

function fv(string $key, ?array $src, string $default = ''): string {
    return htmlspecialchars((string)($src[$key] ?? $default));
}
function fsel(string $key, string $val, ?array $src): string {
    return (string)($src[$key] ?? '') === $val ? 'selected' : '';
}
function fchk(string $key, ?array $src): string {
    return !empty($src[$key]) ? 'checked' : '';
}
$head   = array_values(array_filter($members ?? [], fn($m) => $m['role'] === 'Head'))[0] ?? [];
$wife   = array_values(array_filter($members ?? [], fn($m) => $m['role'] === 'Wife'))[0] ?? [];
$child1 = $children[0] ?? [];
$child2 = $children[1] ?? [];

require_once __DIR__ . '/../templates/bns_layout.php';
?>
<style>
        :root {
            --kn-green:#6B7A3A; --kn-green-d:#556030;
            --kn-orange:#C4722A; --kn-cream:#F5EDD6;
            --kn-dark:#3D4A1E; --kn-muted:rgba(61,74,30,.55);
        }
        body { background:#fdf0e8; font-family:'Segoe UI',system-ui,sans-serif; color:var(--kn-dark); }

        /* ── Step pills (matches Mother Wizard) ── */
        #step-indicator { flex-wrap:wrap; }
        .step-pill {
            padding:.45rem 1.1rem; border-radius:20px; font-size:.88rem; font-weight:600;
            background:#fff; color:var(--kn-muted);
            border:1.5px solid rgba(107,122,58,.2); cursor:pointer; transition:.2s;
            white-space:nowrap;
        }
        .step-pill.active { background:var(--kn-green); color:#fff; border-color:var(--kn-green); }
        .step-pill.done   { background:rgba(107,122,58,.12); color:var(--kn-green); border-color:rgba(107,122,58,.3); }
        .step-divider { flex:1; height:2px; background:rgba(107,122,58,.15); min-width:12px; }

        /* ── Wizard steps ── */
        .wizard-step { display:none; }
        .wizard-step.active { display:block; animation:fadeIn .22s ease; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(5px)} to{opacity:1;transform:translateY(0)} }

        /* ── Cards (matches Mother Wizard card style) ── */
        .card { border:1.5px solid rgba(107,122,58,.15); border-radius:.85rem; }
        .card-header {
            background:rgba(107,122,58,.06); border-bottom:1.5px solid rgba(107,122,58,.12);
            font-weight:700; font-size:.95rem; color:var(--kn-dark);
            padding:.75rem 1.25rem; border-radius:.85rem .85rem 0 0 !important;
        }
        .card-body { padding:1.35rem 1.25rem; }
        .card.shadow-sm { box-shadow:0 2px 8px rgba(61,74,30,.07) !important; }

        /* ── Form fields ── */
        .form-label { font-size:.92rem; font-weight:600; color:var(--kn-dark); margin-bottom:.3rem; }
        .form-control, .form-select {
            border:1.5px solid rgba(107,122,58,.25); border-radius:8px;
            font-size:.95rem; padding:.5rem .85rem; transition:.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color:var(--kn-green); box-shadow:0 0 0 3px rgba(107,122,58,.12); outline:none;
        }
        .form-control::placeholder { color:rgba(61,74,30,.35); }
        .input-group-text {
            background:rgba(107,122,58,.08); border:1.5px solid rgba(107,122,58,.25);
            color:var(--kn-green);
        }

        /* ── Sex toggle ── */
        .sex-toggle { display:flex; gap:.5rem; }
        .sex-toggle input[type=radio] { display:none; }
        .sex-toggle label {
            flex:1; text-align:center; padding:.5rem; border:1.5px solid rgba(107,122,58,.25);
            border-radius:8px; font-size:.92rem; font-weight:600; cursor:pointer; transition:.2s;
            color:var(--kn-muted); background:#fff;
        }
        .sex-toggle input[type=radio]:checked + label { background:var(--kn-green); color:#fff; border-color:var(--kn-green); }

        /* ── Checkbox / radio cards ── */
        .check-card-group { display:flex; flex-wrap:wrap; gap:.5rem; }
        .check-card input[type=checkbox],
        .check-card input[type=radio] { display:none; }
        .check-card label {
            display:flex; align-items:center; gap:.45rem; padding:.5rem 1rem;
            border:1.5px solid rgba(107,122,58,.25); border-radius:8px;
            font-size:.92rem; cursor:pointer; transition:.2s; color:var(--kn-dark); background:#fff;
        }
        .check-card label:hover { border-color:var(--kn-green); background:rgba(107,122,58,.05); }
        .check-card input[type=checkbox]:checked + label,
        .check-card input[type=radio]:checked + label { background:var(--kn-green); color:#fff; border-color:var(--kn-green); }

        /* ── Child card ── */
        .child-card {
            background:rgba(107,122,58,.04); border:1.5px solid rgba(107,122,58,.15);
            border-radius:.85rem; padding:1.25rem; height:100%;
        }
        .child-title { font-size:.88rem; font-weight:700; color:var(--kn-green); text-transform:uppercase; letter-spacing:.05em; margin-bottom:.85rem; }
        .age-badge { display:inline-block; background:rgba(107,122,58,.1); color:var(--kn-green); font-size:.82rem; font-weight:700; padding:.12rem .5rem; border-radius:20px; margin-left:.3rem; }

        /* ── Nav buttons ── */
        .nav-bar { display:flex; justify-content:space-between; align-items:center; padding:1.25rem 0 1.5rem; gap:1rem; }
        .btn-kn { background:var(--kn-green); color:#fff; border:none; border-radius:8px; font-weight:600; font-size:.95rem; transition:.2s; }
        .btn-kn:hover { background:var(--kn-green-d); color:#fff; }
        .btn-prev { background:#fff; color:var(--kn-dark); border:1.5px solid rgba(107,122,58,.3); border-radius:8px; font-weight:600; font-size:.95rem; transition:.2s; }
        .btn-prev:hover { background:rgba(107,122,58,.06); }

        /* ── Review table ── */
        .review-table td { padding:.45rem .65rem; font-size:.92rem; border-bottom:1px solid rgba(107,122,58,.08); }
        .review-table td:first-child { color:var(--kn-muted); font-weight:500; width:45%; }
        .review-table td:last-child { font-weight:600; }
        .review-section-title { font-size:.82rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--kn-green); padding:.6rem 0 .25rem; }

        /* ── Misc ── */
        .hint { font-size:.82rem; color:var(--kn-muted); margin-top:.2rem; }
        .req { color:var(--kn-orange); }

        /* ── Modal ── */
        .modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center; }
        .modal-overlay.show { display:flex; }
        .modal-box { background:#fff; border-radius:1rem; padding:1.5rem; max-width:400px; width:90%; box-shadow:0 10px 40px rgba(0,0,0,.25); animation:fadeIn .25s ease; }
        .modal-title { font-size:1.05rem; font-weight:700; color:var(--kn-dark); margin-bottom:.4rem; }
        .modal-message { color:var(--kn-muted); font-size:.92rem; margin-bottom:1.25rem; }
        .modal-actions { display:flex; gap:.65rem; justify-content:flex-end; }
        .btn-modal-cancel { background:#fff; color:var(--kn-dark); border:1.5px solid rgba(107,122,58,.3); border-radius:8px; padding:.45rem 1.1rem; font-weight:600; cursor:pointer; }
        .btn-modal-confirm { background:#dc3545; color:#fff; border:none; border-radius:8px; padding:.45rem 1.1rem; font-weight:600; cursor:pointer; }
    </style>

<div style="max-width:1200px; margin:0 auto; padding:0 1rem">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:.92rem">
            <li class="breadcrumb-item"><a href="index.php?action=familyProfiles" style="color:var(--kn-green)">Family Profiles</a></li>
            <li class="breadcrumb-item active"><?= $title ?></li>
        </ol>
    </nav>

    <div class="d-flex align-items-center gap-3 mb-4">
        <div style="width:48px;height:48px;background:var(--kn-green);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0">&#128106;</div>
        <div>
            <h4 class="fw-bold mb-0"><?= $title ?></h4>
            <p class="mb-0" style="font-size:.92rem;color:var(--kn-muted)">BNS Family Profile &mdash; complete all sections</p>
        </div>
    </div>

    <!-- Step indicator (matches Mother Wizard pill style) -->
    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap" id="step-indicator">
        <div class="step-pill active" id="pill-1" onclick="goStep(1)">1 Household</div>
        <div class="step-divider"></div>
        <div class="step-pill" id="pill-2" onclick="goStep(2)">2 Family Head</div>
        <div class="step-divider"></div>
        <div class="step-pill" id="pill-3" onclick="goStep(3)">3 Children</div>
        <div class="step-divider"></div>
        <div class="step-pill" id="pill-4" onclick="goStep(4)">4 Socio-Economic</div>
        <div class="step-divider"></div>
        <div class="step-pill" id="pill-5" onclick="goStep(5)">5 Review &amp; Save</div>
    </div>

    <form method="POST" action="index.php?action=saveFamilyProfile" id="profileForm" novalidate>
        <?php if ($isEdit): ?>
            <input type="hidden" name="family_id" value="<?= $family['family_id'] ?>">
        <?php endif; ?>

        <!-- ═══════════════════════════════════════════════════════════
             STEP 1 – Household Identification
        ════════════════════════════════════════════════════════════════ -->
        <div class="wizard-step active" id="section-1">

            <div class="card shadow-sm mb-4">
                <div class="card-header"><i class="bi bi-house-door me-2"></i>Household Identification</div>
                <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Household Number (HH #) <span class="req">*</span></label>
                        <input type="text" name="hh_number" class="form-control"
                               placeholder="e.g. 001"
                               value="<?= fv('hh_number', $family) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Purok</label>
                        <input type="text" name="purok" class="form-control"
                               placeholder="e.g. Purok 3" value="<?= fv('purok', $family) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"># of Household Members</label>
                        <input type="number" name="num_hh_members" class="form-control" min="1"
                               placeholder="0" value="<?= fv('num_hh_members', $family) ?>">
                    </div>
                </div>
                </div>
            </div>

            <div class="nav-bar">
                <span class="text-muted" style="font-size:.88rem">Step 1 of 5</span>
                <button type="button" class="btn btn-kn px-4" onclick="goStep(2)">
                    Next <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════
             STEP 2 – Head of Family / Spouse
        ════════════════════════════════════════════════════════════════ -->
        <div class="wizard-step" id="section-2">

            <div class="card shadow-sm mb-4">
                <div class="card-header"><i class="bi bi-person-badge me-2"></i>Head of the Family</div>
                <div class="card-body">
                <p class="hint mb-3"><i class="bi bi-info-circle me-1"></i>The <strong>Head of Family</strong> is the member with the higher income. If incomes are equal or not provided, the family designates who the Head is.</p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Last Name <span class="req">*</span></label>
                        <input type="text" name="head_last_name" class="form-control" required
                               placeholder="Surname"
                               value="<?= htmlspecialchars($head['last_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">First Name <span class="req">*</span></label>
                        <input type="text" name="head_first_name" class="form-control" required
                               placeholder="Given Name"
                               value="<?= htmlspecialchars($head['first_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="head_middle_name" class="form-control"
                               placeholder="Optional"
                               value="<?= htmlspecialchars($head['middle_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Suffix</label>
                        <input type="text" name="head_suffix" class="form-control"
                               placeholder="Jr."
                               value="<?= htmlspecialchars($head['suffix'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sex</label>
                        <div class="sex-toggle">
                            <input type="radio" name="head_sex" id="head_sex_m" value="M" <?= ($head['sex'] ?? '') === 'M' ? 'checked' : '' ?>>
                            <label for="head_sex_m">&#9794; Male</label>
                            <input type="radio" name="head_sex" id="head_sex_f" value="F" <?= ($head['sex'] ?? '') === 'F' ? 'checked' : '' ?>>
                            <label for="head_sex_f">&#9792; Female</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth <span style="font-weight:400;font-size:.82rem;color:var(--kn-muted)">(for elderly identification)</span></label>
                        <input type="date" name="head_dob" class="form-control"
                               max="<?= date('Y-m-d') ?>"
                               value="<?= htmlspecialchars($head['dob'] ?? '') ?>">
                        <?php if (!empty($head['dob'])): ?>
                        <div class="hint">
                            Age: <strong><?= floor((time() - strtotime($head['dob'])) / (365.25 * 86400)) ?> years</strong>
                            <?php if (floor((time() - strtotime($head['dob'])) / (365.25 * 86400)) >= 60): ?>
                                <span style="color:var(--kn-orange);font-weight:700"> · Elderly (60+)</span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Civil Status</label>
                        <select name="head_civil_status" id="head_civil_status" class="form-select">
                            <option value="">— Select —</option>
                            <option value="Single" <?= ($head['civil_status'] ?? '') === 'Single' ? 'selected' : '' ?>>Single</option>
                            <option value="Married" <?= ($head['civil_status'] ?? '') === 'Married' ? 'selected' : '' ?>>Married</option>
                            <option value="Widowed" <?= ($head['civil_status'] ?? '') === 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                            <option value="Separated" <?= ($head['civil_status'] ?? '') === 'Separated' ? 'selected' : '' ?>>Separated</option>
                            <option value="Live-in" <?= ($head['civil_status'] ?? '') === 'Live-in' ? 'selected' : '' ?>>Live-in</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Occupation</label>
                        <input type="text" name="head_occupation" class="form-control"
                               placeholder="e.g. Farmer, Teacher, Housewife"
                               value="<?= htmlspecialchars($head['occupation'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Educational Attainment</label>
                        <select name="head_educ_id" class="form-select">
                            <option value="">— Select —</option>
                            <?php foreach ($lookups['educ_levels'] as $e): ?>
                                <option value="<?= $e['id'] ?>" <?= (int)($head['educ_level_id'] ?? 0) === (int)$e['id'] ? 'selected' : '' ?>><?= htmlspecialchars($e['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="bi bi-envelope me-1" style="color:var(--kn-orange)"></i>
                            Resident Email Address
                            <span style="font-weight:400;font-size:.82rem;color:var(--kn-muted)">(optional)</span>
                        </label>
                        <?php if ($isEdit && !empty($family['head_email'])): ?>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($family['head_email']) ?>" readonly
                                   style="background:rgba(107,122,58,.06);color:var(--kn-muted)">
                            <div class="hint" style="color:var(--kn-green)">
                                <i class="bi bi-check-circle-fill me-1"></i>Account already linked to this email.
                            </div>
                        <?php elseif ($isEdit && !empty($family['source_user_id'])): ?>
                            <input type="email" class="form-control" value="Account linked" readonly
                                   style="background:rgba(107,122,58,.06);color:var(--kn-muted)">
                        <?php else: ?>
                            <input type="email" name="head_email" class="form-control"
                                   placeholder="e.g. juan@gmail.com"
                                   value="<?= htmlspecialchars($family['head_email'] ?? '') ?>">
                            <div class="hint">
                                <i class="bi bi-info-circle me-1"></i>
                                If provided, an account will be created and credentials sent to this email.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header"><i class="bi bi-person-heart me-2"></i><span id="spouse-label-text">Spouse</span></div>
                <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="wife_last_name" class="form-control"
                               placeholder="Surname"
                               value="<?= htmlspecialchars($wife['last_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">First Name</label>
                        <input type="text" name="wife_first_name" class="form-control"
                               placeholder="Given Name"
                               value="<?= htmlspecialchars($wife['first_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="wife_middle_name" class="form-control"
                               placeholder="Optional"
                               value="<?= htmlspecialchars($wife['middle_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Suffix</label>
                        <input type="text" name="wife_suffix" class="form-control"
                               placeholder="Jr."
                               value="<?= htmlspecialchars($wife['suffix'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sex</label>
                        <div class="sex-toggle">
                            <input type="radio" name="wife_sex" id="wife_sex_m" value="M" <?= ($wife['sex'] ?? '') === 'M' ? 'checked' : '' ?>>
                            <label for="wife_sex_m">&#9794; Male</label>
                            <input type="radio" name="wife_sex" id="wife_sex_f" value="F" <?= ($wife['sex'] ?? '') === 'F' ? 'checked' : '' ?>>
                            <label for="wife_sex_f">&#9792; Female</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth <span style="font-weight:400;font-size:.82rem;color:var(--kn-muted)">(for elderly identification)</span></label>
                        <input type="date" name="wife_dob" class="form-control"
                               max="<?= date('Y-m-d') ?>"
                               value="<?= htmlspecialchars($wife['dob'] ?? '') ?>">
                        <?php if (!empty($wife['dob'])): ?>
                        <div class="hint">
                            Age: <strong><?= floor((time() - strtotime($wife['dob'])) / (365.25 * 86400)) ?> years</strong>
                            <?php if (floor((time() - strtotime($wife['dob'])) / (365.25 * 86400)) >= 60): ?>
                                <span style="color:var(--kn-orange);font-weight:700"> · Elderly (60+)</span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Occupation</label>
                        <input type="text" name="wife_occupation" class="form-control"
                               placeholder="e.g. Farmer, Teacher, Housewife"
                               value="<?= htmlspecialchars($wife['occupation'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Educational Attainment</label>
                        <select name="wife_educ_id" class="form-select">
                            <option value="">— Select —</option>
                            <?php foreach ($lookups['educ_levels'] as $e): ?>
                                <option value="<?= $e['id'] ?>" <?= (int)($wife['educ_level_id'] ?? 0) === (int)$e['id'] ? 'selected' : '' ?>><?= htmlspecialchars($e['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                </div>
            </div>

            <div class="nav-bar">
                <button type="button" class="btn btn-prev px-4" onclick="goStep(1)">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <span class="text-muted" style="font-size:.88rem">Step 2 of 5</span>
                <button type="button" class="btn btn-kn px-4" onclick="goStep(3)">
                    Next <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════
             STEP 3 – Children
        ════════════════════════════════════════════════════════════════ -->
        <div class="wizard-step" id="section-3">

            <div class="card shadow-sm mb-4">
                <div class="card-header"><i class="bi bi-emoji-smile me-2"></i>Children</div>
                <div class="card-body">

                    <div class="card mb-4" style="border-color:rgba(107,122,58,.15)">
                        <div class="card-header" style="font-size:.88rem"><i class="bi bi-bar-chart me-2"></i>No. of Children by Age Group <span style="font-weight:400;color:var(--kn-green);font-size:.82rem"><i class="bi bi-magic me-1"></i>Auto-calculated from birthdates</span></div>
                        <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">0–5 months old</label>
                                <input type="number" name="children_0_5mos" id="children_0_5mos" class="form-control" min="0"
                                       placeholder="0" value="<?= fv('children_0_5mos', $family) ?>"
                                       readonly style="background:rgba(107,122,58,.06);cursor:default">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">6–23 months old</label>
                                <input type="number" name="children_6_23mos" id="children_6_23mos" class="form-control" min="0"
                                       placeholder="0" value="<?= fv('children_6_23mos', $family) ?>"
                                       readonly style="background:rgba(107,122,58,.06);cursor:default">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">24–59 months old</label>
                                <input type="number" name="children_24_59mos" id="children_24_59mos" class="form-control" min="0"
                                       placeholder="0" value="<?= fv('children_24_59mos', $family) ?>"
                                       readonly style="background:rgba(107,122,58,.06);cursor:default">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&gt;60 months old</label>
                                <input type="number" name="children_60plus" id="children_60plus" class="form-control" min="0"
                                       placeholder="0" value="<?= fv('children_60plus', $family) ?>"
                                       readonly style="background:rgba(107,122,58,.06);cursor:default">
                            </div>
                        </div>
                        </div>
                    </div>

                    <p class="text-muted mb-3" style="font-size:.88rem">Add children living in this household. Click "Add Child" to add more rows.</p>

                    <div id="childrenContainer"></div>

                    <button type="button" class="btn btn-sm mt-2"
                            style="background:rgba(107,122,58,.1);color:var(--kn-green);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;font-weight:600;font-size:.88rem"
                            onclick="addChild()">
                        <i class="bi bi-plus-circle me-1"></i> Add Child
                    </button>
                </div>
            </div>

            <div class="nav-bar">
                <button type="button" class="btn btn-prev px-4" onclick="goStep(2)">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <span class="text-muted" style="font-size:.88rem">Step 3 of 5</span>
                <button type="button" class="btn btn-kn px-4" onclick="goStep(4)">
                    Next <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════
             STEP 4 – Fill In / Check If
        ════════════════════════════════════════════════════════════════ -->
        <div class="wizard-step" id="section-4">

            <div class="card shadow-sm mb-4">
                <div class="card-header"><i class="bi bi-check2-square me-2"></i>Health Information</div>
                <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Mother's Pregnancy Status</label>
                        <select name="wife_pregnancy_status" class="form-select"
                                onchange="syncPregnancyCheckbox(this.value)">
                            <option value="">— Select —</option>
                            <?php foreach (['Not Pregnant','Pregnant 1st Trimester','Pregnant 2nd Trimester','Pregnant 3rd Trimester','Postpartum'] as $ps): ?>
                                <option value="<?= $ps ?>"
                                    <?= ($family['wife_pregnancy_status'] ?? '') === $ps ? 'selected' : '' ?>>
                                    <?= $ps ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <!-- Hidden checkbox kept for backward compatibility with saveProfile() -->
                        <input type="hidden" name="is_mother_prog" id="isMotherProg" value="<?= !empty($family['is_mother_prog']) ? '1' : '0' ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mother's Breastfeeding Status</label>
                        <select name="wife_breastfeeding_status" class="form-select"
                                onchange="syncBreastfeedingCheckboxes(this.value)">
                            <option value="">— Select —</option>
                            <?php foreach (['Not Breastfeeding','EBF (Exclusive Breastfeeding)','Mixed Feeding','Bottle Feeding'] as $bs): ?>
                                <option value="<?= $bs ?>"
                                    <?php
                                    $curBf = '';
                                    if (!empty($family['is_erf'])) $curBf = 'EBF (Exclusive Breastfeeding)';
                                    elseif (!empty($family['is_mixed_milk'])) $curBf = 'Mixed Feeding';
                                    elseif (!empty($family['is_bottle_feeding'])) $curBf = 'Bottle Feeding';
                                    elseif (isset($family['is_erf'])) $curBf = 'Not Breastfeeding';
                                    echo $curBf === $bs ? 'selected' : '';
                                    ?>>
                                    <?= $bs ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <!-- Hidden checkboxes kept for backward compatibility -->
                        <input type="hidden" name="is_erf"           id="isErf"   value="<?= !empty($family['is_erf'])           ? '1' : '0' ?>">
                        <input type="hidden" name="is_mixed_milk"    id="isMixed" value="<?= !empty($family['is_mixed_milk'])    ? '1' : '0' ?>">
                        <input type="hidden" name="is_bottle_feeding" id="isBottle" value="<?= !empty($family['is_bottle_feeding']) ? '1' : '0' ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Couple Family Planning Method</label>
                        <select name="fp_method_id" id="fp_method_id" class="form-select"
                                onchange="toggleFpOther(this.value)">
                            <option value="">— Select —</option>
                            <?php foreach ($lookups['fp_methods'] as $m): ?>
                                <option value="<?= $m['id'] ?>" <?= (int)($family['fp_method_id'] ?? 0) === (int)$m['id'] ? 'selected' : '' ?>><?= htmlspecialchars($m['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div id="fp_other_wrap" style="margin-top:.5rem;display:none">
                            <input type="text" name="fp_method_other" id="fp_method_other"
                                   class="form-control" placeholder="Please specify…"
                                   maxlength="100"
                                   value="<?= htmlspecialchars($family['fp_method_other'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header"><i class="bi bi-droplet me-2"></i>Sanitation &amp; Water</div>
                <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Toilet Type</label>
                        <div class="check-card-group">
                            <?php foreach ($lookups['toilet_types'] as $tt): ?>
                            <div class="check-card">
                                <input type="radio" name="toilet_type_id" id="toilet_<?= $tt['id'] ?>" value="<?= $tt['id'] ?>"
                                       <?= (int)($family['toilet_type_id'] ?? 0) === (int)$tt['id'] ? 'checked' : '' ?>>
                                <label for="toilet_<?= $tt['id'] ?>"><?= htmlspecialchars($tt['label']) ?> (<?= $tt['code'] ?>)</label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Water Source</label>
                        <div class="check-card-group">
                            <?php foreach ($lookups['water_sources'] as $ws): ?>
                            <div class="check-card">
                                <input type="radio" name="water_source_id" id="water_<?= $ws['id'] ?>" value="<?= $ws['id'] ?>"
                                       <?= (int)($family['water_source_id'] ?? 0) === (int)$ws['id'] ? 'checked' : '' ?>>
                                <label for="water_<?= $ws['id'] ?>"><?= htmlspecialchars($ws['label']) ?> (<?= $ws['code'] ?>)</label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Dwelling Type</label>
                        <div class="check-card-group">
                            <?php foreach ($lookups['dwelling_types'] as $dt): ?>
                            <div class="check-card">
                                <input type="radio" name="dwelling_type_id" id="dwell_<?= $dt['id'] ?>" value="<?= $dt['id'] ?>"
                                       <?= (int)($family['dwelling_type_id'] ?? 0) === (int)$dt['id'] ? 'checked' : '' ?>>
                                <label for="dwell_<?= $dt['id'] ?>"><?= htmlspecialchars($dt['label']) ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header"><i class="bi bi-basket2 me-2"></i>Food Production &amp; Nutrition</div>
                <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Food Production Activity <span style="font-weight:400;font-size:.85rem">(check all that apply)</span></label>
                        <div class="check-card-group mt-1">
                            <?php foreach ($lookups['food_activities'] as $fa): ?>
                            <div class="check-card">
                                <input type="checkbox" name="food_activity_ids[]" id="fpa_<?= $fa['id'] ?>" value="<?= $fa['id'] ?>"
                                       <?= in_array($fa['id'], $selectedFoodAct ?? []) ? 'checked' : '' ?>>
                                <label for="fpa_<?= $fa['id'] ?>"><?= htmlspecialchars($fa['label']) ?> (<?= $fa['code'] ?>)</label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total Family Income <span style="font-weight:400;font-size:.85rem">(&#8369;/month)</span></label>
                        <div class="input-group">
                            <span class="input-group-text">&#8369;</span>
                            <input type="number" name="total_income" class="form-control" min="0" step="0.01"
                                   placeholder="0.00" value="<?= fv('total_income', $family) ?>">
                        </div>
                        <label class="form-label mt-3">Check if:</label>
                        <div class="check-card-group mt-1">
                            <div class="check-card">
                                <input type="checkbox" name="uses_iodized_salt" id="iodized" <?= fchk('uses_iodized_salt', $family) ?>>
                                <label for="iodized">Iodized Salt</label>
                            </div>
                            <div class="check-card">
                                <input type="checkbox" name="uses_ifr" id="ifr" <?= fchk('uses_ifr', $family) ?>>
                                <label for="ifr">IFR (Iron Fortified Rice)</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2"
                                  placeholder="Optional notes…"><?= fv('remarks', $family) ?></textarea>
                    </div>
                </div>
                </div>
            </div>

            <div class="nav-bar">
                <button type="button" class="btn btn-prev px-4" onclick="goStep(3)">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <span class="text-muted" style="font-size:.88rem">Step 4 of 5</span>
                <button type="button" class="btn btn-kn px-4" onclick="buildReview(); goStep(5)">
                    Review &amp; Save <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════
             STEP 5 – Review & Save
        ════════════════════════════════════════════════════════════════ -->
        <div class="wizard-step" id="section-5">

            <div class="card shadow-sm mb-4">
                <div class="card-header"><i class="bi bi-clipboard-check me-2"></i>Review Before Saving</div>
                <div class="card-body">
                <p class="text-muted mb-3" style="font-size:.85rem">Please review the information below. Click a step above to go back and edit.</p>

                <div class="review-section-title">Household Identification</div>
                <table class="review-table w-100 mb-3">
                    <tr><td>HH #</td><td id="rv_hh">—</td></tr>
                    <tr><td>Purok</td><td id="rv_purok">—</td></tr>
                    <tr><td># of HH Members</td><td id="rv_hh_members">—</td></tr>
                    <tr><td>Children 0–5 mos</td><td id="rv_c05">—</td></tr>
                    <tr><td>Children 6–23 mos</td><td id="rv_c623">—</td></tr>
                    <tr><td>Children 24–59 mos</td><td id="rv_c2459">—</td></tr>
                    <tr><td>Children &gt;60 mos</td><td id="rv_c60">—</td></tr>
                </table>

                <div class="review-section-title">Head of Family / Spouse</div>
                <table class="review-table w-100 mb-3">
                    <tr><td>Head Name</td><td id="rv_head_name">—</td></tr>
                    <tr><td>Head Sex</td><td id="rv_head_sex">—</td></tr>
                    <tr><td>Head Civil Status</td><td id="rv_head_civil">—</td></tr>
                    <tr><td>Head Occupation</td><td id="rv_head_occ">—</td></tr>
                    <tr><td>Head Education</td><td id="rv_head_educ">—</td></tr>
                    <tr><td>Spouse Name</td><td id="rv_wife_name">—</td></tr>
                    <tr><td>Spouse Sex</td><td id="rv_wife_sex">—</td></tr>
                    <tr><td>Spouse Occupation</td><td id="rv_wife_occ">—</td></tr>
                    <tr><td>Spouse Education</td><td id="rv_wife_educ">—</td></tr>
                </table>

                <div class="review-section-title">Children</div>
                <p class="text-muted" style="font-size:.85rem">Review children details in Step 3.</p>

                <div class="review-section-title">Socio-Economic</div>
                <table class="review-table w-100 mb-3">
                    <tr><td>Pregnancy Status</td><td id="rv_mother">—</td></tr>
                    <tr><td>Breastfeeding Status</td><td id="rv_erf">—</td></tr>
                    <tr><td>FP Method</td><td id="rv_fp">—</td></tr>
                    <tr><td>Toilet Type</td><td id="rv_toilet">—</td></tr>
                    <tr><td>Water Source</td><td id="rv_water">—</td></tr>
                    <tr><td>Food Production</td><td id="rv_fpa">—</td></tr>
                    <tr><td>Uses Iodized Salt</td><td id="rv_iodized">—</td></tr>
                    <tr><td>Uses IFR</td><td id="rv_ifr">—</td></tr>
                    <tr><td>Dwelling Type</td><td id="rv_dwelling">—</td></tr>
                    <tr><td>Total Family Income</td><td id="rv_income">—</td></tr>
                    <tr><td>Remarks</td><td id="rv_remarks">—</td></tr>
                </table>
                </div>
            </div>

            <?php if ($isEdit && empty($family['source_user_id'])): ?>
            <div class="card shadow-sm mb-4" style="border-color:rgba(196,114,42,.3)">
                <div class="card-body" style="background:rgba(196,114,42,.04)">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div style="width:36px;height:36px;background:var(--kn-orange);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem;flex-shrink:0">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <div>
                            <div style="font-size:.95rem;font-weight:700;color:var(--kn-dark)">No Resident Account Linked</div>
                            <div style="font-size:.82rem;color:var(--kn-muted)">Register the Head of Family to enable online profile updates</div>
                        </div>
                    </div>
                    <a href="index.php?action=registerResident&prefill_family_id=<?= $family['family_id'] ?>&prefill_name=<?= urlencode(($head['last_name'] ?? '') . ', ' . ($head['first_name'] ?? '')) ?>"
                       class="btn btn-sm"
                       style="background:var(--kn-orange);color:#fff;border-radius:8px;padding:.45rem 1.1rem;font-weight:600;font-size:.88rem">
                        <i class="bi bi-person-plus-fill me-1"></i> Register Resident Account
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <div class="nav-bar">
                <button type="button" class="btn btn-prev px-4" onclick="goStep(4)">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <span class="text-muted" style="font-size:.88rem">Step 5 of 5</span>
                <button type="submit" class="btn btn-kn px-5">
                    <i class="bi bi-floppy me-1"></i> <?= $isEdit ? 'Update Profile' : 'Save Profile' ?>
                </button>
            </div>
        </div>

    </form>
</div><!-- /container -->

<!-- Delete Child Modal -->
<div class="modal-overlay" id="deleteChildModal">
    <div class="modal-box">
        <div class="modal-title">Remove Child</div>
        <div class="modal-message">Are you sure you want to remove this child from the profile?</div>
        <div class="modal-actions">
            <button type="button" class="btn-modal-cancel" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="btn-modal-confirm" onclick="confirmDeleteChild()">Remove</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let currentStep = 1;
const totalSteps = 5;
let childCount = 0;
let childToDelete = null;

function goStep(n) {
    // hide current
    document.getElementById('section-' + currentStep).classList.remove('active');
    document.getElementById('pill-' + currentStep).classList.remove('active');
    document.getElementById('pill-' + currentStep).classList.add('done');

    currentStep = n;
    document.getElementById('section-' + currentStep).classList.add('active');

    // update all pills
    for (let i = 1; i <= totalSteps; i++) {
        const pill = document.getElementById('pill-' + i);
        pill.classList.remove('active', 'done');
        if (i < currentStep) pill.classList.add('done');
        else if (i === currentStep) pill.classList.add('active');
    }
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function showDeleteModal(childElement) {
    childToDelete = childElement;
    document.getElementById('deleteChildModal').classList.add('show');
}

function closeDeleteModal() {
    document.getElementById('deleteChildModal').classList.remove('show');
    childToDelete = null;
}

function confirmDeleteChild() {
    if (childToDelete) {
        childToDelete.remove();
        childToDelete = null;
    }
    closeDeleteModal();
    recalcAgeGroups();
}

// Close modal when clicking outside
document.getElementById('deleteChildModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

function addChild() {
    childCount++;
    const container = document.getElementById('childrenContainer');
    const childDiv = document.createElement('div');
    childDiv.className = 'child-row';
    childDiv.id = 'child-' + childCount;
    childDiv.style.cssText = 'background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:.75rem;padding:1rem 1.25rem;margin-bottom:.75rem;position:relative';
    childDiv.innerHTML = `
        <button type="button" class="btn-remove-child"
                style="position:absolute;top:.6rem;right:.75rem;background:none;border:none;color:#dc3545;font-size:1rem;cursor:pointer;padding:.1rem .3rem;line-height:1"
                onclick="showDeleteModal(document.getElementById('child-${childCount}'))">
            <i class="bi bi-x-lg"></i>
        </button>
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Last Name <span class="req">*</span></label>
                <input type="text" name="child${childCount}_last_name" class="form-control" placeholder="Surname">
            </div>
            <div class="col-md-3">
                <label class="form-label">First Name <span class="req">*</span></label>
                <input type="text" name="child${childCount}_first_name" class="form-control" placeholder="Given Name">
            </div>
            <div class="col-md-2">
                <label class="form-label">Middle Name</label>
                <input type="text" name="child${childCount}_middle_name" class="form-control" placeholder="Optional">
            </div>
            <div class="col-md-1">
                <label class="form-label">Suffix</label>
                <input type="text" name="child${childCount}_suffix" class="form-control" placeholder="Jr.">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="child${childCount}_dob" id="child${childCount}_dob" class="form-control"
                       onchange="calcAge('child${childCount}_dob','child${childCount}_age')">
                <div class="hint">Age: <span id="child${childCount}_age" class="age-badge">—</span></div>
            </div>
            <div class="col-md-1">
                <label class="form-label">Sex</label>
                <select name="child${childCount}_sex" class="form-select">
                    <option value="">—</option>
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>
            </div>
        </div>
    `;
    container.appendChild(childDiv);
}

function calcAge(dobId, badgeId) {
    const dob = document.getElementById(dobId).value;
    const badge = document.getElementById(badgeId);
    if (!dob) { badge.textContent = '—'; recalcAgeGroups(); return; }
    const today = new Date();
    const birth = new Date(dob);
    let months = (today.getFullYear() - birth.getFullYear()) * 12 + (today.getMonth() - birth.getMonth());
    if (months < 0) { badge.textContent = '—'; recalcAgeGroups(); return; }
    if (months < 24) {
        badge.textContent = months + ' month' + (months !== 1 ? 's' : '');
    } else {
        const yrs = Math.floor(months / 12);
        badge.textContent = yrs + ' year' + (yrs > 1 ? 's' : '') + ' old';
    }
    recalcAgeGroups();
}

function recalcAgeGroups() {
    let g0_5 = 0, g6_23 = 0, g24_59 = 0, g60plus = 0;
    const today = new Date();
    document.querySelectorAll('[id^="child"][id$="_dob"]').forEach(function(input) {
        if (!input.value) return;
        const birth = new Date(input.value);
        const months = (today.getFullYear() - birth.getFullYear()) * 12 + (today.getMonth() - birth.getMonth());
        if (months < 0) return;
        if (months <= 5)        g0_5++;
        else if (months <= 23)  g6_23++;
        else if (months <= 59)  g24_59++;
        else                    g60plus++;
    });
    document.getElementById('children_0_5mos').value   = g0_5   || '';
    document.getElementById('children_6_23mos').value  = g6_23  || '';
    document.getElementById('children_24_59mos').value = g24_59 || '';
    document.getElementById('children_60plus').value   = g60plus || '';
}

function syncPregnancyCheckbox(val) {
    const el = document.getElementById('isMotherProg');
    if (el) el.value = val && val !== 'Not Pregnant' && val !== 'Postpartum' ? '1' : '0';
}

function syncBreastfeedingCheckboxes(val) {
    const erf    = document.getElementById('isErf');
    const mixed  = document.getElementById('isMixed');
    const bottle = document.getElementById('isBottle');
    if (erf)    erf.value    = val === 'EBF (Exclusive Breastfeeding)' ? '1' : '0';
    if (mixed)  mixed.value  = val === 'Mixed Feeding'                 ? '1' : '0';
    if (bottle) bottle.value = val === 'Bottle Feeding'                ? '1' : '0';
}

function toggleFpOther(val) {
    const wrap = document.getElementById('fp_other_wrap');
    const input = document.getElementById('fp_method_other');
    if (!wrap) return;
    const sel = document.getElementById('fp_method_id');
    const selectedLabel = sel?.options[sel?.selectedIndex]?.text || '';
    const isOthers = selectedLabel.toLowerCase() === 'others';
    wrap.style.display = isOthers ? 'block' : 'none';
    if (input) input.required = isOthers;
    if (!isOthers && input) input.value = '';
}

// Run on load for edit mode
document.addEventListener('DOMContentLoaded', function() {
    // Initialize fp_other visibility on page load (for edit mode)
    toggleFpOther(document.getElementById('fp_method_id')?.value);
    // Initialize with 2 children by default for new forms
    // For edit mode, children will be loaded from PHP
    <?php if (!empty($children)): ?>
        // Load existing children for edit mode
        <?php foreach ($children as $idx => $child): ?>
        addChildWithData(
            <?= $idx + 1 ?>,
            <?= json_encode($child['last_name'] ?? '') ?>,
            <?= json_encode($child['first_name'] ?? '') ?>,
            <?= json_encode($child['middle_name'] ?? '') ?>,
            <?= json_encode($child['suffix'] ?? '') ?>,
            <?= json_encode($child['dob'] ?? '') ?>,
            <?= json_encode($child['sex'] ?? '') ?>
        );
        <?php endforeach; ?>
    <?php else: ?>
        // New form — start empty, BNS adds children as needed
    <?php endif; ?>
    
    updateSpouseLabel();
    recalcAgeGroups();
});

function addChildWithData(num, lastName, firstName, middleName, suffix, dob, sex) {
    childCount = num;
    const container = document.getElementById('childrenContainer');
    const childDiv = document.createElement('div');
    childDiv.className = 'child-row';
    childDiv.id = 'child-' + num;
    childDiv.style.cssText = 'background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:.75rem;padding:1rem 1.25rem;margin-bottom:.75rem;position:relative';
    childDiv.innerHTML = `
        <button type="button" class="btn-remove-child"
                style="position:absolute;top:.6rem;right:.75rem;background:none;border:none;color:#dc3545;font-size:1rem;cursor:pointer;padding:.1rem .3rem;line-height:1"
                onclick="showDeleteModal(document.getElementById('child-${num}'))">
            <i class="bi bi-x-lg"></i>
        </button>
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Last Name <span class="req">*</span></label>
                <input type="text" name="child${num}_last_name" class="form-control" placeholder="Surname" value="${lastName}">
            </div>
            <div class="col-md-3">
                <label class="form-label">First Name <span class="req">*</span></label>
                <input type="text" name="child${num}_first_name" class="form-control" placeholder="Given Name" value="${firstName}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Middle Name</label>
                <input type="text" name="child${num}_middle_name" class="form-control" placeholder="Optional" value="${middleName}">
            </div>
            <div class="col-md-1">
                <label class="form-label">Suffix</label>
                <input type="text" name="child${num}_suffix" class="form-control" placeholder="Jr." value="${suffix}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="child${num}_dob" id="child${num}_dob" class="form-control"
                       value="${dob}" onchange="calcAge('child${num}_dob','child${num}_age')">
                <div class="hint">Age: <span id="child${num}_age" class="age-badge">—</span></div>
            </div>
            <div class="col-md-1">
                <label class="form-label">Sex</label>
                <select name="child${num}_sex" class="form-select">
                    <option value="">—</option>
                    <option value="M" ${sex === 'M' ? 'selected' : ''}>Male</option>
                    <option value="F" ${sex === 'F' ? 'selected' : ''}>Female</option>
                </select>
            </div>
        </div>
    `;
    container.appendChild(childDiv);
    calcAge('child' + num + '_dob', 'child' + num + '_age');
}

function gv(name) {
    const el = document.querySelector('[name="' + name + '"]');
    if (!el) return '—';
    if (el.type === 'radio') {
        const checked = document.querySelector('[name="' + name + '"]:checked');
        return checked ? checked.value : '—';
    }
    return el.value.trim() || '—';
}

function getChecked(names) {
    const labels = [];
    names.forEach(function(n) {
        const el = document.querySelector('[name="' + n + '"]');
        if (el && el.checked) {
            const lbl = document.querySelector('label[for="' + el.id + '"]');
            labels.push(lbl ? lbl.textContent.trim() : n);
        }
    });
    return labels.length ? labels.join(', ') : 'None';
}

function buildReview() {
    document.getElementById('rv_hh').textContent         = gv('hh_number');
    document.getElementById('rv_purok').textContent      = gv('purok');
    document.getElementById('rv_hh_members').textContent = gv('num_hh_members');
    document.getElementById('rv_c05').textContent        = gv('children_0_5mos');
    document.getElementById('rv_c623').textContent       = gv('children_6_23mos');
    document.getElementById('rv_c2459').textContent      = gv('children_24_59mos');
    document.getElementById('rv_c60').textContent        = gv('children_60plus');

    // Build full names for review
    const buildFullName = (prefix) => {
        const last = gv(prefix + '_last_name');
        const first = gv(prefix + '_first_name');
        const middle = gv(prefix + '_middle_name');
        const suffix = gv(prefix + '_suffix');
        
        if (!last && !first) return '—';
        
        let name = last;
        if (first) {
            name += ', ' + first;
            if (middle) name += ' ' + middle.charAt(0) + '.';
        }
        if (suffix) name += ' ' + suffix;
        return name;
    };
    
    document.getElementById('rv_head_name').textContent  = buildFullName('head');
    document.getElementById('rv_head_sex').textContent   = gv('head_sex') === 'M' ? 'Male' : gv('head_sex') === 'F' ? 'Female' : '—';
    document.getElementById('rv_head_civil').textContent = document.querySelector('[name="head_civil_status"] option:checked')?.textContent || '—';
    document.getElementById('rv_head_occ').textContent   = gv('head_occupation');
    document.getElementById('rv_head_educ').textContent  = document.querySelector('[name="head_educ_id"] option:checked')?.textContent || '—';
    document.getElementById('rv_wife_name').textContent  = buildFullName('wife');
    document.getElementById('rv_wife_sex').textContent   = gv('wife_sex') === 'M' ? 'Male' : gv('wife_sex') === 'F' ? 'Female' : '—';
    document.getElementById('rv_wife_occ').textContent   = gv('wife_occupation');
    document.getElementById('rv_wife_educ').textContent  = document.querySelector('[name="wife_educ_id"] option:checked')?.textContent || '—';

    const chk = (id) => document.getElementById(id)?.checked ? 'Yes' : 'No';
    document.getElementById('rv_mother').textContent   = document.querySelector('[name="wife_pregnancy_status"] option:checked')?.textContent || '—';
    document.getElementById('rv_erf').textContent      = document.querySelector('[name="wife_breastfeeding_status"] option:checked')?.textContent || '—';
    document.getElementById('rv_fp').textContent       = document.querySelector('[name="fp_method_id"] option:checked')?.textContent || '—';
    document.getElementById('rv_toilet').textContent   = document.querySelector('[name="toilet_type_id"]:checked')?.closest('.check-card')?.querySelector('label')?.textContent || '—';
    document.getElementById('rv_water').textContent    = document.querySelector('[name="water_source_id"]:checked')?.closest('.check-card')?.querySelector('label')?.textContent || '—';
    document.getElementById('rv_dwelling').textContent = document.querySelector('[name="dwelling_type_id"]:checked')?.closest('.check-card')?.querySelector('label')?.textContent || '—';

    const fpaChecked = [];    document.querySelectorAll('[name="food_activity_ids[]"]:checked').forEach(el => {
        fpaChecked.push(el.closest('.check-card').querySelector('label').textContent.trim());
    });
    document.getElementById('rv_fpa').textContent     = fpaChecked.length ? fpaChecked.join(', ') : 'None';
    document.getElementById('rv_iodized').textContent = chk('iodized');
    document.getElementById('rv_ifr').textContent     = chk('ifr');
    const inc = gv('total_income');
    document.getElementById('rv_income').textContent  = inc !== '—' ? '₱' + parseFloat(inc).toLocaleString() : '—';
    document.getElementById('rv_remarks').textContent = gv('remarks');
}

// Dynamic spouse label based on head sex
function updateSpouseLabel() {
    const checked = document.querySelector('[name="head_sex"]:checked');
    const label   = document.getElementById('spouse-label-text');
    if (!label) return;
    label.textContent = 'Spouse';
}

document.querySelectorAll('[name="head_sex"]').forEach(function(el) {
    el.addEventListener('change', updateSpouseLabel);
});
</script>
</div><!-- /max-width wrapper -->

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
