<?php
$pageTitle = htmlspecialchars($mealPlan['plan_name']);
$activeNav = 'meal_plans';
require_once __DIR__ . '/../templates/mother_layout.php';

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

// Group items by day
$mealsByDay = [];
if (!empty($items)) {
    foreach ($items as $item) {
        $mealsByDay[$item['day_number']][] = $item;
    }
}
?>

<style>
.meal-plan-info {
    background: linear-gradient(135deg, rgba(196,114,42,.08) 0%, rgba(196,114,42,.02) 100%);
    border-radius: 1rem;
    padding: 1.5rem;
    border: 1.5px solid rgba(196,114,42,.15);
    margin-bottom: 1.5rem;
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
    border-color: rgba(196,114,42,.2);
}

.day-card-header {
    background: linear-gradient(135deg, rgba(196,114,42,.1) 0%, rgba(196,114,42,.05) 100%);
    padding: 1rem 1.5rem;
    border-bottom: 2px solid rgba(196,114,42,.1);
}

.meal-item {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(0,0,0,.05);
}

.meal-item:last-child {
    border-bottom: none;
}

.meal-type-badge {
    background: linear-gradient(135deg, #C4722A 0%, #A85E22 100%);
    color: #fff;
    padding: .25rem .75rem;
    border-radius: .5rem;
    font-size: .8rem;
    font-weight: 600;
}

.ingredient-pill {
    background: rgba(196,114,42,.08);
    color: #8C4A1A;
    padding: .25rem .75rem;
    border-radius: .5rem;
    font-size: .85rem;
    display: inline-block;
    margin: .25rem;
}

.ingredient-available {
    background: rgba(22,163,74,.1);
    color: #059669;
    border: 1px solid rgba(22,163,74,.3);
}

.ingredient-status .badge {
    font-size: 0.7rem;
}

/* Pantry Link Button */
.btn-outline-primary {
    border-color: #C4722A;
    color: #C4722A;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.btn-outline-primary:hover {
    background-color: #C4722A;
    border-color: #C4722A;
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
</style>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?action=mealPlansList" style="color:#C4722A">Meal Plans</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($mealPlan['plan_name']) ?></li>
    </ol>
</nav>

<div class="container-fluid">
    <!-- Meal Plan Info -->
    <div class="meal-plan-info">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h4 class="mb-2 fw-bold" style="color:#C4722A"><?= htmlspecialchars($mealPlan['plan_name']) ?></h4>
                <?php if ($mealPlan['creator_first_name']): ?>
                <div class="text-muted small mb-1">
                    <i class="bi bi-person-badge me-1"></i>
                    Created by <?= htmlspecialchars($mealPlan['creator_role']) ?>: 
                    <?= htmlspecialchars($mealPlan['creator_first_name'] . ' ' . $mealPlan['creator_last_name']) ?>
                </div>
                <?php endif; ?>
                <div class="text-muted small">
                    <i class="bi bi-clock-history me-1"></i>
                    <?= date('F j, Y', strtotime($mealPlan['created_date'])) ?>
                </div>
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

        <?php if ($mealPlan['notes']): ?>
        <div class="alert alert-info mb-3" style="background:rgba(196,114,42,.08);border-color:rgba(196,114,42,.2);color:#8C4A1A">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Important Note:</strong> <?= nl2br(htmlspecialchars($mealPlan['notes'])) ?>
        </div>
        <?php endif; ?>

        <div class="d-flex gap-4">
            <div>
                <i class="bi bi-calendar-week me-1" style="color:#C4722A"></i>
                <strong><?= $mealPlan['target_weeks'] ?></strong> week<?= $mealPlan['target_weeks'] > 1 ? 's' : '' ?>
            </div>
            <div>
                <i class="bi bi-list-ul me-1" style="color:#C4722A"></i>
                <strong><?= count($items) ?></strong> meal<?= count($items) != 1 ? 's' : '' ?> planned
            </div>
            <?php
            // Calculate consumption progress
            $totalMeals = count($items);
            $consumedMeals = count(array_filter($items, fn($i) => !empty($i['is_consumed'])));
            $progressPercent = $totalMeals > 0 ? round(($consumedMeals / $totalMeals) * 100) : 0;
            ?>
            <?php if ($totalMeals > 0): ?>
            <div>
                <i class="bi bi-pie-chart me-1" style="color:#16a34a"></i>
                <strong><?= $consumedMeals ?>/<?= $totalMeals ?></strong> consumed 
                <span class="badge bg-success ms-1"><?= $progressPercent ?>%</span>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($totalMeals > 0 && $progressPercent > 0): ?>
        <div class="mt-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="text-muted fw-bold">Progress:</small>
                <small class="text-muted"><?= $consumedMeals ?> of <?= $totalMeals ?> meals</small>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-success" role="progressbar" 
                     style="width: <?= $progressPercent ?>%" 
                     aria-valuenow="<?= $progressPercent ?>" 
                     aria-valuemin="0" 
                     aria-valuemax="100"></div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Weekly Meal Plan -->
    <?php for ($day = 1; $day <= 7; $day++): ?>
    <div class="day-card">
        <div class="day-card-header">
            <h6 class="mb-0 fw-bold" style="color:#C4722A">
                <i class="bi bi-calendar3 me-2"></i>Day <?= $day ?> - <?= $days[$day - 1] ?>
            </h6>
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
                        <?php if ($meal['food_category']): ?>
                        <span class="food-category-<?= strtolower($meal['food_category']) ?>">
                            <?= htmlspecialchars($meal['food_category']) ?>
                        </span>
                        <?php endif; ?>
                        <h6 class="mb-0 fw-bold flex-grow-1"><?= htmlspecialchars($meal['dish_name']) ?></h6>
                        
                        <?php if (!empty($meal['is_consumed'])): ?>
                        <span class="badge bg-success">
                            <i class="bi bi-check-circle-fill me-1"></i>Consumed
                        </span>
                        <?php else: ?>
                            <?php 
                            $availability = $ingredientAvailability[$meal['item_id']] ?? null;
                            if ($availability && $availability['is_ready']): ?>
                            <!-- Ready to Cook - Show cooking action -->
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-warning" 
                                        onclick="startCooking(<?= $meal['item_id'] ?>, '<?= htmlspecialchars($meal['dish_name'], ENT_QUOTES) ?>')">
                                    <i class="bi bi-fire me-1"></i>Start Cooking
                                </button>
                                <button class="btn btn-sm btn-success" 
                                        onclick="markMealConsumed(<?= $meal['item_id'] ?>, '<?= htmlspecialchars($meal['dish_name'], ENT_QUOTES) ?>')"
                                        id="consume-btn-<?= $meal['item_id'] ?>">
                                    <i class="bi bi-check-circle me-1"></i>Mark as Consumed
                                </button>
                            </div>
                            <?php elseif ($availability && $availability['completion_percent'] > 0): ?>
                            <!-- Partially ready - Show what's missing -->
                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                <i class="bi bi-hourglass-split me-1"></i>Missing Ingredients (<?= $availability['completion_percent'] ?>% Ready)
                            </button>
                            <?php else: ?>
                            <!-- Not ready - Show regular consume button -->
                            <button class="btn btn-sm btn-success" 
                                    onclick="markMealConsumed(<?= $meal['item_id'] ?>, '<?= htmlspecialchars($meal['dish_name'], ENT_QUOTES) ?>')"
                                    id="consume-btn-<?= $meal['item_id'] ?>">
                                <i class="bi bi-check-circle me-1"></i>Mark as Consumed
                            </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($meal['is_consumed']) && !empty($meal['consumed_date'])): ?>
                    <div class="alert alert-success small mb-2" style="padding:.5rem">
                        <i class="bi bi-calendar-check me-1"></i>
                        Consumed on <?= date('M j, Y \a\t g:i A', strtotime($meal['consumed_date'])) ?>
                        
                        <?php if (!empty($meal['consumption_photo'])): ?>
                        <div class="mt-2">
                            <img src="<?= htmlspecialchars($meal['consumption_photo']) ?>" 
                                 alt="Meal evidence" 
                                 class="img-fluid rounded" 
                                 style="max-height: 150px; cursor: pointer; border: 2px solid rgba(22,163,74,.3)"
                                 onclick="window.open('<?= htmlspecialchars($meal['consumption_photo']) ?>', '_blank')">
                            <div class="text-muted small mt-1">
                                <i class="bi bi-camera-fill me-1"></i>Click to view full size
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($meal['consumption_notes'])): ?>
                        <div class="mt-2">
                            <i class="bi bi-chat-left-text me-1"></i>
                            <em><?= htmlspecialchars($meal['consumption_notes']) ?></em>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($meal['ingredients']): ?>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="small text-muted">
                                <i class="bi bi-list-ul me-1"></i>Ingredients:
                            </strong>
                            <?php 
                            $availability = $ingredientAvailability[$meal['item_id']] ?? null;
                            if ($availability): ?>
                            <div class="ingredient-status">
                                <?php if ($availability['is_ready']): ?>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle-fill me-1"></i>Ready to Cook!
                                </span>
                                <?php elseif ($availability['completion_percent'] > 0): ?>
                                <span class="badge bg-warning">
                                    <i class="bi bi-hourglass-split me-1"></i><?= $availability['completion_percent'] ?>% Ready
                                </span>
                                <?php else: ?>
                                <span class="badge bg-secondary">
                                    <i class="bi bi-cart me-1"></i>Need Shopping
                                </span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-1">
                            <?php 
                            $ingredients = explode(',', $meal['ingredients']);
                            foreach ($ingredients as $ingredient): 
                                $ingredient = trim($ingredient);
                                if ($ingredient):
                                    // Check if this ingredient is available in pantry
                                    $isAvailable = false;
                                    if ($availability && !empty($availability['matched_ingredients'])) {
                                        foreach ($availability['matched_ingredients'] as $matched) {
                                            if (stripos($matched, $ingredient) !== false) {
                                                $isAvailable = true;
                                                break;
                                            }
                                        }
                                    }
                            ?>
                            <span class="ingredient-pill <?= $isAvailable ? 'ingredient-available' : '' ?>">
                                <?php if ($isAvailable): ?>
                                <i class="bi bi-check-circle-fill me-1 text-success"></i>
                                <?php endif; ?>
                                <?= htmlspecialchars($ingredient) ?>
                            </span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                        
                        <?php if ($availability && !empty($availability['missing_ingredients'])): ?>
                        <div class="mt-2 p-2 bg-light border-start border-warning border-3">
                            <small class="text-muted">
                                <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                                <strong>Still needed:</strong> <?= implode(', ', $availability['missing_ingredients']) ?>
                            </small>
                            <div class="mt-1">
                                <a href="index.php?action=pantry" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-plus-circle me-1"></i>Add to Pantry
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($meal['serving_size']): ?>
                    <div class="small text-muted mb-1">
                        <i class="bi bi-cup me-1"></i>
                        <strong>Serving Size:</strong> <?= htmlspecialchars($meal['serving_size']) ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($meal['nutritional_info']): ?>
                    <div class="small text-muted mb-1">
                        <i class="bi bi-heart-pulse me-1"></i>
                        <strong>Nutrition:</strong> <?= htmlspecialchars($meal['nutritional_info']) ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($meal['preparation_notes']): ?>
                    <div class="alert alert-light small mt-2 mb-0" style="background:rgba(196,114,42,.03);border-color:rgba(196,114,42,.1)">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Preparation:</strong> <?= nl2br(htmlspecialchars($meal['preparation_notes'])) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endfor; ?>
