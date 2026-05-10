<?php
require_once __DIR__ . '/../../config/database.php';

class FamilyLinkModel {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Insert a new Pending family link.
     * Returns the new link_id.
     */
    public function createLink(int $userA, int $userB, string $type): int {
        $stmt = $this->db->prepare("
            INSERT INTO family_links (user_id_a, user_id_b, relationship_type, verification_status)
            VALUES (:user_id_a, :user_id_b, :relationship_type, 'Pending')
        ");
        $stmt->execute([
            ':user_id_a'         => $userA,
            ':user_id_b'         => $userB,
            ':relationship_type' => $type,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Check whether the target user already has a Verified Husband-Wife link.
     */
    public function hasVerifiedSpouse(int $userId): bool {
        $stmt = $this->db->prepare("
            SELECT link_id FROM family_links
            WHERE (user_id_a = :uid OR user_id_b = :uid2)
              AND relationship_type = 'Husband-Wife'
              AND verification_status = 'Verified'
            LIMIT 1
        ");
        $stmt->execute([':uid' => $userId, ':uid2' => $userId]);
        return (bool) $stmt->fetch();
    }

    /**
     * Update the verification_status of a link.
     * Sets verified_at when transitioning to Verified.
     */
    public function updateStatus(int $linkId, string $status): void {
        if ($status === 'Verified') {
            $stmt = $this->db->prepare("
                UPDATE family_links
                SET verification_status = 'Verified', verified_at = NOW()
                WHERE link_id = :link_id
            ");
            $stmt->execute([':link_id' => $linkId]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE family_links
                SET verification_status = :status
                WHERE link_id = :link_id
            ");
            $stmt->execute([':status' => $status, ':link_id' => $linkId]);
        }
    }

    /**
     * Return the count of Pending family links for household members of a given profile.
     */
    public function getPendingLinksForProfile(int $profileId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT fl.link_id)
            FROM family_links fl
            JOIN household_members hm
              ON fl.user_id_a = hm.user_id OR fl.user_id_b = hm.user_id
            JOIN user_profiles up ON up.user_id = hm.user_id
            WHERE up.profile_id = :profile_id
              AND fl.verification_status = 'Pending'
        ");
        $stmt->execute([':profile_id' => $profileId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Return a single link row by link_id.
     */
    public function getById(int $linkId): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM family_links WHERE link_id = :link_id
        ");
        $stmt->execute([':link_id' => $linkId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Return all links involving a user (as either party).
     */
    public function getLinksForUser(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT fl.*,
                   ua.first_name AS user_a_first, ua.last_name AS user_a_last,
                   ub.first_name AS user_b_first, ub.last_name AS user_b_last
            FROM family_links fl
            LEFT JOIN users ua ON ua.user_id = fl.user_id_a
            LEFT JOIN users ub ON ub.user_id = fl.user_id_b
            WHERE fl.user_id_a = :uid OR fl.user_id_b = :uid2
            ORDER BY fl.created_at DESC
        ");
        $stmt->execute([':uid' => $userId, ':uid2' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
