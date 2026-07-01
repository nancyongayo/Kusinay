<?php
$pageTitle = 'Resident Assessment';
$activeNav = 'data_encoding';
require_once __DIR__ . '/../templates/bns_layout.php';

$activeTab = $_GET['tab'] ?? 'children';
?>
<style>
:root{--kn-green:#6B7A3A;--kn-orange:#C4722A;--kn-dark:#3D4A1E;--kn-muted:rgba(61,74,30,.55);}

/* Modern Tab Buttons - Smaller variant */
.modern-tab-btn-sm {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(107,122,58,.15);
    border-radius: 10px;
    padding: .5rem 1rem;
    font-size: .82rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,.06);
    position: relative;
    overflow: hidden;
}
.modern-tab-btn-sm::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0;
    transition: opacity .2s;
    border-radius: 10px;
}
.modern-tab-btn-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,.1);
    border-color: rgba(107,122,58,.25);
}
.modern-tab-btn-sm:hover::before {
    opacity: 1;
}
.modern-tab-btn-sm:active {
    transform: translateY(0);
}

/* Color variants */
.modern-tab-btn-sm[data-color="dark"] { color: var(--kn-dark); }
.modern-tab-btn-sm[data-color="dark"]::before { background: linear-gradient(135deg, rgba(61,74,30,.08) 0%, rgba(61,74,30,.04) 100%); }
.modern-tab-btn-sm[data-color="orange"] { color: var(--kn-orange); }
.modern-tab-btn-sm[data-color="orange"]::before { background: linear-gradient(135deg, rgba(196,114,42,.1) 0%, rgba(196,114,42,.05) 100%); }
.modern-tab-btn-sm[data-color="green"] { color: var(--kn-green); }
.modern-tab-btn-sm[data-color="green"]::before { background: linear-gradient(135deg, rgba(107,122,58,.1) 0%, rgba(107,122,58,.05) 100%); }
.modern-tab-btn-sm[data-color="red"] { color: #e74c3c; }
.modern-tab-btn-sm[data-color="red"]::before { background: linear-gradient(135deg, rgba(231,76,60,.1) 0%, rgba(231,76,60,.05) 100%); }
.modern-tab-btn-sm[data-color="purple"] { color: #8e44ad; }
.modern-tab-btn-sm[data-color="purple"]::before { background: linear-gradient(135deg, rgba(142,68,173,.1) 0%, rgba(142,68,173,.05) 100%); }
.modern-tab-btn-sm[data-color="blue"] { color: #2980b9; }
.modern-tab-btn-sm[data-color="blue"]::before { background: linear-gradient(135deg, rgba(41,128,185,.1) 0%, rgba(41,128,185,.05) 100%); }

.modern-tab-btn-sm i {
    font-size: .95rem;
    position: relative;
    z-index: 1;
}

/* Modern Tab Buttons */
.modern-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(107,122,58,.15);
    border-radius: 12px;
    padding: .65rem 1.3rem;
    font-size: .88rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(0,0,0,.06), 0 1px 3px rgba(0,0,0,.04);
    position: relative;
    overflow: hidden;
}
.modern-tab-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0;
    transition: opacity .3s;
    border-radius: 12px;
}
.modern-tab-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,.1), 0 4px 8px rgba(0,0,0,.06);
    border-color: rgba(107,122,58,.25);
}
.modern-tab-btn:hover::before {
    opacity: 1;
}
.modern-tab-btn:active {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
}

