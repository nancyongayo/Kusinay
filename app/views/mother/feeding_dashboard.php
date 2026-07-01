<?php
/**
 * Mother/Parent - Feeding Program Dashboard
 * Shows child's feeding program participation
 */
$pageTitle = 'My Child\'s Feeding Program';
$activeNav = 'feeding_program';
include __DIR__ . '/../templates/mother_layout.php';
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1" style="color:var(--kn-dark);font-weight:700">
                👶 My Child's Feeding Program
            </h4>
            <p class="text-muted mb-0" style="font-size:.9rem">
                Track your child's participation in the feeding program
            </p>
        </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['flash']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?php if (empty($children)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-info-circle" style="font-size: 3rem; color: #6c757d; opacity: 0.3;"></i>
                <p class="text-muted mt-3 mb-0">No children enrolled in feeding programs yet.</p>
            </div>
        </div>
    <?php else: ?>
        <?php
        // Final UI safety-net: prevent duplicate child cards caused by dirty legacy rows.
        $seenChildCards = [];
        ?>
        <?php foreach ($children as $child): ?>
        <?php
            $rawName = (string)($child['child_name'] ?? '');
            $normalizedName = mb_strtolower(trim($rawName), 'UTF-8');
            $normalizedName = str_replace("\xC2\xA0", ' ', $normalizedName); // NBSP
            $normalizedName = preg_replace('/\s+/u', ' ', $normalizedName) ?? $normalizedName;
            $normalizedName = preg_replace('/[^\p{L}\p{N}]+/u', '', $normalizedName) ?? $normalizedName;
            $cardKey = $normalizedName . '|' . (string)($child['proposal_title'] ?? '');
            if ($cardKey === '|' || isset($seenChildCards[$cardKey])) {
                continue;
            }
            $seenChildCards[$cardKey] = true;
        ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15); padding: 1.25rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1" style="color:var(--kn-dark);font-weight:700">
                            <?= htmlspecialchars($child['child_name']) ?>
                        </h5>
                        <p class="mb-0 text-muted" style="font-size:.9rem">
                            <?= htmlspecialchars($child['program_title'] ?? 'Feeding Program') ?>
                        </p>
                    </div>
                    <span class="badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: .5rem 1rem; border-radius: 8px; font-size: .9rem;">
                        Active
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Upcoming Sessions -->
                <h6 class="mb-3" style="color:var(--kn-dark);font-weight:600">
                    <i class="bi bi-calendar-event me-2" style="color:#5A7038"></i>Upcoming Sessions
                </h6>
                
                <?php if (empty($child['upcoming_sessions'])): ?>
                    <div class="alert" style="background: rgba(196,114,42,.08); border-left: 4px solid #C4722A; color: var(--kn-dark);">
                        <i class="bi bi-info-circle me-2"></i>No upcoming sessions scheduled yet.
                    </div>
                <?php else: ?>
                    <div class="list-group mb-4">
                        <?php foreach (array_slice($child['upcoming_sessions'], 0, 5) as $session): ?>
                        <a href="index.php?action=feedingAttendanceHistory&child_name=<?= urlencode($child['child_name']) ?>&session_id=<?= $session['session_id'] ?>" 
                           class="list-group-item list-group-item-action border-0 mb-2" 
                           style="background: rgba(90,112,56,.04); border-radius: 12px; text-decoration: none; transition: all .2s ease;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-calendar3" style="color:#5A7038"></i>
                                        <strong><?= date('F j, Y', strtotime($session['session_date'])) ?></strong>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($session['purok_barangay']) ?>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="bi bi-activity me-1"></i><?= htmlspecialchars($session['activity_name']) ?>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <?php if ($session['rsvp_status'] === 'confirmed'): ?>
                                        <span class="badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                            <i class="bi bi-check-circle me-1"></i>Confirmed
                                        </span>
                                    <?php elseif ($session['rsvp_status'] === 'declined'): ?>
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-x-circle me-1"></i>Declined
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock me-1"></i>Pending
                                        </span>
                                        <div class="mt-2">
                                            <form method="POST" action="index.php?action=respondToRSVP" class="d-inline" onclick="event.stopPropagation();">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                                <input type="hidden" name="attendance_id" value="<?= $session['attendance_id'] ?>">
                                                <input type="hidden" name="response" value="confirmed">
                                                <button type="submit" class="btn btn-sm" style="background: #10b981; color: #fff; border: none; padding: .25rem .75rem; border-radius: 6px; font-size: .85rem;" onclick="event.stopPropagation();">
                                                    <i class="bi bi-check"></i> Confirm
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm" style="background: transparent; color: #dc2626; border: 1px solid #dc2626; padding: .25rem .75rem; border-radius: 6px; font-size: .85rem;" onclick="showDeclineModal(event, <?= $session['attendance_id'] ?>);">
                                                <i class="bi bi-x"></i> Decline
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Attendance Summary -->
                <h6 class="mb-3" style="color:var(--kn-dark);font-weight:600">
                    <i class="bi bi-bar-chart me-2" style="color:#C4722A"></i>Attendance Summary
                </h6>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0" style="background: linear-gradient(135deg, rgba(90,112,56,.12) 0%, rgba(90,112,56,.08) 100%); border-radius: 12px;">
                            <div class="card-body text-center">
                                <h3 class="mb-0" style="color: #5A7038; font-weight: 700;"><?= $child['stats']['total'] ?? 0 ?></h3>
                                <small style="color: var(--kn-dark); font-weight: 600;">Total Sessions</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0" style="background: linear-gradient(135deg, rgba(16,185,129,.12) 0%, rgba(16,185,129,.08) 100%); border-radius: 12px;">
                            <div class="card-body text-center">
                                <h3 class="mb-0" style="color: #10b981; font-weight: 700;"><?= $child['stats']['attended'] ?? 0 ?></h3>
                                <small style="color: var(--kn-dark); font-weight: 600;">Attended</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0" style="background: linear-gradient(135deg, rgba(220,38,38,.12) 0%, rgba(220,38,38,.08) 100%); border-radius: 12px;">
                            <div class="card-body text-center">
                                <h3 class="mb-0" style="color: #dc2626; font-weight: 700;"><?= $child['stats']['missed'] ?? 0 ?></h3>
                                <small style="color: var(--kn-dark); font-weight: 600;">Missed</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View History Button -->
                <div class="text-center">
                    <a href="index.php?action=feedingAttendanceHistory&child_name=<?= urlencode($child['child_name']) ?>" 
                       class="btn" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); color: #fff; border: none; padding: .6rem 1.5rem; border-radius: 10px; font-weight: 600;">
                        <i class="bi bi-clock-history me-2"></i>View Full Attendance History
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(220,38,38,.1) 0%, rgba(220,38,38,.05) 100%); border-bottom: 2px solid rgba(220,38,38,.2);">
                <h5 class="modal-title" id="declineModalLabel" style="color: #dc2626; font-weight: 700;">
                    <i class="bi bi-x-circle me-2"></i>Decline Attendance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="index.php?action=respondToRSVP" id="declineForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <input type="hidden" name="attendance_id" id="declineAttendanceId">
                    <input type="hidden" name="response" value="declined">
                    
                    <div class="mb-3">
                        <label for="declineReason" class="form-label" style="color: var(--kn-dark); font-weight: 600;">
                            Please provide a reason for declining <span style="color: #dc2626;">*</span>
                        </label>
                        <textarea class="form-control" id="declineReason" name="decline_reason" rows="4" 
                                  style="border-radius: 8px; border: 1px solid rgba(220,38,38,.3);" 
                                  placeholder="e.g., Child is sick, Family emergency, Prior commitment..."
                                  required></textarea>
                        <div class="form-text">
                            This will help the BNS understand why your child cannot attend.
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(0,0,0,.1);">
                    <button type="button" class="btn" style="background: transparent; color: #6c757d; border: 1px solid #dee2e6; padding: .5rem 1rem; border-radius: 8px;" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: #fff; border: none; padding: .5rem 1.5rem; border-radius: 8px; font-weight: 600;">
                        <i class="bi bi-x-circle me-1"></i>Submit Decline
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showDeclineModal(event, attendanceId) {
    // Prevent any link navigation
    event.preventDefault();
    event.stopPropagation();
    
    // Set the attendance ID in the hidden input
    document.getElementById('declineAttendanceId').value = attendanceId;
    
    // Clear the textarea
    document.getElementById('declineReason').value = '';
    
    // Show the modal
    var modal = new bootstrap.Modal(document.getElementById('declineModal'));
    modal.show();
    
    return false;
}
</script>

<?php include __DIR__ . '/../templates/mother_layout_end.php'; ?>
