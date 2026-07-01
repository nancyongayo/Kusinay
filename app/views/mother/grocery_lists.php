<?php
$pageTitle = 'Items to Buy Locally';
$activeNav = 'grocery_lists';
require_once __DIR__ . '/../templates/mother_layout.php';

// Get all active grocery list items (not yet purchased)
try {
    $stmt = getDBConnection()->prepare("
        SELECT 
            gli.item_id,
            gli.product_name as item_name,
            gli.category,
            gli.quantity,
            gli.unit,
            gli.estimated_price,
            gl.list_name,
            gl.list_date
        FROM grocery_list_items gli
        JOIN grocery_lists gl ON gli.grocery_list_id = gl.grocery_list_id
        WHERE gl.user_id = ? 
          AND gl.status = 'Active' 
          AND gli.is_purchased = 0
        ORDER BY gl.list_date DESC, gli.product_name ASC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $missingItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug logging
    error_log("Grocery Lists View - User ID: " . $_SESSION['user_id']);
    error_log("Grocery Lists View - Items found: " . count($missingItems));
} catch (Exception $e) {
    error_log("Error fetching grocery list items: " . $e->getMessage());
    $missingItems = [];
}
?>

<div class="mb-4">
    <!-- Back to Shopping Cart Button -->
    <div class="mb-3">
        <a href="index.php?action=viewCart" 
           class="btn btn-outline-secondary" 
           style="border-radius:.75rem; padding:.6rem 1.25rem; border-width:2px; font-weight:600;">
            <i class="bi bi-arrow-left-circle me-2"></i>Back to Shopping Cart
        </a>
    </div>
    
    <h4 class="fw-bold mb-1" style="color:#C4722A">
        <i class="bi bi-clipboard-check me-2"></i>Items to Buy Locally
    </h4>
    <p class="text-muted mb-0" style="font-size:.95rem">
        These ingredients were not found online. Buy them at your local wet market.
    </p>
</div>

<?php if (empty($missingItems)): ?>
<!-- Empty State -->
<div class="card" style="border:1.5px dashed rgba(196,114,42,.2);border-radius:1rem">
    <div class="card-body text-center py-5">
        <i class="bi bi-check-circle" style="font-size:4rem;color:rgba(34,197,94,.3)"></i>
        <h5 class="mt-3 mb-2" style="color:#666">All Set!</h5>
        <p class="text-muted mb-0">
            All needed ingredients are available online or you haven't used "Shop Now" yet.
        </p>
    </div>
</div>
<?php else: ?>
<!-- Missing Items List -->
<div class="card" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1rem">
    <div class="card-header" style="background:rgba(196,114,42,.05);border-bottom:1.5px solid rgba(196,114,42,.1)">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold" style="color:#C4722A">
                <i class="bi bi-basket me-2"></i><?= count($missingItems) ?> Items Not Found Online
            </h6>
            <small class="text-muted">
                <i class="bi bi-calendar3 me-1"></i>
                Updated <?= date('M j, Y') ?>
            </small>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:.9rem">
                <thead style="background:rgba(196,114,42,.03)">
                    <tr>
                        <th class="ps-4" style="border-bottom:1.5px solid rgba(196,114,42,.1)">Ingredient</th>
                        <th style="border-bottom:1.5px solid rgba(196,114,42,.1)">Quantity</th>
                        <th style="border-bottom:1.5px solid rgba(196,114,42,.1)">From</th>
                        <th class="pe-4 text-center" style="border-bottom:1.5px solid rgba(196,114,42,.1)">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($missingItems as $item): ?>
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size:1.5rem">
                                    <?php
                                    // Emoji based on category
                                    $emoji = match(strtolower($item['category'] ?? '')) {
                                        'vegetables' => '🥬',
                                        'fruits' => '🍎',
                                        'meat' => '🥩',
                                        'fish' => '🐟',
                                        'spices' => '🌶️',
                                        'condiments' => '🧂',
                                        default => '🛒'
                                    };
                                    echo $emoji;
                                    ?>
                                </span>
                                <strong style="color:#333"><?= htmlspecialchars($item['item_name']) ?></strong>
                            </div>
                        </td>
                        <td class="py-3">
                            <span style="color:#666;font-weight:500">
                                <?= htmlspecialchars($item['quantity']) ?> <?= htmlspecialchars($item['unit']) ?>
                            </span>
                        </td>
                        <td class="py-3">
                            <small class="text-muted" style="font-size:.8rem">
                                <?= htmlspecialchars($item['list_name']) ?>
                            </small>
                        </td>
                        <td class="pe-4 py-3 text-center">
                            <button 
                                class="btn btn-sm btn-outline-danger" 
                                onclick="deleteGroceryItem(<?= $item['item_id'] ?>)"
                                title="Delete this item">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer" style="background:rgba(196,114,42,.02);border-top:1.5px solid rgba(196,114,42,.1)">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Buy these items at your local wet market
            </small>
            <a href="index.php?action=groceryMode" class="btn btn-sm" style="background:#C4722A;color:#fff">
                <i class="bi bi-shop me-1"></i>Go Shopping
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function deleteGroceryItem(itemId) {
    if (!confirm('Are you sure you want to delete this item?')) {
        return;
    }
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?action=deleteGroceryItem';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'item_id';
    input.value = itemId;
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
