<?php
$pageTitle = 'My Meal Plans';
$activeNav = 'meal_plans';
require_once __DIR__ . '/../templates/mother_layout.php';
?>

<style>
.meal-plan-card {
    border: 1.5px solid rgba(196,114,42,.15);
    border-radius: 1rem;
    transition: all .2s ease;
    background: #fff;
}

.meal-plan-card:hover {
    border-color: rgba(196,114,42,.3);
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
    transform: translateY(-2px);
}

.meal-plan-header {
    background: linear-gradient(135deg, rgba(196,114,42,.08) 0%, rgba(196,114,42,.02) 100%);
    padding: 1.25rem;
    border-bottom: 1px solid rgba(196,114,42,.1);
}

.status-badge {
    font-size: .75rem;
    padding: .35rem .75rem;
    border-radius: .5rem;
    font-weight: 600;
}

.status-draft {
    background: rgba(107,114,128,.1);
    color: #4b5563;
}

.status-active {
    background: rgba(16,185,129,.1);
    color: #059669;
}

.created-by-badge {
    background: rgba(196,114,42,.1);
    color: #8C4A1A;
    padding: .25rem .5rem;
    border-radius: .35rem;
    font-size: .7rem;
    font-weight: 600;
}
</style>

<div class="container-fluid py-4">
    <div class="mb-4">
        <h4 class="mb-1 fw-bold">
            <i class="bi bi-journal-richtext me-2" style="color:#C4722A"></i>My Meal Plans
        </h4>
        <p class="text-muted mb-0">View meal plans created by your BNS</p>
    </div>

    <?php if (empty($mealPlans)): ?>
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body">
            <i class="bi bi-journal-x" style="font-size: 4rem; opacity: .2; color:#C4722A;"></i>
            <h5 class="mt-3 mb-2">No Meal Plans Yet</h5>
            <p class="text-muted mb-0">Your Nutrition Officer will create meal plans for your household.</p>
        </div>
    </div>
    <?php else: ?>
    
    <div class="row g-4">
        <?php foreach ($mealPlans as $plan): ?>
        <div class="col-md-6 col-lg-4">
            <div class="meal-plan-card">
                <div class="meal-plan-header">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($plan['plan_name']) ?></h6>
                        <span class="status-badge status-<?= strtolower($plan['status']) ?>">
                            <?= htmlspecialchars($plan['status']) ?>
                        </span>
                    </div>
                    <?php if ($plan['creator_first_name']): ?>
                    <div class="mb-2">
                        <span class="created-by-badge">
                            <i class="bi bi-person-badge me-1"></i>
                            By <?= htmlspecialchars($plan['creator_role']) ?>: <?= htmlspecialchars($plan['creator_first_name'] . ' ' . $plan['creator_last_name']) ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="p-3">
                    <?php if ($plan['plan_description']): ?>
                    <p class="text-muted small mb-3"><?= nl2br(htmlspecialchars($plan['plan_description'])) ?></p>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="small text-muted">
                            <i class="bi bi-calendar-week me-1"></i>
                            <?= $plan['target_weeks'] ?> week<?= $plan['target_weeks'] > 1 ? 's' : '' ?>
                        </div>
                        <div class="small text-muted">
                            <i class="bi bi-list-ul me-1"></i>
                            <?= $plan['item_count'] ?> meal<?= $plan['item_count'] != 1 ? 's' : '' ?>
                        </div>
                    </div>

                    <?php if ($plan['notes']): ?>
                    <div class="alert alert-info small mb-3" style="background: rgba(196,114,42,.05); border-color: rgba(196,114,42,.1);">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Note:</strong> <?= nl2br(htmlspecialchars($plan['notes'])) ?>
                    </div>
                    <?php endif; ?>

                    <div class="text-muted small mb-3">
                        <i class="bi bi-clock-history me-1"></i>
                        Created <?= date('M j, Y', strtotime($plan['created_date'])) ?>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="index.php?action=mealPlanView&id=<?= $plan['meal_plan_id'] ?>" 
                           class="btn btn-primary" style="background:#C4722A;border:none">
                            <i class="bi bi-eye me-1"></i>View Meal Plan
                        </a>
                        <?php if ($plan['item_count'] > 0): ?>
                        <form method="GET" action="index.php" style="margin: 0;" onsubmit="console.log('Form submitting...'); return true;">
                            <input type="hidden" name="action" value="shopFromMealPlan">
                            <input type="hidden" name="meal_plan_id" value="<?= $plan['meal_plan_id'] ?>">
                            <button type="submit" class="btn btn-success btn-sm w-100" onclick="console.log('Button clicked, meal_plan_id: <?= $plan['meal_plan_id'] ?>');">
                                <i class="bi bi-cart-plus me-1"></i>Shop Now
                            </button>
                        </form>
                        <?php endif; ?>
                        <?php if ($plan['status'] === 'Active' && empty($plan['completed_by_mother'])): ?>
                        <button class="btn btn-outline-success btn-sm" 
                                onclick="showCompletionModal(<?= $plan['meal_plan_id'] ?>, '<?= htmlspecialchars($plan['plan_name'], ENT_QUOTES) ?>')">
                            <i class="bi bi-check-circle me-1"></i>Mark as Completed
                        </button>
                        <?php elseif (!empty($plan['completed_by_mother'])): ?>
                        <div class="alert alert-success small mb-0" style="padding:.5rem">
                            <i class="bi bi-check-circle-fill me-1"></i>Completed on <?= date('M j, Y', strtotime($plan['completion_date'])) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>
</div>

<!-- ✨ Mark as Completed Modal -->
<div class="modal fade" id="completionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:rgba(22,163,74,.05);border-bottom:1px solid rgba(22,163,74,.1)">
                <h5 class="modal-title fw-bold" style="color:#16a34a">
                    <i class="bi bi-check-circle me-2"></i>Mark Meal Plan as Completed
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=markMealPlanCompleted">
                <input type="hidden" name="meal_plan_id" id="completion-meal-plan-id">
                <div class="modal-body">
                    <div class="alert alert-info" style="background:rgba(22,163,74,.05);border:1px solid rgba(22,163,74,.1);color:#16a34a">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Meal Plan:</strong> <span id="completion-meal-plan-name"></span>
                    </div>
                    
                    <p class="mb-3">Did you follow this meal plan for your family?</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Your Feedback (Optional)</label>
                        <textarea name="feedback" class="form-control" rows="4" 
                                  placeholder="e.g., My children loved the meals! Very nutritious and affordable."></textarea>
                        <small class="text-muted">Your feedback helps the BNS improve future meal plans.</small>
                    </div>
                    
                    <div class="alert alert-warning small">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Note:</strong> This will notify your BNS that you completed the meal plan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i>Mark as Completed
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showCompletionModal(mealPlanId, mealPlanName) {
    document.getElementById('completion-meal-plan-id').value = mealPlanId;
    document.getElementById('completion-meal-plan-name').textContent = mealPlanName;
    new bootstrap.Modal(document.getElementById('completionModal')).show();
}
</script>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
