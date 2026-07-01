<?php
$pageTitle = 'Orders';
$activeNav = 'orders';
include __DIR__ . '/../templates/market_vendor_layout.php';
?>

<style>
    .order-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        border: 2px solid rgba(212,165,116,.15);
        transition: all .3s ease;
        margin-bottom: 1rem;
    }
    .order-card:hover {
        border-color: #D4A574;
        box-shadow: 0 4px 16px rgba(212,165,116,.15);
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0" style="color: #333; font-weight: 700;">Orders</h4>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if (empty($orders)): ?>
    <div class="empty-state">
        <i class="bi bi-cart-x"></i>
        <h5 style="color: #333; font-weight: 600;">No orders yet</h5>
        <p>Orders from customers will appear here.</p>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Payment Status</th>
                            <th>Order Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($order['order_number']) ?></strong></td>
                            <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                            <td><?= $order['vendor_items_count'] ?> item(s)</td>
                            <td><strong>₱<?= number_format($order['grand_total'], 2) ?></strong></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $order['payment_status'] === 'paid' ? 'success' : 
                                    ($order['payment_status'] === 'pending' ? 'warning' : 'secondary') 
                                ?>">
                                    <?= ucfirst($order['payment_status']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= 
                                    $order['order_status'] === 'completed' ? 'success' : 
                                    ($order['order_status'] === 'processing' ? 'info' :
                                    ($order['order_status'] === 'pending' ? 'warning' : 'secondary'))
                                ?>">
                                    <?= ucfirst($order['order_status']) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                            <td>
                                <a href="index.php?action=vendorOrderDetail&id=<?= $order['order_id'] ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../templates/market_vendor_layout_end.php'; ?>
