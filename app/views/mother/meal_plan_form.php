<?php
$pageTitle = $mealPlan ? 'Edit Meal Plan' : 'New Meal Plan';
$activeNav = 'meal_plans';
require_once __DIR__ . '/../templates/mother_layout.php';

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$mealTypes = ['Breakfast', 'Lunch', 'Dinner', 'Snack'];

// Group items by day
$mealsByDay = [];
if (!empty($items)) {
    foreach ($items as $item) {
        $mealsByDay[$item['day_number']][] = $item;
    }
}
?>

<style>
/* Enhanced Weekly Meal Planner Styles */
.weekly-planner {
    background: linear-gradient(135deg, rgba(196,114,42,.05) 0%, rgba(196,114,42,.02) 100%);
    border-radius: 1.5rem;
    padding: 2rem;
}

.planner-header {
    background: linear-gradient(135deg, #C4722A 0%, #A85E22 100%);
    color: #fff;
    padding: 1.5rem 2rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 16px rgba(196,114,42,.2);
}

.planner-header h3 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: .75rem;
}

.day-card-wrapper {
    margin-bottom: 1.5rem;
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.day-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    overflow: hidden;
    transition: all .3s;
    border: 2px solid transparent;
}

.day-card:hover {
    border-color: rgba(196,114,42,.3);
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    transform: translateY(-2px);
}

