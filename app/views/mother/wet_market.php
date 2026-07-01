<?php
$pageTitle = 'Wet Market';
$activeNav = 'grocery';
require_once __DIR__ . '/../templates/mother_layout.php';

// Get cart count
require_once __DIR__ . '/../../models/ShoppingCartModel.php';
$cartModel = new ShoppingCartModel(getDBConnection());
$cartCount = $cartModel->getCartCount($_SESSION['user_id']);
?>

<style>
.product-card {
    border: 1.5px solid rgba(196,114,42,.15);
    border-radius: 1rem;
    transition: all .3s;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(196,114,42,.2);
    border-color: rgba(196,114,42,.3);
}

.product-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 1rem 1rem 0 0;
    background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
}

.product-placeholder {
    width: 100%;
    height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(196,114,42,.08) 0%, rgba(196,114,42,.03) 100%);
    border-radius: 1rem 1rem 0 0;
    font-size: 3rem;
    color: rgba(196,114,42,.3);
}

.vendor-badge {
    background: rgba(196,114,42,.1);
    color: #C4722A;
    padding: .25rem .75rem;
    border-radius: .5rem;
    font-size: .75rem;
    font-weight: 600;
}

.price-tag {
    font-size: 1.5rem;
    font-weight: 700;
    color: #22c55e;
}

.stock-badge {
    background: rgba(34,197,94,.1);
    color: #16a34a;
    padding: .25rem .5rem;
    border-radius: .5rem;
    font-size: .75rem;
    font-weight: 600;
}

.out-of-stock {
    background: rgba(239,68,68,.1);
    color: #dc2626;
}

/* Floating Cart Button */
.floating-cart {
    position: fixed;
    top: 5rem;
    right: 2rem;
    z-index: 1000;
}

.cart-button {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #C4722A 0%, #A85E22 100%);
    color: white;
    border: none;
    box-shadow: 0 8px 24px rgba(196,114,42,.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    cursor: pointer;
    transition: all .3s ease;
    position: relative;
}

.cart-button:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow: 0 12px 32px rgba(196,114,42,.5);
}

.cart-button:active {
    transform: translateY(-2px) scale(1.02);
}

.cart-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .9rem;
    font-weight: 700;
    border: 3px solid white;
    box-shadow: 0 4px 12px rgba(239,68,68,.4);
}

