<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/HouseholdModel.php';
require_once __DIR__ . '/../models/FamilyLinkModel.php';
require_once __DIR__ . '/../models/HealthProfileModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class BnsValidationController {

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

    private function requireBNS(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BNS Staff') {
            header('Location: index.php?action=login'); exit;
        }
    }

    // ── List Pending ──────────────────────────────────────────────────────────

    public function listPending(): void {
        $this->requireBNS();
        $bnsId  = $_SESSION['user_id'];
        $search = trim($_GET['search'] ?? '');

        $where  = "WHERE up.profile_status IN ('Submitted','Returned') AND (up.assigned_bns_id = :bns_id OR up.assigned_bns_id IS NULL)";
        $params = [':bns_id' => $bnsId];

        if ($search !== '') {
            $like = '%' . $search . '%';
            $where .= " AND (
                CONCAT(u.first_name,' ',u.last_name) LIKE :s1
                OR h.household_code LIKE :s2
                OR h.purok LIKE :s3
            )";
            $params[':s1'] = $like;
            $params[':s2'] = $like;
            $params[':s3'] = $like;
        }

        $stmt = $this->db->prepare("
            SELECT up.profile_id, up.profile_status, up.submitted_at,
                   u.user_id, u.first_name, u.last_name, u.email,
                   h.household_code, h.purok
            FROM user_profiles up
            INNER JOIN users u ON u.user_id = up.user_id
            LEFT JOIN household_members hm ON hm.user_id = u.user_id
            LEFT JOIN households h ON h.household_id = hm.household_id
            $where
            ORDER BY up.submitted_at ASC
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Summary counts
        $countStmt = $this->db->prepare("
            SELECT
                SUM(CASE WHEN profile_status IN ('Submitted','Returned') THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN profile_status = 'Validated'               THEN 1 ELSE 0 END) AS validated,
                COUNT(*) AS total
            FROM user_profiles
            WHERE assigned_bns_id = :bns_id
        ");
        $countStmt->execute([':bns_id' => $bnsId]);
        $counts = $countStmt->fetch(PDO::FETCH_ASSOC);

        // Recently auto-validated (BNS-registered residents) — last 30 days
        $recentStmt = $this->db->prepare("
            SELECT up.profile_id, up.profile_status, up.validated_at,
                   u.user_id, u.first_name, u.last_name,
                   h.household_code, h.purok
            FROM user_profiles up
            INNER JOIN users u ON u.user_id = up.user_id
            LEFT JOIN household_members hm ON hm.user_id = u.user_id
            LEFT JOIN households h ON h.household_id = hm.household_id
            WHERE up.assigned_bns_id = :bns_id
              AND up.profile_status = 'Validated'
              AND up.validated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ORDER BY up.validated_at DESC
            LIMIT 10
        ");
        $recentStmt->execute([':bns_id' => $bnsId]);
        $recentValidated = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

        $pageTitle = 'Profile Validation';
        $activeNav = 'validation';
        include __DIR__ . '/../views/bns/bns_validation_list.php';
    }

    // ── Show Detail ───────────────────────────────────────────────────────────

    public function showDetail(): void {
        $this->requireBNS();
        $bnsId     = $_SESSION['user_id'];
        $profileId = (int) ($_GET['profile_id'] ?? 0);

        if (!$profileId) {
            header('Location: index.php?action=bnsValidationList'); exit;
        }

        $stmt = $this->db->prepare("
            SELECT up.*, u.first_name, u.last_name, u.email,
                   u.gender, u.civil_status, u.birthdate, u.contact
            FROM user_profiles up
            INNER JOIN users u ON u.user_id = up.user_id
            WHERE up.profile_id = :pid
              AND (up.assigned_bns_id = :bns_id OR up.assigned_bns_id IS NULL)
        ");
        $stmt->execute([':pid' => $profileId, ':bns_id' => $bnsId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$profile) {
            header('Location: index.php?action=bnsValidationList'); exit;
        }

        $healthProfile = $this->healthModel->getByUserId($profile['user_id']);
        if (!$healthProfile) {
            $healthProfile = [];
        }
        
        // Load household with spouse education label
        $household = $this->householdModel->getByUserId($profile['user_id']);
        
        // If no household exists, check if there's a BNS family_profiles record
        if (!$household) {
            $bnsStmt = $this->db->prepare("
                SELECT fp.hh_number as household_code, fp.purok,
                       fp.water_source_id, fp.toilet_type_id, fp.uses_iodized_salt,
                       fp.uses_ifr, fp.dwelling_type_id, fp.is_mother_prog, fp.fp_method_id,
                       fm_spouse.first_name as spouse_first_name,
                       fm_spouse.middle_name as spouse_middle_name,
                       fm_spouse.last_name as spouse_last_name,
                       fm_spouse.suffix as spouse_suffix,
                       fm_spouse.occupation as spouse_occupation,
                       fm_spouse.educ_level_id as spouse_educ_level_id
                FROM family_profiles fp
                LEFT JOIN family_members fm_spouse 
                    ON fm_spouse.family_id = fp.family_id 
                    AND fm_spouse.role = 'Wife'
                WHERE fp.source_user_id = :uid
                LIMIT 1
            ");
            $bnsStmt->execute([':uid' => $profile['user_id']]);
            $bnsData = $bnsStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($bnsData) {
                $household = $bnsData;
                // Load spouse education label
                if ($household['spouse_educ_level_id']) {
                    $spouseEducStmt = $this->db->prepare("
                        SELECT label FROM ref_educ_levels WHERE id = :educ_id
                    ");
                    $spouseEducStmt->execute([':educ_id' => $household['spouse_educ_level_id']]);
                    $household['spouse_educ_label'] = $spouseEducStmt->fetchColumn() ?: null;
                }
            }
        }
        
        if ($household && !isset($household['spouse_educ_label'])) {
            // Add spouse education label
            $spouseEducStmt = $this->db->prepare("
                SELECT rel.label 
                FROM ref_educ_levels rel 
                WHERE rel.id = :educ_id
            ");
            $spouseEducStmt->execute([':educ_id' => $household['spouse_educ_level_id'] ?? 0]);
            $household['spouse_educ_label'] = $spouseEducStmt->fetchColumn() ?: null;
        }

        // Fallback maternal status from family_profiles when health profile is incomplete.
        if (
            empty(trim((string)($healthProfile['pregnancy_status'] ?? ''))) ||
            empty(trim((string)($healthProfile['breastfeeding_status'] ?? '')))
        ) {
            $maternalStmt = $this->db->prepare("
                SELECT
                    NULLIF(TRIM(wife_pregnancy_status), '') AS fp_pregnancy_status,
                    NULLIF(TRIM(wife_breastfeeding_status), '') AS fp_breastfeeding_status
                FROM family_profiles
                WHERE source_user_id = :uid
                LIMIT 1
            ");
            $maternalStmt->execute([':uid' => $profile['user_id']]);
            $maternal = $maternalStmt->fetch(PDO::FETCH_ASSOC) ?: [];

            if (empty(trim((string)($healthProfile['pregnancy_status'] ?? ''))) && !empty($maternal['fp_pregnancy_status'])) {
                $healthProfile['pregnancy_status'] = $maternal['fp_pregnancy_status'];
            }
            if (empty(trim((string)($healthProfile['breastfeeding_status'] ?? ''))) && !empty($maternal['fp_breastfeeding_status'])) {
                $healthProfile['breastfeeding_status'] = $maternal['fp_breastfeeding_status'];
            }
        }

        // Fallback family planning method from family_profiles if missing in households.
        if ($household && empty($household['fp_method_id'])) {
            $fpFallbackStmt = $this->db->prepare("
                SELECT fp_method_id
                FROM family_profiles
                WHERE source_user_id = :uid
                LIMIT 1
            ");
            $fpFallbackStmt->execute([':uid' => $profile['user_id']]);
            $household['fp_method_id'] = $fpFallbackStmt->fetchColumn() ?: null;
        }

        if ($household && !empty($household['fp_method_id'])) {
            // Add family planning method label for readable display
            $fpStmt = $this->db->prepare("
                SELECT label
                FROM ref_fp_methods
                WHERE id = :fp_id
            ");
            $fpStmt->execute([':fp_id' => $household['fp_method_id']]);
            $household['fp_method_label'] = $fpStmt->fetchColumn() ?: null;
        }
        
        $familyLinks   = $this->familyLinkModel->getLinksForUser($profile['user_id']);
        $pendingLinks  = $this->familyLinkModel->getPendingLinksForProfile($profileId);
        $householdMembers = ($household && !empty($household['household_id']))
            ? $this->householdModel->getMembers($household['household_id'])
            : [];

        $pageTitle = 'Profile Detail';
        $activeNav = 'validation';
        include __DIR__ . '/../views/bns/bns_validation_detail.php';
    }

    // ── Validate Profile ──────────────────────────────────────────────────────

    public function validateProfile(): void {
        $this->requireBNS();
        $bnsId     = $_SESSION['user_id'];
        $profileId = (int) ($_POST['profile_id'] ?? 0);

        if (!$profileId) {
            header('Location: index.php?action=bnsValidationList'); exit;
        }

        // Check for pending links
        $pendingCount = $this->familyLinkModel->getPendingLinksForProfile($profileId);
        $confirmed    = !empty($_POST['confirm_pending']);

        if ($pendingCount > 0 && !$confirmed) {
            $_SESSION['flash_warning'] = 'This profile has ' . $pendingCount . ' pending family link(s). Check the confirmation box to proceed.';
            header("Location: index.php?action=bnsValidationDetail&profile_id=$profileId"); exit;
        }

        $this->db->beginTransaction();
        try {
            // Optimistic lock: only update if still Submitted
            $stmt = $this->db->prepare("
                UPDATE user_profiles
                SET profile_status = 'Validated',
                    validated_by   = :bns_id,
                    validated_at   = NOW()
                WHERE profile_id = :pid
                  AND profile_status = 'Submitted'
            ");
            $stmt->execute([':bns_id' => $bnsId, ':pid' => $profileId]);

            if ($stmt->rowCount() === 0) {
                $this->db->rollBack();
                $_SESSION['flash_error'] = 'Profile could not be validated. It may have already been validated.';
                header("Location: index.php?action=bnsValidationDetail&profile_id=$profileId"); exit;
            }

            // Get Mother's user_id
            $motherStmt = $this->db->prepare("SELECT user_id FROM user_profiles WHERE profile_id = :pid");
            $motherStmt->execute([':pid' => $profileId]);
            $motherId = (int) $motherStmt->fetchColumn();

            // ── Auto-create family_profiles record from mother's validated data ──
            $this->createFamilyProfileFromValidated($motherId, $bnsId);

            // Notify Mother
            $this->notifModel->create(
                $motherId,
                'profile_validated',
                $profileId,
                'Your family profile has been validated by your BNS.'
            );

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['flash_error'] = 'Validation failed: ' . $e->getMessage();
            header("Location: index.php?action=bnsValidationDetail&profile_id=$profileId"); exit;
        }

        $_SESSION['flash'] = 'Profile validated successfully.';
        header('Location: index.php?action=bnsValidationList'); exit;
    }

    // ── Return Profile for Correction ─────────────────────────────────────────

    public function returnProfile(): void {
        $this->requireBNS();
        $bnsId     = $_SESSION['user_id'];
        $profileId = (int) ($_POST['profile_id'] ?? 0);
        $reason    = trim($_POST['return_reason'] ?? '');

        if (!$profileId || !$reason) {
            $_SESSION['flash_error'] = 'A reason is required to return the profile.';
            header("Location: index.php?action=bnsValidationDetail&profile_id=$profileId"); exit;
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                UPDATE user_profiles
                SET profile_status = 'Returned',
                    return_reason  = :reason
                WHERE profile_id = :pid
                  AND profile_status = 'Submitted'
            ");
            $stmt->execute([':reason' => $reason, ':pid' => $profileId]);

            if ($stmt->rowCount() === 0) {
                $this->db->rollBack();
                $_SESSION['flash_error'] = 'Could not return profile. It may have already been processed.';
                header("Location: index.php?action=bnsValidationDetail&profile_id=$profileId"); exit;
            }

            // Get mother's user_id
            $motherStmt = $this->db->prepare("SELECT user_id FROM user_profiles WHERE profile_id = :pid");
            $motherStmt->execute([':pid' => $profileId]);
            $motherId = (int) $motherStmt->fetchColumn();

            // Notify mother
            $this->notifModel->create(
                $motherId,
                'profile_returned',
                $profileId,
                'Your family profile was returned for correction by your BNS. Reason: ' . $reason
            );

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['flash_error'] = 'Return failed: ' . $e->getMessage();
            header("Location: index.php?action=bnsValidationDetail&profile_id=$profileId"); exit;
        }

        $_SESSION['flash'] = 'Profile returned to mother for correction.';
        header('Location: index.php?action=bnsValidationList'); exit;
    }

    // ── Create family_profiles from validated mother data ─────────────────────

    private function createFamilyProfileFromValidated(int $motherId, int $bnsId): void {
        // Skip if already exists for this mother
        $check = $this->db->prepare("SELECT family_id FROM family_profiles WHERE source_user_id = ?");
        $check->execute([$motherId]);
        if ($check->fetchColumn()) return;

        // Also skip if this user's SPOUSE already has a family_profiles record
        // (e.g. wife submits after husband was already registered by BNS)
        $spouseCheckStmt = $this->db->prepare("
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
        $spouseCheckStmt->execute([':uid' => $motherId, ':uid2' => $motherId, ':uid3' => $motherId]);
        if ($spouseCheckStmt->fetchColumn()) return;

        // Load household
        $household = $this->householdModel->getByUserId($motherId);

        // Load health profile
        $hp = $this->healthModel->getByUserId($motherId);

        // Load user
        $uStmt = $this->db->prepare("
            SELECT u.first_name, u.middle_name, u.last_name, u.gender, u.civil_status
            FROM users u WHERE u.user_id = ?
        ");
        $uStmt->execute([$motherId]);
        $user = $uStmt->fetch(PDO::FETCH_ASSOC);

        // Load spouse via family_links OR from household typed spouse fields
        $spouseStmt = $this->db->prepare("
            SELECT u2.first_name, u2.middle_name, u2.last_name, u2.gender
            FROM family_links fl
            JOIN users u2 ON u2.user_id = CASE
                WHEN fl.user_id_a = :uid THEN fl.user_id_b
                ELSE fl.user_id_a
            END
            WHERE (fl.user_id_a = :uid2 OR fl.user_id_b = :uid3)
              AND fl.relationship_type = 'Husband-Wife'
              AND fl.verification_status = 'Verified'
            LIMIT 1
        ");
        $spouseStmt->execute([':uid' => $motherId, ':uid2' => $motherId, ':uid3' => $motherId]);
        $spouse = $spouseStmt->fetch(PDO::FETCH_ASSOC);

        // Determine head and wife names based on income/occupation rule:
        // Head = whoever has a job/income. If both have income, higher income wins.
        // Fallback: male member is Head if neither has income.
        $userFirstName  = $user['first_name']  ?? '';
        $userMiddleName = $user['middle_name'] ?? '';
        $userLastName   = $user['last_name']   ?? '';
        $userGender     = $user['gender']      ?? 'Female';

        // Spouse name: prefer linked account, fall back to typed spouse fields in household
        if ($spouse) {
            $spouseFirstName  = $spouse['first_name']  ?? '';
            $spouseMiddleName = $spouse['middle_name'] ?? '';
            $spouseLastName   = $spouse['last_name']   ?? '';
        } else {
            $spouseFirstName  = $household['spouse_first_name']  ?? '';
            $spouseMiddleName = $household['spouse_middle_name'] ?? '';
            $spouseLastName   = $household['spouse_last_name']   ?? '';
        }
        
        //   1. Higher income → Head
        //   2. Tie (equal or both zero) → use hof_user_id the mother designated
        //   3. No designation → default to male member
        $userIncome   = (float) ($hp['monthly_income']              ?? 0);
        $spouseIncome = (float) ($household['spouse_monthly_income'] ?? 0);
        $hofUserId    = (int)   ($household['hof_user_id']           ?? 0);

        if ($userIncome > $spouseIncome) {
            // Registered user earns more → user is Head
            $userIsHof = true;
        } elseif ($spouseIncome > $userIncome) {
            // Spouse earns more → spouse is Head
            $userIsHof = false;
        } else {
            // Tie or both zero → respect the manual designation
            // hof_user_id == motherId means mother is Head
            // hof_user_id == 0 or 'spouse' means unregistered spouse was designated as Head
            // anything else (another user_id) means that user is Head
            if ($hofUserId === 0 || $household['hof_user_id'] === 'spouse') {
                $userIsHof = false; // unregistered spouse is HOF
            } else {
                $userIsHof = ($hofUserId === (int) $motherId);
            }
        }

        if ($userIsHof) {
            $headFirstName   = $userFirstName;
            $headMiddleName  = $userMiddleName;
            $headLastName    = $userLastName;
            $headSex         = $userGender === 'Male' ? 'M' : 'F';
            $headOccupation  = $hp['occupation']    ?? null;
            $headEducLevelId = $hp['educ_level_id'] ?? null;
            
            $wifeFirstName   = $spouseFirstName;
            $wifeMiddleName  = $spouseMiddleName;
            $wifeLastName    = $spouseLastName;
            $wifeSex         = $userGender === 'Male' ? 'F' : 'M';
            $wifeOccupation  = $household['spouse_occupation'] ?? null;
            $wifeEducLevelId = $household['spouse_educ_level_id'] ?? null;
        } else {
            $headFirstName   = $spouseFirstName;
            $headMiddleName  = $spouseMiddleName;
            $headLastName    = $spouseLastName;
            $headSex         = $userGender === 'Male' ? 'F' : 'M';
            $headOccupation  = $household['spouse_occupation'] ?? null;
            $headEducLevelId = $household['spouse_educ_level_id'] ?? null;
            
            $wifeFirstName   = $userFirstName;
            $wifeMiddleName  = $userMiddleName;
            $wifeLastName    = $userLastName;
            $wifeSex         = $userGender === 'Male' ? 'M' : 'F';
            $wifeOccupation  = $hp['occupation'] ?? null;
            $wifeEducLevelId = $hp['educ_level_id'] ?? null;
        }

        // Auto-generate next HH # for this BNS (sequential: 001, 002, ...)
        $hhStmt = $this->db->prepare("
            SELECT COUNT(*) FROM family_profiles WHERE bns_id = ?
        ");
        $hhStmt->execute([$bnsId]);
        $hhCount  = (int) $hhStmt->fetchColumn();
        $hhNumber = str_pad($hhCount + 1, 3, '0', STR_PAD_LEFT);

        // Insert family_profiles record
        $stmt = $this->db->prepare("
            INSERT INTO family_profiles (
                bns_id, source_user_id,
                hh_number, purok, num_hh_members,
                children_0_5mos, children_6_23mos, children_24_59mos, children_60plus,
                toilet_type_id, water_source_id, uses_iodized_salt, uses_ifr,
                dwelling_type_id, total_income, is_mother_prog, fp_method_id
            ) VALUES (
                :bns_id, :source_user_id,
                :hh_number, :purok, :num_hh_members,
                :c0_5, :c6_23, :c24_59, :c60plus,
                :toilet, :water, :iodized, :ifr,
                :dwelling, :income, :mother_prog, :fp_method
            )
        ");
        $stmt->execute([
            ':bns_id'         => $bnsId,
            ':source_user_id' => $motherId,
            ':hh_number'      => $hhNumber,
            ':purok'          => $household['purok']             ?? null,
            ':num_hh_members' => $household['num_hh_members']    ?? null,
            ':c0_5'           => $household['children_0_5mos']   ?? null,
            ':c6_23'          => $household['children_6_23mos']  ?? null,
            ':c24_59'         => $household['children_24_59mos'] ?? null,
            ':c60plus'        => $household['children_60plus']   ?? null,
            ':toilet'         => $household['toilet_type_id']    ?? null,
            ':water'          => $household['water_source_id']   ?? null,
            ':iodized'        => $household['uses_iodized_salt'] ?? 0,
            ':ifr'            => $household['uses_ifr']          ?? 0,
            ':dwelling'       => $household['dwelling_type_id']  ?? null,
            ':income'         => $hp['monthly_income']           ?? null,
            ':mother_prog'    => $household['is_mother_prog']    ?? 0,
            ':fp_method'      => $household['fp_method_id']      ?? null,
        ]);
        $familyId = (int) $this->db->lastInsertId();

        // Insert Head member — whoever the family designated as HOF
        if ($headFirstName || $headLastName) {
            $this->db->prepare("
                INSERT INTO family_members (family_id, role, first_name, middle_name, last_name, sex, occupation, educ_level_id, sort_order)
                VALUES (?, 'Head', ?, ?, ?, ?, ?, ?, 0)
            ")->execute([$familyId, $headFirstName, $headMiddleName, $headLastName, $headSex, $headOccupation, $headEducLevelId]);
        }

        // Insert Wife/Spouse member
        if ($wifeFirstName || $wifeLastName) {
            $this->db->prepare("
                INSERT INTO family_members (family_id, role, first_name, middle_name, last_name, sex, occupation, educ_level_id, sort_order)
                VALUES (?, 'Wife', ?, ?, ?, ?, ?, ?, 1)
            ")->execute([$familyId, $wifeFirstName, $wifeMiddleName, $wifeLastName, $wifeSex, $wifeOccupation, $wifeEducLevelId]);
        }

        // Copy children from household_children
        if ($household) {
            $cStmt = $this->db->prepare("
                SELECT last_name, first_name, middle_name, suffix, sex, dob FROM household_children
                WHERE household_id = ? ORDER BY dob ASC
            ");
            $cStmt->execute([$household['household_id']]);
            $children = $cStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($children as $i => $child) {
                // Check if this child already exists in family_members (prevent duplicates)
                $dupCheck = $this->db->prepare("
                    SELECT member_id FROM family_members
                    WHERE family_id = ?
                      AND role = 'Child'
                      AND LOWER(TRIM(first_name)) = LOWER(TRIM(?))
                      AND LOWER(TRIM(last_name)) = LOWER(TRIM(?))
                      AND dob = ?
                    LIMIT 1
                ");
                $dupCheck->execute([
                    $familyId,
                    $child['first_name'],
                    $child['last_name'],
                    $child['dob']
                ]);
                
                // Skip if child already exists
                if ($dupCheck->fetchColumn()) {
                    continue;
                }
                
                // Insert child
                $this->db->prepare("
                    INSERT INTO family_members (family_id, role, last_name, first_name, middle_name, suffix, sex, dob, sort_order)
                    VALUES (?, 'Child', ?, ?, ?, ?, ?, ?, ?)
                ")->execute([
                    $familyId, 
                    $child['last_name'], 
                    $child['first_name'], 
                    $child['middle_name'], 
                    $child['suffix'], 
                    $child['sex'], 
                    $child['dob'], 
                    $i + 10
                ]);
            }
        }
    }

    // ── Override HOF ──────────────────────────────────────────────────────────

    public function overrideHof(): void {
        $this->requireBNS();
        $profileId   = (int) ($_POST['profile_id']   ?? 0);
        $hofUserId   = (int) ($_POST['hof_user_id']  ?? 0);
        $householdId = (int) ($_POST['household_id'] ?? 0);

        if ($householdId && $hofUserId) {
            $this->householdModel->updateHof($householdId, $hofUserId);
            $_SESSION['flash'] = 'Head of Family updated.';
        }

        header("Location: index.php?action=bnsValidationDetail&profile_id=$profileId"); exit;
    }
}
