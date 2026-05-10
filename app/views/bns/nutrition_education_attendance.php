<?php
$pageTitle = 'Record Attendance';
$activeNav = 'nutrition_education';
require_once __DIR__ . '/../templates/bns_layout.php';
?>
<style>
:root{--kn-green:#6B7A3A;--kn-orange:#C4722A;--kn-dark:#3D4A1E;}
.session-info{background:rgba(107,122,58,.06);border:1.5px solid rgba(107,122,58,.2);border-radius:10px;padding:1.5rem;margin-bottom:2rem;}
.session-info h5{color:var(--kn-dark);font-weight:700;margin-bottom:.8rem;}
.session-meta{display:flex;gap:1.5rem;flex-wrap:wrap;font-size:.9rem;color:#666;}
.session-meta i{color:var(--kn-green);}
.form-card{background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:10px;padding:1.5rem;margin-bottom:2rem;}
.form-label{font-weight:600;color:var(--kn-dark);font-size:.9rem;margin-bottom:.4rem;}
.form-control,.form-select{border:1.5px solid rgba(107,122,58,.2);border-radius:7px;padding:.6rem .9rem;font-size:.9rem;}
.form-control:focus,.form-select:focus{border-color:var(--kn-green);box-shadow:0 0 0 .2rem rgba(107,122,58,.15);}
.btn-submit{background:var(--kn-green);color:#fff;border:none;border-radius:7px;padding:.65rem 1.5rem;font-weight:600;font-size:.9rem;}
.btn-submit:hover{background:#556030;}
.attendance-table{background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:10px;overflow:hidden;}
.attendance-table th{background:rgba(107,122,58,.07);color:var(--kn-dark);font-size:.85rem;font-weight:700;padding:.8rem;border-bottom:2px solid rgba(107,122,58,.15);}
.attendance-table td{font-size:.88rem;padding:.8rem;border-bottom:1px solid rgba(107,122,58,.08);}
.badge-present{background:rgba(40,167,69,.1);color:#28a745;padding:.25rem .6rem;border-radius:5px;font-size:.75rem;font-weight:700;}
</style>

<div class="mb-4">
    <a href="index.php?action=nutritionEducationList" class="text-decoration-none" style="color:var(--kn-green)">
        <i class="bi bi-arrow-left"></i> Back to Sessions
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

<!-- Session Info -->
<div class="session-info">
    <h5><?= htmlspecialchars($session['session_title']) ?></h5>
    <div class="session-meta">
        <span><i class="bi bi-calendar3"></i> <?= date('F j, Y', strtotime($session['session_date'])) ?></span>
        <span><i class="bi bi-clock"></i> <?= date('g:i A', strtotime($session['session_time'])) ?></span>
        <span><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($session['venue']) ?></span>
        <span><i class="bi bi-person-check-fill"></i> <?= count($attendance) ?> attendees</span>
    </div>
    <?php if ($session['status'] === 'Planned'): ?>
    <form method="POST" action="index.php?action=startSession" class="mt-3">
        <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
        <button type="submit" class="btn-submit">
            <i class="bi bi-play-circle"></i> Start Session
        </button>
    </form>
    <?php elseif ($session['status'] === 'Ongoing'): ?>
    <div class="mt-3">
        <span class="badge bg-primary">Session is ongoing</span>
    </div>
    <?php elseif ($session['status'] === 'Completed'): ?>
    <div class="mt-3">
        <span class="badge bg-success">Session completed</span>
    </div>
    <?php endif; ?>
</div>

<!-- Attendance Form -->
<?php if ($session['status'] !== 'Completed' && $session['status'] !== 'Cancelled'): ?>
<div class="form-card">
    <h5 class="fw-bold mb-3">Record Attendance</h5>
    <form method="POST" action="index.php?action=saveAttendance" id="attendanceForm">
        <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label" for="attendeeSelect">Select Attendee <span class="text-danger">*</span></label>
                <select name="user_id" class="form-select" id="attendeeSelect" required onchange="fillAttendeeInfo()">
                    <option value="">-- Select an attendee --</option>
                    <?php foreach ($availableAttendees as $a): ?>
                    <option value="<?= htmlspecialchars($a['user_id'] ?? '') ?>"
                            data-name="<?= htmlspecialchars($a['full_name']) ?>"
                            data-purok="<?= htmlspecialchars($a['purok'] ?? '') ?>"
                            data-has-account="<?= $a['has_account'] ? '1' : '0' ?>">
                        <?= htmlspecialchars($a['full_name']) ?>
                        <?= $a['purok'] ? '(' . htmlspecialchars($a['purok']) . ')' : '' ?>
                        <?= !$a['has_account'] ? ' — No Account' : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($availableAttendees)): ?>
                <small class="text-muted">No family members found. Add family profiles first.</small>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="fullName">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="full_name" id="fullName" class="form-control" required readonly
                       placeholder="Select an attendee first">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label" for="purok">Purok</label>
                <input type="text" name="purok" id="purok" class="form-control" readonly>
            </div>
            <div class="col-md-8">
                <label class="form-label">Topics Discussed <span style="font-weight:400;font-size:.8rem;color:var(--kn-muted)">(check all that apply)</span></label>
                <div class="d-flex flex-wrap gap-3 mt-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="topic_pinggang_pinoy" id="topicPP" value="1">
                        <label class="form-check-label fw-semibold" for="topicPP" style="font-size:.9rem">
                            Pinggang Pinoy
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="topic_10_kumainments" id="topic10K" value="1">
                        <label class="form-check-label fw-semibold" for="topic10K" style="font-size:.9rem">
                            10 Kumainments
                        </label>
                    </div>
                    <div class="form-check d-flex align-items-center gap-2">
                        <input class="form-check-input" type="checkbox" id="topicOthersCheck" value="1"
                               onchange="document.getElementById('topicOthersText').style.display = this.checked ? 'inline-block' : 'none'">
                        <label class="form-check-label fw-semibold" for="topicOthersCheck" style="font-size:.9rem">Others:</label>
                        <input type="text" name="topic_others" id="topicOthersText"
                               class="form-control form-control-sm" style="display:none;width:160px"
                               placeholder="Specify…" maxlength="100">
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden: signature auto-set to Present -->
        <input type="hidden" name="signature" value="Present">

        <button type="submit" class="btn-submit" id="submitAttendance" <?= empty($availableAttendees) ? 'disabled' : '' ?>>
            <i class="bi bi-check-circle"></i> Record Attendance
        </button>
    </form>
</div>
<?php endif; ?>

<!-- Attendance List -->
<div class="attendance-table">
    <table class="table mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Purok</th>
                <th style="text-align:center">Pinggang Pinoy</th>
                <th style="text-align:center">10 Kumainments</th>
                <th style="text-align:center">Others</th>
                <th style="text-align:center">Present</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($attendance)): ?>
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    No attendance recorded yet.
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($attendance as $i => $a): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td class="fw-semibold">
                    <?= htmlspecialchars($a['full_name']) ?>
                    <?php if (empty($a['user_id'])): ?>
                    <span style="font-size:.72rem;background:rgba(196,114,42,.12);color:var(--kn-orange);border-radius:4px;padding:.1rem .4rem;margin-left:.3rem;font-weight:600">Walk-in</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($a['purok'] ?? '—') ?></td>
                <td style="text-align:center">
                    <?= $a['topic_pinggang_pinoy'] ? '<i class="bi bi-check-lg" style="color:var(--kn-green);font-size:1.1rem"></i>' : '<span style="color:#ccc">—</span>' ?>
                </td>
                <td style="text-align:center">
                    <?= $a['topic_10_kumainments'] ? '<i class="bi bi-check-lg" style="color:var(--kn-green);font-size:1.1rem"></i>' : '<span style="color:#ccc">—</span>' ?>
                </td>
                <td style="text-align:center;font-size:.82rem">
                    <?= $a['topic_others'] ? htmlspecialchars($a['topic_others']) : '<span style="color:#ccc">—</span>' ?>
                </td>
                <td style="text-align:center">
                    <span class="badge-present">✓ Present</span>
                </td>
                <td><?= date('g:i A', strtotime($a['attended_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($session['status'] === 'Ongoing' && count($attendance) > 0): ?>
<div class="mt-3">
    <form method="POST" action="index.php?action=completeSession">
        <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
        <div class="mb-2">
            <label class="form-label">Session Notes (Optional)</label>
            <textarea name="notes" class="form-control" rows="2" 
                      placeholder="Summary of the session, key points discussed, etc."></textarea>
        </div>
        <button type="submit" class="btn-submit">
            <i class="bi bi-check-circle"></i> Complete Session
        </button>
    </form>
</div>
<?php endif; ?>

<script>
function fillAttendeeInfo() {
    const select = document.getElementById('attendeeSelect');
    const option = select.options[select.selectedIndex];
    document.getElementById('fullName').value = option.dataset.name || '';
    document.getElementById('purok').value    = option.dataset.purok || '';

    const submitBtn = document.getElementById('submitAttendance');
    submitBtn.disabled = !option.value;
}

// Form validation and submission
document.getElementById('attendanceForm')?.addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitAttendance');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Recording...';
});

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

// Confirmation for completing session
document.querySelectorAll('form[action*="completeSession"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!confirm('Mark this session as completed? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
});
</script>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
