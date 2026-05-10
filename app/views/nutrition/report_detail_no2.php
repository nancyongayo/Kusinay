<?php
$pageTitle = 'Report Detail';
$activeNav = 'validation';
require_once __DIR__ . '/../templates/nutrition_layout.php';

$months = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
$isSubmitted = $report['status'] === 'Submitted';
?>
<style>
:root{--kn-green:#6B7A3A;--kn-orange:#C4722A;--kn-dark:#3D4A1E;--kn-muted:rgba(61,74,30,.55);}
.form-wrapper{background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:1rem;overflow:hidden;box-shadow:0 2px 12px rgba(61,74,30,.08);}
.section-header{background:rgba(107,122,58,.06);border-bottom:1.5px solid rgba(107,122,58,.1);padding:.6rem 1.25rem;font-weight:700;font-size:.82rem;color:var(--kn-green);text-transform:uppercase;letter-spacing:.05em;}
.report-row{display:flex;align-items:center;padding:.45rem 1.25rem;border-bottom:1px solid rgba(107,122,58,.07);gap:1rem;}
.report-row:last-child{border-bottom:none;}
.row-label{flex:1;font-size:.85rem;color:var(--kn-dark);}
.row-val{font-weight:700;font-size:.9rem;color:var(--kn-dark);min-width:50px;text-align:right;}
</style>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="index.php?action=reportValidation"
       style="display:inline-flex;align-items:center;gap:.4rem;background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.35rem .85rem;font-size:.88rem;font-weight:600;text-decoration:none">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    <div>
        <div class="fw-bold" style="color:var(--kn-dark)">
            <?= htmlspecialchars($bns['first_name'] . ' ' . $bns['last_name']) ?> —
            <?= $monthName ?> <?= $report['report_year'] ?>
        </div>
        <div style="font-size:.78rem;color:var(--kn-muted)">BNS Monthly Accomplishment Report</div>
    </div>
</div>

<div class="row g-4">
<div class="col-lg-8">

<div class="form-wrapper mb-3">
    <div class="section-header"><i class="bi bi-clipboard2-data me-2"></i>OPT Plus</div>
    <?php
    $rows = [
        ['No. of 0-23 mos PS weighed and taken height monthly',           'ps_0_23_weighed'],
        ['No. of 24-59 mos old PS weighed and taken height semi-annually', 'ps_24_59_weighed'],
        ['No. of 0-59 mos old malnourished PS weighed',                   'ps_malnourished'],
        ['Total No. of MAM',                                               'total_mam'],
        ['No. of MAM monitored bi-monthly',                                'mam_monitored'],
        ['MAM — New Admission',                                            'mam_new_admission'],
        ['MAM — Non-cured',                                                'mam_non_cured'],
        ['MAM — Defaulter',                                                'mam_defaulter'],
        ['MAM — Died',                                                     'mam_died'],
        ['Total No. of SAM',                                               'total_sam'],
        ['No. of SAM monitored weekly',                                    'sam_monitored'],
        ['SAM — New Admission',                                            'sam_new_admission'],
        ['SAM — Non-cured',                                                'sam_non_cured'],
        ['SAM — Died',                                                     'sam_died'],
    ];
    foreach ($rows as [$label, $field]):
    ?>
    <div class="report-row">
        <div class="row-label"><?= htmlspecialchars($label) ?></div>
        <div class="row-val"><?= (int)($report[$field] ?? 0) ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div class="form-wrapper mb-3">
    <div class="section-header"><i class="bi bi-activity me-2"></i>Other Activities</div>
    <?php
    $rows2 = [
        ['No. of CVD patients served',                           'cvd_patients'],
        ['No. of Pregnant Women (new cases)',                    'pregnant_new'],
        ['No. of Lactating Mothers (new cases)',                 'lactating_new'],
        ['Families of malnourished children',                    'families_malnourished'],
        ['Adolescents (10-19 years old)',                        'adolescents'],
        ['Adults (20-59 years old)',                             'adults'],
        ['Elderly (60 years old and above)',                     'elderly_assessed'],
        ['No. of 6-11 mos infants given Vit. A',                'infants_vita'],
        ['No. of 12-59 mos PS given 2 doses Vit. A',            'children_vita'],
        ['No. of 1-4 yrs old SC given 2 doses deworming meds',  'deworm_1_4'],
        ['No. of 5-9 yrs old SC given 2 doses deworming meds',  'deworm_5_9'],
        ['No. of 10-19 yrs old SC given 2 doses deworming meds','deworm_10_19'],
        ['No. of monthly meeting attended',                      'monthly_meetings'],
    ];
    foreach ($rows2 as [$label, $field]):
    ?>
    <div class="report-row">
        <div class="row-label"><?= htmlspecialchars($label) ?></div>
        <div class="row-val"><?= (int)($report[$field] ?? 0) ?></div>
    </div>
    <?php endforeach; ?>
    <?php if ($report['remarks']): ?>
    <div style="padding:.75rem 1.25rem;font-size:.85rem">
        <strong>Remarks:</strong> <?= htmlspecialchars($report['remarks']) ?>
    </div>
    <?php endif; ?>
</div>

<!-- Attachments (read-only for NO II) -->
<?php if (!empty($attachments)): ?>
<div class="form-wrapper mb-3">
    <div class="section-header"><i class="bi bi-paperclip me-2"></i>Supporting Attachments</div>
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
        $imgBase = htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
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
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

</div>

<!-- Right: Actions -->
<div class="col-lg-4">
    <?php if ($isSubmitted): ?>
    <!-- Approve with e-signature -->
    <div class="card shadow-sm border-success mb-3">
        <div class="card-header fw-semibold text-success"><i class="bi bi-patch-check-fill me-2"></i>Approve Report</div>
        <div class="card-body">
            <p class="text-muted small mb-3">Draw your signature below, then click Approve to finalize this report.</p>

            <!-- Signature pad -->
            <div style="border:1.5px solid rgba(107,122,58,.3);border-radius:8px;overflow:hidden;margin-bottom:.75rem;background:#fafafa">
                <canvas id="sigCanvas" width="320" height="120"
                        style="display:block;cursor:crosshair;touch-action:none;width:100%;height:120px"></canvas>
            </div>
            <div class="d-flex gap-2 mb-3">
                <button type="button" onclick="clearSig()"
                        style="background:#fff;color:var(--kn-muted);border:1.5px solid rgba(107,122,58,.2);border-radius:7px;padding:.25rem .75rem;font-size:.8rem;cursor:pointer">
                    <i class="bi bi-eraser me-1"></i> Clear
                </button>
                <span style="font-size:.75rem;color:var(--kn-muted);align-self:center">Sign above using mouse or touch</span>
            </div>

            <form method="POST" action="index.php?action=approveReport" id="approveForm">
                <input type="hidden" name="report_id" value="<?= (int)$report['report_id'] ?>">
                <input type="hidden" name="signature" id="sigData">
                <button type="submit" class="btn btn-success w-100 fw-semibold"
                        onclick="return submitApproval()">
                    <i class="bi bi-patch-check-fill me-2"></i> Approve with Signature
                </button>
            </form>
        </div>
    </div>

    <script>
    // ── Signature Pad ──────────────────────────────────────────────────────────
    const canvas  = document.getElementById('sigCanvas');
    const ctx     = canvas.getContext('2d');
    let drawing   = false;
    let lastX = 0, lastY = 0;

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width  / rect.width;
        const scaleY = canvas.height / rect.height;
        if (e.touches) {
            return {
                x: (e.touches[0].clientX - rect.left) * scaleX,
                y: (e.touches[0].clientY - rect.top)  * scaleY
            };
        }
        return {
            x: (e.clientX - rect.left) * scaleX,
            y: (e.clientY - rect.top)  * scaleY
        };
    }

    ctx.strokeStyle = '#1a3a6b';
    ctx.lineWidth   = 2;
    ctx.lineCap     = 'round';
    ctx.lineJoin    = 'round';

    canvas.addEventListener('mousedown',  e => { drawing = true; const p = getPos(e); lastX = p.x; lastY = p.y; });
    canvas.addEventListener('mousemove',  e => {
        if (!drawing) return;
        const p = getPos(e);
        ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(p.x, p.y); ctx.stroke();
        lastX = p.x; lastY = p.y;
    });
    canvas.addEventListener('mouseup',   () => drawing = false);
    canvas.addEventListener('mouseleave',() => drawing = false);

    canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing = true; const p = getPos(e); lastX = p.x; lastY = p.y; }, {passive:false});
    canvas.addEventListener('touchmove',  e => {
        e.preventDefault();
        if (!drawing) return;
        const p = getPos(e);
        ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(p.x, p.y); ctx.stroke();
        lastX = p.x; lastY = p.y;
    }, {passive:false});
    canvas.addEventListener('touchend',  () => drawing = false);

    function clearSig() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    function isCanvasBlank() {
        const data = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
        return !data.some(v => v !== 0);
    }

    function submitApproval() {
        if (isCanvasBlank()) {
            alert('Please draw your signature before approving.');
            return false;
        }
        document.getElementById('sigData').value = canvas.toDataURL('image/png');
        return confirm('Approve this report with your e-signature?');
    }
    </script>

    <!-- Return -->
    <div class="card shadow-sm" style="border:1.5px solid rgba(196,114,42,.35)">
        <div class="card-header fw-semibold" style="color:var(--kn-orange);background:rgba(196,114,42,.06)">
            <i class="bi bi-arrow-counterclockwise me-2"></i>Return for Correction
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?action=returnReport">
                <input type="hidden" name="report_id" value="<?= (int)$report['report_id'] ?>">
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.88rem">Reason <span class="text-danger">*</span></label>
                    <textarea name="return_reason" class="form-control" rows="3"
                              style="border:1.5px solid rgba(196,114,42,.3);border-radius:8px"
                              placeholder="e.g. Incorrect count for MAM, missing data…" required></textarea>
                </div>
                <button type="submit" class="btn w-100 fw-semibold"
                        style="background:rgba(196,114,42,.1);color:var(--kn-orange);border:1.5px solid rgba(196,114,42,.35)"
                        onclick="return confirm('Return this report for correction?')">
                    <i class="bi bi-arrow-counterclockwise me-2"></i> Return to BNS
                </button>
            </form>
        </div>
    </div>

    <?php elseif ($report['status'] === 'Approved'): ?>
    <div class="alert alert-success">
        <i class="bi bi-patch-check-fill me-2"></i>
        Approved on <?= date('M j, Y g:i A', strtotime($report['reviewed_at'])) ?>.
    </div>
    <?php if (!empty($report['no2_signature'])): ?>
    <div style="background:#fff;border:1.5px solid rgba(107,122,58,.2);border-radius:.75rem;padding:.75rem;text-align:center">
        <div style="font-size:.75rem;color:var(--kn-muted);margin-bottom:.4rem;font-weight:600">E-SIGNATURE OF NUTRITION OFFICER II</div>
        <img src="<?= htmlspecialchars($report['no2_signature']) ?>"
             style="max-width:200px;max-height:80px;object-fit:contain;border-bottom:1.5px solid #333;padding-bottom:4px">
        <div style="font-size:.72rem;color:var(--kn-muted);margin-top:.3rem"><?= htmlspecialchars($userName) ?></div>
        <div style="font-size:.7rem;color:var(--kn-muted)">Nutrition Officer II</div>
    </div>
    <?php endif; ?>

    <?php elseif ($report['status'] === 'Returned'): ?>
    <div class="card shadow-sm" style="border:1.5px solid rgba(196,114,42,.35)">
        <div class="card-header fw-semibold" style="color:var(--kn-orange);background:rgba(196,114,42,.06)">
            <i class="bi bi-arrow-counterclockwise me-2"></i>Returned for Correction
        </div>
        <div class="card-body">
            <p class="text-muted small mb-1">Reason given:</p>
            <p class="mb-0"><?= nl2br(htmlspecialchars($report['return_reason'] ?? '—')) ?></p>
        </div>
    </div>
    <?php endif; ?>
