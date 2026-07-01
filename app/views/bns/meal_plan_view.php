<?php
$pageTitle = htmlspecialchars($mealPlan['plan_name']) . ' - Details';
$activeNav = 'meal_plans';
require_once __DIR__ . '/../templates/bns_layout.php';

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

// Group items by day
$mealsByDay = [];
if (!empty($items)) {
    foreach ($items as $item) {
        $mealsByDay[$item['day_number']][] = $item;
    }
}

// Calculate stats
$totalMeals = count($items);
$consumedMeals = count(array_filter($items, fn($i) => !empty($i['is_consumed'])));
$progressPercent = $totalMeals > 0 ? round(($consumedMeals / $totalMeals) * 100) : 0;
?>

<style>
.meal-plan-info {
    background: linear-gradient(135deg, rgba(42,157,143,.08) 0%, rgba(42,157,143,.02) 100%);
    border-radius: 1rem;
    padding: 1.5rem;
    border: 1.5px solid rgba(42,157,143,.15);
    margin-bottom: 1.5rem;
}

.day-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    margin-bottom: 1.5rem;
    border: 2px solid transparent;
}

.day-card-header {
    background: linear-gradient(135deg, rgba(42,157,143,.1) 0%, rgba(42,157,143,.05) 100%);
    padding: 1rem 1.5rem;
    border-bottom: 2px solid rgba(42,157,143,.1);
}

.meal-item {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(0,0,0,.05);
}

.meal-item:last-child {
    border-bottom: none;
}

