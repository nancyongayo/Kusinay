<?php
/**
 * BNS - View Feeding Program Proposal Details (Read-only)
 */
$pageTitle = 'Proposal Details';
$activeNav = 'feeding_program';
include __DIR__ . '/../templates/bns_layout.php';
?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.php?action=feedingProgramList" style="color: #5A7038; text-decoration: none; font-weight: 500;">Feeding Programs</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--kn-dark);">Proposal Details</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="mb-2" style="color: var(--kn-dark); font-weight: 700;">
                        <i class="bi bi-file-text me-2" style="color: #5A7038;"></i>
                        <?= htmlspecialchars($proposal['proposal_title'] ?? 'Proposal') ?>
                    </h2>
                    <p class="text-muted mb-0">
                        <?php
                        $statusColors = [
                            'Draft'      => 'background: linear-gradient(135deg, rgba(107,114,128,.15) 0%, rgba(107,114,128,.1) 100%); color: #374151; border: 1px solid rgba(107,114,128,.2);',
                            'For Review' => 'background: linear-gradient(135deg, rgba(196,114,42,.15) 0%, rgba(196,114,42,.1) 100%); color: #C4722A; border: 1px solid rgba(196,114,42,.2);',
                            'Approved'   => 'background: linear-gradient(135deg, rgba(90,112,56,.15) 0%, rgba(90,112,56,.1) 100%); color: #5A7038; border: 1px solid rgba(90,112,56,.2);',
                            'Rejected'   => 'background: linear-gradient(135deg, rgba(220,38,38,.15) 0%, rgba(220,38,38,.1) 100%); color: #dc2626; border: 1px solid rgba(220,38,38,.2);',
                        ];
                        $statusStyle = $statusColors[$proposal['status']] ?? $statusColors['Draft'];
                        ?>
                        <span class="badge me-2" style="<?= $statusStyle ?> padding: .4rem .8rem; border-radius: 8px; font-weight: 600;">
                            <?= htmlspecialchars($proposal['status']) ?>
                        </span>
                        Created by <?= htmlspecialchars($proposal['creator_first_name'] . ' ' . $proposal['creator_last_name']) ?>
                        · <?= date('F j, Y', strtotime($proposal['created_at'])) ?>
                    </p>
                </div>
                
                <?php if ($proposal['status'] === 'Approved'): ?>
                <a href="index.php?action=feedingSessions&proposal_id=<?= $proposal['proposal_id'] ?>" 
                   class="btn" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); color: #fff; border: none; padding: .6rem 1.2rem; border-radius: 10px; font-weight: 600;">
                    <i class="bi bi-calendar-check me-1"></i>
                    View Sessions
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Proposal Details -->
    <div class="row g-4">
        <!-- Basic Information -->
        <div class="col-md-6">
            <div class="card h-100 border-0" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.08);">
                <div class="card-header" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15); border-radius: 16px 16px 0 0 !important; padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: var(--kn-dark); font-weight: 700;"><i class="bi bi-info-circle me-2" style="color: #5A7038;"></i>Basic Information</h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div class="mb-3">
                        <label class="text-muted small" style="font-weight: 600; text-transform: uppercase; letter-spacing: .05em;">Program Type</label>
                        <p class="mb-0 fw-bold" style="color: var(--kn-dark);"><?= htmlspecialchars($proposal['program_type']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small" style="font-weight: 600; text-transform: uppercase; letter-spacing: .05em;">Proponent</label>
                        <p class="mb-0" style="color: var(--kn-dark);"><?= htmlspecialchars($proposal['proponent'] ?? 'N/A') ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small" style="font-weight: 600; text-transform: uppercase; letter-spacing: .05em;">Location</label>
                        <p class="mb-0" style="color: var(--kn-dark);"><?= htmlspecialchars($proposal['location'] ?? 'N/A') ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small" style="font-weight: 600; text-transform: uppercase; letter-spacing: .05em;">Target Beneficiaries</label>
                        <p class="mb-0" style="color: var(--kn-dark);"><?= htmlspecialchars($proposal['target_beneficiaries']) ?></p>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small" style="font-weight: 600; text-transform: uppercase; letter-spacing: .05em;">Number of Beneficiaries</label>
                        <p class="mb-0 fw-bold" style="color: #5A7038; font-size: 1.1rem;"><?= $proposal['num_beneficiaries'] ?> children</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule & Budget -->
        <div class="col-md-6">
            <div class="card h-100 border-0" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.08);">
                <div class="card-header" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15); border-radius: 16px 16px 0 0 !important; padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: var(--kn-dark); font-weight: 700;"><i class="bi bi-calendar-range me-2" style="color: #C4722A;"></i>Schedule & Budget</h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div class="mb-3">
                        <label class="text-muted small" style="font-weight: 600; text-transform: uppercase; letter-spacing: .05em;">Implementation Period</label>
                        <p class="mb-0" style="color: var(--kn-dark);">
                            <?= date('F j, Y', strtotime($proposal['start_date'])) ?> - 
                            <?= date('F j, Y', strtotime($proposal['end_date'])) ?>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small" style="font-weight: 600; text-transform: uppercase; letter-spacing: .05em;">Duration</label>
                        <p class="mb-0" style="color: var(--kn-dark);"><?= $proposal['implementation_days'] ?> days</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small" style="font-weight: 600; text-transform: uppercase; letter-spacing: .05em;">Feeding Schedule</label>
                        <p class="mb-0" style="color: var(--kn-dark);"><?= htmlspecialchars($proposal['feeding_schedule'] ?? 'N/A') ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small" style="font-weight: 600; text-transform: uppercase; letter-spacing: .05em;">Estimated Budget</label>
                        <p class="mb-0 fw-bold" style="color: #5A7038; font-size: 1.1rem;">₱<?= number_format($proposal['estimated_budget'], 2) ?></p>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small" style="font-weight: 600; text-transform: uppercase; letter-spacing: .05em;">Funding Source</label>
                        <p class="mb-0" style="color: var(--kn-dark);"><?= htmlspecialchars($proposal['funding_source'] ?? 'N/A') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Objectives -->
        <div class="col-12">
            <div class="card border-0" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.08);">
                <div class="card-header" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15); border-radius: 16px 16px 0 0 !important; padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: var(--kn-dark); font-weight: 700;"><i class="bi bi-bullseye me-2" style="color: #C4722A;"></i>Goals and Objectives</h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <p class="mb-0" style="white-space: pre-wrap; color: var(--kn-dark); line-height: 1.8;"><?= htmlspecialchars($proposal['objectives']) ?></p>
                </div>
            </div>
        </div>

        <!-- Rationale -->
        <div class="col-12">
            <div class="card border-0" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.08);">
                <div class="card-header" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15); border-radius: 16px 16px 0 0 !important; padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: var(--kn-dark); font-weight: 700;"><i class="bi bi-journal-text me-2" style="color: #5A7038;"></i>Background and Rationale</h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <p class="mb-0" style="white-space: pre-wrap; color: var(--kn-dark); line-height: 1.8;"><?= htmlspecialchars($proposal['rationale']) ?></p>
                </div>
            </div>
        </div>

        <?php if (!empty($proposal['implementation_plan'])): ?>
        <!-- Implementation Plan -->
        <div class="col-12">
            <div class="card border-0" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.08);">
                <div class="card-header" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15); border-radius: 16px 16px 0 0 !important; padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: var(--kn-dark); font-weight: 700;"><i class="bi bi-list-check me-2" style="color: #C4722A;"></i>Implementation Plan</h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <p class="mb-0" style="white-space: pre-wrap; color: var(--kn-dark); line-height: 1.8;"><?= htmlspecialchars($proposal['implementation_plan']) ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($validations)): ?>
        <!-- Validation History -->
        <div class="col-12">
            <div class="card border-0" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.08);">
                <div class="card-header" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15); border-radius: 16px 16px 0 0 !important; padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: var(--kn-dark); font-weight: 700;"><i class="bi bi-check-circle me-2" style="color: #5A7038;"></i>Validation History</h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <?php foreach ($validations as $validation): ?>
                        <?php
                        $validationStyles = [
                            'Approved' => 'border-color: #5A7038;',
                            'Rejected' => 'border-color: #dc2626;',
                            'Needs Revision' => 'border-color: #C4722A;'
                        ];
                        $validationBadgeStyles = [
                            'Approved' => 'background: linear-gradient(135deg, rgba(90,112,56,.15) 0%, rgba(90,112,56,.1) 100%); color: #5A7038; border: 1px solid rgba(90,112,56,.2);',
                            'Rejected' => 'background: linear-gradient(135deg, rgba(220,38,38,.15) 0%, rgba(220,38,38,.1) 100%); color: #dc2626; border: 1px solid rgba(220,38,38,.2);',
                            'Needs Revision' => 'background: linear-gradient(135deg, rgba(196,114,42,.15) 0%, rgba(196,114,42,.1) 100%); color: #C4722A; border: 1px solid rgba(196,114,42,.2);'
                        ];
                        $borderStyle = $validationStyles[$validation['decision']] ?? 'border-color: #e5e7eb;';
                        $badgeStyle = $validationBadgeStyles[$validation['decision']] ?? $validationBadgeStyles['Approved'];
                        ?>
                        <div class="border-start border-3 ps-3 mb-3" style="<?= $borderStyle ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge" style="<?= $badgeStyle ?> padding: .4rem .8rem; border-radius: 8px; font-weight: 600;">
                                        <?= htmlspecialchars($validation['decision']) ?>
                                    </span>
                                    <span class="text-muted ms-2">
                                        by <?= htmlspecialchars($validation['validator_first_name'] . ' ' . $validation['validator_last_name']) ?>
                                    </span>
                                </div>
                                <small class="text-muted">
                                    <?= date('M j, Y g:i A', strtotime($validation['validated_at'])) ?>
                                </small>
                            </div>
                            <?php if (!empty($validation['feedback'])): ?>
                                <p class="mb-0 mt-2 text-muted"><?= htmlspecialchars($validation['feedback']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="mt-4">
        <a href="index.php?action=feedingProgramList" class="btn" style="background: transparent; color: var(--kn-dark); border: 2px solid #e5e7eb; padding: .6rem 1.5rem; border-radius: 10px; font-weight: 600;">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Programs
        </a>
    </div>
</div>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('show');
    document.getElementById('sidebarOverlay').classList.toggle('show');
});
document.getElementById('sidebarOverlay')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.remove('show');
    document.getElementById('sidebarOverlay').classList.remove('show');
});
</script>
</body>
</html>
