<?php
$pageTitle = 'Family Profile';
$activeNav = 'family_profile';
include __DIR__ . '/../templates/mother_layout.php';

$errors      = $_SESSION['errors']      ?? [];
$flashError  = $_SESSION['flash_error'] ?? null;
unset($_SESSION['errors'], $_SESSION['flash_error']);

$profileStatus = $user['profile_status'] ?? 'Draft';
$readOnly      = $readOnly ?? false;
$wasReturned   = ($profileStatus === 'Returned') || !empty($user['return_reason']);

// Lookup data
$waterSources  = $lookups['water_sources']  ?? [];
$toiletTypes   = $lookups['toilet_types']   ?? [];
$dwellingTypes = $lookups['dwelling_types'] ?? [];

// Pre-fill values
$v = fn(string $key, $default = '') => htmlspecialchars($user[$key] ?? $default);
$hv = fn(string $key, $default = '') => htmlspecialchars($healthProfile[$key] ?? $default);
$hh = fn(string $key, $default = '') => htmlspecialchars($household[$key] ?? $default);
?>

<?php if ($readOnly): ?>
<div class="alert-kn-info d-flex align-items-center gap-2 mb-4 p-3">
    <i class="bi bi-info-circle-fill fs-5" style="color:var(--kn-green)"></i>
    <div>
        <strong>Profile <?= htmlspecialchars($profileStatus) ?></strong> —
        <?php if ($profileStatus === 'Submitted'): ?>
            Your profile is awaiting BNS validation. No changes can be made at this time.
        <?php else: ?>
            Your profile has been validated. It is now read-only.
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php if ($wasReturned): ?>
<div class="mb-4 p-3" style="background:rgba(196,114,42,.08);border:1.5px solid rgba(196,114,42,.35);border-radius:.85rem">
    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="bi bi-arrow-counterclockwise fs-5" style="color:var(--kn-orange)"></i>
        <strong style="color:var(--kn-orange)">Profile Returned for Correction</strong>
    </div>
    <p class="mb-2" style="font-size:.9rem;color:var(--kn-dark)">
        Your BNS reviewed your profile and found issues that need to be corrected. Please update the information below and click <strong>"Resubmit for Validation"</strong> when done.
    </p>
    <?php if (!empty($user['return_reason'])): ?>
    <div style="background:rgba(196,114,42,.06);border-left:3px solid var(--kn-orange);padding:.6rem .9rem;border-radius:0 6px 6px 0;font-size:.9rem">
        <strong>BNS Note:</strong> <?= nl2br(htmlspecialchars($user['return_reason'])) ?>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($errors): ?>
<div class="alert-kn-error d-flex align-items-start gap-2 mb-3 p-3">
    <i class="bi bi-exclamation-triangle-fill mt-1" style="color:var(--kn-orange)"></i>
    <ul class="mb-0 ps-3">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if ($flashError): ?>
<div class="alert-kn-error d-flex align-items-center gap-2 mb-3 p-3">
    <i class="bi bi-exclamation-triangle-fill" style="color:var(--kn-orange)"></i>
    <?= htmlspecialchars($flashError) ?>
</div>
<?php endif; ?>

<!-- Step Indicator -->
<div class="d-flex align-items-center gap-2 mb-4" id="step-indicator">
    <div class="step-pill active" id="pill-1">1 Personal Data</div>
    <div class="step-divider"></div>
    <div class="step-pill" id="pill-2">2 Family</div>
    <div class="step-divider"></div>
    <div class="step-pill" id="pill-3">3 Household Details</div>
    <div class="step-divider"></div>
    <div class="step-pill" id="pill-4">4 Children</div>
</div>

<form method="POST" id="wizard-form" novalidate>
    <!-- Hidden field to track BNS family profile linkage -->
    <?php if (!empty($household['bns_family_id'])): ?>
        <input type="hidden" name="bns_family_id" value="<?= (int)$household['bns_family_id'] ?>">
    <?php endif; ?>