.meal-type-badge {
    background: linear-gradient(135deg, #6B7D3C 0%, #5A6A31 100%);
    color: #fff;
    padding: .25rem .75rem;
    border-radius: .5rem;
    font-size: .8rem;
    font-weight: 600;
}

.consumed-badge {
    background: rgba(22,163,74,.1);
    color: #16a34a;
    padding: .25rem .75rem;
    border-radius: .5rem;
    font-size: .75rem;
    font-weight: 600;
}

.not-consumed-badge {
    background: rgba(107,114,128,.1);
    color: #6b7280;
    padding: .25rem .75rem;
    border-radius: .5rem;
    font-size: .75rem;
    font-weight: 600;
}
</style>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?action=bnsMealPlansList" style="color:#6B7D3C">Meal Plans</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($mealPlan['plan_name']) ?></li>
    </ol>
</nav>

<div class="container-fluid">
    <!-- Meal Plan Info -->
    <div class="meal-plan-info">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h4 class="mb-2 fw-bold" style="color:#6B7D3C"><?= htmlspecialchars($mealPlan['plan_name']) ?></h4>
                <div class="text-muted small mb-1">
                    <i class="bi bi-house-fill me-1"></i>
                    Household: HH-<?= htmlspecialchars($mealPlan['hh_number']) ?> • Purok <?= htmlspecialchars($mealPlan['purok'] ?? 'N/A') ?>
                </div>
                <?php if ($mealPlan['parent_first_name']): ?>
                <div class="text-muted small">
                    <i class="bi bi-person-fill me-1"></i>
                    Parent: <?= htmlspecialchars($mealPlan['parent_first_name'] . ' ' . $mealPlan['parent_last_name']) ?>
                </div>
                <?php endif; ?>
            </div>
            <span class="badge bg-success" style="background:#10b981!important">
                <?= htmlspecialchars($mealPlan['status']) ?>
            </span>
        </div>

        <?php if ($mealPlan['plan_description']): ?>
        <div class="mb-3">
            <strong class="d-block mb-1">Description:</strong>
            <p class="mb-0"><?= nl2br(htmlspecialchars($mealPlan['plan_description'])) ?></p>
        </div>
        <?php endif; ?>

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="text-center p-3" style="background:rgba(42,157,143,.05);border-radius:.75rem">
                    <h3 class="fw-bold mb-0" style="color:#6B7D3C"><?= $mealPlan['target_weeks'] ?></h3>
                    <small class="text-muted">Week<?= $mealPlan['target_weeks'] > 1 ? 's' : '' ?></small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-3" style="background:rgba(42,157,143,.05);border-radius:.75rem">
                    <h3 class="fw-bold mb-0" style="color:#6B7D3C"><?= $totalMeals ?></h3>
                    <small class="text-muted">Total Meals</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-3" style="background:rgba(22,163,74,.1);border-radius:.75rem">
                    <h3 class="fw-bold mb-0" style="color:#16a34a"><?= $consumedMeals ?></h3>
                    <small class="text-muted">Consumed</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-3" style="background:rgba(42,157,143,.05);border-radius:.75rem">
                    <h3 class="fw-bold mb-0" style="color:#6B7D3C"><?= $progressPercent ?>%</h3>
                    <small class="text-muted">Completion</small>
                </div>
            </div>
        </div>

        <?php if ($totalMeals > 0): ?>
        <div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="small">Consumption Progress:</strong>
                <small class="text-muted"><?= $consumedMeals ?> of <?= $totalMeals ?> meals consumed</small>
            </div>
            <div class="progress" style="height: 12px;">
                <div class="progress-bar <?= $progressPercent >= 70 ? 'bg-success' : ($progressPercent >= 40 ? 'bg-warning' : 'bg-danger') ?>" 
                     role="progressbar" 
                     style="width: <?= $progressPercent ?>%" 
                     aria-valuenow="<?= $progressPercent ?>" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    <?= $progressPercent ?>%
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($mealPlan['completed_by_mother'])): ?>
        <div class="alert alert-success mt-3 mb-0">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Mother marked this meal plan as completed!</strong>
            <br>
            <small>Completed on: <?= date('F j, Y \a\t g:i A', strtotime($mealPlan['completion_date'])) ?></small>
            <?php if (!empty($mealPlan['mother_feedback'])): ?>
            <br><br>
            <strong>Feedback:</strong> "<?= htmlspecialchars($mealPlan['mother_feedback']) ?>"
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Weekly Meal Plan -->
    <?php for ($day = 1; $day <= 7; $day++): ?>
    <div class="day-card">
        <div class="day-card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold" style="color:#6B7D3C">
                    <i class="bi bi-calendar3 me-2"></i>Day <?= $day ?> - <?= $days[$day - 1] ?>
                </h6>
                <?php if (!empty($mealsByDay[$day])): ?>
                <?php 
                $dayConsumed = count(array_filter($mealsByDay[$day], fn($m) => !empty($m['is_consumed'])));
                $dayTotal = count($mealsByDay[$day]);
                ?>
                <span class="badge <?= $dayConsumed == $dayTotal ? 'bg-success' : 'bg-secondary' ?>">
                    <?= $dayConsumed ?>/<?= $dayTotal ?> consumed
                </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="day-card-body">
            <?php if (empty($mealsByDay[$day])): ?>
            <div class="text-center py-4 text-muted">
                <i class="bi bi-inbox" style="font-size:2rem;opacity:.3"></i>
                <p class="mb-0 mt-2">No meals planned for this day</p>
            </div>
            <?php else: ?>
                <?php foreach ($mealsByDay[$day] as $meal): ?>
                <div class="meal-item">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="meal-type-badge"><?= htmlspecialchars($meal['meal_type']) ?></span>
                        <h6 class="mb-0 fw-bold flex-grow-1"><?= htmlspecialchars($meal['dish_name']) ?></h6>
                        
                        <?php if (!empty($meal['is_consumed'])): ?>
                        <span class="consumed-badge">
                            <i class="bi bi-check-circle-fill me-1"></i>Consumed
                        </span>
                        <?php else: ?>
                        <span class="not-consumed-badge">
                            <i class="bi bi-circle me-1"></i>Not Yet
                        </span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($meal['is_consumed']) && !empty($meal['consumed_date'])): ?>
                    <div class="alert alert-success small mb-2" style="padding:.75rem">
                        <div class="row align-items-center">
                            <div class="col-md-<?= !empty($meal['consumption_photo']) ? '8' : '12' ?>">
                                <i class="bi bi-calendar-check me-1"></i>
                                <strong>Consumed:</strong> <?= date('M j, Y \a\t g:i A', strtotime($meal['consumed_date'])) ?>
                                
                                <?php if (!empty($meal['consumption_notes'])): ?>
                                <br><i class="bi bi-chat-left-text me-1"></i>
                                <strong>Notes:</strong> "<?= htmlspecialchars($meal['consumption_notes']) ?>"
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($meal['consumption_photo'])): ?>
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <img src="<?= htmlspecialchars($meal['consumption_photo']) ?>" 
                                         alt="Consumption evidence" 
                                         class="img-fluid rounded" 
                                         style="max-height: 120px; cursor: pointer; border: 2px solid rgba(22,163,74,.4); box-shadow: 0 2px 8px rgba(0,0,0,.1)"
                                         onclick="viewPhotoFullscreen('<?= htmlspecialchars($meal['consumption_photo']) ?>', '<?= htmlspecialchars($meal['dish_name'], ENT_QUOTES) ?>')">
                                    <div class="position-absolute top-0 end-0 m-1">
                                        <span class="badge bg-success" style="font-size:.7rem">
                                            <i class="bi bi-camera-fill"></i> Photo Evidence
                                        </span>
                                    </div>
                                </div>
                                <small class="text-muted d-block text-center mt-1" style="font-size:.7rem">
                                    <i class="bi bi-zoom-in me-1"></i>Click to enlarge
                                </small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($meal['ingredients']): ?>
                    <div class="mb-2">
                        <strong class="small text-muted">
                            <i class="bi bi-list-ul me-1"></i>Ingredients:
                        </strong>
                        <div class="mt-1">
                            <?= htmlspecialchars($meal['ingredients']) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($meal['serving_size']): ?>
                    <div class="small text-muted">
                        <i class="bi bi-cup me-1"></i>
                        <strong>Serving:</strong> <?= htmlspecialchars($meal['serving_size']) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endfor; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Photo Viewer Modal -->
<div class="modal fade" id="photoViewerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:rgba(22,163,74,.05);border-bottom:1px solid rgba(22,163,74,.1)">
                <h5 class="modal-title fw-bold" style="color:#16a34a">
                    <i class="bi bi-camera-fill me-2"></i>
                    <span id="photo-modal-title">Photo Evidence</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img id="photo-modal-image" src="" alt="Full size photo" class="img-fluid rounded" 
                     style="max-height: 70vh; border: 3px solid rgba(22,163,74,.3); box-shadow: 0 4px 16px rgba(0,0,0,.2)">
            </div>
            <div class="modal-footer">
                <a id="photo-download-link" href="" download class="btn btn-success">
                    <i class="bi bi-download me-2"></i>Download Photo
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
let photoModal;

function viewPhotoFullscreen(photoPath, mealName) {
    document.getElementById('photo-modal-title').textContent = 'Photo Evidence: ' + mealName;
    document.getElementById('photo-modal-image').src = photoPath;
    document.getElementById('photo-download-link').href = photoPath;
    
    if (!photoModal) {
        photoModal = new bootstrap.Modal(document.getElementById('photoViewerModal'));
    }
    photoModal.show();
}
</script>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
