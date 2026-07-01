<?php
$pageTitle = 'My Orders';
$activeNav = 'grocery';
require_once __DIR__ . '/../templates/mother_layout.php';
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?action=groceryMode" style="color:#C4722A">Grocery</a></li>
        <li class="breadcrumb-item active">My Orders</li>
    </ol>
</nav>

<style>
.order-item {
    background: #fff;
    border: 1.5px solid rgba(196,114,42,.15);
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all .3s;
}

.order-item:hover {
    box-shadow: 0 4px 16px rgba(196,114,42,.15);
    transform: translateY(-2px);
}

.status-badge {
    padding: .4rem .8rem;
    border-radius: .5rem;
    font-size: .85rem;
    font-weight: 600;
}

.status-pending {
    background: rgba(156,163,175,.15);
    color: #6b7280;
}

.status-confirmed {
    background: rgba(59,130,246,.15);
    color: #2563eb;
}

.status-preparing {
    background: rgba(245,158,11,.15);
    color: #d97706;
}

.status-ready {
    background: rgba(34,197,94,.15);
    color: #16a34a;
}

.status-completed {
    background: rgba(34,197,94,.15);
    color: #16a34a;
}

.status-cancelled {
    background: rgba(239,68,68,.15);
    color: #dc2626;
}

.status-paid {
    background: rgba(34,197,94,.1);
    color: #16a34a;
}

.status-processing {
    background: rgba(59,130,246,.1);
    color: #2563eb;
}

.status-delivered {
    background: rgba(34,197,94,.1);
    color: #16a34a;
}
</style>

<div class="mb-4">
    <h4 class="fw-bold mb-1" style="color:#C4722A">
        <i class="bi bi-bag-check me-2"></i>My Orders
    </h4>
    <p class="text-muted mb-0">Track and view your order history</p>
</div>

<?php if (empty($orders)): ?>
<!-- No Orders -->
<div class="text-center py-5">
    <i class="bi bi-inbox" style="font-size:5rem;color:#ccc"></i>
    <h5 class="mt-4 mb-2">No orders yet</h5>
    <p class="text-muted mb-4">Start shopping to place your first order</p>
    <a href="index.php?action=supermarket" class="btn btn-lg" style="background:#C4722A;color:#fff">
        <i class="bi bi-shop me-2"></i>Start Shopping
    </a>
</div>
<?php else: ?>

<!-- Order List -->
<?php foreach ($orders as $order): ?>
<div class="order-item">
    <div class="row align-items-center">
        <div class="col-md-3">
            <p class="text-muted small mb-1">Order Number</p>
            <h6 class="fw-bold mb-2" style="color:#C4722A;font-family:monospace">
                <?= htmlspecialchars($order['order_number']) ?>
            </h6>
            <p class="small text-muted mb-0">
                <i class="bi bi-calendar me-1"></i>
                <?= date('M d, Y', strtotime($order['order_date'])) ?>
            </p>
        </div>
        
        <div class="col-md-2 mt-3 mt-md-0">
            <p class="text-muted small mb-1">Payment</p>
            <span class="status-badge status-<?= $order['payment_status'] ?>">
                <?= ucfirst($order['payment_status']) ?>
            </span>
        </div>
        
        <div class="col-md-2 mt-3 mt-md-0">
            <p class="text-muted small mb-1">Order Status</p>
            <span class="status-badge status-<?= $order['order_status'] ?>">
                <?php
                $statusLabels = [
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'preparing' => 'Preparing',
                    'ready' => 'Ready for Pickup',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled'
                ];
                echo $statusLabels[$order['order_status']] ?? ucfirst($order['order_status']);
                ?>
            </span>
        </div>
        
        <div class="col-md-2 mt-3 mt-md-0 text-center">
            <p class="text-muted small mb-1">Total</p>
            <h5 class="fw-bold mb-0" style="color:#16a34a">
                ₱<?= number_format($order['grand_total'], 2) ?>
            </h5>
        </div>
        
        <div class="col-md-3 mt-3 mt-md-0 text-end">
            <a href="index.php?action=orderConfirmation&order_id=<?= $order['order_id'] ?>" 
               class="btn btn-sm" style="background:rgba(196,114,42,.1);color:#C4722A;border:1px solid rgba(196,114,42,.2);font-weight:600">
                <i class="bi bi-eye me-1"></i>View Details
            </a>
            
            <?php if ($order['payment_status'] === 'pending' && $order['payment_method'] === 'paymongo' && !empty($order['paymongo_checkout_url'])): ?>
            <br>
            <a href="<?= htmlspecialchars($order['paymongo_checkout_url']) ?>" 
               class="btn btn-sm btn-warning mt-2">
                <i class="bi bi-credit-card me-1"></i>Pay Now
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Pagination -->
<div class="mt-4 text-center">
    <p class="text-muted">Showing <?= count($orders) ?> order(s)</p>
</div>

<?php endif; ?>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
