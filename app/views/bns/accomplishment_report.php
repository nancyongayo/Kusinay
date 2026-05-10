<?php
require_once __DIR__ . '/../templates/bns_layout.php';
$bnsName = $_SESSION['user_name'] ?? 'BNS Staff';
$imgBase = htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
$months  = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
$statusColors = [
    'Draft'     => ['bg'=>'rgba(107,122,58,.1)',  'color'=>'var(--kn-green)',  'icon'=>'bi-pencil'],
    'Submitted' => ['bg'=>'rgba(196,114,42,.1)',  'color'=>'var(--kn-orange)', 'icon'=>'bi-send'],
    'Approved'  => ['bg'=>'rgba(107,122,58,.15)', 'color'=>'var(--kn-green)',  'icon'=>'bi-check-circle-fill'],
    'Returned'  => ['bg'=>'rgba(220,53,69,.1)',   'color'=>'#dc3545',          'icon'=>'bi-arrow-counterclockwise'],
];
$sc = $statusColors[$report['status']] ?? $statusColors['Draft'];
$isLocked = in_array($report['status'], ['Submitted','Approved']);
$canEdit = in_array($report['status'], ['Draft','Returned']);
?>
<style>
:root{--kn-green:#6B7A3A;--kn-orange:#C4722A;--kn-dark:#3D4A1E;--kn-muted:rgba(61,74,30,.55);}
.form-wrapper{background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:1rem;overflow:hidden;box-shadow:0 2px 12px rgba(61,74,30,.08);}
.section-header{background:rgba(107,122,58,.06);border-bottom:1.5px solid rgba(107,122,58,.1);padding:.6rem 1.25rem;font-weight:700;font-size:.82rem;color:var(--kn-green);text-transform:uppercase;letter-spacing:.05em;}
.report-row{display:flex;align-items:center;padding:.5rem 1.25rem;border-bottom:1px solid rgba(107,122,58,.07);gap:1rem;}
.report-row:last-child{border-bottom:none;}
.row-label{flex:1;font-size:.85rem;color:var(--kn-dark);}
.row-auto{font-size:.75rem;color:var(--kn-muted);margin-left:.25rem;}
.row-input{width:90px;flex-shrink:0;}
.row-input input{border:1.5px solid rgba(107,122,58,.25);border-radius:7px;padding:.3rem .6rem;font-size:.88rem;text-align:center;width:100%;}
.row-input input:focus{border-color:var(--kn-green);outline:none;box-shadow:0 0 0 2px rgba(107,122,58,.12);}
.row-input input[readonly]{background:rgba(107,122,58,.04);color:var(--kn-green);font-weight:700;}
.row-input input:not([readonly]):hover{border-color:var(--kn-green);background:rgba(107,122,58,.02);}
.status-badge{display:inline-flex;align-items:center;gap:.35rem;padding:.25rem .75rem;border-radius:20px;font-weight:700;font-size:.82rem;}
.history-item{padding:.5rem 1rem;border-bottom:1px solid rgba(107,122,58,.07);font-size:.82rem;}
body .print-only{display:none !important;}
@page{size:A4 portrait;margin:8mm 10mm;}
@media print{
    *{-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;}
    @page{size:A4 portrait;margin:8mm 10mm;}
    .no-print,.kn-sidebar,.kn-topbar,.kn-flash,.screen-toolbar,.form-wrapper,.history-card,.attachments-section,.status-banner{display:none!important;}
    .kn-main{margin:0!important;padding:0!important;width:100%!important;}
    .kn-content{margin:0!important;padding:0!important;width:100%!important;}
    body{font-size:8pt;background:#fff!important;margin:0!important;padding:0!important;}
    body .print-only{display:block!important;position:fixed;top:0;left:0;width:100%;z-index:9999;}
    .rpt-paper{font-family:Arial,sans-serif;font-size:8pt;color:#000;padding:5mm 8mm;}
    .rpt-table{width:100%;border-collapse:collapse;font-size:7.5pt;}
    .rpt-table th,.rpt-table td{border:1px solid #000;padding:2px 4px;vertical-align:middle;}
    .rpt-table th{background:#d9d9d9!important;font-weight:700;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    .rpt-table td.act{text-align:left;}
}
</style>

<!-- Screen toolbar -->
<div class="screen-toolbar no-print" style="margin-bottom:1.5rem">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <div class="fw-bold" style="color:var(--kn-dark)">
                Monthly Accomplishment Report
                <?php if ($canEdit && $report['status'] === 'Returned'): ?>
                <span style="display:inline-flex;align-items:center;gap:.3rem;background:rgba(220,53,69,.1);color:#dc3545;border-radius:6px;padding:.2rem .6rem;font-size:.75rem;font-weight:700;margin-left:.5rem">
                    <i class="bi bi-exclamation-triangle"></i> Needs Correction
                </span>
                <?php endif; ?>
            </div>
            <div style="font-size:.78rem;color:var(--kn-muted)">BNS Monthly Report — <?= $monthName ?> <?= $year ?></div>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <!-- Month/Year selector -->
            <form method="GET" action="index.php" class="d-flex gap-2 align-items-center">
                <input type="hidden" name="action" value="accomplishmentReport">
                <select name="month" class="form-select form-select-sm" style="width:120px;border:1.5px solid rgba(107,122,58,.25);border-radius:7px">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>><?= $months[$m] ?></option>
                    <?php endfor; ?>
                </select>
                <select name="year" class="form-select form-select-sm" style="width:90px;border:1.5px solid rgba(107,122,58,.25);border-radius:7px">
                    <?php for ($y = date('Y'); $y >= date('Y')-2; $y--): ?>
                    <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="btn btn-sm fw-semibold"
                        style="background:rgba(107,122,58,.1);color:var(--kn-green);border:1.5px solid rgba(107,122,58,.25);border-radius:7px">Go</button>
            </form>
            <button onclick="window.print()"
                    style="display:inline-flex;align-items:center;gap:.4rem;background:var(--kn-green);color:#fff;border:none;border-radius:8px;padding:.4rem 1rem;font-size:.88rem;font-weight:600;cursor:pointer">
                <i class="bi bi-printer-fill"></i> Print / Save as PDF
            </button>
            <?php if ($canEdit): ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-4">
<div class="col-lg-8">

<!-- Status banner -->
<div class="status-banner" style="background:<?= $sc['bg'] ?>;border:1.5px solid <?= $sc['color'] ?>33;border-radius:.75rem;padding:.65rem 1rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem">
    <i class="bi <?= $sc['icon'] ?>" style="color:<?= $sc['color'] ?>"></i>
    <span style="font-weight:700;color:<?= $sc['color'] ?>"><?= $report['status'] ?></span>
    <?php if ($report['status'] === 'Returned' && $report['return_reason']): ?>
    <span style="font-size:.82rem;color:var(--kn-dark);margin-left:.5rem">— <?= htmlspecialchars($report['return_reason']) ?></span>
    <?php endif; ?>
    <?php if ($report['submitted_at']): ?>
    <span style="font-size:.78rem;color:var(--kn-muted);margin-left:auto">Submitted: <?= date('M j, Y g:i A', strtotime($report['submitted_at'])) ?></span>
    <?php endif; ?>
</div>

<?php if ($report['status'] === 'Returned'): ?>
<div class="status-banner" style="background:rgba(255,193,7,.1);border:1.5px solid rgba(255,193,7,.3);border-radius:.75rem;padding:.65rem 1rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem">
    <i class="bi bi-exclamation-triangle" style="color:#ffc107"></i>
    <span style="font-size:.85rem;color:#856404">This report was returned for correction. Please review the feedback, make necessary changes, and resubmit.</span>
</div>
<?php endif; ?>

<form method="POST" action="index.php?action=<?= $canEdit ? 'saveAccomplishment' : '' ?>" id="reportForm">
    <input type="hidden" name="report_id" value="<?= (int)$report['report_id'] ?>">

    <div class="form-wrapper mb-3">
        <!-- OPT Plus Section -->
        <div class="section-header"><i class="bi bi-clipboard2-data me-2"></i>OPT Plus</div>

        <?php
        $rows = [
            // [label, field_name, is_auto]
            ['No. of 0-23 mos PS weighed and taken height monthly',          'ps_0_23_weighed',  true],
            ['No. of 24-59 mos old PS weighed and taken height semi-annually','ps_24_59_weighed', true],
            ['No. of 0-59 mos old malnourished PS weighed',                  'ps_malnourished',  true],
            ['Total No. of MAM',                                              'total_mam',        true],
            ['No. of MAM monitored bi-monthly',                               'mam_monitored',    false],
            ['MAM — New Admission',                                           'mam_new_admission',false],
            ['MAM — Non-cured',                                               'mam_non_cured',    false],
            ['MAM — Defaulter',                                               'mam_defaulter',    false],
            ['MAM — Died',                                                    'mam_died',         false],
            ['Total No. of SAM',                                              'total_sam',        true],
            ['No. of SAM monitored weekly',                                   'sam_monitored',    false],
            ['SAM — New Admission',                                           'sam_new_admission',false],
            ['SAM — Non-cured',                                               'sam_non_cured',    false],
            ['SAM — Died',                                                    'sam_died',         false],
        ];
        foreach ($rows as [$label, $field, $auto]):
        ?>
        <div class="report-row">
            <div class="row-label">
                <?= htmlspecialchars($label) ?>
                <?php if ($auto): ?><span class="row-auto">(auto)</span><?php endif; ?>
            </div>
            <div class="row-input">
                <input type="number" name="<?= $field ?>" min="0"
                       value="<?= (int)($report[$field] ?? 0) ?>"
                       <?= (!$canEdit || $auto) ? 'readonly' : '' ?>>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="form-wrapper mb-3">
        <!-- Other Activities -->
        <div class="section-header"><i class="bi bi-activity me-2"></i>Other Activities</div>
        <?php
        $rows2 = [
            ['No. of CVD patients served',                          'cvd_patients',         false],
            ['No. of Pregnant Women (new cases)',                   'pregnant_new',         true],
            ['No. of Lactating Mothers (new cases)',                'lactating_new',        true],
            ['Families of malnourished children',                   'families_malnourished',false],
            ['Adolescents (10-19 years old)',                       'adolescents',          true],
            ['Adults (20-59 years old)',                            'adults',               true],
            ['Elderly (60 years old and above)',                    'elderly_assessed',     true],
            ['No. of 6-11 mos infants given Vit. A',               'infants_vita',         false],
            ['No. of 12-59 mos PS given 2 doses Vit. A',           'children_vita',        false],
            ['No. of 1-4 yrs old SC given 2 doses deworming meds', 'deworm_1_4',           false],
            ['No. of 5-9 yrs old SC given 2 doses deworming meds', 'deworm_5_9',           false],
            ['No. of 10-19 yrs old SC given 2 doses deworming meds','deworm_10_19',        false],
            ['No. of monthly meeting attended',                     'monthly_meetings',     false],
        ];
        foreach ($rows2 as [$label, $field, $auto]):
        ?>
        <div class="report-row">
            <div class="row-label">
                <?= htmlspecialchars($label) ?>
                <?php if ($auto): ?><span class="row-auto">(auto)</span><?php endif; ?>
            </div>
            <div class="row-input">
                <input type="number" name="<?= $field ?>" min="0"
                       value="<?= (int)($report[$field] ?? 0) ?>"
                       <?= (!$canEdit || $auto) ? 'readonly' : '' ?>>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Remarks -->
        <div style="padding:.75rem 1.25rem">
            <label style="font-size:.82rem;font-weight:700;color:var(--kn-dark)">Remarks</label>
            <textarea name="remarks" class="form-control mt-1" rows="2"
                      style="border:1.5px solid rgba(107,122,58,.25);border-radius:8px;font-size:.85rem"
                      <?= !$canEdit ? 'readonly' : '' ?>><?= htmlspecialchars($report['remarks'] ?? '') ?></textarea>
        </div>
    </div>

</form>

<!-- ── Attachments Section ─────────────────────────────────────────────── -->
<div class="form-wrapper mt-3 attachments-section">
    <div class="section-header">
        <i class="bi bi-paperclip me-2"></i>Supporting Attachments
        <span style="font-weight:400;font-size:.75rem;text-transform:none;letter-spacing:0;margin-left:.5rem;opacity:.75">
            — PDF, images, Word, Excel (max 10 MB each)
        </span>
    </div>

    <?php if (!empty($attachments)): ?>
    <div style="padding:.5rem 1.25rem">
        <?php
        $iconMap = [
            'application/pdf'  => ['bi-file-earmark-pdf-fill', '#e74c3c'],
            'image/jpeg'       => ['bi-file-earmark-image-fill', '#2980b9'],
            'image/png'        => ['bi-file-earmark-image-fill', '#2980b9'],
            'image/gif'        => ['bi-file-earmark-image-fill', '#2980b9'],
            'application/msword' => ['bi-file-earmark-word-fill', '#2b579a'],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['bi-file-earmark-word-fill', '#2b579a'],
            'application/vnd.ms-excel' => ['bi-file-earmark-excel-fill', '#217346'],
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['bi-file-earmark-excel-fill', '#217346'],
        ];
        foreach ($attachments as $att):
            [$ico, $icoColor] = $iconMap[$att['file_type']] ?? ['bi-file-earmark-fill', '#95a5a6'];
            $sizeLabel = $att['file_size'] >= 1048576
                ? round($att['file_size'] / 1048576, 1) . ' MB'
                : round($att['file_size'] / 1024, 0) . ' KB';
        ?>
        <div style="display:flex;align-items:center;gap:.75rem;padding:.55rem .25rem;border-bottom:1px solid rgba(107,122,58,.07)">
            <i class="bi <?= $ico ?>" style="font-size:1.4rem;color:<?= $icoColor ?>;flex-shrink:0"></i>
            <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:.85rem;color:var(--kn-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    <?= htmlspecialchars($att['label'] ?: $att['file_name']) ?>
                </div>
                <div style="font-size:.72rem;color:var(--kn-muted)">
                    <?= htmlspecialchars($att['file_name']) ?> · <?= $sizeLabel ?>
                    · <?= date('M j, Y', strtotime($att['uploaded_at'])) ?>
                </div>
            </div>
            <button type="button"
                    onclick="openAttachmentViewer('<?= htmlspecialchars($imgBase . '/' . $att['file_path'], ENT_QUOTES) ?>', '<?= htmlspecialchars($att['file_name'], ENT_QUOTES) ?>', '<?= $att['file_type'] ?>')"
                    style="display:inline-flex;align-items:center;gap:.3rem;background:rgba(107,122,58,.08);color:var(--kn-green);border:1px solid rgba(107,122,58,.2);border-radius:6px;padding:.2rem .6rem;font-size:.78rem;font-weight:600;cursor:pointer;white-space:nowrap">
                <i class="bi bi-eye"></i> View
            </button>
            <?php if ($canEdit): ?>
            <form method="POST" action="index.php?action=deleteAttachment"
                  onsubmit="return confirm('Remove this attachment?')"
                  style="margin:0">
                <input type="hidden" name="attachment_id" value="<?= (int)$att['attachment_id'] ?>">
                <button type="submit"
                        style="display:inline-flex;align-items:center;gap:.3rem;background:rgba(220,53,69,.08);color:#dc3545;border:1px solid rgba(220,53,69,.2);border-radius:6px;padding:.2rem .6rem;font-size:.78rem;font-weight:600;cursor:pointer;white-space:nowrap">
                    <i class="bi bi-trash3"></i> Remove
                </button>
            </form>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="padding:1.25rem;text-align:center;color:var(--kn-muted);font-size:.85rem">
        <i class="bi bi-paperclip" style="font-size:1.5rem;display:block;margin-bottom:.35rem;opacity:.4"></i>
        No attachments yet.
    </div>
    <?php endif; ?>

    <?php if ($canEdit): ?>
    <div style="padding:.75rem 1.25rem;border-top:1.5px solid rgba(107,122,58,.08);background:rgba(107,122,58,.02)">
        <form method="POST" action="index.php?action=uploadAttachment"
              enctype="multipart/form-data"
              id="attachForm">
            <input type="hidden" name="report_id" value="<?= (int)$report['report_id'] ?>">
            <div style="font-size:.82rem;font-weight:700;color:var(--kn-dark);margin-bottom:.5rem">
                <i class="bi bi-upload me-1" style="color:var(--kn-green)"></i> Add Attachment
            </div>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:flex-end">
                <div style="flex:2;min-width:200px">
                    <label style="font-size:.75rem;color:var(--kn-muted);display:block;margin-bottom:.2rem">File</label>
                    <input type="file" name="attachment_file" id="attachFileInput" required
                           accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx"
                           style="width:100%;border:1.5px solid rgba(107,122,58,.25);border-radius:7px;padding:.25rem .5rem;font-size:.82rem;background:#fff">
                </div>
                <div>
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:.4rem;background:var(--kn-green);color:#fff;border:none;border-radius:8px;padding:.38rem 1rem;font-size:.88rem;font-weight:600;cursor:pointer;white-space:nowrap">
                        <i class="bi bi-upload"></i> Upload
                    </button>
                </div>
            </div>
            <div style="font-size:.72rem;color:var(--kn-muted);margin-top:.35rem">
                Accepted: PDF, JPG, PNG, GIF, DOC, DOCX, XLS, XLSX · Max 10 MB
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

<?php if ($canEdit): ?>
<div class="d-flex gap-2 mt-3 no-print">
    <button type="submit" form="reportForm" class="btn fw-semibold"
            style="background:rgba(107,122,58,.1);color:var(--kn-green);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.45rem 1.25rem">
        <i class="bi bi-save me-1"></i> Save <?= $report['status'] === 'Returned' ? 'Changes' : 'Draft' ?>
    </button>
    <button type="submit" form="reportForm"
            formaction="index.php?action=submitAccomplishment"
            class="btn fw-semibold"
            style="background:var(--kn-green);color:#fff;border:none;border-radius:8px;padding:.45rem 1.25rem"
            onclick="return confirm('<?= $report['status'] === 'Returned' ? 'Resubmit this corrected report to the Nutrition Officer II?' : 'Submit this report to the Nutrition Officer II?' ?>')">
        <i class="bi bi-send me-1"></i> <?= $report['status'] === 'Returned' ? 'Resubmit' : 'Submit' ?> to Nutrition Officer II
    </button>
</div>
<?php endif; ?>

</div>

<!-- Right: History -->
<div class="col-lg-4">
    <div class="history-card" style="background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:1rem;overflow:hidden">
        <div style="background:rgba(107,122,58,.06);padding:.65rem 1rem;font-weight:700;font-size:.82rem;color:var(--kn-green);text-transform:uppercase;letter-spacing:.05em;border-bottom:1.5px solid rgba(107,122,58,.1)">
            <i class="bi bi-clock-history me-1"></i> Report History
        </div>
        <?php if (empty($reports)): ?>
        <div style="padding:1.5rem;text-align:center;color:var(--kn-muted);font-size:.82rem">No reports yet.</div>
        <?php else: ?>
        <?php foreach ($reports as $r): 
            $sc2 = $statusColors[$r['status']] ?? $statusColors['Draft']; 
            $canEditReport = in_array($r['status'], ['Draft','Returned']);
            $isCurrent = ($r['report_month'] == $month && $r['report_year'] == $year);
        ?>
        <div class="history-item" style="<?= $isCurrent ? 'background:rgba(107,122,58,.04)' : '' ?>">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <a href="index.php?action=accomplishmentReport&month=<?= $r['report_month'] ?>&year=<?= $r['report_year'] ?>"
                   style="font-weight:600;color:var(--kn-dark);text-decoration:none;font-size:.85rem;flex:1">
                    <?= $months[$r['report_month']] ?> <?= $r['report_year'] ?>
                    <?php if ($isCurrent): ?>
                    <i class="bi bi-eye-fill ms-1" style="font-size:.7rem;color:var(--kn-green)"></i>
                    <?php endif; ?>
                </a>
                <div class="d-flex align-items-center gap-1">
                    <?php if ($canEditReport): ?>
                    <a href="index.php?action=accomplishmentReport&month=<?= $r['report_month'] ?>&year=<?= $r['report_year'] ?>"
                       class="btn btn-sm"
                       style="padding:.15rem .4rem;font-size:.7rem;background:rgba(107,122,58,.1);color:var(--kn-green);border:1px solid rgba(107,122,58,.2);border-radius:5px"
                       title="Edit Report">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <?php endif; ?>
                    <span style="background:<?= $sc2['bg'] ?>;color:<?= $sc2['color'] ?>;border-radius:12px;padding:.1em .55em;font-size:.72rem;font-weight:700">
                        <?= $r['status'] ?>
                    </span>
                </div>
            </div>
            <?php if ($r['status'] === 'Returned' && !empty($r['return_reason'])): ?>
            <div style="font-size:.72rem;color:#dc3545;margin-top:.3rem;padding-left:.25rem">
                <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars(substr($r['return_reason'], 0, 50)) ?><?= strlen($r['return_reason']) > 50 ? '...' : '' ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</div>

<!-- PRINT-ONLY: BNS Monthly Accomplishment Report -->
<div class="print-only">
<div class="rpt-paper">
    <div style="text-align:center;font-size:7.5pt;margin-bottom:2px">Republic of the Philippines</div>
    <div style="text-align:center;font-size:7.5pt;margin-bottom:2px">CITY HEALTH OFFICE - NUTRITION DIVISION</div>
    <div style="text-align:center;font-size:7.5pt;margin-bottom:4px">District: _____________</div>
    <div style="text-align:center;font-weight:800;font-size:10pt;margin-bottom:1px">BNS MONTHLY ACCOMPLISHMENT REPORT</div>
    <div style="text-align:center;font-size:8pt;margin-bottom:6px">BARANGAY: _____________ &nbsp;&nbsp; Year: <?= $year ?></div>

    <table class="rpt-table">
        <thead>
            <tr>
                <th style="width:200px" class="act">ACTIVITIES</th>
                <?php for ($m = 1; $m <= 6; $m++): ?>
                <th style="width:35px"><?= strtoupper(substr($months[$m],0,3)) ?></th>
                <?php endfor; ?>
                <th style="width:35px">TOTAL</th>
                <th style="width:60px">REMARKS</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $allRows = [
            ['OPT Plus', null],
            ['No. of 0-23 mos PS weighed and taken height monthly', 'ps_0_23_weighed'],
            ['No. of 0-59 mos old malnourished PS weighed and taken height semi-annually', 'ps_malnourished'],
            ['No. of 24-59 mos old PS weighed and taken height semi-annually', 'ps_24_59_weighed'],
            ['No. of mothers/fathers/caregivers with malnourished children counseled through home visits', 'families_malnourished'],
            ['Total No. of MAM', 'total_mam'],
            ['No. of MAM monitored bi-monthly', 'mam_monitored'],
            ['  • New Admission', 'mam_new_admission'],
            ['  • Non-cured', 'mam_non_cured'],
            ['  • Defaulter', 'mam_defaulter'],
            ['  • Died', 'mam_died'],
            ['Total No. of SAM', 'total_sam'],
            ['No. of SAM monitored weekly', 'sam_monitored'],
            ['  • New Admission', 'sam_new_admission'],
            ['  • Non-cured', 'sam_non_cured'],
            ['  • Died', 'sam_died'],
            ['No. of CVD patients served', 'cvd_patients'],
            ['No. of Pregnant Women (new cases)', 'pregnant_new'],
            ['No. of Lactating Mothers (new cases)', 'lactating_new'],
            ['Families of malnourished children', 'families_malnourished'],
            ['Adolescents (10-19 years old)', 'adolescents'],
            ['Adults (20-59 years old)', 'adults'],
            ['Elderly (60 years old and above)', 'elderly_assessed'],
            ['No. of 6-11 mos infants given Vit. A', 'infants_vita'],
            ['No. of 12-59 mos PS given 2 doses Vit. A', 'children_vita'],
            ['No. of 1-4 yrs old SC given 2 doses deworming meds', 'deworm_1_4'],
            ['No. of 5-9 yrs old SC given 2 doses deworming meds', 'deworm_5_9'],
            ['No. of 10-19 yrs old SC given 2 doses deworming meds', 'deworm_10_19'],
            ['No. of monthly meeting attended', 'monthly_meetings'],
        ];
        foreach ($allRows as [$label, $field]):
            if ($field === null): // section header
        ?>
        <tr><td colspan="9" style="background:#e0e0e0;font-weight:700;text-align:left;padding:2px 4px"><?= $label ?></td></tr>
        <?php else: $val = (int)($report[$field] ?? 0); ?>
        <tr>
            <td class="act"><?= htmlspecialchars($label) ?></td>
            <!-- Jan-Jun: only current month has value, others blank -->
            <?php for ($m = 1; $m <= 6; $m++): ?>
            <td><?= ($m == $month) ? ($val ?: '') : '' ?></td>
            <?php endfor; ?>
            <td><?= $val ?: '' ?></td>
            <td></td>
        </tr>
        <?php endif; endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top:12px;display:flex;justify-content:space-between;font-size:7.5pt">
        <div>
            Prepared by: <span style="border-bottom:1px solid #000;min-width:130px;display:inline-block">&nbsp;<?= htmlspecialchars($bnsName) ?>&nbsp;</span><br>
            <span style="font-size:6.5pt">BNS</span>
        </div>
        <div>
            Noted by: <span style="border-bottom:1px solid #000;min-width:130px;display:inline-block">&nbsp;</span><br>
            <span style="font-size:6.5pt">BNS PRESIDENT</span>
        </div>
        <div>
            Approved by:
            <?php if (!empty($report['no2_signature'])): ?>
            <img src="<?= htmlspecialchars($report['no2_signature']) ?>"
                 style="max-width:120px;max-height:40px;object-fit:contain;display:block;border-bottom:1px solid #000">
            <?php else: ?>
            <span style="border-bottom:1px solid #000;min-width:130px;display:inline-block">&nbsp;</span>
            <?php endif; ?><br>
            <span style="font-size:6.5pt">NUTRITION OFFICER II</span>
        </div>
    </div>
</div>
</div>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>

<!-- ── Attachment Viewer Modal ── -->
<div id="attachViewerModal" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.65);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:.75rem;width:90vw;max-width:900px;height:88vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,.3)">
        <!-- Modal header -->
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.65rem 1rem;border-bottom:1.5px solid rgba(107,122,58,.12);background:rgba(107,122,58,.04);flex-shrink:0">
            <div style="display:flex;align-items:center;gap:.5rem">
                <i id="viewerIcon" class="bi bi-file-earmark-fill" style="font-size:1.1rem;color:#e74c3c"></i>
                <span id="viewerTitle" style="font-weight:700;font-size:.9rem;color:#3D4A1E;max-width:600px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></span>
            </div>
            <button onclick="closeAttachmentViewer()"
                    style="background:none;border:none;font-size:1.3rem;color:#888;cursor:pointer;padding:.1rem .35rem;border-radius:4px;line-height:1"
                    title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <!-- Modal body -->
        <div id="viewerBody" style="flex:1;overflow:auto;background:#f5f5f5;display:flex;align-items:center;justify-content:center">
            <!-- Content injected by JS -->
        </div>
    </div>
</div>

<script>
function openAttachmentViewer(url, filename, mimeType) {
    var modal   = document.getElementById('attachViewerModal');
    var title   = document.getElementById('viewerTitle');
    var body    = document.getElementById('viewerBody');
    var icon    = document.getElementById('viewerIcon');

    title.textContent = filename;

    // Set icon color by type
    if (mimeType === 'application/pdf') {
        icon.className = 'bi bi-file-earmark-pdf-fill';
        icon.style.color = '#e74c3c';
    } else if (mimeType.startsWith('image/')) {
        icon.className = 'bi bi-file-earmark-image-fill';
        icon.style.color = '#2980b9';
    } else {
        icon.className = 'bi bi-file-earmark-fill';
        icon.style.color = '#95a5a6';
    }

    // Render content
    if (mimeType === 'application/pdf') {
        body.innerHTML = '<iframe src="' + url + '" style="width:100%;height:100%;border:none;min-height:70vh"></iframe>';
    } else if (mimeType.startsWith('image/')) {
        body.innerHTML = '<img src="' + url + '" style="max-width:100%;max-height:80vh;object-fit:contain;padding:1rem">';
    } else {
        // Non-previewable — show download prompt
        body.innerHTML = '<div style="text-align:center;padding:3rem 2rem">'
            + '<i class="bi bi-file-earmark" style="font-size:3rem;color:#aaa;display:block;margin-bottom:1rem"></i>'
            + '<div style="font-size:.95rem;color:#555;margin-bottom:1rem">Preview not available for this file type.</div>'
            + '<a href="' + url + '" download="' + filename + '" style="display:inline-flex;align-items:center;gap:.4rem;background:#6B7A3A;color:#fff;border:none;border-radius:8px;padding:.5rem 1.25rem;font-size:.9rem;font-weight:600;text-decoration:none">'
            + '<i class="bi bi-download"></i> Download File</a>'
            + '</div>';
    }

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeAttachmentViewer() {
    var modal = document.getElementById('attachViewerModal');
    modal.style.display = 'none';
    document.getElementById('viewerBody').innerHTML = '';
    document.body.style.overflow = '';
}

// Close on backdrop click
document.getElementById('attachViewerModal').addEventListener('click', function(e) {
    if (e.target === this) closeAttachmentViewer();
});
</script>