.day-card-header {
    background: linear-gradient(135deg, #f5f5f5 0%, #e5e5e5 100%);
    padding: 1rem 1.5rem;
    border-bottom: 2px solid rgba(196,114,42,.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.day-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: .5rem;
}

.day-date {
    font-size: .85rem;
    color: #666;
    font-weight: 400;
}

.day-card-body {
    padding: 0;
}

.meal-slots {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
}

.meal-slot {
    padding: 1.25rem;
    border-right: 1px solid rgba(0,0,0,.05);
    border-bottom: 1px solid rgba(0,0,0,.05);
    min-height: 180px;
    transition: background .2s;
    position: relative;
}

.meal-slot:hover {
    background: rgba(196,114,42,.02);
}

.meal-slot:nth-child(4n) {
    border-right: none;
}

.meal-slot-header {
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #666;
    margin-bottom: .75rem;
    letter-spacing: .5px;
}

.meal-slot.breakfast .meal-slot-header { color: #f59e0b; }
.meal-slot.lunch .meal-slot-header { color: #10b981; }
.meal-slot.dinner .meal-slot-header { color: #3b82f6; }
.meal-slot.snack .meal-slot-header { color: #ec4899; }

.meal-content {
    display: flex;
    flex-direction: column;
    gap: .5rem;
}

.meal-item {
    background: #fff;
    border: 1.5px solid rgba(196,114,42,.15);
    border-radius: .75rem;
    padding: .75rem;
    transition: all .2s;
    position: relative;
    cursor: pointer;
}

.meal-item:hover {
    border-color: #C4722A;
    box-shadow: 0 2px 8px rgba(196,114,42,.15);
    transform: translateY(-1px);
}

.meal-item-image {
    width: 60px;
    height: 60px;
    border-radius: .5rem;
    object-fit: cover;
    float: left;
    margin-right: .75rem;
    border: 2px solid rgba(196,114,42,.1);
}

.meal-item-name {
    font-weight: 600;
    font-size: .9rem;
    color: #333;
    margin-bottom: .25rem;
    line-height: 1.3;
}

.meal-item-price {
    font-size: .85rem;
    color: #C4722A;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: .25rem;
}

.meal-item-actions {
    position: absolute;
    top: .5rem;
    right: .5rem;
    display: flex;
    gap: .25rem;
    opacity: 0;
    transition: opacity .2s;
}

.meal-item:hover .meal-item-actions {
    opacity: 1;
}

.meal-action-btn {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    cursor: pointer;
    transition: all .2s;
    padding: 0;
}

.meal-action-btn.edit {
    background: rgba(59,130,246,.1);
    color: #3b82f6;
}

.meal-action-btn.edit:hover {
    background: #3b82f6;
    color: #fff;
}

.meal-action-btn.delete {
    background: rgba(239,68,68,.1);
    color: #ef4444;
}

.meal-action-btn.delete:hover {
    background: #ef4444;
    color: #fff;
}

.add-meal-slot {
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(196,114,42,.05);
    border: 2px dashed rgba(196,114,42,.2);
    border-radius: .75rem;
    padding: 1rem;
    cursor: pointer;
    transition: all .2s;
    min-height: 100px;
}

.add-meal-slot:hover {
    background: rgba(196,114,42,.1);
    border-color: #C4722A;
    border-style: solid;
}

.add-meal-slot i {
    font-size: 1.5rem;
    color: #C4722A;
}

.add-meal-slot span {
    margin-left: .5rem;
    font-weight: 600;
    color: #C4722A;
    font-size: .9rem;
}

.daily-summary {
    background: linear-gradient(135deg, rgba(196,114,42,.05) 0%, rgba(168,94,34,.05) 100%);
    border-radius: 1rem;
    padding: 1.5rem;
    margin-top: 1.5rem;
    border: 1.5px solid rgba(196,114,42,.15);
}

.daily-summary h6 {
    font-weight: 700;
    color: #C4722A;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: .5rem 0;
    border-bottom: 1px solid rgba(0,0,0,.05);
}

.summary-item:last-child {
    border-bottom: none;
    font-weight: 700;
    font-size: 1.1rem;
    color: #C4722A;
    padding-top: .75rem;
}

.budget-indicator {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .5rem 1rem;
    background: rgba(196,114,42,.1);
    border-radius: .5rem;
    font-weight: 600;
    color: #C4722A;
}

.quick-notes {
    background: rgba(251,191,36,.1);
    border-left: 3px solid #f59e0b;
    padding: .75rem 1rem;
    border-radius: .5rem;
    font-size: .85rem;
    color: #92400e;
    margin-top: 1rem;
}

.view-toggle {
    background: #fff;
    border: 1.5px solid rgba(196,114,42,.2);
    border-radius: .75rem;
    padding: .25rem;
    display: inline-flex;
    gap: .25rem;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}

.view-toggle-btn {
    padding: .5rem 1rem;
    border: none;
    background: transparent;
    border-radius: .5rem;
    font-weight: 600;
    color: #666;
    cursor: pointer;
    transition: all .2s;
}

.view-toggle-btn.active {
    background: linear-gradient(135deg, #C4722A 0%, #A85E22 100%);
    color: #fff;
    box-shadow: 0 2px 6px rgba(196,114,42,.3);
}

.view-toggle-btn:hover:not(.active) {
    background: rgba(196,114,42,.1);
    color: #C4722A;
}

/* Meal Type Colors */
.meal-type-breakfast { border-left-color: #f59e0b; }
.meal-type-lunch { border-left-color: #10b981; }
.meal-type-dinner { border-left-color: #3b82f6; }
.meal-type-snack { border-left-color: #ec4899; }

/* Responsive */
@media (max-width: 992px) {
    .meal-slots {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .meal-slots {
        grid-template-columns: 1fr;
    }
    
    .planner-header {
        padding: 1rem 1.5rem;
    }
    
    .planner-header h3 {
        font-size: 1.25rem;
    }
}
</style>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="font-size:.9rem">
        <li class="breadcrumb-item"><a href="index.php?action=mealPlansList" style="color:#C4722A">Meal Plans</a></li>
        <li class="breadcrumb-item active"><?= $pageTitle ?></li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#C4722A">📋 <?= $pageTitle ?></h4>
        <p class="text-muted mb-0" style="font-size:.9rem">Plan your family's meals for the week</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <?php if ($mealPlan): ?>
        <div class="view-toggle">
            <button class="view-toggle-btn active" onclick="toggleView('calendar')">
                <i class="bi bi-calendar-week me-1"></i>Calendar
            </button>
            <button class="view-toggle-btn" onclick="toggleView('list')">
                <i class="bi bi-list-ul me-1"></i>List
            </button>
        </div>
        <?php endif; ?>
        <button type="button" class="btn btn-primary" style="background:#C4722A;border:none" onclick="openPlanDetailsModal()">
            <i class="bi bi-gear me-2"></i>Settings
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Main Planner -->
    <div class="col-lg-9">
        <?php if (!$mealPlan): ?>
        <!-- Create Meal Plan First -->
        <div class="card" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1.5rem">
            <div class="card-body p-5 text-center">
                <div style="font-size:3rem;margin-bottom:1rem;opacity:.3">📅</div>
                <h5 class="fw-bold mb-3" style="color:#C4722A">Create Your Meal Plan</h5>
                <p class="text-muted mb-4">Start by giving your meal plan a name and description</p>
                <button type="button" class="btn btn-primary" style="background:#C4722A;border:none" onclick="openPlanDetailsModal()">
                    <i class="bi bi-plus-circle me-2"></i>Create Meal Plan
                </button>
            </div>
        </div>
        <?php else: ?>

        <!-- Enhanced Calendar View -->
        <div id="calendarView">
            <div class="planner-header">
                <h3><i class="bi bi-calendar-week"></i> Weekly Meal Planner</h3>
            </div>
            
            <?php 
            // Get current week dates
            $today = new DateTime();
            $weekStart = clone $today;
            $weekStart->modify('monday this week');
            ?>
            
            <?php foreach ($days as $dayNum => $dayName): 
                $dayNumber = $dayNum + 1;
                $dayMeals = $mealsByDay[$dayNumber] ?? [];
                
                // Calculate date for this day
                $currentDate = clone $weekStart;
                $currentDate->modify("+$dayNum days");
                $dateStr = $currentDate->format('M d, Y');
                
                // Calculate total for the day
                $dayTotal = 0;
                foreach ($dayMeals as $meal) {
                    // You can add price calculation here if you have meal prices
                    $dayTotal += 50; // Placeholder
                }
            ?>
            <div class="day-card-wrapper">
                <div class="day-card">
                    <div class="day-card-header">
                        <div>
                            <div class="day-name">
                                <i class="bi bi-calendar-day"></i>
                                <?= $dayName ?>
                            </div>
                            <div class="day-date"><?= $dateStr ?></div>
                        </div>
                        <button class="btn btn-sm btn-primary" style="background:#C4722A;border:none" 
                                onclick="quickAddMeal(<?= $dayNumber ?>, '<?= $dayName ?>')">
                            <i class="bi bi-plus-circle me-1"></i>Add Meal
                        </button>
                    </div>
                    
                    <div class="day-card-body">
                        <div class="meal-slots">
                            <?php foreach (['Breakfast', 'Lunch', 'Dinner', 'Snack'] as $mealType): 
                                $mealTypeClass = strtolower($mealType);
                                $typeMeals = array_filter($dayMeals, function($m) use ($mealType) {
                                    return $m['meal_type'] === $mealType;
                                });
                            ?>
                            <div class="meal-slot <?= $mealTypeClass ?>">
                                <div class="meal-slot-header"><?= $mealType ?></div>
                                <div class="meal-content">
                                    <?php if (empty($typeMeals)): ?>
                                    <div class="add-meal-slot" onclick="quickAddMealType(<?= $dayNumber ?>, '<?= $mealType ?>')">
                                        <i class="bi bi-plus-circle"></i>
                                        <span>Add</span>
                                    </div>
                                    <?php else: ?>
                                    <?php foreach ($typeMeals as $meal): ?>
                                    <div class="meal-item meal-type-<?= $mealTypeClass ?>">
                                        <div class="meal-item-actions">
                                            <button class="meal-action-btn edit" onclick="editMeal(<?= $meal['item_id'] ?>)" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="meal-action-btn delete" onclick="deleteMeal(<?= $meal['item_id'] ?>)" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <div class="meal-item-name"><?= htmlspecialchars($meal['dish_name']) ?></div>
                                        <?php if ($meal['ingredients']): ?>
                                        <div style="font-size:.75rem;color:#666;margin-top:.25rem">
                                            <?= htmlspecialchars(substr($meal['ingredients'], 0, 40)) ?><?= strlen($meal['ingredients']) > 40 ? '...' : '' ?>
                                        </div>
                                        <?php endif; ?>
                                        <div class="meal-item-price">
                                            <i class="bi bi-tag-fill"></i> ₱50.00
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($dayMeals)): ?>
                    <div class="daily-summary">
                        <h6><i class="bi bi-calculator"></i> Daily Summary</h6>
                        <div class="summary-item">
                            <span>Total Budget:</span>
                            <span class="budget-indicator">
                                <i class="bi bi-cash-coin"></i> ₱<?= number_format($dayTotal, 2) ?>
                            </span>
                        </div>
                        <div class="quick-notes">
                            <i class="bi bi-sticky"></i> 
                            <?= count($dayMeals) ?> meals planned for this day
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- List View (Hidden by default) -->
        <div id="listView" style="display:none">
            <div class="card" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1rem">
                <div class="card-header d-flex justify-content-between align-items-center"
                     style="background:rgba(196,114,42,.05);border-bottom:1.5px solid rgba(196,114,42,.1);border-radius:1rem 1rem 0 0">
                    <h6 class="mb-0 fw-bold" style="color:#C4722A">
                        <i class="bi bi-egg-fried me-2"></i>All Meals
                    </h6>
                    <button type="button" class="btn btn-sm btn-primary" onclick="showAddMealModal()"
                            style="background:#C4722A;border:none">
                        <i class="bi bi-plus-circle me-1"></i>Add Meal
                    </button>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($items)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox" style="font-size:2.5rem;opacity:.3"></i>
                        <p class="mb-0 mt-2">No meals yet. Click "Add Meal" to start planning.</p>
                    </div>
                    <?php else: ?>
                    <div class="accordion" id="mealAccordion">
                        <?php foreach ($days as $dayNum => $dayName): 
                            $dayNumber = $dayNum + 1;
                            $dayMeals = $mealsByDay[$dayNumber] ?? [];
                        ?>
                        <div class="accordion-item" style="border:none;border-bottom:1px solid rgba(196,114,42,.1)">
                            <h2 class="accordion-header">
                                <button class="accordion-button <?= empty($dayMeals) ? 'collapsed' : '' ?>" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#day<?= $dayNumber ?>"
                                        style="background:rgba(196,114,42,.02);color:#C4722A;font-weight:600">
                                    <?= $dayName ?> 
                                    <span class="badge bg-secondary ms-2"><?= count($dayMeals) ?> meals</span>
                                </button>
                            </h2>
                            <div id="day<?= $dayNumber ?>" class="accordion-collapse collapse <?= !empty($dayMeals) ? 'show' : '' ?>"
                                 data-bs-parent="#mealAccordion">
                                <div class="accordion-body">
                                    <?php if (empty($dayMeals)): ?>
                                    <p class="text-muted small mb-0">No meals planned for this day.</p>
                                    <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($dayMeals as $meal): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="badge" style="background:#C4722A"><?= $meal['meal_type'] ?></span>
                                                        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($meal['dish_name']) ?></h6>
                                                    </div>
                                                    <?php if ($meal['ingredients']): ?>
                                                    <p class="small text-muted mb-1">
                                                        <strong>Ingredients:</strong> <?= htmlspecialchars($meal['ingredients']) ?>
                                                    </p>
                                                    <?php endif; ?>
                                                    <?php if ($meal['preparation_notes']): ?>
                                                    <p class="small text-muted mb-0">
                                                        <strong>Notes:</strong> <?= htmlspecialchars($meal['preparation_notes']) ?>
                                                    </p>
                                                    <?php endif; ?>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteMeal(<?= $meal['item_id'] ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar Suggestions -->
    <div class="col-lg-3">
        <div class="card" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1rem;position:sticky;top:20px">
            <div class="card-header" style="background:rgba(196,114,42,.05);border-bottom:1.5px solid rgba(196,114,42,.1);border-radius:1rem 1rem 0 0">
                <h6 class="mb-0 fw-bold" style="color:#C4722A">
                    <i class="bi bi-lightbulb me-2"></i>Smart Suggestions
                </h6>
            </div>
            <div class="card-body p-3">
                <p class="small text-muted mb-3">Suggested recipes based on your available ingredients in the pantry.</p>
                
                <?php if (empty($suggestions)): ?>
                <div class="text-center py-4 text-muted" style="background:rgba(0,0,0,.02);border-radius:.75rem">
                    <i class="bi bi-pantry mb-2 d-block" style="font-size:1.5rem;opacity:.3"></i>
                    <p class="small mb-0">No pantry items found or no matches.</p>
                </div>
                <?php else: ?>
                <div class="suggestion-list d-flex flex-column gap-3">
                    <?php foreach (array_slice($suggestions, 0, 5) as $recipe): ?>
                    <div class="suggestion-card p-2" style="border:1px solid rgba(196,114,42,.1);border-radius:.75rem">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="fw-bold mb-0" style="font-size:.85rem"><?= htmlspecialchars($recipe['recipe_name']) ?></h6>
                            <span class="badge bg-success" style="font-size:.65rem"><?= $recipe['match_percent'] ?>% Match</span>
                        </div>
                        <p class="text-muted mb-2" style="font-size:.7rem"><?= htmlspecialchars($recipe['description']) ?></p>
                        
                        <div class="mb-2">
                            <div class="small fw-bold" style="font-size:.65rem;color:#C4722A">Missing:</div>
                            <div class="d-flex flex-wrap gap-1">
                                <?php foreach ($recipe['missing_ingredients'] as $missing): ?>
                                <span class="badge bg-light text-dark border" style="font-size:.6rem"><?= htmlspecialchars($missing) ?></span>
                                <?php endforeach; ?>
                                <?php if (empty($recipe['missing_ingredients'])): ?>
                                <span class="badge bg-success-light text-success" style="font-size:.6rem">None! All items in pantry</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <button class="btn btn-sm btn-outline-primary w-100 py-1" 
                                style="font-size:.7rem;border-color:#C4722A;color:#C4722A"
                                onclick="useRecipeSuggestion('<?= addslashes($recipe['recipe_name']) ?>', '<?= addslashes(implode(', ', array_merge($recipe['missing_ingredients'], ['(Items in Pantry)']))) ?>')">
                            Use this Suggestion
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <hr class="my-3" style="opacity:.1">
                <a href="index.php?action=supermarket" class="btn btn-sm w-100 btn-light text-muted" style="font-size:.75rem">
                    <i class="bi bi-cart me-1"></i>Go to Market
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Add Meal Modal -->
<div class="modal fade" id="addMealModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:rgba(196,114,42,.05)">
                <h5 class="modal-title fw-bold" style="color:#C4722A">
                    <i class="bi bi-plus-circle me-2"></i>Add Meal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addMealForm">
                    <input type="hidden" name="meal_plan_id" value="<?= $mealPlan['meal_plan_id'] ?? '' ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Day <span class="text-danger">*</span></label>
                            <select name="day_number" id="mealDaySelect" class="form-select" required>
                                <?php foreach ($days as $num => $name): ?>
                                <option value="<?= $num + 1 ?>"><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Meal Type <span class="text-danger">*</span></label>
                            <select name="meal_type" class="form-select" required>
                                <?php foreach ($mealTypes as $type): ?>
                                <option value="<?= $type ?>"><?= $type ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Dish Name <span class="text-danger">*</span></label>
                            <input type="text" name="dish_name" class="form-control" required
                                   placeholder="e.g., Chicken Adobo, Vegetable Soup">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Ingredients</label>
                            <textarea name="ingredients" class="form-control" rows="2"
                                      placeholder="e.g., Chicken, Soy sauce, Vinegar, Garlic"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Serving Size</label>
                            <input type="text" name="serving_size" class="form-control"
                                   placeholder="e.g., 4-6 servings">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nutritional Info</label>
                            <input type="text" name="nutritional_info" class="form-control"
                                   placeholder="e.g., 350 calories">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Preparation Notes</label>
                            <textarea name="preparation_notes" class="form-control" rows="2"
                                      placeholder="Cooking instructions or notes..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitAddMeal()" style="background:#C4722A;border:none">
                    <i class="bi bi-plus-circle me-1"></i>Add Meal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Plan Details Modal -->
<div class="modal fade" id="planDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:rgba(196,114,42,.05)">
                <h5 class="modal-title fw-bold" style="color:#C4722A">
                    <i class="bi bi-gear me-2"></i>Meal Plan Settings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=saveMealPlan">
                <?php if ($mealPlan): ?>
                <input type="hidden" name="meal_plan_id" value="<?= $mealPlan['meal_plan_id'] ?>">
                <?php endif; ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Plan Name <span class="text-danger">*</span></label>
                        <input type="text" name="plan_name" class="form-control" required
                               placeholder="e.g., Family Meal Plan - Week 1"
                               value="<?= htmlspecialchars($mealPlan['plan_name'] ?? '') ?>">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Target Weeks</label>
                            <input type="number" name="target_weeks" class="form-control" min="1" value="<?= $mealPlan['target_weeks'] ?? 1 ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="Draft" <?= ($mealPlan['status'] ?? 'Draft') === 'Draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="Active" <?= ($mealPlan['status'] ?? '') === 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Archived" <?= ($mealPlan['status'] ?? '') === 'Archived' ? 'selected' : '' ?>>Archived</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="plan_description" class="form-control" rows="2"
                                  placeholder="Brief description..."><?= htmlspecialchars($mealPlan['plan_description'] ?? '') ?></textarea>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Additional notes..."><?= htmlspecialchars($mealPlan['notes'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#C4722A;border:none">
                        <i class="bi bi-save me-1"></i>Save Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let addMealModal;
let planDetailsModal;

// Initialize modals as soon as DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing modals...');
    try {
        const addMealModalEl = document.getElementById('addMealModal');
        const planModalEl = document.getElementById('planDetailsModal');
        
        if (addMealModalEl) {
            addMealModal = new bootstrap.Modal(addMealModalEl);
            console.log('Add Meal Modal initialized');
        } else {
            console.error('Add Meal Modal element not found');
        }
        
        if (planModalEl) {
            planDetailsModal = new bootstrap.Modal(planModalEl);
            console.log('Plan Details Modal initialized');
        } else {
            console.error('Plan Details Modal element not found');
        }
    } catch (error) {
        console.error('Error initializing modals:', error);
    }
});

function showAddMealModal() {
    <?php if (!$mealPlan): ?>
    alert('Please create a meal plan first before adding meals!');
    openPlanDetailsModal();
    return;
    <?php endif; ?>
    
    try {
        document.getElementById('addMealForm').reset();
        if (addMealModal) {
            addMealModal.show();
        } else {
            console.error('addMealModal not initialized');
            alert('Error: Modal not initialized. Please refresh the page.');
        }
    } catch (error) {
        console.error('Error showing add meal modal:', error);
        alert('Error opening modal: ' + error.message);
    }
}

function submitAddMeal() {
    const form = document.getElementById('addMealForm');
    const formData = new FormData(form);
    
    // Validate meal_plan_id exists
    const mealPlanId = formData.get('meal_plan_id');
    if (!mealPlanId || mealPlanId === '') {
        alert('Error: No meal plan selected. Please create a meal plan first.');
        return;
    }
    
    fetch('index.php?action=addMealItem', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (addMealModal) {
                addMealModal.hide();
            }
            location.reload();
        } else {
            alert('Error adding meal: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Error: ' + error);
    });
}

function deleteMeal(itemId) {
    if (!confirm('Delete this meal?')) return;
    
    const formData = new FormData();
    formData.append('item_id', itemId);
    
    fetch('index.php?action=deleteMealItem', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error deleting meal');
        }
    });
}

function markAsConsumed(itemId, dishName, mealType) {
    if (!confirm('Record consumption for ' + dishName + '?')) return;
    
    const formData = new FormData();
    formData.append('meal_plan_id', '<?= $mealPlan['meal_plan_id'] ?>');
    formData.append('meal_plan_item_id', itemId);
    formData.append('dish_name', dishName);
    formData.append('meal_type', mealType);
    
    fetch('index.php?action=recordConsumption', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Consumption recorded successfully! This will be visible to the Nutrition Officer for recovery validation.');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => alert('Error: ' + error));
}

function toggleView(view) {
    const calendarView = document.getElementById('calendarView');
    const listView = document.getElementById('listView');
    const buttons = document.querySelectorAll('.view-toggle-btn');
    
    if (view === 'calendar') {
        calendarView.style.display = 'block';
        listView.style.display = 'none';
        buttons[0].classList.add('active');
        buttons[1].classList.remove('active');
    } else {
        calendarView.style.display = 'none';
        listView.style.display = 'block';
        buttons[0].classList.remove('active');
        buttons[1].classList.add('active');
    }
}

function quickAddMeal(dayNumber, dayName) {
    <?php if (!$mealPlan): ?>
    alert('Please create a meal plan first!');
    openPlanDetailsModal();
    return;
    <?php endif; ?>
    
    try {
        document.getElementById('addMealForm').reset();
        document.getElementById('mealDaySelect').value = dayNumber;
        if (addMealModal) {
            addMealModal.show();
        } else {
            console.error('addMealModal not initialized');
            alert('Error: Please refresh the page and try again.');
        }
    } catch (error) {
        console.error('Error in quickAddMeal:', error);
        alert('Error: ' + error.message);
    }
}

function quickAddMealType(dayNumber, mealType) {
    <?php if (!$mealPlan): ?>
    alert('Please create a meal plan first!');
    openPlanDetailsModal();
    return;
    <?php endif; ?>
    
    try {
        const form = document.getElementById('addMealForm');
        form.reset();
        form.querySelector('[name="day_number"]').value = dayNumber;
        form.querySelector('[name="meal_type"]').value = mealType;
        
        if (addMealModal) {
            addMealModal.show();
        } else {
            console.error('addMealModal not initialized');
            alert('Error: Please refresh the page and try again.');
        }
    } catch (error) {
        console.error('Error in quickAddMealType:', error);
        alert('Error: ' + error.message);
    }
}

function editMeal(itemId) {
    // TODO: Implement edit functionality
    alert('Edit meal feature coming soon! Item ID: ' + itemId);
}

function openPlanDetailsModal() {
    try {
        if (planDetailsModal) {
            planDetailsModal.show();
        } else {
            console.error('planDetailsModal not initialized');
            // Fallback: try to initialize and show
            const modalEl = document.getElementById('planDetailsModal');
            if (modalEl) {
                planDetailsModal = new bootstrap.Modal(modalEl);
                planDetailsModal.show();
            } else {
                alert('Error: Modal element not found. Please refresh the page.');
            }
        }
    } catch (error) {
        console.error('Error opening plan details modal:', error);
        alert('Error opening modal: ' + error.message);
    }
}

function useRecipeSuggestion(recipeName, ingredients) {
    document.getElementById('addMealForm').reset();
    
    // Fill the form with suggestion data
    const form = document.getElementById('addMealForm');
    form.querySelector('[name="dish_name"]').value = recipeName;
    form.querySelector('[name="ingredients"]').value = ingredients;
    
    // Show the modal
    addMealModal.show();
}
</script>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
