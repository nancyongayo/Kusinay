<?php
require_once __DIR__ . '/../templates/bns_layout.php';
require_once __DIR__ . '/../../../core/NutritionCalculator.php';
$bnsName = $_SESSION['user_name'] ?? 'BNS Staff';
$imgBase = htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
$total   = count($results);

// Status color mapping — matches e-OPT Plus color scheme exactly
function statusBg(string $s): string {
    return match($s) {
        'SUW','SSt','SAM' => '#f39c12', // yellow/amber — severely malnourished
        'UW','St','MAM'   => '#f1c40f', // bright yellow — moderately malnourished
        'Normal','Tall'   => '#27ae60', // green — normal
        'OW','Ob'         => '#e74c3c', // red — overweight/obese
        default           => '#bdc3c7',
    };
}
function statusFg(string $s): string {
    return match($s) {
        'UW','St','MAM' => '#000', // dark text on bright yellow
        default         => '#fff',
    };
}
?>
<style>
:root{--kn-green:#6B7A3A;--kn-green-d:#556030;--kn-orange:#C4722A;--kn-dark:#3D4A1E;--kn-muted:rgba(61,74,30,.55);}

/* Screen wrapper */
.form-wrapper{background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:1rem;overflow:hidden;box-shadow:0 2px 12px rgba(61,74,30,.08);}
.nnc-header-band{background:linear-gradient(135deg,#3D4A1E 0%,#6B7A3A 100%);color:#fff;padding:.85rem 1.5rem;display:flex;align-items:center;gap:1rem;}
.nnc-seal-placeholder{width:48px;height:48px;flex-shrink:0;border:2px dashed rgba(255,255,255,.4);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.5rem;color:rgba(255,255,255,.6);text-align:center;line-height:1.3;}
.nnc-agency{flex:1;text-align:center;}
.nnc-agency .l1{font-size:.72rem;opacity:.85;}
.nnc-agency .l3{font-size:.95rem;font-weight:800;letter-spacing:.04em;}
.nnc-agency .l4{font-size:.8rem;opacity:.9;}
.nnc-logo-group{display:flex;gap:.4rem;flex-shrink:0;}
.nnc-logo-pill{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);border-radius:7px;padding:.3rem .5rem;text-align:center;font-size:.58rem;color:rgba(255,255,255,.9);line-height:1.4;}

/* Title bar */
.form-title-bar{background:rgba(107,122,58,.06);border-bottom:1.5px solid rgba(107,122,58,.12);padding:.65rem 1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;}
.year-badge{background:var(--kn-green);color:#fff;border-radius:6px;padding:.15rem .65rem;font-weight:700;font-size:.85rem;}

/* OPT table */
.opt-table{width:100%;border-collapse:collapse;font-size:.78rem;}
.opt-table th{background:#2c3e50;color:#fff;font-weight:700;font-size:.7rem;text-align:center;padding:.45rem .4rem;border:1px solid #1a252f;}
.opt-table th.grp-wfa{background:#1a5c2a;}
.opt-table th.grp-hfa{background:#1a5c4a;}
.opt-table th.grp-wfh{background:#5c3a1a;}
.opt-table td{padding:.38rem .4rem;border:1px solid rgba(107,122,58,.12);vertical-align:middle;text-align:center;font-size:.78rem;}
.opt-table tr:nth-child(even) td{background:rgba(107,122,58,.03);}
.opt-table td.tl{text-align:left;}
.status-chip{display:inline-block;padding:.18em .6em;border-radius:5px;font-weight:800;font-size:.72rem;color:#fff;min-width:38px;text-align:center;}
.legend-dot{display:inline-block;width:12px;height:12px;border-radius:3px;margin-right:4px;vertical-align:middle;}

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
    .opt-paper{font-family:Arial,sans-serif;font-size:7.5pt;color:#000;padding:5mm 6mm;}
    .opt-ptable{width:100%;border-collapse:collapse;font-size:7pt;}
    .opt-ptable th,.opt-ptable td{border:1px solid #000;padding:2px 3px;vertical-align:middle;text-align:center;}
    .opt-ptable th{background:#2c3e50!important;color:#fff!important;font-weight:700;font-size:6.5pt;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    .opt-ptable th.grp-wfa{background:#1a5c2a!important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    .opt-ptable th.grp-hfa{background:#1a5c4a!important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    .opt-ptable th.grp-wfh{background:#5c3a1a!important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    .opt-ptable td.tl{text-align:left;}
    .ps-chip{display:inline-block;padding:1px 4px;border-radius:3px;font-weight:800;font-size:6.5pt;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    .ps-red{background:#f39c12!important;color:#fff!important;}
    .ps-orange{background:#f1c40f!important;color:#000!important;}
    .ps-green{background:#27ae60!important;color:#fff!important;}
    .ps-blue{background:#e74c3c!important;color:#fff!important;}
    .ps-grey{background:#bdc3c7!important;color:#000!important;}
    .nnc-paper-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;}
    .nnc-paper-seal{width:50px;height:50px;border:1px solid #999;display:flex;align-items:center;justify-content:center;font-size:5.5pt;color:#888;text-align:center;flex-shrink:0;}
    .nnc-paper-center{text-align:center;flex:1;padding:0 8px;line-height:1.5;}
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
                <div class="fw-bold" style="color:var(--kn-dark)">OPT Plus Results</div>
                <div style="font-size:.78rem;color:var(--kn-muted)">Most recent assessment per child — Weight for Age, Height for Age, Weight for Height</div>
            </div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <form method="GET" action="index.php" class="d-flex gap-2 align-items-center">
                <input type="hidden" name="action" value="optResults">
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
        </div>
    </div>
</div>

<!-- Screen UI -->
<div class="form-wrapper">

    <!-- Header band -->
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
                <span>NNC · 1974</span>
            </div>
            <div class="nnc-logo-pill">
                <img src="<?= $imgBase ?>/public/images/opt_plus_tsek.png" alt="OPT Plus TSEK"
                     style="width:34px;height:34px;object-fit:contain;display:block;margin:0 auto 2px"
                     onerror="this.style.display='none'">
                <span>OPT Plus TSEK</span>
            </div>
        </div>
    </div>

    <!-- Title bar -->
    <div class="form-title-bar">
        <div style="font-weight:700;font-size:.92rem;color:var(--kn-dark)">
            Community Level e-OPT Plus Tool — Weight for Age, Height for Age, &amp; Weight for Length/Height Status
        </div>
        <div class="d-flex align-items-center gap-2">
            <span style="font-size:.82rem;color:var(--kn-muted)">Year:</span>
            <span class="year-badge"><?= $year ?></span>
            <span style="font-size:.82rem;color:var(--kn-muted);margin-left:.5rem"># of Children:</span>
            <span style="background:var(--kn-green);color:#fff;border-radius:6px;padding:.15rem .65rem;font-weight:700;font-size:.85rem"><?= $total ?></span>
        </div>
    </div>

    <!-- Legend -->
    <div style="padding:.6rem 1.5rem;border-bottom:1.5px solid rgba(107,122,58,.08);display:flex;gap:1rem;flex-wrap:wrap;font-size:.78rem;align-items:center">
        <span style="font-weight:600;color:var(--kn-muted)">Status:</span>
        <span><span class="legend-dot" style="background:#f39c12"></span>Severely Malnourished (SUW/SSt/SAM)</span>
        <span><span class="legend-dot" style="background:#f1c40f;border:1px solid #ccc"></span>Moderately Malnourished (UW/St/MAM)</span>
        <span><span class="legend-dot" style="background:#27ae60"></span>Normal</span>
        <span><span class="legend-dot" style="background:#e74c3c"></span>Overweight/Obese</span>
    </div>

    <!-- Table -->
    <div style="padding:0 1.5rem 1.5rem;overflow-x:auto">
        <?php if (empty($results)): ?>
        <div style="text-align:center;padding:3rem 1rem;color:var(--kn-muted)">
            <i class="bi bi-clipboard2-data" style="font-size:2.5rem;color:rgba(107,122,58,.3);display:block;margin-bottom:.5rem"></i>
            <div class="fw-semibold" style="color:var(--kn-dark)">No assessments recorded for <?= $year ?></div>
            <div style="font-size:.82rem;margin-top:.25rem">Go to Resident Assessment → Children tab → click Assess to start recording.</div>
        </div>
        <?php else: ?>
        <table class="opt-table" style="margin-top:1rem">
            <thead>
                <tr>
                    <th rowspan="2" style="width:30px">Seq.</th>
                    <th rowspan="2" style="width:75px">Purok/<br>Address</th>
                    <th rowspan="2" style="width:110px">Name of Mother<br>or Caregiver</th>
                    <th rowspan="2" style="width:120px">Full Name of Child</th>
                    <th rowspan="2" style="width:28px">Sex</th>
                    <th rowspan="2" style="width:70px">Date of Birth</th>
                    <th rowspan="2" style="width:70px">Date Measured</th>
                    <th rowspan="2" style="width:42px">Weight<br>(kg)</th>
                    <th rowspan="2" style="width:42px">Height<br>(cm)</th>
                    <th rowspan="2" style="width:38px">Age<br>(mos)</th>
                    <th class="grp-wfa" style="width:55px">Weight<br>for Age</th>
                    <th class="grp-hfa" style="width:55px">Height<br>for Age</th>
                    <th class="grp-wfh" style="width:55px">Weight for<br>Length/Height</th>
                </tr>
                <tr>
                    <th class="grp-wfa" style="font-size:.65rem;font-weight:400">WFA Status</th>
                    <th class="grp-hfa" style="font-size:.65rem;font-weight:400">HFA Status</th>
                    <th class="grp-wfh" style="font-size:.65rem;font-weight:400">WFH Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($results as $i => $r):
                $wfa = $r['wfa_status'] ?? null;
                $hfa = $r['hfa_status'] ?? null;
                $wfh = $r['wfh_status'] ?? null;
            ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td class="tl"><?= htmlspecialchars($r['purok'] ?? '—') ?></td>
                <td class="tl"><?= htmlspecialchars($r['caregiver_name'] ?? '—') ?></td>
                <td class="tl fw-semibold"><?= htmlspecialchars($r['full_name']) ?></td>
                <td><?= $r['sex'] === 'M' ? 'M' : 'F' ?></td>
                <td><?= $r['dob'] ? date('M-d-Y', strtotime($r['dob'])) : '—' ?></td>
                <td><?= $r['assessment_date'] ? date('M-d-Y', strtotime($r['assessment_date'])) : '—' ?></td>
                <td><?= $r['weight_kg'] ?></td>
                <td><?= $r['height_cm'] ?></td>
                <td><?= (int)$r['age_in_months'] ?></td>
                <td>
                    <?php if ($wfa): ?>
                    <span class="status-chip" style="background:<?= statusBg($wfa) ?>;color:<?= statusFg($wfa) ?>">
                        <?= $wfa ?>
                    </span>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td>
                    <?php if ($hfa): ?>
                    <span class="status-chip" style="background:<?= statusBg($hfa) ?>;color:<?= statusFg($hfa) ?>">
                        <?= $hfa ?>
                    </span>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td>
                    <?php if ($wfh): ?>
                    <span class="status-chip" style="background:<?= statusBg($wfh) ?>;color:<?= statusFg($wfh) ?>">
                        <?= $wfh ?>
                    </span>
                    <?php else: ?>—<?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div style="background:rgba(107,122,58,.04);border-top:1.5px solid rgba(107,122,58,.1);padding:.65rem 1.5rem;display:flex;justify-content:space-between;font-size:.78rem;color:var(--kn-muted)">
        <div><strong>Prepared by:</strong> <?= htmlspecialchars($bnsName) ?> &nbsp;&nbsp; <strong>Date:</strong> <?= date('F j, Y') ?></div>
        <div><?= $total ?> child(ren) assessed · <?= $year ?></div>
    </div>

</div><!-- /form-wrapper -->

<!-- PRINT-ONLY: e-OPT Plus format (landscape) -->
<div class="print-only">
<div class="opt-paper">

    <div class="nnc-paper-header">
        <!-- Left: LGU Seal only -->
        <div style="flex-shrink:0;">
            <div class="nnc-paper-seal">SPACE FOR<br>OFFICIAL<br>SEAL OF LGU</div>
        </div>
        <!-- Center: Agency text + Tool title -->
        <div class="nnc-paper-center">
            <div style="font-size:7pt">Republic of the Philippines · Department of Health</div>
            <div style="font-weight:800;font-size:10pt">NATIONAL NUTRITION COUNCIL</div>
            <div style="font-size:8pt">XI Davao Region</div>
            <div style="font-weight:800;font-size:9pt;margin-top:2px">COMMUNITY LEVEL e-OPT PLUS TOOL</div>
            <div style="font-size:7.5pt">WEIGHT FOR AGE, HEIGHT FOR AGE, &amp; WEIGHT FOR LENGTH/HEIGHT STATUS</div>
        </div>
        <!-- Right: NNC logo + OPT Plus TSEK -->
        <div style="display:flex;gap:3px;align-items:center;flex-shrink:0;">
            <img src="<?= $imgBase ?>/public/images/nnc_logo.png"
                 style="width:48px;height:48px;object-fit:contain;display:block" alt="NNC">
            <div style="text-align:center">
                <img src="<?= $imgBase ?>/public/images/opt_plus_tsek.png"
                     style="width:48px;height:48px;object-fit:contain;display:block;margin:0 auto" alt="OPT Plus TSEK">
                <div style="font-size:5.5pt;font-weight:700;line-height:1.2">OPT Plus TSEK<br><span style="font-weight:400">Operasyon Timbang Plus</span></div>
            </div>
        </div>
    </div>

    <!-- Location fields row -->
    <div style="display:flex;gap:12px;font-size:7.5pt;margin-bottom:2px;border-bottom:1px solid #ccc;padding-bottom:2px">
        <span><strong>Purok/Area/Block:</strong> <span style="border-bottom:1px solid #000;min-width:80px;display:inline-block">&nbsp;</span></span>
        <span><strong>Barangay:</strong> <span style="border-bottom:1px solid #000;min-width:80px;display:inline-block">&nbsp;</span></span>
        <span><strong>City:</strong> <span style="border-bottom:1px solid #000;min-width:80px;display:inline-block">&nbsp;</span></span>
        <span><strong>Province:</strong> <span style="border-bottom:1px solid #000;min-width:70px;display:inline-block">&nbsp;</span></span>
    </div>

    <div style="display:flex;gap:16px;font-size:7.5pt;margin-bottom:3px">
        <span><strong>Year:</strong> <?= $year ?></span>
        <span><strong>BNS:</strong> <?= htmlspecialchars($bnsName) ?></span>
        <span><strong># of Children:</strong> <?= $total ?></span>
    </div>

    <table class="opt-ptable">
        <thead>
            <tr>
                <th rowspan="2" style="width:22px">Seq.</th>
                <th rowspan="2" style="width:55px">Address/<br>Purok</th>
                <th rowspan="2" style="width:80px">Name of Mother<br>or Caregiver</th>
                <th rowspan="2" style="width:90px">Full Name of Child</th>
                <th rowspan="2" style="width:18px">Sex</th>
                <th rowspan="2" style="width:48px">Date of Birth</th>
                <th rowspan="2" style="width:48px">Date Measured</th>
                <th rowspan="2" style="width:30px">Weight<br>(kg)</th>
                <th rowspan="2" style="width:30px">Height<br>(cm)</th>
                <th rowspan="2" style="width:25px">Age<br>(mos)</th>
                <th class="grp-wfa" style="width:42px">Weight<br>for Age<br>Status</th>
                <th class="grp-hfa" style="width:42px">Height<br>for Age<br>Status</th>
                <th class="grp-wfh" style="width:42px">Weight for<br>Length/Height<br>Status</th>
            </tr>
            <tr>
                <th class="grp-wfa" style="font-size:5.5pt;font-weight:400">WFA</th>
                <th class="grp-hfa" style="font-size:5.5pt;font-weight:400">HFA</th>
                <th class="grp-wfh" style="font-size:5.5pt;font-weight:400">WFH</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($results)): ?>
        <tr><td colspan="13" style="text-align:center;padding:8px">No assessments recorded for <?= $year ?>.</td></tr>
        <?php else: ?>
        <?php foreach ($results as $i => $r):
            $wfa = $r['wfa_status'] ?? null;
            $hfa = $r['hfa_status'] ?? null;
            $wfh = $r['wfh_status'] ?? null;
            $wfaBg = match($wfa) { 'SUW','SSt','SAM'=>'ps-red', 'UW','St','MAM'=>'ps-orange', 'Normal','Tall'=>'ps-green', 'OW','Ob'=>'ps-blue', default=>'ps-grey' };
            $hfaBg = match($hfa) { 'SUW','SSt','SAM'=>'ps-red', 'UW','St','MAM'=>'ps-orange', 'Normal','Tall'=>'ps-green', 'OW','Ob'=>'ps-blue', default=>'ps-grey' };
            $wfhBg = match($wfh) { 'SUW','SSt','SAM'=>'ps-red', 'UW','St','MAM'=>'ps-orange', 'Normal','Tall'=>'ps-green', 'OW','Ob'=>'ps-blue', default=>'ps-grey' };
        ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td class="tl"><?= htmlspecialchars($r['purok'] ?? '') ?></td>
            <td class="tl"><?= htmlspecialchars($r['caregiver_name'] ?? '') ?></td>
            <td class="tl"><strong><?= htmlspecialchars($r['full_name']) ?></strong></td>
            <td><?= $r['sex'] === 'M' ? 'M' : 'F' ?></td>
            <td><?= $r['dob'] ? date('m/d/Y', strtotime($r['dob'])) : '' ?></td>
            <td><?= $r['assessment_date'] ? date('m/d/Y', strtotime($r['assessment_date'])) : '' ?></td>
            <td><?= $r['weight_kg'] ?></td>
            <td><?= $r['height_cm'] ?></td>
            <td><?= (int)$r['age_in_months'] ?></td>
            <td><?php if ($wfa): ?><span class="ps-chip <?= $wfaBg ?>"><?= $wfa ?></span><?php endif; ?></td>
            <td><?php if ($hfa): ?><span class="ps-chip <?= $hfaBg ?>"><?= $hfa ?></span><?php endif; ?></td>
            <td><?php if ($wfh): ?><span class="ps-chip <?= $wfhBg ?>"><?= $wfh ?></span><?php endif; ?></td>
        </tr>
        <?php endforeach; ?>
        <!-- Blank rows -->
        <?php for ($b = count($results); $b < 20; $b++): ?>
        <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <?php endfor; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:6px;font-size:7.5pt">
        <strong>Prepared by:</strong> <?= htmlspecialchars($bnsName) ?>
        &nbsp;&nbsp;&nbsp;
        <strong>Date:</strong> <?= date('F j, Y') ?>
    </div>

</div>
</div><!-- /print-only -->

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
