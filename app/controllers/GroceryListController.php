<?php
/**
 * GroceryListController
 * 
 * Handles Process 19: Buying Goods (Grocery List Management)
 * - Mothers create and manage grocery lists
 * - Can generate from meal plans
 * - Mark items as purchased
 */

require_once __DIR__ . '/../models/GroceryListModel.php';
require_once __DIR__ . '/../models/MealPlanModel.php';
require_once __DIR__ . '/../models/PantryModel.php';
require_once __DIR__ . '/../models/MarketVendorModel.php';

class GroceryListController {
    private PDO $db;
    private GroceryListModel $model;
    private MealPlanModel $mealPlanModel;
    private PantryModel $pantryModel;
    private MarketVendorModel $vendorModel;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->model = new GroceryListModel($db);
        $this->mealPlanModel = new MealPlanModel($db);
        $this->pantryModel = new PantryModel($db);
        $this->vendorModel = new MarketVendorModel($db);
    }

    /**
     * Show grocery mode (selection page)
     */
    public function showGroceryMode(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        include __DIR__ . '/../views/mother/grocery_mode.php';
    }

    /**
     * Show wet market products from vendors
     */
    public function showWetMarket(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $category = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;
        
        // Get all available products from market vendors
        $products = $this->vendorModel->getAllAvailableProducts($category, $search);
        
        // Get categories for filter
        $categories = $this->vendorModel->getCategories();
        
        include __DIR__ . '/../views/mother/wet_market.php';
    }

    /**
     * Show supermarket products (from SRP + vendors)
     */
    public function showSupermarket(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $category = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;
        
        // Get supermarket products from both SRP and vendors
        $products = $this->model->getSupermarketProducts($category, $search);
        
        // Get categories for filter dropdown
        $categories = [
            'Vegetables', 'Fruits', 'Canned Goods', 'Rice', 'Grains', 
            'Snacks', 'Dairy', 'Condiments', 'Beverages', 'Protein', 
            'Spices', 'Rootcrops', 'Instant Food'
        ];
        
        include __DIR__ . '/../views/mother/supermarket.php';
    }
    
    /**
     * ✨ SIMPLIFIED: Shop directly - Auto-add to cart and show checkout
     * ONE-CLICK: Meal Plan → Cart (Background: check pantry, match products, add to cart)
     */
    public function shopFromMealPlan(): void {
        error_log("=== shopFromMealPlan CALLED ===");
        error_log("User ID: " . ($_SESSION['user_id'] ?? 'NOT SET'));
        error_log("Meal Plan ID: " . ($_GET['meal_plan_id'] ?? 'NOT SET'));
        
        if (!isset($_SESSION['user_id'])) {
            error_log("No user session, redirecting to login");
            header('Location: index.php?action=login');
            exit;
        }
        
        $mealPlanId = (int)($_GET['meal_plan_id'] ?? 0);
        
        if (!$mealPlanId) {
            error_log("Invalid meal plan ID");
            $_SESSION['flash_error'] = 'Invalid meal plan';
            header('Location: index.php?action=mealPlansList');
            exit;
        }
        
        error_log("Starting shop from meal plan process...");
        
        try {
            // STEP 1: Clear the existing cart to start fresh
            require_once __DIR__ . '/../models/ShoppingCartModel.php';
            $cartModel = new ShoppingCartModel($this->db);
            $cartModel->clearCart($_SESSION['user_id']);
            error_log("Cart cleared");
            
            // STEP 2: Clear/delete old grocery list items for fresh start
            $this->clearOldGroceryListItems($_SESSION['user_id']);
            error_log("Old grocery list items cleared");
            
            // STEP 3: Get what we need from meal plan (after checking pantry)
            $neededItems = $this->getNeededItemsFromMealPlan($mealPlanId, $_SESSION['user_id']);
            error_log("Needed items count: " . count($neededItems));
            
            if (empty($neededItems)) {
                error_log("No items needed");
                $_SESSION['flash_success'] = 'You have everything in your pantry! No need to shop.';
                header('Location: index.php?action=mealPlansList');
                exit;
            }
            
            // Auto-add all available items to cart
            $addedCount = $this->autoAddNeededItemsToCart($neededItems, $_SESSION['user_id']);
            error_log("Added $addedCount items to cart");
            
            if ($addedCount > 0) {
                // Check if grocery list was created for missing items
                if (isset($_SESSION['grocery_list_created'])) {
                    $listInfo = $_SESSION['grocery_list_created'];
                    $_SESSION['flash_success'] = "Added $addedCount items to your cart! {$listInfo['count']} item(s) not found online were added to your grocery list for local purchase.";
                    unset($_SESSION['grocery_list_created']); // Clear after use
                } else {
                    $_SESSION['flash_success'] = "Added $addedCount items to your cart from Supermarket (SRP) and Wet Market! Compare prices and choose what you prefer.";
                }
                header('Location: index.php?action=viewCart');
            } else {
                error_log("No items could be added to cart");
                
                // Check if items were added to grocery list instead
                if (isset($_SESSION['grocery_list_created'])) {
                    $listInfo = $_SESSION['grocery_list_created'];
                    $_SESSION['flash_warning'] = "No items available online. {$listInfo['count']} item(s) were added to your grocery list for local purchase.";
                    unset($_SESSION['grocery_list_created']);
                    header('Location: index.php?action=groceryLists');
                } else {
                    $_SESSION['flash_error'] = 'No items available online. Please check the wet market.';
                    header('Location: index.php?action=mealPlansList');
                }
            }
        } catch (Exception $e) {
            error_log("ERROR in shopFromMealPlan: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['flash_error'] = 'An error occurred while adding items to cart. Please try again.';
            header('Location: index.php?action=mealPlansList');
        }
        exit;
    }
    
    /**
     * Clear old "Items to Buy Locally" grocery list items for fresh start
     */
    private function clearOldGroceryListItems(int $userId): void {
        try {
            // Delete all items from active "Items to Buy Locally" lists
            $deleteStmt = $this->db->prepare("
                DELETE gli 
                FROM grocery_list_items gli
                INNER JOIN grocery_lists gl ON gli.grocery_list_id = gl.grocery_list_id
                WHERE gl.user_id = ? 
                  AND gl.status = 'Active'
                  AND gl.list_name LIKE 'Items to Buy Locally%'
            ");
            $deleteStmt->execute([$userId]);
            
            $deletedCount = $deleteStmt->rowCount();
            error_log("Cleared $deletedCount old grocery list items");
        } catch (Exception $e) {
            error_log("Error clearing old grocery list items: " . $e->getMessage());
        }
    }
    
    /**
     * Get items needed from meal plan (after checking pantry)
     * Returns simplified list of items with quantities
     */
    private function getNeededItemsFromMealPlan(int $mealPlanId, int $userId): array {
        error_log("=== GET NEEDED ITEMS FROM MEAL PLAN ===");
        error_log("Meal Plan ID: $mealPlanId");
        
        // Get meal plan
        $stmt = $this->db->prepare("SELECT * FROM meal_plans WHERE meal_plan_id = :meal_plan_id");
        $stmt->execute([':meal_plan_id' => $mealPlanId]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$plan) {
            error_log("Meal plan not found");
            return [];
        }
        
        error_log("Meal plan found: " . $plan['plan_name'] . " (Family ID: " . ($plan['family_id'] ?? 'NULL') . ")");
        
        // List of ingredients we don't need to buy (like water)
        $skipIngredients = ['water', 'tubig'];
        
        // Get meal plan items and extract ingredients
        $stmt = $this->db->prepare("SELECT ingredients FROM meal_plan_items WHERE meal_plan_id = :meal_plan_id");
        $stmt->execute([':meal_plan_id' => $mealPlanId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Meal plan items count: " . count($items));
        
        // Count ingredients
        $ingredientMap = [];
        foreach ($items as $item) {
            error_log("Processing meal item ingredients: " . ($item['ingredients'] ?? 'NULL'));
            
            if (empty($item['ingredients'])) continue;
            
            // First split on comma
            $ingredientParts = explode(',', $item['ingredients']);
            foreach ($ingredientParts as $ingredientPart) {
                $ingredientPart = trim($ingredientPart);
                if (empty($ingredientPart)) continue;
                
                // Now check if it contains "or" — split on that too
                if (stripos($ingredientPart, ' or ') !== false) {
                    $options = explode(' or ', $ingredientPart);
                    // For "or" options, we'll just pick the first one as the ingredient to look for
                    $ingredient = trim($options[0]);
                } else {
                    $ingredient = $ingredientPart;
                }
                
                if (empty($ingredient)) continue;
                
                // Skip ingredients we don't need to buy
                if (in_array(strtolower($ingredient), $skipIngredients)) continue;
                
                $key = strtolower($ingredient);
                if (!isset($ingredientMap[$key])) {
                    $ingredientMap[$key] = [
                        'name' => $ingredient,
                        'quantity' => 1,
                        'category' => 'Other',  // Added category
                        'unit' => 'pc'  // Added unit
                    ];
                } else {
                    $ingredientMap[$key]['quantity']++;
                }
            }
        }
        
        error_log("Total unique ingredients before pantry check: " . count($ingredientMap));
        
        // Check pantry and subtract what we have
        if ($plan['family_id']) {
            $pantryItems = $this->pantryModel->getPantryItems($plan['family_id']);
            error_log("Pantry items count: " . count($pantryItems));
            
            foreach ($pantryItems as $pItem) {
                $pantryItemLower = strtolower($pItem['item_name']);
                error_log("Checking pantry item: " . $pItem['item_name'] . " (qty: " . $pItem['quantity'] . ")");
                
                // Direct match check
                if (isset($ingredientMap[$pantryItemLower])) {
                    error_log("MATCH FOUND (direct): " . $pItem['item_name']);
                    $ingredientMap[$pantryItemLower]['quantity'] -= (int)$pItem['quantity'];
                    if ($ingredientMap[$pantryItemLower]['quantity'] <= 0) {
                        error_log("Removing from list - have enough in pantry");
                        unset($ingredientMap[$pantryItemLower]);
                    }
                    continue;
                }
                
                // Check aliases (e.g., "kamatis" = "tomato")
                $aliases = $this->model->getIngredientAliases($pItem['item_name']);
                foreach ($aliases as $alias) {
                    $aliasLower = strtolower($alias);
                    if (isset($ingredientMap[$aliasLower])) {
                        error_log("MATCH FOUND (alias): " . $pItem['item_name'] . " matches " . $alias);
                        $ingredientMap[$aliasLower]['quantity'] -= (int)$pItem['quantity'];
                        if ($ingredientMap[$aliasLower]['quantity'] <= 0) {
                            error_log("Removing from list - have enough in pantry");
                            unset($ingredientMap[$aliasLower]);
                        }
                        break;
                    }
                }
            }
        } else {
            error_log("No family_id set for this meal plan");
        }
        
        $result = array_values($ingredientMap);
        error_log("Final needed items count: " . count($result));
        
        return $result;
    }
    
    /**
     * Auto-add needed items to cart
     * Returns number of items successfully added
     */
    private function autoAddNeededItemsToCart(array $neededItems, int $userId): int {
        require_once __DIR__ . '/../models/ShoppingCartModel.php';
        $cartModel = new ShoppingCartModel($this->db);
        
        $addedCount = 0;
        $missingItems = []; // Track items not found online
        
        error_log("=== AUTO ADD TO CART ===");
        error_log("Needed items count: " . count($neededItems));
        
        foreach ($neededItems as $item) {
            error_log("Looking for product: " . $item['name']);
            
            // Find ALL matching products (both SRP and Wet Market)
            $products = $this->findAllMatchingProducts($item['name']);
            
            if (!empty($products)) {
                error_log("Found " . count($products) . " matching products for: " . $item['name']);
                
                // Add ALL matching products to cart
                foreach ($products as $product) {
                    error_log("Adding product: " . $product['product_name'] . " (ID: " . $product['product_id'] . ", Source: " . $product['source'] . ")");
                    
                    $cartData = [
                        'user_id' => $userId,
                        'product_id' => $product['product_id'],
                        'product_type' => $product['source'],
                        'product_name' => $product['product_name'],
                        'quantity' => $item['quantity'],
                        'unit' => $product['unit'],
                        'price_per_unit' => $product['price']
                    ];
                    
                    if ($cartModel->addToCart($cartData)) {
                        $addedCount++;
                        error_log("Added to cart successfully");
                    } else {
                        error_log("Failed to add to cart");
                    }
                }
            } else {
                error_log("No matching product found for: " . $item['name']);
                $missingItems[] = $item; // Track missing items
            }
        }
        
        error_log("Total added to cart: $addedCount");
        
        // NEW: Add missing items to grocery list (safe addition, doesn't break existing code)
        if (!empty($missingItems)) {
            $this->addMissingItemsToGroceryList($missingItems, $userId);
        }
        
        return $addedCount;
    }
    
    /**
     * Add items not found online to grocery list
     * Creates/reuses today's "Items to Buy Locally" list
     */
    private function addMissingItemsToGroceryList(array $missingItems, int $userId): void {
        try {
            error_log("=== ADDING MISSING ITEMS TO GROCERY LIST ===");
            
            // Check if there's already an active grocery list for today
            $today = date('Y-m-d');
            $checkStmt = $this->db->prepare("
                SELECT grocery_list_id 
                FROM grocery_lists 
                WHERE user_id = ? 
                  AND list_date = ? 
                  AND status = 'Active'
                  AND list_name LIKE 'Items to Buy Locally%'
                ORDER BY created_date DESC 
                LIMIT 1
            ");
            $checkStmt->execute([$userId, $today]);
            $existingList = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingList) {
                // Use existing list (items were already cleared earlier)
                $groceryListId = $existingList['grocery_list_id'];
                error_log("Using existing grocery list ID: $groceryListId");
            } else {
                // Create a new grocery list
                $groceryListData = [
                    'user_id' => $userId,
                    'list_name' => 'Items to Buy Locally - ' . date('M d, Y'),
                    'list_date' => $today,
                    'status' => 'Active',
                    'notes' => 'These items were not found online. Please buy them at your local market.'
                ];
                
                $groceryListId = $this->model->createGroceryList($groceryListData);
                error_log("Created new grocery list ID: $groceryListId");
            }
            
            // Add each missing item to the grocery list (no duplicate check needed since we cleared earlier)
            $addedToListCount = 0;
            foreach ($missingItems as $item) {
                $itemData = [
                    'grocery_list_id' => $groceryListId,
                    'product_name' => $item['name'],
                    'category' => $item['category'] ?? 'Other',
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? 'pc',
                    'estimated_price' => 0.00,
                    'notes' => null
                ];
                
                if ($this->model->addGroceryListItem($itemData)) {
                    $addedToListCount++;
                    error_log("Added to grocery list: " . $item['name']);
                }
            }
            
            // Store info in session for display
            $_SESSION['grocery_list_created'] = [
                'id' => $groceryListId,
                'count' => $addedToListCount
            ];
            
            error_log("Added $addedToListCount items to grocery list");
            
        } catch (Exception $e) {
            error_log("ERROR adding missing items to grocery list: " . $e->getMessage());
            // Don't throw - this is optional functionality
        }
    }
    
    /**
     * Find best matching product from supermarket OR wet market
     */
    private function findBestMatchingProduct(string $itemName): ?array {
        // This method is deprecated - use findAllMatchingProducts instead
        $matches = $this->findAllMatchingProducts($itemName);
        return !empty($matches) ? $matches[0] : null;
    }
    
    /**
     * Find CHEAPEST matching product from both SRP and Wet Market
     * Returns only the lowest-priced option to avoid duplicate items in cart
     */
    private function findAllMatchingProducts(string $itemName): array {
        error_log("=== FINDING MATCH FOR: $itemName ===");
        
        // Get all products (supermarket + wet market)
        $supermarketProducts = $this->model->getSupermarketProducts();
        error_log("Supermarket products count: " . count($supermarketProducts));
        
        // Get vendor products from wet market
        $vendorProducts = $this->vendorModel->getAllAvailableProducts();
        error_log("Vendor products count: " . count($vendorProducts));
        
        // Format vendor products to match the structure we need
        $formattedVendorProducts = array_map(function($product) {
            return [
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'category' => $product['category'],
                'unit' => $product['unit'],
                'price' => $product['price_per_unit'],
                'source' => 'vendor'
            ];
        }, $vendorProducts);
        
        // Combine all products
        $allProducts = array_merge($supermarketProducts, $formattedVendorProducts);
        error_log("Total products to search: " . count($allProducts));
        
        $itemNameLower = strtolower($itemName);
        error_log("Item name (lowercase): $itemNameLower");
        
        // Get all possible aliases for this ingredient
        $aliases = $this->model->getIngredientAliases($itemName);
        $aliasLowers = array_map('strtolower', $aliases);
        error_log("Aliases found: " . implode(', ', $aliasLowers));
        
        $matchedProducts = [];
        
        // STEP 1: Try exact match with any alias first (EXISTING BEHAVIOR - preserved!)
        error_log("STEP 1: Trying exact alias match...");
        foreach ($allProducts as $product) {
            $productNameLower = strtolower($product['product_name']);
            if (in_array($productNameLower, $aliasLowers)) {
                error_log("  MATCH FOUND (exact): {$product['product_name']} matches alias");
                $matchedProducts[] = $product;
            }
        }
        error_log("STEP 1 results: " . count($matchedProducts) . " matches");
        
        // STEP 2: If no exact matches, try base name matching (NEW - handles variants)
        // This matches "Carrots" with "Carrots (big)" without breaking existing exact matches
        if (empty($matchedProducts)) {
            error_log("STEP 2: Trying base name matching...");
            foreach ($allProducts as $product) {
                $productNameLower = strtolower($product['product_name']);
                
                // Extract base name by removing anything in parentheses
                // "Carrots (big)" becomes "Carrots"
                $productBaseName = trim(preg_replace('/\s*\([^)]*\)/', '', $productNameLower));
                
                foreach ($aliasLowers as $aliasLower) {
                    $aliasBaseName = trim(preg_replace('/\s*\([^)]*\)/', '', $aliasLower));
                    
                    // Try exact match on base names
                    if ($productBaseName === $aliasBaseName) {
                        error_log("  MATCH FOUND (base): {$product['product_name']} (base: $productBaseName) matches alias base: $aliasBaseName");
                        $matchedProducts[] = $product;
                        break; // Only add once per product
                    }
                }
            }
            error_log("STEP 2 results: " . count($matchedProducts) . " matches");
        }
        
        // STEP 3: If still no matches, try word-boundary match (EXISTING BEHAVIOR - preserved!)
        if (empty($matchedProducts)) {
            error_log("STEP 3: Trying word-boundary matching...");
            foreach ($allProducts as $product) {
                $productNameLower = strtolower($product['product_name']);
                foreach ($aliasLowers as $aliasLower) {
                    // Only try if alias is at least 4 characters long
                    if (strlen($aliasLower) < 4) continue;
                    
                    // Check for word boundary match
                    $pattern = '/\b' . preg_quote($aliasLower, '/') . '\b/i';
                    if (preg_match($pattern, $productNameLower)) {
                        error_log("  MATCH FOUND (word boundary): {$product['product_name']} matches pattern: $pattern");
                        $matchedProducts[] = $product;
                        break; // Only add once per product
                    }
                }
            }
            error_log("STEP 3 results: " . count($matchedProducts) . " matches");
        }
        
        // Return only the CHEAPEST product to avoid duplicates
        if (empty($matchedProducts)) {
            error_log("NO MATCHES FOUND for: $itemName");
            
            // DEBUG: Show some sample product names that might be close
            error_log("Sample products in database:");
            $sampleCount = 0;
            foreach ($allProducts as $product) {
                if (stripos($product['product_name'], 'kala') !== false || 
                    stripos($product['product_name'], 'squash') !== false ||
                    stripos($product['product_name'], 'pump') !== false) {
                    error_log("  - {$product['product_name']} (source: {$product['source']}, available: " . ($product['is_available'] ?? 'N/A') . ")");
                    $sampleCount++;
                }
            }
            if ($sampleCount === 0) {
                error_log("  (No products found with 'kala', 'squash', or 'pump' in name)");
            }
            
            return [];
        }
        
        error_log("Total matches before price filter: " . count($matchedProducts));
        
        // Sort by price (ascending) and return only the cheapest one
        usort($matchedProducts, function($a, $b) {
            return $a['price'] <=> $b['price'];
        });
        
        error_log("CHEAPEST MATCH: {$matchedProducts[0]['product_name']} (₱{$matchedProducts[0]['price']}, source: {$matchedProducts[0]['source']})");
        
        // Return only the first (cheapest) product as an array
        return [$matchedProducts[0]];
    }
    
    /**
     * ✨ PHASE 2: Shop Grocery List Online
     * Pre-filters supermarket to show only items from grocery list
     * Includes "Quick Add All to Cart" functionality
     */
    public function shopGroceryListOnline(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $groceryListId = (int)($_GET['grocery_list_id'] ?? 0);
        
        if (!$groceryListId) {
            $_SESSION['flash_error'] = 'Invalid grocery list';
            header('Location: index.php?action=groceryLists');
            exit;
        }
        
        // Get grocery list details
        $groceryList = $this->model->getGroceryListById($groceryListId);
        
        if (!$groceryList || $groceryList['user_id'] != $_SESSION['user_id']) {
            $_SESSION['flash_error'] = 'Grocery list not found';
            header('Location: index.php?action=groceryLists');
            exit;
        }
        
        // Get grocery list items
        $groceryItems = $this->model->getGroceryListItems($groceryListId);
        
        // Get ALL supermarket products
        $products = $this->model->getSupermarketProducts();
        
        // Match grocery items with supermarket products
        $matchedProducts = [];
        $unmatchedItems = [];
        
        foreach ($groceryItems as $gItem) {
            if ($gItem['is_purchased']) continue; // Skip already purchased
            
            $found = false;
            $itemName = strtolower($gItem['product_name']);
            
            // Try to find matching product in supermarket
            foreach ($products as $product) {
                $productName = strtolower($product['product_name']);
                
                // Fuzzy match: check if names contain each other
                if (str_contains($productName, $itemName) || str_contains($itemName, $productName)) {
                    $matchedProducts[] = [
                        'product' => $product,
                        'grocery_item' => $gItem,
                        'needed_quantity' => $gItem['quantity']
                    ];
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $unmatchedItems[] = $gItem;
            }
        }
        
        // Get categories for filter
        $categories = [
            'Vegetables', 'Fruits', 'Canned Goods', 'Rice', 'Grains', 
            'Snacks', 'Dairy', 'Condiments', 'Beverages', 'Protein', 
            'Spices', 'Rootcrops', 'Instant Food'
        ];
        
        include __DIR__ . '/../views/mother/shop_grocery_list.php';
    }

    /**
     * Show grocery lists
     */
    public function showGroceryLists(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $groceryLists = $this->model->getGroceryListsByUser($_SESSION['user_id']);
        
        include __DIR__ . '/../views/mother/grocery_lists.php';
    }

    /**
     * Show grocery list form (create/edit)
     */
    public function showGroceryListForm(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $groceryList = null;
        $items = [];
        
        if (!empty($_GET['id'])) {
            $groceryList = $this->model->getGroceryListById((int)$_GET['id']);
            if ($groceryList && $groceryList['user_id'] != $_SESSION['user_id']) {
                $_SESSION['flash'] = 'Unauthorized access.';
                header('Location: index.php?action=groceryLists');
                exit;
            }
            if ($groceryList) {
                $items = $this->model->getGroceryListItems((int)$_GET['id']);
            }
        }

        // Get meal plans for generation option
        $mealPlans = $this->mealPlanModel->getMealPlansByUser($_SESSION['user_id']);
        
        include __DIR__ . '/../views/mother/grocery_list_form.php';
    }

    /**
     * Save grocery list
     */
    public function saveGroceryList(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: index.php?action=groceryLists');
            exit;
        }

        try {
            $data = [
                'user_id'              => $_SESSION['user_id'],
                'family_id'            => $_POST['family_id'] ?? null,
                'meal_plan_id'         => $_POST['meal_plan_id'] ?? null,
                'list_name'            => trim($_POST['list_name']),
                'list_date'            => $_POST['list_date'] ?? date('Y-m-d'),
                'total_estimated_cost' => (float)($_POST['total_estimated_cost'] ?? 0),
                'status'               => $_POST['status'] ?? 'Active',
                'notes'                => trim($_POST['notes'] ?? ''),
            ];

            if (empty($data['list_name'])) {
                throw new Exception('List name is required.');
            }

            if (!empty($_POST['grocery_list_id'])) {
                // Update
                $groceryListId = (int)$_POST['grocery_list_id'];
                $this->model->updateGroceryList($groceryListId, $data);
                $_SESSION['flash'] = 'Grocery list updated successfully!';
            } else {
                // Create
                $groceryListId = $this->model->createGroceryList($data);
                $_SESSION['flash'] = 'Grocery list created successfully!';
            }

            header('Location: index.php?action=groceryListForm&id=' . $groceryListId);
            exit;
        } catch (Exception $e) {
            $_SESSION['flash'] = 'Error: ' . $e->getMessage();
            header('Location: index.php?action=groceryListForm');
            exit;
        }
    }

    /**
     * Generate grocery list from meal plan
     */
    public function generateFromMealPlan(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        // Accept both GET and POST
        $mealPlanId = (int)($_POST['meal_plan_id'] ?? $_GET['meal_plan_id'] ?? 0);
        
        if (!$mealPlanId) {
            $_SESSION['flash_error'] = 'Please select a meal plan.';
            header('Location: index.php?action=mealPlansList');
            exit;
        }

        $groceryListId = $this->model->generateFromMealPlan($mealPlanId, $_SESSION['user_id']);
        
        if ($groceryListId) {
            $_SESSION['flash_success'] = 'Grocery list generated from meal plan successfully! Items checked against your pantry.';
            header('Location: index.php?action=groceryListForm&id=' . $groceryListId);
        } else {
            $_SESSION['flash_error'] = 'Error generating grocery list.';
            header('Location: index.php?action=mealPlansList');
        }
        exit;
    }

    /**
     * Add item to grocery list
     */
    public function addGroceryItem(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        try {
            $data = [
                'grocery_list_id' => (int)$_POST['grocery_list_id'],
                'product_name'    => trim($_POST['product_name']),
                'category'        => trim($_POST['category'] ?? ''),
                'quantity'        => (float)$_POST['quantity'],
                'unit'            => trim($_POST['unit']),
                'estimated_price' => !empty($_POST['estimated_price']) ? (float)$_POST['estimated_price'] : null,
                'notes'           => trim($_POST['notes'] ?? ''),
            ];

            $itemId = $this->model->addGroceryListItem($data);
            
            // Auto-lookup SRP price if not provided
            if (empty($data['estimated_price'])) {
                $srpPrice = $this->model->getSRPPrice($data['product_name']);
                if ($srpPrice) {
                    $this->model->updateGroceryListItem($itemId, ['estimated_price' => $srpPrice]);
                }
            }

            // Recalculate total cost
            $this->model->recalculateTotalCost($data['grocery_list_id']);
            
            echo json_encode(['success' => true, 'item_id' => $itemId]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Update grocery item
     */
    public function updateGroceryItem(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        try {
            $itemId = (int)$_POST['item_id'];
            $data = [
                'product_name'    => trim($_POST['product_name']),
                'category'        => trim($_POST['category'] ?? ''),
                'quantity'        => (float)$_POST['quantity'],
                'unit'            => trim($_POST['unit']),
                'estimated_price' => !empty($_POST['estimated_price']) ? (float)$_POST['estimated_price'] : null,
                'notes'           => trim($_POST['notes'] ?? ''),
            ];

            $success = $this->model->updateGroceryListItem($itemId, $data);
            
            // Recalculate total cost
            if ($success && !empty($_POST['grocery_list_id'])) {
                $this->model->recalculateTotalCost((int)$_POST['grocery_list_id']);
            }
            
            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Mark item as purchased
     */
    public function markItemPurchased(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        $itemId = (int)($_POST['item_id'] ?? 0);
        $data = [
            'vendor_id'    => !empty($_POST['vendor_id']) ? (int)$_POST['vendor_id'] : null,
            'actual_price' => !empty($_POST['actual_price']) ? (float)$_POST['actual_price'] : null,
        ];

        // Get item details for pantry replenishment
        $item = $this->model->getGroceryListItemById($itemId);
        $groceryList = $item ? $this->model->getGroceryListById($item['grocery_list_id']) : null;

        $success = $this->model->markItemAsPurchased($itemId, $data);

        if ($success && $item && $groceryList) {
            // Automatically replenish pantry
            $this->pantryModel->replenishItem([
                'family_id'    => $groceryList['family_id'],
                'user_id'      => $_SESSION['user_id'],
                'item_name'    => $item['product_name'],
                'category'     => $item['category'],
                'quantity'     => $item['quantity'],
                'unit'         => $item['unit'],
                'reference_id' => $itemId,
                'notes'        => 'Purchased from grocery list: ' . $groceryList['list_name']
            ]);
        }

        echo json_encode(['success' => $success]);
        exit;
    }

    /**
     * Unmark item as purchased
     */
    public function unmarkItemPurchased(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        $itemId = (int)($_POST['item_id'] ?? 0);
        $success = $this->model->unmarkItemAsPurchased($itemId);
        echo json_encode(['success' => $success]);
        exit;
    }

    /**
     * Delete grocery item
     */
    public function deleteGroceryItem(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            // Handle AJAX requests
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(['success' => false]);
                exit;
            }
            // Handle form submission
            header('Location: index.php?action=groceryLists');
            exit;
        }

        $itemId = (int)($_POST['item_id'] ?? 0);
        $groceryListId = (int)($_POST['grocery_list_id'] ?? 0);
        
        $success = $this->model->deleteGroceryListItem($itemId);
        
        // Recalculate total cost
        if ($success && $groceryListId) {
            $this->model->recalculateTotalCost($groceryListId);
        }
        
        // Handle AJAX requests
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo json_encode(['success' => $success]);
            exit;
        }
        
        // Handle form submission - redirect back
        if ($success) {
            $_SESSION['flash_success'] = 'Item deleted successfully.';
        } else {
            $_SESSION['flash_error'] = 'Failed to delete item.';
        }
        header('Location: index.php?action=groceryLists');
        exit;
    }

    /**
     * Delete grocery list
     */
    public function deleteGroceryList(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: index.php?action=groceryLists');
            exit;
        }

        $groceryListId = (int)($_POST['grocery_list_id'] ?? 0);
        $this->model->deleteGroceryList($groceryListId);
        
        $_SESSION['flash'] = 'Grocery list deleted successfully.';
        header('Location: index.php?action=groceryLists');
        exit;
    }

    /**
     * Mark grocery list as completed
     */
    public function markListCompleted(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: index.php?action=groceryLists');
            exit;
        }

        $groceryListId = (int)($_POST['grocery_list_id'] ?? 0);
        $this->model->markAsCompleted($groceryListId);
        
        $_SESSION['flash'] = 'Grocery list marked as completed!';
        header('Location: index.php?action=groceryLists');
        exit;
    }
}
