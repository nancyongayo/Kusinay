<?php
require_once __DIR__ . '/../templates/bns_layout.php';
require_once __DIR__ . '/../../../core/NutritionCalculator.php';
$bnsName  = $_SESSION['user_name'] ?? 'BNS Staff';
$s        = $summary ?? [];
$undernut = ((int)($s['MUW']??0)) + ((int)($s['SUW']??0)) + ((int)($s['MSt']??0)) + ((int)($s['SSt']??0)) + ((int)($s['MAM']??0)) + ((int)($s['SAM']??0));
$overnut  = ((int)($s['OW']??0)) + ((int)($s['Ob']??0));
$total    = (int)($s['total']??0);
// Location resolved server-side by controller
$locBarangay    = htmlspecialchars($locationBarangay    ?? '');
$locMunicipality = htmlspecialchars($locationMunicipality ?? '');
$locProvince    = htmlspecialchars($locationProvince    ?? '');
?>
<style>
:root{--kn-green:#6B7A3A;--kn-green-d:#556030;--kn-orange:#C4722A;--kn-cream:#F5EDD6;--kn-dark:#3D4A1E;--kn-muted:rgba(61,74,30,.55);}

/* ── Screen-only toolbar & wrapper ── */
.screen-toolbar { margin-bottom: 1.5rem; }
.form-wrapper {
    background: #fff;
    border: 1.5px solid rgba(107,122,58,.15);
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(61,74,30,.08);
}

/* ── Official header band ── */
.nnc-header-band {
    background: linear-gradient(135deg, #3D4A1E 0%, #6B7A3A 100%);
    color: #fff;
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.nnc-seal-placeholder {
    width: 56px; height: 56px; flex-shrink: 0;
    border: 2px dashed rgba(255,255,255,.4);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .55rem; color: rgba(255,255,255,.6); text-align: center;
    line-height: 1.3;
}
.nnc-agency { flex: 1; text-align: center; }
.nnc-agency .line1 { font-size: .78rem; opacity: .85; }
.nnc-agency .line2 { font-size: .78rem; opacity: .85; }
.nnc-agency .line3 { font-size: 1.05rem; font-weight: 800; letter-spacing: .04em; }
.nnc-agency .line4 { font-size: .85rem; opacity: .9; }
.nnc-logo-group { display: flex; gap: .5rem; flex-shrink: 0; }
.nnc-logo-pill {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 8px;
    padding: .35rem .6rem;
    text-align: center;
    font-size: .62rem;
    color: rgba(255,255,255,.9);
    line-height: 1.4;
}
.nnc-logo-pill .logo-icon { font-size: 1.1rem; display: block; }

/* ── Form title bar ── */
.form-title-bar {
    background: rgba(107,122,58,.06);
    border-bottom: 1.5px solid rgba(107,122,58,.12);
    padding: .75rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}
.form-title-text { font-weight: 700; font-size: .95rem; color: var(--kn-dark); }
.year-badge {
    background: var(--kn-green);
    color: #fff;
    border-radius: 6px;
    padding: .2rem .75rem;
    font-weight: 700;
    font-size: .88rem;
}

/* ── Summary section ── */
.summary-section {
    padding: 1rem 1.5rem;
    border-bottom: 1.5px solid rgba(107,122,58,.1);
    display: flex;
    gap: 1.5rem;
    align-items: flex-start;
    flex-wrap: wrap;
}
.summary-location { flex: 0 0 200px; }
.summary-location .loc-row { display: flex; align-items: center; gap: .4rem; margin-bottom: .35rem; font-size: .85rem; }
.summary-location .loc-row label { font-weight: 700; color: var(--kn-dark); white-space: nowrap; min-width: 80px; }
.summary-location .loc-line { flex: 1; border-bottom: 1.5px solid rgba(107,122,58,.3); min-width: 100px; height: 1.2rem; }
.summary-counts { flex: 1; min-width: 260px; }
.counts-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: .5rem; }
.count-card {
    border: 1.5px solid rgba(107,122,58,.15);
    border-radius: 8px;
    padding: .5rem .75rem;
    text-align: center;
    background: #fff;
}
.count-card.severe {
    border-color: rgba(180,30,30,.25);
    background: rgba(180,30,30,.04);
}
.count-card.moderate {
    border-color: rgba(196,114,42,.25);
    background: rgba(196,114,42,.04);
}
.count-card.overweight {
    border-color: rgba(30,100,180,.2);
    background: rgba(30,100,180,.04);
}
.count-card .cv { font-size: 1.4rem; font-weight: 800; line-height: 1; }
.count-card .cl { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; margin-top: .15rem; }
.count-card.severe .cv   { color: #b41e1e; }
.count-card.moderate .cv { color: var(--kn-orange); }
.count-card.overweight .cv { color: #1e64b4; }
.count-card .cv.normal   { color: var(--kn-green); }
.summary-totals { flex: 0 0 200px; }
.total-box {
    border: 1.5px solid rgba(107,122,58,.2);
    border-radius: 8px;
    padding: .75rem 1rem;
    background: rgba(107,122,58,.04);
}
.total-box .tb-row { display: flex; justify-content: space-between; align-items: center; font-size: .82rem; margin-bottom: .3rem; }
.total-box .tb-row:last-child { margin-bottom: 0; }
.total-box .tb-label { color: var(--kn-muted); }
.total-box .tb-val { font-weight: 700; color: var(--kn-dark); }
.total-box .tb-val.danger { color: #b41e1e; }
.total-box .tb-val.info   { color: #1e64b4; }
.grand-total {
    margin-top: .5rem;
    background: var(--kn-green);
    color: #fff;
    border-radius: 8px;
    padding: .5rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 700;
}

/* ── Data table ── */
.table-section { padding: 0 1.5rem 1.5rem; }
.nnc-data-table { width: 100%; border-collapse: collapse; font-size: .82rem; margin-top: 1rem; }
.nnc-data-table th {
    background: rgba(107,122,58,.1);
    color: var(--kn-dark);
    font-weight: 700;
    font-size: .75rem;
    text-align: center;
    padding: .5rem .6rem;
    border: 1px solid rgba(107,122,58,.2);
}
.nnc-data-table th.nutri-header { background: rgba(107,122,58,.18); }
.nnc-data-table td {
    padding: .45rem .6rem;
    border: 1px solid rgba(107,122,58,.1);
    vertical-align: middle;
    text-align: center;
    font-size: .82rem;
}
.nnc-data-table tr:nth-child(even) td { background: rgba(107,122,58,.025); }
.nnc-data-table td.text-left { text-align: left; }
.status-badge {
    display: inline-block;
    padding: .15em .55em;
    border-radius: 5px;
    font-weight: 700;
    font-size: .72rem;
}
.sb-suw, .sb-ssam { background: rgba(180,30,30,.1); color: #b41e1e; }
.sb-uw,  .sb-mam  { background: rgba(196,114,42,.12); color: var(--kn-orange); }
.sb-normal        { background: rgba(107,122,58,.12); color: var(--kn-green); }
.sb-ow, .sb-ob    { background: rgba(30,100,180,.1); color: #1e64b4; }
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--kn-muted);
}
.empty-state i { font-size: 2.5rem; color: rgba(107,122,58,.3); }

/* ── Footer ── */
.form-footer {
    background: rgba(107,122,58,.04);
    border-top: 1.5px solid rgba(107,122,58,.1);
    padding: .75rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: .8rem;
    color: var(--kn-muted);
}

/* Hide print layout on screen */
.print-only,
body .print-only,
html body .print-only { display: none !important; }

@page{size:A4 landscape;margin:8mm 10mm;}

/* Show print layout only when printing */
@media print {
    *{-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;}
    @page{size:A4 landscape;margin:8mm 10mm;}
    .no-print, .kn-sidebar, .kn-topbar, .kn-flash,
    .screen-toolbar, .form-wrapper { display: none !important; }
    .kn-main { margin: 0 !important; padding: 0 !important; width: 100% !important; }
    .kn-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
    body { font-size: 9pt; background: #fff !important; margin: 0 !important; padding: 0 !important; }

    .print-only,
    body .print-only,
    html body .print-only { display: block !important; position: fixed; top: 0; left: 0; width: 100%; z-index: 9999; }

    /* NNC paper table */
    .nnc-paper { font-family: Arial, sans-serif; font-size: 9pt; color: #000; padding: 6mm 8mm; }
    .nnc-paper-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 3px; }
    .nnc-paper-center { text-align: center; flex: 1; line-height: 1.4; }
    .nnc-paper-seal { width: 52px; height: 52px; border: 1px solid #999; display: flex; align-items: center; justify-content: center; font-size: 6pt; color: #888; text-align: center; }
    .nnc-paper-logos { display: flex; gap: 4px; }
    .nnc-paper-logo-box { border: 1px solid #ccc; padding: 2px 4px; text-align: center; font-size: 6pt; }
    .nnc-ptable { width: 100%; border-collapse: collapse; font-size: 8pt; }
    .nnc-ptable th, .nnc-ptable td { border: 1px solid #000; padding: 2px 4px; vertical-align: middle; }
    .nnc-ptable th { background: #d9d9d9 !important; text-align: center; font-weight: 700; font-size: 7pt; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .nnc-ptable th.nutri-th { background: #b8cca0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .nnc-ptable td { text-align: center; font-size: 8pt; }
    .nnc-ptable td.tl { text-align: left; }
    .bold-box { background: #e0e0e0 !important; font-weight: 800; padding: 1px 5px; border: 1px solid #999; display: inline-block; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>

<!-- ── Screen toolbar ── -->
<div class="screen-toolbar no-print">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="index.php?action=dataEncoding"
               style="display:inline-flex;align-items:center;gap:.4rem;background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.35rem .85rem;font-size:.88rem;font-weight:600;text-decoration:none">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <div>
                <div class="fw-bold" style="color:var(--kn-dark)">OPT Plus Form C — At-risk Children</div>
                <div style="font-size:.78rem;color:var(--kn-muted)">List of Affected/At-risk 0–59 Month-Old Children</div>
            </div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <form method="GET" action="index.php" class="d-flex gap-2 align-items-center">
                <input type="hidden" name="action" value="formCReport">
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

<!-- ══════════════════════════════════════════════════════════════
     FORM DOCUMENT
════════════════════════════════════════════════════════════════ -->
<div class="form-wrapper">

    <!-- Official Header -->
    <div class="nnc-header-band">
        <div class="nnc-seal-placeholder">SPACE FOR<br>OFFICIAL<br>SEAL OF LGU</div>
        <div class="nnc-agency">
            <div class="line1">Republic of the Philippines</div>
            <div class="line2">Department of Health</div>
            <div class="line3">NATIONAL NUTRITION COUNCIL</div>
            <div class="line4">XI Davao Region</div>
        </div>
        <div class="nnc-logo-group">
            <div class="nnc-logo-pill">
                <img src="<?= htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/')) ?>/public/images/nnc_logo.png"
                     alt="NNC Logo"
                     style="width:38px;height:38px;object-fit:contain;display:block;margin:0 auto 2px"
                     onerror="this.style.display='none'">
                <span style="font-size:.6rem;opacity:.85"></span>
            </div>
            <div class="nnc-logo-pill">
                <img src="<?= htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/')) ?>/public/images/opt_plus_tsek.png"
                     alt="OPT Plus TSEK"
                     style="width:38px;height:38px;object-fit:contain;display:block;margin:0 auto 2px"
                     onerror="this.style.display='none'">
                <span style="font-size:.6rem;opacity:.85"></span>
            </div>
        </div>
    </div>

    <!-- Form Title Bar -->
    <div class="form-title-bar">
        <div class="form-title-text">
            OPT Plus Form C. &nbsp;List of Affected/At-risk 0-59 Month-Old Children
        </div>
        <div class="d-flex align-items-center gap-2">
            <span style="font-size:.82rem;color:var(--kn-muted)">Year:</span>
            <span class="year-badge"><?= $year ?></span>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">

        <!-- Location fields -->
        <div class="summary-location">
            <div style="font-size:.78rem;font-weight:700;color:var(--kn-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem">Location</div>
            <div class="loc-row"><label>Barangay:</label><span class="loc-line"><?= $locBarangay ?></span></div>
            <div class="loc-row"><label>Municipality:</label><span class="loc-line"><?= $locMunicipality ?></span></div>
            <div class="loc-row"><label>Province:</label><span class="loc-line"><?= $locProvince ?></span></div>
        </div>

        <!-- Status counts -->
        <div class="summary-counts">
            <div style="font-size:.78rem;font-weight:700;color:var(--kn-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem">Nutritional Status Summary</div>
            <div class="counts-grid">
                <div class="count-card moderate">
                    <div class="cv"><?= (int)($s['MUW']??0) ?></div>
                    <div class="cl">MUW</div>
                </div>
                <div class="count-card moderate">
                    <div class="cv"><?= (int)($s['MSt']??0) ?></div>
                    <div class="cl">MSt</div>
                </div>
                <div class="count-card moderate">
                    <div class="cv"><?= (int)($s['MAM']??0) ?></div>
                    <div class="cl">MW/MAM</div>
                </div>
                <div class="count-card severe">
                    <div class="cv"><?= (int)($s['SUW']??0) ?></div>
                    <div class="cl">SUW</div>
                </div>
                <div class="count-card severe">
                    <div class="cv"><?= (int)($s['SSt']??0) ?></div>
                    <div class="cl">SSt</div>
                </div>
                <div class="count-card severe">
                    <div class="cv"><?= (int)($s['SAM']??0) ?></div>
                    <div class="cl">SW/SAM</div>
                </div>
                <div class="count-card overweight">
                    <div class="cv"><?= (int)($s['OW']??0) ?></div>
                    <div class="cl">OW</div>
                </div>
                <div class="count-card overweight">
                    <div class="cv"><?= (int)($s['Ob']??0) ?></div>
                    <div class="cl">Ob</div>
                </div>
                <div class="count-card" style="border-color:rgba(107,122,58,.3);background:rgba(107,122,58,.06)">
                    <div class="cv normal"><?= $total ?></div>
                    <div class="cl" style="color:var(--kn-green)">Total</div>
                </div>
            </div>
        </div>

        <!-- Totals box -->
        <div class="summary-totals">
            <div style="font-size:.78rem;font-weight:700;color:var(--kn-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem">Counts</div>
            <div class="total-box">
                <div class="tb-row">
                    <span class="tb-label">Affected by Undernutrition:</span>
                    <span class="tb-val danger"><?= $undernut ?></span>
                </div>
                <div class="tb-row">
                    <span class="tb-label">Overweight or Obesity:</span>
                    <span class="tb-val info"><?= $overnut ?></span>
                </div>
            </div>
            <div class="grand-total">
                <span>Total At-risk</span>
                <span style="font-size:1.2rem"><?= $total ?></span>
            </div>
        </div>

    </div><!-- /summary-section -->

    <!-- Data Table -->
    <div class="table-section">
        <?php if (empty($records)): ?>
        <div class="empty-state">
            <i class="bi bi-clipboard2-check d-block mb-2"></i>
            <div class="fw-semibold" style="color:var(--kn-dark)">No at-risk children recorded for <?= $year ?></div>
            <div style="font-size:.82rem;margin-top:.25rem">Children flagged during assessment will appear here.</div>
        </div>
        <?php else: ?>
        <table class="nnc-data-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width:32px">Child<br>Seq.</th>
                    <th rowspan="2" style="width:90px">Address<br><span style="font-weight:400;font-size:.68rem">Purok / Location</span></th>
                    <th rowspan="2" style="width:120px">Name of Mother<br>or Caregiver</th>
                    <th rowspan="2" style="width:130px">Full Name of Child</th>
                    <th rowspan="2" style="width:28px">Sex</th>
                    <th rowspan="2" style="width:38px">Age<br>(mos)</th>
                    <th colspan="3" class="nutri-header">Nutritional Status</th>
                    <th rowspan="2" style="width:40px">MUAC<br>(cm)</th>
                </tr>
                <tr>
                    <th class="nutri-header" style="width:60px">Weight<br>for Age</th>
                    <th class="nutri-header" style="width:60px">Length/Height<br>for Age</th>
                    <th class="nutri-header" style="width:60px">Weight for<br>Length/Height</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sbClass = [
                'SUW'=>'sb-suw','UW'=>'sb-uw','Normal'=>'sb-normal',
                'SSt'=>'sb-suw','St'=>'sb-uw','Tall'=>'sb-normal',
                'SAM'=>'sb-ssam','MAM'=>'sb-mam','OW'=>'sb-ow','Ob'=>'sb-ob',
            ];
            foreach ($records as $i => $r):
            ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td class="text-left"><?= htmlspecialchars($r['purok'] ?? '—') ?></td>
                <td class="text-left"><?= htmlspecialchars($r['caregiver_name'] ?? '—') ?></td>
                <td class="text-left fw-semibold"><?= htmlspecialchars($r['full_name']) ?></td>
                <td><?= $r['sex'] === 'M' ? 'M' : 'F' ?></td>
                <td><?= (int)$r['age_in_months'] ?></td>
                <td>
                    <?php if ($r['wfa_status']): ?>
                    <span class="status-badge <?= $sbClass[$r['wfa_status']]??'' ?>"><?= $r['wfa_status'] ?></span>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td>
                    <?php if ($r['hfa_status']): ?>
                    <span class="status-badge <?= $sbClass[$r['hfa_status']]??'' ?>"><?= $r['hfa_status'] ?></span>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td>
                    <?php if ($r['wfh_status']): ?>
                    <span class="status-badge <?= $sbClass[$r['wfh_status']]??'' ?>"><?= $r['wfh_status'] ?></span>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td><?= $r['muac_cm'] ?: '—' ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="form-footer">
        <div>
            <strong>Prepared by:</strong> <?= htmlspecialchars($bnsName) ?>
            &nbsp;&nbsp;
            <strong>Date:</strong> <?= date('F j, Y') ?>
        </div>
        <div><?= count($records) ?> record(s) · <?= $year ?></div>
    </div>

</div><!-- /form-wrapper -->

<!-- ══════════════════════════════════════════════════════════════
     PRINT-ONLY: NNC Standard Paper Format (hidden on screen)
════════════════════════════════════════════════════════════════ -->
<div class="print-only">
<div class="nnc-paper">

    <!-- Header -->
    <div class="nnc-paper-header">
        <div class="nnc-paper-seal">SPACE FOR<br>OFFICIAL<br>SEAL OF LGU</div>
        <div class="nnc-paper-center">
            <div style="font-size:8pt">Republic of the Philippines</div>
            <div style="font-size:8pt">Department of Health</div>
            <div style="font-size:11pt;font-weight:800">NATIONAL NUTRITION COUNCIL</div>
            <div style="font-size:9pt">XI Davao Region</div>
        </div>
        <div class="nnc-paper-logos">
            <div class="nnc-paper-logo-box">
                <img src="<?= htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/')) ?>/public/images/nnc_logo.png"
                     style="width:36px;height:36px;object-fit:contain;display:block" alt="NNC">
            </div>
            <div class="nnc-paper-logo-box">
                <img src="<?= htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/')) ?>/public/images/opt_plus_tsek.png"
                     style="width:44px;height:44px;object-fit:contain;display:block" alt="OPT Plus TSEK">
            </div>
        </div>
    </div>

    <!-- Year + Title -->
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:3px">
        <div>
            <div style="font-size:9pt"><strong>YEAR:</strong> <span style="border-bottom:1px solid #000;min-width:70px;display:inline-block">&nbsp;<?= $year ?>&nbsp;</span></div>
            <div style="font-weight:700;font-size:10pt;margin-top:2px">OPT Plus Form C. List of Affected/At-risk 0-59 Month-Old Children</div>
        </div>
        <div style="border:1px solid #000;padding:1px 5px;font-size:7pt"># Pages for Printing: <strong><?= max(1, ceil(count($records)/25)) ?></strong></div>
    </div>

    <!-- Summary row -->
    <div style="display:flex;gap:12px;align-items:flex-start;margin-bottom:5px">
        <!-- Left: Total + Location -->
        <div style="flex:0 0 200px;font-size:8pt">
            <div style="margin-bottom:3px"><strong>Total # Children Affected/At-Risk:</strong><br>
                <span style="border-bottom:1px solid #000;min-width:80px;display:inline-block">&nbsp;<?= $total ?>&nbsp;</span>
            </div>
            <div style="margin-bottom:2px"><strong>Barangay:</strong> <span id="print-loc-barangay" style="border-bottom:1px solid #000;min-width:100px;display:inline-block">&nbsp;<?= $locBarangay ?>&nbsp;</span></div>
            <div style="margin-bottom:2px"><strong>Municipality:</strong> <span id="print-loc-municipality" style="border-bottom:1px solid #000;min-width:90px;display:inline-block">&nbsp;<?= $locMunicipality ?>&nbsp;</span></div>
            <div><strong>Province:</strong> <span id="print-loc-province" style="border-bottom:1px solid #000;min-width:100px;display:inline-block">&nbsp;<?= $locProvince ?>&nbsp;</span></div>
        </div>

        <!-- Center: Status counts -->
        <div style="flex:1;font-size:8.5pt">
            <div style="display:flex;gap:20px;margin-bottom:3px">
                <span>MUW= <strong><?= (int)($s['MUW']??0) ?></strong></span>
                <span>MSt= <strong><?= (int)($s['MSt']??0) ?></strong></span>
                <span>MW/MAM= <strong><?= (int)($s['MAM']??0) ?></strong></span>
            </div>
            <div style="display:flex;gap:12px;margin-bottom:5px">
                <span class="bold-box">SUW= <?= (int)($s['SUW']??0) ?></span>
                <span class="bold-box">SSt= <?= (int)($s['SSt']??0) ?></span>
                <span class="bold-box">SW/SAM= <?= (int)($s['SAM']??0) ?></span>
            </div>
            <div style="margin-bottom:2px">OW= <strong><?= (int)($s['OW']??0) ?></strong></div>
            <div>Ob= <strong><?= (int)($s['Ob']??0) ?></strong></div>
        </div>

        <!-- Right: Undernutrition box -->
        <div style="flex:0 0 210px;border:1px solid #000;padding:4px 6px;font-size:8pt">
            <div style="margin-bottom:3px">Number of Children Affected by Undernutrition: <strong><?= $undernut ?></strong></div>
            <div>Number of Children with Overweight or Obesity: <strong><?= $overnut ?></strong></div>
        </div>
    </div>

    <!-- Data table -->
    <table class="nnc-ptable">
        <thead>
            <tr>
                <th rowspan="2" style="width:28px">Child<br>Seq.</th>
                <th rowspan="2" style="width:80px">Address<br><span style="font-weight:400;font-size:6pt">Purok or Location in the Barangay</span></th>
                <th rowspan="2" style="width:100px">Name of Mother<br>or Caregiver</th>
                <th rowspan="2" style="width:110px">Full Name of Child</th>
                <th rowspan="2" style="width:22px">Sex</th>
                <th rowspan="2" style="width:32px">Age in<br>Months</th>
                <th colspan="3" class="nutri-th">Nutritional Status</th>
                <th rowspan="2" style="width:32px">MUAC</th>
            </tr>
            <tr>
                <th class="nutri-th" style="width:52px">Weight<br>for Age</th>
                <th class="nutri-th" style="width:52px">Length/<br>Height for Age</th>
                <th class="nutri-th" style="width:52px">Weight for<br>Length/Height</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($records)): ?>
        <tr><td colspan="10" style="text-align:center;padding:8px;color:#666">No at-risk children recorded for <?= $year ?>.</td></tr>
        <?php else: ?>
        <?php foreach ($records as $i => $r): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td class="tl"><?= htmlspecialchars($r['purok'] ?? '') ?></td>
            <td class="tl"><?= htmlspecialchars($r['caregiver_name'] ?? '') ?></td>
            <td class="tl"><strong><?= htmlspecialchars($r['full_name']) ?></strong></td>
            <td><?= $r['sex'] === 'M' ? 'M' : 'F' ?></td>
            <td><?= (int)$r['age_in_months'] ?></td>
            <td style="font-weight:700"><?= $r['wfa_status'] ?? '—' ?></td>
            <td style="font-weight:700"><?= $r['hfa_status'] ?? '—' ?></td>
            <td style="font-weight:700"><?= $r['wfh_status'] ?? '—' ?></td>
            <td><?= $r['muac_cm'] ?: '' ?></td>
        </tr>
        <?php endforeach; ?>
        <!-- Blank rows -->
        <?php for ($b = count($records); $b < 25; $b++): ?>
        <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <?php endfor; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:6px;font-size:8pt">
        <strong>Prepared by:</strong> <?= htmlspecialchars($bnsName) ?>
        &nbsp;&nbsp;&nbsp;
        <strong>Date:</strong> <?= date('F j, Y') ?>
    </div>

</div><!-- /nnc-paper -->
</div><!-- /print-only -->

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
