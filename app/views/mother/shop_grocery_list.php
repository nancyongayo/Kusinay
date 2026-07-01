<?php
$pageTitle = 'Shop Grocery List - ' . htmlspecialchars($groceryList['list_name']);
$activeNav = 'grocery_lists';
require_once __DIR__ . '/../templates/mother_layout.php';
?>

<style>
.product-card { transition: all .2s; }
.product-card:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(196,114,42,.15); }
.category-badge { padding: .25rem .75rem; border-radius: 20px; font-size: .75rem; font-weight: 600; }
</style>

<!-- Header -->
<div class="mb-4">
    <div class="d-flex align-items-center gap-3 mb-2">
        <a href="index.php?action=groceryLists" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <div class="flex-grow-1">
            <h4 class="fw-bold mb-0" style="color:#C4722A">
                🛒 Shop: <?= htmlspecialchars($groceryList['list_name']) ?>
            </h4>
            <p class="text-muted mb-0" style="font-size:.9rem">
                Add items from your grocery list to cart
            </p>
        </div>
        <?php if (!empty($matchedProducts)): ?>
        <button onclick="quickAddAllToCart()" class="btn btn-primary" style="background:#6B7D3C;border:none">
            <i class="bi bi-cart-plus me-2"></i>Quick Add All to Cart
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card" style="border:1.5px solid rgba(107,125,60,.2);border-radius:1rem">
            <div class="card-body text-center">
                <div style="font-size:2rem;margin-bottom:.5rem">✅</div>
                <h3 class="fw-bold mb-0" style="color:#6B7D3C"><?= count($matchedProducts) ?></h3>
                <small class="text-muted">Items Found</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card" style="border:1.5px solid rgba(196,114,42,.2);border-radius:1rem">
            <div class="card-body text-center">
                <div style="font-size:2rem;margin-bottom:.5rem">⚠️</div>
                <h3 class="fw-bold mb-0" style="color:#C4722A"><?= count($unmatchedItems) ?></h3>
                <small class="text-muted">Not Available</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card" style="border:1.5px solid rgba(107,125,60,.2);border-radius:1rem">
            <div class="card-body text-center">
                <div style="font-size:2rem;margin-bottom:.5rem">💰</div>
                <h3 class="fw-bold mb-0" style="color:#6B7D3C">
                    ₱<?= number_format($groceryList['total_estimated_cost'], 2) ?>
                </h3>
                <small class="text-muted">Estimated Cost</small>
            </div>
        </div>
    </div>
</div>

<!-- Matched Products -->
<?php if (!empty($matchedProducts)): ?>
<div class="mb-4">
    <h5 class="fw-bold mb-3" style="color:#6B7D3C">
        <i class="bi bi-check-circle-fill me-2"></i>Available Items (<?= count($matchedProducts) ?>)
    </h5>
    
    <div class="row g-3">
        <?php foreach ($matchedProducts as $match): ?>
        <?php 
        $product = $match['product']; 
        $gItem = $match['grocery_item'];
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="card product-card h-100" style="border:1.5px solid rgba(107,125,60,.15);border-radius:1rem">
                <!-- Product Image -->
                <?php if (!empty($product['product_image_url'])): ?>
                <img src="<?= htmlspecialchars($product['product_image_url']) ?>" 
                     class="card-img-top" 
                     style="height:200px;object-fit:cover;border-radius:1rem 1rem 0 0"
                     alt="<?= htmlspecialchars($product['product_name']) ?>">
                <?php else: ?>
                <div style="height:200px;background:linear-gradient(135deg, rgba(107,125,60,.1) 0%, rgba(196,114,42,.1) 100%);border-radius:1rem 1rem 0 0;display:flex;align-items:center;justify-content:center">
                    <span style="font-size:3rem;opacity:.3">📦</span>
                </div>
                <?php endif; ?>
                
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="category-badge" style="background:rgba(107,125,60,.1);color:#6B7D3C">
                            <?= htmlspecialchars($product['category']) ?>
                        </span>
                        <span class="badge bg-success">In Stock</span>
                    </div>
                    
                    <h6 class="fw-bold mb-1" style="color:#333">
                        <?= htmlspecialchars($product['product_name']) ?>
                    </h6>
                    
                    <?php if (!empty($product['product_variant'])): ?>
                    <small class="text-muted d-block mb-2"><?= htmlspecialchars($product['product_variant']) ?></small>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="fw-bold" style="color:#6B7D3C;font-size:1.25rem">
                                ₱<?= number_format($product['price'], 2) ?>
                            </div>
                            <small class="text-muted">per <?= htmlspecialchars($product['unit']) ?></small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Need:</small>
                            <span class="fw-bold" style="color:#C4722A">
                                <?= $gItem['quantity'] ?> <?= htmlspecialchars($gItem['unit']) ?>
                            </span>
                        </div>
                    </div>
                    
                    <button class="btn btn-sm w-100 add-to-cart-btn" 
                            style="background:#6B7D3C;color:white;border:none"
                            data-product-id="<?= $product['product_id'] ?>"
                            data-product-type="<?= $product['source'] ?>"
                            data-product-name="<?= htmlspecialchars($product['product_name']) ?>"
                            data-quantity="<?= $gItem['quantity'] ?>"
                            data-unit="<?= htmlspecialchars($product['unit']) ?>"
                            data-price="<?= $product['price'] ?>">
                        <i class="bi bi-cart-plus me-2"></i>Add to Cart
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Unmatched Items -->
<?php if (!empty($unmatchedItems)): ?>
<div class="mb-4">
    <h5 class="fw-bold mb-3" style="color:#C4722A">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>Items Not Available Online (<?= count($unmatchedItems) ?>)
    </h5>
    
    <div class="alert" style="background:rgba(196,114,42,.1);border:1.5px solid rgba(196,114,42,.2);border-radius:1rem;color:#C4722A">
        <p class="mb-2"><strong>These items are not currently available in our online supermarket:</strong></p>
        <ul class="mb-0">
            <?php foreach ($unmatchedItems as $item): ?>
            <li><?= htmlspecialchars($item['product_name']) ?> (<?= $item['quantity'] ?> <?= htmlspecialchars($item['unit']) ?>)</li>
            <?php endforeach; ?>
        </ul>
        <p class="mt-3 mb-0"><small>💡 You may need to purchase these from the wet market or mark them as purchased separately.</small></p>
    </div>
