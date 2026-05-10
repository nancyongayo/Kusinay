<?php
$pageTitle = 'Notifications';
$activeNav = 'notifications';

// Pick layout based on role
$role = $_SESSION['role'] ?? '';
if ($role === 'BNS Staff') {
    include __DIR__ . '/../templates/bns_layout.php';
} elseif ($role === 'Mother') {
    include __DIR__ . '/../templates/mother_layout.php';
} else {
    // Fallback: require login
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?action=login'); exit;
    }
    include __DIR__ . '/../templates/bns_layout.php';
}
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <h5 class="mb-0"><i class="bi bi-bell-fill me-2"></i>Notifications</h5>
    <?php if (!empty($notifications)): ?>
    <span class="badge bg-secondary"><?= count($notifications) ?> total</span>
    <?php endif; ?>
</div>

<?php if (empty($notifications)): ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-bell-slash fs-1 d-block mb-3"></i>
    <p>No notifications yet.</p>
</div>
<?php else: ?>

<div class="list-group" id="notification-list">
    <?php foreach ($notifications as $n): ?>
    <?php
        $isUnread = !(bool)$n['is_read'];
        $isAction = $n['action_type'] === 'relationship_confirm';
        
        // Determine navigation URL based on action type
        $navUrl = '';
        $role   = $_SESSION['role'] ?? '';
        switch ($n['action_type']) {
            case 'profile_validated':
            case 'profile_returned':
            case 'profile_submitted':
                if ($role === 'BNS Staff') {
                    // BNS: go to validation list to review
                    $navUrl = 'index.php?action=bnsValidationList';
                } else {
                    // Mother: go to their own wizard
                    $navUrl = 'index.php?action=showWizard';
                }
                break;
            case 'profile_completed':
                // BNS: auto-validated resident — go to family profiles
                $navUrl = 'index.php?action=familyProfiles';
                break;
            case 'relationship_confirm':
            case 'relationship_rejected':
                // Stay on notifications page (has action buttons)
                $navUrl = '';
                break;
            case 'report_submitted':
            case 'report_approved':
            case 'report_returned':
                if (!empty($n['reference_id'])) {
                    $navUrl = 'index.php?action=accomplishmentReport';
                }
                break;
            case 'nutrition_education':
                // BNS: go to session list; Mother: go to upcoming sessions
                $navUrl = ($role === 'BNS Staff')
                    ? 'index.php?action=nutritionEducationList'
                    : 'index.php?action=upcomingSessions';
                break;
            case 'session_rsvp':
                // BNS: go to confirmed attendees list for that session
                $navUrl = !empty($n['reference_id'])
                    ? 'index.php?action=sessionRsvpList&session_id=' . (int)$n['reference_id']
                    : 'index.php?action=nutritionEducationList';
                break;
            default:
                $navUrl = '';
        }
    ?>
    <div class="list-group-item list-group-item-action <?= $isUnread ? 'list-group-item-light border-start border-4 border-primary' : '' ?> <?= $navUrl ? 'cursor-pointer' : '' ?>"
         id="notif-<?= (int)$n['notification_id'] ?>"
         data-notif-id="<?= (int)$n['notification_id'] ?>"
         data-read="<?= $n['is_read'] ? '1' : '0' ?>"
         <?= $navUrl ? 'onclick="navigateToNotification(\'' . htmlspecialchars($navUrl, ENT_QUOTES) . '\')" style="cursor:pointer"' : '' ?>>

        <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <?php if ($n['action_type'] === 'relationship_confirm'): ?>
                        <i class="bi bi-people-fill text-primary"></i>
                    <?php elseif ($n['action_type'] === 'profile_validated'): ?>
                        <i class="bi bi-patch-check-fill text-success"></i>
                    <?php elseif ($n['action_type'] === 'relationship_rejected'): ?>
                        <i class="bi bi-x-circle-fill text-danger"></i>
                    <?php elseif ($n['action_type'] === 'profile_submitted'): ?>
                        <i class="bi bi-send-fill text-info"></i>
                    <?php elseif ($n['action_type'] === 'profile_completed'): ?>
                        <i class="bi bi-person-check-fill text-success"></i>
                    <?php elseif ($n['action_type'] === 'session_rsvp'): ?>
                        <i class="bi bi-calendar-check-fill" style="color:var(--kn-green)"></i>
                    <?php elseif ($n['action_type'] === 'nutrition_education'): ?>
                        <i class="bi bi-book-fill" style="color:var(--kn-green)"></i>
                    <?php elseif (in_array($n['action_type'], ['report_submitted','report_approved','report_returned'])): ?>
                        <i class="bi bi-file-earmark-text-fill text-warning"></i>
                    <?php else: ?>
                        <i class="bi bi-bell-fill text-secondary"></i>
                    <?php endif; ?>
                    <span class="fw-semibold"><?= htmlspecialchars($n['message'] ?? '') ?></span>
                    <?php if ($isUnread): ?>
                        <span class="badge bg-primary ms-1">New</span>
                    <?php endif; ?>
                </div>
                <div class="text-muted small">
                    <?= date('M j, Y g:i A', strtotime($n['created_at'])) ?>
                </div>
            </div>
            
            <!-- Delete button -->
            <button type="button" class="btn btn-sm btn-outline-danger ms-2" 
                    onclick="deleteNotification(<?= (int)$n['notification_id'] ?>, this)" 
                    title="Delete notification">
                <i class="bi bi-trash"></i>
            </button>
        </div>

        <?php if ($isAction): ?>
        <div class="mt-2 d-flex gap-2">
            <form method="POST" action="index.php?action=confirmFamilyLink" class="d-inline">
                <input type="hidden" name="link_id" value="<?= (int)$n['reference_id'] ?>">
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="bi bi-check-lg me-1"></i> Confirm
                </button>
            </form>
            <form method="POST" action="index.php?action=rejectFamilyLink" class="d-inline">
                <input type="hidden" name="link_id" value="<?= (int)$n['reference_id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="bi bi-x-lg me-1"></i> Reject
                </button>
            </form>
        </div>
        <?php endif; ?>

    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteNotifModal" tabindex="-1" aria-labelledby="deleteNotifModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title" id="deleteNotifModalLabel">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Delete Notification
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="mb-0" style="font-size: 0.95rem;">Are you sure you want to delete this notification?</p>
                <p class="text-muted small mt-2 mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pt-0">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-sm btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Navigate to notification target
