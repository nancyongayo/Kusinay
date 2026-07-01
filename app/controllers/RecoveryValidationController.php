<?php
/**
 * RecoveryValidationController
 * 
 * Handles Process 17: Validating Nutritional Recovery
 * Primary User: Nutrition Officer II
 * Secondary User: BNS Staff (read-only)
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Security.php';
require_once __DIR__ . '/../models/FeedingProgramModel.php';
require_once __DIR__ . '/../models/NutritionAssessmentModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class RecoveryValidationController {

    private PDO $db;
    private FeedingProgramModel $feedingModel;
    private NutritionAssessmentModel $assessmentModel;
    private NotificationModel $notifModel;

    public function __construct() {
        $this->db              = getDBConnection();
        $this->feedingModel    = new FeedingProgramModel($this->db);
        $this->assessmentModel = new NutritionAssessmentModel($this->db);
        $this->notifModel      = new NotificationModel($this->db);
    }

    private function requireNO2(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Nutrition Officer II') {
            header('Location: index.php?action=login'); exit;
        }
    }

    private function requireBNS(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BNS Staff') {
            header('Location: index.php?action=login'); exit;
        }
    }

    /** Same barangay access as Feeding Programs list / sessions (not proposal owner only). */
    private function assertBNSCanAccessProposal(?array $proposal): void {
        if (!$proposal) {
            $_SESSION['flash_error'] = 'Feeding program not found.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }
        $userBarangay = $_SESSION['barangay_code'] ?? '';
        if (($proposal['barangay_code'] ?? '') !== $userBarangay) {
            $_SESSION['flash_error'] = 'Access denied. This program belongs to a different barangay.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }
    }

    // ========================================================================
    // NUTRITION OFFICER II - Main Dashboard
    // ========================================================================

    /**
     * Show Recovery Validation Dashboard (NO II)
     * Lists all feeding programs with recovery validation status
     */
    public function showDashboard(): void {
        $this->requireNO2();

        // Get all approved feeding programs
        $proposals = $this->feedingModel->getProposals(['status' => 'Approved']);

        // For each proposal, get recovery validation stats and ready count
        foreach ($proposals as &$proposal) {
            $stats = $this->feedingModel->getRecoveryStatsByProposal($proposal['proposal_id']);
            $proposal['recovery_stats'] = $stats;

            $eligible = $this->feedingModel->getChildrenEligibleForRecoveryValidation($proposal['proposal_id']);
            $readyCount = 0;
            foreach ($eligible as $child) {
                $paired = $this->resolveBaselineAndFollowup(
                    $child['full_name'],
                    $proposal['start_date'],
                    $proposal['end_date']
                );
                if ($paired['baseline'] && $paired['followup']) {
                    $readyCount++;
                }
            }
            $proposal['eligible_count'] = count($eligible);
            $proposal['ready_count'] = $readyCount;
        }
        unset($proposal);

        $pageTitle = 'Recovery Validation';
        $activeNav = 'recovery';
        include __DIR__ . '/../views/nutrition/recovery_dashboard.php';
    }

    /**
     * Show list of children eligible for recovery validation
     */
    public function showEligibleChildren(): void {
        $this->requireNO2();
        $proposalId = (int)($_GET['proposal_id'] ?? 0);

        $proposal = $this->feedingModel->getProposalById($proposalId);
        if (!$proposal) {
            $_SESSION['flash_error'] = 'Feeding program not found.';
            header('Location: index.php?action=recoveryValidation'); exit;
        }

        // Get children eligible for validation
        $eligibleChildren = $this->feedingModel->getChildrenEligibleForRecoveryValidation($proposalId);

        // For each child, pair baseline + follow-up assessments
        foreach ($eligibleChildren as &$child) {
            $paired = $this->resolveBaselineAndFollowup(
                $child['full_name'],
                $proposal['start_date'],
                $proposal['end_date']
            );
            $child['baseline'] = $paired['baseline'];
            $child['followup'] = $paired['followup'];
            $child['ready_for_validation'] = ($paired['baseline'] && $paired['followup']);
        }
        unset($child); // prevent foreach-by-reference corrupting the list in the view

        $pageTitle = 'Eligible Children - ' . $proposal['proposal_title'];
        $activeNav = 'recovery';
        include __DIR__ . '/../views/nutrition/recovery_eligible_list.php';
    }

    /**
     * Show recovery validation form
     */
    public function showValidationForm(): void {
        $this->requireNO2();
        $proposalId = (int)($_GET['proposal_id'] ?? 0);
        $childName  = $_GET['child_name'] ?? '';

        $proposal = $this->feedingModel->getProposalById($proposalId);
        if (!$proposal) {
            $_SESSION['flash_error'] = 'Feeding program not found.';
            header('Location: index.php?action=recoveryValidation'); exit;
        }

        $paired = $this->resolveBaselineAndFollowup($childName, $proposal['start_date'], $proposal['end_date']);
        $baseline = $paired['baseline'];
        $followup = $paired['followup'];

        if (!$baseline || !$followup) {
            $_SESSION['flash_error'] = 'Missing baseline or follow-up assessment for this child.';
            header("Location: index.php?action=recoveryEligibleList&proposal_id=$proposalId"); exit;
        }

        // Get attendance data
        $attendanceHistory = $this->feedingModel->getChildAttendanceHistory($proposalId, $childName);
        $totalSessions = count($this->feedingModel->getSessionsByProposal($proposalId));
        $attendedSessions = count(array_filter($attendanceHistory, fn($a) => $a['is_present'] == 1));
        $attendanceRate = $totalSessions > 0 ? ($attendedSessions / $totalSessions * 100) : 0;

        // Calculate days in program
        $startDate = new DateTime($proposal['start_date']);
        $endDate = new DateTime($proposal['end_date']);
        $daysInProgram = $startDate->diff($endDate)->days;

        // Auto-calculate recovery status
        $autoRecoveryStatus = $this->feedingModel->calculateRecoveryStatus(
            [
                'weight_kg' => $baseline['weight_kg'],
                'height_cm' => $baseline['height_cm'],
                'wfa_status' => $baseline['wfa_status'],
                'hfa_status' => $baseline['hfa_status'],
                'wfh_status' => $baseline['wfh_status'],
                'bmi_status' => $baseline['bmi_status']
            ],
            [
                'weight_kg' => $followup['weight_kg'],
                'height_cm' => $followup['height_cm'],
                'wfa_status' => $followup['wfa_status'],
                'hfa_status' => $followup['hfa_status'],
                'wfh_status' => $followup['wfh_status'],
                'bmi_status' => $followup['bmi_status']
            ]
        );

        // Calculate gains
        $weightGain = $followup['weight_kg'] - $baseline['weight_kg'];
        $heightGain = $followup['height_cm'] - $baseline['height_cm'];
        $muacGain = (isset($baseline['muac_cm'], $followup['muac_cm'])
            && $baseline['muac_cm'] !== '' && $followup['muac_cm'] !== '')
            ? (float)$followup['muac_cm'] - (float)$baseline['muac_cm']
            : null;

        // Get diet record (Process 22)
        $mealPlanModel = new MealPlanModel($this->db);
        $childId = $baseline['child_id'] ?? 0;
        $dietRecord = [];
        if ($childId) {
            $dietRecord = $mealPlanModel->getDietRecordByChild(
                (int)$childId, 
                $proposal['start_date'], 
                $proposal['end_date']
            );
        }

        $pageTitle = 'Recovery Validation - ' . $childName;
        $activeNav = 'recovery';
        include __DIR__ . '/../views/nutrition/recovery_validation_form.php';
    }

    /**
     * Save recovery validation
     */
    public function saveValidation(): void {
        $this->requireNO2();
        Security::verifyCsrf();
        $no2Id = $_SESSION['user_id'];

        $data = [
            'child_id'                 => $_POST['child_id'] ?? null,
            'fm_member_id'             => $_POST['fm_member_id'] ?? null,
            'full_name'                => $_POST['full_name'],
            'proposal_id'              => (int)$_POST['proposal_id'],
            'baseline_assessment_id'   => (int)$_POST['baseline_assessment_id'],
            'baseline_date'            => $_POST['baseline_date'],
            'baseline_weight_kg'       => (float)$_POST['baseline_weight_kg'],
            'baseline_height_cm'       => (float)$_POST['baseline_height_cm'],
            'baseline_muac_cm'         => !empty($_POST['baseline_muac_cm']) ? (float)$_POST['baseline_muac_cm'] : null,
            'baseline_bmi'             => !empty($_POST['baseline_bmi']) ? (float)$_POST['baseline_bmi'] : null,
            'baseline_wfa_status'      => $_POST['baseline_wfa_status'] ?? null,
            'baseline_hfa_status'      => $_POST['baseline_hfa_status'] ?? null,
            'baseline_wfh_status'      => $_POST['baseline_wfh_status'] ?? null,
            'baseline_bmi_status'      => $_POST['baseline_bmi_status'] ?? null,
            'followup_assessment_id'   => (int)$_POST['followup_assessment_id'],
            'followup_date'            => $_POST['followup_date'],
            'followup_weight_kg'       => (float)$_POST['followup_weight_kg'],
            'followup_height_cm'       => (float)$_POST['followup_height_cm'],
            'followup_muac_cm'         => !empty($_POST['followup_muac_cm']) ? (float)$_POST['followup_muac_cm'] : null,
            'followup_bmi'             => !empty($_POST['followup_bmi']) ? (float)$_POST['followup_bmi'] : null,
            'followup_wfa_status'      => $_POST['followup_wfa_status'] ?? null,
            'followup_hfa_status'      => $_POST['followup_hfa_status'] ?? null,
            'followup_wfh_status'      => $_POST['followup_wfh_status'] ?? null,
            'followup_bmi_status'      => $_POST['followup_bmi_status'] ?? null,
            'recovery_status'          => $_POST['recovery_status'],
            'weight_gain_kg'           => (float)$_POST['weight_gain_kg'],
            'height_gain_cm'           => (float)$_POST['height_gain_cm'],
            'muac_gain_cm'             => !empty($_POST['muac_gain_cm']) ? (float)$_POST['muac_gain_cm'] : null,
            'days_in_program'          => (int)$_POST['days_in_program'],
            'attendance_rate'          => (float)$_POST['attendance_rate'],
            'validated_by_user_id'     => $no2Id,
            'remarks'                  => trim($_POST['remarks'] ?? ''),
            'recommendation'           => trim($_POST['recommendation'] ?? '')
        ];

        try {
            $validationId = $this->feedingModel->createRecoveryValidation($data);

            // Notify BNS who conducted the feeding program
            $proposal = $this->feedingModel->getProposalById($data['proposal_id']);
            if ($proposal && $proposal['bns_id']) {
                $this->notifModel->create(
                    (int)$proposal['bns_id'],
                    'recovery_validated',
                    $validationId,
                    "Recovery validation completed for {$data['full_name']} in {$proposal['proposal_title']}. Status: {$data['recovery_status']}"
                );
            }

            $_SESSION['flash'] = 'Recovery validation saved successfully.';
            header("Location: index.php?action=recoveryValidationList&proposal_id={$data['proposal_id']}");
            exit;

        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error saving validation: ' . $e->getMessage();
            header("Location: index.php?action=recoveryValidationForm&proposal_id={$data['proposal_id']}&child_name={$data['full_name']}");
            exit;
        }
    }

    /**
     * Show list of completed recovery validations for a program
     */
    public function showValidationList(): void {
        $this->requireNO2();
        $proposalId = (int)($_GET['proposal_id'] ?? 0);

        $proposal = $this->feedingModel->getProposalById($proposalId);
        if (!$proposal) {
            $_SESSION['flash_error'] = 'Feeding program not found.';
            header('Location: index.php?action=recoveryValidation'); exit;
        }

        // Get all validations for this proposal
        $validations = $this->feedingModel->getRecoveryValidationsByProposal($proposalId);

        // Get statistics
        $stats = $this->feedingModel->getRecoveryStatsByProposal($proposalId);

        $pageTitle = 'Recovery Validations - ' . $proposal['proposal_title'];
        $activeNav = 'recovery';
        include __DIR__ . '/../views/nutrition/recovery_validation_list.php';
    }

    /**
     * Show detailed view of a single recovery validation
     */
    public function showValidationDetail(): void {
        $this->requireNO2();
        $validationId = (int)($_GET['validation_id'] ?? 0);

        $validation = $this->feedingModel->getRecoveryValidationById($validationId);
        if (!$validation) {
            $_SESSION['flash_error'] = 'Validation record not found.';
            header('Location: index.php?action=recoveryValidation'); exit;
        }

        $pageTitle = 'Recovery Validation Detail - ' . $validation['full_name'];
        $activeNav = 'recovery';
        include __DIR__ . '/../views/nutrition/recovery_validation_detail.php';
    }

    /**
     * Show recovery statistics for a program
     */
    public function showStatistics(): void {
        $this->requireNO2();
        $proposalId = (int)($_GET['proposal_id'] ?? 0);

        $proposal = $this->feedingModel->getProposalById($proposalId);
        if (!$proposal) {
            $_SESSION['flash_error'] = 'Feeding program not found.';
            header('Location: index.php?action=recoveryValidation'); exit;
        }

        // Get statistics
        $stats = $this->feedingModel->getRecoveryStatsByProposal($proposalId);

        // Get all validations for detailed analysis
        $validations = $this->feedingModel->getRecoveryValidationsByProposal($proposalId);

        $pageTitle = 'Recovery Statistics - ' . $proposal['proposal_title'];
        $activeNav = 'recovery';
        include __DIR__ . '/../views/nutrition/recovery_statistics.php';
    }

    // ========================================================================
    // BNS STAFF - Read-Only Views
    // ========================================================================

    /**
     * Show recovery status for BNS (read-only)
     */
    public function showBNSRecoveryStatus(): void {
        $this->requireBNS();
        $proposalId = (int)($_GET['proposal_id'] ?? 0);

        $proposal = $this->feedingModel->getProposalById($proposalId);
        $this->assertBNSCanAccessProposal($proposal);

        $validations = $this->feedingModel->getRecoveryValidationsByProposal($proposalId);
        $stats = $this->feedingModel->getRecoveryStatsByProposal($proposalId);

        $needsFollowup = [];
        $eligible = $this->feedingModel->getChildrenEligibleForRecoveryValidation($proposalId);
        foreach ($eligible as $child) {
            $paired = $this->resolveBaselineAndFollowup(
                $child['full_name'],
                $proposal['start_date'],
                $proposal['end_date']
            );
            if (!$paired['followup']) {
                $child['baseline'] = $paired['baseline'];
                $child['needs'] = !$paired['baseline'] ? 'baseline_and_followup' : 'followup';
                $needsFollowup[] = $child;
            }
        }

        $pageTitle = 'Recovery Status - ' . $proposal['proposal_title'];
        $activeNav = 'feeding_program';
        include __DIR__ . '/../views/bns/recovery_status.php';
    }

    // ========================================================================
    // HELPER METHODS
    // ========================================================================

    /**
     * Pair baseline and follow-up assessments for recovery validation.
     *
     * Baseline  = earliest assessment before program start (admission).
     * Follow-up = latest on/after program end, or latest during/after start, or latest after baseline.
     */
    private function resolveBaselineAndFollowup(string $childName, string $startDate, string $endDate): array {
        $stmt = $this->db->prepare("
            SELECT * FROM nutrition_assessments
            WHERE full_name COLLATE utf8mb4_general_ci = :child_name
              AND assessed_type = 'child'
            ORDER BY assessment_date ASC, assessment_id ASC
        ");
        $stmt->execute([':child_name' => $childName]);
        $assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($assessments)) {
            return ['baseline' => null, 'followup' => null];
        }

        $baseline = null;
        $followup = null;

        // Baseline: earliest assessment before program start
        foreach ($assessments as $a) {
            if ($a['assessment_date'] < $startDate) {
                $baseline = $a;
                break;
            }
        }

        // Follow-up: latest assessment on/after program end
        for ($i = count($assessments) - 1; $i >= 0; $i--) {
            if ($assessments[$i]['assessment_date'] >= $endDate) {
                $followup = $assessments[$i];
                break;
            }
        }

        if (!$baseline) {
            $baseline = $assessments[0];
        }

        $baselineId = (int)$baseline['assessment_id'];

        // During/after program: latest assessment on/after start (different record)
        if (!$followup) {
            for ($i = count($assessments) - 1; $i >= 0; $i--) {
                $a = $assessments[$i];
                if ((int)$a['assessment_id'] === $baselineId) {
                    continue;
                }
                if ($a['assessment_date'] >= $startDate) {
                    $followup = $a;
                    break;
                }
            }
        }

        // Demo fallback: latest assessment after baseline (different record)
        if (!$followup) {
            foreach ($assessments as $a) {
                if ((int)$a['assessment_id'] === $baselineId) {
                    continue;
                }
                if ($a['assessment_date'] > $baseline['assessment_date']) {
                    $followup = $a;
                } elseif (
                    $a['assessment_date'] === $baseline['assessment_date']
                    && (int)$a['assessment_id'] > $baselineId
                ) {
                    $followup = $a;
                }
            }
        }

        if ($followup && (int)$followup['assessment_id'] === $baselineId) {
            $followup = null;
        }

        return ['baseline' => $baseline, 'followup' => $followup];
    }

    /**
     * Delete a recovery validation (NO II only)
     */
    public function deleteValidation(): void {
        $this->requireNO2();
        $validationId = (int)($_POST['validation_id'] ?? 0);

        $validation = $this->feedingModel->getRecoveryValidationById($validationId);
        if (!$validation) {
            $_SESSION['flash_error'] = 'Validation record not found.';
            header('Location: index.php?action=recoveryValidation'); exit;
        }

        $proposalId = $validation['proposal_id'];
        $this->feedingModel->deleteRecoveryValidation($validationId);

        $_SESSION['flash'] = 'Recovery validation deleted successfully.';
        header("Location: index.php?action=recoveryValidationList&proposal_id=$proposalId");
        exit;
    }
}
