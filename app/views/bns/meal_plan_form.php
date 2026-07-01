<?php
$pageTitle = $mealPlan ? 'Edit Meal Plan' : 'Create Meal Plan';
$activeNav = 'meal_plans';
require_once __DIR__ . '/../templates/bns_layout.php';

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
.meal-planner {
    background: linear-gradient(135deg, rgba(42,157,143,.05) 0%, rgba(42,157,143,.02) 100%);
    border-radius: 1.5rem;
    padding: 2rem;
}

.day-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    margin-bottom: 1.5rem;
    border: 2px solid transparent;
    transition: all .2s;
}

.day-card:hover {
    border-color: rgba(42,157,143,.2);
    transform: translateY(-2px);
}

.day-card-header {
    background: linear-gradient(135deg, #f5f5f5 0%, #e5e5e5 100%);
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
    background: linear-gradient(135deg, #2A9D8F 0%, #238276 100%);
    color: #fff;
    padding: .25rem .75rem;
    border-radius: .5rem;
    font-size: .8rem;
    font-weight: 600;
}

/* Pinggang Pinoy Colors */
.food-category-go {
    background: linear-gradient(135deg, #FDB022 0%, #F39C12 100%);
    color: #fff;
    padding: .25rem .75rem;
    border-radius: .5rem;
    font-size: .75rem;
    font-weight: 600;
    display: inline-block;
}

.food-category-grow {
    background: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%);
    color: #fff;
    padding: .25rem .75rem;
    border-radius: .5rem;
    font-size: .75rem;
    font-weight: 600;
    display: inline-block;
}

.food-category-glow {
    background: linear-gradient(135deg, #27AE60 0%, #229954 100%);
    color: #fff;
    padding: .25rem .75rem;
    border-radius: .5rem;
    font-size: .75rem;
    font-weight: 600;
    display: inline-block;
}

.balance-indicator {
    display: flex;
    gap: .5rem;
    align-items: center;
    font-size: .85rem;
}

.balance-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    font-weight: 700;
}

.balance-icon.balanced {
    background: #27AE60;
    color: #fff;
}

.balance-icon.needs-improvement {
    background: #F39C12;
    color: #fff;
}

.balance-icon.poor {
    background: #E74C3C;
    color: #fff;
}

.food-search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: .5rem;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
}

.food-search-item {
    padding: .75rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: background .2s;
}

.food-search-item:hover {
    background: rgba(42,157,143,.05);
}

.food-search-item:last-child {
    border-bottom: none;
}

/* BNS Breadcrumb Styling */
.breadcrumb-item a {
    color: #6B7D3C !important;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: #4A5A2C !important;
    text-decoration: underline;
}
</style>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?action=bnsMealPlansList" style="color:#6B7D3C;text-decoration:none">Meal Plans</a></li>
        <li class="breadcrumb-item active"><?= $pageTitle ?></li>
    </ol>
</nav>

<div class="container-fluid">
    <?php if (!$mealPlan): ?>
    <!-- Step 1: Create Meal Plan -->
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">
                <i class="bi bi-journal-plus me-2"></i>Create Meal Plan for Household
            </h5>
            
            <form method="POST" action="index.php?action=saveMealPlan" id="createMealPlanForm">
                <div class="row g-3">
                    <!-- Template Selector -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-lightning-charge-fill me-1"></i>Quick Start Template
                        </label>
                        <select id="templateSelector" class="form-select form-select-lg" style="border:2px solid #2A9D8F">
                            <option value="">-- Select Template --</option>
                            <option value="weight_gain">🟡 Weight Gain - Malnourished Child</option>
                            <option value="maintenance">🟢 Maintenance - Healthy Family</option>
                            <option value="therapeutic">🔴 Therapeutic - Severe Malnutrition</option>
                            <option value="budget">💰 Budget-Friendly - Large Family</option>
                            <option value="pregnant">🤰 High Nutrition - Pregnant/Lactating Mother</option>
                            <option value="custom">✏️ Custom (Manual Entry)</option>
                        </select>
                        <small class="text-muted">Choose a template to auto-fill plan details</small>
                    </div>

                    <div class="col-12"><hr></div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Select Household <span class="text-danger">*</span></label>
                        <select name="family_id" class="form-select" required>
                            <option value="">-- Choose Household --</option>
                            <?php foreach ($households as $hh): ?>
                            <option value="<?= $hh['family_id'] ?>">
                                HH-<?= htmlspecialchars($hh['hh_number']) ?>
                                <?php if (!empty($hh['parent_first_name']) || !empty($hh['parent_last_name'])): ?>
                                    - <?= htmlspecialchars(trim($hh['parent_first_name'] . ' ' . $hh['parent_last_name'])) ?>
                                <?php endif; ?>
                                (<?= htmlspecialchars($hh['purok'] ?? 'N/A') ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Plan Name <span class="text-danger">*</span></label>
                        <input type="text" name="plan_name" id="planName" class="form-control" required
                               placeholder="e.g., Weekly Meal Plan - Week 1">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Target Weeks</label>
                        <input type="number" name="target_weeks" id="targetWeeks" class="form-control" min="1" value="1">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="Draft">Draft</option>
                            <option value="Active">Active</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="plan_description" id="planDescription" class="form-control" rows="2"
                                  placeholder="Brief description of this meal plan..."></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Include in Notes (Check applicable):</label>
                        <div class="border rounded p-3" style="background:#f8f9fa">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="note1" value="Feed 3 main meals + 2 snacks daily">
                                        <label class="form-check-label small" for="note1">
                                            Feed 3 main meals + 2 snacks daily
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="note2" value="Monitor weight weekly">
                                        <label class="form-check-label small" for="note2">
                                            Monitor weight weekly
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="note3" value="Continue taking prescribed vitamins">
                                        <label class="form-check-label small" for="note3">
                                            Continue taking prescribed vitamins
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="note4" value="Avoid salty and processed foods">
                                        <label class="form-check-label small" for="note4">
                                            Avoid salty and processed foods
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="note5" value="Increase water intake (6-8 glasses daily)">
                                        <label class="form-check-label small" for="note5">
                                            Increase water intake (6-8 glasses daily)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="note6" value="Schedule follow-up in 4 weeks">
                                        <label class="form-check-label small" for="note6">
                                            Schedule follow-up in 4 weeks
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="note7" value="Call BNS if child refuses to eat">
                                        <label class="form-check-label small" for="note7">
                                            Call BNS if child refuses to eat
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="note8" value="Use local and seasonal ingredients">
                                        <label class="form-check-label small" for="note8">
                                            Use local and seasonal ingredients
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Additional Instructions (Optional)</label>
                        <textarea name="additional_notes" id="additionalNotes" class="form-control" rows="2"
                                  placeholder="Add any custom instructions not covered above..."></textarea>
                        <input type="hidden" name="notes" id="notesHidden">
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary" style="background:#6B7D3C;border:none">
                        <i class="bi bi-check-circle me-2"></i>Create Meal Plan
                    </button>
                    <a href="index.php?action=bnsMealPlansList" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Template definitions
    const templates = {
        weight_gain: {
            name: "Weight Gain Program - Week [WEEK]",
            weeks: 4,
            description: "High-calorie, high-protein meal plan designed for weight gain and recovery from malnutrition in children.",
            notes: ["note1", "note2", "note3", "note6", "note7"]
        },
        maintenance: {
            name: "Balanced Meal Plan - Week [WEEK]",
            weeks: 2,
            description: "Balanced meal plan following Pinggang Pinoy guidelines for healthy growth and development.",
            notes: ["note1", "note3", "note8"]
        },
        therapeutic: {
            name: "Therapeutic Feeding Program - Week [WEEK]",
            weeks: 8,
            description: "Intensive therapeutic feeding program for severe acute malnutrition. Requires strict monitoring.",
            notes: ["note1", "note2", "note3", "note6", "note7"]
        },
        budget: {
            name: "Budget-Friendly Meal Plan - Week [WEEK]",
            weeks: 2,
            description: "Nutritious and affordable meal plan using local and seasonal ingredients for large families.",
            notes: ["note1", "note8"]
        },
        pregnant: {
            name: "High Nutrition Plan - Pregnant/Lactating - Week [WEEK]",
            weeks: 4,
            description: "High-protein, iron-rich meal plan for pregnant or breastfeeding mothers.",
            notes: ["note1", "note3", "note5"]
        }
    };

    // Template selector change handler
    document.getElementById('templateSelector').addEventListener('change', function() {
        const template = templates[this.value];
        
        if (!template) {
            // Clear if custom or empty
            document.getElementById('planName').value = '';
            document.getElementById('targetWeeks').value = '1';
            document.getElementById('planDescription').value = '';
            // Uncheck all
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            return;
        }
        
        // Auto-fill form
        document.getElementById('planName').value = template.name.replace('[WEEK]', '1');
        document.getElementById('targetWeeks').value = template.weeks;
        document.getElementById('planDescription').value = template.description;
        
        // Uncheck all first
        document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        
        // Check template notes
        template.notes.forEach(noteId => {
            const checkbox = document.getElementById(noteId);
            if (checkbox) checkbox.checked = true;
        });
    });

    // Form submit handler - combine checked notes
    document.getElementById('createMealPlanForm').addEventListener('submit', function(e) {
        const checkedNotes = [];
        document.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
            checkedNotes.push('• ' + cb.value);
        });
        
        let finalNotes = '';
        if (checkedNotes.length > 0) {
            finalNotes = 'IMPORTANT INSTRUCTIONS:\n' + checkedNotes.join('\n');
        }
        
        const additional = document.getElementById('additionalNotes').value.trim();
        if (additional) {
            if (finalNotes) finalNotes += '\n\n';
            finalNotes += 'ADDITIONAL NOTES:\n' + additional;
        }
        
        document.getElementById('notesHidden').value = finalNotes;
    });
    </script>

    <?php else: ?>
    <!-- Step 2: Edit Meal Plan and Add Meals -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h4 class="mb-1 fw-bold"><?= htmlspecialchars($mealPlan['plan_name']) ?></h4>
            <div class="text-muted">
                <span class="badge bg-secondary"><?= htmlspecialchars($mealPlan['status']) ?></span>
                <?php if ($mealPlan['hh_number']): ?>
                • HH-<?= htmlspecialchars($mealPlan['hh_number']) ?>
                <?php endif; ?>
                <?php if ($mealPlan['parent_first_name']): ?>
                • <?= htmlspecialchars($mealPlan['parent_first_name'] . ' ' . $mealPlan['parent_last_name']) ?>
                <?php endif; ?>
            </div>
        </div>
        <button type="button" class="btn btn-outline-primary" style="border-color:#6B7D3C;color:#6B7D3C" onclick="openPlanSettingsModal()">
            <i class="bi bi-gear me-2"></i>Plan Settings
        </button>
    </div>

    <div class="meal-planner">
        <!-- Weekly Planner -->
        <?php for ($day = 1; $day <= 7; $day++): ?>
        <div class="day-card">
            <div class="day-card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-calendar3 me-2"></i>Day <?= $day ?> - <?= $days[$day - 1] ?>
                        </h6>
                        <?php if (!empty($dailyBalance[$day])): 
                            $dayBal = $dailyBalance[$day];
                            $isBalanced = $dayBal['is_balanced'];
                            $status = $isBalanced ? 'balanced' : ($dayBal['GO'] == 0 && $dayBal['GROW'] == 0 ? 'poor' : 'needs-improvement');
                        ?>
                        <div class="balance-indicator mt-1">
                            <div class="balance-icon <?= $status ?>">
                                <?= $isBalanced ? '✓' : '!' ?>
                            </div>
                            <small class="text-muted">
                                <span class="food-category-go" style="padding:.15rem .4rem;font-size:.7rem">GO: <?= $dayBal['GO'] ?></span>
                                <span class="food-category-grow" style="padding:.15rem .4rem;font-size:.7rem">GROW: <?= $dayBal['GROW'] ?></span>
                                <span class="food-category-glow" style="padding:.15rem .4rem;font-size:.7rem">GLOW: <?= $dayBal['GLOW'] ?></span>
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" style="background:#6B7D3C;border:none" onclick="showAddMealModal(<?= $day ?>)">
                        <i class="bi bi-plus-circle me-1"></i>Add Meal
                    </button>
                </div>
            </div>

            <div class="day-card-body">
                <?php if (empty($mealsByDay[$day])): ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox" style="font-size:2rem;opacity:.3"></i>
                    <p class="mb-0 mt-2">No meals planned. Click "Add Meal" to start.</p>
                </div>
                <?php else: ?>
                    <?php foreach ($mealsByDay[$day] as $meal): ?>
                    <div class="meal-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <span class="meal-type-badge"><?= htmlspecialchars($meal['meal_type']) ?></span>
                                    <?php if ($meal['food_category']): ?>
                                    <span class="food-category-<?= strtolower($meal['food_category']) ?>">
                                        <?= htmlspecialchars($meal['food_category']) ?>
                                    </span>
                                    <?php endif; ?>
                                    <strong><?= htmlspecialchars($meal['dish_name']) ?></strong>
                                </div>
                                <?php if ($meal['ingredients']): ?>
                                <div class="small text-muted mb-1">
                                    <i class="bi bi-list-ul me-1"></i>
                                    <strong>Ingredients:</strong> <?= htmlspecialchars($meal['ingredients']) ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($meal['serving_size']): ?>
                                <div class="small text-muted mb-1">
                                    <i class="bi bi-cup me-1"></i>
                                    <strong>Serving:</strong> <?= htmlspecialchars($meal['serving_size']) ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($meal['preparation_notes']): ?>
                                <div class="small text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <?= nl2br(htmlspecialchars($meal['preparation_notes'])) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                    onclick="deleteMealItem(<?= $meal['item_id'] ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endfor; ?>
    </div>

    <?php endif; ?>
</div>

<?php if ($mealPlan): ?>
<!-- Add Meal Modal -->
<div class="modal fade" id="addMealModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Meal to <span id="modalDayName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addMealForm">
                <input type="hidden" name="meal_plan_id" value="<?= $mealPlan['meal_plan_id'] ?>">
                <input type="hidden" name="day_number" id="modalDayNumber">
                
                <div class="modal-body">
                    <!-- ✨ NEW: Quick Recipe Selector -->
                    <div class="card mb-3" style="background:linear-gradient(135deg, rgba(42,157,143,.05), rgba(42,157,143,.01));border:2px solid #2A9D8F">
                        <div class="card-body p-3">
                            <h6 class="mb-2 fw-bold">
                                <i class="bi bi-lightning-charge-fill me-1" style="color:#2A9D8F"></i>
                                Quick Add from Recipe ⚡
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <select id="recipeSelector" class="form-select form-select-lg" style="border:2px solid #2A9D8F">
                                        <option value="">-- Select Pre-Made Recipe --</option>
                                        <optgroup label="🌟 Popular Recipes" id="popularRecipes"></optgroup>
                                        <optgroup label="🟡 GO - Energy Foods" id="goRecipes"></optgroup>
                                        <optgroup label="🔴 GROW - Protein Foods" id="growRecipes"></optgroup>
                                        <optgroup label="🟢 GLOW - Vegetables/Fruits" id="glowRecipes"></optgroup>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-success btn-lg w-100" onclick="useRecipe()" style="background:#C4722A;border:none">
                                        <i class="bi bi-check-circle me-1"></i>Use Recipe
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Select a recipe to auto-fill all fields instantly! You can edit after.
                            </small>
                        </div>
                    </div>

                    <!-- Pinggang Pinoy Guide -->
                    <div class="alert alert-info mb-3" style="background:linear-gradient(135deg, rgba(42,157,143,.08), rgba(42,157,143,.02));border:1px solid rgba(42,157,143,.2)">
                        <strong><i class="bi bi-info-circle me-1"></i>Pinggang Pinoy Guide:</strong><br>
                        <small>
                            <span class="food-category-go me-2">GO</span> Energy foods (Rice, Bread, Pasta)<br>
                            <span class="food-category-grow me-2">GROW</span> Body building (Fish, Meat, Eggs, Beans)<br>
                            <span class="food-category-glow me-2">GLOW</span> Regulating (Vegetables, Fruits)
                        </small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Meal Type <span class="text-danger">*</span></label>
                            <select name="meal_type" id="mealTypeSelect" class="form-select" required onchange="filterRecipesByMealType()">
                                <option value="">-- Select --</option>
                                <?php foreach ($mealTypes as $type): ?>
                                <option value="<?= $type ?>"><?= $type ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pinggang Pinoy Category <span class="text-danger">*</span></label>
                            <select name="food_category" id="foodCategory" class="form-select" required>
                                <option value="">-- Select Category --</option>
                                <option value="GO">🟡 GO - Energy (Carbs)</option>
                                <option value="GROW">🔴 GROW - Body Building (Protein)</option>
                                <option value="GLOW">🟢 GLOW - Regulating (Vitamins)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Dish Name <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" name="dish_name" id="dishNameInput" class="form-control" required
                                       placeholder="Search Filipino foods or type custom dish..."
                                       autocomplete="off">
                                <div id="foodSearchResults" class="food-search-results" style="display:none"></div>
                            </div>
                            <small class="text-muted">Type to search from Filipino foods/small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Ingredients</label>
                            <textarea name="ingredients" class="form-control" rows="2"
                                      placeholder="e.g., Chicken, Soy sauce, Vinegar, Garlic"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Serving Size</label>
                            <input type="text" name="serving_size" class="form-control"
                                   placeholder="e.g., 1 cup per person">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nutritional Info</label>
                            <input type="text" name="nutritional_info" class="form-control"
                                   placeholder="e.g., 300 kcal, 20g protein">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Preparation Notes</label>
                            <textarea name="preparation_notes" class="form-control" rows="2"
                                      placeholder="Cooking instructions or tips..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#6B7D3C;border:none">Add Meal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Plan Settings Modal -->
<div class="modal fade" id="planSettingsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Meal Plan Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=saveMealPlan">
                <input type="hidden" name="meal_plan_id" value="<?= $mealPlan['meal_plan_id'] ?>">
                <input type="hidden" name="family_id" value="<?= $mealPlan['family_id'] ?>">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Plan Name <span class="text-danger">*</span></label>
                        <input type="text" name="plan_name" class="form-control" required
                               value="<?= htmlspecialchars($mealPlan['plan_name']) ?>">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Target Weeks</label>
                            <input type="number" name="target_weeks" class="form-control" min="1" 
                                   value="<?= $mealPlan['target_weeks'] ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="Draft" <?= $mealPlan['status'] === 'Draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="Active" <?= $mealPlan['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Archived" <?= $mealPlan['status'] === 'Archived' ? 'selected' : '' ?>>Archived</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="plan_description" class="form-control" rows="2"><?= htmlspecialchars($mealPlan['plan_description'] ?? '') ?></textarea>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($mealPlan['notes'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#6B7D3C;border:none">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const days = <?= json_encode($days) ?>;
const filipinoFoods = <?= json_encode($filipinoFoods) ?>;
let addMealModal, planSettingsModal;

document.addEventListener('DOMContentLoaded', function() {
    addMealModal = new bootstrap.Modal(document.getElementById('addMealModal'));
    planSettingsModal = new bootstrap.Modal(document.getElementById('planSettingsModal'));
    
    // Setup food search autocomplete
    setupFoodSearch();
});

function setupFoodSearch() {
    const dishInput = document.getElementById('dishNameInput');
    const resultsDiv = document.getElementById('foodSearchResults');
    const categorySelect = document.getElementById('foodCategory');
    
    if (!dishInput) return;
    
    let searchTimeout;
    
    dishInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchFoods(query, resultsDiv, categorySelect);
        }, 300);
    });
    
    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!dishInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });
}

