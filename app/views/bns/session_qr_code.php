<?php
/**
 * BNS - Single Session QR Code
 * One QR code for the entire session - participants scan to mark attendance
 */
$pageTitle = 'Session QR Code - ' . ($session['activity_name'] ?? 'Feeding Session');
$activeNav = 'feeding_program';
include __DIR__ . '/../templates/bns_layout.php';
?>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}

.session-qr-container {
    background: white;
    border: 3px solid #5A7038;
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    max-width: 600px;
    margin: 0 auto;
}

.qr-display {
    background: white;
    padding: 20px;
    border-radius: 15px;
    display: inline-block;
    margin: 1rem 0;
}
</style>

<div class="container-fluid py-4 no-print">
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.php?action=feedingProgramList" style="color: #5A7038; text-decoration: none; font-weight: 500;">Feeding Programs</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="index.php?action=feedingSessions&proposal_id=<?= $session['proposal_id'] ?>" style="color: #5A7038; text-decoration: none; font-weight: 500;">
                            Sessions
                        </a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--kn-dark);">Session QR Code</li>
                </ol>
            </nav>

            <h2 class="mb-2" style="color: var(--kn-dark); font-weight: 700;">
                <i class="bi bi-qr-code me-2" style="color: #5A7038;"></i>
                Session QR Code
            </h2>
            <p class="text-muted">
                One QR code for all participants to scan
            </p>
        </div>
        <div class="col-auto">
            <button onclick="window.print()" class="btn" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); color: #fff; border: none; padding: .6rem 1.2rem; border-radius: 10px; font-weight: 600;">
                <i class="bi bi-printer me-1"></i>
                Print QR Code
            </button>
            <button onclick="downloadQRCode()" class="btn ms-2" style="background: linear-gradient(135deg, #C4722A 0%, #A85F22 100%); color: #fff; border: none; padding: .6rem 1.2rem; border-radius: 10px; font-weight: 600;">
                <i class="bi bi-download me-1"></i>
                Download
            </button>
        </div>
    </div>

    <div class="alert" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-left: 4px solid #5A7038; border-radius: 12px; color: var(--kn-dark);">
        <i class="bi bi-info-circle me-2" style="color: #5A7038;"></i>
        <strong>How to use:</strong>
        <ul class="mb-0 mt-2">
            <li>Print or display this QR code at the feeding session venue</li>
            <li>Participants scan the QR code with their phone</li>
            <li>They enter their name and click submit</li>
            <li>Attendance is recorded instantly!</li>
        </ul>
    </div>
