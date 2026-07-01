<?php
/**
 * PayMongoService
 * 
 * Handles PayMongo API integration for Process 19: Buying Goods
 * - Create payment intents
 * - Create payment methods
 * - Process checkout sessions
 * - Handle webhooks
 */

class PayMongoService {
    private array $config;
    private string $secretKey;
    private string $publicKey;
    private string $baseUrl;
    
    public function __construct() {
        $this->config = require __DIR__ . '/../../config/paymongo.php';
        $this->secretKey = $this->config['api_key']['secret'];
        $this->publicKey = $this->config['api_key']['public'];
        $this->baseUrl = $this->config['base_url'];
    }
    
    /**
     * Create a checkout session for grocery list payment
     */
    public function createCheckoutSession(array $data): array {
        $lineItems = [];
        
        // Build line items from grocery list
        foreach ($data['items'] as $item) {
            $lineItems[] = [
                'name' => $item['product_name'],
                'quantity' => (int)$item['quantity'],
                'amount' => (int)round($item['estimated_price'] * 100), // Convert to centavos
                'currency' => 'PHP',
                'description' => $item['category'] . ' - ' . $item['unit'],
            ];
        }
        
        // Calculate total
        $totalAmount = array_sum(array_map(function($item) {
            return $item['amount'] * $item['quantity'];
        }, $lineItems));
        
        $payload = [
            'data' => [
                'attributes' => [
                    'line_items' => $lineItems,
                    'payment_method_types' => $data['payment_methods'] ?? ['gcash', 'paymaya', 'card', 'grab_pay'],
                    'success_url' => $this->getFullUrl($this->config['success_url'] . '&session_id={CHECKOUT_SESSION_ID}'),
                    'cancel_url' => $this->getFullUrl($this->config['cancel_url']),
                    'description' => $data['description'] ?? 'KusiNay Grocery Payment',
                    'statement_descriptor' => $this->config['statement_descriptor'],
                    'metadata' => [
                        'grocery_list_id' => $data['grocery_list_id'],
                        'user_id' => $data['user_id'],
                        'customer_name' => $data['customer_name'] ?? 'Customer',
                    ],
                ],
            ],
        ];
        
        return $this->makeRequest('POST', '/checkout_sessions', $payload);
    }
    
    /**
     * Create a payment intent
     */
    public function createPaymentIntent(float $amount, array $metadata = []): array {
        $amountInCentavos = (int)round($amount * 100);
        
        $payload = [
            'data' => [
                'attributes' => [
                    'amount' => $amountInCentavos,
                    'currency' => 'PHP',
                    'description' => $metadata['description'] ?? 'KusiNay Market Payment',
                    'statement_descriptor' => $this->config['statement_descriptor'],
                    'metadata' => $metadata,
                ],
            ],
        ];
        
        return $this->makeRequest('POST', '/payment_intents', $payload);
    }
    
    /**
     * Create a payment method
     */
    public function createPaymentMethod(string $type, array $details): array {
        $payload = [
            'data' => [
                'attributes' => [
                    'type' => $type,
                    'details' => $details,
                ],
            ],
        ];
        
        return $this->makeRequest('POST', '/payment_methods', $payload);
    }
    
    /**
     * Attach payment method to payment intent
     */
    public function attachPaymentIntent(string $paymentIntentId, string $paymentMethodId, string $returnUrl): array {
        $payload = [
            'data' => [
                'attributes' => [
                    'payment_method' => $paymentMethodId,
                    'return_url' => $returnUrl,
                ],
            ],
        ];
        
        return $this->makeRequest('POST', "/payment_intents/{$paymentIntentId}/attach", $payload);
    }
    
    /**
     * Retrieve a payment intent
     */
    public function getPaymentIntent(string $paymentIntentId): array {
        return $this->makeRequest('GET', "/payment_intents/{$paymentIntentId}");
    }
    
    /**
     * Retrieve a checkout session
     */
    public function getCheckoutSession(string $sessionId): array {
        return $this->makeRequest('GET', "/checkout_sessions/{$sessionId}");
    }
    
