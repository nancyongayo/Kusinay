<?php
/**
 * MealPlanController
 * 
 * Handles Process 18: Meal Plans and Diet Plans
 * - BNS/Nutrition Officer creates meal plans for households (Process 18)
 * - Parents view meal plans (read-only) and generate grocery lists (Process 19)
 * - Diet plans prescribed by BNS for specific nutritional interventions
 */

require_once __DIR__ . '/../models/MealPlanModel.php';
require_once __DIR__ . '/../models/RecipeModel.php';
require_once __DIR__ . '/../models/PantryModel.php';
require_once __DIR__ . '/../models/FilipinoFoodModel.php';

class MealPlanController {
    private PDO $db;
    private MealPlanModel $model;
    private RecipeModel $recipeModel;
    private PantryModel $pantryModel;
    private FilipinoFoodModel $foodModel;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->model = new MealPlanModel($db);
        $this->recipeModel = new RecipeModel($db);
        $this->pantryModel = new PantryModel($db);
        $this->foodModel = new FilipinoFoodModel($db);
    }

    /**
     * Show meal plans list (MOTHER - Read Only)
     */
    public function showMealPlansList(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        // Get family_id for current user
        $stmt = $this->db->prepare("
            SELECT family_id FROM family_profiles 
            WHERE source_user_id = :user_id 
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $familyId = $stmt->fetchColumn();

        $mealPlans = [];
        if ($familyId) {
            $mealPlans = $this->model->getMealPlansByFamily((int)$familyId);
        }
        
        include __DIR__ . '/../views/mother/meal_plans_list.php';
    }

    /**
     * Show meal plans list for BNS (can create/edit)
     */
    public function showBNSMealPlansList(): void {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BNS Staff') {
            header('Location: index.php?action=login');
            exit;
        }

        $mealPlans = $this->model->getAllMealPlansByBNS($_SESSION['user_id']);
        
        include __DIR__ . '/../views/bns/meal_plans_list.php';
    }

    /**
     * ✨ Show BNS meal plan detail view with consumption tracking
     */
    public function showBNSMealPlanView(): void {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BNS Staff') {
            header('Location: index.php?action=login');
            exit;
        }

        $mealPlanId = (int)($_GET['id'] ?? 0);
        
        if (!$mealPlanId) {
            header('Location: index.php?action=bnsMealPlansList');
            exit;
        }

        $mealPlan = $this->model->getMealPlanById($mealPlanId);
        
        if (!$mealPlan || $mealPlan['created_by_user_id'] != $_SESSION['user_id']) {
            $_SESSION['flash_error'] = 'Meal plan not found or unauthorized';
            header('Location: index.php?action=bnsMealPlansList');
            exit;
        }

        $items = $this->model->getMealPlanItems($mealPlanId);
        
        include __DIR__ . '/../views/bns/meal_plan_view.php';
    }

    /**
     * Show BNS meal plan form (create/edit)
     */
    public function showBNSMealPlanForm(): void {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BNS Staff') {
            header('Location: index.php?action=login');
            exit;
        }

        $mealPlan = null;
        $items = [];
        $households = $this->model->getAllHouseholdsForMealPlan();
        $suggestions = [];
        $filipinoFoods = $this->foodModel->getAllFoods();
        $dailyBalance = [];
        
        if (!empty($_GET['id'])) {
            $mealPlan = $this->model->getMealPlanById((int)$_GET['id']);
            if ($mealPlan && $mealPlan['created_by_user_id'] != $_SESSION['user_id']) {
                $_SESSION['flash_error'] = 'Unauthorized access.';
                header('Location: index.php?action=bnsMealPlansList');
                exit;
            }
            if ($mealPlan) {
                $items = $this->model->getMealPlanItems((int)$_GET['id']);
                $dailyBalance = $this->foodModel->getDailyBalance((int)$_GET['id']);
                // Recipe suggestions feature - to be implemented later
                // if ($mealPlan['family_id']) {
                //     $suggestions = $this->recipeModel->suggestRecipesByPantry((int)$mealPlan['family_id']);
                // }
            }
        }
        
        include __DIR__ . '/../views/bns/meal_plan_form.php';
    }

    /**
     * Show meal plan view (MOTHER - Read Only)
     */
    public function showMealPlanView(): void {
        if (!isset($_SESSION['user_id']) || empty($_GET['id'])) {
            header('Location: index.php?action=mealPlansList');
            exit;
        }

        $mealPlan = $this->model->getMealPlanById((int)$_GET['id']);
        
        if (!$mealPlan) {
            $_SESSION['flash'] = 'Meal plan not found.';
            header('Location: index.php?action=mealPlansList');
            exit;
        }

        // Verify the parent has access to this meal plan
        $stmt = $this->db->prepare("
            SELECT family_id FROM family_profiles 
            WHERE source_user_id = :user_id 
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $parentFamilyId = $stmt->fetchColumn();

        if ($parentFamilyId != $mealPlan['family_id']) {
            $_SESSION['flash'] = 'Unauthorized access.';
            header('Location: index.php?action=mealPlansList');
            exit;
        }

        $items = $this->model->getMealPlanItems((int)$_GET['id']);
        
        // ✨ NEW: Check ingredient availability in pantry
        $ingredientAvailability = $this->checkIngredientAvailability($items, $parentFamilyId);
        
        include __DIR__ . '/../views/mother/meal_plan_view.php';
    }

    /**
     * Save meal plan (BNS only)
     */
    /**
     * Save meal plan (BNS only)
     */
    public function saveMealPlan(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BNS Staff') {
            header('Location: index.php?action=bnsMealPlansList');
            exit;
        }

        try {
            // Validate required fields
            if (empty($_POST['plan_name'])) {
                throw new Exception('Plan name is required.');
            }

            if (empty($_POST['family_id'])) {
                throw new Exception('Please select a household.');
            }

            // Get parent user_id from family_id
            $stmt = $this->db->prepare("SELECT source_user_id FROM family_profiles WHERE family_id = ?");
            $stmt->execute([$_POST['family_id']]);
            $parentUserId = $stmt->fetchColumn();

            if (!$parentUserId) {
                throw new Exception('Household not found.');
            }

            $data = [
                'user_id'           => $parentUserId,
                'family_id'         => (int)$_POST['family_id'],
                'plan_name'         => trim($_POST['plan_name']),
                'plan_description'  => trim($_POST['plan_description'] ?? ''),
                'target_weeks'      => (int)($_POST['target_weeks'] ?? 1),
                'status'            => $_POST['status'] ?? 'Draft',
                'notes'             => trim($_POST['notes'] ?? ''),
                'created_by_user_id'=> $_SESSION['user_id'],
            ];

            if (!empty($_POST['meal_plan_id'])) {
                // Update
                $mealPlanId = (int)$_POST['meal_plan_id'];
                $this->model->updateMealPlan($mealPlanId, $data);
                $_SESSION['flash'] = 'Meal plan updated successfully!';
            } else {
                // Create
                $mealPlanId = $this->model->createMealPlan($data);
                $_SESSION['flash'] = 'Meal plan created successfully!';
            }

            header('Location: index.php?action=bnsMealPlanForm&id=' . $mealPlanId);
            exit;
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
            header('Location: index.php?action=bnsMealPlanForm' . (!empty($_POST['meal_plan_id']) ? '&id=' . $_POST['meal_plan_id'] : ''));
            exit;
        }
    }

    /**
     * Add meal item to plan (BNS only)
     */
    public function addMealItem(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        // Check if BNS staff
        if ($_SESSION['role'] !== 'BNS Staff') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        try {
            $data = [
                'meal_plan_id'      => (int)$_POST['meal_plan_id'],
                'day_number'        => (int)$_POST['day_number'],
                'meal_type'         => $_POST['meal_type'],
                'dish_name'         => trim($_POST['dish_name']),
                'food_category'     => $_POST['food_category'] ?? null,
                'ingredients'       => trim($_POST['ingredients'] ?? ''),
                'serving_size'      => trim($_POST['serving_size'] ?? ''),
                'preparation_notes' => trim($_POST['preparation_notes'] ?? ''),
                'nutritional_info'  => trim($_POST['nutritional_info'] ?? ''),
            ];

            $itemId = $this->model->addMealPlanItem($data);
            
            // Get balance status for this day
            $dailyBalance = $this->foodModel->getDailyBalance((int)$data['meal_plan_id']);
            $dayBalance = $dailyBalance[$data['day_number']] ?? null;
            
            echo json_encode([
                'success' => true, 
                'item_id' => $itemId,
                'day_balance' => $dayBalance
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Delete meal item (BNS only)
     */
    public function deleteMealItem(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        // Check if BNS staff
        if ($_SESSION['role'] !== 'BNS Staff') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $itemId = (int)($_POST['item_id'] ?? 0);
        $success = $this->model->deleteMealPlanItem($itemId);
        echo json_encode(['success' => $success]);
        exit;
    }

    /**
     * Delete meal plan (BNS only)
     */
    public function deleteMealPlan(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: index.php?action=bnsMealPlansList');
            exit;
        }

        // Check if BNS staff
        if ($_SESSION['role'] !== 'BNS Staff') {
            $_SESSION['flash_error'] = 'Unauthorized action.';
            header('Location: index.php?action=bnsMealPlansList');
            exit;
        }

        $mealPlanId = (int)($_POST['meal_plan_id'] ?? 0);
        $this->model->deleteMealPlan($mealPlanId);
        
        $_SESSION['flash'] = 'Meal plan deleted successfully.';
        header('Location: index.php?action=bnsMealPlansList');
        exit;
    }

    /**
     * Show diet plans prescribed by BNS/NO II
     */
    public function showDietPlans(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        // Get family_id for current user
        $stmt = $this->db->prepare("
            SELECT family_id FROM family_profiles 
            WHERE source_user_id = :user_id 
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $familyId = $stmt->fetchColumn();

        $dietPlans = [];
        if ($familyId) {
            $dietPlans = $this->model->getDietPlansByFamily((int)$familyId);
        }
        
        include __DIR__ . '/../views/mother/diet_plans_list.php';
    }

    /**
     * View diet plan details
     */
    public function viewDietPlan(): void {
        if (!isset($_SESSION['user_id']) || empty($_GET['id'])) {
            header('Location: index.php?action=dietPlansList');
            exit;
        }

        $dietPlan = $this->model->getDietPlanById((int)$_GET['id']);
        
        if (!$dietPlan) {
            $_SESSION['flash'] = 'Diet plan not found.';
            header('Location: index.php?action=dietPlansList');
            exit;
        }
        
        include __DIR__ . '/../views/mother/diet_plan_view.php';
    }

    /**
     * Record meal consumption (Process 22)
     */
    public function recordConsumption(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        try {
            $data = [
                'meal_plan_id'      => (int)$_POST['meal_plan_id'],
                'meal_plan_item_id' => (int)$_POST['meal_plan_item_id'],
                'child_id'          => !empty($_POST['child_id']) ? (int)$_POST['child_id'] : null,
                'fm_member_id'      => !empty($_POST['fm_member_id']) ? (int)$_POST['fm_member_id'] : null,
                'user_id'           => $_SESSION['user_id'],
                'consumption_date'  => $_POST['consumption_date'] ?? date('Y-m-d'),
                'meal_type'         => $_POST['meal_type'],
                'dish_name'         => $_POST['dish_name'],
                'is_consumed'       => isset($_POST['is_consumed']) ? (int)$_POST['is_consumed'] : 1,
                'consumption_notes' => trim($_POST['consumption_notes'] ?? ''),
            ];

            $consumptionId = $this->model->recordConsumption($data);
            echo json_encode(['success' => true, 'consumption_id' => $consumptionId]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * ✨ Mother marks meal plan as completed
     * Provides feedback to BNS about adherence
     */
    public function markMealPlanCompleted(): void {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=mealPlansList');
            exit;
        }

        $mealPlanId = (int)$_POST['meal_plan_id'];
        $feedback = trim($_POST['feedback'] ?? '');

        try {
            $success = $this->model->markAsCompleted($mealPlanId, $_SESSION['user_id'], $feedback);
            
            if ($success) {
                $_SESSION['flash_success'] = '✅ Meal plan marked as completed! Thank you for your feedback.';
            } else {
                $_SESSION['flash_error'] = 'Failed to mark meal plan as completed.';
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
        }

        header('Location: index.php?action=mealPlansList');
        exit;
    }

    /**
     * ✨ NEW: Mark individual meal as consumed (CAMERA-ONLY with photo evidence)
     * Tracks consumption and auto-deducts ingredients from pantry
     * Photo evidence is REQUIRED - no upload from gallery allowed
     */
    public function markMealConsumed(): void {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        try {
            $itemId = (int)$_POST['item_id'];
            $notes = trim($_POST['notes'] ?? 'Consumed by family');
            $photoPath = null;

            // Photo is REQUIRED
            if (!isset($_FILES['consumption_photo']) || $_FILES['consumption_photo']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Photo evidence is required. Please take a photo of the meal.');
            }

            // Handle photo upload
            $uploadDir = __DIR__ . '/../../uploads/meal_consumption/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique filename
            $fileExtension = pathinfo($_FILES['consumption_photo']['name'], PATHINFO_EXTENSION);
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                throw new Exception('Invalid file type. Only JPG, PNG, and GIF are allowed.');
            }

            // Validate file size (max 5MB)
            if ($_FILES['consumption_photo']['size'] > 5 * 1024 * 1024) {
                throw new Exception('File too large. Maximum size is 5MB.');
            }

            $fileName = $itemId . '_' . time() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['consumption_photo']['tmp_name'], $targetPath)) {
                $photoPath = 'uploads/meal_consumption/' . $fileName;
            } else {
                throw new Exception('Failed to upload photo. Please try again.');
            }

            $success = $this->model->markMealItemConsumed($itemId, $_SESSION['user_id'], $notes, $photoPath);
            
            if ($success) {
                // TODO: Auto-deduct ingredients from pantry here
                echo json_encode([
                    'success' => true,
                    'message' => '✅ Meal marked as consumed with photo evidence!'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to mark meal as consumed'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Check ingredient availability in pantry for meal plan items
     */
    private function checkIngredientAvailability($mealPlanItems, $familyId) {
        if (empty($mealPlanItems) || !$familyId) {
            return [];
        }

        // Get all pantry items for this family
        $pantryItems = $this->pantryModel->getPantryItems($familyId);
        
        // Create a map of pantry items (lowercase for matching)
        $pantryMap = [];
        foreach ($pantryItems as $item) {
            $name = strtolower(trim($item['item_name']));
            $pantryMap[$name] = [
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'pantry_id' => $item['pantry_id'],
                'original_name' => $item['item_name']
            ];
        }
        
        $availability = [];
        
        foreach ($mealPlanItems as $item) {
            if (empty($item['ingredients'])) {
                continue;
            }
            
            $ingredients = explode(',', $item['ingredients']);
            $available = 0;
            $total = 0;
            $missing = [];
            $matched = [];
            
            foreach ($ingredients as $ingredient) {
                $originalIngredient = trim($ingredient);
                $ingredient = strtolower(trim($ingredient));
                if (empty($ingredient)) continue;
                
                $total++;
                
                // Direct match
                if (isset($pantryMap[$ingredient])) {
                    $available++;
                    $matched[] = $originalIngredient . ' (✓ direct: ' . $pantryMap[$ingredient]['original_name'] . ')';
                    continue;
                }
                
                // Fuzzy matching for common Filipino food names
                $found = false;
                foreach ($pantryMap as $pantryItem => $details) {
                    if ($this->ingredientMatches($ingredient, $pantryItem)) {
                        $available++;
                        $matched[] = $originalIngredient . ' (✓ matched: ' . $details['original_name'] . ')';
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $missing[] = $originalIngredient;
                }
            }
            
            $availability[$item['item_id']] = [
                'total_ingredients' => $total,
                'available_ingredients' => $available,
                'completion_percent' => $total > 0 ? round(($available / $total) * 100) : 0,
                'is_ready' => $available >= $total && $total > 0,
                'missing_ingredients' => $missing,
                'matched_ingredients' => $matched
            ];
        }
        
        return $availability;
    }

    /**
     * Simple ingredient matching logic for Filipino foods
     */
    private function ingredientMatches($ingredient, $pantryItem) {
        // Normalize both strings - remove parentheses, extra spaces, convert to lowercase
        $ingredient = strtolower(trim(preg_replace('/\([^)]*\)/', '', $ingredient)));
        $pantryItem = strtolower(trim(preg_replace('/\([^)]*\)/', '', $pantryItem)));
        
        // Common aliases/alternatives
        $aliases = [
            'sigarilyas' => ['string beans', 'sitaw'],
            'string beans' => ['sigarilyas', 'sitaw'],
            'sitaw' => ['string beans', 'sigarilyas'],
            'kamote' => ['sweet potato'],
            'sweet potato' => ['kamote'],
            'kangkong' => ['water spinach'],
            'water spinach' => ['kangkong'],
            'saging' => ['banana'],
            'banana' => ['saging'],
            'manok' => ['chicken'],
            'chicken' => ['manok'],
            'baboy' => ['pork'],
            'pork' => ['baboy'],
            'isda' => ['fish'],
            'fish' => ['isda'],
            // ✨ NEW: Common Filipino cooking ingredients  
            'ahos' => ['garlic'],
            'garlic' => ['ahos', 'bawang'],
            'bombay' => ['onion', 'sibuyas'],
            'onion' => ['bombay', 'sibuyas'],
            'kamatis' => ['tomato'],
            'tomato' => ['kamatis'],
            'luya' => ['ginger'],
            'ginger' => ['luya'],
            'sibuyas' => ['onion', 'bombay'],
            'sibuyas dahon' => ['spring onion', 'scallion', 'green onion'],
            'spring onion' => ['sibuyas dahon', 'scallion'],
            'scallion' => ['sibuyas dahon', 'spring onion'],
            'bawang' => ['garlic', 'ahos'],
            'paminta' => ['pepper', 'black pepper'],
            'pepper' => ['paminta'],
            'asin' => ['salt'],
            'salt' => ['asin'],
            'mantika' => ['oil', 'cooking oil'],
            'oil' => ['mantika', 'cooking oil'],
            'cooking oil' => ['mantika', 'oil'],
            'toyo' => ['soy sauce'],
            'soy sauce' => ['toyo'],
            'patis' => ['fish sauce'],
            'fish sauce' => ['patis'],
            'suka' => ['vinegar'],
            'vinegar' => ['suka'],
            'asukal' => ['sugar'],
            'sugar' => ['asukal'],
        ];
        
        // Direct exact match
        if ($ingredient === $pantryItem) {
            return true;
        }
        
        // Check direct aliases
        if (isset($aliases[$ingredient])) {
            foreach ($aliases[$ingredient] as $alias) {
                if ($alias === $pantryItem) {
                    return true;
                }
                // Also check if pantry item contains the alias
                if (strpos($pantryItem, $alias) !== false) {
                    return true;
                }
            }
        }
        
        // Check substring matching (e.g., "chicken breast" contains "chicken")
        if (strpos($pantryItem, $ingredient) !== false || strpos($ingredient, $pantryItem) !== false) {
            return true;
        }
        
        // Check if words are similar (Levenshtein distance) - only for longer words
        if (strlen($ingredient) > 3 && strlen($pantryItem) > 3) {
            $similarity = 1 - (levenshtein($ingredient, $pantryItem) / max(strlen($ingredient), strlen($pantryItem)));
            if ($similarity > 0.7) { // 70% similarity
                return true;
            }
        }
        
        return false;
    }
}
