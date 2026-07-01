<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meeting Minutes — <?= date('F j, Y', strtotime($minute['meeting_date'])) ?></title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
            padding-top: 0;
            margin: 0;
        }

        /* ── Toolbar (hidden) ── */
        .toolbar {
            display: none !important;
        }

        /* ── Page ── */
        .page {
            width: 8.5in;
            min-height: 11in;
            margin: 0 auto;
            padding: .75in .9in;
            background: #fff;
            box-shadow: none;
        }

        /* ── Letterhead ── */
        .letterhead {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            border-bottom: 3px double #000;
            padding-bottom: .5rem;
            margin-bottom: 1.2rem;
        }
        .lh-logo {
            width: 68px; height: 68px;
            border: 2px solid #8B4513;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 8pt; text-align: center;
            color: #8B4513; font-weight: bold; flex-shrink: 0;
        }
        .lh-text { text-align: center; flex: 1; }
        .lh-text .rep  { font-size: 10pt; }
        .lh-text .city { font-size: 11pt; font-weight: bold; }
        .lh-text .brgy { font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .lh-text .off  { font-size: 10pt; font-weight: bold; text-transform: uppercase; }

        /* ── Doc title ── */
        .doc-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: .8rem 0 1rem;
        }

        /* ── Header info block ── */
        .header-block {
            margin-bottom: 1rem;
            line-height: 1.8;
        }
        .header-block .row {
            display: flex;
            gap: .5rem;
        }
        .header-block .lbl {
            font-weight: bold;
            min-width: 60px;
        }

        /* ── Section ── */
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            margin: 1rem 0 .4rem;
        }

        /* ── Attendance list ── */
        .attendance-list {
            list-style: disc;
            padding-left: 1.5rem;
            margin-bottom: .5rem;
        }
        .attendance-list li {
            margin-bottom: .2rem;
            font-size: 11pt;
        }

        /* ── Body text ── */
        .body-text {
            font-size: 11pt;
            line-height: 1.7;
            text-align: justify;
            margin-bottom: .4rem;
        }

        /* ── Signature ── */
        .sig-section {
            margin-top: 2.5rem;
            display: flex;
            gap: 2rem;
        }
        .sig-block { flex: 1; }
        .sig-label { font-size: 11pt; margin-bottom: .8rem; }
        .sig-name {
            font-size: 11pt;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: .2rem;
            display: inline-block;
            min-width: 180px;
        }
        .sig-title { font-size: 10.5pt; }

        @media print {
            .toolbar { display: none !important; }
            body { background: #fff !important; padding-top: 0 !important; }
            .page {
                width: 100%; margin: 0;
                padding: .6in .75in;
                box-shadow: none !important;
            }
            @page { size: letter portrait; margin: 0; }
        }
    </style>
</head>
<body>

<script>
// Auto-trigger print dialog when page loads
window.addEventListener('load', function() {
    setTimeout(function() {
        window.print();
    }, 100);
});
</script>

<!-- Toolbar -->
<div class="toolbar">
    <div class="doc-info">
        <div class="title">📋 Meeting Minutes — <?= date('F j, Y', strtotime($minute['meeting_date'])) ?></div>
        <div class="sub"><?= htmlspecialchars($minute['agenda']) ?></div>
    </div>
    <div class="actions">
        <a href="javascript:window.close()" class="btn-close">✕ Close</a>
        <button class="btn-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
    </div>
</div>

<?php
$attendees = json_decode($minute['attendees'] ?? '[]', true) ?? [];
$barangay  = 'Bayabas'; // fallback — ideally from session/config
?>

<!-- Page -->
<div class="page">

    <!-- Letterhead -->
    <div class="letterhead">
        <div class="lh-logo">BRGY<br>SEAL</div>
        <div class="lh-text">
            <div class="rep">Republic of the Philippines</div>
            <div class="city">City of Davao</div>
            <div class="brgy">Barangay <?= htmlspecialchars($barangay) ?></div>
            <div class="off">Office of the Sangguniang Barangay</div>
        </div>
        <div class="lh-logo">NNC<br>LOGO</div>
    </div>

    <!-- Document Title -->
    <div class="doc-title">Minutes of the Meeting</div>

    <!-- Header Info Block -->
    <div class="header-block">
        <div class="row">
            <span class="lbl">About:</span>
            <span><?= htmlspecialchars($minute['agenda']) ?></span>
        </div>
        <div class="row">
            <span class="lbl">When:</span>
            <span>
                <?= date('F j, Y', strtotime($minute['meeting_date'])) ?>
                &nbsp;&nbsp;
                <?= date('g:i A', strtotime($minute['meeting_time'])) ?>
            </span>
        </div>
        <div class="row">
            <span class="lbl">Where:</span>
            <span><?= htmlspecialchars($minute['venue']) ?></span>
        </div>
        <?php if (!empty($attendees)): ?>
        <div class="row">
            <span class="lbl">Who:</span>
            <span>
                <?php
                $names = array_map(fn($a) => htmlspecialchars($a['role'] ?? ''), $attendees);
                echo implode(', ', $names);
                ?>
            </span>
        </div>
        <?php endif; ?>
        <?php if (!empty($minute['meeting_time'])): ?>
        <div class="row">
            <span class="lbl">Time:</span>
            <span><?= date('g:i A', strtotime($minute['meeting_time'])) ?></span>
        </div>
        <?php endif; ?>
    </div>

    <!-- I. Attendance -->
    <?php if (!empty($attendees)): ?>
    <div class="section-title">I. Attendance</div>
    <ul class="attendance-list">
        <?php foreach ($attendees as $att): ?>
            <li>
                <strong><?= htmlspecialchars($att['name'] ?? '') ?></strong>
                — <?= htmlspecialchars($att['role'] ?? '') ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <!-- II. Agenda / Discussion -->
    <div class="section-title">II. Agenda</div>
    <?php foreach (explode("\n", $minute['discussion_summary'] ?? '') as $para): ?>
        <?php if (trim($para)): ?>
            <p class="body-text"><?= htmlspecialchars(trim($para)) ?></p>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- III. Main Points / Decisions -->
    <div class="section-title">III. Main Points / Decisions Made</div>
    <?php foreach (explode("\n", $minute['decisions_made'] ?? '') as $para): ?>
        <?php if (trim($para)): ?>
            <p class="body-text"><?= htmlspecialchars(trim($para)) ?></p>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- IV. Action Items -->
    <?php if (!empty($minute['action_items'])): ?>
    <div class="section-title">IV. Action Items</div>
    <?php foreach (explode("\n", $minute['action_items']) as $para): ?>
        <?php if (trim($para)): ?>
            <p class="body-text"><?= htmlspecialchars(trim($para)) ?></p>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php endif; ?>

    <!-- Next Meeting -->
    <?php if (!empty($minute['next_meeting_date'])): ?>
    <p class="body-text" style="margin-top:.8rem">
        <strong>Next Meeting:</strong> <?= date('F j, Y', strtotime($minute['next_meeting_date'])) ?>
    </p>
    <?php endif; ?>

    <!-- Signatures -->
    <div class="sig-section">
        <div class="sig-block">
            <div class="sig-label"><strong>Prepared by:</strong></div>
            <br><br>
            <div class="sig-name">
                <?= htmlspecialchars($minute['recorder_first_name'] . ' ' . $minute['recorder_last_name']) ?>
            </div>
            <div class="sig-title">Committee Secretary</div>
        </div>

        <div class="sig-block">
            <div class="sig-label"><strong>Approved by:</strong></div>
            <br><br>
            <div class="sig-name">_______________________________</div>
            <div class="sig-title">Chairperson, Punong Barangay</div>
        </div>
    </div>

</div><!-- end page -->

</body>
</html>
