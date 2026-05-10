<?php
require_once __DIR__ . '/../../config/database.php';

class HealthProfileModel {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Insert or update the health profile for a user.
     */
    public function upsert(int $userId, array $data): void {
        $stmt = $this->db->prepare("
            INSERT INTO user_health_profiles
                (user_id, pregnancy_status, breastfeeding_status, monthly_income, occupation, educ_level_id)
            VALUES
                (:user_id, :pregnancy_status, :breastfeeding_status, :monthly_income, :occupation, :educ_level_id)
            ON DUPLICATE KEY UPDATE
                pregnancy_status     = VALUES(pregnancy_status),
                breastfeeding_status = VALUES(breastfeeding_status),
                monthly_income       = VALUES(monthly_income),
                occupation           = VALUES(occupation),
                educ_level_id        = VALUES(educ_level_id)
        ");
        $stmt->execute([
            ':user_id'              => $userId,
            ':pregnancy_status'     => $data['pregnancy_status']     ?? null,
            ':breastfeeding_status' => $data['breastfeeding_status'] ?? null,
            ':monthly_income'       => $data['monthly_income']       ?? null,
            ':occupation'           => $data['occupation']           ?? null,
            ':educ_level_id'        => $data['educ_level_id']        ?? null,
        ]);
    }

    /**
     * Return the health profile row for a user, or null if not found.
     */
    public function getByUserId(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT uhp.*, rel.label AS educ_label
            FROM user_health_profiles uhp
            LEFT JOIN ref_educ_levels rel ON rel.id = uhp.educ_level_id
            WHERE uhp.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