/* Color variants with glassmorphism */
.modern-tab-btn[data-color="dark"] { color: var(--kn-dark); }
.modern-tab-btn[data-color="dark"]::before { background: linear-gradient(135deg, rgba(61,74,30,.08) 0%, rgba(61,74,30,.04) 100%); }
.modern-tab-btn[data-color="orange"] { color: var(--kn-orange); }
.modern-tab-btn[data-color="orange"]::before { background: linear-gradient(135deg, rgba(196,114,42,.1) 0%, rgba(196,114,42,.05) 100%); }
.modern-tab-btn[data-color="green"] { color: var(--kn-green); }
.modern-tab-btn[data-color="green"]::before { background: linear-gradient(135deg, rgba(107,122,58,.1) 0%, rgba(107,122,58,.05) 100%); }
.modern-tab-btn[data-color="red"] { color: #e74c3c; }
.modern-tab-btn[data-color="red"]::before { background: linear-gradient(135deg, rgba(231,76,60,.1) 0%, rgba(231,76,60,.05) 100%); }
.modern-tab-btn[data-color="purple"] { color: #8e44ad; }
.modern-tab-btn[data-color="purple"]::before { background: linear-gradient(135deg, rgba(142,68,173,.1) 0%, rgba(142,68,173,.05) 100%); }
.modern-tab-btn[data-color="blue"] { color: #2980b9; }
.modern-tab-btn[data-color="blue"]::before { background: linear-gradient(135deg, rgba(41,128,185,.1) 0%, rgba(41,128,185,.05) 100%); }

.modern-tab-btn i {
    font-size: 1.1rem;
    position: relative;
    z-index: 1;
    filter: drop-shadow(0 1px 2px rgba(0,0,0,.1));
}

/* Tab Buttons */
.tab-btn{
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(107,122,58,.15);
    border-radius:12px;
    padding:.65rem 1.3rem;
    font-size:.9rem;
    font-weight:600;
    color:var(--kn-muted);
    cursor:pointer;
    transition:all .3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(0,0,0,.06), 0 1px 3px rgba(0,0,0,.04);
    position: relative;
}
.tab-btn:hover{
    transform:translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,.1), 0 3px 6px rgba(0,0,0,.06);
    color:var(--kn-green);
    border-color: rgba(107,122,58,.25);
}
.tab-btn.active{
    background: linear-gradient(135deg, var(--kn-green) 0%, var(--kn-green-d) 100%);
    color:#fff;
    border-color: var(--kn-green);
    box-shadow: 0 6px 20px rgba(107,122,58,.3), 0 3px 8px rgba(107,122,58,.2);
}
.tab-btn.active:hover{
    transform:translateY(-2px);
    box-shadow: 0 8px 24px rgba(107,122,58,.35), 0 4px 10px rgba(107,122,58,.25);
}

.tab-pane{display:none;}.tab-pane.active{display:block;}
.assess-btn{background:var(--kn-green);color:#fff;border:none;border-radius:7px;padding:.3rem .85rem;font-size:.82rem;font-weight:600;text-decoration:none;transition:.2s;}
.assess-btn:hover{background:#556030;color:#fff;}
.badge-risk{background:rgba(196,114,42,.12);color:var(--kn-orange);border:1px solid rgba(196,114,42,.3);border-radius:5px;font-size:.75rem;padding:.15em .5em;font-weight:700;}
.badge-ok{background:rgba(107,122,58,.1);color:var(--kn-green);border:1px solid rgba(107,122,58,.2);border-radius:5px;font-size:.75rem;padding:.15em .5em;font-weight:700;}

/* Modern Table Styles */
.kn-table-wrapper {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.06), 0 1px 3px rgba(0,0,0,.04);
}
.kn-table {
    margin-bottom: 0;
}
.kn-table thead {
    background: linear-gradient(135deg, rgba(107,122,58,.08) 0%, rgba(107,122,58,.04) 100%);
}
.kn-table th {
    background: transparent;
    color: var(--kn-dark);
    font-size: .82rem;
    font-weight: 700;
    border-bottom: 2px solid rgba(107,122,58,.15);
    padding: 1rem .75rem;
    text-transform: uppercase;
    letter-spacing: .03em;
    font-size: .75rem;
}
.kn-table tbody tr {
    border-bottom: 1px solid rgba(107,122,58,.08);
    transition: all .2s ease;
}
.kn-table tbody tr:hover {
    background: rgba(107,122,58,.03);
}
.kn-table td {
    font-size: .88rem;
    vertical-align: middle;
    padding: .85rem .75rem;
    border-bottom: none;
}

/* Modern Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .65rem;
    border-radius: 20px;
    font-size: .75rem;
    font-weight: 600;
    white-space: nowrap;
}
.status-badge.not-assessed {
    background: rgba(220,53,69,.1);
    color: #dc3545;
    border: 1px solid rgba(220,53,69,.2);
}
.status-badge.assessed {
    background: rgba(107,122,58,.1);
    color: var(--kn-green);
    border: 1px solid rgba(107,122,58,.2);
}
.status-badge i {
    font-size: .7rem;
}

/* Modern Action Buttons */
.action-btn-group {
    display: flex;
    gap: .4rem;
    flex-wrap: wrap;
}
.action-btn {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .35rem .75rem;
    border-radius: 8px;
    font-size: .8rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    transition: all .2s ease;
    white-space: nowrap;
}
.action-btn.btn-assess {
    background: linear-gradient(135deg, var(--kn-green) 0%, var(--kn-green-d) 100%);
    color: #fff;
    box-shadow: 0 2px 4px rgba(107,122,58,.2);
}
.action-btn.btn-assess:hover {
    background: linear-gradient(135deg, var(--kn-green-d) 0%, var(--kn-green) 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(107,122,58,.3);
    color: #fff;
}
.action-btn.btn-followup {
    background: linear-gradient(135deg, var(--kn-orange) 0%, #a85e22 100%);
    color: #fff;
    box-shadow: 0 2px 4px rgba(196,114,42,.2);
}
.action-btn.btn-followup:hover {
    background: linear-gradient(135deg, #a85e22 0%, var(--kn-orange) 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(196,114,42,.3);
    color: #fff;
}
.action-btn i {
    font-size: .75rem;
}
</style>

<div class="d-flex align-items-center justify-content-between mb-4 gap-3" style="flex-wrap: wrap;">
    <div style="min-width: 250px;">
        <h4 class="fw-bold mb-1" style="font-size:1.35rem">Resident Assessment</h4>
        <p class="text-muted mb-0" style="font-size:.85rem">OPT Plus — List of Recipients for Nutrition Assessment</p>
    </div>
    <div class="d-flex gap-2 flex-wrap" style="flex-shrink: 0;">
        <a href="index.php?action=optResults" class="modern-tab-btn-sm" data-color="dark">
            <i class="bi bi-table"></i> OPT Plus Results
        </a>
        <a href="index.php?action=formCReport" class="modern-tab-btn-sm" data-color="orange">
            <i class="bi bi-file-earmark-bar-graph-fill"></i> Form C — At-risk Children
        </a>
        <a href="index.php?action=p12Monitoring" class="modern-tab-btn-sm" data-color="green">
            <i class="bi bi-clipboard2-pulse-fill"></i> Monitoring List
        </a>
        <a href="index.php?action=pregnantMasterlist" class="modern-tab-btn-sm" data-color="red">
            <i class="bi bi-heart-pulse-fill"></i> Pregnant
        </a>
        <a href="index.php?action=lactatingMasterlist" class="modern-tab-btn-sm" data-color="purple">
            <i class="bi bi-person-heart"></i> Lactating
        </a>
        <a href="index.php?action=seniorMasterlist" class="modern-tab-btn-sm" data-color="blue">
            <i class="bi bi-person-cane"></i> Elderly
        </a>
    </div>
</div>

<!-- Tabs -->
<div class="d-flex gap-2 mb-3 flex-wrap">
    <button class="tab-btn <?= $activeTab==='children' ? 'active':'' ?>" onclick="switchTab('children')">
        <i class="bi bi-emoji-smile me-1"></i> Children (0–59 mos)
        <span class="ms-1" style="font-size:.78rem;opacity:.8"><?= count($children) ?></span>
    </button>
    <button class="tab-btn <?= $activeTab==='maternal' ? 'active':'' ?>" onclick="switchTab('maternal')">
        <i class="bi bi-heart-pulse me-1"></i> Maternal
        <span class="ms-1" style="font-size:.78rem;opacity:.8"><?= count($maternal) ?></span>
    </button>
    <button class="tab-btn <?= $activeTab==='seniors' ? 'active':'' ?>" onclick="switchTab('seniors')">
        <i class="bi bi-person-cane me-1"></i> Elderly (60+)
        <span class="ms-1" style="font-size:.78rem;opacity:.8"><?= count($seniors) ?></span>
    </button>
    <button class="tab-btn <?= $activeTab==='recent' ? 'active':'' ?>" onclick="switchTab('recent')">
        <i class="bi bi-clock-history me-1"></i> Recent Assessments
    </button>
</div>

<!-- ── Tab: Children ─────────────────────────────────────────────────────── -->
<div class="tab-pane <?= $activeTab==='children' ? 'active':'' ?>" id="tab-children">
    <?php if (empty($children)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-emoji-smile" style="font-size:2rem"></i>
        <p class="mt-2">No validated children (0–59 months) found under your families.</p>
    </div>
    <?php else: ?>
    <div class="kn-table-wrapper">
        <div class="table-responsive">
            <table class="table kn-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name of Child</th>
                        <th>Sex</th>
                        <th>Date of Birth</th>
                        <th>Age (mos)</th>
                        <th>Purok</th>
                        <th>Mother/Caregiver</th>
                        <th>Last Assessed</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($children as $i => $c): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($c['full_name']) ?></td>
                    <td><?= in_array($c['sex'], ['M','Male']) ? '♂ Male' : '♀ Female' ?></td>
                    <td><?= $c['dob'] ? date('M j, Y', strtotime($c['dob'])) : '—' ?></td>
                    <td><strong><?= (int)$c['age_in_months'] ?></strong> mos</td>
                    <td><?= htmlspecialchars($c['purok'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($c['caregiver_name'] ?? '—') ?></td>
                    <td>
                        <?php if ($c['last_assessed']): ?>
                            <span class="status-badge assessed">
                                <i class="bi bi-check-circle-fill"></i>
                                <?= date('M j, Y', strtotime($c['last_assessed'])) ?>
                            </span>
                        <?php else: ?>
                            <span class="status-badge not-assessed">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                Not yet assessed
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        // Route to correct assessment URL based on source
                        if (!empty($c['child_id'])) {
                            $assessUrl = "index.php?action=assessmentForm&type=child&child_id=" . (int)$c['child_id'];
                        } else {
                            $assessUrl = "index.php?action=assessmentForm&type=child&fm_member_id=" . (int)$c['fm_member_id'];
                        }
                        ?>
                        <div class="action-btn-group">
                            <a href="<?= $assessUrl ?>" class="action-btn btn-assess">
                                <i class="bi bi-clipboard2-plus"></i> Assess
                            </a>
                            <?php if ($c['last_assessed']): ?>
                            <a href="<?= $assessUrl ?>" class="action-btn btn-followup">
                                <i class="bi bi-arrow-repeat"></i> Follow Up
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- ── Tab: Maternal ─────────────────────────────────────────────────────── -->
<div class="tab-pane <?= $activeTab==='maternal' ? 'active':'' ?>" id="tab-maternal">
    <?php if (empty($maternal)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-heart-pulse" style="font-size:2rem"></i>
        <p class="mt-2">No pregnant or lactating mothers found under your validated families.</p>
    </div>
    <?php else: ?>
    <div class="kn-table-wrapper">
        <div class="table-responsive">
            <table class="table kn-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Status</th>
                        <th>Age</th>
                        <th>Purok</th>
                        <th>HH #</th>
                        <th>Last Assessed</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($maternal as $i => $m): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($m['full_name']) ?></td>
                    <td>
                        <?php
                        $ps = $m['pregnancy_status'] ?? '';
                        $bs = $m['breastfeeding_status'] ?? '';
                        if (str_contains($ps, 'Pregnant')): ?>
                            <span class="badge bg-info text-dark"><?= htmlspecialchars($ps) ?></span>
                        <?php elseif ($bs !== ''): ?>
                            <span class="badge bg-success"><?= htmlspecialchars($bs) ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= $m['age_in_years'] !== null ? (int)$m['age_in_years'] . ' yrs' : '—' ?></td>
                    <td><?= htmlspecialchars($m['purok'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($m['hh_number'] ?? '—') ?></td>
                    <td>
                        <?php if ($m['last_assessed']): ?>
                            <span class="status-badge assessed">
                                <i class="bi bi-check-circle-fill"></i>
                                <?= date('M j, Y', strtotime($m['last_assessed'])) ?>
                            </span>
                        <?php else: ?>
                            <span class="status-badge not-assessed">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                Not yet assessed
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        // Build correct assess URL: user_id for registered users, fm_member_id for BNS-only
                        if (!empty($m['user_id'])) {
                            $assessUrl = "index.php?action=assessmentForm&type=maternal&user_id=" . (int)$m['user_id'];
                        } else {
                            $assessUrl = "index.php?action=assessmentForm&type=maternal&fm_member_id=" . (int)$m['fm_member_id'];
                        }
                        ?>
                        <div class="action-btn-group">
                            <a href="<?= $assessUrl ?>" class="action-btn btn-assess">
                                <i class="bi bi-clipboard2-plus"></i> Assess
                            </a>
                            <?php if ($m['last_assessed']): ?>
                            <a href="<?= $assessUrl ?>" class="action-btn btn-followup">
                                <i class="bi bi-arrow-repeat"></i> Follow Up
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- ── Tab: Elderly ──────────────────────────────────────────────────────── -->
<div class="tab-pane <?= $activeTab==='seniors' ? 'active':'' ?>" id="tab-seniors">
    <?php if (empty($seniors)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-person-cane" style="font-size:2rem"></i>
        <p class="mt-2">No elderly citizens (60+ years) found under your validated families.</p>
    </div>
    <?php else: ?>
    <div class="kn-table-wrapper">
        <div class="table-responsive">
            <table class="table kn-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Sex</th>
                        <th>Age</th>
                        <th>Purok</th>
                        <th>HH #</th>
                        <th>Last Assessed</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($seniors as $i => $s): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($s['full_name']) ?></td>
                    <td><?= in_array($s['sex'], ['M','Male']) ? '♂ Male' : '♀ Female' ?></td>
                    <td><?= (int)$s['age_in_years'] ?> yrs</td>
                    <td><?= htmlspecialchars($s['purok'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($s['hh_number'] ?? '—') ?></td>
                    <td>
                        <?php if ($s['last_assessed']): ?>
                            <span class="status-badge assessed">
                                <i class="bi bi-check-circle-fill"></i>
                                <?= date('M j, Y', strtotime($s['last_assessed'])) ?>
                            </span>
                        <?php else: ?>
                            <span class="status-badge not-assessed">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                Not yet assessed
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-btn-group">
                            <?php
                            if (!empty($s['user_id'])) {
                                $seniorAssessUrl = "index.php?action=assessmentForm&type=senior&user_id=" . (int)$s['user_id'];
                            } else {
                                $seniorAssessUrl = "index.php?action=assessmentForm&type=senior&fm_member_id=" . (int)$s['fm_member_id'];
                            }
                            ?>
                            <a href="<?= $seniorAssessUrl ?>" class="action-btn btn-assess">
                                <i class="bi bi-clipboard2-plus"></i> Assess
                            </a>
                            <?php if ($s['last_assessed']): ?>
                            <a href="<?= $seniorAssessUrl ?>" class="action-btn btn-followup">
                                <i class="bi bi-arrow-repeat"></i> Follow Up
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- ── Tab: Recent Assessments ──────────────────────────────────────────── -->
<div class="tab-pane <?= $activeTab==='recent' ? 'active':'' ?>" id="tab-recent">
    <?php if (empty($recent)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-clock-history" style="font-size:2rem"></i>
        <p class="mt-2">No assessments recorded yet.</p>
    </div>
    <?php else: ?>
    <div class="kn-table-wrapper">
        <div class="table-responsive">
            <table class="table kn-table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Age</th>
                        <th>Weight</th>
                        <th>Height</th>
                        <th>WFA</th>
                        <th>HFA</th>
                        <th>WFH / BMI</th>
                        <th>Flag</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $colors = ['SUW'=>'danger','UW'=>'warning','SSt'=>'danger','St'=>'warning',
                           'SAM'=>'danger','MAM'=>'warning','OW'=>'info','Ob'=>'info',
                           'Normal'=>'success','Tall'=>'primary',
                           'Underweight'=>'warning','Overweight'=>'info','Obese'=>'info'];
                foreach ($recent as $r):
                    $isChild = $r['assessed_type'] === 'child';
                ?>
                <tr>
                    <td><?= date('M j, Y', strtotime($r['assessment_date'])) ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($r['full_name']) ?></td>
                    <td><span class="badge bg-secondary"><?= ucfirst($r['assessed_type']) ?></span></td>
                    <td><?= $isChild ? ((int)$r['age_in_months'].' mos') : ((int)$r['age_in_years'].' yrs') ?></td>
                    <td><?= $r['weight_kg'] ?> kg</td>
                    <td><?= $r['height_cm'] ?> cm</td>
                    <td><?php if ($r['wfa_status']): $c=$colors[$r['wfa_status']]??'secondary'; ?>
                        <span class="badge bg-<?= $c ?>"><?= $r['wfa_status'] ?></span>
                    <?php else: ?>—<?php endif; ?></td>
                    <td><?php if ($r['hfa_status']): $c=$colors[$r['hfa_status']]??'secondary'; ?>
                        <span class="badge bg-<?= $c ?>"><?= $r['hfa_status'] ?></span>
                    <?php else: ?>—<?php endif; ?></td>
                    <td><?php if ($r['wfh_status']): $c=$colors[$r['wfh_status']]??'secondary'; ?>
                        <span class="badge bg-<?= $c ?>"><?= $r['wfh_status'] ?></span>
                    <?php elseif ($r['bmi_status']): $c=$colors[$r['bmi_status']]??'secondary'; ?>
                        <span class="badge bg-<?= $c ?>"><?= $r['bmi_status'] ?> (<?= $r['bmi'] ?>)</span>
                    <?php else: ?>—<?php endif; ?></td>
                    <td>
                        <?php if ($r['is_at_risk']): ?>
                            <span class="badge bg-danger">At-risk</span>
                        <?php elseif ($r['needs_monitoring']): ?>
                            <span class="badge bg-warning text-dark">Monitor</span>
                        <?php else: ?>
                            <span class="badge bg-success">Normal</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    event.currentTarget.classList.add('active');
}
</script>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