</div>

<!-- ✨ Mark Meal as Consumed Modal -->
<div class="modal fade" id="consumeMealModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:rgba(22,163,74,.05);border-bottom:1px solid rgba(22,163,74,.1)">
                <h5 class="modal-title fw-bold" style="color:#16a34a">
                    <i class="bi bi-check-circle me-2"></i>Mark Meal as Consumed
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="consume-item-id">
                <div class="alert alert-info" style="background:rgba(22,163,74,.05);border:1px solid rgba(22,163,74,.1);color:#16a34a">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Meal:</strong> <span id="consume-meal-name"></span>
                </div>
                
                <p class="mb-3">Did your family consume this meal?</p>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-camera-fill me-1"></i>Photo Evidence (Required)
                    </label>
                    
                    <!-- Hidden file input - camera only (fallback) -->
                    <input type="file" id="consume-photo-file" style="display:none;" 
                           accept="image/*" capture="environment">
                    
                    <!-- Camera Interface Container -->
                    <div id="camera-interface">
                        <!-- Camera Button -->
                        <div id="camera-button-container">
                            <button type="button" class="btn btn-primary w-100" onclick="startCamera()" style="padding:1rem;">
                                <i class="bi bi-camera-fill me-2" style="font-size:1.5rem;"></i>
                                <span style="font-size:1.1rem;font-weight:600;">Open Camera</span>
                            </button>
                            <small class="text-muted d-block mt-2 text-center">
                                <i class="bi bi-info-circle me-1"></i>Camera will open to take a live photo
                            </small>
                        </div>
                        
                        <!-- Live Camera View -->
                        <div id="camera-view-container" style="display:none;">
                            <div class="position-relative">
                                <video id="camera-stream" autoplay playsinline style="width:100%; max-height:400px; border-radius:12px; background:#000;"></video>
                                <div class="position-absolute bottom-0 start-0 end-0 p-3 text-center" style="background:linear-gradient(transparent, rgba(0,0,0,0.7));">
                                    <button type="button" class="btn btn-light btn-lg rounded-circle" onclick="capturePhoto()" style="width:70px; height:70px;">
                                        <i class="bi bi-camera-fill" style="font-size:2rem;"></i>
                                    </button>
                                    <br>
                                    <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="stopCamera()">
                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2 text-center">
                                <i class="bi bi-info-circle me-1"></i>Position the meal/child in frame and tap the camera button
                            </small>
                        </div>
                        
                        <!-- Photo Preview -->
                        <div id="photo-preview-container" style="display:none;">
                            <div class="text-center">
                                <img id="photo-preview" src="" alt="Preview" class="img-fluid rounded" 
                                     style="max-height: 300px; border: 3px solid rgba(22,163,74,.3); box-shadow: 0 4px 12px rgba(0,0,0,.1);">
                                <!-- Hidden canvas for photo capture -->
                                <canvas id="photo-canvas" style="display:none;"></canvas>
                            </div>
                            <button type="button" class="btn btn-danger w-100 mt-3" onclick="retakePhoto()">
                                <i class="bi bi-arrow-clockwise me-2"></i>Retake Photo
                            </button>
                            <div class="alert alert-success mt-2 mb-0" style="padding:.75rem;">
                                <i class="bi bi-check-circle-fill me-2"></i>Photo captured! You can now submit.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Notes (Optional)</label>
                    <input type="text" id="consume-notes" class="form-control" 
                           placeholder="e.g., Children loved it!" maxlength="200">
                    <small class="text-muted">How did your family like this meal?</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="submit-consumption-btn" onclick="submitMealConsumption()" disabled>
                    <i class="bi bi-check-circle me-2"></i>Mark as Consumed
                </button>
                <small class="text-muted w-100 text-center mt-2">
                    <i class="bi bi-exclamation-triangle me-1"></i>Please take a photo first
                </small>
            </div>
        </div>
    </div>
