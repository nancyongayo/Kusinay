<?php
$pageTitle = 'Shopping Cart';
$activeNav = 'grocery';
require_once __DIR__ . '/../templates/mother_layout.php';

// Free delivery threshold
$freeDeliveryThreshold = 500.00;
$subtotal = $cartSummary['total_amount'] ?? 0;
$deliveryFee = ($subtotal >= $freeDeliveryThreshold) ? 0 : 50.00;
$amountNeeded = max(0, $freeDeliveryThreshold - $subtotal);
$grandTotal = $subtotal + $deliveryFee;
?>

<style>
.cart-item-card {
    border: 1.5px solid rgba(196,114,42,.15);
    border-radius: 1rem;
    transition: all .3s;
    background: #fff;
}

.cart-item-card:hover {
    box-shadow: 0 4px 16px rgba(196,114,42,.15);
}

.quantity-input {
    width: 80px;
    text-align: center;
    border: 1.5px solid rgba(196,114,42,.2);
    border-radius: .5rem;
}

.quantity-btn {
    width: 35px;
    height: 35px;
    border: 1.5px solid rgba(196,114,42,.2);
    background: rgba(196,114,42,.05);
    color: #C4722A;
    border-radius: .5rem;
    font-weight: bold;
}

.quantity-btn:hover {
    background: #C4722A;
    color: #fff;
}

.remove-btn {
    color: #dc2626;
    transition: all .3s;
}

.remove-btn:hover {
    color: #991b1b;
    transform: scale(1.1);
}

.cart-summary {
    position: sticky;
    top: 20px;
}
</style>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?action=groceryMode" style="color:#C4722A">Grocery</a></li>
        <li class="breadcrumb-item active">Shopping Cart</li>
    </ol>
</nav>

<div class="mb-4">
    <h4 class="fw-bold mb-1" style="color:#C4722A">
        <i class="bi bi-cart3 me-2"></i>Shopping Cart
    </h4>
    <p class="text-muted mb-0">Review your items before checkout</p>
</div>

<?php if (empty($cartItems)): ?>
<!-- Empty Cart -->
<div class="text-center py-5">
    <i class="bi bi-cart-x" style="font-size:5rem;color:#ccc"></i>
    <h5 class="mt-4 mb-2">Your cart is empty</h5>
    <p class="text-muted mb-4">Add items from Supermarket (SRP) or Wet Market vendors</p>
    <div class="d-flex gap-2 justify-content-center">
        <a href="index.php?action=supermarket" class="btn" style="background:#3b82f6;color:#fff">
            <i class="bi bi-shop me-2"></i>Supermarket (SRP)
        </a>
        <a href="index.php?action=wetMarket" class="btn" style="background:#C4722A;color:#fff">
            <i class="bi bi-basket me-2"></i>Wet Market
        </a>
    </div>
</div>
<?php else: ?>

