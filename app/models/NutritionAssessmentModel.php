<?php
require_once __DIR__ . '/../../config/database.php';

class NutritionAssessmentModel {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // ── Z-score lookups ───────────────────────────────────────────────────────

    public function getWFARef(int $ageMonths, string $sex): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM ref_zscore_wfa
            WHERE age_months = :age AND sex = :sex LIMIT 1
        ");
        $stmt->execute([':age' => $ageMonths, ':sex' => $sex]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getHFARef(int $ageMonths, string $sex): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM ref_zscore_hfa
            WHERE age_months = :age AND sex = :sex LIMIT 1
        ");
        $stmt->execute([':age' => $ageMonths, ':sex' => $sex]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Find the closest WFH reference row by rounding height to nearest 0.5 cm.
     */
    public function getWFHRef(float $heightCm, string $sex): ?array {
        // Round to nearest 0.5
        $rounded = round($heightCm * 2) / 2;
        // Clamp to table range 45.0–110.0
        $rounded = max(45.0, min(110.0, $rounded));
        $stmt = $this->db->prepare("
            SELECT * FROM ref_zscore_wfh
            WHERE height_cm = :h AND sex = :sex LIMIT 1
        ");
        $stmt->execute([':h' => $rounded, ':sex' => $sex]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // ── Qualification lists (Process 3) ──────────────────────────────────────

    /**
     * Children 0-59 months from validated families under this BNS.
     */
    public function getQualifiedChildren(int $bnsId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM (

                -- Source 1: Children added by parents via mother wizard (household_children)
                SELECT
                    hc.child_id,
                    NULL AS fm_member_id,
                    TRIM(CONCAT(
                        COALESCE(hc.last_name, ''), ', ',
                        COALESCE(hc.first_name, ''),
                        IF(hc.middle_name IS NOT NULL AND hc.middle_name != '', CONCAT(' ', hc.middle_name), ''),
                        IF(hc.suffix IS NOT NULL AND hc.suffix != '', CONCAT(' ', hc.suffix), '')
                    )) AS full_name,
                    hc.sex,
                    hc.dob,
                    TIMESTAMPDIFF(MONTH, hc.dob, CURDATE()) AS age_in_months,
                    h.purok,
                    CONCAT(u.first_name,' ',u.last_name) AS caregiver_name,
                    fp.family_id,
                    fp.hh_number,
                    (SELECT MAX(na.assessment_date)
                     FROM nutrition_assessments na
                     WHERE na.child_id = hc.child_id) AS last_assessed,
                    'household' AS source
                FROM household_children hc
                JOIN households h         ON h.household_id = hc.household_id
                JOIN household_members hm ON hm.household_id = h.household_id
                JOIN users u              ON u.user_id = hm.user_id
                JOIN user_profiles up     ON up.user_id = u.user_id
                JOIN family_profiles fp   ON fp.source_user_id = u.user_id
                WHERE fp.bns_id = :bns1
                  AND up.profile_status = 'Validated'
                  AND hc.dob IS NOT NULL
                  AND TIMESTAMPDIFF(MONTH, hc.dob, CURDATE()) BETWEEN 0 AND 59
                  AND (TRIM(COALESCE(hc.first_name,'')) != '' OR TRIM(COALESCE(hc.last_name,'')) != '')

                UNION ALL

                -- Source 2: Children added by BNS directly in family profiles (family_members)
                SELECT
                    NULL AS child_id,
                    fm.member_id AS fm_member_id,
                    TRIM(CONCAT(
                        COALESCE(fm.last_name, ''), ', ',
                        COALESCE(fm.first_name, ''),
                        IF(fm.middle_name IS NOT NULL AND fm.middle_name != '', CONCAT(' ', fm.middle_name), ''),
                        IF(fm.suffix IS NOT NULL AND fm.suffix != '', CONCAT(' ', fm.suffix), '')
                    )) AS full_name,
                    fm.sex,
                    fm.dob,
                    TIMESTAMPDIFF(MONTH, fm.dob, CURDATE()) AS age_in_months,
                    fp.purok,
                    COALESCE(
                        (SELECT CONCAT(hm_head.last_name, ', ', hm_head.first_name)
                         FROM family_members hm_head
                         WHERE hm_head.family_id = fp.family_id AND hm_head.role = 'Head' LIMIT 1),
                        (SELECT CONCAT(u2.first_name,' ',u2.last_name)
                         FROM users u2 WHERE u2.user_id = fp.source_user_id LIMIT 1),
                        'BNS Encoded'
                    ) AS caregiver_name,
                    fp.family_id,
                    fp.hh_number,
                    (SELECT MAX(na.assessment_date)
                     FROM nutrition_assessments na
                     WHERE na.fm_member_id = fm.member_id) AS last_assessed,
                    'family_profile' AS source
                FROM family_members fm
                JOIN family_profiles fp ON fp.family_id = fm.family_id
                WHERE fp.bns_id = :bns2
                  AND fm.role = 'Child'
                  AND fm.dob IS NOT NULL
                  AND TIMESTAMPDIFF(MONTH, fm.dob, CURDATE()) BETWEEN 0 AND 59
                  AND (TRIM(COALESCE(fm.first_name,'')) != '' OR TRIM(COALESCE(fm.last_name,'')) != '')

            ) AS combined
            ORDER BY purok, full_name
        ");
        $stmt->execute([':bns1' => $bnsId, ':bns2' => $bnsId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Maternal (Pregnant / Lactating) from validated families under this BNS.
     */
    public function getQualifiedMaternal(int $bnsId): array {
        // ── SOURCE 1: Registered users (have accounts) ────────────────────────
        $stmt = $this->db->prepare("
            SELECT
                u.user_id,
                NULL AS fm_member_id,
                CONCAT(u.first_name,' ',u.last_name) AS full_name,
                u.gender AS sex,
                u.birthdate AS dob,
                TIMESTAMPDIFF(YEAR, u.birthdate, CURDATE()) AS age_in_years,
                uhp.pregnancy_status,
                uhp.breastfeeding_status,
                COALESCE(h.purok, fp2.purok) AS purok,
                COALESCE(fp2.hh_number, '') AS hh_number,
                (SELECT MAX(na.assessment_date)
                 FROM nutrition_assessments na
                 WHERE na.user_id = u.user_id) AS last_assessed
            FROM users u
            JOIN user_profiles up ON up.user_id = u.user_id
            JOIN user_health_profiles uhp ON uhp.user_id = u.user_id
            LEFT JOIN household_members hm ON hm.user_id = u.user_id
            LEFT JOIN households h ON h.household_id = hm.household_id
            LEFT JOIN family_profiles fp2 ON fp2.source_user_id = u.user_id
            WHERE up.assigned_bns_id = :bns
              AND up.profile_status = 'Validated'
              AND (
                uhp.pregnancy_status IN ('Pregnant 1st Trimester','Pregnant 2nd Trimester','Pregnant 3rd Trimester')
                OR uhp.breastfeeding_status IN ('EBF (Exclusive Breastfeeding)','Mixed Feeding','Exclusively Breastfeeding')
              )
            ORDER BY purok, u.last_name
        ");
        $stmt->execute([':bns' => $bnsId]);
        $fromUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ── SOURCE 2: BNS-encoded female member (no account) with EBF in family_profiles ──
        // Joins on sex='F' so it works whether the female is the Head or the Spouse
        $stmt2 = $this->db->prepare("
            SELECT
                NULL AS user_id,
                fm_female.member_id AS fm_member_id,
                TRIM(CONCAT(
                    COALESCE(fm_female.last_name,''), ', ',
                    COALESCE(fm_female.first_name,''),
                    IF(fm_female.middle_name IS NOT NULL AND fm_female.middle_name != '', CONCAT(' ', fm_female.middle_name), '')
                )) AS full_name,
                fm_female.sex,
                fm_female.dob,
                TIMESTAMPDIFF(YEAR, fm_female.dob, CURDATE()) AS age_in_years,
                NULL AS pregnancy_status,
                'EBF (Exclusive Breastfeeding)' AS breastfeeding_status,
                fp.purok,
                fp.hh_number,
                (SELECT MAX(na.assessment_date)
                 FROM nutrition_assessments na
                 WHERE na.fm_member_id = fm_female.member_id) AS last_assessed
            FROM family_profiles fp
            JOIN family_members fm_female ON fm_female.family_id = fp.family_id
                AND fm_female.role IN ('Head','Wife')
                AND fm_female.sex = 'F'
            WHERE fp.bns_id = :bns
              AND fp.is_erf = 1
              AND (fm_female.first_name IS NOT NULL OR fm_female.last_name IS NOT NULL)
              AND NOT EXISTS (
                  SELECT 1 FROM users u_female
                  JOIN user_profiles up2 ON up2.user_id = u_female.user_id
                  WHERE (u_female.first_name = fm_female.first_name AND u_female.last_name = fm_female.last_name)
                    AND up2.profile_status = 'Validated'
                    AND up2.assigned_bns_id = :bns2
              )
        ");
        $stmt2->execute([':bns' => $bnsId, ':bns2' => $bnsId]);
        $fromBns = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // ── SOURCE 3: BNS-encoded female member marked as pregnant (is_mother_prog = 1) ──
        // Joins on sex='F' so it works whether the female is the Head or the Spouse
        $stmt3 = $this->db->prepare("
            SELECT
                NULL AS user_id,
                fm_female.member_id AS fm_member_id,
                TRIM(CONCAT(
                    COALESCE(fm_female.last_name,''), ', ',
                    COALESCE(fm_female.first_name,''),
                    IF(fm_female.middle_name IS NOT NULL AND fm_female.middle_name != '', CONCAT(' ', fm_female.middle_name), '')
                )) AS full_name,
                fm_female.sex,
                fm_female.dob,
                TIMESTAMPDIFF(YEAR, fm_female.dob, CURDATE()) AS age_in_years,
                'Pregnant' AS pregnancy_status,
                NULL AS breastfeeding_status,
                fp.purok,
                fp.hh_number,
                (SELECT MAX(na.assessment_date)
                 FROM nutrition_assessments na
                 WHERE na.fm_member_id = fm_female.member_id) AS last_assessed
            FROM family_profiles fp
            JOIN family_members fm_female ON fm_female.family_id = fp.family_id
                AND fm_female.role IN ('Head','Wife')
                AND fm_female.sex = 'F'
            WHERE fp.bns_id = :bns
              AND fp.is_mother_prog = 1
              AND (fm_female.first_name IS NOT NULL OR fm_female.last_name IS NOT NULL)
              AND NOT EXISTS (
                  SELECT 1 FROM users u_female
                  JOIN user_profiles up2 ON up2.user_id = u_female.user_id
                  WHERE (u_female.first_name = fm_female.first_name AND u_female.last_name = fm_female.last_name)
                    AND up2.profile_status = 'Validated'
                    AND up2.assigned_bns_id = :bns2
              )
        ");
        $stmt3->execute([':bns' => $bnsId, ':bns2' => $bnsId]);
        $fromPregnant = $stmt3->fetchAll(PDO::FETCH_ASSOC);

        // Merge — registered users first, then BNS-only wives
        $seen = [];
        $results = [];
        foreach ($fromUsers as $r) {
            $seen['u_' . $r['user_id']] = true;
            $results[] = $r;
        }
        foreach ($fromBns as $r) {
            if ($r['user_id'] && isset($seen['u_' . $r['user_id']])) continue;
            $key = 'fm_' . $r['fm_member_id'];
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $results[] = $r;
        }
        foreach ($fromPregnant as $r) {
            if ($r['user_id'] && isset($seen['u_' . $r['user_id']])) continue;
            // Deduplicate by fm_member_id (avoid showing same wife twice if both EBF and pregnant)
            $key = 'fm_' . $r['fm_member_id'];
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $results[] = $r;
        }

        return $results;
    }

    /**
     * Elderly citizens (60+ years) from validated families under this BNS.
     */
    public function getQualifiedSeniors(int $bnsId): array {
        // ── SOURCE 1: Registered users (have accounts) ───────────────────────
        $stmt = $this->db->prepare("
            SELECT
                u.user_id,
                NULL AS fm_member_id,
                TRIM(CONCAT(
                    COALESCE(u.last_name,''), ', ',
                    COALESCE(u.first_name,''),
                    IF(u.middle_name IS NOT NULL AND u.middle_name != '', CONCAT(' ', u.middle_name), '')
                )) AS full_name,
                u.gender AS sex,
                u.birthdate AS dob,
                TIMESTAMPDIFF(YEAR, u.birthdate, CURDATE()) AS age_in_years,
                COALESCE(h.purok, fp.purok) AS purok,
                fp.hh_number,
                (SELECT MAX(na.assessment_date)
                 FROM nutrition_assessments na
                 WHERE na.user_id = u.user_id AND na.assessed_type = 'senior') AS last_assessed
            FROM users u
            JOIN user_profiles up ON up.user_id = u.user_id
            LEFT JOIN household_members hm ON hm.user_id = u.user_id
            LEFT JOIN households h ON h.household_id = hm.household_id
            LEFT JOIN family_profiles fp ON fp.source_user_id = u.user_id
            WHERE fp.bns_id = :bns
              AND up.profile_status = 'Validated'
              AND TIMESTAMPDIFF(YEAR, u.birthdate, CURDATE()) >= 60
            ORDER BY purok, u.last_name
        ");
        $stmt->execute([':bns' => $bnsId]);
        $fromUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ── SOURCE 2: BNS-encoded family members (Head/Wife) with DOB making them 60+ ──
        // These people have no user account but have a DOB in family_members
        $stmt2 = $this->db->prepare("
            SELECT
                NULL AS user_id,
                fm.member_id AS fm_member_id,
                TRIM(CONCAT(
                    COALESCE(fm.last_name,''), ', ',
                    COALESCE(fm.first_name,''),
                    IF(fm.middle_name IS NOT NULL AND fm.middle_name != '', CONCAT(' ', fm.middle_name), '')
                )) AS full_name,
                fm.sex,
                fm.dob,
                TIMESTAMPDIFF(YEAR, fm.dob, CURDATE()) AS age_in_years,
                fp.purok,
                fp.hh_number,
                (SELECT MAX(na.assessment_date)
                 FROM nutrition_assessments na
                 WHERE na.fm_member_id = fm.member_id AND na.assessed_type = 'senior') AS last_assessed
            FROM family_profiles fp
            JOIN family_members fm ON fm.family_id = fp.family_id
                AND fm.role IN ('Head','Wife')
                AND fm.dob IS NOT NULL
                AND TIMESTAMPDIFF(YEAR, fm.dob, CURDATE()) >= 60
            WHERE fp.bns_id = :bns
              AND (fm.first_name IS NOT NULL OR fm.last_name IS NOT NULL)
            ORDER BY fp.purok, fm.last_name
        ");
        $stmt2->execute([':bns' => $bnsId]);
        $fromBns = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Merge — registered users first, then BNS-only members
        $seen = [];
        $results = [];
        foreach ($fromUsers as $r) {
            $seen['u_' . $r['user_id']] = true;
            $results[] = $r;
        }
        foreach ($fromBns as $r) {
            $key = 'fm_' . $r['fm_member_id'];
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $results[] = $r;
        }

        return $results;
    }

    // ── Save assessment (Process 5) ───────────────────────────────────────────

    public function save(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO nutrition_assessments (
                bns_id, assessed_type, child_id, fm_member_id, user_id,
                full_name, sex, dob, age_in_months, age_in_years,
                weight_kg, height_cm, muac_cm, assessment_date,
                wfa_status, hfa_status, wfh_status,
                bmi, bmi_status,
                lmp, edc, pre_preg_weight, aog_months,
                weight_gain_kg, weight_gain_status,
                philhealth, is_4ps,
                needs_monitoring, is_at_risk,
                caregiver_name, purok, remarks
            ) VALUES (
                :bns_id, :assessed_type, :child_id, :fm_member_id, :user_id,
                :full_name, :sex, :dob, :age_in_months, :age_in_years,
                :weight_kg, :height_cm, :muac_cm, :assessment_date,
                :wfa_status, :hfa_status, :wfh_status,
                :bmi, :bmi_status,
                :lmp, :edc, :pre_preg_weight, :aog_months,
                :weight_gain_kg, :weight_gain_status,
                :philhealth, :is_4ps,
                :needs_monitoring, :is_at_risk,
                :caregiver_name, :purok, :remarks
            )
        ");
        $stmt->execute([
            ':bns_id'             => $data['bns_id'],
            ':assessed_type'      => $data['assessed_type'],
            ':child_id'           => $data['child_id']           ?? null,
            ':fm_member_id'       => $data['fm_member_id']       ?? null,
            ':user_id'            => $data['user_id']            ?? null,
            ':full_name'          => $data['full_name'],
            ':sex'                => $data['sex'],
            ':dob'                => $data['dob'],
            ':age_in_months'      => $data['age_in_months']      ?? null,
            ':age_in_years'       => $data['age_in_years']       ?? null,
            ':weight_kg'          => $data['weight_kg'],
            ':height_cm'          => $data['height_cm'],
            ':muac_cm'            => $data['muac_cm']            ?? null,
            ':assessment_date'    => $data['assessment_date'],
            ':wfa_status'         => $data['wfa_status']         ?? null,
            ':hfa_status'         => $data['hfa_status']         ?? null,
            ':wfh_status'         => $data['wfh_status']         ?? null,
            ':bmi'                => $data['bmi']                ?? null,
            ':bmi_status'         => $data['bmi_status']         ?? null,
            ':lmp'                => $data['lmp']                ?? null,
            ':edc'                => $data['edc']                ?? null,
            ':pre_preg_weight'    => $data['pre_preg_weight']    ?? null,
            ':aog_months'         => $data['aog_months']         ?? null,
            ':weight_gain_kg'     => $data['weight_gain_kg']     ?? null,
            ':weight_gain_status' => $data['weight_gain_status'] ?? null,
            ':philhealth'         => $data['philhealth']         ?? null,
            ':is_4ps'             => $data['is_4ps']             ?? null,
            ':needs_monitoring'   => $data['needs_monitoring']   ? 1 : 0,
            ':is_at_risk'         => $data['is_at_risk']         ? 1 : 0,
            ':caregiver_name'     => $data['caregiver_name']     ?? null,
            ':purok'              => $data['purok']              ?? null,
            ':remarks'            => $data['remarks']            ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    // ── Form C: At-risk children list ────────────────────────────────────────

    public function getFormC(int $bnsId, ?string $year = null): array {
        $year = $year ?? date('Y');
        $stmt = $this->db->prepare("
            SELECT na.*,
                   YEAR(na.assessment_date) AS yr
            FROM nutrition_assessments na
            WHERE na.bns_id = :bns
              AND na.assessed_type = 'child'
              AND na.is_at_risk = 1
              AND YEAR(na.assessment_date) = :year
            ORDER BY na.purok, na.full_name
        ");
        $stmt->execute([':bns' => $bnsId, ':year' => $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Summary counts for Form C header (MUW, MSt, MW/MAM, SUW, SSt, SW/SAM, OW, Ob).
     */
    public function getFormCSummary(int $bnsId, ?string $year = null): array {
        $year = $year ?? date('Y');
        $stmt = $this->db->prepare("
            SELECT
                SUM(wfa_status = 'UW')     AS MUW,
                SUM(hfa_status = 'St')     AS MSt,
                SUM(wfh_status = 'MAM')    AS MAM,
                SUM(wfa_status = 'SUW')    AS SUW,
                SUM(hfa_status = 'SSt')    AS SSt,
                SUM(wfh_status = 'SAM')    AS SAM,
                SUM(wfh_status = 'OW')     AS OW,
                SUM(wfh_status = 'Ob')     AS Ob,
                COUNT(*)                   AS total
            FROM nutrition_assessments
            WHERE bns_id = :bns
              AND assessed_type = 'child'
              AND is_at_risk = 1
              AND YEAR(assessment_date) = :year
        ");
        $stmt->execute([':bns' => $bnsId, ':year' => $year]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    // ── Monitoring lists (filtered by status) ────────────────────────────────

    /**
     * Get monitoring list filtered by a WHERE clause on nutrition_assessments.
     * $filter examples:
     *   "age_in_months BETWEEN 0 AND 23"
     *   "wfh_status = 'MAM'"
     *   "wfa_status IN ('UW','SUW') AND hfa_status IN ('St','SSt')"
     */
    public function getMonitoringList(int $bnsId, string $filter, ?string $year = null): array {
        $year = $year ?? date('Y');
        $stmt = $this->db->prepare("
            SELECT na.*,
                   mv1.visit_date AS v1_date, mv1.intervention_done AS v1_int, mv1.nutritional_status AS v1_status,
                   mv2.visit_date AS v2_date, mv2.intervention_done AS v2_int, mv2.nutritional_status AS v2_status,
                   mv3.visit_date AS v3_date, mv3.intervention_done AS v3_int, mv3.nutritional_status AS v3_status,
                   mv4.visit_date AS v4_date, mv4.intervention_done AS v4_int, mv4.nutritional_status AS v4_status,
                   mv5.visit_date AS v5_date, mv5.intervention_done AS v5_int, mv5.nutritional_status AS v5_status,
                   mv6.visit_date AS v6_date, mv6.intervention_done AS v6_int, mv6.nutritional_status AS v6_status
            FROM nutrition_assessments na
            LEFT JOIN monitoring_visits mv1 ON mv1.assessment_id = na.assessment_id AND mv1.visit_month_number = 1
            LEFT JOIN monitoring_visits mv2 ON mv2.assessment_id = na.assessment_id AND mv2.visit_month_number = 2
            LEFT JOIN monitoring_visits mv3 ON mv3.assessment_id = na.assessment_id AND mv3.visit_month_number = 3
            LEFT JOIN monitoring_visits mv4 ON mv4.assessment_id = na.assessment_id AND mv4.visit_month_number = 4
            LEFT JOIN monitoring_visits mv5 ON mv5.assessment_id = na.assessment_id AND mv5.visit_month_number = 5
            LEFT JOIN monitoring_visits mv6 ON mv6.assessment_id = na.assessment_id AND mv6.visit_month_number = 6
            WHERE na.bns_id = :bns
              AND na.assessed_type = 'child'
              AND YEAR(na.assessment_date) = :year
              AND ($filter)
            ORDER BY na.purok, na.full_name
        ");
        $stmt->execute([':bns' => $bnsId, ':year' => $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Maternal & Elderly Masterlist queries ─────────────────────────────────

    public function getPregnantMasterlist(int $bnsId, ?string $quarter = null, ?string $year = null): array {
        $year    = $year    ?? date('Y');
        $quarter = $quarter ?? ceil(date('n') / 3);
        $monthStart = ($quarter - 1) * 3 + 1;
        $monthEnd   = $monthStart + 2;
        
        // ── SOURCE 1: Registered users assessed this quarter ─────────────────
        $stmt = $this->db->prepare("
            SELECT DISTINCT na.user_id,
                   u.first_name, u.middle_name, u.last_name,
                   u.contact, u.civil_status,
                   h.spouse_name,
                   na.purok,
                   NULL AS fm_member_id
            FROM nutrition_assessments na
            JOIN users u ON u.user_id = na.user_id
            LEFT JOIN household_members hm ON hm.user_id = na.user_id
            LEFT JOIN households h ON h.household_id = hm.household_id
            WHERE na.bns_id = :bns
              AND na.assessed_type = 'maternal'
              AND YEAR(na.assessment_date) = :year
              AND MONTH(na.assessment_date) BETWEEN :ms AND :me
            ORDER BY na.purok, u.last_name, u.first_name
        ");
        $stmt->execute([':bns' => $bnsId, ':year' => $year, ':ms' => $monthStart, ':me' => $monthEnd]);
        $assessed = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ── SOURCE 2: BNS-encoded female member with is_mother_prog = 1 (not yet assessed) ──
        // Joins on sex='F' so it works whether the female is the Head or the Spouse
        $stmt2 = $this->db->prepare("
            SELECT DISTINCT
                   NULL AS user_id,
                   fm_female.first_name, fm_female.middle_name, fm_female.last_name,
                   NULL AS contact,
                   fm_female.civil_status,
                   CONCAT(fm_other.last_name, ', ', fm_other.first_name) AS spouse_name,
                   fp.purok,
                   fm_female.member_id AS fm_member_id
            FROM family_profiles fp
            JOIN family_members fm_female ON fm_female.family_id = fp.family_id
                AND fm_female.role IN ('Head','Wife')
                AND fm_female.sex = 'F'
            LEFT JOIN family_members fm_other ON fm_other.family_id = fp.family_id
                AND fm_other.role IN ('Head','Wife')
                AND fm_other.sex != 'F'
            WHERE fp.bns_id = :bns
              AND fp.is_mother_prog = 1
              AND (fm_female.first_name IS NOT NULL OR fm_female.last_name IS NOT NULL)
              AND NOT EXISTS (
                  SELECT 1 FROM nutrition_assessments na2
                  WHERE na2.fm_member_id = fm_female.member_id
                    AND na2.bns_id = :bns2
                    AND YEAR(na2.assessment_date) = :year2
                    AND MONTH(na2.assessment_date) BETWEEN :ms2 AND :me2
              )
        ");
        $stmt2->execute([':bns' => $bnsId, ':bns2' => $bnsId, ':year2' => $year, ':ms2' => $monthStart, ':me2' => $monthEnd]);
        $unassessed = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Merge both sources
        $women = array_merge($assessed, $unassessed);
        
        // For each woman, get up to 3 assessments (one per month in the quarter)
        $results = [];
        foreach ($women as $woman) {
            if ($woman['user_id']) {
                // Registered user — look up by user_id
                $assessStmt = $this->db->prepare("
                    SELECT na.*,
                           MONTH(na.assessment_date) - ? + 1 AS month_num
                    FROM nutrition_assessments na
                    WHERE na.user_id = ?
                      AND na.bns_id = ?
                      AND na.assessed_type = 'maternal'
                      AND YEAR(na.assessment_date) = ?
                      AND MONTH(na.assessment_date) BETWEEN ? AND ?
                    ORDER BY na.assessment_date ASC
                    LIMIT 3
                ");
                $assessStmt->execute([$monthStart, $woman['user_id'], $bnsId, $year, $monthStart, $monthEnd]);
            } elseif ($woman['fm_member_id']) {
                // BNS-only wife — look up by fm_member_id
                $assessStmt = $this->db->prepare("
                    SELECT na.*,
                           MONTH(na.assessment_date) - ? + 1 AS month_num
                    FROM nutrition_assessments na
                    WHERE na.fm_member_id = ?
                      AND na.bns_id = ?
                      AND na.assessed_type = 'maternal'
                      AND YEAR(na.assessment_date) = ?
                      AND MONTH(na.assessment_date) BETWEEN ? AND ?
                    ORDER BY na.assessment_date ASC
                    LIMIT 3
                ");
                $assessStmt->execute([$monthStart, $woman['fm_member_id'], $bnsId, $year, $monthStart, $monthEnd]);
            } else {
                continue;
            }
            $assessments = $assessStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Build record with all woman's data plus 3 months of assessments
            $record = array_merge($woman, [
                'dob'        => $assessments[0]['dob']        ?? null,
                'lmp'        => $assessments[0]['lmp']        ?? null,
                'edc'        => $assessments[0]['edc']        ?? null,
                'height_cm'  => $assessments[0]['height_cm']  ?? null,
                'philhealth' => $assessments[0]['philhealth'] ?? null,
                'is_4ps'     => $assessments[0]['is_4ps']     ?? null,
            ]);
            
            // Add data for each month
            foreach ($assessments as $assess) {
                $monthNum = (int)$assess['month_num'];
                $record["month{$monthNum}_aog"]    = $assess['aog_months'];
                $record["month{$monthNum}_weight"]  = $assess['weight_kg'];
                $record["month{$monthNum}_status"]  = $assess['weight_gain_status'] ?? $assess['bmi_status'];
            }
            
            $results[] = $record;
        }
        
        return $results;
    }

    public function getLactatingMasterlist(int $bnsId, ?string $quarter = null, ?string $year = null): array {
        $year    = $year    ?? date('Y');
        $quarter = $quarter ?? ceil(date('n') / 3);
        $monthStart = ($quarter - 1) * 3 + 1;
        $monthEnd   = $monthStart + 2;

        // ── SOURCE 1: Registered users with EBF in health profile ────────────
        $stmt1 = $this->db->prepare("
            SELECT DISTINCT
                   u.user_id,
                   NULL AS fm_member_id,
                   NULL AS family_id,
                   TRIM(CONCAT(
                       COALESCE(u.last_name,''), ', ',
                       COALESCE(u.first_name,''),
                       IF(u.middle_name IS NOT NULL AND u.middle_name != '', CONCAT(' ', u.middle_name), '')
                   )) AS full_name,
                   u.birthdate AS dob,
                   u.contact,
                   uhp.breastfeeding_status,
                   COALESCE(
                       (SELECT h2.purok
                        FROM household_members hm2
                        JOIN households h2 ON h2.household_id = hm2.household_id
                        WHERE hm2.user_id = u.user_id LIMIT 1),
                       (SELECT fp2.purok
                        FROM family_profiles fp2
                        WHERE fp2.source_user_id = u.user_id LIMIT 1)
                   ) AS purok
            FROM users u
            JOIN user_health_profiles uhp ON uhp.user_id = u.user_id
            WHERE u.gender = 'Female'
              AND uhp.breastfeeding_status = 'EBF (Exclusive Breastfeeding)'
              AND (
                  EXISTS (
                      SELECT 1 FROM user_profiles up
                      WHERE up.user_id = u.user_id
                        AND up.assigned_bns_id = :bns
                  )
                  OR EXISTS (
                      SELECT 1 FROM family_profiles fp3
                      WHERE fp3.source_user_id = u.user_id AND fp3.bns_id = :bns2
                  )
              )
        ");
        $stmt1->execute([':bns' => $bnsId, ':bns2' => $bnsId]);
        $fromUsers = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        // ── SOURCE 2: BNS-encoded female member with is_erf = 1 in family_profiles ───
        // Joins on sex='F' so it works whether the female is the Head or the Spouse
        $stmt2 = $this->db->prepare("
            SELECT DISTINCT
                   NULL AS user_id,
                   fm_female.member_id AS fm_member_id,
                   fp.family_id,
                   TRIM(CONCAT(
                       COALESCE(fm_female.last_name,''), ', ',
                       COALESCE(fm_female.first_name,''),
                       IF(fm_female.middle_name IS NOT NULL AND fm_female.middle_name != '', CONCAT(' ', fm_female.middle_name), '')
                   )) AS full_name,
                   fm_female.dob,
                   COALESCE(
                       (SELECT u2.contact FROM users u2 WHERE u2.user_id = fp.source_user_id LIMIT 1),
                       NULL
                   ) AS contact,
                   'EBF (Exclusive Breastfeeding)' AS breastfeeding_status,
                   fp.purok
            FROM family_profiles fp
            JOIN family_members fm_female ON fm_female.family_id = fp.family_id
                AND fm_female.role IN ('Head','Wife')
                AND fm_female.sex = 'F'
            WHERE fp.bns_id = :bns
              AND fp.is_erf = 1
              AND (fm_female.first_name IS NOT NULL OR fm_female.last_name IS NOT NULL)
        ");
        $stmt2->execute([':bns' => $bnsId]);
        $fromBns = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // ── Merge, deduplicate by user_id (prefer registered user record) ────
        $seen = [];
        $mothers = [];

        foreach ($fromUsers as $m) {
            $key = 'u_' . $m['user_id'];
            $seen[$key] = true;
            $mothers[] = $m;
        }

        foreach ($fromBns as $m) {
            // Skip if this wife already has a user account in SOURCE 1
            if ($m['user_id'] && isset($seen['u_' . $m['user_id']])) continue;
            // Deduplicate by family_id + role
            $key = 'f_' . $m['family_id'];
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $mothers[] = $m;
        }

        // ── For each mother, get latest assessment in this quarter ────────────
        $results = [];
        foreach ($mothers as $mother) {
            $assessment = null;

            if ($mother['user_id']) {
                // Registered user — look up by user_id
                $assessStmt = $this->db->prepare("
                    SELECT na.*,
                           TIMESTAMPDIFF(YEAR, na.dob, na.assessment_date) AS age_in_years
                    FROM nutrition_assessments na
                    WHERE na.user_id = :uid
                      AND na.bns_id = :bns
                      AND na.assessed_type = 'maternal'
                      AND YEAR(na.assessment_date) = :year
                      AND MONTH(na.assessment_date) BETWEEN :ms AND :me
                    ORDER BY na.assessment_date DESC
                    LIMIT 1
                ");
                $assessStmt->execute([
                    ':uid'  => $mother['user_id'],
                    ':bns'  => $bnsId,
                    ':year' => $year,
                    ':ms'   => $monthStart,
                    ':me'   => $monthEnd,
                ]);
                $assessment = $assessStmt->fetch(PDO::FETCH_ASSOC) ?: null;
            } elseif ($mother['fm_member_id']) {
                // BNS-only (no account) — look up by fm_member_id
                $assessStmt = $this->db->prepare("
                    SELECT na.*,
                           TIMESTAMPDIFF(YEAR, na.dob, na.assessment_date) AS age_in_years
                    FROM nutrition_assessments na
                    WHERE na.fm_member_id = :fmid
                      AND na.bns_id = :bns
                      AND na.assessed_type = 'maternal'
                      AND YEAR(na.assessment_date) = :year
                      AND MONTH(na.assessment_date) BETWEEN :ms AND :me
                    ORDER BY na.assessment_date DESC
                    LIMIT 1
                ");
                $assessStmt->execute([
                    ':fmid' => $mother['fm_member_id'],
                    ':bns'  => $bnsId,
                    ':year' => $year,
                    ':ms'   => $monthStart,
                    ':me'   => $monthEnd,
                ]);
                $assessment = $assessStmt->fetch(PDO::FETCH_ASSOC) ?: null;
            }

            if ($assessment) {
                $results[] = array_merge($mother, [
                    'dob'             => $assessment['dob'] ?? $mother['dob'],
                    'age_in_years'    => $assessment['age_in_years'],
                    'height_cm'       => $assessment['height_cm'],
                    'weight_kg'       => $assessment['weight_kg'],
                    'bmi'             => $assessment['bmi'],
                    'bmi_status'      => $assessment['bmi_status'],
                    'assessment_date' => $assessment['assessment_date'],
                    'purok'           => $assessment['purok'] ?? $mother['purok'],
                    'full_name'       => $assessment['full_name'] ?? $mother['full_name'],
                ]);
            } else {
                $dob = $mother['dob'];
                $ageYears = $dob ? (int) date_diff(date_create($dob), date_create('today'))->y : null;
                $results[] = array_merge($mother, [
                    'age_in_years'    => $ageYears,
                    'height_cm'       => null,
                    'weight_kg'       => null,
                    'bmi'             => null,
                    'bmi_status'      => null,
                    'assessment_date' => null,
                ]);
            }
        }

        // Sort by purok then name
        usort($results, fn($a, $b) =>
            strcmp($a['purok'] ?? '', $b['purok'] ?? '') ?: strcmp($a['full_name'] ?? '', $b['full_name'] ?? '')
        );

        return $results;
    }

    public function getSeniorMasterlist(int $bnsId, ?string $quarter = null, ?string $year = null): array {
        $year    = $year    ?? date('Y');
        $quarter = $quarter ?? ceil(date('n') / 3);
        $monthStart = ($quarter - 1) * 3 + 1;
        $monthEnd   = $monthStart + 2;
        $stmt = $this->db->prepare("
            SELECT na.*,
                   COALESCE(u.first_name, fm.first_name) AS first_name,
                   COALESCE(u.middle_name, fm.middle_name) AS middle_name,
                   COALESCE(u.last_name, fm.last_name) AS last_name,
                   u.contact,
                   u.civil_status
            FROM nutrition_assessments na
            LEFT JOIN users u ON u.user_id = na.user_id
            LEFT JOIN family_members fm ON fm.member_id = na.fm_member_id
            WHERE na.bns_id = :bns
              AND na.assessed_type = 'senior'
              AND YEAR(na.assessment_date) = :year
              AND MONTH(na.assessment_date) BETWEEN :ms AND :me
            ORDER BY na.purok, na.full_name
        ");
        $stmt->execute([':bns' => $bnsId, ':year' => $year, ':ms' => $monthStart, ':me' => $monthEnd]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── OPT Plus Results: most recent assessment per child ───────────────────

    public function getOPTResults(int $bnsId, ?string $year = null): array {
        $year = $year ?? date('Y');
        $stmt = $this->db->prepare("
            SELECT na.*
            FROM nutrition_assessments na
            WHERE na.bns_id = :bns
              AND na.assessed_type = 'child'
              AND YEAR(na.assessment_date) = :year
              AND na.assessment_id IN (
                  -- Get most recent assessment per child (from household_children)
                  SELECT MAX(na2.assessment_id)
                  FROM nutrition_assessments na2
                  WHERE na2.bns_id = :bns2
                    AND na2.assessed_type = 'child'
                    AND YEAR(na2.assessment_date) = :year2
                    AND na2.child_id IS NOT NULL
                  GROUP BY na2.child_id
                  
                  UNION
                  
                  -- Get most recent assessment per child (from family_members)
                  SELECT MAX(na3.assessment_id)
                  FROM nutrition_assessments na3
                  WHERE na3.bns_id = :bns3
                    AND na3.assessed_type = 'child'
                    AND YEAR(na3.assessment_date) = :year3
                    AND na3.fm_member_id IS NOT NULL
                  GROUP BY na3.fm_member_id
              )
            ORDER BY na.purok, na.full_name
        ");
        $stmt->execute([
            ':bns'   => $bnsId, ':year'  => $year,
            ':bns2'  => $bnsId, ':year2' => $year,
            ':bns3'  => $bnsId, ':year3' => $year,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── P12 Monitoring list ───────────────────────────────────────────────────

    public function getP12List(int $bnsId, ?string $year = null): array {
        $year = $year ?? date('Y');
        $stmt = $this->db->prepare("
            SELECT na.*,
                   mv1.visit_date AS v1_date, mv1.intervention_done AS v1_int, mv1.nutritional_status AS v1_status,
                   mv2.visit_date AS v2_date, mv2.intervention_done AS v2_int, mv2.nutritional_status AS v2_status,
                   mv3.visit_date AS v3_date, mv3.intervention_done AS v3_int, mv3.nutritional_status AS v3_status,
                   mv4.visit_date AS v4_date, mv4.intervention_done AS v4_int, mv4.nutritional_status AS v4_status,
                   mv5.visit_date AS v5_date, mv5.intervention_done AS v5_int, mv5.nutritional_status AS v5_status,
                   mv6.visit_date AS v6_date, mv6.intervention_done AS v6_int, mv6.nutritional_status AS v6_status
            FROM nutrition_assessments na
            LEFT JOIN monitoring_visits mv1 ON mv1.assessment_id = na.assessment_id AND mv1.visit_month_number = 1
            LEFT JOIN monitoring_visits mv2 ON mv2.assessment_id = na.assessment_id AND mv2.visit_month_number = 2
            LEFT JOIN monitoring_visits mv3 ON mv3.assessment_id = na.assessment_id AND mv3.visit_month_number = 3
            LEFT JOIN monitoring_visits mv4 ON mv4.assessment_id = na.assessment_id AND mv4.visit_month_number = 4
            LEFT JOIN monitoring_visits mv5 ON mv5.assessment_id = na.assessment_id AND mv5.visit_month_number = 5
            LEFT JOIN monitoring_visits mv6 ON mv6.assessment_id = na.assessment_id AND mv6.visit_month_number = 6
            WHERE na.bns_id = :bns
              AND na.needs_monitoring = 1
              AND YEAR(na.assessment_date) = :year
            ORDER BY na.purok, na.full_name
        ");
        $stmt->execute([':bns' => $bnsId, ':year' => $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveFollowUp(array $data): void {
        $stmt = $this->db->prepare("
            INSERT INTO monitoring_visits
                (assessment_id, visit_month_number, visit_date, intervention_done, nutritional_status, recorded_by)
            VALUES
                (:aid, :month, :date, :intervention, :status, :bns)
            ON DUPLICATE KEY UPDATE
                visit_date          = VALUES(visit_date),
                intervention_done   = VALUES(intervention_done),
                nutritional_status  = VALUES(nutritional_status),
                recorded_by         = VALUES(recorded_by)
        ");
        $stmt->execute([
            ':aid'          => $data['assessment_id'],
            ':month'        => $data['visit_month_number'],
            ':date'         => $data['visit_date'],
            ':intervention' => $data['intervention_done'] ?? null,
            ':status'       => $data['nutritional_status'] ?? null,
            ':bns'          => $data['recorded_by'],
        ]);
    }

    // ── Recent assessments list ───────────────────────────────────────────────

    public function getRecentByBns(int $bnsId, int $limit = 50): array {
        $stmt = $this->db->prepare("
            SELECT * FROM nutrition_assessments
            WHERE bns_id = :bns
            ORDER BY assessment_date DESC, created_at DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':bns', $bnsId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM nutrition_assessments WHERE assessment_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
