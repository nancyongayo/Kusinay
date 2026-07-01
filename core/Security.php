<?php
/**
 * Security helper — CSRF tokens, permission checks, DLP utilities
 */
class Security {

    // ── CSRF ─────────────────────────────────────────────────────────────────

    public static function csrfToken(): string {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function csrfField(): string {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::csrfToken()) . '">';
    }

    public static function verifyCsrf(): void {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals(self::csrfToken(), $token)) {
            http_response_code(403);
            die('CSRF token mismatch. Please go back and try again.');
        }
    }

    // ── Role / Permission checks ──────────────────────────────────────────────

    /** Allowed roles per action key */
    private static array $permissions = [
        'securityLogs'         => ['Admin'],
        'userManagement'       => ['Admin'],
        'userActivity'         => ['Admin'],
        'reportValidation'     => ['Admin', 'Nutrition Officer II'],
        'reportDetail'         => ['Admin', 'Nutrition Officer II'],
        'approveReport'        => ['Admin', 'Nutrition Officer II'],
        'returnReport'         => ['Admin', 'Nutrition Officer II'],
        'bnsDashboard'         => ['Admin', 'BNS Staff'],
        'familyProfiles'       => ['Admin', 'BNS Staff'],
        'familyProfileForm'    => ['Admin', 'BNS Staff'],
        'saveFamilyProfile'    => ['Admin', 'BNS Staff'],
        'deleteFamilyProfile'  => ['Admin', 'BNS Staff'],
        'bnsValidationList'    => ['Admin', 'BNS Staff'],
        'bnsValidationDetail'  => ['Admin', 'BNS Staff'],
        'doValidateProfile'    => ['Admin', 'BNS Staff'],
        'doReturnProfile'      => ['Admin', 'BNS Staff'],
        'doOverrideHof'        => ['Admin', 'BNS Staff'],
        'dataEncoding'         => ['Admin', 'BNS Staff'],
        'assessmentForm'       => ['Admin', 'BNS Staff'],
        'saveAssessment'       => ['Admin', 'BNS Staff'],
        'formCReport'          => ['Admin', 'BNS Staff'],
        'p12Monitoring'        => ['Admin', 'BNS Staff'],
        'optResults'           => ['Admin', 'BNS Staff'],
        'pregnantMasterlist'   => ['Admin', 'BNS Staff'],
        'lactatingMasterlist'  => ['Admin', 'BNS Staff'],
        'seniorMasterlist'     => ['Admin', 'BNS Staff'],
        'accomplishmentReport' => ['Admin', 'BNS Staff'],
        'saveAccomplishment'   => ['Admin', 'BNS Staff'],
        'submitAccomplishment' => ['Admin', 'BNS Staff'],
        'nutritionEducationList'=> ['Admin', 'BNS Staff'],
        'nutritionEducationForm'=> ['Admin', 'BNS Staff'],
        'saveSession'          => ['Admin', 'BNS Staff'],
        'deleteSession'        => ['Admin', 'BNS Staff'],
        'recordAttendance'     => ['Admin', 'BNS Staff'],
        'saveAttendance'       => ['Admin', 'BNS Staff'],
        'startSession'         => ['Admin', 'BNS Staff'],
        'completeSession'      => ['Admin', 'BNS Staff'],
        'cancelSession'        => ['Admin', 'BNS Staff'],
        'home'                 => ['Admin', 'Mother'],
        'motherWizard'         => ['Admin', 'Mother'],
        'motherProfile'        => ['Admin', 'Mother'],
        'saveWizardDraft'      => ['Admin', 'Mother'],
        'submitWizardProfile'  => ['Admin', 'Mother'],
        'upcomingSessions'     => ['Admin', 'Mother'],
        'notifications'        => ['Admin', 'BNS Staff', 'Mother', 'Nutrition Officer II', 'Committee Chair on Health', 'Committee Secretary', 'Barangay Captain', 'Market Vendor'],
        'markNotificationRead' => ['Admin', 'BNS Staff', 'Mother', 'Nutrition Officer II', 'Committee Chair on Health', 'Committee Secretary', 'Barangay Captain', 'Market Vendor'],
        'deleteNotification'   => ['Admin', 'BNS Staff', 'Mother', 'Nutrition Officer II', 'Committee Chair on Health', 'Committee Secretary', 'Barangay Captain', 'Market Vendor'],
        'notificationBadge'    => ['Admin', 'BNS Staff', 'Mother', 'Nutrition Officer II', 'Committee Chair on Health', 'Committee Secretary', 'Barangay Captain', 'Market Vendor'],
        'confirmFamilyLink'    => ['Admin', 'Mother'],
        'rejectFamilyLink'     => ['Admin', 'Mother'],
        // BNS Resident Registration
        'registerResident'     => ['BNS Staff'],
        'listResidents'        => ['BNS Staff'],
        'resendCredentials'    => ['BNS Staff'],
        // Force password change — accessible to any authenticated role
        'forceChangePassword'  => ['Admin', 'BNS Staff', 'Mother', 'Nutrition Officer II', 'Committee Chair on Health', 'Committee Secretary', 'Barangay Captain', 'Market Vendor'],
        'doForceChangePassword'=> ['Admin', 'BNS Staff', 'Mother', 'Nutrition Officer II', 'Committee Chair on Health', 'Committee Secretary', 'Barangay Captain', 'Market Vendor'],
        // Process 12: Nutrition Risk (BNS)
        'affectedChildren'     => ['Admin', 'BNS Staff'],
        // Process 13: Feeding Program Planning (Committee Chair + Secretary)
        'committeeChairDashboard' => ['Admin', 'Committee Chair on Health'],
        'proposalForm'         => ['Admin', 'Committee Chair on Health'],
        'saveProposal'         => ['Admin', 'Committee Chair on Health'],
        'viewProposal'         => ['Admin', 'Committee Chair on Health', 'Committee Secretary', 'Barangay Captain', 'BNS Staff'],
        'submitProposal'       => ['Admin', 'Committee Chair on Health'],
        'deleteProposal'       => ['Admin', 'Committee Chair on Health'],
        'secretaryDashboard'   => ['Admin', 'Committee Secretary'],
        'minutesForm'          => ['Admin', 'Committee Secretary'],
        'saveMeetingMinutes'   => ['Admin', 'Committee Secretary'],
        // Process 14: Proposal Validation (Barangay Captain)
        'captainDashboard'     => ['Admin', 'Barangay Captain'],
        'validationForm'       => ['Admin', 'Barangay Captain'],
        'submitValidation'     => ['Admin', 'Barangay Captain'],
        // Market Vendor
        'marketVendorDashboard'=> ['Admin', 'Market Vendor'],
        'vendorProducts'       => ['Admin', 'Market Vendor'],
        'vendorGroceryLists'   => ['Admin', 'Market Vendor'],
        // Process 17: Recovery Validation (Nutrition Officer II + BNS read-only)
        'recoveryValidation'       => ['Admin', 'Nutrition Officer II'],
        'recoveryEligibleList'     => ['Admin', 'Nutrition Officer II'],
        'recoveryValidationForm'   => ['Admin', 'Nutrition Officer II'],
        'saveRecoveryValidation'   => ['Admin', 'Nutrition Officer II'],
        'recoveryValidationList'   => ['Admin', 'Nutrition Officer II'],
        'recoveryValidationDetail' => ['Admin', 'Nutrition Officer II'],
        'recoveryStatistics'       => ['Admin', 'Nutrition Officer II'],
        'deleteRecoveryValidation' => ['Admin', 'Nutrition Officer II'],
        'bnsRecoveryStatus'        => ['Admin', 'BNS Staff'],
        
        // Process 15-16: Feeding Program Execution & Attendance
        'feedingProgramList'       => ['Admin', 'BNS Staff'],
        'feedingSessions'          => ['Admin', 'BNS Staff'],
        'feedingSessionForm'       => ['Admin', 'BNS Staff'],
        'saveFeedingSession'       => ['Admin', 'BNS Staff'],
        'deleteFeedingSession'     => ['Admin', 'BNS Staff'],
        'bulkSessionForm'          => ['Admin', 'BNS Staff'],
        'saveBulkSessions'         => ['Admin', 'BNS Staff'],
        'sessionAttendance'        => ['Admin', 'BNS Staff'],
        'feedingAttendance'        => ['Admin', 'BNS Staff'],
        'saveFeedingAttendance'    => ['Admin', 'BNS Staff'],
        'deleteFeedingAttendance'  => ['Admin', 'BNS Staff'],
        'attendanceReport'         => ['Admin', 'BNS Staff'],
        'qrScanner'                => ['Admin', 'BNS Staff'],
        'sessionQRCode'            => ['Admin', 'BNS Staff'],
        'generateQRCodes'          => ['Admin', 'BNS Staff'],
        'markQRAttendance'         => ['Admin', 'BNS Staff'],
        'sessionRSVPList'          => ['Admin', 'BNS Staff'],
        'respondToRSVP'            => ['Admin', 'BNS Staff', 'Mother'],
        'markAttendance'           => ['Admin', 'BNS Staff'],
        
        // Parent/Mother Feeding Program Dashboard
        'feedingDashboard'         => ['Admin', 'Mother'],
        'feedingAttendanceHistory' => ['Admin', 'Mother'],
        
        // Process 18: Meal Plans (BNS creates, Mother views)
        'bnsMealPlansList'         => ['Admin', 'BNS Staff'],
        'bnsMealPlanView'          => ['Admin', 'BNS Staff'],
        'bnsMealPlanForm'          => ['Admin', 'BNS Staff'],
        'saveMealPlan'             => ['Admin', 'BNS Staff'],
        'addMealItem'              => ['Admin', 'BNS Staff'],
        'deleteMealItem'           => ['Admin', 'BNS Staff'],
        'deleteMealPlan'           => ['Admin', 'BNS Staff'],
        'mealPlansList'            => ['Admin', 'Mother'],
        'mealPlanView'             => ['Admin', 'Mother'],
        'markMealPlanCompleted'    => ['Admin', 'Mother'],
        'markMealConsumed'         => ['Admin', 'Mother'],
        'dietPlansList'            => ['Admin', 'BNS Staff', 'Mother'],
        'viewDietPlan'             => ['Admin', 'BNS Staff', 'Mother'],
        
        // Process 19: Grocery Lists (Mother)
        'groceryMode'              => ['Admin', 'Mother'],
        'supermarket'              => ['Admin', 'Mother'],
        'wetMarket'                => ['Admin', 'Mother'],
        'groceryLists'             => ['Admin', 'Mother'],
        'groceryListForm'          => ['Admin', 'Mother'],
        'saveGroceryList'          => ['Admin', 'Mother'],
        'generateFromMealPlan'     => ['Admin', 'Mother'],
        'shopFromMealPlan'         => ['Admin', 'Mother'],
        'shopGroceryListOnline'    => ['Admin', 'Mother'],
        'addGroceryItem'           => ['Admin', 'Mother'],
        'updateGroceryItem'        => ['Admin', 'Mother'],
        'markItemPurchased'        => ['Admin', 'Mother'],
        'unmarkItemPurchased'      => ['Admin', 'Mother'],
        'deleteGroceryItem'        => ['Admin', 'Mother'],
        'deleteGroceryList'        => ['Admin', 'Mother'],
        'markListCompleted'        => ['Admin', 'Mother'],
        
        // Process 20: Market Vendor - Selling Goods
        'vendorProductForm'        => ['Admin', 'Market Vendor'],
        'saveProduct'              => ['Admin', 'Market Vendor'],
        'deleteProduct'            => ['Admin', 'Market Vendor'],
        'toggleProductAvailability'=> ['Admin', 'Market Vendor'],
        'vendorOrders'             => ['Admin', 'Market Vendor'],
        'vendorOrderDetail'        => ['Admin', 'Market Vendor'],
        'updateOrderStatus'        => ['Admin', 'Market Vendor'],
        'vendorSalesReports'       => ['Admin', 'Market Vendor'],
        
        // Process 21-22: Pantry Management (Mother)
        'pantry'                   => ['Admin', 'Mother'],
        'addPantryItem'            => ['Admin', 'Mother'],
        'updatePantryQuantity'     => ['Admin', 'Mother'],
        'deletePantryItem'         => ['Admin', 'Mother'],
        'consumePantryItem'        => ['Admin', 'Mother'],
        'pantryHistory'            => ['Admin', 'Mother'],
        
        // Shopping Cart & Checkout (Process 19 - Online Purchase)
        'addToCart'                => ['Admin', 'Mother'],
        'viewCart'                 => ['Admin', 'Mother'],
        'updateCartQuantity'       => ['Admin', 'Mother'],
        'removeCartItem'           => ['Admin', 'Mother'],
        'getCartCount'             => ['Admin', 'Mother'],
        'showCheckout'             => ['Admin', 'Mother'],
        'processCheckout'          => ['Admin', 'Mother'],
        'paymentSuccess'           => ['Admin', 'Mother'],
        'paymentCancelled'         => ['Admin', 'Mother'],
        'orderConfirmation'        => ['Admin', 'Mother'],
        'myOrders'                 => ['Admin', 'Mother'],
        
        // Additional missing routes
        'p12MonitoringAll'         => ['Admin', 'BNS Staff'],
        'saveFollowUp'             => ['Admin', 'BNS Staff'],
        'previewAssessment'        => ['Admin', 'BNS Staff'],
        'uploadAttachment'         => ['Admin', 'BNS Staff'],
        'deleteAttachment'         => ['Admin', 'BNS Staff'],
        'chairMinutesList'         => ['Admin', 'Committee Chair on Health'],
        'reviewAffectedChildren'   => ['Admin', 'BNS Staff'],
        'printProposal'            => ['Admin', 'Committee Chair on Health', 'Committee Secretary', 'Barangay Captain'],
        'printMinutes'             => ['Admin', 'Committee Secretary'],
        'viewMinutes'              => ['Admin', 'Committee Secretary', 'Committee Chair on Health'],
        'markMinutesReviewed'      => ['Admin', 'Committee Secretary'],
        'rsvpSession'              => ['Admin', 'Mother'],
        'sessionRsvpList'          => ['Admin', 'BNS Staff'],
        'uploadMaterial'           => ['Admin', 'BNS Staff'],
        'downloadMaterial'         => ['Admin', 'BNS Staff', 'Mother'],
        'deleteMaterial'           => ['Admin', 'BNS Staff'],
        'bnsSettings'              => ['Admin', 'BNS Staff'],
        'saveBnsLocation'          => ['Admin', 'BNS Staff'],
        'searchUsers'              => ['Admin', 'Mother'],
    ];

    /**
     * Check if the current session user has permission for the given action.
     * Public routes (login, register, etc.) are skipped entirely.
     * Redirects to login or shows 403 if not authorized.
     */
    public static function requirePermission(string $action): void {
        // These routes are always public — never require auth
        $publicRoutes = [
            'landing', 'login', 'register', 'verify', 'verifyOtp',
            'forgotPassword', 'resetPassword', 'doLogin', 'doRegister',
            'doVerifyOtp', 'doForgotPwd', 'doResetPwd', 'resendOtp',
            'roleSelection', 'doRoleSelection', 'saveRoleSelection',
            'setupAccount', 'doSetupAccount',
            // QR Attendance - Public access for parents without accounts
            'attendViaQR', 'submitAttendanceViaQR',
        ];
        if (in_array($action, $publicRoutes, true)) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) session_start();

        // Not logged in — send to login
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $role = $_SESSION['role'] ?? '';

        // No restriction defined for this action — allow
        if (!isset(self::$permissions[$action])) {
            return;
        }

        // Role not in allowed list — show 403
        if (!in_array($role, self::$permissions[$action], true)) {
            http_response_code(403);
            include __DIR__ . '/../app/views/shared/403.php';
            exit;
        }
    }

    // ── Data Classification ───────────────────────────────────────────────────

    /**
     * Returns an HTML badge for a data classification level.
     * Levels: PUBLIC, INTERNAL, CONFIDENTIAL, RESTRICTED
     */
    public static function classificationBadge(string $level): string {
        $map = [
            'PUBLIC'       => ['bg' => '#6c757d', 'icon' => '🌐'],
            'INTERNAL'     => ['bg' => '#0d6efd', 'icon' => '🏢'],
            'CONFIDENTIAL' => ['bg' => '#fd7e14', 'icon' => '🔒'],
            'RESTRICTED'   => ['bg' => '#dc3545', 'icon' => '🚫'],
        ];
        $level = strtoupper($level);
        $cfg   = $map[$level] ?? $map['INTERNAL'];
        return sprintf(
            '<span class="data-classification-badge" style="background:%s;color:#fff;font-size:.65rem;font-weight:700;padding:.2rem .55rem;border-radius:4px;letter-spacing:.05em;user-select:none">%s %s</span>',
            $cfg['bg'], $cfg['icon'], $level
        );
    }

    // ── DLP: CSS/JS for print blocking and sensitive element protection ─────────

    public static function dlpHeadAssets(): string {
        return ''; // Watermark disabled
        // Build print watermark SVG using session name — baked into CSS at render time
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userName = htmlspecialchars($_SESSION['user_name'] ?? 'KusiNay User', ENT_QUOTES);

        // SVG tile: two lines — label + user name, rotated -30deg, tiled via background-repeat
        // Encoded as a data URI so it works in all browsers without external files
        $svgLine1 = '⚠ CONFIDENTIAL | KusiNay';
        $svgLine2 = $userName;

        $svg = rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" width="320" height="130">'
            . '<text x="160" y="52" font-family="Arial,sans-serif" font-size="15" font-weight="bold"'
            . ' fill="#1a1a1a" opacity="0.18" text-anchor="middle" transform="rotate(-30,160,65)">'
            . htmlspecialchars($svgLine1, ENT_XML1) . '</text>'
            . '<text x="160" y="76" font-family="Arial,sans-serif" font-size="13"'
            . ' fill="#1a1a1a" opacity="0.18" text-anchor="middle" transform="rotate(-30,160,65)">'
            . htmlspecialchars($svgLine2, ENT_XML1) . '</text>'
            . '</svg>'
        );

        return <<<HTML
