<?php include __DIR__ . '/../templates/bns_layout.php'; ?>

<style>
.info-banner {
    background: linear-gradient(135deg, #6B7A3A 0%, #556030 100%);
    color: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.stats-summary {
    background: #fff;
    border: 1.5px solid rgba(107,122,58,.15);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}
.stat-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
}
.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--kn-green);
}
.stat-label {
    font-size: 0.85rem;
    color: var(--kn-muted);
    margin-top: 0.5rem;
}
.validation-card {
    background: #fff;
    border: 1.5px solid rgba(107,122,58,.15);
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: all 0.2s;
}
.validation-card:hover {
    border-color: var(--kn-green);
    box-shadow: 0 2px 8px rgba(107,122,58,.1);
}
.child-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--kn-dark);
    margin-bottom: 0.5rem;
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
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}
.info-item {
    font-size: 0.9rem;
}
.info-label {
    color: var(--kn-muted);
    font-weight: 600;
    margin-bottom: 0.25rem;
}
.info-value {
    color: var(--kn-dark);
}
.gain-positive {
    color: #28a745;
    font-weight: 700;
}
.gain-negative {
    color: #dc3545;
    font-weight: 700;
}
</style>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="index.php?action=feedingProgramList"
       style="display:inline-flex;align-items:center;gap:.4rem;background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.35rem .85rem;font-size:.88rem;font-weight:600;text-decoration:none">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    <div>
        <h4 class="mb-0" style="color:var(--kn-dark);font-weight:700">
            Recovery Status
        </h4>
        <p class="mb-0" style="color:var(--kn-muted);font-size:0.85rem">
            <?= htmlspecialchars($proposal['proposal_title']) ?>
        </p>
    </div>
</div>

<!-- BNS: children needing follow-up assessment -->
<?php if (!empty($needsFollowup)): ?>
<div class="alert alert-warning mb-4" style="border-radius:12px;border:none">
    <h6 class="fw-bold mb-2"><i class="bi bi-clipboard-plus"></i> Your task: Record follow-up assessments</h6>
    <p class="mb-2 small">Before the Nutrition Officer II can validate recovery, you must save a <strong>follow-up</strong> assessment
    (weight, height, MUAC) for each child below — via <strong>Resident Assessment</strong>.</p>
    <ul class="mb-0 small">
        <?php foreach ($needsFollowup as $nf): ?>
        <li class="mb-2">
            <strong><?= htmlspecialchars($nf['full_name']) ?></strong>
            — <?= $nf['needs'] === 'baseline_and_followup' ? 'needs baseline &amp; follow-up' : 'needs follow-up' ?>
            <?php
            $assessUrl = 'index.php?action=assessmentForm&type=child';
            if (!empty($nf['child_id'])) {
                $assessUrl .= '&child_id=' . (int)$nf['child_id'];
            } elseif (!empty($nf['fm_member_id'])) {
                $assessUrl .= '&fm_member_id=' . (int)$nf['fm_member_id'];
            }
            $assessUrl .= '&proposal_id=' . (int)$proposal['proposal_id'];
            ?>
            <a href="<?= htmlspecialchars($assessUrl) ?>" class="btn btn-sm btn-dark ms-1">Conduct assessment</a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<!-- Info Banner -->
<div class="info-banner">
    <div style="display:flex;align-items:center;gap:1rem">
        <div style="font-size:3rem">
            <i class="bi bi-info-circle-fill"></i>
        </div>
        <div>
            <h5 style="margin-bottom:0.5rem;font-weight:700">BNS &amp; NO II roles</h5>
            <p style="margin-bottom:0;opacity:0.9">
                <strong>You (BNS):</strong> Conduct baseline &amp; follow-up assessments.
                <strong>Nutrition Officer II:</strong> Validates recovery and gives recommendations (including hospital referral if needed).
                Validations below are read-only for you.
            </p>
        </div>
    </div>
</div>

