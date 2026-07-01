<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance - <?= htmlspecialchars($session['activity_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --kn-green: #5A7038;
            --kn-orange: #C4722A;
        }
        body {
            background: linear-gradient(135deg, rgba(90,112,56,0.1) 0%, rgba(196,114,42,0.1) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .attendance-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-circle {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--kn-green) 0%, #4A5D2E 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .submit-btn {
            background: linear-gradient(135deg, var(--kn-green) 0%, #4A5D2E 100%);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
            font-size: 1.1rem;
        }
        .submit-btn:hover {
            background: linear-gradient(135deg, #4A5D2E 0%, var(--kn-green) 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="attendance-card">
        <div class="logo-section">
            <div class="logo-circle">
                <i class="bi bi-check2-circle" style="font-size: 2.5rem; color: white;"></i>
            </div>
            <h4 style="color: var(--kn-green); font-weight: 700; margin-bottom: 0.5rem;">
                Mark Your Attendance
            </h4>
            <p style="color: #6b7280; margin-bottom: 0;">
                <?= htmlspecialchars($session['activity_name']) ?>
            </p>
        </div>

        <?php if (isset($_SESSION['attendance_success'])): ?>
            <div class="alert alert-success text-center">
                <i class="bi bi-check-circle-fill" style="font-size: 3rem; color: #10b981;"></i>
                <h5 class="mt-3">Attendance Recorded!</h5>
                <p class="mb-0"><?= htmlspecialchars($_SESSION['attendance_success']) ?></p>
            </div>
            <?php unset($_SESSION['attendance_success']); ?>
        <?php elseif (isset($_SESSION['attendance_error'])): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($_SESSION['attendance_error']) ?>
            </div>
            <?php unset($_SESSION['attendance_error']); ?>
        <?php endif; ?>

        <div class="mb-4 p-3" style="background: rgba(90,112,56,0.08); border-radius: 12px;">
            <div class="row g-2 text-center">
                <div class="col-6">
                    <div>
                        <i class="bi bi-calendar-event" style="color: var(--kn-orange); font-size: 1.5rem;"></i>
                        <p class="mb-0 mt-1" style="color: #6b7280; font-size: 0.875rem;">Date</p>
                        <strong style="color: var(--kn-green);">
                            <?= date('M j, Y', strtotime($session['session_date'])) ?>
                        </strong>
                    </div>
                </div>
                <div class="col-6">
                    <div>
                        <i class="bi bi-geo-alt-fill" style="color: var(--kn-orange); font-size: 1.5rem;"></i>
                        <p class="mb-0 mt-1" style="color: #6b7280; font-size: 0.875rem;">Location</p>
                        <strong style="color: var(--kn-green);">
                            <?= htmlspecialchars($session['purok_barangay']) ?>
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <?php 
        // Check if user is logged in and has children
        $hasAccount = isset($loggedInUserId) && $loggedInUserId > 0;
        $hasChildren = !empty($userChildren);
        ?>

        <form method="POST" action="index.php?action=submitAttendanceViaQR" id="attendanceForm">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">

            <?php if ($hasAccount && $hasChildren): ?>
                <!-- For parents with accounts: Automatically mark ALL children present -->
                <div class="alert alert-success mb-3">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Welcome!</strong> Your children will be automatically marked as present.
                </div>
                
                <div class="mb-3 p-3" style="background: rgba(90,112,56,0.08); border-radius: 12px;">
                    <label class="form-label" style="color: var(--kn-green); font-weight: 600; margin-bottom: 1rem;">
                        <i class="bi bi-people-fill me-1"></i>
                        Your Children for This Session:
                    </label>
                    
                    <?php 
                    $childrenToMark = [];
                    foreach ($userChildren as $child): 
                        if ($child['is_present'] != 1):
                            $childrenToMark[] = $child['attendance_id'];
                        endif;
                    ?>
                        <div class="d-flex align-items-center mb-2 p-2" style="background: white; border-radius: 8px;">
                            <div class="flex-grow-1">
                                <strong style="color: var(--kn-green);">
                                    <?= htmlspecialchars($child['name_of_client']) ?>
                                </strong>
                            </div>
                            <div>
                                <?php if ($child['is_present'] == 1): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle-fill me-1"></i>Already Present
                                    </span>
                                <?php else: ?>
                                    <span class="badge" style="background: var(--kn-orange);">
                                        <i class="bi bi-clock-fill me-1"></i>Not Yet Marked
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Hidden field with all children IDs to mark -->
                    <input type="hidden" name="auto_mark_children" value="<?= implode(',', $childrenToMark) ?>">
                    
                    <?php if (!empty($childrenToMark)): ?>
                        <div class="mt-3 text-center" style="color: #6b7280; font-size: 0.875rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            Click "Mark All Present" below to confirm attendance for <?= count($childrenToMark) ?> child<?= count($childrenToMark) > 1 ? 'ren' : '' ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            All your children are already marked as present for this session!
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- For users without accounts or without children: Show all participants -->
                <?php if ($hasAccount && !$hasChildren): ?>
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        No children found for your account. Please select from the list below or contact BNS staff.
                    </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label class="form-label" style="color: var(--kn-green); font-weight: 600;">
                        <i class="bi bi-person-fill me-1"></i>
                        Select Your Name
                    </label>
                    
                    <?php if (empty($participants)): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            No participants registered yet. Please contact the BNS staff.
                        </div>
                    <?php else: ?>
                        <select name="attendance_id" 
                                class="form-select form-select-lg" 
                                required
                                style="border: 2px solid #e5e7eb; border-radius: 12px; padding: 0.8rem;">
                            <option value="">-- Select your name --</option>
                            <?php foreach ($participants as $participant): ?>
                                <option value="<?= $participant['attendance_id'] ?>"
                                        <?= ($participant['is_present'] == 1) ? 'disabled' : '' ?>>
                                    <?= htmlspecialchars($participant['name_of_client']) ?>
                                    <?php if ($participant['is_present'] == 1): ?>
                                        (Already marked present ✓)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <div class="mt-2" style="font-size: 0.875rem; color: #6b7280;">
                            <i class="bi bi-info-circle me-1"></i>
                            Can't find your name? Please inform the BNS staff.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($participants) || ($hasAccount && $hasChildren)): ?>
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmAttendance" required>
                        <label class="form-check-label" for="confirmAttendance" style="color: #6b7280;">
                            I confirm that I am attending this session
                        </label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="bi bi-check2-circle me-2"></i>
                    Mark Present
                </button>
            <?php endif; ?>
        </form>

        <div class="text-center mt-3">
            <small style="color: #9ca3af;">
                <i class="bi bi-shield-check me-1"></i>
                Your attendance is being recorded securely
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide success message after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector('.alert-success');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    </script>
</body>
</html>
