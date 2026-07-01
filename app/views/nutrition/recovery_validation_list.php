<?php include __DIR__ . '/../templates/nutrition_layout.php'; ?>

<style>
.stats-summary {
    background: linear-gradient(135deg, var(--kn-green) 0%, #5a6e3a 100%);
    color: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}
.stat-card {
    background: rgba(255,255,255,0.15);
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
}
.stat-value {
    font-size: 2rem;
    font-weight: 700;
}
.stat-label {
    font-size: 0.85rem;
    opacity: 0.9;
    margin-top: 0.25rem;
}
.validation-table {
    background: #fff;
    border: 1.5px solid rgba(107,122,58,.15);
    border-radius: 12px;
    overflow: hidden;
}
.table {
    margin-bottom: 0;
}
.table thead {
    background: #f8f9fa;
}
.table th {
    font-weight: 700;
    color: var(--kn-dark);
    border-bottom: 2px solid var(--kn-green);
    padding: 1rem;
    font-size: 0.85rem;
}
.table td {
    padding: 1rem;
    vertical-align: middle;
}
.status-badge {
    display: inline-block;
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}
.status-recovered { background: #d4edda; color: #155724; }
.status-improving { background: #d1ecf1; color: #0c5460; }
.status-no-progress { background: #fff3cd; color: #856404; }
.status-deteriorating { background: #f8d7da; color: #721c24; }
.btn-view {
    background: var(--kn-green);
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 0.4rem 0.85rem;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    transition: all 0.2s;
}
.btn-view:hover {
    background: #5a6e3a;
    color: #fff;
}
.filter-bar {
    background: #fff;
    border: 1.5px solid rgba(107,122,58,.15);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}
.gain-positive {
    color: #28a745;
    font-weight: 600;
}
.gain-negative {
    color: #dc3545;
    font-weight: 600;
}
</style>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="index.php?action=recoveryValidation"
       style="display:inline-flex;align-items:center;gap:.4rem;background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.35rem .85rem;font-size:.88rem;font-weight:600;text-decoration:none">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    <div>
        <h4 class="mb-0" style="color:var(--kn-dark);font-weight:700">
            Recovery Validations
        </h4>
        <p class="mb-0" style="color:var(--kn-muted);font-size:0.85rem">
            <?= htmlspecialchars($proposal['proposal_title']) ?>
        </p>
    </div>
</div>

<!-- Statistics Summary -->
<?php if (!empty($stats) && $stats['total_validations'] > 0): ?>
<div class="stats-summary">
    <h5 style="margin-bottom:0.5rem;font-weight:700">
        <i class="bi bi-graph-up"></i> Recovery Statistics
    </h5>
    <p style="opacity:0.9;margin-bottom:0;font-size:0.9rem">
        Program Duration: <?= date('M d, Y', strtotime($proposal['start_date'])) ?> - 
        <?= date('M d, Y', strtotime($proposal['end_date'])) ?>
    </p>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?= $stats['total_validations'] ?></div>
            <div class="stat-label">Total Validated</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $stats['recovered_count'] ?></div>
            <div class="stat-label">Recovered</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $stats['improving_count'] ?></div>
            <div class="stat-label">Improving</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $stats['no_progress_count'] ?></div>
            <div class="stat-label">No Progress</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $stats['deteriorating_count'] ?></div>
            <div class="stat-label">Deteriorating</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">
                <?= $stats['total_validations'] > 0 ? round(($stats['recovered_count'] / $stats['total_validations']) * 100, 1) : 0 ?>%
            </div>
            <div class="stat-label">Recovery Rate</div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Filter Bar -->
<div class="filter-bar">
    <div class="row align-items-center">
        <div class="col-md-6">
            <strong style="color:var(--kn-dark)">
                <i class="bi bi-list-check"></i> 
                <?= count($validations) ?> Validation<?= count($validations) !== 1 ? 's' : '' ?> Recorded
            </strong>
        </div>
        <div class="col-md-6 text-end">
            <a href="index.php?action=recoveryStatistics&proposal_id=<?= $proposal['proposal_id'] ?>" 
               class="btn-view">
                <i class="bi bi-graph-up"></i> View Statistics
            </a>
            <a href="index.php?action=recoveryEligibleList&proposal_id=<?= $proposal['proposal_id'] ?>" 
               class="btn-view ms-2">
                <i class="bi bi-plus-circle"></i> Add Validation
            </a>
        </div>
    </div>
</div>

<!-- Validations Table -->
<?php if (empty($validations)): ?>
<div style="text-align:center;padding:3rem 1rem;color:var(--kn-muted);background:#fff;border-radius:12px">
    <i class="bi bi-clipboard-x" style="font-size:3rem;opacity:0.3;margin-bottom:1rem"></i>
    <h5>No Validations Yet</h5>
    <p>No recovery validations have been recorded for this feeding program.</p>
    <a href="index.php?action=recoveryEligibleList&proposal_id=<?= $proposal['proposal_id'] ?>" 
       class="btn-view mt-3">
        <i class="bi bi-plus-circle"></i> Start Validating
    </a>
</div>
<?php else: ?>

<div class="validation-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Child Name</th>
                <th>Recovery Status</th>
                <th>Weight Gain</th>
                <th>Height Gain</th>
                <th>Attendance</th>
                <th>Validated By</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($validations as $validation): ?>
            <tr>
                <td>
                    <strong style="color:var(--kn-dark)">
                        <?= htmlspecialchars($validation['full_name']) ?>
                    </strong>
                </td>
                <td>
                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $validation['recovery_status'])) ?>">
                        <?= htmlspecialchars($validation['recovery_status']) ?>
                    </span>
                </td>
                <td>
                    <span class="<?= $validation['weight_gain_kg'] >= 0 ? 'gain-positive' : 'gain-negative' ?>">
                        <?= $validation['weight_gain_kg'] >= 0 ? '+' : '' ?><?= number_format($validation['weight_gain_kg'], 2) ?> kg
                    </span>
                </td>
                <td>
                    <span class="<?= $validation['height_gain_cm'] >= 0 ? 'gain-positive' : 'gain-negative' ?>">
                        <?= $validation['height_gain_cm'] >= 0 ? '+' : '' ?><?= number_format($validation['height_gain_cm'], 2) ?> cm
                    </span>
                </td>
                <td>
                    <?= number_format($validation['attendance_rate'], 1) ?>%
                </td>
                <td>
                    <div style="font-size:0.85rem">
                        <?= htmlspecialchars($validation['validator_first_name'] . ' ' . $validation['validator_last_name']) ?>
                    </div>
                </td>
                <td>
                    <div style="font-size:0.85rem">
                        <?= date('M d, Y', strtotime($validation['validation_date'])) ?>
                    </div>
                    <div style="font-size:0.75rem;color:var(--kn-muted)">
                        <?= date('g:i A', strtotime($validation['validation_date'])) ?>
                    </div>
                </td>
                <td>
                    <a href="index.php?action=recoveryValidationDetail&validation_id=<?= $validation['validation_id'] ?>" 
                       class="btn-view">
                        <i class="bi bi-eye"></i> View
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>
