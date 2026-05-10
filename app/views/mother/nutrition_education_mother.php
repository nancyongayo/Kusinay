<?php
$pageTitle = 'Nutrition Education';
$activeNav = 'nutrition_education';
require_once __DIR__ . '/../templates/mother_layout.php';
?>
<style>
:root{--kn-green:#6B7A3A;--kn-orange:#C4722A;--kn-dark:#3D4A1E;}
.session-card{background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:10px;padding:1.5rem;margin-bottom:1.2rem;transition:.2s;}
.session-card:hover{border-color:var(--kn-green);box-shadow:0 2px 8px rgba(107,122,58,.1);}
.session-title{font-size:1.1rem;font-weight:700;color:var(--kn-dark);margin-bottom:.5rem;}
.session-date{font-size:.95rem;color:var(--kn-green);font-weight:600;margin-bottom:.8rem;}
.session-meta{display:flex;gap:1.2rem;flex-wrap:wrap;font-size:.85rem;color:#666;margin-bottom:.8rem;}
.session-meta i{color:var(--kn-green);}
.badge-upcoming{background:rgba(107,122,58,.1);color:var(--kn-green);padding:.3rem .7rem;border-radius:5px;font-size:.8rem;font-weight:700;}
.badge-attended{background:rgba(40,167,69,.1);color:#28a745;padding:.3rem .7rem;border-radius:5px;font-size:.8rem;font-weight:700;}
.section-title{font-size:1.3rem;font-weight:700;color:var(--kn-dark);margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;}
.section-title i{color:var(--kn-green);}
.empty-state{text-align:center;padding:3rem 1rem;color:#999;}
.empty-state i{font-size:3rem;margin-bottom:1rem;color:rgba(107,122,58,.3);}
</style>

<div class="mb-4">
    <h4 class="fw-bold">Nutrition Education Sessions</h4>
    <p class="text-muted mb-0" style="font-size:.9rem">
        Join nutrition education sessions to learn about healthy eating, Pinggang Pinoy, and the 10 Kumainments!
    </p>
</div>

<?php if (isset($_SESSION['flash'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> <?= htmlspecialchars($_SESSION['flash']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash']); endif; ?>

<!-- Upcoming Sessions -->
<div class="section-title">
    <i class="bi bi-calendar-event"></i> Upcoming Sessions
</div>

<?php if (empty($upcomingSessions)): ?>
<div class="empty-state">
    <i class="bi bi-calendar-x"></i>
    <p>No upcoming sessions scheduled yet.</p>
</div>
<?php else: ?>
<?php foreach ($upcomingSessions as $s): ?>
<div class="session-card">
    <div class="d-flex justify-content-between align-items-start mb-2">
        <div class="session-title"><?= htmlspecialchars($s['session_title']) ?></div>
        <span class="badge-upcoming">Upcoming</span>
    </div>
    
    <div class="session-date">
        <i class="bi bi-calendar3"></i> <?= date('F j, Y', strtotime($s['session_date'])) ?>
        <i class="bi bi-clock ms-2"></i> <?= date('g:i A', strtotime($s['session_time'])) ?>
    </div>

    <div class="session-meta">
        <span><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($s['venue']) ?></span>
        <span><i class="bi bi-book-fill"></i> <?= htmlspecialchars($s['topic']) ?></span>
        <?php if ($s['target_group']): ?>
        <span><i class="bi bi-people-fill"></i> <?= htmlspecialchars($s['target_group']) ?></span>
        <?php endif; ?>
        <span><i class="bi bi-person-badge"></i> By: <?= htmlspecialchars($s['bns_name']) ?></span>
    </div>

    <?php if ($s['objectives']): ?>
    <div style="font-size:.88rem;color:#666;margin-top:.8rem;padding-top:.8rem;border-top:1px solid rgba(107,122,58,.1);">
        <strong>What you'll learn:</strong><br>
        <?= nl2br(htmlspecialchars($s['objectives'])) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($s['materials'])): ?>
    <div style="margin-top:.85rem;padding-top:.85rem;border-top:1px solid rgba(107,122,58,.1)">
        <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--kn-muted);margin-bottom:.45rem">
            <i class="bi bi-paperclip me-1"></i>Session Materials
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($s['materials'] as $m): ?>
            <?php
                $url      = 'index.php?action=downloadMaterial&id=' . $m['material_id'];
                $isImage  = str_starts_with($m['file_type'], 'image/');
                $isPdf    = $m['file_type'] === 'application/pdf';
                $canEmbed = $isImage || $isPdf;
            ?>
            <button type="button"
                    onclick="openMaterialViewer('<?= htmlspecialchars($url, ENT_QUOTES) ?>', '<?= htmlspecialchars($m['file_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($m['file_type'], ENT_QUOTES) ?>')"
                    style="display:inline-flex;align-items:center;gap:.35rem;background:rgba(107,122,58,.06);border:1.5px solid rgba(107,122,58,.2);border-radius:7px;padding:.3rem .75rem;font-size:.82rem;color:var(--kn-dark);cursor:pointer;transition:.15s"
                    onmouseover="this.style.background='rgba(107,122,58,.12)'" onmouseout="this.style.background='rgba(107,122,58,.06)'">
                <i class="bi bi-file-earmark-arrow-down" style="color:var(--kn-green)"></i>
                <?= htmlspecialchars($m['file_name']) ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Attendance Confirmation section -->
    <div style="margin-top:.85rem;padding-top:.85rem;border-top:1px solid rgba(107,122,58,.1);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem">
        <div style="font-size:.82rem;color:var(--kn-muted)">
            <?php if ($s['rsvp_count'] > 0): ?>
                <i class="bi bi-people-fill me-1" style="color:var(--kn-green)"></i>
                <strong><?= (int)$s['rsvp_count'] ?></strong> <?= $s['rsvp_count'] === 1 ? 'person' : 'people' ?> confirmed to attend
            <?php else: ?>
                <i class="bi bi-people me-1"></i> Be the first to confirm attendance
            <?php endif; ?>
        </div>
        <form method="POST" action="index.php?action=rsvpSession" style="margin:0">
            <input type="hidden" name="session_id" value="<?= $s['session_id'] ?>">
            <?php if ($s['user_rsvp']): ?>
            <button type="submit"
                    style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(107,122,58,.1);color:var(--kn-green);border:1.5px solid rgba(107,122,58,.3);border-radius:8px;padding:.4rem 1rem;font-size:.85rem;font-weight:600;cursor:pointer;transition:.2s"
                    onmouseover="this.style.background='rgba(107,122,58,.18)'" onmouseout="this.style.background='rgba(107,122,58,.1)'">
                <i class="bi bi-check-circle-fill"></i> Confirmed · Cancel
            </button>
            <?php else: ?>
            <button type="submit"
                    style="display:inline-flex;align-items:center;gap:.4rem;background:var(--kn-green);color:#fff;border:none;border-radius:8px;padding:.4rem 1rem;font-size:.85rem;font-weight:600;cursor:pointer;transition:.2s"
                    onmouseover="this.style.background='var(--kn-green-d)'" onmouseout="this.style.background='var(--kn-green)'">
                <i class="bi bi-calendar-check"></i> Confirm Attendance
            </button>
            <?php endif; ?>
        </form>
    </div>
    <div style="font-size:.75rem;color:var(--kn-muted);margin-top:.35rem">
        <i class="bi bi-info-circle me-1"></i>
        Confirming attendance lets the BNS know you plan to come. Your official attendance will be recorded by the BNS on the day of the session.
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<!-- My Attendance History -->
<div class="section-title mt-5">
    <i class="bi bi-clipboard-check"></i> My Attendance History
</div>

<?php if (empty($myAttendance)): ?>
<div class="empty-state">
    <i class="bi bi-clipboard-x"></i>
    <p>You haven't attended any sessions yet.</p>
</div>
<?php else: ?>
<?php foreach ($myAttendance as $a): ?>
<div class="session-card">
    <div class="d-flex justify-content-between align-items-start mb-2">
        <div class="session-title"><?= htmlspecialchars($a['session_title']) ?></div>
        <span class="badge-attended"><i class="bi bi-check-circle"></i> Attended</span>
    </div>
    
    <div class="session-date">
        <i class="bi bi-calendar3"></i> <?= date('F j, Y', strtotime($a['session_date'])) ?>
        <i class="bi bi-clock ms-2"></i> <?= date('g:i A', strtotime($a['session_time'])) ?>
    </div>

    <div class="session-meta">
        <span><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($a['venue']) ?></span>
        <span><i class="bi bi-book-fill"></i> <?= htmlspecialchars($a['topic']) ?></span>
        <?php if ($a['kumainments_discussed']): ?>
        <span><i class="bi bi-journal-text"></i> Covered: <?= htmlspecialchars($a['kumainments_discussed']) ?></span>
        <?php endif; ?>
    </div>

    <?php if ($a['feedback']): ?>
    <div style="font-size:.88rem;color:#666;margin-top:.8rem;padding-top:.8rem;border-top:1px solid rgba(107,122,58,.1);">
        <strong>My feedback:</strong> <?= htmlspecialchars($a['feedback']) ?>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>
<?php endif; ?>

<!-- ── Material Viewer Modal ── -->
<div id="materialViewerModal"
     style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.72);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:.85rem;width:92vw;max-width:960px;height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,.35)">
        <!-- Header -->
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.65rem 1rem;border-bottom:1.5px solid rgba(107,122,58,.12);background:rgba(107,122,58,.04);flex-shrink:0">
            <div style="display:flex;align-items:center;gap:.5rem">
                <i id="mvIcon" class="bi bi-file-earmark-fill" style="font-size:1.1rem;color:var(--kn-green)"></i>
                <span id="mvTitle" style="font-weight:700;font-size:.9rem;color:var(--kn-dark);max-width:600px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></span>
            </div>
            <div style="display:flex;gap:.5rem;align-items:center">
                <a id="mvDownload" href="#" download
                   style="display:inline-flex;align-items:center;gap:.3rem;background:var(--kn-green);color:#fff;border:none;border-radius:7px;padding:.3rem .75rem;font-size:.82rem;font-weight:600;text-decoration:none">
                    <i class="bi bi-download"></i> Download
                </a>
                <button onclick="closeMaterialViewer()"
                        style="background:none;border:none;font-size:1.4rem;color:#888;cursor:pointer;padding:.1rem .4rem;border-radius:4px;line-height:1"
                        title="Close">&times;</button>
            </div>
        </div>
        <!-- Content -->
        <div id="mvContent" style="flex:1;overflow:auto;display:flex;align-items:center;justify-content:center;background:#f5f5f5;padding:.5rem"></div>
    </div>
</div>

<script>
function openMaterialViewer(url, filename, mimeType) {
    const modal   = document.getElementById('materialViewerModal');
    const title   = document.getElementById('mvTitle');
    const icon    = document.getElementById('mvIcon');
    const content = document.getElementById('mvContent');
    const dl      = document.getElementById('mvDownload');

    title.textContent = filename;
    dl.href = url;
    dl.download = filename;

    if (mimeType === 'application/pdf') {
        icon.className = 'bi bi-file-earmark-pdf-fill';
        icon.style.color = '#e74c3c';
    } else if (mimeType.startsWith('image/')) {
        icon.className = 'bi bi-file-earmark-image-fill';
        icon.style.color = '#2980b9';
    } else if (mimeType.includes('word')) {
        icon.className = 'bi bi-file-earmark-word-fill';
        icon.style.color = '#2b579a';
    } else {
        icon.className = 'bi bi-file-earmark-fill';
        icon.style.color = 'var(--kn-green)';
    }

    if (mimeType === 'application/pdf') {
        content.innerHTML = `<iframe src="${url}" style="width:100%;height:100%;border:none;min-height:70vh"></iframe>`;
    } else if (mimeType.startsWith('image/')) {
        content.innerHTML = `<img src="${url}" style="max-width:100%;max-height:80vh;object-fit:contain;border-radius:6px;box-shadow:0 2px 12px rgba(0,0,0,.15)">`;
    } else {
        content.innerHTML = `
            <div style="text-align:center;padding:3rem 2rem">
                <i class="${icon.className}" style="font-size:4rem;color:${icon.style.color};display:block;margin-bottom:1rem"></i>
                <div style="font-size:1rem;font-weight:600;color:#333;margin-bottom:.5rem">${filename}</div>
                <div style="font-size:.88rem;color:#888;margin-bottom:1.5rem">This file type cannot be previewed. Click below to download it.</div>
                <a href="${url}" download="${filename}"
                   style="display:inline-flex;align-items:center;gap:.4rem;background:var(--kn-green);color:#fff;border-radius:8px;padding:.55rem 1.5rem;font-weight:600;text-decoration:none">
                    <i class="bi bi-download"></i> Download File
                </a>
            </div>`;
    }

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeMaterialViewer() {
    const modal = document.getElementById('materialViewerModal');
    modal.style.display = 'none';
    document.getElementById('mvContent').innerHTML = '';
    document.body.style.overflow = '';
}

document.getElementById('materialViewerModal').addEventListener('click', function(e) {
    if (e.target === this) closeMaterialViewer();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeMaterialViewer();
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => new bootstrap.Alert(alert).close(), 5000);
    });
});
</script>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
