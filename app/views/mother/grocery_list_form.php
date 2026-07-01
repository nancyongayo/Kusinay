<?php
$pageTitle = $groceryList ? 'Edit Shopping List' : 'New Shopping List';
$activeNav = 'grocery_lists';
require_once __DIR__ . '/../templates/mother_layout.php';
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="font-size:.9rem">
        <li class="breadcrumb-item"><a href="index.php?action=groceryLists" style="color:#C4722A">Shopping Lists</a></li>
        <li class="breadcrumb-item active"><?= $pageTitle ?></li>
    </ol>
</nav>

<div class="mb-4">
    <h4 class="fw-bold mb-1" style="color:#C4722A">🛒 <?= $pageTitle ?></h4>
    <p class="text-muted mb-0" style="font-size:.9rem">Create and manage your shopping list</p>
</div>

<form method="POST" action="index.php?action=saveGroceryList" id="groceryListForm">
    <?php if ($groceryList): ?>
    <input type="hidden" name="grocery_list_id" value="<?= $groceryList['grocery_list_id'] ?>">
    <?php endif; ?>
    
    <div class="row g-3">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="card mb-3" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1rem">
                <div class="card-header" style="background:rgba(196,114,42,.05);border-bottom:1.5px solid rgba(196,114,42,.1);border-radius:1rem 1rem 0 0">
                    <h6 class="mb-0 fw-bold" style="color:#C4722A">
                        <i class="bi bi-info-circle me-2"></i>List Details
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">List Name <span class="text-danger">*</span></label>
                            <input type="text" name="list_name" class="form-control" required
                                   placeholder="e.g., Weekly Groceries, Market Day"
                                   value="<?= htmlspecialchars($groceryList['list_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" name="list_date" class="form-control"
                                   value="<?= $groceryList['list_date'] ?? date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="Draft" <?= ($groceryList['status'] ?? 'Active') === 'Draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="Active" <?= ($groceryList['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Completed" <?= ($groceryList['status'] ?? '') === 'Completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Estimated Cost</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" name="total_estimated_cost" class="form-control" 
                                       step="0.01" readonly
                                       value="<?= number_format($groceryList['total_estimated_cost'] ?? 0, 2, '.', '') ?>"
                                       style="background:#f8f9fa">
                            </div>
                            <small class="text-muted">Auto-calculated from items</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2"
                                      placeholder="Additional notes about this shopping list..."><?= htmlspecialchars($groceryList['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Items Section -->
            <?php if ($groceryList): ?>
            <div class="card" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1rem">
                <div class="card-header d-flex justify-content-between align-items-center" 
                     style="background:rgba(196,114,42,.05);border-bottom:1.5px solid rgba(196,114,42,.1);border-radius:1rem 1rem 0 0">
                    <h6 class="mb-0 fw-bold" style="color:#C4722A">
                        <i class="bi bi-basket me-2"></i>Shopping Items
                    </h6>
                    <button type="button" class="btn btn-sm btn-primary" onclick="showAddItemModal()" 
                            style="background:#C4722A;border:none">
                        <i class="bi bi-plus-circle me-1"></i>Add Item
                    </button>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($items)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox" style="font-size:2.5rem;opacity:.3"></i>
                        <p class="mb-0 mt-2">No items yet. Click "Add Item" to start.</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background:rgba(196,114,42,.03)">
                                <tr>
                                    <th width="5%"></th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th width="12%">Quantity</th>
                                    <th width="12%">Unit</th>
                                    <th width="15%">Est. Price</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTable">
                                <?php foreach ($items as $item): ?>
                                <tr id="item-<?= $item['item_id'] ?>">
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input" 
                                               <?= $item['is_purchased'] ? 'checked' : '' ?>
                                               onchange="togglePurchased(<?= $item['item_id'] ?>, this.checked)">
                                    </td>
                                    <td class="<?= $item['is_purchased'] ? 'text-decoration-line-through text-muted' : 'fw-semibold' ?>">
                                        <?= htmlspecialchars($item['product_name']) ?>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($item['category'] ?: 'N/A') ?></span></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= htmlspecialchars($item['unit']) ?></td>
                                    <td class="fw-semibold" style="color:#C4722A">
                                        <?php if ($item['estimated_price']): ?>
                                            ₱<?= number_format($item['estimated_price'], 2) ?>
                                            <br>
                                            <small class="text-muted" style="font-size:.75rem">
                                                <?php
                                                // Show price source info
                                                if (!empty($item['price_source_info'])) {
                                                    echo htmlspecialchars($item['price_source_info']);
                                                } else {
                                                    echo 'Estimated';
                                                }
                                                ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span><br>
                                            <small class="text-muted" style="font-size:.75rem">No price available</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteItem(<?= $item['item_id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Generate from Meal Plan -->
            <?php if (!$groceryList && !empty($mealPlans)): ?>
            <div class="card mb-3" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1rem">
                <div class="card-body">
                    <h6 class="fw-bold mb-3" style="color:#C4722A">
                        <i class="bi bi-magic me-2"></i>Generate from Meal Plan
                    </h6>
                    <p class="text-muted small mb-3">Automatically create a shopping list from your meal plan ingredients.</p>
                    <form method="POST" action="index.php?action=generateFromMealPlan">
                        <select name="meal_plan_id" class="form-select mb-2" required>
                            <option value="">Select a meal plan...</option>
                            <?php foreach ($mealPlans as $mp): ?>
                            <option value="<?= $mp['meal_plan_id'] ?>">
                                <?= htmlspecialchars($mp['plan_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary w-100" style="background:#C4722A;border:none">
                            <i class="bi bi-magic me-2"></i>Generate List
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Quick Add Categories -->
            <div class="card" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1rem">
                <div class="card-body">
                    <h6 class="fw-bold mb-3" style="color:#C4722A">
                        <i class="bi bi-info-circle me-2"></i>Product Categories
                    </h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php
                        $categories = ['Vegetables', 'Fruits', 'Meat', 'Fish', 'Grains', 'Dairy', 'Condiments', 'Snacks'];
                        foreach ($categories as $cat): ?>
                        <span class="badge" style="background:rgba(196,114,42,.1);color:#C4722A;font-weight:500">
                            <?= $cat ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="card mt-3" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1rem">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" style="background:#C4722A;border:none">
                            <i class="bi bi-save me-2"></i>Save Shopping List
                        </button>
                        
                        <a href="index.php?action=groceryLists" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:rgba(196,114,42,.05)">
                <h5 class="modal-title fw-bold" style="color:#C4722A">
                    <i class="bi bi-plus-circle me-2"></i>Add Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addItemForm">
                    <input type="hidden" name="grocery_list_id" value="<?= $groceryList['grocery_list_id'] ?? '' ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="product_name" class="form-control" required
                               placeholder="e.g., Tomatoes, Rice, Chicken">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="category" class="form-select">
                            <option value="">Select category...</option>
                            <option value="Vegetables">Vegetables</option>
                            <option value="Fruits">Fruits</option>
                            <option value="Meat">Meat</option>
                            <option value="Fish">Fish</option>
                            <option value="Grains">Grains</option>
                            <option value="Dairy">Dairy</option>
                            <option value="Condiments">Condiments</option>
                            <option value="Snacks">Snacks</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control" required step="0.01" min="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                            <select name="unit" class="form-select" required>
                                <option value="kg">kg</option>
                                <option value="pcs">pcs</option>
                                <option value="liters">liters</option>
                                <option value="bundle">bundle</option>
                                <option value="pack">pack</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Estimated Price (₱)</label>
                        <input type="number" name="estimated_price" class="form-control" step="0.01" min="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitAddItem()" style="background:#C4722A;border:none">
                    <i class="bi bi-plus-circle me-1"></i>Add Item
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let addItemModal;

document.addEventListener('DOMContentLoaded', function() {
    addItemModal = new bootstrap.Modal(document.getElementById('addItemModal'));
});

function showAddItemModal() {
    document.getElementById('addItemForm').reset();
    addItemModal.show();
}

function submitAddItem() {
    const form = document.getElementById('addItemForm');
    const formData = new FormData(form);
    
    fetch('index.php?action=addGroceryItem', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addItemModal.hide();
            location.reload();
        } else {
            alert('Error adding item: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

function togglePurchased(itemId, isPurchased) {
    const formData = new FormData();
    formData.append('item_id', itemId);
    
    const action = isPurchased ? 'markItemPurchased' : 'unmarkItemPurchased';
    
    fetch('index.php?action=' + action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating item');
        }
    });
}

function deleteItem(itemId) {
    if (!confirm('Delete this item?')) return;
    
    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('grocery_list_id', '<?= $groceryList['grocery_list_id'] ?? '' ?>');
    
    fetch('index.php?action=deleteGroceryItem', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('item-' + itemId).remove();
            location.reload();
        } else {
            alert('Error deleting item');
        }
    });
}
</script>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
