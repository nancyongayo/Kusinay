<?php
$pageTitle = 'Supermarket';
$activeNav = 'grocery';
require_once __DIR__ . '/../templates/mother_layout.php';
?>

<style>
.aisle-header {
    background: linear-gradient(135deg, rgba(196,114,42,.08) 0%, rgba(196,114,42,.03) 100%);
    border-left: 4px solid #C4722A;
    padding: 1rem 1.5rem;
    border-radius: .5rem;
    margin-bottom: 1.5rem;
}

.supermarket-product {
    border: 1px solid rgba(0,0,0,.08);
    border-radius: .75rem;
    transition: all .3s;
    background: #fff;
    height: 100%;
}

.supermarket-product:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,.1);
    transform: translateY(-2px);
}

.product-img-super {
    width: 100%;
    height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: .75rem .75rem 0 0;
    font-size: 2.5rem;
    border-bottom: 1px solid rgba(0,0,0,.05);
}

.product-img-super img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: .75rem .75rem 0 0;
}

.srp-badge {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: #fff;
    padding: .25rem .6rem;
    border-radius: .5rem;
    font-size: .7rem;
    font-weight: 600;
    letter-spacing: .02em;
}

.price-super {
    font-size: 1.3rem;
    font-weight: 700;
    color: #16a34a;
}

.add-cart-btn {
    background: linear-gradient(135deg, #C4722A 0%, #A85E22 100%);
    border: none;
    color: #fff;
    font-weight: 600;
    transition: all .3s;
}

.add-cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(196,114,42,.3);
    color: #fff;
}

/* Floating Cart Button */
.floating-cart-btn {
    position: fixed;
    top: 5.5rem;
    right: 2rem;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #C4722A 0%, #A85E22 100%);
    color: #fff;
    border: 3px solid #fff;
    box-shadow: 0 4px 16px rgba(196,114,42,.4), 0 2px 8px rgba(0,0,0,.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    cursor: pointer;
    transition: all .3s ease;
    z-index: 999;
    text-decoration: none;
}

.floating-cart-btn:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 6px 20px rgba(196,114,42,.5), 0 3px 10px rgba(0,0,0,.25);
    color: #fff;
}

.floating-cart-btn .cart-count-badge {
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
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.15); }
}

.floating-cart-btn:active {
    transform: translateY(0) scale(1);
}

@media (max-width: 768px) {
    .floating-cart-btn {
        width: 50px;
        height: 50px;
        font-size: 1.3rem;
        top: 4.5rem;
        right: 1rem;
    }
    
    .floating-cart-btn .cart-count-badge {
        min-width: 22px;
        height: 22px;
        font-size: .7rem;
        top: -6px;
        right: -6px;
    }
}
</style>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="font-size:.9rem">
        <li class="breadcrumb-item"><a href="index.php?action=groceryMode" style="color:#C4722A">Grocery</a></li>
        <li class="breadcrumb-item active">Supermarket</li>
    </ol>
</nav>

<div class="mb-4">
    <h4 class="fw-bold mb-1" style="color:#C4722A">🏪 Supermarket</h4>
    <p class="text-muted mb-0" style="font-size:.95rem">Browse aisles with government SRP prices</p>
</div>

<!-- Search & Filter -->
<div class="card mb-4" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1rem">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="action" value="supermarket">
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
                    <option value="">All Aisles</option>
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

<?php if (empty($products)): ?>
<div class="text-center py-5">
    <i class="bi bi-inbox" style="font-size:4rem;color:#ccc"></i>
    <p class="text-muted mt-3">No products found.</p>
    <a href="index.php?action=supermarket" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-clockwise me-1"></i>Clear Filters
    </a>