<!-- ── Step 1: Personal Data ─────────────────────────────────────────────── -->
<div class="wizard-step" id="step-1">
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold"><i class="bi bi-person-fill me-2"></i>Personal Data</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control"
                           value="<?= $v('first_name') ?>"
                           <?= $readOnly ? 'readonly' : '' ?> required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control"
                           value="<?= $v('middle_name') ?>"
                           <?= $readOnly ? 'readonly' : '' ?>>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control"
                           value="<?= $v('last_name') ?>"
                           <?= $readOnly ? 'readonly' : '' ?> required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Birthdate <span class="text-danger">*</span></label>
                    <input type="date" name="birthdate" class="form-control"
                           value="<?= $v('birthdate') ?>"
                           <?= $readOnly ? 'readonly' : '' ?> required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" id="gender" class="form-select"
                            <?= $readOnly ? 'disabled' : '' ?> required>
                        <option value="">— Select —</option>
                        <?php foreach (['Male','Female','Other'] as $g): ?>
                            <option value="<?= $g ?>" <?= ($user['gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($readOnly && !empty($user['gender'])): ?>
                        <input type="hidden" name="gender" value="<?= $v('gender') ?>">
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Civil Status <span class="text-danger">*</span></label>
                    <select name="civil_status" id="civil_status" class="form-select"
                            <?= $readOnly ? 'disabled' : '' ?> required>
                        <option value="">— Select —</option>
                        <?php foreach (['Single','Married','Widowed','Separated','Annulled'] as $cs): ?>
                            <option value="<?= $cs ?>" <?= ($user['civil_status'] ?? '') === $cs ? 'selected' : '' ?>><?= $cs ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($readOnly && !empty($user['civil_status'])): ?>
                        <input type="hidden" name="civil_status" value="<?= $v('civil_status') ?>">
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                    <input type="tel" name="contact" class="form-control"
                           value="<?= $v('contact') ?>"
                           <?= $readOnly ? 'readonly' : '' ?> required>
                </div>
            </div>
        </div>
    </div>
    <?php if (!$readOnly): ?>
    <div class="d-flex justify-content-end">
        <button type="button" class="btn-kn-primary" onclick="goToStep(2)">
            Next <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- ── Step 2: Family ───────────────────────────────────────────── -->
<div class="wizard-step d-none" id="step-2">
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold"><i class="bi bi-people-fill me-2"></i>Family</div>
        <div class="card-body">

            <!-- Health fields (Female only — for Male users, these appear in Wife section below) -->
            <div id="health-fields" class="<?= ($user['gender'] ?? '') !== 'Female' ? 'd-none' : '' ?>">
                <h6 class="text-muted mb-3">Health Information</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Pregnancy Status <span class="text-danger">*</span></label>
                        <select name="pregnancy_status" id="pregnancy_status" class="form-select"
                                <?= $readOnly ? 'disabled' : '' ?>>
                            <option value="">— Select —</option>
                            <?php foreach (['Not Pregnant','Pregnant 1st Trimester','Pregnant 2nd Trimester','Pregnant 3rd Trimester','Postpartum'] as $ps): ?>
                                <option value="<?= $ps ?>" <?= ($healthProfile['pregnancy_status'] ?? '') === $ps ? 'selected' : '' ?>><?= $ps ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($readOnly && !empty($healthProfile['pregnancy_status'])): ?>
                            <input type="hidden" name="pregnancy_status" value="<?= $hv('pregnancy_status') ?>">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Breastfeeding Status <span class="text-danger">*</span></label>
                        <select name="breastfeeding_status" id="breastfeeding_status" class="form-select"
                                <?= $readOnly ? 'disabled' : '' ?>>
                            <option value="">— Select —</option>
                            <?php foreach (['Not Breastfeeding','EBF (Exclusive Breastfeeding)','Mixed Feeding','Bottle Feeding'] as $bs): ?>
                                <option value="<?= $bs ?>" <?= ($healthProfile['breastfeeding_status'] ?? '') === $bs ? 'selected' : '' ?>><?= $bs ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($readOnly && !empty($healthProfile['breastfeeding_status'])): ?>
                            <input type="hidden" name="breastfeeding_status" value="<?= $hv('breastfeeding_status') ?>">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Couple Practice Family Planning</label>
                        <select name="fp_method_id" id="fp_method_id_wizard" class="form-select"
                                <?= $readOnly ? 'disabled' : '' ?>
                                onchange="toggleFpOther(this)">
                            <option value="">— Select method —</option>
                            <?php foreach ($lookups['fp_methods'] as $m): ?>
                                <option value="<?= $m['id'] ?>"
                                    <?= (int)($household['fp_method_id'] ?? 0) === (int)$m['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['label']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($readOnly && !empty($household['fp_method_id'])): ?>
                            <input type="hidden" name="fp_method_id" value="<?= (int)$household['fp_method_id'] ?>">
                        <?php endif; ?>
                        <div id="fp_other_wrap_wizard" style="margin-top:.5rem;display:none">
                            <input type="text" name="fp_method_other" id="fp_method_other_wizard"
                                   class="form-control" placeholder="Please specify…"
                                   maxlength="100" <?= $readOnly ? 'disabled' : '' ?>
                                   value="<?= htmlspecialchars($household['fp_method_other'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                <hr>
            </div>

            <!-- Spouse tag (Married only) -->
            <div id="spouse-tag-section" class="<?= ($user['civil_status'] ?? '') !== 'Married' ? 'd-none' : '' ?>">
                <?php $isMale = ($user['gender'] ?? '') === 'Male'; ?>
                <h6 class="text-muted mb-3" id="spouse_section_title">
                    <?= $isMale ? 'Wife Information' : 'Husband Information' ?>
                </h6>
                <div class="row g-3 mb-3">

                    <!-- Row 1: Last Name | First Name | Middle Name | Suffix -->
                    <div class="col-md-4">
                        <label class="form-label" id="spouse_name_label">
                            <span id="spouse_name_role_lbl"><?= $isMale ? 'Wife' : 'Husband' ?></span> Last Name
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="spouse_last_name" id="spouse_last_name" class="form-control"
                               placeholder="Surname"
                               value="<?= htmlspecialchars($household['spouse_last_name'] ?? '') ?>"
                               <?= $readOnly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            <span class="spouse-role-lbl"><?= $isMale ? 'Wife' : 'Husband' ?></span> First Name
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="spouse_first_name" id="spouse_first_name" class="form-control"
                               placeholder="Given Name"
                               value="<?= htmlspecialchars($household['spouse_first_name'] ?? '') ?>"
                               <?= $readOnly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="spouse_middle_name" id="spouse_middle_name" class="form-control"
                               placeholder="Optional"
                               value="<?= htmlspecialchars($household['spouse_middle_name'] ?? '') ?>"
                               <?= $readOnly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Suffix</label>
                        <input type="text" name="spouse_suffix" id="spouse_suffix" class="form-control"
                               placeholder="Jr."
                               value="<?= htmlspecialchars($household['spouse_suffix'] ?? '') ?>"
                               <?= $readOnly ? 'readonly' : '' ?>>
                    </div>
                    <!-- Hidden combined spouse_name for HOF dropdown compatibility -->
                    <input type="hidden" name="spouse_name" id="spouse_name"
                           value="<?= htmlspecialchars($household['spouse_name'] ?? '') ?>">

                    <!-- Row 2: Occupation | Monthly Income -->
                    <div class="col-md-6">
                        <label class="form-label" id="spouse_occ_label">
                            <?= $isMale ? 'Wife Occupation' : 'Husband Occupation' ?>
                        </label>
                        <input type="text" name="spouse_occupation" class="form-control"
                               placeholder="e.g. Farmer, Driver, Housewife"
                               value="<?= htmlspecialchars($spouseOccupation ?? '') ?>"
                               <?= $readOnly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" id="spouse_income_label">
                            <?= $isMale ? 'Wife Monthly Income' : 'Husband Monthly Income' ?> (&#8369;)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:rgba(107,122,58,.08);border:1.5px solid rgba(107,122,58,.25);color:var(--kn-green)">&#8369;</span>
                            <input type="number" name="spouse_monthly_income" class="form-control" min="0" step="0.01"
                                   placeholder="0.00"
                                   value="<?= htmlspecialchars($household['spouse_monthly_income'] ?? '') ?>"
                                   <?= $readOnly ? 'readonly' : '' ?>>
                        </div>
                        <div style="font-size:.8rem;color:var(--kn-muted);margin-top:.25rem">Leave blank if spouse has no income</div>
                    </div>

                    <!-- Row 3: Education | Link to Account -->
                    <div class="col-md-6">
                        <label class="form-label" id="spouse_educ_label">
                            <span class="spouse-role-lbl"><?= $isMale ? 'Wife' : 'Husband' ?></span> Education
                        </label>
                        <select name="spouse_educ_level_id" class="form-select" <?= $readOnly ? 'disabled' : '' ?>>
                            <option value="">— Select —</option>
                            <?php foreach ($lookups['educ_levels'] as $el): ?>
                            <option value="<?= $el['id'] ?>"
                                <?= ($household['spouse_educ_level_id'] ?? '') == $el['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($el['label']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if (!$readOnly): ?>
                    <div class="col-md-6">
                        <label class="form-label">Link to Registered Account <span style="font-weight:400;color:var(--kn-muted)">(optional)</span></label>
                        <input type="text" id="spouse_search" class="form-control"
                               placeholder="Search by name or email…"
                               autocomplete="off"
                               value="<?= htmlspecialchars($spouseUser['display_name'] ?? '') ?>">
                        <div id="spouse_results" class="list-group mt-1 position-absolute" style="z-index:1000;min-width:300px;"></div>
                        <input type="hidden" name="spouse_user_id" id="spouse_user_id"
                               value="<?= htmlspecialchars($spouseUser['user_id'] ?? '') ?>">
                        <div id="spouse_display" class="mt-1" style="font-size:.82rem;color:var(--kn-green)">
                            <?php if ($spouseUser): ?>
                                <i class="bi bi-check-circle-fill me-1"></i><?= htmlspecialchars($spouseUser['display_name']) ?> (linked)
                                <a href="#" id="unlink_spouse" style="font-size:.8rem;color:var(--kn-orange);margin-left:.5rem">Remove link</a>
                            <?php endif; ?>
                        </div>
                        <div style="font-size:.8rem;color:var(--kn-muted);margin-top:.25rem">
                            Linking allows the spouse to receive notifications. Not required.
                        </div>
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="spouse_user_id" id="spouse_user_id"
                               value="<?= htmlspecialchars($spouseUser['user_id'] ?? '') ?>">
                    <?php endif; ?>

                    <!-- Wife Health Information (Male user only — wife is the female) -->
                    <div class="col-12 spouse-health-fields" id="spouse-health-block" style="display: <?= $isMale ? 'block' : 'none' ?>">
                        <div class="pt-2 pb-1 mb-2" style="border-top:1.5px solid rgba(107,122,58,.12)">
                            <span style="font-size:.82rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--kn-green)">
                                <i class="bi bi-heart-pulse me-1"></i> Wife Health Information
                            </span>
                        </div>
                        <div class="row g-3">
                            <!-- Row: Pregnancy Status | Breastfeeding Status -->
                            <div class="col-md-6">
                                <label class="form-label">Wife Pregnancy Status</label>
                                <select name="spouse_pregnancy_status" id="spouse_pregnancy_status" class="form-select" <?= $readOnly ? 'disabled' : '' ?>>
                                    <option value="">— Select —</option>
                                    <?php foreach (['Not Pregnant','Pregnant 1st Trimester','Pregnant 2nd Trimester','Pregnant 3rd Trimester','Postpartum'] as $ps): ?>
                                        <option value="<?= $ps ?>" <?= ($household['spouse_pregnancy_status'] ?? '') === $ps ? 'selected' : '' ?>><?= $ps ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Wife Breastfeeding Status</label>
                                <select name="spouse_breastfeeding_status" id="spouse_breastfeeding_status" class="form-select" <?= $readOnly ? 'disabled' : '' ?>>
                                    <option value="">— Select —</option>
                                    <?php foreach (['Not Breastfeeding','EBF (Exclusive Breastfeeding)','Mixed Feeding','Bottle Feeding'] as $bs): ?>
                                        <option value="<?= $bs ?>" <?= ($household['spouse_breastfeeding_status'] ?? '') === $bs ? 'selected' : '' ?>><?= $bs ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Row: FP Method (half width, left-aligned) -->
                            <div class="col-md-6">
                                <label class="form-label">Couple Practice Family Planning</label>
                                <select name="fp_method_id" id="fp_method_id_wife" class="form-select"
                                        <?= $readOnly ? 'disabled' : '' ?>
                                        onchange="toggleFpOther(this)">
                                    <option value="">— Select method —</option>
                                    <?php foreach ($lookups['fp_methods'] as $m): ?>
                                        <option value="<?= $m['id'] ?>"
                                            <?= (int)($household['fp_method_id'] ?? 0) === (int)$m['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($m['label']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($readOnly && !empty($household['fp_method_id'])): ?>
                                    <input type="hidden" name="fp_method_id" value="<?= (int)$household['fp_method_id'] ?>">
                                <?php endif; ?>
                                <div id="fp_other_wrap_wife" style="margin-top:.5rem;display:none">
                                    <input type="text" name="fp_method_other" id="fp_method_other_wife"
                                           class="form-control" placeholder="Please specify…"
                                           maxlength="100" <?= $readOnly ? 'disabled' : '' ?>
                                           value="<?= htmlspecialchars($household['fp_method_other'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <hr>
            </div>

            <!-- Head of Family -->
            <h6 class="text-muted mb-3">Head of Family</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Designate Head of Family</label>
                    <div style="font-size:.8rem;color:var(--kn-muted);margin-bottom:.4rem">
                        <i class="bi bi-info-circle me-1"></i>
                        The member with the higher income is the Head. If incomes are equal or both are zero, you decide.
                    </div>
                    <?php
                        // Use typed spouse_name first, fall back to linked account name
                        $spouseDisplayName = $household['spouse_name'] ?? ($spouseUser['display_name'] ?? null);
                        $spouseHofId       = $spouseUser['user_id'] ?? null;
                        // For unregistered spouses we use value="spouse" as a sentinel
                        $hofVal            = $household['hof_user_id'] ?? '';
                    ?>
                    <select name="hof_user_id" id="hof_user_id" class="form-select"
                            <?= $readOnly ? 'disabled' : '' ?>>
                        <option value="">— Default to myself —</option>
                        <option value="<?= (int)($user['user_id'] ?? 0) ?>"
                            <?= ($hofVal != '' && $hofVal == ($user['user_id'] ?? '')) ? 'selected' : '' ?>>
                            <?= $v('first_name') ?> <?= $v('last_name') ?> (Me)
                        </option>
                        <?php if ($spouseDisplayName): ?>
                        <option value="<?= $spouseHofId ? (int)$spouseHofId : 'spouse' ?>"
                            <?= ($hofVal === 'spouse' || ($spouseHofId && $hofVal == $spouseHofId)) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($spouseDisplayName) ?> (Spouse)
                        </option>
                        <?php endif; ?>
                    </select>
                    <?php if ($readOnly && !empty($hofVal)): ?>
                        <input type="hidden" name="hof_user_id" value="<?= htmlspecialchars($hofVal) ?>">
                    <?php endif; ?>
                </div>
            </div>

            <hr class="my-3">

            <!-- Income & Occupation -->
            <h6 class="text-muted mb-3">Income, Occupation &amp; Education</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Occupation</label>
                    <input type="text" name="occupation" class="form-control"
                           placeholder="e.g. Farmer, Teacher, Housewife"
                           value="<?= htmlspecialchars($healthProfile['occupation'] ?? '') ?>"
                           <?= $readOnly ? 'readonly' : '' ?>>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Monthly Income (&#8369;)</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:rgba(107,122,58,.08);border:1.5px solid rgba(107,122,58,.25);color:var(--kn-green)">&#8369;</span>
                        <input type="number" name="monthly_income" class="form-control" min="0" step="0.01"
                               placeholder="0.00"
                               value="<?= htmlspecialchars($healthProfile['monthly_income'] ?? '') ?>"
                               <?= $readOnly ? 'readonly' : '' ?>>
                    </div>
                    <div style="font-size:.8rem;color:var(--kn-muted);margin-top:.25rem">Your personal monthly income</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Educational Attainment <span class="text-danger">*</span></label>
                    <select name="educ_level_id" class="form-select" <?= $readOnly ? 'disabled' : '' ?>>
                        <option value="">— Select —</option>
                        <?php foreach ($lookups['educ_levels'] as $e): ?>
                            <option value="<?= $e['id'] ?>"
                                <?= (int)($healthProfile['educ_level_id'] ?? 0) === (int)$e['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($readOnly && !empty($healthProfile['educ_level_id'])): ?>
                        <input type="hidden" name="educ_level_id" value="<?= (int)$healthProfile['educ_level_id'] ?>">
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
    <?php if (!$readOnly): ?>
    <div class="d-flex justify-content-between">
        <button type="button" class="btn-kn-outline" onclick="goToStep(1)">
            <i class="bi bi-arrow-left me-1"></i> Back
        </button>
        <button type="button" class="btn-kn-primary" onclick="goToStep(3)">
            Next <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- ── Step 3: Household Details ─────────────────────────────────────────── -->
<div class="wizard-step d-none" id="step-3">
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold"><i class="bi bi-house-fill me-2"></i>Household Details</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Water Source</label>
                    <select name="water_source_id" class="form-select" <?= $readOnly ? 'disabled' : '' ?>>
                        <option value="">— Select —</option>
                        <?php foreach ($waterSources as $ws): ?>
                            <option value="<?= $ws['id'] ?>"
                                <?= ($household['water_source_id'] ?? '') == $ws['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ws['label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($readOnly && !empty($household['water_source_id'])): ?>
                        <input type="hidden" name="water_source_id" value="<?= (int)$household['water_source_id'] ?>">
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Toilet Type</label>
                    <select name="toilet_type_id" class="form-select" <?= $readOnly ? 'disabled' : '' ?>>
                        <option value="">— Select —</option>
                        <?php foreach ($toiletTypes as $tt): ?>
                            <option value="<?= $tt['id'] ?>"
                                <?= ($household['toilet_type_id'] ?? '') == $tt['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tt['label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($readOnly && !empty($household['toilet_type_id'])): ?>
                        <input type="hidden" name="toilet_type_id" value="<?= (int)$household['toilet_type_id'] ?>">
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dwelling Type</label>
                    <select name="dwelling_type_id" class="form-select" <?= $readOnly ? 'disabled' : '' ?>>
                        <option value="">— Select —</option>
                        <?php foreach ($dwellingTypes as $dt): ?>
                            <option value="<?= $dt['id'] ?>"
                                <?= ($household['dwelling_type_id'] ?? '') == $dt['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dt['label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($readOnly && !empty($household['dwelling_type_id'])): ?>
                        <input type="hidden" name="dwelling_type_id" value="<?= (int)$household['dwelling_type_id'] ?>">
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Purok / Street</label>
                    <input type="text" name="purok" class="form-control"
                           value="<?= $hh('purok') ?>"
                           <?= $readOnly ? 'readonly' : '' ?>>
                </div>
                <div class="col-md-6">
                    <label class="form-label"># of Household Members <span style="font-weight:400;font-size:.82rem;color:var(--kn-muted)">(optional)</span></label>
                    <input type="number" name="num_hh_members" class="form-control" min="1"
                           placeholder="e.g. 5"
                           value="<?= htmlspecialchars($household['num_hh_members'] ?? '') ?>"
                           <?= $readOnly ? 'readonly' : '' ?>>
                    <div style="font-size:.8rem;color:var(--kn-muted);margin-top:.25rem">Total number of people living in your household</div>
                </div>
                <div class="col-12 d-flex align-items-center gap-4 pt-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="uses_iodized_salt"
                               id="uses_iodized_salt" value="1"
                               <?= !empty($household['uses_iodized_salt']) ? 'checked' : '' ?>
                               <?= $readOnly ? 'disabled' : '' ?>>
                        <label class="form-check-label" for="uses_iodized_salt">
                            Uses Iodized Salt
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="uses_ifr"
                               id="uses_ifr" value="1"
                               <?= !empty($household['uses_ifr']) ? 'checked' : '' ?>
                               <?= $readOnly ? 'disabled' : '' ?>>
                        <label class="form-check-label" for="uses_ifr">
                            Uses IFR (Iron Fortified Rice)
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$readOnly): ?>
    <div class="d-flex justify-content-between">
        <button type="button" class="btn-kn-outline" onclick="goToStep(2)">
            <i class="bi bi-arrow-left me-1"></i> Back
        </button>
        <button type="button" class="btn-kn-primary" onclick="goToStep(4)">
            Next <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- ── Step 4: Children ───────────────────────────────────────────────────── -->
<div class="wizard-step d-none" id="step-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold"><i class="bi bi-emoji-smile-fill me-2"></i>Children</div>
        <div class="card-body">
            <p class="text-muted mb-3" style="font-size:.88rem">
                Add your children below. These are children living in your household who do not have their own accounts yet.
            </p>

            <!-- Age bracket counts (optional) -->
            <div class="mb-4 p-3" style="background:rgba(107,122,58,.04);border:1.5px solid rgba(107,122,58,.15);border-radius:.75rem">
                <div class="fw-semibold mb-2" style="font-size:.88rem;color:var(--kn-green);text-transform:uppercase;letter-spacing:.04em">
                    <i class="bi bi-bar-chart-fill me-1"></i> No. of Children by Age Group <span style="font-weight:400;text-transform:none;color:var(--kn-muted)">(optional)</span>
                </div>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <label class="form-label" style="font-size:.85rem">0–5 months old</label>
                        <input type="number" name="children_0_5mos" class="form-control" min="0"
                               placeholder="0"
                               value="<?= htmlspecialchars($household['children_0_5mos'] ?? '') ?>"
                               <?= $readOnly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label" style="font-size:.85rem">6–23 months old</label>
                        <input type="number" name="children_6_23mos" class="form-control" min="0"
                               placeholder="0"
                               value="<?= htmlspecialchars($household['children_6_23mos'] ?? '') ?>"
                               <?= $readOnly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label" style="font-size:.85rem">24–59 months old</label>
                        <input type="number" name="children_24_59mos" class="form-control" min="0"
                               placeholder="0"
                               value="<?= htmlspecialchars($household['children_24_59mos'] ?? '') ?>"
                               <?= $readOnly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label" style="font-size:.85rem">&gt;60 months old</label>
                        <input type="number" name="children_60plus" class="form-control" min="0"
                               placeholder="0"
                               value="<?= htmlspecialchars($household['children_60plus'] ?? '') ?>"
                               <?= $readOnly ? 'readonly' : '' ?>>
                    </div>
                </div>
            </div>

            <div id="children-list">
                <?php foreach ($children ?? [] as $i => $child): ?>
                <div class="child-entry border rounded p-3 mb-3 position-relative" style="background:rgba(107,122,58,.03);border-color:rgba(107,122,58,.2) !important">
                    <button type="button" class="btn-remove-child" onclick="removeChild(this)" title="Remove">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <input type="hidden" name="child_id[]" value="<?= (int)$child['child_id'] ?>">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="child_last_name[]" class="form-control"
                                   placeholder="Surname"
                                   value="<?= htmlspecialchars($child['last_name'] ?? '') ?>"
                                   <?= $readOnly ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="child_first_name[]" class="form-control"
                                   placeholder="Given Name"
                                   value="<?= htmlspecialchars($child['first_name'] ?? '') ?>"
                                   <?= $readOnly ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="child_middle_name[]" class="form-control"
                                   placeholder="Optional"
                                   value="<?= htmlspecialchars($child['middle_name'] ?? '') ?>"
                                   <?= $readOnly ? 'readonly' : '' ?>>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Suffix</label>
                            <input type="text" name="child_suffix[]" class="form-control"
                                   placeholder="Jr."
                                   value="<?= htmlspecialchars($child['suffix'] ?? '') ?>"
                                   <?= $readOnly ? 'readonly' : '' ?>>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="child_dob[]" class="form-control child-dob-input"
                                   value="<?= htmlspecialchars($child['dob'] ?? '') ?>"
                                   <?= $readOnly ? 'readonly' : '' ?>
                                   onchange="updateAgeDisplay(this)">
                            <div class="mt-1" style="font-size:.82rem;color:var(--kn-muted)">
                                Age: <span class="child-age-badge" style="background:rgba(107,122,58,.1);color:var(--kn-green);font-weight:700;padding:.15em .5em;border-radius:12px;font-size:.82rem">
                                    <?php
                                    if (!empty($child['dob'])) {
                                        $diff   = (new DateTime())->diff(new DateTime($child['dob']));
                                        $months = $diff->y * 12 + $diff->m;
                                        $yrs    = $diff->y;
                                        echo $months < 24
                                            ? $months . ' month' . ($months !== 1 ? 's' : '')
                                            : $yrs . ' year' . ($yrs > 1 ? 's' : '') . ' old';
                                    } else { echo '—'; }
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sex</label>
                            <select name="child_sex[]" class="form-select" <?= $readOnly ? 'disabled' : '' ?>>
                                <option value="">— Select —</option>
                                <option value="M" <?= ($child['sex'] ?? '') === 'M' ? 'selected' : '' ?>>Male</option>
                                <option value="F" <?= ($child['sex'] ?? '') === 'F' ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (!$readOnly): ?>
            <button type="button" class="btn-kn-outline mt-1" onclick="addChild()">
                <i class="bi bi-plus-circle me-1"></i> Add Child
            </button>
            <?php endif; ?>

            <?php if (empty($children)): ?>
            <p class="text-muted mt-3 mb-0" style="font-size:.85rem" id="no-children-msg">
                No children added yet. Click "Add Child" to add one.
            </p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$readOnly): ?>
    <div class="d-flex justify-content-between">
        <button type="button" class="btn-kn-outline" onclick="goToStep(3)">
            <i class="bi bi-arrow-left me-1"></i> Back
        </button>
        <div class="d-flex gap-2">
            <?php if ($wasReturned): ?>
                <!-- Returned profile: Show only Resubmit button with clear message -->
                <button type="submit" formaction="index.php?action=submitWizardProfile" class="btn-kn-submit">
                    <i class="bi bi-arrow-repeat me-1"></i> Resubmit for Validation
                </button>
            <?php else: ?>
                <!-- Draft profile: Show both Save Draft and Submit buttons -->
                <button type="submit" formaction="index.php?action=saveWizardDraft" class="btn-kn-draft">
                    <i class="bi bi-floppy-fill me-1"></i> Save Draft
                </button>
                <button type="submit" formaction="index.php?action=submitWizardProfile" class="btn-kn-submit">
                    <i class="bi bi-send-fill me-1"></i> Submit Profile
                </button>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

</form>

<style>
:root {
    --kn-green  : #6B7A3A;
    --kn-green-d: #556030;
    --kn-orange : #C4722A;
    --kn-cream  : #F5EDD6;
    --kn-dark   : #3D4A1E;
    --kn-muted  : rgba(61,74,30,.55);
}
.step-pill {
    padding: 6px 18px;
    border-radius: 20px;
    background: rgba(107,122,58,.1);
    color: var(--kn-muted);
    font-size: .88rem;
    font-weight: 600;
    white-space: nowrap;
    border: 1.5px solid rgba(107,122,58,.2);
}
.step-pill.active {
    background: var(--kn-green);
    color: #fff;
    border-color: var(--kn-green);
}
.step-divider {
    flex: 1;
    height: 2px;
    background: rgba(107,122,58,.15);
}
/* Cards */
.card { border: 1.5px solid rgba(107,122,58,.15); border-radius: .85rem; }
.card-header { background: rgba(107,122,58,.06); color: var(--kn-dark); border-bottom: 1.5px solid rgba(107,122,58,.12); border-radius: .85rem .85rem 0 0 !important; }
/* Inputs */
.form-control, .form-select {
    border: 1.5px solid rgba(107,122,58,.25);
    border-radius: 8px;
    font-size: .95rem;
}
.form-control:focus, .form-select:focus {
    border-color: var(--kn-green);
    box-shadow: 0 0 0 3px rgba(107,122,58,.12);
}
.form-control.is-invalid { border-color: var(--kn-orange); }
.form-label { font-size: .9rem; font-weight: 600; color: var(--kn-dark); }
.text-danger { color: var(--kn-orange) !important; }
/* Buttons */
.btn-kn-primary {
    background: var(--kn-green); color: #fff; border: none;
    border-radius: 8px; font-weight: 600; padding: .45rem 1.25rem;
    transition: background .2s;
}
.btn-kn-primary:hover { background: var(--kn-green-d); color: #fff; }
.btn-kn-outline {
    background: #fff; color: var(--kn-dark);
    border: 1.5px solid rgba(107,122,58,.3);
    border-radius: 8px; font-weight: 600; padding: .45rem 1.25rem;
    transition: background .2s;
}
.btn-kn-outline:hover { background: rgba(107,122,58,.06); }
.btn-kn-draft {
    background: #fff; color: var(--kn-green);
    border: 1.5px solid var(--kn-green);
    border-radius: 8px; font-weight: 600; padding: .45rem 1.25rem;
}
.btn-kn-draft:hover { background: rgba(107,122,58,.06); }
.btn-kn-submit {
    background: var(--kn-green); color: #fff; border: none;
    border-radius: 8px; font-weight: 600; padding: .45rem 1.25rem;
}
.btn-kn-submit:hover { background: var(--kn-green-d); color: #fff; }
/* Alerts */
.alert-kn-info {
    background: rgba(107,122,58,.08);
    border: 1.5px solid rgba(107,122,58,.25);
    color: var(--kn-dark);
    border-radius: .75rem;
}
.alert-kn-error {
    background: rgba(196,114,42,.08);
    border: 1.5px solid rgba(196,114,42,.3);
    color: var(--kn-dark);
    border-radius: .75rem;
}
/* Spouse search results */
#spouse_results .list-group-item-action:hover {
    background: rgba(107,122,58,.08);
    color: var(--kn-dark);
}
/* Checkbox */
.form-check-input:checked {
    background-color: var(--kn-green);
    border-color: var(--kn-green);
}
/* Remove child button */
.btn-remove-child {
    position: absolute;
    top: .6rem;
    right: .6rem;
    background: none;
    border: none;
    color: var(--kn-muted);
    font-size: .9rem;
    cursor: pointer;
    padding: .2rem .4rem;
    border-radius: 4px;
    transition: color .15s, background .15s;
}
.btn-remove-child:hover {
    color: #c03030;
    background: rgba(200,50,50,.08);
}
</style>

<script>
let currentStep = 1;

function toggleFpOtherWizard(val) {
    // Legacy alias — delegates to the generic handler using the wizard select
    const sel = document.getElementById('fp_method_id_wizard');
    if (sel) toggleFpOther(sel);
}

// Generic: pass the <select> element itself so it works for both fp selects
function toggleFpOther(sel) {
    const selectedLabel = sel?.options[sel?.selectedIndex]?.text?.trim().toLowerCase() || '';
    const isOthers = selectedLabel === 'others';

    // Find the sibling wrap div right after the select's parent
    const wrap  = sel.parentElement.querySelector('[id^="fp_other_wrap"]');
    const input = sel.parentElement.querySelector('[id^="fp_method_other"]');

    if (!wrap) return;
    wrap.style.display = isOthers ? 'block' : 'none';
    if (input) {
        input.required = isOthers;
        if (!isOthers) input.value = '';
    }
}

function goToStep(n) {
    if (n > currentStep && !validateStep(currentStep)) return;
    document.getElementById('step-' + currentStep).classList.add('d-none');
    document.getElementById('pill-' + currentStep).classList.remove('active');
    currentStep = n;
    document.getElementById('step-' + currentStep).classList.remove('d-none');
    document.getElementById('pill-' + currentStep).classList.add('active');
    window.scrollTo(0, 0);
    
    // Run HOF auto-select when navigating to Step 2
    if (n === 2) {
        runHofAutoSelect();
    }
}

function validateStep(n) {
    const step = document.getElementById('step-' + n);
    const inputs = step.querySelectorAll('[required]');
    let valid = true;
    inputs.forEach(el => {
        // Skip disabled or readonly fields
        if (el.disabled || el.readOnly) return;
        // Skip fields whose closest ancestor with d-none is within this step
        let ancestor = el.parentElement;
        let hidden = false;
        while (ancestor && ancestor !== step) {
            if (ancestor.classList.contains('d-none') ||
                ancestor.style.display === 'none' ||
                ancestor.style.visibility === 'hidden') {
                hidden = true;
                break;
            }
            ancestor = ancestor.parentElement;
        }
        if (hidden) return;

        el.classList.remove('is-invalid');
        const val = (el.tagName === 'SELECT' ? el.value : el.value.trim());
        if (!val) {
            el.classList.add('is-invalid');
            valid = false;
        }
    });
    return valid;
}

// Gender toggle
function applyGenderRules(gender) {
    const healthSection   = document.getElementById('health-fields');
    const spouseHealthBlk = document.getElementById('spouse-health-block');
    const psSelect  = document.getElementById('pregnancy_status');
    const bsSelect  = document.getElementById('breastfeeding_status');

    if (gender === 'Female') {
        // Female user: show own health fields at top, hide wife health block
        healthSection.classList.remove('d-none');
        if (psSelect) psSelect.required = true;
        if (bsSelect) bsSelect.required = true;
        if (spouseHealthBlk) spouseHealthBlk.style.display = 'none';
    } else {
        // Male user: hide own health fields, show wife health block inside wife section
        healthSection.classList.add('d-none');
        if (psSelect) { psSelect.required = false; psSelect.value = ''; }
        if (bsSelect) { bsSelect.required = false; bsSelect.value = ''; }
        // Wife health block visibility depends on civil status (only show if Married)
        const civilStatus = document.getElementById('civil_status')?.value;
        if (spouseHealthBlk) {
            spouseHealthBlk.style.display = civilStatus === 'Married' ? 'block' : 'none';
        }
    }

    // Update spouse label and placeholder based on gender
    const spouseLabel    = document.getElementById('spouse_name_label');
    const spouseTitle    = document.getElementById('spouse_section_title');
    const spouseOccLbl   = document.getElementById('spouse_occ_label');
    const spouseIncLbl   = document.getElementById('spouse_income_label');
    const spouseRoleLbls = document.querySelectorAll('.spouse-role-lbl, #spouse_name_role_lbl');

    if (gender === 'Male') {
        if (spouseTitle)   spouseTitle.textContent = 'Wife Information';
        if (spouseLabel)   spouseLabel.innerHTML   = 'Wife Last Name <span class="text-danger">*</span>';
        if (spouseOccLbl)  spouseOccLbl.textContent  = 'Wife Occupation';
        if (spouseIncLbl)  spouseIncLbl.innerHTML    = 'Wife Monthly Income (&#8369;)';
        spouseRoleLbls.forEach(el => el.textContent = 'Wife');
    } else {
        if (spouseTitle)   spouseTitle.textContent = 'Husband/Spouse Information';
        if (spouseLabel)   spouseLabel.innerHTML   = 'Husband Last Name <span class="text-danger">*</span>';
        if (spouseOccLbl)  spouseOccLbl.textContent  = 'Husband Occupation';
        if (spouseIncLbl)  spouseIncLbl.innerHTML    = 'Husband Monthly Income (&#8369;)';
        spouseRoleLbls.forEach(el => el.textContent = 'Husband');
    }
}

// Civil status toggle
function applyCivilStatusRules(status) {
    const spouseSection   = document.getElementById('spouse-tag-section');
    const spouseHealthBlk = document.getElementById('spouse-health-block');
    const gender = document.getElementById('gender')?.value;

    if (status === 'Married') {
        spouseSection.classList.remove('d-none');
        // Show wife health block only for male users
        if (spouseHealthBlk && gender === 'Male') {
            spouseHealthBlk.style.display = 'block';
        }
    } else {
        spouseSection.classList.add('d-none');
        if (spouseHealthBlk) spouseHealthBlk.style.display = 'none';
        const spouseId      = document.getElementById('spouse_user_id');
        const spouseDisplay = document.getElementById('spouse_display');
        const spouseSearch  = document.getElementById('spouse_search');
        if (spouseId)      spouseId.value      = '';
        if (spouseDisplay) spouseDisplay.innerHTML = '';
        const spouseLn = document.getElementById('spouse_last_name');
        const spouseFn = document.getElementById('spouse_first_name');
        const spouseMn = document.getElementById('spouse_middle_name');
        const spouseSx = document.getElementById('spouse_suffix');
        const spouseHidden = document.getElementById('spouse_name');
        if (spouseLn) spouseLn.value = '';
        if (spouseFn) spouseFn.value = '';
        if (spouseMn) spouseMn.value = '';
        if (spouseSx) spouseSx.value = '';
        if (spouseHidden) spouseHidden.value = '';
        if (spouseSearch)  spouseSearch.value  = '';
        updateHofSpouseOption(null, null);
    }
}

// Unlink spouse account (keep typed name, just remove the user_id link)
function bindUnlinkSpouse() {
    const unlinkBtn = document.getElementById('unlink_spouse');
    if (!unlinkBtn) return;
    unlinkBtn.addEventListener('click', function (e) {
        e.preventDefault();
        const spouseIdEl   = document.getElementById('spouse_user_id');
        const spouseDisp   = document.getElementById('spouse_display');
        const spouseSearch = document.getElementById('spouse_search');
        if (spouseIdEl)   spouseIdEl.value   = '';
        if (spouseDisp)   spouseDisp.innerHTML = '';
        if (spouseSearch) spouseSearch.value  = '';
        // Re-render HOF option with typed name only (no user_id)
        updateHofSpouseOption(getSpouseDisplayName(), null);
    });
}
// Bind on load in case page loaded with a linked spouse
document.addEventListener('DOMContentLoaded', function() {
    bindUnlinkSpouse();
    // Initialize fp_other visibility on page load (for edit mode)
    const fpWizSel = document.getElementById('fp_method_id_wizard');
    const fpWifeSel = document.getElementById('fp_method_id_wife');
    if (fpWizSel)  toggleFpOther(fpWizSel);
    if (fpWifeSel) toggleFpOther(fpWifeSel);
});

// HOF dropdown: update spouse option when spouse_name input changes
function updateHofSpouseOption(spouseName, spouseId) {
    const hofSelect = document.getElementById('hof_user_id');
    if (!hofSelect) return;
    const existing = hofSelect.querySelector('[data-spouse]');
    if (existing) existing.remove();
    if (spouseName && spouseName.trim()) {
        const opt = document.createElement('option');
        opt.value      = spouseId || 'spouse';
        opt.textContent = spouseName.trim() + ' (Spouse)';
        opt.setAttribute('data-spouse', '1');
        hofSelect.appendChild(opt);
        // Don't auto-select here - let the income-based logic handle it
    }
}

// Keep HOF dropdown in sync as mother types spouse name fields
function getSpouseDisplayName() {
    const ln = (document.getElementById('spouse_last_name')?.value  || '').trim();
    const fn = (document.getElementById('spouse_first_name')?.value || '').trim();
    const mn = (document.getElementById('spouse_middle_name')?.value || '').trim();
    const sx = (document.getElementById('spouse_suffix')?.value     || '').trim();
    if (!ln && !fn) return '';
    let name = ln + (fn ? ', ' + fn : '');
    if (mn) name += ' ' + mn;
    if (sx) name += ' ' + sx;
    // Also keep hidden spouse_name in sync
    const hidden = document.getElementById('spouse_name');
    if (hidden) hidden.value = name;
    return name;
}

['spouse_last_name','spouse_first_name','spouse_middle_name','spouse_suffix'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('input', function () {
            const linkedId = document.getElementById('spouse_user_id')?.value || null;
            updateHofSpouseOption(getSpouseDisplayName(), linkedId || null);
            // Re-trigger auto-select after updating spouse option
            const hofSelect = document.getElementById('hof_user_id');
            const myIncomeInput  = document.querySelector('input[name="monthly_income"]');
            const spsIncomeInput = document.querySelector('input[name="spouse_monthly_income"]');
            if (hofSelect && myIncomeInput && spsIncomeInput) {
                const mi = parseFloat(myIncomeInput.value || 0) || 0;
                const si = parseFloat(spsIncomeInput.value || 0) || 0;
                const hofNeedsReview = <?= (int)($household['hof_needs_review'] ?? 1) ?>;
                if (hofNeedsReview === 1) {
                    const sOpt = hofSelect.querySelector('[data-spouse]');
                    const mOpt = hofSelect.querySelector('option[value="<?= (int)($user['user_id'] ?? 0) ?>"]');
                    if (si > mi && sOpt) { sOpt.selected = true; }
                    else if (mi > si && mOpt) { mOpt.selected = true; }
                }
            }
        });
    }
});

// Function to run HOF auto-select logic
function runHofAutoSelect() {
    const hofSelect      = document.getElementById('hof_user_id');
    const myIncomeInput  = document.querySelector('input[name="monthly_income"]');
    const spsIncomeInput = document.querySelector('input[name="spouse_monthly_income"]');
    
    if (!hofSelect || !myIncomeInput || !spsIncomeInput) return;

    const spouseName = getSpouseDisplayName();
    if (!spouseName) return; // No spouse, nothing to auto-select

    const myIncome    = parseFloat(myIncomeInput.value || 0) || 0;
    const spouseIncome = parseFloat(spsIncomeInput.value || 0) || 0;

    // Always auto-select based on income
    if (spouseIncome > myIncome) {
        const spouseOpt = hofSelect.querySelector('[data-spouse]');
        if (spouseOpt) {
            spouseOpt.selected = true;
            hofSelect.value = spouseOpt.value;
        }
    } else if (myIncome > spouseIncome) {
        const myOpt = hofSelect.querySelector('option[value="<?= (int)($user['user_id'] ?? 0) ?>"]');
        if (myOpt) {
            myOpt.selected = true;
            hofSelect.value = myOpt.value;
        }
    }
}

// On page load: restore spouse option + auto-select HOF based on income
document.addEventListener('DOMContentLoaded', function () {
    const spouseIdEl     = document.getElementById('spouse_user_id');
    const hofSelect      = document.getElementById('hof_user_id');
    if (!hofSelect) return;

    const spouseName = getSpouseDisplayName();
    const spouseId   = spouseIdEl ? spouseIdEl.value.trim() : '';

    // Add spouse option first (without auto-selecting based on saved value)
    if (spouseName) {
        const existing = hofSelect.querySelector('[data-spouse]');
        if (existing) existing.remove();
        const opt = document.createElement('option');
        opt.value      = spouseId || 'spouse';
        opt.textContent = spouseName.trim() + ' (Spouse)';
        opt.setAttribute('data-spouse', '1');
        hofSelect.appendChild(opt);
    }

    // Run auto-select logic DIRECTLY HERE
    const myIncomeInput  = document.querySelector('input[name="monthly_income"]');
    const spsIncomeInput = document.querySelector('input[name="spouse_monthly_income"]');
    
    if (myIncomeInput && spsIncomeInput && spouseName) {
        const myIncome    = parseFloat(myIncomeInput.value || 0) || 0;
        const spouseIncome = parseFloat(spsIncomeInput.value || 0) || 0;

        // Always auto-select based on income (no hof_needs_review check)
        if (spouseIncome > myIncome) {
            const spouseOpt = hofSelect.querySelector('[data-spouse]');
            if (spouseOpt) {
                spouseOpt.selected = true;
                hofSelect.value = spouseOpt.value;
            }
        } else if (myIncome > spouseIncome) {
            const myOpt = hofSelect.querySelector('option[value="<?= (int)($user['user_id'] ?? 0) ?>"]');
            if (myOpt) {
                myOpt.selected = true;
                hofSelect.value = myOpt.value;
            }
        }
    }

    // Also re-run auto-select when either income field changes
    function reAutoSelectHof() {
        runHofAutoSelect();
    }
    if (myIncomeInput)  myIncomeInput.addEventListener('change',  reAutoSelectHof);
    if (spsIncomeInput) spsIncomeInput.addEventListener('change', reAutoSelectHof);
});

// Spouse AJAX search
const spouseSearch  = document.getElementById('spouse_search');
const spouseResults = document.getElementById('spouse_results');
let searchTimeout;

if (spouseSearch) {
    spouseSearch.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (q.length < 2) { spouseResults.innerHTML = ''; return; }
        searchTimeout = setTimeout(() => {
            fetch('index.php?action=searchUsers&q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(data => {
                    spouseResults.innerHTML = '';
                    if (!data.length) {
                        spouseResults.innerHTML = '<div class="list-group-item text-muted">No users found</div>';
                        return;
                    }
                    data.forEach(u => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action';
                        item.textContent = u.display_name + ' (' + u.email + ')';
                        item.addEventListener('click', () => {
                            document.getElementById('spouse_user_id').value = u.user_id;
                            // Fill hidden spouse_name with linked user's display name
                            const spouseHidden = document.getElementById('spouse_name');
                            if (spouseHidden) spouseHidden.value = u.display_name;
                            const spouseDisp = document.getElementById('spouse_display');
                            if (spouseDisp) {
                                spouseDisp.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>' + u.display_name + ' (linked) <a href="#" id="unlink_spouse" style="font-size:.8rem;color:var(--kn-orange);margin-left:.5rem">Remove link</a>';
                                bindUnlinkSpouse();
                            }
                            spouseSearch.value = u.display_name;
                            spouseResults.innerHTML = '';
                            updateHofSpouseOption(
                                getSpouseDisplayName() || u.display_name,
                                u.user_id
                            );
                        });
                        spouseResults.appendChild(item);
                    });
                });
        }, 300);
    });

    document.addEventListener('click', e => {
        if (!spouseSearch.contains(e.target)) spouseResults.innerHTML = '';
    });
}

// Bind events
const genderSel = document.getElementById('gender');
const civilSel  = document.getElementById('civil_status');
if (genderSel) {
    genderSel.addEventListener('change', () => applyGenderRules(genderSel.value));
    applyGenderRules(genderSel.value);
}
if (civilSel) {
    civilSel.addEventListener('change', () => applyCivilStatusRules(civilSel.value));
    applyCivilStatusRules(civilSel.value);
}

// On load: recalc brackets from existing children DOBs
document.addEventListener('DOMContentLoaded', function() {
    recalcAgeBrackets();
    // Update age display for all existing children and bind live input event
    document.querySelectorAll('.child-dob-input').forEach(function(input) {
        if (input.value) {
            updateAgeDisplay(input);
        }
        // 'input' fires on every keystroke/picker selection across all browsers
        input.addEventListener('input', function() { updateAgeDisplay(this); });
    });
});

// Children add/remove
let childCount = <?= count($children ?? []) ?>;

function addChild() {
    childCount++;
    const noMsg = document.getElementById('no-children-msg');
    if (noMsg) noMsg.remove();

    const entry = document.createElement('div');
    entry.className = 'child-entry border rounded p-3 mb-3 position-relative';
    entry.style.cssText = 'background:rgba(107,122,58,.03);border-color:rgba(107,122,58,.2) !important';
    entry.innerHTML = `
        <button type="button" class="btn-remove-child" onclick="removeChild(this)" title="Remove">
            <i class="bi bi-x-lg"></i>
        </button>
        <input type="hidden" name="child_id[]" value="">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                <input type="text" name="child_last_name[]" class="form-control"
                       placeholder="e.g. Dela Cruz" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" name="child_first_name[]" class="form-control"
                       placeholder="e.g. Juan" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Middle Name</label>
                <input type="text" name="child_middle_name[]" class="form-control"
                       placeholder="e.g. Santos">
            </div>
        </div>
        <div class="row g-3 mt-1">
            <div class="col-md-2">
                <label class="form-label">Suffix</label>
                <input type="text" name="child_suffix[]" class="form-control"
                       placeholder="Jr., Sr., III">
            </div>
            <div class="col-md-4">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="child_dob[]" class="form-control child-dob-input"
                       onchange="updateAgeDisplay(this)">
                <div class="mt-1" style="font-size:.82rem;color:var(--kn-muted)">
                    Age: <span class="child-age-badge" style="background:rgba(107,122,58,.1);color:var(--kn-green);font-weight:700;padding:.15em .5em;border-radius:12px;font-size:.82rem">—</span>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Sex</label>
                <select name="child_sex[]" class="form-select">
                    <option value="">— Select —</option>
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>
            </div>
        </div>`;
    document.getElementById('children-list').appendChild(entry);

    // Bind live input event on the newly added DOB field
    const newDob = entry.querySelector('.child-dob-input');
    if (newDob) {
        newDob.addEventListener('input', function() { updateAgeDisplay(this); });
    }
}

function removeChild(btn) {
    btn.closest('.child-entry').remove();
    recalcAgeBrackets();
}

function updateAgeDisplay(input) {
    // Walk up to the nearest col div (col-md-3 or col-md-4) that contains the age badge
    const parentCol = input.closest('[class*="col-"]');
    if (!parentCol) return;
    const badge = parentCol.querySelector('.child-age-badge');
    if (!badge || !input.value) { 
        if (badge) badge.textContent = '—'; 
        recalcAgeBrackets(); 
        return; 
    }
    const today = new Date();
    const dob   = new Date(input.value);
    let months  = (today.getFullYear() - dob.getFullYear()) * 12 + (today.getMonth() - dob.getMonth());
    if (months < 0) { 
        badge.textContent = '—'; 
        recalcAgeBrackets(); 
        return; 
    }
    const yrs = Math.floor(months / 12);
    badge.textContent = months < 24
        ? months + ' month' + (months !== 1 ? 's' : '')
        : yrs + ' year' + (yrs > 1 ? 's' : '') + ' old';
    recalcAgeBrackets();
}

function recalcAgeBrackets() {
    let c0_5 = 0, c6_23 = 0, c24_59 = 0, c60plus = 0;
    const today = new Date();

    document.querySelectorAll('.child-dob-input').forEach(function(input) {
        if (!input.value) return;
        const dob    = new Date(input.value);
        const months = (today.getFullYear() - dob.getFullYear()) * 12
                     + (today.getMonth() - dob.getMonth());
        if (months < 0)        return;
        if (months <= 5)       c0_5++;
        else if (months <= 23) c6_23++;
        else if (months <= 59) c24_59++;
        else                   c60plus++;
    });

    const f0_5   = document.querySelector('[name="children_0_5mos"]');
    const f6_23  = document.querySelector('[name="children_6_23mos"]');
    const f24_59 = document.querySelector('[name="children_24_59mos"]');
    const f60p   = document.querySelector('[name="children_60plus"]');

    if (f0_5)   f0_5.value   = c0_5;
    if (f6_23)  f6_23.value  = c6_23;
    if (f24_59) f24_59.value = c24_59;
    if (f60p)   f60p.value   = c60plus;
}

<?php if ($readOnly): ?>
// Show all steps in read-only mode
document.querySelectorAll('.wizard-step').forEach(s => s.classList.remove('d-none'));
document.querySelectorAll('.step-pill').forEach(p => p.classList.add('active'));
document.getElementById('step-indicator').style.display = 'none';
<?php endif; ?>
</script>

<?php if (!$readOnly): ?>
<!-- ── DLP: Encrypted localStorage auto-backup ──────────────────────────────
     Saves a snapshot of the wizard form to localStorage every 30 seconds.
     Data is AES-encrypted via CryptoJS before storage — plain text is never
     written to the browser. Key = session user ID (server-rendered, not guessable).
     Draft is cleared on successful Save Draft or Submit.
     This is purely additive — it does NOT interfere with the existing form,
     Save Draft button, or any server-side logic.
──────────────────────────────────────────────────────────────────────────── -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.2.0/crypto-js.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
(function () {
    // ── Config ────────────────────────────────────────────────────────────────
    var LS_KEY      = 'kn_wizard_draft_<?= (int)($_SESSION['user_id'] ?? 0) ?>';
    var ENC_KEY     = 'KusiNay_WizardDraft_<?= (int)($_SESSION['user_id'] ?? 0) ?>';
    var SAVE_INTERVAL_MS = 30000; // auto-save every 30 seconds
    var _restored   = false;

    // ── Helpers ───────────────────────────────────────────────────────────────

    function collectFormData() {
        var form = document.getElementById('wizard-form');
        if (!form) return null;
        var data = {};
        // Only collect simple text/select/date/number inputs — skip file inputs
        form.querySelectorAll('input:not([type=file]):not([type=submit]):not([type=button]), select, textarea').forEach(function (el) {
            if (!el.name) return;
            if (el.type === 'checkbox' || el.type === 'radio') {
                if (el.checked) data[el.name] = el.value;
            } else {
                data[el.name] = el.value;
            }
        });
        return data;
    }

    function saveEncryptedDraft() {
        try {
            var data = collectFormData();
            if (!data) return;
            var json      = JSON.stringify(data);
            var encrypted = CryptoJS.AES.encrypt(json, ENC_KEY).toString();
            localStorage.setItem(LS_KEY, encrypted);
        } catch (e) {
            // Silently fail — never break the form
        }
    }

    function clearDraft() {
        try { localStorage.removeItem(LS_KEY); } catch (e) {}
    }

    function loadEncryptedDraft() {
        try {
            var stored = localStorage.getItem(LS_KEY);
            if (!stored) return null;
            var bytes   = CryptoJS.AES.decrypt(stored, ENC_KEY);
            var json    = bytes.toString(CryptoJS.enc.Utf8);
            return json ? JSON.parse(json) : null;
        } catch (e) {
            return null;
        }
    }

    // ── Restore banner ────────────────────────────────────────────────────────

    function showRestoreBanner() {
        var banner = document.createElement('div');
        banner.id  = 'kn-draft-restore-banner';
        banner.style.cssText = [
            'position:fixed', 'top:1.5rem', 'left:50%',
            'transform:translateX(-50%)',
            'background:#3D4A1E', 'color:#F5EDD6',
            'padding:.75rem 1.5rem', 'border-radius:.6rem',
            'font-size:.88rem', 'font-weight:600',
            'z-index:99997', 'box-shadow:0 4px 16px rgba(0,0,0,.25)',
            'display:flex', 'align-items:center', 'gap:.75rem',
            'max-width:90vw'
        ].join(';');
        banner.innerHTML =
            '<i class="bi bi-shield-lock-fill"></i>' +
            '<span>Encrypted draft found. Restore?</span>' +
            '<button id="kn-restore-yes" style="background:var(--kn-orange);color:#fff;border:none;border-radius:5px;padding:.3rem .85rem;font-weight:700;cursor:pointer">Restore</button>' +
            '<button id="kn-restore-no"  style="background:transparent;color:#F5EDD6;border:1px solid rgba(245,237,214,.4);border-radius:5px;padding:.3rem .75rem;cursor:pointer">Discard</button>';
        document.body.appendChild(banner);

        document.getElementById('kn-restore-yes').addEventListener('click', function () {
            restoreDraft();
            banner.remove();
        });
        document.getElementById('kn-restore-no').addEventListener('click', function () {
            clearDraft();
            banner.remove();
        });
    }

    function restoreDraft() {
        var data = loadEncryptedDraft();
        if (!data) return;
        var form = document.getElementById('wizard-form');
        if (!form) return;
        Object.keys(data).forEach(function (name) {
            // Use querySelectorAll to handle array fields (child_first_name[], etc.)
            var els = form.querySelectorAll('[name="' + CSS.escape(name) + '"]');
            els.forEach(function (el) {
                if (el.type === 'checkbox' || el.type === 'radio') {
                    el.checked = (el.value === data[name]);
                } else {
                    el.value = data[name];
                }
            });
        });
        _restored = true;
    }

    // ── Init ──────────────────────────────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', function () {
        // Check for existing encrypted draft on page load
        var existing = loadEncryptedDraft();
        if (existing) {
            showRestoreBanner();
        }

        // Auto-save every 30 seconds
        setInterval(saveEncryptedDraft, SAVE_INTERVAL_MS);

        // Clear draft on successful form submission (Save Draft or Submit)
        var form = document.getElementById('wizard-form');
        if (form) {
            form.addEventListener('submit', function () {
                clearDraft();
            });
        }
    });
})();
</script>
<?php endif; ?>


<?php include __DIR__ . '/../templates/mother_layout_end.php'; ?>
