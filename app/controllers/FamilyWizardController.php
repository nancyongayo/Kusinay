<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/HouseholdModel.php';
require_once __DIR__ . '/../models/FamilyLinkModel.php';
require_once __DIR__ . '/../models/HealthProfileModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class FamilyWizardController {

    private PDO $db;
    private HouseholdModel    $householdModel;
    private FamilyLinkModel   $familyLinkModel;
    private HealthProfileModel $healthModel;
    private NotificationModel  $notifModel;

    public function __construct() {
        $this->db              = getDBConnection();
        $this->householdModel  = new HouseholdModel($this->db);
        $this->familyLinkModel = new FamilyLinkModel($this->db);
        $this->healthModel     = new HealthProfileModel($this->db);
        $this->notifModel      = new NotificationModel($this->db);
    }

    // ── Guards ────────────────────────────────────────────────────────────────

    private function requireMother(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Mother') {
            header('Location: index.php?action=login'); exit;
        }
    }

    // ── Show Wizard ───────────────────────────────────────────────────────────

    public function showWizard(): void {
        $this->requireMother();
        $userId = $_SESSION['user_id'];

        // Load user row (with new columns)
        $stmt = $this->db->prepare("
            SELECT u.user_id, u.first_name, u.middle_name, u.last_name,
                   u.email, u.gender, u.civil_status, u.birthdate, u.contact,
                   up.profile_id, up.profile_status, up.submitted_at,
                   up.validated_at, up.assigned_bns_id, up.return_reason
            FROM users u
            LEFT JOIN user_profiles up ON up.user_id = u.user_id
            WHERE u.user_id = :uid
        ");
        $stmt->execute([':uid' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If user not found, redirect to login
        if (!$user) {
            session_unset();
            session_destroy();
            header('Location: index.php?action=login&reason=user_not_found');
            exit;
        }

        $healthProfile = $this->healthModel->getByUserId($userId);
        $household     = $this->householdModel->getByUserId($userId);

        // Check if BNS already encoded a family profile for this resident
        $bnsProfileData = $this->loadBnsFamilyProfileData($userId);
        if ($bnsProfileData) {
            // Merge BNS data with user data (BNS data takes precedence for household info)
            $user = array_merge($user, $bnsProfileData['user']);
            
            // If no household data yet, use BNS data
            if (!$household) {
                $household = $bnsProfileData['household'];
            } else {
                // Household exists - preserve user-entered spouse health status fields
                // (these don't exist in BNS data, so we keep what user saved)
                // Merge BNS data with existing household, but preserve spouse health fields
                $household = array_merge(
                    $bnsProfileData['household'],
                    [
                        'spouse_pregnancy_status' => $household['spouse_pregnancy_status'] ?? null,
                        'spouse_breastfeeding_status' => $household['spouse_breastfeeding_status'] ?? null,
                        'household_id' => $household['household_id'] ?? null, // Keep the real household_id
                    ]
                );
            }
            
            // Merge health profile data (BNS data takes precedence if fields are empty)
            if (!$healthProfile || empty($healthProfile['monthly_income'])) {
                $healthProfile = $healthProfile 
                    ? array_merge($healthProfile, $bnsProfileData['healthProfile'])
                    : $bnsProfileData['healthProfile'];
            }
        }

        // Load spouse link if any
        $spouseLink = null;
        $spouseUser = null;
        if ($user['civil_status'] === 'Married') {
            // Check family_links (any status — Pending counts too, spouse is still known)
            $stmt2 = $this->db->prepare("
                SELECT fl.*, u2.user_id AS spouse_id,
                       CONCAT(u2.first_name,' ',u2.last_name) AS spouse_name,
                       u2.email AS spouse_email
                FROM family_links fl
                JOIN users u2 ON u2.user_id = CASE
                    WHEN fl.user_id_a = :uid THEN fl.user_id_b
                    ELSE fl.user_id_a
                END
                WHERE (fl.user_id_a = :uid2 OR fl.user_id_b = :uid3)
                  AND fl.relationship_type = 'Husband-Wife'
                ORDER BY fl.created_at DESC
                LIMIT 1
            ");
            $stmt2->execute([':uid' => $userId, ':uid2' => $userId, ':uid3' => $userId]);
            $spouseLink = $stmt2->fetch(PDO::FETCH_ASSOC);
            if ($spouseLink) {
                $spouseUser = ['user_id' => $spouseLink['spouse_id'], 'display_name' => $spouseLink['spouse_name']];
            }

            // Fallback: use draft_spouse_user_id if column exists and no link found yet
            if (!$spouseUser && $household) {
                try {
                    $draftSpouseId = $household['draft_spouse_user_id'] ?? null;
                    if ($draftSpouseId) {
                        $draftStmt = $this->db->prepare("
                            SELECT user_id,
                                   CONCAT(first_name,' ',last_name) AS display_name
                            FROM users WHERE user_id = :uid
                        ");
                        $draftStmt->execute([':uid' => $draftSpouseId]);
                        $draftSpouse = $draftStmt->fetch(PDO::FETCH_ASSOC);
                        if ($draftSpouse) {
                            $spouseUser = $draftSpouse;
                        }
                    }
                } catch (Exception $e) {
                    // Column doesn't exist yet (migration not run) — silently ignore
                }
            }
        }

        // Load lookup tables for dropdowns
        $lookups = $this->getLookups();

        // Load children for this household (only if not from BNS)
        $children = [];
        $spouseOccupation = $household['spouse_occupation'] ?? '';
        
        // If we have BNS children, use those instead of household_children
        if (!empty($bnsProfileData['children'])) {
            $children = $bnsProfileData['children'];
        } elseif ($household && !empty($household['household_id'])) {
            // Load from household_children only if no BNS data
            $cStmt = $this->db->prepare("
                SELECT child_id, last_name, first_name, middle_name, suffix, sex, dob
                FROM household_children
                WHERE household_id = :hid
                ORDER BY dob ASC
            ");
            $cStmt->execute([':hid' => $household['household_id']]);
            $children = $cStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Draft and Returned = editable; Submitted and Validated = locked
        $readOnly  = in_array($user['profile_status'] ?? 'Draft', ['Submitted', 'Validated']);

        // Validated users go to the read-only profile summary
        if (($user['profile_status'] ?? '') === 'Validated') {
            header('Location: index.php?action=motherProfile'); exit;
        }

        $pageTitle = 'Family Profile';
        $activeNav = 'family_profile';

        include __DIR__ . '/../views/mother/mother_wizard.php';
    }

    // ── Show Profile Summary (post-validation) ────────────────────────────────

    public function showProfile(): void {
        $this->requireMother();
        $userId = $_SESSION['user_id'];

        $stmt = $this->db->prepare("
            SELECT u.*, up.profile_id, up.profile_status, up.submitted_at,
                   up.validated_at, up.assigned_bns_id
            FROM users u
            LEFT JOIN user_profiles up ON up.user_id = u.user_id
            WHERE u.user_id = :uid
        ");
        $stmt->execute([':uid' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If not yet submitted, send back to wizard
        if (!in_array($user['profile_status'] ?? '', ['Submitted', 'Validated', 'Returned'])) {
            header('Location: index.php?action=motherWizard'); exit;
        }

        // Returned users go back to wizard to fix
        if (($user['profile_status'] ?? '') === 'Returned') {
            header('Location: index.php?action=motherWizard'); exit;
        }

        // Load health profile with educ label joined
        $hpStmt = $this->db->prepare("
            SELECT uhp.*, rel.label AS educ_label
            FROM user_health_profiles uhp
            LEFT JOIN ref_educ_levels rel ON rel.id = uhp.educ_level_id
            WHERE uhp.user_id = :uid
        ");
        $hpStmt->execute([':uid' => $userId]);
        $healthProfile = $hpStmt->fetch(PDO::FETCH_ASSOC) ?: null;

        $familyLinks   = $this->familyLinkModel->getLinksForUser($userId);

        // Household with labels joined
        $hStmt = $this->db->prepare("
            SELECT h.*,
                   rws.label AS water_label,
                   rtt.label AS toilet_label,
                   rdt.label AS dwelling_label,
                   rfp.label AS fp_label,
                   rel.label AS spouse_educ_label
            FROM households h
            JOIN household_members hm ON hm.household_id = h.household_id
            LEFT JOIN ref_water_sources rws  ON rws.id = h.water_source_id
            LEFT JOIN ref_toilet_types  rtt  ON rtt.id = h.toilet_type_id
            LEFT JOIN ref_dwelling_types rdt ON rdt.id = h.dwelling_type_id
            LEFT JOIN ref_fp_methods rfp     ON rfp.id = h.fp_method_id
            LEFT JOIN ref_educ_levels rel    ON rel.id = h.spouse_educ_level_id
            WHERE hm.user_id = :uid
            LIMIT 1
        ");
        $hStmt->execute([':uid' => $userId]);
        $household = $hStmt->fetch(PDO::FETCH_ASSOC) ?: null;
        
        // If no household, check for BNS family_profiles data
        if (!$household) {
            $bnsProfileData = $this->loadBnsFamilyProfileData($userId);
            if ($bnsProfileData) {
                $household = $bnsProfileData['household'];
                // Add labels for display
                if ($household) {
                    // Load reference labels
                    if (!empty($household['water_source_id'])) {
                        $wsStmt = $this->db->prepare("SELECT label FROM ref_water_sources WHERE id = ?");
                        $wsStmt->execute([$household['water_source_id']]);
                        $household['water_label'] = $wsStmt->fetchColumn() ?: null;
                    }
                    if (!empty($household['toilet_type_id'])) {
                        $ttStmt = $this->db->prepare("SELECT label FROM ref_toilet_types WHERE id = ?");
                        $ttStmt->execute([$household['toilet_type_id']]);
                        $household['toilet_label'] = $ttStmt->fetchColumn() ?: null;
                    }
                    if (!empty($household['dwelling_type_id'])) {
                        $dtStmt = $this->db->prepare("SELECT label FROM ref_dwelling_types WHERE id = ?");
                        $dtStmt->execute([$household['dwelling_type_id']]);
                        $household['dwelling_label'] = $dtStmt->fetchColumn() ?: null;
                    }
                    if (!empty($household['fp_method_id'])) {
                        $fpStmt = $this->db->prepare("SELECT label FROM ref_fp_methods WHERE id = ?");
                        $fpStmt->execute([$household['fp_method_id']]);
                        $household['fp_label'] = $fpStmt->fetchColumn() ?: null;
                    }
                    if (!empty($household['spouse_educ_level_id'])) {
                        $seStmt = $this->db->prepare("SELECT label FROM ref_educ_levels WHERE id = ?");
                        $seStmt->execute([$household['spouse_educ_level_id']]);
                        $household['spouse_educ_label'] = $seStmt->fetchColumn() ?: null;
                    }
                }
            }
        }

        // Load children
        $children = [];
        if ($household) {
            $cStmt = $this->db->prepare("
                SELECT child_id, last_name, first_name, middle_name, suffix, sex, dob
                FROM household_children
                WHERE household_id = :hid
                ORDER BY dob ASC
            ");
            $cStmt->execute([':hid' => $household['household_id']]);
            $children = $cStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $pageTitle = 'My Profile';
        $activeNav = 'family_profile';
        include __DIR__ . '/../views/mother/mother_profile_view.php';
    }

    // ── Save Draft ────────────────────────────────────────────────────────────

    public function saveDraft(): void {
        $this->requireMother();
        $userId = $_SESSION['user_id'];

        // Verify user exists — session may be stale after DB reset
        $check = $this->db->prepare("SELECT user_id FROM users WHERE user_id = :uid");
        $check->execute([':uid' => $userId]);
        if (!$check->fetchColumn()) {
            session_unset(); session_destroy();
            header('Location: index.php?action=login&reason=session_expired'); exit;
        }

        $this->db->beginTransaction();
        try {
            $this->upsertUserColumns($userId, $_POST);
            $this->upsertUserProfile($userId, 'Draft');

            // Always save income & occupation regardless of gender
            $this->healthModel->upsert($userId, [
                'pregnancy_status'     => ($this->getGender($userId) ?? ($_POST['gender'] ?? '')) === 'Female' ? ($_POST['pregnancy_status'] ?? null) : null,
                'breastfeeding_status' => ($this->getGender($userId) ?? ($_POST['gender'] ?? '')) === 'Female' ? ($_POST['breastfeeding_status'] ?? null) : null,
                'monthly_income'       => $_POST['monthly_income'] ?? null,
                'occupation'           => trim($_POST['occupation'] ?? '') ?: null,
                'educ_level_id'        => $_POST['educ_level_id'] ?? null ?: null,
            ]);

            $this->upsertHousehold($userId, $_POST);
            $this->saveChildren($userId, $_POST);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['flash_error'] = 'Draft save failed: ' . $e->getMessage();
            header('Location: index.php?action=motherWizard'); exit;
        }

        $_SESSION['flash'] = 'Draft saved successfully.';
        header('Location: index.php?action=motherWizard'); exit;
    }

    // ── Submit Profile ────────────────────────────────────────────────────────

    public function submitProfile(): void {
        $this->requireMother();
        $userId = $_SESSION['user_id'];

        // Check profile is not already submitted/validated
        $stmt = $this->db->prepare("
            SELECT profile_status, return_reason
            FROM user_profiles
            WHERE user_id = :uid
        ");
        $stmt->execute([':uid' => $userId]);
        $profileRow = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $existing = trim((string)($profileRow['profile_status'] ?? ''));
        $normalizedExisting = strtoupper($existing);
        $hasReturnReason = trim((string)($profileRow['return_reason'] ?? '')) !== '';
        $wasPreviouslyReturned = ($normalizedExisting === 'RETURNED') || $hasReturnReason;
        
        // Debug: Log the existing status
        error_log("FamilyWizardController::submitProfile - User ID: $userId, Existing Status: " . ($existing ?: 'NULL'));
        
        // Allow resubmit if Returned (BNS sent back for correction); block if already Submitted or Validated
        if (in_array($normalizedExisting, ['SUBMITTED', 'VALIDATED'], true)) {
            $_SESSION['flash_error'] = 'Your profile has already been submitted.';
            header('Location: index.php?action=motherWizard'); exit;
        }

        // Server-side conditional stripping
        $data = $_POST;
        $data = self::stripConditionalFields($data);

        // Validate required fields
        $errors = $this->validateSubmission($data);
        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?action=motherWizard'); exit;
        }

        $this->db->beginTransaction();
        try {
            $this->upsertUserColumns($userId, $data);
            
            // Check if this resident was registered by BNS
            // Use LEFT JOIN in case user_profiles doesn't exist yet
            $bnsCheckStmt = $this->db->prepare("
                SELECT up.assigned_bns_id 
                FROM users u
                LEFT JOIN user_profiles up ON up.user_id = u.user_id
                WHERE u.user_id = :uid
            ");
            $bnsCheckStmt->execute([':uid' => $userId]);
            $assignedBnsId = $bnsCheckStmt->fetchColumn();
            
            // Debug: Log the auto-validation decision
            error_log("FamilyWizardController::submitProfile - Assigned BNS ID: " . ($assignedBnsId ?: 'NULL') . ", Existing Status: " . ($existing ?: 'NULL'));
            error_log("FamilyWizardController::submitProfile - Was previously returned: " . ($wasPreviouslyReturned ? 'YES' : 'NO'));
            error_log("FamilyWizardController::submitProfile - Will auto-validate: " . (($assignedBnsId && !$wasPreviouslyReturned) ? 'YES' : 'NO'));
            
            // Auto-validate only for BNS-registered residents on FIRST submission
            // If status was 'Returned', BNS needs to re-review the corrections (no auto-validate)
            $newStatus = 'Submitted'; // Default: submit for BNS review
            if ($assignedBnsId && !$wasPreviouslyReturned) {
                // BNS-registered resident, first submission: auto-validate
                error_log("FamilyWizardController::submitProfile - AUTO-VALIDATING for user_id=$userId");
                $newStatus = 'Validated';
                $this->upsertUserProfile($userId, 'Validated', [
                    'submitted_at' => date('Y-m-d H:i:s'),
                    'validated_at' => date('Y-m-d H:i:s'),
                    'validated_by' => $assignedBnsId
                ]);
            } else {
                // Self-registered resident OR resubmission after Return: submit for BNS review
                error_log("FamilyWizardController::submitProfile - SUBMITTING FOR REVIEW for user_id=$userId");
                $this->upsertUserProfile($userId, 'Submitted', ['submitted_at' => date('Y-m-d H:i:s')]);
            }

            // Always save income & occupation regardless of gender
            $this->healthModel->upsert($userId, [
                'pregnancy_status'     => ($data['gender'] ?? '') === 'Female' ? ($data['pregnancy_status']     ?? null) : null,
                'breastfeeding_status' => ($data['gender'] ?? '') === 'Female' ? ($data['breastfeeding_status'] ?? null) : null,
                'monthly_income'       => $data['monthly_income'] ?? null,
                'occupation'           => trim($data['occupation'] ?? '') ?: null,
                'educ_level_id'        => $data['educ_level_id'] ?? null ?: null,
            ]);

            $this->upsertHousehold($userId, $data);
            $this->saveChildren($userId, $data);

            // Auto-assign a BNS if none assigned yet
            $this->autoAssignBns($userId);

            // ── Sync to BNS family_profiles if this resident is linked ────────
            // Prevents duplicate records when BNS pre-encoded the household offline
            // and later registered the resident's account.
            $this->syncToBnsFamilyProfile($userId, $data);

            // ── Auto-create family_profiles for BNS-registered residents ──────
            // If BNS registered this resident via Register Resident (no pre-existing
            // family_profiles), create one now from their submitted wizard data.
            if ($assignedBnsId) {
                $this->autoCreateFamilyProfileIfNeeded($userId, (int)$assignedBnsId);
            }

            // Spouse tag
            if (!empty($data['spouse_user_id'])) {
                $spouseId = (int) $data['spouse_user_id'];
                if (!$this->familyLinkModel->hasVerifiedSpouse($spouseId)) {
                    $linkId = $this->familyLinkModel->createLink($userId, $spouseId, 'Husband-Wife');
                    $spouseStmt = $this->db->prepare("SELECT first_name, last_name FROM users WHERE user_id = :uid");
                    $spouseStmt->execute([':uid' => $userId]);
                    $me = $spouseStmt->fetch(PDO::FETCH_ASSOC);
                    $this->notifModel->create(
                        $spouseId,
                        'relationship_confirm',
                        $linkId,
                        ($me['first_name'] . ' ' . $me['last_name']) . ' has tagged you as their spouse. Please confirm or reject.'
                    );
                }
            }

            // Notify assigned BNS (only for self-registered residents who need validation)
            $bnsStmt = $this->db->prepare("
                SELECT assigned_bns_id FROM user_profiles WHERE user_id = :uid
            ");
            $bnsStmt->execute([':uid' => $userId]);
            $bnsId = $bnsStmt->fetchColumn();
            
            if ($bnsId) {
                $profileStmt = $this->db->prepare("SELECT profile_id FROM user_profiles WHERE user_id = :uid");
                $profileStmt->execute([':uid' => $userId]);
                $profileId = (int) $profileStmt->fetchColumn();
                $meStmt = $this->db->prepare("SELECT first_name, last_name FROM users WHERE user_id = :uid");
                $meStmt->execute([':uid' => $userId]);
                $me = $meStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($newStatus === 'Validated') {
                    // BNS-registered resident, first submission: auto-validated
                    $this->notifModel->create(
                        (int) $bnsId,
                        'profile_completed',
                        $profileId,
                        ($me['first_name'] . ' ' . $me['last_name']) . ' has completed and submitted their family profile. It has been automatically validated.'
                    );
                } else {
                    // Self-registered OR resubmission after Return: needs BNS review
                    $resubmit = $wasPreviouslyReturned;
                    $this->notifModel->create(
                        (int) $bnsId,
                        'profile_submitted',
                        $profileId,
                        $resubmit
                            ? ($me['first_name'] . ' ' . $me['last_name']) . ' has resubmitted their corrected family profile. Please review it in Profile Validation.'
                            : ($me['first_name'] . ' ' . $me['last_name']) . ' has submitted their family profile for your validation. Please review it in Profile Validation.'
                    );
                }
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['flash_error'] = 'Submission failed: ' . $e->getMessage();
            header('Location: index.php?action=motherWizard'); exit;
        }

        // Different success message based on whether auto-validated or not
        if ($newStatus === 'Validated') {
            $_SESSION['flash'] = 'Profile completed successfully. Your account is now active!';
        } else {
            $_SESSION['flash'] = 'Profile submitted successfully. Your BNS will review it shortly.';
        }
        header('Location: index.php?action=motherWizard'); exit;
    }

    // ── Search Users (AJAX) ───────────────────────────────────────────────────

    public function searchUsers(): void {
        $this->requireMother();
        $q      = trim($_GET['q'] ?? '');
        $userId = $_SESSION['user_id'];

        if (strlen($q) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }

        $like = '%' . $q . '%';
        $stmt = $this->db->prepare("
            SELECT user_id,
                   CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) AS display_name,
                   email
            FROM users
            WHERE user_id != :uid
              AND (first_name LIKE :q1 OR last_name LIKE :q2 OR email LIKE :q3
                   OR CONCAT(first_name,' ',last_name) LIKE :q4)
            LIMIT 10
        ");
        $stmt->execute([
            ':uid' => $userId,
            ':q1'  => $like,
            ':q2'  => $like,
            ':q3'  => $like,
            ':q4'  => $like,
        ]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($results);
    }

    // ── Static helper: strip conditional fields ───────────────────────────────

    /**
     * Strip health fields if gender is not Female.
     * Strip spouse_user_id if civil_status is not Married.
     */
    public static function stripConditionalFields(array $data): array {
        if (($data['gender'] ?? '') !== 'Female') {
            unset($data['pregnancy_status'], $data['breastfeeding_status']);
        }
        if (($data['civil_status'] ?? '') !== 'Married') {
            unset($data['spouse_user_id'], $data['spouse_occupation']);
        }
        return $data;
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function upsertUserColumns(int $userId, array $data): void {
        $stmt = $this->db->prepare("
            UPDATE users
            SET gender       = :gender,
                civil_status = :civil_status,
                birthdate    = :birthdate,
                contact      = :contact,
                first_name   = :first_name,
                middle_name  = :middle_name,
                last_name    = :last_name
            WHERE user_id = :user_id
        ");
        $stmt->execute([
            ':gender'       => $data['gender']       ?? null,
            ':civil_status' => $data['civil_status'] ?? null,
            ':birthdate'    => $data['birthdate']    ?? null,
            ':contact'      => $data['contact']      ?? null,
            ':first_name'   => $data['first_name']   ?? null,
            ':middle_name'  => $data['middle_name']  ?? null,
            ':last_name'    => $data['last_name']    ?? null,
            ':user_id'      => $userId,
        ]);
    }

    private function upsertUserProfile(int $userId, string $status, array $extra = []): void {
        $submittedAt = $extra['submitted_at'] ?? null;
        $validatedAt = $extra['validated_at'] ?? null;
        $validatedBy = $extra['validated_by'] ?? null;
        // Clear return_reason when resubmitting
        $clearReason = $status === 'Submitted' ? 1 : 0;
        // Set profile_complete = 1 when status is Submitted or Validated
        $profileComplete = in_array($status, ['Submitted', 'Validated']) ? 1 : 0;
        
        $stmt = $this->db->prepare("
            INSERT INTO user_profiles (user_id, profile_status, submitted_at, profile_complete, validated_at, validated_by)
            VALUES (:user_id, :status, :submitted_at, :profile_complete, :validated_at, :validated_by)
            ON DUPLICATE KEY UPDATE
                profile_status = VALUES(profile_status),
                submitted_at   = COALESCE(VALUES(submitted_at), submitted_at),
                profile_complete = VALUES(profile_complete),
                validated_at   = COALESCE(VALUES(validated_at), validated_at),
                validated_by   = COALESCE(VALUES(validated_by), validated_by),
                return_reason  = IF(:clear_reason, NULL, return_reason)
        ");
        $stmt->execute([
            ':user_id'         => $userId,
            ':status'          => $status,
            ':submitted_at'    => $submittedAt,
            ':profile_complete' => $profileComplete,
            ':validated_at'    => $validatedAt,
            ':validated_by'    => $validatedBy,
            ':clear_reason'    => $clearReason,
        ]);
    }

    private function upsertHousehold(int $userId, array $data): void {
        $existing = $this->householdModel->getByUserId($userId);
        
        // If household already exists, just update it
        if ($existing) {
            $this->householdModel->updateDetails($existing['household_id'], $data);
            return;
        }
        
        // Check if this resident has a BNS-encoded family profile
        // If yes, we should NOT create a new household to avoid duplicate HH codes
        $bnsFamilyId = $data['bns_family_id'] ?? null;
        
        // Also check database directly in case bns_family_id wasn't passed in $data
        if (!$bnsFamilyId) {
            $bnsCheckStmt = $this->db->prepare("
                SELECT family_id FROM family_profiles WHERE source_user_id = :uid LIMIT 1
            ");
            $bnsCheckStmt->execute([':uid' => $userId]);
            $bnsFamilyId = $bnsCheckStmt->fetchColumn();
        }
        
        // Also check if user's spouse has a family_profiles record
        // This handles the case where wife registers after husband was registered by BNS
        if (!$bnsFamilyId && ($data['civil_status'] ?? '') === 'Married') {
            // Check if there's a verified spouse link
            $spouseLinkStmt = $this->db->prepare("
                SELECT CASE 
                    WHEN fl.user_id_a = :uid THEN fl.user_id_b 
                    ELSE fl.user_id_a 
                END AS spouse_id
                FROM family_links fl
                WHERE (fl.user_id_a = :uid2 OR fl.user_id_b = :uid3)
                  AND fl.relationship_type = 'Husband-Wife'
                  AND fl.verification_status = 'Verified'
                LIMIT 1
            ");
            $spouseLinkStmt->execute([':uid' => $userId, ':uid2' => $userId, ':uid3' => $userId]);
            $spouseId = $spouseLinkStmt->fetchColumn();
            
            if ($spouseId) {
                // Check if spouse has a family_profiles record
                $spouseFamilyStmt = $this->db->prepare("
                    SELECT family_id FROM family_profiles WHERE source_user_id = :spouse_id LIMIT 1
                ");
                $spouseFamilyStmt->execute([':spouse_id' => $spouseId]);
                $spouseFamilyId = $spouseFamilyStmt->fetchColumn();
                
                if ($spouseFamilyId) {
                    // Spouse has BNS family_profiles - skip household creation
                    // This user will use spouse's family record
                    error_log("upsertHousehold: Skipping household creation for user_id=$userId - spouse has family_id=$spouseFamilyId");
                    return;
                }
            }
        }
        
        if ($bnsFamilyId) {
            // Resident has BNS data - skip household creation
            // The BNS family_profiles record already exists with its own HH number
            error_log("upsertHousehold: Skipping household creation for user_id=$userId - has BNS family_id=$bnsFamilyId");
            return;
        }
        
        $hofUserId = !empty($data['hof_user_id']) ? (int) $data['hof_user_id'] : $userId;
        // 'spouse' sentinel means unregistered spouse is HOF — store 0 as placeholder, flag for review
        $hofIsSpouseSentinel = ($data['hof_user_id'] ?? '') === 'spouse';
        if ($hofIsSpouseSentinel) {
            $hofUserId      = 0; // no user_id for unregistered spouse
            $hofNeedsReview = 1; // BNS will confirm during validation
        } else {
            $hofNeedsReview = empty($data['hof_user_id']) ? 1 : 0;
        }

        // Only save spouse fields if married
        $spouseOccupation = ($data['civil_status'] ?? '') === 'Married'
            ? (trim($data['spouse_occupation'] ?? '') ?: null)
            : null;

        $spouseName = ($data['civil_status'] ?? '') === 'Married'
            ? (trim($data['spouse_name'] ?? '') ?: null)
            : null;

        // Build combined spouse_name from separated fields if available
        $spouseLastName   = ($data['civil_status'] ?? '') === 'Married' ? (trim($data['spouse_last_name']   ?? '') ?: null) : null;
        $spouseFirstName  = ($data['civil_status'] ?? '') === 'Married' ? (trim($data['spouse_first_name']  ?? '') ?: null) : null;
        $spouseMiddleName = ($data['civil_status'] ?? '') === 'Married' ? (trim($data['spouse_middle_name'] ?? '') ?: null) : null;
        $spouseSuffix     = ($data['civil_status'] ?? '') === 'Married' ? (trim($data['spouse_suffix']      ?? '') ?: null) : null;
        $spouseEducLevelId = ($data['civil_status'] ?? '') === 'Married' ? ($data['spouse_educ_level_id'] ?? null ?: null) : null;

        // Rebuild combined spouse_name from parts
        if ($spouseLastName || $spouseFirstName) {
            $parts = array_filter([$spouseLastName . ($spouseFirstName ? ', ' . $spouseFirstName : ''), $spouseMiddleName, $spouseSuffix]);
            $spouseName = trim(implode(' ', $parts)) ?: null;
        }

        $spouseMonthlyIncome = ($data['civil_status'] ?? '') === 'Married' && isset($data['spouse_monthly_income']) && $data['spouse_monthly_income'] !== ''
            ? (float) $data['spouse_monthly_income']
            : null;

        // Spouse health status (only for Female spouse / when user is Male)
        $spousePregnancyStatus = ($data['civil_status'] ?? '') === 'Married' && !empty($data['spouse_pregnancy_status'])
            ? $data['spouse_pregnancy_status']
            : null;
        $spouseBreastfeedingStatus = ($data['civil_status'] ?? '') === 'Married' && !empty($data['spouse_breastfeeding_status'])
            ? $data['spouse_breastfeeding_status']
            : null;

        // Save draft spouse user_id so the HOF dropdown persists across draft saves
        $draftSpouseUserId = ($data['civil_status'] ?? '') === 'Married' && !empty($data['spouse_user_id'])
            ? (int) $data['spouse_user_id']
            : null;

        $householdData = [
            'water_source_id'        => $data['water_source_id']    ?? null,
            'toilet_type_id'         => $data['toilet_type_id']     ?? null,
            'uses_iodized_salt'      => isset($data['uses_iodized_salt']) ? 1 : 0,
            'uses_ifr'               => isset($data['uses_ifr'])          ? 1 : 0,
            'dwelling_type_id'       => $data['dwelling_type_id']   ?? null,
            'purok'                  => $data['purok']              ?? null,
            'num_hh_members'         => $data['num_hh_members']     ?? null ?: null,
            'is_mother_prog'         => isset($data['is_mother_prog'])    ? 1 : 0,
            'fp_method_id'           => $data['fp_method_id']       ?? null ?: null,
            'spouse_name'            => $spouseName,
            'spouse_last_name'       => $spouseLastName,
            'spouse_first_name'      => $spouseFirstName,
            'spouse_middle_name'     => $spouseMiddleName,
            'spouse_suffix'          => $spouseSuffix,
            'spouse_educ_level_id'   => $spouseEducLevelId,
            'spouse_occupation'      => $spouseOccupation,
            'spouse_monthly_income'  => $spouseMonthlyIncome,
            'spouse_pregnancy_status' => $spousePregnancyStatus,
            'spouse_breastfeeding_status' => $spouseBreastfeedingStatus,
            'draft_spouse_user_id'   => $draftSpouseUserId,
            'hof_user_id'            => $hofUserId,
            'hof_needs_review'       => 1, // Always 1 until BNS confirms
            'children_0_5mos'        => $data['children_0_5mos']   ?? null ?: null,
            'children_6_23mos'       => $data['children_6_23mos']  ?? null ?: null,
            'children_24_59mos'      => $data['children_24_59mos'] ?? null ?: null,
            'children_60plus'        => $data['children_60plus']   ?? null ?: null,
        ];

        if ($existing) {
            $this->householdModel->updateDetails($existing['household_id'], $householdData);
            if ($hofNeedsReview === 0) {
                $this->householdModel->updateHof($existing['household_id'], $hofUserId);
            }
        } else {
            $householdId = $this->householdModel->createHousehold($householdData);
            $this->householdModel->addMember($householdId, $userId);
        }
    }

    private function getGender(int $userId): ?string {
        $stmt = $this->db->prepare("SELECT gender FROM users WHERE user_id = :uid");
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchColumn() ?: null;
    }

    private function validateSubmission(array $data): array {
        $errors = [];
        $required = [
            'first_name'   => 'First name',
            'last_name'    => 'Last name',
            'birthdate'    => 'Birthdate',
            'gender'       => 'Gender',
            'civil_status' => 'Civil status',
            'contact'      => 'Contact number',
        ];
        foreach ($required as $field => $label) {
            if (empty(trim($data[$field] ?? ''))) {
                $errors[] = "$label is required.";
            }
        }
        if (($data['gender'] ?? '') === 'Female') {
            if (empty($data['pregnancy_status'])) {
                $errors[] = 'Pregnancy status is required for Female.';
            }
            if (empty($data['breastfeeding_status'])) {
                $errors[] = 'Breastfeeding status is required for Female.';
            }
        }
        return $errors;
    }

    private function autoAssignBns(int $userId): void {
        // Check if already assigned
        $stmt = $this->db->prepare("SELECT assigned_bns_id FROM user_profiles WHERE user_id = :uid");
        $stmt->execute([':uid' => $userId]);
        $current = $stmt->fetchColumn();
        if ($current) return;

        // Pick any active BNS Staff user (round-robin: least assigned)
        $stmt = $this->db->prepare("
            SELECT u.user_id
            FROM users u
            JOIN roles r ON r.role_id = u.role_id
            LEFT JOIN user_profiles up2 ON up2.assigned_bns_id = u.user_id
            WHERE r.role_name = 'BNS Staff'
            GROUP BY u.user_id
            ORDER BY COUNT(up2.profile_id) ASC
            LIMIT 1
        ");
        $stmt->execute();
        $bnsId = $stmt->fetchColumn();

        if ($bnsId) {
            $this->db->prepare("
                UPDATE user_profiles SET assigned_bns_id = :bns WHERE user_id = :uid
            ")->execute([':bns' => $bnsId, ':uid' => $userId]);
        }
    }

    private function saveChildren(int $userId, array $data): void {
        $household = $this->householdModel->getByUserId($userId);
        if (!$household) return;

        $householdId = (int) $household['household_id'];
        $lastNames   = $data['child_last_name']   ?? [];
        $firstNames  = $data['child_first_name']  ?? [];
        $middleNames = $data['child_middle_name'] ?? [];
        $suffixes    = $data['child_suffix']      ?? [];
        $dobs        = $data['child_dob']         ?? [];
        $sexes       = $data['child_sex']         ?? [];
        $ids         = $data['child_id']          ?? [];

        // Collect submitted child IDs (existing ones)
        $submittedIds = array_filter(array_map('intval', $ids));

        // Delete children that were removed (not in submitted list)
        if ($submittedIds) {
            $placeholders = implode(',', array_fill(0, count($submittedIds), '?'));
            $this->db->prepare("
                DELETE FROM household_children
                WHERE household_id = ? AND child_id NOT IN ($placeholders)
            ")->execute(array_merge([$householdId], $submittedIds));
        } else {
            // All children removed
            $this->db->prepare("DELETE FROM household_children WHERE household_id = ?")
                     ->execute([$householdId]);
        }

        // Upsert each child
        $upsert = $this->db->prepare("
            INSERT INTO household_children (child_id, household_id, added_by, last_name, first_name, middle_name, suffix, sex, dob)
            VALUES (:child_id, :household_id, :added_by, :last_name, :first_name, :middle_name, :suffix, :sex, :dob)
            ON DUPLICATE KEY UPDATE
                last_name   = VALUES(last_name),
                first_name  = VALUES(first_name),
                middle_name = VALUES(middle_name),
                suffix      = VALUES(suffix),
                sex         = VALUES(sex),
                dob         = VALUES(dob)
        ");

        foreach ($lastNames as $i => $lastName) {
            $lastName  = trim($lastName);
            $firstName = trim($firstNames[$i] ?? '');
            if (!$lastName && !$firstName) continue; // Skip if both empty
            
            $childId    = !empty($ids[$i]) ? (int)$ids[$i] : null;
            $middleName = trim($middleNames[$i] ?? '') ?: null;
            $suffix     = trim($suffixes[$i] ?? '') ?: null;
            $dob        = !empty($dobs[$i]) ? $dobs[$i] : null;
            $sex        = !empty($sexes[$i]) ? $sexes[$i] : null;

            if ($childId) {
                $upsert->execute([
                    ':child_id'     => $childId,
                    ':household_id' => $householdId,
                    ':added_by'     => $userId,
                    ':last_name'    => $lastName ?: null,
                    ':first_name'   => $firstName ?: null,
                    ':middle_name'  => $middleName,
                    ':suffix'       => $suffix,
                    ':sex'          => $sex,
                    ':dob'          => $dob,
                ]);
            } else {
                $this->db->prepare("
                    INSERT INTO household_children (household_id, added_by, last_name, first_name, middle_name, suffix, sex, dob)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ")->execute([$householdId, $userId, $lastName ?: null, $firstName ?: null, $middleName, $suffix, $sex, $dob]);
            }
        }
    }

    // ── Load BNS-encoded family profile data ─────────────────────────────────
    /**
     * When a resident logs in and has a BNS-encoded family profile
     * (source_user_id = userId), load that data to pre-fill the Mother Wizard.
     * 
     * Also determines the correct role label based on gender:
     * - Male resident → "Head" / "DAD"
     * - Female resident → "Wife" / "MOTHER"
     */
    private function loadBnsFamilyProfileData(int $userId): ?array {
        try {
            // Find BNS family_profiles linked to this resident
            $stmt = $this->db->prepare("
                SELECT fp.*, 
                       fm_head.member_id as head_member_id,
                       fm_head.first_name as head_first_name,
                       fm_head.middle_name as head_middle_name,
                       fm_head.last_name as head_last_name,
                       fm_head.suffix as head_suffix,
                       fm_head.sex as head_sex,
                       fm_head.dob as head_dob,
                       fm_head.civil_status as head_civil_status,
                       fm_head.occupation as head_occupation,
                       fm_head.educ_level_id as head_educ_level_id,
                       fm_head.monthly_income as head_monthly_income,
                       fm_wife.member_id as wife_member_id,
                       fm_wife.first_name as wife_first_name,
                       fm_wife.middle_name as wife_middle_name,
                       fm_wife.last_name as wife_last_name,
                       fm_wife.suffix as wife_suffix,
                       fm_wife.sex as wife_sex,
                       fm_wife.dob as wife_dob,
                       fm_wife.civil_status as wife_civil_status,
                       fm_wife.occupation as wife_occupation,
                       fm_wife.educ_level_id as wife_educ_level_id,
                       fm_wife.monthly_income as wife_monthly_income
                FROM family_profiles fp
                LEFT JOIN family_members fm_head 
                    ON fm_head.family_id = fp.family_id AND fm_head.role = 'Head'
                LEFT JOIN family_members fm_wife 
                    ON fm_wife.family_id = fp.family_id AND fm_wife.role = 'Wife'
                WHERE fp.source_user_id = :uid
                LIMIT 1
            ");
            $stmt->execute([':uid' => $userId]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$profile) return null;
            
            // Load user's gender and name to determine their role in the family
            $userStmt = $this->db->prepare("SELECT gender, contact, first_name, last_name FROM users WHERE user_id = ?");
            $userStmt->execute([$userId]);
            $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
            $userGender = $userData['gender'] ?? 'Female';
            $userFirstName = strtolower($userData['first_name'] ?? '');
            $userLastName = strtolower($userData['last_name'] ?? '');
            
            // Determine which family_members row belongs to this user
            // First try to match by name, then fall back to gender
            $headFirstName = strtolower($profile['head_first_name'] ?? '');
            $headLastName = strtolower($profile['head_last_name'] ?? '');
            $wifeFirstName = strtolower($profile['wife_first_name'] ?? '');
            $wifeLastName = strtolower($profile['wife_last_name'] ?? '');
            
            // Check if user's name matches Head or Wife
            $nameMatchesHead = ($userFirstName === $headFirstName && $userLastName === $headLastName);
            $nameMatchesWife = ($userFirstName === $wifeFirstName && $userLastName === $wifeLastName);
            
            // Determine if user is Head:
            // 1. If name matches Head → they are Head
            // 2. If name matches Wife → they are Wife (not Head)
            // 3. If no name match, use gender: Male → Head, Female → Wife
            if ($nameMatchesHead) {
                $isUserHead = true;
            } elseif ($nameMatchesWife) {
                $isUserHead = false;
            } else {
                // Fallback to gender-based matching
                $isUserHead = ($userGender === 'Male');
            }
            
            // User's own data (the logged-in resident) - STEP 1 & 2
            $memberData = $isUserHead ? [
                'first_name' => $profile['head_first_name'],
                'last_name' => $profile['head_last_name'],
                'middle_name' => $profile['head_middle_name'],
                'suffix' => $profile['head_suffix'],
                'birthdate' => $profile['head_dob'],
                'occupation' => $profile['head_occupation'],
                'educ_level_id' => $profile['head_educ_level_id'],
                'civil_status' => $profile['head_civil_status'] ?: 'Married',
                'monthly_income' => $profile['head_monthly_income'],
            ] : [
                'first_name' => $profile['wife_first_name'],
                'last_name' => $profile['wife_last_name'],
                'middle_name' => $profile['wife_middle_name'],
                'suffix' => $profile['wife_suffix'],
                'birthdate' => $profile['wife_dob'],
                'occupation' => $profile['wife_occupation'],
                'educ_level_id' => $profile['wife_educ_level_id'],
                'civil_status' => $profile['wife_civil_status'] ?: 'Married',
                'monthly_income' => $profile['wife_monthly_income'],
            ];
            
            // Spouse data (the other person in the BNS record) - STEP 1 & 2
            $spouseData = $isUserHead ? [
                'first_name' => $profile['wife_first_name'],
                'last_name' => $profile['wife_last_name'],
                'middle_name' => $profile['wife_middle_name'],
                'suffix' => $profile['wife_suffix'],
                'birthdate' => $profile['wife_dob'],
                'occupation' => $profile['wife_occupation'],
                'educ_level_id' => $profile['wife_educ_level_id'],
                'monthly_income' => $profile['wife_monthly_income'],
            ] : [
                'first_name' => $profile['head_first_name'],
                'last_name' => $profile['head_last_name'],
                'middle_name' => $profile['head_middle_name'],
                'suffix' => $profile['head_suffix'],
                'birthdate' => $profile['head_dob'],
                'occupation' => $profile['head_occupation'],
                'educ_level_id' => $profile['head_educ_level_id'],
                'monthly_income' => $profile['head_monthly_income'],
            ];
            
            // Build household data structure from BNS family profile - STEP 3
            $household = [
                'household_id' => null, // Will be created on save
                'bns_family_id' => $profile['family_id'],
                'hh_number' => $profile['hh_number'],
                'purok' => $profile['purok'],
                'water_source_id' => $profile['water_source_id'],
                'toilet_type_id' => $profile['toilet_type_id'],
                'uses_iodized_salt' => $profile['uses_iodized_salt'],
                'uses_ifr' => $profile['uses_ifr'],
                'dwelling_type_id' => $profile['dwelling_type_id'],
                'is_mother_prog' => $profile['is_mother_prog'],
                'fp_method_id' => $profile['fp_method_id'],
                'num_hh_members' => $profile['num_hh_members'],
                'children_0_5mos' => $profile['children_0_5mos'],
                'children_6_23mos' => $profile['children_6_23mos'],
                'children_24_59mos' => $profile['children_24_59mos'],
                'children_60plus' => $profile['children_60plus'],
                // Spouse fields - auto-fill from BNS data
                'spouse_last_name' => $spouseData['last_name'],
                'spouse_first_name' => $spouseData['first_name'],
                'spouse_middle_name' => $spouseData['middle_name'],
                'spouse_suffix' => $spouseData['suffix'],
                'spouse_occupation' => $spouseData['occupation'],
                'spouse_educ_level_id' => $spouseData['educ_level_id'],
                'spouse_monthly_income' => $spouseData['monthly_income'],
                'hof_user_id' => $userId, // Resident is the Head of Family
            ];
            
            // Build health profile data (user's own income/occupation) - STEP 2
            $healthProfile = [
                'monthly_income' => $memberData['monthly_income'],
                'occupation' => $memberData['occupation'],
                'educ_level_id' => $memberData['educ_level_id'],
                // Pregnancy/breastfeeding status not in BNS form - will be filled by user
            ];
            
            // Build user data (name from BNS record) - STEP 1
            $user = [
                'first_name' => $memberData['first_name'],
                'last_name' => $memberData['last_name'],
                'middle_name' => $memberData['middle_name'],
                'suffix' => $memberData['suffix'] ?? null,
                'birthdate' => $memberData['birthdate'],
                'civil_status' => $memberData['civil_status'],
                'contact' => $userData['contact'] ?? null, // From users table
                'gender' => $userGender,
                'is_head' => $isUserHead,
            ];
            
            // Load children from BNS family_members - STEP 4
            $childrenStmt = $this->db->prepare("
                SELECT member_id, first_name, middle_name, last_name, suffix, sex, dob
                FROM family_members
                WHERE family_id = :fid AND role = 'Child'
                ORDER BY dob ASC
            ");
            $childrenStmt->execute([':fid' => $profile['family_id']]);
            $bnsChildren = $childrenStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Transform BNS children to match wizard format
            $children = [];
            foreach ($bnsChildren as $child) {
                $children[] = [
                    'child_id' => null, // No child_id yet - will be created on save
                    'first_name' => $child['first_name'],
                    'middle_name' => $child['middle_name'],
                    'last_name' => $child['last_name'],
                    'suffix' => $child['suffix'],
                    'sex' => $child['sex'], // M or F
                    'dob' => $child['dob'],
                ];
            }
            
            return [
                'user' => $user,
                'household' => $household,
                'healthProfile' => $healthProfile,
                'children' => $children,
            ];
            
        } catch (\Exception $e) {
            error_log('FamilyWizardController::loadBnsFamilyProfileData error: ' . $e->getMessage());
            return null;
        }
    }

    // ── Auto-create family_profiles for BNS-registered residents ─────────────
    /**
     * When a BNS-registered resident completes their wizard, check if a
     * family_profiles record already exists for them. If not, create one
     * from their household/wizard data so it appears in the BNS Family Profiles list.
     */
    private function autoCreateFamilyProfileIfNeeded(int $userId, int $bnsId): void {
        try {
            // Skip if already exists
            $check = $this->db->prepare("SELECT family_id FROM family_profiles WHERE source_user_id = ?");
            $check->execute([$userId]);
            if ($check->fetchColumn()) return;

            // Also skip if spouse already has a family_profiles record
            $spouseCheck = $this->db->prepare("
                SELECT fp.family_id
                FROM family_links fl
                JOIN family_profiles fp ON fp.source_user_id = CASE
                    WHEN fl.user_id_a = :uid THEN fl.user_id_b
                    ELSE fl.user_id_a
                END
                WHERE (fl.user_id_a = :uid2 OR fl.user_id_b = :uid3)
                  AND fl.relationship_type = 'Husband-Wife'
                LIMIT 1
            ");
            $spouseCheck->execute([':uid' => $userId, ':uid2' => $userId, ':uid3' => $userId]);
            if ($spouseCheck->fetchColumn()) return;

            // Load household
            $household = $this->householdModel->getByUserId($userId);
            if (!$household) return;

            // Load health profile
            $hpStmt = $this->db->prepare("SELECT * FROM user_health_profiles WHERE user_id = ?");
            $hpStmt->execute([$userId]);
            $hp = $hpStmt->fetch(\PDO::FETCH_ASSOC) ?: [];

            // Load user data
            $uStmt = $this->db->prepare("SELECT first_name, middle_name, last_name, gender, civil_status FROM users WHERE user_id = ?");
            $uStmt->execute([$userId]);
            $user = $uStmt->fetch(\PDO::FETCH_ASSOC);
            if (!$user) return;

            // Auto-generate next HH # for this BNS
            $hhStmt = $this->db->prepare("SELECT COUNT(*) FROM family_profiles WHERE bns_id = ?");
            $hhStmt->execute([$bnsId]);
            $hhCount  = (int) $hhStmt->fetchColumn();
            $hhNumber = str_pad($hhCount + 1, 3, '0', STR_PAD_LEFT);

            // Determine head/wife based on gender AND civil status
            // A female who is single/separated/widowed is the Head, not the Wife
            $civilStatus = $user['civil_status'] ?? '';
            $hasSpouse   = in_array($civilStatus, ['Married', 'Live-in']);
            $isHead      = ($user['gender'] === 'Male') || !$hasSpouse;

            $headFirstName  = $isHead ? $user['first_name']  : ($household['spouse_first_name']  ?? '');
            $headLastName   = $isHead ? $user['last_name']   : ($household['spouse_last_name']   ?? '');
            $headMiddleName = $isHead ? $user['middle_name'] : ($household['spouse_middle_name'] ?? '');
            $headOccupation = $isHead ? ($hp['occupation'] ?? null) : ($household['spouse_occupation'] ?? null);
            $headEducId     = $isHead ? ($hp['educ_level_id'] ?? null) : ($household['spouse_educ_level_id'] ?? null);

            $wifeFirstName  = $isHead ? ($household['spouse_first_name']  ?? '') : $user['first_name'];
            $wifeLastName   = $isHead ? ($household['spouse_last_name']   ?? '') : $user['last_name'];
            $wifeMiddleName = $isHead ? ($household['spouse_middle_name'] ?? '') : $user['middle_name'];
            $wifeOccupation = $isHead ? ($household['spouse_occupation'] ?? null) : ($hp['occupation'] ?? null);
            $wifeEducId     = $isHead ? ($household['spouse_educ_level_id'] ?? null) : ($hp['educ_level_id'] ?? null);

            // Determine pregnancy/breastfeeding flags
            $isMotherProg = 0;
            $isErf = 0;
            $isMixed = 0;
            $isBottle = 0;
            $pregnancyStatus = $hp['pregnancy_status'] ?? '';
            $bfStatus = $hp['breastfeeding_status'] ?? '';
            if (str_contains($pregnancyStatus, 'Pregnant')) $isMotherProg = 1;
            if (str_contains($bfStatus, 'EBF') || str_contains($bfStatus, 'Exclusive')) $isErf = 1;
            if (str_contains($bfStatus, 'Mixed')) $isMixed = 1;

            // Validate fp_method_id — null it if zero or doesn't exist in ref_fp_methods
            $fpMethodId = $household['fp_method_id'] ?? null;
            if ($fpMethodId) {
                $fpCheck = $this->db->prepare("SELECT id FROM ref_fp_methods WHERE id = ?");
                $fpCheck->execute([$fpMethodId]);
                if (!$fpCheck->fetchColumn()) $fpMethodId = null;
            } else {
                $fpMethodId = null; // treat 0 and empty as NULL
            }

            // Insert family_profiles
            $this->db->prepare("
                INSERT INTO family_profiles (
                    bns_id, source_user_id, hh_number, purok, num_hh_members,
                    toilet_type_id, water_source_id, uses_iodized_salt, uses_ifr,
                    dwelling_type_id, total_income, fp_method_id,
                    is_mother_prog, is_erf, is_mixed_milk, is_bottle_feeding
                ) VALUES (
                    :bns_id, :source_user_id, :hh_number, :purok, :num_hh_members,
                    :toilet, :water, :iodized, :ifr,
                    :dwelling, :income, :fp_method,
                    :mother_prog, :is_erf, :is_mixed, :is_bottle
                )
            ")->execute([
                ':bns_id'         => $bnsId,
                ':source_user_id' => $userId,
                ':hh_number'      => $hhNumber,
                ':purok'          => $household['purok']           ?? null,
                ':num_hh_members' => $household['num_hh_members']  ?? null,
                ':toilet'         => $household['toilet_type_id']  ?? null,
                ':water'          => $household['water_source_id'] ?? null,
                ':iodized'        => $household['uses_iodized_salt'] ?? 0,
                ':ifr'            => $household['uses_ifr']        ?? 0,
                ':dwelling'       => $household['dwelling_type_id'] ?? null,
                ':income'         => $hp['monthly_income']         ?? null,
                ':fp_method'      => $fpMethodId,
                ':mother_prog'    => $isMotherProg,
                ':is_erf'         => $isErf,
                ':is_mixed'       => $isMixed,
                ':is_bottle'      => $isBottle,
            ]);
            $familyId = (int) $this->db->lastInsertId();

            // Insert Head member
            if ($headFirstName || $headLastName) {
                $this->db->prepare("
                    INSERT INTO family_members (family_id, role, first_name, middle_name, last_name, occupation, educ_level_id, sort_order)
                    VALUES (?, 'Head', ?, ?, ?, ?, ?, 0)
                ")->execute([$familyId, $headFirstName ?: null, $headMiddleName ?: null, $headLastName ?: null, $headOccupation, $headEducId]);
            }

            // Insert Wife member
            if ($wifeFirstName || $wifeLastName) {
                $this->db->prepare("
                    INSERT INTO family_members (family_id, role, first_name, middle_name, last_name, occupation, educ_level_id, sort_order)
                    VALUES (?, 'Wife', ?, ?, ?, ?, ?, 1)
                ")->execute([$familyId, $wifeFirstName ?: null, $wifeMiddleName ?: null, $wifeLastName ?: null, $wifeOccupation, $wifeEducId]);
            }

            // Insert children from household_children
            $childrenStmt = $this->db->prepare("
                SELECT * FROM household_children WHERE household_id = ? ORDER BY dob ASC
            ");
            $childrenStmt->execute([$household['household_id']]);
            $children = $childrenStmt->fetchAll(\PDO::FETCH_ASSOC);
            $sortOrder = 10;
            foreach ($children as $child) {
                // Check if this child already exists in family_members (prevent duplicates)
                $dupCheck = $this->db->prepare("
                    SELECT member_id FROM family_members
                    WHERE family_id = ?
                      AND role = 'Child'
                      AND LOWER(TRIM(COALESCE(first_name,''))) = LOWER(TRIM(COALESCE(?,'')))
                      AND LOWER(TRIM(COALESCE(last_name,''))) = LOWER(TRIM(COALESCE(?,'')))
                      AND dob = ?
                    LIMIT 1
                ");
                $dupCheck->execute([
                    $familyId,
                    $child['first_name'] ?? null,
                    $child['last_name'] ?? null,
                    $child['dob'] ?? null
                ]);
                
                // Skip if child already exists
                if ($dupCheck->fetchColumn()) {
                    continue;
                }
                
                // Insert child
                $this->db->prepare("
                    INSERT INTO family_members (family_id, role, first_name, middle_name, last_name, suffix, sex, dob, sort_order)
                    VALUES (?, 'Child', ?, ?, ?, ?, ?, ?, ?)
                ")->execute([
                    $familyId,
                    $child['first_name'] ?? null,
                    $child['middle_name'] ?? null,
                    $child['last_name'] ?? null,
                    $child['suffix'] ?? null,
                    $child['sex'] ?? null,
                    $child['dob'] ?? null,
                    $sortOrder++,
                ]);
            }

        } catch (\Exception $e) {
            error_log('FamilyWizardController::autoCreateFamilyProfileIfNeeded error: ' . $e->getMessage());
        }
    }

    // ── Sync resident submission to BNS family_profiles ───────────────────────
    /**
     * When a resident submits their profile via the Mother Wizard, check if a
     * BNS-encoded family_profiles record already has source_user_id = $userId.
     * If found, update the Head (or Wife) family_members row with the resident's
     * confirmed name data so the BNS record stays accurate and no duplicate is created.
     *
     * This is the anti-duplicate bridge between the two data models:
     *   - households (Mother Wizard / resident-side)
     *   - family_profiles + family_members (BNS-side)
     */
    private function syncToBnsFamilyProfile(int $userId, array $data): void {
        try {
            // Find a BNS family_profiles record linked to this resident
            $stmt = $this->db->prepare("
                SELECT fp.family_id, fm.member_id, fm.role
                FROM family_profiles fp
                LEFT JOIN family_members fm ON fm.family_id = fp.family_id
                    AND fm.role IN ('Head', 'Wife')
                WHERE fp.source_user_id = :uid
                ORDER BY fm.role ASC
                LIMIT 1
            ");
            $stmt->execute([':uid' => $userId]);
            $linked = $stmt->fetch(\PDO::FETCH_ASSOC);

            // If user doesn't have a family_profiles record, check if their spouse does
            if (!$linked) {
                // Check if user has a verified spouse
                $spouseLinkStmt = $this->db->prepare("
                    SELECT CASE 
                        WHEN fl.user_id_a = :uid THEN fl.user_id_b 
                        ELSE fl.user_id_a 
                    END AS spouse_id
                    FROM family_links fl
                    WHERE (fl.user_id_a = :uid2 OR fl.user_id_b = :uid3)
                      AND fl.relationship_type = 'Husband-Wife'
                      AND fl.verification_status = 'Verified'
                    LIMIT 1
                ");
                $spouseLinkStmt->execute([':uid' => $userId, ':uid2' => $userId, ':uid3' => $userId]);
                $spouseId = $spouseLinkStmt->fetchColumn();
                
                if ($spouseId) {
                    // Check if spouse has a family_profiles record
                    $spouseFamilyStmt = $this->db->prepare("
                        SELECT fp.family_id, fm.member_id, fm.role
                        FROM family_profiles fp
                        LEFT JOIN family_members fm ON fm.family_id = fp.family_id
                            AND fm.role IN ('Head', 'Wife')
                        WHERE fp.source_user_id = :spouse_id
                        ORDER BY fm.role ASC
                        LIMIT 1
                    ");
                    $spouseFamilyStmt->execute([':spouse_id' => $spouseId]);
                    $linked = $spouseFamilyStmt->fetch(\PDO::FETCH_ASSOC);
                }
            }

            if (!$linked) return; // No linked BNS record — nothing to sync

            $firstName  = trim($data['first_name']  ?? '');
            $lastName   = trim($data['last_name']    ?? '');
            $middleName = trim($data['middle_name']  ?? '') ?: null;
            $occupation = trim($data['occupation']   ?? '') ?: null;
            $educId     = $data['educ_level_id']     ?? null ?: null;
            
            // Determine which role this user should update based on their gender
            $userGender = $data['gender'] ?? 'Female';
            $targetRole = ($userGender === 'Male') ? 'Head' : 'Wife';
            
            // Find the family_members row for this role
            $memberStmt = $this->db->prepare("
                SELECT member_id FROM family_members
                WHERE family_id = :family_id AND role = :role
                LIMIT 1
            ");
            $memberStmt->execute([':family_id' => $linked['family_id'], ':role' => $targetRole]);
            $memberId = $memberStmt->fetchColumn();

            if ($memberId) {
                // Update the existing family_members row
                $this->db->prepare("
                    UPDATE family_members
                    SET first_name    = :first_name,
                        last_name     = :last_name,
                        middle_name   = :middle_name,
                        occupation    = :occupation,
                        educ_level_id = :educ_id
                    WHERE member_id = :member_id
                ")->execute([
                    ':first_name'  => $firstName,
                    ':last_name'   => $lastName,
                    ':middle_name' => $middleName,
                    ':occupation'  => $occupation,
                    ':educ_id'     => $educId,
                    ':member_id'   => $memberId,
                ]);
            } else {
                // No family_members row yet — insert one with the appropriate role
                $this->db->prepare("
                    INSERT INTO family_members
                        (family_id, role, first_name, last_name, middle_name, occupation, educ_level_id, sort_order)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ")->execute([
                    $linked['family_id'],
                    $targetRole,
                    $firstName, $lastName, $middleName, $occupation, $educId,
                    ($targetRole === 'Head' ? 0 : 1), // sort_order: Head=0, Wife=1
                ]);
            }

            // Also mark the family_profiles record as having a confirmed resident link
            // Only update if source_user_id is NULL (not already set)
            $this->db->prepare("
                UPDATE family_profiles
                SET source_user_id = :uid
                WHERE family_id = :fid AND source_user_id IS NULL
            ")->execute([':uid' => $userId, ':fid' => $linked['family_id']]);

        } catch (\Exception $e) {
            // Non-fatal — log but don't break the submission
            error_log('FamilyWizardController::syncToBnsFamilyProfile error: ' . $e->getMessage());
        }
    }

    private function getLookups(): array {
        $get = fn(string $sql) => $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return [
            'water_sources'  => $get("SELECT id, code, label FROM ref_water_sources ORDER BY id"),
            'toilet_types'   => $get("SELECT id, code, label FROM ref_toilet_types ORDER BY id"),
            'dwelling_types' => $get("SELECT id, label FROM ref_dwelling_types ORDER BY id"),
            'educ_levels'    => $get("SELECT id, label FROM ref_educ_levels ORDER BY id"),
            'fp_methods'     => $get("SELECT id, label FROM ref_fp_methods ORDER BY id"),
        ];
    }
}