</div>
<?php else: ?>
    <?php
    // Group products by category (aisle)
    $byCategory = [];
    foreach ($products as $product) {
        $cat = $product['category'] ?? 'Other';
        if (!isset($byCategory[$cat])) {
            $byCategory[$cat] = [];
        }
        $byCategory[$cat][] = $product;
    }
    ?>
    
    <?php foreach ($byCategory as $cat => $items): ?>
    <!-- Aisle Header -->
    <div class="aisle-header">
        <h5 class="mb-0 fw-bold d-flex align-items-center" style="color:#C4722A">
            <?php
            $aisleEmoji = match($cat) {
                'Vegetables' => '🥬',
                'Fruits' => '🍎',
                'Canned Goods' => '🥫',
                'Rice' => '🌾',
                'Dairy' => '🥛',
                'Snacks' => '🍪',
                'Condiments' => '🧂',
                'Beverages' => '🥤',
                'Instant Food' => '🍜',
                'Protein' => '🥩',
                'Spices' => '🌶️',
                'Rootcrops' => '🥔',
                default => '🛒'
            };
            echo $aisleEmoji;
            ?>
            <span class="ms-2">Aisle: <?= htmlspecialchars($cat) ?></span>
            <span class="badge bg-secondary ms-2"><?= count($items) ?> items</span>
        </h5>
    </div>
    
    <div class="row g-3 mb-5">
        <?php foreach ($items as $product): ?>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="supermarket-product">
                <div class="product-img-super">
                    <?php if (!empty($product['product_image_url'])): ?>
                        <img src="<?= htmlspecialchars($product['product_image_url']) ?>" 
                             alt="<?= htmlspecialchars($product['product_name']) ?>"
                             onerror="this.style.display='none'; this.parentElement.innerHTML='<?php
                                // Product emoji based on name
                                $productEmoji = match(true) {
                                    str_contains(strtolower($product['product_name']), 'rice') => '🍚',
                                    str_contains(strtolower($product['product_name']), 'sardine') => '🐟',
                                    str_contains(strtolower($product['product_name']), 'milk') => '🥛',
                                    str_contains(strtolower($product['product_name']), 'egg') => '🥚',
                                    str_contains(strtolower($product['product_name']), 'oil') => '🫗',
                                    str_contains(strtolower($product['product_name']), 'soy') => '🍶',
                                    str_contains(strtolower($product['product_name']), 'vinegar') => '🍶',
                                    str_contains(strtolower($product['product_name']), 'sugar') => '🧂',
                                    str_contains(strtolower($product['product_name']), 'salt') => '🧂',
                                    str_contains(strtolower($product['product_name']), 'coffee') => '☕',
                                    str_contains(strtolower($product['product_name']), 'noodle') => '🍜',
                                    str_contains(strtolower($product['product_name']), 'corned') => '🥫',
                                    default => '📦'
                                };
                                echo $productEmoji;
                             ?>';">
                    <?php else: ?>
                        <?php
                        // Product emoji based on name
                        $productEmoji = match(true) {
                            str_contains(strtolower($product['product_name']), 'rice') => '🍚',
                            str_contains(strtolower($product['product_name']), 'sardine') => '🐟',
                            str_contains(strtolower($product['product_name']), 'milk') => '🥛',
                            str_contains(strtolower($product['product_name']), 'egg') => '🥚',
                            str_contains(strtolower($product['product_name']), 'oil') => '🫗',
                            str_contains(strtolower($product['product_name']), 'soy') => '🍶',
                            str_contains(strtolower($product['product_name']), 'vinegar') => '🍶',
                            str_contains(strtolower($product['product_name']), 'sugar') => '🧂',
                            str_contains(strtolower($product['product_name']), 'salt') => '🧂',
                            str_contains(strtolower($product['product_name']), 'coffee') => '☕',
                            str_contains(strtolower($product['product_name']), 'noodle') => '🍜',
                            str_contains(strtolower($product['product_name']), 'corned') => '🥫',
                            default => '📦'
                        };
                        echo $productEmoji;
                        ?>
                    <?php endif; ?>
                </div>
                
                <div class="p-2">
                    <div class="mb-2">
                        <span class="srp-badge">
                            <i class="bi bi-shield-check me-1"></i>SRP
                        </span>
                    </div>
                    
                    <h6 class="fw-bold mb-0" style="font-size:.85rem;line-height:1.2;color:#333">
                        <?= htmlspecialchars($product['product_name']) ?>
                        <?php if (!empty($product['product_variant'])): ?>
                        <br><small class="text-muted" style="font-size:.75rem"><?= htmlspecialchars($product['product_variant']) ?></small>
                        <?php endif; ?>
                    </h6>
                    
                    <div class="my-2">
                        <div class="price-super">
                            ₱<?= number_format($product['price'], 2) ?>
                        </div>
                        <small class="text-muted" style="font-size:.7rem">per <?= htmlspecialchars($product['unit']) ?></small>
                    </div>
                    
                    <button class="btn btn-sm w-100 add-cart-btn" 
                            onclick="addToCart(<?= $product['product_id'] ?>, '<?= htmlspecialchars($product['product_name'], ENT_QUOTES) ?>', <?= $product['price'] ?>, '<?= htmlspecialchars($product['unit'], ENT_QUOTES) ?>')">
                        <i class="bi bi-cart-plus me-1"></i>Add
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
    
    <div class="text-center mb-4">
        <p class="text-muted">
            <i class="bi bi-info-circle me-1"></i>
            Showing <?= count($products) ?> product(s) with government SRP prices
        </p>
    </div>
