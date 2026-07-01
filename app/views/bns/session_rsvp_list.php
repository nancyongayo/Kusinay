<?php
/**
 * BNS - Session Attendance List
 * Shows who confirmed attendance and allows marking actual attendance
 */
// Don't override $pageTitle and $activeNav - they are set by the controller
// $pageTitle defaults to 'Attendance' if not set
// $activeNav is set by controller: 'feeding_program' or 'nutrition_education'
if (!isset($pageTitle)) {
    $pageTitle = 'Attendance';
}
if (!isset($activeNav)) {
    $activeNav = 'feeding_program'; // fallback default
}
include __DIR__ . '/../templates/bns_layout.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <?php if ($activeNav === 'feeding_program'): ?>
                    <li class="breadcrumb-item">
                        <a href="index.php?action=feedingProgramList" style="color: #5A7038; text-decoration: none;">Feeding Programs</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="index.php?action=feedingSessions&proposal_id=<?= $session['proposal_id'] ?? '' ?>" style="color: #5A7038; text-decoration: none;">Sessions</a>
                    </li>
                    <?php else: ?>
                    <li class="breadcrumb-item">
                        <a href="index.php?action=nutritionEducationList" style="color: #5A7038; text-decoration: none;">Nutrition Education</a>
                    </li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active" style="color: var(--kn-dark);">Attendance</li>
                </ol>
            </nav>

            <h2 class="mb-2" style="color: var(--kn-dark); font-weight: 700;">
                <i class="bi bi-check2-square me-2" style="color: #5A7038;"></i>
                Attendance
            </h2>
            <p class="text-muted">
                <?php 
                // Handle both feeding program (activity_name) and nutrition education (session_title)
                $displayName = $session['activity_name'] ?? $session['session_title'] ?? 'Session';
                echo htmlspecialchars($displayName);
                ?> - 
                <?= date('F j, Y', strtotime($session['session_date'])) ?>
            </p>
        </div>
        <div class="col-auto">
            <?php if ($activeNav === 'feeding_program'): ?>
            <a href="index.php?action=sessionQRCode&session_id=<?= $session['session_id'] ?>" 
               class="btn" style="background: linear-gradient(135deg, #C4722A 0%, #A85F22 100%); color: #fff; border: none; padding: .6rem 1.2rem; border-radius: 10px; font-weight: 600;">
                <i class="bi bi-qr-code me-1"></i>
                Session QR Code
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-dismissible fade show" style="background: linear-gradient(135deg, rgba(90,112,56,.1) 0%, rgba(90,112,56,.05) 100%); border-left: 4px solid #5A7038; border-radius: 12px;">
            <i class="bi bi-check-circle-fill me-2" style="color: #5A7038;"></i>
            <?= htmlspecialchars($_SESSION['flash']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0" style="border-radius: 12px; background: linear-gradient(135deg, rgba(90,112,56,.1) 0%, rgba(90,112,56,.05) 100%);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Total Invited</p>
                            <h3 class="mb-0" style="color: var(--kn-dark); font-weight: 700;"><?= $stats['total'] ?></h3>
                        </div>
                        <div class="ms-3">
                            <i class="bi bi-people-fill" style="font-size: 2rem; color: #5A7038;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0" style="border-radius: 12px; background: linear-gradient(135deg, rgba(16,185,129,.1) 0%, rgba(16,185,129,.05) 100%);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Confirmed</p>
                            <h3 class="mb-0" style="color: #10b981; font-weight: 700;"><?= $stats['confirmed'] ?></h3>
                        </div>
                        <div class="ms-3">
                            <i class="bi bi-check-circle-fill" style="font-size: 2rem; color: #10b981;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0" style="border-radius: 12px; background: linear-gradient(135deg, rgba(245,158,11,.1) 0%, rgba(245,158,11,.05) 100%);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Pending</p>
                            <h3 class="mb-0" style="color: #f59e0b; font-weight: 700;"><?= $stats['pending'] ?></h3>
                        </div>
                        <div class="ms-3">
                            <i class="bi bi-clock-fill" style="font-size: 2rem; color: #f59e0b;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0" style="border-radius: 12px; background: linear-gradient(135deg, rgba(196,114,42,.1) 0%, rgba(196,114,42,.05) 100%);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Present</p>
                            <h3 class="mb-0" style="color: #C4722A; font-weight: 700;"><?= $stats['present'] ?></h3>
                        </div>
                        <div class="ms-3">
                            <i class="bi bi-person-check-fill" style="font-size: 2rem; color: #C4722A;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RSVP List -->
    <?php if (empty($rsvpList)): ?>
        <div class="alert" style="background: linear-gradient(135deg, rgba(196,114,42,.08) 0%, rgba(196,114,42,.04) 100%); border-left: 4px solid #C4722A; border-radius: 12px;">
            <i class="bi bi-info-circle me-2" style="color: #C4722A;"></i>
            No participants yet. Parents will appear here after they receive notifications.
        </div>
    <?php else: ?>
        <div class="card border-0" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.08);">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15);">
                            <tr>
                                <th style="padding: 1rem;">Child Name</th>
                                <th>Parent Name</th>
                                <th>Contact</th>
                                <th>Confirmation Status</th>
                                <th>Attendance Time</th>
                                <th>Attendance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rsvpList as $rsvp): ?>
                                <tr style="border-bottom: 1px solid rgba(0,0,0,.05);">
                                    <td style="padding: 1rem;">
                                        <strong style="color: var(--kn-dark);"><?= htmlspecialchars($rsvp['name_of_client']) ?></strong>
                                    </td>
                                    <td>
                                        <?php 
                                        // Prioritize the recorded mother/wife name; fallback to linked user account.
                                        $motherName = trim((string)($rsvp['mother_name'] ?? ''));
                                        $isPlaceholderMotherName = in_array(strtolower($motherName), ['n/a', 'no parent information', 'no parent account'], true);

                                        if ($motherName !== '' && !$isPlaceholderMotherName) {
                                            echo htmlspecialchars($rsvp['mother_name']);
                                        } elseif (!empty($rsvp['parent_first_name']) && !empty($rsvp['parent_last_name'])) {
                                            echo htmlspecialchars($rsvp['parent_first_name'] . ' ' . $rsvp['parent_last_name']);
                                        } elseif ($rsvp['mother_id']) {
                                            echo 'N/A';
                                        } else {
                                            echo 'No Parent Account';
                                        }
                                        ?>
                                    </td>
                                    <td style="font-size: .875rem;">
                                        <?php if ($rsvp['parent_phone']): ?>
                                            <i class="bi bi-telephone me-1"></i><?= htmlspecialchars($rsvp['parent_phone']) ?><br>
                                        <?php endif; ?>
                                        <?php if ($rsvp['parent_email']): ?>
                                            <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($rsvp['parent_email']) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $rsvpStyles = [
                                            'confirmed' => 'background: linear-gradient(135deg, rgba(16,185,129,.15) 0%, rgba(16,185,129,.1) 100%); color: #10b981; border: 1px solid rgba(16,185,129,.2);',
                                            'declined' => 'background: linear-gradient(135deg, rgba(220,38,38,.15) 0%, rgba(220,38,38,.1) 100%); color: #dc2626; border: 1px solid rgba(220,38,38,.2);',
                                            'pending' => 'background: linear-gradient(135deg, rgba(245,158,11,.15) 0%, rgba(245,158,11,.1) 100%); color: #f59e0b; border: 1px solid rgba(245,158,11,.2);'
                                        ];
                                        $style = $rsvpStyles[$rsvp['rsvp_status']] ?? $rsvpStyles['pending'];
                                        ?>
                                        <span class="badge" style="<?= $style ?> padding: .4rem .8rem; border-radius: 8px; font-weight: 600;">
                                            <?= ucfirst($rsvp['rsvp_status']) ?>
                                        </span>
                                        <?php if ($rsvp['rsvp_status'] === 'declined' && !empty($rsvp['decline_reason'])): ?>
                                            <button type="button" class="btn btn-sm" style="background: transparent; color: #dc2626; border: none; padding: .2rem .4rem;" 
                                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                                                    title="<strong>Reason:</strong><br><?= htmlspecialchars($rsvp['decline_reason']) ?>">
                                                <i class="bi bi-info-circle-fill"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-size: .875rem;">
                                        <?php 
                                        // Show attendance marked timestamp only when attendance has been marked
                                        $attendanceTime = $rsvp['attendance_timestamp'] ?? $rsvp['attendance_marked_at'] ?? null;
                                        
                                        if ($rsvp['is_present'] !== null && !empty($attendanceTime)) {
                                            // Attendance has been marked (either present or absent)
                                            echo date('M j, g:i A', strtotime($attendanceTime));
                                        } else {
                                            // Not yet marked
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($rsvp['is_present'] === null): ?>
                                            <span class="badge" style="background: #e5e7eb; color: #6b7280; padding: .4rem .8rem; border-radius: 8px;">
                                                Not Marked
                                            </span>
                                        <?php elseif ($rsvp['is_present'] == 1): ?>
                                            <span class="badge" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); padding: .4rem .8rem; border-radius: 8px;">
                                                <i class="bi bi-check-circle me-1"></i>Present
                                            </span>
                                        <?php else: ?>
                                            <span class="badge" style="background: linear-gradient(135deg, rgba(220,38,38,.8) 0%, rgba(220,38,38,.6) 100%); padding: .4rem .8rem; border-radius: 8px;">
                                                <i class="bi bi-x-circle me-1"></i>Absent
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); color: #fff; border: none;" 
                                                    onclick="markAttendance(<?= $activeNav === 'feeding_program' ? $rsvp['attendance_id'] : $rsvp['user_id'] ?>, 1)" title="Mark Present">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                            <button type="button" class="btn" style="background: transparent; color: #dc2626; border: 1px solid #dc2626;" 
                                                    onclick="markAttendance(<?= $activeNav === 'feeding_program' ? $rsvp['attendance_id'] : $rsvp['user_id'] ?>, 0)" title="Mark Absent">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Mark Attendance Form -->
<form id="markAttendanceForm" method="POST" action="<?= $activeNav === 'feeding_program' ? 'index.php?action=markAttendance' : 'index.php?action=markNutritionAttendance' ?>" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
    <?php if ($activeNav === 'feeding_program'): ?>
        <input type="hidden" name="attendance_id" id="markAttendanceId">
    <?php else: ?>
        <input type="hidden" name="user_id" id="markAttendanceId">
    <?php endif; ?>
    <input type="hidden" name="is_present" id="markIsPresent">
</form>

<script>
function markAttendance(attendanceId, isPresent) {
    const action = isPresent ? 'present' : 'absent';
    if (confirm(`Mark this participant as ${action}?`)) {
        document.getElementById('markAttendanceId').value = attendanceId;
        document.getElementById('markIsPresent').value = isPresent;
        document.getElementById('markAttendanceForm').submit();
    }
}

// Initialize Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('show');
    document.getElementById('sidebarOverlay').classList.toggle('show');
});
document.getElementById('sidebarOverlay')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.remove('show');
    document.getElementById('sidebarOverlay').classList.remove('show');
});
</script>
</body>
</html>
