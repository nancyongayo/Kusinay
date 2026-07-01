<?php
/**
 * BNS - QR Code Scanner for Attendance
 * Allows quick check-in by scanning QR codes
 */
$pageTitle = 'QR Scanner - ' . ($session['activity_name'] ?? 'Feeding Session');
$activeNav = 'feeding_program';
include __DIR__ . '/../templates/bns_layout.php';
?>

<div class="container-fluid py-4">
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
                    <li class="breadcrumb-item active" style="color: var(--kn-dark);">QR Scanner</li>
                </ol>
            </nav>

            <h2 class="mb-2" style="color: var(--kn-dark); font-weight: 700;">
                <i class="bi bi-qr-code-scan me-2" style="color: #5A7038;"></i>
                QR Code Attendance Scanner
            </h2>
            <p class="text-muted">
                <i class="bi bi-calendar-event me-1" style="color: #C4722A;"></i>
                <?= date('F j, Y', strtotime($session['session_date'])) ?>
                <span class="mx-2">•</span>
                <i class="bi bi-geo-alt-fill me-1" style="color: #5A7038;"></i>
                <?= htmlspecialchars($session['purok_barangay']) ?>
            </p>
        </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-success alert-dismissible fade show" id="flashAlert">
            <i class="bi bi-check-circle me-2"></i>
            <?= htmlspecialchars($_SESSION['flash']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" id="flashAlert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Scanner Card -->
        <div class="col-lg-6">
            <div class="card border-0" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.08);">
                <div class="card-header" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15); border-radius: 16px 16px 0 0 !important; padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: var(--kn-dark); font-weight: 700;">
                        <i class="bi bi-camera-fill me-2" style="color: #5A7038;"></i>
                        Scan QR Code
                    </h5>
                </div>
                <div class="card-body text-center p-4">
                    <div id="qr-reader" style="width: 100%; max-width: 500px; margin: 0 auto; border-radius: 12px; overflow: hidden;"></div>
                    
                    <div class="mt-3">
                        <button id="startScanBtn" class="btn" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); color: #fff; border: none; padding: .6rem 1.5rem; border-radius: 10px; font-weight: 600;">
                            <i class="bi bi-play-fill me-1"></i>
                            Start Scanner
                        </button>
                        <button id="stopScanBtn" class="btn d-none" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: #fff; border: none; padding: .6rem 1.5rem; border-radius: 10px; font-weight: 600;">
                            <i class="bi bi-stop-fill me-1"></i>
                            Stop Scanner
                        </button>
                    </div>

                    <div id="scanStatus" class="mt-3"></div>
                </div>
            </div>

            <!-- Manual Entry Option -->
            <div class="card border-0 mt-3" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.08);">
                <div class="card-body text-center p-4">
                    <p class="mb-2" style="color: var(--kn-dark); font-weight: 600;">Can't scan QR code?</p>
                    <a href="index.php?action=feedingAttendance&session_id=<?= $session['session_id'] ?>" 
                       class="btn" style="background: transparent; color: #5A7038; border: 2px solid #5A7038; padding: .5rem 1.2rem; border-radius: 10px; font-weight: 600;">
                        <i class="bi bi-pencil-square me-1"></i>
                        Manual Entry
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Scans -->
        <div class="col-lg-6">
            <div class="card border-0" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.08);">
                <div class="card-header" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15); border-radius: 16px 16px 0 0 !important; padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: var(--kn-dark); font-weight: 700;">
                        <i class="bi bi-clock-history me-2" style="color: #5A7038;"></i>
                        Recent Check-ins
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div id="recentScans" class="list-group" style="max-height: 500px; overflow-y: auto;">
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-2">No check-ins yet. Start scanning QR codes.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card border-0 mt-3" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.08);">
                <div class="card-body p-3">
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-3" style="background: linear-gradient(135deg, rgba(90,112,56,.12) 0%, rgba(90,112,56,.08) 100%); border-radius: 10px;">
                                <h3 class="mb-0" style="color: #5A7038; font-weight: 700;" id="todayCount">0</h3>
                                <small style="color: var(--kn-dark); font-weight: 600;">Today's Scans</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3" style="background: linear-gradient(135deg, rgba(196,114,42,.12) 0%, rgba(196,114,42,.08) 100%); border-radius: 10px;">
                                <h3 class="mb-0" style="color: #C4722A; font-weight: 700;" id="totalCount"><?= $stats['present_count'] ?? 0 ?></h3>
                                <small style="color: var(--kn-dark); font-weight: 600;">Total Present</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="index.php?action=feedingAttendance&session_id=<?= $session['session_id'] ?>" 
           class="btn" style="background: transparent; color: var(--kn-dark); border: 2px solid #e5e7eb; padding: .6rem 1.5rem; border-radius: 10px; font-weight: 600;">
            <i class="bi bi-list-check me-1"></i>
            View Full Attendance
        </a>
    </div>
