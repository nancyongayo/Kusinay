<?php include __DIR__ . '/../templates/nutrition_layout.php'; ?>

<style>
.program-card {
    background: #fff;
    border: 1.5px solid rgba(107,122,58,.15);
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: all 0.2s;
}
.program-card:hover {
    border-color: var(--kn-green);
    box-shadow: 0 4px 12px rgba(107,122,58,.1);
}
.program-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1rem;
}
.program-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--kn-dark);
    margin-bottom: 0.25rem;
}
.program-meta {
    font-size: 0.85rem;
    color: var(--kn-muted);
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.75rem;
    margin-top: 1rem;
}
.stat-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.75rem;
    text-align: center;
}
.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--kn-green);
}
.stat-label {
    font-size: 0.75rem;
    color: var(--kn-muted);
    margin-top: 0.25rem;
}
.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status-recovered { background: #d4edda; color: #155724; }
.status-improving { background: #d1ecf1; color: #0c5460; }
.status-no-progress { background: #fff3cd; color: #856404; }
.status-deteriorating { background: #f8d7da; color: #721c24; }
.action-buttons {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}
.btn-action {
    flex: 1;
    padding: 0.5rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    transition: all 0.2s;
}
.btn-primary-action {
    background: var(--kn-green);
    color: #fff;
    border: none;
}
.btn-primary-action:hover {
    background: #5a6e3a;
    color: #fff;
}
.btn-secondary-action {
    background: #fff;
    color: var(--kn-green);
    border: 1.5px solid var(--kn-green);
}
.btn-secondary-action:hover {
    background: var(--kn-green);
    color: #fff;
}
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--kn-muted);
}
.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1" style="color:var(--kn-dark);font-weight:700">Recovery Validation</h4>
        <p class="mb-0" style="color:var(--kn-muted);font-size:0.9rem">
            <i class="bi bi-heart-pulse me-1"></i> Validate nutritional recovery (OTC-style: weight, height, MUAC, WFA/HFA/WFH)
        </p>
    </div>
</div>

<?php if (empty($proposals)): ?>
<div class="empty-state">
    <i class="bi bi-clipboard-data"></i>
    <h5>No Feeding Programs Found</h5>
    <p>There are no approved feeding programs available for recovery validation.</p>
</div>
<?php else: ?>

<?php foreach ($proposals as $proposal): 
    $stats = $proposal['recovery_stats'];
    $eligibleCount = $proposal['eligible_count'];
    $totalValidated = $stats['total_validations'] ?? 0;
    $recoveredCount = $stats['recovered_count'] ?? 0;
    $improvingCount = $stats['improving_count'] ?? 0;
    $noProgressCount = $stats['no_progress_count'] ?? 0;
    $deterioratingCount = $stats['deteriorating_count'] ?? 0;
    
    $recoveryRate = $totalValidated > 0 ? round(($recoveredCount / $totalValidated) * 100, 1) : 0;
    $improvementRate = $totalValidated > 0 ? round((($recoveredCount + $improvingCount) / $totalValidated) * 100, 1) : 0;
?>
<div class="program-card">
    <div class="program-header">
        <div>
            <div class="program-title"><?= htmlspecialchars($proposal['proposal_title']) ?></div>
            <div class="program-meta">
                <span class="badge bg-secondary me-1">Program #<?= (int)$proposal['proposal_id'] ?></span>
                <i class="bi bi-calendar3"></i>
                <?= date('M d, Y', strtotime($proposal['start_date'])) ?> -
                <?= date('M d, Y', strtotime($proposal['end_date'])) ?>
                <span class="mx-2">•</span>
                <i class="bi bi-people"></i> <?= $proposal['num_beneficiaries'] ?> beneficiaries
            </div>
        </div>
        <div class="d-flex flex-wrap gap-1 justify-content-end">
            <?php $readyCount = (int)($proposal['ready_count'] ?? 0); ?>
            <?php if ($readyCount > 0): ?>
                <span class="badge bg-success">
                    <i class="bi bi-check-circle"></i> <?= $readyCount ?> Ready
                </span>
            <?php endif; ?>
            <?php $pendingCount = max(0, $eligibleCount - $readyCount); ?>
            <?php if ($pendingCount > 0): ?>
                <span class="badge bg-warning text-dark">
                    <i class="bi bi-clock"></i> <?= $pendingCount ?> Need follow-up
                </span>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($totalValidated > 0): ?>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-value"><?= $totalValidated ?></div>
            <div class="stat-label">Total Validated</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color:#28a745"><?= $recoveredCount ?></div>
            <div class="stat-label">Recovered</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color:#C4722A"><?= $improvingCount ?></div>
            <div class="stat-label">Improving</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color:#ffc107"><?= $noProgressCount ?></div>
            <div class="stat-label">No Progress</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color:#dc3545"><?= $deterioratingCount ?></div>
            <div class="stat-label">Deteriorating</div>
        </div>
        <div class="stat-box">
            <div class="stat-value"><?= $recoveryRate ?>%</div>
            <div class="stat-label">Recovery Rate</div>
        </div>
    </div>

    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid rgba(0,0,0,.1)">
        <div style="font-size:0.85rem;color:var(--kn-muted);margin-bottom:0.5rem">
            <strong>Average Gains:</strong>
            Weight: <?= number_format($stats['avg_weight_gain'] ?? 0, 2) ?> kg •
            Height: <?= number_format($stats['avg_height_gain'] ?? 0, 2) ?> cm •
            Attendance: <?= number_format($stats['avg_attendance_rate'] ?? 0, 1) ?>%
        </div>
    </div>
    <?php else: ?>
    <div style="padding:1rem;background:#f8f9fa;border-radius:8px;text-align:center;color:var(--kn-muted)">
        <i class="bi bi-info-circle me-1"></i> No recovery validations yet
    </div>
    <?php endif; ?>

    <div class="action-buttons">
        <?php if ($eligibleCount > 0): ?>
        <a href="index.php?action=recoveryEligibleList&proposal_id=<?= $proposal['proposal_id'] ?>" 
           class="btn-action btn-primary-action">
            <i class="bi bi-clipboard-check"></i> Validate Recovery (<?= $eligibleCount ?>)
        </a>
        <?php endif; ?>
        
        <?php if ($totalValidated > 0): ?>
        <a href="index.php?action=recoveryValidationList&proposal_id=<?= $proposal['proposal_id'] ?>" 
           class="btn-action btn-secondary-action">
            <i class="bi bi-list-ul"></i> View Validations (<?= $totalValidated ?>)
        </a>
        <a href="index.php?action=recoveryStatistics&proposal_id=<?= $proposal['proposal_id'] ?>" 
           class="btn-action btn-secondary-action">
            <i class="bi bi-graph-up"></i> Statistics
        </a>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>

<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>