@media (max-width: 768px) {
    .floating-cart {
        top: 4rem;
        right: 1rem;
    }
    .cart-button {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    .cart-badge {
        width: 28px;
        height: 28px;
        font-size: .85rem;
    }
}
</style>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="font-size:.9rem">
        <li class="breadcrumb-item"><a href="index.php?action=groceryMode" style="color:#C4722A">Grocery</a></li>
        <li class="breadcrumb-item active">Wet Market</li>
    </ol>
</nav>

<div class="mb-4">
    <h4 class="fw-bold mb-1" style="color:#C4722A">🥬 Wet Market</h4>
    <p class="text-muted mb-0" style="font-size:.95rem">Fresh products from local market vendors</p>
</div>

<!-- Search & Filter -->
<div class="card mb-4" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1rem">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="action" value="wetMarket">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text" style="background:#fff;border-color:rgba(196,114,42,.2)">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control" placeholder="Search products..." 
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                           style="border-color:rgba(196,114,42,.2)">
                </div>
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select" style="border-color:rgba(196,114,42,.2)">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= ($category ?? '') === $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn w-100" style="background:#C4722A;color:#fff;border:none">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Products Grid -->
<?php if (empty($products)): ?>
<div class="text-center py-5">
    <i class="bi bi-inbox" style="font-size:4rem;color:#ccc"></i>
    <p class="text-muted mt-3">No products available at the moment.</p>
    <a href="index.php?action=wetMarket" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-clockwise me-1"></i>Clear Filters
    </a>
</div>
<?php else: ?>
<div class="row g-4 mb-4">
    <?php foreach ($products as $product): ?>
    <div class="col-6 col-md-4 col-lg-3">
        <div class="product-card">
            <?php if (!empty($product['product_image_url'])): ?>
            <img src="<?= htmlspecialchars($product['product_image_url']) ?>" 
                 alt="<?= htmlspecialchars($product['product_name']) ?>" 
                 class="product-image"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="product-placeholder" style="display:none">
                <?php
                // Emoji fallback based on category
                $emoji = match($product['category'] ?? 'Other') {
                    'Vegetables' => '🥬',
                    'Fruits' => '🍎',
                    'Meat' => '🥩',
                    'Fish' => '🐟',
                    'Poultry' => '🍗',
                    'Eggs' => '🥚',
                    default => '🛒'
                };
                echo $emoji;
                ?>
            </div>
            <?php elseif (!empty($product['product_image'])): ?>
            <img src="<?= htmlspecialchars($product['product_image']) ?>" 
                 alt="<?= htmlspecialchars($product['product_name']) ?>" 
                 class="product-image"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="product-placeholder" style="display:none">
                <?php
                // Emoji fallback based on category
                $emoji = match($product['category'] ?? 'Other') {
                    'Vegetables' => '🥬',
                    'Fruits' => '🍎',
                    'Meat' => '🥩',
                    'Fish' => '🐟',
                    'Poultry' => '🍗',
                    'Eggs' => '🥚',
                    default => '🛒'
                };
                echo $emoji;
                ?>
            </div>
            <?php else: ?>
            <div class="product-placeholder">
                <?php
                // Emoji based on category
                $emoji = match($product['category'] ?? 'Other') {
                    'Vegetables' => '🥬',
                    'Fruits' => '🍎',
                    'Meat' => '🥩',
                    'Fish' => '🐟',
                    'Poultry' => '🍗',
                    'Eggs' => '🥚',
                    default => '🛒'
                };
                echo $emoji;
                ?>
            </div>
            <?php endif; ?>
            
            <div class="p-3">
                <div class="mb-2">
                    <span class="vendor-badge">
                        <i class="bi bi-shop me-1"></i><?= htmlspecialchars($product['vendor_first_name'] . ' ' . $product['vendor_last_name']) ?>
                    </span>
                </div>
                
                <h6 class="fw-bold mb-1" style="color:#333"><?= htmlspecialchars($product['product_name']) ?></h6>
                
                <?php if (!empty($product['category'])): ?>
                <p class="text-muted small mb-2"><?= htmlspecialchars($product['category']) ?></p>
                <?php endif; ?>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="price-tag">
                        ₱<?= number_format($product['price_per_unit'], 2) ?>
                        <small style="font-size:.65rem;font-weight:500;color:#666">/ <?= htmlspecialchars($product['unit']) ?></small>
                    </div>
                    <?php if ($product['stock_quantity'] > 0): ?>
                    <span class="stock-badge">
                        <?= number_format($product['stock_quantity'], 0) ?> <?= htmlspecialchars($product['unit']) ?>
                    </span>
                    <?php else: ?>
                    <span class="stock-badge out-of-stock">Out of stock</span>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($product['description'])): ?>
                <p class="small text-muted mb-2" style="font-size:.8rem;line-height:1.3">
                    <?= htmlspecialchars(substr($product['description'], 0, 60)) ?><?= strlen($product['description']) > 60 ? '...' : '' ?>
                </p>
                <?php endif; ?>
                
                <button class="btn btn-sm w-100 add-to-cart-btn" 
                        style="background:rgba(196,114,42,.1);color:#C4722A;border:1px solid rgba(196,114,42,.2);font-weight:600"
                        onclick="addToCart(<?= $product['product_id'] ?>, '<?= addslashes(htmlspecialchars($product['product_name'])) ?>', <?= $product['price_per_unit'] ?>, '<?= htmlspecialchars($product['unit']) ?>')">
                    <i class="bi bi-cart-plus me-1"></i>Add to Cart
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="text-center">
    <p class="text-muted">Showing <?= count($products) ?> product(s)</p>
</div>
<?php endif; ?>

<!-- Floating Cart Button -->
<div class="floating-cart">
    <a href="index.php?action=viewCart" class="cart-button" title="View Cart">
        <i class="bi bi-cart-fill"></i>
        <?php if ($cartCount > 0): ?>
            <span class="cart-badge" id="cartBadge"><?= $cartCount ?></span>
        <?php endif; ?>
    </a>
</div>

<script>
function addToCart(productId, productName, price, unit) {
    const button = event.target.closest('button');
    
    // Show loading state
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Adding...';
    
    fetch('index.php?action=addToCart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'product_id': productId,
            'product_type': 'vendor',
            'product_name': productName,
            'quantity': 1,
            'unit': unit || 'pc',
            'price': price
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const Toast = {
                show: (message) => {
                    const toast = document.createElement('div');
                    toast.style.cssText = 'position:fixed;top:20px;right:20px;background:#22c55e;color:white;padding:1rem 1.5rem;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:9999;animation:slideIn 0.3s ease';
                    toast.innerHTML = `<i class="bi bi-check-circle me-2"></i>${message}`;
                    document.body.appendChild(toast);
                    setTimeout(() => {
                        toast.style.animation = 'slideOut 0.3s ease';
                        setTimeout(() => toast.remove(), 300);
                    }, 2000);
                }
            };
            
            Toast.show(data.message);
            
            // Update cart badge
            if (data.cart_count) {
                const badge = document.getElementById('cartBadge');
                if (badge) {
                    badge.textContent = data.cart_count;
                } else {
                    // Create badge if it doesn't exist
                    const cartButton = document.querySelector('.cart-button');
                    const newBadge = document.createElement('span');
                    newBadge.id = 'cartBadge';
                    newBadge.className = 'cart-badge';
                    newBadge.textContent = data.cart_count;
                    cartButton.appendChild(newBadge);
                }
            }
        } else {
            alert('Error: ' + data.message);
        }
        
        // Reset button
        button.disabled = false;
        button.innerHTML = '<i class="bi bi-cart-plus me-1"></i>Add to Cart';
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add to cart. Please try again.');
        button.disabled = false;
        button.innerHTML = '<i class="bi bi-cart-plus me-1"></i>Add to Cart';
    });
}
</script>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
