<?php
/**
 * Process 12: Committee Chair on Health Reviews List of Affected Children
 * This view allows the Committee Chair to review the OPT Plus Form C data
 * before proceeding to create a feeding program proposal (Process 13)
 */
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = $pageTitle ?? 'Review Affected Children';
$activeNav = $activeNav ?? 'affected_children';
include __DIR__ . '/../templates/committee_chair_layout.php';
include __DIR__ . '/../templates/button_styles.php';
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1" style="color:var(--kn-dark);font-weight:700">
                        📋 OPT Plus Form C: List of Affected/At-Risk Children
                    </h4>
                    <p class="text-muted mb-0" style="font-size:.9rem">
                        Review nutrition assessment data to plan feeding program interventions
                    </p>
                </div>
                <div>
                    <button onclick="window.print()" class="btn-kn-outline btn-kn-sm">
                        <i class="bi bi-printer-fill"></i>Print Form C
                    </button>
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

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-3">
                <input type="hidden" name="action" value="reviewAffectedChildren">
                
                <div class="col-md-3">
                    <label class="form-label-kn">
                        <i class="bi bi-geo-alt-fill"></i>Purok
                    </label>
                    <select name="barangay_code" class="form-select form-select-kn">
                        <option value="">All Puroks</option>
                        <?php foreach ($purokList ?? [] as $purok): ?>
                            <option value="<?= htmlspecialchars($purok) ?>"
                                <?= ($selectedBarangay ?? '') === $purok ? 'selected' : '' ?>>
                                <?= htmlspecialchars($purok) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label-kn">
                        <i class="bi bi-person-badge-fill"></i>BNS Staff
                    </label>
                    <select name="bns_id" class="form-select form-select-kn">
                        <option value="">All BNS Staff</option>
                        <?php foreach ($bnsList ?? [] as $bns): ?>
                            <option value="<?= $bns['user_id'] ?>"
                                <?= ($selectedBns ?? 0) == $bns['user_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($bns['first_name'] . ' ' . $bns['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label-kn">
                        <i class="bi bi-calendar-range-fill"></i>Assessment Period
                    </label>
                    <select name="period" class="form-select form-select-kn">
                        <option value="all">All Time</option>
                        <option value="current_month" <?= ($selectedPeriod ?? '') === 'current_month' ? 'selected' : '' ?>>Current Month</option>
                        <option value="last_3_months" <?= ($selectedPeriod ?? '') === 'last_3_months' ? 'selected' : '' ?>>Last 3 Months</option>
                        <option value="last_6_months" <?= ($selectedPeriod ?? '') === 'last_6_months' ? 'selected' : '' ?>>Last 6 Months</option>
                        <option value="current_year" <?= ($selectedPeriod ?? '') === 'current_year' ? 'selected' : '' ?>>Current Year</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn-kn-primary btn-kn-block">
                        <i class="bi bi-funnel-fill"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics (Form C Header Data) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0" style="font-weight:600;color:var(--kn-dark)">
                📊 Summary Statistics
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <!-- Total Affected -->
                <div class="col-md-12 mb-3">
                    <div class="p-3" style="background:rgba(220,53,69,0.05);border-left:4px solid #dc3545;border-radius:8px">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted" style="font-size:.85rem;font-weight:600">Total # Children Affected/At-Risk</div>
                                <div style="font-size:2.5rem;font-weight:700;color:#dc3545">
                                    <?= $stats['total'] ?? 0 ?>
                                </div>
                            </div>
                            <div style="font-size:3rem;opacity:0.2">👶</div>
                        </div>
                    </div>
                </div>

                <!-- Undernutrition Categories -->
                <div class="col-md-12">
                    <h6 class="mb-3" style="font-weight:600;color:var(--kn-dark)">Number of Children Affected by Undernutrition:</h6>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <div class="card border-0" style="background:rgba(255,193,7,0.1)">
                                <div class="card-body text-center">
                                    <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em">MUW</div>
                                    <div style="font-size:1.75rem;font-weight:700;color:#ffc107">
                                        <?= $stats['moderately_underweight'] ?? 0 ?>
                                    </div>
                                    <div style="font-size:.7rem;color:var(--kn-muted)">Moderately Underweight</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card border-0" style="background:rgba(220,53,69,0.1)">
                                <div class="card-body text-center">
                                    <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em">MSt</div>
                                    <div style="font-size:1.75rem;font-weight:700;color:#dc3545">
                                        <?= $stats['moderately_stunted'] ?? 0 ?>
                                    </div>
                                    <div style="font-size:.7rem;color:var(--kn-muted)">Moderately Stunted</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card border-0" style="background:rgba(220,53,69,0.15)">
                                <div class="card-body text-center">
                                    <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em">MW/MAM</div>
                                    <div style="font-size:1.75rem;font-weight:700;color:#dc3545">
                                        <?= $stats['moderately_wasted'] ?? 0 ?>
                                    </div>
                                    <div style="font-size:.7rem;color:var(--kn-muted)">Moderately Wasted</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card border-0" style="background:rgba(220,53,69,0.2)">
                                <div class="card-body text-center">
                                    <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em">SUW</div>
                                    <div style="font-size:1.75rem;font-weight:700;color:#dc3545">
                                        <?= $stats['severely_underweight'] ?? 0 ?>
                                    </div>
                                    <div style="font-size:.7rem;color:var(--kn-muted)">Severely Underweight</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card border-0" style="background:rgba(220,53,69,0.25)">
                                <div class="card-body text-center">
                                    <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em">SSt</div>
                                    <div style="font-size:1.75rem;font-weight:700;color:#dc3545">
                                        <?= $stats['severely_stunted'] ?? 0 ?>
                                    </div>
                                    <div style="font-size:.7rem;color:var(--kn-muted)">Severely Stunted</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card border-0" style="background:rgba(220,53,69,0.3)">
                                <div class="card-body text-center">
                                    <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em">SW/SAM</div>
                                    <div style="font-size:1.75rem;font-weight:700;color:#dc3545">
                                        <?= $stats['severely_wasted'] ?? 0 ?>
                                    </div>
                                    <div style="font-size:.7rem;color:var(--kn-muted)">Severely Wasted</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overweight/Obesity -->
                <div class="col-md-12">
                    <h6 class="mb-3" style="font-weight:600;color:var(--kn-dark)">Number of Children with Overweight or Obesity:</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border-0" style="background:rgba(13,110,253,0.1)">
                                <div class="card-body text-center">
                                    <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em">OW</div>
                                    <div style="font-size:1.75rem;font-weight:700;color:#0d6efd">
                                        <?= $stats['overweight'] ?? 0 ?>
                                    </div>
                                    <div style="font-size:.7rem;color:var(--kn-muted)">Overweight</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0" style="background:rgba(13,110,253,0.15)">
                                <div class="card-body text-center">
                                    <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em">Ob</div>
                                    <div style="font-size:1.75rem;font-weight:700;color:#0d6efd">
                                        <?= $stats['obese'] ?? 0 ?>
                                    </div>
                                    <div style="font-size:.7rem;color:var(--kn-muted)">Obese</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Children List (Form C Table) -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0" style="font-weight:600;color:var(--kn-dark)">
                    Detailed List of Affected Children
                </h6>
                <span class="badge bg-danger"><?= count($affectedChildren ?? []) ?> children</span>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($affectedChildren)): ?>
                <div class="text-center py-5">
                    <div style="font-size:3rem;opacity:0.3">✅</div>
                    <p class="text-muted mb-0">No affected children found for the selected filters.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0" style="font-size:.85rem">
                        <thead style="background:rgba(107,122,58,0.05)">
                            <tr>
                                <th rowspan="2" class="text-center align-middle" style="font-size:.75rem;font-weight:600">Child Seq.</th>
                                <th rowspan="2" class="align-middle" style="font-size:.75rem;font-weight:600">Address<br><small>(Purok/Location)</small></th>
                                <th rowspan="2" class="align-middle" style="font-size:.75rem;font-weight:600">Name of Mother or Caregiver</th>
                                <th rowspan="2" class="align-middle" style="font-size:.75rem;font-weight:600">Full Name of Child</th>
                                <th rowspan="2" class="text-center align-middle" style="font-size:.75rem;font-weight:600">Sex</th>
                                <th rowspan="2" class="text-center align-middle" style="font-size:.75rem;font-weight:600">Age in Months</th>
                                <th colspan="3" class="text-center" style="font-size:.75rem;font-weight:600;background:rgba(107,122,58,0.1)">Nutritional Status</th>
                                <th rowspan="2" class="text-center align-middle" style="font-size:.75rem;font-weight:600">MUAC</th>
                            </tr>
                            <tr>
                                <th class="text-center" style="font-size:.7rem;font-weight:600;background:rgba(107,122,58,0.08)">Weight for Age</th>
                                <th class="text-center" style="font-size:.7rem;font-weight:600;background:rgba(107,122,58,0.08)">Length/Height for Age</th>
                                <th class="text-center" style="font-size:.7rem;font-weight:600;background:rgba(107,122,58,0.08)">Weight for Length/Height</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($affectedChildren as $index => $child): ?>
                                <tr>
                                    <td class="text-center" style="font-weight:600"><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($child['purok'] ?? 'N/A') ?></td>
                                    <td style="font-weight:600;color:var(--kn-dark)">
                                        <?= htmlspecialchars($child['mother_name'] ?? $child['caregiver_name'] ?? 'N/A') ?>
                                    </td>
                                    <td style="font-weight:600;color:var(--kn-dark)">
                                        <?= htmlspecialchars($child['full_name']) ?>
                                    </td>
                                    <td class="text-center"><?= htmlspecialchars($child['sex'] ?? 'N/A') ?></td>
                                    <td class="text-center"><?= (int)$child['age_in_months'] ?></td>
                                    <td class="text-center">
                                        <?php
                                        $status = $child['wfa_status'] ?? 'N';
                                        $statusCode = match($status) {
                                            'SUW' => 'SUW',
                                            'UW'  => 'UW',
                                            'OW'  => 'OW',
                                            'N'   => 'N',
                                            default => 'N'
                                        };
                                        $badgeClass = match($status) {
                                            'SUW' => 'bg-danger',
                                            'UW'  => 'bg-warning text-dark',
                                            'OW'  => 'bg-info',
                                            'N'   => 'bg-success',
                                            default => 'bg-success'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>" style="font-size:.65rem">
                                            <?= $statusCode ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $status = $child['hfa_status'] ?? 'N';
                                        $statusCode = match($status) {
                                            'SSt' => 'SSt',
                                            'St'  => 'St',
                                            'N'   => 'N',
                                            default => 'N'
                                        };
                                        $badgeClass = match($status) {
                                            'SSt' => 'bg-danger',
                                            'St'  => 'bg-warning text-dark',
                                            'N'   => 'bg-success',
                                            default => 'bg-success'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>" style="font-size:.65rem">
                                            <?= $statusCode ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $status = $child['wfh_status'] ?? 'N';
                                        $statusCode = match($status) {
                                            'SAM' => 'SAM',
                                            'MAM' => 'MAM',
                                            'OW'  => 'OW',
                                            'Ob'  => 'Ob',
                                            'N'   => 'N',
                                            default => 'N'
                                        };
                                        $badgeClass = match($status) {
                                            'SAM' => 'bg-danger',
                                            'MAM' => 'bg-warning text-dark',
                                            'OW'  => 'bg-info',
                                            'Ob'  => 'bg-info',
                                            'N'   => 'bg-success',
                                            default => 'bg-success'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>" style="font-size:.65rem">
                                            <?= $statusCode ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $muac = $child['muac_cm'] ?? null;
                                        if ($muac) {
                                            echo number_format($muac, 1) . ' cm';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
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
                <div class="row">
                    <div class="col-md-8">
                        <div class="text-muted" style="font-size:.85rem">
                            <strong>Legend:</strong>
                            <span class="badge bg-success ms-2">N</span> Normal
                            <span class="badge bg-danger ms-2">SUW</span> Severely Underweight
                            <span class="badge bg-warning text-dark ms-2">UW</span> Underweight
                            <span class="badge bg-danger ms-2">SSt</span> Severely Stunted
                            <span class="badge bg-warning text-dark ms-2">St</span> Stunted
                            <span class="badge bg-danger ms-2">SW/SAM</span> Severely Wasted
                            <span class="badge bg-warning text-dark ms-2">MW/MAM</span> Moderately Wasted
                            <span class="badge bg-info ms-2">OW</span> Overweight
                            <span class="badge bg-info ms-2">Ob</span> Obese
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <form method="POST" action="index.php?action=proposalForm" id="proceedToProposalForm">
                            <input type="hidden" name="affected_children_data" id="affectedChildrenData" value="">
                            <button type="submit" class="btn-kn-secondary">
                                <i class="bi bi-arrow-right-circle-fill"></i>Proceed to Create Proposal
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <script>
            // Prepare affected children data for proposal
            document.getElementById('proceedToProposalForm').addEventListener('submit', function() {
                const affectedChildren = <?= json_encode($affectedChildren) ?>;
                
                // Transform data to match expected format
                const transformedData = affectedChildren.map(child => ({
                    child_name: child.full_name,
                    mother_name: child.mother_name || child.caregiver_name || 'N/A',
                    mother_id: child.parent_user_id || null, // Get from parent_user_id field
                    purok: child.purok || '',
                    age_months: child.age_in_months,
                    sex: child.sex,
                    wfa_status: child.wfa_status,
                    hfa_status: child.hfa_status,
                    wfh_status: child.wfh_status,
                    assessment_date: child.assessment_date
                }));
                
                document.getElementById('affectedChildrenData').value = JSON.stringify(transformedData);
            });
            </script>
        <?php endif; ?>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    .btn, .alert, nav, .sidebar, .card-footer .btn {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }
    
    .table {
        font-size: 10pt !important;
    }
    
    .table th, .table td {
        padding: 4px !important;
    }
    
    @page {
        size: legal landscape;
        margin: 0.5in;
    }
}
</style>

<?php include __DIR__ . '/../templates/committee_chair_layout_end.php'; ?>
