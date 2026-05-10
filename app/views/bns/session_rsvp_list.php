<?php
$pageTitle = 'Confirmed Attendees';
$activeNav = 'nutrition_education';
require_once __DIR__ . '/../templates/bns_layout.php';
?>

<div class="mb-3">
    <a href="index.php?action=nutritionEducationList" class="text-decoration-none" style="color:var(--kn-green);font-size:.9rem">
        <i class="bi bi-arrow-left"></i> Back to Sessions
    </a>
</div>

<!-- Session Info -->
<div style="background:rgba(107,122,58,.06);border:1.5px solid rgba(107,122,58,.2);border-radius:10px;padding:1.25rem;margin-bottom:1.5rem">
    <div style="font-weight:700;font-size:1.05rem;color:var(--kn-dark);margin-bottom:.4rem">
        <?= htmlspecialchars($session['session_title']) ?>
    </div>
    <div style="font-size:.88rem;color:var(--kn-muted);display:flex;gap:1.25rem;flex-wrap:wrap">
        <span><i class="bi bi-calendar3 me-1"></i><?= date('F j, Y', strtotime($session['session_date'])) ?></span>
        <span><i class="bi bi-clock me-1"></i><?= date('g:i A', strtotime($session['session_time'])) ?></span>
        <span><i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($session['venue']) ?></span>
        <?php if ($session['target_group']): ?>
        <span><i class="bi bi-people-fill me-1"></i><?= htmlspecialchars($session['target_group']) ?></span>
        <?php endif; ?>
    </div>
</div>

<!-- RSVP List -->
<div style="background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:10px;overflow:hidden">
    <div style="padding:.85rem 1.25rem;border-bottom:1.5px solid rgba(107,122,58,.1);display:flex;align-items:center;justify-content:space-between">
        <div style="font-weight:700;color:var(--kn-dark)">
            <i class="bi bi-calendar-check-fill me-2" style="color:var(--kn-green)"></i>
            Confirmed Attendees
        </div>
        <span style="background:rgba(107,122,58,.1);color:var(--kn-green);font-size:.82rem;font-weight:700;padding:.2rem .65rem;border-radius:6px">
            <?= count($rsvpList) ?> confirmed
        </span>
    </div>

    <?php if (empty($rsvpList)): ?>
    <div style="text-align:center;padding:3rem 1rem;color:var(--kn-muted)">
        <i class="bi bi-calendar-x" style="font-size:2.5rem;display:block;margin-bottom:.75rem;opacity:.4"></i>
        No one has confirmed attendance yet.
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr style="background:rgba(107,122,58,.06)">
                    <th style="border:none;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--kn-muted);padding:.65rem 1rem">#</th>
                    <th style="border:none;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--kn-muted)">Name</th>
                    <th style="border:none;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--kn-muted)">Confirmed At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rsvpList as $i => $r): ?>
                <tr>
                    <td style="padding:.65rem 1rem;color:var(--kn-muted);font-size:.88rem"><?= $i + 1 ?></td>
                    <td style="font-weight:600"><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
                    <td style="font-size:.85rem;color:var(--kn-muted)"><?= date('M j, Y g:i A', strtotime($r['rsvp_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="padding:.65rem 1.25rem;border-top:1px solid rgba(107,122,58,.08);font-size:.78rem;color:var(--kn-muted)">
        <i class="bi bi-info-circle me-1"></i>
        This list shows who plans to attend. Official attendance is recorded separately during the session.
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
