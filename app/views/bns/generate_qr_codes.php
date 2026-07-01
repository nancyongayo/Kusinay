<?php
/**
 * BNS - Generate QR Codes for Participants
 * Creates printable QR codes for feeding program participants
 */
$pageTitle = 'Generate QR Codes - ' . ($session['activity_name'] ?? 'Feeding Session');
$activeNav = 'feeding_program';
include __DIR__ . '/../templates/bns_layout.php';
?>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    .qr-card {
        page-break-inside: avoid;
        break-inside: avoid;
    }
    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}

.qr-card {
    border: 2px solid #5A7038;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    background: white;
}

.qr-code-container {
    background: white;
    padding: 10px;
    border-radius: 8px;
    display: inline-block;
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
                    <li class="breadcrumb-item active" style="color: var(--kn-dark);">Generate QR Codes</li>
                </ol>
            </nav>

            <h2 class="mb-2" style="color: var(--kn-dark); font-weight: 700;">
                <i class="bi bi-qr-code me-2" style="color: #5A7038;"></i>
                Generate Participant QR Codes
            </h2>
            <p class="text-muted">
                Print or share these QR codes with participants for quick check-in
            </p>
        </div>
        <div class="col-auto">
            <button onclick="window.print()" class="btn" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); color: #fff; border: none; padding: .6rem 1.2rem; border-radius: 10px; font-weight: 600;">
                <i class="bi bi-printer me-1"></i>
                Print All QR Codes
            </button>
            <button onclick="downloadAllQRCodes()" class="btn ms-2" style="background: linear-gradient(135deg, #C4722A 0%, #A85F22 100%); color: #fff; border: none; padding: .6rem 1.2rem; border-radius: 10px; font-weight: 600;">
                <i class="bi bi-download me-1"></i>
                Download All
            </button>
        </div>
    </div>

    <div class="alert" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-left: 4px solid #5A7038; border-radius: 12px; color: var(--kn-dark);">
        <i class="bi bi-info-circle me-2" style="color: #5A7038;"></i>
        <strong>How to use:</strong> Print these QR codes and distribute to participants. They can scan their QR code at the feeding session for instant check-in.
    </div>
</div>

<div class="container-fluid py-4">
    <div class="row g-3">
        <?php if (empty($participants)): ?>
            <div class="col-12 no-print">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    No participants found. Please add participants to the feeding session first.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($participants as $participant): ?>
                <?php
                // Generate QR data (JSON format)
                $qrData = json_encode([
                    'session_id' => $session['session_id'],
                    'participant_id' => $participant['participant_id'] ?? $participant['member_id'],
                    'participant_name' => trim(($participant['first_name'] ?? '') . ' ' . ($participant['last_name'] ?? '') . ($participant['name_of_client'] ?? '')),
                    'session_name' => $session['activity_name']
                ]);
                
                // URL encode for QR code API
                $qrDataEncoded = urlencode($qrData);
                ?>
                
                <div class="col-md-4 col-lg-3">
                    <div class="qr-card">
                        <div class="qr-code-container">
                            <!-- Using Google Charts QR Code API (free) -->
                            <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=<?= $qrDataEncoded ?>&choe=UTF-8" 
                                 alt="QR Code" 
                                 class="qr-code-image"
                                 data-participant="<?= htmlspecialchars($participant['participant_id'] ?? $participant['member_id']) ?>"
                                 style="max-width: 100%; height: auto;">
                        </div>
                        <h6 class="mt-3 mb-1" style="color: var(--kn-dark); font-weight: 700;">
                            <?= htmlspecialchars(trim(($participant['first_name'] ?? '') . ' ' . ($participant['last_name'] ?? '') . ($participant['name_of_client'] ?? ''))) ?>
                        </h6>
                        <small class="text-muted d-block"><?= htmlspecialchars($session['activity_name']) ?></small>
                        <small class="text-muted d-block"><?= date('M j, Y', strtotime($session['session_date'])) ?></small>
                        
                        <div class="mt-2 no-print">
                            <button onclick="downloadQRCode(<?= $participant['participant_id'] ?? $participant['member_id'] ?>)" 
                                    class="btn btn-sm" 
                                    style="background: transparent; color: #5A7038; border: 1px solid #5A7038; padding: .25rem .5rem; border-radius: 6px; font-size: 0.875rem;">
                                <i class="bi bi-download"></i> Download
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div class="mt-4 no-print">
        <a href="index.php?action=feedingAttendance&session_id=<?= $session['session_id'] ?>" 
           class="btn" style="background: transparent; color: var(--kn-dark); border: 2px solid #e5e7eb; padding: .6rem 1.5rem; border-radius: 10px; font-weight: 600;">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Attendance
        </a>
    </div>
</div>

<script>
// Download individual QR code
function downloadQRCode(participantId) {
    const img = document.querySelector(`img[data-participant="${participantId}"]`);
    if (!img) return;
    
    // Create a temporary link and trigger download
    const link = document.createElement('a');
    link.href = img.src;
    link.download = `QR_Participant_${participantId}.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Download all QR codes as a ZIP (requires additional library)
// For simplicity, we'll just print them
function downloadAllQRCodes() {
    window.print();
}
</script>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
