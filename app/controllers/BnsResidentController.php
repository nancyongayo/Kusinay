<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Security.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../../core/Mailer.php';

class BnsResidentController {

    private PDO       $db;
    private UserModel $userModel;
    private Mailer    $mailer;

    public function __construct() {
        $this->db        = getDBConnection();
        $this->userModel = new UserModel();
        $this->mailer    = new Mailer();
    }

    // ── Guards ────────────────────────────────────────────────────────────────

    private function requireBNS(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BNS Staff') {
            header('Location: index.php?action=login'); exit;
        }
    }

    // ── Password generator ────────────────────────────────────────────────────

    /**
     * Generates a cryptographically random temporary password.
     * Guarantees: length >= 12, at least one uppercase, lowercase, digit, special char.
     *
     * Feature: bns-resident-registration, Property 1: Temporary password satisfies policy
     */
    private function generateTemporaryPassword(): string {
        $upper   = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower   = 'abcdefghjkmnpqrstuvwxyz';
        $digits  = '23456789';
        $special = '!@#$%^&*';
        $all     = $upper . $lower . $digits . $special;

        // Guarantee at least one of each character class
        $password  = $upper[random_int(0, strlen($upper) - 1)];
        $password .= $lower[random_int(0, strlen($lower) - 1)];
        $password .= $digits[random_int(0, strlen($digits) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        // Fill remaining 8 characters from the full set (total = 12)
        for ($i = 0; $i < 8; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        // Shuffle to avoid predictable positions
        return str_shuffle($password);
    }

    // ── Name-match helper ─────────────────────────────────────────────────────

    /**
     * Finds family_members rows (Head or Wife) whose name case-insensitively matches
     * the given first/last name, scoped to the BNS's own family_profiles.
     *
     * Feature: bns-resident-registration, Property 11: Name-match check is case-insensitive and BNS-scoped
     */
    private function findNameMatches(string $firstName, string $lastName, int $bnsId): array {
        if (!$firstName && !$lastName) return [];

        $stmt = $this->db->prepare("
            SELECT fm.member_id, fm.role, fm.first_name, fm.last_name,
                   fp.family_id, fp.hh_number, fp.purok
            FROM family_members fm
            INNER JOIN family_profiles fp ON fp.family_id = fm.family_id
            WHERE fp.bns_id = :bns_id
              AND fm.role IN ('Head', 'Wife')
              AND LOWER(fm.first_name) = LOWER(:first_name)
              AND LOWER(fm.last_name)  = LOWER(:last_name)
        ");
        $stmt->execute([
            ':bns_id'     => $bnsId,
            ':first_name' => $firstName,
            ':last_name'  => $lastName,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Audit log ─────────────────────────────────────────────────────────────

    private function logActivity(int $userId, string $actionType, string $desc): void {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $this->db->prepare("
                INSERT INTO system_logs (user_id, action_type, description, ip_address)
                VALUES (?, ?, ?, ?)
            ")->execute([$userId, $actionType, $desc, $ip]);
        } catch (\Exception $e) {
            error_log('BnsResidentController log error: ' . $e->getMessage());
        }
    }

    // ── Register Resident ─────────────────────────────────────────────────────

    /**
     * GET:  Render the registration form.
     * POST: Validate input, check for duplicates, create account, send credentials.
     */
    public function registerResident(): void {
        $this->requireBNS();
        $bnsId = (int) $_SESSION['user_id'];

        // ── GET ───────────────────────────────────────────────────────────────
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $matches   = [];
            $residents = $this->userModel->getResidentsByBns($bnsId);
            include __DIR__ . '/../views/bns/bns_register_resident.php';
            return;
        }

        // ── POST ──────────────────────────────────────────────────────────────
        Security::verifyCsrf();

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName  = trim($_POST['last_name']  ?? '');
        $email     = strtolower(trim($_POST['email'] ?? ''));
        $address   = trim($_POST['address'] ?? '');

        // ── AJAX name-check (returns JSON) ────────────────────────────────────
        if (!empty($_POST['ajax_name_check'])) {
            header('Content-Type: application/json');
            echo json_encode($this->findNameMatches($firstName, $lastName, $bnsId));
            exit;
        }

        // ── Validation ────────────────────────────────────────────────────────
        $errors = [];
        if (!$firstName)                                    $errors[] = 'First name is required.';
        if (!$lastName)                                     $errors[] = 'Last name is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))     $errors[] = 'A valid email address is required.';
        if ($this->userModel->emailExists($email))          $errors[] = 'This email address is already registered.';

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = compact('firstName', 'lastName', 'email', 'address');
            header('Location: index.php?action=registerResident'); exit;
        }

        // ── Name-match suggestion (non-AJAX path) ─────────────────────────────
        $confirmMatchFamilyId = (int) ($_POST['confirm_match_family_id'] ?? 0);
        $matchDismissed       = !empty($_POST['match_dismissed']);

        if (!$confirmMatchFamilyId && !$matchDismissed) {
            $matches = $this->findNameMatches($firstName, $lastName, $bnsId);
            if ($matches) {
                // Re-render form with suggestions
                $_SESSION['old'] = compact('firstName', 'lastName', 'email', 'address');
                $residents = $this->userModel->getResidentsByBns($bnsId);
                include __DIR__ . '/../views/bns/bns_register_resident.php';
                return;
            }
        }

        // ── Create account ────────────────────────────────────────────────────
        try {
            $result    = $this->userModel->registerByBns([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $email,
            ], $bnsId);

            $newUserId  = $result['user_id'];
            $setupToken = $result['setup_token'];

            // Link to existing family profile if BNS confirmed a match
            if ($confirmMatchFamilyId) {
                $this->db->prepare("
                    UPDATE family_profiles
                    SET source_user_id = ?
                    WHERE family_id = ? AND bns_id = ?
                ")->execute([$newUserId, $confirmMatchFamilyId, $bnsId]);
            }

            // Audit log
            $this->logActivity($bnsId, 'BNS_RESIDENT_CREATED',
                "BNS registered resident: {$email} (user_id={$newUserId})");

        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Registration failed. Please try again.'];
            error_log('BnsResidentController::registerResident error: ' . $e->getMessage());
            header('Location: index.php?action=registerResident'); exit;
        }

        // ── Send invite email (magic link — no password) ──────────────────────
        $fullName = $firstName . ' ' . $lastName;
        $sent     = $this->mailer->sendResidentInviteEmail($email, $fullName, $setupToken);

        if ($sent) {
            $_SESSION['flash'] = "Account created for {$fullName}. A setup link has been sent to {$email}.";
        } else {
            $setupLink = 'http://localhost/KusiNay(Capstone)/index.php?action=setupAccount&token=' . urlencode($setupToken);
            $_SESSION['flash'] = "Account created for {$fullName}. Email could not be sent. "
                . "Share this setup link manually: <a href='" . htmlspecialchars($setupLink) . "' style='color:var(--kn-orange)'>"
                . htmlspecialchars($setupLink) . "</a>";
        }

        header('Location: index.php?action=registerResident'); exit;
    }

    // ── List Residents ────────────────────────────────────────────────────────

    /**
     * GET: Paginated list of all residents registered by this BNS.
     */
    public function listResidents(): void {
        $this->requireBNS();
        // Merged into registerResident page
        header('Location: index.php?action=registerResident'); exit;
    }

    // ── Resend Credentials ────────────────────────────────────────────────────

    /**
     * POST: Regenerate a temporary password and resend credentials to a resident.
     */
    public function resendCredentials(): void {
        $this->requireBNS();
        Security::verifyCsrf();

        $bnsId     = (int) $_SESSION['user_id'];
        $residentId = (int) ($_POST['resident_id'] ?? 0);

        if (!$residentId) {
            $_SESSION['errors'] = ['Invalid resident.'];
            header('Location: index.php?action=listResidents'); exit;
        }

        // Verify the resident belongs to this BNS
        $stmt = $this->db->prepare("
            SELECT u.user_id, u.first_name, u.last_name, u.email
            FROM users u
            INNER JOIN user_profiles p ON p.user_id = u.user_id
            WHERE u.user_id = ? AND p.assigned_bns_id = ?
        ");
        $stmt->execute([$residentId, $bnsId]);
        $resident = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resident) {
            $_SESSION['errors'] = ['Resident not found or not assigned to you.'];
            header('Location: index.php?action=listResidents'); exit;
        }

        $newToken = $this->userModel->regenerateSetupToken($residentId);

        // Audit log
        $this->logActivity($bnsId, 'BNS_CREDENTIAL_RESENT',
            "BNS resent invite link for resident user_id={$residentId}");

        $fullName = $resident['first_name'] . ' ' . $resident['last_name'];
        $sent     = $this->mailer->sendResidentInviteEmail($resident['email'], $fullName, $newToken);

        if ($sent) {
            $_SESSION['flash'] = "New setup link sent to {$resident['email']}.";
        } else {
            $setupLink = 'http://localhost/KusiNay(Capstone)/index.php?action=setupAccount&token=' . urlencode($newToken);
            $_SESSION['flash'] = "Email could not be sent. Share this link manually: <a href='" . htmlspecialchars($setupLink) . "' style='color:var(--kn-orange)'>" . htmlspecialchars($setupLink) . "</a>";
        }

        header('Location: index.php?action=registerResident'); exit;
    }
}
