<?php
require_once __DIR__ . '/../templates/bns_layout.php';
$quarterLabel = ['Q1 (Jan–Mar)', 'Q2 (Apr–Jun)', 'Q3 (Jul–Sep)', 'Q4 (Oct–Dec)'];
$qLabel = $quarterLabel[$quarter - 1] ?? "Q$quarter";
$total  = count($records);
function bmiColorS(string $s): string {
    return match($s) {
        'Underweight' => '#e67e22',
        'At Risk'     => '#f39c12',
        'Normal'      => '#27ae60',
        'Overweight'  => '#2980b9',
        'Obese'       => '#e74c3c',
        default       => '#95a5a6',
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
.ml-table{width:100%;border-collapse:collapse;font-size:.8rem;}
.ml-table th{background:rgba(107,122,58,.1);color:var(--kn-dark);font-weight:700;font-size:.72rem;text-align:center;padding:.4rem .4rem;border:1px solid rgba(107,122,58,.2);}
.ml-table td{padding:.38rem .4rem;border:1px solid rgba(107,122,58,.1);vertical-align:middle;text-align:center;font-size:.8rem;}
.ml-table tr:nth-child(even) td{background:rgba(107,122,58,.025);}
.ml-table td.tl{text-align:left;}
.ns-chip{display:inline-block;padding:.12em .5em;border-radius:4px;font-weight:700;font-size:.7rem;color:#fff;}
.empty-state{text-align:center;padding:3rem 1rem;color:var(--kn-muted);}
.form-footer{background:rgba(107,122,58,.04);border-top:1.5px solid rgba(107,122,58,.1);padding:.65rem 1.5rem;display:flex;justify-content:space-between;font-size:.78rem;color:var(--kn-muted);}
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
    .ml-ptable td.tl{text-align:left;}
}
</style>

<div class="screen-toolbar no-print" style="margin-bottom:1.5rem">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="index.php?action=dataEncoding"
               style="display:inline-flex;align-items:center;gap:.4rem;background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.35rem .85rem;font-size:.88rem;font-weight:600;text-decoration:none">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <div>
                <div class="fw-bold" style="color:var(--kn-dark)">Elderly Citizens Masterlist</div>
                <div style="font-size:.78rem;color:var(--kn-muted)">Quarterly BMI Monitoring Sheet (60+ years)</div>
            </div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <form method="GET" action="index.php" class="d-flex gap-2 align-items-center flex-wrap">
                <input type="hidden" name="action" value="seniorMasterlist">
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
            Masterlist and Monitoring Sheet of <span style="color:var(--kn-orange)">Elderly Citizens (60+ years)</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="q-badge"><?= $qLabel ?></span>
            <span class="year-badge">CY <?= $year ?></span>
            <span style="font-size:.82rem;color:var(--kn-muted)">Total: <strong><?= $total ?></strong></span>
        </div>
    </div>

    <div style="padding:.5rem 1.5rem;background:rgba(30,100,180,.04);border-bottom:1.5px solid rgba(30,100,180,.1);font-size:.8rem">
        <i class="bi bi-info-circle me-1" style="color:#2980b9"></i>
        <strong>BMI Classification (Senior-specific Standards):</strong>
        Underweight &lt;18.5 &nbsp;|&nbsp; At Risk 18.5–22.9 &nbsp;|&nbsp; Normal 23.0–27.9 &nbsp;|&nbsp; Overweight 28.0–31.9 &nbsp;|&nbsp; Obese ≥32.0
    </div>

    <div style="padding:0 1.5rem 1.5rem;overflow-x:auto">
        <?php if (empty($records)): ?>
        <div class="empty-state">
            <i class="bi bi-person-cane" style="font-size:2.5rem;color:rgba(107,122,58,.3);display:block;margin-bottom:.5rem"></i>
            <div class="fw-semibold" style="color:var(--kn-dark)">No elderly citizens assessed for <?= $qLabel ?> <?= $year ?></div>
        </div>
        <?php else: ?>
        <table class="ml-table" style="margin-top:1rem">
            <thead>
                <tr>
                    <th style="width:30px">No.</th>
                    <th style="width:130px">Full Name</th>
                    <th style="width:28px">Sex</th>
                    <th style="width:65px">Birthday</th>
                    <th style="width:38px">Age</th>
                    <th style="width:80px">Purok</th>
                    <th style="width:38px">HT (cm)</th>
                    <th style="width:42px">WT (kg)</th>
                    <th style="width:42px">BMI</th>
                    <th style="width:90px">BMI Status</th>
                    <th style="width:70px">Contact</th>
                    <th style="width:65px">Date Assessed</th>
                    <th style="width:80px">Remarks</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $i => $r): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td class="tl fw-semibold"><?= htmlspecialchars($r['full_name']) ?></td>
                <td><?= $r['sex'] === 'M' ? '♂ M' : '♀ F' ?></td>
                <td><?= $r['dob'] ? date('M j, Y', strtotime($r['dob'])) : '—' ?></td>
                <td><?= (int)$r['age_in_years'] ?> yrs</td>
                <td class="tl"><?= htmlspecialchars($r['purok'] ?? '—') ?></td>
                <td><?= $r['height_cm'] ?></td>
                <td><?= $r['weight_kg'] ?></td>
                <td><?= $r['bmi'] ?? '—' ?></td>
                <td>
                    <?php if ($r['bmi_status']): ?>
                    <span class="ns-chip" style="background:<?= bmiColorS($r['bmi_status']) ?>">
                        <?= $r['bmi_status'] ?>
                    </span>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['contact'] ?? '—') ?></td>
                <td><?= $r['assessment_date'] ? date('M j, Y', strtotime($r['assessment_date'])) : '—' ?></td>
                <td class="tl" style="font-size:.72rem"><?= htmlspecialchars($r['remarks'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <div class="form-footer">
        <div><strong>Reported by:</strong> <?= htmlspecialchars($bnsName) ?></div>
        <div><?= $total ?> record(s) · <?= $qLabel ?> <?= $year ?></div>
    </div>
</div>

<!-- PRINT-ONLY -->
<div class="print-only">
<div class="ml-paper">
    <div style="text-align:center;font-size:8pt;margin-bottom:2px">
        Purok: _____________ &nbsp;&nbsp; Barangay: _____________ &nbsp;&nbsp; City/Municipality: _____________
    </div>
    <div style="text-align:center;font-weight:800;font-size:10pt;margin-bottom:4px">
        MASTERLIST AND MONITORING SHEET OF ELDERLY CITIZENS (60+ YEARS) — <?= strtoupper($qLabel) ?> CY <?= $year ?>
    </div>
    <div style="font-size:6.5pt;margin-bottom:3px">
        BMI Classification (Senior-specific): Underweight &lt;18.5 | At Risk 18.5–22.9 | Normal 23.0–27.9 | Overweight 28.0–31.9 | Obese ≥32.0
    </div>
    <table class="ml-ptable">
        <thead>
            <tr>
                <th style="width:20px">No.</th>
                <th style="width:110px">Full Name</th>
                <th style="width:18px">Sex</th>
                <th style="width:50px">Birthday</th>
                <th style="width:22px">Age</th>
                <th style="width:60px">Purok</th>
                <th style="width:28px">HT (cm)</th>
                <th style="width:30px">WT (kg)</th>
                <th style="width:28px">BMI</th>
                <th style="width:55px">BMI Status</th>
                <th style="width:55px">Contact</th>
                <th style="width:50px">Date Assessed</th>
                <th style="width:70px">Remarks</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($records)): ?>
        <tr><td colspan="13" style="text-align:center;padding:8px">No records.</td></tr>
        <?php else: ?>
        <?php foreach ($records as $i => $r): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td class="tl"><?= htmlspecialchars($r['full_name']) ?></td>
            <td><?= $r['sex'] === 'M' ? 'M' : 'F' ?></td>
            <td><?= $r['dob'] ? date('m/d/y', strtotime($r['dob'])) : '' ?></td>
            <td><?= (int)$r['age_in_years'] ?></td>
            <td class="tl"><?= htmlspecialchars($r['purok'] ?? '') ?></td>
            <td><?= $r['height_cm'] ?></td>
            <td><?= $r['weight_kg'] ?></td>
            <td><?= $r['bmi'] ?? '' ?></td>
            <td><?= $r['bmi_status'] ?? '' ?></td>
            <td><?= htmlspecialchars($r['contact'] ?? '') ?></td>
            <td><?= $r['assessment_date'] ? date('m/d/y', strtotime($r['assessment_date'])) : '' ?></td>
            <td class="tl"><?= htmlspecialchars($r['remarks'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php for ($b = count($records); $b < 15; $b++): ?>
        <tr><td><?= $b+1 ?></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <?php endfor; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <div style="margin-top:10px;display:flex;justify-content:space-between;font-size:7.5pt">
        <div>Reported by: <span style="border-bottom:1px solid #000;min-width:150px;display:inline-block">&nbsp;<?= htmlspecialchars($bnsName) ?>&nbsp;</span></div>
        <div>Certified True by: <span style="border-bottom:1px solid #000;min-width:150px;display:inline-block">&nbsp;</span></div>
        <div>Noted by: <span style="border-bottom:1px solid #000;min-width:120px;display:inline-block">&nbsp;</span></div>
    </div>
</div>
</div>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
