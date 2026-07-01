<?php
/**
 * OrderModel
 * Handles order creation and management
 */

class OrderModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Create order from cart
     */
    public function createOrder(array $data): ?int {
        try {
            $this->db->beginTransaction();

            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Insert order
            $stmt = $this->db->prepare("
                INSERT INTO orders (
                    user_id, order_number, total_amount, delivery_fee, grand_total,
                    payment_method, fulfillment_method, delivery_address, contact_number, notes,
                    order_status, payment_status
                ) VALUES (
                    :user_id, :order_number, :total_amount, :delivery_fee, :grand_total,
                    :payment_method, :fulfillment_method, :delivery_address, :contact_number, :notes,
                    'pending', 'pending'
                )
            ");
            $stmt->execute([
                ':user_id' => $data['user_id'],
                ':order_number' => $orderNumber,
                ':total_amount' => $data['total_amount'],
                ':delivery_fee' => $data['delivery_fee'] ?? 0.00,
                ':grand_total' => $data['grand_total'],
                ':payment_method' => $data['payment_method'],
                ':fulfillment_method' => $data['fulfillment_method'] ?? 'pickup',
                ':delivery_address' => $data['delivery_address'] ?? null,
                ':contact_number' => $data['contact_number'] ?? null,
                ':notes' => $data['notes'] ?? null
            ]);

            $orderId = $this->db->lastInsertId();

            // Copy cart items to order_items
            $stmt = $this->db->prepare("
                INSERT INTO order_items (
                    order_id, product_id, product_type, product_name,
                    quantity, unit, price_per_unit, subtotal
                )
                SELECT 
                    :order_id, product_id, product_type, product_name,
                    quantity, unit, price_per_unit, subtotal
                FROM shopping_cart
                WHERE user_id = :user_id
            ");
            $stmt->execute([
                ':order_id' => $orderId,
                ':user_id' => $data['user_id']
            ]);

            // Clear cart
            $stmt = $this->db->prepare("DELETE FROM shopping_cart WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $data['user_id']]);

            $this->db->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Create order error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update order with PayMongo details
     */
    public function updatePayMongoDetails(int $orderId, array $paymongoData): bool {
        $stmt = $this->db->prepare("
            UPDATE orders SET
                paymongo_payment_intent_id = :payment_intent_id,
                paymongo_checkout_url = :checkout_url,
                paymongo_payment_method = :payment_method
            WHERE order_id = :order_id
        ");
        return $stmt->execute([
            ':payment_intent_id' => $paymongoData['payment_intent_id'] ?? null,
            ':checkout_url' => $paymongoData['checkout_url'] ?? null,
            ':payment_method' => $paymongoData['payment_method'] ?? null,
            ':order_id' => $orderId
        ]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(int $orderId, string $status): bool {
        $stmt = $this->db->prepare("
            UPDATE orders SET
                payment_status = :status,
                payment_date = CASE WHEN :status2 = 'paid' THEN NOW() ELSE payment_date END
            WHERE order_id = :order_id
        ");
        return $stmt->execute([
            ':status' => $status,
            ':status2' => $status,
            ':order_id' => $orderId
        ]);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(int $orderId, string $status): bool {
        $stmt = $this->db->prepare("
            UPDATE orders SET order_status = :status WHERE order_id = :order_id
        ");
        return $stmt->execute([
            ':status' => $status,
            ':order_id' => $orderId
        ]);
    }

    /**
     * Get order by ID
     */
    public function getOrderById(int $orderId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($order) {
            $order['items'] = $this->getOrderItems($orderId);
        }
        
        return $order ?: null;
    }

    /**
     * Get order items
     */
    public function getOrderItems(int $orderId): array {
        $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get user orders
     */
    public function getUserOrders(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM orders 
            WHERE user_id = :user_id 
            ORDER BY order_date DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber(): string {
        $year = date('Y');
        $random = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        return "ORD-{$year}-{$random}";
    }
}