function searchFoods(query, resultsDiv, categorySelect) {
    const lowerQuery = query.toLowerCase();
    const filtered = filipinoFoods.filter(food => 
        food.food_name.toLowerCase().includes(lowerQuery)
    ).slice(0, 10);
    
    if (filtered.length === 0) {
        resultsDiv.innerHTML = '<div class="food-search-item text-muted">No matches found. Type custom dish name.</div>';
        resultsDiv.style.display = 'block';
        return;
    }
    
    let html = '';
    filtered.forEach(food => {
        const categoryClass = 'food-category-' + food.food_category.toLowerCase();
        html += `
            <div class="food-search-item" onclick="selectFood('${escapeHtml(food.food_name)}', '${food.food_category}', '${escapeHtml(food.common_serving || '')}')">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${escapeHtml(food.food_name)}</strong>
                        ${food.common_serving ? `<small class="text-muted d-block">${escapeHtml(food.common_serving)}</small>` : ''}
                    </div>
                    <span class="${categoryClass}" style="font-size:.7rem;padding:.2rem .5rem">
                        ${food.food_category}
                    </span>
                </div>
            </div>
        `;
    });
    
    resultsDiv.innerHTML = html;
    resultsDiv.style.display = 'block';
}

function selectFood(foodName, category, serving) {
    document.getElementById('dishNameInput').value = foodName;
    document.getElementById('foodCategory').value = category;
    if (serving) {
        document.querySelector('input[name="serving_size"]').value = serving;
    }
    document.getElementById('foodSearchResults').style.display = 'none';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function openPlanSettingsModal() {
    planSettingsModal.show();
}

document.getElementById('addMealForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('index.php?action=addMealItem', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert('Error: ' + (result.message || 'Failed to add meal'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
});

