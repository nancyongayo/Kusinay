<?php
/**
 * MealPlanModel
 * 
 * Handles Process 18: Creating Meal Plan & Diet Plan
 * - Meal plans created by BNS/Nutrition Officer for households (Process 18)
 * - Parents view meal plans and extract ingredients for grocery (Process 19)
 * - Diet plans prescribed by BNS/Nutrition Officer for specific nutritional needs
 */
class MealPlanModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // ========================================================================
    // PROCESS 18: Meal Plans
    // ========================================================================

    /**
     * Create a new meal plan (by BNS/NO for a household)
     */
    public function createMealPlan(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO meal_plans (
                user_id, family_id, plan_name, plan_description,
                target_weeks, status, notes, created_by_user_id
            ) VALUES (
                :user_id, :family_id, :plan_name, :plan_description,
                :target_weeks, :status, :notes, :created_by
            )
        ");
        
        $stmt->execute([
            ':user_id'          => $data['user_id'],
            ':family_id'        => $data['family_id'] ?? null,
            ':plan_name'        => $data['plan_name'],
            ':plan_description' => $data['plan_description'] ?? null,
            ':target_weeks'     => $data['target_weeks'] ?? 1,
            ':status'           => $data['status'] ?? 'Draft',
            ':notes'            => $data['notes'] ?? null,
            ':created_by'       => $data['created_by_user_id'] ?? $data['user_id'],
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update meal plan
     */
    public function updateMealPlan(int $mealPlanId, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE meal_plans SET
                plan_name = :plan_name,
                plan_description = :plan_description,
                target_weeks = :target_weeks,
                status = :status,
                notes = :notes
            WHERE meal_plan_id = :meal_plan_id
        ");
        
        return $stmt->execute([
            ':meal_plan_id'     => $mealPlanId,
            ':plan_name'        => $data['plan_name'],
            ':plan_description' => $data['plan_description'] ?? null,
            ':target_weeks'     => $data['target_weeks'] ?? 1,
            ':status'           => $data['status'],
            ':notes'            => $data['notes'] ?? null,
        ]);
    }

    /**
     * Get meal plan by ID
     */
    public function getMealPlanById(int $mealPlanId): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                mp.*,
                u.first_name AS parent_first_name,
                u.last_name AS parent_last_name,
                creator.first_name AS creator_first_name,
                creator.last_name AS creator_last_name,
                r.role_name AS creator_role,
                fp.hh_number,
                fp.purok
            FROM meal_plans mp
            LEFT JOIN users u ON u.user_id = mp.user_id
            LEFT JOIN users creator ON creator.user_id = mp.created_by_user_id
            LEFT JOIN roles r ON r.role_id = creator.role_id
            LEFT JOIN family_profiles fp ON fp.family_id = mp.family_id
            WHERE mp.meal_plan_id = :meal_plan_id
        ");
        $stmt->execute([':meal_plan_id' => $mealPlanId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all meal plans for a user (parent view)
     */
    public function getMealPlansByUser(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT 
                mp.*,
                (SELECT COUNT(*) FROM meal_plan_items WHERE meal_plan_id = mp.meal_plan_id) AS item_count,
                creator.first_name AS creator_first_name,
                creator.last_name AS creator_last_name,
                r.role_name AS creator_role
            FROM meal_plans mp
            LEFT JOIN users creator ON creator.user_id = mp.created_by_user_id
            LEFT JOIN roles r ON r.role_id = creator.role_id
            WHERE mp.user_id = :user_id
            ORDER BY mp.created_date DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all meal plans for a family (parent view by family)
     */
    public function getMealPlansByFamily(int $familyId): array {
        $stmt = $this->db->prepare("
            SELECT 
                mp.*,
                (SELECT COUNT(*) FROM meal_plan_items WHERE meal_plan_id = mp.meal_plan_id) AS item_count,
                creator.first_name AS creator_first_name,
                creator.last_name AS creator_last_name,
                r.role_name AS creator_role,
                u.first_name AS parent_first_name,
                u.last_name AS parent_last_name
            FROM meal_plans mp
            LEFT JOIN users creator ON creator.user_id = mp.created_by_user_id
            LEFT JOIN roles r ON r.role_id = creator.role_id
            LEFT JOIN users u ON u.user_id = mp.user_id
            WHERE mp.family_id = :family_id
            ORDER BY mp.created_date DESC
        ");
        $stmt->execute([':family_id' => $familyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all meal plans created by BNS (for BNS view)
     */
    public function getAllMealPlansByBNS(int $bnsUserId): array {
        $stmt = $this->db->prepare("
            SELECT 
                mp.*,
                (SELECT COUNT(*) FROM meal_plan_items WHERE meal_plan_id = mp.meal_plan_id) AS item_count,
                (SELECT COUNT(*) FROM meal_plan_items WHERE meal_plan_id = mp.meal_plan_id AND is_consumed = 1) AS consumed_meals,
                u.first_name AS parent_first_name,
                u.last_name AS parent_last_name,
                fp.hh_number,
                fp.purok
            FROM meal_plans mp
            LEFT JOIN users u ON u.user_id = mp.user_id
            LEFT JOIN family_profiles fp ON fp.family_id = mp.family_id
            WHERE mp.created_by_user_id = :bns_user_id
            ORDER BY mp.created_date DESC
        ");
        $stmt->execute([':bns_user_id' => $bnsUserId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all households/families for meal plan assignment
     * Only includes households with a linked user/name
     */
    public function getAllHouseholdsForMealPlan(): array {
        $stmt = $this->db->prepare("
            SELECT 
                fp.family_id,
                fp.hh_number,
                fp.purok,
                fp.source_user_id,
                u.first_name AS parent_first_name,
                u.last_name AS parent_last_name,
                u.email,
                (SELECT COUNT(*) FROM meal_plans WHERE family_id = fp.family_id) AS meal_plan_count
            FROM family_profiles fp
            INNER JOIN users u ON u.user_id = fp.source_user_id
            ORDER BY fp.hh_number ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete meal plan
     */
    public function deleteMealPlan(int $mealPlanId): bool {
        $stmt = $this->db->prepare("DELETE FROM meal_plans WHERE meal_plan_id = :meal_plan_id");
        return $stmt->execute([':meal_plan_id' => $mealPlanId]);
    }

    // ========================================================================
    // Meal Plan Items
    // ========================================================================

    /**
     * Add item to meal plan
     */
    public function addMealPlanItem(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO meal_plan_items (
                meal_plan_id, day_number, meal_type, dish_name, food_category,
                ingredients, serving_size, preparation_notes, nutritional_info
            ) VALUES (
                :meal_plan_id, :day_number, :meal_type, :dish_name, :food_category,
                :ingredients, :serving_size, :preparation_notes, :nutritional_info
            )
        ");
        
        $stmt->execute([
            ':meal_plan_id'      => $data['meal_plan_id'],
            ':day_number'        => $data['day_number'],
            ':meal_type'         => $data['meal_type'],
            ':dish_name'         => $data['dish_name'],
            ':food_category'     => $data['food_category'] ?? null,
            ':ingredients'       => $data['ingredients'] ?? null,
            ':serving_size'      => $data['serving_size'] ?? null,
            ':preparation_notes' => $data['preparation_notes'] ?? null,
            ':nutritional_info'  => $data['nutritional_info'] ?? null,
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update meal plan item
     */
    public function updateMealPlanItem(int $itemId, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE meal_plan_items SET
                day_number = :day_number,
                meal_type = :meal_type,
                dish_name = :dish_name,
                food_category = :food_category,
                ingredients = :ingredients,
                serving_size = :serving_size,
                preparation_notes = :preparation_notes,
                nutritional_info = :nutritional_info
            WHERE item_id = :item_id
        ");
        
        return $stmt->execute([
            ':item_id'           => $itemId,
            ':day_number'        => $data['day_number'],
            ':meal_type'         => $data['meal_type'],
            ':dish_name'         => $data['dish_name'],
            ':food_category'     => $data['food_category'] ?? null,
            ':ingredients'       => $data['ingredients'] ?? null,
            ':serving_size'      => $data['serving_size'] ?? null,
            ':preparation_notes' => $data['preparation_notes'] ?? null,
            ':nutritional_info'  => $data['nutritional_info'] ?? null,
        ]);
    }

    /**
     * Get all items for a meal plan
     */
    public function getMealPlanItems(int $mealPlanId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM meal_plan_items
            WHERE meal_plan_id = :meal_plan_id
            ORDER BY day_number ASC, 
                FIELD(meal_type, 'Breakfast', 'Snack', 'Lunch', 'Dinner') ASC
        ");
        $stmt->execute([':meal_plan_id' => $mealPlanId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete meal plan item
     */
    public function deleteMealPlanItem(int $itemId): bool {
        $stmt = $this->db->prepare("DELETE FROM meal_plan_items WHERE item_id = :item_id");
        return $stmt->execute([':item_id' => $itemId]);
    }

    // ========================================================================
    // Diet Plans
    // ========================================================================

    /**
     * Create a diet plan (by BNS or Nutrition Officer)
     */
    public function createDietPlan(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO diet_plans (
                child_id, fm_member_id, family_id, full_name, created_by_user_id,
                plan_type, nutritional_status, target_calories_per_day,
                target_protein_grams, dietary_restrictions, recommended_foods,
                foods_to_avoid, meal_frequency, special_instructions,
                start_date, end_date, status
            ) VALUES (
                :child_id, :fm_member_id, :family_id, :full_name, :created_by_user_id,
                :plan_type, :nutritional_status, :target_calories_per_day,
                :target_protein_grams, :dietary_restrictions, :recommended_foods,
                :foods_to_avoid, :meal_frequency, :special_instructions,
                :start_date, :end_date, :status
            )
        ");
        
        $stmt->execute([
            ':child_id'                => $data['child_id'] ?? null,
            ':fm_member_id'            => $data['fm_member_id'] ?? null,
            ':family_id'               => $data['family_id'] ?? null,
            ':full_name'               => $data['full_name'],
            ':created_by_user_id'      => $data['created_by_user_id'],
            ':plan_type'               => $data['plan_type'] ?? 'General',
            ':nutritional_status'      => $data['nutritional_status'] ?? null,
            ':target_calories_per_day' => $data['target_calories_per_day'] ?? null,
            ':target_protein_grams'    => $data['target_protein_grams'] ?? null,
            ':dietary_restrictions'    => $data['dietary_restrictions'] ?? null,
            ':recommended_foods'       => $data['recommended_foods'],
            ':foods_to_avoid'          => $data['foods_to_avoid'] ?? null,
            ':meal_frequency'          => $data['meal_frequency'] ?? null,
            ':special_instructions'    => $data['special_instructions'] ?? null,
            ':start_date'              => $data['start_date'],
            ':end_date'                => $data['end_date'] ?? null,
            ':status'                  => $data['status'] ?? 'Active',
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update diet plan
     */
    public function updateDietPlan(int $dietPlanId, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE diet_plans SET
                plan_type = :plan_type,
                nutritional_status = :nutritional_status,
                target_calories_per_day = :target_calories_per_day,
                target_protein_grams = :target_protein_grams,
                dietary_restrictions = :dietary_restrictions,
                recommended_foods = :recommended_foods,
                foods_to_avoid = :foods_to_avoid,
                meal_frequency = :meal_frequency,
                special_instructions = :special_instructions,
                end_date = :end_date,
                status = :status
            WHERE diet_plan_id = :diet_plan_id
        ");
        
        return $stmt->execute([
            ':diet_plan_id'            => $dietPlanId,
            ':plan_type'               => $data['plan_type'],
            ':nutritional_status'      => $data['nutritional_status'] ?? null,
            ':target_calories_per_day' => $data['target_calories_per_day'] ?? null,
            ':target_protein_grams'    => $data['target_protein_grams'] ?? null,
            ':dietary_restrictions'    => $data['dietary_restrictions'] ?? null,
            ':recommended_foods'       => $data['recommended_foods'],
            ':foods_to_avoid'          => $data['foods_to_avoid'] ?? null,
            ':meal_frequency'          => $data['meal_frequency'] ?? null,
            ':special_instructions'    => $data['special_instructions'] ?? null,
            ':end_date'                => $data['end_date'] ?? null,
            ':status'                  => $data['status'],
        ]);
    }

    /**
     * Get diet plan by ID
     */
    public function getDietPlanById(int $dietPlanId): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                dp.*,
                u.first_name AS creator_first_name,
                u.last_name AS creator_last_name,
                u.role AS creator_role
            FROM diet_plans dp
            JOIN users u ON u.user_id = dp.created_by_user_id
            WHERE dp.diet_plan_id = :diet_plan_id
        ");
        $stmt->execute([':diet_plan_id' => $dietPlanId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get diet plans for a child
     */
    public function getDietPlansByChild(int $childId): array {
        $stmt = $this->db->prepare("
            SELECT 
                dp.*,
                u.first_name AS creator_first_name,
                u.last_name AS creator_last_name
            FROM diet_plans dp
            JOIN users u ON u.user_id = dp.created_by_user_id
            WHERE dp.child_id = :child_id
            ORDER BY dp.created_date DESC
        ");
        $stmt->execute([':child_id' => $childId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get diet plans for a family
     */
    public function getDietPlansByFamily(int $familyId): array {
        $stmt = $this->db->prepare("
            SELECT 
                dp.*,
                u.first_name AS creator_first_name,
                u.last_name AS creator_last_name
            FROM diet_plans dp
            JOIN users u ON u.user_id = dp.created_by_user_id
            WHERE dp.family_id = :family_id
            ORDER BY dp.created_date DESC
        ");
        $stmt->execute([':family_id' => $familyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete diet plan
     */
    public function deleteDietPlan(int $dietPlanId): bool {
        $stmt = $this->db->prepare("DELETE FROM diet_plans WHERE diet_plan_id = :diet_plan_id");
        return $stmt->execute([':diet_plan_id' => $dietPlanId]);
    }

    // ========================================================================
    // PROCESS 22: Consumption Logs (Diet Record)
    // ========================================================================

    /**
     * Record meal consumption
     */
    public function recordConsumption(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO diet_consumption_logs (
                meal_plan_id, meal_plan_item_id, child_id, fm_member_id,
                user_id, consumption_date, meal_type, dish_name,
                is_consumed, consumption_notes
            ) VALUES (
                :meal_plan_id, :meal_plan_item_id, :child_id, :fm_member_id,
                :user_id, :consumption_date, :meal_type, :dish_name,
                :is_consumed, :consumption_notes
            )
        ");
        
        $stmt->execute([
            ':meal_plan_id'      => $data['meal_plan_id'],
            ':meal_plan_item_id' => $data['meal_plan_item_id'],
            ':child_id'          => $data['child_id'] ?? null,
            ':fm_member_id'      => $data['fm_member_id'] ?? null,
            ':user_id'           => $data['user_id'],
            ':consumption_date'  => $data['consumption_date'] ?? date('Y-m-d'),
            ':meal_type'         => $data['meal_type'],
            ':dish_name'         => $data['dish_name'],
            ':is_consumed'       => $data['is_consumed'] ?? 1,
            ':consumption_notes' => $data['consumption_notes'] ?? null,
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Get consumption history for a meal plan
     */
    public function getConsumptionHistory(int $mealPlanId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM diet_consumption_logs
            WHERE meal_plan_id = :meal_plan_id
            ORDER BY consumption_date DESC, 
                FIELD(meal_type, 'Breakfast', 'Snack', 'Lunch', 'Dinner') ASC
        ");
        $stmt->execute([':meal_plan_id' => $mealPlanId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get diet record for a child (used in Process 17)
     */
    public function getDietRecordByChild(int $childId, string $startDate, string $endDate): array {
        $stmt = $this->db->prepare("
            SELECT * FROM diet_consumption_logs
            WHERE child_id = :child_id
            AND consumption_date BETWEEN :start AND :end
            ORDER BY consumption_date DESC
        ");
        $stmt->execute([
            ':child_id' => $childId,
            ':start'    => $startDate,
            ':end'      => $endDate
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Link diet plan to meal plan
     */
    public function linkDietPlanToMealPlan(int $dietPlanId, int $mealPlanId): bool {
        $stmt = $this->db->prepare("
            INSERT INTO diet_plan_meal_plan_link (diet_plan_id, meal_plan_id)
            VALUES (:diet_plan_id, :meal_plan_id)
            ON DUPLICATE KEY UPDATE linked_date = CURRENT_TIMESTAMP
        ");
        return $stmt->execute([
            ':diet_plan_id' => $dietPlanId,
            ':meal_plan_id' => $mealPlanId
        ]);
    }

    /**
     * Get linked meal plans for a diet plan
     */
    public function getLinkedMealPlans(int $dietPlanId): array {
        $stmt = $this->db->prepare("
            SELECT mp.*
            FROM meal_plans mp
            JOIN diet_plan_meal_plan_link dpl ON dpl.meal_plan_id = mp.meal_plan_id
            WHERE dpl.diet_plan_id = :diet_plan_id
        ");
        $stmt->execute([':diet_plan_id' => $dietPlanId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ✨ Mark meal plan as completed by mother
     * Tracks adherence and provides feedback to BNS
     */
    public function markAsCompleted(int $mealPlanId, int $userId, string $feedback = ''): bool {
        $stmt = $this->db->prepare("
            UPDATE meal_plans SET
                completion_status = 'Completed',
                completed_by_mother = 1,
                completion_date = CURRENT_TIMESTAMP,
                mother_feedback = :feedback
            WHERE meal_plan_id = :meal_plan_id
        ");
        
        return $stmt->execute([
            ':meal_plan_id' => $mealPlanId,
            ':feedback' => $feedback
        ]);
    }

    /**
     * ✨ Get completion statistics for BNS dashboard
     */
    public function getCompletionStats(int $bnsUserId): array {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_plans,
                SUM(CASE WHEN completed_by_mother = 1 THEN 1 ELSE 0 END) as completed_plans,
                SUM(CASE WHEN completion_status = 'In Progress' THEN 1 ELSE 0 END) as in_progress_plans,
                SUM(CASE WHEN completion_status = 'Not Started' THEN 1 ELSE 0 END) as not_started_plans
            FROM meal_plans
            WHERE created_by_user_id = :bns_user_id
        ");
        $stmt->execute([':bns_user_id' => $bnsUserId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * ✨ NEW: Mark individual meal item as consumed (with photo evidence)
     */
    public function markMealItemConsumed(int $itemId, int $userId, string $notes = '', ?string $photoPath = null): bool {
        $stmt = $this->db->prepare("
            UPDATE meal_plan_items SET
                is_consumed = 1,
                consumed_date = CURRENT_TIMESTAMP,
                consumed_by_user_id = :user_id,
                consumption_notes = :notes,
                consumption_photo = :photo_path
            WHERE item_id = :item_id
        ");
        
        return $stmt->execute([
            ':item_id' => $itemId,
            ':user_id' => $userId,
            ':notes' => $notes,
            ':photo_path' => $photoPath
        ]);
    }

    /**
     * ✨ Get consumption statistics for a meal plan
     */
    public function getMealPlanConsumptionStats(int $mealPlanId): array {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_meals,
                SUM(CASE WHEN is_consumed = 1 THEN 1 ELSE 0 END) as consumed_meals,
                ROUND(SUM(CASE WHEN is_consumed = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as completion_percentage
            FROM meal_plan_items
            WHERE meal_plan_id = :meal_plan_id
        ");
        $stmt->execute([':meal_plan_id' => $mealPlanId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total_meals' => 0,
            'consumed_meals' => 0,
            'completion_percentage' => 0
        ];
    }
}
