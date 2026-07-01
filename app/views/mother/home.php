<?php
$pageTitle = 'Home';
$activeNav = 'home';
require_once __DIR__ . '/../templates/mother_layout.php';
require_once __DIR__ . '/../../models/MealPlanModel.php';
require_once __DIR__ . '/../../models/GroceryListModel.php';
require_once __DIR__ . '/../../models/FeedingProgramModel.php';

$hour = (int)date('H');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
$firstName = htmlspecialchars(explode(' ', $userName)[0]);

// Get statistics
$db = getDBConnection();
$mealPlanModel = new MealPlanModel($db);
$groceryModel = new GroceryListModel($db);
$feedingModel = new FeedingProgramModel($db);

$mealPlansCount = 0;
$groceryListsCount = 0;

try {
    $mealPlansCount = count($mealPlanModel->getMealPlansByUser($_SESSION['user_id']));
} catch (PDOException $e) {
    // meal_plans table doesn't exist yet
}

try {
    $groceryListsCount = count($groceryModel->getGroceryListsByUser($_SESSION['user_id'], 'Active'));
} catch (PDOException $e) {
    // grocery_lists table doesn't exist yet
}

// Get family profile status (safely check if table exists)
$hasFamily = 'No';
$familyId = null;
try {
    $stmtFamily = $db->prepare("SELECT family_id FROM family_profiles WHERE source_user_id = ? LIMIT 1");
    $stmtFamily->execute([$_SESSION['user_id']]);
    $familyId = $stmtFamily->fetchColumn();
    $hasFamily = $familyId ? 'Yes' : 'No';
} catch (PDOException $e) {
    // Table doesn't exist yet, that's okay
}

// Get children enrolled in feeding programs
$childrenCount = 0;
if ($familyId) {
    try {
        $stmt = $db->prepare("
            SELECT COUNT(DISTINCT name_of_client) 
            FROM feeding_program_attendance 
            WHERE mother_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $childrenCount = (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        // Table doesn't exist yet, that's okay
    }
}
?>

<style>
.meal-hero {
    background: linear-gradient(135deg, rgba(196,114,42,.95) 0%, rgba(168,94,34,.95) 100%);
    border-radius: 1.5rem;
    padding: 2.5rem;
    color: #fff;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(196,114,42,.3);
}

.smart-action-card {
    background: rgba(255,255,255,.15);
    backdrop-filter: blur(10px);
    border: 1.5px solid rgba(255,255,255,.2);
    border-radius: 1rem;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all .3s;
    height: 100%;
}

.smart-action-card:hover {
    background: rgba(255,255,255,.25);
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,.2);
}

.smart-action-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: .9;
}

.feature-btn {
    background: rgba(245,237,214,.15);
    border: 1.5px solid rgba(245,237,214,.3);
    color: #fff;
    padding: .75rem 1.5rem;
    border-radius: .75rem;
    font-weight: 600;
    transition: all .2s;
}

.feature-btn:hover {
    background: rgba(245,237,214,.25);
    border-color: rgba(245,237,214,.5);
    color: #fff;
    transform: translateX(4px);
}

.meal-highlight-card {
    background: #fff;
    border-radius: 1rem;
    overflow: hidden;
    border: 1.5px solid rgba(196,114,42,.1);
    transition: all .3s;
}

.meal-highlight-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(196,114,42,.15);
}

.alert-item {
    background: #fff;
    border-left: 4px solid #C4722A;
    padding: 1rem 1.25rem;
    border-radius: .5rem;
    margin-bottom: .75rem;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
}

.alert-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.category-card {
    position: relative;
    border-radius: 1rem;
    overflow: hidden;
    height: 180px;
    cursor: pointer;
    transition: all .3s;
}

.category-card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 24px rgba(0,0,0,.2);
}

.category-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,.8), transparent);
    padding: 1.5rem 1rem;
    color: #fff;
}
</style>

<div class="meal-hero">
    <h3 class="fw-bold mb-2">Hi, <?= $firstName ?>!</h3>
    <p class="mb-4" style="opacity:.9;font-size:1rem">What would you like to cook today?</p>
    <p class="small mb-0" style="opacity:.7"><?= date('l, F j, Y g:i A') ?></p>
</div>

