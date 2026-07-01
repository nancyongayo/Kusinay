<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/google.php';

class UserModel {
    private PDO $db;

    public function __construct() {
        $this->db = getDBConnection();
        $this->ensureColumns();
    }

    // ── Auto-migration: add new columns if they don't exist yet ──────────────
    // This runs once per request and is a no-op after the first successful run.

    private function ensureColumns(): void {
        try {
            // Check if force_password_change exists on users
            $cols = $this->db->query("SHOW COLUMNS FROM `users` LIKE 'force_password_change'")->fetchAll();
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE `users` ADD COLUMN `force_password_change` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_verified`");
            }

            // Check if assigned_bns_id exists on user_profiles
            $cols = $this->db->query("SHOW COLUMNS FROM `user_profiles` LIKE 'assigned_bns_id'")->fetchAll();
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE `user_profiles` ADD COLUMN `assigned_bns_id` INT(11) DEFAULT NULL AFTER `profile_complete`");
            }

            // Check if skip_otp exists on user_auth
            $cols = $this->db->query("SHOW COLUMNS FROM `user_auth` LIKE 'skip_otp'")->fetchAll();
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE `user_auth` ADD COLUMN `skip_otp` TINYINT(1) NOT NULL DEFAULT 0");
            }

            // Check if first_login_completed exists on user_auth
            $cols = $this->db->query("SHOW COLUMNS FROM `user_auth` LIKE 'first_login_completed'")->fetchAll();
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE `user_auth` ADD COLUMN `first_login_completed` TINYINT(1) NOT NULL DEFAULT 0");
            }

            // Create trusted devices table if it doesn't exist
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS `user_trusted_devices` (
                  `device_id`    INT(11)      NOT NULL AUTO_INCREMENT,
                  `user_id`      INT(11)      NOT NULL,
                  `device_token` VARCHAR(255) NOT NULL,
                  `user_agent`   VARCHAR(500) DEFAULT NULL,
                  `ip_address`   VARCHAR(45)  DEFAULT NULL,
                  `expires_at`   DATETIME     NOT NULL,
                  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`device_id`),
                  UNIQUE KEY `uq_device_token` (`device_token`),
                  KEY `idx_user_trusted` (`user_id`),
                  CONSTRAINT `fk_trusted_user`
                    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        } catch (\PDOException $e) {
            // Log but don't crash — worst case the feature just won't work yet
            error_log('UserModel::ensureColumns error: ' . $e->getMessage());
        }
    }

    // ── Encryption helpers ───────────────────────────────────────────────────

    public function encryptAddress(string $address): string {
        $iv        = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($address, 'AES-128-CBC', AES_KEY, 0, $iv);
        return base64_encode($iv . '::' . $encrypted);
    }

    public function decryptAddress(string $stored): string {
        $decoded = base64_decode($stored);
        [$iv, $encrypted] = explode('::', $decoded, 2);
        return openssl_decrypt($encrypted, 'AES-128-CBC', AES_KEY, 0, $iv) ?: '';
    }

    // ── Shared base query ────────────────────────────────────────────────────
    // Joins all normalized tables into one flat result for the controllers.

    private function baseQuery(): string {
        return "
            SELECT
                u.user_id, u.first_name, u.middle_name, u.last_name,
                u.email, u.password_hash, u.is_verified, u.role_id,
                u.force_password_change,
                u.created_at, u.updated_at,
                r.role_name,
                p.address_encrypted, p.barangay_code, p.profile_complete,
                p.assigned_bns_id,
                a.google_id, a.verification_token, a.failed_attempts, a.locked_until, a.skip_otp, a.first_login_completed
            FROM users u
            LEFT JOIN roles         r ON u.role_id  = r.role_id
            LEFT JOIN user_profiles p ON u.user_id  = p.user_id
            LEFT JOIN user_auth     a ON u.user_id  = a.user_id
        ";
    }

    // ── Registration ─────────────────────────────────────────────────────────

    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return (bool) $stmt->fetch();
    }

    public function register(array $data): int {
        $hash  = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        // Use shorter token to avoid truncation issues
        $token = bin2hex(random_bytes(16)); // 32 characters instead of 64

        // Insert core user
        $this->db->prepare("
            INSERT INTO users (first_name, middle_name, last_name, email, password_hash, is_verified)
            VALUES (?, ?, ?, ?, ?, 0)
        ")->execute([
            $data['first_name'],
            $data['middle_name'] ?? null,
            $data['last_name'],
            $data['email'],
            $hash,
        ]);
        $userId = (int) $this->db->lastInsertId();

        // Insert auth row with verification token
        $this->db->prepare("
            INSERT INTO user_auth (user_id, verification_token) VALUES (?, ?)
        ")->execute([$userId, $token]);

        return $userId;
    }

    // ── Email Verification ───────────────────────────────────────────────────

    public function getVerificationToken(string $email): ?string {
        $stmt = $this->db->prepare("
            SELECT a.verification_token
            FROM user_auth a
            INNER JOIN users u ON a.user_id = u.user_id
            WHERE u.email = ?
        ");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ? $row['verification_token'] : null;
    }

    public function verifyEmail(string $token): bool {
        $stmt = $this->db->prepare("
            SELECT user_id FROM user_auth WHERE verification_token = ?
        ");
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        if (!$row) return false;

        $userId = $row['user_id'];

        $update = $this->db->prepare("
            UPDATE users SET is_verified = 1 WHERE user_id = ? AND is_verified = 0
        ");
        $update->execute([$userId]);

        if ($update->rowCount() === 0) return false;

        return true;
    }

    // ── Find helpers ─────────────────────────────────────────────────────────

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare($this->baseQuery() . " WHERE u.email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare($this->baseQuery() . " WHERE u.user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // ── Login lockout ────────────────────────────────────────────────────────

    public function incrementFailedAttempts(int $userId): void {
        // Upsert — ensure user_auth row exists
        $this->db->prepare("
            INSERT INTO user_auth (user_id, failed_attempts)
            VALUES (?, 1)
            ON DUPLICATE KEY UPDATE failed_attempts = failed_attempts + 1
        ")->execute([$userId]);

        $stmt = $this->db->prepare("SELECT failed_attempts FROM user_auth WHERE user_id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        if ($row && $row['failed_attempts'] >= MAX_FAILED_ATTEMPTS) {
            $lockUntil = date('Y-m-d H:i:s', strtotime('+' . LOCKOUT_MINUTES . ' minutes'));
            $this->db->prepare("UPDATE user_auth SET locked_until = ? WHERE user_id = ?")
                     ->execute([$lockUntil, $userId]);
        }
    }

    public function resetFailedAttempts(int $userId): void {
        $this->db->prepare("
            UPDATE user_auth SET failed_attempts = 0, locked_until = NULL WHERE user_id = ?
        ")->execute([$userId]);
    }

    // ── OTP ──────────────────────────────────────────────────────────────────

    public function saveOTP(int $userId, string $otp): void {
        $expiry = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));
        try {
            // Always INSERT a new row — keeps full OTP history
            $this->db->prepare("
                INSERT INTO user_otp (user_id, otp_code, otp_expiry)
                VALUES (?, ?, ?)
            ")->execute([$userId, $otp, $expiry]);
        } catch (\PDOException $e) {
            error_log('saveOTP error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function verifyOTP(int $userId, string $otp): bool {
        // Get the latest OTP for this user
        $stmt = $this->db->prepare("
            SELECT otp_id, otp_code, otp_expiry
            FROM user_otp
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        if (!$row || $row['otp_code'] !== $otp) return false;
        if (strtotime($row['otp_expiry']) < time())  return false;

        return true;
    }

    // ── Profile / Role setup ─────────────────────────────────────────────────

    public function updateRoleAndAddress(int $userId, int $roleId, string $address, string $barangayCode): bool {
        $encrypted = $this->encryptAddress($address);

        $this->db->prepare("UPDATE users SET role_id = ? WHERE user_id = ?")
                 ->execute([$roleId, $userId]);

        $this->db->prepare("
            INSERT INTO user_profiles (user_id, address_encrypted, barangay_code, profile_complete)
            VALUES (?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE
                address_encrypted = VALUES(address_encrypted),
                barangay_code     = VALUES(barangay_code),
                profile_complete  = 1
        ")->execute([$userId, $encrypted, $barangayCode]);

        return true;
    }

    // ── Google OAuth ──────────────────────────────────────────────────────────

    public function findOrCreateGoogleUser(string $googleId, string $email, string $firstName, string $lastName): array {
        // Try by google_id first, then email
        $stmt = $this->db->prepare("
            SELECT u.user_id FROM users u
            LEFT JOIN user_auth a ON u.user_id = a.user_id
            WHERE a.google_id = ? OR u.email = ?
            LIMIT 1
        ");
        $stmt->execute([$googleId, $email]);
        $row = $stmt->fetch();

        if ($row) {
            $userId = $row['user_id'];
            // Link google_id if not yet linked, mark verified
            $this->db->prepare("
                INSERT INTO user_auth (user_id, google_id)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE
                    google_id = COALESCE(user_auth.google_id, VALUES(google_id))
            ")->execute([$userId, $googleId]);

            $this->db->prepare("UPDATE users SET is_verified = 1 WHERE user_id = ?")
                     ->execute([$userId]);

            return $this->findById($userId);
        }

        // Create new user from Google
        $this->db->prepare("
            INSERT INTO users (first_name, last_name, email, password_hash, is_verified)
            VALUES (?, ?, ?, '', 1)
        ")->execute([$firstName, $lastName, $email]);

        $userId = (int) $this->db->lastInsertId();

        $this->db->prepare("
            INSERT INTO user_auth (user_id, google_id) VALUES (?, ?)
        ")->execute([$userId, $googleId]);

        return $this->findById($userId);
    }

    // ── Password Reset ────────────────────────────────────────────────────────

    public function savePasswordResetToken(int $userId, string $token): void {
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        // Upsert — replace any existing token for this user so only one is active
        $this->db->prepare("
            INSERT INTO user_password_resets (user_id, reset_token, expires_at)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE reset_token = VALUES(reset_token), expires_at = VALUES(expires_at)
        ")->execute([$userId, $token, $expiry]);
    }

    public function findByResetToken(string $token): ?array {
        $stmt = $this->db->prepare("
            SELECT u.user_id FROM user_password_resets r
            INNER JOIN users u ON r.user_id = u.user_id
            WHERE r.reset_token = ? AND r.expires_at > NOW()
        ");
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        return $row ? $this->findById($row['user_id']) : null;
    }

    public function updatePassword(int $userId, string $newPassword): void {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->db->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?")
                 ->execute([$hash, $userId]);
        // Keep reset token row — just expire it so it can't be reused
        $this->db->prepare("
            UPDATE user_password_resets SET expires_at = NOW() WHERE user_id = ?
        ")->execute([$userId]);
    }

    // ── BNS Resident Registration ─────────────────────────────────────────────

    /**
     * Creates a BNS-initiated resident account in a single transaction.
     * Uses invite/magic-link flow — no temporary password.
     * A setup token is stored in user_auth.verification_token.
     * Returns [user_id, setup_token].
     *
     * $data keys: first_name, last_name, email
     */
    public function registerByBns(array $data, int $bnsUserId): array {
        $setupToken = bin2hex(random_bytes(32));

        $this->db->beginTransaction();
        try {
            // Step 1: Insert core user — pre-verified, Mother role (4), force change, NO password
            $this->db->prepare("
                INSERT INTO users (first_name, last_name, email, password_hash, is_verified, role_id, force_password_change)
                VALUES (?, ?, ?, '', 1, 4, 1)
            ")->execute([
                $data['first_name'],
                $data['last_name'],
                $data['email'],
            ]);
            $userId = (int) $this->db->lastInsertId();

            // Step 2: Insert user_auth with setup token (reuse verification_token column)
            $this->db->prepare("
                INSERT INTO user_auth (user_id, verification_token) VALUES (?, ?)
            ")->execute([$userId, $setupToken]);

            // Step 3: Insert user_profiles with assigned BNS
            $this->db->prepare("
                INSERT INTO user_profiles (user_id, profile_complete, assigned_bns_id)
                VALUES (?, 0, ?)
                ON DUPLICATE KEY UPDATE assigned_bns_id = VALUES(assigned_bns_id)
            ")->execute([$userId, $bnsUserId]);

            $this->db->commit();
            return ['user_id' => $userId, 'setup_token' => $setupToken];
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Finds a user by their setup token (stored in user_auth.verification_token).
     * Only checks that the token exists and is not NULL — no other conditions.
     * Token is cleared after use in completeSetup().
     */
    public function findBySetupToken(string $token): ?array {
        if (!$token) return null;

        // Log for debugging
        error_log("findBySetupToken: looking up token length=" . strlen($token) . " first20=" . substr($token, 0, 20));

        $stmt = $this->db->prepare("
            SELECT a.user_id, a.verification_token
            FROM user_auth a
            WHERE a.verification_token = ?
        ");
        $stmt->execute([$token]);
        $row = $stmt->fetch();

        if (!$row) {
            // Try to find if token exists at all (maybe truncated)
            $partial = substr($token, 0, 30);
            $stmt2 = $this->db->prepare("SELECT user_id, LEFT(verification_token,30) as tok FROM user_auth WHERE verification_token LIKE ?");
            $stmt2->execute([$partial . '%']);
            $partial_row = $stmt2->fetch();
            error_log("findBySetupToken: NOT FOUND. Partial match: " . ($partial_row ? "user_id=" . $partial_row['user_id'] : "none"));
            return null;
        }

        error_log("findBySetupToken: FOUND user_id=" . $row['user_id']);
        return $this->findById((int)$row['user_id']);
    }

    /**
     * Completes account setup: sets password, clears setup token, clears force_password_change.
     * Does NOT set profile_complete — BNS-registered residents must complete Mother Wizard first.
     */
    public function completeSetup(int $userId, string $password): void {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->db->prepare("UPDATE users SET password_hash = ?, force_password_change = 0 WHERE user_id = ?")
                 ->execute([$hash, $userId]);
        // Clear the setup token so the link can't be reused
        $this->db->prepare("UPDATE user_auth SET verification_token = NULL WHERE user_id = ?")
                 ->execute([$userId]);
        // Ensure user_profiles row exists (profile_complete stays 0 until Mother Wizard is done)
        $this->db->prepare("
            INSERT IGNORE INTO user_profiles (user_id, profile_complete)
            VALUES (?, 0)
        ")->execute([$userId]);
    }

    /**
     * Generates a new setup token for resending the invite link.
     */
    public function regenerateSetupToken(int $userId): string {
        $token = bin2hex(random_bytes(32));
        // Reset force_password_change and update token
        $this->db->prepare("UPDATE users SET force_password_change = 1, password_hash = '' WHERE user_id = ?")
                 ->execute([$userId]);
        $this->db->prepare("
            INSERT INTO user_auth (user_id, verification_token) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE verification_token = VALUES(verification_token)
        ")->execute([$userId, $token]);
        return $token;
    }

    /**
     * Sets force_password_change = 1 for a user.
     * Used when resending credentials.
     */
    public function setForcePasswordChange(int $userId): void {
        $this->db->prepare("UPDATE users SET force_password_change = 1 WHERE user_id = ?")
                 ->execute([$userId]);
    }

    /**
     * Clears force_password_change = 0 for a user.
     * Called after the resident successfully changes their password.
     */
    public function clearForcePasswordChange(int $userId): void {
        $this->db->prepare("UPDATE users SET force_password_change = 0 WHERE user_id = ?")
                 ->execute([$userId]);
    }

    /**
     * Sets skip_otp = 1 in user_auth — OTP skipped on future logins for this user.
     */
    public function setSkipOtp(int $userId): void {
        try {
            $this->db->prepare("
                INSERT INTO user_auth (user_id, skip_otp) VALUES (?, 1)
                ON DUPLICATE KEY UPDATE skip_otp = 1
            ")->execute([$userId]);
        } catch (\PDOException $e) {
            // Column may not exist yet — silently ignore
            error_log('setSkipOtp: ' . $e->getMessage());
        }
    }

    /**
     * Marks first login as completed — OTP will be skipped on future logins.
     */
    public function markFirstLoginCompleted(int $userId): void {
        try {
            $this->db->prepare("
                INSERT INTO user_auth (user_id, first_login_completed) VALUES (?, 1)
                ON DUPLICATE KEY UPDATE first_login_completed = 1
            ")->execute([$userId]);
        } catch (\PDOException $e) {
            error_log('markFirstLoginCompleted: ' . $e->getMessage());
        }
    }

    /**
     * Returns all resident accounts assigned to the given BNS user.
     * Includes force_password_change (pending first login indicator) and profile_complete.
     */
    public function getResidentsByBns(int $bnsId): array {
        $stmt = $this->db->prepare("
            SELECT
                u.user_id, u.first_name, u.last_name, u.email,
                u.force_password_change, u.created_at,
                p.profile_complete, p.assigned_bns_id
            FROM users u
            INNER JOIN user_profiles p ON p.user_id = u.user_id
            WHERE p.assigned_bns_id = ?
            ORDER BY u.created_at DESC
        ");
        $stmt->execute([$bnsId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // ── Trusted Device (per-role OTP policy) ─────────────────────────────────

    /**
     * Checks whether the given raw cookie token is a valid, non-expired
     * trusted device for this user.
     */
    public function isTrustedDevice(int $userId, string $rawToken): bool {
        if (!$rawToken) return false;
        $hashed = hash('sha256', $rawToken);
        $stmt = $this->db->prepare("
            SELECT device_id FROM user_trusted_devices
            WHERE user_id = ? AND device_token = ? AND expires_at > NOW()
        ");
        $stmt->execute([$userId, $hashed]);
        return (bool) $stmt->fetch();
    }

    /**
     * Registers a new trusted device for the user.
     * Returns the raw (unhashed) token to be stored in a cookie.
     *
     * @param int    $userId
     * @param int    $days    How many days to trust this device
     * @return string         Raw token (store in cookie)
     */
    public function trustDevice(int $userId, int $days): string {
        $rawToken = bin2hex(random_bytes(32));
        $hashed   = hash('sha256', $rawToken);
        $expiry   = date('Y-m-d H:i:s', strtotime("+{$days} days"));
        $ua       = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $ip       = $_SERVER['REMOTE_ADDR']     ?? null;

        // Remove any expired tokens for this user first (housekeeping)
        $this->db->prepare("
            DELETE FROM user_trusted_devices WHERE user_id = ? AND expires_at <= NOW()
        ")->execute([$userId]);

        $this->db->prepare("
            INSERT INTO user_trusted_devices (user_id, device_token, user_agent, ip_address, expires_at)
            VALUES (?, ?, ?, ?, ?)
        ")->execute([$userId, $hashed, $ua, $ip, $expiry]);

        return $rawToken;
    }

    /**
     * Revokes all trusted devices for a user (e.g., after password reset).
     * Also resets first_login_completed flag for security.
     */
    public function revokeAllTrustedDevices(int $userId): void {
        $this->db->prepare("
            DELETE FROM user_trusted_devices WHERE user_id = ?
        ")->execute([$userId]);
        
        // Reset first login flag so OTP is required again after password reset
        try {
            $this->db->prepare("
                UPDATE user_auth SET first_login_completed = 0 WHERE user_id = ?
            ")->execute([$userId]);
        } catch (\PDOException $e) {
            error_log('revokeAllTrustedDevices: ' . $e->getMessage());
        }
    }

    /**
     * Manually verify an email address - bypasses email verification process
     * Useful when email delivery fails but user needs access
     */
    public function manuallyVerifyEmail(string $email): bool {
        $stmt = $this->db->prepare("
            SELECT user_id FROM users WHERE email = ? AND is_verified = 0
        ");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return false; // Email not found or already verified
        }
        
        $userId = $row['user_id'];
        
        // Mark as verified
        $update = $this->db->prepare("
            UPDATE users SET is_verified = 1 WHERE user_id = ?
        ");
        $update->execute([$userId]);
        
        // Clear verification token
        $this->db->prepare("
            UPDATE user_auth SET verification_token = NULL WHERE user_id = ?
        ")->execute([$userId]);
        
        return $update->rowCount() > 0;
    }
}
