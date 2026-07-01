<?php include __DIR__ . '/../templates/nutrition_layout.php'; ?>

<style>
.detail-section {
    background: #fff;
    border: 1.5px solid rgba(107,122,58,.15);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--kn-green);
}
.section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--kn-dark);
}
.info-row {
    display: flex;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}
.info-row:last-child {
    border-bottom: none;
}
.info-label {
    flex: 0 0 200px;
    font-weight: 600;
    color: var(--kn-muted);
    font-size: 0.9rem;
}
.info-value {
    flex: 1;
    color: var(--kn-dark);
    font-size: 0.95rem;
}
.comparison-table {
    width: 100%;
    border-collapse: collapse;
}
.comparison-table th {
    background: #f8f9fa;
    padding: 0.75rem;
    text-align: left;
    font-weight: 700;
    color: var(--kn-dark);
    border-bottom: 2px solid var(--kn-green);
}
.comparison-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #f0f0f0;
}
.comparison-table tr:last-child td {
    border-bottom: none;
}
.gain-cell {
    font-weight: 700;
}
.gain-positive {
    color: #28a745;
}
.gain-negative {
    color: #dc3545;
}
.status-badge-large {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-size: 1.1rem;
    font-weight: 700;
}
.status-recovered { background: #d4edda; color: #155724; }
.status-improving { background: #d1ecf1; color: #0c5460; }
.status-no-progress { background: #fff3cd; color: #856404; }
.status-deteriorating { background: #f8d7da; color: #721c24; }
.btn-action {
    padding: 0.5rem 1.25rem;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}
.btn-delete {
    background: #dc3545;
    color: #fff;
    border: none;
}
.btn-delete:hover {
    background: #c82333;
    color: #fff;
}
.btn-back {
    background: #fff;
    color: var(--kn-dark);
    border: 1.5px solid rgba(107,122,58,.25);
}
.btn-back:hover {
    background: #f8f9fa;
    color: var(--kn-dark);
}
</style>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="index.php?action=recoveryValidationList&proposal_id=<?= $validation['proposal_id'] ?>" 
           class="btn-action btn-back">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
        <div>
            <h4 class="mb-0" style="color:var(--kn-dark);font-weight:700">
                Recovery Validation Detail
            </h4>
            <p class="mb-0" style="color:var(--kn-muted);font-size:0.85rem">
                <?= htmlspecialchars($validation['full_name']) ?>
            </p>
        </div>
    </div>
    <div>
        <form method="POST" action="index.php?action=deleteRecoveryValidation" style="display:inline" 
              onsubmit="return confirm('Are you sure you want to delete this recovery validation? This action cannot be undone.');">
            <input type="hidden" name="validation_id" value="<?= $validation['validation_id'] ?>">
            <button type="submit" class="btn-action btn-delete">
                <i class="bi bi-trash"></i> Delete
            </button>
        </form>
    </div>
</div>

<!-- Child & Program Information -->
<div class="detail-section">
    <div class="section-header">
        <div class="section-title">
            <i class="bi bi-person-badge"></i> Child & Program Information
        </div>
    </div>
    
    <div class="info-row">
        <div class="info-label">Child Name:</div>
        <div class="info-value"><strong><?= htmlspecialchars($validation['full_name']) ?></strong></div>
    </div>
    <div class="info-row">
        <div class="info-label">Feeding Program:</div>
        <div class="info-value"><?= htmlspecialchars($validation['proposal_title']) ?></div>
    </div>
    <div class="info-row">
        <div class="info-label">Program Type:</div>
        <div class="info-value"><?= htmlspecialchars($validation['program_type']) ?></div>
    </div>
    <div class="info-row">
        <div class="info-label">Program Duration:</div>
        <div class="info-value">
            <?= date('M d, Y', strtotime($validation['program_start_date'])) ?> - 
            <?= date('M d, Y', strtotime($validation['program_end_date'])) ?>
            (<?= $validation['days_in_program'] ?> days)
        </div>
    </div>
    <div class="info-row">
        <div class="info-label">Attendance Rate:</div>
        <div class="info-value">
            <strong><?= number_format($validation['attendance_rate'], 1) ?>%</strong>
        </div>
    </div>
</div>

<!-- Recovery Status -->
<div class="detail-section">
    <div class="section-header">
        <div class="section-title">
            <i class="bi bi-heart-pulse"></i> Recovery Status
        </div>
        <div>
            <span class="status-badge-large status-<?= strtolower(str_replace(' ', '-', $validation['recovery_status'])) ?>">
                <?= htmlspecialchars($validation['recovery_status']) ?>
            </span>
        </div>
    </div>
    
    <div class="info-row">
        <div class="info-label">Validated By:</div>
        <div class="info-value">
            <?= htmlspecialchars($validation['validator_first_name'] . ' ' . $validation['validator_last_name']) ?>
            <span style="color:var(--kn-muted);font-size:0.85rem">(Nutrition Officer II)</span>
        </div>
    </div>
    <div class="info-row">
        <div class="info-label">Validation Date:</div>
        <div class="info-value">
            <?= date('F d, Y', strtotime($validation['validation_date'])) ?> at 
            <?= date('g:i A', strtotime($validation['validation_date'])) ?>
        </div>
    </div>
</div>

<!-- Measurement Comparison -->
<div class="detail-section">
    <div class="section-header">
        <div class="section-title">
            <i class="bi bi-speedometer2"></i> Measurement Comparison
        </div>
    </div>
    
    <table class="comparison-table">
        <thead>
            <tr>
                <th>Measurement</th>
                <th>Baseline</th>
                <th>Follow-up</th>
                <th>Gain/Loss</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Weight</strong></td>
                <td>
                    <?= number_format($validation['baseline_weight_kg'], 2) ?> kg
                    <div style="font-size:0.75rem;color:var(--kn-muted)">
                        <?= date('M d, Y', strtotime($validation['baseline_date'])) ?>
                    </div>
                </td>
                <td>
                    <?= number_format($validation['followup_weight_kg'], 2) ?> kg
                    <div style="font-size:0.75rem;color:var(--kn-muted)">
                        <?= date('M d, Y', strtotime($validation['followup_date'])) ?>
                    </div>
                </td>
                <td class="gain-cell <?= $validation['weight_gain_kg'] >= 0 ? 'gain-positive' : 'gain-negative' ?>">
                    <?= $validation['weight_gain_kg'] >= 0 ? '+' : '' ?><?= number_format($validation['weight_gain_kg'], 2) ?> kg
                </td>
            </tr>
            <tr>
                <td><strong>Height</strong></td>
                <td><?= number_format($validation['baseline_height_cm'], 2) ?> cm</td>
                <td><?= number_format($validation['followup_height_cm'], 2) ?> cm</td>
                <td class="gain-cell <?= $validation['height_gain_cm'] >= 0 ? 'gain-positive' : 'gain-negative' ?>">
                    <?= $validation['height_gain_cm'] >= 0 ? '+' : '' ?><?= number_format($validation['height_gain_cm'], 2) ?> cm
                </td>
            </tr>
            <tr>
                <td><strong>MUAC</strong></td>
                <td><?= $validation['baseline_muac_cm'] !== null && $validation['baseline_muac_cm'] !== '' ? number_format((float)$validation['baseline_muac_cm'], 1) . ' cm' : 'N/A' ?></td>
                <td><?= $validation['followup_muac_cm'] !== null && $validation['followup_muac_cm'] !== '' ? number_format((float)$validation['followup_muac_cm'], 1) . ' cm' : 'N/A' ?></td>
                <td class="gain-cell">
                    <?php if ($validation['muac_gain_cm'] !== null && $validation['muac_gain_cm'] !== ''): ?>
                    <span class="<?= $validation['muac_gain_cm'] >= 0 ? 'gain-positive' : 'gain-negative' ?>">
                        <?= $validation['muac_gain_cm'] >= 0 ? '+' : '' ?><?= number_format((float)$validation['muac_gain_cm'], 1) ?> cm
                    </span>
                    <?php else: ?>
                    <span style="color:var(--kn-muted)">N/A</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if ($validation['baseline_bmi'] && $validation['followup_bmi']): ?>
            <tr>
                <td><strong>BMI</strong></td>
                <td><?= number_format($validation['baseline_bmi'], 2) ?></td>
                <td><?= number_format($validation['followup_bmi'], 2) ?></td>
                <td class="gain-cell <?= ($validation['followup_bmi'] - $validation['baseline_bmi']) >= 0 ? 'gain-positive' : 'gain-negative' ?>">
                    <?= ($validation['followup_bmi'] - $validation['baseline_bmi']) >= 0 ? '+' : '' ?>
                    <?= number_format($validation['followup_bmi'] - $validation['baseline_bmi'], 2) ?>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Nutritional Status Comparison -->
<div class="detail-section">
    <div class="section-header">
        <div class="section-title">
            <i class="bi bi-clipboard-data"></i> Nutritional Status Comparison
        </div>
    </div>
    
    <table class="comparison-table">
        <thead>
            <tr>
                <th>Indicator</th>
                <th>Baseline Status</th>
                <th>Follow-up Status</th>
                <th>Change</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Weight-for-Age</strong></td>
                <td><span class="badge bg-secondary"><?= htmlspecialchars($validation['baseline_wfa_status'] ?? 'N/A') ?></span></td>
                <td><span class="badge bg-info"><?= htmlspecialchars($validation['followup_wfa_status'] ?? 'N/A') ?></span></td>
                <td>
                    <?php 
                    $improved = ($validation['baseline_wfa_status'] !== $validation['followup_wfa_status']);
                    echo $improved ? '<i class="bi bi-arrow-up-circle-fill gain-positive"></i> Changed' : '<i class="bi bi-dash-circle"></i> Same';
                    ?>
                </td>
            </tr>
            <tr>
                <td><strong>Height-for-Age</strong></td>
                <td><span class="badge bg-secondary"><?= htmlspecialchars($validation['baseline_hfa_status'] ?? 'N/A') ?></span></td>
                <td><span class="badge bg-info"><?= htmlspecialchars($validation['followup_hfa_status'] ?? 'N/A') ?></span></td>
                <td>
                    <?php 
                    $improved = ($validation['baseline_hfa_status'] !== $validation['followup_hfa_status']);
                    echo $improved ? '<i class="bi bi-arrow-up-circle-fill gain-positive"></i> Changed' : '<i class="bi bi-dash-circle"></i> Same';
                    ?>
                </td>
            </tr>
            <tr>
                <td><strong>Weight-for-Height</strong></td>
                <td><span class="badge bg-secondary"><?= htmlspecialchars($validation['baseline_wfh_status'] ?? 'N/A') ?></span></td>
                <td><span class="badge bg-info"><?= htmlspecialchars($validation['followup_wfh_status'] ?? 'N/A') ?></span></td>
                <td>
                    <?php 
                    $improved = ($validation['baseline_wfh_status'] !== $validation['followup_wfh_status']);
                    echo $improved ? '<i class="bi bi-arrow-up-circle-fill gain-positive"></i> Changed' : '<i class="bi bi-dash-circle"></i> Same';
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Remarks & Recommendation -->
<?php if ($validation['remarks'] || $validation['recommendation']): ?>
<div class="detail-section">
    <div class="section-header">
        <div class="section-title">
            <i class="bi bi-chat-left-text"></i> Remarks & Recommendation
        </div>
    </div>
    
    <?php if ($validation['remarks']): ?>
    <div class="info-row">
        <div class="info-label">Remarks:</div>
        <div class="info-value"><?= nl2br(htmlspecialchars($validation['remarks'])) ?></div>
    </div>
    <?php endif; ?>
    
    <?php if ($validation['recommendation']): ?>
    <div class="info-row">
        <div class="info-label">Recommendation:</div>
        <div class="info-value"><strong><?= nl2br(htmlspecialchars($validation['recommendation'])) ?></strong></div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>
