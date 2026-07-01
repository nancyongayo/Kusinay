<?php include __DIR__ . '/../templates/nutrition_layout.php'; ?>

<style>
.stats-card {
    background: #fff;
    border: 1.5px solid rgba(107,122,58,.15);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.stats-header {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--kn-dark);
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--kn-green);
}
.big-stat {
    text-align: center;
    padding: 2rem 1rem;
}
.big-stat-value {
    font-size: 4rem;
    font-weight: 700;
    color: var(--kn-green);
    line-height: 1;
}
.big-stat-label {
    font-size: 1.1rem;
    color: var(--kn-muted);
    margin-top: 0.5rem;
}
.progress-bar-custom {
    height: 30px;
    border-radius: 15px;
    background: #f0f0f0;
    overflow: hidden;
    margin-bottom: 1rem;
}
.progress-fill {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 0.9rem;
    transition: width 0.5s ease;
}
.progress-recovered { background: #28a745; }
.progress-improving { background: #C4722A; }
.progress-no-progress { background: #ffc107; }
.progress-deteriorating { background: #dc3545; }
.stat-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}
.stat-row:last-child {
    border-bottom: none;
}
.stat-label {
    font-weight: 600;
    color: var(--kn-muted);
}
.stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--kn-dark);
}
.chart-placeholder {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 3rem;
    text-align: center;
    color: var(--kn-muted);
}
.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}
.summary-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.25rem;
    text-align: center;
}
.summary-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--kn-green);
}
.summary-label {
    font-size: 0.85rem;
    color: var(--kn-muted);
    margin-top: 0.5rem;
}
</style>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="index.php?action=recoveryValidation"
       style="display:inline-flex;align-items:center;gap:.4rem;background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.35rem .85rem;font-size:.88rem;font-weight:600;text-decoration:none">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    <div>
        <h4 class="mb-0" style="color:var(--kn-dark);font-weight:700">
            Recovery Statistics
        </h4>
        <p class="mb-0" style="color:var(--kn-muted);font-size:0.85rem">
            <?= htmlspecialchars($proposal['proposal_title']) ?>
        </p>
    </div>
</div>

<?php if (empty($stats) || $stats['total_validations'] == 0): ?>
<div style="text-align:center;padding:3rem 1rem;color:var(--kn-muted);background:#fff;border-radius:12px">
    <i class="bi bi-graph-up" style="font-size:3rem;opacity:0.3;margin-bottom:1rem"></i>
    <h5>No Statistics Available</h5>
    <p>No recovery validations have been recorded yet for this feeding program.</p>
    <a href="index.php?action=recoveryEligibleList&proposal_id=<?= $proposal['proposal_id'] ?>" 
       style="display:inline-flex;align-items:center;gap:0.5rem;background:var(--kn-green);color:#fff;padding:0.75rem 1.5rem;border-radius:8px;text-decoration:none;font-weight:600;margin-top:1rem">
        <i class="bi bi-plus-circle"></i> Start Validating
    </a>
</div>
<?php else: 
    $totalValidations = $stats['total_validations'];
    $recoveredCount = $stats['recovered_count'];
    $improvingCount = $stats['improving_count'];
    $noProgressCount = $stats['no_progress_count'];
    $deterioratingCount = $stats['deteriorating_count'];
    
    $recoveryRate = round(($recoveredCount / $totalValidations) * 100, 1);
    $improvementRate = round((($recoveredCount + $improvingCount) / $totalValidations) * 100, 1);
    
    $recoveredPercent = round(($recoveredCount / $totalValidations) * 100, 1);
    $improvingPercent = round(($improvingCount / $totalValidations) * 100, 1);
    $noProgressPercent = round(($noProgressCount / $totalValidations) * 100, 1);
    $deterioratingPercent = round(($deterioratingCount / $totalValidations) * 100, 1);
?>

<!-- Key Metrics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stats-card">
            <div class="big-stat">
                <div class="big-stat-value"><?= $recoveryRate ?>%</div>
                <div class="big-stat-label">Recovery Rate</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="big-stat">
                <div class="big-stat-value"><?= $improvementRate ?>%</div>
                <div class="big-stat-label">Improvement Rate</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="big-stat">
                <div class="big-stat-value"><?= $totalValidations ?></div>
                <div class="big-stat-label">Total Validated</div>
            </div>
        </div>
    </div>
</div>

