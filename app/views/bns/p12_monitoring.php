<?php
require_once __DIR__ . '/../templates/bns_layout.php';
$bnsName  = $_SESSION['user_name'] ?? 'BNS Staff';
$imgBase  = htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
$total    = count($records);
$curList  = $lists[$tab];
$curTitle = $curList['title'];
$curCols  = $curList['cols'];
$tabLabels = [
    'age_0_23'  => ['short'=>'0-23 Months',    'icon'=>'bi-calendar2-heart'],
    'mam'       => ['short'=>'MAM (Wasted)',    'icon'=>'bi-exclamation-triangle'],
    'sam'       => ['short'=>'SAM (Severe)',    'icon'=>'bi-exclamation-octagon'],
    'ow_ob'     => ['short'=>'Overweight/Obese','icon'=>'bi-arrow-up-circle'],
    'uw_st'     => ['short'=>'UW + Stunted',    'icon'=>'bi-people'],
    'stunted'   => ['short'=>'Stunted',         'icon'=>'bi-arrow-down-circle'],
    'st_wasted' => ['short'=>'Stunted + Wasted','icon'=>'bi-dash-circle'],
    'st_ow'     => ['short'=>'Stunted + OW/Ob', 'icon'=>'bi-plus-slash-minus'],
    'muac'      => ['short'=>'MUAC',            'icon'=>'bi-rulers'],
];
?>
<style>
:root{--kn-green:#6B7A3A;--kn-green-d:#556030;--kn-orange:#C4722A;--kn-dark:#3D4A1E;--kn-muted:rgba(61,74,30,.55);}

/* ── Screen wrapper ── */
.form-wrapper{background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:1rem;overflow:hidden;box-shadow:0 2px 12px rgba(61,74,30,.08);}

/* ── Header band (same as Form C) ── */
.nnc-header-band{background:linear-gradient(135deg,#3D4A1E 0%,#6B7A3A 100%);color:#fff;padding:1rem 1.5rem;display:flex;align-items:center;gap:1rem;}
.nnc-seal-placeholder{width:56px;height:56px;flex-shrink:0;border:2px dashed rgba(255,255,255,.4);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.55rem;color:rgba(255,255,255,.6);text-align:center;line-height:1.3;}
.nnc-agency{flex:1;text-align:center;}
.nnc-agency .l1{font-size:.78rem;opacity:.85;}
.nnc-agency .l2{font-size:.78rem;opacity:.85;}
.nnc-agency .l3{font-size:1.05rem;font-weight:800;letter-spacing:.04em;}
.nnc-agency .l4{font-size:.85rem;opacity:.9;}
.nnc-logo-group{display:flex;gap:.5rem;flex-shrink:0;}
.nnc-logo-pill{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);border-radius:8px;padding:.35rem .6rem;text-align:center;font-size:.62rem;color:rgba(255,255,255,.9);line-height:1.4;}

/* ── Title bar ── */
.form-title-bar{background:rgba(107,122,58,.06);border-bottom:1.5px solid rgba(107,122,58,.12);padding:.75rem 1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;}
.form-title-text{font-weight:700;font-size:.95rem;color:var(--kn-dark);}
.year-badge{background:var(--kn-green);color:#fff;border-radius:6px;padding:.2rem .75rem;font-weight:700;font-size:.88rem;}

/* ── Info bar ── */
.info-bar{padding:.75rem 1.5rem;border-bottom:1.5px solid rgba(107,122,58,.08);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;background:rgba(107,122,58,.02);}
.info-bar .loc-group{display:flex;gap:1.5rem;flex-wrap:wrap;}
.info-bar .loc-item{display:flex;align-items:center;gap:.4rem;font-size:.85rem;}
.info-bar .loc-item label{font-weight:700;color:var(--kn-dark);white-space:nowrap;}
.info-bar .loc-line{border-bottom:1.5px solid rgba(107,122,58,.3);min-width:100px;height:1.2rem;}
.count-pill{background:var(--kn-green);color:#fff;border-radius:20px;padding:.2rem .85rem;font-weight:700;font-size:.88rem;}

/* ── Screen table ── */
.table-section{padding:0 1.5rem 1.5rem;}
.mon-table{width:100%;border-collapse:collapse;font-size:.8rem;margin-top:1rem;}
.mon-table th{background:rgba(107,122,58,.1);color:var(--kn-dark);font-weight:700;font-size:.72rem;text-align:center;padding:.45rem .5rem;border:1px solid rgba(107,122,58,.2);}
.mon-table th.visit-th{background:rgba(107,122,58,.18);}
.mon-table td{padding:.4rem .5rem;border:1px solid rgba(107,122,58,.1);vertical-align:top;text-align:center;font-size:.8rem;}
.mon-table tr:nth-child(even) td{background:rgba(107,122,58,.025);}
.mon-table td.tl{text-align:left;}
.visit-recorded{background:rgba(107,122,58,.06);border-radius:6px;padding:.25rem .4rem;font-size:.72rem;line-height:1.4;text-align:left;}
.visit-recorded .vd{font-weight:600;color:var(--kn-dark);}
.visit-recorded .vi{color:var(--kn-muted);font-size:.68rem;}
.visit-recorded .vs{display:inline-block;background:rgba(107,122,58,.15);color:var(--kn-green);border-radius:4px;padding:.05em .4em;font-weight:700;font-size:.68rem;margin-top:2px;}
.record-btn{background:#fff;color:var(--kn-green);border:1.5px solid rgba(107,122,58,.3);border-radius:6px;padding:.15rem .5rem;font-size:.7rem;font-weight:600;cursor:pointer;transition:.15s;white-space:nowrap;}
.record-btn:hover{background:rgba(107,122,58,.08);}
.empty-state{text-align:center;padding:3rem 1rem;color:var(--kn-muted);}
.empty-state i{font-size:2.5rem;color:rgba(107,122,58,.3);}

/* ── Footer ── */
.form-footer{background:rgba(107,122,58,.04);border-top:1.5px solid rgba(107,122,58,.1);padding:.75rem 1.5rem;display:flex;justify-content:space-between;align-items:center;font-size:.8rem;color:var(--kn-muted);}

/* ── Print: hide screen, show paper ── */
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
    .nnc-paper{font-family:Arial,sans-serif;font-size:8pt;color:#000;padding:5mm 7mm;}
    .nnc-ptable{width:100%;border-collapse:collapse;font-size:7pt;}
    .nnc-ptable th,.nnc-ptable td{border:1px solid #000;padding:2px 3px;vertical-align:top;}
    .nnc-ptable th{background:#d9d9d9!important;text-align:center;font-weight:700;font-size:6.5pt;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    .nnc-ptable th.vth{background:#b8cca0!important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    .nnc-ptable td{text-align:center;}
    .nnc-ptable td.tl{text-align:left;}
    .nnc-paper-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;}
    .nnc-paper-seal{width:55px;height:55px;border:1px solid #999;display:flex;align-items:center;justify-content:center;font-size:5.5pt;color:#888;text-align:center;flex-shrink:0;}
    .nnc-paper-center{text-align:center;flex:1;line-height:1.5;padding:0 8px;}
    .nnc-paper-logos{display:flex;gap:4px;align-items:center;flex-shrink:0;}
    .nnc-paper-logo-box{border:none;padding:0 2px;text-align:center;font-size:5.5pt;}
}
</style>

<!-- ── Screen toolbar ── -->
<div class="screen-toolbar no-print" style="margin-bottom:1rem">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="index.php?action=dataEncoding"
               style="display:inline-flex;align-items:center;gap:.4rem;background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.35rem .85rem;font-size:.88rem;font-weight:600;text-decoration:none">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <div>
                <div class="fw-bold" style="color:var(--kn-dark)">Monitoring Lists</div>
                <div style="font-size:.78rem;color:var(--kn-muted)">NNC Standard Monitoring Lists — Children 0–59 months</div>
            </div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <form method="GET" action="index.php" class="d-flex gap-2 align-items-center">
                <input type="hidden" name="action" value="p12Monitoring">
                <input type="hidden" name="tab"    value="<?= htmlspecialchars($tab) ?>">
                <select name="year" class="form-select form-select-sm" style="width:90px;border:1.5px solid rgba(107,122,58,.25);border-radius:7px">
                    <?php for ($y = date('Y'); $y >= date('Y')-3; $y--): ?>
                    <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="btn btn-sm fw-semibold"
                        style="background:rgba(107,122,58,.1);color:var(--kn-green);border:1.5px solid rgba(107,122,58,.25);border-radius:7px">
                    Filter
                </button>
            </form>
            <button onclick="window.print()"
                    style="display:inline-flex;align-items:center;gap:.4rem;background:var(--kn-green);color:#fff;border:none;border-radius:8px;padding:.4rem 1rem;font-size:.88rem;font-weight:600;cursor:pointer">
                <i class="bi bi-printer-fill"></i> Print / Save as PDF
            </button>
            <a href="index.php?action=p12MonitoringAll&year=<?= $year ?>"
               style="display:inline-flex;align-items:center;gap:.4rem;background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.4rem 1rem;font-size:.88rem;font-weight:600;text-decoration:none">
                <i class="bi bi-files"></i> Print All Lists
            </a>
        </div>
    </div>

    <!-- Tab pills -->
    <div style="display:grid;grid-template-columns:repeat(9,1fr);gap:.4rem">
        <?php foreach ($tabLabels as $key => $lbl): ?>
        <a href="index.php?action=p12Monitoring&tab=<?= $key ?>&year=<?= $year ?>"
           style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.25rem;padding:.4rem .3rem;border-radius:12px;font-size:.75rem;font-weight:600;text-decoration:none;transition:.15s;text-align:center;
                  <?= $tab === $key
                      ? 'background:var(--kn-green);color:#fff;border:1.5px solid var(--kn-green)'
                      : 'background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.2)' ?>">
            <i class="bi <?= $lbl['icon'] ?>" style="font-size:.9rem"></i>
            <span style="line-height:1.2"><?= $lbl['short'] ?></span>
            <?php if (($counts[$key] ?? 0) > 0): ?>
            <span style="background:<?= $tab === $key ? 'rgba(255,255,255,.25)' : 'rgba(107,122,58,.12)' ?>;border-radius:10px;padding:.05em .45em;font-size:.68rem">
                <?= $counts[$key] ?>
            </span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     SCREEN UI
════════════════════════════════════════════════════════════════ -->
<div class="form-wrapper">

    <!-- Header band -->
    <div class="nnc-header-band">
        <div class="nnc-seal-placeholder">SPACE FOR<br>OFFICIAL<br>SEAL OF LGU</div>
        <div class="nnc-agency">
            <div class="l1">Republic of the Philippines</div>
            <div class="l2">Department of Health</div>
            <div class="l3">NATIONAL NUTRITION COUNCIL</div>
            <div class="l4">XI Davao Region</div>
        </div>
        <div class="nnc-logo-group">
            <div class="nnc-logo-pill">
                <img src="<?= $imgBase ?>/public/images/nnc_logo.png" alt="NNC"
                     style="width:38px;height:38px;object-fit:contain;display:block;margin:0 auto 2px"
                     onerror="this.style.display='none'">
                <span style="font-size:.6rem;opacity:.85"></span>
            </div>
            <div class="nnc-logo-pill">
                <img src="<?= $imgBase ?>/public/images/opt_plus_tsek.png" alt="OPT Plus TSEK"
                     style="width:38px;height:38px;object-fit:contain;display:block;margin:0 auto 2px"
                     onerror="this.style.display='none'">
                <span style="font-size:.6rem;opacity:.85"></span>
            </div>
        </div>
    </div>

    <!-- Title bar -->
    <div class="form-title-bar">
        <div class="form-title-text" style="font-size:.88rem">
            <?= htmlspecialchars($curTitle) ?>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span style="font-size:.82rem;color:var(--kn-muted)">Year:</span>
            <span class="year-badge"><?= $year ?></span>
        </div>
    </div>

    <!-- Info bar -->
    <div class="info-bar">
        <div class="loc-group">
            <div class="loc-item">
                <label>Barangay:</label>
                <input type="text" id="barangayField" 
                       style="border:none;border-bottom:1.5px solid rgba(107,122,58,.3);background:transparent;padding:.1rem .3rem;font-size:.85rem;color:var(--kn-dark);min-width:120px;outline:none"
                       placeholder="Enter barangay">
            </div>
            <div class="loc-item">
                <label>Municipality:</label>
                <input type="text" id="municipalityField" 
                       style="border:none;border-bottom:1.5px solid rgba(107,122,58,.3);background:transparent;padding:.1rem .3rem;font-size:.85rem;color:var(--kn-dark);min-width:120px;outline:none"
                       placeholder="Enter municipality">
            </div>
            <div class="loc-item">
                <label>Province:</label>
                <input type="text" id="provinceField" 
                       style="border:none;border-bottom:1.5px solid rgba(107,122,58,.3);background:transparent;padding:.1rem .3rem;font-size:.85rem;color:var(--kn-dark);min-width:100px;outline:none"
                       placeholder="Enter province">
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span style="font-size:.82rem;color:var(--kn-muted)"># of Children:</span>
            <span class="count-pill"><?= $total ?></span>
        </div>
    </div>

<script>
// Auto-save and restore location fields using localStorage
(function() {
    const barangayField = document.getElementById('barangayField');
    const municipalityField = document.getElementById('municipalityField');
    const provinceField = document.getElementById('provinceField');
    
    // Restore saved values on page load
    barangayField.value = localStorage.getItem('p12_barangay') || '';
    municipalityField.value = localStorage.getItem('p12_municipality') || '';
    provinceField.value = localStorage.getItem('p12_province') || '';
    
    // Save values when changed
    barangayField.addEventListener('input', () => {
        localStorage.setItem('p12_barangay', barangayField.value);
    });
    municipalityField.addEventListener('input', () => {
        localStorage.setItem('p12_municipality', municipalityField.value);
    });
    provinceField.addEventListener('input', () => {
        localStorage.setItem('p12_province', provinceField.value);
    });
    
    // Before printing, copy values to print section
    window.addEventListener('beforeprint', () => {
        document.getElementById('printBarangay').textContent = barangayField.value || '\u00A0';
        document.getElementById('printMunicipality').textContent = municipalityField.value || '\u00A0';
        document.getElementById('printProvince').textContent = provinceField.value || '\u00A0';
    });
})();</script>
    <!-- Table -->
    <div class="table-section">
        <?php if (empty($records)): ?>
        <div class="empty-state">
            <i class="bi bi-clipboard2-check d-block mb-2"></i>
            <div class="fw-semibold" style="color:var(--kn-dark)">No children flagged for monitoring in <?= $year ?></div>
            <div style="font-size:.82rem;margin-top:.25rem">Children assessed as underweight or stunted will appear here.</div>
        </div>
        <?php else: ?>
        <table class="mon-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width:32px">Seq.</th>
                    <th rowspan="2" style="width:85px">Address / Purok</th>
                    <th rowspan="2" style="width:110px">Mother / Caregiver</th>
                    <th rowspan="2" style="width:120px">Full Name of Child</th>
                    <th rowspan="2" style="width:28px">Sex</th>
                    <th rowspan="2" style="width:70px">Birthdate</th>
                    <?php if (in_array('height', $curCols) || in_array('wfa', $curCols)): ?>
                    <th rowspan="2" style="width:45px">Height<br>(cm)</th>
                    <th rowspan="2" style="width:45px">Weight<br>(kg)</th>
                    <?php endif; ?>
                    <?php if (in_array('muac', $curCols) && !in_array('wfa', $curCols)): ?>
                    <th rowspan="2" style="width:50px">MUAC<br>(cm)</th>
                    <?php endif; ?>
                    <?php if (in_array('wfa', $curCols)): ?>
                    <th rowspan="2" style="width:50px">WFA<br>Status</th>
                    <th rowspan="2" style="width:50px">L/HFA<br>Status</th>
                    <th rowspan="2" style="width:50px">WFH<br>Status</th>
                    <th rowspan="2" style="width:55px">MUAC<br>Status</th>
                    <?php endif; ?>
                    <th colspan="6" class="visit-th">Follow-up Visits</th>
                </tr>
                <tr>
                    <?php for ($m = 1; $m <= 6; $m++): ?>
                    <th class="visit-th" style="width:90px;white-space:nowrap">Month <?= $m ?></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $i => $r): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td class="tl"><?= htmlspecialchars($r['purok'] ?? '—') ?></td>
                <td class="tl"><?= htmlspecialchars($r['caregiver_name'] ?? '—') ?></td>
                <td class="tl fw-semibold"><?= htmlspecialchars($r['full_name']) ?></td>
                <td><?= $r['sex'] === 'M' ? 'M' : 'F' ?></td>
                <td><?= $r['dob'] ? date('M j, Y', strtotime($r['dob'])) : '—' ?></td>
                <?php if (in_array('height', $curCols) || in_array('wfa', $curCols)): ?>
                <td><?= $r['height_cm'] ?></td>
                <td><?= $r['weight_kg'] ?></td>
                <?php endif; ?>
                <?php if (in_array('muac', $curCols) && !in_array('wfa', $curCols)): ?>
                <td><?= $r['muac_cm'] ?: '—' ?></td>
                <?php endif; ?>
                <?php if (in_array('wfa', $curCols)): ?>
                <td style="font-weight:700;font-size:.75rem"><?= $r['wfa_status'] ?? '—' ?></td>
                <td style="font-weight:700;font-size:.75rem"><?= $r['hfa_status'] ?? '—' ?></td>
                <td style="font-weight:700;font-size:.75rem"><?= $r['wfh_status'] ?? '—' ?></td>
                <td style="font-size:.75rem"><?= $r['muac_cm'] ? $r['muac_cm'].' cm' : '—' ?></td>
                <?php endif; ?>
                <?php for ($m = 1; $m <= 6; $m++):
                    $vDate   = $r["v{$m}_date"]   ?? null;
                    $vInt    = $r["v{$m}_int"]    ?? null;
                    $vStatus = $r["v{$m}_status"] ?? null;
                ?>
                <td>
                    <?php if ($vDate): ?>
                    <div class="visit-recorded">
                        <div class="vd"><?= date('M j, Y', strtotime($vDate)) ?></div>
                        <?php if ($vInt): ?><div class="vi"><?= htmlspecialchars(substr($vInt,0,45)) ?></div><?php endif; ?>
                        <?php if ($vStatus): ?><span class="vs"><?= htmlspecialchars($vStatus) ?></span><?php endif; ?>
                    </div>
                    <?php else: ?>
                    <button type="button" class="record-btn no-print"
                            onclick="openFollowUp(<?= (int)$r['assessment_id'] ?>, <?= $m ?>)">
                        <i class="bi bi-plus-circle me-1"></i>Record
                    </button>
                    <?php endif; ?>
                </td>
                <?php endfor; ?>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="form-footer">
        <div><strong>Prepared by:</strong> <?= htmlspecialchars($bnsName) ?> &nbsp;&nbsp; <strong>Date:</strong> <?= date('F j, Y') ?></div>
        <div><?= $total ?> record(s) · <?= $year ?></div>
    </div>

</div><!-- /form-wrapper -->

<!-- ══════════════════════════════════════════════════════════════
     PRINT-ONLY: NNC Standard Paper Format
════════════════════════════════════════════════════════════════ -->
<div class="print-only">
<div class="nnc-paper">

    <div class="nnc-paper-header">
        <!-- Left: LGU Seal + NNC Logo -->
        <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;">
            <div class="nnc-paper-seal">SPACE FOR<br>OFFICIAL<br>SEAL OF LGU</div>
            <img src="<?= $imgBase ?>/public/images/nnc_logo.png"
                 style="width:58px;height:58px;object-fit:contain;display:block" alt="NNC">
        </div>

        <!-- Center: Agency text -->
        <div class="nnc-paper-center">
            <div style="font-size:7.5pt">Republic of the Philippines</div>
            <div style="font-size:7.5pt">Department of Health</div>
            <div style="font-weight:800;font-size:11pt;letter-spacing:.02em">NATIONAL NUTRITION COUNCIL</div>
            <div style="font-size:9pt;font-weight:600">XI Davao Region</div>
        </div>

        <!-- Right: OPT Plus TSEK logo only -->
        <div style="flex-shrink:0;text-align:center;">
            <img src="<?= $imgBase ?>/public/images/opt_plus_tsek.png"
                 style="width:58px;height:58px;object-fit:contain;display:block;margin:0 auto" alt="OPT Plus TSEK">
            <div style="font-size:5.5pt;font-weight:700;line-height:1.3">OPT Plus TSEK<br><span style="font-weight:400">Operasyon Timbang Plus</span></div>
        </div>
    </div>

    <div style="font-weight:800;font-size:9pt;margin-bottom:2px;text-align:center">
        <?= strtoupper(htmlspecialchars($curTitle)) ?>
    </div>

    <div style="display:flex;gap:16px;font-size:7.5pt;margin-bottom:3px;flex-wrap:wrap">
        <span><strong>Barangay:</strong> <span id="printBarangay" style="border-bottom:1px solid #000;min-width:100px;display:inline-block">&nbsp;</span></span>
        <span><strong>Municipality:</strong> <span id="printMunicipality" style="border-bottom:1px solid #000;min-width:100px;display:inline-block">&nbsp;</span></span>
        <span><strong>Province:</strong> <span id="printProvince" style="border-bottom:1px solid #000;min-width:80px;display:inline-block">&nbsp;</span></span>
        <span><strong>Year:</strong> <span style="border-bottom:1px solid #000;min-width:50px;display:inline-block">&nbsp;<?= $year ?>&nbsp;</span></span>
    </div>

    <div style="font-size:7.5pt;margin-bottom:4px">
        <strong># of Children:</strong> <span style="border:1px solid #000;padding:0 6px;font-weight:800"><?= $total ?></span>
        &nbsp;&nbsp;<em style="font-size:6.5pt">Note: This list can be copied to create other customized monitoring worksheets.</em>
    </div>

    <table class="nnc-ptable">
        <thead>
            <tr>
                <th rowspan="2" style="width:24px">Child<br>Seq.</th>
                <th rowspan="2" style="width:70px">Address<br><span style="font-weight:400;font-size:5.5pt">Purok or Location in the Barangay</span></th>
                <th rowspan="2" style="width:90px">Name of Mother<br>or Caregiver</th>
                <th rowspan="2" style="width:100px">Full Name of Child</th>
                <th rowspan="2" style="width:18px">Sex</th>
                <th rowspan="2" style="width:44px">Birthdate</th>
                <th rowspan="2" style="width:32px">Length/<br>Height<br>(cm)</th>
                <th rowspan="2" style="width:32px">Weight<br>(kg)</th>
                <th colspan="6" class="vth">Follow-up Visits</th>
            </tr>
            <tr>
                <?php for ($m = 1; $m <= 6; $m++): ?>
                <th class="vth" style="width:60px">
                    Month #<?= $m ?><br>
                    <span style="font-weight:400;font-size:5.5pt">(1) Date<br>(2) Intervention<br>(3) Nutr. Status</span>
                </th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($records)): ?>
        <tr><td colspan="14" style="text-align:center;padding:8px">No children flagged for monitoring in <?= $year ?>.</td></tr>
        <?php else: ?>
        <?php foreach ($records as $i => $r): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td class="tl"><?= htmlspecialchars($r['purok'] ?? '') ?></td>
            <td class="tl"><?= htmlspecialchars($r['caregiver_name'] ?? '') ?></td>
            <td class="tl"><strong><?= htmlspecialchars($r['full_name']) ?></strong></td>
            <td><?= $r['sex'] === 'M' ? 'M' : 'F' ?></td>
            <td><?= $r['dob'] ? date('m/d/Y', strtotime($r['dob'])) : '' ?></td>
            <td><?= $r['height_cm'] ?></td>
            <td><?= $r['weight_kg'] ?></td>
            <?php for ($m = 1; $m <= 6; $m++):
                $vDate   = $r["v{$m}_date"]   ?? null;
                $vInt    = $r["v{$m}_int"]    ?? null;
                $vStatus = $r["v{$m}_status"] ?? null;
            ?>
            <td style="vertical-align:top;min-height:28px">
                <?php if ($vDate): ?>
                <div style="font-size:6.5pt;line-height:1.4">
                    <div><?= date('m/d/Y', strtotime($vDate)) ?></div>
                    <?php if ($vInt): ?><div><?= htmlspecialchars(substr($vInt,0,40)) ?></div><?php endif; ?>
                    <?php if ($vStatus): ?><div><strong><?= htmlspecialchars($vStatus) ?></strong></div><?php endif; ?>
                </div>
                <?php endif; ?>
            </td>
            <?php endfor; ?>
        </tr>
        <?php endforeach; ?>
        <?php for ($b = count($records); $b < 20; $b++): ?>
        <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <?php endfor; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:6px;font-size:7.5pt">
        <strong>Prepared by:</strong> <?= htmlspecialchars($bnsName) ?>
        &nbsp;&nbsp;&nbsp;
        <strong>Date Prepared:</strong> <?= date('F j, Y') ?>
    </div>

</div><!-- /nnc-paper -->
</div><!-- /print-only -->

<!-- Follow-up Modal -->
<div class="modal fade no-print" id="followUpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:rgba(107,122,58,.06);border-bottom:1.5px solid rgba(107,122,58,.15)">
                <h6 class="modal-title fw-bold" style="color:var(--kn-dark)">
                    <i class="bi bi-clipboard2-plus me-2" style="color:var(--kn-green)"></i>Record Follow-up Visit
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=saveFollowUp">
                <div class="modal-body">
                    <input type="hidden" name="assessment_id"      id="fu_assessment_id">
                    <input type="hidden" name="visit_month_number" id="fu_month">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.88rem">Visit Date</label>
                        <input type="date" name="visit_date" class="form-control" required max="<?= date('Y-m-d') ?>"
                               style="border:1.5px solid rgba(107,122,58,.25);border-radius:8px">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.88rem">Intervention Done</label>
                        <textarea name="intervention_done" class="form-control" rows="2"
                                  style="border:1.5px solid rgba(107,122,58,.25);border-radius:8px"
                                  placeholder="e.g. Micronutrient supplementation, dietary counseling…"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.88rem">Nutritional Status at Visit</label>
                        <select name="nutritional_status" class="form-select"
                                style="border:1.5px solid rgba(107,122,58,.25);border-radius:8px">
                            <option value="">— Select —</option>
                            <option>SUW</option><option>UW</option><option>Normal</option>
                            <option>SSt</option><option>St</option>
                            <option>SAM</option><option>MAM</option>
                            <option>OW</option><option>Ob</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1.5px solid rgba(107,122,58,.12)">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn fw-semibold"
                            style="background:var(--kn-green);color:#fff;border:none;border-radius:8px">
                        <i class="bi bi-save me-1"></i> Save Visit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openFollowUp(assessmentId, month) {
    document.getElementById('fu_assessment_id').value = assessmentId;
    document.getElementById('fu_month').value = month;
    new bootstrap.Modal(document.getElementById('followUpModal')).show();
}

</script>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