<style>
/* DLP: disable text selection on sensitive data */
.dlp-no-select { user-select: none; -webkit-user-select: none; }

/* DLP: screen watermark overlay — canvas-based, pointer-events off */
#dlp-watermark {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    pointer-events: none;
    z-index: 99998;
    overflow: hidden;
}
#dlp-watermark canvas {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
}

/* DLP: print watermark — SVG tiled background, reliable across all browsers */
@media print {
    .dlp-no-print { display: none !important; }
    #dlp-watermark { display: none !important; } /* hide canvas on print, use CSS instead */

    body::before {
        content: '';
        display: block;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-image: url("data:image/svg+xml,{$svg}");
        background-repeat: repeat;
        background-size: 320px 130px;
        pointer-events: none;
        z-index: 99999;
    }

    body::after {
        content: "⚠ CONFIDENTIAL | KusiNay — Unauthorized printing or distribution is prohibited.";
        display: block;
        font-size: .8rem;
        font-weight: bold;
        color: #c00;
        text-align: center;
        padding: .5rem;
        border-top: 2px solid #c00;
        margin-top: 1rem;
    }
}
</style>
HTML;
    }

    /**
     * JS snippet — blocks Ctrl+P, right-click on sensitive elements, and renders
     * a diagonal canvas watermark on screen.
     * On print, the CSS SVG watermark in dlpHeadAssets() takes over.
     * Both show "⚠ CONFIDENTIAL | KusiNay" + the logged-in user's name,
     * making any screenshot or printout traceable.
     */
    public static function dlpBodyScript(bool $blockCopy = false): string {
        return ''; // Watermark disabled
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userName = htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES);

        $copyBlock = $blockCopy ? "
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === 'c' || e.key === 'C')) {
                e.preventDefault();
                showDlpWarning('Copying is restricted on this page.');
            }
        });
        document.addEventListener('selectstart', function(e) {
            if (e.target.closest('.dlp-no-select')) e.preventDefault();
        });" : '';

        return <<<HTML
