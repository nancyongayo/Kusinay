<?php
/**
 * ShoppingCartController
 * Handles shopping cart operations and checkout
 */

require_once __DIR__ . '/../models/ShoppingCartModel.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/PayMongoCheckout.php';
require_once __DIR__ . '/../models/PantryModel.php';
require_once __DIR__ . '/../models/GroceryListModel.php';

class ShoppingCartController {
    private PDO $db;
    private ShoppingCartModel $cartModel;
    private OrderModel $orderModel;
    private PayMongoCheckout $paymongo;
    private PantryModel $pantryModel;
    private GroceryListModel $groceryListModel;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->cartModel = new ShoppingCartModel($db);
        $this->orderModel = new OrderModel($db);
        $this->paymongo = new PayMongoCheckout();
        $this->pantryModel = new PantryModel($db);
        $this->groceryListModel = new GroceryListModel($db);
    }

    /**
     * Add item to cart (AJAX)
     */
    public function addToCart(): void {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Please login first']);
            exit;
        }

        $data = [
            'user_id' => $_SESSION['user_id'],
            'product_id' => (int)$_POST['product_id'],
            'product_type' => $_POST['product_type'] ?? 'srp',
            'product_name' => $_POST['product_name'],
            'quantity' => (float)$_POST['quantity'],
            'unit' => $_POST['unit'],
            'price_per_unit' => (float)$_POST['price']
        ];

        $success = $this->cartModel->addToCart($data);
        
        if ($success) {
            $cartCount = $this->cartModel->getCartCount($_SESSION['user_id']);
            echo json_encode([
                'success' => true,
                'message' => 'Added to cart!',
                'cart_count' => $cartCount
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add to cart']);
        }
        exit;
    }

    /**
     * View shopping cart
     */
    public function viewCart(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $cartItems = $this->cartModel->getCartItems($_SESSION['user_id']);
        $cartSummary = $this->cartModel->getCartSummary($_SESSION['user_id']);
        
        include __DIR__ . '/../views/mother/shopping_cart.php';
    }

    /**
     * Update cart item quantity (AJAX)
     */
    public function updateCartQuantity(): void {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        $cartId = (int)$_POST['cart_id'];
        $quantity = (float)$_POST['quantity'];
        
        $success = $this->cartModel->updateQuantity($cartId, $quantity);
        
        if ($success) {
            $cartSummary = $this->cartModel->getCartSummary($_SESSION['user_id']);
            echo json_encode([
                'success' => true,
                'cart_summary' => $cartSummary
            ]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    /**
     * Remove item from cart (AJAX)
     */
    public function removeCartItem(): void {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        $cartId = (int)$_POST['cart_id'];
        $success = $this->cartModel->removeItem($cartId);
        
        echo json_encode(['success' => $success]);
        exit;
    }

    /**
     * Show checkout page
     */
    public function showCheckout(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $cartItems = $this->cartModel->getCartItems($_SESSION['user_id']);
        $cartSummary = $this->cartModel->getCartSummary($_SESSION['user_id']);
        
        if (empty($cartItems)) {
            $_SESSION['flash_error'] = 'Your cart is empty';
            header('Location: index.php?action=supermarket');
            exit;
        }

        include __DIR__ . '/../views/mother/checkout.php';
    }


    /**
     * Process checkout
     */
    public function processCheckout(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: index.php?action=viewCart');
            exit;
        }

        $cartSummary = $this->cartModel->getCartSummary($_SESSION['user_id']);
        
        if ($cartSummary['total_items'] == 0) {
            $_SESSION['flash_error'] = 'Your cart is empty';
            header('Location: index.php?action=supermarket');
            exit;
        }

        // Get fulfillment method
        $fulfillmentMethod = $_POST['fulfillment_method'] ?? 'pickup';
        
        // Calculate delivery fee based on fulfillment method
        $freeDeliveryThreshold = 500.00;
        $subtotal = $cartSummary['total_amount'];
        
        if ($fulfillmentMethod === 'pickup') {
            $deliveryFee = 0.00; // Pickup is always free
        } else {
            $deliveryFee = ($subtotal >= $freeDeliveryThreshold) ? 0.00 : 50.00;
        }
        
        $grandTotal = $subtotal + $deliveryFee;

        // Create order
        $orderData = [
            'user_id' => $_SESSION['user_id'],
            'total_amount' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'grand_total' => $grandTotal,
            'fulfillment_method' => $fulfillmentMethod,
            'payment_method' => $_POST['payment_method'] ?? 'paymongo',
            'delivery_address' => $_POST['delivery_address'] ?? null,
            'contact_number' => $_POST['contact_number'] ?? '',
            'notes' => $_POST['notes'] ?? ''
        ];

        $orderId = $this->orderModel->createOrder($orderData);
        
        if (!$orderId) {
            $_SESSION['flash_error'] = 'Failed to create order';
            header('Location: index.php?action=showCheckout');
            exit;
        }

        // Get order details
        $order = $this->orderModel->getOrderById($orderId);

        // If payment method is PayMongo, create checkout session
        if ($orderData['payment_method'] === 'paymongo') {
            $this->createPayMongoCheckout($order);
        } else {
            // COD or other payment methods
            $_SESSION['flash_success'] = 'Order placed successfully!';
            header('Location: index.php?action=orderConfirmation&order_id=' . $orderId);
        }
        exit;
    }

    /**
     * Create PayMongo checkout session
     */
    private function createPayMongoCheckout(array $order): void {
        // Prepare line items for PayMongo
        $lineItems = [];
        foreach ($order['items'] as $item) {
            $lineItems[] = [
                'name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'price' => $item['price_per_unit']
            ];
        }

        // Add delivery fee if applicable
        if ($order['delivery_fee'] > 0) {
            $lineItems[] = [
                'name' => 'Delivery Fee',
                'quantity' => 1,
                'price' => $order['delivery_fee']
            ];
        }

        $checkoutData = [
            'items' => $lineItems,
            'order_number' => $order['order_number'],
            'user_id' => $order['user_id'],
            'success_url' => 'https://kusinayapp.freehosting.dev/index.php?action=paymentSuccess&order_id=' . $order['order_id'],
            'cancel_url' => 'https://kusinayapp.freehosting.dev/index.php?action=paymentCancelled&order_id=' . $order['order_id']
        ];

        $response = $this->paymongo->createCheckoutSession($checkoutData);

        if ($response && isset($response['data']['attributes']['checkout_url'])) {
            // Update order with PayMongo details
            $this->orderModel->updatePayMongoDetails($order['order_id'], [
                'payment_intent_id' => $response['data']['id'],
                'checkout_url' => $response['data']['attributes']['checkout_url']
            ]);

            // Redirect to PayMongo checkout
            header('Location: ' . $response['data']['attributes']['checkout_url']);
        } else {
            $_SESSION['flash_error'] = 'Payment gateway error. Please try again.';
            header('Location: index.php?action=showCheckout');
        }
        exit;
    }

    /**
     * Payment success callback
     */
    public function paymentSuccess(): void {
        if (!isset($_GET['order_id'])) {
            header('Location: index.php?action=myOrders');
            exit;
        }

        $orderId = (int)$_GET['order_id'];
        $order = $this->orderModel->getOrderById($orderId);
        
        if (!$order) {
            $_SESSION['flash_error'] = 'Order not found.';
            header('Location: index.php?action=myOrders');
            exit;
        }
        
        // Update payment status to paid
        $this->orderModel->updatePaymentStatus($orderId, 'paid');
        
        // Update order status to confirmed (payment received)
        $this->orderModel->updateOrderStatus($orderId, 'confirmed');
        
        // ✅ Record vendor sales for analytics
        $this->recordVendorSales($orderId);
        
        // Auto-add purchased items to user's pantry
        $this->addOrderItemsToPantry($orderId);
        
        // ✨ PHASE 3: Auto-mark grocery list items as purchased
        $this->markGroceryListItemsAsPurchased($orderId);
        
        // ✅ ENSURE cart is cleared (safety check)
        $this->cartModel->clearCart($order['user_id']);
        
        $_SESSION['flash_success'] = 'Payment successful! Your order has been confirmed and items added to your pantry.';
        header('Location: index.php?action=orderConfirmation&order_id=' . $orderId);
        exit;
    }
    
    /**
     * Add order items to user's pantry automatically
     */
    private function addOrderItemsToPantry(int $orderId): void {
        try {
            // Get order details
            $order = $this->orderModel->getOrderById($orderId);
            
            if (!$order || empty($order['items'])) {
                return;
            }
            
            $userId = $order['user_id'];
            
            // Get user's family_id from family_profiles
            $stmt = $this->db->prepare("
                SELECT family_id FROM family_profiles 
                WHERE source_user_id = :user_id 
                LIMIT 1
            ");
            $stmt->execute([':user_id' => $userId]);
            $familyRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$familyRow) {
                error_log("No family found for user_id: $userId. Skipping pantry update.");
                return;
            }
            
            $familyId = $familyRow['family_id'];
            
            // Add each order item to pantry
            foreach ($order['items'] as $item) {
                $pantryData = [
                    'family_id' => $familyId,
                    'user_id' => $userId,
                    'item_name' => $item['product_name'],
                    'category' => $this->getCategoryFromProductName($item['product_name']),
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'reference_id' => $orderId,
                    'notes' => 'Auto-added from order #' . $order['order_number']
                ];
                
                $this->pantryModel->replenishItem($pantryData);
            }
            
            error_log("Successfully added " . count($order['items']) . " items to pantry for user_id: $userId");
            
        } catch (Exception $e) {
            error_log("Error adding items to pantry: " . $e->getMessage());
            // Don't fail the payment success - just log the error
        }
    }
    
    /**
     * Determine category from product name (basic logic)
     */
    private function getCategoryFromProductName(string $productName): string {
        $name = strtolower($productName);
        
        // Spices & Seasonings (check first before vegetables)
        $spices = [
            'pepper', 'paminta', 'black pepper', 'white pepper',
            'chili', 'sili', 'chili pepper', 'chilli',
            'ginger', 'luya',
            'garlic', 'bawang', 'ahos',
            'onion', 'sibuyas', 'bombay',
            'turmeric', 'luyang dilaw',
            'cinnamon', 'kanela',
            'bay leaf', 'laurel', 'dahon ng laurel',
            'star anise', 'anis',
            'cloves', 'klabo',
            'cumin', 'komino',
            'coriander', 'wansoy', 'cilantro',
            'oregano', 'oregano',
            'basil', 'balanoy',
            'thyme', 'tomilyo',
            'rosemary', 'rosemaryo',
            'paprika', 'paprika',
            'curry powder', 'curry',
            'cayenne', 'cayenne',
            'annatto', 'atsuete', 'achuete'
        ];
        
        foreach ($spices as $spice) {
            if (str_contains($name, $spice)) {
                return 'Spices';
            }
        }
        
        // Vegetables - expanded list (removed garlic, onion, chili since they're now in Spices)
        $vegetables = [
            'ampalaya', 'bitter gourd', 'bitter melon',
            'kalabasa', 'squash', 'pumpkin',
            'talong', 'eggplant', 'aubergine',
            'cauliflower', 'koliplor',
            'cabbage', 'repolyo',
            'okra', 'okr',
            'sitaw', 'string beans', 'yard long beans',
            'bataw', 'hyacinth bean',
            'kangkong', 'water spinach',
            'pechay', 'bok choy',
            'lettuce', 'letsugas',
            'carrot', 'karot',
            'radish', 'labanos',
            'potato', 'patatas',
            'kamote', 'sweet potato',
            'gabi', 'taro',
            'tomato', 'kamatis',
            'cucumber', 'pipino',
            'upo', 'bottle gourd',
            'patola', 'luffa', 'loofah',
            'sayote', 'chayote'
        ];
        
        foreach ($vegetables as $veg) {
            if (str_contains($name, $veg)) {
                return 'Vegetables';
            }
        }
        
        // Fruits - expanded list
        $fruits = [
            'mango', 'manga', 'mangga',
            'banana', 'saging',
            'apple', 'mansanas',
            'orange', 'dalandan',
            'calamansi', 'kalamansi',
            'papaya', 'papaya',
            'pineapple', 'pinya',
            'watermelon', 'pakwan',
            'melon', 'melon',
            'guava', 'bayabas',
            'durian', 'durian',
            'lanzones', 'lanzones',
            'rambutan', 'rambutan',
            'avocado', 'abokado',
            'santol', 'santol',
            'dragon fruit', 'dragonfruit'
        ];
        
        foreach ($fruits as $fruit) {
            if (str_contains($name, $fruit)) {
                return 'Fruits';
            }
        }
        
        // Grains
        if (str_contains($name, 'rice') || str_contains($name, 'bigas') || str_contains($name, 'malagkit')) {
            return 'Grains';
        }
        
        // Protein
        if (str_contains($name, 'egg') || str_contains($name, 'itlog') ||
            str_contains($name, 'chicken') || str_contains($name, 'manok') ||
            str_contains($name, 'pork') || str_contains($name, 'baboy') ||
            str_contains($name, 'beef') || str_contains($name, 'baka') ||
            str_contains($name, 'fish') || str_contains($name, 'isda')) {
            return 'Protein';
        }
        
        // Dairy
        if (str_contains($name, 'milk') || str_contains($name, 'gatas') ||
            str_contains($name, 'cheese') || str_contains($name, 'keso')) {
            return 'Dairy';
        }
        
        // Canned Goods
        if (str_contains($name, 'sardine') || str_contains($name, 'corned') || 
            str_contains($name, 'canned') || str_contains($name, 'lata')) {
            return 'Canned Goods';
        }
        
        // Condiments & Sauces
        if (str_contains($name, 'oil') || str_contains($name, 'langis') ||
            str_contains($name, 'soy sauce') || str_contains($name, 'toyo') ||
            str_contains($name, 'vinegar') || str_contains($name, 'suka') ||
            str_contains($name, 'sugar') || str_contains($name, 'asukal') ||
            str_contains($name, 'salt') || str_contains($name, 'asin') ||
            str_contains($name, 'sauce') || str_contains($name, 'sarsa') ||
            str_contains($name, 'patis') || str_contains($name, 'fish sauce') ||
            str_contains($name, 'bagoong')) {
            return 'Condiments';
        }
        
        // Beverages
        if (str_contains($name, 'coffee') || str_contains($name, 'kape') ||
            str_contains($name, 'juice') || str_contains($name, 'juice') ||
            str_contains($name, 'tea') || str_contains($name, 'tsaa')) {
            return 'Beverages';
        }
        
        // Instant Food & Noodles
        if (str_contains($name, 'noodle') || str_contains($name, 'pancit') ||
            str_contains($name, 'instant') || str_contains($name, 'bihon') ||
            str_contains($name, 'canton') || str_contains($name, 'sotanghon')) {
            return 'Instant Food';
        }
        
        return 'Other';
    }
    
    /**
     * ✨ PHASE 3: Mark grocery list items as purchased after online order
     * Completes the loop: Meal Plan → Grocery List → Shopping → Pantry → Mark Purchased
     */
    private function markGroceryListItemsAsPurchased(int $orderId): void {
        try {
            // Get order details
            $order = $this->orderModel->getOrderById($orderId);
            
            if (!$order || empty($order['items'])) {
                return;
            }
            
            $userId = $order['user_id'];
            
            // Mark items in grocery list as purchased
            $markedCount = $this->groceryListModel->markItemsPurchasedFromOrder(
                $userId, 
                $order['items'], 
                $orderId
            );
            
            if ($markedCount > 0) {
                error_log("Successfully marked $markedCount grocery list items as purchased for order #$orderId");
            }
            
        } catch (Exception $e) {
            error_log("Error marking grocery list items as purchased: " . $e->getMessage());
            // Don't fail the payment success - just log the error
        }
    }

    /**
     * Payment cancelled callback
     */
    public function paymentCancelled(): void {
        if (!isset($_GET['order_id'])) {
            header('Location: index.php?action=viewCart');
            exit;
        }

        $orderId = (int)$_GET['order_id'];
        $_SESSION['flash_error'] = 'Payment was cancelled. Your order is still pending.';
        header('Location: index.php?action=orderConfirmation&order_id=' . $orderId);
        exit;
    }

    /**
     * Order confirmation page
     */
    public function orderConfirmation(): void {
        if (!isset($_GET['order_id']) || !isset($_SESSION['user_id'])) {
            header('Location: index.php?action=myOrders');
            exit;
        }

        $order = $this->orderModel->getOrderById((int)$_GET['order_id']);
        
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            header('Location: index.php?action=myOrders');
            exit;
        }

        include __DIR__ . '/../views/mother/order_confirmation.php';
    }

    /**
     * My orders page
     */
    public function myOrders(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $orders = $this->orderModel->getUserOrders($_SESSION['user_id']);
        include __DIR__ . '/../views/mother/my_orders.php';
    }

    /**
     * Get cart count (for badge)
     */
    public function getCartCount(): void {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['count' => 0]);
            exit;
        }

        $count = $this->cartModel->getCartCount($_SESSION['user_id']);
        echo json_encode(['count' => $count]);
        exit;
    }

    /**
     * ✅ Record vendor sales when order is paid
     * This populates the product_sales table for vendor analytics
     */
    private function recordVendorSales(int $orderId): void {
        try {
            require_once __DIR__ . '/../models/MarketVendorModel.php';
            $vendorModel = new MarketVendorModel($this->db);
            
            // Get order details
            $order = $this->orderModel->getOrderById($orderId);
            
            if (!$order || empty($order['items'])) {
                return;
            }
            
            $buyerUserId = $order['user_id'];
            
            // Record each vendor product sale
            foreach ($order['items'] as $item) {
                // Check if this is a vendor product
                if ($item['product_type'] === 'vendor' && !empty($item['product_id'])) {
                    // Get vendor details
                    $product = $vendorModel->getProductById($item['product_id']);
                    
                    if ($product) {
                        $saleData = [
                            'product_id' => $item['product_id'],
                            'vendor_user_id' => $product['vendor_user_id'],
                            'buyer_user_id' => $buyerUserId,
                            'quantity_sold' => $item['quantity'],
                            'unit' => $item['unit'],
                            'price_per_unit' => $item['price_per_unit'],
                            'total_amount' => $item['subtotal'],
                            'payment_method' => $order['payment_method'],
                            'notes' => 'Sale from order #' . $order['order_number']
                        ];
                        
                        $vendorModel->recordSale($saleData);
                        
                        error_log("Recorded sale for vendor product: {$item['product_name']}");
                    }
                }
            }
            
        } catch (Exception $e) {
            error_log("Error recording vendor sales: " . $e->getMessage());
            // Don't fail payment success - just log the error
        }
    }
}
