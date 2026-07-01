<?php include __DIR__ . '/../templates/nutrition_layout.php'; ?>

<style>
.child-card {
    background: #fff;
    border: 1.5px solid rgba(107,122,58,.15);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    transition: all 0.2s;
}
.child-card:hover {
    border-color: var(--kn-green);
    box-shadow: 0 2px 8px rgba(107,122,58,.1);
}
.child-card.ready {
    border-left: 4px solid #28a745;
}
.child-card.not-ready {
    border-left: 4px solid #ffc107;
    background: #fffbf0;
}
.child-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}
.child-name {
    font-size: 1rem;
    font-weight: 700;
    color: var(--kn-dark);
}
.child-info {
    font-size: 0.85rem;
    color: var(--kn-muted);
    margin-bottom: 0.5rem;
}
.assessment-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-top: 0.75rem;
}
.assessment-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.75rem;
}
.assessment-label {
    font-size: 0.75rem;
    color: var(--kn-muted);
    font-weight: 600;
    margin-bottom: 0.25rem;
}
.assessment-value {
    font-size: 0.9rem;
    color: var(--kn-dark);
}
.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status-ready {
    background: #d4edda;
    color: #155724;
}
.status-pending {
    background: #fff3cd;
    color: #856404;
}
.btn-validate {
    background: var(--kn-green);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}
.btn-validate:hover {
    background: #5a6e3a;
    color: #fff;
    transform: translateY(-1px);
}
.btn-validate:disabled {
    background: #ccc;
    cursor: not-allowed;
}
.filter-bar {
    background: #fff;
    border: 1.5px solid rgba(107,122,58,.15);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    display: flex;
    gap: 1rem;
    align-items: center;
}
</style>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="index.php?action=recoveryValidation"
       style="display:inline-flex;align-items:center;gap:.4rem;background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.35rem .85rem;font-size:.88rem;font-weight:600;text-decoration:none">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    <div>
        <h4 class="mb-0" style="color:var(--kn-dark);font-weight:700">
            <?= htmlspecialchars($proposal['proposal_title']) ?>
        </h4>
        <p class="mb-0" style="color:var(--kn-muted);font-size:0.85rem">
            <i class="bi bi-calendar3"></i> 
            <?= date('M d, Y', strtotime($proposal['start_date'])) ?> - 
            <?= date('M d, Y', strtotime($proposal['end_date'])) ?>
        </p>
    </div>
</div>

<div class="alert alert-light border mb-3" style="border-radius:10px;font-size:.85rem">
    <i class="bi bi-info-circle me-2" style="color:var(--kn-green)"></i>
    <strong>Your role (Nutrition Officer II):</strong> Review BNS assessments only — validate recovery and write recommendations.
    <strong>BNS</strong> conducts all baseline &amp; follow-up measurements (Resident Assessment).
    When both assessments exist → <span class="badge bg-success">Ready</span> → click <strong>Validate Recovery</strong>.
</div>

<div class="filter-bar">
    <div style="flex:1">
        <strong style="color:var(--kn-dark)">
            <i class="bi bi-people-fill"></i> 
            <?= count($eligibleChildren) ?> Children Eligible for Recovery Validation
        </strong>
    </div>
    <div>
        <span class="status-indicator status-ready">
            <i class="bi bi-check-circle-fill"></i> 
            <?= count(array_filter($eligibleChildren, fn($c) => $c['ready_for_validation'])) ?> Ready
        </span>
        <span class="status-indicator status-pending ms-2">
            <i class="bi bi-clock-fill"></i> 
            <?= count(array_filter($eligibleChildren, fn($c) => !$c['ready_for_validation'])) ?> Pending Assessment
        </span>
    </div>
</div>

<?php if (empty($eligibleChildren)): ?>
<div style="text-align:center;padding:3rem 1rem;color:var(--kn-muted)">
    <i class="bi bi-clipboard-check" style="font-size:3rem;opacity:0.3;margin-bottom:1rem"></i>
    <h5>All Children Validated</h5>
    <p>All children in this feeding program have been validated for recovery.</p>
    <a href="index.php?action=recoveryValidationList&proposal_id=<?= $proposal['proposal_id'] ?>" 
       class="btn-validate mt-3">
        <i class="bi bi-list-ul"></i> View Validations
    </a>
</div>
<?php else: ?>

<?php foreach ($eligibleChildren as $child): 
    $baseline = $child['baseline'];
    $followup = $child['followup'];
    $ready = $child['ready_for_validation'];