</div>

<script>
let consumeModal;
let cameraStream = null;
let capturedPhotoBlob = null;

// Start Cooking function with timer
function startCooking(itemId, mealName) {
    // Show cooking timer notification
    const alert = document.createElement('div');
    alert.className = 'alert alert-warning position-fixed';
    alert.id = `cooking-timer-${itemId}`;
    alert.style.cssText = `
        top: 20px; 
        right: 20px; 
        z-index: 9999; 
        min-width: 350px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    alert.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-fire text-warning me-2" style="font-size: 1.2rem;"></i>
                    <strong>Cooking: ${mealName}</strong>
                </div>
                <div class="d-flex align-items-center">
                    <i class="bi bi-clock me-2"></i>
                    <span id="timer-${itemId}" class="fw-bold" style="font-size: 1.1rem;">00:00</span>
                </div>
            </div>
            <button class="btn btn-success btn-sm ms-3" onclick="finishCooking(${itemId}, '${mealName.replace(/'/g, '\\\'')}')" id="finish-btn-${itemId}">
                <i class="bi bi-check-circle me-1"></i>Finish Cooking
            </button>
        </div>
    `;
    
    document.body.appendChild(alert);
    
    // Start timer
    startTimer(itemId);
    
    // Update Start Cooking button
    const startBtn = document.querySelector(`button[onclick*="startCooking(${itemId}"]`);
    if (startBtn) {
        startBtn.disabled = true;
        startBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Cooking...';
        startBtn.className = 'btn btn-sm btn-secondary';
    }
}

