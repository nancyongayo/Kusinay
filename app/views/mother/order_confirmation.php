<?php
$pageTitle = 'Order Confirmation';
$activeNav = 'grocery';
require_once __DIR__ . '/../templates/mother_layout.php';
?>

<style>
.success-icon {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
}

.order-card {
    background: #fff;
    border: 1.5px solid rgba(196,114,42,.15);
    border-radius: 1rem;
    padding: 1.5rem;
}

.status-badge {
    padding: .5rem 1rem;
    border-radius: .75rem;
    font-weight: 600;
    font-size: .9rem;
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
</style>

<div class="text-center mb-5">
    <div class="success-icon">
        <i class="bi bi-check-lg" style="font-size:3rem;color:#fff"></i>
    </div>
    
    <h3 class="fw-bold mb-2" style="color:#16a34a">Order Placed Successfully!</h3>
    <p class="text-muted mb-4">Thank you for your order. We'll start processing it soon.</p>
    
    <div class="mb-4">
        <h5 class="fw-bold mb-1">Order Number</h5>
        <h4 style="color:#C4722A;font-family:monospace"><?= htmlspecialchars($order['order_number']) ?></h4>
    </div>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-lg-8">
        <!-- Order Status -->
        <div class="order-card mb-4">
            <div class="row text-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h6 class="text-muted mb-2">Payment Status</h6>
                    <span class="status-badge status-<?= $order['payment_status'] ?>">
                        <?= strtoupper($order['payment_status']) ?>
                    </span>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Order Status</h6>
                    <span class="status-badge status-<?= !empty($order['order_status']) ? $order['order_status'] : 'confirmed' ?>">
                        <?php
                        $currentStatus = !empty($order['order_status']) ? $order['order_status'] : 'confirmed';
                        $statusLabels = [
                            'pending' => 'PENDING',
                            'confirmed' => 'CONFIRMED',
                            'preparing' => 'PREPARING',
                            'ready' => 'READY FOR PICKUP',
                            'completed' => 'COMPLETED',
                            'cancelled' => 'CANCELLED'
                        ];
                        echo $statusLabels[$currentStatus] ?? strtoupper($currentStatus);
                        ?>
                    </span>
                </div>
            </div>
            
            <?php 
            $currentStatus = !empty($order['order_status']) ? $order['order_status'] : 'confirmed';
            if ($currentStatus === 'confirmed'): ?>
            <div class="alert alert-info mt-3 mb-0" style="background:rgba(59,130,246,.05);border:1px solid rgba(59,130,246,.2)">
                <i class="bi bi-info-circle me-2"></i>
                <strong>What's Next?</strong> Your order is confirmed and will be prepared soon. You'll be notified when it's ready for pickup!
            </div>
            <?php elseif ($currentStatus === 'preparing'): ?>
            <div class="alert alert-warning mt-3 mb-0" style="background:rgba(245,158,11,.05);border:1px solid rgba(245,158,11,.2)">
                <i class="bi bi-box-seam me-2"></i>
                <strong>We're packing your order!</strong> Your items are being prepared. This usually takes 30-60 minutes.
            </div>
            <?php elseif ($currentStatus === 'ready'): ?>
            <div class="alert alert-success mt-3 mb-0" style="background:rgba(34,197,94,.05);border:1px solid rgba(34,197,94,.2)">
                <i class="bi bi-check-circle me-2"></i>
                <strong>Your order is ready!</strong> You can now pick it up at the supermarket.
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Order Details -->
        <div class="order-card mb-4">
            <h5 class="fw-bold mb-3" style="color:#C4722A">
                <i class="bi bi-receipt me-2"></i>Order Details
            </h5>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <p class="mb-2"><strong>Order Date:</strong></p>
                    <p class="text-muted"><?= date('F d, Y h:i A', strtotime($order['order_date'])) ?></p>
                </div>
                
                <div class="col-md-6">
                    <p class="mb-2"><strong>Payment Method:</strong></p>
                    <p class="text-muted"><?= strtoupper($order['payment_method']) ?></p>
                </div>
                
                <?php if ($order['delivery_address']): ?>
                <div class="col-md-6">
                    <p class="mb-2"><strong>Delivery Address:</strong></p>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($order['contact_number']): ?>
                <div class="col-md-6">
                    <p class="mb-2"><strong>Contact Number:</strong></p>
                    <p class="text-muted"><?= htmlspecialchars($order['contact_number']) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="order-card mb-4">
            <h5 class="fw-bold mb-3" style="color:#C4722A">
                <i class="bi bi-bag-check me-2"></i>Items Ordered
            </h5>
            
            <?php foreach ($order['items'] as $item): ?>
            <div class="d-flex justify-content-between align-items-start mb-3 pb-3" style="border-bottom:1px dashed #eee">
                <div>
                    <h6 class="mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                    <p class="text-muted small mb-0">
                        <?= $item['quantity'] ?> <?= htmlspecialchars($item['unit']) ?> × ₱<?= number_format($item['price_per_unit'], 2) ?>
                    </p>
                </div>
                <strong style="color:#C4722A">₱<?= number_format($item['subtotal'], 2) ?></strong>
            </div>
            <?php endforeach; ?>
            
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal</span>
                <strong>₱<?= number_format($order['total_amount'], 2) ?></strong>
            </div>
            
            <div class="d-flex justify-content-between mb-3 pb-3" style="border-bottom:2px solid #eee">
                <span class="text-muted">Delivery Fee</span>
                <?php if ($order['delivery_fee'] > 0): ?>
                    <strong>₱<?= number_format($order['delivery_fee'], 2) ?></strong>
                <?php else: ?>
                    <strong class="text-success">
                        <del class="text-muted small">₱50.00</del> FREE
                    </strong>
                <?php endif; ?>
            </div>
            
            <div class="d-flex justify-content-between">
                <span class="fw-bold" style="font-size:1.2rem">Total</span>
                <strong style="color:#16a34a;font-size:1.5rem">₱<?= number_format($order['grand_total'], 2) ?></strong>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="text-center">
            <a href="index.php?action=myOrders" class="btn btn-lg" style="background:#C4722A;color:#fff">
                <i class="bi bi-list-ul me-2"></i>View My Orders
            </a>
            <a href="index.php?action=supermarket" class="btn btn-lg btn-outline-secondary">
                <i class="bi bi-shop me-2"></i>Continue Shopping
            </a>
        </div>
        
        <?php if ($order['payment_status'] === 'pending' && $order['payment_method'] === 'paymongo'): ?>
        <div class="alert alert-warning mt-4" style="border-radius:1rem">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Payment Pending:</strong> Please complete your payment to process your order.
            <?php if (!empty($order['paymongo_checkout_url'])): ?>
            <br>
            <a href="<?= htmlspecialchars($order['paymongo_checkout_url']) ?>" class="btn btn-sm btn-warning mt-2">
                <i class="bi bi-credit-card me-1"></i>Complete Payment
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
