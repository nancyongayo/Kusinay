<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/FeedingProgramModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../../core/Security.php';

/**
 * FeedingProgramController
 * 
 * Handles:
 * - Process 12: Checking nutrition risk (BNS views affected children)
 * - Process 13: Planning feeding program (Committee Chair + Secretary)
 * - Process 14: Validating program proposal (Barangay Captain)
 */
class FeedingProgramController {
    private PDO $db;
    private FeedingProgramModel $model;
    private NotificationModel $notifModel;

    public function __construct() {
        $this->db = getDBConnection();
        $this->model = new FeedingProgramModel($this->db);
        $this->notifModel = new NotificationModel($this->db);
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

    // ========================================================================
    // PROCESS 12: Checking Nutrition Risk (BNS)
    // ========================================================================

    /**
     * BNS views list of affected (malnourished) children
     * This is the starting point for creating a feeding program proposal
     */
    public function showAffectedChildren(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        $bnsId = $_SESSION['user_id'];
        $barangayCode = $_SESSION['barangay_code'] ?? null;

        $affectedChildren = $this->model->getAffectedChildren($bnsId, $barangayCode);
        $stats = $this->model->getAffectedChildrenStats($bnsId, $barangayCode);

        $pageTitle = 'Nutrition Risk Assessment';
        $activeNav = 'feeding_program';
        include __DIR__ . '/../views/bns/affected_children_list.php';
    }

    // ========================================================================
    // PROCESS 12: Committee Chair Reviews Affected Children List
    // ========================================================================

    /**
     * Committee Chair reviews the OPT Plus Form C - List of Affected Children
     * This is the entry point before creating a feeding program proposal
     */
    public function reviewAffectedChildren(): void {
        $this->requireRole(['Committee Chair on Health', 'Admin']);

        // Get filter parameters
        $selectedPurok = $_GET['barangay_code'] ?? ''; // Keep param name for backward compatibility
        $selectedBns = (int)($_GET['bns_id'] ?? 0);
        $selectedPeriod = $_GET['period'] ?? 'all';

        // Build date filter
        $dateFilter = null;
        switch ($selectedPeriod) {
            case 'current_month':
                $dateFilter = date('Y-m-01');
                break;
            case 'last_3_months':
                $dateFilter = date('Y-m-d', strtotime('-3 months'));
                break;
            case 'last_6_months':
                $dateFilter = date('Y-m-d', strtotime('-6 months'));
                break;
            case 'current_year':
                $dateFilter = date('Y-01-01');
                break;
        }

        // Get affected children data
        $affectedChildren = $this->model->getAffectedChildrenForCommittee(
            $selectedBns ?: null,
            $selectedPurok ?: null,
            $dateFilter
        );


        // Calculate statistics
        $stats = $this->model->calculateAffectedChildrenStats($affectedChildren);

        // Get list of puroks from nutrition_assessments (only puroks with at-risk children)
        $purokStmt = $this->db->query("
            SELECT DISTINCT purok
            FROM nutrition_assessments
            WHERE purok IS NOT NULL 
              AND purok != '' 
              AND is_at_risk = 1
              AND assessed_type = 'child'
            ORDER BY purok
        ");
        $purokList = $purokStmt->fetchAll(PDO::FETCH_COLUMN);


        // Get list of BNS staff
        $bnsStmt = $this->db->prepare("
            SELECT u.user_id, u.first_name, u.last_name, up.barangay_code
            FROM users u
            JOIN roles r ON r.role_id = u.role_id
            LEFT JOIN user_profiles up ON up.user_id = u.user_id
            WHERE r.role_name = 'BNS Staff'
            ORDER BY u.last_name ASC
        ");
        $bnsStmt->execute();
        $bnsList = $bnsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Pass selectedBarangay for backward compatibility with view
        $selectedBarangay = $selectedPurok;

        $pageTitle = 'Review Affected Children';
        $activeNav = 'affected_children';
        include __DIR__ . '/../views/committee_chair/affected_children_review.php';
    }

    // ========================================================================
    // PROCESS 13: Planning Feeding Program (Committee Chair on Health)
    // ========================================================================

    /**
     * Committee Chair dashboard - view all proposals
     */
    public function showCommitteeChairDashboard(): void {
        $this->requireRole(['Committee Chair on Health', 'Admin']);
        $userId = $_SESSION['user_id'];

        $myProposals = $this->model->getProposals(['created_by' => $userId]);

        // Minutes from Secretary not yet reviewed by Chair
        $pendingMinutes = $this->model->getAllMinutes(null, true);

        $allProposals = [];
        if ($_SESSION['role'] === 'Admin') {
            $allProposals = $this->model->getProposals();
        }

        $pageTitle = 'Feeding Program Dashboard';
        $activeNav = 'dashboard';
        include __DIR__ . '/../views/committee_chair/dashboard.php';
    }

    /**
     * Committee Chair — dedicated minutes list page
     */
    public function showChairMinutesList(): void {
        $this->requireRole(['Committee Chair on Health', 'Admin']);

        $allMinutes = $this->model->getAllMinutes(); // all minutes, not filtered

        $pageTitle = 'Meeting Minutes';
        $activeNav = 'minutes_review';
        include __DIR__ . '/../views/committee_chair/minutes_list.php';
    }

    /**
     * Chair marks minutes as reviewed
     */
    public function markMinutesReviewed(): void {
        $this->requireRole(['Committee Chair on Health', 'Admin']);
        Security::verifyCsrf();

        $minuteId = (int)$_POST['minute_id'];
        $this->db->prepare("
            UPDATE meeting_minutes
            SET is_reviewed = 1, reviewed_by = :uid, reviewed_at = NOW()
            WHERE minute_id = :mid
        ")->execute([':uid' => $_SESSION['user_id'], ':mid' => $minuteId]);

        $_SESSION['flash'] = 'Minutes marked as reviewed.';
        header('Location: index.php?action=viewMinutes&minute_id=' . $minuteId);
        exit;
    }

    /**
     * Show proposal form (create/edit)
     */
    public function showProposalForm(): void {
        $this->requireRole(['Committee Chair on Health', 'Admin']);
        $proposalId = (int)($_GET['proposal_id'] ?? 0);
        $proposal = null;

        if ($proposalId) {
            $proposal = $this->model->getProposalById($proposalId);
            if (!$proposal || ($proposal['created_by_user_id'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
                $_SESSION['flash_error'] = 'Proposal not found or access denied.';
                header('Location: index.php?action=committeeChairDashboard');
                exit;
            }
        }

        // Get list of BNS staff for selection
        $bnsStmt = $this->db->prepare("
            SELECT u.user_id, u.first_name, u.last_name, up.barangay_code
            FROM users u
            JOIN roles r ON r.role_id = u.role_id
            LEFT JOIN user_profiles up ON up.user_id = u.user_id
            WHERE r.role_name = 'BNS Staff'
            ORDER BY u.last_name ASC
        ");
        $bnsStmt->execute();
        $bnsList = $bnsStmt->fetchAll(PDO::FETCH_ASSOC);

        // If coming from a minutes review, pre-link the minute
        $fromMinuteId = (int)($_GET['from_minute'] ?? 0);
        $fromMinute   = null;
        if ($fromMinuteId) {
            $fromMinute = $this->model->getMeetingMinuteById($fromMinuteId);
            // Auto-mark as reviewed when Chair opens proposal form from minutes
            if ($fromMinute && !$fromMinute['is_reviewed']) {
                $this->db->prepare("
                    UPDATE meeting_minutes SET is_reviewed=1, reviewed_by=:uid, reviewed_at=NOW()
                    WHERE minute_id=:mid
                ")->execute([':uid' => $_SESSION['user_id'], ':mid' => $fromMinuteId]);
            }
        }

        $pageTitle = $proposalId ? 'Edit Feeding Program Proposal' : 'Create Feeding Program Proposal';
        $activeNav = 'feeding_program';
        $isEdit    = (bool)$proposalId;
        include __DIR__ . '/../views/committee_chair/proposal_form.php';
    }

    /**
     * Save proposal (create or update)
     */
    public function saveProposal(): void {
        $this->requireRole(['Committee Chair on Health', 'Admin']);
        Security::verifyCsrf();

        $proposalId = (int)($_POST['proposal_id'] ?? 0);
        $userId = $_SESSION['user_id'];

        // Build budget items JSON from parallel arrays
        $budgetDescs  = $_POST['budget_item_desc']  ?? [];
        $budgetCosts  = $_POST['budget_item_cost']  ?? [];
        $budgetTotals = $_POST['budget_item_total'] ?? [];
        $budgetItems  = [];
        $totalBudget  = 0;
        foreach ($budgetDescs as $i => $desc) {
            $desc = trim($desc);
            if ($desc !== '') {
                $cost  = (float)($budgetCosts[$i]  ?? 0);
                $total = (float)($budgetTotals[$i] ?? 0);
                $budgetItems[] = ['item' => $desc, 'daily_cost' => $cost, 'total' => $total];
                $totalBudget  += $total;
            }
        }

        $data = [
            'created_by_user_id'   => $userId,
            'bns_id'               => (int)($_POST['bns_id'] ?? 0) ?: $userId, // Use current user if BNS not specified
            'barangay_code'        => trim($_POST['barangay_code'] ?? $_SESSION['barangay_code'] ?? ''),
            'proposal_title'       => trim($_POST['proposal_title'] ?? ''),
            'program_type'         => trim($_POST['program_type'] ?? ''),
            'proponent'            => trim($_POST['proponent'] ?? ''),
            'location'             => trim($_POST['location'] ?? ''),
            'target_beneficiaries' => trim($_POST['target_beneficiaries'] ?? ''),
            'num_beneficiaries'    => (int)($_POST['num_beneficiaries'] ?? 0),
            'implementation_days'  => (int)($_POST['implementation_days'] ?? 120),
            'start_date'           => trim($_POST['start_date'] ?? ''),
            'end_date'             => trim($_POST['end_date'] ?? ''),
            'feeding_schedule'     => trim($_POST['feeding_schedule'] ?? '') ?: null,
            'estimated_budget'     => $totalBudget ?: (float)($_POST['estimated_budget'] ?? 0),
            'funding_source'       => trim($_POST['funding_source'] ?? '') ?: null,
            'resources_needed'     => trim($_POST['resources_needed'] ?? '') ?: null,
            'objectives'           => trim($_POST['objectives'] ?? ''),
            'rationale'            => trim($_POST['rationale'] ?? ''),
            'implementation_plan'  => trim($_POST['implementation_plan'] ?? '') ?: null,
            'monitoring_plan'      => trim($_POST['monitoring_plan'] ?? '') ?: null,
            'signature_data'       => trim($_POST['signature_data'] ?? '') ?: null,
            'budget_items'         => !empty($budgetItems) ? json_encode($budgetItems) : null,
            'affected_children_data' => trim($_POST['affected_children_data'] ?? '') ?: null,
            'status'               => 'Draft',
        ];

        // Validation
        $errors = [];
        // BNS field removed - no longer required
        if (empty($data['proposal_title']))        $errors[] = 'Project title is required.';
        if (empty($data['program_type']))          $errors[] = 'Program type is required.';
        if (empty($data['target_beneficiaries']))  $errors[] = 'Target beneficiaries description is required.';
        if (empty($data['start_date']))            $errors[] = 'Start date is required.';
        if (empty($data['end_date']))              $errors[] = 'End date is required.';
        if (empty($data['objectives']))            $errors[] = 'Goals and objectives are required.';
        if (empty($data['rationale']))             $errors[] = 'Background and rationale is required.';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $data;
            $redirect = $proposalId ? "index.php?action=proposalForm&proposal_id=$proposalId" : 'index.php?action=proposalForm';
            header("Location: $redirect");
            exit;
        }

        try {
            if ($proposalId) {
                $this->model->updateProposal($proposalId, $data);
                $_SESSION['flash'] = 'Proposal updated successfully.';
            } else {
                $proposalId = $this->model->createProposal($data);
                $_SESSION['flash'] = 'Proposal created successfully.';
            }
            header('Location: index.php?action=viewProposal&proposal_id=' . $proposalId);
            exit;
        } catch (Exception $e) {
            error_log("Error saving proposal: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while saving the proposal.';
            header('Location: index.php?action=committeeChairDashboard');
            exit;
        }
    }

    /**
     * Print / Save PDF — Meeting Minutes official format
     */
    public function printMinutes(): void {
        $this->requireAuth();
        $minuteId = (int)($_GET['minute_id'] ?? 0);
        $minute   = $this->model->getMeetingMinuteById($minuteId);

        if (!$minute) {
            http_response_code(404);
            echo 'Minutes not found.';
            exit;
        }

        include __DIR__ . '/../views/shared/minutes_print.php';
        exit;
    }

    /**
     * Print / Save PDF — official document format
     */
    public function printProposal(): void {
        $this->requireAuth();
        $proposalId = (int)($_GET['proposal_id'] ?? 0);

        $proposal = $this->model->getProposalById($proposalId);
        if (!$proposal) {
            http_response_code(404);
            echo 'Proposal not found.';
            exit;
        }

        $validations = $this->model->getValidationsByProposal($proposalId);

        // Render the print view directly — no layout wrapper
        include __DIR__ . '/../views/committee_chair/proposal_print.php';
        exit;
    }

    /**
     * View proposal details — role-aware layout
     */
    public function viewProposal(): void {
        $this->requireAuth();
        $proposalId = (int)($_GET['proposal_id'] ?? 0);

        $proposal = $this->model->getProposalById($proposalId);
        if (!$proposal) {
            $_SESSION['flash_error'] = 'Proposal not found.';
            header('Location: index.php?action=login');
            exit;
        }

        $meetingMinutes = $this->model->getMeetingMinutesByProposal($proposalId);
        $validations    = $this->model->getValidationsByProposal($proposalId);
        $pageTitle      = 'Proposal Details';

        // Use the correct layout based on who is viewing
        $role = $_SESSION['role'] ?? '';
        switch ($role) {
            case 'BNS Staff':
                $activeNav = 'feeding_program';
                include __DIR__ . '/../views/bns/proposal_view.php';
                break;
            case 'Barangay Captain':
                $activeNav = 'dashboard';
                include __DIR__ . '/../views/barangay_captain/proposal_view.php';
                break;
            case 'Committee Secretary':
                $activeNav = 'dashboard';
                include __DIR__ . '/../views/committee_secretary/proposal_view.php';
                break;
            default:
                // Committee Chair on Health, Admin
                $activeNav = 'dashboard';
                include __DIR__ . '/../views/committee_chair/proposal_view.php';
                break;
        }
    }

    /**
     * Submit proposal for review (to Barangay Captain)
     */
    public function submitProposal(): void {
        $this->requireRole(['Committee Chair on Health', 'Admin']);
        Security::verifyCsrf();

        $proposalId = (int)($_POST['proposal_id'] ?? 0);
        $proposal = $this->model->getProposalById($proposalId);

        if (!$proposal || ($proposal['created_by_user_id'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
            $_SESSION['flash_error'] = 'Proposal not found or access denied.';
            header('Location: index.php?action=committeeChairDashboard');
            exit;
        }

        if (!in_array($proposal['status'], ['Draft', 'Rejected'])) {
            $_SESSION['flash_error'] = 'Only draft or rejected proposals can be submitted.';
            header('Location: index.php?action=viewProposal&proposal_id=' . $proposalId);
            exit;
        }

        try {
            $this->model->submitProposal($proposalId);
            
            // Notify Barangay Captain(s)
            $this->notifyBarangayCaptains($proposalId, $proposal);
            
            $_SESSION['flash'] = 'Proposal submitted for review by Barangay Captain.';
            header('Location: index.php?action=viewProposal&proposal_id=' . $proposalId);
            exit;
        } catch (Exception $e) {
            error_log("Error submitting proposal: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while submitting the proposal.';
            header('Location: index.php?action=viewProposal&proposal_id=' . $proposalId);
            exit;
        }
    }

    /**
     * Delete proposal
     */
    public function deleteProposal(): void {
        $this->requireRole(['Committee Chair on Health', 'Admin']);
        Security::verifyCsrf();

        $proposalId = (int)($_POST['proposal_id'] ?? 0);
        $proposal = $this->model->getProposalById($proposalId);

        if (!$proposal || ($proposal['created_by_user_id'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
            $_SESSION['flash_error'] = 'Proposal not found or access denied.';
            header('Location: index.php?action=committeeChairDashboard');
            exit;
        }

        try {
            $this->model->deleteProposal($proposalId);
            $_SESSION['flash'] = 'Proposal deleted successfully.';
        } catch (Exception $e) {
            error_log("Error deleting proposal: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while deleting the proposal.';
        }

        header('Location: index.php?action=committeeChairDashboard');
        exit;
    }

    // ========================================================================
    // PROCESS 13: Meeting Minutes (Committee Secretary)
    // ========================================================================

    /**
     * Committee Secretary dashboard — shows all minutes (standalone)
     */
    public function showSecretaryDashboard(): void {
        $this->requireRole(['Committee Secretary', 'Admin']);

        // Get ALL minutes recorded by this secretary (or all if admin)
        $userId = $_SESSION['user_id'];
        $allMinutes = $this->model->getAllMinutes(
            $_SESSION['role'] === 'Admin' ? null : $userId
        );

        $pageTitle = 'Meeting Minutes';
        $activeNav = 'dashboard';
        include __DIR__ . '/../views/committee_secretary/dashboard.php';
    }

    /**
     * Show meeting minutes form — standalone, no proposal required
     */
    public function showMinutesForm(): void {
        $this->requireRole(['Committee Secretary', 'Admin']);

        // Optional: link to an existing proposal
        $proposalId = (int)($_GET['proposal_id'] ?? 0);
        $proposal   = null;
        if ($proposalId) {
            $proposal = $this->model->getProposalById($proposalId);
        }

        $pageTitle = 'Record Meeting Minutes';
        $activeNav = 'dashboard';
        include __DIR__ . '/../views/committee_secretary/minutes_form.php';
    }

    /**
     * View a single set of meeting minutes
     */
    public function viewMinutes(): void {
        $this->requireAuth();
        $minuteId = (int)($_GET['minute_id'] ?? 0);
        $minute   = $this->model->getMeetingMinuteById($minuteId);

        if (!$minute) {
            $_SESSION['flash_error'] = 'Minutes not found.';
            header('Location: index.php?action=secretaryDashboard');
            exit;
        }

        // Load linked proposal if any
        $proposal = $minute['proposal_id']
            ? $this->model->getProposalById($minute['proposal_id'])
            : null;

        $pageTitle = 'Meeting Minutes — ' . date('F j, Y', strtotime($minute['meeting_date']));
        $activeNav = 'dashboard';

        // Render in the correct layout based on role
        $role = $_SESSION['role'];
        if ($role === 'Committee Secretary') {
            include __DIR__ . '/../views/committee_secretary/minutes_view.php';
        } elseif ($role === 'Committee Chair on Health') {
            include __DIR__ . '/../views/committee_chair/minutes_review.php';
        } elseif ($role === 'Barangay Captain') {
            include __DIR__ . '/../views/barangay_captain/minutes_view.php';
        } else {
            include __DIR__ . '/../views/committee_secretary/minutes_view.php';
        }
    }

    /**
     * Save meeting minutes
     */
    public function saveMeetingMinutes(): void {
        $this->requireRole(['Committee Secretary', 'Admin']);
        Security::verifyCsrf();

        // Build attendees JSON from parallel arrays
        $attendeeNames = $_POST['attendee_name'] ?? [];
        $attendeeRoles = $_POST['attendee_role'] ?? [];
        $attendees = [];
        foreach ($attendeeNames as $i => $name) {
            $name = trim($name);
            if ($name !== '') {
                $attendees[] = [
                    'name' => $name,
                    'role' => trim($attendeeRoles[$i] ?? '')
                ];
            }
        }

        $data = [
            'proposal_id'          => (int)$_POST['proposal_id'] ?: null,
            'recorded_by_user_id'  => $_SESSION['user_id'],
            'meeting_date'         => trim($_POST['meeting_date'] ?? ''),
            'meeting_time'         => trim($_POST['meeting_time'] ?? ''),
            'venue'                => trim($_POST['venue'] ?? ''),
            'meeting_type'         => trim($_POST['meeting_type'] ?? 'Planning'),
            'attendees'            => !empty($attendees) ? json_encode($attendees) : null,
            'num_attendees'        => count($attendees),
            'agenda'               => trim($_POST['agenda'] ?? ''),
            'discussion_summary'   => trim($_POST['discussion_summary'] ?? ''),
            'decisions_made'       => trim($_POST['decisions_made'] ?? ''),
            'action_items'         => trim($_POST['action_items'] ?? '') ?: null,
            'next_meeting_date'    => trim($_POST['next_meeting_date'] ?? '') ?: null,
            'signature_data'       => trim($_POST['signature_data'] ?? '') ?: null,
        ];

        // Validation
        $errors = [];
        if (empty($data['meeting_date']))       $errors[] = 'Meeting date is required.';
        if (empty($data['meeting_time']))       $errors[] = 'Meeting time is required.';
        if (empty($data['venue']))              $errors[] = 'Venue is required.';
        if (empty($data['agenda']))             $errors[] = 'Meeting subject/agenda is required.';
        if (empty($data['discussion_summary'])) $errors[] = 'Discussion summary is required.';
        if (empty($data['decisions_made']))     $errors[] = 'Decisions made are required.';
        if (empty($data['signature_data']))     $errors[] = 'Digital signature is required.';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = array_merge($_POST, ['attendee_name' => $attendeeNames, 'attendee_role' => $attendeeRoles]);
            header('Location: index.php?action=minutesForm&proposal_id=' . $data['proposal_id']);
            exit;
        }

        try {
            $minuteId = $this->model->createMeetingMinutes($data);
            
            // Notify ALL Committee Chairs about the new minutes
            $this->notifyCommitteeChairs($minuteId, $data);

            // Also notify proposal creator if linked
            if ($data['proposal_id']) {
                $proposal = $this->model->getProposalById($data['proposal_id']);
                if ($proposal) {
                    $message = "Meeting minutes have been recorded for proposal: \"{$proposal['proposal_title']}\".";
                    $this->notifModel->create($proposal['created_by_user_id'], 'meeting_minutes_added', $data['proposal_id'], $message);
                }
            }
            
            $_SESSION['flash'] = 'Meeting minutes recorded successfully. The Committee Chair has been notified.';
            header('Location: index.php?action=secretaryDashboard');
            exit;
        } catch (Exception $e) {
            error_log("Error saving meeting minutes: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while saving the meeting minutes.';
            header('Location: index.php?action=minutesForm');
            exit;
        }
    }

    /**
     * Notify all Committee Chairs about new meeting minutes
     */
    private function notifyCommitteeChairs(int $minuteId, array $minuteData): void {
        try {
            $stmt = $this->db->prepare("
                SELECT u.user_id
                FROM users u
                JOIN roles r ON r.role_id = u.role_id
                WHERE r.role_name = 'Committee Chair on Health'
            ");
            $stmt->execute();
            $chairs = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $message = "New meeting minutes recorded: \"{$minuteData['agenda']}\" on "
                     . date('F j, Y', strtotime($minuteData['meeting_date']))
                     . ". Please review and create a feeding program proposal if needed.";

            foreach ($chairs as $chairId) {
                $this->notifModel->create((int)$chairId, 'meeting_minutes_added', $minuteId, $message);
            }
        } catch (Exception $e) {
            error_log("Error notifying chairs: " . $e->getMessage());
        }
    }

    // ========================================================================
    // PROCESS 14: Validating Program Proposal (Barangay Captain)
    // ========================================================================

    /**
     * Barangay Captain dashboard - view proposals for review
     */
    public function showCaptainDashboard(): void {
        $this->requireRole(['Barangay Captain', 'Admin']);
        
        // Get proposals pending review
        $pendingProposals = $this->model->getProposals(['status' => 'For Review']);
        
        // Get all proposals (for history)
        $allProposals = $this->model->getProposals();
        
        // Get all meeting minutes (for transparency/view-only)
        $allMinutes = $this->model->getAllMinutes();

        $pageTitle = 'Barangay Captain Dashboard';
        $activeNav = 'feeding_program';
        include __DIR__ . '/../views/barangay_captain/dashboard.php';
    }

    /**
     * Show validation form (approve/reject proposal)
     */
    public function showValidationForm(): void {
        $this->requireRole(['Barangay Captain', 'Admin']);
        $proposalId = (int)($_GET['proposal_id'] ?? 0);

        $proposal = $this->model->getProposalById($proposalId);
        if (!$proposal) {
            $_SESSION['flash_error'] = 'Proposal not found.';
            header('Location: index.php?action=captainDashboard');
            exit;
        }

        // Get meeting minutes
        $meetingMinutes = $this->model->getMeetingMinutesByProposal($proposalId);
        
        // Get previous validations
        $validations = $this->model->getValidationsByProposal($proposalId);

        $pageTitle = 'Validate Proposal';
        $activeNav = 'feeding_program';
        include __DIR__ . '/../views/barangay_captain/validation_form.php';
    }

    /**
     * Submit validation (approve/reject with digital signature)
     */
    public function submitValidation(): void {
        $this->requireRole(['Barangay Captain', 'Admin']);
        Security::verifyCsrf();

        $data = [
            'proposal_id'           => (int)$_POST['proposal_id'],
            'validated_by_user_id'  => $_SESSION['user_id'],
            'decision'              => trim($_POST['decision'] ?? ''),
            'feedback'              => trim($_POST['feedback'] ?? '') ?: null,
            'conditions'            => trim($_POST['conditions'] ?? '') ?: null,
            'signature_data'        => trim($_POST['signature_data'] ?? ''),
            'signature_type'        => trim($_POST['signature_type'] ?? 'drawn'),
            'ip_address'            => $_SERVER['REMOTE_ADDR'] ?? null,
        ];

        // Validation
        $errors = [];
        if (!in_array($data['decision'], ['Approved', 'Rejected', 'Needs Revision'])) {
            $errors[] = 'Please select a valid decision.';
        }
        // Signature only required for Approve
        if ($data['decision'] === 'Approved' && empty($data['signature_data'])) {
            $errors[] = 'Digital signature is required when approving.';
        }
        // Feedback required for Reject and Needs Revision
        if (in_array($data['decision'], ['Rejected', 'Needs Revision']) && empty($data['feedback'])) {
            $errors[] = 'Feedback is required when rejecting or requesting revision.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $data;
            header('Location: index.php?action=validationForm&proposal_id=' . $data['proposal_id']);
            exit;
        }

        try {
            $validationId = $this->model->createValidation($data);
            
            // Notify proposal creator and BNS
            $proposal = $this->model->getProposalById($data['proposal_id']);
            $decisionText = $data['decision'];
            $message = "Your feeding program proposal \"{$proposal['proposal_title']}\" has been {$decisionText} by the Barangay Captain.";
            
            $this->notifModel->create($proposal['created_by_user_id'], 'feeding_proposal_' . strtolower(str_replace(' ', '_', $decisionText)), $data['proposal_id'], $message);
            $this->notifModel->create($proposal['bns_id'], 'feeding_proposal_' . strtolower(str_replace(' ', '_', $decisionText)), $data['proposal_id'], $message);
            
            // If approved, notify BNS and affected mothers to start feeding program
            if ($data['decision'] === 'Approved') {
                $this->notifyBnsAndMothers($data['proposal_id'], $proposal);
            }
            
            $_SESSION['flash'] = "Proposal {$decisionText} successfully.";
            // Redirect to Captain's own dashboard — not viewProposal (which uses Chair layout)
            header('Location: index.php?action=captainDashboard');
            exit;
        } catch (Exception $e) {
            error_log("Error submitting validation: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while submitting the validation.';
            header('Location: index.php?action=validationForm&proposal_id=' . $data['proposal_id']);
            exit;
        }
    }
    // HELPER METHODS
    // ========================================================================

    /**
     * Notify all Barangay Captains about new proposal
     */
    private function notifyBarangayCaptains(int $proposalId, array $proposal): void {
        try {
            $stmt = $this->db->prepare("
                SELECT u.user_id
                FROM users u
                JOIN roles r ON r.role_id = u.role_id
                WHERE r.role_name = 'Barangay Captain'
            ");
            $stmt->execute();
            $captains = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $message = "New feeding program proposal submitted for review: \"{$proposal['proposal_title']}\". "
                     . "Beneficiaries: {$proposal['num_beneficiaries']}, Budget: ₱" . number_format($proposal['estimated_budget'], 2);

            foreach ($captains as $captainId) {
                $this->notifModel->create((int)$captainId, 'feeding_proposal_submitted', $proposalId, $message);
            }
        } catch (Exception $e) {
            error_log("Error notifying captains: " . $e->getMessage());
        }
    }

    /**
     * Notify BNS and affected mothers about approved proposal
     */
    private function notifyBnsAndMothers(int $proposalId, array $proposal): void {
        try {
            // Notify BNS
            $message = "Feeding program \"{$proposal['proposal_title']}\" has been approved. You can now conduct feeding sessions.";
            $this->notifModel->create($proposal['bns_id'], 'feeding_program_approved', $proposalId, $message);

            // Notify affected mothers (from affected_children_data)
            if (!empty($proposal['affected_children_data'])) {
                $affectedChildren = json_decode($proposal['affected_children_data'], true);
                if (is_array($affectedChildren)) {
                    $notifiedMothers = [];
                    foreach ($affectedChildren as $child) {
                        $motherId = $child['mother_id'] ?? null;
                        if ($motherId && !in_array($motherId, $notifiedMothers)) {
                            $motherMessage = "Your child has been included in the feeding program: \"{$proposal['proposal_title']}\". "
                                           . "The program will start on " . date('F j, Y', strtotime($proposal['start_date'])) . ".";
                            $this->notifModel->create((int)$motherId, 'feeding_program_approved', $proposalId, $motherMessage);
                            $notifiedMothers[] = $motherId;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Error notifying BNS and mothers: " . $e->getMessage());
        }
    }

    /**
     * Notify mothers about new feeding session
     */
    /**
     * Notify mothers about new feeding session and create attendance records
     * 
     * LOGIC FLOW (DO NOT CHANGE WITHOUT UNDERSTANDING):
     * ================================================
     * 
     * METHOD 1 (Primary): Use Proposal's Affected Children Data
     * ---------------------------------------------------------
     * - IF proposal has 'affected_children_data' JSON field
     * - THEN use this as the source of truth for who should attend
     * - FOR EACH child in affected_children_data:
     *   1. Create attendance record (feeding_program_attendance)
     *   2. Send notification to parent (if they have an account)
     * 
     * WHY: The affected_children_data is the official list approved by 
     * Committee Chair and Barangay Captain. This is the authoritative source.
     * 
     * METHOD 2 (Fallback): Query Nutrition Assessments
     * -------------------------------------------------
     * - IF proposal does NOT have affected_children_data (old proposals)
     * - THEN query nutrition_assessments table for latest at-risk children
     * - FOR EACH at-risk child found:
     *   1. Create attendance record (feeding_program_attendance)
     *   2. Send notification to parent (if they have an account)
     * 
     * WHY: This is a fallback for old proposals created before the 
     * affected_children_data field was added. New proposals should always
     * have this field populated from Process 13 (Review Affected Children).
     * 
     * IMPORTANT RULES:
     * ----------------
     * 1. ALWAYS create attendance records for ALL children (Method 1 or 2)
     * 2. ONLY send notifications to parents who have user accounts
     * 3. Method 1 and Method 2 are MUTUALLY EXCLUSIVE (use if/else)
     * 4. Never mix data from both methods - use one OR the other
     * 5. Log everything for debugging
     * 
     * @param array $proposal The approved feeding program proposal
     * @param array $sessionData The feeding session data (must include session_id)
     * @return void
     */
    private function notifyMothersAboutSession(array $proposal, array $sessionData): void {
        try {
            $notifiedMothers = [];
            $sessionDate = date('F j, Y', strtotime($sessionData['session_date']));
            $processedFromProposalData = false;
            
            // DEBUG: Log the proposal data
            error_log("=== notifyMothersAboutSession called ===");
            error_log("Proposal ID: " . ($proposal['proposal_id'] ?? 'NULL'));
            error_log("Has affected_children_data: " . (!empty($proposal['affected_children_data']) ? 'YES' : 'NO'));
            if (!empty($proposal['affected_children_data'])) {
                error_log("Raw affected_children_data length: " . strlen($proposal['affected_children_data']));
                error_log("First 200 chars: " . substr($proposal['affected_children_data'], 0, 200));
            }
            
            // Method 1: Try to get from proposal's affected_children_data
            if (!empty($proposal['affected_children_data'])) {
                $affectedChildren = json_decode($proposal['affected_children_data'], true);
                
                if ($affectedChildren === null) {
                    error_log("ERROR: JSON decode failed! JSON error: " . json_last_error_msg());
                    error_log("Raw data: " . $proposal['affected_children_data']);
                }
                
                if (is_array($affectedChildren) && !empty($affectedChildren)) {
                    $processedFromProposalData = true;
                    error_log("✅ Found " . count($affectedChildren) . " affected children in proposal data");
                    
                    foreach ($affectedChildren as $index => $child) {
                        $motherId = $child['mother_id'] ?? $child['parent_user_id'] ?? null;
                        $childName = $child['child_name'] ?? $child['full_name'] ?? 'Unknown';
                        $motherName = $child['mother_name'] ?? 'No Parent Information';
                        $purok = $child['purok'] ?? $child['address'] ?? null;
                        
                        error_log("  - Child: {$childName}, Mother: {$motherName}, Purok: {$purok}, Mother ID: " . ($motherId ?? 'NULL'));

                        // Smooth graduation flow:
                        // If child is already marked Recovered in this proposal,
                        // skip creating NEW attendance rows for future sessions.
                        if ($this->isChildRecoveredInProposal((int)$proposal['proposal_id'], (string)$childName)) {
                            error_log("  ⏭️ Skipped child '{$childName}' - already marked Recovered for this program");
                            continue;
                        }
                        
                        // Create attendance record for ALL children (with or without parent accounts)
                        $attendanceId = $this->createRSVPRecord(
                            $sessionData['session_id'] ?? null,
                            $proposal['proposal_id'],
                            $childName,
                            $motherName,
                            $motherId, // Can be NULL for children without parent accounts
                            $purok
                        );
                        
                        if ($attendanceId) {
                            error_log("  ✅ Created attendance record ID: {$attendanceId}");
                        } else {
                            error_log("  ❌ Failed to create attendance record");
                        }
                        
                        // Only send notification if parent has an account
                        if ($motherId && !in_array($motherId, $notifiedMothers)) {
                            $message = "Feeding session scheduled for {$childName}!\n\n"
                                     . "Activity: {$sessionData['activity_name']}\n"
                                     . "Date: {$sessionDate}\n"
                                     . "Location: {$sessionData['purok_barangay']}\n\n"
                                     . "Please confirm your attendance.";
                            
                            $this->notifModel->create(
                                (int)$motherId, 
                                'feeding_session_scheduled', 
                                $sessionData['session_id'] ?? null,
                                $message
                            );
                            $notifiedMothers[] = $motherId;
                            error_log("  ✅ Notified parent ID {$motherId}");
                        } else {
                            error_log("  ℹ️  No parent account to notify (mother_id: " . ($motherId ?? 'NULL') . ")");
                        }
                    }
                    
                    error_log("=== Summary: Created " . count($affectedChildren) . " attendance records, notified " . count($notifiedMothers) . " parents ===");
                } else {
                    error_log("❌ affected_children_data is not a valid array or is empty after JSON decode");
                }
            }
            
            // Method 2: FALLBACK - If proposal has NO usable affected_children_data, 
            // get ALL at-risk children from nutrition assessments
            // This is a fallback for old proposals that don't have affected_children_data
            // Include children both WITH and WITHOUT parent accounts
            // Get mother's name from family_members table (prefer Wife role over Head role)
            // IMPORTANT: Only get children whose LATEST assessment shows they are at-risk
            if (!$processedFromProposalData) {
                error_log("No usable affected_children_data in proposal, falling back to nutrition assessments");
                
                $stmt = $this->db->prepare("
                    SELECT DISTINCT
                        latest_na.full_name as child_name,
                        latest_na.purok,
                        fp.source_user_id as parent_user_id,
                        COALESCE(
                            NULLIF(TRIM(CONCAT_WS(' ', 
                                COALESCE(wife.first_name, head.first_name),
                                COALESCE(wife.middle_name, head.middle_name),
                                COALESCE(wife.last_name, head.last_name)
                            )), ''),
                            'No Parent Information'
                        ) as mother_name
                    FROM (
                        -- Get only the LATEST assessment per child
                        SELECT na1.*
                        FROM nutrition_assessments na1
                        INNER JOIN (
                            SELECT 
                                COALESCE(child_id, fm_member_id, CONCAT(full_name, dob)) as child_key,
                                MAX(assessment_date) as max_date
                            FROM nutrition_assessments
                            WHERE assessed_type = 'child'
                            GROUP BY child_key
                        ) na2 ON COALESCE(na1.child_id, na1.fm_member_id, CONCAT(na1.full_name, na1.dob)) = na2.child_key 
                             AND na1.assessment_date = na2.max_date
                        WHERE na1.assessed_type = 'child'
                          AND na1.is_at_risk = 1
                    ) latest_na
                    LEFT JOIN family_members fm_child ON fm_child.member_id = latest_na.fm_member_id
                    LEFT JOIN family_profiles fp ON fp.family_id = fm_child.family_id
                    LEFT JOIN family_members wife ON wife.family_id = fm_child.family_id AND wife.role = 'Wife'
                    LEFT JOIN family_members head ON head.family_id = fm_child.family_id AND head.role = 'Head'
                    ORDER BY latest_na.full_name
                ");
                $stmt->execute();
                $atRiskChildren = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                error_log("Found " . count($atRiskChildren) . " at-risk children based on LATEST assessment (including those without parent accounts)");
                
                foreach ($atRiskChildren as $child) {
                    $parentId = $child['parent_user_id'];
                    $childName = $child['child_name'];
                    $purok = $child['purok'] ?? null;
                    // Use mother's name from family_members table, not from users table
                    $motherName = !empty($child['mother_name']) ? $child['mother_name'] : 'No Parent Information';

                    // Skip new attendance rows once child is already marked recovered
                    // for this specific feeding program.
                    if ($this->isChildRecoveredInProposal((int)$proposal['proposal_id'], (string)$childName)) {
                        error_log("Skipped child {$childName} in fallback flow - already marked Recovered for this program");
                        continue;
                    }
                    
                    // Create attendance record for ALL children (with or without parent accounts)
                    $attendanceId = $this->createRSVPRecord(
                        $sessionData['session_id'] ?? null,
                        $proposal['proposal_id'],
                        $childName,
                        $motherName,
                        $parentId, // Can be NULL for children without parent accounts
                        $purok
                    );
                    
                    // Only send notification if parent has an account
                    if ($parentId && !in_array($parentId, $notifiedMothers)) {
                        $message = "Feeding session scheduled for {$childName}!\n\n"
                                 . "Activity: {$sessionData['activity_name']}\n"
                                 . "Date: {$sessionDate}\n"
                                 . "Location: {$sessionData['purok_barangay']}\n\n"
                                 . "Please confirm your attendance.";
                        
                        $this->notifModel->create(
                            (int)$parentId, 
                            'feeding_session_scheduled', 
                            $sessionData['session_id'] ?? null,  // Use session_id instead of attendance_id
                            $message
                        );
                        
                        $notifiedMothers[] = $parentId;
                        error_log("Notified parent ID {$parentId} about child {$childName} (mother: {$motherName})");
                    } else {
                        error_log("Added child {$childName} to attendance (mother: {$motherName}, no parent account for notification)");
                    }
                }
            }
            
            // Log the result
            if (!empty($notifiedMothers)) {
                error_log("Notified " . count($notifiedMothers) . " mothers about feeding session");
            } else {
                error_log("No mothers to notify - no affected children data found");
            }
            
        } catch (Exception $e) {
            error_log("Error notifying mothers about session: " . $e->getMessage());
        }
    }

    // ========================================================================
    // PROCESS 15: Conducting Feeding Program (BNS)
    // ========================================================================

    /**
     * BNS views list of approved feeding programs
     */
    public function showFeedingProgramList(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        $barangayCode = $_SESSION['barangay_code'] ?? '';

        // Get approved proposals from the same barangay
        $approvedPrograms = $this->model->getProposals([
            'barangay_code' => $barangayCode,
            'status' => 'Approved'
        ]);

        $pageTitle = 'Feeding Programs';
        $activeNav = 'feeding_program';
        include __DIR__ . '/../views/bns/feeding_program_list.php';
    }

    /**
     * BNS views sessions for a specific feeding program
     */
    public function showFeedingSessions(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        $proposalId = (int)($_GET['proposal_id'] ?? 0);

        $proposal = $this->model->getProposalById($proposalId);
        if (!$proposal) {
            $_SESSION['flash_error'] = 'Feeding program not found.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        // Check if BNS is from the same barangay or is Admin
        $userBarangay = $_SESSION['barangay_code'] ?? '';
        if ($proposal['barangay_code'] != $userBarangay && $_SESSION['role'] !== 'Admin') {
            $_SESSION['flash_error'] = 'Access denied. This program belongs to a different barangay.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        $sessions = $this->model->getSessionsByProposal($proposalId);

        $pageTitle = 'Feeding Sessions - ' . $proposal['proposal_title'];
        $activeNav = 'feeding_program';
        include __DIR__ . '/../views/bns/feeding_sessions_list.php';
    }

    /**
     * Show session form (create/edit)
     */
    public function showSessionForm(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        $proposalId = (int)($_GET['proposal_id'] ?? 0);
        $sessionId = (int)($_GET['session_id'] ?? 0);

        $proposal = $this->model->getProposalById($proposalId);
        if (!$proposal) {
            $_SESSION['flash_error'] = 'Feeding program not found.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        $session = null;
        if ($sessionId) {
            $session = $this->model->getSessionById($sessionId);
            if (!$session || $session['proposal_id'] != $proposalId) {
                $_SESSION['flash_error'] = 'Session not found.';
                header('Location: index.php?action=feedingSessions&proposal_id=' . $proposalId);
                exit;
            }
        }

        $pageTitle = $sessionId ? 'Edit Feeding Session' : 'Create Feeding Session';
        $activeNav = 'feeding_program';
        include __DIR__ . '/../views/bns/feeding_session_form.php';
    }

    /**
     * Save session (create or update)
     */
    public function saveSession(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        Security::verifyCsrf();

        $sessionId = (int)($_POST['session_id'] ?? 0);
        $proposalId = (int)($_POST['proposal_id'] ?? 0);

        // Build IEC age group JSON from checkboxes
        $iecAgeGroups = $_POST['iec_age_group'] ?? [];
        $iecAgeGroupJson = !empty($iecAgeGroups) ? json_encode($iecAgeGroups) : null;
        
        // Get "Others" specification if "Others" is checked
        $iecOthersSpecify = null;
        if (in_array('Others', $iecAgeGroups)) {
            $iecOthersSpecify = trim($_POST['iec_others_specify'] ?? '');
        }

        $data = [
            'proposal_id'                => $proposalId,
            'session_date'               => trim($_POST['session_date'] ?? ''),
            'activity_name'              => trim($_POST['activity_name'] ?? ''),
            'purok_barangay'             => trim($_POST['purok_barangay'] ?? ''),
            'iec_age_group'              => $iecAgeGroupJson,
            'iec_others_specify'         => $iecOthersSpecify,
            'conducted_by_user_id'       => $_SESSION['user_id'],
            'prepared_by'                => trim($_POST['prepared_by'] ?? '') ?: null,
            'nutrition_officer_signature'=> trim($_POST['nutrition_officer_signature'] ?? '') ?: null,
            'status'                     => trim($_POST['status'] ?? 'Scheduled'),
            'remarks'                    => trim($_POST['remarks'] ?? '') ?: null,
        ];

        // Validation
        $errors = [];
        if (empty($data['session_date']))    $errors[] = 'Session date is required.';
        if (empty($data['activity_name']))   $errors[] = 'Activity name is required.';
        if (empty($data['purok_barangay']))  $errors[] = 'Purok/Barangay is required.';
        
        // Validate "Others" specification if "Others" is checked
        if (in_array('Others', $iecAgeGroups) && empty($iecOthersSpecify)) {
            $errors[] = 'Please specify the "Others" age group.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $data;
            $redirect = $sessionId 
                ? "index.php?action=sessionForm&proposal_id=$proposalId&session_id=$sessionId"
                : "index.php?action=sessionForm&proposal_id=$proposalId";
            header("Location: $redirect");
            exit;
        }

        try {
            if ($sessionId) {
                $this->model->updateSession($sessionId, $data);
                $_SESSION['flash'] = 'Session updated successfully.';
            } else {
                $sessionId = $this->model->createSession($data);
                
                // Notify affected mothers about the new feeding session
                $proposal = $this->model->getProposalById($proposalId);
                if ($proposal) {
                    // Add session_id to data for RSVP creation
                    $data['session_id'] = $sessionId;
                    $this->notifyMothersAboutSession($proposal, $data);
                }
                
                $_SESSION['flash'] = 'Session created successfully. Parents have been notified.';
            }
            header('Location: index.php?action=sessionRSVPList&session_id=' . $sessionId);
            exit;
        } catch (Exception $e) {
            error_log("Error saving session: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while saving the session.';
            header('Location: index.php?action=feedingSessions&proposal_id=' . $proposalId);
            exit;
        }
    }

    /**
     * Delete session
     */
    public function deleteSession(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        Security::verifyCsrf();

        $sessionId = (int)($_POST['session_id'] ?? 0);
        $session = $this->model->getSessionById($sessionId);

        if (!$session) {
            $_SESSION['flash_error'] = 'Session not found.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        $proposalId = $session['proposal_id'];

        try {
            $this->model->deleteSession($sessionId);
            $_SESSION['flash'] = 'Session deleted successfully.';
        } catch (Exception $e) {
            error_log("Error deleting session: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while deleting the session.';
        }

        header('Location: index.php?action=feedingSessions&proposal_id=' . $proposalId);
        exit;
    }

    // ========================================================================
    // BULK SESSION CREATION
    // ========================================================================

    /**
     * Show bulk session creation form
     */
    public function showBulkSessionForm(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        $proposalId = (int)($_GET['proposal_id'] ?? 0);

        $proposal = $this->model->getProposalById($proposalId);
        if (!$proposal) {
            $_SESSION['flash_error'] = 'Feeding program not found.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        // Check if BNS is from the same barangay or is Admin
        $userBarangay = $_SESSION['barangay_code'] ?? '';
        if ($proposal['barangay_code'] != $userBarangay && $_SESSION['role'] !== 'Admin') {
            $_SESSION['flash_error'] = 'Access denied. This program belongs to a different barangay.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        $pageTitle = 'Create Multiple Sessions';
        $activeNav = 'feeding_program';
        include __DIR__ . '/../views/bns/bulk_session_form.php';
    }

    /**
     * Save bulk sessions
     */
    public function saveBulkSessions(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        Security::verifyCsrf();

        $proposalId = (int)($_POST['proposal_id'] ?? 0);
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $feedingDays = $_POST['feeding_days'] ?? [];

        // Build IEC age group JSON
        $iecAgeGroups = $_POST['iec_age_group'] ?? [];
        $iecAgeGroupJson = !empty($iecAgeGroups) ? json_encode($iecAgeGroups) : null;
        
        // Get "Others" specification
        $iecOthersSpecify = null;
        if (in_array('Others', $iecAgeGroups)) {
            $iecOthersSpecify = trim($_POST['iec_others_specify'] ?? '');
        }

        // Common data for all sessions
        $commonData = [
            'proposal_id'                => $proposalId,
            'activity_name'              => trim($_POST['activity_name'] ?? ''),
            'purok_barangay'             => trim($_POST['purok_barangay'] ?? ''),
            'iec_age_group'              => $iecAgeGroupJson,
            'iec_others_specify'         => $iecOthersSpecify,
            'conducted_by_user_id'       => $_SESSION['user_id'],
            'prepared_by'                => trim($_POST['prepared_by'] ?? '') ?: null,
            'status'                     => 'Scheduled',
            'remarks'                    => trim($_POST['remarks'] ?? '') ?: null,
        ];

        // Validation
        $errors = [];
        if (empty($startDate)) $errors[] = 'Start date is required.';
        if (empty($endDate)) $errors[] = 'End date is required.';
        if (empty($feedingDays)) $errors[] = 'Please select at least one feeding day.';
        if (empty($commonData['activity_name'])) $errors[] = 'Activity name is required.';
        if (empty($commonData['purok_barangay'])) $errors[] = 'Location is required.';
        
        if (in_array('Others', $iecAgeGroups) && empty($iecOthersSpecify)) {
            $errors[] = 'Please specify the "Others" age group.';
        }

        // Validate date range
        if (!empty($startDate) && !empty($endDate)) {
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
            if ($end < $start) {
                $errors[] = 'End date must be after start date.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            header("Location: index.php?action=bulkSessionForm&proposal_id=$proposalId");
            exit;
        }

        try {
            // Generate sessions
            $dayMap = [
                'Sunday' => 0,
                'Monday' => 1,
                'Tuesday' => 2,
                'Wednesday' => 3,
                'Thursday' => 4,
                'Friday' => 5,
                'Saturday' => 6
            ];

            $selectedDayNumbers = array_map(fn($day) => $dayMap[$day], $feedingDays);
            
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
            $current = clone $start;
            
            $createdCount = 0;
            $proposal = $this->model->getProposalById($proposalId);
            
            while ($current <= $end) {
                if (in_array((int)$current->format('w'), $selectedDayNumbers)) {
                    $sessionData = array_merge($commonData, [
                        'session_date' => $current->format('Y-m-d')
                    ]);
                    
                    $sessionId = $this->model->createSession($sessionData);
                    
                    // Populate participants for ALL sessions
                    if ($proposal) {
                        $sessionData['session_id'] = $sessionId;
                        $this->notifyMothersAboutSession($proposal, $sessionData);
                    }
                    
                    $createdCount++;
                }
                $current->modify('+1 day');
            }

            $_SESSION['flash'] = "Successfully created $createdCount feeding sessions! Parents have been notified.";
            header('Location: index.php?action=feedingSessions&proposal_id=' . $proposalId);
            exit;
        } catch (Exception $e) {
            error_log("Error creating bulk sessions: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while creating sessions.';
            header('Location: index.php?action=bulkSessionForm&proposal_id=' . $proposalId);
            exit;
        }
    }

    // ========================================================================
    // PROCESS 16: Participating in Feeding Program (Attendance)
    // ========================================================================

    /**
     * Show attendance form for a session
     */
    public function showSessionAttendance(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        $sessionId = (int)($_GET['session_id'] ?? 0);

        $session = $this->model->getSessionById($sessionId);
        if (!$session) {
            $_SESSION['flash_error'] = 'Session not found.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        // Get existing attendance records
        $attendanceRecords = $this->model->getAttendanceBySession($sessionId);

        // Get affected children from proposal (for pre-population)
        $affectedChildren = [];
        if (!empty($session['affected_children_data'])) {
            $affectedChildren = json_decode($session['affected_children_data'], true) ?: [];
        }

        // Get attendance statistics
        $stats = $this->model->getSessionAttendanceStats($sessionId);

        $pageTitle = 'Attendance - ' . $session['activity_name'];
        $activeNav = 'feeding_program';
        include __DIR__ . '/../views/bns/feeding_attendance_form.php';
    }

    /**
     * Save attendance record
     */
    public function saveAttendance(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        Security::verifyCsrf();

        $attendanceId = (int)($_POST['attendance_id'] ?? 0);
        $sessionId = (int)($_POST['session_id'] ?? 0);

        $session = $this->model->getSessionById($sessionId);
        if (!$session) {
            $_SESSION['flash_error'] = 'Session not found.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        // Verify BNS has access to this session's barangay
        $proposal = $this->model->getProposalById($session['proposal_id']);
        if (!$proposal) {
            $_SESSION['flash_error'] = 'Feeding program not found.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        $userBarangay = $_SESSION['barangay_code'] ?? '';
        if ($proposal['barangay_code'] != $userBarangay && $_SESSION['role'] !== 'Admin') {
            $_SESSION['flash_error'] = 'Access denied. This program belongs to a different barangay.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        $data = [
            'session_id'       => $sessionId,
            'proposal_id'      => $session['proposal_id'],
            'child_id'         => (int)($_POST['child_id'] ?? 0) ?: null,
            'mother_id'        => (int)($_POST['mother_id'] ?? 0) ?: null,
            'name_of_client'   => trim($_POST['name_of_client'] ?? ''),
            'mother_name'      => trim($_POST['mother_name'] ?? '') ?: null,
            'purok'            => trim($_POST['purok'] ?? '') ?: null,
            'pinggang_pinoy'   => isset($_POST['pinggang_pinoy']) ? 1 : 0,
            'id_kumainments'   => isset($_POST['id_kumainments']) ? 1 : 0,
            'others'           => trim($_POST['others'] ?? '') ?: null,
            'signature_data'   => trim($_POST['signature_data'] ?? '') ?: null,
            'is_present'       => isset($_POST['is_present']) ? 1 : 0,
            'time_in'          => trim($_POST['time_in'] ?? '') ?: null,
            'meal_received'    => trim($_POST['meal_received'] ?? '') ?: null,
        ];

        // Validation
        $errors = [];
        if (empty($data['name_of_client'])) {
            $errors[] = 'Name of client is required.';
        }

        // Check for duplicate attendance (only when creating new record)
        if (!$attendanceId && !empty($data['name_of_client'])) {
            $existingRecords = $this->model->getAttendanceBySession($sessionId);
            foreach ($existingRecords as $record) {
                if (strtolower(trim($record['name_of_client'])) === strtolower(trim($data['name_of_client']))) {
                    $errors[] = 'This participant is already in the attendance list.';
                    break;
                }
            }
        }

        // IMPORTANT: Validate that the child is in the Committee's affected_children_data
        // Only enforce this for NEW records, not edits
        if (!$attendanceId && !empty($data['name_of_client'])) {
            $affectedChildren = [];
            if (!empty($proposal['affected_children_data'])) {
                $affectedChildren = json_decode($proposal['affected_children_data'], true);
            }
            
            if (!empty($affectedChildren)) {
                $childFound = false;
                $childNameLower = strtolower(trim($data['name_of_client']));
                
                foreach ($affectedChildren as $child) {
                    $listChildName = $child['child_name'] ?? $child['name'] ?? $child['full_name'] ?? '';
                    if (strtolower(trim($listChildName)) === $childNameLower) {
                        $childFound = true;
                        break;
                    }
                }
                
                if (!$childFound) {
                    $errors[] = 'This child is not in the Committee on Health\'s list of affected children for this feeding program. Only children from the approved list can be added.';
                }
            }

            // Prevent re-adding children already marked as recovered for this proposal.
            // Existing old attendance rows are preserved for history/audit.
            if (empty($errors) && $this->isChildRecoveredInProposal((int)$proposal['proposal_id'], (string)$data['name_of_client'])) {
                $errors[] = 'This child is already marked as Recovered for this feeding program and should no longer be added to new attendance entries.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $data;
            header('Location: index.php?action=sessionAttendance&session_id=' . $sessionId);
            exit;
        }

        try {
            if ($attendanceId) {
                $this->model->updateAttendance($attendanceId, $data);
                $_SESSION['flash'] = 'Attendance updated successfully.';
            } else {
                $this->model->recordAttendance($data);
                $_SESSION['flash'] = 'Attendance recorded successfully.';
            }
            header('Location: index.php?action=sessionAttendance&session_id=' . $sessionId);
            exit;
        } catch (Exception $e) {
            error_log("Error saving attendance: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while saving attendance.';
            header('Location: index.php?action=sessionAttendance&session_id=' . $sessionId);
            exit;
        }
    }

    /**
     * Delete attendance record
     */
    public function deleteAttendance(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        Security::verifyCsrf();

        $attendanceId = (int)($_POST['attendance_id'] ?? 0);
        $attendance = $this->model->getAttendanceById($attendanceId);

        if (!$attendance) {
            $_SESSION['flash_error'] = 'Attendance record not found.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        $sessionId = $attendance['session_id'];

        try {
            $this->model->deleteAttendance($attendanceId);
            $_SESSION['flash'] = 'Attendance record deleted successfully.';
        } catch (Exception $e) {
            error_log("Error deleting attendance: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred while deleting the attendance record.';
        }

        header('Location: index.php?action=sessionAttendance&session_id=' . $sessionId);
        exit;
    }

    /**
     * View attendance report for a session
     */
    public function viewAttendanceReport(): void {
        $this->requireAuth();
        $sessionId = (int)($_GET['session_id'] ?? 0);

        $session = $this->model->getSessionById($sessionId);
        if (!$session) {
            $_SESSION['flash_error'] = 'Session not found.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        $attendanceRecords = $this->model->getAttendanceBySession($sessionId);
        $stats = $this->model->getSessionAttendanceStats($sessionId);

        $pageTitle = 'Attendance Report';
        include __DIR__ . '/../views/bns/feeding_attendance_report.php';
    }

    // ========================================================================
    // RSVP SYSTEM - Parent Confirmation
    // ========================================================================

    /**
     * Create an attendance/RSVP record for a child in a feeding session
     * 
     * This creates a record in the feeding_program_attendance table which serves
     * two purposes:
     * 1. RSVP tracking - parent can confirm/decline attendance
     * 2. Attendance tracking - BNS can mark present/absent during the session
     * 
     * IMPORTANT: This should be called for EVERY affected child, regardless of
     * whether their parent has a user account or not.
     * 
     * Create RSVP record when notifying parent
     * Returns the attendance_id
     * Now supports children WITHOUT parent accounts (parentId can be NULL)
     * parentName should be the mother's name from family_members table (Wife or Head role)
     */
    private function createRSVPRecord(?int $sessionId, int $proposalId, string $childName, string $parentName, ?int $parentId, ?string $purok = null): ?int {
        try {
            // Only create if session exists
            if (!$sessionId) {
                error_log("Cannot create RSVP: session_id is NULL");
                return null;
            }

            // Check if attendance record already exists for this child in this session
            $checkStmt = $this->db->prepare("
                SELECT attendance_id 
                FROM feeding_program_attendance 
                WHERE session_id = :session_id 
                AND LOWER(TRIM(name_of_client)) = LOWER(TRIM(:child_name))
            ");
            $checkStmt->execute([
                ':session_id' => $sessionId,
                ':child_name' => $childName
            ]);
            
            $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if ($existing) {
                error_log("Attendance record already exists for child '{$childName}' in session {$sessionId} (attendance_id: {$existing['attendance_id']})");
                return (int)$existing['attendance_id']; // Return existing ID instead of creating duplicate
            }

            // Set RSVP status - use 'pending' for all since that's the valid ENUM value
            $rsvpStatus = 'pending';

            $stmt = $this->db->prepare("
                INSERT INTO feeding_program_attendance (
                    session_id, proposal_id, name_of_client, mother_name, mother_id,
                    purok, rsvp_status, is_present
                ) VALUES (
                    :session_id, :proposal_id, :child_name, :parent_name, :parent_id,
                    :purok, :rsvp_status, NULL
                )
            ");
            
            $stmt->execute([
                ':session_id' => $sessionId,
                ':proposal_id' => $proposalId,
                ':child_name' => $childName,
                ':parent_name' => $parentName,
                ':parent_id' => $parentId,
                ':purok' => $purok,
                ':rsvp_status' => $rsvpStatus
            ]);
            
            $newId = (int)$this->db->lastInsertId();
            error_log("Created new attendance record for child '{$childName}' in session {$sessionId} (attendance_id: {$newId})");
            return $newId;
        } catch (Exception $e) {
            error_log("Error creating RSVP record: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check whether the latest recovery validation for this child in this proposal
     * already marks the child as Recovered.
     */
    private function isChildRecoveredInProposal(int $proposalId, string $childName): bool {
        $childName = trim($childName);
        if ($proposalId <= 0 || $childName === '') {
            return false;
        }

        $stmt = $this->db->prepare("
            SELECT nrv.recovery_status
            FROM nutritional_recovery_validations nrv
            WHERE nrv.proposal_id = :proposal_id
              AND LOWER(TRIM(nrv.full_name)) = LOWER(TRIM(:child_name))
            ORDER BY nrv.validation_date DESC, nrv.validation_id DESC
            LIMIT 1
        ");
        $stmt->execute([
            ':proposal_id' => $proposalId,
            ':child_name' => $childName
        ]);

        $latestStatus = $stmt->fetchColumn();
        return $latestStatus === 'Recovered';
    }

    /**
     * Parent confirms or declines attendance
     */
    public function respondToRSVP(): void {
        $this->requireAuth();
        Security::verifyCsrf();

        $attendanceId = (int)($_POST['attendance_id'] ?? 0);
        $response = trim($_POST['response'] ?? ''); // 'confirmed' or 'declined'
        $declineReason = trim($_POST['decline_reason'] ?? ''); // Reason for declining

        if (!in_array($response, ['confirmed', 'declined'])) {
            $_SESSION['flash_error'] = 'Invalid response.';
            header('Location: index.php?action=feedingDashboard');
            exit;
        }

        // If declining, reason is required
        if ($response === 'declined' && empty($declineReason)) {
            $_SESSION['flash_error'] = 'Please provide a reason for declining.';
            header('Location: index.php?action=feedingDashboard');
            exit;
        }

        try {
            // First, check if the attendance record exists and belongs to this parent
            $checkStmt = $this->db->prepare("
                SELECT a.attendance_id, a.mother_id, a.name_of_client, a.mother_name, a.session_id,
                       s.activity_name, s.session_date
                FROM feeding_program_attendance a
                JOIN feeding_program_sessions s ON s.session_id = a.session_id
                WHERE a.attendance_id = :attendance_id
            ");
            $checkStmt->execute([':attendance_id' => $attendanceId]);
            $record = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if (!$record) {
                $_SESSION['flash_error'] = 'Attendance record not found.';
                header('Location: index.php?action=feedingDashboard');
                exit;
            }

            // Verify ownership: either mother_id matches OR mother_name matches logged-in user
            $parentUserId = $_SESSION['user_id'];
            $parentFullName = trim((string)($_SESSION['user_name'] ?? ''));
            
            $isOwner = false;
            if ($record['mother_id'] && $record['mother_id'] == $parentUserId) {
                $isOwner = true;
            } elseif (!$record['mother_id'] && $parentFullName !== '') {
                // For records without mother_id, match by mother_name
                if (strtolower(trim($record['mother_name'])) === strtolower(trim($parentFullName))) {
                    $isOwner = true;
                }
            }

            if (!$isOwner) {
                error_log("RSVP ownership check failed - User ID: {$parentUserId}, Name: {$parentFullName}, Record mother_id: {$record['mother_id']}, Record mother_name: {$record['mother_name']}");
                $_SESSION['flash_error'] = 'You do not have permission to respond to this attendance record.';
                header('Location: index.php?action=feedingDashboard');
                exit;
            }

            // Update the RSVP status with decline reason if applicable
            if ($response === 'declined') {
                $stmt = $this->db->prepare("
                    UPDATE feeding_program_attendance
                    SET rsvp_status = :status, 
                        rsvp_date = NOW(),
                        decline_reason = :decline_reason,
                        decline_date = NOW()
                    WHERE attendance_id = :attendance_id
                ");
                
                $stmt->execute([
                    ':status' => $response,
                    ':decline_reason' => $declineReason,
                    ':attendance_id' => $attendanceId
                ]);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE feeding_program_attendance
                    SET rsvp_status = :status, 
                        rsvp_date = NOW(),
                        decline_reason = NULL,
                        decline_date = NULL
                    WHERE attendance_id = :attendance_id
                ");
                
                $stmt->execute([
                    ':status' => $response,
                    ':attendance_id' => $attendanceId
                ]);
            }

            $rowsAffected = $stmt->rowCount();
            error_log("RSVP updated - Attendance ID: {$attendanceId}, Response: {$response}, Rows affected: {$rowsAffected}");

            // Notify BNS about the decline with reason
            if ($response === 'declined') {
                // Get BNS for this session
                $bnsStmt = $this->db->prepare("
                    SELECT bns_id FROM feeding_program_proposals
                    WHERE proposal_id = (SELECT proposal_id FROM feeding_program_sessions WHERE session_id = :session_id)
                ");
                $bnsStmt->execute([':session_id' => $record['session_id']]);
                $bnsId = $bnsStmt->fetchColumn();

                if ($bnsId) {
                    $message = sprintf(
                        "%s declined attendance for %s (Session: %s). Reason: %s",
                        htmlspecialchars($record['name_of_client']),
                        date('F j, Y', strtotime($record['session_date'])),
                        htmlspecialchars($record['activity_name']),
                        htmlspecialchars($declineReason)
                    );
                    
                    $this->notifModel->create(
                        $bnsId,
                        'feeding_rsvp_declined',
                        $record['session_id'],
                        $message
                    );
                }
            }

            $message = $response === 'confirmed' 
                ? 'Thank you for confirming your attendance!' 
                : 'Your response has been recorded. The BNS has been notified.';
            
            $_SESSION['flash'] = $message;
            header('Location: index.php?action=feedingDashboard');
            exit;
        } catch (Exception $e) {
            error_log("Error responding to RSVP: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred: ' . $e->getMessage();
            header('Location: index.php?action=feedingDashboard');
            exit;
        }
    }

    /**
     * BNS views RSVP list for a session
     */
    public function showSessionRSVPList(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        $sessionId = (int)($_GET['session_id'] ?? 0);

        $session = $this->model->getSessionById($sessionId);
        if (!$session) {
            $_SESSION['flash_error'] = 'Session not found.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        // Repair legacy rows that still have placeholder parent names.
        $this->backfillMissingMotherNamesForSession($sessionId);

        // Get RSVP list
        $stmt = $this->db->prepare("
            SELECT 
                fpa.*,
                fpa.attendance_marked_at as attendance_timestamp,
                u.first_name as parent_first_name,
                u.last_name as parent_last_name,
                u.email as parent_email,
                u.contact as parent_phone
            FROM feeding_program_attendance fpa
            LEFT JOIN users u ON u.user_id = fpa.mother_id
            WHERE fpa.session_id = :session_id
            ORDER BY fpa.rsvp_status DESC, fpa.name_of_client ASC
        ");
        $stmt->execute([':session_id' => $sessionId]);
        $rsvpList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate stats
        $stats = [
            'total' => count($rsvpList),
            'confirmed' => 0,
            'declined' => 0,
            'pending' => 0,
            'present' => 0,
            'absent' => 0,
            'not_marked' => 0
        ];

        foreach ($rsvpList as $rsvp) {
            $stats[$rsvp['rsvp_status']]++;
            if ($rsvp['is_present'] === null) {
                $stats['not_marked']++;
            } elseif ($rsvp['is_present'] == 1) {
                $stats['present']++;
            } else {
                $stats['absent']++;
            }
        }

        $pageTitle = 'Attendance';
        $activeNav = 'feeding_program';
        include __DIR__ . '/../views/bns/session_rsvp_list.php';
    }

    /**
     * Backfill missing/placeholder mother names for existing session attendance rows.
     * This repairs older rows created before mother/wife mapping improvements.
     */
    private function backfillMissingMotherNamesForSession(int $sessionId): void {
        try {
            $stmt = $this->db->prepare("
                UPDATE feeding_program_attendance fpa
                LEFT JOIN (
                    SELECT
                        latest_na.full_name AS child_name,
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
                        ) AS resolved_mother_name
                    FROM (
                        SELECT na1.*
                        FROM nutrition_assessments na1
                        INNER JOIN (
                            SELECT
                                COALESCE(child_id, fm_member_id, CONCAT(full_name, dob)) AS child_key,
                                MAX(assessment_date) AS max_date
                            FROM nutrition_assessments
                            WHERE assessed_type = 'child'
                            GROUP BY child_key
                        ) na2 ON COALESCE(na1.child_id, na1.fm_member_id, CONCAT(na1.full_name, na1.dob)) = na2.child_key
                             AND na1.assessment_date = na2.max_date
                        WHERE na1.assessed_type = 'child'
                    ) latest_na
                    LEFT JOIN family_members fm_child ON fm_child.member_id = latest_na.fm_member_id
                    LEFT JOIN family_members wife ON wife.family_id = fm_child.family_id AND wife.role = 'Wife'
                    LEFT JOIN family_members head ON head.family_id = fm_child.family_id AND head.role = 'Head'
                ) resolved ON resolved.child_name COLLATE utf8mb4_general_ci = fpa.name_of_client COLLATE utf8mb4_general_ci
                SET fpa.mother_name = resolved.resolved_mother_name
                WHERE fpa.session_id = :session_id
                  AND (
                      fpa.mother_name IS NULL
                      OR TRIM(fpa.mother_name) = ''
                      OR fpa.mother_name IN ('N/A', 'No Parent Information', 'No Parent Account')
                  )
                  AND resolved.resolved_mother_name IS NOT NULL
                  AND TRIM(resolved.resolved_mother_name) != ''
            ");
            $stmt->execute([':session_id' => $sessionId]);
        } catch (Exception $e) {
            error_log("Error backfilling mother names for session {$sessionId}: " . $e->getMessage());
        }
    }

    /**
     * BNS marks attendance (present/absent)
     */
    public function markAttendance(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        Security::verifyCsrf();

        $attendanceId = (int)($_POST['attendance_id'] ?? 0);
        $isPresent = isset($_POST['is_present']) ? (int)$_POST['is_present'] : null;
        $sessionId = (int)($_POST['session_id'] ?? 0);

        try {
            // Get current timestamp
            $now = date('Y-m-d H:i:s');
            
            $stmt = $this->db->prepare("
                UPDATE feeding_program_attendance
                SET is_present = :is_present,
                    attendance_marked_by = :bns_id,
                    attendance_marked_at = :marked_at
                WHERE attendance_id = :attendance_id
            ");
            
            $result = $stmt->execute([
                ':is_present' => $isPresent,
                ':bns_id' => $_SESSION['user_id'],
                ':marked_at' => $now,
                ':attendance_id' => $attendanceId
            ]);

            if ($result && $stmt->rowCount() > 0) {
                $_SESSION['flash'] = 'Attendance marked successfully.';
            } else {
                error_log("Warning: markAttendance - No rows updated for attendance_id: $attendanceId");
                $_SESSION['flash'] = 'Attendance marked successfully.';
            }
            
            header('Location: index.php?action=sessionRSVPList&session_id=' . $sessionId);
            exit;
        } catch (Exception $e) {
            error_log("Error marking attendance: " . $e->getMessage());
            $_SESSION['flash_error'] = 'An error occurred: ' . $e->getMessage();
            header('Location: index.php?action=sessionRSVPList&session_id=' . $sessionId);
            exit;
        }
    }

    // ========================================================================
    // PROCESS 16: QR Code Attendance
    // ========================================================================

    /**
     * Show QR code scanner page
     */
    public function showQRScanner(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        $sessionId = (int)($_GET['session_id'] ?? 0);

        // Get session details
        $session = $this->model->getSessionById($sessionId);
        if (!$session) {
            $_SESSION['flash_error'] = 'Session not found.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        // Get attendance stats
        $stats = $this->model->getSessionAttendanceStats($sessionId);

        $pageTitle = 'QR Scanner';
        $activeNav = 'feeding_program';
        include __DIR__ . '/../views/bns/qr_scanner.php';
    }

    /**
     * Show single session QR code (NEW - one QR for all participants)
     */
    public function showSessionQRCode(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        $sessionId = (int)($_GET['session_id'] ?? 0);

        // Get session details
        $session = $this->model->getSessionById($sessionId);
        if (!$session) {
            $_SESSION['flash_error'] = 'Session not found.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        $pageTitle = 'Session QR Code';
        $activeNav = 'feeding_program';
        include __DIR__ . '/../views/bns/session_qr_code.php';
    }

    /**
     * Show attendance form for participants who scanned QR code
     * 
     * PUBLIC ACCESS - No authentication required
     * This allows parents/guardians WITHOUT ACCOUNTS to mark attendance
     * by scanning QR codes at feeding sessions
     */
    public function attendViaQR(): void {
        // PUBLIC ACCESS - No requireAuth() call here intentionally
        
        $sessionId = (int)($_GET['session_id'] ?? 0);

        // Get session details
        $session = $this->model->getSessionById($sessionId);
        if (!$session) {
            echo '<h3>Session not found</h3>';
            exit;
        }

        // Check if user is logged in (has account)
        $loggedInUserId = $_SESSION['user_id'] ?? null;
        $userChildren = [];
        
        if ($loggedInUserId) {
            // Get children associated with this parent/mother account
            // First, get their household_id
            $stmtHousehold = $this->db->prepare("
                SELECT hm.household_id
                FROM household_members hm
                WHERE hm.user_id = :user_id
                LIMIT 1
            ");
            $stmtHousehold->execute([':user_id' => $loggedInUserId]);
            $householdRow = $stmtHousehold->fetch(PDO::FETCH_ASSOC);
            
            if ($householdRow) {
                $householdId = $householdRow['household_id'];
                
                // Strategy 1: Try direct ID matching (ideal case)
                $stmtChildren = $this->db->prepare("
                    SELECT 
                        fpa.attendance_id,
                        fpa.name_of_client,
                        fpa.mother_name,
                        fpa.purok,
                        fpa.rsvp_status,
                        fpa.is_present,
                        fpa.child_id,
                        fpa.mother_id
                    FROM feeding_program_attendance fpa
                    LEFT JOIN household_children hc ON hc.child_id = fpa.child_id
                    WHERE fpa.session_id = :session_id
                      AND (fpa.mother_id = :user_id OR hc.household_id = :household_id)
                    ORDER BY fpa.name_of_client ASC
                ");
                $stmtChildren->execute([
                    ':session_id' => $sessionId,
                    ':user_id' => $loggedInUserId,
                    ':household_id' => $householdId
                ]);
                $userChildren = $stmtChildren->fetchAll(PDO::FETCH_ASSOC);
                
                // Strategy 2: If no results, try name-based matching
                if (empty($userChildren)) {
                    // Get household children names
                    $stmtHouseholdChildren = $this->db->prepare("
                        SELECT child_id, first_name, last_name,
                               CONCAT(last_name, ', ', first_name) as formatted_name
                        FROM household_children
                        WHERE household_id = :household_id
                    ");
                    $stmtHouseholdChildren->execute([':household_id' => $householdId]);
                    $householdChildren = $stmtHouseholdChildren->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Match by name pattern
                    if (!empty($householdChildren)) {
                        $nameConditions = [];
                        $params = [':session_id' => $sessionId];
                        
                        foreach ($householdChildren as $idx => $child) {
                            $paramKey = ":name{$idx}";
                            $nameConditions[] = "fpa.name_of_client LIKE {$paramKey}";
                            $params[$paramKey] = $child['formatted_name'] . '%';
                        }
                        
                        if (!empty($nameConditions)) {
                            $nameQuery = "
                                SELECT 
                                    fpa.attendance_id,
                                    fpa.name_of_client,
                                    fpa.mother_name,
                                    fpa.purok,
                                    fpa.rsvp_status,
                                    fpa.is_present,
                                    fpa.child_id,
                                    fpa.mother_id
                                FROM feeding_program_attendance fpa
                                WHERE fpa.session_id = :session_id
                                  AND (" . implode(' OR ', $nameConditions) . ")
                                ORDER BY fpa.name_of_client ASC
                            ";
                            $stmtNameMatch = $this->db->prepare($nameQuery);
                            $stmtNameMatch->execute($params);
                            $userChildren = $stmtNameMatch->fetchAll(PDO::FETCH_ASSOC);
                        }
                    }
                }
            }
        }

        // Get list of ALL participants/children for this session (fallback for users without accounts)
        $stmt = $this->db->prepare("
            SELECT 
                attendance_id,
                name_of_client,
                mother_name,
                purok,
                rsvp_status,
                is_present
            FROM feeding_program_attendance
            WHERE session_id = :session_id
            ORDER BY name_of_client ASC
        ");
        $stmt->execute([':session_id' => $sessionId]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Generate CSRF token if not exists
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        include __DIR__ . '/../views/shared/attend_via_qr.php';
    }

    /**
     * Submit attendance from QR code scan
     * 
     * PUBLIC ACCESS - No authentication required
     * Allows unauthenticated users to submit their attendance
     */
    public function submitAttendanceViaQR(): void {
        // PUBLIC ACCESS - No requireAuth() call here intentionally
        
        Security::verifyCsrf();

        $sessionId = (int)($_POST['session_id'] ?? 0);
        
        // Check if this is automated multi-child marking (for parents with accounts)
        $autoMarkChildren = $_POST['auto_mark_children'] ?? '';
        $attendanceId = (int)($_POST['attendance_id'] ?? 0);

        if (!$sessionId) {
            $_SESSION['attendance_error'] = 'Invalid session.';
            header('Location: index.php?action=attendViaQR&session_id=' . $sessionId);
            exit;
        }

        // Handle automated multi-child marking
        if (!empty($autoMarkChildren)) {
            $childrenIds = array_filter(array_map('intval', explode(',', $autoMarkChildren)));
            
            if (empty($childrenIds)) {
                $_SESSION['attendance_error'] = 'No children to mark.';
                header('Location: index.php?action=attendViaQR&session_id=' . $sessionId);
                exit;
            }

            try {
                $now = date('Y-m-d H:i:s');
                $timeOnly = date('H:i:s');
                $markedCount = 0;
                $childrenNames = [];

                foreach ($childrenIds as $attId) {
                    // Get the attendance record
                    $stmt = $this->db->prepare("
                        SELECT name_of_client, is_present
                        FROM feeding_program_attendance 
                        WHERE attendance_id = :attendance_id 
                          AND session_id = :session_id
                    ");
                    $stmt->execute([
                        ':attendance_id' => $attId,
                        ':session_id' => $sessionId
                    ]);
                    
                    $record = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($record && $record['is_present'] != 1) {
                        // Update the record to mark as present
                        $updateStmt = $this->db->prepare("
                            UPDATE feeding_program_attendance 
                            SET is_present = 1,
                                time_in = :time_in,
                                attendance_marked_at = :marked_at,
                                attendance_marked_by = NULL,
                                updated_at = :updated_at
                            WHERE attendance_id = :attendance_id
                        ");
                        
                        $updateStmt->execute([
                            ':attendance_id' => $attId,
                            ':time_in' => $timeOnly,
                            ':marked_at' => $now,
                            ':updated_at' => $now
                        ]);
                        
                        $markedCount++;
                        $childrenNames[] = $record['name_of_client'];
                    }
                }

                if ($markedCount > 0) {
                    $message = $markedCount === 1 
                        ? 'Thank you! Attendance recorded for ' . htmlspecialchars($childrenNames[0]) 
                        : 'Thank you! Attendance recorded for ' . $markedCount . ' children: ' . htmlspecialchars(implode(', ', $childrenNames));
                    $_SESSION['attendance_success'] = $message;
                } else {
                    $_SESSION['attendance_error'] = 'All children are already marked as present.';
                }

                header('Location: index.php?action=attendViaQR&session_id=' . $sessionId);
                exit;

            } catch (Exception $e) {
                error_log("QR Attendance error: " . $e->getMessage());
                $_SESSION['attendance_error'] = 'An error occurred. Please try again or inform the staff.';
                header('Location: index.php?action=attendViaQR&session_id=' . $sessionId);
                exit;
            }
        }

        // Handle single attendance marking (walk-ins or single selection)
        if (!$attendanceId) {
            $_SESSION['attendance_error'] = 'Please select your name.';
            header('Location: index.php?action=attendViaQR&session_id=' . $sessionId);
            exit;
        }

        try {
            // Get the attendance record
            $stmt = $this->db->prepare("
                SELECT name_of_client, is_present
                FROM feeding_program_attendance 
                WHERE attendance_id = :attendance_id 
                  AND session_id = :session_id
            ");
            $stmt->execute([
                ':attendance_id' => $attendanceId,
                ':session_id' => $sessionId
            ]);
            
            $record = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$record) {
                $_SESSION['attendance_error'] = 'Attendance record not found.';
                header('Location: index.php?action=attendViaQR&session_id=' . $sessionId);
                exit;
            }

            // Check if already marked present
            if ($record['is_present'] == 1) {
                $_SESSION['attendance_error'] = 'You have already marked your attendance.';
                header('Location: index.php?action=attendViaQR&session_id=' . $sessionId);
                exit;
            }

            // Update the record to mark as present
            $now = date('Y-m-d H:i:s');
            $timeOnly = date('H:i:s');
            
            $updateStmt = $this->db->prepare("
                UPDATE feeding_program_attendance 
                SET is_present = 1,
                    time_in = :time_in,
                    attendance_marked_at = :marked_at,
                    attendance_marked_by = NULL,
                    updated_at = :updated_at
                WHERE attendance_id = :attendance_id
            ");
            
            $updateStmt->execute([
                ':attendance_id' => $attendanceId,
                ':time_in' => $timeOnly,
                ':marked_at' => $now,
                ':updated_at' => $now
            ]);

            $_SESSION['attendance_success'] = 'Thank you, ' . htmlspecialchars($record['name_of_client']) . '! Your attendance has been recorded.';
            header('Location: index.php?action=attendViaQR&session_id=' . $sessionId);
            exit;

        } catch (Exception $e) {
            error_log("QR Attendance error: " . $e->getMessage());
            $_SESSION['attendance_error'] = 'An error occurred. Please try again or inform the staff.';
            header('Location: index.php?action=attendViaQR&session_id=' . $sessionId);
            exit;
        }
    }

    /**
     * Show QR code generation page
     */
    public function showGenerateQRCodes(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        $sessionId = (int)($_GET['session_id'] ?? 0);

        // Get session details
        $session = $this->model->getSessionById($sessionId);
        if (!$session) {
            $_SESSION['flash_error'] = 'Session not found.';
            header('Location: index.php?action=feedingProgramList');
            exit;
        }

        // Get participants - could be from attendance records or family members
        // Option 1: Get from existing attendance records
        $participants = $this->model->getSessionAttendance($sessionId);

        // Option 2: If no attendance yet, get potential participants from affected children
        if (empty($participants)) {
            // Get affected children based on the proposal
            $proposal = $this->model->getProposalById($session['proposal_id']);
            if ($proposal) {
                $participants = $this->model->getAffectedChildren(
                    $proposal['bns_id'],
                    $proposal['barangay_code']
                );
            }
        }

        $pageTitle = 'Generate QR Codes';
        $activeNav = 'feeding_program';
        include __DIR__ . '/../views/bns/generate_qr_codes.php';
    }

    /**
     * Mark attendance via QR code scan (AJAX endpoint)
     */
    public function markQRAttendance(): void {
        $this->requireRole(['BNS Staff', 'Admin']);
        header('Content-Type: application/json');

        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                echo json_encode(['success' => false, 'message' => 'Invalid request data.']);
                exit;
            }

            // Verify CSRF
            if (!isset($input['csrf_token']) || $input['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                echo json_encode(['success' => false, 'message' => 'Invalid security token.']);
                exit;
            }

            $sessionId = (int)($input['session_id'] ?? 0);
            $participantId = $input['participant_id'] ?? '';
            $participantName = $input['participant_name'] ?? '';

            if (!$sessionId || !$participantId) {
                echo json_encode(['success' => false, 'message' => 'Missing required data.']);
                exit;
            }

            // Check if already marked present today
            $stmt = $this->db->prepare("
                SELECT attendance_id 
                FROM feeding_program_attendance 
                WHERE session_id = :session_id 
                  AND (participant_id = :participant_id OR name_of_client = :participant_name)
                  AND DATE(created_at) = CURDATE()
            ");
            $stmt->execute([
                ':session_id' => $sessionId,
                ':participant_id' => $participantId,
                ':participant_name' => $participantName
            ]);
            
            if ($stmt->fetch()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Already marked present today.',
                    'participant_name' => $participantName
                ]);
                exit;
            }

            // Insert attendance record
            $now = date('Y-m-d H:i:s');
            $timeOnly = date('H:i:s');
            
            $insertStmt = $this->db->prepare("
                INSERT INTO feeding_program_attendance (
                    session_id, participant_id, name_of_client, 
                    is_present, time_in, attendance_marked_at,
                    attendance_marked_by, created_at
                ) VALUES (
                    :session_id, :participant_id, :participant_name,
                    1, :time_in, :marked_at, :bns_id, :created_at
                )
            ");
            
            $insertStmt->execute([
                ':session_id' => $sessionId,
                ':participant_id' => $participantId,
                ':participant_name' => $participantName,
                ':time_in' => $timeOnly,
                ':marked_at' => $now,
                ':bns_id' => $_SESSION['user_id'],
                ':created_at' => $now
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Attendance recorded successfully.',
                'participant_name' => $participantName,
                'time' => date('h:i A')
            ]);

        } catch (Exception $e) {
            error_log("QR Attendance error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while recording attendance.'
            ]);
        }
        exit;
    }
}
