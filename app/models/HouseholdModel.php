<?php
require_once __DIR__ . '/../../config/database.php';

class HouseholdModel {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Insert a new household row.
     * Generates a sequential household code.
     * Returns the new household_id.
     */
    public function createHousehold(array $data): int {
        // Generate sequential household code: HH-000001, HH-000002, etc.
        $code = $this->generateHouseholdCode($data['purok'] ?? null);

        $stmt = $this->db->prepare("
            INSERT INTO households
                (household_code, water_source_id, toilet_type_id, uses_iodized_salt, uses_ifr,
                 dwelling_type_id, purok, num_hh_members, is_mother_prog, fp_method_id, fp_method_other,
                 spouse_name, spouse_last_name, spouse_first_name, spouse_middle_name, spouse_suffix,
                 spouse_educ_level_id, spouse_occupation, spouse_monthly_income, 
                 spouse_pregnancy_status, spouse_breastfeeding_status, draft_spouse_user_id,
                 hof_user_id, hof_needs_review,
                 children_0_5mos, children_6_23mos, children_24_59mos, children_60plus)
            VALUES
                (:household_code, :water_source_id, :toilet_type_id, :uses_iodized_salt, :uses_ifr,
                 :dwelling_type_id, :purok, :num_hh_members, :is_mother_prog, :fp_method_id, :fp_method_other,
                 :spouse_name, :spouse_last_name, :spouse_first_name, :spouse_middle_name, :spouse_suffix,
                 :spouse_educ_level_id, :spouse_occupation, :spouse_monthly_income,
                 :spouse_pregnancy_status, :spouse_breastfeeding_status, :draft_spouse_user_id,
                 :hof_user_id, :hof_needs_review,
                 :children_0_5mos, :children_6_23mos, :children_24_59mos, :children_60plus)
        ");
        $stmt->execute([
            ':household_code'        => $code,
            ':water_source_id'       => $data['water_source_id']       ?? null,
            ':toilet_type_id'        => $data['toilet_type_id']        ?? null,
            ':uses_iodized_salt'     => $data['uses_iodized_salt']     ?? 0,
            ':uses_ifr'              => $data['uses_ifr']              ?? 0,
            ':dwelling_type_id'      => $data['dwelling_type_id']      ?? null,
            ':purok'                 => $data['purok']                 ?? null,
            ':num_hh_members'        => $data['num_hh_members']        ?? null,
            ':is_mother_prog'        => $data['is_mother_prog']        ?? 0,
            ':fp_method_id'          => $data['fp_method_id']          ?? null,
            ':fp_method_other'       => !empty($data['fp_method_other']) ? trim($data['fp_method_other']) : null,
            ':spouse_name'           => $data['spouse_name']           ?? null,
            ':spouse_last_name'      => $data['spouse_last_name']      ?? null,
            ':spouse_first_name'     => $data['spouse_first_name']     ?? null,
            ':spouse_middle_name'    => $data['spouse_middle_name']    ?? null,
            ':spouse_suffix'         => $data['spouse_suffix']         ?? null,
            ':spouse_educ_level_id'  => $data['spouse_educ_level_id']  ?? null,
            ':spouse_occupation'     => $data['spouse_occupation']     ?? null,
            ':spouse_monthly_income' => $data['spouse_monthly_income'] ?? null,
            ':spouse_pregnancy_status' => $data['spouse_pregnancy_status'] ?? null,
            ':spouse_breastfeeding_status' => $data['spouse_breastfeeding_status'] ?? null,
            ':draft_spouse_user_id'  => $data['draft_spouse_user_id']  ?? null,
            ':hof_user_id'           => $data['hof_user_id']           ?? null,
            ':hof_needs_review'      => $data['hof_needs_review']      ?? 0,
            ':children_0_5mos'       => $data['children_0_5mos']       ?? null,
            ':children_6_23mos'      => $data['children_6_23mos']      ?? null,
            ':children_24_59mos'     => $data['children_24_59mos']     ?? null,
            ':children_60plus'       => $data['children_60plus']       ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Add a user to a household. Silently ignores duplicate entries.
     */
    public function addMember(int $householdId, int $userId): void {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO household_members (household_id, user_id)
            VALUES (:household_id, :user_id)
        ");
        $stmt->execute([':household_id' => $householdId, ':user_id' => $userId]);
    }

    /**
     * Return the household row for a given user, or null if not found.
     */
    public function getByUserId(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT h.*
            FROM households h
            INNER JOIN household_members hm ON hm.household_id = h.household_id
            WHERE hm.user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Update the HOF designation and clear the review flag.
     */
    public function updateHof(int $householdId, int $hofUserId): void {
        $stmt = $this->db->prepare("
            UPDATE households
            SET hof_user_id = :hof_user_id, hof_needs_review = 0
            WHERE household_id = :household_id
        ");
        $stmt->execute([':hof_user_id' => $hofUserId, ':household_id' => $householdId]);
    }

    /**
     * Update household details (water, toilet, salt, dwelling, purok).
     */
    public function updateDetails(int $householdId, array $data): void {
        // Try with new spouse columns first; fall back if columns don't exist yet
        try {
            $stmt = $this->db->prepare("
                UPDATE households
                SET water_source_id       = :water_source_id,
                    toilet_type_id        = :toilet_type_id,
                    uses_iodized_salt     = :uses_iodized_salt,
                    uses_ifr              = :uses_ifr,
                    dwelling_type_id      = :dwelling_type_id,
                    purok                 = :purok,
                    num_hh_members        = :num_hh_members,
                    is_mother_prog        = :is_mother_prog,
                    fp_method_id          = :fp_method_id,
                    fp_method_other       = :fp_method_other,
                    spouse_last_name      = :spouse_last_name,
                    spouse_first_name     = :spouse_first_name,
                    spouse_middle_name    = :spouse_middle_name,
                    spouse_suffix         = :spouse_suffix,
                    spouse_educ_level_id  = :spouse_educ_level_id,
                    spouse_occupation     = :spouse_occupation,
                    spouse_monthly_income = :spouse_monthly_income,
                    spouse_pregnancy_status = :spouse_pregnancy_status,
                    spouse_breastfeeding_status = :spouse_breastfeeding_status,
                    draft_spouse_user_id  = :draft_spouse_user_id,
                    children_0_5mos       = :children_0_5mos,
                    children_6_23mos      = :children_6_23mos,
                    children_24_59mos     = :children_24_59mos,
                    children_60plus       = :children_60plus
                WHERE household_id = :household_id
            ");
            $stmt->execute([
                ':water_source_id'        => $data['water_source_id']       ?? null,
                ':toilet_type_id'         => $data['toilet_type_id']        ?? null,
                ':uses_iodized_salt'      => $data['uses_iodized_salt']     ?? 0,
                ':uses_ifr'               => $data['uses_ifr']              ?? 0,
                ':dwelling_type_id'       => $data['dwelling_type_id']      ?? null,
                ':purok'                  => $data['purok']                 ?? null,
                ':num_hh_members'         => $data['num_hh_members']        ?? null,
                ':is_mother_prog'         => $data['is_mother_prog']        ?? 0,
                ':fp_method_id'           => $data['fp_method_id']          ?? null,
                ':fp_method_other'        => !empty($data['fp_method_other']) ? trim($data['fp_method_other']) : null,
                ':spouse_last_name'       => $data['spouse_last_name']      ?? null,
                ':spouse_first_name'      => $data['spouse_first_name']     ?? null,
                ':spouse_middle_name'     => $data['spouse_middle_name']    ?? null,
                ':spouse_suffix'          => $data['spouse_suffix']         ?? null,
                ':spouse_educ_level_id'   => $data['spouse_educ_level_id']  ?? null,
                ':spouse_occupation'      => $data['spouse_occupation']     ?? null,
                ':spouse_monthly_income'  => $data['spouse_monthly_income'] ?? null,
                ':spouse_pregnancy_status' => $data['spouse_pregnancy_status'] ?? null,
                ':spouse_breastfeeding_status' => $data['spouse_breastfeeding_status'] ?? null,
                ':draft_spouse_user_id'   => $data['draft_spouse_user_id']  ?? null,
                ':children_0_5mos'        => $data['children_0_5mos']       ?? null,
                ':children_6_23mos'       => $data['children_6_23mos']      ?? null,
                ':children_24_59mos'      => $data['children_24_59mos']     ?? null,
                ':children_60plus'        => $data['children_60plus']       ?? null,
                ':household_id'           => $householdId,
            ]);
        } catch (PDOException $e) {
            // Fallback: columns not yet migrated — save without new spouse name/educ columns
            if (str_contains($e->getMessage(), 'Unknown column')) {
                $stmt = $this->db->prepare("
                    UPDATE households
                    SET water_source_id       = :water_source_id,
                        toilet_type_id        = :toilet_type_id,
                        uses_iodized_salt     = :uses_iodized_salt,
                        uses_ifr              = :uses_ifr,
                        dwelling_type_id      = :dwelling_type_id,
                        purok                 = :purok,
                        num_hh_members        = :num_hh_members,
                        is_mother_prog        = :is_mother_prog,
                        fp_method_id          = :fp_method_id,
                        spouse_name           = :spouse_name,
                        spouse_occupation     = :spouse_occupation,
                        spouse_monthly_income = :spouse_monthly_income,
                        draft_spouse_user_id  = :draft_spouse_user_id,
                        children_0_5mos       = :children_0_5mos,
                        children_6_23mos      = :children_6_23mos,
                        children_24_59mos     = :children_24_59mos,
                        children_60plus       = :children_60plus
                    WHERE household_id = :household_id
                ");
                $stmt->execute([
                    ':water_source_id'        => $data['water_source_id']       ?? null,
                    ':toilet_type_id'         => $data['toilet_type_id']        ?? null,
                    ':uses_iodized_salt'      => $data['uses_iodized_salt']     ?? 0,
                    ':uses_ifr'               => $data['uses_ifr']              ?? 0,
                    ':dwelling_type_id'       => $data['dwelling_type_id']      ?? null,
                    ':purok'                  => $data['purok']                 ?? null,
                    ':num_hh_members'         => $data['num_hh_members']        ?? null,
                    ':is_mother_prog'         => $data['is_mother_prog']        ?? 0,
                    ':fp_method_id'           => $data['fp_method_id']          ?? null,
                    ':spouse_name'            => $data['spouse_name']           ?? null,
                    ':spouse_occupation'      => $data['spouse_occupation']     ?? null,
                    ':spouse_monthly_income'  => $data['spouse_monthly_income'] ?? null,
                    ':draft_spouse_user_id'   => $data['draft_spouse_user_id']  ?? null,
                    ':children_0_5mos'        => $data['children_0_5mos']       ?? null,
                    ':children_6_23mos'       => $data['children_6_23mos']      ?? null,
                    ':children_24_59mos'      => $data['children_24_59mos']     ?? null,
                    ':children_60plus'        => $data['children_60plus']       ?? null,
                    ':household_id'           => $householdId,
                ]);
            } else {
                throw $e;
            }
        }
    }

    /**
     * Return all members of a household as an array of user rows.
     */
    public function getMembers(int $householdId): array {
        $stmt = $this->db->prepare("
            SELECT u.user_id,
                   CONCAT(u.first_name, ' ', u.last_name) AS full_name,
                   u.email
            FROM household_members hm
            INNER JOIN users u ON u.user_id = hm.user_id
            WHERE hm.household_id = :household_id
        ");
        $stmt->execute([':household_id' => $householdId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Generate a unique sequential household code.
     * Format: HH-PXXX-NNNN where P is purok number and NNNN is sequential
     * If no purok, format is: HH-NNNNNN (6 digits)
     */
    private function generateHouseholdCode(?string $purok = null): string {
        try {
            // Treat empty string, "0", or null as no purok
            $hasPurok = $purok && $purok !== '0' && trim($purok) !== '';

            for ($attempt = 0; $attempt < 10; $attempt++) {
                if ($hasPurok) {
                    $purokNum = str_pad((int)$purok, 3, '0', STR_PAD_LEFT);

                    // Get the highest sequence number for this purok pattern
                    $stmt = $this->db->prepare("
                        SELECT MAX(CAST(SUBSTRING_INDEX(household_code, '-', -1) AS UNSIGNED))
                        FROM households
                        WHERE household_code LIKE :pattern
                    ");
                    $stmt->execute([':pattern' => "HH-{$purokNum}-%"]);
                    $maxNum = (int)$stmt->fetchColumn();
                    $nextNum = $maxNum + 1 + $attempt;
                    $seqNum = str_pad($nextNum, 4, '0', STR_PAD_LEFT);
                    $code = "HH-{$purokNum}-{$seqNum}";
                } else {
                    // No purok — use HH-000-XXXX format
                    $stmt = $this->db->query("
                        SELECT MAX(CAST(SUBSTRING_INDEX(household_code, '-', -1) AS UNSIGNED))
                        FROM households
                        WHERE household_code LIKE 'HH-000-%'
                    ");
                    $maxNum = (int)$stmt->fetchColumn();
                    $nextNum = $maxNum + 1 + $attempt;
                    $seqNum = str_pad($nextNum, 4, '0', STR_PAD_LEFT);
                    $code = "HH-000-{$seqNum}";
                }

                // Check if this code already exists
                $checkStmt = $this->db->prepare("SELECT 1 FROM households WHERE household_code = ?");
                $checkStmt->execute([$code]);
                if (!$checkStmt->fetchColumn()) {
                    return $code; // Code is available
                }
                // Code taken — loop and try next number
            }

            // All attempts failed — use timestamp-based unique code
            return 'HH-' . date('ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        } catch (Exception $e) {
            error_log("Error generating sequential HH code: " . $e->getMessage());
            return 'HH-' . strtoupper(substr(md5(uniqid('', true)), 0, 6));
        }
    }
}