<!-- Statistics Summary -->
<?php if (!empty($stats) && $stats['total_validations'] > 0): ?>
<div class="stats-summary">
    <h5 style="font-weight:700;color:var(--kn-dark);margin-bottom:1.5rem">
        <i class="bi bi-graph-up"></i> Program Recovery Statistics
    </h5>
    
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-value"><?= $stats['total_validations'] ?></div>
            <div class="stat-label">Total Validated</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color:#28a745"><?= $stats['recovered_count'] ?></div>
            <div class="stat-label">Recovered</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color:#C4722A"><?= $stats['improving_count'] ?></div>
            <div class="stat-label">Improving</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color:#ffc107"><?= $stats['no_progress_count'] ?></div>
            <div class="stat-label">No Progress</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color:#dc3545"><?= $stats['deteriorating_count'] ?></div>
            <div class="stat-label">Deteriorating</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">
                <?= $stats['total_validations'] > 0 ? round(($stats['recovered_count'] / $stats['total_validations']) * 100, 1) : 0 ?>%
            </div>
            <div class="stat-label">Recovery Rate</div>
        </div>
    </div>
    
    <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid #e0e0e0">
        <div style="font-size:0.9rem;color:var(--kn-muted)">
            <strong>Average Gains:</strong>
            Weight: <?= number_format($stats['avg_weight_gain'] ?? 0, 2) ?> kg •
            Height: <?= number_format($stats['avg_height_gain'] ?? 0, 2) ?> cm •
            Attendance: <?= number_format($stats['avg_attendance_rate'] ?? 0, 1) ?>%
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Validations List -->
<div style="margin-bottom:1rem">
    <h5 style="font-weight:700;color:var(--kn-dark)">
        <i class="bi bi-list-check"></i> Recovery Validations (<?= count($validations) ?>)
    </h5>
</div>

<?php if (empty($validations)): ?>
<div style="text-align:center;padding:3rem 1rem;color:var(--kn-muted);background:#fff;border-radius:12px">
    <i class="bi bi-clipboard-x" style="font-size:3rem;opacity:0.3;margin-bottom:1rem"></i>
    <h5>No Validations Yet</h5>
    <p>No recovery validations have been recorded for this feeding program yet.<br>
    The Nutrition Officer II will validate the recovery status of children after the program ends.</p>
</div>
<?php else: ?>

<?php foreach ($validations as $validation): ?>
<div class="validation-card">
    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:1rem">
        <div>
            <div class="child-name"><?= htmlspecialchars($validation['full_name']) ?></div>
            <div style="font-size:0.85rem;color:var(--kn-muted)">
                <i class="bi bi-calendar-check"></i> 
                Validated on <?= date('M d, Y', strtotime($validation['validation_date'])) ?>
                by <?= htmlspecialchars($validation['validator_first_name'] . ' ' . $validation['validator_last_name']) ?>
            </div>
        </div>
        <div>
            <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $validation['recovery_status'])) ?>">
                <?= htmlspecialchars($validation['recovery_status']) ?>
            </span>
        </div>
    </div>
    
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Weight Gain</div>
            <div class="info-value">
                <span class="<?= $validation['weight_gain_kg'] >= 0 ? 'gain-positive' : 'gain-negative' ?>">
                    <?= $validation['weight_gain_kg'] >= 0 ? '+' : '' ?><?= number_format($validation['weight_gain_kg'], 2) ?> kg
                </span>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-label">Height Gain</div>
            <div class="info-value">
                <span class="<?= $validation['height_gain_cm'] >= 0 ? 'gain-positive' : 'gain-negative' ?>">
                    <?= $validation['height_gain_cm'] >= 0 ? '+' : '' ?><?= number_format($validation['height_gain_cm'], 2) ?> cm
                </span>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-label">Attendance Rate</div>
            <div class="info-value">
                <?= number_format($validation['attendance_rate'], 1) ?>%
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-label">Days in Program</div>
            <div class="info-value">
                <?= $validation['days_in_program'] ?> days
            </div>
        </div>
    </div>
    
    <?php if ($validation['recommendation']): ?>
    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid #f0f0f0">
        <div style="font-size:0.85rem">
            <strong style="color:var(--kn-dark)">
                <i class="bi bi-clipboard-check"></i> Recommendation:
            </strong>
            <div style="color:var(--kn-muted);margin-top:0.25rem">
                <?= nl2br(htmlspecialchars($validation['recommendation'])) ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($validation['remarks']): ?>
    <div style="margin-top:0.75rem">
        <div style="font-size:0.85rem">
            <strong style="color:var(--kn-dark)">
                <i class="bi bi-chat-left-text"></i> Remarks:
            </strong>
            <div style="color:var(--kn-muted);margin-top:0.25rem">
                <?= nl2br(htmlspecialchars($validation['remarks'])) ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>