<!-- Recovery Status Distribution -->
<div class="stats-card">
    <div class="stats-header">
        <i class="bi bi-pie-chart"></i> Recovery Status Distribution
    </div>
    
    <div class="mb-3">
        <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem">
            <span style="font-weight:600;color:#28a745">Recovered</span>
            <span style="font-weight:700"><?= $recoveredCount ?> (<?= $recoveredPercent ?>%)</span>
        </div>
        <div class="progress-bar-custom">
            <div class="progress-fill progress-recovered" style="width:<?= $recoveredPercent ?>%">
                <?= $recoveredPercent ?>%
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem">
            <span style="font-weight:600;color:#C4722A">Improving</span>
            <span style="font-weight:700"><?= $improvingCount ?> (<?= $improvingPercent ?>%)</span>
        </div>
        <div class="progress-bar-custom">
            <div class="progress-fill progress-improving" style="width:<?= $improvingPercent ?>%">
                <?= $improvingPercent ?>%
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem">
            <span style="font-weight:600;color:#ffc107">No Progress</span>
            <span style="font-weight:700"><?= $noProgressCount ?> (<?= $noProgressPercent ?>%)</span>
        </div>
        <div class="progress-bar-custom">
            <div class="progress-fill progress-no-progress" style="width:<?= $noProgressPercent ?>%">
                <?= $noProgressPercent ?>%
            </div>
        </div>
    </div>
    
    <div>
        <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem">
            <span style="font-weight:600;color:#dc3545">Deteriorating</span>
            <span style="font-weight:700"><?= $deterioratingCount ?> (<?= $deterioratingPercent ?>%)</span>
        </div>
        <div class="progress-bar-custom">
            <div class="progress-fill progress-deteriorating" style="width:<?= $deterioratingPercent ?>%">
                <?= $deterioratingPercent ?>%
            </div>
        </div>
    </div>
</div>

<!-- Average Gains -->
<div class="stats-card">
    <div class="stats-header">
        <i class="bi bi-graph-up-arrow"></i> Average Gains
    </div>
    
    <div class="summary-grid">
        <div class="summary-box">
            <div class="summary-value">
                <?= number_format($stats['avg_weight_gain'] ?? 0, 2) ?>
            </div>
            <div class="summary-label">kg</div>
            <div style="font-size:0.9rem;color:var(--kn-dark);margin-top:0.5rem;font-weight:600">
                Average Weight Gain
            </div>
        </div>
        
        <div class="summary-box">
            <div class="summary-value">
                <?= number_format($stats['avg_height_gain'] ?? 0, 2) ?>
            </div>
            <div class="summary-label">cm</div>
            <div style="font-size:0.9rem;color:var(--kn-dark);margin-top:0.5rem;font-weight:600">
                Average Height Gain
            </div>
        </div>
        
        <div class="summary-box">
            <div class="summary-value">
                <?= number_format($stats['avg_attendance_rate'] ?? 0, 1) ?>%
            </div>
            <div class="summary-label">attendance</div>
            <div style="font-size:0.9rem;color:var(--kn-dark);margin-top:0.5rem;font-weight:600">
                Average Attendance
            </div>
        </div>
    </div>
</div>

<!-- Detailed Breakdown -->
<div class="stats-card">
    <div class="stats-header">
        <i class="bi bi-list-check"></i> Detailed Breakdown
    </div>
    
    <div class="stat-row">
        <div class="stat-label">Total Children Validated</div>
        <div class="stat-value"><?= $totalValidations ?></div>
    </div>
    
    <div class="stat-row">
        <div class="stat-label">Successfully Recovered</div>
        <div class="stat-value" style="color:#28a745"><?= $recoveredCount ?></div>
    </div>
    
    <div class="stat-row">
        <div class="stat-label">Showing Improvement</div>
        <div class="stat-value" style="color:#C4722A"><?= $improvingCount ?></div>
    </div>
    
    <div class="stat-row">
        <div class="stat-label">No Significant Progress</div>
        <div class="stat-value" style="color:#ffc107"><?= $noProgressCount ?></div>
    </div>
    
    <div class="stat-row">
        <div class="stat-label">Deteriorating Condition</div>
        <div class="stat-value" style="color:#dc3545"><?= $deterioratingCount ?></div>
    </div>
    
    <div class="stat-row">
        <div class="stat-label">Recovery Success Rate</div>
        <div class="stat-value" style="color:var(--kn-green)"><?= $recoveryRate ?>%</div>
    </div>
    
    <div class="stat-row">
        <div class="stat-label">Overall Improvement Rate</div>
        <div class="stat-value" style="color:var(--kn-green)"><?= $improvementRate ?>%</div>
    </div>
