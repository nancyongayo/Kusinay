<?php
/**
 * FilipinoFoodModel
 * 
 * Manages Filipino foods database for Pinggang Pinoy categorization
 * GO (Energy), GROW (Body Building), GLOW (Regulating)
 */
class FilipinoFoodModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Search foods by name with autocomplete
     */
    public function searchFoods(string $query, ?string $category = null): array {
        $sql = "SELECT * FROM filipino_foods WHERE is_active = 1 AND food_name LIKE :query";
        $params = [':query' => "%{$query}%"];
        
        if ($category) {
            $sql .= " AND food_category = :category";
            $params[':category'] = $category;
        }
        
        $sql .= " ORDER BY food_name ASC LIMIT 20";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all foods by category
     */
    public function getFoodsByCategory(string $category): array {
        $stmt = $this->db->prepare("
            SELECT * FROM filipino_foods 
            WHERE food_category = :category AND is_active = 1
            ORDER BY food_name ASC
        ");
        $stmt->execute([':category' => $category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all active foods
     */
    public function getAllFoods(): array {
        $stmt = $this->db->prepare("
            SELECT * FROM filipino_foods 
            WHERE is_active = 1
            ORDER BY food_category, food_name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get food by ID
     */
    public function getFoodById(int $foodId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM filipino_foods WHERE food_id = :food_id");
        $stmt->execute([':food_id' => $foodId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Add new food
     */
    public function addFood(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO filipino_foods (
                food_name, food_category, food_type, 
                common_serving, description
            ) VALUES (
                :food_name, :food_category, :food_type,
                :common_serving, :description
            )
        ");
        
        $stmt->execute([
            ':food_name'       => $data['food_name'],
            ':food_category'   => $data['food_category'],
            ':food_type'       => $data['food_type'] ?? null,
            ':common_serving'  => $data['common_serving'] ?? null,
            ':description'     => $data['description'] ?? null,
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Get category statistics for a meal plan
     */
    public function getMealPlanBalance(int $mealPlanId): array {
        $stmt = $this->db->prepare("
            SELECT 
                food_category,
                COUNT(*) as count
            FROM meal_plan_items
            WHERE meal_plan_id = :meal_plan_id 
            AND food_category IS NOT NULL
            GROUP BY food_category
        ");
        $stmt->execute([':meal_plan_id' => $mealPlanId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Initialize with zeros
        $balance = [
            'GO' => 0,
            'GROW' => 0,
            'GLOW' => 0
        ];
        
        foreach ($results as $row) {
            $balance[$row['food_category']] = (int)$row['count'];
        }
        
        return $balance;
    }

    /**
     * Get daily balance for a meal plan
     */
    public function getDailyBalance(int $mealPlanId): array {
        $stmt = $this->db->prepare("
            SELECT 
                day_number,
                food_category,
                COUNT(*) as count
            FROM meal_plan_items
            WHERE meal_plan_id = :meal_plan_id 
            AND food_category IS NOT NULL
            GROUP BY day_number, food_category
            ORDER BY day_number
        ");
        $stmt->execute([':meal_plan_id' => $mealPlanId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Organize by day
        $dailyBalance = [];
        for ($day = 1; $day <= 7; $day++) {
            $dailyBalance[$day] = [
                'GO' => 0,
                'GROW' => 0,
                'GLOW' => 0,
                'is_balanced' => false
            ];
        }
        
        foreach ($results as $row) {
            $day = $row['day_number'];
            $category = $row['food_category'];
            $dailyBalance[$day][$category] = (int)$row['count'];
        }
        
        // Check if each day is balanced (has all 3 categories)
        foreach ($dailyBalance as $day => &$balance) {
            $balance['is_balanced'] = ($balance['GO'] > 0 && $balance['GROW'] > 0 && $balance['GLOW'] > 0);
        }
        
        return $dailyBalance;
    }

    /**
     * Check if a meal is balanced
     */
    public function isMealBalanced(array $items): array {
        $categories = [];
        foreach ($items as $item) {
            if (!empty($item['food_category'])) {
                $categories[$item['food_category']] = true;
            }
        }
        
        $hasGo = isset($categories['GO']);
        $hasGrow = isset($categories['GROW']);
        $hasGlow = isset($categories['GLOW']);
        
        $isBalanced = $hasGo && $hasGrow && $hasGlow;
        $missing = [];
        
        if (!$hasGo) $missing[] = 'GO';
        if (!$hasGrow) $missing[] = 'GROW';
        if (!$hasGlow) $missing[] = 'GLOW';
        
        return [
            'is_balanced' => $isBalanced,
            'has_go' => $hasGo,
            'has_grow' => $hasGrow,
            'has_glow' => $hasGlow,
            'missing' => $missing,
            'status' => $isBalanced ? 'balanced' : (count($missing) >= 2 ? 'poor' : 'needs_improvement')
        ];
    }
}
