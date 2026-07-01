<?php
$pageTitle = 'Sales Reports';
$activeNav = 'sales';
include __DIR__ . '/../templates/market_vendor_layout.php';
?>

<style>
    .stat-card {
        background: linear-gradient(135deg, #fff 0%, #FAF6F0 100%);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 16px rgba(212,165,116,.12);
        border: 1px solid rgba(212,165,116,.15);
        transition: all .3s ease;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(212,165,116,.2);
    }
    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: #C09563;
        margin-bottom: .5rem;
    }
    .stat-label {
        font-size: .9rem;
        color: #666;
        font-weight: 600;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0" style="color: #333; font-weight: 700;">Sales Reports</h4>
</div>

<!-- Date Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="action" value="vendorSalesReports">
            <div class="col-md-4">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" 
                       value="<?= htmlspecialchars($dateFrom) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" 
                       value="<?= htmlspecialchars($dateTo) ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-filter me-2"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Statistics -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-label">Total Sales</div>
            <div class="stat-value"><?= $salesStats['total_sales'] ?? 0 ?></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-label">Items Sold</div>
            <div class="stat-value"><?= number_format($salesStats['total_quantity_sold'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">₱<?= number_format($salesStats['total_revenue'] ?? 0, 2) ?></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-label">Avg Sale</div>
            <div class="stat-value">₱<?= number_format($salesStats['average_sale_amount'] ?? 0, 2) ?></div>
        </div>
    </div>
</div>

<!-- Top Selling Products -->
<?php if (!empty($topProducts)): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-star me-2"></i>Top Selling Products</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Sales Count</th>
                        <th>Quantity Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topProducts as $product): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($product['product_name']) ?></strong></td>
                        <td><?= htmlspecialchars($product['category']) ?></td>
                        <td><?= $product['sales_count'] ?></td>
                        <td><?= number_format($product['total_quantity_sold'] ?? 0) ?></td>
                        <td><strong>₱<?= number_format($product['total_revenue'] ?? 0, 2) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Sales History -->
<?php if (!empty($salesHistory)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Sales History</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Buyer</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salesHistory as $sale): ?>
                    <tr>
                        <td><?= date('M d, Y h:i A', strtotime($sale['sale_date'])) ?></td>
                        <td><?= htmlspecialchars($sale['product_name']) ?></td>
                        <td><?= htmlspecialchars(($sale['buyer_first_name'] ?? 'N/A') . ' ' . ($sale['buyer_last_name'] ?? '')) ?></td>
                        <td><?= $sale['quantity_sold'] ?> <?= htmlspecialchars($sale['unit']) ?></td>
                        <td>₱<?= number_format($sale['price_per_unit'], 2) ?></td>
                        <td><strong>₱<?= number_format($sale['total_amount'], 2) ?></strong></td>
                        <td><?= htmlspecialchars($sale['payment_method']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>No sales found for the selected period.
</div>
<?php endif; ?>

<?php include __DIR__ . '/../templates/market_vendor_layout_end.php'; ?>