</div>

<!-- Program Effectiveness -->
<div class="stats-card">
    <div class="stats-header">
        <i class="bi bi-award"></i> Program Effectiveness
    </div>
    
    <div style="background:#f8f9fa;border-radius:8px;padding:1.5rem;margin-bottom:1rem">
        <h5 style="color:var(--kn-dark);font-weight:700;margin-bottom:1rem">Overall Assessment</h5>
        <?php if ($recoveryRate >= 70): ?>
            <div style="color:#28a745;font-size:1.1rem;font-weight:600;margin-bottom:0.5rem">
                <i class="bi bi-check-circle-fill"></i> Highly Effective Program
            </div>
            <p style="color:var(--kn-muted);margin-bottom:0">
                This feeding program has achieved an excellent recovery rate of <?= $recoveryRate ?>%. 
                The majority of children have shown significant improvement in their nutritional status.
            </p>
        <?php elseif ($recoveryRate >= 50): ?>
            <div style="color:#C4722A;font-size:1.1rem;font-weight:600;margin-bottom:0.5rem">
                <i class="bi bi-check-circle"></i> Effective Program
            </div>
            <p style="color:var(--kn-muted);margin-bottom:0">
                This feeding program has achieved a good recovery rate of <?= $recoveryRate ?>%. 
                Most children have shown positive progress in their nutritional status.
            </p>
        <?php elseif ($recoveryRate >= 30): ?>
            <div style="color:#ffc107;font-size:1.1rem;font-weight:600;margin-bottom:0.5rem">
                <i class="bi bi-exclamation-circle"></i> Moderately Effective Program
            </div>
            <p style="color:var(--kn-muted);margin-bottom:0">
                This feeding program has achieved a moderate recovery rate of <?= $recoveryRate ?>%. 
                Some children have recovered, but there is room for improvement in program implementation.
            </p>
        <?php else: ?>
            <div style="color:#dc3545;font-size:1.1rem;font-weight:600;margin-bottom:0.5rem">
                <i class="bi bi-x-circle"></i> Needs Improvement
            </div>
            <p style="color:var(--kn-muted);margin-bottom:0">
                This feeding program has achieved a recovery rate of <?= $recoveryRate ?>%. 
                Program implementation and intervention strategies may need to be reviewed and improved.
            </p>
        <?php endif; ?>
    </div>
    
    <div style="background:#e7f3ff;border-left:4px solid #0066cc;padding:1rem;border-radius:8px">
        <strong style="color:#0066cc"><i class="bi bi-info-circle"></i> Key Insights:</strong>
        <ul style="margin-top:0.5rem;margin-bottom:0;padding-left:1.5rem">
            <li>Average weight gain: <strong><?= number_format($stats['avg_weight_gain'] ?? 0, 2) ?> kg</strong></li>
            <li>Average height gain: <strong><?= number_format($stats['avg_height_gain'] ?? 0, 2) ?> cm</strong></li>
            <li>Average attendance: <strong><?= number_format($stats['avg_attendance_rate'] ?? 0, 1) ?>%</strong></li>
            <li>Improvement rate (recovered + improving): <strong><?= $improvementRate ?>%</strong></li>
        </ul>
    </div>
</div>

<!-- Actions -->
<div class="text-center" style="margin-top:2rem">
    <a href="index.php?action=recoveryValidationList&proposal_id=<?= $proposal['proposal_id'] ?>" 
       style="display:inline-flex;align-items:center;gap:0.5rem;background:var(--kn-green);color:#fff;padding:0.75rem 1.5rem;border-radius:8px;text-decoration:none;font-weight:600;margin-right:1rem">
        <i class="bi bi-list-ul"></i> View All Validations
    </a>
    <a href="index.php?action=recoveryEligibleList&proposal_id=<?= $proposal['proposal_id'] ?>" 
       style="display:inline-flex;align-items:center;gap:0.5rem;background:#fff;color:var(--kn-green);border:1.5px solid var(--kn-green);padding:0.75rem 1.5rem;border-radius:8px;text-decoration:none;font-weight:600">
        <i class="bi bi-plus-circle"></i> Add More Validations
    </a>
</div>

<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>