<!-- DLP watermark container (screen only) -->
<div id="dlp-watermark" aria-hidden="true"><canvas id="dlp-wm-canvas"></canvas></div>

<script>
(function() {
    // ── Screen Watermark (canvas) ─────────────────────────────────────────────
    var WM_LINE1 = '\u26a0 CONFIDENTIAL | KusiNay';
    var WM_LINE2 = '{$userName}';

    function drawWatermark() {
        var canvas = document.getElementById('dlp-wm-canvas');
        if (!canvas) return;
        var W = window.innerWidth;
        var H = window.innerHeight;
        canvas.width  = W;
        canvas.height = H;
        var ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, W, H);

        ctx.save();
        ctx.globalAlpha = 0.09;
        ctx.fillStyle   = '#1a1a1a';
        ctx.textAlign   = 'center';

        var tileW = 320;
        var tileH = 130;
        ctx.translate(W / 2, H / 2);
        ctx.rotate(-Math.PI / 6); // -30 degrees
        ctx.translate(-W / 2, -H / 2);

        for (var y = -tileH * 2; y < H + tileH * 2; y += tileH) {
            for (var x = -tileW; x < W + tileW; x += tileW) {
                var cx = x + tileW / 2;
                var cy = y + tileH / 2;
                ctx.font = 'bold 15px Arial, sans-serif';
                ctx.fillText(WM_LINE1, cx, cy - 8);
                if (WM_LINE2) {
                    ctx.font = '13px Arial, sans-serif';
                    ctx.fillText(WM_LINE2, cx, cy + 14);
                }
            }
        }
        ctx.restore();
    }

    drawWatermark();
    window.addEventListener('resize', drawWatermark);

    // ── Block Ctrl+P / Cmd+P ─────────────────────────────────────────────────
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            showDlpWarning('Printing is restricted. Contact your administrator.');
        }
        if (e.key === 'PrintScreen') {
            showDlpWarning('This page is watermarked. Screenshots are traceable.');
        }
    });

    // Block right-click everywhere on the page
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        showDlpWarning('Right-click is disabled on this page.');
    });

    {$copyBlock}

    function showDlpWarning(msg) {
        var el = document.getElementById('dlp-toast');
        if (!el) {
            el = document.createElement('div');
            el.id = 'dlp-toast';
            el.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;background:#dc3545;color:#fff;padding:.75rem 1.25rem;border-radius:.5rem;font-size:.88rem;font-weight:600;z-index:99999;box-shadow:0 4px 12px rgba(0,0,0,.25);display:none';
            document.body.appendChild(el);
        }
        el.textContent = '\uD83D\uDEAB ' + msg;
        el.style.display = 'block';
        clearTimeout(el._t);
        el._t = setTimeout(function(){ el.style.display = 'none'; }, 3500);
    }
})();
</script>
HTML;
    }
}
} // End class guard