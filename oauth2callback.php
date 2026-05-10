<?php
// Google OAuth 2.0 callback — must load everything independently
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/google.php';
require_once __DIR__ . '/app/models/UserModel.php';
require_once __DIR__ . '/core/Mailer.php';
require_once __DIR__ . '/vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Lax',  // Must be Lax (not Strict) for OAuth redirects
    ]);
    session_start();
}

// ── Exchange code for token ───────────────────────────────────────────────────
if (!isset($_GET['code'])) {
    header('Location: index.php?action=login');
    exit;
}

$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    $_SESSION['errors'] = ['Google sign-in failed. Please try again.'];
    header('Location: index.php?action=login');
    exit;
}

$client->setAccessToken($token['access_token']);
$oauth2 = new Google_Service_Oauth2($client);
$gUser  = $oauth2->userinfo->get();

// ── Find or create user ───────────────────────────────────────────────────────
$userModel = new UserModel();
$user = $userModel->findOrCreateGoogleUser(
    $gUser->id,
    $gUser->email,
    $gUser->givenName  ?? '',
    $gUser->familyName ?? ''
);

// ── Send OTP — Google login still requires OTP verification ──────────────────
$otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$userModel->saveOTP($user['user_id'], $otp);

// Store temp session BEFORE sending email so redirect always fires
$_SESSION['temp_user_id']    = $user['user_id'];
$_SESSION['temp_via_google'] = true;

$mailer = new Mailer();
$sent   = $mailer->sendOTPEmail($user['email'], $user['first_name'], $otp);

if (!$sent) {
    $_SESSION['dev_otp'] = $otp;
}

try {
    $db = getDBConnection();
    $db->prepare("INSERT INTO system_logs (user_id, action_type, description, ip_address) VALUES (?,?,?,?)")
       ->execute([$user['user_id'], 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
} catch (Exception $e) {
    error_log('Log error: ' . $e->getMessage());
}

// Force session write before redirect
session_write_close();

header('Location: index.php?action=verifyOtp');
exit;
