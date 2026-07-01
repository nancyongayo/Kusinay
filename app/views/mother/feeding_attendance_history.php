<?php
/**
 * Mother/Parent - Feeding Attendance History
 * Shows detailed attendance history for a child
 */
$pageTitle = 'Attendance History';
$activeNav = 'feeding_program';
include __DIR__ . '/../templates/mother_layout.php';
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.php?action=feedingDashboard" style="color: #5A7038; text-decoration: none; font-weight: 500;">
                            Feeding Program
                        </a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--kn-dark);">Attendance History</li>
                </ol>
            </nav>

            <h4 class="mb-1" style="color:var(--kn-dark);font-weight:700">
                📋 Attendance History
            </h4>
            <p class="text-muted mb-0" style="font-size:.9rem">
                <?= htmlspecialchars($childName ?? 'Child') ?> - <?= htmlspecialchars($programTitle ?? 'Feeding Program') ?>
            </p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0" style="background: linear-gradient(135deg, rgba(90,112,56,.12) 0%, rgba(90,112,56,.08) 100%); border-radius: 12px;">
                <div class="card-body text-center">
                    <h3 class="mb-0" style="color: #5A7038; font-weight: 700;"><?= $stats['total'] ?? 0 ?></h3>
                    <small style="color: var(--kn-dark); font-weight: 600;">Total Sessions</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0" style="background: linear-gradient(135deg, rgba(16,185,129,.12) 0%, rgba(16,185,129,.08) 100%); border-radius: 12px;">
                <div class="card-body text-center">
                    <h3 class="mb-0" style="color: #10b981; font-weight: 700;"><?= $stats['attended'] ?? 0 ?></h3>
                    <small style="color: var(--kn-dark); font-weight: 600;">Attended</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0" style="background: linear-gradient(135deg, rgba(220,38,38,.12) 0%, rgba(220,38,38,.08) 100%); border-radius: 12px;">
                <div class="card-body text-center">
                    <h3 class="mb-0" style="color: #dc2626; font-weight: 700;"><?= $stats['missed'] ?? 0 ?></h3>
                    <small style="color: var(--kn-dark); font-weight: 600;">Missed</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance History -->
    <div class="card border-0 shadow-sm">
        <div class="card-header" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15); padding: 1rem 1.5rem;">
            <h6 class="mb-0" style="color:var(--kn-dark);font-weight:700">
                Detailed Attendance Records
            </h6>
        </div>
        <div class="card-body p-0">
            <?php if (empty($attendanceHistory)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x" style="font-size: 3rem; color: #6c757d; opacity: 0.3;"></i>
                    <p class="text-muted mt-3 mb-0">No attendance records yet.</p>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($attendanceHistory as $record): ?>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <?php if ($record['is_present'] === null || $record['is_present'] === NULL): ?>
                                        <div class="me-3" style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-clock" style="color: #fff; font-size: 1.5rem;"></i>
                                        </div>
                                    <?php elseif ($record['is_present'] == 1): ?>
                                        <div class="me-3" style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-check-lg" style="color: #fff; font-size: 1.5rem;"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="me-3" style="width: 48px; height: 48px; background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-x-lg" style="color: #fff; font-size: 1.5rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <strong style="color: var(--kn-dark);">
                                            <?= date('M j, Y', strtotime($record['session_date'])) ?>
                                        </strong>
                                        <div class="text-muted small">
                                            <?= date('l', strtotime($record['session_date'])) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div>
                                    <div style="font-weight: 600; color: var(--kn-dark);">
                                        <?= htmlspecialchars($record['activity_name']) ?>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($record['purok_barangay']) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <?php if ($record['is_present'] === null || $record['is_present'] === NULL): ?>
                                    <span class="badge bg-warning text-dark" style="padding: .4rem .8rem; border-radius: 8px; font-size: .85rem;">
                                        <i class="bi bi-clock me-1"></i>Not Marked
                                    </span>
                                <?php elseif ($record['is_present'] == 1): ?>
                                    <span class="badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: .4rem .8rem; border-radius: 8px; font-size: .85rem;">
                                        <i class="bi bi-check-circle me-1"></i>Present
                                    </span>
                                <?php else: ?>
                                    <span class="badge" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); padding: .4rem .8rem; border-radius: 8px; font-size: .85rem;">
                                        <i class="bi bi-x-circle me-1"></i>Absent
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <?php if (!empty($record['meal_received'])): ?>
                                    <div class="text-muted small">
                                        <i class="bi bi-egg-fried me-1" style="color: #C4722A;"></i>
                                        <strong>Meal:</strong> <?= htmlspecialchars($record['meal_received']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($record['time_in']): ?>
                                    <div class="text-muted small">
                                        <i class="bi bi-clock me-1"></i>
                                        <strong>Time In:</strong> <?= date('g:i A', strtotime($record['time_in'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-1 text-end">
                                <?php if ($record['rsvp_status'] === 'confirmed'): ?>
                                    <i class="bi bi-check-circle-fill" style="color: #10b981;" title="You confirmed attendance"></i>
                                <?php elseif ($record['rsvp_status'] === 'declined'): ?>
                                    <i class="bi bi-x-circle-fill" style="color: #dc2626;" title="You declined attendance"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-4">
        <a href="index.php?action=feedingDashboard" class="btn" style="background: transparent; color: var(--kn-dark); border: 2px solid #e5e7eb; padding: .6rem 1.5rem; border-radius: 10px; font-weight: 600;">
            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
        </a>
    </div>
</div>

<?php include __DIR__ . '/../templates/mother_layout_end.php'; ?>
