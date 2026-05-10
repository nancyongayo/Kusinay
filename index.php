<?php
// ── Bootstrap ────────────────────────────────────────────────────────────────
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/google.php';
require_once __DIR__ . '/core/Security.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/FamilyProfileController.php';
require_once __DIR__ . '/app/controllers/FamilyWizardController.php';
require_once __DIR__ . '/app/controllers/BnsValidationController.php';
require_once __DIR__ . '/app/controllers/NotificationController.php';
require_once __DIR__ . '/app/controllers/NutritionAssessmentController.php';
require_once __DIR__ . '/app/controllers/AccomplishmentReportController.php';
require_once __DIR__ . '/app/controllers/NutritionEducationController.php';
require_once __DIR__ . '/app/controllers/BnsResidentController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => false,   // true in production
        'httponly' => true,
        'samesite' => 'Lax',   // Lax required for OAuth redirect flows
    ]);
    session_start();
}

// ── Idle session timeout ──────────────────────────────────────────────────────
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
    header('Location: index.php?action=login&reason=timeout');
    exit;
}
if (isset($_SESSION['user_id'])) {
    $_SESSION['last_activity'] = time();
}

// ── Admin lock check — force-logout if account was locked while session active ─
if (isset($_SESSION['user_id'])) {
    $db = getDBConnection();
    $lockStmt = $db->prepare("SELECT locked_until FROM user_auth WHERE user_id = ?");
    $lockStmt->execute([$_SESSION['user_id']]);
    $lockRow = $lockStmt->fetch();
    if ($lockRow && $lockRow['locked_until'] && strtotime($lockRow['locked_until']) > time()) {
        session_unset();
        session_destroy();
        header('Location: index.php?action=login&reason=locked');
        exit;
    }
}

// ── Route ─────────────────────────────────────────────────────────────────────
$action = $_GET['action'] ?? 'landing';
$auth   = new AuthController();
$fp     = new FamilyProfileController();
$fw     = new FamilyWizardController();
$bv     = new BnsValidationController();
$nc     = new NotificationController();
$na     = new NutritionAssessmentController();
$ar     = new AccomplishmentReportController();
$ne     = new NutritionEducationController();
$br     = new BnsResidentController();