function navigateToNotification(url) {
    if (url) {
        window.location.href = url;
    }
}

// Delete notification with modal confirmation
let pendingDeleteId = null;
let pendingDeleteButton = null;

function deleteNotification(notificationId, button) {
    pendingDeleteId = notificationId;
    pendingDeleteButton = button;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('deleteNotifModal'));
    modal.show();
}

// Handle delete confirmation
document.addEventListener('DOMContentLoaded', function() {
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if (!pendingDeleteId) return;
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteNotifModal'));
            modal.hide();
            
            // Disable button and show loading
            pendingDeleteButton.disabled = true;
            pendingDeleteButton.innerHTML = '<i class="bi bi-hourglass-split"></i>';
            
            fetch('index.php?action=deleteNotification', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'notification_id=' + encodeURIComponent(pendingDeleteId)
            })
            .then(r => r.json())
            .then(data => {
                // Remove notification from DOM with animation
                const notifElement = document.getElementById('notif-' + pendingDeleteId);
                if (notifElement) {
                    notifElement.style.transition = 'opacity 0.3s, transform 0.3s';
                    notifElement.style.opacity = '0';
                    notifElement.style.transform = 'translateX(-100%)';
                    setTimeout(() => notifElement.remove(), 300);
                }
                
                // Update sidebar badge
                updateSidebarBadge(data.unread_count);
                
                // Check if no notifications left
                const remaining = document.querySelectorAll('[id^="notif-"]');
                if (remaining.length === 0) {
                    location.reload(); // Reload to show empty state
                }
                
                // Reset
                pendingDeleteId = null;
                pendingDeleteButton = null;
            })
            .catch(err => {
                console.error('Delete failed:', err);
                pendingDeleteButton.disabled = false;
                pendingDeleteButton.innerHTML = '<i class="bi bi-trash"></i>';
                alert('Failed to delete notification. Please try again.');
                
                // Reset
                pendingDeleteId = null;
                pendingDeleteButton = null;
            });
        });
    }
});

// Mark unread notifications as read on page load
document.addEventListener('DOMContentLoaded', function () {
    const unread = document.querySelectorAll('[data-notif-id][data-read="0"]');
    unread.forEach(el => {
        const id = el.getAttribute('data-notif-id');
        fetch('index.php?action=markNotificationRead', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'notification_id=' + encodeURIComponent(id)
        })
        .then(r => r.json())
        .then(data => {
            el.setAttribute('data-read', '1');
            el.classList.remove('list-group-item-light', 'border-start', 'border-4', 'border-primary');
            const badge = el.querySelector('.badge.bg-primary');
            if (badge) badge.remove();
            // Update sidebar badge
            updateSidebarBadge(data.unread_count);
        });
    });
});

function updateSidebarBadge(count) {
    const badge = document.getElementById('notif-badge');
    if (!badge) return;
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = '';
    } else {
        badge.style.display = 'none';
    }
}
</script>

<?php include __DIR__ . '/../templates/bns_layout_end.php'; ?>
