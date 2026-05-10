<?php
$pageTitle = 'Resident Assessment';
$activeNav = 'data_encoding';
require_once __DIR__ . '/../templates/bns_layout.php';

$activeTab = $_GET['tab'] ?? 'children';
?>
<style>
:root{--kn-green:#6B7A3A;--kn-orange:#C4722A;--kn-dark:#3D4A1E;--kn-muted:rgba(61,74,30,.55);}
.tab-btn{background:#fff;border:1.5px solid rgba(107,122,58,.2);border-radius:8px;padding:.45rem 1.1rem;font-size:.9rem;font-weight:600;color:var(--kn-muted);cursor:pointer;transition:.2s;}
.tab-btn.active{background:var(--kn-green);color:#fff;border-color:var(--kn-green);}
.tab-pane{display:none;}.tab-pane.active{display:block;}
.assess-btn{background:var(--kn-green);color:#fff;border:none;border-radius:7px;padding:.3rem .85rem;font-size:.82rem;font-weight:600;text-decoration:none;transition:.2s;}
.assess-btn:hover{background:#556030;color:#fff;}
.badge-risk{background:rgba(196,114,42,.12);color:var(--kn-orange);border:1px solid rgba(196,114,42,.3);border-radius:5px;font-size:.75rem;padding:.15em .5em;font-weight:700;}
.badge-ok{background:rgba(107,122,58,.1);color:var(--kn-green);border:1px solid rgba(107,122,58,.2);border-radius:5px;font-size:.75rem;padding:.15em .5em;font-weight:700;}
.kn-table th{background:rgba(107,122,58,.07);color:var(--kn-dark);font-size:.82rem;font-weight:700;border-bottom:2px solid rgba(107,122,58,.15);}
.kn-table td{font-size:.88rem;vertical-align:middle;border-bottom:1px solid rgba(107,122,58,.08);}
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">Resident Assessment</h4>
        <p class="text-muted mb-0" style="font-size:.88rem">OPT Plus — List of Recipients for Nutrition Assessment</p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?action=optResults"
           style="display:inline-flex;align-items:center;gap:.45rem;background:#fff;color:var(--kn-dark);border:1.5px solid rgba(61,74,30,.3);border-radius:8px;padding:.4rem 1rem;font-size:.88rem;font-weight:600;text-decoration:none;transition:.2s"
           onmouseover="this.style.background='rgba(61,74,30,.06)'" onmouseout="this.style.background='#fff'">
            <i class="bi bi-table"></i> OPT Plus Results
        </a>
        <a href="index.php?action=formCReport"
           style="display:inline-flex;align-items:center;gap:.45rem;background:#fff;color:var(--kn-orange);border:1.5px solid rgba(196,114,42,.4);border-radius:8px;padding:.4rem 1rem;font-size:.88rem;font-weight:600;text-decoration:none;transition:.2s"
           onmouseover="this.style.background='rgba(196,114,42,.08)'" onmouseout="this.style.background='#fff'">
            <i class="bi bi-file-earmark-bar-graph-fill"></i> Form C — At-risk Children
        </a>
        <a href="index.php?action=p12Monitoring"
           style="display:inline-flex;align-items:center;gap:.45rem;background:#fff;color:var(--kn-green);border:1.5px solid rgba(107,122,58,.35);border-radius:8px;padding:.4rem 1rem;font-size:.88rem;font-weight:600;text-decoration:none;transition:.2s"
           onmouseover="this.style.background='rgba(107,122,58,.07)'" onmouseout="this.style.background='#fff'">
            <i class="bi bi-clipboard2-pulse-fill"></i> Monitoring List
        </a>
        <a href="index.php?action=pregnantMasterlist"
           style="display:inline-flex;align-items:center;gap:.45rem;background:#fff;color:#e74c3c;border:1.5px solid rgba(231,76,60,.35);border-radius:8px;padding:.4rem 1rem;font-size:.88rem;font-weight:600;text-decoration:none;transition:.2s"
           onmouseover="this.style.background='rgba(231,76,60,.06)'" onmouseout="this.style.background='#fff'">
            <i class="bi bi-heart-pulse-fill"></i> Pregnant
        </a>
        <a href="index.php?action=lactatingMasterlist"
           style="display:inline-flex;align-items:center;gap:.45rem;background:#fff;color:#8e44ad;border:1.5px solid rgba(142,68,173,.35);border-radius:8px;padding:.4rem 1rem;font-size:.88rem;font-weight:600;text-decoration:none;transition:.2s"
           onmouseover="this.style.background='rgba(142,68,173,.06)'" onmouseout="this.style.background='#fff'">
            <i class="bi bi-person-heart"></i> Lactating
        </a>
        <a href="index.php?action=seniorMasterlist"
           style="display:inline-flex;align-items:center;gap:.45rem;background:#fff;color:#2980b9;border:1.5px solid rgba(41,128,185,.35);border-radius:8px;padding:.4rem 1rem;font-size:.88rem;font-weight:600;text-decoration:none;transition:.2s"
           onmouseover="this.style.background='rgba(41,128,185,.06)'" onmouseout="this.style.background='#fff'">
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
    <div class="card shadow-sm">
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
                            <span class="badge-ok"><?= date('M j, Y', strtotime($c['last_assessed'])) ?></span>
                        <?php else: ?>
                            <span class="badge-risk">Not yet assessed</span>
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
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="<?= $assessUrl ?>" class="assess-btn" style="padding:.2rem .45rem;font-size:.7rem;white-space:nowrap">
                                <i class="bi bi-clipboard2-plus" style="font-size:.65rem"></i> Assess
                            </a>
                            <?php if ($c['last_assessed']): ?>
                            <a href="<?= $assessUrl ?>" class="assess-btn" style="background:var(--kn-orange);padding:.2rem .45rem;font-size:.7rem;white-space:nowrap">
                                <i class="bi bi-arrow-repeat" style="font-size:.65rem"></i> Follow Up
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
    <div class="card shadow-sm">
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
                            <span class="badge-ok"><?= date('M j, Y', strtotime($m['last_assessed'])) ?></span>
                        <?php else: ?>
                            <span class="badge-risk">Not yet assessed</span>
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
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="<?= $assessUrl ?>"
                               class="assess-btn" style="padding:.2rem .45rem;font-size:.7rem;white-space:nowrap">
                                <i class="bi bi-clipboard2-plus" style="font-size:.65rem"></i> Assess
                            </a>
                            <?php if ($m['last_assessed']): ?>
                            <a href="<?= $assessUrl ?>"
                               class="assess-btn" style="background:var(--kn-orange);padding:.2rem .45rem;font-size:.7rem;white-space:nowrap">
                                <i class="bi bi-arrow-repeat" style="font-size:.65rem"></i> Follow Up
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
    <div class="card shadow-sm">
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
                            <span class="badge-ok"><?= date('M j, Y', strtotime($s['last_assessed'])) ?></span>
                        <?php else: ?>
                            <span class="badge-risk">Not yet assessed</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <?php
                            if (!empty($s['user_id'])) {
                                $seniorAssessUrl = "index.php?action=assessmentForm&type=senior&user_id=" . (int)$s['user_id'];
                            } else {
                                $seniorAssessUrl = "index.php?action=assessmentForm&type=senior&fm_member_id=" . (int)$s['fm_member_id'];
                            }
                            ?>
                            <a href="<?= $seniorAssessUrl ?>"
                               class="assess-btn" style="padding:.2rem .45rem;font-size:.7rem;white-space:nowrap">
                                <i class="bi bi-clipboard2-plus" style="font-size:.65rem"></i> Assess
                            </a>
                            <?php if ($s['last_assessed']): ?>
                            <a href="<?= $seniorAssessUrl ?>"
                               class="assess-btn" style="background:var(--kn-orange);padding:.2rem .45rem;font-size:.7rem;white-space:nowrap">
                                <i class="bi bi-arrow-repeat" style="font-size:.65rem"></i> Follow Up
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
    <div class="card shadow-sm">
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