<!-- Smart Meal Suggestions -->
<div class="card mb-4" style="border:1.5px solid rgba(196,114,42,.1);border-radius:1.5rem;background:linear-gradient(135deg, #fef5e7 0%, #fff 100%)">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3" style="color:#C4722A">🧠 Smart Meal Suggestions</h5>
        <p class="text-muted mb-4">Tell us what you want to cook or scan ingredients for personalized suggestions.</p>
        
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="smart-action-card" onclick="startVoiceSearch()">
                    <div class="smart-action-icon">🎤</div>
                    <h6 class="fw-bold mb-2">Voice Search</h6>
                    <p class="small mb-0" style="opacity:.8">"What I can make with..."</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="smart-action-card" onclick="scanIngredients()">
                    <div class="smart-action-icon">📷</div>
                    <h6 class="fw-bold mb-2">Scan Ingredients</h6>
                    <p class="small mb-0" style="opacity:.8">Use your camera</p>
                </div>
            </div>
        </div>
        
        <div class="row g-2">
            <div class="col-md-4">
                <button class="feature-btn w-100" onclick="suggestMeal()">
                    <i class="bi bi-stars me-2"></i>Suggest Meal
                </button>
            </div>
            <div class="col-md-4">
                <button class="feature-btn w-100" onclick="location.href='index.php?action=mealPlansList'">
                    <i class="bi bi-calendar-week me-2"></i>Plan My Week
                </button>
            </div>
            <div class="col-md-4">
                <button class="feature-btn w-100" onclick="scanPlanner()">
                    <i class="bi bi-upc-scan me-2"></i>Scan Planner
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Today's Meal Highlight -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card meal-highlight-card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3" style="color:#C4722A">🍲 Today's Meal Highlight</h6>
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400" 
                             alt="Meal" class="img-fluid rounded" style="object-fit:cover;height:200px;width:100%">
                    </div>
                    <div class="col-md-8">
                        <h5 class="fw-bold mb-2">Ginisang Monggo with Kalabasa & Malunggay</h5>
                        <div class="d-flex gap-3 mb-3 flex-wrap" style="font-size:.9rem">
                            <span class="text-success"><i class="bi bi-fire me-1"></i>280 cal</span>
                            <span class="text-primary"><i class="bi bi-globe me-1"></i>Filipino</span>
                            <span class="text-warning"><i class="bi bi-clock me-1"></i>35 min</span>
                        </div>
                        <p class="text-muted mb-3">Hearty mung bean stew with squash, moringa leaves, and pork, seasoned with garlic and onions.</p>
                        <button class="btn btn-sm btn-primary" style="background:#C4722A;border:none" onclick="viewRecipe()">
                            <i class="bi bi-book me-1"></i>View Recipe
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="col-lg-4">
        <div class="row g-3">
            <div class="col-6 col-lg-12">
                <a href="index.php?action=mealPlansList" style="text-decoration:none">
                    <div class="card h-100" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1rem;transition:all .2s"
                         onmouseover="this.style.borderColor='rgba(196,114,42,.4)';this.style.transform='translateY(-2px)'"
                         onmouseout="this.style.borderColor='rgba(196,114,42,.15)';this.style.transform='translateY(0)'">
                        <div class="card-body text-center">
                            <div style="font-size:2rem;margin-bottom:.5rem">📋</div>
                            <div style="font-size:1.8rem;font-weight:800;color:#C4722A"><?= $mealPlansCount ?></div>
                            <div style="font-size:.85rem;color:#666;text-transform:uppercase;letter-spacing:.05em">Meal Plans</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-12">
                <a href="index.php?action=groceryMode" style="text-decoration:none">
                    <div class="card h-100" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1rem;transition:all .2s"
                         onmouseover="this.style.borderColor='rgba(196,114,42,.4)';this.style.transform='translateY(-2px)'"
                         onmouseout="this.style.borderColor='rgba(196,114,42,.15)';this.style.transform='translateY(0)'">
                        <div class="card-body text-center">
                            <div style="font-size:2rem;margin-bottom:.5rem">🛒</div>
                            <div style="font-size:1.8rem;font-weight:800;color:#C4722A"><?= $groceryListsCount ?></div>
                            <div style="font-size:.85rem;color:#666;text-transform:uppercase;letter-spacing:.05em">Grocery Lists</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Alerts -->
<div class="card mb-4" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1rem">
    <div class="card-header d-flex justify-content-between align-items-center" 
         style="background:rgba(196,114,42,.05);border-bottom:1.5px solid rgba(196,114,42,.1)">
        <h6 class="mb-0 fw-bold" style="color:#C4722A">
            <i class="bi bi-bell-fill me-2"></i>Alerts
        </h6>
        <a href="#" class="small" style="color:#C4722A">View All</a>
    </div>
    <div class="card-body p-3">
        <div class="alert-item">
            <div class="d-flex gap-3">
                <div class="alert-icon" style="background:rgba(255,193,7,.15);color:#ff9800">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1" style="font-size:.95rem">Expiring Soon</h6>
                    <p class="small text-muted mb-0">Spinach and avocados expire in 2 days</p>
                </div>
            </div>
        </div>
        
        <div class="alert-item">
            <div class="d-flex gap-3">
                <div class="alert-icon" style="background:rgba(220,53,69,.15);color:#dc3545">
                    <i class="bi bi-basket-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1" style="font-size:.95rem">Low Stock</h6>
                    <p class="small text-muted mb-0">You're running low on milk and eggs</p>
                </div>
            </div>
        </div>
        
        <div class="alert-item">
            <div class="d-flex gap-3">
                <div class="alert-icon" style="background:rgba(40,167,69,.15);color:#28a745">
                    <i class="bi bi-tree-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1" style="font-size:.95rem">Meal Prep Reminder</h6>
                    <p class="small text-muted mb-0">Prep chicken and rice for tomorrow's lunch</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function startVoiceSearch() {
    alert('🎤 Voice search feature coming soon!\n\nYou will be able to say:\n"What can I make with chicken and vegetables?"');
}

function scanIngredients() {
    alert('📷 Ingredient scanner coming soon!\n\nYou will be able to scan ingredients using your camera and get recipe suggestions.');
}

function suggestMeal() {
    alert('🧠 AI meal suggestion feature coming soon!\n\nGet personalized meal recommendations based on:\n• Your dietary preferences\n• Available ingredients\n• Family size\n• Nutritional goals');
}

function scanPlanner() {
    alert('📱 Scan planner coming soon!\n\nQuickly scan barcodes or QR codes to add items to your meal plan.');
}

function viewRecipe() {
    alert('📖 Recipe viewer coming soon!\n\nView detailed:\n• Ingredients list\n• Step-by-step instructions\n• Cooking tips\n• Nutritional information');
}
</script>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
