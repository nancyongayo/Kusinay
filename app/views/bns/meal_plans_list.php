<?php
$pageTitle = 'Meal Plans Management';
$activeNav = 'meal_plans';
require_once __DIR__ . '/../templates/bns_layout.php';
?>

<style>
.meal-plan-card {
    border: 1.5px solid rgba(42,157,143,.15);
    border-radius: 1rem;
    transition: all .2s ease;
    background: #fff;
}

.meal-plan-card:hover {
    border-color: rgba(42,157,143,.3);
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
    transform: translateY(-2px);
}

.meal-plan-header {
    background: linear-gradient(135deg, rgba(42,157,143,.08) 0%, rgba(42,157,143,.02) 100%);
    padding: 1.25rem;
    border-bottom: 1px solid rgba(42,157,143,.1);
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

.status-archived {
    background: rgba(239,68,68,.1);
    color: #dc2626;
}
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-journal-richtext me-2"></i>Meal Plans Management
            </h4>
            <p class="text-muted mb-0">Create and manage meal plans for households</p>
        </div>
        <a href="index.php?action=bnsMealPlanForm" class="btn btn-primary" style="background:#6B7D3C;border:none">
            <i class="bi bi-plus-circle me-2"></i>Create Meal Plan
        </a>
    </div>

    <?php if (empty($mealPlans)): ?>
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body">
            <i class="bi bi-journal-x" style="font-size: 4rem; opacity: .2;"></i>
            <h5 class="mt-3 mb-2">No Meal Plans Yet</h5>
            <p class="text-muted mb-4">Start creating meal plans for households to guide their nutrition.</p>
            <a href="index.php?action=bnsMealPlanForm" class="btn btn-primary" style="background:#6B7D3C;border:none">
                <i class="bi bi-plus-circle me-2"></i>Create First Meal Plan
            </a>
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
                    <div class="text-muted small">
                        <i class="bi bi-house-fill me-1"></i>
                        HH-<?= htmlspecialchars($plan['hh_number']) ?> • Purok <?= htmlspecialchars($plan['purok'] ?? 'N/A') ?>
                    </div>
                    <?php if ($plan['parent_first_name']): ?>
                    <div class="text-muted small">
                        <i class="bi bi-person-fill me-1"></i>
                        <?= htmlspecialchars($plan['parent_first_name'] . ' ' . $plan['parent_last_name']) ?>
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

                    <?php 
                    // Get consumption stats
                    $consumedCount = 0;
                    $totalMeals = (int)$plan['item_count'];
                    if (isset($plan['consumed_meals'])) {
                        $consumedCount = (int)$plan['consumed_meals'];
                    }
                    $progressPercent = $totalMeals > 0 ? round(($consumedCount / $totalMeals) * 100) : 0;
                    ?>

                    <?php if ($totalMeals > 0): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted fw-bold">
                                <i class="bi bi-pie-chart me-1"></i>Consumption Progress:
                            </small>
                            <small class="fw-bold" style="color:#16a34a">
                                <?= $consumedCount ?>/<?= $totalMeals ?> (<?= $progressPercent ?>%)
                            </small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar <?= $progressPercent >= 70 ? 'bg-success' : ($progressPercent >= 40 ? 'bg-warning' : 'bg-danger') ?>" 
                                 role="progressbar" 
                                 style="width: <?= $progressPercent ?>%" 
                                 aria-valuenow="<?= $progressPercent ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($plan['completed_by_mother'])): ?>
                    <div class="alert alert-success small mb-3" style="padding:.5rem">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        <strong>Completed!</strong> <?= date('M j, Y', strtotime($plan['completion_date'])) ?>
                        <?php if (!empty($plan['mother_feedback'])): ?>
                        <br><small class="text-muted">"<?= htmlspecialchars(substr($plan['mother_feedback'], 0, 50)) ?><?= strlen($plan['mother_feedback']) > 50 ? '...' : '' ?>"</small>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="text-muted small mb-3">
                        <i class="bi bi-clock-history me-1"></i>
                        Created <?= date('M j, Y', strtotime($plan['created_date'])) ?>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="index.php?action=bnsMealPlanView&id=<?= $plan['meal_plan_id'] ?>" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>View Details & Consumption
                        </a>
                        <div class="d-flex gap-2">
                            <a href="index.php?action=bnsMealPlanForm&id=<?= $plan['meal_plan_id'] ?>" 
                               class="btn btn-sm btn-primary flex-fill" style="background:#6B7D3C;border:none">
                                <i class="bi bi-pencil-square me-1"></i>Edit
                            </a>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="confirmDelete(<?= $plan['meal_plan_id'] ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this meal plan? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="index.php?action=deleteMealPlan" id="deleteForm">
                    <input type="hidden" name="meal_plan_id" id="deleteMealPlanId">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmDelete(mealPlanId) {
    document.getElementById('deleteMealPlanId').value = mealPlanId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
