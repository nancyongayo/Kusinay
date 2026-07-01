<?php
/**
 * Recipe API Endpoint
 * Fetches recipes for quick meal plan creation
 */

// Prevent any output before JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    // Include database connection
    $configPath = __DIR__ . '/../config/database.php';
    if (!file_exists($configPath)) {
        throw new Exception('Database config file not found');
    }
    require_once $configPath;
    
    // Get database connection
    $db = getDBConnection();
    
    if (!$db) {
        throw new Exception('Failed to connect to database');
    }
    
    // Include RecipeModel
    $modelPath = __DIR__ . '/../app/models/RecipeModel.php';
    if (!file_exists($modelPath)) {
        throw new Exception('RecipeModel file not found');
    }
    require_once $modelPath;
    
    $recipeModel = new RecipeModel($db);
    
    // Get filter parameters
    $category = $_GET['category'] ?? null;
    $mealType = $_GET['meal_type'] ?? null;
    $popular = $_GET['popular'] ?? null;
    $search = $_GET['search'] ?? null;
    
    $recipes = [];
    
    // Search by name
    if ($search) {
        $recipes = $recipeModel->searchRecipes($search);
    }
    // Get popular recipes only
    elseif ($popular) {
        $recipes = $recipeModel->getPopularRecipes(20);
    }
    // Filter by category AND meal type
    elseif ($category && $mealType) {
        $recipes = $recipeModel->getRecipesByCategoryAndMealType($category, $mealType);
    }
    // Filter by category only
    elseif ($category) {
        $recipes = $recipeModel->getRecipesByCategory($category);
    }
    // Filter by meal type only
    elseif ($mealType) {
        $recipes = $recipeModel->getRecipesByMealType($mealType);
    }
    // Get all recipes
    else {
        $recipes = $recipeModel->getAllRecipes();
    }
    
    // Return JSON response
    echo json_encode([
        'success' => true,
        'count' => count($recipes),
        'recipes' => $recipes
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