</div>

<!-- Include html5-qrcode library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrCode;
let scanCount = 0;

// Initialize scanner when start button is clicked
document.getElementById('startScanBtn').addEventListener('click', function() {
    startScanner();
});

document.getElementById('stopScanBtn').addEventListener('click', function() {
    stopScanner();
});

function startScanner() {
    html5QrCode = new Html5Qrcode("qr-reader");
    
    html5QrCode.start(
        { facingMode: "environment" }, // Use back camera
        {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        },
        onScanSuccess,
        onScanError
    ).then(() => {
        document.getElementById('startScanBtn').classList.add('d-none');
        document.getElementById('stopScanBtn').classList.remove('d-none');
        showStatus('Scanner is active. Point camera at QR code.', 'info');
    }).catch(err => {
        showStatus('Unable to start camera. Please check permissions.', 'danger');
        console.error('Camera error:', err);
    });
}

function stopScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            document.getElementById('startScanBtn').classList.remove('d-none');
            document.getElementById('stopScanBtn').classList.add('d-none');
            showStatus('Scanner stopped.', 'secondary');
        }).catch(err => {
            console.error('Error stopping scanner:', err);
        });
    }
}

function onScanSuccess(decodedText, decodedResult) {
    // Parse QR code data
    try {
        const data = JSON.parse(decodedText);
        
        // Validate session_id matches
        if (data.session_id != <?= $session['session_id'] ?>) {
            showStatus('Invalid QR code. This code is for a different session.', 'warning');
            return;
        }
        
        // Submit attendance
        submitQRAttendance(data);
        
    } catch (e) {
        showStatus('Invalid QR code format.', 'danger');
        console.error('QR parse error:', e);
    }
}

function onScanError(errorMessage) {
    // Ignore common scanning errors (too many logs)
}

function submitQRAttendance(data) {
    // Stop scanner temporarily to prevent duplicate scans
    if (html5QrCode) {
        html5QrCode.pause(true);
    }
    
    showStatus('Processing...', 'info');
    
    // Send AJAX request to mark attendance
    fetch('index.php?action=markQRAttendance', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            session_id: data.session_id,
            participant_id: data.participant_id,
            participant_name: data.participant_name,
            csrf_token: '<?= $_SESSION['csrf_token'] ?? '' ?>'
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showStatus(`✓ ${result.participant_name} checked in successfully!`, 'success');
            addRecentScan(result.participant_name, result.time);
            scanCount++;
            document.getElementById('todayCount').textContent = scanCount;
            document.getElementById('totalCount').textContent = parseInt(document.getElementById('totalCount').textContent) + 1;
            
            // Play success sound (optional)
            playSuccessSound();
            
            // Resume scanner after 2 seconds
            setTimeout(() => {
                if (html5QrCode) {
                    html5QrCode.resume();
                    showStatus('Ready to scan next QR code.', 'info');
                }
            }, 2000);
        } else {
            showStatus(result.message || 'Failed to record attendance.', 'danger');
            // Resume scanner after 2 seconds
            setTimeout(() => {
                if (html5QrCode) {
                    html5QrCode.resume();
                }
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showStatus('Network error. Please try again.', 'danger');
        // Resume scanner after 2 seconds
        setTimeout(() => {
            if (html5QrCode) {
                html5QrCode.resume();
            }
        }, 2000);
    });
}

function showStatus(message, type) {
    const statusDiv = document.getElementById('scanStatus');
    const alertClass = `alert-${type}`;
    statusDiv.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show">
            ${message}
        </div>
    `;
    
    // Auto-hide after 3 seconds for non-error messages
    if (type === 'success' || type === 'info') {
        setTimeout(() => {
            statusDiv.innerHTML = '';
        }, 3000);
    }
}

function addRecentScan(name, time) {
    const recentScansDiv = document.getElementById('recentScans');
    
    // Remove "no check-ins" message if present
    if (recentScansDiv.querySelector('.text-muted')) {
        recentScansDiv.innerHTML = '';
    }
    
    const scanItem = document.createElement('div');
    scanItem.className = 'list-group-item border-0 mb-2';
    scanItem.style = 'background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-radius: 10px;';
    scanItem.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-person-check-fill me-2" style="color: #5A7038;"></i>
                <strong>${name}</strong>
            </div>
            <small class="text-muted">${time}</small>
        </div>
    `;
    
    recentScansDiv.insertBefore(scanItem, recentScansDiv.firstChild);
    
    // Keep only last 10 scans visible
    while (recentScansDiv.children.length > 10) {
        recentScansDiv.removeChild(recentScansDiv.lastChild);
    }
}

function playSuccessSound() {
    // Simple beep using Web Audio API
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.2);
    } catch (e) {
        // Ignore if audio fails
    }
}

// Cleanup when leaving page
window.addEventListener('beforeunload', function() {
    if (html5QrCode) {
        html5QrCode.stop();
    }
});
</script>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
