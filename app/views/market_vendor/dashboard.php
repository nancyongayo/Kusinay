<?php
$pageTitle = 'Market Vendor Dashboard';
$activeNav = 'dashboard';
include __DIR__ . '/../templates/market_vendor_layout.php';
?>

<style>
    .modern-stat-card {
        background: linear-gradient(135deg, #fff 0%, #FAF6F0 100%);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 16px rgba(212,165,116,.12);
        border: 1px solid rgba(212,165,116,.15);
        transition: all .3s ease;
    }
    .modern-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(212,165,116,.2);
    }
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        margin-bottom: 1rem;
    }
    .stat-icon.orange { background: linear-gradient(135deg, #D4A574 0%, #C09563 100%); color: #fff; }
    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: #C09563;
        line-height: 1;
        margin-bottom: .5rem;
    }
    .stat-label {
        font-size: .88rem;
        color: #666;
        font-weight: 500;
    }
    .welcome-card {
        background: linear-gradient(135deg, #D4A574 0%, #C09563 100%);
        border-radius: 20px;
        padding: 2rem;
        color: #fff;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(212,165,116,.25);
    }
    .welcome-card h1 {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: .5rem;
    }
    .welcome-card p {
        opacity: .95;
        font-size: 1rem;
    }
    .quick-action-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.25rem;
        border: 2px solid rgba(212,165,116,.15);
        transition: all .3s ease;
        text-decoration: none;
        display: block;
        color: inherit;
    }
    .quick-action-card:hover {
        border-color: #D4A574;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(212,165,116,.15);
    }
    .quick-action-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #FAF6F0 0%, #F5EFE5 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-bottom: .75rem;
        color: #D4A574;
    }
    .quick-action-title {
        font-size: 1rem;
        font-weight: 700;
        color: #333;
        margin-bottom: .25rem;
    }
    .quick-action-desc {
        font-size: .85rem;
        color: #666;
    }
</style>

<div class="welcome-card">
    <h1>🛒 Welcome, <?= htmlspecialchars($userName) ?>!</h1>
    <p>Manage your products and connect with families in your community.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="modern-stat-card">
            <div class="stat-icon orange">
                <i class="bi bi-basket-fill"></i>
            </div>
            <div class="stat-value"><?= $stats['available_products'] ?? 0 ?></div>
            <div class="stat-label">Active Products</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="modern-stat-card">
            <div class="stat-icon orange">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="stat-value"><?= $stats['total_products'] ?? 0 ?></div>
            <div class="stat-label">Total Products</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="modern-stat-card">
            <div class="stat-icon orange">
                <i class="bi bi-cart-check-fill"></i>
            </div>
            <div class="stat-value"><?= $stats['today_sales_count'] ?? 0 ?></div>
            <div class="stat-label">Sales Today</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="modern-stat-card">
            <div class="stat-icon orange">
                <i class="bi bi-currency-peso"></i>
            </div>
            <div class="stat-value">₱<?= number_format($stats['today_revenue'] ?? 0, 2) ?></div>
            <div class="stat-label">Today Revenue</div>
        </div>
    </div>
</div>

<?php if (!empty($recentOrders)): ?>
<div class="d-flex justify-content-between align-items-center mb-3 mt-4">
    <h5 class="mb-0" style="color: #333; font-weight: 700;">Recent Orders</h5>
    <a href="index.php?action=vendorOrders" class="btn btn-sm btn-outline-secondary">
        View All Orders <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                    <tr style="cursor: pointer;" onclick="window.location='index.php?action=vendorOrderDetail&id=<?= $order['order_id'] ?>'">
                        <td><strong><?= htmlspecialchars($order['order_number']) ?></strong></td>
                        <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                        <td><?= $order['vendor_items_count'] ?> item(s)</td>
                        <td><strong>₱<?= number_format($order['grand_total'], 2) ?></strong></td>
                        <td>
                            <span class="badge bg-<?= 
                                $order['order_status'] === 'completed' ? 'success' : 
                                ($order['order_status'] === 'pending' ? 'warning' : 'info') 
                            ?>">
                                <?= ucfirst($order['order_status']) ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (count($recentOrders) >= 5): ?>
        <div class="text-center mt-3 pt-3 border-top">
            <small class="text-muted">Showing last 5 orders</small>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php else: ?>
<h5 class="mb-3 mt-4" style="color: #333; font-weight: 700;">Recent Orders</h5>
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-4">
        <i class="bi bi-inbox" style="font-size: 3rem; color: #ddd;"></i>
        <p class="text-muted mt-3 mb-0">No orders yet. Your orders will appear here.</p>
    </div>
</div>
<?php endif; ?>

<h5 class="mb-3 mt-4" style="color: #333; font-weight: 700;">Quick Actions</h5>
<div class="row g-3">
    <div class="col-12 col-sm-6 col-lg-4">
        <a href="index.php?action=vendorProducts" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="bi bi-basket-fill"></i>
            </div>
            <div class="quick-action-title">Manage Products</div>
            <div class="quick-action-desc">Add, edit, or remove your products</div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
        <a href="index.php?action=vendorSalesReports" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="quick-action-title">Sales Reports</div>
            <div class="quick-action-desc">View sales statistics and analytics</div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
        <a href="index.php?action=vendorProducts&show=form" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="bi bi-plus-circle"></i>
            </div>
            <div class="quick-action-title">Add New Product</div>
            <div class="quick-action-desc">List a new product for sale</div>
        </a>
    </div>
</div>

<?php include __DIR__ . '/../templates/market_vendor_layout_end.php'; ?>
