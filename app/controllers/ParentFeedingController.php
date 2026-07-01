<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/FeedingProgramModel.php';

/**
 * ParentFeedingController
 * 
 * Handles parent/mother views for feeding program participation
 * - View children enrolled in feeding programs
 * - View upcoming sessions
 * - View attendance history and statistics
 */
class ParentFeedingController {
    private PDO $db;
    private FeedingProgramModel $model;

    public function __construct() {
        $this->db = getDBConnection();
        $this->model = new FeedingProgramModel($this->db);
    }

    private function normalizeName(string $name): string {
        $name = strtolower(trim($name));
        // Normalize hidden whitespace (including NBSP), punctuation, and symbols.
        $name = str_replace("\xC2\xA0", ' ', $name); // non-breaking space
        $name = preg_replace('/\s+/u', ' ', $name) ?? $name;
        $name = preg_replace('/[^a-z0-9]+/u', '', $name) ?? $name;
        return $name;
    }

    private function requireAuth(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    private function requireRole(array $allowedRoles): void {
        $this->requireAuth();
        if (!in_array($_SESSION['role'], $allowedRoles, true)) {
            http_response_code(403);
            include __DIR__ . '/../views/shared/403.php';
            exit;
        }
    }

    /**
     * Show feeding program dashboard for parent
     * Displays all children enrolled in feeding programs with their stats
     */
    public function showFeedingDashboard(): void {
        $this->requireRole(['Mother', 'Father', 'Admin']);
        $parentUserId = $_SESSION['user_id'];
        $parentFullName = trim((string)($_SESSION['user_name'] ?? ''));

        // Get all children enrolled in feeding programs
        $children = $this->model->getParentChildren($parentUserId, $parentFullName);

        // Safety net: dedupe by normalized child name in case of legacy dirty rows.
        $deduped = [];
        foreach ($children as $child) {
            $key = $this->normalizeName((string)($child['child_name'] ?? ''));
            if ($key === '') continue;
            if (!isset($deduped[$key])) {
                $deduped[$key] = $child;
            }
        }
        $children = array_values($deduped);

        // For each child, get upcoming sessions and attendance stats
        foreach ($children as &$child) {
            $child['upcoming_sessions'] = $this->model->getChildUpcomingSessions($child['child_name'], $parentUserId, $parentFullName);
            $child['stats'] = $this->model->getChildAttendanceStats($child['child_name'], $parentUserId, $parentFullName);
        }

        $pageTitle = 'My Child\'s Feeding Program';
        $activeNav = 'feeding_program';
        include __DIR__ . '/../views/mother/feeding_dashboard.php';
    }

    /**
     * Show detailed attendance history for a specific child
     */
    public function showAttendanceHistory(): void {
        $this->requireRole(['Mother', 'Father', 'Admin']);
        $parentUserId = $_SESSION['user_id'];
        $parentFullName = trim((string)($_SESSION['user_name'] ?? ''));
        $childName = $_GET['child_name'] ?? '';
        $sessionId = isset($_GET['session_id']) ? (int)$_GET['session_id'] : null;

        if (empty($childName)) {
            $_SESSION['flash_error'] = 'Child name is required.';
            header('Location: index.php?action=feedingDashboard');
            exit;
        }

        // Get attendance history (filtered by session if provided)
        $attendanceHistory = $this->model->getChildAttendanceHistoryForParent($childName, $parentUserId, $sessionId, $parentFullName);
        
        // Get statistics
        $stats = $this->model->getChildAttendanceStats($childName, $parentUserId, $parentFullName);

        // Get program title
        $programTitle = 'Feeding Program';
        if (!empty($attendanceHistory)) {
            $programTitle = $attendanceHistory[0]['program_title'] ?? 'Feeding Program';
        }

        $pageTitle = 'Attendance History';
        $activeNav = 'feeding_program';
        include __DIR__ . '/../views/mother/feeding_attendance_history.php';
    }
}
