<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../models/FamilyLinkModel.php';

class NotificationController {

    private PDO $db;
    private NotificationModel $notifModel;
    private FamilyLinkModel   $familyLinkModel;

    public function __construct() {
        $this->db              = getDBConnection();
        $this->notifModel      = new NotificationModel($this->db);
        $this->familyLinkModel = new FamilyLinkModel($this->db);
    }

    // ── Guards ────────────────────────────────────────────────────────────────

    private function requireAuth(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login'); exit;
        }
    }

    // ── List Notifications ────────────────────────────────────────────────────

    public function listNotifications(): void {
        $this->requireAuth();
        $userId        = $_SESSION['user_id'];
        $role          = $_SESSION['role'] ?? '';
        $notifications = $this->notifModel->listForUser($userId);
        $pageTitle     = 'Notifications';
        $activeNav     = 'notifications';
        
        // Route to role-specific notification view
        $viewMap = [
            'Market Vendor'            => __DIR__ . '/../views/market_vendor/notifications.php',
            'BNS Staff'                => __DIR__ . '/../views/bns/notifications.php',
            'Nutrition Officer II'     => __DIR__ . '/../views/nutrition/notifications.php',
            'Committee Chair on Health'=> __DIR__ . '/../views/committee_chair/notifications.php',
            'Committee Secretary'      => __DIR__ . '/../views/committee_secretary/notifications.php',
            'Barangay Captain'         => __DIR__ . '/../views/barangay_captain/notifications.php',
            'Mother'                   => __DIR__ . '/../views/mother/notifications.php',
        ];
        
        $viewFile = $viewMap[$role] ?? __DIR__ . '/../views/mother/notifications.php';
        
        // If role-specific view doesn't exist, create it or use mother view as fallback
        if (!file_exists($viewFile)) {
            $viewFile = __DIR__ . '/../views/mother/notifications.php';
        }
        
        include $viewFile;
    }

    // ── Mark Read ─────────────────────────────────────────────────────────────

    public function markRead(): void {
        $this->requireAuth();
        $userId         = $_SESSION['user_id'];
        $notificationId = (int) ($_POST['notification_id'] ?? 0);

        if ($notificationId) {
            $this->notifModel->markRead($notificationId, $userId);
        }

        $unreadCount = $this->notifModel->getUnreadCount($userId);
        header('Content-Type: application/json');
        echo json_encode(['unread_count' => $unreadCount]);
    }

    // ── Confirm Family Link ───────────────────────────────────────────────────

    public function confirmLink(): void {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $linkId = (int) ($_POST['link_id'] ?? 0);

        if (!$linkId) {
            header('Location: index.php?action=notifications'); exit;
        }

        $link = $this->familyLinkModel->getById($linkId);
        if (!$link || (int) $link['user_id_b'] !== $userId) {
            $_SESSION['flash_error'] = 'Invalid or unauthorized action.';
            header('Location: index.php?action=notifications'); exit;
        }

        if ($link['verification_status'] !== 'Pending') {
            $_SESSION['flash_error'] = 'This request has already been resolved.';
            header('Location: index.php?action=notifications'); exit;
        }

        $this->familyLinkModel->updateStatus($linkId, 'Verified');
        $_SESSION['flash'] = 'Family relationship confirmed.';
        header('Location: index.php?action=notifications'); exit;
    }

    // ── Reject Family Link ────────────────────────────────────────────────────

    public function rejectLink(): void {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $linkId = (int) ($_POST['link_id'] ?? 0);

        if (!$linkId) {
            header('Location: index.php?action=notifications'); exit;
        }

        $link = $this->familyLinkModel->getById($linkId);
        if (!$link || (int) $link['user_id_b'] !== $userId) {
            $_SESSION['flash_error'] = 'Invalid or unauthorized action.';
            header('Location: index.php?action=notifications'); exit;
        }

        if ($link['verification_status'] !== 'Pending') {
            $_SESSION['flash_error'] = 'This request has already been resolved.';
            header('Location: index.php?action=notifications'); exit;
        }

        $this->familyLinkModel->updateStatus($linkId, 'Rejected');

        // Notify the initiating user (user_id_a)
        $initiatorId = (int) $link['user_id_a'];
        $rejecterStmt = $this->db->prepare("SELECT first_name, last_name FROM users WHERE user_id = :uid");
        $rejecterStmt->execute([':uid' => $userId]);
        $rejecter = $rejecterStmt->fetch(PDO::FETCH_ASSOC);
        $this->notifModel->create(
            $initiatorId,
            'relationship_rejected',
            $linkId,
            ($rejecter['first_name'] . ' ' . $rejecter['last_name']) . ' has rejected your spouse relationship request.'
        );

        $_SESSION['flash'] = 'Family relationship rejected.';
        header('Location: index.php?action=notifications'); exit;
    }

    // ── Badge Count (JSON) ────────────────────────────────────────────────────

    public function getBadgeCount(): void {
        $this->requireAuth();
        $count = $this->notifModel->getUnreadCount($_SESSION['user_id']);
        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
    }

    // ── Delete Notification ───────────────────────────────────────────────────

    public function deleteNotification(): void {
        $this->requireAuth();
        $userId         = $_SESSION['user_id'];
        $notificationId = (int) ($_POST['notification_id'] ?? 0);

        if ($notificationId) {
            $this->notifModel->delete($notificationId, $userId);
        }

        // Return updated unread count
        $unreadCount = $this->notifModel->getUnreadCount($userId);
        header('Content-Type: application/json');
        echo json_encode(['unread_count' => $unreadCount]);
    }
}
