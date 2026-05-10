<?php
require_once __DIR__ . '/../templates/bns_layout.php';
$bnsName = $_SESSION['user_name'] ?? 'BNS Staff';
$imgBase = htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
?>
<style>
:root{--kn-green:#6B7A3A;--kn-dark:#3D4A1E;--kn-muted:rgba(61,74,30,.55);}

/* Screen: simple card per list */
.all-section{background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:1rem;overflow:hidden;box-shadow:0 2px 8px rgba(61,74,30,.06);margin-bottom:1.5rem;}
.all-section-header{background:rgba(107,122,58,.07);border-bottom:1.5px solid rgba(107,122,58,.12);padding:.6rem 1.25rem;font-weight:700;font-size:.82rem;color:var(--kn-green);}
.all-table{width:100%;border-collapse:collapse;font-size:.78rem;}
.all-table th{background:rgba(107,122,58,.1);color:var(--kn-dark);font-weight:700;font-size:.7rem;text-align:center;padding:.4rem .4rem;border:1px solid rgba(107,122,58,.2);}
.all-table th.vth{background:rgba(107,122,58,.18);}
.all-table td{padding:.35rem .4rem;border:1px solid rgba(107,122,58,.1);vertical-align:top;text-align:center;font-size:.78rem;}
.all-table td.tl{text-align:left;}
.empty-note{padding:1rem 1.25rem;font-size:.82rem;color:var(--kn-muted);text-align:center;}

/* Print */
@page{size:A4 landscape;margin:8mm 10mm;}
body .print-only{display:none !important;}
@media print{
    *{-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;}
    @page{size:A4 landscape;margin:8mm 10mm;}
    .no-print,.kn-sidebar,.kn-topbar,.kn-flash,.screen-toolbar,.sidebar-toggle,.sidebar-overlay{display:none!important;}
    .kn-main{margin:0!important;padding:0!important;width:100%!important;}
    .kn-content{margin:0!important;padding:0!important;width:100%!important;}
    body{font-size:7.5pt;background:#fff!important;margin:0!important;padding:0!important;}

    /* Hide screen cards, show print sections */
    .all-section{display:none!important;}
    body .print-only{display:block!important;}

    .print-section{page-break-after:always;}
    .print-section:last-child{page-break-after:avoid;}

    .nnc-paper{font-family:Arial,sans-serif;font-size:7pt;color:#000;padding:3mm 4mm;}
    .nnc-ptable{width:100%;border-collapse:collapse;font-size:6.5pt;}
    .nnc-ptable th,.nnc-ptable td{border:1px solid #000;padding:1.5px 3px;vertical-align:top;}
    .nnc-ptable th{background:#d9d9d9!important;text-align:center;font-weight:700;font-size:6pt;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    .nnc-ptable th.vth{background:#b8cca0!important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    .nnc-ptable td{text-align:center;}
    .nnc-ptable td.tl{text-align:left;}
    .section-title{font-weight:800;font-size:8pt;text-align:center;margin-bottom:3px;}
    .section-meta{font-size:6.5pt;margin-bottom:3px;display:flex;gap:16px;}
}
</style>

<!-- Screen toolbar -->
<div class="screen-toolbar no-print" style="margin-bottom:1.5rem">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="index.php?action=p12Monitoring&year=<?= $year ?>"
               style="display:inline-flex;align-items:center;gap:.4rem;background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.35rem .85rem;font-size:.88rem;font-weight:600;text-decoration:none">
                <i class="bi bi-arrow-left"></i> Back to Monitoring List
            </a>
            <div>
                <div class="fw-bold" style="color:var(--kn-dark)">All Monitoring Lists — <?= $year ?></div>
                <div style="font-size:.78rem;color:var(--kn-muted)">All 9 NNC monitoring lists in one page</div>
            </div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <form method="GET" action="index.php" class="d-flex gap-2 align-items-center">
                <input type="hidden" name="action" value="p12MonitoringAll">
                <select name="year" class="form-select form-select-sm" style="width:90px;border:1.5px solid rgba(107,122,58,.25);border-radius:7px">
                    <?php for ($y = date('Y'); $y >= date('Y')-3; $y--): ?>
                    <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="btn btn-sm fw-semibold"
                        style="background:rgba(107,122,58,.1);color:var(--kn-green);border:1.5px solid rgba(107,122,58,.25);border-radius:7px">Filter</button>
            </form>
            <button onclick="window.print()"
                    style="display:inline-flex;align-items:center;gap:.4rem;background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.4rem 1rem;font-size:.88rem;font-weight:600;cursor:pointer">
                <i class="bi bi-printer-fill"></i> Print
            </button>
            <button onclick="window.print()"
                    style="display:inline-flex;align-items:center;gap:.4rem;background:var(--kn-green);color:#fff;border:none;border-radius:8px;padding:.4rem 1rem;font-size:.88rem;font-weight:600;cursor:pointer">
                <i class="bi bi-file-earmark-pdf-fill"></i> Save as PDF
            </button>
        </div>
    </div>
</div>

<!-- ── SCREEN VIEW ── -->
<?php foreach ($allLists as $key => $list):
    $recs = $list['records'];
?>
<div class="all-section">
    <div class="all-section-header">
        <i class="bi bi-clipboard2-data me-1"></i>
        <?= htmlspecialchars($list['title']) ?>
        <span style="font-weight:400;font-size:.75rem;margin-left:.5rem;opacity:.7">(<?= count($recs) ?> record<?= count($recs) != 1 ? 's' : '' ?>)</span>
    </div>
    <?php if (empty($recs)): ?>
    <div class="empty-note"><i class="bi bi-check-circle text-success me-1"></i>No children flagged for this list in <?= $year ?>.</div>
    <?php else: ?>
    <div style="overflow-x:auto">
    <table class="all-table">
        <thead>
            <tr>
                <th style="width:28px">Seq.</th>
                <th style="width:75px">Purok</th>
                <th style="width:100px">Mother/Caregiver</th>
                <th style="width:110px">Full Name of Child</th>
                <th style="width:22px">Sex</th>
                <th style="width:60px">Birthdate</th>
                <th style="width:38px">Ht (cm)</th>
                <th style="width:38px">Wt (kg)</th>
                <th colspan="6" class="vth">Follow-up Visits (Month 1–6)</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($recs as $i => $r): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td class="tl"><?= htmlspecialchars($r['purok'] ?? '—') ?></td>
            <td class="tl"><?= htmlspecialchars($r['caregiver_name'] ?? '—') ?></td>
            <td class="tl fw-semibold"><?= htmlspecialchars($r['full_name']) ?></td>
            <td><?= $r['sex'] === 'M' ? 'M' : 'F' ?></td>
            <td><?= $r['dob'] ? date('m/d/Y', strtotime($r['dob'])) : '—' ?></td>
            <td><?= $r['height_cm'] ?></td>
            <td><?= $r['weight_kg'] ?></td>
            <?php for ($m = 1; $m <= 6; $m++):
                $vDate = $r["v{$m}_date"] ?? null;
            ?>
            <td style="font-size:.7rem"><?= $vDate ? date('m/d/y', strtotime($vDate)) : '' ?></td>
            <?php endfor; ?>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<!-- ── PRINT-ONLY VIEW ── -->
<div class="print-only">
<?php foreach ($allLists as $key => $list):
    $recs = $list['records'];
?>
<div class="print-section">
<div class="nnc-paper">

    <!-- Header -->
    <div style="text-align:center;margin-bottom:3px">
        <div style="font-size:7pt">Republic of the Philippines · Department of Health</div>
        <div style="font-weight:800;font-size:9pt">NATIONAL NUTRITION COUNCIL — XI Davao Region</div>
    </div>

    <div class="section-title"><?= strtoupper(htmlspecialchars($list['title'])) ?></div>

    <div class="section-meta">
        <span><strong>Barangay:</strong> <span style="border-bottom:1px solid #000;min-width:80px;display:inline-block">&nbsp;</span></span>
        <span><strong>Municipality:</strong> <span style="border-bottom:1px solid #000;min-width:80px;display:inline-block">&nbsp;</span></span>
        <span><strong>Province:</strong> <span style="border-bottom:1px solid #000;min-width:70px;display:inline-block">&nbsp;</span></span>
        <span><strong>Year:</strong> <?= $year ?></span>
        <span><strong>BNS:</strong> <?= htmlspecialchars($bnsName) ?></span>
        <span><strong># Children:</strong> <?= count($recs) ?></span>
    </div>

    <table class="nnc-ptable">
        <thead>
            <tr>
                <th rowspan="2" style="width:20px">Seq.</th>
                <th rowspan="2" style="width:55px">Address/<br>Purok</th>
                <th rowspan="2" style="width:80px">Mother/<br>Caregiver</th>
                <th rowspan="2" style="width:90px">Full Name of Child</th>
                <th rowspan="2" style="width:16px">Sex</th>
                <th rowspan="2" style="width:44px">Birthdate</th>
                <th rowspan="2" style="width:28px">Ht<br>(cm)</th>
                <th rowspan="2" style="width:28px">Wt<br>(kg)</th>
                <th colspan="6" class="vth">Follow-up Visits</th>
            </tr>
            <tr>
                <?php for ($m = 1; $m <= 6; $m++): ?>
                <th class="vth" style="width:52px">Month #<?= $m ?><br><span style="font-weight:400;font-size:5pt">(1)Date<br>(2)Intervention<br>(3)Status</span></th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($recs)): ?>
        <tr><td colspan="14" style="text-align:center;padding:6px;color:#666">No children flagged for this list in <?= $year ?>.</td></tr>
        <?php else: ?>
        <?php foreach ($recs as $i => $r): ?>
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
                $vDate = $r["v{$m}_date"]   ?? null;
                $vInt  = $r["v{$m}_int"]    ?? null;
                $vStat = $r["v{$m}_status"] ?? null;
            ?>
            <td style="vertical-align:top">
                <?php if ($vDate): ?>
                <div style="font-size:5.5pt;line-height:1.4">
                    <div><?= date('m/d/y', strtotime($vDate)) ?></div>
                    <?php if ($vInt): ?><div><?= htmlspecialchars(substr($vInt,0,30)) ?></div><?php endif; ?>
                    <?php if ($vStat): ?><div><strong><?= htmlspecialchars($vStat) ?></strong></div><?php endif; ?>
                </div>
                <?php endif; ?>
            </td>
            <?php endfor; ?>
        </tr>
        <?php endforeach; ?>
        <!-- Blank rows (min 10) -->
        <?php for ($b = count($recs); $b < 10; $b++): ?>
        <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <?php endfor; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:5px;font-size:6.5pt">
        <strong>Prepared by:</strong> <?= htmlspecialchars($bnsName) ?>
        &nbsp;&nbsp;&nbsp;
        <strong>Date Prepared:</strong> <?= date('F j, Y') ?>
    </div>

</div><!-- /nnc-paper -->
</div><!-- /print-section -->
<?php endforeach; ?>
</div><!-- /print-only -->

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