$routes = [
    // GET
    'landing'         => fn() => include __DIR__ . '/app/views/auth/landing.php',
    'login'           => fn() => $auth->showLogin(),
    'register'        => fn() => $auth->showRegister(),
    'verifyOtp'       => fn() => $auth->showVerifyOtp(),
    'roleSelection'   => fn() => $auth->showRoleSelection(),
    'forgotPassword'  => fn() => $auth->showForgotPassword(),
    'resetPassword'   => fn() => $auth->showResetPassword(),
    'verify'          => fn() => $auth->verifyEmail(),
    'logout'          => fn() => $auth->logout(),
    'resendOtp'       => fn() => $auth->resendOtp(),
    'home'            => fn() => include __DIR__ . '/app/views/mother/home.php',
    'reportValidation'=> fn() => include __DIR__ . '/app/views/nutrition/report_validation.php',
    'securityLogs'    => fn() => include __DIR__ . '/app/views/admin/security_logs.php',
    'userManagement'  => fn() => include __DIR__ . '/app/views/admin/user_management.php',
    'userActivity'    => fn() => include __DIR__ . '/app/views/admin/user_activity.php',
    'dataEncoding'    => fn() => $na->showDataEncoding(),

    // Nutrition Assessment (Processes 3, 4, 5)
    'assessmentForm'  => fn() => $na->showAssessmentForm(),
    'saveAssessment'  => fn() => $na->saveAssessment(),
    'formCReport'     => fn() => $na->showFormC(),
    'p12Monitoring'   => fn() => $na->showP12(),
    'p12MonitoringAll' => fn() => $na->showP12All(),
    'saveFollowUp'      => fn() => $na->saveFollowUp(),
    'previewAssessment' => fn() => $na->previewAssessment(),
    'optResults'          => fn() => $na->showOPTResults(),
    'pregnantMasterlist'  => fn() => $na->showPregnantMasterlist(),
    'lactatingMasterlist' => fn() => $na->showLactatingMasterlist(),
    'seniorMasterlist'    => fn() => $na->showSeniorMasterlist(),

    // Process 6: BNS Monthly Accomplishment Report
    'accomplishmentReport' => fn() => $ar->showReport(),
    'saveAccomplishment'   => fn() => $ar->saveReport(),
    'submitAccomplishment' => fn() => $ar->submitReport(),
    'uploadAttachment'     => fn() => $ar->uploadAttachment(),
    'deleteAttachment'     => fn() => $ar->deleteAttachment(),

    // Process 7: Nutrition Officer II Validation
    'reportValidation'    => fn() => $ar->showNO2Dashboard(),
    'reportDetail'        => fn() => $ar->showReportDetail(),
    'approveReport'       => fn() => $ar->approveReport(),
    'returnReport'        => fn() => $ar->returnReport(),

    // Processes 8, 9, 10: Nutrition Education
    'nutritionEducationList' => fn() => $ne->showSessionList(),
    'nutritionEducationForm' => fn() => $ne->showSessionForm(),
    'saveSession'            => fn() => $ne->saveSession(),
    'deleteSession'          => fn() => $ne->deleteSession(),
    'recordAttendance'       => fn() => $ne->showAttendanceForm(),
    'saveAttendance'         => fn() => $ne->saveAttendance(),
    'startSession'           => fn() => $ne->startSession(),
    'completeSession'        => fn() => $ne->completeSession(),
    'cancelSession'          => fn() => $ne->cancelSession(),
    'upcomingSessions'       => fn() => $ne->showUpcomingSessions(),
    'rsvpSession'            => fn() => $ne->toggleRsvp(),
    'sessionRsvpList'        => fn() => $ne->showRsvpList(),
    'downloadMaterial'       => fn() => $ne->downloadMaterial(),
    'deleteMaterial'         => fn() => $ne->deleteMaterial(),
    'uploadMaterial'         => fn() => $ne->uploadMaterialDirect(),

    // BNS Family Profile
    'bnsDashboard'       => fn() => $fp->showDashboard(),
    'familyProfiles'     => fn() => $fp->listProfiles(),
    'familyProfileForm'  => fn() => $fp->showForm(),
    'saveFamilyProfile'  => fn() => $fp->saveProfile(),
    'deleteFamilyProfile'=> fn() => $fp->deleteProfile(),
    'bnsSettings'        => fn() => $fp->showSettings(),
    'saveBnsLocation'    => fn() => $fp->saveLocation(),

    // BNS Resident Registration
    'registerResident'    => fn() => $br->registerResident(),
    'listResidents'       => fn() => $br->listResidents(),
    'resendCredentials'   => fn() => $br->resendCredentials(),
    'forceChangePassword' => fn() => $auth->showForceChangePassword(),
    'doForceChangePassword' => fn() => $auth->forceChangePassword(),
    'setupAccount'        => fn() => $auth->showSetupAccount(),
    'doSetupAccount'      => fn() => $auth->setupAccount(),

    // Mother Wizard
    'motherWizard'        => fn() => $fw->showWizard(),
    'motherProfile'       => fn() => $fw->showProfile(),
    'saveWizardDraft'     => fn() => $fw->saveDraft(),
    'submitWizardProfile' => fn() => $fw->submitProfile(),
    'searchUsers'         => fn() => $fw->searchUsers(),

    // BNS Validation
    'bnsValidationList'   => fn() => $bv->listPending(),
    'bnsValidationDetail' => fn() => $bv->showDetail(),
    'doValidateProfile'   => fn() => $bv->validateProfile(),
    'doReturnProfile'     => fn() => $bv->returnProfile(),
    'doOverrideHof'       => fn() => $bv->overrideHof(),

    // Notifications
    'notifications'        => fn() => $nc->listNotifications(),
    'markNotificationRead' => fn() => $nc->markRead(),
    'deleteNotification'   => fn() => $nc->deleteNotification(),
    'confirmFamilyLink'    => fn() => $nc->confirmLink(),
    'rejectFamilyLink'     => fn() => $nc->rejectLink(),
    'notificationBadge'    => fn() => $nc->getBadgeCount(),

    // POST (same action key, method checked inside controller)
    'doLogin'         => fn() => $auth->login(),
    'doRegister'      => fn() => $auth->register(),
    'doVerifyOtp'     => fn() => $auth->verifyOtp(),
    'doRoleSelection' => fn() => $auth->saveRoleSelection(),
    'doForgotPwd'     => fn() => $auth->forgotPassword(),
    'doResetPwd'      => fn() => $auth->resetPassword(),
];

// Unified POST routing: forms POST to same action name
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postMap = [
        'login'          => 'doLogin',
        'register'       => 'doRegister',
        'verifyOtp'      => 'doVerifyOtp',
        'saveRoleSelection' => 'doRoleSelection',
        'forgotPassword' => 'doForgotPwd',
        'resetPassword'  => 'doResetPwd',
        'saveWizardDraft'      => 'saveWizardDraft',
        'submitWizardProfile'  => 'submitWizardProfile',
        'doValidateProfile'    => 'doValidateProfile',
        'doReturnProfile'      => 'doReturnProfile',
        'doOverrideHof'        => 'doOverrideHof',
        'markNotificationRead' => 'markNotificationRead',
        'confirmFamilyLink'    => 'confirmFamilyLink',
        'rejectFamilyLink'     => 'rejectFamilyLink',
        'deleteNotification'   => 'deleteNotification',
        'registerResident'     => 'registerResident',
        'resendCredentials'    => 'resendCredentials',
        'forceChangePassword'  => 'doForceChangePassword',
        'setupAccount'         => 'doSetupAccount',
    ];
    if (isset($postMap[$action])) {
        $action = $postMap[$action];
    }
}

if (isset($routes[$action])) {
    // ── Permission check ──────────────────────────────────────────────────────
    Security::requirePermission($action);
    ($routes[$action])();
} else {
    // Default: redirect logged-in users to their dashboard
    if (isset($_SESSION['user_id'])) {
        $roleMap = [
            'Admin'               => 'securityLogs',
            'Nutrition Officer II'=> 'reportValidation',
            'BNS Staff'           => 'bnsDashboard',
            'Mother'              => 'motherWizard',
        ];
        $dest = $roleMap[$_SESSION['role'] ?? ''] ?? 'home';
        header("Location: index.php?action={$dest}");
    } else {
        header('Location: index.php?action=login');
    }
    exit;
}
