<?php
/**
 * Recipe Model
 * Manages pre-loaded Filipino meal recipes for fast meal plan creation
 * 
 * @author KusiNay Development Team
 * @date June 19, 2026
 */

class RecipeModel {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Get all active recipes
     * 
     * @return array List of all recipes
     */
    public function getAllRecipes() {
        $query = "
            SELECT * FROM meal_recipes 
            WHERE is_active = 1 
            ORDER BY is_popular DESC, recipe_name ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get recipes by category (GO, GROW, GLOW)
     * 
     * @param string $category Food category
     * @return array List of recipes in that category
     */
    public function getRecipesByCategory($category) {
        $query = "
            SELECT * FROM meal_recipes 
            WHERE food_category = :category 
            AND is_active = 1 
            ORDER BY is_popular DESC, recipe_name ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute(['category' => $category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get recipes by meal type (Breakfast, Lunch, Dinner, Snack)
     * 
     * @param string $mealType Meal type
     * @return array List of recipes for that meal type
     */
    public function getRecipesByMealType($mealType) {
        $query = "
            SELECT * FROM meal_recipes 
            WHERE (meal_type = :meal_type OR meal_type = 'Any')
            AND is_active = 1 
            ORDER BY is_popular DESC, recipe_name ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute(['meal_type' => $mealType]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get recipes by category AND meal type
     * 
     * @param string $category Food category
     * @param string $mealType Meal type
     * @return array Filtered recipes
     */
    public function getRecipesByCategoryAndMealType($category, $mealType) {
        $query = "
            SELECT * FROM meal_recipes 
            WHERE food_category = :category 
            AND (meal_type = :meal_type OR meal_type = 'Any')
            AND is_active = 1 
            ORDER BY is_popular DESC, recipe_name ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'category' => $category,
            'meal_type' => $mealType
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get popular recipes (most commonly used)
     * 
     * @param int $limit Number of recipes to return
     * @return array List of popular recipes
     */
    public function getPopularRecipes($limit = 10) {
        $query = "
            SELECT * FROM meal_recipes 
            WHERE is_popular = 1 
            AND is_active = 1 
            ORDER BY recipe_name ASC
            LIMIT :limit
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get a single recipe by ID
     * 
     * @param int $recipeId Recipe ID
     * @return array|false Recipe data or false if not found
     */
    public function getRecipeById($recipeId) {
        $query = "
            SELECT * FROM meal_recipes 
            WHERE recipe_id = :recipe_id 
            AND is_active = 1
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute(['recipe_id' => $recipeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Search recipes by name
     * 
     * @param string $searchTerm Search term
     * @return array Matching recipes
     */
    public function searchRecipes($searchTerm) {
        $query = "
            SELECT * FROM meal_recipes 
            WHERE recipe_name LIKE :search 
            AND is_active = 1 
            ORDER BY is_popular DESC, recipe_name ASC
            LIMIT 20
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute(['search' => '%' . $searchTerm . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get recipe statistics
     * 
     * @return array Statistics about recipes
     */
    public function getRecipeStats() {
        $query = "
            SELECT 
                COUNT(*) as total_recipes,
                SUM(CASE WHEN food_category = 'GO' THEN 1 ELSE 0 END) as go_count,
                SUM(CASE WHEN food_category = 'GROW' THEN 1 ELSE 0 END) as grow_count,
                SUM(CASE WHEN food_category = 'GLOW' THEN 1 ELSE 0 END) as glow_count,
                SUM(CASE WHEN is_popular = 1 THEN 1 ELSE 0 END) as popular_count,
                SUM(CASE WHEN meal_type = 'Breakfast' THEN 1 ELSE 0 END) as breakfast_count,
                SUM(CASE WHEN meal_type IN ('Lunch', 'Dinner', 'Any') THEN 1 ELSE 0 END) as main_meal_count,
                SUM(CASE WHEN meal_type = 'Snack' THEN 1 ELSE 0 END) as snack_count
            FROM meal_recipes 
            WHERE is_active = 1
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Create a new custom recipe (for BNS to add their own)
     * 
     * @param array $data Recipe data
     * @return int|false New recipe ID or false on failure
     */
    public function createRecipe($data) {
        $query = "
            INSERT INTO meal_recipes (
                recipe_name, 
                food_category, 
                meal_type, 
                ingredients, 
                serving_size, 
                nutritional_info, 
                preparation_notes,
                is_popular
            ) VALUES (
                :recipe_name, 
                :food_category, 
                :meal_type, 
                :ingredients, 
                :serving_size, 
                :nutritional_info, 
                :preparation_notes,
                0
            )
        ";
        
        $stmt = $this->db->prepare($query);
        
        $result = $stmt->execute([
            'recipe_name' => $data['recipe_name'],
            'food_category' => $data['food_category'],
            'meal_type' => $data['meal_type'] ?? 'Any',
            'ingredients' => $data['ingredients'],
            'serving_size' => $data['serving_size'],
            'nutritional_info' => $data['nutritional_info'] ?? '',
            'preparation_notes' => $data['preparation_notes'] ?? ''
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Update recipe
     * 
     * @param int $recipeId Recipe ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function updateRecipe($recipeId, $data) {
        $query = "
            UPDATE meal_recipes SET
                recipe_name = :recipe_name,
                food_category = :food_category,
                meal_type = :meal_type,
                ingredients = :ingredients,
                serving_size = :serving_size,
                nutritional_info = :nutritional_info,
                preparation_notes = :preparation_notes
            WHERE recipe_id = :recipe_id
        ";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            'recipe_id' => $recipeId,
            'recipe_name' => $data['recipe_name'],
            'food_category' => $data['food_category'],
            'meal_type' => $data['meal_type'],
            'ingredients' => $data['ingredients'],
            'serving_size' => $data['serving_size'],
            'nutritional_info' => $data['nutritional_info'],
            'preparation_notes' => $data['preparation_notes']
        ]);
    }
    
    /**
     * Toggle recipe popularity
     * 
     * @param int $recipeId Recipe ID
     * @return bool Success status
     */
    public function togglePopular($recipeId) {
        $query = "
            UPDATE meal_recipes 
            SET is_popular = NOT is_popular 
            WHERE recipe_id = :recipe_id
        ";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['recipe_id' => $recipeId]);
    }
    
    /**
     * Deactivate recipe (soft delete)
     * 
     * @param int $recipeId Recipe ID
     * @return bool Success status
     */
    public function deactivateRecipe($recipeId) {
        $query = "
            UPDATE meal_recipes 
            SET is_active = 0 
            WHERE recipe_id = :recipe_id
        ";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['recipe_id' => $recipeId]);
    }
}
?>
