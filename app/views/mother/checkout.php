<?php
$pageTitle = 'Checkout';
$activeNav = 'grocery';
require_once __DIR__ . '/../templates/mother_layout.php';

// Free delivery threshold
$freeDeliveryThreshold = 500.00;
$subtotal = $cartSummary['total_amount'];
$deliveryFee = ($subtotal >= $freeDeliveryThreshold) ? 0 : 50.00;
$grandTotal = $subtotal + $deliveryFee;
?>

<style>
.checkout-section {
    background: #fff;
    border: 1.5px solid rgba(196,114,42,.15);
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.payment-method {
    border: 2px solid #ddd;
    border-radius: 1rem;
    padding: 1rem;
    cursor: pointer;
    transition: all .3s;
}

.payment-method:hover {
    border-color: #C4722A;
    background: rgba(196,114,42,.05);
}

.payment-method.active {
    border-color: #C4722A;
    background: rgba(196,114,42,.08);
}

.payment-logo {
    height: 30px;
    object-fit: contain;
}
</style>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?action=groceryMode" style="color:#C4722A">Grocery</a></li>
        <li class="breadcrumb-item"><a href="index.php?action=viewCart" style="color:#C4722A">Cart</a></li>
        <li class="breadcrumb-item active">Checkout</li>
    </ol>
</nav>

<div class="mb-4">
    <h4 class="fw-bold mb-1" style="color:#C4722A">
        <i class="bi bi-credit-card me-2"></i>Checkout
    </h4>
    <p class="text-muted mb-0">Complete your order</p>
</div>

<form method="POST" action="index.php?action=processCheckout" id="checkout-form">
    <!-- Hidden input: Always delivery -->
    <input type="hidden" name="fulfillment_method" value="delivery">
    
<div class="row g-4">
    <div class="col-lg-8">
        <!-- Delivery Information -->
        <div class="checkout-section">
            <h5 class="fw-bold mb-3" style="color:#C4722A">
                <i class="bi bi-truck me-2"></i>Delivery Information
            </h5>
            
            <div class="alert alert-info mb-3" style="background:rgba(196,114,42,.05);border:1px solid rgba(196,114,42,.2)">
                <i class="bi bi-info-circle me-2"></i>
                <small><strong>Free delivery</strong> for orders ≥ ₱500 | Otherwise ₱50 delivery fee</small>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Delivery Address *</label>
                <textarea name="delivery_address" class="form-control" rows="3" 
                          placeholder="House No., Street, Barangay, City" required></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Contact Number *</label>
                <input type="tel" name="contact_number" class="form-control" 
                       placeholder="09XX XXX XXXX" pattern="[0-9]{11}" required>
            </div>
            
            <div class="mb-0">
                <label class="form-label fw-bold">Order Notes (Optional)</label>
                <textarea name="notes" class="form-control" rows="2" 
                          placeholder="Special instructions for delivery..."></textarea>
            </div>
        </div>
        
        <!-- Payment Method -->
        <div class="checkout-section">
            <h5 class="fw-bold mb-3" style="color:#C4722A">
                <i class="bi bi-wallet2 me-2"></i>Payment Method
            </h5>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="payment-method active" onclick="selectPayment('paymongo', this)">
                        <input type="radio" name="payment_method" value="paymongo" checked class="d-none">
                        <div class="text-center">
                            <i class="bi bi-credit-card" style="font-size:2rem;color:#C4722A"></i>
                            <h6 class="mt-2 mb-1 fw-bold">Online Payment</h6>
                            <p class="small text-muted mb-0">Card, GCash, PayMaya, GrabPay</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="payment-method" onclick="selectPayment('cod', this)">
                        <input type="radio" name="payment_method" value="cod" class="d-none">
                        <div class="text-center">
                            <i class="bi bi-cash-coin" style="font-size:2rem;color:#16a34a"></i>
                            <h6 class="mt-2 mb-1 fw-bold">Cash on Delivery</h6>
                            <p class="small text-muted mb-0">Pay when you receive</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info mt-3 mb-0" style="background:rgba(59,130,246,.05);border:1px solid rgba(59,130,246,.2)">
                <i class="bi bi-info-circle me-2"></i>
                <small>Online payment is processed securely by PayMongo</small>
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="checkout-section">
            <h5 class="fw-bold mb-3" style="color:#C4722A">
                <i class="bi bi-bag-check me-2"></i>Order Items (<?= count($cartItems) ?>)
            </h5>
            
            <?php foreach ($cartItems as $item): ?>
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom:1px dashed #eee">
                <div>
                    <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                    <br>
                    <small class="text-muted">
                        <?= $item['quantity'] ?> <?= htmlspecialchars($item['unit']) ?> × ₱<?= number_format($item['price_per_unit'], 2) ?>
                    </small>
                </div>
                <strong style="color:#C4722A">₱<?= number_format($item['subtotal'], 2) ?></strong>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Order Summary -->
    <div class="col-lg-4">
        <div class="checkout-section position-sticky" style="top:20px">
            <h5 class="fw-bold mb-3" style="color:#C4722A">Order Summary</h5>
            
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal</span>
                <strong id="summary-subtotal">₱<?= number_format($subtotal, 2) ?></strong>
            </div>
            
            <div class="d-flex justify-content-between mb-2" id="delivery-fee-row">
                <span class="text-muted">Delivery Fee</span>
                <strong id="summary-delivery-fee">₱0.00</strong>
            </div>
            
            <div class="mb-3 pb-3" style="border-bottom:2px dashed #ddd"></div>
            
            <div class="d-flex justify-content-between mb-3 pb-3" style="border-bottom:2px dashed #ddd">
                <span class="fw-bold" style="font-size:1.1rem">Total</span>
                <strong id="summary-total" style="color:#16a34a;font-size:1.4rem">₱<?= number_format($subtotal, 2) ?></strong>
            </div>
            
            <button type="submit" class="btn btn-lg w-100" 
                    style="background:linear-gradient(135deg, #C4722A 0%, #A85E22 100%);color:#fff;border:none;font-weight:600">
                <i class="bi bi-check-circle me-2"></i>Place Order
            </button>
            
            <a href="index.php?action=viewCart" class="btn btn-outline-secondary w-100 mt-2">
                <i class="bi bi-arrow-left me-2"></i>Back to Cart
            </a>
        </div>
    </div>
</div>
</form>

<script>
const subtotal = <?= $subtotal ?>;
const deliveryFee = <?= $deliveryFee ?>;

// Set delivery fee on load
document.getElementById('summary-delivery-fee').textContent = deliveryFee > 0 
    ? '₱' + deliveryFee.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})
    : 'FREE';

function selectPayment(method, element) {
    // Remove active from all payment methods
    document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('active'));
    // Add active to selected
    element.classList.add('active');
    // Check the radio
    element.querySelector('input[type="radio"]').checked = true;
}

document.getElementById('checkout-form').addEventListener('submit', function(e) {
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
});
</script>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