?>
<div class="child-card <?= $ready ? 'ready' : 'not-ready' ?>">
    <div class="child-header">
        <div>
            <div class="child-name"><?= htmlspecialchars($child['full_name']) ?></div>
            <div class="child-info">
                <i class="bi bi-calendar-event"></i> Attended <?= $child['sessions_attended'] ?> sessions
                <span class="mx-2">•</span>
                <i class="bi bi-percent"></i> <?= number_format($child['attendance_rate'], 1) ?>% attendance
            </div>
        </div>
        <div>
            <?php if ($ready): ?>
                <span class="status-indicator status-ready">
                    <i class="bi bi-check-circle-fill"></i> Ready
                </span>
            <?php else: ?>
                <span class="status-indicator status-pending">
                    <i class="bi bi-clock-fill"></i> Pending Assessment
                </span>
            <?php endif; ?>
        </div>
    </div>

    <div class="assessment-grid">
        <div class="assessment-box">
            <div class="assessment-label">
                <i class="bi bi-clipboard-data"></i> BASELINE ASSESSMENT
            </div>
            <?php if ($baseline): ?>
                <div class="assessment-value">
                    <strong><?= number_format($baseline['weight_kg'], 1) ?> kg</strong> • 
                    <?= number_format($baseline['height_cm'], 1) ?> cm
                </div>
                <div style="font-size:0.75rem;color:var(--kn-muted);margin-top:0.25rem">
                    <?= date('M d, Y', strtotime($baseline['assessment_date'])) ?>
                    <span class="mx-1">•</span>
                    WFA: <?= htmlspecialchars($baseline['wfa_status'] ?? 'N/A') ?>
                    <span class="mx-1">•</span>
                    MUAC: <?= !empty($baseline['muac_cm']) ? number_format((float)$baseline['muac_cm'], 1) . ' cm' : 'N/A' ?>
                </div>
            <?php else: ?>
                <div class="assessment-value" style="color:#dc3545">
                    <i class="bi bi-exclamation-triangle"></i> No baseline assessment
                </div>
            <?php endif; ?>
        </div>

        <div class="assessment-box">
            <div class="assessment-label">
                <i class="bi bi-clipboard-check"></i> FOLLOW-UP ASSESSMENT
            </div>
            <?php if ($followup): ?>
                <div class="assessment-value">
                    <strong><?= number_format($followup['weight_kg'], 1) ?> kg</strong> • 
                    <?= number_format($followup['height_cm'], 1) ?> cm
                </div>
                <div style="font-size:0.75rem;color:var(--kn-muted);margin-top:0.25rem">
                    <?= date('M d, Y', strtotime($followup['assessment_date'])) ?>
                    <span class="mx-1">•</span>
                    WFA: <?= htmlspecialchars($followup['wfa_status'] ?? 'N/A') ?>
                    <span class="mx-1">•</span>
                    MUAC: <?= !empty($followup['muac_cm']) ? number_format((float)$followup['muac_cm'], 1) . ' cm' : 'N/A' ?>
                </div>
            <?php else: ?>
                <div class="assessment-value" style="color:#ffc107">
                    <i class="bi bi-clock"></i> Awaiting follow-up assessment
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($ready): ?>
        <?php 
        $weightGain = $followup['weight_kg'] - $baseline['weight_kg'];
        $heightGain = $followup['height_cm'] - $baseline['height_cm'];
        ?>
        <div style="margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid rgba(0,0,0,.1)">
            <div style="font-size:0.85rem;color:var(--kn-muted);margin-bottom:0.5rem">
                <strong>Preliminary Gains:</strong>
                Weight: <span style="color:<?= $weightGain > 0 ? '#28a745' : '#dc3545' ?>">
                    <?= $weightGain > 0 ? '+' : '' ?><?= number_format($weightGain, 2) ?> kg
                </span>
                <span class="mx-2">•</span>
                Height: <span style="color:<?= $heightGain > 0 ? '#28a745' : '#dc3545' ?>">
                    <?= $heightGain > 0 ? '+' : '' ?><?= number_format($heightGain, 2) ?> cm
                </span>
            </div>
        </div>
    <?php endif; ?>

    <div style="margin-top:0.75rem;text-align:right">
        <?php if ($ready): ?>
            <a href="index.php?action=recoveryValidationForm&proposal_id=<?= $proposal['proposal_id'] ?>&child_name=<?= urlencode($child['full_name']) ?>" 
               class="btn-validate">
                <i class="bi bi-clipboard-check"></i> Validate Recovery
            </a>
        <?php elseif (!$followup): ?>
            <div class="text-end" style="max-width:320px;margin-left:auto">
                <span class="badge bg-warning text-dark mb-2">
                    <i class="bi bi-hourglass-split"></i> Awaiting BNS follow-up
                </span>
                <p style="font-size:0.8rem;color:var(--kn-muted);margin:0">
                    Ask assigned <strong>BNS</strong> to record follow-up assessment sa
                    <em>Resident Assessment</em> (weight, height, MUAC). Refresh this page when done.
                </p>
            </div>
        <?php elseif (!$baseline): ?>
            <!-- Missing baseline assessment -->
            <button class="btn-validate" disabled title="Baseline assessment required before program start">
                <i class="bi bi-exclamation-triangle"></i> Missing Baseline
            </button>
        <?php else: ?>
            <button class="btn-validate" disabled>
                <i class="bi bi-clock"></i> Awaiting Assessment
            </button>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>

<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>