<div class="row g-4">
    <!-- Cart Items -->
    <div class="col-lg-8">
        <!-- Info Banner -->
        <div class="alert alert-success mb-3" style="background:rgba(34,197,94,.05);border:1px solid rgba(34,197,94,.15);border-radius:.75rem">
            <div class="d-flex gap-2 align-items-start">
                <i class="bi bi-check-circle-fill" style="color:#16a34a;font-size:1.2rem"></i>
                <div style="flex:1">
                    <strong style="color:#15803d">Best Prices Selected</strong>
                    <p class="mb-0 small mt-1" style="color:#475569">
                        We've automatically added the <strong>lowest-priced</strong> products for your grocery list.
                        Each item shows whether it's from <strong>Supermarket (SRP)</strong> or <strong>Wet Market vendors</strong>.
                    </p>
                    <?php if (isset($_SESSION['grocery_list_created'])): ?>
                    <div class="alert alert-warning mt-2 mb-0 p-2 small" style="background:rgba(245,158,11,.05);border:1px solid rgba(245,158,11,.2)">
                        <i class="bi bi-clipboard-check me-1"></i>
                        <strong>Note:</strong> Some items were not found online and were added to your 
                        <a href="index.php?action=showGroceryLists" class="alert-link">Grocery List</a> for local purchase.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php foreach ($cartItems as $item): ?>
        <div class="cart-item-card p-3 mb-3" id="cart-item-<?= $item['cart_id'] ?>">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                    <div class="mb-2">
                        <?php if ($item['product_type'] === 'srp'): ?>
                            <span class="badge" style="background:linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);color:#fff;font-size:.7rem">
                                <i class="bi bi-shield-check me-1"></i>SRP
                            </span>
                        <?php else: ?>
                            <span class="badge" style="background:rgba(196,114,42,.1);color:#C4722A;font-size:.7rem">
                                <i class="bi bi-shop me-1"></i><?= htmlspecialchars($item['vendor_name'] ?? 'Wet Market') ?>
                            </span>
                            <?php if (!empty($item['vendor_category'])): ?>
                            <span class="badge bg-light text-muted" style="font-size:.65rem">
                                <?= htmlspecialchars($item['vendor_category']) ?>
                            </span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <p class="mb-0">
                        <strong style="color:#16a34a">₱<?= number_format($item['price_per_unit'], 2) ?></strong>
                        <span class="text-muted">/ <?= htmlspecialchars($item['unit']) ?></span>
                    </p>
                </div>
                
                <div class="col-md-3 col-6 mt-3 mt-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn quantity-btn" onclick="updateQuantity(<?= $item['cart_id'] ?>, -1, <?= $item['quantity'] ?>)">
                            <i class="bi bi-dash"></i>
                        </button>
                        <input type="number" class="form-control quantity-input" 
                               id="qty-<?= $item['cart_id'] ?>"
                               value="<?= $item['quantity'] ?>" 
                               min="1" 
                               readonly>
                        <button class="btn quantity-btn" onclick="updateQuantity(<?= $item['cart_id'] ?>, 1, <?= $item['quantity'] ?>)">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
                
                <div class="col-md-2 col-4 mt-3 mt-md-0 text-center">
                    <strong class="item-subtotal" style="color:#C4722A;font-size:1.1rem">
                        ₱<?= number_format($item['subtotal'], 2) ?>
                    </strong>
                </div>
                
                <div class="col-md-1 col-2 mt-3 mt-md-0 text-end">
                    <button class="btn btn-link remove-btn p-0" 
                            onclick="removeItem(<?= $item['cart_id'] ?>)"
                            title="Remove">
                        <i class="bi bi-trash" style="font-size:1.2rem"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
    
    <!-- Cart Summary -->
    <div class="col-lg-4">
        <div class="cart-summary">
            <div class="card" style="border:1.5px solid rgba(196,114,42,.2);border-radius:1rem">
                <div class="card-header" style="background:rgba(196,114,42,.05);border-bottom:1.5px solid rgba(196,114,42,.1)">
                    <h5 class="mb-0 fw-bold" style="color:#C4722A">
                        <i class="bi bi-receipt me-2"></i>Order Summary
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Items (<?= $cartSummary['total_items'] ?>)</span>
                        <span id="items-count"><?= $cartSummary['total_items'] ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <strong id="subtotal-amount">₱<?= number_format($subtotal, 2) ?></strong>
                    </div>
                    
                    <?php if ($deliveryFee > 0): ?>
                    <!-- Has delivery fee -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Delivery Fee</span>
                        <strong>₱<?= number_format($deliveryFee, 2) ?></strong>
                    </div>
                    
                    <div class="alert alert-warning mb-3 p-2" style="font-size:.85rem;background:rgba(245,158,11,.05);border:1px solid rgba(245,158,11,.2)">
                        <i class="bi bi-info-circle me-1"></i>
                        Add <strong>₱<?= number_format($amountNeeded, 2) ?></strong> more for <strong>FREE delivery</strong>!
                    </div>
                    <?php else: ?>
                    <!-- Free delivery achieved -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Delivery Fee</span>
                        <strong class="text-success">
                            <del class="text-muted">₱50.00</del> FREE
                        </strong>
                    </div>
                    
                    <div class="alert alert-success mb-3 p-2" style="font-size:.85rem">
                        <i class="bi bi-check-circle me-1"></i>
                        <strong>FREE Delivery</strong> available! Enjoy free shipping on this order.
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between mb-3 pb-3" style="border-bottom:1px dashed #ddd">
                        <span class="fw-bold" style="font-size:1.1rem">Total</span>
                        <strong id="grand-total" style="color:#16a34a;font-size:1.3rem">
                            ₱<?= number_format($grandTotal, 2) ?>
                        </strong>
                    </div>
                    
                    <a href="index.php?action=showCheckout" class="btn btn-lg w-100" 
                       style="background:linear-gradient(135deg, #C4722A 0%, #A85E22 100%);color:#fff;border:none;font-weight:600">
                        <i class="bi bi-credit-card me-2"></i>Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<script>
