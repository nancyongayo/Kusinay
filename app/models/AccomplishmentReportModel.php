<?php
require_once __DIR__ . '/../../config/database.php';

class AccomplishmentReportModel {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // ── Auto-compute counts from nutrition_assessments + family profiles ─────

    public function computeFromAssessments(int $bnsId, int $month, int $year): array {
        // 1. From nutrition_assessments (actual weighed/measured this month)
        $stmt = $this->db->prepare("
            SELECT
                SUM(assessed_type='child' AND age_in_months BETWEEN 0 AND 23)  AS ps_0_23_weighed,
                SUM(assessed_type='child' AND age_in_months BETWEEN 24 AND 59) AS ps_24_59_weighed,
                SUM(assessed_type='child' AND is_at_risk=1)                    AS ps_malnourished,
                SUM(assessed_type='child' AND wfh_status='MAM')                AS total_mam,
                SUM(assessed_type='child' AND wfh_status='SAM')                AS total_sam,
                SUM(assessed_type='maternal' AND pregnancy_status IS NOT NULL
                    AND pregnancy_status != '')                                 AS pregnant_new,
                SUM(assessed_type='maternal' AND (breastfeeding_status='Exclusively Breastfeeding'
                    OR breastfeeding_status='Mixed Feeding'))                   AS lactating_new,
                SUM(assessed_type='senior')                                    AS elderly_assessed
            FROM nutrition_assessments na
            LEFT JOIN user_health_profiles uhp ON uhp.user_id = na.user_id
            WHERE na.bns_id = :bns
              AND MONTH(na.assessment_date) = :month
              AND YEAR(na.assessment_date)  = :year
        ");
        $stmt->execute([':bns' => $bnsId, ':month' => $month, ':year' => $year]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $result = array_map('intval', $row ?: []);

        // 2. Auto-count Adolescents (10-19 yrs) from BOTH sources under this BNS
        // Source A: household_children (no account) aged 10-19 years
        $stmtA = $this->db->prepare("
            SELECT COUNT(DISTINCT hc.child_id)
            FROM household_children hc
            JOIN households h ON h.household_id = hc.household_id
            JOIN household_members hm ON hm.household_id = h.household_id
            JOIN users u ON u.user_id = hm.user_id
            JOIN user_profiles up ON up.user_id = u.user_id
            JOIN family_profiles fp ON fp.source_user_id = u.user_id
            WHERE fp.bns_id = :bns
              AND up.profile_status = 'Validated'
              AND TIMESTAMPDIFF(YEAR, hc.dob, CURDATE()) BETWEEN 10 AND 19
        ");
        $stmtA->execute([':bns' => $bnsId]);
        $adolFromChildren = (int)$stmtA->fetchColumn();

        // Source B: registered users in households under this BNS aged 10-19 years
        $stmtB = $this->db->prepare("
            SELECT COUNT(DISTINCT u.user_id)
            FROM users u
            JOIN user_profiles up ON up.user_id = u.user_id
            JOIN household_members hm ON hm.user_id = u.user_id
            JOIN family_profiles fp ON fp.source_user_id = u.user_id
            WHERE fp.bns_id = :bns
              AND up.profile_status = 'Validated'
              AND TIMESTAMPDIFF(YEAR, u.birthdate, CURDATE()) BETWEEN 10 AND 19
        ");
        $stmtB->execute([':bns' => $bnsId]);
        $adolFromUsers = (int)$stmtB->fetchColumn();

        $result['adolescents'] = $adolFromChildren + $adolFromUsers;

        // 3. Auto-count Adults (20-59 yrs) from registered users under this BNS
        // (excluding pregnant/lactating already counted as maternal)
        $stmtC = $this->db->prepare("
            SELECT COUNT(DISTINCT u.user_id)
            FROM users u
            JOIN user_profiles up ON up.user_id = u.user_id
            JOIN household_members hm ON hm.user_id = u.user_id
            JOIN family_profiles fp ON fp.source_user_id = u.user_id
            LEFT JOIN user_health_profiles uhp ON uhp.user_id = u.user_id
            WHERE fp.bns_id = :bns
              AND up.profile_status = 'Validated'
              AND TIMESTAMPDIFF(YEAR, u.birthdate, CURDATE()) BETWEEN 20 AND 59
              AND (uhp.pregnancy_status IS NULL
                   OR uhp.pregnancy_status NOT IN ('Pregnant 1st Trimester','Pregnant 2nd Trimester','Pregnant 3rd Trimester'))
              AND (uhp.breastfeeding_status IS NULL
                   OR uhp.breastfeeding_status NOT IN ('Exclusively Breastfeeding','Mixed Feeding'))
        ");
        $stmtC->execute([':bns' => $bnsId]);
        $result['adults'] = (int)$stmtC->fetchColumn();

        return $result;
    }

    // ── Get or create draft for a month ──────────────────────────────────────

    public function getOrCreate(int $bnsId, int $month, int $year): array {
        $stmt = $this->db->prepare("
            SELECT * FROM accomplishment_reports
            WHERE bns_id = :bns AND report_month = :month AND report_year = :year
            LIMIT 1
        ");
        $stmt->execute([':bns' => $bnsId, ':month' => $month, ':year' => $year]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existing) return $existing;

        // Auto-compute and create draft
        $computed = $this->computeFromAssessments($bnsId, $month, $year);
        $this->db->prepare("
            INSERT INTO accomplishment_reports
                (bns_id, report_month, report_year, status,
                 ps_0_23_weighed, ps_24_59_weighed, ps_malnourished,
                 total_mam, total_sam, pregnant_new, lactating_new, elderly_assessed,
                 adolescents, adults)
            VALUES
                (:bns, :month, :year, 'Draft',
                 :p023, :p2459, :pmal, :mam, :sam, :preg, :lact, :eld,
                 :adol, :adult)
        ")->execute([
            ':bns'   => $bnsId, ':month' => $month, ':year' => $year,
            ':p023'  => $computed['ps_0_23_weighed']  ?? 0,
            ':p2459' => $computed['ps_24_59_weighed'] ?? 0,
            ':pmal'  => $computed['ps_malnourished']  ?? 0,
            ':mam'   => $computed['total_mam']        ?? 0,
            ':sam'   => $computed['total_sam']        ?? 0,
            ':preg'  => $computed['pregnant_new']     ?? 0,
            ':lact'  => $computed['lactating_new']    ?? 0,
            ':eld'   => $computed['elderly_assessed'] ?? 0,
            ':adol'  => $computed['adolescents']      ?? 0,
            ':adult' => $computed['adults']           ?? 0,
        ]);
        $id = (int)$this->db->lastInsertId();
        return $this->getById($id);
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM accomplishment_reports WHERE report_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function save(int $reportId, array $data): void {
        $fields = [
            'mam_new_admission','mam_non_cured','mam_defaulter','mam_died',
            'sam_new_admission','sam_non_cured','sam_died',
            'mam_monitored','sam_monitored',
            'cvd_patients','families_malnourished','adolescents','adults',
            'infants_vita','children_vita','deworm_1_4','deworm_5_9','deworm_10_19',
            'monthly_meetings','remarks',
        ];
        $set = implode(', ', array_map(fn($f) => "`$f` = :$f", $fields));
        $params = [':report_id' => $reportId];
        foreach ($fields as $f) {
            $params[":$f"] = $data[$f] ?? ($f === 'remarks' ? null : 0);
        }
        $this->db->prepare("UPDATE accomplishment_reports SET $set WHERE report_id = :report_id")
                 ->execute($params);
    }

    public function submit(int $reportId): void {
        $this->db->prepare("
            UPDATE accomplishment_reports
            SET status='Submitted', submitted_at=NOW()
            WHERE report_id=? AND status IN ('Draft','Returned')
        ")->execute([$reportId]);
    }

    public function approve(int $reportId, int $reviewerId, ?string $signature = null): void {
        $this->db->prepare("
            UPDATE accomplishment_reports
            SET status='Approved', reviewed_by=?, reviewed_at=NOW(), return_reason=NULL,
                no2_signature=?
            WHERE report_id=? AND status='Submitted'
        ")->execute([$reviewerId, $signature, $reportId]);
    }

    public function returnReport(int $reportId, int $reviewerId, string $reason): void {
        $this->db->prepare("
            UPDATE accomplishment_reports
            SET status='Returned', reviewed_by=?, reviewed_at=NOW(), return_reason=?
            WHERE report_id=? AND status='Submitted'
        ")->execute([$reviewerId, $reason, $reportId]);
    }

    // ── Lists ─────────────────────────────────────────────────────────────────

    public function getByBns(int $bnsId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM accomplishment_reports
            WHERE bns_id = :bns
            ORDER BY report_year DESC, report_month DESC
        ");
        $stmt->execute([':bns' => $bnsId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingForNO2(): array {
        $stmt = $this->db->prepare("
            SELECT ar.*, u.first_name, u.last_name
            FROM accomplishment_reports ar
            JOIN users u ON u.user_id = ar.bns_id
            WHERE ar.status = 'Submitted'
            ORDER BY ar.submitted_at ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllForNO2(): array {
        $stmt = $this->db->prepare("
            SELECT ar.*, u.first_name, u.last_name
            FROM accomplishment_reports ar
            JOIN users u ON u.user_id = ar.bns_id
            ORDER BY ar.report_year DESC, ar.report_month DESC, ar.status
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
