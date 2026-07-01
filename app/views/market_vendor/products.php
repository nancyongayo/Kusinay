<?php
$pageTitle = 'My Products';
$activeNav = 'products';
include __DIR__ . '/../templates/market_vendor_layout.php';
?>

<style>
    .product-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        border: 2px solid rgba(212,165,116,.15);
        transition: all .3s ease;
        height: 100%;
    }
    .product-card:hover {
        border-color: #D4A574;
        box-shadow: 0 6px 20px rgba(212,165,116,.15);
    }
    .product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 1rem;
        background: #f5f5f5;
    }
    .product-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #333;
        margin-bottom: .5rem;
    }
    .product-category {
        display: inline-block;
        padding: .25rem .75rem;
        background: rgba(212,165,116,.1);
        color: #C09563;
        border-radius: 20px;
        font-size: .8rem;
        font-weight: 600;
        margin-bottom: .75rem;
    }
    .product-price {
        font-size: 1.5rem;
        font-weight: 800;
        color: #D4A574;
        margin-bottom: .5rem;
    }
    .product-stock {
        font-size: .9rem;
        color: #666;
        margin-bottom: 1rem;
    }
    .product-actions {
        display: flex;
        gap: .5rem;
    }
    .badge-available {
        background: #28a745;
    }
    .badge-unavailable {
        background: #dc3545;
    }
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #666;
    }
    .empty-state i {
        font-size: 4rem;
        color: #D4A574;
        margin-bottom: 1rem;
    }
</style>

<div class="mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-8">
            <h4 class="mb-2" style="color: #333; font-weight: 700;">My Products</h4>
            <p class="text-muted mb-0">Manage your product inventory</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="index.php?action=vendorProductForm" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add Product
            </a>
        </div>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="card mb-4" style="border:2px solid rgba(212,165,116,.15);border-radius:12px">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="action" value="vendorProducts">
            
            <div class="col-md-6">
                <label class="form-label fw-bold">
                    <i class="bi bi-search me-1"></i>Search Products
                </label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Search by product name..." 
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label fw-bold">
                    <i class="bi bi-funnel me-1"></i>Category
                </label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php
                    $categories = ['Vegetables', 'Fruits', 'Meat', 'Fish', 'Rice & Grains', 
                                  'Dairy', 'Eggs', 'Spices', 'Condiments', 'Others'];
                    foreach ($categories as $cat):
                        $selected = (($_GET['category'] ?? '') === $cat) ? 'selected' : '';
                    ?>
                        <option value="<?= $cat ?>" <?= $selected ?>><?= $cat ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Search
                </button>
            </div>
        </form>
        
        <?php if (!empty($_GET['search']) || !empty($_GET['category'])): ?>
        <div class="mt-3">
            <a href="index.php?action=vendorProducts" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>Clear Filters
            </a>
            <span class="text-muted ms-2">
                <?= count($products) ?> product(s) found
            </span>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if (empty($products)): ?>
    <div class="empty-state">
        <i class="bi bi-basket"></i>
        <h5 style="color: #333; font-weight: 600;">No products yet</h5>
        <p>Start adding products to your inventory to connect with families.</p>
        <a href="index.php?action=vendorProductForm" class="btn btn-primary mt-3">
            <i class="bi bi-plus-circle me-2"></i>Add Your First Product
        </a>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($products as $product): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="product-card">
                    <?php if ($product['product_image']): ?>
                        <img src="<?= htmlspecialchars($product['product_image']) ?>" 
                             alt="<?= htmlspecialchars($product['product_name']) ?>" 
                             class="product-image">
                    <?php else: ?>
                        <div class="product-image d-flex align-items-center justify-content-center bg-light">
                            <i class="bi bi-image" style="font-size: 3rem; color: #ccc;"></i>
                        </div>
                    <?php endif; ?>

                    <div class="product-category"><?= htmlspecialchars($product['category'] ?? 'Uncategorized') ?></div>
                    
                    <div class="product-name"><?= htmlspecialchars($product['product_name']) ?></div>
                    
                    <div class="product-price">₱<?= number_format($product['price_per_unit'], 2) ?> / <?= htmlspecialchars($product['unit']) ?></div>
                    
                    <div class="product-stock">
                        Stock: <strong><?= $product['stock_quantity'] ?></strong> <?= htmlspecialchars($product['unit']) ?>
                    </div>

                    <div class="mb-2">
                        <span class="badge <?= $product['is_available'] ? 'badge-available' : 'badge-unavailable' ?>">
                            <?= $product['is_available'] ? '✓ Available' : '✗ Unavailable' ?>
                        </span>
                        <?php if ($product['is_featured']): ?>
                            <span class="badge bg-warning">★ Featured</span>
                        <?php endif; ?>
                    </div>

                    <div class="product-actions">
                        <a href="index.php?action=vendorProductForm&id=<?= $product['product_id'] ?>" 
                           class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <button onclick="toggleAvailability(<?= $product['product_id'] ?>)" 
                                class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-toggle-on"></i>
                        </button>
                        <button onclick="confirmDelete(<?= $product['product_id'] ?>, '<?= addslashes(htmlspecialchars($product['product_name'])) ?>')" 
                                class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
function toggleAvailability(productId) {
    if (confirm('Toggle product availability?')) {
        fetch(`index.php?action=toggleProductAvailability&id=${productId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
    }
}

function confirmDelete(productId, productName) {
    if (confirm(`Are you sure you want to delete "${productName}"?`)) {
        window.location.href = `index.php?action=deleteProduct&id=${productId}`;
    }
}
</script>

<?php include __DIR__ . '/../templates/market_vendor_layout_end.php'; ?>
