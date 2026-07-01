<?php
/**
 * View a single set of meeting minutes (Barangay Captain view - read-only)
 */
$pageTitle = $pageTitle ?? 'Meeting Minutes';
$activeNav = 'dashboard';
include __DIR__ . '/../templates/barangay_captain_layout.php';
include __DIR__ . '/../templates/button_styles.php';
?>

<style>
/* Print-only content - hidden on screen */
.print-only { display: none !important; }

@media print {
    /* Hide screen content */
    .kn-sidebar, .kn-topbar, nav, .btn, button, a.btn,
    .d-flex.gap-2, .card, .border-top, .badge,
    .d-flex.justify-content-between, h4, p.text-muted {
        display: none !important;
    }
    
    body {
        font-family: 'Times New Roman', Times, serif !important;
        font-size: 11pt !important;
        background: white !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .kn-main, .kn-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    
    /* Show print content */
    .print-only { display: block !important; }
    
    @page { size: letter portrait; margin: 0; }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1" style="color:var(--kn-dark);font-weight:700">📋 Meeting Minutes</h4>
        <p class="text-muted mb-0"><?= date('F j, Y', strtotime($minute['meeting_date'])) ?></p>
        <small class="text-muted">View-only access for transparency</small>
    </div>
    <div class="d-flex gap-2">
        <button type="button" onclick="window.print()"
                class="btn-kn-outline btn-kn-sm">
            <i class="bi bi-printer-fill"></i>Print / Save PDF
        </button>
        <a href="index.php?action=captainDashboard" class="btn-kn-outline btn-kn-sm">
            <i class="bi bi-arrow-left"></i>Back
        </a>
    </div>
</div>

<div class="alert alert-info mb-4">
    <i class="bi bi-info-circle-fill me-2"></i>
    <strong>View-Only Access:</strong> These minutes are for your information and transparency. 
    You will validate the feeding program proposal that results from these meetings.
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;font-weight:600">Meeting About</div>
                <div style="font-weight:600"><?= htmlspecialchars($minute['agenda']) ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;font-weight:600">Date & Time</div>
                <div><?= date('F j, Y', strtotime($minute['meeting_date'])) ?></div>
                <div><?= date('g:i A', strtotime($minute['meeting_time'])) ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;font-weight:600">Venue</div>
                <div><?= htmlspecialchars($minute['venue']) ?></div>
            </div>
        </div>

        <?php if (!empty($minute['attendees'])): ?>
        <div class="mb-4">
            <div class="text-muted mb-2" style="font-size:.75rem;text-transform:uppercase;font-weight:600">I. Attendance</div>
            <?php $attendees = json_decode($minute['attendees'], true) ?? []; ?>
            <ul class="mb-0">
                <?php foreach ($attendees as $att): ?>
                    <li><?= htmlspecialchars($att['name']) ?> — <em><?= htmlspecialchars($att['role']) ?></em></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="mb-4">
            <div class="text-muted mb-2" style="font-size:.75rem;text-transform:uppercase;font-weight:600">II. Discussion Summary</div>
            <p style="line-height:1.7"><?= nl2br(htmlspecialchars($minute['discussion_summary'])) ?></p>
        </div>

        <div class="mb-4">
            <div class="text-muted mb-2" style="font-size:.75rem;text-transform:uppercase;font-weight:600">III. Decisions Made</div>
            <p style="line-height:1.7"><?= nl2br(htmlspecialchars($minute['decisions_made'])) ?></p>
        </div>

        <?php if ($minute['action_items']): ?>
        <div class="mb-4">
            <div class="text-muted mb-2" style="font-size:.75rem;text-transform:uppercase;font-weight:600">IV. Action Items</div>
            <p style="line-height:1.7"><?= nl2br(htmlspecialchars($minute['action_items'])) ?></p>
        </div>
        <?php endif; ?>

        <div class="border-top pt-3 mt-3 text-muted" style="font-size:.85rem">
            Prepared by: <strong><?= htmlspecialchars($minute['recorder_first_name'] . ' ' . $minute['recorder_last_name']) ?></strong> (Committee Secretary)
            &nbsp;•&nbsp;
            Status: <?= $minute['is_reviewed'] ? '<span class="badge bg-success">Reviewed by Chair</span>' : '<span class="badge bg-warning text-dark">Pending Chair Review</span>' ?>
        </div>
    </div>
</div>

<!-- Print-only formal document -->
<div class="print-only" style="padding:.75in .9in;font-family:'Times New Roman',Times,serif;font-size:11pt">
    <?php
    $attendees = json_decode($minute['attendees'] ?? '[]', true) ?? [];
    $barangay  = 'Bayabas';
    ?>
    
    <!-- Letterhead -->
    <div style="display:flex;align-items:center;justify-content:center;gap:1rem;border-bottom:3px double #000;padding-bottom:.5rem;margin-bottom:1.2rem">
        <div style="width:68px;height:68px;border:2px solid #8B4513;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:8pt;text-align:center;color:#8B4513;font-weight:bold">BRGY<br>SEAL</div>
        <div style="text-align:center;flex:1">
            <div style="font-size:10pt">Republic of the Philippines</div>
            <div style="font-size:11pt;font-weight:bold">City of Davao</div>
            <div style="font-size:14pt;font-weight:bold;text-transform:uppercase">Barangay <?= htmlspecialchars($barangay) ?></div>
            <div style="font-size:10pt;font-weight:bold;text-transform:uppercase">Office of the Sangguniang Barangay</div>
        </div>
        <div style="width:68px;height:68px;border:2px solid #8B4513;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:8pt;text-align:center;color:#8B4513;font-weight:bold">NNC<br>LOGO</div>
    </div>
    
    <!-- Title -->
    <div style="text-align:center;font-size:12pt;font-weight:bold;text-transform:uppercase;text-decoration:underline;margin:.8rem 0 1rem">Minutes of the Meeting</div>
    
    <!-- Header Info -->
    <div style="margin-bottom:1rem;line-height:1.8">
        <div><strong>About:</strong> <?= htmlspecialchars($minute['agenda']) ?></div>
        <div><strong>When:</strong> <?= date('F j, Y', strtotime($minute['meeting_date'])) ?> <?= date('g:i A', strtotime($minute['meeting_time'])) ?></div>
        <div><strong>Where:</strong> <?= htmlspecialchars($minute['venue']) ?></div>
    </div>
    
    <!-- I. Attendance -->
    <?php if (!empty($attendees)): ?>
    <div style="font-size:11pt;font-weight:bold;margin:1rem 0 .4rem">I. Attendance</div>
    <ul style="list-style:disc;padding-left:1.5rem;margin-bottom:.5rem">
        <?php foreach ($attendees as $att): ?>
            <li style="margin-bottom:.2rem"><strong><?= htmlspecialchars($att['name'] ?? '') ?></strong> — <?= htmlspecialchars($att['role'] ?? '') ?></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    
    <!-- II. Agenda -->
    <div style="font-size:11pt;font-weight:bold;margin:1rem 0 .4rem">II. Agenda</div>
    <?php foreach (explode("\n", $minute['discussion_summary'] ?? '') as $para): ?>
        <?php if (trim($para)): ?>
            <p style="font-size:11pt;line-height:1.7;text-align:justify;margin-bottom:.4rem"><?= htmlspecialchars(trim($para)) ?></p>
        <?php endif; ?>
    <?php endforeach; ?>
    
    <!-- III. Decisions -->
    <div style="font-size:11pt;font-weight:bold;margin:1rem 0 .4rem">III. Main Points / Decisions Made</div>
    <?php foreach (explode("\n", $minute['decisions_made'] ?? '') as $para): ?>
        <?php if (trim($para)): ?>
            <p style="font-size:11pt;line-height:1.7;text-align:justify;margin-bottom:.4rem"><?= htmlspecialchars(trim($para)) ?></p>
        <?php endif; ?>
    <?php endforeach; ?>
    
    <!-- IV. Action Items -->
    <?php if (!empty($minute['action_items'])): ?>
    <div style="font-size:11pt;font-weight:bold;margin:1rem 0 .4rem">IV. Action Items</div>
    <?php foreach (explode("\n", $minute['action_items']) as $para): ?>
        <?php if (trim($para)): ?>
            <p style="font-size:11pt;line-height:1.7;text-align:justify;margin-bottom:.4rem"><?= htmlspecialchars(trim($para)) ?></p>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Signatures -->
    <div style="margin-top:2.5rem;display:flex;gap:2rem">
        <div style="flex:1">
            <div style="font-size:11pt;margin-bottom:.8rem"><strong>Prepared by:</strong></div>
            <?php if (!empty($minute['signature_data'])): ?>
                <div style="margin-bottom:.5rem">
                    <img src="<?= htmlspecialchars($minute['signature_data']) ?>" 
                         alt="Secretary Signature" 
                         style="max-width:200px;height:auto;border-bottom:1px solid #000">
                </div>
            <?php else: ?>
                <br><br>
            <?php endif; ?>
            <div style="font-size:11pt;font-weight:bold;<?= empty($minute['signature_data']) ? 'border-top:1px solid #000;padding-top:.2rem;' : '' ?>display:inline-block;min-width:180px">
                <?= htmlspecialchars($minute['recorder_first_name'] . ' ' . $minute['recorder_last_name']) ?>
            </div>
            <div style="font-size:10.5pt">Committee Secretary</div>
        </div>
        <div style="flex:1">
            <div style="font-size:11pt;margin-bottom:.8rem"><strong>Noted by:</strong></div>
            <br><br>
            <div style="font-size:11pt;font-weight:bold;border-top:1px solid #000;padding-top:.2rem;display:inline-block;min-width:180px">_______________________________</div>
            <div style="font-size:10.5pt">Punong Barangay</div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/barangay_captain_layout_end.php'; ?>
