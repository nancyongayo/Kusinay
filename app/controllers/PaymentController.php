<?php
/**
 * PaymentController
 * 
 * Handles PayMongo payment processing for Process 19: Buying Goods
 * - Create checkout sessions
 * - Process payments
 * - Handle payment callbacks
 * - Manage webhooks
 */

require_once __DIR__ . '/../models/PayMongoService.php';
require_once __DIR__ . '/../models/GroceryListModel.php';

class PaymentController {
    private PDO $db;
    private PayMongoService $paymongoService;
    private GroceryListModel $groceryModel;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->paymongoService = new PayMongoService();
        $this->groceryModel = new GroceryListModel($db);
    }

    /**
     * Show checkout page for grocery list
     */
    public function showCheckout(): void {
        if (!isset($_SESSION['user_id']) || empty($_GET['grocery_list_id'])) {
            header('Location: index.php?action=groceryLists');
            exit;
        }

        $groceryListId = (int)$_GET['grocery_list_id'];
        $groceryList = $this->groceryModel->getGroceryListById($groceryListId);
        
        if (!$groceryList || $groceryList['user_id'] != $_SESSION['user_id']) {
            $_SESSION['flash'] = 'Unauthorized access or list not found.';
            header('Location: index.php?action=groceryLists');
            exit;
        }

        // Get items
        $items = $this->groceryModel->getGroceryListItems($groceryListId);
        
        if (empty($items)) {
            $_SESSION['flash'] = 'Cannot checkout an empty list.';
            header('Location: index.php?action=groceryListForm&id=' . $groceryListId);
            exit;
        }

        // Get available payment methods
        $paymentMethods = $this->paymongoService->getAvailablePaymentMethods();
        
        // Calculate totals
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += ($item['estimated_price'] ?? 0) * $item['quantity'];
        }
        
        include __DIR__ . '/../views/mother/checkout.php';
    }

    /**
     * Create PayMongo checkout session
     */
    public function createCheckoutSession(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        try {
            $groceryListId = (int)$_POST['grocery_list_id'];
            $paymentMethod = $_POST['payment_method'] ?? 'gcash';
            
            // Get grocery list and validate ownership
            $groceryList = $this->groceryModel->getGroceryListById($groceryListId);
            if (!$groceryList || $groceryList['user_id'] != $_SESSION['user_id']) {
                throw new Exception('Unauthorized access');
            }

            // Get items
            $items = $this->groceryModel->getGroceryListItems($groceryListId);
            if (empty($items)) {
                throw new Exception('Cannot checkout an empty list');
            }

            // Calculate total
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += ($item['estimated_price'] ?? 0) * $item['quantity'];
            }

            // Calculate fee
            $fee = $this->paymongoService->calculateFee($totalAmount, $paymentMethod);
            $netAmount = $totalAmount + $fee;

            // Create payment transaction record
            $transactionId = $this->createPaymentTransaction([
                'grocery_list_id' => $groceryListId,
                'user_id' => $_SESSION['user_id'],
                'amount' => $totalAmount,
                'fee' => $fee,
                'net_amount' => $netAmount,
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'customer_name' => $groceryList['first_name'] . ' ' . $groceryList['last_name'],
                'description' => 'Payment for ' . $groceryList['list_name'],
            ]);

            // Save transaction items
            foreach ($items as $item) {
                $this->savePaymentItem($transactionId, $item);
            }

            // Create PayMongo checkout session
            $checkoutData = [
                'items' => $items,
                'payment_methods' => [$paymentMethod],
                'grocery_list_id' => $groceryListId,
                'user_id' => $_SESSION['user_id'],
                'customer_name' => $groceryList['first_name'] . ' ' . $groceryList['last_name'],
                'description' => 'Payment for ' . $groceryList['list_name'],
            ];

            $response = $this->paymongoService->createCheckoutSession($checkoutData);
            
            // Update transaction with PayMongo IDs
            $this->updatePaymentTransaction($transactionId, [
                'paymongo_session_id' => $response['data']['id'] ?? null,
                'payment_status' => 'processing',
            ]);

            // Return checkout URL
            $checkoutUrl = $response['data']['attributes']['checkout_url'] ?? null;
            
            if ($checkoutUrl) {
                echo json_encode([
                    'success' => true,
                    'checkout_url' => $checkoutUrl,
                    'transaction_id' => $transactionId,
                ]);
            } else {
                throw new Exception('Failed to create checkout session');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        exit;
    }

    /**
     * Handle payment success callback
     */
    public function paymentSuccess(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $sessionId = $_GET['session_id'] ?? null;
        $transaction = null;
        
        if ($sessionId) {
            try {
                // Retrieve checkout session from PayMongo
                $session = $this->paymongoService->getCheckoutSession($sessionId);
                
                // Get transaction from database
                $stmt = $this->db->prepare("
                    SELECT * FROM payment_transactions 
                    WHERE paymongo_session_id = :session_id
                ");
                $stmt->execute([':session_id' => $sessionId]);
                $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($transaction && $session['data']['attributes']['payment_status'] === 'paid') {
                    // Update transaction status
                    $this->updatePaymentTransaction($transaction['transaction_id'], [
                        'payment_status' => 'paid',
                        'paid_at' => date('Y-m-d H:i:s'),
                        'paymongo_payment_id' => $session['data']['attributes']['payments'][0]['id'] ?? null,
                    ]);
                    
                    // Update grocery list
                    if ($transaction['grocery_list_id']) {
                        $this->db->prepare("
                            UPDATE grocery_lists SET 
                                payment_status = 'paid',
                                payment_method = 'online',
                                total_paid = :amount,
                                payment_date = NOW(),
                                status = 'Completed'
                            WHERE grocery_list_id = :grocery_list_id
                        ")->execute([
                            ':amount' => $transaction['amount'],
                            ':grocery_list_id' => $transaction['grocery_list_id'],
                        ]);
                        
                        // Mark all items as purchased
                        $this->db->prepare("
                            UPDATE grocery_list_items SET
                                is_purchased = 1,
                                actual_price = estimated_price,
                                purchase_date = NOW()
                            WHERE grocery_list_id = :grocery_list_id
                        ")->execute([':grocery_list_id' => $transaction['grocery_list_id']]);
                    }
                }
            } catch (Exception $e) {
                error_log('Payment success callback error: ' . $e->getMessage());
            }
        }
        
        include __DIR__ . '/../views/mother/payment_success.php';
    }

    /**
     * Handle payment cancellation
     */
    public function paymentCancel(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        include __DIR__ . '/../views/mother/payment_cancel.php';
    }

    /**
     * Show payment history for user
     */
    public function showPaymentHistory(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $stmt = $this->db->prepare("
            SELECT 
                pt.*,
                gl.list_name,
                gl.total_estimated_cost
            FROM payment_transactions pt
            LEFT JOIN grocery_lists gl ON gl.grocery_list_id = pt.grocery_list_id
            WHERE pt.user_id = :user_id
            ORDER BY pt.created_at DESC
        ");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include __DIR__ . '/../views/mother/payment_history.php';
    }

    /**
     * View payment transaction details
     */
    public function viewTransaction(): void {
        if (!isset($_SESSION['user_id']) || empty($_GET['id'])) {
            header('Location: index.php?action=paymentHistory');
            exit;
        }

        $transactionId = (int)$_GET['id'];
        
        // Get transaction
        $stmt = $this->db->prepare("
            SELECT 
                pt.*,
                gl.list_name,
                gl.hh_number
            FROM payment_transactions pt
            LEFT JOIN grocery_lists gl ON gl.grocery_list_id = pt.grocery_list_id
            WHERE pt.transaction_id = :transaction_id AND pt.user_id = :user_id
        ");
        $stmt->execute([
            ':transaction_id' => $transactionId,
            ':user_id' => $_SESSION['user_id'],
        ]);
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$transaction) {
            $_SESSION['flash'] = 'Transaction not found.';
            header('Location: index.php?action=paymentHistory');
            exit;
        }

        // Get transaction items
        $stmt = $this->db->prepare("
            SELECT * FROM payment_transaction_items
            WHERE transaction_id = :transaction_id
        ");
        $stmt->execute([':transaction_id' => $transactionId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include __DIR__ . '/../views/mother/payment_detail.php';
    }

    /**
     * PayMongo Webhook Handler
     */
    public function handleWebhook(): void {
        // Get raw POST data
        $payload = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_PAYMONGO_SIGNATURE'] ?? '';
        
        // Verify signature
        if (!$this->paymongoService->verifyWebhookSignature($payload, $signature)) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid signature']);
            exit;
        }
        
        $event = json_decode($payload, true);
        
        // Log webhook
        $webhookId = $this->logWebhook($event, $signature);
        
        try {
            $eventType = $event['data']['attributes']['type'] ?? '';
            $paymentData = $event['data']['attributes']['data'] ?? [];
            
            switch ($eventType) {
                case 'payment.paid':
                    $this->handlePaymentPaid($paymentData);
                    break;
                case 'payment.failed':
                    $this->handlePaymentFailed($paymentData);
                    break;
                case 'source.chargeable':
                    $this->handleSourceChargeable($paymentData);
                    break;
            }
            
            // Mark webhook as processed
            $this->db->prepare("
                UPDATE payment_webhooks SET 
                    processed = 1,
                    processed_at = NOW()
                WHERE webhook_id = :webhook_id
            ")->execute([':webhook_id' => $webhookId]);
            
            http_response_code(200);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            // Log error
            $this->db->prepare("
                UPDATE payment_webhooks SET 
                    error_message = :error
                WHERE webhook_id = :webhook_id
            ")->execute([
                ':error' => $e->getMessage(),
                ':webhook_id' => $webhookId,
            ]);
            
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Create payment transaction record
     */
    private function createPaymentTransaction(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO payment_transactions (
                grocery_list_id, user_id, amount, fee, net_amount,
                payment_method, payment_status, customer_name,
                customer_email, customer_phone, description
            ) VALUES (
                :grocery_list_id, :user_id, :amount, :fee, :net_amount,
                :payment_method, :payment_status, :customer_name,
                :customer_email, :customer_phone, :description
            )
        ");
        
        $stmt->execute([
            ':grocery_list_id' => $data['grocery_list_id'] ?? null,
            ':user_id' => $data['user_id'],
            ':amount' => $data['amount'],
            ':fee' => $data['fee'] ?? 0,
            ':net_amount' => $data['net_amount'],
            ':payment_method' => $data['payment_method'],
            ':payment_status' => $data['payment_status'] ?? 'pending',
            ':customer_name' => $data['customer_name'] ?? null,
            ':customer_email' => $data['customer_email'] ?? null,
            ':customer_phone' => $data['customer_phone'] ?? null,
            ':description' => $data['description'] ?? null,
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    /**
     * Update payment transaction
     */
    private function updatePaymentTransaction(int $transactionId, array $data): bool {
        $fields = [];
        $params = [':transaction_id' => $transactionId];
        
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }
        
        $sql = "UPDATE payment_transactions SET " . implode(', ', $fields) . " WHERE transaction_id = :transaction_id";
        
        return $this->db->prepare($sql)->execute($params);
    }

    /**
     * Save payment transaction item
     */
    private function savePaymentItem(int $transactionId, array $item): void {
        $stmt = $this->db->prepare("
            INSERT INTO payment_transaction_items (
                transaction_id, grocery_list_item_id, product_name,
                category, quantity, unit, unit_price, total_price
            ) VALUES (
                :transaction_id, :grocery_list_item_id, :product_name,
                :category, :quantity, :unit, :unit_price, :total_price
            )
        ");
        
        $unitPrice = $item['estimated_price'] ?? 0;
        $totalPrice = $unitPrice * $item['quantity'];
        
        $stmt->execute([
            ':transaction_id' => $transactionId,
            ':grocery_list_item_id' => $item['item_id'] ?? null,
            ':product_name' => $item['product_name'],
            ':category' => $item['category'] ?? null,
            ':quantity' => $item['quantity'],
            ':unit' => $item['unit'],
            ':unit_price' => $unitPrice,
            ':total_price' => $totalPrice,
        ]);
    }

    /**
     * Log webhook event
     */
    private function logWebhook(array $event, string $signature): int {
        $stmt = $this->db->prepare("
            INSERT INTO payment_webhooks (
                event_type, paymongo_event_id, paymongo_payment_id,
                payload, signature
            ) VALUES (
                :event_type, :paymongo_event_id, :paymongo_payment_id,
                :payload, :signature
            )
        ");
        
        $stmt->execute([
            ':event_type' => $event['data']['attributes']['type'] ?? 'unknown',
            ':paymongo_event_id' => $event['data']['id'] ?? null,
            ':paymongo_payment_id' => $event['data']['attributes']['data']['id'] ?? null,
            ':payload' => json_encode($event),
            ':signature' => $signature,
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    /**
     * Handle payment paid webhook
     */
    private function handlePaymentPaid(array $paymentData): void {
        $paymentId = $paymentData['id'] ?? null;
        if (!$paymentId) return;
        
        $stmt = $this->db->prepare("
            UPDATE payment_transactions SET
                payment_status = 'paid',
                paid_at = NOW(),
                paymongo_payment_id = :payment_id
            WHERE paymongo_intent_id = :intent_id
            OR paymongo_session_id IN (
                SELECT checkout_session_id FROM paymongo_sessions_payments WHERE payment_id = :payment_id2
            )
        ");
        $stmt->execute([
            ':payment_id' => $paymentId,
            ':intent_id' => $paymentData['attributes']['payment_intent_id'] ?? '',
            ':payment_id2' => $paymentId,
        ]);
    }

    /**
     * Handle payment failed webhook
     */
    private function handlePaymentFailed(array $paymentData): void {
        $paymentId = $paymentData['id'] ?? null;
        if (!$paymentId) return;
        
        $stmt = $this->db->prepare("
            UPDATE payment_transactions SET
                payment_status = 'failed',
                error_message = :error
            WHERE paymongo_payment_id = :payment_id
            OR paymongo_intent_id = :intent_id
        ");
        $stmt->execute([
            ':payment_id' => $paymentId,
            ':intent_id' => $paymentData['attributes']['payment_intent_id'] ?? '',
            ':error' => $paymentData['attributes']['last_payment_error']['message'] ?? 'Payment failed',
        ]);
    }

    /**
     * Handle source chargeable webhook
     */
    private function handleSourceChargeable(array $sourceData): void {
        // This can be used to automatically create payment when source becomes chargeable
        $sourceId = $sourceData['id'] ?? null;
        if (!$sourceId) return;
        
        // Implementation depends on your specific flow
    }
}