<?php endif; ?>

<!-- Info Banner -->
<div class="alert alert-info" style="background:rgba(59,130,246,.05);border:1px solid rgba(59,130,246,.2);border-radius:1rem">
    <div class="d-flex gap-3 align-items-start">
        <i class="bi bi-info-circle-fill" style="color:#3b82f6;font-size:1.5rem"></i>
        <div>
            <h6 class="fw-bold mb-2" style="color:#1e40af">About SRP Prices</h6>
            <p class="mb-0 small">
                These are Government Suggested Retail Prices (SRP) from DTI monitoring. 
                Actual supermarket prices may vary slightly. Use these as reference for fair pricing.
            </p>
        </div>
    </div>
</div>

<!-- Floating Cart Button -->
<a href="index.php?action=viewCart" class="floating-cart-btn" id="floatingCartBtn" style="display:none">
    <i class="bi bi-cart3"></i>
    <span class="cart-count-badge" id="floatingCartCount">0</span>
</a>

<script>
function addToCart(productId, productName, price, unit) {
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    btn.disabled = true;
    
    fetch('index.php?action=addToCart', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            product_id: productId,
            product_type: 'srp',
            product_name: productName,
            quantity: 1,
            unit: unit,
            price: price
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Added!';
            btn.style.background = '#16a34a';
            
            // Update floating cart button
            updateFloatingCart(data.cart_count);
            
            // Show toast
            showToast('success', data.message);
            
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.style.background = '';
                btn.disabled = false;
            }, 2000);
        } else {
            alert(data.message);
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add to cart');
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    });
}

function updateFloatingCart(count) {
    const floatingBtn = document.getElementById('floatingCartBtn');
    const floatingCount = document.getElementById('floatingCartCount');
    
    if (count > 0) {
        floatingBtn.style.display = 'flex';
        floatingCount.textContent = count;
    } else {
        floatingBtn.style.display = 'none';
    }
}

function loadCartCount() {
    fetch('index.php?action=getCartCount')
        .then(response => response.json())
        .then(data => {
            updateFloatingCart(data.count);
        })
        .catch(error => console.error('Error loading cart count:', error));
}

// Load cart count on page load
document.addEventListener('DOMContentLoaded', loadCartCount);

function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.style.minWidth = '250px';
    toast.innerHTML = `<i class="bi bi-check-circle me-2"></i>${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
