<?php
$pageTitle = 'Profile Detail';
$activeNav = 'validation';
include __DIR__ . '/../templates/bns_layout.php';

$flashWarning = $_SESSION['flash_warning'] ?? null;
$flashError   = $_SESSION['flash_error']   ?? null;
unset($_SESSION['flash_warning'], $_SESSION['flash_error']);

$statusColor = [
    'Draft'     => 'rgba(107,122,58,.12)',
    'Submitted' => 'rgba(196,114,42,.12)',
    'Validated' => 'rgba(107,122,58,.2)',
    'Returned'  => 'rgba(196,114,42,.15)',
];
$statusText = [
    'Draft'     => 'var(--kn-muted)',
    'Submitted' => 'var(--kn-orange)',
    'Validated' => 'var(--kn-green)',
    'Returned'  => 'var(--kn-orange)',
];
$ps  = $profile['profile_status'] ?? 'Draft';
$sc  = $statusColor[$ps]  ?? $statusColor['Draft'];
$stc = $statusText[$ps]   ?? $statusText['Draft'];
?>
<style>
:root { --kn-green:#6B7A3A; --kn-green-d:#556030; --kn-orange:#C4722A; --kn-cream:#F5EDD6; --kn-dark:#3D4A1E; --kn-muted:rgba(61,74,30,.55); }
.kn-card { background:#fff; border:1.5px solid rgba(107,122,58,.15); border-radius:.85rem; margin-bottom:1.25rem; overflow:hidden; }
.kn-card-header { background:rgba(107,122,58,.06); padding:.75rem 1.25rem; font-weight:600; color:var(--kn-dark); border-bottom:1.5px solid rgba(107,122,58,.1); }
.kn-card-body { padding:1.25rem; }
dl.row dt { color:var(--kn-muted); font-weight:500; font-size:.88rem; }
dl.row dd { font-size:.92rem; color:var(--kn-dark); }
.form-control, .form-select { border:1.5px solid rgba(107,122,58,.25); border-radius:8px; }
.form-control:focus, .form-select:focus { border-color:var(--kn-green); box-shadow:0 0 0 3px rgba(107,122,58,.12); }
.form-check-input:checked { background-color:var(--kn-green); border-color:var(--kn-green); }
</style>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="index.php?action=bnsValidationList"
       style="background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.35rem .9rem;font-size:.88rem;font-weight:600;text-decoration:none">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
    <h5 class="mb-0">
        <?= htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?>
        <span style="background:<?= $sc ?>;color:<?= $stc ?>;font-size:.78rem;font-weight:700;padding:.2em .65em;border-radius:6px;margin-left:.5rem">
            <?= htmlspecialchars($ps) ?>
        </span>
    </h5>
</div>

<?php if ($flashWarning): ?>
<div style="background:rgba(196,114,42,.08);border:1.5px solid rgba(196,114,42,.3);border-radius:.75rem;padding:.85rem 1.1rem;margin-bottom:1rem;color:var(--kn-dark)" class="d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-triangle-fill" style="color:var(--kn-orange)"></i>
    <?= htmlspecialchars($flashWarning) ?>
    <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()"></button>
</div>
<?php endif; ?>

<?php if ($flashError): ?>
<div style="background:rgba(196,114,42,.08);border:1.5px solid rgba(196,114,42,.3);border-radius:.75rem;padding:.85rem 1.1rem;margin-bottom:1rem;color:var(--kn-dark)" class="d-flex align-items-center gap-2">
    <i class="bi bi-x-circle-fill" style="color:var(--kn-orange)"></i>
    <?= htmlspecialchars($flashError) ?>
    <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()"></button>
</div>
<?php endif; ?>

<?php if ($pendingLinks > 0): ?>
<div style="background:rgba(196,114,42,.08);border:1.5px solid rgba(196,114,42,.25);border-radius:.75rem;padding:.85rem 1.1rem;margin-bottom:1rem;color:var(--kn-dark)">
    <i class="bi bi-exclamation-triangle-fill me-2" style="color:var(--kn-orange)"></i>
    <strong><?= $pendingLinks ?> pending family link(s)</strong> have not yet been confirmed. You may still validate but must confirm below.
</div>
<?php endif; ?>

<div class="row g-4">

    <!-- Left: Mother-submitted data (read-only) -->
    <div class="col-lg-7">

        <!-- Personal Data -->
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-person-fill me-2"></i>Personal Data</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Full Name</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($profile['first_name'] . ' ' . ($profile['middle_name'] ?? '') . ' ' . $profile['last_name']) ?></dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($profile['email'] ?? '—') ?></dd>

                    <dt class="col-sm-4">Birthdate</dt>
                    <dd class="col-sm-8"><?= $profile['birthdate'] ? date('F j, Y', strtotime($profile['birthdate'])) : '—' ?></dd>

                    <dt class="col-sm-4">Gender</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($profile['gender'] ?? '—') ?></dd>

                    <dt class="col-sm-4">Civil Status</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($profile['civil_status'] ?? '—') ?></dd>

                    <dt class="col-sm-4">Contact</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($profile['contact'] ?? '—') ?></dd>

                    <?php if ($healthProfile): ?>
                    <dt class="col-sm-4">Occupation</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($healthProfile['occupation'] ?? '—') ?></dd>

                    <dt class="col-sm-4">Monthly Income</dt>
                    <dd class="col-sm-8"><?= $healthProfile['monthly_income'] ? '₱' . number_format($healthProfile['monthly_income'], 2) : '—' ?></dd>

                    <?php if (!empty($healthProfile['educ_label'])): ?>
                    <dt class="col-sm-4">Education</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($healthProfile['educ_label']) ?></dd>
                    <?php endif; ?>
                    
                    <?php if (($profile['gender'] ?? '') === 'Female'): ?>
                        <?php if (!empty($healthProfile['pregnancy_status'])): ?>
                        <dt class="col-sm-4">Pregnancy Status</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($healthProfile['pregnancy_status']) ?></dd>
                        <?php endif; ?>
                        
                        <?php if (!empty($healthProfile['breastfeeding_status'])): ?>
                        <dt class="col-sm-4">Breastfeeding Status</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($healthProfile['breastfeeding_status']) ?></dd>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <!-- Spouse / Partner Info -->
        <?php
        $spouseLn = $household['spouse_last_name']  ?? null;
        $spouseFn = $household['spouse_first_name'] ?? null;
        $spouseMn = $household['spouse_middle_name'] ?? null;
        $spouseSx = $household['spouse_suffix']     ?? null;
        // Build display name from separated fields, fall back to combined spouse_name
        if ($spouseLn || $spouseFn) {
            $spouseDisplay = trim(($spouseLn ?? '') . ($spouseFn ? ', ' . $spouseFn : '') . ($spouseMn ? ' ' . $spouseMn : '') . ($spouseSx ? ' ' . $spouseSx : ''));
        } else {
            $spouseDisplay = $household['spouse_name'] ?? null;
        }
        $profileGenderForSpouse = $profile['gender'] ?? 'Female';
        $spouseSectionLabel = $profileGenderForSpouse === 'Male' ? 'Wife' : 'Husband/Spouse';
        
        // Show spouse section if user is Married OR if any spouse data exists
        $isMarried = ($profile['civil_status'] ?? '') === 'Married';
        $hasSpouseData = $spouseDisplay 
            || !empty($household['spouse_occupation']) 
            || !empty($household['spouse_monthly_income'])
            || !empty($household['spouse_educ_level_id'])
            || !empty($household['spouse_pregnancy_status'])
            || !empty($household['spouse_breastfeeding_status']);
        
        if ($isMarried || $hasSpouseData):
        ?>
        <div class="kn-card">
            <div class="kn-card-header"><i class="bi bi-person-heart me-2"></i><?= $spouseSectionLabel ?> Information</div>
            <div class="kn-card-body">
                <dl class="row mb-0">
                    <?php if ($spouseDisplay): ?>
                    <dt class="col-sm-5">Full Name</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($spouseDisplay) ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($household['spouse_occupation'])): ?>
                    <dt class="col-sm-5">Occupation</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($household['spouse_occupation']) ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($household['spouse_monthly_income'])): ?>
                    <dt class="col-sm-5">Monthly Income</dt>
                    <dd class="col-sm-7">₱<?= number_format((float)$household['spouse_monthly_income'], 2) ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($household['spouse_educ_level_id'])): ?>
                    <dt class="col-sm-5">Education</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($household['spouse_educ_label'] ?? 'Level ' . $household['spouse_educ_level_id']) ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($household['spouse_pregnancy_status'])): ?>
                    <dt class="col-sm-5">Pregnancy Status</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($household['spouse_pregnancy_status']) ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($household['spouse_breastfeeding_status'])): ?>
                    <dt class="col-sm-5">Breastfeeding Status</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($household['spouse_breastfeeding_status']) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
        <?php endif; ?>

        <!-- Household Details -->
        <?php if ($household): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-house-fill me-2"></i>Household Details</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Household Code</dt>
                    <dd class="col-sm-7"><code><?= htmlspecialchars($household['household_code']) ?></code></dd>

                    <dt class="col-sm-5">Purok</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($household['purok'] ?? '—') ?></dd>

                    <dt class="col-sm-5">Water Source</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($household['water_source_id'] ?? '—') ?></dd>

                    <dt class="col-sm-5">Toilet Type</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($household['toilet_type_id'] ?? '—') ?></dd>

                    <dt class="col-sm-5">Dwelling Type</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($household['dwelling_type_id'] ?? '—') ?></dd>

                    <dt class="col-sm-5">Uses Iodized Salt</dt>
                    <dd class="col-sm-7"><?= ($household['uses_iodized_salt'] ?? 0) ? 'Yes' : 'No' ?></dd>

                    <?php if (isset($household['hof_needs_review'])): ?>
                    <dt class="col-sm-5">HOF Needs Review</dt>
                    <dd class="col-sm-7">
                        <?php if ($household['hof_needs_review']): ?>
                            <span class="badge bg-warning text-dark">Yes</span>
                        <?php else: ?>
                            <span class="badge bg-success">No</span>
                        <?php endif; ?>
                    </dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
        <?php endif; ?>

        <!-- Family Links -->
        <?php if ($familyLinks): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-diagram-3-fill me-2"></i>Family Links</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User A</th>
                            <th>User B</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($familyLinks as $fl): ?>
                        <tr>
                            <td><?= htmlspecialchars($fl['user_a_first'] . ' ' . $fl['user_a_last']) ?></td>
                            <td><?= htmlspecialchars($fl['user_b_first'] . ' ' . $fl['user_b_last']) ?></td>
                            <td><?= htmlspecialchars($fl['relationship_type']) ?></td>
                            <td>
                                <?php
                                $lbadge = ['Pending'=>'warning','Verified'=>'success','Rejected'=>'danger'];
                                $lc = $lbadge[$fl['verification_status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $lc ?> text-<?= $lc === 'warning' ? 'dark' : 'white' ?>">
                                    <?= htmlspecialchars($fl['verification_status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- Right: BNS Actions -->
    <div class="col-lg-5">

        <!-- HOF Override -->
        <?php
            // Build HOF candidates: always include the mother (registered user)
            // Plus the typed spouse_name if present (may not have an account)
            $spouseNameForHof = $household['spouse_name'] ?? null;
        ?>
        <?php if ($household): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-person-badge-fill me-2"></i>Override Head of Family</div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    The <strong>Head of Family</strong> is the member with the higher income. If incomes are equal or not provided, the family's own designation is used. Override here if needed.
                </p>
                <?php
                    // Define gender-based labels here so they're available throughout this block
                    $profileGender = $profile['gender'] ?? 'Female';
                    $userLabel     = $profileGender === 'Male' ? 'Husband' : 'Mother/Wife';
                    $spouseLabel   = $profileGender === 'Male' ? 'Wife'    : 'Husband/Father';

                    // Show income comparison hint
                    $motherIncome = (float)($healthProfile['monthly_income'] ?? 0);
                    $spouseIncome = (float)($household['spouse_monthly_income'] ?? 0);
                    if ($motherIncome > 0 || $spouseIncome > 0):
                        $autoHof = $spouseIncome > $motherIncome
                            ? ($spouseNameForHof ?? 'Spouse')
                            : ($profile['first_name'] . ' ' . $profile['last_name']);
                ?>
                <div class="alert alert-info py-2 px-3 small mb-3">
                    <i class="bi bi-calculator me-1"></i>
                    Based on income: <strong><?= htmlspecialchars($autoHof) ?></strong> earns more
                    (<?= $profileGender === 'Male' ? 'Husband' : 'Mother' ?>: ₱<?= number_format($motherIncome, 2) ?>,
                    <?= $profileGender === 'Male' ? 'Wife' : 'Husband' ?>: ₱<?= number_format($spouseIncome, 2) ?>).
                    System will auto-assign Head unless you override.
                </div>
                <?php endif; ?>
                <form method="POST" action="index.php?action=doOverrideHof">
                    <input type="hidden" name="profile_id"   value="<?= (int)$profile['profile_id'] ?>">
                    <input type="hidden" name="household_id" value="<?= (int)($household['household_id'] ?? 0) ?>">
                    <div class="mb-3">
                        <label class="form-label">Select Head of Family</label>
                        <select name="hof_user_id" class="form-select">
                            <?php
                            $currentHof    = $household['hof_user_id'] ?? '';
                            // Always auto-select by income (system recommendation)
                            $spouseEarnsMore = $spouseIncome > $motherIncome;
                            $autoSelectSpouse = $spouseEarnsMore;
                            $autoSelectSelf   = !$spouseEarnsMore;
                            ?>
                            <option value="<?= (int)$profile['user_id'] ?>"
                                <?= $autoSelectSelf ? 'selected' : '' ?>>
                                <?= htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?> (<?= $userLabel ?>)
                            </option>
                            <?php if ($spouseNameForHof): ?>
                            <option value="0"
                                <?= $autoSelectSpouse ? 'selected' : '' ?>>
                                <?= htmlspecialchars($spouseNameForHof) ?> (<?= $spouseLabel ?>)
                            </option>
                            <?php endif; ?>
                            <?php
                            // Any other registered household members (excluding the mother)
                            foreach ($householdMembers as $m):
                                if ($m['user_id'] == $profile['user_id']) continue;
                            ?>
                            <option value="<?= (int)$m['user_id'] ?>"
                                <?= ($currentHof == $m['user_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['full_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-check2 me-1"></i> Update HOF
                    </button>
                </form>
            </div>
        </div>
        <?php endif; // End HOF Override ?>

        <!-- Validate / Return -->
        <?php if ($profile['profile_status'] === 'Submitted'): ?>
        <div class="card shadow-sm border-success mb-3">
            <div class="card-header fw-semibold text-success"><i class="bi bi-patch-check-fill me-2"></i>Validate Profile</div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    After conducting the interview and verifying the data, click Validate to finalize this profile.
                </p>
                <form method="POST" action="index.php?action=doValidateProfile">
                    <input type="hidden" name="profile_id" value="<?= (int)$profile['profile_id'] ?>">
                    <?php if ($pendingLinks > 0): ?>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="confirm_pending"
                               id="confirm_pending" value="1" required>
                        <label class="form-check-label text-warning fw-semibold" for="confirm_pending">
                            I acknowledge there are pending family links and wish to proceed.
                        </label>
                    </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-patch-check-fill me-2"></i> Validate Profile
                    </button>
                </form>
            </div>
        </div>

        <!-- Return for Correction -->
        <div class="card shadow-sm" style="border:1.5px solid rgba(196,114,42,.35)">
            <div class="card-header fw-semibold" style="color:var(--kn-orange);background:rgba(196,114,42,.06)">
                <i class="bi bi-arrow-counterclockwise me-2"></i>Return for Correction
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Found incorrect or incomplete information? Return the profile to the mother with a note so she can fix and resubmit.
                </p>
                <form method="POST" action="index.php?action=doReturnProfile">
                    <input type="hidden" name="profile_id" value="<?= (int)$profile['profile_id'] ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.88rem">Reason / What to fix <span style="color:var(--kn-orange)">*</span></label>
                        <textarea name="return_reason" class="form-control" rows="3"
                                  placeholder="e.g. Incorrect birthdate, missing occupation, household details need verification…"
                                  required style="font-size:.9rem"></textarea>
                    </div>
                    <button type="submit" class="btn w-100 fw-semibold"
                            style="background:rgba(196,114,42,.1);color:var(--kn-orange);border:1.5px solid rgba(196,114,42,.35)"
                            onclick="return confirm('Return this profile for correction?')">
                        <i class="bi bi-arrow-counterclockwise me-2"></i> Return
                    </button>
                </form>
            </div>
        </div>

        <?php elseif ($profile['profile_status'] === 'Validated'): ?>
        <div class="alert alert-success">
            <i class="bi bi-patch-check-fill me-2"></i>
            Profile validated on <?= date('M j, Y g:i A', strtotime($profile['validated_at'])) ?>.
        </div>

        <?php elseif ($profile['profile_status'] === 'Returned'): ?>
        <div class="card shadow-sm" style="border:1.5px solid rgba(196,114,42,.35)">
            <div class="card-header fw-semibold" style="color:var(--kn-orange);background:rgba(196,114,42,.06)">
                <i class="bi bi-arrow-counterclockwise me-2"></i>Returned for Correction
            </div>
            <div class="card-body">
                <p class="text-muted small mb-1">Reason given:</p>
                <p class="mb-0" style="font-size:.92rem"><?= nl2br(htmlspecialchars($profile['return_reason'] ?? '—')) ?></p>
                <hr class="my-3">
                <p class="text-muted small mb-0">Waiting for the mother to correct and resubmit.</p>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php include __DIR__ . '/../templates/bns_layout_end.php'; ?>