</div>
<?php endif; ?>

<script>
// Add to cart function
document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const productId = this.dataset.productId;
        const productType = this.dataset.productType;
        const productName = this.dataset.productName;
        const quantity = this.dataset.quantity;
        const unit = this.dataset.unit;
        const price = this.dataset.price;
        
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('product_type', productType);
        formData.append('product_name', productName);
        formData.append('quantity', quantity);
        formData.append('unit', unit);
        formData.append('price', price);
        
        const originalText = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';
        
        fetch('index.php?action=addToCart', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.innerHTML = '<i class="bi bi-check-circle me-2"></i>Added!';
                this.style.background = '#198754';
                
                // Show success notification
                showNotification('✅ Added to cart!', 'success');
                
                // Update cart badge if exists
                updateCartBadge();
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.background = '#6B7D3C';
                    this.disabled = false;
                }, 2000);
            } else {
                this.innerHTML = originalText;
                this.disabled = false;
                showNotification('❌ ' + (data.message || 'Failed to add to cart'), 'error');
            }
        })
        .catch(err => {
            console.error(err);
            this.innerHTML = originalText;
            this.disabled = false;
            showNotification('❌ Network error', 'error');
        });
    });
});

// Quick add all to cart
function quickAddAllToCart() {
    const buttons = document.querySelectorAll('.add-to-cart-btn');
    let addedCount = 0;
    let totalCount = buttons.length;
    
    showNotification('🛒 Adding ' + totalCount + ' items to cart...', 'info');
    
    buttons.forEach((btn, index) => {
        setTimeout(() => {
            btn.click();
            addedCount++;
            
            if (addedCount === totalCount) {
                setTimeout(() => {
                    showNotification('✅ All items added to cart!', 'success');
                }, 500);
            }
        }, index * 300); // Stagger the requests
    });
}

// Simple notification system
function showNotification(message, type) {
    const colors = {
        success: '#198754',
        error: '#dc3545',
        info: '#0dcaf0'
    };
    
    const notif = document.createElement('div');
    notif.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${colors[type] || '#198754'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,.15);
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    `;
    notif.textContent = message;
    
    document.body.appendChild(notif);
    
    setTimeout(() => {
        notif.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => notif.remove(), 300);
    }, 3000);
}

// Update cart badge
function updateCartBadge() {
    fetch('index.php?action=getCartCount')
        .then(res => res.json())
        .then(data => {
            const badge = document.querySelector('.cart-badge');
            if (badge && data.count) {
                badge.textContent = data.count;
                badge.style.display = 'inline-block';
            }
        });
}
</script>

<style>
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
@keyframes slideOut {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}
</style>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
