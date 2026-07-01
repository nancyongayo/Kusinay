<?php
/**
 * FeedingProgramModel
 * 
 * Handles data operations for:
 * - Process 13: Planning feeding program (Committee Chair + Secretary)
 * - Process 14: Validating program proposal (Barangay Captain)
 * - Process 15: Conducting feeding program (Sessions)
 * - Process 16: Participating in feeding program (Attendance)
 * - Process 17: Validating nutritional recovery (Nutrition Officer)
 */
class FeedingProgramModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // ========================================================================
    // PROCESS 13: Feeding Program Proposals
    // ========================================================================

    /**
     * Create a new feeding program proposal
     */
    public function createProposal(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO feeding_program_proposals (
                created_by_user_id, bns_id, barangay_code,
                proponent, location,
                proposal_title, program_type, target_beneficiaries, num_beneficiaries,
                implementation_days, start_date, end_date, feeding_schedule,
                estimated_budget, funding_source, resources_needed,
                objectives, rationale, implementation_plan, monitoring_plan,
                signature_data, budget_items, affected_children_data, status
            ) VALUES (
                :created_by, :bns_id, :barangay_code,
                :proponent, :location,
                :title, :program_type, :target_beneficiaries, :num_beneficiaries,
                :implementation_days, :start_date, :end_date, :feeding_schedule,
                :estimated_budget, :funding_source, :resources_needed,
                :objectives, :rationale, :implementation_plan, :monitoring_plan,
                :signature_data, :budget_items, :affected_children_data, :status
            )
        ");
        $stmt->execute([
            ':created_by'             => $data['created_by_user_id'],
            ':bns_id'                 => $data['bns_id'],
            ':barangay_code'          => $data['barangay_code'],
            ':proponent'              => $data['proponent'] ?? null,
            ':location'               => $data['location'] ?? null,
            ':title'                  => $data['proposal_title'],
            ':program_type'           => $data['program_type'],
            ':target_beneficiaries'   => $data['target_beneficiaries'],
            ':num_beneficiaries'      => $data['num_beneficiaries'] ?? 0,
            ':implementation_days'    => $data['implementation_days'] ?? 120,
            ':start_date'             => $data['start_date'],
            ':end_date'               => $data['end_date'],
            ':feeding_schedule'       => $data['feeding_schedule'] ?? null,
            ':estimated_budget'       => $data['estimated_budget'] ?? 0.00,
            ':funding_source'         => $data['funding_source'] ?? null,
            ':resources_needed'       => $data['resources_needed'] ?? null,
            ':objectives'             => $data['objectives'],
            ':rationale'              => $data['rationale'],
            ':implementation_plan'    => $data['implementation_plan'] ?? null,
            ':monitoring_plan'        => $data['monitoring_plan'] ?? null,
            ':signature_data'         => $data['signature_data'] ?? null,
            ':budget_items'           => $data['budget_items'] ?? null,
            ':affected_children_data' => $data['affected_children_data'] ?? null,
            ':status'                 => $data['status'] ?? 'Draft',
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing proposal
     */
    public function updateProposal(int $proposalId, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE feeding_program_proposals SET
                proposal_title       = :title,
                program_type         = :program_type,
                proponent            = :proponent,
                location             = :location,
                target_beneficiaries = :target_beneficiaries,
                num_beneficiaries    = :num_beneficiaries,
                implementation_days  = :implementation_days,
                start_date           = :start_date,
                end_date             = :end_date,
                feeding_schedule     = :feeding_schedule,
                estimated_budget     = :estimated_budget,
                funding_source       = :funding_source,
                resources_needed     = :resources_needed,
                objectives           = :objectives,
                rationale            = :rationale,
                implementation_plan  = :implementation_plan,
                monitoring_plan      = :monitoring_plan,
                signature_data       = :signature_data,
                budget_items         = :budget_items,
                affected_children_data = :affected_children_data
            WHERE proposal_id = :proposal_id
        ");
        return $stmt->execute([
            ':proposal_id'            => $proposalId,
            ':title'                  => $data['proposal_title'],
            ':program_type'           => $data['program_type'],
            ':proponent'              => $data['proponent'] ?? null,
            ':location'               => $data['location'] ?? null,
            ':target_beneficiaries'   => $data['target_beneficiaries'],
            ':num_beneficiaries'      => $data['num_beneficiaries'] ?? 0,
            ':implementation_days'    => $data['implementation_days'] ?? 120,
            ':start_date'             => $data['start_date'],
            ':end_date'               => $data['end_date'],
            ':feeding_schedule'       => $data['feeding_schedule'] ?? null,
            ':estimated_budget'       => $data['estimated_budget'] ?? 0.00,
            ':funding_source'         => $data['funding_source'] ?? null,
            ':resources_needed'       => $data['resources_needed'] ?? null,
            ':objectives'             => $data['objectives'],
            ':rationale'              => $data['rationale'],
            ':implementation_plan'    => $data['implementation_plan'] ?? null,
            ':monitoring_plan'        => $data['monitoring_plan'] ?? null,
            ':signature_data'         => $data['signature_data'] ?? null,
            ':budget_items'           => $data['budget_items'] ?? null,
            ':affected_children_data' => $data['affected_children_data'] ?? null,
        ]);
    }

    /**
     * Submit proposal for review (changes status)
     */
    public function submitProposal(int $proposalId): bool {
        $stmt = $this->db->prepare("
            UPDATE feeding_program_proposals 
            SET status = 'For Review', submitted_at = NOW()
            WHERE proposal_id = :proposal_id
        ");
        return $stmt->execute([':proposal_id' => $proposalId]);
    }

    /**
     * Get proposal by ID
     */
    public function getProposalById(int $proposalId): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                fpp.*,
                u.first_name AS creator_first_name,
                u.last_name AS creator_last_name,
                bns.first_name AS bns_first_name,
                bns.last_name AS bns_last_name
            FROM feeding_program_proposals fpp
            JOIN users u ON u.user_id = fpp.created_by_user_id
            LEFT JOIN users bns ON bns.user_id = fpp.bns_id
            WHERE fpp.proposal_id = :proposal_id
        ");
        $stmt->execute([':proposal_id' => $proposalId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all proposals (with filters)
     */
    public function getProposals(array $filters = []): array {
        $where = [];
        $params = [];
        
        if (!empty($filters['created_by'])) {
            $where[] = "fpp.created_by_user_id = :created_by";
            $params[':created_by'] = $filters['created_by'];
        }
        
        if (!empty($filters['bns_id'])) {
            $where[] = "fpp.bns_id = :bns_id";
            $params[':bns_id'] = $filters['bns_id'];
        }
        
        if (!empty($filters['status'])) {
            $where[] = "fpp.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['barangay_code'])) {
            $where[] = "fpp.barangay_code = :barangay_code";
            $params[':barangay_code'] = $filters['barangay_code'];
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $stmt = $this->db->prepare("
            SELECT 
                fpp.*,
                u.first_name AS creator_first_name,
                u.last_name AS creator_last_name,
                bns.first_name AS bns_first_name,
                bns.last_name AS bns_last_name,
                (SELECT COUNT(*) FROM meeting_minutes mm WHERE mm.proposal_id = fpp.proposal_id) AS minutes_count,
                (SELECT COUNT(*) FROM proposal_validations pv WHERE pv.proposal_id = fpp.proposal_id) AS validation_count
            FROM feeding_program_proposals fpp
            JOIN users u ON u.user_id = fpp.created_by_user_id
            LEFT JOIN users bns ON bns.user_id = fpp.bns_id
            {$whereClause}
            ORDER BY fpp.created_at DESC
        ");
        
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a proposal
     */
    public function deleteProposal(int $proposalId): bool {
        $stmt = $this->db->prepare("DELETE FROM feeding_program_proposals WHERE proposal_id = :proposal_id");
        return $stmt->execute([':proposal_id' => $proposalId]);
    }

    // ========================================================================
    // PROCESS 13: Meeting Minutes
    // ========================================================================

    /**
     * Get all meeting minutes (optionally filtered by recorder)
     */
    public function getAllMinutes(?int $recordedBy = null, ?bool $unreviewedOnly = null): array {
        $where  = [];
        $params = [];

        if ($recordedBy) {
            $where[]                = 'mm.recorded_by_user_id = :recorded_by';
            $params[':recorded_by'] = $recordedBy;
        }

        // Only filter by reviewed status when explicitly requested
        if ($unreviewedOnly === true) {
            $where[] = 'mm.is_reviewed = 0';
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->db->prepare("
            SELECT
                mm.*,
                u.first_name AS recorder_first_name,
                u.last_name  AS recorder_last_name,
                fpp.proposal_title
            FROM meeting_minutes mm
            JOIN users u ON u.user_id = mm.recorded_by_user_id
            LEFT JOIN feeding_program_proposals fpp ON fpp.proposal_id = mm.proposal_id
            {$whereClause}
            ORDER BY mm.meeting_date DESC, mm.meeting_time DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create meeting minutes (proposal_id is optional)
     */
    public function createMeetingMinutes(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO meeting_minutes (
                proposal_id, recorded_by_user_id,
                meeting_date, meeting_time, venue, meeting_type,
                attendees, num_attendees,
                agenda, discussion_summary, decisions_made, action_items, next_meeting_date,
                signature_data
            ) VALUES (
                :proposal_id, :recorded_by,
                :meeting_date, :meeting_time, :venue, :meeting_type,
                :attendees, :num_attendees,
                :agenda, :discussion_summary, :decisions_made, :action_items, :next_meeting_date,
                :signature_data
            )
        ");
        
        $stmt->execute([
            ':proposal_id'         => $data['proposal_id'],
            ':recorded_by'         => $data['recorded_by_user_id'],
            ':meeting_date'        => $data['meeting_date'],
            ':meeting_time'        => $data['meeting_time'],
            ':venue'               => $data['venue'],
            ':meeting_type'        => $data['meeting_type'] ?? 'Planning',
            ':attendees'           => $data['attendees'] ?? null,
            ':num_attendees'       => $data['num_attendees'] ?? 0,
            ':agenda'              => $data['agenda'],
            ':discussion_summary'  => $data['discussion_summary'],
            ':decisions_made'      => $data['decisions_made'],
            ':action_items'        => $data['action_items'] ?? null,
            ':next_meeting_date'   => $data['next_meeting_date'] ?? null,
            ':signature_data'      => $data['signature_data'] ?? null,
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Get meeting minutes by proposal ID
     */
    public function getMeetingMinutesByProposal(int $proposalId): array {
        $stmt = $this->db->prepare("
            SELECT 
                mm.*,
                u.first_name AS recorder_first_name,
                u.last_name AS recorder_last_name
            FROM meeting_minutes mm
            JOIN users u ON u.user_id = mm.recorded_by_user_id
            WHERE mm.proposal_id = :proposal_id
            ORDER BY mm.meeting_date DESC, mm.meeting_time DESC
        ");
        $stmt->execute([':proposal_id' => $proposalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single meeting minute by ID
     */
    public function getMeetingMinuteById(int $minuteId): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                mm.*,
                u.first_name AS recorder_first_name,
                u.last_name AS recorder_last_name
            FROM meeting_minutes mm
            JOIN users u ON u.user_id = mm.recorded_by_user_id
            WHERE mm.minute_id = :minute_id
        ");
        $stmt->execute([':minute_id' => $minuteId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // ========================================================================
    // PROCESS 14: Proposal Validations (Barangay Captain)
    // ========================================================================

    /**
     * Create validation record (approve/reject)
     */
    public function createValidation(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO proposal_validations (
                proposal_id, validated_by_user_id,
                decision, feedback, conditions,
                signature_data, signature_type, ip_address
            ) VALUES (
                :proposal_id, :validated_by,
                :decision, :feedback, :conditions,
                :signature_data, :signature_type, :ip_address
            )
        ");
        
        $stmt->execute([
            ':proposal_id'    => $data['proposal_id'],
            ':validated_by'   => $data['validated_by_user_id'],
            ':decision'       => $data['decision'],
            ':feedback'       => $data['feedback'] ?? null,
            ':conditions'     => $data['conditions'] ?? null,
            ':signature_data' => $data['signature_data'] ?: null,
            ':signature_type' => $data['signature_type'] ?? 'drawn',
            ':ip_address'     => $data['ip_address'] ?? null,
        ]);
        
        $validationId = (int) $this->db->lastInsertId();
        
        // Update proposal status based on decision
        $newStatus = match($data['decision']) {
            'Approved' => 'Approved',
            'Rejected' => 'Rejected',
            'Needs Revision' => 'Draft',
            default => 'For Review'
        };
        
        $this->db->prepare("
            UPDATE feeding_program_proposals 
            SET status = :status 
            WHERE proposal_id = :proposal_id
        ")->execute([
            ':status' => $newStatus,
            ':proposal_id' => $data['proposal_id']
        ]);
        
        return $validationId;
    }

    /**
     * Get validation history for a proposal
     */
    public function getValidationsByProposal(int $proposalId): array {
        $stmt = $this->db->prepare("
            SELECT 
                pv.*,
                u.first_name AS validator_first_name,
                u.last_name AS validator_last_name
            FROM proposal_validations pv
            JOIN users u ON u.user_id = pv.validated_by_user_id
            WHERE pv.proposal_id = :proposal_id
            ORDER BY pv.validated_at DESC
        ");
        $stmt->execute([':proposal_id' => $proposalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get latest validation for a proposal
     */
    public function getLatestValidation(int $proposalId): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                pv.*,
                u.first_name AS validator_first_name,
                u.last_name AS validator_last_name
            FROM proposal_validations pv
            JOIN users u ON u.user_id = pv.validated_by_user_id
            WHERE pv.proposal_id = :proposal_id
            ORDER BY pv.validated_at DESC
            LIMIT 1
        ");
        $stmt->execute([':proposal_id' => $proposalId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // ========================================================================
    // PROCESS 12: Committee Chair Reviews Affected Children
    // ========================================================================

    /**
     * Get affected children for Committee Chair review (with filters)
     * This allows filtering by BNS, barangay, and date range
     * Only shows children whose LATEST assessment shows malnutrition
     */
    public function getAffectedChildrenForCommittee(?int $bnsId = null, ?string $purok = null, ?string $dateFrom = null): array {
        $where = [];
        $params = [];
        
        // Filter by BNS
        if ($bnsId) {
            $where[] = "latest_na.bns_id = :bns_id";
            $params[':bns_id'] = $bnsId;
        }
        
        // Filter by purok
        if ($purok) {
            $where[] = "latest_na.purok = :purok";
            $params[':purok'] = $purok;
        }
        
        // Filter by assessment date
        if ($dateFrom) {
            $where[] = "latest_na.assessment_date >= :date_from";
            $params[':date_from'] = $dateFrom;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $stmt = $this->db->prepare("
            SELECT 
                latest_na.assessment_id,
                latest_na.full_name,
                latest_na.sex,
                latest_na.dob,
                latest_na.age_in_months,
                latest_na.age_in_years,
                latest_na.weight_kg,
                latest_na.height_cm,
                latest_na.muac_cm,
                latest_na.bmi,
                latest_na.wfa_status,
                latest_na.hfa_status,
                latest_na.wfh_status,
                latest_na.bmi_status,
                latest_na.assessment_date,
                latest_na.caregiver_name,
                latest_na.purok,
                latest_na.bns_id,
                bns.first_name AS bns_first_name,
                bns.last_name  AS bns_last_name,
                up.barangay_code,
                fp.source_user_id AS parent_user_id,
                COALESCE(
                    NULLIF(TRIM(CONCAT_WS(' ',
                        wife.first_name,
                        wife.middle_name,
                        wife.last_name
                    )), ''),
                    NULLIF(TRIM(CONCAT_WS(' ',
                        head.first_name,
                        head.middle_name,
                        head.last_name
                    )), ''),
                    latest_na.caregiver_name
                ) AS mother_name
            FROM (
                -- Get only the LATEST assessment per child
                SELECT na1.*
                FROM nutrition_assessments na1
                INNER JOIN (
                    SELECT 
                        COALESCE(child_id, fm_member_id, CONCAT(full_name, dob)) as child_key,
                        MAX(assessment_date) as max_date,
                        MAX(assessment_id) as max_id
                    FROM nutrition_assessments
                    WHERE assessed_type = 'child'
                    GROUP BY child_key
                ) na2 ON COALESCE(na1.child_id, na1.fm_member_id, CONCAT(na1.full_name, na1.dob)) = na2.child_key 
                     AND na1.assessment_date = na2.max_date
                     AND na1.assessment_id = na2.max_id
                WHERE na1.assessed_type = 'child'
            ) latest_na
            LEFT JOIN users bns ON bns.user_id = latest_na.bns_id
            LEFT JOIN user_profiles up ON up.user_id = latest_na.bns_id
            LEFT JOIN family_members fm ON fm.member_id = latest_na.fm_member_id
            LEFT JOIN family_profiles fp ON fp.family_id = fm.family_id
            LEFT JOIN family_members wife ON wife.family_id = fm.family_id AND wife.role = 'Wife'
            LEFT JOIN family_members head ON head.family_id = fm.family_id AND head.role = 'Head'
            {$whereClause}
              -- Only include if LATEST assessment shows malnutrition (is_at_risk = 1)
              " . (!empty($where) ? 'AND' : 'WHERE') . " latest_na.is_at_risk = 1
            ORDER BY latest_na.assessment_date DESC, latest_na.purok ASC, latest_na.full_name ASC
        ");
        
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calculate statistics from affected children array
     */
    public function calculateAffectedChildrenStats(array $children): array {
        $stats = [
            'total' => count($children),
            'moderately_underweight' => 0,
            'severely_underweight' => 0,
            'moderately_stunted' => 0,
            'severely_stunted' => 0,
            'moderately_wasted' => 0,
            'severely_wasted' => 0,
            'overweight' => 0,
            'obese' => 0,
        ];
        
        foreach ($children as $child) {
            // Weight for Age (wfa_status): SUW, UW, Normal, OW
            if ($child['wfa_status'] === 'UW')  $stats['moderately_underweight']++;
            if ($child['wfa_status'] === 'SUW') $stats['severely_underweight']++;
            if ($child['wfa_status'] === 'OW')  $stats['overweight']++;
            
            // Height for Age (hfa_status): SSt, St, Normal, Tall
            if ($child['hfa_status'] === 'St')  $stats['moderately_stunted']++;
            if ($child['hfa_status'] === 'SSt') $stats['severely_stunted']++;
            
            // Weight for Height (wfh_status): SAM, MAM, Normal, OW, Ob
            if ($child['wfh_status'] === 'MAM') $stats['moderately_wasted']++;
            if ($child['wfh_status'] === 'SAM') $stats['severely_wasted']++;
            if ($child['wfh_status'] === 'Ob')  $stats['obese']++;
        }
        
        return $stats;
    }

    // ========================================================================
    // HELPER: Get affected children from nutrition assessments (BNS view)
    // ========================================================================

    /**
     * Get list of affected children (malnourished) for a BNS
     * This reuses existing nutrition assessment data
     * Only shows children whose LATEST assessment shows malnutrition
     */
    public function getAffectedChildren(int $bnsId, ?string $barangayCode = null): array {
        $where = "WHERE fp.bns_id = :bns_id";
        $params = [':bns_id' => $bnsId];
        
        if ($barangayCode) {
            $where .= " AND fp.barangay_code = :barangay_code";
            $params[':barangay_code'] = $barangayCode;
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                fm.member_id,
                fm.first_name,
                fm.middle_name,
                fm.last_name,
                fm.sex,
                fm.dob,
                TIMESTAMPDIFF(YEAR, fm.dob, CURDATE()) AS age_years,
                TIMESTAMPDIFF(MONTH, fm.dob, CURDATE()) AS age_months,
                latest_na.weight_kg,
                latest_na.height_cm,
                latest_na.bmi,
                latest_na.wfa_status AS weight_for_age_status,
                latest_na.hfa_status AS height_for_age_status,
                latest_na.wfh_status AS weight_for_height_status,
                latest_na.bmi_status AS bmi_for_age_status,
                latest_na.assessment_date,
                fp.purok,
                fp.barangay_code
            FROM family_members fm
            JOIN family_profiles fp ON fp.family_id = fm.family_id
            -- Get only the LATEST assessment per child
            LEFT JOIN (
                SELECT 
                    na1.*
                FROM nutrition_assessments na1
                INNER JOIN (
                    SELECT fm_member_id, MAX(assessment_date) as max_date
                    FROM nutrition_assessments
                    WHERE fm_member_id IS NOT NULL
                    GROUP BY fm_member_id
                ) na2 ON na1.fm_member_id = na2.fm_member_id AND na1.assessment_date = na2.max_date
            ) latest_na ON latest_na.fm_member_id = fm.member_id
            {$where}
              AND fm.role = 'Child'
              AND latest_na.assessment_id IS NOT NULL
              -- Only include if LATEST assessment shows malnutrition
              AND (
                  latest_na.wfa_status IN ('SUW', 'UW')
                  OR latest_na.hfa_status IN ('SSt', 'St')
                  OR latest_na.wfh_status IN ('SAM', 'MAM')
                  OR latest_na.bmi_status IN ('SAM', 'MAM')
              )
            ORDER BY latest_na.assessment_date DESC, fm.last_name ASC
        ");
        
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get statistics for affected children
     */
    public function getAffectedChildrenStats(int $bnsId, ?string $barangayCode = null): array {
        $children = $this->getAffectedChildren($bnsId, $barangayCode);
        
        $stats = [
            'total' => count($children),
            'severely_underweight' => 0,
            'underweight' => 0,
            'severely_stunted' => 0,
            'stunted' => 0,
            'severely_wasted' => 0,
            'wasted' => 0,
            'age_0_23_months' => 0,
            'age_24_59_months' => 0,
            'age_60_plus_months' => 0,
        ];
        
        foreach ($children as $child) {
            $ageMonths = (int) $child['age_months'];
            
            if ($ageMonths <= 23) $stats['age_0_23_months']++;
            elseif ($ageMonths <= 59) $stats['age_24_59_months']++;
            else $stats['age_60_plus_months']++;
            
            if ($child['weight_for_age_status'] === 'Severely Underweight') $stats['severely_underweight']++;
            if ($child['weight_for_age_status'] === 'Underweight') $stats['underweight']++;
            if ($child['height_for_age_status'] === 'Severely Stunted') $stats['severely_stunted']++;
            if ($child['height_for_age_status'] === 'Stunted') $stats['stunted']++;
            if (in_array($child['weight_for_height_status'], ['Severely Wasted', 'Wasted']) ||
                in_array($child['bmi_for_age_status'], ['Severely Wasted', 'Wasted'])) {
                if (str_contains($child['weight_for_height_status'] ?? '', 'Severely') ||
                    str_contains($child['bmi_for_age_status'] ?? '', 'Severely')) {
                    $stats['severely_wasted']++;
                } else {
                    $stats['wasted']++;
                }
            }
        }
        
        return $stats;
    }

    // ========================================================================
    // PROCESS 15: Conducting Feeding Program (Sessions)
    // ========================================================================

    /**
     * Create a new feeding session
     */
    public function createSession(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO feeding_program_sessions (
                proposal_id, session_date, activity_name, purok_barangay,
                iec_age_group, iec_others_specify, conducted_by_user_id, prepared_by,
                nutrition_officer_signature, status, remarks
            ) VALUES (
                :proposal_id, :session_date, :activity_name, :purok_barangay,
                :iec_age_group, :iec_others_specify, :conducted_by, :prepared_by,
                :nutrition_officer_signature, :status, :remarks
            )
        ");
        
        $stmt->execute([
            ':proposal_id'                => $data['proposal_id'],
            ':session_date'               => $data['session_date'],
            ':activity_name'              => $data['activity_name'],
            ':purok_barangay'             => $data['purok_barangay'],
            ':iec_age_group'              => $data['iec_age_group'] ?? null,
            ':iec_others_specify'         => $data['iec_others_specify'] ?? null,
            ':conducted_by'               => $data['conducted_by_user_id'],
            ':prepared_by'                => $data['prepared_by'] ?? null,
            ':nutrition_officer_signature'=> $data['nutrition_officer_signature'] ?? null,
            ':status'                     => $data['status'] ?? 'Scheduled',
            ':remarks'                    => $data['remarks'] ?? null,
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update session
     */
    public function updateSession(int $sessionId, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE feeding_program_sessions SET
                session_date = :session_date,
                activity_name = :activity_name,
                purok_barangay = :purok_barangay,
                iec_age_group = :iec_age_group,
                iec_others_specify = :iec_others_specify,
                prepared_by = :prepared_by,
                nutrition_officer_signature = :nutrition_officer_signature,
                status = :status,
                remarks = :remarks
            WHERE session_id = :session_id
        ");
        
        return $stmt->execute([
            ':session_id'                 => $sessionId,
            ':session_date'               => $data['session_date'],
            ':activity_name'              => $data['activity_name'],
            ':purok_barangay'             => $data['purok_barangay'],
            ':iec_age_group'              => $data['iec_age_group'] ?? null,
            ':iec_others_specify'         => $data['iec_others_specify'] ?? null,
            ':prepared_by'                => $data['prepared_by'] ?? null,
            ':nutrition_officer_signature'=> $data['nutrition_officer_signature'] ?? null,
            ':status'                     => $data['status'],
            ':remarks'                    => $data['remarks'] ?? null,
        ]);
    }

    /**
     * Get session by ID
     */
    public function getSessionById(int $sessionId): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                fps.*,
                fpp.proposal_title,
                fpp.program_type,
                fpp.affected_children_data,
                u.first_name AS conductor_first_name,
                u.last_name AS conductor_last_name,
                (SELECT COUNT(*) FROM feeding_program_attendance WHERE session_id = fps.session_id) AS attendance_count
            FROM feeding_program_sessions fps
            JOIN feeding_program_proposals fpp ON fpp.proposal_id = fps.proposal_id
            JOIN users u ON u.user_id = fps.conducted_by_user_id
            WHERE fps.session_id = :session_id
        ");
        $stmt->execute([':session_id' => $sessionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all sessions for a proposal
     */
    public function getSessionsByProposal(int $proposalId): array {
        $stmt = $this->db->prepare("
            SELECT 
                fps.*,
                u.first_name AS conductor_first_name,
                u.last_name AS conductor_last_name,
                (SELECT COUNT(*) FROM feeding_program_attendance WHERE session_id = fps.session_id) AS attendance_count
            FROM feeding_program_sessions fps
            JOIN users u ON u.user_id = fps.conducted_by_user_id
            WHERE fps.proposal_id = :proposal_id
            ORDER BY fps.session_date DESC
        ");
        $stmt->execute([':proposal_id' => $proposalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all sessions (with filters)
     */
    public function getSessions(array $filters = []): array {
        $where = [];
        $params = [];
        
        if (!empty($filters['proposal_id'])) {
            $where[] = "fps.proposal_id = :proposal_id";
            $params[':proposal_id'] = $filters['proposal_id'];
        }
        
        if (!empty($filters['conducted_by'])) {
            $where[] = "fps.conducted_by_user_id = :conducted_by";
            $params[':conducted_by'] = $filters['conducted_by'];
        }
        
        if (!empty($filters['status'])) {
            $where[] = "fps.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['date_from'])) {
            $where[] = "fps.session_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = "fps.session_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $stmt = $this->db->prepare("
            SELECT 
                fps.*,
                fpp.proposal_title,
                fpp.program_type,
                u.first_name AS conductor_first_name,
                u.last_name AS conductor_last_name,
                (SELECT COUNT(*) FROM feeding_program_attendance WHERE session_id = fps.session_id) AS attendance_count
            FROM feeding_program_sessions fps
            JOIN feeding_program_proposals fpp ON fpp.proposal_id = fps.proposal_id
            JOIN users u ON u.user_id = fps.conducted_by_user_id
            {$whereClause}
            ORDER BY fps.session_date DESC
        ");
        
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete session
     */
    public function deleteSession(int $sessionId): bool {
        $stmt = $this->db->prepare("DELETE FROM feeding_program_sessions WHERE session_id = :session_id");
        return $stmt->execute([':session_id' => $sessionId]);
    }

    // ========================================================================
    // PROCESS 16: Participating in Feeding Program (Attendance)
    // ========================================================================

    /**
     * Record attendance for a participant
     */
    public function recordAttendance(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO feeding_program_attendance (
                session_id, proposal_id, child_id, mother_id,
                name_of_client, mother_name, purok,
                pinggang_pinoy, id_kumainments, others,
                signature_data, is_present, time_in, meal_received
            ) VALUES (
                :session_id, :proposal_id, :child_id, :mother_id,
                :name_of_client, :mother_name, :purok,
                :pinggang_pinoy, :id_kumainments, :others,
                :signature_data, :is_present, :time_in, :meal_received
            )
        ");
        
        $stmt->execute([
            ':session_id'      => $data['session_id'],
            ':proposal_id'     => $data['proposal_id'],
            ':child_id'        => $data['child_id'] ?? null,
            ':mother_id'       => $data['mother_id'] ?? null,
            ':name_of_client'  => $data['name_of_client'],
            ':mother_name'     => $data['mother_name'] ?? null,
            ':purok'           => $data['purok'] ?? null,
            ':pinggang_pinoy'  => $data['pinggang_pinoy'] ?? 0,
            ':id_kumainments'  => $data['id_kumainments'] ?? 0,
            ':others'          => $data['others'] ?? null,
            ':signature_data'  => $data['signature_data'] ?? null,
            ':is_present'      => $data['is_present'] ?? 1,
            ':time_in'         => $data['time_in'] ?? null,
            ':meal_received'   => $data['meal_received'] ?? null,
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update attendance record
     */
    public function updateAttendance(int $attendanceId, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE feeding_program_attendance SET
                name_of_client = :name_of_client,
                mother_name = :mother_name,
                purok = :purok,
                pinggang_pinoy = :pinggang_pinoy,
                id_kumainments = :id_kumainments,
                others = :others,
                signature_data = :signature_data,
                is_present = :is_present,
                time_in = :time_in,
                meal_received = :meal_received
            WHERE attendance_id = :attendance_id
        ");
        
        return $stmt->execute([
            ':attendance_id'   => $attendanceId,
            ':name_of_client'  => $data['name_of_client'],
            ':mother_name'     => $data['mother_name'] ?? null,
            ':purok'           => $data['purok'] ?? null,
            ':pinggang_pinoy'  => $data['pinggang_pinoy'] ?? 0,
            ':id_kumainments'  => $data['id_kumainments'] ?? 0,
            ':others'          => $data['others'] ?? null,
            ':signature_data'  => $data['signature_data'] ?? null,
            ':is_present'      => $data['is_present'] ?? 1,
            ':time_in'         => $data['time_in'] ?? null,
            ':meal_received'   => $data['meal_received'] ?? null,
        ]);
    }

    /**
     * Get attendance records for a session
     */
    public function getAttendanceBySession(int $sessionId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM feeding_program_attendance
            WHERE session_id = :session_id
            ORDER BY name_of_client ASC
        ");
        $stmt->execute([':session_id' => $sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get attendance record by ID
     */
    public function getAttendanceById(int $attendanceId): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM feeding_program_attendance
            WHERE attendance_id = :attendance_id
        ");
        $stmt->execute([':attendance_id' => $attendanceId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Delete attendance record
     */
    public function deleteAttendance(int $attendanceId): bool {
        $stmt = $this->db->prepare("DELETE FROM feeding_program_attendance WHERE attendance_id = :attendance_id");
        return $stmt->execute([':attendance_id' => $attendanceId]);
    }

    /**
     * Get attendance statistics for a session
     */
    public function getSessionAttendanceStats(int $sessionId): array {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_records,
                SUM(is_present) as present_count,
                SUM(CASE WHEN is_present = 0 THEN 1 ELSE 0 END) as absent_count,
                SUM(pinggang_pinoy) as pinggang_pinoy_count,
                SUM(id_kumainments) as id_kumainments_count
            FROM feeding_program_attendance
            WHERE session_id = :session_id
        ");
        $stmt->execute([':session_id' => $sessionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get attendance history for a child across all sessions
     */
    public function getChildAttendanceHistory(int $proposalId, string $childName): array {
        $stmt = $this->db->prepare("
            SELECT 
                fpa.*,
                fps.session_date,
                fps.activity_name
            FROM feeding_program_attendance fpa
            JOIN feeding_program_sessions fps ON fps.session_id = fpa.session_id
            WHERE fpa.proposal_id = :proposal_id
              AND fpa.name_of_client = :child_name
            ORDER BY fps.session_date DESC
        ");
        $stmt->execute([
            ':proposal_id' => $proposalId,
            ':child_name' => $childName
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ========================================================================
    // PARENT DASHBOARD: View Child's Feeding Program Participation
    // ========================================================================

    /**
     * Get all children of a parent who are enrolled in feeding programs
     * Returns children with their active feeding program information
     * Each child appears only ONCE even if they have multiple sessions
     */
    public function getParentChildren(int $parentUserId, ?string $parentFullName = null): array {
        $parentFullName = trim((string)$parentFullName);
        $stmt = $this->db->prepare("
            SELECT 
                MIN(TRIM(fpa.name_of_client)) AS child_name,
                MAX(fpp.proposal_id) AS proposal_id,
                MAX(fpp.proposal_title) AS program_title,
                MAX(fpp.start_date) AS start_date,
                MAX(fpp.end_date) AS end_date,
                MAX(fpp.status) AS program_status
            FROM feeding_program_attendance fpa
            JOIN feeding_program_proposals fpp ON fpp.proposal_id = fpa.proposal_id
            WHERE (
                    fpa.mother_id = :parent_user_id
                    OR (
                        :parent_full_name_check != ''
                        AND fpa.mother_id IS NULL
                        AND LOWER(TRIM(fpa.mother_name)) = LOWER(TRIM(:parent_full_name_match))
                    )
                  )
              AND fpp.status = 'Approved'
            GROUP BY LOWER(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(TRIM(fpa.name_of_client), ' ', ''),
                        ',', ''),
                    '.', ''),
                '-', '')
            )
            ORDER BY MAX(fpp.start_date) DESC, MIN(TRIM(fpa.name_of_client)) ASC
        ");
        $stmt->execute([
            ':parent_user_id' => $parentUserId,
            ':parent_full_name_check' => $parentFullName,
            ':parent_full_name_match' => $parentFullName
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get upcoming sessions for a specific child
     * Returns sessions that haven't occurred yet with RSVP status
     */
    public function getChildUpcomingSessions(string $childName, int $parentUserId, ?string $parentFullName = null): array {
        $parentFullName = trim((string)$parentFullName);
        $stmt = $this->db->prepare("
            SELECT 
                fps.session_id,
                MAX(fps.session_date) AS session_date,
                MAX(fps.activity_name) AS activity_name,
                MAX(fps.purok_barangay) AS purok_barangay,
                MIN(fpa.attendance_id) AS attendance_id,
                MAX(fpa.rsvp_status) AS rsvp_status,
                MAX(fpa.is_present) AS is_present
            FROM feeding_program_sessions fps
            JOIN feeding_program_attendance fpa ON fpa.session_id = fps.session_id
            WHERE fpa.name_of_client = :child_name
              AND (
                    fpa.mother_id = :parent_user_id
                    OR (
                        :parent_full_name_check != ''
                        AND fpa.mother_id IS NULL
                        AND LOWER(TRIM(fpa.mother_name)) = LOWER(TRIM(:parent_full_name_match))
                    )
                  )
              AND fps.session_date >= CURDATE()
              AND fps.status != 'Cancelled'
            GROUP BY fps.session_id
            ORDER BY fps.session_date ASC
        ");
        $stmt->execute([
            ':child_name' => $childName,
            ':parent_user_id' => $parentUserId,
            ':parent_full_name_check' => $parentFullName,
            ':parent_full_name_match' => $parentFullName
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get attendance statistics for a specific child
     * Calculates total sessions, attended, missed, and attendance rate
     */
    public function getChildAttendanceStats(string $childName, int $parentUserId, ?string $parentFullName = null): array {
        $parentFullName = trim((string)$parentFullName);
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(DISTINCT fps.session_id) as total,
                COUNT(DISTINCT CASE WHEN fpa.is_present = 1 THEN fps.session_id END) as attended,
                COUNT(DISTINCT CASE WHEN fpa.is_present = 0 THEN fps.session_id END) as missed,
                COUNT(DISTINCT CASE WHEN fpa.is_present IS NULL AND fps.session_date < CURDATE() THEN fps.session_id END) as not_marked
            FROM feeding_program_attendance fpa
            JOIN feeding_program_sessions fps ON fps.session_id = fpa.session_id
            WHERE fpa.name_of_client = :child_name
              AND (
                    fpa.mother_id = :parent_user_id
                    OR (
                        :parent_full_name_check != ''
                        AND fpa.mother_id IS NULL
                        AND LOWER(TRIM(fpa.mother_name)) = LOWER(TRIM(:parent_full_name_match))
                    )
                  )
              AND fps.session_date <= CURDATE()
        ");
        $stmt->execute([
            ':child_name' => $childName,
            ':parent_user_id' => $parentUserId,
            ':parent_full_name_check' => $parentFullName,
            ':parent_full_name_match' => $parentFullName
        ]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $stats;
    }

    /**
     * Get detailed attendance history for a specific child (parent view)
     * Returns all attendance records with session details
     * Optionally filter by specific session
     */
    public function getChildAttendanceHistoryForParent(string $childName, int $parentUserId, ?int $sessionId = null, ?string $parentFullName = null): array {
        $parentFullName = trim((string)$parentFullName);
        $where = "WHERE fpa.name_of_client = :child_name
              AND (
                    fpa.mother_id = :parent_user_id
                    OR (
                        :parent_full_name_check != ''
                        AND fpa.mother_id IS NULL
                        AND LOWER(TRIM(fpa.mother_name)) = LOWER(TRIM(:parent_full_name_match))
                    )
                  )";
        
        $params = [
            ':child_name' => $childName,
            ':parent_user_id' => $parentUserId,
            ':parent_full_name_check' => $parentFullName,
            ':parent_full_name_match' => $parentFullName
        ];
        
        if ($sessionId) {
            $where .= " AND fps.session_id = :session_id";
            $params[':session_id'] = $sessionId;
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                fpa.attendance_id,
                fpa.name_of_client,
                fpa.is_present,
                fpa.rsvp_status,
                fpa.meal_received,
                fpa.time_in,
                fps.session_id,
                fps.session_date,
                fps.activity_name,
                fps.purok_barangay,
                fps.status AS session_status,
                fpp.proposal_id,
                fpp.proposal_title AS program_title
            FROM feeding_program_attendance fpa
            JOIN feeding_program_sessions fps ON fps.session_id = fpa.session_id
            JOIN feeding_program_proposals fpp ON fpp.proposal_id = fpa.proposal_id
            {$where}
            ORDER BY fps.session_date DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ========================================================================
    // PROCESS 17: Validating Nutritional Recovery
    // ========================================================================

    /**
     * Create a nutritional recovery validation record
     * Compares baseline (before feeding program) vs follow-up (after feeding program) assessments
     */
    public function createRecoveryValidation(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO nutritional_recovery_validations (
                child_id, fm_member_id, full_name, proposal_id,
                baseline_assessment_id, baseline_date, baseline_weight_kg, baseline_height_cm,
                baseline_muac_cm, baseline_bmi, baseline_wfa_status, baseline_hfa_status,
                baseline_wfh_status, baseline_bmi_status,
                followup_assessment_id, followup_date, followup_weight_kg, followup_height_cm,
                followup_muac_cm, followup_bmi, followup_wfa_status, followup_hfa_status,
                followup_wfh_status, followup_bmi_status,
                recovery_status, weight_gain_kg, height_gain_cm, muac_gain_cm,
                days_in_program, attendance_rate,
                validated_by_user_id, validation_date, remarks, recommendation
            ) VALUES (
                :child_id, :fm_member_id, :full_name, :proposal_id,
                :baseline_assessment_id, :baseline_date, :baseline_weight_kg, :baseline_height_cm,
                :baseline_muac_cm, :baseline_bmi, :baseline_wfa_status, :baseline_hfa_status,
                :baseline_wfh_status, :baseline_bmi_status,
                :followup_assessment_id, :followup_date, :followup_weight_kg, :followup_height_cm,
                :followup_muac_cm, :followup_bmi, :followup_wfa_status, :followup_hfa_status,
                :followup_wfh_status, :followup_bmi_status,
                :recovery_status, :weight_gain_kg, :height_gain_cm, :muac_gain_cm,
                :days_in_program, :attendance_rate,
                :validated_by_user_id, :validation_date, :remarks, :recommendation
            )
        ");
        
        $stmt->execute([
            ':child_id'                 => $data['child_id'] ?? null,
            ':fm_member_id'             => $data['fm_member_id'] ?? null,
            ':full_name'                => $data['full_name'],
            ':proposal_id'              => $data['proposal_id'],
            ':baseline_assessment_id'   => $data['baseline_assessment_id'] ?? null,
            ':baseline_date'            => $data['baseline_date'],
            ':baseline_weight_kg'       => $data['baseline_weight_kg'] ?? null,
            ':baseline_height_cm'       => $data['baseline_height_cm'] ?? null,
            ':baseline_muac_cm'         => $data['baseline_muac_cm'] ?? null,
            ':baseline_bmi'             => $data['baseline_bmi'] ?? null,
            ':baseline_wfa_status'      => $data['baseline_wfa_status'] ?? null,
            ':baseline_hfa_status'      => $data['baseline_hfa_status'] ?? null,
            ':baseline_wfh_status'      => $data['baseline_wfh_status'] ?? null,
            ':baseline_bmi_status'      => $data['baseline_bmi_status'] ?? null,
            ':followup_assessment_id'   => $data['followup_assessment_id'] ?? null,
            ':followup_date'            => $data['followup_date'],
            ':followup_weight_kg'       => $data['followup_weight_kg'] ?? null,
            ':followup_height_cm'       => $data['followup_height_cm'] ?? null,
            ':followup_muac_cm'         => $data['followup_muac_cm'] ?? null,
            ':followup_bmi'             => $data['followup_bmi'] ?? null,
            ':followup_wfa_status'      => $data['followup_wfa_status'] ?? null,
            ':followup_hfa_status'      => $data['followup_hfa_status'] ?? null,
            ':followup_wfh_status'      => $data['followup_wfh_status'] ?? null,
            ':followup_bmi_status'      => $data['followup_bmi_status'] ?? null,
            ':recovery_status'          => $data['recovery_status'],
            ':weight_gain_kg'           => $data['weight_gain_kg'] ?? null,
            ':height_gain_cm'           => $data['height_gain_cm'] ?? null,
            ':muac_gain_cm'             => $data['muac_gain_cm'] ?? null,
            ':days_in_program'          => $data['days_in_program'] ?? null,
            ':attendance_rate'          => $data['attendance_rate'] ?? null,
            ':validated_by_user_id'     => $data['validated_by_user_id'],
            ':validation_date'          => $data['validation_date'] ?? date('Y-m-d H:i:s'),
            ':remarks'                  => $data['remarks'] ?? null,
            ':recommendation'           => $data['recommendation'] ?? null,
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing recovery validation record
     */
    public function updateRecoveryValidation(int $validationId, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE nutritional_recovery_validations SET
                followup_assessment_id = :followup_assessment_id,
                followup_date = :followup_date,
                followup_weight_kg = :followup_weight_kg,
                followup_height_cm = :followup_height_cm,
                followup_muac_cm = :followup_muac_cm,
                followup_bmi = :followup_bmi,
                followup_wfa_status = :followup_wfa_status,
                followup_hfa_status = :followup_hfa_status,
                followup_wfh_status = :followup_wfh_status,
                followup_bmi_status = :followup_bmi_status,
                recovery_status = :recovery_status,
                weight_gain_kg = :weight_gain_kg,
                height_gain_cm = :height_gain_cm,
                muac_gain_cm = :muac_gain_cm,
                days_in_program = :days_in_program,
                attendance_rate = :attendance_rate,
                remarks = :remarks,
                recommendation = :recommendation
            WHERE validation_id = :validation_id
        ");
        
        return $stmt->execute([
            ':validation_id'            => $validationId,
            ':followup_assessment_id'   => $data['followup_assessment_id'] ?? null,
            ':followup_date'            => $data['followup_date'],
            ':followup_weight_kg'       => $data['followup_weight_kg'] ?? null,
            ':followup_height_cm'       => $data['followup_height_cm'] ?? null,
            ':followup_muac_cm'         => $data['followup_muac_cm'] ?? null,
            ':followup_bmi'             => $data['followup_bmi'] ?? null,
            ':followup_wfa_status'      => $data['followup_wfa_status'] ?? null,
            ':followup_hfa_status'      => $data['followup_hfa_status'] ?? null,
            ':followup_wfh_status'      => $data['followup_wfh_status'] ?? null,
            ':followup_bmi_status'      => $data['followup_bmi_status'] ?? null,
            ':recovery_status'          => $data['recovery_status'],
            ':weight_gain_kg'           => $data['weight_gain_kg'] ?? null,
            ':height_gain_cm'           => $data['height_gain_cm'] ?? null,
            ':muac_gain_cm'             => $data['muac_gain_cm'] ?? null,
            ':days_in_program'          => $data['days_in_program'] ?? null,
            ':attendance_rate'          => $data['attendance_rate'] ?? null,
            ':remarks'                  => $data['remarks'] ?? null,
            ':recommendation'           => $data['recommendation'] ?? null,
        ]);
    }

    /**
     * Get recovery validation by ID
     */
    public function getRecoveryValidationById(int $validationId): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                nrv.*,
                fpp.proposal_title,
                fpp.program_type,
                fpp.start_date AS program_start_date,
                fpp.end_date AS program_end_date,
                u.first_name AS validator_first_name,
                u.last_name AS validator_last_name
            FROM nutritional_recovery_validations nrv
            JOIN feeding_program_proposals fpp ON fpp.proposal_id = nrv.proposal_id
            JOIN users u ON u.user_id = nrv.validated_by_user_id
            WHERE nrv.validation_id = :validation_id
        ");
        $stmt->execute([':validation_id' => $validationId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all recovery validations for a feeding program proposal
     */
    public function getRecoveryValidationsByProposal(int $proposalId): array {
        $stmt = $this->db->prepare("
            SELECT 
                nrv.*,
                u.first_name AS validator_first_name,
                u.last_name AS validator_last_name
            FROM nutritional_recovery_validations nrv
            JOIN users u ON u.user_id = nrv.validated_by_user_id
            WHERE nrv.proposal_id = :proposal_id
            ORDER BY nrv.validation_date DESC, nrv.full_name ASC
        ");
        $stmt->execute([':proposal_id' => $proposalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recovery validation for a specific child in a feeding program
     */
    public function getRecoveryValidationByChild(int $proposalId, string $childName): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                nrv.*,
                fpp.proposal_title,
                u.first_name AS validator_first_name,
                u.last_name AS validator_last_name
            FROM nutritional_recovery_validations nrv
            JOIN feeding_program_proposals fpp ON fpp.proposal_id = nrv.proposal_id
            JOIN users u ON u.user_id = nrv.validated_by_user_id
            WHERE nrv.proposal_id = :proposal_id
              AND nrv.full_name = :child_name
            ORDER BY nrv.validation_date DESC
            LIMIT 1
        ");
        $stmt->execute([
            ':proposal_id' => $proposalId,
            ':child_name' => $childName
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get recovery statistics for a feeding program
     * Returns counts by recovery status
     */
    public function getRecoveryStatsByProposal(int $proposalId): array {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_validations,
                SUM(CASE WHEN recovery_status = 'Recovered' THEN 1 ELSE 0 END) as recovered_count,
                SUM(CASE WHEN recovery_status = 'Improving' THEN 1 ELSE 0 END) as improving_count,
                SUM(CASE WHEN recovery_status = 'No Progress' THEN 1 ELSE 0 END) as no_progress_count,
                SUM(CASE WHEN recovery_status = 'Deteriorating' THEN 1 ELSE 0 END) as deteriorating_count,
                AVG(weight_gain_kg) as avg_weight_gain,
                AVG(height_gain_cm) as avg_height_gain,
                AVG(attendance_rate) as avg_attendance_rate
            FROM nutritional_recovery_validations
            WHERE proposal_id = :proposal_id
        ");
        $stmt->execute([':proposal_id' => $proposalId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get all recovery validations with filters
     */
    public function getRecoveryValidations(array $filters = []): array {
        $where = [];
        $params = [];
        
        if (!empty($filters['proposal_id'])) {
            $where[] = "nrv.proposal_id = :proposal_id";
            $params[':proposal_id'] = $filters['proposal_id'];
        }
        
        if (!empty($filters['validated_by'])) {
            $where[] = "nrv.validated_by_user_id = :validated_by";
            $params[':validated_by'] = $filters['validated_by'];
        }
        
        if (!empty($filters['recovery_status'])) {
            $where[] = "nrv.recovery_status = :recovery_status";
            $params[':recovery_status'] = $filters['recovery_status'];
        }
        
        if (!empty($filters['date_from'])) {
            $where[] = "nrv.validation_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = "nrv.validation_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $stmt = $this->db->prepare("
            SELECT 
                nrv.*,
                fpp.proposal_title,
                fpp.program_type,
                u.first_name AS validator_first_name,
                u.last_name AS validator_last_name
            FROM nutritional_recovery_validations nrv
            JOIN feeding_program_proposals fpp ON fpp.proposal_id = nrv.proposal_id
            JOIN users u ON u.user_id = nrv.validated_by_user_id
            {$whereClause}
            ORDER BY nrv.validation_date DESC, nrv.full_name ASC
        ");
        
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a recovery validation record
     */
    public function deleteRecoveryValidation(int $validationId): bool {
        $stmt = $this->db->prepare("DELETE FROM nutritional_recovery_validations WHERE validation_id = :validation_id");
        return $stmt->execute([':validation_id' => $validationId]);
    }

    /**
     * Normalize a display name for deduplication (ignores spaces, commas, case).
     */
    public function normalizeNameKey(string $name): string {
        return strtolower(preg_replace('/[^a-z0-9]/', '', $name));
    }

    /**
     * Resolve child_id / fm_member_id from attendance display name.
     * Checks nutrition_assessments, household_children, then family_members.
     */
    public function resolveChildIdentityFromName(string $displayName): array {
        $displayName = trim($displayName);
        $empty = ['child_id' => null, 'fm_member_id' => null, 'source' => null];
        if ($displayName === '') {
            return $empty;
        }

        $nameSql = "
            TRIM(CONCAT(
                COALESCE(%s.last_name, ''), ', ',
                COALESCE(%s.first_name, ''),
                IF(%s.middle_name IS NOT NULL AND %s.middle_name != '', CONCAT(' ', %s.middle_name), ''),
                IF(%s.suffix IS NOT NULL AND %s.suffix != '', CONCAT(' ', %s.suffix), '')
            ))
        ";

        // 1) Prior nutrition assessment (child_id or fm_member_id)
        $stmt = $this->db->prepare("
            SELECT child_id, fm_member_id
            FROM nutrition_assessments
            WHERE assessed_type = 'child'
              AND full_name COLLATE utf8mb4_general_ci = :name
            ORDER BY assessment_date DESC, assessment_id DESC
            LIMIT 1
        ");
        $stmt->execute([':name' => $displayName]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && (!empty($row['child_id']) || !empty($row['fm_member_id']))) {
            return [
                'child_id'      => $row['child_id'] ? (int)$row['child_id'] : null,
                'fm_member_id'  => $row['fm_member_id'] ? (int)$row['fm_member_id'] : null,
                'source'        => 'nutrition_assessment',
            ];
        }

        // 2) Registered household child
        $hcName = sprintf($nameSql, 'hc', 'hc', 'hc', 'hc', 'hc', 'hc', 'hc', 'hc');
        $stmt = $this->db->prepare("
            SELECT hc.child_id
            FROM household_children hc
            WHERE {$hcName} COLLATE utf8mb4_general_ci = :name
            LIMIT 1
        ");
        $stmt->execute([':name' => $displayName]);
        $childId = $stmt->fetchColumn();
        if ($childId) {
            return ['child_id' => (int)$childId, 'fm_member_id' => null, 'source' => 'household_children'];
        }

        // 3) BNS-encoded family profile child (no suffix column)
        $fmName = "
            TRIM(CONCAT(
                COALESCE(fm.last_name, ''), ', ',
                COALESCE(fm.first_name, ''),
                IF(fm.middle_name IS NOT NULL AND fm.middle_name != '', CONCAT(' ', fm.middle_name), '')
            ))
        ";
        $stmt = $this->db->prepare("
            SELECT fm.member_id
            FROM family_members fm
            WHERE fm.role = 'Child'
              AND {$fmName} COLLATE utf8mb4_general_ci = :name
            LIMIT 1
        ");
        $stmt->execute([':name' => $displayName]);
        $fmId = $stmt->fetchColumn();
        if ($fmId) {
            return ['child_id' => null, 'fm_member_id' => (int)$fmId, 'source' => 'family_members'];
        }

        return $empty;
    }

    /**
     * Get children eligible for recovery validation
     * Returns children who participated in a feeding program but don't have recovery validation yet
     */
    public function getChildrenEligibleForRecoveryValidation(int $proposalId): array {
        $stmt = $this->db->prepare("
            SELECT
                MIN(TRIM(fpa.name_of_client)) AS full_name,
                MAX(fpa.child_id) AS child_id,
                MAX(fpa.mother_id) AS mother_id,
                COUNT(DISTINCT fpa.session_id) AS sessions_attended,
                (COUNT(DISTINCT fpa.session_id) * 100.0 /
                    NULLIF((SELECT COUNT(*) FROM feeding_program_sessions WHERE proposal_id = :proposal_id1), 0)
                ) AS attendance_rate
            FROM feeding_program_attendance fpa
            WHERE fpa.proposal_id = :proposal_id2
              AND fpa.is_present = 1
              AND NOT EXISTS (
                  SELECT 1 FROM nutritional_recovery_validations nrv
                  WHERE nrv.proposal_id = fpa.proposal_id
                    AND nrv.full_name COLLATE utf8mb4_general_ci = fpa.name_of_client COLLATE utf8mb4_general_ci
              )
            GROUP BY LOWER(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(TRIM(fpa.name_of_client), ' ', ''),
                        ',', ''),
                    '.', ''),
                '-', '')
            )
            ORDER BY MIN(TRIM(fpa.name_of_client)) ASC
        ");
        $stmt->execute([
            ':proposal_id1' => $proposalId,
            ':proposal_id2' => $proposalId,
        ]);

        $children = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $resolved = $this->resolveChildIdentityFromName($row['full_name']);
            if (empty($row['child_id']) && !empty($resolved['child_id'])) {
                $row['child_id'] = $resolved['child_id'];
            }
            if (empty($row['fm_member_id']) && !empty($resolved['fm_member_id'])) {
                $row['fm_member_id'] = $resolved['fm_member_id'];
            }
            $row['can_assess'] = !empty($row['child_id']) || !empty($row['fm_member_id']);
            $children[] = $row;
        }
        return $children;
    }

    /**
     * Auto-calculate recovery status based on measurements
     * Helper function to determine recovery status from baseline vs followup data
     */
    public function calculateRecoveryStatus(array $baseline, array $followup): string {
        $improvements = 0;
        $deteriorations = 0;
        
        // Check weight gain
        if (isset($baseline['weight_kg']) && isset($followup['weight_kg'])) {
            $weightGain = $followup['weight_kg'] - $baseline['weight_kg'];
            if ($weightGain > 0.5) $improvements++;
            elseif ($weightGain < -0.2) $deteriorations++;
        }
        
        // Check height gain
        if (isset($baseline['height_cm']) && isset($followup['height_cm'])) {
            $heightGain = $followup['height_cm'] - $baseline['height_cm'];
            if ($heightGain > 0.5) $improvements++;
            elseif ($heightGain < 0) $deteriorations++;
        }
        
        // Check nutritional status improvements
        $statusFields = ['wfa_status', 'hfa_status', 'wfh_status', 'bmi_status'];
        foreach ($statusFields as $field) {
            if (isset($baseline[$field]) && isset($followup[$field])) {
                $baselineStatus = $baseline[$field];
                $followupStatus = $followup[$field];
                
                // Check if status improved (e.g., from SUW to UW, or UW to Normal)
                if ($this->isStatusImproved($baselineStatus, $followupStatus)) {
                    $improvements++;
                } elseif ($this->isStatusWorsened($baselineStatus, $followupStatus)) {
                    $deteriorations++;
                }
            }
        }
        
        // Determine overall recovery status
        if ($deteriorations > 0) {
            return 'Deteriorating';
        } elseif ($improvements >= 3) {
            return 'Recovered';
        } elseif ($improvements >= 1) {
            return 'Improving';
        } else {
            return 'No Progress';
        }
    }

    /**
     * Helper: Check if nutritional status improved
     */
    private function isStatusImproved(string $baseline, string $followup): bool {
        $statusHierarchy = [
            'SAM' => 1, 'SUW' => 1, 'SSt' => 1,
            'MAM' => 2, 'UW' => 2, 'St' => 2,
            'Normal' => 3,
            'OW' => 2, 'Ob' => 1, 'Tall' => 3
        ];
        
        $baselineLevel = $statusHierarchy[$baseline] ?? 3;
        $followupLevel = $statusHierarchy[$followup] ?? 3;
        
        return $followupLevel > $baselineLevel;
    }

    /**
     * Helper: Check if nutritional status worsened
     */
    private function isStatusWorsened(string $baseline, string $followup): bool {
        $statusHierarchy = [
            'SAM' => 1, 'SUW' => 1, 'SSt' => 1,
            'MAM' => 2, 'UW' => 2, 'St' => 2,
            'Normal' => 3,
            'OW' => 2, 'Ob' => 1, 'Tall' => 3
        ];
        
        $baselineLevel = $statusHierarchy[$baseline] ?? 3;
        $followupLevel = $statusHierarchy[$followup] ?? 3;
        
        return $followupLevel < $baselineLevel;
    }

    // ========================================================================
    // PROCESS 16: QR Code Attendance Support
    // ========================================================================

    /**
     * Get all attendance records for a session
     * Used for QR code generation
     */
    public function getSessionAttendance(int $sessionId): array {
        $stmt = $this->db->prepare("
            SELECT 
                fpa.*,
                fpa.attendance_id as participant_id
            FROM feeding_program_attendance fpa
            WHERE fpa.session_id = :session_id
            ORDER BY fpa.name_of_client ASC
        ");
        $stmt->execute([':session_id' => $sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
