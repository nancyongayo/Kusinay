<?php
require_once __DIR__ . '/../../config/database.php';

class NotificationModel {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Insert a new notification record.
     * Returns the new notification_id.
     */
    public function create(int $userId, string $actionType, int $referenceId, string $message): int {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, action_type, reference_id, message)
            VALUES (:user_id, :action_type, :reference_id, :message)
        ");
        $stmt->execute([
            ':user_id'      => $userId,
            ':action_type'  => $actionType,
            ':reference_id' => $referenceId,
            ':message'      => $message,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Return the count of unread notifications for a user.
     */
    public function getUnreadCount(int $userId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM notifications
            WHERE user_id = :user_id AND is_read = 0
        ");
        $stmt->execute([':user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Mark a single notification as read (scoped to the owning user for safety).
     */
    public function markRead(int $notificationId, int $userId): void {
        $stmt = $this->db->prepare("
            UPDATE notifications
            SET is_read = 1
            WHERE notification_id = :notification_id AND user_id = :user_id
        ");
        $stmt->execute([
            ':notification_id' => $notificationId,
            ':user_id'         => $userId,
        ]);
    }

    /**
     * Return all notifications for a user, newest first.
     */
    public function listForUser(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM notifications
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a notification (scoped to the owning user for safety).
     */
    public function delete(int $notificationId, int $userId): void {
        $stmt = $this->db->prepare("
            DELETE FROM notifications
            WHERE notification_id = :notification_id AND user_id = :user_id
        ");
        $stmt->execute([
            ':notification_id' => $notificationId,
            ':user_id'         => $userId,
        ]);
    }
}
