<?php
/**
 * ShoppingCartModel
 * Handles shopping cart operations for online grocery purchases
 */

class ShoppingCartModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Add item to cart
     */
    public function addToCart(array $data): bool {
        try {
            // Check if item already exists in cart
            $stmt = $this->db->prepare("
                SELECT cart_id, quantity FROM shopping_cart 
                WHERE user_id = :user_id 
                  AND product_id = :product_id 
                  AND product_type = :product_type
            ");
            $stmt->execute([
                ':user_id' => $data['user_id'],
                ':product_id' => $data['product_id'],
                ':product_type' => $data['product_type']
            ]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Update quantity
                $newQuantity = $existing['quantity'] + $data['quantity'];
                $subtotal = $newQuantity * $data['price_per_unit'];
                
                $stmt = $this->db->prepare("
                    UPDATE shopping_cart SET 
                        quantity = :quantity,
                        subtotal = :subtotal
                    WHERE cart_id = :cart_id
                ");
                return $stmt->execute([
                    ':quantity' => $newQuantity,
                    ':subtotal' => $subtotal,
                    ':cart_id' => $existing['cart_id']
                ]);
            } else {
                // Insert new item
                $subtotal = $data['quantity'] * $data['price_per_unit'];
                
                $stmt = $this->db->prepare("
                    INSERT INTO shopping_cart (
                        user_id, product_id, product_type, product_name,
                        quantity, unit, price_per_unit, subtotal
                    ) VALUES (
                        :user_id, :product_id, :product_type, :product_name,
                        :quantity, :unit, :price_per_unit, :subtotal
                    )
                ");
                return $stmt->execute([
                    ':user_id' => $data['user_id'],
                    ':product_id' => $data['product_id'],
                    ':product_type' => $data['product_type'],
                    ':product_name' => $data['product_name'],
                    ':quantity' => $data['quantity'],
                    ':unit' => $data['unit'],
                    ':price_per_unit' => $data['price_per_unit'],
                    ':subtotal' => $subtotal
                ]);
            }
        } catch (Exception $e) {
            error_log('Add to cart error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cart items for a user
     */
    public function getCartItems(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT 
                sc.*,
                CASE 
                    WHEN sc.product_type = 'vendor' THEN CONCAT(u.first_name, ' ', u.last_name)
                    ELSE NULL
                END as vendor_name,
                CASE 
                    WHEN sc.product_type = 'vendor' THEN vp.category
                    ELSE NULL
                END as vendor_category
            FROM shopping_cart sc
            LEFT JOIN vendor_products vp ON sc.product_type = 'vendor' AND sc.product_id = vp.product_id
            LEFT JOIN users u ON vp.vendor_user_id = u.user_id
            WHERE sc.user_id = :user_id 
            ORDER BY sc.added_date DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get cart summary
     */
    public function getCartSummary(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_items,
                SUM(quantity) as total_quantity,
                SUM(subtotal) as total_amount
            FROM shopping_cart 
            WHERE user_id = :user_id
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total_items' => 0,
            'total_quantity' => 0,
            'total_amount' => 0.00
        ];
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(int $cartId, float $quantity): bool {
        $stmt = $this->db->prepare("
            UPDATE shopping_cart SET 
                quantity = :quantity,
                subtotal = quantity * price_per_unit
            WHERE cart_id = :cart_id
        ");
        return $stmt->execute([
            ':quantity' => $quantity,
            ':cart_id' => $cartId
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $cartId): bool {
        $stmt = $this->db->prepare("DELETE FROM shopping_cart WHERE cart_id = :cart_id");
        return $stmt->execute([':cart_id' => $cartId]);
    }

    /**
     * Clear entire cart
     */
    public function clearCart(int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM shopping_cart WHERE user_id = :user_id");
        return $stmt->execute([':user_id' => $userId]);
    }

    /**
     * Get cart item count (for badge)
     */
    public function getCartCount(int $userId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM shopping_cart WHERE user_id = :user_id
        ");
        $stmt->execute([':user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }
}
