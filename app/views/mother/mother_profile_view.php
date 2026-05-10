<?php
$pageTitle = 'My Profile';
$activeNav = 'family_profile';
include __DIR__ . '/../templates/mother_layout.php';
?>
<style>
:root { --kn-green:#6B7A3A; --kn-green-d:#556030; --kn-orange:#C4722A; --kn-cream:#F5EDD6; --kn-dark:#3D4A1E; --kn-muted:rgba(61,74,30,.55); }
.kn-card { background:#fff; border:1.5px solid rgba(107,122,58,.15); border-radius:1rem; margin-bottom:1.25rem; overflow:hidden; }
.kn-card-header { background:rgba(107,122,58,.06); padding:.75rem 1.25rem; font-weight:700; font-size:.88rem; text-transform:uppercase; letter-spacing:.05em; color:var(--kn-green); border-bottom:1.5px solid rgba(107,122,58,.1); display:flex; align-items:center; gap:.5rem; }
.kn-card-body { padding:1.35rem; }
.info-row { display:flex; justify-content:space-between; padding:.45rem 0; border-bottom:1px solid rgba(107,122,58,.08); font-size:.95rem; }
.info-row:last-child { border-bottom:none; }
.info-label { color:var(--kn-muted); font-weight:500; }
.info-value { font-weight:600; color:var(--kn-dark); text-align:right; }
.status-badge-validated { background:rgba(107,122,58,.12); color:var(--kn-green); font-size:.8rem; font-weight:700; padding:.25em .7em; border-radius:20px; }
.status-badge-submitted { background:rgba(196,114,42,.12); color:var(--kn-orange); font-size:.8rem; font-weight:700; padding:.25em .7em; border-radius:20px; }
.status-badge-returned  { background:rgba(196,114,42,.15); color:var(--kn-orange); font-size:.8rem; font-weight:700; padding:.25em .7em; border-radius:20px; }
</style>

<!-- Header -->
<div class="d-flex align-items-center gap-3 mb-4">
    <div style="width:52px;height:52px;background:var(--kn-green);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0">👤</div>
    <div>
        <h4 class="fw-bold mb-0"><?= htmlspecialchars($user['first_name'] . ' ' . ($user['middle_name'] ? $user['middle_name'] . ' ' : '') . $user['last_name']) ?></h4>
        <div class="d-flex align-items-center gap-2 mt-1">
            <?php if (($user['profile_status'] ?? '') === 'Validated'): ?>
                <span class="status-badge-validated"><i class="bi bi-patch-check-fill me-1"></i>Profile Validated</span>
                <span style="font-size:.82rem;color:var(--kn-muted)">on <?= date('M j, Y', strtotime($user['validated_at'])) ?></span>
            <?php elseif (($user['profile_status'] ?? '') === 'Returned'): ?>
                <span class="status-badge-returned"><i class="bi bi-arrow-counterclockwise me-1"></i>Returned for Correction</span>
                <a href="index.php?action=motherWizard" style="font-size:.82rem;color:var(--kn-orange);font-weight:600">Fix &amp; Resubmit →</a>
            <?php else: ?>
                <span class="status-badge-submitted"><i class="bi bi-clock me-1"></i>Awaiting Validation</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">

        <!-- Personal Info -->
        <div class="kn-card">
            <div class="kn-card-header"><i class="bi bi-person-fill"></i> Personal Information</div>
            <div class="kn-card-body">
                <div class="info-row"><span class="info-label">Full Name</span><span class="info-value"><?= htmlspecialchars($user['first_name'] . ' ' . ($user['middle_name'] ?? '') . ' ' . $user['last_name']) ?></span></div>
                <div class="info-row"><span class="info-label">Email</span><span class="info-value"><?= htmlspecialchars($user['email'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Birthdate</span><span class="info-value"><?= $user['birthdate'] ? date('F j, Y', strtotime($user['birthdate'])) : '—' ?></span></div>
                <div class="info-row"><span class="info-label">Gender</span><span class="info-value"><?= htmlspecialchars($user['gender'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Civil Status</span><span class="info-value"><?= htmlspecialchars($user['civil_status'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Contact</span><span class="info-value"><?= htmlspecialchars($user['contact'] ?? '—') ?></span></div>
            </div>
        </div>

        <!-- Health Profile -->
        <?php if ($healthProfile): ?>
        <div class="kn-card">
            <div class="kn-card-header"><i class="bi bi-heart-pulse-fill"></i> Profile</div>
            <div class="kn-card-body">
                <div class="info-row"><span class="info-label">Occupation</span><span class="info-value"><?= htmlspecialchars($healthProfile['occupation'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Educational Attainment</span><span class="info-value"><?= htmlspecialchars($healthProfile['educ_label'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Monthly Income</span><span class="info-value"><?= $healthProfile['monthly_income'] ? '₱' . number_format($healthProfile['monthly_income'], 2) : '—' ?></span></div>
                <?php if (!empty($healthProfile['pregnancy_status'])): ?>
                <div class="info-row"><span class="info-label">Pregnancy Status</span><span class="info-value"><?= htmlspecialchars($healthProfile['pregnancy_status']) ?></span></div>
                <?php endif; ?>
                <?php if (!empty($healthProfile['breastfeeding_status'])): ?>
                <div class="info-row"><span class="info-label">Breastfeeding Status</span><span class="info-value"><?= htmlspecialchars($healthProfile['breastfeeding_status']) ?></span></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Spouse Information -->
        <?php 
        // Build spouse name from separate fields or use combined field
        $spouseName = null;
        if ($household) {
            if (!empty($household['spouse_name'])) {
                $spouseName = $household['spouse_name'];
            } elseif (!empty($household['spouse_first_name']) || !empty($household['spouse_last_name'])) {
                $spouseName = trim(
                    ($household['spouse_first_name'] ?? '') . ' ' . 
                    ($household['spouse_middle_name'] ?? '') . ' ' . 
                    ($household['spouse_last_name'] ?? '')
                );
            }
        }
        
        if ($household && ($user['civil_status'] ?? '') === 'Married' && $spouseName): 
        ?>
        <div class="kn-card">
            <div class="kn-card-header">
                <i class="bi bi-person-hearts"></i> 
                <?= ($user['gender'] ?? '') === 'Male' ? 'Wife Information' : 'Husband Information' ?>
            </div>
            <div class="kn-card-body">
                <div class="info-row"><span class="info-label">Full Name</span><span class="info-value"><?= htmlspecialchars($spouseName) ?></span></div>
                <?php if (!empty($household['spouse_occupation'])): ?>
                <div class="info-row"><span class="info-label">Occupation</span><span class="info-value"><?= htmlspecialchars($household['spouse_occupation']) ?></span></div>
                <?php endif; ?>
                <?php if (!empty($household['spouse_monthly_income'])): ?>
                <div class="info-row"><span class="info-label">Monthly Income</span><span class="info-value">₱<?= number_format($household['spouse_monthly_income'], 2) ?></span></div>
                <?php endif; ?>
                <?php if (!empty($household['spouse_educ_label'])): ?>
                <div class="info-row"><span class="info-label">Education</span><span class="info-value"><?= htmlspecialchars($household['spouse_educ_label']) ?></span></div>
                <?php endif; ?>
                <?php if (!empty($household['spouse_pregnancy_status'])): ?>
                <div class="info-row"><span class="info-label">Pregnancy Status</span><span class="info-value"><?= htmlspecialchars($household['spouse_pregnancy_status']) ?></span></div>
                <?php endif; ?>
                <?php if (!empty($household['spouse_breastfeeding_status'])): ?>
                <div class="info-row"><span class="info-label">Breastfeeding Status</span><span class="info-value"><?= htmlspecialchars($household['spouse_breastfeeding_status']) ?></span></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
    <div class="col-lg-6">

        <!-- Household -->
        <?php if ($household): ?>
        <div class="kn-card">
            <div class="kn-card-header"><i class="bi bi-house-fill"></i> Household Details</div>
            <div class="kn-card-body">
                <div class="info-row"><span class="info-label">Household Code</span><span class="info-value" style="font-family:monospace;font-size:.9rem"><?= htmlspecialchars($household['household_code'] ?? $household['hh_number'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Purok</span><span class="info-value"><?= htmlspecialchars($household['purok'] ?? '—') ?></span></div>
                <?php if (!empty($household['num_hh_members'])): ?>
                <div class="info-row"><span class="info-label"># of Household Members</span><span class="info-value"><?= (int)$household['num_hh_members'] ?></span></div>
                <?php endif; ?>
                <div class="info-row"><span class="info-label">Water Source</span><span class="info-value"><?= htmlspecialchars($household['water_label'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Toilet Type</span><span class="info-value"><?= htmlspecialchars($household['toilet_label'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Dwelling Type</span><span class="info-value"><?= htmlspecialchars($household['dwelling_label'] ?? '—') ?></span></div>
                <div class="info-row"><span class="info-label">Uses Iodized Salt</span><span class="info-value"><?= $household['uses_iodized_salt'] ? 'Yes' : 'No' ?></span></div>
                <div class="info-row"><span class="info-label">Uses IFR (Iron Fortified Rice)</span><span class="info-value"><?= $household['uses_ifr'] ? 'Yes' : 'No' ?></span></div>
                <?php if (!empty($household['fp_method_id'])): ?>
                <div class="info-row"><span class="info-label">Family Planning Method</span><span class="info-value"><?= htmlspecialchars($household['fp_label'] ?? '—') ?></span></div>
                <?php endif; ?>
                <?php if (!empty($household['children_0_5mos']) || !empty($household['children_6_23mos']) || !empty($household['children_24_59mos']) || !empty($household['children_60plus'])): ?>
                <div class="info-row"><span class="info-label">Children 0–5 mos</span><span class="info-value"><?= (int)($household['children_0_5mos'] ?? 0) ?></span></div>
                <div class="info-row"><span class="info-label">Children 6–23 mos</span><span class="info-value"><?= (int)($household['children_6_23mos'] ?? 0) ?></span></div>
                <div class="info-row"><span class="info-label">Children 24–59 mos</span><span class="info-value"><?= (int)($household['children_24_59mos'] ?? 0) ?></span></div>
                <div class="info-row"><span class="info-label">Children &gt;60 mos</span><span class="info-value"><?= (int)($household['children_60plus'] ?? 0) ?></span></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Family Links -->
        <?php if (!empty($familyLinks)): ?>
        <div class="kn-card">
            <div class="kn-card-header"><i class="bi bi-diagram-3-fill"></i> Family Links</div>
            <div class="kn-card-body" style="padding:0">
                <table class="table mb-0" style="font-size:.9rem">
                    <thead style="background:rgba(107,122,58,.06)">
                        <tr>
                            <th style="border:none;padding:.6rem 1rem;color:var(--kn-muted);font-weight:600;font-size:.78rem;text-transform:uppercase">Person</th>
                            <th style="border:none;padding:.6rem 1rem;color:var(--kn-muted);font-weight:600;font-size:.78rem;text-transform:uppercase">Relationship</th>
                            <th style="border:none;padding:.6rem 1rem;color:var(--kn-muted);font-weight:600;font-size:.78rem;text-transform:uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($familyLinks as $fl): ?>
                    <?php
                        $otherName = ($fl['user_id_a'] == $user['user_id'])
                            ? $fl['user_b_first'] . ' ' . $fl['user_b_last']
                            : $fl['user_a_first'] . ' ' . $fl['user_a_last'];
                        $sc = ['Pending'=>'rgba(196,114,42,.12)','Verified'=>'rgba(107,122,58,.12)','Rejected'=>'rgba(200,50,50,.1)'];
                        $tc = ['Pending'=>'var(--kn-orange)','Verified'=>'var(--kn-green)','Rejected'=>'#c03030'];
                        $vs = $fl['verification_status'];
                    ?>
                    <tr>
                        <td style="padding:.6rem 1rem;border-color:rgba(107,122,58,.08)"><?= htmlspecialchars($otherName) ?></td>
                        <td style="padding:.6rem 1rem;border-color:rgba(107,122,58,.08);font-size:.85rem;color:var(--kn-muted)"><?= htmlspecialchars($fl['relationship_type']) ?></td>
                        <td style="padding:.6rem 1rem;border-color:rgba(107,122,58,.08)">
                            <span style="background:<?= $sc[$vs] ?? '#eee' ?>;color:<?= $tc[$vs] ?? '#666' ?>;font-size:.75rem;font-weight:700;padding:.2em .55em;border-radius:6px">
                                <?= htmlspecialchars($vs) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Children -->
        <?php if (!empty($children)): ?>
        <div class="kn-card">
            <div class="kn-card-header"><i class="bi bi-emoji-smile-fill"></i> Children</div>
            <div class="kn-card-body" style="padding:0">
                <table class="table mb-0" style="font-size:.9rem">
                    <thead style="background:rgba(107,122,58,.06)">
                        <tr>
                            <th style="border:none;padding:.6rem 1rem;color:var(--kn-muted);font-weight:600;font-size:.78rem;text-transform:uppercase">Name</th>
                            <th style="border:none;padding:.6rem 1rem;color:var(--kn-muted);font-weight:600;font-size:.78rem;text-transform:uppercase">Sex</th>
                            <th style="border:none;padding:.6rem 1rem;color:var(--kn-muted);font-weight:600;font-size:.78rem;text-transform:uppercase">Age</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($children as $child): ?>
                    <?php
                        // Build full name from separated fields
                        $childName = trim(($child['first_name'] ?? '') . ' ' . ($child['middle_name'] ?? '') . ' ' . ($child['last_name'] ?? ''));
                        if (!empty($child['suffix'])) {
                            $childName .= ' ' . $child['suffix'];
                        }
                        $childName = $childName ?: '—';
                        
                        $age = '—';
                        if (!empty($child['dob'])) {
                            $diff   = (new DateTime())->diff(new DateTime($child['dob']));
                            $months = $diff->y * 12 + $diff->m;
                            $yrs    = $diff->y;
                            $age    = $months < 24
                                ? $months . ' month' . ($months !== 1 ? 's' : '')
                                : $yrs . ' year' . ($yrs > 1 ? 's' : '') . ' old';
                        }
                    ?>
                    <tr>
                        <td style="padding:.6rem 1rem;border-color:rgba(107,122,58,.08)"><?= htmlspecialchars($childName) ?></td>
                        <td style="padding:.6rem 1rem;border-color:rgba(107,122,58,.08);color:var(--kn-muted)">
                            <?= $child['sex'] === 'M' ? '♂ Male' : ($child['sex'] === 'F' ? '♀ Female' : '—') ?>
                        </td>
                        <td style="padding:.6rem 1rem;border-color:rgba(107,122,58,.08)">
                            <span style="background:rgba(107,122,58,.1);color:var(--kn-green);font-size:.8rem;font-weight:700;padding:.2em .55em;border-radius:6px">
                                <?= $age ?>
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
</div>

<?php include __DIR__ . '/../templates/bns_layout_end.php'; ?>
