<?php
/**
 * PayMongo Configuration
 * 
 * Get your API keys from: https://dashboard.paymongo.com/developers/api-keys
 * 
 * IMPORTANT: 
 * - Use test keys for development
 * - Use live keys only in production
 * - Never commit your live API keys to version control
 */

return [
    // API Keys
    'api_key' => [
        'public' => getenv('PAYMONGO_PUBLIC_KEY') ?: 'pk_test_nSJHEygsL5LX9c1Ndchr9vRN',
        'secret' => getenv('PAYMONGO_SECRET_KEY') ?: 'sk_test_fZGuKc84wyCCU1kaZow3bWUo',
    ],
    
    // API Endpoint
    'base_url' => 'https://api.paymongo.com/v1',
    
    // Webhook Configuration (optional)
    'webhook_secret' => getenv('PAYMONGO_WEBHOOK_SECRET') ?: '',
    
    // Payment Configuration
    'currency' => 'PHP',
    'statement_descriptor' => 'KusiNay Market',
    
    // Payment Methods
    'payment_methods' => [
        'gcash' => [
            'enabled' => true,
            'name' => 'GCash',
            'description' => 'Pay using GCash e-wallet',
            'icon' => 'bi-phone',
        ],
        'paymaya' => [
            'enabled' => true,
            'name' => 'PayMaya',
            'description' => 'Pay using PayMaya e-wallet',
            'icon' => 'bi-credit-card',
        ],
        'grab_pay' => [
            'enabled' => true,
            'name' => 'GrabPay',
            'description' => 'Pay using GrabPay e-wallet',
            'icon' => 'bi-wallet2',
        ],
        'card' => [
            'enabled' => true,
            'name' => 'Credit/Debit Card',
            'description' => 'Pay using Visa, Mastercard, JCB',
            'icon' => 'bi-credit-card-2-front',
        ],
    ],
    
    // Checkout Settings
    'success_url' => '/index.php?action=paymentSuccess',
    'cancel_url' => '/index.php?action=paymentCancel',
    
    // Transaction Fees
    'fees' => [
        'gcash' => 0.00, // Free for GCash
        'paymaya' => 0.00, // Free for PayMaya
        'grab_pay' => 0.00, // Free for GrabPay
        'card' => 3.00, // 3% + PHP 15 for cards (will be calculated)
        'card_fixed' => 15.00,
    ],
];
