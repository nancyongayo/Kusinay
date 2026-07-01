<?php
/**
 * PayMongoCheckout
 * Handles PayMongo payment integration for checkout
 */

class PayMongoCheckout {
    private string $secretKey;
    private string $publicKey;
    private string $apiUrl = 'https://api.paymongo.com/v1';

    public function __construct() {
        // PayMongo API Keys
        $this->secretKey = 'sk_test_fZGuKc84wyCCU1kaZow3bWUo';
        $this->publicKey = 'pk_test_nSJHEygsL5LX9c1Ndchr9vRN';
    }

    /**
     * Create Payment Intent
     */
    public function createPaymentIntent(array $data): ?array {
        $url = $this->apiUrl . '/payment_intents';
        
        $payload = [
            'data' => [
                'attributes' => [
                    'amount' => (int)($data['amount'] * 100), // Convert to cents
                    'currency' => 'PHP',
                    'payment_method_allowed' => ['card', 'gcash', 'paymaya', 'grab_pay'],
                    'description' => $data['description'] ?? 'KusiNay Grocery Order',
                    'statement_descriptor' => 'KUSINAY GROCERY',
                    'metadata' => [
                        'order_number' => $data['order_number'] ?? null,
                        'user_id' => $data['user_id'] ?? null,
                    ]
                ]
            ]
        ];

        return $this->makeRequest('POST', $url, $payload);
    }

    /**
     * Create Checkout Session
     */
    public function createCheckoutSession(array $data): ?array {
        $url = $this->apiUrl . '/checkout_sessions';
        
        // Prepare line items
        $lineItems = [];
        foreach ($data['items'] as $item) {
            $lineItems[] = [
                'currency' => 'PHP',
                'amount' => (int)($item['price'] * 100), // Convert to cents
                'description' => $item['name'],
                'name' => $item['name'],
                'quantity' => (int)$item['quantity']
            ];
        }

        $payload = [
            'data' => [
                'attributes' => [
                    'send_email_receipt' => true,
                    'show_description' => true,
                    'show_line_items' => true,
                    'line_items' => $lineItems,
                    'payment_method_types' => ['card', 'gcash', 'paymaya', 'grab_pay'],
                    'description' => 'KusiNay Grocery Order: ' . ($data['order_number'] ?? ''),
                    'success_url' => $data['success_url'],
                    'cancel_url' => $data['cancel_url'],
                    'metadata' => [
                        'order_number' => $data['order_number'] ?? null,
                        'user_id' => $data['user_id'] ?? null,
                    ]
                ]
            ]
        ];

        return $this->makeRequest('POST', $url, $payload);
    }

    /**
     * Retrieve Payment Intent
     */
    public function getPaymentIntent(string $paymentIntentId): ?array {
        $url = $this->apiUrl . '/payment_intents/' . $paymentIntentId;
        return $this->makeRequest('GET', $url);
    }

    /**
     * Retrieve Checkout Session
     */
    public function getCheckoutSession(string $checkoutSessionId): ?array {
        $url = $this->apiUrl . '/checkout_sessions/' . $checkoutSessionId;
        return $this->makeRequest('GET', $url);
    }

    /**
     * Make HTTP request to PayMongo API
     */
    private function makeRequest(string $method, string $url, array $data = null): ?array {
        $ch = curl_init($url);
        
        $headers = [
            'Authorization: Basic ' . base64_encode($this->secretKey . ':'),
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            error_log('PayMongo cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }
        
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        } else {
            error_log('PayMongo API Error: ' . $response);
            return null;
        }
    }

    /**
     * Get public key for client-side use
     */
    public function getPublicKey(): string {
        return $this->publicKey;
    }
}