// Timer function
function startTimer(itemId) {
    let seconds = 0;
    window[`cookingInterval${itemId}`] = setInterval(() => {
        seconds++;
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        const timeString = `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
        
        const timerElement = document.getElementById(`timer-${itemId}`);
        if (timerElement) {
            timerElement.textContent = timeString;
        }
    }, 1000);
}

// Finish cooking function
function finishCooking(itemId, mealName) {
    // Stop timer
    if (window[`cookingInterval${itemId}`]) {
        clearInterval(window[`cookingInterval${itemId}`]);
        delete window[`cookingInterval${itemId}`];
    }
    
    // Get cooking time
    const timerElement = document.getElementById(`timer-${itemId}`);
    const cookingTime = timerElement ? timerElement.textContent : '00:00';
    
    // Remove cooking timer notification
    const cookingAlert = document.getElementById(`cooking-timer-${itemId}`);
    if (cookingAlert) {
        cookingAlert.remove();
    }
    
    // Show completion notification
    const alert = document.createElement('div');
    alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
    alert.style.cssText = `
        top: 20px; 
        right: 20px; 
        z-index: 9999; 
        min-width: 300px;
    `;
    alert.innerHTML = `
        <div>
            <i class="bi bi-check-circle-fill text-success me-2"></i>
            <strong>Finished cooking ${mealName}</strong>
            <br><small>Cooking time: ${cookingTime} - Ready to eat!</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    // Update button to "Cooking Complete"
    const startBtn = document.querySelector(`button[onclick*="startCooking(${itemId}"]`);
    if (startBtn) {
        startBtn.disabled = true;
        startBtn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Cooking Complete';
        startBtn.className = 'btn btn-sm btn-success';
    }
    
    // Auto-remove notification after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

function markMealConsumed(itemId, mealName) {
    document.getElementById('consume-item-id').value = itemId;
    document.getElementById('consume-meal-name').textContent = mealName;
    document.getElementById('consume-notes').value = '';
    
    // Reset camera state
    stopCamera();
    capturedPhotoBlob = null;
    document.getElementById('camera-button-container').style.display = 'block';
    document.getElementById('camera-view-container').style.display = 'none';
    document.getElementById('photo-preview-container').style.display = 'none';
    document.getElementById('submit-consumption-btn').disabled = true;
    
    if (!consumeModal) {
        consumeModal = new bootstrap.Modal(document.getElementById('consumeMealModal'));
    }
    consumeModal.show();
}

// Start camera using MediaDevices API
async function startCamera() {
    try {
        const constraints = {
            video: {
                facingMode: 'environment', // Back camera on mobile
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        };
        
        cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
        const videoElement = document.getElementById('camera-stream');
        videoElement.srcObject = cameraStream;
        
        // Show camera view
        document.getElementById('camera-button-container').style.display = 'none';
        document.getElementById('camera-view-container').style.display = 'block';
        
    } catch (error) {
        console.error('Camera error:', error);
        
        // Fallback to file input if camera API fails
        if (error.name === 'NotAllowedError') {
            alert('📸 Camera permission denied. Please allow camera access to take photos.');
        } else if (error.name === 'NotFoundError') {
            alert('📸 No camera found. Opening file picker as fallback...');
            // Fallback to file input with capture attribute
            document.getElementById('consume-photo-file').click();
            setupFileInputFallback();
        } else {
            alert('📸 Camera error: ' + error.message);
        }
    }
}

// Stop camera stream
function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }
    document.getElementById('camera-view-container').style.display = 'none';
    document.getElementById('camera-button-container').style.display = 'block';
}

// Capture photo from video stream
function capturePhoto() {
    const video = document.getElementById('camera-stream');
    const canvas = document.getElementById('photo-canvas');
    const preview = document.getElementById('photo-preview');
    
    // Set canvas size to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw current video frame to canvas
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Convert canvas to blob
    canvas.toBlob((blob) => {
        capturedPhotoBlob = blob;
        
        // Show preview
        const url = URL.createObjectURL(blob);
        preview.src = url;
        
        // Stop camera and show preview
        stopCamera();
        document.getElementById('camera-view-container').style.display = 'none';
        document.getElementById('photo-preview-container').style.display = 'block';
        document.getElementById('submit-consumption-btn').disabled = false;
        
    }, 'image/jpeg', 0.9);
}

// Setup file input fallback for devices without camera API support
function setupFileInputFallback() {
    const fileInput = document.getElementById('consume-photo-file');
    fileInput.onchange = function(e) {
        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            capturedPhotoBlob = file;
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photo-preview').src = e.target.result;
                document.getElementById('photo-preview-container').style.display = 'block';
                document.getElementById('camera-button-container').style.display = 'none';
                document.getElementById('submit-consumption-btn').disabled = false;
            };
            reader.readAsDataURL(file);
        }
    };
}

