<?php include __DIR__ . '/../templates/nutrition_layout.php'; ?>

<style>
.form-section {
    background: #fff;
    border: 1.5px solid rgba(107,122,58,.15);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--kn-dark);
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--kn-green);
}
.comparison-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}
.comparison-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
}
.comparison-box.baseline {
    border-left: 4px solid #6c757d;
}
.comparison-box.followup {
    border-left: 4px solid #C4722A;
}
.comparison-box.gain {
    border-left: 4px solid #28a745;
}
.comparison-label {
    font-size: 0.75rem;
    color: var(--kn-muted);
    font-weight: 600;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
}
.comparison-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--kn-dark);
}
.comparison-unit {
    font-size: 0.85rem;
    color: var(--kn-muted);
}
.status-badge-large {
    display: inline-block;
    padding: 0.5rem 1.5rem;
    border-radius: 25px;
    font-size: 1rem;
    font-weight: 700;
    margin: 0.5rem 0;
}
.form-group {
    margin-bottom: 1.25rem;
}
.form-label {
    font-weight: 600;
    color: var(--kn-dark);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}
.form-control, .form-select {
    border: 1.5px solid rgba(107,122,58,.25);
    border-radius: 8px;
    padding: 0.65rem;
}
.form-control:focus, .form-select:focus {
    border-color: var(--kn-green);
    box-shadow: 0 0 0 0.2rem rgba(107,122,58,.15);
}
.btn-submit {
    background: var(--kn-green);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 2rem;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-submit:hover {
    background: #5a6e3a;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(107,122,58,.3);
}
.info-box {
    background: #e7f3ff;
    border-left: 4px solid #0066cc;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}
.gain-positive {
    color: #28a745;
}
.gain-negative {
    color: #dc3545;
}
</style>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="index.php?action=recoveryEligibleList&proposal_id=<?= $proposal['proposal_id'] ?>"
       style="display:inline-flex;align-items:center;gap:.4rem;background:#fff;color:var(--kn-dark);border:1.5px solid rgba(107,122,58,.25);border-radius:8px;padding:.35rem .85rem;font-size:.88rem;font-weight:600;text-decoration:none">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    <div>
        <h4 class="mb-0" style="color:var(--kn-dark);font-weight:700">
            Recovery Validation - <?= htmlspecialchars($childName) ?>
        </h4>
        <p class="mb-0" style="color:var(--kn-muted);font-size:0.85rem">
            <?= htmlspecialchars($proposal['proposal_title']) ?>
        </p>
    </div>
</div>

<div class="alert alert-light border mb-4" style="border-radius:10px;font-size:.88rem">
    <i class="bi bi-card-checklist me-2" style="color:var(--kn-green)"></i>
    <strong>Nutrition Officer II:</strong> Review BNS baseline &amp; follow-up data only.
    Set final status and <strong>recommendation</strong> (diet, continued monitoring, or referral).
</div>

<form method="POST" action="index.php?action=saveRecoveryValidation" id="validationForm">
    <?= \Security::csrfField() ?>
    <!-- Hidden Fields -->
    <input type="hidden" name="proposal_id" value="<?= $proposal['proposal_id'] ?>">
    <input type="hidden" name="full_name" value="<?= htmlspecialchars($childName) ?>">
    <input type="hidden" name="child_id" value="<?= $baseline['child_id'] ?? '' ?>">
    <input type="hidden" name="fm_member_id" value="<?= $baseline['fm_member_id'] ?? '' ?>">
    <input type="hidden" name="days_in_program" value="<?= $daysInProgram ?>">
    <input type="hidden" name="attendance_rate" value="<?= $attendanceRate ?>">
    
    <!-- Baseline Data -->
    <input type="hidden" name="baseline_assessment_id" value="<?= $baseline['assessment_id'] ?>">
    <input type="hidden" name="baseline_date" value="<?= $baseline['assessment_date'] ?>">
    <input type="hidden" name="baseline_weight_kg" value="<?= $baseline['weight_kg'] ?>">
    <input type="hidden" name="baseline_height_cm" value="<?= $baseline['height_cm'] ?>">
    <input type="hidden" name="baseline_muac_cm" value="<?= $baseline['muac_cm'] ?? '' ?>">
    <input type="hidden" name="baseline_bmi" value="<?= $baseline['bmi'] ?? '' ?>">
    <input type="hidden" name="baseline_wfa_status" value="<?= $baseline['wfa_status'] ?? '' ?>">
    <input type="hidden" name="baseline_hfa_status" value="<?= $baseline['hfa_status'] ?? '' ?>">
    <input type="hidden" name="baseline_wfh_status" value="<?= $baseline['wfh_status'] ?? '' ?>">
    <input type="hidden" name="baseline_bmi_status" value="<?= $baseline['bmi_status'] ?? '' ?>">
    
    <!-- Follow-up Data -->
    <input type="hidden" name="followup_assessment_id" value="<?= $followup['assessment_id'] ?>">
    <input type="hidden" name="followup_date" value="<?= $followup['assessment_date'] ?>">
    <input type="hidden" name="followup_weight_kg" value="<?= $followup['weight_kg'] ?>">
    <input type="hidden" name="followup_height_cm" value="<?= $followup['height_cm'] ?>">
    <input type="hidden" name="followup_muac_cm" value="<?= $followup['muac_cm'] ?? '' ?>">
    <input type="hidden" name="followup_bmi" value="<?= $followup['bmi'] ?? '' ?>">
    <input type="hidden" name="followup_wfa_status" value="<?= $followup['wfa_status'] ?? '' ?>">
    <input type="hidden" name="followup_hfa_status" value="<?= $followup['hfa_status'] ?? '' ?>">
    <input type="hidden" name="followup_wfh_status" value="<?= $followup['wfh_status'] ?? '' ?>">
    <input type="hidden" name="followup_bmi_status" value="<?= $followup['bmi_status'] ?? '' ?>">
    
    <!-- Calculated Gains -->
    <input type="hidden" name="weight_gain_kg" value="<?= $weightGain ?>">
    <input type="hidden" name="height_gain_cm" value="<?= $heightGain ?>">
    <input type="hidden" name="muac_gain_cm" value="<?= $muacGain !== null ? $muacGain : '' ?>">

    <!-- Program Information -->
    <div class="form-section">
        <div class="section-title">
            <i class="bi bi-info-circle"></i> Program Information
        </div>
        <div class="info-box">
            <div class="row">
                <div class="col-md-4">
                    <strong>Duration:</strong> <?= $daysInProgram ?> days
                </div>
                <div class="col-md-4">
                    <strong>Sessions Attended:</strong> <?= count($attendanceHistory) ?>
                </div>
                <div class="col-md-4">
                    <strong>Attendance Rate:</strong> <?= number_format($attendanceRate, 1) ?>%
                </div>
            </div>
        </div>
    </div>

    <!-- Weight Comparison -->
    <div class="form-section">
        <div class="section-title">
            <i class="bi bi-speedometer2"></i> Weight Comparison
        </div>
        <div class="comparison-grid">
            <div class="comparison-box baseline">
                <div class="comparison-label">Baseline</div>
                <div class="comparison-value"><?= number_format($baseline['weight_kg'], 2) ?></div>
                <div class="comparison-unit">kg</div>
                <div style="font-size:0.75rem;color:var(--kn-muted);margin-top:0.5rem">
                    <?= date('M d, Y', strtotime($baseline['assessment_date'])) ?>
                </div>
            </div>
            <div class="comparison-box followup">
                <div class="comparison-label">Follow-up</div>
                <div class="comparison-value"><?= number_format($followup['weight_kg'], 2) ?></div>
                <div class="comparison-unit">kg</div>
                <div style="font-size:0.75rem;color:var(--kn-muted);margin-top:0.5rem">
                    <?= date('M d, Y', strtotime($followup['assessment_date'])) ?>
                </div>
            </div>
            <div class="comparison-box gain">
                <div class="comparison-label">Gain/Loss</div>
                <div class="comparison-value <?= $weightGain >= 0 ? 'gain-positive' : 'gain-negative' ?>">
                    <?= $weightGain >= 0 ? '+' : '' ?><?= number_format($weightGain, 2) ?>
                </div>
                <div class="comparison-unit">kg</div>
                <div style="font-size:0.75rem;color:var(--kn-muted);margin-top:0.5rem">
                    <?= $weightGain >= 0 ? 'Gained' : 'Lost' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Height Comparison -->
    <div class="form-section">
        <div class="section-title">
            <i class="bi bi-rulers"></i> Height Comparison
        </div>
        <div class="comparison-grid">
            <div class="comparison-box baseline">
                <div class="comparison-label">Baseline</div>
                <div class="comparison-value"><?= number_format($baseline['height_cm'], 2) ?></div>
                <div class="comparison-unit">cm</div>
            </div>
            <div class="comparison-box followup">
                <div class="comparison-label">Follow-up</div>
                <div class="comparison-value"><?= number_format($followup['height_cm'], 2) ?></div>
                <div class="comparison-unit">cm</div>
            </div>
            <div class="comparison-box gain">
                <div class="comparison-label">Gain</div>
                <div class="comparison-value <?= $heightGain >= 0 ? 'gain-positive' : 'gain-negative' ?>">
                    <?= $heightGain >= 0 ? '+' : '' ?><?= number_format($heightGain, 2) ?>
                </div>
                <div class="comparison-unit">cm</div>
            </div>
        </div>
    </div>

    <?php
    $baselineMuac = isset($baseline['muac_cm']) && $baseline['muac_cm'] !== '' && $baseline['muac_cm'] !== null
        ? (float)$baseline['muac_cm'] : null;
    $followupMuac = isset($followup['muac_cm']) && $followup['muac_cm'] !== '' && $followup['muac_cm'] !== null
        ? (float)$followup['muac_cm'] : null;
    $muacGainDisplay = ($baselineMuac !== null && $followupMuac !== null) ? $followupMuac - $baselineMuac : null;
    ?>
    <div class="form-section">
        <div class="section-title">
            <i class="bi bi-circle"></i> MUAC Comparison (cm)
        </div>
        <p class="text-muted small mb-2"></p>
        <div class="comparison-grid">
            <div class="comparison-box baseline">
                <div class="comparison-label">Baseline</div>
                <div class="comparison-value"><?= $baselineMuac !== null ? number_format($baselineMuac, 1) : 'N/A' ?></div>
                <div class="comparison-unit">cm</div>
                <?php if ($baselineMuac === null): ?>
                <div style="font-size:0.7rem;color:#dc3545;margin-top:0.35rem">Not recorded — ask BNS to update assessment</div>
                <?php endif; ?>
            </div>
            <div class="comparison-box followup">
                <div class="comparison-label">Follow-up</div>
                <div class="comparison-value"><?= $followupMuac !== null ? number_format($followupMuac, 1) : 'N/A' ?></div>
                <div class="comparison-unit">cm</div>
                <?php if ($followupMuac === null): ?>
                <div style="font-size:0.7rem;color:#dc3545;margin-top:0.35rem">Not recorded — ask BNS to update assessment</div>
                <?php endif; ?>
            </div>
            <div class="comparison-box gain">
                <div class="comparison-label">Change</div>
                <?php if ($muacGainDisplay !== null): ?>
                <div class="comparison-value <?= $muacGainDisplay >= 0 ? 'gain-positive' : 'gain-negative' ?>">
                    <?= $muacGainDisplay >= 0 ? '+' : '' ?><?= number_format($muacGainDisplay, 1) ?>
                </div>
                <div class="comparison-unit">cm</div>
                <?php else: ?>
                <div class="comparison-value" style="color:var(--kn-muted)">N/A</div>
                <div class="comparison-unit">cm</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Nutritional Status Comparison -->
    <div class="form-section">
        <div class="section-title">
            <i class="bi bi-clipboard-data"></i> Nutritional Status Comparison
        </div>
        <div class="row">
            <div class="col-md-6">
                <h6 style="font-weight:700;color:var(--kn-dark);margin-bottom:1rem">Baseline Status</h6>
                <div class="mb-2">
                    <strong>Weight-for-Age:</strong> 
                    <span class="badge bg-secondary"><?= htmlspecialchars($baseline['wfa_status'] ?? 'N/A') ?></span>
                </div>
                <div class="mb-2">
                    <strong>Height-for-Age:</strong> 
                    <span class="badge bg-secondary"><?= htmlspecialchars($baseline['hfa_status'] ?? 'N/A') ?></span>
                </div>
                <div class="mb-2">
                    <strong>Weight-for-Height:</strong> 
                    <span class="badge bg-secondary"><?= htmlspecialchars($baseline['wfh_status'] ?? 'N/A') ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <h6 style="font-weight:700;color:var(--kn-dark);margin-bottom:1rem">Follow-up Status</h6>
                <div class="mb-2">
                    <strong>Weight-for-Age:</strong> 
                    <span class="badge bg-info"><?= htmlspecialchars($followup['wfa_status'] ?? 'N/A') ?></span>
                </div>
                <div class="mb-2">
                    <strong>Height-for-Age:</strong> 
                    <span class="badge bg-info"><?= htmlspecialchars($followup['hfa_status'] ?? 'N/A') ?></span>
                </div>
                <div class="mb-2">
                    <strong>Weight-for-Height:</strong> 
                    <span class="badge bg-info"><?= htmlspecialchars($followup['wfh_status'] ?? 'N/A') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recovery Status -->
    <div class="form-section">
        <div class="section-title">
            <i class="bi bi-heart-pulse"></i> Recovery Status Assessment
        </div>
        
        <div class="info-box mb-3" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border-left: 4px solid #4caf50;">
            <strong><i class="bi bi-cpu-fill"></i> Auto-Calculated Status (System Generated):</strong>
            <p class="mb-2 mt-2" style="font-size:0.9rem; color: #2e7d32;">
                This status was <strong>automatically calculated</strong> based on weight gain, height gain, and nutritional status improvements.
                <strong>This field cannot be edited.</strong>
            </p>
        </div>

        <div class="form-group">
            <label class="form-label" for="recovery_status">
                <i class="bi bi-check-circle-fill"></i> Final Recovery Status <span style="color:#dc3545">*</span>
            </label>
            
            <!-- Display as read-only field with prominent styling -->
            <div class="p-3 mb-2" style="background: #f8f9fa; border: 2px solid 
                <?php 
                    echo match($autoRecoveryStatus) {
                        'Recovered' => '#4caf50',
                        'Improving' => '#2196f3',
                        'No Progress' => '#ff9800',
                        'Deteriorating' => '#f44336',
                        default => '#9e9e9e'
                    };
                ?>; border-radius: 10px;">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="status-badge-large 
                            <?php 
                                echo match($autoRecoveryStatus) {
                                    'Recovered' => 'status-recovered',
                                    'Improving' => 'status-improving',
                                    'No Progress' => 'status-no-progress',
                                    'Deteriorating' => 'status-deteriorating',
                                    default => 'bg-secondary'
                                };
                            ?>" style="font-size: 1.2rem; padding: 0.5rem 1rem;">
                            <i class="bi bi-shield-check"></i> <?= htmlspecialchars($autoRecoveryStatus) ?>
                        </span>
                        <p class="mb-0 mt-2" style="font-size: 0.85rem; color: #666;">
                            <?php
                            echo match($autoRecoveryStatus) {
                                'Recovered' => 'Significant improvement, nutritional status normalized',
                                'Improving' => 'Showing positive progress, needs continued monitoring',
                                'No Progress' => 'No significant change in nutritional status',
                                'Deteriorating' => 'Nutritional status has worsened',
                                default => 'Status could not be determined'
                            };
                            ?>
                        </p>
                    </div>
                    <div>
                        <i class="bi bi-lock-fill" style="font-size: 2rem; color: #9e9e9e; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            
            <!-- Hidden field to submit the value (read-only/disabled fields don't submit) -->
            <input type="hidden" name="recovery_status" value="<?= htmlspecialchars($autoRecoveryStatus) ?>">
            
            <div class="form-text">
                <i class="bi bi-info-circle-fill"></i> 
                <strong>Automated Field:</strong> This value is automatically determined by the system and cannot be manually changed.
            </div>
        </div>

        <div class="alert alert-warning border-0 mb-3" id="referralGuide" style="border-radius:10px;font-size:.88rem;display:none">
            <strong><i class="bi bi-exclamation-triangle-fill"></i> No improvement / worsening — suggested actions:</strong>
            <ul class="mb-0 mt-2 ps-3">
                <li><strong>No Progress:</strong> Continue supplementary feeding; BNS home visit; re-assess in 2–4 weeks; nutrition counseling for caregiver.</li>
                <li><strong>Consider referral</strong> if still no weight gain or MUAC improvement after extended monitoring.</li>
                <li><strong>Deteriorating:</strong> <span class="text-danger fw-bold">Refer to hospital / ITC immediately.</span> Notify BNS and family. Document weight loss.</li>
            </ul>
        </div>

        <div class="form-group">
            <label class="form-label" for="recommendation">
                <i class="bi bi-clipboard-check"></i> Recommendation &amp; Action Plan <span style="color:#dc3545">*</span>
            </label>
            <textarea class="form-control" id="recommendation" name="recommendation" rows="5" required
                      placeholder="e.g. Continue RUTF and home diet; monthly BNS monitoring; OR refer to hospital/ITC if no improvement..."></textarea>
            <div class="form-text">Required: state diet/monitoring plan OR referral to hospital/ITC when not improving.</div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="text-center" style="margin-top:2rem">
        <button type="submit" class="btn-submit">
            <i class="bi bi-check-circle-fill"></i> Save Recovery Validation
        </button>
    </div>
</form>

<script>
(function () {
    // Since recovery_status is now a hidden field with auto-calculated value,
    // we just need to update the recommendation hints and referral guide
    const recoveryStatus = '<?= htmlspecialchars($autoRecoveryStatus) ?>';
    const recField = document.getElementById('recommendation');
    const hints = {
        'Recovered': 'Child has recovered. Continue healthy diet at home. Monthly monitoring by BNS. Discharge from supplementary feeding when appropriate.',
        'Improving': 'Positive progress noted. Continue feeding program diet, vegetables, and adequate fluids. Re-assess in 2–4 weeks.',
        'No Progress': 'No significant improvement. Intensify nutrition counseling and home visit. Consider referral for medical evaluation if no change next visit.',
        'Deteriorating': 'URGENT: Refer to hospital/ITC immediately. Notify BNS and caregiver. Document weight loss and failed recovery.'
    };

    const referralGuide = document.getElementById('referralGuide');

    // Apply hint based on auto-calculated status
    function applyHint() {
        if (!recField.value.trim() && hints[recoveryStatus]) {
            recField.placeholder = hints[recoveryStatus];
        }
        if (referralGuide) {
            referralGuide.style.display = (recoveryStatus === 'No Progress' || recoveryStatus === 'Deteriorating') ? 'block' : 'none';
        }
    }
    
    // Run on page load
    applyHint();

    document.getElementById('validationForm').addEventListener('submit', function (e) {
        const recommendation = recField.value.trim();
        if (recommendation.length < 10) {
            e.preventDefault();
            alert('Please provide a detailed recommendation (at least 10 characters).');
            return false;
        }
        return confirm('Save this recovery validation? The BNS assigned to this program will be notified.');
    });
})();
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