</div>
</div>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>

<!-- ── Attachment Viewer Modal ── -->
<div id="attachViewerModal" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.65);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:.75rem;width:90vw;max-width:900px;height:88vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,.3)">
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
        <div id="viewerBody" style="flex:1;overflow:auto;background:#f5f5f5;display:flex;align-items:center;justify-content:center"></div>
    </div>
</div>
<script>
function openAttachmentViewer(url, filename, mimeType) {
    var modal = document.getElementById('attachViewerModal');
    var title = document.getElementById('viewerTitle');
    var body  = document.getElementById('viewerBody');
    var icon  = document.getElementById('viewerIcon');
    title.textContent = filename;
    if (mimeType === 'application/pdf') {
        icon.className = 'bi bi-file-earmark-pdf-fill'; icon.style.color = '#e74c3c';
        body.innerHTML = '<iframe src="' + url + '" style="width:100%;height:100%;border:none;min-height:70vh"></iframe>';
    } else if (mimeType.startsWith('image/')) {
        icon.className = 'bi bi-file-earmark-image-fill'; icon.style.color = '#2980b9';
        body.innerHTML = '<img src="' + url + '" style="max-width:100%;max-height:80vh;object-fit:contain;padding:1rem">';
    } else {
        icon.className = 'bi bi-file-earmark-fill'; icon.style.color = '#95a5a6';
        body.innerHTML = '<div style="text-align:center;padding:3rem 2rem"><i class="bi bi-file-earmark" style="font-size:3rem;color:#aaa;display:block;margin-bottom:1rem"></i><div style="font-size:.95rem;color:#555;margin-bottom:1rem">Preview not available.</div><a href="' + url + '" download="' + filename + '" style="display:inline-flex;align-items:center;gap:.4rem;background:#6B7A3A;color:#fff;border:none;border-radius:8px;padding:.5rem 1.25rem;font-size:.9rem;font-weight:600;text-decoration:none"><i class="bi bi-download"></i> Download File</a></div>';
    }
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeAttachmentViewer() {
    document.getElementById('attachViewerModal').style.display = 'none';
    document.getElementById('viewerBody').innerHTML = '';
    document.body.style.overflow = '';
}
document.getElementById('attachViewerModal').addEventListener('click', function(e) {
    if (e.target === this) closeAttachmentViewer();
});
</script>
