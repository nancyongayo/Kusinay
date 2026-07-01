<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/google.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../../core/Mailer.php';
require_once __DIR__ . '/../../core/Security.php';

class AuthController {
    private UserModel $userModel;
    private Mailer $mailer;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->mailer    = new Mailer();
    }

    // ── Session guard ────────────────────────────────────────────────────────

    private function startSecureSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => false, // set true in production (HTTPS)
                'httponly' => true,
                'samesite' => 'Lax', // Lax required for OAuth redirect flows
            ]);
            session_start();
        }
        // Idle timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
            $this->logout();
        }
        $_SESSION['last_activity'] = time();
    }



    public function showRegister(): void {
        include __DIR__ . '/../views/auth/register.php';
    }

    public function register(): void {
        $this->startSecureSession();
        Security::verifyCsrf();

        $firstName  = trim($_POST['first_name']  ?? '');
        $middleName = trim($_POST['middle_name'] ?? '');
        $lastName   = trim($_POST['last_name']   ?? '');
        $email      = strtolower(trim($_POST['email'] ?? ''));
        $password   = $_POST['password']  ?? '';
        $confirm    = $_POST['confirm_password'] ?? '';

        // Validation
        $errors = [];
        if (!$firstName || !$lastName)          $errors[] = 'First and last name are required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
        if ($this->userModel->emailExists($email)) $errors[] = 'Email is already registered.';
        if (!$this->validatePassword($password)) $errors[] = 'Password must be at least 8 characters with uppercase, lowercase, number, and special character.';
        if ($password !== $confirm)              $errors[] = 'Passwords do not match.';

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = compact('firstName', 'middleName', 'lastName', 'email');
            header('Location: index.php?action=register');
            exit;
        }

        $userId = $this->userModel->register([
            'first_name'  => $firstName,
            'middle_name' => $middleName ?: null,
            'last_name'   => $lastName,
            'email'       => $email,
            'password'    => $password,
        ]);

        $token = $this->userModel->getVerificationToken($email);
        $sent  = $this->mailer->sendVerificationEmail($email, "$firstName $lastName", $token);
        $this->logActivity(null, 'REGISTER', "New registration: {$email}", $email);

        // Always show direct verification link for now (email issues)
        $link = 'https://kusinayapp.freehosting.dev/verify_success.php?token=' . urlencode($token);
        
        if ($sent) {
            $_SESSION['flash'] = 'Registration successful! Please check your email to verify your account. If no email received, <a href="' . $link . '" style="color:var(--kn-orange)">click here to verify directly</a>.';
        } else {
            $_SESSION['flash'] = 'Registered! Email could not be sent. <a href="' . $link . '" style="color:var(--kn-orange)">Click here to verify your account</a>.';
        }

        header('Location: index.php?action=login');
        exit;
    }

    // ── Email Verification ────────────────────────────────────────────────────

    public function verifyEmail(): void {
        $this->startSecureSession();
        $token = $_GET['token'] ?? '';

        if ($this->userModel->verifyEmail($token)) {
            $_SESSION['flash'] = 'Email verified! You can now log in.';
        } else {
            $_SESSION['errors'] = ['Invalid or expired verification link.'];
        }
        header('Location: index.php?action=login');
        exit;
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function showLogin(): void {
        include __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void {
        $this->startSecureSession();
        Security::verifyCsrf();

        $email    = strtolower(trim($_POST['email']    ?? ''));
        $password = $_POST['password'] ?? '';
        $ip       = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $user = $this->userModel->findByEmail($email);

        // Unknown email — generic error to prevent user enumeration
        if (!$user) {
            $_SESSION['errors'] = ['Invalid email or password.'];
            header('Location: index.php?action=login');
            exit;
        }

        // Lockout check
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $remaining = ceil((strtotime($user['locked_until']) - time()) / 60);
            $_SESSION['errors'] = ["Account locked. Try again in {$remaining} minute(s)."];
            $this->logActivity($user['user_id'], 'LOGIN_LOCKED', "Locked account login attempt from {$ip}");
            header('Location: index.php?action=login');
            exit;
        }

        // Email verification check
        if (!$user['is_verified']) {
            $_SESSION['errors'] = ['Please verify your email before logging in.'];
            header('Location: index.php?action=login');
            exit;
        }

        // Password check
        if (!password_verify($password, $user['password_hash'])) {
            $this->userModel->incrementFailedAttempts($user['user_id']);
            $this->logActivity($user['user_id'], 'LOGIN_FAILED', "Failed login attempt from {$ip}");

            $remaining = MAX_FAILED_ATTEMPTS - ($user['failed_attempts'] + 1);
            $msg = $remaining > 0
                ? "Invalid email or password. {$remaining} attempt(s) remaining."
                : 'Account locked due to too many failed attempts.';
            $_SESSION['errors'] = [$msg];
            header('Location: index.php?action=login');
            exit;
        }

        // Success — reset attempts
        $this->userModel->resetFailedAttempts($user['user_id']);

        $role      = $user['role_name'] ?? null;
        $userId    = $user['user_id'];

        // ── Simplified OTP policy: First login only ──────────────────────────
        //
        // OTP is required ONLY on the first login.
        // After first successful OTP verification, all subsequent logins skip OTP.
        //
        // Special case: BNS-registered residents with skip_otp=1 and no
        // force_password_change bypass OTP entirely (legacy behaviour kept).

        $skipOtp = !empty($user['skip_otp']) && empty($user['force_password_change']);
        $firstLoginCompleted = !empty($user['first_login_completed']);

        if ($skipOtp || $firstLoginCompleted) {
            // Bypass OTP — not first login or BNS-registered resident
            session_regenerate_id(true);
            $_SESSION['user_id']          = $userId;
            $_SESSION['user_name']        = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_email']       = $user['email'];
            $_SESSION['role']             = $role;
            $_SESSION['profile_complete'] = !empty($user['profile_complete']) && !empty($user['role_id']);
            $_SESSION['barangay_code']    = $user['barangay_code'] ?? '';
            $_SESSION['last_activity']    = time();

            $reason = $skipOtp ? 'OTP skipped — BNS-registered resident' : 'OTP skipped — not first login';
            $this->logActivity($userId, 'LOGIN_SUCCESS', "User logged in ({$reason})");
            session_write_close();

            if (!$_SESSION['profile_complete']) {
                header('Location: index.php?action=roleSelection'); exit;
            }
            $this->redirectByRole($role);
        }

        // OTP required — first login, generate and send
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->userModel->saveOTP($userId, $otp);

        $_SESSION['temp_user_id'] = $userId;
        $this->logActivity($userId, 'OTP_SENT', "OTP sent to {$email} (first login)");

        // Send OTP email — if it fails, show code on screen (dev fallback)
        $sent = $this->mailer->sendOTPEmail($email, $user['first_name'], $otp);
        if (!$sent) {
            $_SESSION['dev_otp'] = $otp;
        }

        header('Location: index.php?action=verifyOtp');
        exit;
    }

    // ── OTP Verification ──────────────────────────────────────────────────────

    public function showVerifyOtp(): void {
        $this->startSecureSession();
        if (!isset($_SESSION['temp_user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        include __DIR__ . '/../views/auth/verify_otp.php';
    }

    public function verifyOtp(): void {
        $this->startSecureSession();

        $userId = $_SESSION['temp_user_id'] ?? null;
        $otp    = trim($_POST['otp'] ?? '');

        if (!$userId) {
            header('Location: index.php?action=login');
            exit;
        }

        if (!$this->userModel->verifyOTP((int) $userId, $otp)) {
            $_SESSION['errors'] = ['Invalid or expired OTP. Please try again.'];
            $this->logActivity($userId, 'OTP_FAILED', 'Invalid OTP entered');
            header('Location: index.php?action=verifyOtp');
            exit;
        }

        $user = $this->userModel->findById((int) $userId);
        unset($_SESSION['temp_user_id']);

        // Regenerate session ID first, then write all vars
        session_regenerate_id(true);
        $_SESSION['user_id']          = $user['user_id'];
        $_SESSION['user_name']        = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email']       = $user['email'];
        $_SESSION['role']             = $user['role_name'] ?? null;
        $_SESSION['profile_complete'] = !empty($user['profile_complete']) && !empty($user['role_id']);
        $_SESSION['barangay_code']    = $user['barangay_code'] ?? '';
        $_SESSION['last_activity']    = time();

        $this->logActivity($user['user_id'], isset($_SESSION['temp_via_google']) ? 'GOOGLE_LOGIN' : 'LOGIN_SUCCESS', 'User logged in successfully');
        unset($_SESSION['temp_via_google']);

        // Mark first login as completed so future logins skip OTP
        $this->userModel->markFirstLoginCompleted($user['user_id']);

        // Force session write before redirect
        session_write_close();

        // Feature: bns-resident-registration, Property 7
        // If the user was registered by a BNS and has not yet changed their temp password,
        // redirect to the mandatory password change page before any other action.
        if (!empty($user['force_password_change'])) {
            header('Location: index.php?action=forceChangePassword');
            exit;
        }

        // Route based on profile completeness.
        // profile_complete=1 AND role_id set = fully onboarded user → go to dashboard.
        // Missing either means they haven't finished role selection yet.
        if (!$_SESSION['profile_complete']) {
            header('Location: index.php?action=roleSelection');
            exit;
        }

        $this->redirectByRole($user['role_name']);
    }

    // ── Role Selection (first login) ──────────────────────────────────────────

    public function showRoleSelection(): void {
        $this->startSecureSession();
        $this->requireAuth();
        include __DIR__ . '/../views/auth/role_selection.php';
    }

    public function saveRoleSelection(): void {
        $this->startSecureSession();
        $this->requireAuth();

        $roleId       = (int) ($_POST['role_id']       ?? 0);
        $address      = trim($_POST['address']         ?? '');
        $barangayCode = trim($_POST['barangay_code']   ?? '');

        $errors = [];
        if (!in_array($roleId, [1, 2, 3, 4, 5, 6, 7, 8])) $errors[] = 'Please select a valid role.';
        if (!$address)                          $errors[] = 'Address is required.';
        if (!$barangayCode)                     $errors[] = 'Please select a barangay.';

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?action=roleSelection');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $this->userModel->updateRoleAndAddress($userId, $roleId, $address, $barangayCode);

        $user = $this->userModel->findById($userId);
        $_SESSION['role']             = $user['role_name'];
        $_SESSION['barangay_code']    = $user['barangay_code'] ?? '';
        $_SESSION['profile_complete'] = true;

        $this->logActivity($userId, 'ROLE_SET', "Role set to: {$user['role_name']}");
        $this->redirectByRole($user['role_name']);
    }

    // ── Resend OTP ────────────────────────────────────────────────────────────

    public function resendOtp(): void {
        $this->startSecureSession();

        $userId = $_SESSION['temp_user_id'] ?? null;
        if (!$userId) {
            header('Location: index.php?action=login');
            exit;
        }

        $user = $this->userModel->findById((int) $userId);
        if (!$user) {
            header('Location: index.php?action=login');
            exit;
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->userModel->saveOTP((int) $userId, $otp);
        $this->logActivity($userId, 'OTP_RESENT', 'OTP resent to ' . $user['email']);

        $sent = $this->mailer->sendOTPEmail($user['email'], $user['first_name'], $otp);
        if (!$sent) {
            $_SESSION['dev_otp'] = $otp;
        }

        $_SESSION['flash'] = $sent ? 'A new OTP has been sent to your email.' : null;
        header('Location: index.php?action=verifyOtp');
        exit;
    }

    // ── Google OAuth ──────────────────────────────────────────────────────────

    public function googleCallback(): void {
        $this->startSecureSession();

        require_once __DIR__ . '/../../vendor/autoload.php';

        $client = new Google_Client();
        $client->setClientId(GOOGLE_CLIENT_ID);
        $client->setClientSecret(GOOGLE_CLIENT_SECRET);
        $client->setRedirectUri(GOOGLE_REDIRECT_URI);

        if (!isset($_GET['code'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        if (isset($token['error'])) {
            $_SESSION['errors'] = ['Google sign-in failed. Please try again.'];
            header('Location: index.php?action=login');
            exit;
        }

        $client->setAccessToken($token['access_token']);
        $oauth2   = new Google_Service_Oauth2($client);
        $gUser    = $oauth2->userinfo->get();

        $user = $this->userModel->findOrCreateGoogleUser(
            $gUser->id,
            $gUser->email,
            $gUser->givenName  ?? '',
            $gUser->familyName ?? ''
        );

        $userId = $user['user_id'];
        $firstLoginCompleted = !empty($user['first_login_completed']);

        // Google OAuth login successful
        file_put_contents(__DIR__ . '/../../google_login_debug.txt', 
            date('Y-m-d H:i:s') . " - User: {$user['email']}, first_login_completed: " . 
            ($user['first_login_completed'] ?? 'NULL') . ", will skip OTP: " . 
            ($firstLoginCompleted ? 'YES' : 'NO') . "\n", 
            FILE_APPEND
        );

        // Check if this is first login - only require OTP on first login
        if ($firstLoginCompleted) {
            // Not first login - skip OTP and login directly
            session_regenerate_id(true);
            $_SESSION['user_id']          = $userId;
            $_SESSION['user_name']        = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_email']       = $user['email'];
            $_SESSION['role']             = $user['role_name'] ?? null;
            $_SESSION['profile_complete'] = !empty($user['profile_complete']) && !empty($user['role_id']);
            $_SESSION['barangay_code']    = $user['barangay_code'] ?? '';
            $_SESSION['last_activity']    = time();

            $this->logActivity($userId, 'GOOGLE_LOGIN', 'User logged in via Google (OTP skipped - not first login)');
            session_write_close();

            if (!$_SESSION['profile_complete']) {
                header('Location: index.php?action=roleSelection');
                exit;
            }
            $this->redirectByRole($user['role_name']);
        }

        // First login - require OTP for security
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->userModel->saveOTP($userId, $otp);

        $_SESSION['temp_user_id']    = $userId;
        $_SESSION['temp_via_google'] = true;
        $this->logActivity($userId, 'GOOGLE_OTP_SENT', 'OTP sent after Google login (first login)');

        $sent = $this->mailer->sendOTPEmail($user['email'], $user['first_name'], $otp);
        if (!$sent) {
            $_SESSION['dev_otp'] = $otp;
        }

        header('Location: index.php?action=verifyOtp');
        exit;
    }

    // ── Account Setup (BNS invite magic link) ────────────────────────────────

    public function showSetupAccount(): void {
        $this->startSecureSession();
        // Normalize token — replace spaces with + in case of URL decode issue
        $token = str_replace(' ', '+', $_GET['token'] ?? '');
        error_log("showSetupAccount: token=" . substr($token, 0, 30) . " len=" . strlen($token));
        $user  = $this->userModel->findBySetupToken($token);
        if (!$user) {
            $_SESSION['errors'] = ['This setup link is invalid or has already been used. Please contact your BNS.'];
            header('Location: index.php?action=login'); exit;
        }
        include __DIR__ . '/../views/auth/setup_account.php';
    }

    public function setupAccount(): void {
        $this->startSecureSession();
        Security::verifyCsrf();

        $token    = $_POST['token'] ?? $_GET['token'] ?? '';
        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        error_log("setupAccount: START - token=" . substr($token, 0, 20));

        $user = $this->userModel->findBySetupToken($token);
        if (!$user) {
            error_log("setupAccount: Token not found or already used");
            $_SESSION['errors'] = ['This setup link is invalid or has already been used.'];
            header('Location: index.php?action=login'); exit;
        }

        error_log("setupAccount: User found - ID=" . $user['user_id'] . " force_pwd=" . ($user['force_password_change'] ?? 'NULL'));

        $errors = [];
        if (!$this->validatePassword($password))  $errors[] = 'Password must be at least 8 characters with uppercase, lowercase, number, and special character.';
        if ($password !== $confirm)               $errors[] = 'Passwords do not match.';

        if ($errors) {
            error_log("setupAccount: Validation errors - " . implode(', ', $errors));
            $_SESSION['errors'] = $errors;
            header('Location: index.php?action=setupAccount&token=' . urlencode($token)); exit;
        }

        // Complete setup: set password, clear token, clear force_password_change
        error_log("setupAccount: Calling completeSetup for user_id=" . $user['user_id']);
        $this->userModel->completeSetup($user['user_id'], $password);
        
        // Mark skip_otp so future logins don't require OTP
        error_log("setupAccount: Calling setSkipOtp for user_id=" . $user['user_id']);
        $this->userModel->setSkipOtp($user['user_id']);

        $this->logActivity($user['user_id'], 'ACCOUNT_SETUP_COMPLETE', 'Resident completed account setup via invite link');

        // Fetch updated user data after completeSetup
        error_log("setupAccount: Fetching updated user data");
        $updatedUser = $this->userModel->findById($user['user_id']);
        error_log("setupAccount: Updated user - force_pwd=" . ($updatedUser['force_password_change'] ?? 'NULL') . " skip_otp=" . ($updatedUser['skip_otp'] ?? 'NULL') . " profile_complete=" . ($updatedUser['profile_complete'] ?? 'NULL'));

        // Auto-login the resident
        session_regenerate_id(true);
        $_SESSION['user_id']          = $updatedUser['user_id'];
        $_SESSION['user_name']        = $updatedUser['first_name'] . ' ' . $updatedUser['last_name'];
        $_SESSION['user_email']       = $updatedUser['email'];
        $_SESSION['role']             = $updatedUser['role_name'] ?? 'Mother';
        $_SESSION['profile_complete'] = (bool) ($updatedUser['profile_complete'] ?? false);
        $_SESSION['barangay_code']    = $updatedUser['barangay_code'] ?? '';
        $_SESSION['last_activity']    = time();
        $_SESSION['flash']            = 'Welcome to KusiNay! Your account is ready.';

        error_log("setupAccount: Session set - user_id=" . $_SESSION['user_id'] . " role=" . $_SESSION['role'] . " profile_complete=" . ($_SESSION['profile_complete'] ? 'true' : 'false'));

        // Force session write before redirect
        session_write_close();

        // BNS-registered residents already have role_id=4 (Mother) set.
        // They need to complete the Mother Wizard first before accessing other features.
        if (!$_SESSION['profile_complete']) {
            error_log("setupAccount: Profile incomplete - redirecting to Mother Wizard");
            header('Location: index.php?action=motherWizard');
            exit;
        }

        // Profile complete - go to dashboard
        $role = $updatedUser['role_name'] ?? 'Mother';
        error_log("setupAccount: Profile complete - redirecting to role=" . $role);
        $this->redirectByRole($role);
    }

    // ── Force Password Change (BNS-registered residents) ─────────────────────

    /**
     * GET: Show the mandatory password change form.
     * Only reachable when force_password_change = 1 (set after OTP verification).
     */
    public function showForceChangePassword(): void {
        $this->startSecureSession();
        $this->requireAuth();
        include __DIR__ . '/../views/auth/force_change_password.php';
    }

    /**
     * POST: Process the mandatory password change.
     * Validates policy, ensures new password differs from temp, clears flag.
     *
     * Feature: bns-resident-registration, Property 8
     */
    public function forceChangePassword(): void {
        $this->startSecureSession();
        $this->requireAuth();
        Security::verifyCsrf();

        $userId   = (int) $_SESSION['user_id'];
        $user     = $this->userModel->findById($userId);

        // Idempotent: if flag already cleared, redirect to normal dashboard
        if (!$user || empty($user['force_password_change'])) {
            $this->redirectByRole($_SESSION['role'] ?? null);
        }

        $newPassword = $_POST['password']         ?? '';
        $confirm     = $_POST['confirm_password'] ?? '';

        $errors = [];
        if (!$this->validatePassword($newPassword)) {
            $errors[] = 'Password must be at least 8 characters with uppercase, lowercase, number, and special character.';
        }
        if ($newPassword !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }
        // New password must differ from the current (temporary) password
        if (!$errors && password_verify($newPassword, $user['password_hash'])) {
            $errors[] = 'Your new password must be different from your temporary password.';
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?action=forceChangePassword'); exit;
        }

        $this->userModel->updatePassword($userId, $newPassword);
        $this->userModel->clearForcePasswordChange($userId);
        // Mark this account to skip OTP on future logins (first login complete)
        $this->userModel->setSkipOtp($userId);
        // Revoke any existing trusted devices and reset first login flag
        $this->userModel->revokeAllTrustedDevices($userId);
        $this->logActivity($userId, 'PASSWORD_FORCE_CHANGED', 'Resident completed mandatory password change');

        // Refresh session profile_complete flag
        $updatedUser = $this->userModel->findById($userId);
        $_SESSION['profile_complete'] = (bool) ($updatedUser['profile_complete'] ?? false);

        if (!$_SESSION['profile_complete']) {
            header('Location: index.php?action=roleSelection'); exit;
        }

        $this->redirectByRole($_SESSION['role'] ?? null);
    }

    // ── Forgot / Reset Password ───────────────────────────────────────────────

    public function showForgotPassword(): void {
        include __DIR__ . '/../views/auth/forgot_password.php';
    }

    public function forgotPassword(): void {
        $this->startSecureSession();
        $email = strtolower(trim($_POST['email'] ?? ''));
        $user  = $this->userModel->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $this->userModel->savePasswordResetToken($user['user_id'], $token);
            $sent = $this->mailer->sendPasswordResetEmail($email, $user['first_name'], $token);
            $this->logActivity($user['user_id'], 'PASSWORD_RESET_REQUEST', "Reset requested for {$email} - Email sent: " . ($sent ? 'Yes' : 'No'));
            
            if ($sent) {
                $_SESSION['flash'] = 'A password reset link has been sent to your email. Please check your inbox and spam folder.';
            } else {
                // Email failed - provide the reset link directly for development/testing
                $link = 'https://kusinayapp.freehosting.dev/index.php?action=resetPassword&token=' . urlencode($token);
                $_SESSION['flash'] = 'Email could not be sent. <a href="' . $link . '" style="color:var(--kn-orange);font-weight:600">Click here to reset your password directly</a>.';
            }
        } else {
            // User not found - show generic message
            $_SESSION['flash'] = 'If that email exists, a reset link has been sent. Please check your inbox.';
        }

        header('Location: index.php?action=login');
        exit;
    }

    public function showResetPassword(): void {
        $this->startSecureSession();
        $token = $_GET['token'] ?? '';
        $user  = $this->userModel->findByResetToken($token);
        if (!$user) {
            $_SESSION['errors'] = ['Invalid or expired reset link.'];
            header('Location: index.php?action=login');
            exit;
        }
        include __DIR__ . '/../views/auth/reset_password.php';
    }

    public function resetPassword(): void {
        $this->startSecureSession();
        $token    = $_POST['token']    ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $user     = $this->userModel->findByResetToken($token);

        if (!$user) {
            $_SESSION['errors'] = ['Invalid or expired reset link.'];
            header('Location: index.php?action=login');
            exit;
        }

        $errors = [];
        if (!$this->validatePassword($password)) $errors[] = 'Password must be at least 8 characters with uppercase, lowercase, number, and special character.';
        if ($password !== $confirm)              $errors[] = 'Passwords do not match.';

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?action=resetPassword&token=' . urlencode($token));
            exit;
        }

        $this->userModel->updatePassword($user['user_id'], $password);
        // Revoke all trusted devices and reset first login flag — user must verify with OTP again
        $this->userModel->revokeAllTrustedDevices($user['user_id']);
        $this->logActivity($user['user_id'], 'PASSWORD_RESET', 'Password was reset');
        $_SESSION['flash'] = 'Password updated successfully. Please log in.';
        header('Location: index.php?action=login');
        exit;
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function logout(): void {
        $this->startSecureSession();
        if (isset($_SESSION['user_id'])) {
            $this->logActivity($_SESSION['user_id'], 'LOGOUT', 'User logged out');
        }
        session_unset();
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function requireAuth(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    private function validatePassword(string $password): bool {
        // Min 8 chars, uppercase, lowercase, digit, special char
        return strlen($password) >= 8
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/[0-9]/', $password)
            && preg_match('/[\W_]/', $password);
    }

    private function redirectByRole(?string $role): void {
        $map = [
            'Admin'                    => 'index.php?action=securityLogs',
            'Nutrition Officer II'     => 'index.php?action=reportValidation',
            'BNS Staff'                => 'index.php?action=bnsDashboard',
            'Mother'                   => 'index.php?action=home',
            'Committee Chair on Health'=> 'index.php?action=committeeChairDashboard',
            'Committee Secretary'      => 'index.php?action=secretaryDashboard',
            'Barangay Captain'         => 'index.php?action=captainDashboard',
            'Market Vendor'            => 'index.php?action=marketVendorDashboard',
        ];
        header('Location: ' . ($map[$role] ?? 'index.php?action=home'));
        exit;
    }

    private function logActivity(?int $userId, string $actionType, string $description, string $email = ''): void {
        try {
            $db   = getDBConnection();
            $ip   = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $stmt = $db->prepare("
                INSERT INTO system_logs (user_id, action_type, description, ip_address)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $actionType, $description, $ip]);
        } catch (Exception $e) {
            error_log('Log error: ' . $e->getMessage());
        }
    }
}