</div>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="session-qr-container">
                <h3 style="color: #5A7038; font-weight: 700; margin-bottom: 1rem;">
                    <?= htmlspecialchars($session['activity_name']) ?>
                </h3>
                
                <p style="color: var(--kn-dark); font-size: 1.1rem; margin-bottom: 0.5rem;">
                    <i class="bi bi-calendar-event me-2" style="color: #C4722A;"></i>
                    <?= date('F j, Y', strtotime($session['session_date'])) ?>
                </p>
                
                <p style="color: var(--kn-muted); margin-bottom: 1.5rem;">
                    <i class="bi bi-geo-alt-fill me-2" style="color: #5A7038;"></i>
                    <?= htmlspecialchars($session['purok_barangay']) ?>
                </p>

                <div class="qr-display">
                    <?php
                    // Get the server's IP address for mobile access
                    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                    
                    // Use actual host from request, or fallback to local IP
                    $host = $_SERVER['HTTP_HOST'];
                    
                    // If accessing via localhost, try to get actual IP for QR code
                    if ($host === 'localhost' || strpos($host, '127.0.0.1') !== false) {
                        // Try to get server's local IP address
                        $localIP = $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname());
                        
                        // If that doesn't work, show a warning
                        if ($localIP === '::1' || $localIP === '127.0.0.1' || strpos($localIP, '127.0') === 0) {
                            $localIP = 'YOUR-COMPUTER-IP';
                            $needsIPConfig = true;
                        } else {
                            $host = $localIP;
                            $needsIPConfig = false;
                        }
                    } else {
                        $needsIPConfig = false;
                    }
                    
                    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
                    $baseUrl = $protocol . '://' . $host . $scriptPath;
                    
                    $attendanceUrl = $baseUrl . '/index.php?action=attendViaQR&session_id=' . $session['session_id'];
                    
                    // Try multiple QR code APIs for reliability
                    $qrApis = [
                        "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=" . urlencode($attendanceUrl),
                        "https://chart.googleapis.com/chart?chs=400x400&cht=qr&chl=" . urlencode($attendanceUrl) . "&choe=UTF-8",
                    ];
                    ?>
                    
                    <?php if (isset($needsIPConfig) && $needsIPConfig): ?>
                        <!-- Warning removed - simplified for end users -->
                    <?php endif; ?>
                    
                    <!-- QR Code Image -->
                    <img src="<?= htmlspecialchars($qrApis[0]) ?>" 
                         alt="Session QR Code" 
                         id="sessionQRCode"
                         onerror="handleQRError(this)"
                         style="max-width: 100%; height: auto; border: 3px solid #5A7038; border-radius: 10px;"
                         crossorigin="anonymous">
                    
                    <!-- Debug info (remove in production) -->
                    <div class="mt-2 no-print" style="font-size: 0.75rem; color: #6b7280;">
                        <details>
                            <summary style="cursor: pointer;">Troubleshooting Info</summary>
                            <div class="mt-2 p-2" style="background: #f3f4f6; border-radius: 8px; text-align: left;">
                                <strong>Current Host:</strong> <?= htmlspecialchars($_SERVER['HTTP_HOST']) ?><br>
                                <strong>Server Address:</strong> <?= htmlspecialchars($_SERVER['SERVER_ADDR'] ?? 'Unknown') ?><br>
                                <strong>Attendance URL:</strong><br>
                                <code style="word-break: break-all; font-size: 0.7rem;"><?= htmlspecialchars($attendanceUrl) ?></code>
                                <br><br>
                                <strong>QR API URL:</strong><br>
                                <code style="word-break: break-all; font-size: 0.7rem;"><?= htmlspecialchars($qrApis[0]) ?></code>
                            </div>
                        </details>
                    </div>
                </div>
                
                <script>
                let qrAttempt = 0;
                const qrApis = <?= json_encode($qrApis) ?>;
                
                function handleQRError(img) {
                    qrAttempt++;
                    if (qrAttempt < qrApis.length) {
                        console.log('Trying alternative QR API:', qrApis[qrAttempt]);
                        img.src = qrApis[qrAttempt];
                    } else {
                        // All APIs failed, show manual entry option
                        img.style.display = 'none';
                        const container = img.parentElement;
                        container.innerHTML += `
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Unable to generate QR code automatically.<br>
                                <strong>Alternative:</strong> Share the link below with participants.
                            </div>
                        `;
                    }
                }
                </script>

                <div class="mt-3 p-3" style="background: rgba(90,112,56,.08); border-radius: 12px;">
                    <h5 style="color: var(--kn-dark); font-weight: 700; margin-bottom: 1rem;">
                        <i class="bi bi-phone me-2"></i>
                        Instructions for Participants:
                    </h5>
                    <ol class="text-start" style="color: var(--kn-dark);">
                        <li>Open camera app on your phone</li>
                        <li>Point camera at this QR code</li>
                        <li>Tap the notification/link that appears</li>
                        <li>Enter your name</li>
                        <li>Click "Mark Present"</li>
                        <li>Done! Your attendance is recorded.</li>
                    </ol>
                </div>

                <div class="mt-3 no-print">
                    <p class="text-muted mb-2">Or share this link:</p>
                    <div class="input-group">
                        <input type="text" class="form-control" id="sessionLink" 
                               value="<?= htmlspecialchars($attendanceUrl) ?>" 
                               readonly style="background: white; border: 2px solid #e5e7eb; border-radius: 10px 0 0 10px; padding: 0.6rem;">
                        <button class="btn" onclick="copyLink()" 
                                style="background: #5A7038; color: white; border: none; border-radius: 0 10px 10px 0; padding: 0.6rem 1.2rem;">
                            <i class="bi bi-clipboard me-1"></i>
                            Copy
                        </button>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 no-print">
                <a href="index.php?action=sessionRSVPList&session_id=<?= $session['session_id'] ?>" 
                   class="btn me-2" style="background: linear-gradient(135deg, #C4722A 0%, #A85F22 100%); color: #fff; border: none; padding: .6rem 1.5rem; border-radius: 10px; font-weight: 600;">
                    <i class="bi bi-list-check me-1"></i>
                    View Attendance List
                </a>
                <a href="index.php?action=feedingSessions&proposal_id=<?= $session['proposal_id'] ?>" 
                   class="btn" style="background: transparent; color: var(--kn-dark); border: 2px solid #e5e7eb; padding: .6rem 1.5rem; border-radius: 10px; font-weight: 600;">
                    <i class="bi bi-arrow-left me-1"></i>
                    Back to Sessions
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function downloadQRCode() {
    const img = document.getElementById('sessionQRCode');
    const link = document.createElement('a');
    link.href = img.src;
    link.download = 'Session_QR_Code_<?= $session['session_id'] ?>.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function copyLink() {
    const linkInput = document.getElementById('sessionLink');
    linkInput.select();
    linkInput.setSelectionRange(0, 99999); // For mobile
    
    navigator.clipboard.writeText(linkInput.value).then(() => {
        // Show copied feedback
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Copied!';
        btn.style.background = '#10b981';
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.background = '#5A7038';
        }, 2000);
    });
}
</script>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
