<?php
/**
 * Process 12: Checking Nutrition Risk
 * BNS views list of affected (malnourished) children
 */
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = $pageTitle ?? 'Nutrition Risk Assessment';
$activeNav = $activeNav ?? 'feeding_program';
include __DIR__ . '/../templates/bns_layout.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1" style="color:var(--kn-dark);font-weight:700">
                        📊 Nutrition Risk Assessment
                    </h4>
                    <p class="text-muted mb-0" style="font-size:.9rem">
                        List of children identified as malnourished who may need feeding program intervention
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width:48px;height:48px;background:rgba(220,53,69,0.1)">
                                <span style="font-size:1.5rem">👶</span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Total Affected</div>
                            <div style="font-size:1.75rem;font-weight:700;color:var(--kn-dark)"><?= $stats['total'] ?? 0 ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width:48px;height:48px;background:rgba(255,193,7,0.1)">
                                <span style="font-size:1.5rem">⚖️</span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Underweight</div>
                            <div style="font-size:1.75rem;font-weight:700;color:var(--kn-dark)">
                                <?= ($stats['severely_underweight'] ?? 0) + ($stats['underweight'] ?? 0) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width:48px;height:48px;background:rgba(13,110,253,0.1)">
                                <span style="font-size:1.5rem">📏</span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Stunted</div>
                            <div style="font-size:1.75rem;font-weight:700;color:var(--kn-dark)">
                                <?= ($stats['severely_stunted'] ?? 0) + ($stats['stunted'] ?? 0) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width:48px;height:48px;background:rgba(220,53,69,0.1)">
                                <span style="font-size:1.5rem">🩺</span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Wasted</div>
                            <div style="font-size:1.75rem;font-weight:700;color:var(--kn-dark)">
                                <?= ($stats['severely_wasted'] ?? 0) + ($stats['wasted'] ?? 0) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Affected Children Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0" style="font-weight:600;color:var(--kn-dark)">
                    Affected Children List
                </h6>
                <span class="badge bg-danger"><?= count($affectedChildren ?? []) ?> children</span>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($affectedChildren)): ?>
                <div class="text-center py-5">
                    <div style="font-size:3rem;opacity:0.3">✅</div>
                    <p class="text-muted mb-0">No malnourished children found. Great work!</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:rgba(107,122,58,0.05)">
                            <tr>
                                <th style="font-size:.8rem;font-weight:600;color:var(--kn-dark)">Child Name</th>
                                <th style="font-size:.8rem;font-weight:600;color:var(--kn-dark)">Age</th>
                                <th style="font-size:.8rem;font-weight:600;color:var(--kn-dark)">Sex</th>
                                <th style="font-size:.8rem;font-weight:600;color:var(--kn-dark)">Purok</th>
                                <th style="font-size:.8rem;font-weight:600;color:var(--kn-dark)">Weight Status</th>
                                <th style="font-size:.8rem;font-weight:600;color:var(--kn-dark)">Height Status</th>
                                <th style="font-size:.8rem;font-weight:600;color:var(--kn-dark)">Assessment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($affectedChildren as $child): ?>
                                <tr>
                                    <td style="font-weight:600;color:var(--kn-dark)">
                                        <?= htmlspecialchars($child['first_name'] . ' ' . $child['last_name']) ?>
                                    </td>
                                    <td>
                                        <?php
                                        $years = (int)$child['age_years'];
                                        $months = (int)$child['age_months'] % 12;
                                        echo $years > 0 ? "{$years}y {$months}m" : "{$months}m";
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($child['sex'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($child['purok'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php
                                        $status = $child['weight_for_age_status'] ?? 'N/A';
                                        $badgeClass = match(true) {
                                            str_contains($status, 'Severely') => 'bg-danger',
                                            str_contains($status, 'Underweight') => 'bg-warning text-dark',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>" style="font-size:.7rem">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $status = $child['height_for_age_status'] ?? 'N/A';
                                        $badgeClass = match(true) {
                                            str_contains($status, 'Severely') => 'bg-danger',
                                            str_contains($status, 'Stunted') => 'bg-warning text-dark',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>" style="font-size:.7rem">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    </td>
                                    <td style="font-size:.85rem;color:var(--kn-muted)">
                                        <?= $child['assessment_date'] ? date('M j, Y', strtotime($child['assessment_date'])) : 'N/A' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($affectedChildren)): ?>
            <div class="card-footer bg-white border-top py-3">
                <div class="text-muted" style="font-size:.85rem">
                    <strong>Note:</strong> This list is automatically generated from nutrition assessments. 
                    Share this data with the Committee Chair on Health to plan feeding programs.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../templates/bns_layout_end.php'; ?>