async function deleteMealItem(itemId) {
    // Show custom confirm modal
    const confirmed = await showConfirm('Delete this meal item?', 'Confirm Delete');
    if (!confirmed) return;
    
    const formData = new FormData();
    formData.append('item_id', itemId);
    
    try {
        const response = await fetch('index.php?action=deleteMealItem', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert('Error: Failed to delete meal');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

// ============================================================================
// ✨ RECIPE DATABASE FUNCTIONALITY
// ============================================================================

let allRecipes = [];
let selectedRecipe = null;

// Load recipes when modal opens
function showAddMealModal(dayNumber) {
    document.getElementById('modalDayNumber').value = dayNumber;
    document.getElementById('modalDayName').textContent = 'Day ' + dayNumber + ' - ' + days[dayNumber - 1];
    document.getElementById('addMealForm').reset();
    document.querySelector('#addMealForm input[name="meal_plan_id"]').value = <?= $mealPlan['meal_plan_id'] ?>;
    document.querySelector('#addMealForm input[name="day_number"]').value = dayNumber;
    document.getElementById('foodSearchResults').style.display = 'none';
    
    // Load recipes
    loadRecipes();
    
    addMealModal.show();
}

// Load recipes from API
async function loadRecipes() {
    try {
        // Use relative path from root
        const response = await fetch('api/get_recipes.php');
        
        console.log('Fetching recipes from:', 'api/get_recipes.php');
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            // Try to get error details from response
            const text = await response.text();
            console.error('Response text:', text);
            throw new Error('HTTP error! status: ' + response.status + ' - ' + text);
        }
        
        const data = await response.json();
        
        console.log('Received recipe data:', data);
        
        if (data.success) {
            allRecipes = data.recipes;
            console.log('Total recipes loaded:', allRecipes.length);
            populateRecipeSelector(allRecipes);
            
            // Success - no alert needed
            return;
        } else {
            console.error('API returned error:', data.error);
            throw new Error(data.error || 'Unknown API error');
        }
    } catch (error) {
        console.error('Failed to load recipes:', error);
        // Show custom error modal instead of alert
        showError('Could not load recipes. Error: ' + error.message + '\n\nPlease check:\n1. Run migration: migration_meal_recipes.sql\n2. Check if meal_recipes table exists\n3. Open: https://kusinayapp.freehosting.dev/api/get_recipes.php', 'Recipe Loading Error');
    }
}

// Populate recipe selector dropdown
function populateRecipeSelector(recipes) {
    if (!recipes || recipes.length === 0) {
        console.warn('No recipes to populate');
        return;
    }
    
    const popular = recipes.filter(r => r.is_popular == 1);
    const go = recipes.filter(r => r.food_category === 'GO');
    const grow = recipes.filter(r => r.food_category === 'GROW');
    const glow = recipes.filter(r => r.food_category === 'GLOW');
    
    console.log('Filtering recipes - Popular:', popular.length, 'GO:', go.length, 'GROW:', grow.length, 'GLOW:', glow.length);
    
    // Popular recipes
    const popularEl = document.getElementById('popularRecipes');
    if (popularEl && popular.length > 0) {
        popularEl.innerHTML = popular.map(r => 
            `<option value="${r.recipe_id}" data-recipe='${JSON.stringify(r).replace(/'/g, '&apos;')}'>
                ${escapeHtml(r.recipe_name)}
            </option>`
        ).join('');
    }
    
    // GO recipes
    const goEl = document.getElementById('goRecipes');
    if (goEl && go.length > 0) {
        goEl.innerHTML = go.map(r => 
            `<option value="${r.recipe_id}" data-recipe='${JSON.stringify(r).replace(/'/g, '&apos;')}'>
                ${escapeHtml(r.recipe_name)}
            </option>`
        ).join('');
    }
    
    // GROW recipes
    const growEl = document.getElementById('growRecipes');
    if (growEl && grow.length > 0) {
        growEl.innerHTML = grow.map(r => 
            `<option value="${r.recipe_id}" data-recipe='${JSON.stringify(r).replace(/'/g, '&apos;')}'>
                ${escapeHtml(r.recipe_name)}
            </option>`
        ).join('');
    }
    
    // GLOW recipes
    const glowEl = document.getElementById('glowRecipes');
    if (glowEl && glow.length > 0) {
        glowEl.innerHTML = glow.map(r => 
            `<option value="${r.recipe_id}" data-recipe='${JSON.stringify(r).replace(/'/g, '&apos;')}'>
                ${escapeHtml(r.recipe_name)}
            </option>`
        ).join('');
    }
    
    console.log('Recipe selector populated successfully');
}

// Use selected recipe to auto-fill form
function useRecipe() {
    const selector = document.getElementById('recipeSelector');
    const selectedOption = selector.options[selector.selectedIndex];
    
    if (!selectedOption.value) {
        showWarning('Please select a recipe first!', 'No Recipe Selected');
        return;
    }
    
    try {
        const recipe = JSON.parse(selectedOption.getAttribute('data-recipe'));
        
        // Auto-fill all fields
        document.getElementById('mealTypeSelect').value = recipe.meal_type === 'Any' ? '' : recipe.meal_type;
        document.getElementById('foodCategory').value = recipe.food_category;
        document.getElementById('dishNameInput').value = recipe.recipe_name;
        document.querySelector('textarea[name="ingredients"]').value = recipe.ingredients;
        document.querySelector('input[name="serving_size"]').value = recipe.serving_size;
        document.querySelector('input[name="nutritional_info"]').value = recipe.nutritional_info || '';
        document.querySelector('textarea[name="preparation_notes"]').value = recipe.preparation_notes || '';
        
        // Visual feedback
        selector.style.borderColor = '#27AE60';
        selector.style.background = 'rgba(39, 174, 96, 0.1)';
        
        // Show success message
        const successMsg = document.createElement('div');
        successMsg.className = 'alert alert-success mt-2';
        successMsg.innerHTML = '<i class="bi bi-check-circle me-2"></i>Recipe loaded! All fields filled. You can edit if needed.';
        successMsg.style.animation = 'fadeIn 0.3s';
        
        const cardBody = document.querySelector('#recipeSelector').closest('.card-body');
        const existingAlert = cardBody.querySelector('.alert-success');
        if (existingAlert) existingAlert.remove();
        cardBody.appendChild(successMsg);
        
        setTimeout(() => successMsg.remove(), 3000);
        
    } catch (error) {
        console.error('Failed to parse recipe:', error);
        showError('Error loading recipe. Please try again.', 'Recipe Error');
    }
}

// Filter recipes when meal type changes
function filterRecipesByMealType() {
    const mealType = document.getElementById('mealTypeSelect').value;
    
    if (!mealType || allRecipes.length === 0) {
        populateRecipeSelector(allRecipes);
        return;
    }
    
    // Filter recipes by meal type
    const filtered = allRecipes.filter(r => 
        r.meal_type === mealType || r.meal_type === 'Any'
    );
    
    populateRecipeSelector(filtered);
}

</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
<?php require_once __DIR__ . '/../templates/modals.php'; ?>
