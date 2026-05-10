<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/NutritionEducationModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class NutritionEducationController {
    private PDO $db;
    private NutritionEducationModel $model;
    private NotificationModel $notifModel;

    public function __construct() {
        $this->db          = getDBConnection();
        $this->model       = new NutritionEducationModel($this->db);
        $this->notifModel  = new NotificationModel($this->db);
    }

    private function requireBNS(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BNS Staff') {
            header('Location: index.php?action=login');
            exit;
        }
    }

    private function requireMother(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Mother') {
            header('Location: index.php?action=login');
            exit;
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // PROCESS 8: Planning for Nutrition Education Program (BNS)
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Show session list and planning form
     */
    public function showSessionList(): void {
        $this->requireBNS();
        $bnsId = $_SESSION['user_id'];

        $sessions = $this->model->getSessionsByBns($bnsId);
        $topics   = $this->model->getAllTopics();
        $stats    = $this->model->getSessionStats($bnsId);

        // Attach materials to each session
        foreach ($sessions as &$s) {
            $s['materials']  = $this->getMaterials((int)$s['session_id']);
            $s['rsvp_count'] = $this->model->getRsvpCount((int)$s['session_id']);
        }
        unset($s);

        $pageTitle = 'Nutrition Education Sessions';
        $activeNav = 'nutrition_education';
        include __DIR__ . '/../views/bns/nutrition_education_list.php';
    }

    /**
     * Show create/edit session form
     */
    public function showSessionForm(): void {
        $this->requireBNS();
        $bnsId     = $_SESSION['user_id'];
        $sessionId = (int)($_GET['session_id'] ?? 0);

        $session = null;
        if ($sessionId) {
            $session = $this->model->getSessionById($sessionId);
            if (!$session || $session['bns_id'] != $bnsId) {
                $_SESSION['flash_error'] = 'Session not found.';
                header('Location: index.php?action=nutritionEducationList');
                exit;
            }
        }

        $topics           = $this->model->getAllTopics();
        $existingMaterials = $sessionId ? $this->getMaterials($sessionId) : [];

        $pageTitle = $sessionId ? 'Edit Session' : 'Set Schedule for Nutrition Education';
        $activeNav = 'nutrition_education';
        include __DIR__ . '/../views/bns/nutrition_education_form.php';    }

    /**
     * Save session (create or update)
     */
    public function saveSession(): void {
        $this->requireBNS();
        $bnsId     = $_SESSION['user_id'];
        $sessionId = (int)($_POST['session_id'] ?? 0);

        $data = [
            'bns_id'            => $bnsId,
            'session_title'     => trim($_POST['session_title'] ?? ''),
            'session_date'      => trim($_POST['session_date'] ?? ''),
            'session_time'      => trim($_POST['session_time'] ?? ''),
            'venue'             => trim($_POST['venue'] ?? ''),
            'topic'             => trim($_POST['topic'] ?? ''),
            'target_group'      => trim($_POST['target_group'] ?? '') ?: null,
            'max_participants'  => !empty($_POST['max_participants']) ? (int)$_POST['max_participants'] : null,
            'materials_needed'  => trim($_POST['materials_needed'] ?? '') ?: null,
            'objectives'        => trim($_POST['objectives'] ?? '') ?: null,
            'notes'             => trim($_POST['notes'] ?? '') ?: null,
        ];

        // Enhanced validation
        $errors = [];
        if (empty($data['session_title'])) {
            $errors[] = 'Session title is required.';
        }
        if (empty($data['session_date'])) {
            $errors[] = 'Session date is required.';
        } elseif (!$sessionId && strtotime($data['session_date']) < strtotime('today')) {
            // Only block past dates when creating a new session, not when editing
            $errors[] = 'Session date cannot be in the past.';
        }
        if (empty($data['session_time'])) {
            $errors[] = 'Session time is required.';
        }
        if (empty($data['venue'])) {
            $errors[] = 'Venue is required.';
        }
        if (empty($data['topic'])) {
            $errors[] = 'Topic is required.';
        }
        if ($data['max_participants'] !== null && $data['max_participants'] < 1) {
            $errors[] = 'Max participants must be at least 1.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $data;
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?action=nutritionEducationList'));
            exit;
        }

        try {
            if ($sessionId) {
                // Verify ownership before updating
                $existing = $this->model->getSessionById($sessionId);
                if (!$existing || $existing['bns_id'] != $bnsId) {
                    $_SESSION['flash_error'] = 'Session not found or access denied.';
                    header('Location: index.php?action=nutritionEducationList');
                    exit;
                }
                
                $this->model->updateSession($sessionId, $data);
                $this->handleMaterialUploads($sessionId, $bnsId);
                $_SESSION['flash'] = 'Session updated successfully!';
            } else {
                $newSessionId = $this->model->createSession($data);
                $this->handleMaterialUploads($newSessionId, $bnsId);
                $_SESSION['flash'] = 'Session planned successfully! Mothers will be notified.';
                
                // Send notifications to mothers about new session
                $this->notifyMothersAboutNewSession($newSessionId);
            }
        } catch (Exception $e) {
            error_log("Error saving session: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while saving the session. Please try again.';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?action=nutritionEducationList'));
            exit;
        }

        header('Location: index.php?action=nutritionEducationList');
        exit;
    }

    /**
     * Delete session
     */
    public function deleteSession(): void {
        $this->requireBNS();
        $sessionId = (int)($_POST['session_id'] ?? 0);

        if (!$sessionId) {
            $_SESSION['flash_error'] = 'Invalid session ID.';
            header('Location: index.php?action=nutritionEducationList');
            exit;
        }

        // Verify ownership
        $session = $this->model->getSessionById($sessionId);
        if (!$session || $session['bns_id'] != $_SESSION['user_id']) {
            $_SESSION['flash_error'] = 'Session not found or access denied.';
            header('Location: index.php?action=nutritionEducationList');
            exit;
        }

        try {
            // Also delete uploaded material files from disk
            $materials = $this->getMaterials($sessionId);
            foreach ($materials as $m) {
                $path = __DIR__ . '/../../' . $m['file_path'];
                if (file_exists($path)) @unlink($path);
            }

            $this->model->deleteSession($sessionId);
            $_SESSION['flash'] = 'Session deleted successfully.';
        } catch (Exception $e) {
            error_log("Error deleting session: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while deleting the session.';
        }

        header('Location: index.php?action=nutritionEducationList');
        exit;
    }

    // ═══════════════════════════════════════════════════════════════════════
    // PROCESS 9: Conducting Nutrition Education (BNS)
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Show attendance recording form
     */
    public function showAttendanceForm(): void {
        $this->requireBNS();
        $bnsId     = $_SESSION['user_id'];
        $sessionId = (int)($_GET['session_id'] ?? 0);

        $session = $this->model->getSessionById($sessionId);
        if (!$session || $session['bns_id'] != $bnsId) {
            $_SESSION['flash_error'] = 'Session not found.';
            header('Location: index.php?action=nutritionEducationList');
            exit;
        }

        $attendance = $this->model->getAttendanceBySession($sessionId);

        // Get all Head/Wife members from this BNS's family profiles
        // Includes both registered (with user account) and unregistered members
        $stmt = $this->db->prepare("
            SELECT
                fm.member_id,
                CONCAT(fm.last_name, ', ', fm.first_name,
                       CASE WHEN fm.middle_name IS NOT NULL AND fm.middle_name != ''
                            THEN CONCAT(' ', fm.middle_name) ELSE '' END
                ) AS full_name,
                fp.purok,
                fp.source_user_id AS user_id,
                CASE WHEN fp.source_user_id IS NOT NULL THEN 1 ELSE 0 END AS has_account
            FROM family_members fm
            JOIN family_profiles fp ON fp.family_id = fm.family_id
            WHERE fp.bns_id = :bns_id
              AND fm.role IN ('Head', 'Wife')
              AND (fm.first_name IS NOT NULL OR fm.last_name IS NOT NULL)
            ORDER BY fm.last_name ASC, fm.first_name ASC
        ");
        $stmt->execute([':bns_id' => $bnsId]);
        $availableAttendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pageTitle = 'Record Attendance';
        $activeNav = 'nutrition_education';
        include __DIR__ . '/../views/bns/nutrition_education_attendance.php';
    }

    /**
     * Save attendance record
     */
    public function saveAttendance(): void {
        $this->requireBNS();
        $sessionId = (int)($_POST['session_id'] ?? 0);
        $userId    = (int)($_POST['user_id'] ?? 0); // 0 = walk-in (no account)

        // Validation
        if (!$sessionId) {
            $_SESSION['flash_error'] = 'Invalid session.';
            header('Location: index.php?action=nutritionEducationList');
            exit;
        }

        // Verify session exists and belongs to this BNS
        $session = $this->model->getSessionById($sessionId);
        if (!$session || $session['bns_id'] != $_SESSION['user_id']) {
            $_SESSION['flash_error'] = 'Session not found or access denied.';
            header('Location: index.php?action=nutritionEducationList');
            exit;
        }

        // Check if session is in valid state for attendance
        if (!in_array($session['status'], ['Planned', 'Ongoing'])) {
            $_SESSION['flash_error'] = 'Cannot record attendance for a ' . strtolower($session['status']) . ' session.';
            header('Location: index.php?action=recordAttendance&session_id=' . $sessionId);
            exit;
        }

        // Check if already attended (by user_id for registered, by name for non-account)
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId > 0 && $this->model->hasAttended($sessionId, $userId)) {
            $_SESSION['flash_error'] = 'This person has already been marked as attended.';
            header('Location: index.php?action=recordAttendance&session_id=' . $sessionId);
            exit;
        }

        $data = [
            'session_id'            => $sessionId,
            'user_id'               => $userId > 0 ? $userId : null,
            'full_name'             => trim($_POST['full_name'] ?? ''),
            'purok'                 => trim($_POST['purok'] ?? '') ?: null,
            'kumainments_discussed' => null,
            'topic_pinggang_pinoy'  => !empty($_POST['topic_pinggang_pinoy']) ? 1 : 0,
            'topic_10_kumainments'  => !empty($_POST['topic_10_kumainments']) ? 1 : 0,
            'topic_others'          => trim($_POST['topic_others'] ?? '') ?: null,
            'signature'             => 'Present',
            'feedback'              => trim($_POST['feedback'] ?? '') ?: null,
            'rating'                => !empty($_POST['rating']) ? (int)$_POST['rating'] : null,
        ];

        if (empty($data['full_name'])) {
            $_SESSION['flash_error'] = 'Attendee name is required.';
            header('Location: index.php?action=recordAttendance&session_id=' . $sessionId);
            exit;
        }

        try {
            $this->model->recordAttendance($data);
            $_SESSION['flash'] = 'Attendance recorded successfully for ' . htmlspecialchars($data['full_name']) . '.';
        } catch (Exception $e) {
            error_log("Error recording attendance: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while recording attendance. Please try again.';
        }

        header('Location: index.php?action=recordAttendance&session_id=' . $sessionId);
        exit;
    }

    /**
     * Start session (mark as ongoing)
     */
    public function startSession(): void {
        $this->requireBNS();
        $sessionId = (int)($_POST['session_id'] ?? 0);

        if (!$sessionId) {
            $_SESSION['flash_error'] = 'Invalid session ID.';
            header('Location: index.php?action=nutritionEducationList');
            exit;
        }

        // Verify ownership and status
        $session = $this->model->getSessionById($sessionId);
        if (!$session || $session['bns_id'] != $_SESSION['user_id']) {
            $_SESSION['flash_error'] = 'Session not found or access denied.';
            header('Location: index.php?action=nutritionEducationList');
            exit;
        }

        if ($session['status'] !== 'Planned') {
            $_SESSION['flash_error'] = 'Only planned sessions can be started.';
            header('Location: index.php?action=recordAttendance&session_id=' . $sessionId);
            exit;
        }

        try {
            $this->model->startSession($sessionId);
            $_SESSION['flash'] = 'Session started! You can now record attendance.';
        } catch (Exception $e) {
            error_log("Error starting session: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while starting the session.';
        }

        header('Location: index.php?action=recordAttendance&session_id=' . $sessionId);
        exit;
    }

    /**
     * Complete session
     */
    public function completeSession(): void {
        $this->requireBNS();
        $sessionId = (int)($_POST['session_id'] ?? 0);
        $notes     = trim($_POST['notes'] ?? '') ?: null;

        if (!$sessionId) {
            $_SESSION['flash_error'] = 'Invalid session ID.';
            header('Location: index.php?action=nutritionEducationList');
            exit;
        }

        // Verify ownership and status
        $session = $this->model->getSessionById($sessionId);
        if (!$session || $session['bns_id'] != $_SESSION['user_id']) {
            $_SESSION['flash_error'] = 'Session not found or access denied.';
            header('Location: index.php?action=nutritionEducationList');
            exit;
        }

        if (!in_array($session['status'], ['Planned', 'Ongoing'])) {
            $_SESSION['flash_error'] = 'Only planned or ongoing sessions can be completed.';
            header('Location: index.php?action=nutritionEducationList');
            exit;
        }

        try {
            $this->model->completeSession($sessionId, $notes);
            $attendeeCount = (int)$session['attendee_count'];
            $_SESSION['flash'] = "Session marked as completed! Total attendees: $attendeeCount";
        } catch (Exception $e) {
            error_log("Error completing session: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while completing the session.';
        }

        header('Location: index.php?action=nutritionEducationList');
        exit;
    }

    /**
     * Cancel session
     */
    public function cancelSession(): void {
        $this->requireBNS();
        $sessionId = (int)($_POST['session_id'] ?? 0);
        $reason    = trim($_POST['reason'] ?? '') ?: null;

        if (!$sessionId) {
            $_SESSION['flash_error'] = 'Invalid session ID.';
            header('Location: index.php?action=nutritionEducationList');
            exit;
        }

        // Verify ownership
        $session = $this->model->getSessionById($sessionId);
        if (!$session || $session['bns_id'] != $_SESSION['user_id']) {
            $_SESSION['flash_error'] = 'Session not found or access denied.';
            header('Location: index.php?action=nutritionEducationList');
            exit;
        }

        if ($session['status'] === 'Completed') {
            $_SESSION['flash_error'] = 'Cannot cancel a completed session.';
            header('Location: index.php?action=nutritionEducationList');
            exit;
        }

        try {
            $this->model->cancelSession($sessionId, $reason);
            $_SESSION['flash'] = 'Session cancelled successfully.';
        } catch (Exception $e) {
            error_log("Error cancelling session: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while cancelling the session.';
        }

        header('Location: index.php?action=nutritionEducationList');
        exit;
    }

    // ═══════════════════════════════════════════════════════════════════════
    // PROCESS 10: Attending Nutrition Education (Mother View)
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Show upcoming sessions for mothers
     */
    public function showUpcomingSessions(): void {
        $this->requireMother();
        $userId = $_SESSION['user_id'];

        // Filter sessions by target group matching this user's profile
        $upcomingSessions = $this->model->getUpcomingSessionsForUser($userId);
        $myAttendance     = $this->model->getAttendanceByUser($userId);

        // Attach materials and RSVP state to each upcoming session
        foreach ($upcomingSessions as &$s) {
            $s['materials']  = $this->getMaterials((int)$s['session_id']);
            $s['rsvp_count'] = $this->model->getRsvpCount((int)$s['session_id']);
            $s['user_rsvp']  = $this->model->hasRsvp((int)$s['session_id'], $userId);
        }
        unset($s);

        $pageTitle = 'Nutrition Education';
        $activeNav = 'nutrition_education';
        include __DIR__ . '/../views/mother/nutrition_education_mother.php';
    }

    /** Mother toggles RSVP for a session */
    public function toggleRsvp(): void {
        $this->requireMother();
        $userId    = $_SESSION['user_id'];
        $sessionId = (int)($_POST['session_id'] ?? 0);

        if (!$sessionId) {
            header('Location: index.php?action=upcomingSessions'); exit;
        }

        $session = $this->model->getSessionById($sessionId);
        if (!$session || !in_array($session['status'], ['Planned', 'Ongoing'])) {
            $_SESSION['flash_error'] = 'This session is no longer available.';
            header('Location: index.php?action=upcomingSessions'); exit;
        }

        $confirmed = $this->model->toggleRsvp($sessionId, $userId);

        if ($confirmed) {
            $_SESSION['flash'] = 'Attendance confirmed! The BNS will record your official attendance on the day.';

            // Notify the BNS
            $userName = $_SESSION['user_name'] ?? 'A resident';
            $bnsId    = (int)$session['bns_id'];
            $message  = "{$userName} confirmed attendance for \"{$session['session_title']}\" on "
                      . date('F j, Y', strtotime($session['session_date'])) . ". "
                      . "Total confirmed: " . $this->model->getRsvpCount($sessionId) . ".";

            $this->notifModel->create($bnsId, 'session_rsvp', $sessionId, $message);
        } else {
            $_SESSION['flash'] = 'Attendance confirmation cancelled.';
        }

        header('Location: index.php?action=upcomingSessions'); exit;
    }

    /** BNS views who confirmed attendance for a session */
    public function showRsvpList(): void {
        $this->requireBNS();
        $bnsId     = $_SESSION['user_id'];
        $sessionId = (int)($_GET['session_id'] ?? 0);

        $session = $this->model->getSessionById($sessionId);
        if (!$session || $session['bns_id'] != $bnsId) {
            $_SESSION['flash_error'] = 'Session not found.';
            header('Location: index.php?action=nutritionEducationList'); exit;
        }

        $rsvpList = $this->model->getRsvpList($sessionId);

        $pageTitle = 'Confirmed Attendees';
        $activeNav = 'nutrition_education';
        include __DIR__ . '/../views/bns/session_rsvp_list.php';
    }

    /**
     * Notify mothers about new nutrition education session
     */
    private function notifyMothersAboutNewSession(int $sessionId): void {
        try {
            $session = $this->model->getSessionById($sessionId);
            if (!$session) return;

            $targetGroup = $session['target_group'] ?? '';

            // Build query filtered by target group
            // Base: all validated Mother-role users under this BNS
            $baseJoins = "
                FROM users u
                JOIN roles r ON r.role_id = u.role_id
                JOIN user_profiles up ON up.user_id = u.user_id
                LEFT JOIN user_health_profiles uhp ON uhp.user_id = u.user_id
                LEFT JOIN household_members hm ON hm.user_id = u.user_id
                LEFT JOIN households h ON h.household_id = hm.household_id
                LEFT JOIN family_profiles fp ON fp.source_user_id = u.user_id
                WHERE r.role_name = 'Mother'
                  AND up.profile_status = 'Validated'
                  AND fp.bns_id = :bns_id
            ";

            $extra = '';
            switch ($targetGroup) {
                case 'Pregnant women':
                    // pregnancy_status starts with 'Pregnant ' — excludes 'Not Pregnant'
                    $extra = "AND uhp.pregnancy_status LIKE 'Pregnant %'";
                    break;

                case 'Mothers with 0-23 mos':
                    // Has at least one child aged 0–23 months
                    $extra = "AND (COALESCE(h.children_0_5mos,0) + COALESCE(h.children_6_23mos,0)) > 0";
                    break;

                case 'Mothers with 0-59 mos':
                    // Has at least one child aged 0–59 months
                    $extra = "AND (COALESCE(h.children_0_5mos,0) + COALESCE(h.children_6_23mos,0) + COALESCE(h.children_24_59mos,0)) > 0";
                    break;

                case 'Fathers':
                    // Male users only
                    $extra = "AND u.gender = 'Male'";
                    break;

                case 'Adolescents':
                    // Age 10–19
                    $extra = "AND TIMESTAMPDIFF(YEAR, u.birthdate, CURDATE()) BETWEEN 10 AND 19";
                    break;

                case 'Elderly':
                    // Age 60+
                    $extra = "AND TIMESTAMPDIFF(YEAR, u.birthdate, CURDATE()) >= 60";
                    break;

                case 'Adults':
                    // Age 20–59
                    $extra = "AND TIMESTAMPDIFF(YEAR, u.birthdate, CURDATE()) BETWEEN 20 AND 59";
                    break;

                case 'Others':
                case '':
                default:
                    // All families — no extra filter
                    break;
            }

            $sql  = "SELECT DISTINCT u.user_id " . $baseJoins . " " . $extra;
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':bns_id' => $session['bns_id']]);
            $recipients = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (empty($recipients)) return;

            $targetLabel = $targetGroup ?: 'All families';
            $message = sprintf(
                "New nutrition education session: \"%s\" on %s at %s. Venue: %s. Topic: %s. For: %s.",
                $session['session_title'],
                date('F j, Y', strtotime($session['session_date'])),
                date('g:i A', strtotime($session['session_time'])),
                $session['venue'],
                $session['topic'],
                $targetLabel
            );

            foreach ($recipients as $userId) {
                $this->notifModel->create(
                    (int)$userId,
                    'nutrition_education',
                    $sessionId,
                    $message
                );
            }
        } catch (Exception $e) {
            error_log("Error notifying about session: " . $e->getMessage());
            // Don't fail session creation if notifications fail
        }
    }

    // ── Material file uploads ─────────────────────────────────────────────────

    private function handleMaterialUploads(int $sessionId, int $bnsId): void {
        if (empty($_FILES['materialFiles']['name'][0])) return;

        $allowed  = ['application/pdf', 'application/msword',
                     'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                     'application/vnd.ms-powerpoint',
                     'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                     'image/jpeg', 'image/png', 'image/gif'];
        $maxBytes  = 10 * 1024 * 1024; // 10 MB
        $maxFiles  = 5;
        $uploadDir = __DIR__ . '/../../uploads/session_materials/' . $sessionId . '/';

        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $files = $_FILES['materialFiles'];
        $count = min(count($files['name']), $maxFiles); // never process more than 5

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
            if ($files['size'][$i] > $maxBytes) continue;

            $mime = mime_content_type($files['tmp_name'][$i]);
            if (!in_array($mime, $allowed)) continue;

            $origName = basename($files['name'][$i]);
            $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $stored   = uniqid('mat_', true) . '.' . $ext;
            $destPath = $uploadDir . $stored;

            if (!move_uploaded_file($files['tmp_name'][$i], $destPath)) continue;

            $relPath = 'uploads/session_materials/' . $sessionId . '/' . $stored;
            $this->db->prepare("
                INSERT INTO session_materials (session_id, bns_id, file_name, file_path, file_size, file_type)
                VALUES (?, ?, ?, ?, ?, ?)
            ")->execute([$sessionId, $bnsId, $origName, $relPath, $files['size'][$i], $mime]);
        }
    }

    private function getMaterials(int $sessionId): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM session_materials WHERE session_id = ? ORDER BY uploaded_at DESC"
        );
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function downloadMaterial(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login'); exit;
        }

        $id   = (int)($_GET['id'] ?? 0);
        $stmt = $this->db->prepare("SELECT * FROM session_materials WHERE material_id = ?");
        $stmt->execute([$id]);
        $mat  = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$mat) { http_response_code(404); exit('File not found.'); }

        $path = __DIR__ . '/../../' . $mat['file_path'];
        if (!file_exists($path)) { http_response_code(404); exit('File not found.'); }

        header('Content-Type: ' . $mat['file_type']);
        header('Content-Disposition: inline; filename="' . addslashes($mat['file_name']) . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    public function uploadMaterialDirect(): void {
        $this->requireBNS();
        $bnsId     = $_SESSION['user_id'];
        $sessionId = (int)($_POST['session_id'] ?? 0);

        // Verify ownership
        $session = $this->model->getSessionById($sessionId);
        if (!$session || $session['bns_id'] != $bnsId) {
            $_SESSION['flash_error'] = 'Session not found or access denied.';
            header('Location: index.php?action=nutritionEducationList'); exit;
        }

        if (empty($_FILES['materialFiles']['name'][0])) {
            $_SESSION['flash_error'] = 'No file selected.';
            header('Location: index.php?action=nutritionEducationForm&session_id=' . $sessionId); exit;
        }

        // Check existing count
        $existing = count($this->getMaterials($sessionId));
        if ($existing >= 5) {
            $_SESSION['flash_error'] = 'Maximum 5 files already uploaded for this session.';
            header('Location: index.php?action=nutritionEducationForm&session_id=' . $sessionId); exit;
        }

        $this->handleMaterialUploads($sessionId, $bnsId);
        $_SESSION['flash'] = 'File(s) uploaded successfully.';
        header('Location: index.php?action=nutritionEducationForm&session_id=' . $sessionId); exit;
    }

    public function deleteMaterial(): void {
        $this->requireBNS();
        $bnsId      = $_SESSION['user_id'];
        $materialId = (int)($_POST['material_id'] ?? 0);
        $sessionId  = (int)($_POST['session_id'] ?? 0);

        $stmt = $this->db->prepare("SELECT * FROM session_materials WHERE material_id = ? AND bns_id = ?");
        $stmt->execute([$materialId, $bnsId]);
        $mat = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($mat) {
            $path = __DIR__ . '/../../' . $mat['file_path'];
            if (file_exists($path)) @unlink($path);
            $this->db->prepare("DELETE FROM session_materials WHERE material_id = ?")->execute([$materialId]);
            $_SESSION['flash'] = 'File deleted.';
        }

        header('Location: index.php?action=nutritionEducationForm&session_id=' . $sessionId);
        exit;
    }
}
