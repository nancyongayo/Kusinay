<?php
/**
 * GroceryListModel
 * 
 * Handles Process 19: Buying Goods (Grocery List Management)
 * - Parents create grocery lists (can be generated from meal plans created by BNS)
 * - Track purchases from market vendors
 * - Auto-save purchased items to household pantry (Process 21)
 */
class GroceryListModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // ========================================================================
    // PROCESS 19: Grocery Lists
    // ========================================================================

    /**
     * Create a new grocery list
     */
    public function createGroceryList(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO grocery_lists (
                user_id, family_id, meal_plan_id, list_name,
                list_date, total_estimated_cost, status, notes
            ) VALUES (
                :user_id, :family_id, :meal_plan_id, :list_name,
                :list_date, :total_estimated_cost, :status, :notes
            )
        ");
        
        $stmt->execute([
            ':user_id'              => $data['user_id'],
            ':family_id'            => $data['family_id'] ?? null,
            ':meal_plan_id'         => $data['meal_plan_id'] ?? null,
            ':list_name'            => $data['list_name'],
            ':list_date'            => $data['list_date'] ?? date('Y-m-d'),
            ':total_estimated_cost' => $data['total_estimated_cost'] ?? 0.00,
            ':status'               => $data['status'] ?? 'Active',
            ':notes'                => $data['notes'] ?? null,
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update grocery list
     */
    public function updateGroceryList(int $groceryListId, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE grocery_lists SET
                list_name = :list_name,
                list_date = :list_date,
                total_estimated_cost = :total_estimated_cost,
                status = :status,
                notes = :notes,
                completed_date = :completed_date
            WHERE grocery_list_id = :grocery_list_id
        ");
        
        return $stmt->execute([
            ':grocery_list_id'      => $groceryListId,
            ':list_name'            => $data['list_name'],
            ':list_date'            => $data['list_date'],
            ':total_estimated_cost' => $data['total_estimated_cost'],
            ':status'               => $data['status'],
            ':notes'                => $data['notes'] ?? null,
            ':completed_date'       => $data['completed_date'] ?? null,
        ]);
    }

    /**
     * Get grocery list by ID
     */
    public function getGroceryListById(int $groceryListId): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                gl.*,
                u.first_name,
                u.last_name,
                fp.hh_number,
                mp.plan_name AS meal_plan_name
            FROM grocery_lists gl
            JOIN users u ON u.user_id = gl.user_id
            LEFT JOIN family_profiles fp ON fp.family_id = gl.family_id
            LEFT JOIN meal_plans mp ON mp.meal_plan_id = gl.meal_plan_id
            WHERE gl.grocery_list_id = :grocery_list_id
        ");
        $stmt->execute([':grocery_list_id' => $groceryListId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all grocery lists for a user
     */
    public function getGroceryListsByUser(int $userId, ?string $status = null): array {
        $where = "WHERE gl.user_id = :user_id";
        $params = [':user_id' => $userId];
        
        if ($status) {
            $where .= " AND gl.status = :status";
            $params[':status'] = $status;
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                gl.*,
                (SELECT COUNT(*) FROM grocery_list_items WHERE grocery_list_id = gl.grocery_list_id) AS item_count,
                (SELECT COUNT(*) FROM grocery_list_items WHERE grocery_list_id = gl.grocery_list_id AND is_purchased = 1) AS purchased_count
            FROM grocery_lists gl
            {$where}
            ORDER BY gl.list_date DESC, gl.created_date DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all active grocery lists (for market vendors to see)
     */
    public function getAllActiveGroceryLists(): array {
        $stmt = $this->db->prepare("
            SELECT 
                gl.*,
                u.first_name,
                u.last_name,
                fp.hh_number,
                fp.purok,
                (SELECT COUNT(*) FROM grocery_list_items WHERE grocery_list_id = gl.grocery_list_id) AS item_count,
                (SELECT SUM(estimated_price * quantity) FROM grocery_list_items WHERE grocery_list_id = gl.grocery_list_id) AS total_value
            FROM grocery_lists gl
            JOIN users u ON u.user_id = gl.user_id
            LEFT JOIN family_profiles fp ON fp.family_id = gl.family_id
            WHERE gl.status = 'Active'
            ORDER BY gl.list_date DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete grocery list
     */
    public function deleteGroceryList(int $groceryListId): bool {
        $stmt = $this->db->prepare("DELETE FROM grocery_lists WHERE grocery_list_id = :grocery_list_id");
        return $stmt->execute([':grocery_list_id' => $groceryListId]);
    }

    /**
     * Mark grocery list as completed
     */
    public function markAsCompleted(int $groceryListId): bool {
        $stmt = $this->db->prepare("
            UPDATE grocery_lists SET
                status = 'Completed',
                completed_date = CURRENT_TIMESTAMP
            WHERE grocery_list_id = :grocery_list_id
        ");
        return $stmt->execute([':grocery_list_id' => $groceryListId]);
    }

    // ========================================================================
    // Grocery List Items
    // ========================================================================

    /**
     * Add item to grocery list
     */
    public function addGroceryListItem(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO grocery_list_items (
                grocery_list_id, product_name, category, quantity,
                unit, estimated_price, notes
            ) VALUES (
                :grocery_list_id, :product_name, :category, :quantity,
                :unit, :estimated_price, :notes
            )
        ");
        
        $stmt->execute([
            ':grocery_list_id' => $data['grocery_list_id'],
            ':product_name'    => $data['product_name'],
            ':category'        => $data['category'] ?? null,
            ':quantity'        => $data['quantity'],
            ':unit'            => $data['unit'],
            ':estimated_price' => $data['estimated_price'] ?? null,
            ':notes'           => $data['notes'] ?? null,
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update grocery list item
     */
    public function updateGroceryListItem(int $itemId, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE grocery_list_items SET
                product_name = :product_name,
                category = :category,
                quantity = :quantity,
                unit = :unit,
                estimated_price = :estimated_price,
                notes = :notes
            WHERE item_id = :item_id
        ");
        
        return $stmt->execute([
            ':item_id'         => $itemId,
            ':product_name'    => $data['product_name'],
            ':category'        => $data['category'] ?? null,
            ':quantity'        => $data['quantity'],
            ':unit'            => $data['unit'],
            ':estimated_price' => $data['estimated_price'] ?? null,
            ':notes'           => $data['notes'] ?? null,
        ]);
    }

    /**
     * Mark item as purchased and save to pantry
     */
    public function markItemAsPurchased(int $itemId, array $data): bool {
        try {
            $this->db->beginTransaction();

            // Update grocery item
            $stmt = $this->db->prepare("
                UPDATE grocery_list_items SET
                    is_purchased = 1,
                    purchased_from_vendor_id = :vendor_id,
                    actual_price = :actual_price,
                    purchase_date = CURRENT_TIMESTAMP
                WHERE item_id = :item_id
            ");
            
            $stmt->execute([
                ':item_id'      => $itemId,
                ':vendor_id'    => $data['vendor_id'] ?? null,
                ':actual_price' => $data['actual_price'] ?? null,
            ]);

            // Get grocery list item details
            $item = $this->getGroceryListItemById($itemId);
            if (!$item) {
                throw new Exception('Grocery item not found');
            }

            // Get grocery list details for family_id and user_id
            $list = $this->getGroceryListById($item['grocery_list_id']);
            if (!$list || !$list['family_id']) {
                throw new Exception('Grocery list or family not found');
            }

            // Add to pantry (only if family_id exists)
            require_once __DIR__ . '/PantryModel.php';
            $pantryModel = new PantryModel($this->db);
            
            $pantryModel->replenishItem([
                'family_id'    => $list['family_id'],
                'user_id'      => $list['user_id'],
                'item_name'    => $item['product_name'],
                'category'     => $item['category'],
                'quantity'     => $item['quantity'],
                'unit'         => $item['unit'],
                'reference_id' => $itemId,
                'notes'        => 'Purchased from grocery list: ' . $list['list_name']
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Mark item purchased error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Unmark item as purchased and remove from pantry
     */
    public function unmarkItemAsPurchased(int $itemId): bool {
        try {
            $this->db->beginTransaction();

            // Get item details before unmarking
            $item = $this->getGroceryListItemById($itemId);
            if (!$item) {
                throw new Exception('Grocery item not found');
            }

            // Update grocery item
            $stmt = $this->db->prepare("
                UPDATE grocery_list_items SET
                    is_purchased = 0,
                    purchased_from_vendor_id = NULL,
                    actual_price = NULL,
                    purchase_date = NULL
                WHERE item_id = :item_id
            ");
            $stmt->execute([':item_id' => $itemId]);

            // Get grocery list details
            $list = $this->getGroceryListById($item['grocery_list_id']);
            
            // Remove from pantry if it was added
            if ($list && $list['family_id']) {
                require_once __DIR__ . '/PantryModel.php';
                $pantryModel = new PantryModel($this->db);
                
                // Find pantry item and consume the quantity
                $stmt = $this->db->prepare("
                    SELECT pantry_id FROM household_pantry 
                    WHERE family_id = :family_id AND item_name = :item_name
                ");
                $stmt->execute([
                    ':family_id' => $list['family_id'],
                    ':item_name' => $item['product_name']
                ]);
                $pantryId = $stmt->fetchColumn();
                
                if ($pantryId) {
                    $pantryModel->consumeItem($pantryId, $item['quantity'], $list['user_id'], $itemId);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Unmark item purchased error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all items for a grocery list
     * NOW INCLUDES PRICE SOURCE INFORMATION
     */
    public function getGroceryListItems(int $groceryListId): array {
        $stmt = $this->db->prepare("
            SELECT 
                gli.*,
                u.first_name AS vendor_first_name,
                u.last_name AS vendor_last_name
            FROM grocery_list_items gli
            LEFT JOIN users u ON u.user_id = gli.purchased_from_vendor_id
            WHERE gli.grocery_list_id = :grocery_list_id
            ORDER BY gli.category ASC, gli.product_name ASC
        ");
        $stmt->execute([':grocery_list_id' => $groceryListId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Enrich each item with price source information
        foreach ($items as &$item) {
            if ($item['estimated_price']) {
                // Try to find where this price came from
                $priceInfo = $this->getSmartPrice($item['product_name']);
                
                if ($priceInfo) {
                    if ($priceInfo['source'] === 'vendor') {
                        $item['price_source_info'] = 'Vendor: ' . $priceInfo['vendor_name'];
                    } else if ($priceInfo['source'] === 'srp') {
                        $item['price_source_info'] = 'Gov\'t SRP';
                    }
                } else {
                    $item['price_source_info'] = 'Manual entry';
                }
            } else {
                $item['price_source_info'] = null;
            }
        }
        
        return $items;
    }

    /**
     * Get item by ID
     */
    public function getGroceryListItemById(int $itemId): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM grocery_list_items
            WHERE item_id = :item_id
        ");
        $stmt->execute([':item_id' => $itemId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Delete grocery list item
     */
    public function deleteGroceryListItem(int $itemId): bool {
        $stmt = $this->db->prepare("DELETE FROM grocery_list_items WHERE item_id = :item_id");
        return $stmt->execute([':item_id' => $itemId]);
    }

    /**
     * Get grocery list statistics
     */
    public function getGroceryListStats(int $groceryListId): array {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_items,
                SUM(is_purchased) as purchased_items,
                SUM(CASE WHEN is_purchased = 0 THEN 1 ELSE 0 END) as pending_items,
                SUM(estimated_price * quantity) as total_estimated,
                SUM(CASE WHEN is_purchased = 1 THEN actual_price * quantity ELSE 0 END) as total_actual
            FROM grocery_list_items
            WHERE grocery_list_id = :grocery_list_id
        ");
        $stmt->execute([':grocery_list_id' => $groceryListId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
    
    /**
     * ✨ PHASE 3: Mark grocery list items as purchased based on order
     * After successful online purchase, auto-mark matching items
     */
    public function markItemsPurchasedFromOrder(int $userId, array $orderItems, int $orderId): int {
        $markedCount = 0;
        
        try {
            // Get all active grocery lists for this user
            $stmt = $this->db->prepare("
                SELECT grocery_list_id FROM grocery_lists 
                WHERE user_id = :user_id AND status = 'Active'
            ");
            $stmt->execute([':user_id' => $userId]);
            $groceryLists = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($groceryLists)) {
                return 0;
            }
            
            // For each order item, try to find matching grocery list item
            foreach ($orderItems as $orderItem) {
                $productName = $orderItem['product_name'];
                $quantity = $orderItem['quantity'];
                $price = $orderItem['price_per_unit'];
                
                // Find matching unpurchased item in grocery lists
                $placeholders = implode(',', array_fill(0, count($groceryLists), '?'));
                $stmt = $this->db->prepare("
                    SELECT item_id, grocery_list_id, product_name, quantity
                    FROM grocery_list_items 
                    WHERE grocery_list_id IN ($placeholders)
                      AND is_purchased = 0
                      AND (
                          product_name = ?
                          OR product_name LIKE ?
                          OR ? LIKE CONCAT('%', product_name, '%')
                      )
                    LIMIT 1
                ");
                
                $params = array_merge(
                    $groceryLists,
                    [$productName, '%' . $productName . '%', $productName]
                );
                
                $stmt->execute($params);
                $groceryItem = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($groceryItem) {
                    // Mark as purchased
                    $updateStmt = $this->db->prepare("
                        UPDATE grocery_list_items SET
                            is_purchased = 1,
                            actual_price = ?,
                            purchase_date = CURRENT_TIMESTAMP
                        WHERE item_id = ?
                    ");
                    $updateStmt->execute([$price, $groceryItem['item_id']]);
                    $markedCount++;
                    
                    error_log("Marked grocery item '{$groceryItem['product_name']}' as purchased from order #$orderId");
                }
            }
            
            // Check if any grocery lists are now complete and mark them
            foreach ($groceryLists as $listId) {
                $stats = $this->getGroceryListStats($listId);
                if ($stats['total_items'] > 0 && $stats['pending_items'] == 0) {
                    $this->markAsCompleted($listId);
                    error_log("Auto-completed grocery list #$listId (all items purchased)");
                }
            }
            
            return $markedCount;
        } catch (Exception $e) {
            error_log("Error marking grocery items as purchased: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Generate grocery list from meal plan
     * Extracts ingredients from meal plan items and creates grocery list
     * NOW WITH SMART PRICE LOOKUP (vendor prices + SRP fallback)
     * ✨ PHASE 1: NOW CHECKS PANTRY FIRST - Only adds items that are low/missing!
     */
    public function generateFromMealPlan(int $mealPlanId, int $userId): ?int {
        // Get meal plan
        $stmtPlan = $this->db->prepare("SELECT * FROM meal_plans WHERE meal_plan_id = :meal_plan_id");
        $stmtPlan->execute([':meal_plan_id' => $mealPlanId]);
        $plan = $stmtPlan->fetch(PDO::FETCH_ASSOC);
        
        if (!$plan) {
            return null;
        }
        
        // Create grocery list
        $groceryListId = $this->createGroceryList([
            'user_id'      => $userId,
            'family_id'    => $plan['family_id'] ?? null,
            'meal_plan_id' => $mealPlanId,
            'list_name'    => 'From: ' . $plan['plan_name'],
            'list_date'    => date('Y-m-d'),
            'status'       => 'Draft',
        ]);
        
        // Get meal plan items
        $stmtItems = $this->db->prepare("SELECT * FROM meal_plan_items WHERE meal_plan_id = :meal_plan_id");
        $stmtItems->execute([':meal_plan_id' => $mealPlanId]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        
        // ✨ PHASE 1: Get pantry inventory for smart checking
        $pantryInventory = [];
        if ($plan['family_id']) {
            require_once __DIR__ . '/PantryModel.php';
            $pantryModel = new PantryModel($this->db);
            $pantryItems = $pantryModel->getPantryItems($plan['family_id']);
            
            // Build pantry lookup map
            foreach ($pantryItems as $pItem) {
                $pantryInventory[strtolower($pItem['item_name'])] = [
                    'quantity' => (float)$pItem['quantity'],
                    'unit' => $pItem['unit']
                ];
            }
        }
        
        // Extract ingredients and add to grocery list
        $ingredientMap = [];
        foreach ($items as $item) {
            if (empty($item['ingredients'])) continue;
            
            // Parse ingredients (assuming comma-separated format)
            $ingredients = explode(',', $item['ingredients']);
            foreach ($ingredients as $ingredient) {
                $ingredient = trim($ingredient);
                if (empty($ingredient)) continue;
                
                // Simple grouping by ingredient name
                if (!isset($ingredientMap[$ingredient])) {
                    $ingredientMap[$ingredient] = [
                        'product_name' => $ingredient,
                        'quantity'     => 1,
                        'unit'         => 'unit',
                        'category'     => null,
                    ];
                } else {
                    $ingredientMap[$ingredient]['quantity']++;
                }
            }
        }
        
        // Add ingredients to grocery list WITH SMART PRICE LOOKUP & PANTRY CHECK
        foreach ($ingredientMap as $ingredient) {
            $neededQuantity = $ingredient['quantity'];
            $pantryStatus = 'need'; // Default: need to buy
            
            // ✨ PHASE 1: Check if item exists in pantry
            $ingredientKey = strtolower($ingredient['product_name']);
            if (isset($pantryInventory[$ingredientKey])) {
                $pantryQty = $pantryInventory[$ingredientKey]['quantity'];
                
                if ($pantryQty >= $neededQuantity) {
                    // Have enough in pantry, skip this item!
                    $pantryStatus = 'have';
                    continue; // Don't add to grocery list
                } else if ($pantryQty > 0) {
                    // Have some, but not enough
                    $neededQuantity = $neededQuantity - $pantryQty;
                    $pantryStatus = 'low';
                }
            }
            
            // Try to find price (vendor or SRP)
            $priceInfo = $this->getSmartPrice($ingredient['product_name']);
            
            $itemId = $this->addGroceryListItem([
                'grocery_list_id' => $groceryListId,
                'product_name'    => $ingredient['product_name'],
                'quantity'        => $neededQuantity, // Adjusted quantity based on pantry
                'unit'            => $ingredient['unit'],
                'category'        => $ingredient['category'],
                'estimated_price' => $priceInfo ? $priceInfo['price'] : null,
            ]);
        }
        
        // Recalculate total cost
        $this->recalculateTotalCost($groceryListId);
        
        return $groceryListId;
    }

    /**
     * Recalculate total estimated cost
     */
    public function recalculateTotalCost(int $groceryListId): bool {
        $stmt = $this->db->prepare("
            UPDATE grocery_lists SET
                total_estimated_cost = (
                    SELECT COALESCE(SUM(estimated_price * quantity), 0)
                    FROM grocery_list_items
                    WHERE grocery_list_id = :grocery_list_id1
                )
            WHERE grocery_list_id = :grocery_list_id2
        ");
        return $stmt->execute([
            ':grocery_list_id1' => $groceryListId,
            ':grocery_list_id2' => $groceryListId
        ]);
    }

    /**
     * SMART PRICE LOOKUP: Find best available price for a product
     * Priority: 1) Vendor prices, 2) SRP reference, 3) null
     * NOW WITH BILINGUAL MATCHING (Tomato = Kamatis, etc.)
     * Returns: ['price' => float, 'source' => 'vendor'|'srp', 'vendor_id' => int|null]
     */
    public function getSmartPrice(string $productName): ?array {
        // Step 0: Get all possible names for this ingredient (Filipino ↔ English)
        $searchNames = $this->getIngredientAliases($productName);
        
        // Step 1: Try to find vendor prices (PRIORITY)
        // Build dynamic WHERE clause for all aliases
        $whereClauses = [];
        $params = [];
        foreach ($searchNames as $i => $name) {
            $whereClauses[] = "vp.product_name LIKE :name{$i}a OR vp.product_name LIKE :name{$i}b";
            $params[":name{$i}a"] = '%' . $name . '%';
            $params[":name{$i}b"] = $name . '%';
        }
        $whereSQL = implode(' OR ', $whereClauses);
        
        $stmt = $this->db->prepare("
            SELECT 
                vp.product_id,
                vp.vendor_user_id,
                vp.price_per_unit,
                vp.product_name,
                u.first_name,
                u.last_name
            FROM vendor_products vp
            JOIN users u ON u.user_id = vp.vendor_user_id
            WHERE vp.is_available = 1 AND ({$whereSQL})
            ORDER BY vp.price_per_unit ASC
            LIMIT 1
        ");
        $stmt->execute($params);
        $vendor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($vendor) {
            return [
                'price' => (float)$vendor['price_per_unit'],
                'source' => 'vendor',
                'vendor_id' => $vendor['vendor_user_id'],
                'vendor_name' => $vendor['first_name'] . ' ' . $vendor['last_name'],
                'product_name' => $vendor['product_name']
            ];
        }
        
        // Step 2: Fallback to SRP reference (with aliases)
        $whereClauses = [];
        $params = [];
        foreach ($searchNames as $i => $name) {
            $whereClauses[] = "product_name LIKE :name{$i}a OR product_name LIKE :name{$i}b";
            $params[":name{$i}a"] = '%' . $name . '%';
            $params[":name{$i}b"] = $name . '%';
        }
        $whereSQL = implode(' OR ', $whereClauses);
        
        $stmt = $this->db->prepare("
            SELECT 
                srp_price,
                product_name,
                product_variant,
                price_source,
                market_location
            FROM srp_references 
            WHERE is_active = 1 AND ({$whereSQL})
            ORDER BY LENGTH(product_name) DESC
            LIMIT 1
        ");
        $stmt->execute($params);
        $srp = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($srp) {
            $displayName = $srp['product_name'];
            if ($srp['product_variant']) {
                $displayName .= ' (' . $srp['product_variant'] . ')';
            }
            
            return [
                'price' => (float)$srp['srp_price'],
                'source' => 'srp',
                'vendor_id' => null,
                'vendor_name' => null,
                'product_name' => $displayName,
                'price_source' => $srp['price_source'],
                'market_location' => $srp['market_location']
            ];
        }
        
        // Step 3: No price found
        return null;
    }
    
    /**
     * Get all possible names for an ingredient (Filipino ↔ English aliases)
     * Example: "Tomato" returns ["Tomato", "Kamatis", "Tomatoes"]
     */
    public function getIngredientAliases(string $productName): array {
        // Start with the original name
        $aliases = [$productName];
        
        // Check if ingredient_aliases table exists
        try {
            // Find aliases where this name is either primary or alias
            $stmt = $this->db->prepare("
                SELECT primary_name, alias_name 
                FROM ingredient_aliases
                WHERE primary_name LIKE :name1 
                   OR alias_name LIKE :name2
                   OR primary_name LIKE :name3
                   OR alias_name LIKE :name4
            ");
            $search = '%' . $productName . '%';
            $stmt->execute([
                ':name1' => $search,
                ':name2' => $search,
                ':name3' => $productName,
                ':name4' => $productName
            ]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $row) {
                if (!in_array($row['primary_name'], $aliases)) {
                    $aliases[] = $row['primary_name'];
                }
                if (!in_array($row['alias_name'], $aliases)) {
                    $aliases[] = $row['alias_name'];
                }
            }
        } catch (PDOException $e) {
            // Table doesn't exist yet, just use original name
        }
        
        return $aliases;
    }
    
    /**
     * LEGACY: Keep old method for backward compatibility
     */
    public function getSRPPrice(string $productName): ?float {
        $priceInfo = $this->getSmartPrice($productName);
        return $priceInfo ? $priceInfo['price'] : null;
    }

    /**
     * Get supermarket products (for browsing)
     * Combines SRP references + vendor products
     */
    public function getSupermarketProducts(?string $category = null, ?string $search = null): array {
        $where = ["is_active = 1"];
        $params = [];
        
        // Filter by supermarket-related categories
        // Added Vegetables, Fruits, Protein, Spices for fresh produce section
        $supermarketCategories = [
            'Grains', 'Canned Goods', 'Dairy', 'Condiments', 'Beverages', 'Instant Food',
            'Vegetables', 'Fruits', 'Protein', 'Spices', 'Rootcrops', 'Snacks'
        ];
        $where[] = "category IN ('" . implode("','", $supermarketCategories) . "')";
        
        if ($category) {
            $where[] = "category = :category";
            $params[':category'] = $category;
        }
        
        if ($search) {
            $where[] = "(product_name LIKE :search OR product_variant LIKE :search2)";
            $params[':search'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $where);
        
        $stmt = $this->db->prepare("
            SELECT 
                srp_id as product_id,
                CASE 
                    WHEN product_variant IS NOT NULL AND product_variant != ''
                    THEN CONCAT(product_name, ' (', product_variant, ')')
                    ELSE product_name
                END as product_name,
                product_variant,
                category,
                unit,
                srp_price as price,
                'srp' as source,
                price_source,
                market_location,
                product_image_url
            FROM srp_references
            {$whereClause}
            ORDER BY category ASC, product_name ASC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