// Save scroll position before reload
window.addEventListener('beforeunload', () => {
    sessionStorage.setItem('scrollPos', window.scrollY);
});

// Restore scroll position after reload
window.addEventListener('load', () => {
    const scrollPos = sessionStorage.getItem('scrollPos');
    if (scrollPos) {
        window.scrollTo(0, parseInt(scrollPos));
        sessionStorage.removeItem('scrollPos');
    }
});

function updateQuantity(cartId, change, currentQty) {
    const newQty = currentQty + change;
    if (newQty < 1) return;
    
    fetch('index.php?action=updateCartQuantity', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({cart_id: cartId, quantity: newQty})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Smooth reload without losing scroll position
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error updating quantity:', error);
        alert('Failed to update quantity. Please try again.');
    });
}

function removeItem(cartId) {
    if (!confirm('Remove this item from cart?')) return;
    
    fetch('index.php?action=removeCartItem', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({cart_id: cartId})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>

<!-- Floating Grocery List Button (Items to Buy Locally) - ONLY ON SHOPPING CART PAGE -->
<a href="index.php?action=groceryLists" class="floating-grocery-list-btn" title="Items to Buy Locally">
    <i class="bi bi-clipboard-check"></i>
    <?php if (isset($_groceryListCount) && $_groceryListCount > 0): ?>
    <span class="floating-badge"><?= $_groceryListCount ?></span>
    <?php endif; ?>
</a>
<style>
.floating-grocery-list-btn {
    position: fixed;
    top: 5.5rem;
    right: 2rem;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #fff;
    border: 3px solid #fff;
    box-shadow: 0 4px 16px rgba(245,158,11,.4), 0 2px 8px rgba(0,0,0,.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    cursor: pointer;
    transition: all .3s ease;
    z-index: 998;
    text-decoration: none;
}

.floating-grocery-list-btn:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 6px 20px rgba(245,158,11,.5), 0 3px 10px rgba(0,0,0,.25);
    color: #fff;
}

.floating-grocery-list-btn:active {
    transform: translateY(0) scale(1);
}

.floating-grocery-list-btn .floating-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
    color: #fff;
    border-radius: 50%;
    min-width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
    font-weight: 800;
    padding: 0 6px;
    border: 2px solid #fff;
    box-shadow: 0 2px 6px rgba(220,38,38,.5);
    animation: pulse-floating 2s infinite;
}

@keyframes pulse-floating {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.15); }
}

@media (max-width: 768px) {
    .floating-grocery-list-btn {
        width: 50px;
        height: 50px;
        font-size: 1.3rem;
        top: 4.5rem;
        right: 1rem;
    }
    
    .floating-grocery-list-btn .floating-badge {
        min-width: 22px;
        height: 22px;
        font-size: .7rem;
        top: -6px;
        right: -6px;
    }
}
</style>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
