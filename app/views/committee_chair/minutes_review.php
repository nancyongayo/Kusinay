<?php
/**
 * Committee Chair reviews meeting minutes from Secretary
 * Then decides whether to create a feeding program proposal
 */
$pageTitle = $pageTitle ?? 'Review Meeting Minutes';
$activeNav = 'minutes_review';
include __DIR__ . '/../templates/committee_chair_layout.php';
include __DIR__ . '/../templates/button_styles.php';
?>

<style>
/* Print-only content - hidden on screen */
.print-only { display: none !important; }

@media print {
    /* Hide screen content */
    .kn-sidebar, .kn-topbar, nav, .btn, button, a.btn, form,
    .d-flex.gap-2, .alert, .card, .border-bottom, .border-top,
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
        <h4 class="mb-1" style="color:var(--kn-dark);font-weight:700">📋 Review Meeting Minutes</h4>
        <p class="text-muted mb-0" style="font-size:.9rem">
            Recorded by <?= htmlspecialchars($minute['recorder_first_name'] . ' ' . $minute['recorder_last_name']) ?>
            on <?= date('F j, Y', strtotime($minute['meeting_date'])) ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?action=committeeChairDashboard" class="btn-kn-outline btn-kn-sm">
            <i class="bi bi-arrow-left"></i>Back
        </a>
        <button type="button" onclick="window.print()"
                class="btn-kn-outline btn-kn-sm">
            <i class="bi bi-printer-fill"></i>Print / Save PDF
        </button>
        <?php if (!$minute['is_reviewed']): ?>
        <form method="POST" action="index.php?action=markMinutesReviewed" class="d-inline">
            <?= \Security::csrfField() ?>
            <input type="hidden" name="minute_id" value="<?= $minute['minute_id'] ?>">
            <button type="submit" class="btn-kn-success btn-kn-sm">
                <i class="bi bi-check-circle"></i>Mark as Reviewed
            </button>
        </form>
        <?php endif; ?>
        <a href="index.php?action=proposalForm&from_minute=<?= $minute['minute_id'] ?>"
           class="btn-kn-secondary btn-kn-sm">
            <i class="bi bi-plus-circle-fill"></i>Create Proposal from These Minutes
        </a>
    </div>
</div>

<?php if ($minute['is_reviewed']): ?>
<div class="alert alert-success mb-4">
    <i class="bi bi-check-circle-fill me-2"></i>You have already reviewed these minutes.
    <?php if ($proposal): ?>
        A proposal has been created: <a href="index.php?action=viewProposal&proposal_id=<?= $proposal['proposal_id'] ?>">
            <?= htmlspecialchars($proposal['proposal_title']) ?>
        </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <!-- Header info -->
        <div class="row g-3 mb-4 pb-3 border-bottom">
            <div class="col-md-5">
                <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;font-weight:600">Meeting About</div>
                <div style="font-weight:700;font-size:1.05rem"><?= htmlspecialchars($minute['agenda']) ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;font-weight:600">When</div>
                <div><?= date('F j, Y', strtotime($minute['meeting_date'])) ?></div>
                <div class="text-muted"><?= date('g:i A', strtotime($minute['meeting_time'])) ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;font-weight:600">Where</div>
                <div><?= htmlspecialchars($minute['venue']) ?></div>
            </div>
        </div>

        <!-- Attendance -->
        <?php if (!empty($minute['attendees'])): ?>
        <div class="mb-4">
            <h6 style="font-weight:700;color:var(--kn-dark)">I. Attendance</h6>
            <?php $attendees = json_decode($minute['attendees'], true) ?? []; ?>
            <ul class="mb-0" style="line-height:2">
                <?php foreach ($attendees as $att): ?>
                    <li>
                        <strong><?= htmlspecialchars($att['name']) ?></strong>
                        — <?= htmlspecialchars($att['role']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Discussion -->
        <div class="mb-4">
            <h6 style="font-weight:700;color:var(--kn-dark)">II. Discussion Summary</h6>
            <p style="line-height:1.8"><?= nl2br(htmlspecialchars($minute['discussion_summary'])) ?></p>
        </div>

        <!-- Decisions -->
        <div class="mb-4">
            <h6 style="font-weight:700;color:var(--kn-dark)">III. Decisions Made</h6>
            <p style="line-height:1.8"><?= nl2br(htmlspecialchars($minute['decisions_made'])) ?></p>
        </div>

        <!-- Action Items -->
        <?php if ($minute['action_items']): ?>
        <div class="mb-4">
            <h6 style="font-weight:700;color:var(--kn-dark)">IV. Action Items</h6>
            <p style="line-height:1.8"><?= nl2br(htmlspecialchars($minute['action_items'])) ?></p>
        </div>
        <?php endif; ?>

        <div class="border-top pt-3 text-muted" style="font-size:.85rem">
            Prepared by: <strong><?= htmlspecialchars($minute['recorder_first_name'] . ' ' . $minute['recorder_last_name']) ?></strong>
            (Committee Secretary)
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
            <div style="font-size:11pt;margin-bottom:.8rem"><strong>Approved by:</strong></div>
            <br><br>
            <div style="font-size:11pt;font-weight:bold;border-top:1px solid #000;padding-top:.2rem;display:inline-block;min-width:180px">_______________________________</div>
            <div style="font-size:10.5pt">Chairperson, Punong Barangay</div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/committee_chair_layout_end.php'; ?>
