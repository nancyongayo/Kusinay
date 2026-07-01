<?php
$pageTitle = 'Order Details';
$activeNav = 'orders';
include __DIR__ . '/../templates/market_vendor_layout.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-receipt me-2"></i>Order Details - <?= htmlspecialchars($order['order_number']) ?>
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Customer Information</h6>
                        <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></p>
                        <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                        <p class="mb-1"><strong>Contact:</strong> <?= htmlspecialchars($order['contact_number'] ?? 'N/A') ?></p>
                        <?php if ($order['fulfillment_method'] === 'delivery'): ?>
                            <p class="mb-1"><strong>Delivery Address:</strong> <?= htmlspecialchars($order['delivery_address'] ?? 'N/A') ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Order Information</h6>
                        <p class="mb-1"><strong>Order Date:</strong> <?= date('M d, Y h:i A', strtotime($order['order_date'])) ?></p>
                        <p class="mb-1"><strong>Payment Method:</strong> <?= ucfirst($order['payment_method']) ?></p>
                        <p class="mb-1"><strong>Fulfillment:</strong> <?= ucfirst($order['fulfillment_method']) ?></p>
                        <p class="mb-1">
                            <strong>Payment Status:</strong> 
                            <span class="badge bg-<?= $order['payment_status'] === 'paid' ? 'success' : 'warning' ?>">
                                <?= ucfirst($order['payment_status']) ?>
                            </span>
                        </p>
                        <p class="mb-1">
                            <strong>Order Status:</strong> 
                            <span class="badge bg-<?= 
                                $order['order_status'] === 'completed' ? 'success' : 
                                ($order['order_status'] === 'processing' ? 'info' : 'warning')
                            ?>">
                                <?= ucfirst($order['order_status']) ?>
                            </span>
                        </p>
                    </div>
                </div>

                <h6 class="text-muted mb-3">Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td><?= $item['quantity'] ?> <?= htmlspecialchars($item['unit']) ?></td>
                                <td>₱<?= number_format($item['price_per_unit'], 2) ?></td>
                                <td>₱<?= number_format($item['subtotal'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                <td><strong>₱<?= number_format($order['total_amount'], 2) ?></strong></td>
                            </tr>
                            <?php if ($order['delivery_fee'] > 0): ?>
                            <tr>
                                <td colspan="3" class="text-end">Delivery Fee:</td>
                                <td>₱<?= number_format($order['delivery_fee'], 2) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="table-primary">
                                <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                                <td><strong>₱<?= number_format($order['grand_total'], 2) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <?php if (!empty($order['notes'])): ?>
                    <div class="alert alert-info mt-3">
                        <strong><i class="bi bi-info-circle me-2"></i>Customer Notes:</strong><br>
                        <?= htmlspecialchars($order['notes']) ?>
                    </div>
                <?php endif; ?>

                <div class="d-flex gap-2 mt-4">
                    <a href="index.php?action=vendorOrders" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/market_vendor_layout_end.php'; ?>