    /**
     * Create a GCash payment source
     */
    public function createGCashSource(float $amount, array $metadata = []): array {
        $amountInCentavos = (int)round($amount * 100);
        
        $payload = [
            'data' => [
                'attributes' => [
                    'type' => 'gcash',
                    'amount' => $amountInCentavos,
                    'currency' => 'PHP',
                    'redirect' => [
                        'success' => $this->getFullUrl($this->config['success_url']),
                        'failed' => $this->getFullUrl($this->config['cancel_url']),
                    ],
                    'metadata' => $metadata,
                ],
            ],
        ];
        
        return $this->makeRequest('POST', '/sources', $payload);
    }
    
    /**
     * Create a payment (charge a source)
     */
    public function createPayment(string $sourceId, array $metadata = []): array {
        $payload = [
            'data' => [
                'attributes' => [
                    'amount' => $metadata['amount'] ?? 0,
                    'currency' => 'PHP',
                    'source' => [
                        'id' => $sourceId,
                        'type' => 'source',
                    ],
                    'description' => $metadata['description'] ?? 'KusiNay Payment',
                    'statement_descriptor' => $this->config['statement_descriptor'],
                    'metadata' => $metadata,
                ],
            ],
        ];
        
        return $this->makeRequest('POST', '/payments', $payload);
    }
    
    /**
     * Retrieve a payment
     */
    public function getPayment(string $paymentId): array {
        return $this->makeRequest('GET', "/payments/{$paymentId}");
    }
    
    /**
     * Calculate transaction fee
     */
    public function calculateFee(float $amount, string $paymentMethod): float {
        $fees = $this->config['fees'];
        
        switch ($paymentMethod) {
            case 'card':
                return ($amount * ($fees['card'] / 100)) + $fees['card_fixed'];
            case 'gcash':
            case 'paymaya':
            case 'grab_pay':
                return (float)$fees[$paymentMethod];
            default:
                return 0.00;
        }
    }
    
    /**
     * Get available payment methods
     */
    public function getAvailablePaymentMethods(): array {
        $methods = [];
        foreach ($this->config['payment_methods'] as $key => $method) {
            if ($method['enabled']) {
                $methods[$key] = $method;
            }
        }
        return $methods;
    }
    
    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool {
        if (empty($this->config['webhook_secret'])) {
            return true; // Skip verification if no secret configured
        }
        
        $computedSignature = hash_hmac('sha256', $payload, $this->config['webhook_secret']);
        return hash_equals($computedSignature, $signature);
    }
    
    /**
     * Make HTTP request to PayMongo API
     */
    private function makeRequest(string $method, string $endpoint, ?array $payload = null): array {
        $url = $this->baseUrl . $endpoint;
        $auth = base64_encode($this->secretKey . ':');
        
        $ch = curl_init();
        
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Basic ' . $auth,
        ];
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($payload) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            }
        } elseif ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($payload) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception('PayMongo API Error: ' . $error);
        }
        
        $data = json_decode($response, true);
        
        if ($httpCode >= 400) {
            $errorMessage = $data['errors'][0]['detail'] ?? 'Unknown error';
            throw new Exception('PayMongo API Error: ' . $errorMessage);
        }
        
        return $data;
    }
    
    /**
     * Get full URL with protocol and host
     */
    private function getFullUrl(string $path): string {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // Remove leading slash if present
        $path = ltrim($path, '/');
        
        return "{$protocol}://{$host}/{$path}";
    }
    
    /**
     * Format amount for display
     */
    public function formatAmount(float $amount): string {
        return '₱' . number_format($amount, 2);
    }
    
    /**
     * Convert centavos to pesos
     */
    public function centavosToPesos(int $centavos): float {
        return $centavos / 100;
    }
    
    /**
     * Convert pesos to centavos
     */
    public function pesosToCentavos(float $pesos): int {
        return (int)round($pesos * 100);
    }
}
