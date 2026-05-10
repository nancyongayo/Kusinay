<?php
require_once __DIR__ . '/../templates/bns_layout.php';
$quarterLabel = ['Q1 (Jan–Mar)', 'Q2 (Apr–Jun)', 'Q3 (Jul–Sep)', 'Q4 (Oct–Dec)'];
$qLabel = $quarterLabel[$quarter - 1] ?? "Q$quarter";
$total  = count($records);

function nsColor(string $s): string {
    return match($s) {
        'Low'    => '#e67e22',
        'High'   => '#e74c3c',
        'Normal' => '#27ae60',
        default  => '#95a5a6',
    };
}
?>
<style>
:root{--kn-green:#6B7A3A;--kn-orange:#C4722A;--kn-dark:#3D4A1E;--kn-muted:rgba(61,74,30,.55);}
.form-wrapper{background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:1rem;overflow:hidden;box-shadow:0 2px 12px rgba(61,74,30,.08);}
.nnc-header-band{background:linear-gradient(135deg,#3D4A1E 0%,#6B7A3A 100%);color:#fff;padding:.85rem 1.5rem;display:flex;align-items:center;gap:1rem;}
.nnc-seal-placeholder{width:48px;height:48px;flex-shrink:0;border:2px dashed rgba(255,255,255,.4);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.5rem;color:rgba(255,255,255,.6);text-align:center;line-height:1.3;}
.nnc-agency{flex:1;text-align:center;}
.nnc-agency .l3{font-size:.95rem;font-weight:800;letter-spacing:.04em;}
.nnc-agency .l1,.nnc-agency .l4{font-size:.75rem;opacity:.85;}
.nnc-logo-group{display:flex;gap:.4rem;flex-shrink:0;}
.nnc-logo-pill{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);border-radius:7px;padding:.3rem .5rem;text-align:center;font-size:.58rem;color:rgba(255,255,255,.9);line-height:1.4;}
.form-title-bar{background:rgba(107,122,58,.06);border-bottom:1.5px solid rgba(107,122,58,.12);padding:.65rem 1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;}
.year-badge{background:var(--kn-green);color:#fff;border-radius:6px;padding:.15rem .65rem;font-weight:700;font-size:.85rem;}
.q-badge{background:var(--kn-orange);color:#fff;border-radius:6px;padding:.15rem .65rem;font-weight:700;font-size:.85rem;}
.ml-table{width:100%;border-collapse:collapse;font-size:.78rem;}
.ml-table th{background:rgba(107,122,58,.1);color:var(--kn-dark);font-weight:700;font-size:.7rem;text-align:center;padding:.4rem .35rem;border:1px solid rgba(107,122,58,.2);}
.ml-table th.mon-th{background:rgba(107,122,58,.18);}
.ml-table td{padding:.35rem .35rem;border:1px solid rgba(107,122,58,.1);vertical-align:middle;text-align:center;font-size:.78rem;}
.ml-table tr:nth-child(even) td{background:rgba(107,122,58,.025);}
.ml-table td.tl{text-align:left;}
.ns-chip{display:inline-block;padding:.12em .5em;border-radius:4px;font-weight:700;font-size:.7rem;color:#fff;}
.empty-state{text-align:center;padding:3rem 1rem;color:var(--kn-muted);}
.form-footer{background:rgba(107,122,58,.04);border-top:1.5px solid rgba(107,122,58,.1);padding:.65rem 1.5rem;display:flex;justify-content:space-between;font-size:.78rem;color:var(--kn-muted);}

/* Print */
body .print-only{display:none !important;}
@page{size:A4 landscape;margin:8mm 10mm;}
@media print{
    *{-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;}
    @page{size:A4 landscape;margin:8mm 10mm;}
    .no-print,.kn-sidebar,.kn-topbar,.kn-flash,.screen-toolbar,.form-wrapper{display:none!important;}
    .kn-main{margin:0!important;padding:0!important;width:100%!important;}
    .kn-content{margin:0!important;padding:0!important;width:100%!important;}
    body{font-size:8pt;background:#fff!important;margin:0!important;padding:0!important;}
    body .print-only{display:block!important;position:fixed;top:0;left:0;width:100%;z-index:9999;}
    .ml-paper{font-family:Arial,sans-serif;font-size:7.5pt;color:#000;padding:5mm 6mm;}
    .ml-ptable{width:100%;border-collapse:collapse;font-size:6.5pt;}
    .ml-ptable th,.ml-ptable td{border:1px solid #000;padding:2px 3px;vertical-align:middle;text-align:center;}
    .ml-ptable th{background:#d9d9d9!important;font-weight:700;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    .ml-ptable th.mon-th{background:#b8cca0!important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    .ml-ptable td.tl{text-align:left;}
    .nnc-ph{display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;}
    .nnc-ph-seal{width:50px;height:50px;border:1px solid #999;display:flex;align-items:center;justify-content:center;font-size:5.5pt;color:#888;text-align:center;flex-shrink:0;}
    .nnc-ph-center{text-align:center;flex:1;padding:0 8px;line-height:1.5;}
}
</style>

<!-- Screen toolbar -->
<div class="screen-toolbar no-print" style="margin-bottom:1.5rem">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="index.php?action=dataEncoding"
               style="display:inline-flex;align-items:center;gap:.4rem;background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.35rem .85rem;font-size:.88rem;font-weight:600;text-decoration:none">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <div>
                <div class="fw-bold" style="color:var(--kn-dark)">Pregnant Women Masterlist</div>
                <div style="font-size:.78rem;color:var(--kn-muted)">Form M-1.a — Quarterly Monitoring Sheet</div>
            </div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <form method="GET" action="index.php" class="d-flex gap-2 align-items-center flex-wrap">
                <input type="hidden" name="action" value="pregnantMasterlist">
                <select name="quarter" class="form-select form-select-sm" style="width:130px;border:1.5px solid rgba(107,122,58,.25);border-radius:7px">
                    <?php foreach ([1=>'Q1 (Jan–Mar)',2=>'Q2 (Apr–Jun)',3=>'Q3 (Jul–Sep)',4=>'Q4 (Oct–Dec)'] as $q => $ql): ?>
                    <option value="<?= $q ?>" <?= $q == $quarter ? 'selected' : '' ?>><?= $ql ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="year" class="form-select form-select-sm" style="width:90px;border:1.5px solid rgba(107,122,58,.25);border-radius:7px">
                    <?php for ($y = date('Y'); $y >= date('Y')-3; $y--): ?>
                    <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="btn btn-sm fw-semibold"
                        style="background:rgba(107,122,58,.1);color:var(--kn-green);border:1.5px solid rgba(107,122,58,.25);border-radius:7px">Filter</button>
            </form>
            <button onclick="window.print()"
                    style="display:inline-flex;align-items:center;gap:.4rem;background:var(--kn-green);color:#fff;border:none;border-radius:8px;padding:.4rem 1rem;font-size:.88rem;font-weight:600;cursor:pointer">
                <i class="bi bi-printer-fill"></i> Print / Save as PDF
            </button>
        </div>
    </div>
</div>

<!-- Screen UI -->
<div class="form-wrapper">
    <div class="nnc-header-band">
        <div class="nnc-seal-placeholder">SPACE FOR<br>OFFICIAL<br>SEAL</div>
        <div class="nnc-agency">
            <div class="l1">Republic of the Philippines · Department of Health</div>
            <div class="l3">NATIONAL NUTRITION COUNCIL</div>
            <div class="l4">XI Davao Region</div>
        </div>
        <div class="nnc-logo-group">
            <div class="nnc-logo-pill">
                <img src="<?= $imgBase ?>/public/images/nnc_logo.png" alt="NNC"
                     style="width:34px;height:34px;object-fit:contain;display:block;margin:0 auto 2px"
                     onerror="this.style.display='none'">
                <span>NNC</span>
            </div>
        </div>
    </div>
    <div class="form-title-bar">
        <div style="font-weight:700;font-size:.92rem;color:var(--kn-dark)">
            Masterlist and Monitoring Sheet of <span style="color:#e74c3c">Pregnant Women</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="q-badge"><?= $qLabel ?></span>
            <span class="year-badge">CY <?= $year ?></span>
            <span style="font-size:.82rem;color:var(--kn-muted)">Total: <strong><?= $total ?></strong></span>
        </div>
    </div>

    <div style="padding:0 1.5rem 1.5rem;overflow-x:auto">
        <?php if (empty($records)): ?>
        <div class="empty-state">
            <i class="bi bi-heart-pulse" style="font-size:2.5rem;color:rgba(107,122,58,.3);display:block;margin-bottom:.5rem"></i>
            <div class="fw-semibold" style="color:var(--kn-dark)">No pregnant women assessed for <?= $qLabel ?> <?= $year ?></div>
            <div style="font-size:.82rem;margin-top:.25rem">Go to Resident Assessment → Maternal tab → Assess to start recording.</div>
        </div>
        <?php else: ?>
        <table class="ml-table" style="margin-top:1rem">
            <thead>
                <tr>
                    <th rowspan="2" style="width:30px">HH No.</th>
                    <th rowspan="2" style="width:130px">Pregnant Woman's Name</th>
                    <th rowspan="2" style="width:55px" title="Civil Status">CS<br><span style="font-weight:400;font-size:.6rem;text-transform:none;opacity:.8">Civil Status</span></th>
                    <th rowspan="2" style="width:28px" title="PhilHealth Member">PH<br><span style="font-weight:400;font-size:.6rem;text-transform:none;opacity:.8">PhilHealth</span></th>
                    <th rowspan="2" style="width:28px" title="4Ps Beneficiary (Pantawid Pamilyang Pilipino Program)">4Ps<br><span style="font-weight:400;font-size:.6rem;text-transform:none;opacity:.8">4Ps</span></th>
                    <th rowspan="2" style="width:65px">Birthday</th>
                    <th rowspan="2" style="width:65px" title="Last Menstrual Period">LMP<br><span style="font-weight:400;font-size:.6rem;text-transform:none;opacity:.8">Last Menstrual Period</span></th>
                    <th rowspan="2" style="width:65px" title="Expected Date of Confinement / Due Date">EDC<br><span style="font-weight:400;font-size:.6rem;text-transform:none;opacity:.8">Expected Date of Confinement</span></th>
                    <th rowspan="2" style="width:38px" title="Height in centimeters">HT (cm)<br><span style="font-weight:400;font-size:.6rem;text-transform:none;opacity:.8">Height</span></th>
                    <th rowspan="2" style="width:100px">Name of Spouse</th>
                    <th colspan="3" class="mon-th">1st Trimester</th>
                    <th colspan="3" class="mon-th">2nd Trimester</th>
                    <th colspan="3" class="mon-th">3rd Trimester</th>
                    <th rowspan="2" style="width:70px">Contact No.</th>
                </tr>
                <tr>
                    <?php for ($m = 1; $m <= 3; $m++): ?>
                    <th class="mon-th" style="width:28px" title="Age of Gestation (months)">AOG<br><span style="font-weight:400;font-size:.6rem;text-transform:none;opacity:.8">Age of Gestation</span></th>
                    <th class="mon-th" style="width:35px" title="Weight in kilograms">WT (kg)<br><span style="font-weight:400;font-size:.6rem;text-transform:none;opacity:.8">Weight</span></th>
                    <th class="mon-th" style="width:40px" title="Nutritional Status (Low / Normal / High weight gain)">NS<br><span style="font-weight:400;font-size:.6rem;text-transform:none;opacity:.8">Nutritional Status</span></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $i => $r): 
                // Format name as: Last, First MI
                $lastName  = $r['last_name']  ?? '';
                $firstName = $r['first_name'] ?? '';
                $mi        = $r['middle_name'] ? strtoupper(substr($r['middle_name'], 0, 1)) . '.' : '';
                $fullName  = trim($lastName . ($firstName ? ', ' . $firstName : '') . ($mi ? ' ' . $mi : ''));
            ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td class="tl fw-semibold"><?= htmlspecialchars($fullName) ?></td>
                <td><?= htmlspecialchars($r['civil_status'] ?? '—') ?></td>
                <td><?= $r['philhealth'] === null ? '—' : ($r['philhealth'] ? 'Y' : 'N') ?></td>
                <td><?= $r['is_4ps']     === null ? '—' : ($r['is_4ps']     ? 'Y' : 'N') ?></td>
                <td><?= $r['dob'] ? date('m/d/Y', strtotime($r['dob'])) : '—' ?></td>
                <td><?= $r['lmp'] ? date('m/d/Y', strtotime($r['lmp'])) : '—' ?></td>
                <td><?= $r['edc'] ? date('m/d/Y', strtotime($r['edc'])) : '—' ?></td>
                <td><?= $r['height_cm'] ?></td>
                <td><?= htmlspecialchars($r['spouse_name'] ?? '—') ?></td>
                <!-- Month 1 -->
                <td><?= $r['month1_aog'] ?? '—' ?></td>
                <td><?= $r['month1_weight'] ?? '—' ?></td>
                <td>
                    <?php if (!empty($r['month1_status'])): ?>
                    <span class="ns-chip" style="background:<?= nsColor($r['month1_status']) ?>">
                        <?= $r['month1_status'] ?>
                    </span>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <!-- Month 2 -->
                <td><?= $r['month2_aog'] ?? '' ?></td>
                <td><?= $r['month2_weight'] ?? '' ?></td>
                <td>
                    <?php if (!empty($r['month2_status'])): ?>
                    <span class="ns-chip" style="background:<?= nsColor($r['month2_status']) ?>">
                        <?= $r['month2_status'] ?>
                    </span>
                    <?php endif; ?>
                </td>
                <!-- Month 3 -->
                <td><?= $r['month3_aog'] ?? '' ?></td>
                <td><?= $r['month3_weight'] ?? '' ?></td>
                <td>
                    <?php if (!empty($r['month3_status'])): ?>
                    <span class="ns-chip" style="background:<?= nsColor($r['month3_status']) ?>">
                        <?= $r['month3_status'] ?>
                    </span>
                    <?php endif; ?>
                </td>
                <td class="tl"><?= htmlspecialchars($r['contact'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Abbreviation Legend -->
    <div style="padding:.65rem 1.5rem;border-top:1px solid rgba(107,122,58,.1);background:rgba(107,122,58,.03)">
        <div style="font-size:.72rem;color:var(--kn-muted);display:flex;flex-wrap:wrap;gap:.4rem 1.2rem">
            <span><strong>CS</strong> — Civil Status</span>
            <span><strong>PH</strong> — PhilHealth Member</span>
            <span><strong>4Ps</strong> — Pantawid Pamilyang Pilipino Program</span>
            <span><strong>LMP</strong> — Last Menstrual Period</span>
            <span><strong>EDC</strong> — Expected Date of Confinement</span>
            <span><strong>HT</strong> — Height</span>
            <span><strong>AOG</strong> — Age of Gestation (months)</span>
            <span><strong>WT</strong> — Weight</span>
            <span><strong>NS</strong> — Nutritional Status</span>
        </div>
    </div>

    <div class="form-footer">
        <div><strong>Reported by:</strong> <?= htmlspecialchars($bnsName) ?> (BNS)</div>
        <div><?= $total ?> record(s) · <?= $qLabel ?> <?= $year ?></div>
    </div>
</div>

<!-- PRINT-ONLY -->
<div class="print-only">
<div class="ml-paper">
    <div style="text-align:center;font-size:8pt;margin-bottom:2px">
        Purok: _____________ &nbsp;&nbsp; Barangay: _____________ &nbsp;&nbsp; City/Municipality: _____________
    </div>
    <div style="text-align:center;font-weight:800;font-size:10pt;margin-bottom:1px">
        MASTERLIST AND MONITORING SHEET OF <span style="color:#c00">PREGNANT WOMEN</span>
    </div>
    <div style="text-align:center;font-size:8.5pt;margin-bottom:4px">
        _____ Quarter CY <?= $year ?>
    </div>

    <table class="ml-ptable">
        <thead>
            <tr>
                <th rowspan="2" style="width:22px">HH No.</th>
                <th colspan="3" rowspan="1">Pregnant Woman's Name</th>
                <th rowspan="2" style="width:16px">CS</th>
                <th rowspan="2" style="width:16px">PH Y/N</th>
                <th rowspan="2" style="width:16px">4Ps Y/N</th>
                <th rowspan="2" style="width:48px">Birthday (mm/dd/yy)</th>
                <th rowspan="2" style="width:48px">LMP (mm/dd/yy)</th>
                <th rowspan="2" style="width:48px">EDC (mm/dd/yy)</th>
                <th rowspan="2" style="width:28px">HT (cm)</th>
                <th rowspan="2" style="width:80px">Name of Spouse</th>
                <th colspan="3" class="mon-th">1st Trimester</th>
                <th colspan="3" class="mon-th">2nd Trimester</th>
                <th colspan="3" class="mon-th">3rd Trimester</th>
                <th rowspan="2" style="width:55px">Contact No.</th>
            </tr>
            <tr>
                <th style="width:70px">Last</th>
                <th style="width:60px">First</th>
                <th style="width:20px">MI</th>
                <?php for ($m = 1; $m <= 3; $m++): ?>
                <th class="mon-th" style="width:22px">AOG</th>
                <th class="mon-th" style="width:28px">WT (kg)</th>
                <th class="mon-th" style="width:22px">NS</th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($records)): ?>
        <tr><td colspan="22" style="text-align:center;padding:8px">No pregnant women assessed for <?= $qLabel ?> <?= $year ?>.</td></tr>
        <?php else: ?>
        <?php foreach ($records as $i => $r):
            $nameParts = explode(' ', $r['full_name'] ?? '', 2);
            $lastName  = $r['last_name']  ?? ($nameParts[0] ?? '');
            $firstName = $r['first_name'] ?? ($nameParts[1] ?? '');
            $mi        = !empty($r['middle_name']) ? strtoupper(substr($r['middle_name'], 0, 1)) . '.' : '';
        ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td class="tl"><?= htmlspecialchars($lastName) ?></td>
            <td class="tl"><?= htmlspecialchars($firstName) ?></td>
            <td><?= htmlspecialchars($mi) ?></td>
            <td><?= htmlspecialchars($r['civil_status'] ?? '') ?></td>
            <td><?= $r['philhealth'] === null ? '' : ($r['philhealth'] ? 'Y' : 'N') ?></td>
            <td><?= $r['is_4ps']     === null ? '' : ($r['is_4ps']     ? 'Y' : 'N') ?></td>
            <td><?= $r['dob'] ? date('m/d/y', strtotime($r['dob'])) : '' ?></td>
            <td><?= $r['lmp'] ? date('m/d/y', strtotime($r['lmp'])) : '' ?></td>
            <td><?= $r['edc'] ? date('m/d/y', strtotime($r['edc'])) : '' ?></td>
            <td><?= $r['height_cm'] ?></td>
            <td><?= htmlspecialchars($r['spouse_name'] ?? '') ?></td>
            <td><?= $r['month1_aog'] ?? '' ?></td>
            <td><?= $r['month1_weight'] ?? '' ?></td>
            <td><?= $r['month1_status'] ?? '' ?></td>
            <td><?= $r['month2_aog'] ?? '' ?></td>
            <td><?= $r['month2_weight'] ?? '' ?></td>
            <td><?= $r['month2_status'] ?? '' ?></td>
            <td><?= $r['month3_aog'] ?? '' ?></td>
            <td><?= $r['month3_weight'] ?? '' ?></td>
            <td><?= $r['month3_status'] ?? '' ?></td>
            <td><?= htmlspecialchars($r['contact'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php for ($b = count($records); $b < 15; $b++): ?>
        <tr><td><?= $b+1 ?>.</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <?php endfor; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:8px;font-size:6.5pt">
        <strong>LEGENDS:</strong> HH No. - household number &nbsp;|&nbsp; MI - middle initial &nbsp;|&nbsp;
        CS - civil status (S-single/M-married/DP-domestic partnership/W-widow) &nbsp;|&nbsp;
        PH - PhilHealth member (yes/no) &nbsp;|&nbsp; 4Ps - 4Ps beneficiary (yes/no) &nbsp;|&nbsp;
        LMP - last menstrual period &nbsp;|&nbsp; EDC - expected date of confinement &nbsp;|&nbsp;
        HT - height &nbsp;|&nbsp; AOG - age of gestation &nbsp;|&nbsp; WT - weight &nbsp;|&nbsp;
        NS - nutritional status (IOM Weight Gain Guidelines)
    </div>

    <div style="margin-top:12px;display:flex;justify-content:space-between;font-size:7.5pt">
        <div>Reported by: <span style="border-bottom:1px solid #000;min-width:150px;display:inline-block">&nbsp;<?= htmlspecialchars($bnsName) ?>&nbsp;</span><br>
            <span style="font-size:6.5pt">Signature over printed name of the BNS/BHW</span></div>
        <div>Certified True by: <span style="border-bottom:1px solid #000;min-width:150px;display:inline-block">&nbsp;</span><br>
            <span style="font-size:6.5pt">Signature over printed name of the Midwife</span></div>
        <div>Noted by: <span style="border-bottom:1px solid #000;min-width:120px;display:inline-block">&nbsp;</span><br>
            <span style="font-size:6.5pt">Signature over printed name of the MNAO</span></div>
    </div>
    <div style="text-align:right;font-size:6.5pt;margin-top:4px">Form M-1.a</div>
</div>
</div><!-- /print-only -->

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
