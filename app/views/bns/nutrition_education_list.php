<?php
$pageTitle = 'Nutrition Education Sessions';
$activeNav = 'nutrition_education';
require_once __DIR__ . '/../templates/bns_layout.php';
?>
<style>
:root{--kn-green:#6B7A3A;--kn-orange:#C4722A;--kn-dark:#3D4A1E;}
.session-card{background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:10px;padding:1.2rem;margin-bottom:1rem;transition:.2s;}
.session-card:hover{border-color:var(--kn-green);box-shadow:0 2px 8px rgba(107,122,58,.1);transform:translateY(-2px);}
.session-header{display:flex;justify-content:space-between;align-items:start;margin-bottom:.8rem;gap:1rem;}
.session-title{font-size:1.1rem;font-weight:700;color:var(--kn-dark);margin:0;}
.session-date{font-size:.9rem;color:var(--kn-green);font-weight:600;}
.session-meta{display:flex;gap:1.5rem;flex-wrap:wrap;font-size:.85rem;color:#666;margin-bottom:.8rem;}
.session-meta i{color:var(--kn-green);}
.session-actions{display:flex;gap:.5rem;flex-wrap:wrap;}
.btn-action{padding:.35rem .8rem;font-size:.82rem;border-radius:6px;border:none;font-weight:600;cursor:pointer;transition:.2s;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;}
.btn-primary{background:var(--kn-green);color:#fff;}.btn-primary:hover{background:#556030;color:#fff;}
.btn-secondary{background:#fff;color:var(--kn-dark);border:1.5px solid rgba(61,74,30,.3);}.btn-secondary:hover{background:rgba(61,74,30,.06);color:var(--kn-dark);}
.btn-danger{background:#dc3545;color:#fff;}.btn-danger:hover{background:#c82333;color:#fff;}
.badge-status{padding:.25rem .6rem;border-radius:5px;font-size:.75rem;font-weight:700;white-space:nowrap;}
.badge-planned{background:rgba(107,122,58,.1);color:var(--kn-green);}
.badge-ongoing{background:rgba(0,123,255,.1);color:#007bff;}
.badge-completed{background:rgba(40,167,69,.1);color:#28a745;}
.badge-cancelled{background:rgba(220,53,69,.1);color:#dc3545;}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:2rem;}
.stat-card{background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:8px;padding:1rem;text-align:center;transition:.2s;}
.stat-card:hover{border-color:var(--kn-green);box-shadow:0 2px 8px rgba(107,122,58,.1);}
.stat-value{font-size:2rem;font-weight:700;color:var(--kn-green);}
.stat-label{font-size:.85rem;color:#666;margin-top:.3rem;}
.alert{border-radius:8px;border:none;}
@media (max-width:768px){
    .session-header{flex-direction:column;gap:.5rem;}
    .session-meta{gap:1rem;}
    .stats-grid{grid-template-columns:repeat(2,1fr);}
}
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">Nutrition Education Sessions</h4>
        <p class="text-muted mb-0" style="font-size:.88rem">Plan and manage nutrition education programs</p>
    </div>
    <a href="index.php?action=nutritionEducationForm" class="btn-action btn-primary">
        <i class="bi bi-plus-circle"></i> Set Schedule
    </a>
</div>

<?php if (isset($_SESSION['flash'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> <?= htmlspecialchars($_SESSION['flash']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash']); endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_SESSION['flash_error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= (int)($stats['total_sessions'] ?? 0) ?></div>
        <div class="stat-label">Total Sessions</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= (int)($stats['completed_sessions'] ?? 0) ?></div>
        <div class="stat-label">Completed</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= (int)($stats['planned_sessions'] ?? 0) ?></div>
        <div class="stat-label">Upcoming</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= (int)($stats['total_attendees'] ?? 0) ?></div>
        <div class="stat-label">Total Attendees</div>
    </div>
</div>

<!-- Sessions List -->
<?php if (empty($sessions)): ?>
<div class="text-center py-5 text-muted">
    <i class="bi bi-calendar-event" style="font-size:3rem"></i>
    <p class="mt-3">No sessions planned yet. Click "Set Schedule" to get started.</p>
</div>
<?php else: ?>
<?php foreach ($sessions as $s): ?>
<div class="session-card">
    <div class="session-header">
        <div>
            <h5 class="session-title"><?= htmlspecialchars($s['session_title']) ?></h5>
            <div class="session-date">
                <i class="bi bi-calendar3"></i> <?= date('F j, Y', strtotime($s['session_date'])) ?>
                <i class="bi bi-clock ms-2"></i> <?= date('g:i A', strtotime($s['session_time'])) ?>
            </div>
        </div>
        <span class="badge-status badge-<?= strtolower($s['status']) ?>">
            <?= htmlspecialchars($s['status']) ?>
        </span>
    </div>

    <div class="session-meta">
        <span><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($s['venue']) ?></span>
        <span><i class="bi bi-book-fill"></i> <?= htmlspecialchars($s['topic']) ?></span>
        <?php if ($s['target_group']): ?>
        <span><i class="bi bi-people-fill"></i> <?= htmlspecialchars($s['target_group']) ?></span>
        <?php endif; ?>
        <span><i class="bi bi-person-check-fill"></i> <?= (int)$s['attendee_count'] ?> attendees</span>
        <?php if (in_array($s['status'], ['Planned','Ongoing']) && $s['rsvp_count'] > 0): ?>
        <span style="color:var(--kn-green);font-weight:600">
            <i class="bi bi-calendar-check-fill"></i>
            <a href="index.php?action=sessionRsvpList&session_id=<?= $s['session_id'] ?>"
               style="color:var(--kn-green);text-decoration:none">
                <?= (int)$s['rsvp_count'] ?> confirmed to attend
            </a>
        </span>
        <?php endif; ?>
    </div>

    <?php if ($s['objectives']): ?>
    <div style="font-size:.85rem;color:#666;margin-bottom:.8rem">
        <strong>Objectives:</strong> <?= htmlspecialchars($s['objectives']) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($s['materials'])): ?>
    <div style="margin-bottom:.8rem">
        <div style="font-size:.8rem;font-weight:700;color:var(--kn-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.35rem">
            <i class="bi bi-paperclip me-1"></i>Materials (<?= count($s['materials']) ?>)
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($s['materials'] as $m): ?>
            <button type="button"
                    onclick="openMaterialViewer('<?= htmlspecialchars('index.php?action=downloadMaterial&id=' . $m['material_id'], ENT_QUOTES) ?>', '<?= htmlspecialchars($m['file_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($m['file_type'], ENT_QUOTES) ?>')"
                    style="display:inline-flex;align-items:center;gap:.35rem;background:rgba(107,122,58,.06);border:1px solid rgba(107,122,58,.2);border-radius:6px;padding:.25rem .65rem;font-size:.8rem;color:var(--kn-dark);cursor:pointer;transition:.15s"
                    onmouseover="this.style.background='rgba(107,122,58,.12)'" onmouseout="this.style.background='rgba(107,122,58,.06)'">
                <i class="bi bi-file-earmark" style="color:var(--kn-green)"></i>
                <?= htmlspecialchars($m['file_name']) ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="session-actions">
        <?php if ($s['status'] === 'Planned'): ?>
            <a href="index.php?action=recordAttendance&session_id=<?= $s['session_id'] ?>" class="btn-action btn-primary">
                <i class="bi bi-clipboard-check"></i> Start & Record Attendance
            </a>
            <a href="index.php?action=nutritionEducationForm&session_id=<?= $s['session_id'] ?>" class="btn-action btn-secondary">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <form method="POST" action="index.php?action=cancelSession" style="display:inline">
                <input type="hidden" name="session_id" value="<?= $s['session_id'] ?>">
                <button type="button"
                        onclick="confirmAction('cancel', <?= $s['session_id'] ?>, '<?= htmlspecialchars($s['session_title'], ENT_QUOTES) ?>')"
                        class="btn-action btn-danger">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
            </form>
        <?php elseif ($s['status'] === 'Ongoing'): ?>
            <a href="index.php?action=recordAttendance&session_id=<?= $s['session_id'] ?>" class="btn-action btn-primary">
                <i class="bi bi-clipboard-check"></i> Record Attendance
            </a>
            <form method="POST" action="index.php?action=completeSession" style="display:inline">
                <input type="hidden" name="session_id" value="<?= $s['session_id'] ?>">
                <button type="button"
                        onclick="confirmAction('complete', <?= $s['session_id'] ?>, '<?= htmlspecialchars($s['session_title'], ENT_QUOTES) ?>')"
                        class="btn-action btn-secondary">
                    <i class="bi bi-check-circle"></i> Mark Complete
                </button>
            </form>
        <?php elseif ($s['status'] === 'Completed'): ?>
            <a href="index.php?action=recordAttendance&session_id=<?= $s['session_id'] ?>" class="btn-action btn-secondary">
                <i class="bi bi-eye"></i> View Attendance (<?= (int)$s['attendee_count'] ?>)
            </a>
        <?php endif; ?>

        <!-- Delete button — available for all statuses -->
        <button type="button"
                onclick="confirmDeleteSession(<?= $s['session_id'] ?>, '<?= htmlspecialchars($s['session_title'], ENT_QUOTES) ?>')"
                class="btn-action"
                style="background:#fff;color:#dc3545;border:1.5px solid rgba(220,53,69,.35);"
                onmouseover="this.style.background='rgba(220,53,69,.06)'" onmouseout="this.style.background='#fff'">
            <i class="bi bi-trash3"></i> Delete
        </button>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<!-- ── Action Confirmation Modal (Cancel / Complete) ── -->
<div id="actionConfirmModal"
     style="display:none;position:fixed;inset:0;z-index:99998;background:rgba(0,0,0,.55);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:1rem;width:92vw;max-width:420px;box-shadow:0 8px 40px rgba(0,0,0,.25);overflow:hidden">
        <div style="padding:1.25rem 1.5rem 0">
            <div style="display:flex;align-items:center;gap:.65rem;margin-bottom:.75rem">
                <div id="actionModalIcon"
                     style="width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                </div>
                <div id="actionModalTitle" style="font-weight:700;font-size:1rem;color:var(--kn-dark)"></div>
            </div>
            <p id="actionModalMessage" style="font-size:.9rem;color:#555;margin-bottom:.35rem"></p>
            <p style="font-size:.82rem;color:var(--kn-muted);margin-bottom:1.25rem">This action cannot be undone.</p>
        </div>
        <div style="display:flex;gap:.65rem;justify-content:flex-end;padding:.85rem 1.5rem;border-top:1px solid rgba(0,0,0,.07);background:rgba(0,0,0,.02)">
            <button type="button" onclick="closeActionModal()"
                    style="background:#fff;color:var(--kn-dark);border:1.5px solid rgba(61,74,30,.25);border-radius:8px;padding:.45rem 1.25rem;font-size:.9rem;font-weight:600;cursor:pointer"
                    onmouseover="this.style.background='rgba(0,0,0,.04)'" onmouseout="this.style.background='#fff'">
                Cancel
            </button>
            <button type="button" id="actionModalConfirmBtn" onclick="submitActionForm()"
                    style="border:none;border-radius:8px;padding:.45rem 1.25rem;font-size:.9rem;font-weight:600;cursor:pointer;color:#fff">
            </button>
        </div>
    </div>
</div>

<!-- Hidden forms for cancel/complete -->
<form method="POST" action="index.php?action=cancelSession" id="cancelSessionForm" style="display:none">
    <input type="hidden" name="session_id" id="cancelSessionId">
</form>
<form method="POST" action="index.php?action=completeSession" id="completeSessionForm" style="display:none">
    <input type="hidden" name="session_id" id="completeSessionId">
</form>

<!-- ── Delete Session Confirmation Modal ── -->
<div id="deleteSessionModal"
     style="display:none;position:fixed;inset:0;z-index:99998;background:rgba(0,0,0,.55);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:1rem;width:92vw;max-width:440px;box-shadow:0 8px 40px rgba(0,0,0,.25);overflow:hidden">
        <!-- Header -->
        <div style="padding:1.25rem 1.5rem 0">
            <div style="display:flex;align-items:center;gap:.65rem;margin-bottom:.75rem">
                <div style="width:38px;height:38px;border-radius:50%;background:rgba(220,53,69,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="bi bi-trash3-fill" style="color:#dc3545;font-size:1rem"></i>
                </div>
                <div style="font-weight:700;font-size:1rem;color:var(--kn-dark)">Delete Session</div>
            </div>
            <p style="font-size:.9rem;color:#555;margin-bottom:.5rem">
                Are you sure you want to delete <strong id="deleteSessionTitle" style="color:var(--kn-dark)"></strong>?
            </p>
            <p style="font-size:.82rem;color:#dc3545;margin-bottom:1.25rem">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                All attendance records and uploaded files will also be permanently removed. This cannot be undone.
            </p>
        </div>
        <!-- Actions -->
        <div style="display:flex;gap:.65rem;justify-content:flex-end;padding:.85rem 1.5rem;border-top:1px solid rgba(0,0,0,.07);background:rgba(0,0,0,.02)">
            <button type="button" onclick="closeDeleteModal()"
                    style="background:#fff;color:var(--kn-dark);border:1.5px solid rgba(61,74,30,.25);border-radius:8px;padding:.45rem 1.25rem;font-size:.9rem;font-weight:600;cursor:pointer;transition:.2s"
                    onmouseover="this.style.background='rgba(0,0,0,.04)'" onmouseout="this.style.background='#fff'">
                Cancel
            </button>
            <button type="button" onclick="submitDeleteSession()"
                    style="background:#dc3545;color:#fff;border:none;border-radius:8px;padding:.45rem 1.25rem;font-size:.9rem;font-weight:600;cursor:pointer;transition:.2s"
                    onmouseover="this.style.background='#c82333'" onmouseout="this.style.background='#dc3545'">
                <i class="bi bi-trash3 me-1"></i> Delete Permanently
            </button>
        </div>
    </div>
</div>

<!-- Hidden delete form -->
<form method="POST" action="index.php?action=deleteSession" id="deleteSessionForm" style="display:none">
    <input type="hidden" name="session_id" id="deleteSessionId">
</form>

<!-- ── Material Viewer Modal ── -->
<div id="materialViewerModal"
     style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.72);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:.85rem;width:92vw;max-width:960px;height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,.35)">
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
    document.getElementById('materialViewerModal').style.display = 'none';
    document.getElementById('mvContent').innerHTML = '';
    document.body.style.overflow = '';
}

document.getElementById('materialViewerModal').addEventListener('click', function(e) {
    if (e.target === this) closeMaterialViewer();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeMaterialViewer(); closeDeleteModal(); }
});

// ── Delete session modal ──────────────────────────────────────────────────────
function confirmDeleteSession(sessionId, title) {
    document.getElementById('deleteSessionId').value = sessionId;
    document.getElementById('deleteSessionTitle').textContent = title;
    document.getElementById('deleteSessionModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteSessionModal').style.display = 'none';
    document.body.style.overflow = '';
}

function submitDeleteSession() {
    document.getElementById('deleteSessionForm').submit();
}

document.getElementById('deleteSessionModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

// ── Cancel / Complete session modal ──────────────────────────────────────────
let pendingActionType = null;

function confirmAction(type, sessionId, title) {
    pendingActionType = type;

    const icon    = document.getElementById('actionModalIcon');
    const heading = document.getElementById('actionModalTitle');
    const msg     = document.getElementById('actionModalMessage');
    const btn     = document.getElementById('actionModalConfirmBtn');

    if (type === 'cancel') {
        icon.style.background = 'rgba(220,53,69,.1)';
        icon.innerHTML = '<i class="bi bi-x-circle-fill" style="color:#dc3545;font-size:1rem"></i>';
        heading.textContent = 'Cancel Session';
        msg.innerHTML = 'Cancel <strong>' + title + '</strong>? The session will be marked as cancelled.';
        btn.style.background = '#dc3545';
        btn.innerHTML = '<i class="bi bi-x-circle me-1"></i> Yes, Cancel Session';
        document.getElementById('cancelSessionId').value = sessionId;
    } else {
        icon.style.background = 'rgba(40,167,69,.1)';
        icon.innerHTML = '<i class="bi bi-check-circle-fill" style="color:#28a745;font-size:1rem"></i>';
        heading.textContent = 'Complete Session';
        msg.innerHTML = 'Mark <strong>' + title + '</strong> as completed?';
        btn.style.background = 'var(--kn-green)';
        btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Yes, Mark Complete';
        document.getElementById('completeSessionId').value = sessionId;
    }

    document.getElementById('actionConfirmModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeActionModal() {
    document.getElementById('actionConfirmModal').style.display = 'none';
    document.body.style.overflow = '';
    pendingActionType = null;
}

function submitActionForm() {
    if (pendingActionType === 'cancel') {
        document.getElementById('cancelSessionForm').submit();
    } else {
        document.getElementById('completeSessionForm').submit();
    }
}

document.getElementById('actionConfirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeActionModal();
});
</script>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>

<script>
// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
