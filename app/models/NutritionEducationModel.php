<?php
require_once __DIR__ . '/../../config/database.php';

class NutritionEducationModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // ═══════════════════════════════════════════════════════════════════════
    // PROCESS 8: Planning for Nutrition Education Program
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Create a new nutrition education session
     */
    public function createSession(array $data): int {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO nutrition_education_sessions (
                    bns_id, session_title, session_date, session_time, venue,
                    topic, target_group, max_participants, materials_needed,
                    objectives, status, notes
                ) VALUES (
                    :bns_id, :title, :date, :time, :venue,
                    :topic, :target_group, :max_participants, :materials,
                    :objectives, :status, :notes
                )
            ");
            
            $stmt->execute([
                ':bns_id'           => $data['bns_id'],
                ':title'            => $data['session_title'],
                ':date'             => $data['session_date'],
                ':time'             => $data['session_time'],
                ':venue'            => $data['venue'],
                ':topic'            => $data['topic'],
                ':target_group'     => $data['target_group'] ?? null,
                ':max_participants' => $data['max_participants'] ?? null,
                ':materials'        => $data['materials_needed'] ?? null,
                ':objectives'       => $data['objectives'] ?? null,
                ':status'           => $data['status'] ?? 'Planned',
                ':notes'            => $data['notes'] ?? null,
            ]);
            
            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating session: " . $e->getMessage());
            throw new Exception("Failed to create session. Please try again.");
        }
    }

    /**
     * Get all sessions created by a BNS
     */
    public function getSessionsByBns(int $bnsId): array {
        $stmt = $this->db->prepare("
            SELECT s.*,
                   (SELECT COUNT(*) FROM education_attendance WHERE session_id = s.session_id) AS attendee_count
            FROM nutrition_education_sessions s
            WHERE s.bns_id = :bns_id
            ORDER BY s.session_date DESC, s.session_time DESC
        ");
        $stmt->execute([':bns_id' => $bnsId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get session by ID
     */
    public function getSessionById(int $sessionId): ?array {
        $stmt = $this->db->prepare("
            SELECT s.*,
                   CONCAT(u.first_name, ' ', u.last_name) AS bns_name,
                   (SELECT COUNT(*) FROM education_attendance WHERE session_id = s.session_id) AS attendee_count
            FROM nutrition_education_sessions s
            JOIN users u ON u.user_id = s.bns_id
            WHERE s.session_id = :id
        ");
        $stmt->execute([':id' => $sessionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Update session
     */
    public function updateSession(int $sessionId, array $data): void {
        try {
            $stmt = $this->db->prepare("
                UPDATE nutrition_education_sessions
                SET session_title = :title,
                    session_date = :date,
                    session_time = :time,
                    venue = :venue,
                    topic = :topic,
                    target_group = :target_group,
                    max_participants = :max_participants,
                    materials_needed = :materials,
                    objectives = :objectives,
                    notes = :notes,
                    updated_at = CURRENT_TIMESTAMP
                WHERE session_id = :id
            ");
            
            $stmt->execute([
                ':id'               => $sessionId,
                ':title'            => $data['session_title'],
                ':date'             => $data['session_date'],
                ':time'             => $data['session_time'],
                ':venue'            => $data['venue'],
                ':topic'            => $data['topic'],
                ':target_group'     => $data['target_group'] ?? null,
                ':max_participants' => $data['max_participants'] ?? null,
                ':materials'        => $data['materials_needed'] ?? null,
                ':objectives'       => $data['objectives'] ?? null,
                ':notes'            => $data['notes'] ?? null,
            ]);
        } catch (PDOException $e) {
            error_log("Error updating session: " . $e->getMessage());
            throw new Exception("Failed to update session. Please try again.");
        }
    }

    /**
     * Delete session
     */
    public function deleteSession(int $sessionId): void {
        $this->db->prepare("DELETE FROM nutrition_education_sessions WHERE session_id = ?")
                 ->execute([$sessionId]);
    }

    /**
     * Get all available education topics
     */
    public function getAllTopics(): array {
        $stmt = $this->db->query("
            SELECT * FROM education_topics
            WHERE is_active = 1
            ORDER BY category, topic_name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // PROCESS 9: Conducting Nutrition Education
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Mark session as ongoing (BNS starts the session)
     */
    public function startSession(int $sessionId): void {
        $this->db->prepare("
            UPDATE nutrition_education_sessions
            SET status = 'Ongoing'
            WHERE session_id = ? AND status = 'Planned'
        ")->execute([$sessionId]);
    }

    /**
     * Mark session as completed
     */
    public function completeSession(int $sessionId, ?string $notes = null): void {
        $stmt = $this->db->prepare("
            UPDATE nutrition_education_sessions
            SET status = 'Completed',
                completed_at = NOW(),
                notes = COALESCE(:notes, notes)
            WHERE session_id = :session_id AND status IN ('Planned', 'Ongoing')
        ");
        $stmt->execute([':session_id' => $sessionId, ':notes' => $notes]);
    }

    /**
     * Cancel session
     */
    public function cancelSession(int $sessionId, ?string $reason = null): void {
        $stmt = $this->db->prepare("
            UPDATE nutrition_education_sessions
            SET status = 'Cancelled',
                notes = CONCAT(COALESCE(notes, ''), '\n[Cancelled] ', COALESCE(:reason, 'No reason provided'))
            WHERE session_id = :session_id
        ");
        $stmt->execute([':session_id' => $sessionId, ':reason' => $reason]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // PROCESS 10: Attending Nutrition Education (Recording Attendance)
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Record attendance for a session
     */
    public function recordAttendance(array $data): int {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO education_attendance (
                    session_id, user_id, full_name, purok,
                    kumainments_discussed,
                    topic_pinggang_pinoy, topic_10_kumainments, topic_others,
                    signature, feedback, rating
                ) VALUES (
                    :session_id, :user_id, :full_name, :purok,
                    :kumainments,
                    :pinggang_pinoy, :kumainments_10, :others,
                    :signature, :feedback, :rating
                )
            ");
            
            $stmt->execute([
                ':session_id'     => $data['session_id'],
                ':user_id'        => $data['user_id'],
                ':full_name'      => $data['full_name'],
                ':purok'          => $data['purok'] ?? null,
                ':kumainments'    => $data['kumainments_discussed'] ?? null,
                ':pinggang_pinoy' => $data['topic_pinggang_pinoy'] ?? 0,
                ':kumainments_10' => $data['topic_10_kumainments'] ?? 0,
                ':others'         => $data['topic_others'] ?? null,
                ':signature'      => $data['signature'] ?? 'Present',
                ':feedback'       => $data['feedback'] ?? null,
                ':rating'         => $data['rating'] ?? null,
            ]);
            
            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error recording attendance: " . $e->getMessage());
            throw new Exception("Failed to record attendance. Please try again.");
        }
    }

    /**
     * Get attendance records for a session
     */
    public function getAttendanceBySession(int $sessionId): array {
        $stmt = $this->db->prepare("
            SELECT a.*,
                   CONCAT(u.first_name, ' ', u.last_name) AS user_full_name
            FROM education_attendance a
            LEFT JOIN users u ON u.user_id = a.user_id
            WHERE a.session_id = :id
            ORDER BY a.attended_at ASC
        ");
        $stmt->execute([':id' => $sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if user already attended a session
     */
    public function hasAttended(int $sessionId, int $userId): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM education_attendance
            WHERE session_id = ? AND user_id = ?
        ");
        $stmt->execute([$sessionId, $userId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Get upcoming sessions relevant to a specific user (filtered by target group)
     */
    public function getUpcomingSessionsForUser(int $userId): array {
        // Get user's profile data for filtering
        $stmt = $this->db->prepare("
            SELECT u.gender, u.birthdate,
                   uhp.pregnancy_status, uhp.breastfeeding_status,
                   h.children_0_5mos, h.children_6_23mos, h.children_24_59mos
            FROM users u
            LEFT JOIN user_health_profiles uhp ON uhp.user_id = u.user_id
            LEFT JOIN household_members hm ON hm.user_id = u.user_id
            LEFT JOIN households h ON h.household_id = hm.household_id
            WHERE u.user_id = :uid
            LIMIT 1
        ");
        $stmt->execute([':uid' => $userId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $gender          = $profile['gender'] ?? '';
        $pregnancyStatus = $profile['pregnancy_status'] ?? '';
        $age             = $profile['birthdate']
                           ? (int)date_diff(date_create($profile['birthdate']), date_create())->y
                           : 0;
        $has023          = ((int)($profile['children_0_5mos'] ?? 0) + (int)($profile['children_6_23mos'] ?? 0)) > 0;
        $has059          = ($has023 || (int)($profile['children_24_59mos'] ?? 0) > 0);
        $isPregnant      = str_starts_with($pregnancyStatus, 'Pregnant');

        // Fetch all upcoming sessions
        $stmt2 = $this->db->query("
            SELECT s.*,
                   CONCAT(u.first_name, ' ', u.last_name) AS bns_name
            FROM nutrition_education_sessions s
            JOIN users u ON u.user_id = s.bns_id
            WHERE s.status IN ('Planned', 'Ongoing')
              AND s.session_date >= CURDATE()
            ORDER BY s.session_date ASC, s.session_time ASC
        ");
        $sessions = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Filter by target group
        return array_values(array_filter($sessions, function ($s) use (
            $gender, $isPregnant, $has023, $has059, $age
        ) {
            $tg = $s['target_group'] ?? '';
            if (!$tg) return true; // "All families" — everyone sees it

            switch ($tg) {
                case 'Pregnant women':
                    // Must be currently pregnant (starts with 'Pregnant', not 'Not Pregnant')
                    return $isPregnant;

                case 'Mothers with 0-23 mos':
                    // Has at least one child aged 0–23 months
                    return $has023;

                case 'Mothers with 0-59 mos':
                    // Has at least one child aged 0–59 months
                    return $has059;

                case 'Fathers':
                    // Male users only
                    return $gender === 'Male';

                case 'Adolescents':
                    // Age 10–19
                    return $age >= 10 && $age <= 19;

                case 'Elderly':
                    // Age 60+
                    return $age >= 60;

                case 'Adults':
                    // Age 20–59 (not adolescent, not elderly)
                    return $age >= 20 && $age < 60;

                case 'Others':
                default:
                    return true;
            }
        }));
    }

    /**
     * Get upcoming sessions (for Mother view) — unfiltered, kept for backward compat
     */
    public function getUpcomingSessions(): array {
        $stmt = $this->db->query("
            SELECT s.*,
                   CONCAT(u.first_name, ' ', u.last_name) AS bns_name,
                   (SELECT COUNT(*) FROM education_attendance WHERE session_id = s.session_id) AS attendee_count
            FROM nutrition_education_sessions s
            JOIN users u ON u.user_id = s.bns_id
            WHERE s.status IN ('Planned', 'Ongoing')
              AND s.session_date >= CURDATE()
            ORDER BY s.session_date ASC, s.session_time ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get attendance history for a user (Mother view)
     */
    public function getAttendanceByUser(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT a.*,
                   s.session_title,
                   s.session_date,
                   s.session_time,
                   s.venue,
                   s.topic
            FROM education_attendance a
            JOIN nutrition_education_sessions s ON s.session_id = a.session_id
            WHERE a.user_id = :user_id
            ORDER BY s.session_date DESC, s.session_time DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // RSVP — Mother signals intent to attend
    // ═══════════════════════════════════════════════════════════════════════

    /** Toggle RSVP: add if not exists, remove if exists. Returns new state. */
    public function toggleRsvp(int $sessionId, int $userId): bool {
        if ($this->hasRsvp($sessionId, $userId)) {
            $this->db->prepare("DELETE FROM session_rsvp WHERE session_id = ? AND user_id = ?")
                     ->execute([$sessionId, $userId]);
            return false; // cancelled
        }
        $this->db->prepare("INSERT IGNORE INTO session_rsvp (session_id, user_id) VALUES (?, ?)")
                 ->execute([$sessionId, $userId]);
        return true; // confirmed
    }

    public function hasRsvp(int $sessionId, int $userId): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM session_rsvp WHERE session_id = ? AND user_id = ?");
        $stmt->execute([$sessionId, $userId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function getRsvpCount(int $sessionId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM session_rsvp WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        return (int)$stmt->fetchColumn();
    }

    /** Get list of users who RSVPed for a session (for BNS view) */
    public function getRsvpList(int $sessionId): array {
        $stmt = $this->db->prepare("
            SELECT u.user_id, u.first_name, u.last_name, r.rsvp_at
            FROM session_rsvp r
            JOIN users u ON u.user_id = r.user_id
            WHERE r.session_id = ?
            ORDER BY r.rsvp_at ASC
        ");
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get session statistics
     */
    public function getSessionStats(int $bnsId): array {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) AS total_sessions,
                SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) AS completed_sessions,
                SUM(CASE WHEN status = 'Planned' THEN 1 ELSE 0 END) AS planned_sessions,
                SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled_sessions,
                (SELECT COUNT(*) FROM education_attendance ea 
                 JOIN nutrition_education_sessions nes ON nes.session_id = ea.session_id 
                 WHERE nes.bns_id = :bns_id2) AS total_attendees
            FROM nutrition_education_sessions
            WHERE bns_id = :bns_id
        ");
        $stmt->execute([':bns_id' => $bnsId, ':bns_id2' => $bnsId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}