// Retake photo
function retakePhoto() {
    capturedPhotoBlob = null;
    document.getElementById('photo-preview-container').style.display = 'none';
    document.getElementById('camera-button-container').style.display = 'block';
    document.getElementById('submit-consumption-btn').disabled = true;
}

// Submit consumption with photo
function submitMealConsumption() {
    const itemId = document.getElementById('consume-item-id').value;
    const notes = document.getElementById('consume-notes').value || 'Consumed by family';
    const button = document.querySelector('#consume-btn-' + itemId);
    const submitBtn = document.getElementById('submit-consumption-btn');
    
    // Validate photo is required
    if (!capturedPhotoBlob) {
        alert('❌ Please take a photo first!');
        return;
    }
    
    if (button) {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Uploading...';
    }
    
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading Photo...';
    }
    
    // Create FormData with captured photo
    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('notes', notes);
    formData.append('consumption_photo', capturedPhotoBlob, 'meal_photo_' + Date.now() + '.jpg');
    
    fetch('index.php?action=markMealConsumed', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            consumeModal.hide();
            stopCamera(); // Ensure camera is stopped
            
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
            alert.style.zIndex = '9999';
            alert.innerHTML = `
                <i class="bi bi-check-circle-fill me-2"></i>${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alert);
            
            // Reload page after short delay
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            alert('❌ ' + (data.message || 'Failed to mark meal as consumed'));
            if (button) {
                button.disabled = false;
                button.innerHTML = '<i class="bi bi-check-circle me-1"></i>Mark as Consumed';
            }
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Mark as Consumed';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
        if (button) {
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-check-circle me-1"></i>Mark as Consumed';
        }
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Mark as Consumed';
        }
    });
}

// Clean up camera when modal is closed
document.getElementById('consumeMealModal')?.addEventListener('hidden.bs.modal', function() {
    stopCamera();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
