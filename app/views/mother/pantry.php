<?php
$pageTitle = 'My Pantry';
$activeNav = 'pantry';
require_once __DIR__ . '/../templates/mother_layout.php';
?>

<style>
.pantry-stats {
    background: linear-gradient(135deg, rgba(196,114,42,.08) 0%, rgba(196,114,42,.03) 100%);
    border: 1.5px solid rgba(196,114,42,.15);
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    text-align: center;
}

.stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: #C4722A;
}

.stat-label {
    color: #6b7280;
    font-size: .9rem;
}

.category-section {
    background: #fff;
    border: 1.5px solid rgba(196,114,42,.15);
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.category-header {
    border-bottom: 2px solid rgba(196,114,42,.1);
    padding-bottom: 1rem;
    margin-bottom: 1rem;
}

.pantry-item {
    background: rgba(196,114,42,.02);
    border: 1px solid rgba(196,114,42,.1);
    border-radius: .75rem;
    padding: 1rem;
    margin-bottom: .75rem;
    transition: all .3s;
}

.pantry-item:hover {
    background: rgba(196,114,42,.05);
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(196,114,42,.1);
}

.item-quantity {
    font-size: 1.3rem;
    font-weight: 700;
    color: #16a34a;
}

.low-stock {
    color: #dc2626 !important;
}

.quantity-badge {
    background: rgba(22,163,74,.1);
    color: #16a34a;
    padding: .25rem .75rem;
    border-radius: .5rem;
    font-weight: 600;
}

.low-stock-badge {
    background: rgba(220,38,38,.1);
    color: #dc2626;
}
</style>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h4 class="fw-bold mb-1" style="color:#C4722A">
            <i class="bi bi-box-seam me-2"></i>My Pantry
        </h4>
        <p class="text-muted mb-0">Track your household food inventory</p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?action=pantryHistory" class="btn btn-outline-secondary">
            <i class="bi bi-clock-history me-2"></i>View History
        </a>
        <button class="btn" style="background:#C4722A;color:#fff" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="bi bi-plus-circle me-2"></i>Add Item
        </button>
    </div>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_SESSION['flash_success']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['flash_error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>

<!-- Statistics -->
<div class="pantry-stats">
    <div class="row text-center">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-value"><?= $totalItems ?></div>
                <div class="stat-label">Total Items</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-value"><?= $categories ?></div>
                <div class="stat-label">Categories</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-value">
                    <?php
                    $lastRestocked = '';
                    if (!empty($pantryItems)) {
                        $dates = array_column($pantryItems, 'last_replenished');
                        $lastRestocked = !empty($dates[0]) ? date('M d', strtotime(max($dates))) : 'N/A';
                    } else {
                        $lastRestocked = 'N/A';
                    }
                    echo $lastRestocked;
                    ?>
                </div>
                <div class="stat-label">Last Restocked</div>
            </div>
        </div>
    </div>
</div>

<?php if (empty($pantryItems)): ?>
<!-- Empty Pantry -->
<div class="text-center py-5">
    <i class="bi bi-box" style="font-size:5rem;color:#ccc"></i>
    <h5 class="mt-4 mb-2">Your pantry is empty</h5>
    <p class="text-muted mb-4">Start shopping or add items manually to track your inventory</p>
    <div class="d-flex gap-3 justify-content-center">
        <a href="index.php?action=supermarket" class="btn" style="background:#C4722A;color:#fff">
            <i class="bi bi-cart me-2"></i>Go Shopping
        </a>
        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="bi bi-plus-circle me-2"></i>Add Item Manually
        </button>
    </div>
</div>
<?php else: ?>

<!-- Pantry Items by Category -->
<?php foreach ($itemsByCategory as $category => $items): ?>
<div class="category-section">
    <div class="category-header">
        <h5 class="fw-bold mb-0" style="color:#C4722A">
            <?php
            $categoryIcons = [
                'Grains' => '🌾',
                'Vegetables' => '🥬',
                'Fruits' => '🍎',
                'Protein' => '🥩',
                'Dairy' => '🥛',
                'Canned Goods' => '🥫',
                'Condiments' => '🧂',
                'Beverages' => '🥤',
                'Snacks' => '🍪',
                'Instant Food' => '🍜',
                'Spices' => '🌶️',
                'Rootcrops' => '🥔',
                'Other' => '📦'
            ];
            echo $categoryIcons[$category] ?? '📦';
            ?>
            <?= htmlspecialchars($category) ?>
            <span class="badge bg-secondary ms-2"><?= count($items) ?></span>
        </h5>
    </div>
    
    <?php foreach ($items as $item): ?>
    <div class="pantry-item" id="item-<?= $item['pantry_id'] ?>">
        <div class="row align-items-center">
            <div class="col-md-5">
                <h6 class="fw-bold mb-1"><?= htmlspecialchars($item['item_name']) ?></h6>
                <?php if (!empty($item['notes'])): ?>
                <small class="text-muted"><i class="bi bi-info-circle me-1"></i><?= htmlspecialchars($item['notes']) ?></small>
                <?php endif; ?>
            </div>
            
            <div class="col-md-3 col-6 mt-2 mt-md-0">
                <span class="quantity-badge <?= $item['quantity'] < 1 ? 'low-stock-badge' : '' ?>">
                    <?= number_format($item['quantity'], 1) ?> <?= htmlspecialchars($item['unit']) ?>
                </span>
            </div>
            
            <div class="col-md-2 col-4 mt-2 mt-md-0">
                <small class="text-muted">
                    <i class="bi bi-clock me-1"></i>
                    <?= !empty($item['last_replenished']) ? date('M d', strtotime($item['last_replenished'])) : 'N/A' ?>
                </small>
            </div>
            
            <div class="col-md-2 col-2 mt-2 mt-md-0 text-end">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-success" onclick="useItem(<?= $item['pantry_id'] ?>, '<?= htmlspecialchars($item['item_name'], ENT_QUOTES) ?>', <?= $item['quantity'] ?>, '<?= htmlspecialchars($item['unit'], ENT_QUOTES) ?>')" title="Use for cooking">
                        <i class="bi bi-check-circle"></i>
                    </button>
                    <button class="btn btn-outline-secondary" onclick="editItem(<?= $item['pantry_id'] ?>, '<?= htmlspecialchars($item['item_name'], ENT_QUOTES) ?>', <?= $item['quantity'] ?>, '<?= htmlspecialchars($item['unit'], ENT_QUOTES) ?>')">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteItem(<?= $item['pantry_id'] ?>)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endforeach; ?>

<?php endif; ?>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:rgba(196,114,42,.05);border-bottom:1px solid rgba(196,114,42,.1)">
                <h5 class="modal-title fw-bold" style="color:#C4722A">
                    <i class="bi bi-plus-circle me-2"></i>Add Item to Pantry
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=addPantryItem">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Item Name *</label>
                        <input type="text" name="item_name" class="form-control" required placeholder="e.g., Rice">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category *</label>
                        <select name="category" class="form-select" required>
                            <option value="">Select category</option>
                            <option value="Grains">🌾 Grains</option>
                            <option value="Vegetables">🥬 Vegetables</option>
                            <option value="Fruits">🍎 Fruits</option>
                            <option value="Protein">🥩 Protein</option>
                            <option value="Dairy">🥛 Dairy</option>
                            <option value="Canned Goods">🥫 Canned Goods</option>
                            <option value="Condiments">🧂 Condiments</option>
                            <option value="Beverages">🥤 Beverages</option>
                            <option value="Snacks">🍪 Snacks</option>
                            <option value="Instant Food">🍜 Instant Food</option>
                            <option value="Spices">🌶️ Spices</option>
                            <option value="Rootcrops">🥔 Rootcrops</option>
                            <option value="Other">📦 Other</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Quantity *</label>
                            <input type="number" step="0.1" name="quantity" class="form-control" required placeholder="5">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Unit *</label>
                            <select name="unit" class="form-select" required>
                                <option value="kg">kg</option>
                                <option value="g">g</option>
                                <option value="L">L</option>
                                <option value="mL">mL</option>
                                <option value="pcs">pcs</option>
                                <option value="pack">pack</option>
                                <option value="can">can</option>
                                <option value="bottle">bottle</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes (Optional)</label>
                        <input type="text" name="notes" class="form-control" placeholder="e.g., Bought from supermarket">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" style="background:#C4722A;color:#fff">
                        <i class="bi bi-check-circle me-2"></i>Add Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:rgba(196,114,42,.05)">
                <h5 class="modal-title fw-bold" style="color:#C4722A">
                    <i class="bi bi-pencil me-2"></i>Update Quantity
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-pantry-id">
                <div class="mb-3">
                    <label class="form-label fw-bold">Item Name</label>
                    <input type="text" id="edit-item-name" class="form-control" readonly>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">New Quantity *</label>
                        <input type="number" step="0.1" id="edit-quantity" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Unit</label>
                        <input type="text" id="edit-unit" class="form-control" readonly>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn" style="background:#C4722A;color:#fff" onclick="saveQuantity()">
                    <i class="bi bi-check-circle me-2"></i>Update
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ✨ Use Item Modal (Process 22: Consumption) -->
<div class="modal fade" id="useItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:rgba(22,163,74,.05);border-bottom:1px solid rgba(22,163,74,.1)">
                <h5 class="modal-title fw-bold" style="color:#16a34a">
                    <i class="bi bi-check-circle me-2"></i>Use Item for Cooking
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="use-pantry-id">
                <div class="mb-3">
                    <label class="form-label fw-bold">Item Name</label>
                    <input type="text" id="use-item-name" class="form-control" readonly>
                </div>
                <div class="alert alert-info" style="background:rgba(22,163,74,.05);border:1px solid rgba(22,163,74,.1);color:#16a34a">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Available:</strong> <span id="use-available-qty"></span>
                </div>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-bold">How much did you use? *</label>
                        <input type="number" step="0.1" id="use-quantity" class="form-control" required placeholder="e.g., 2">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Unit</label>
                        <input type="text" id="use-unit" class="form-control" readonly>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Notes (Optional)</label>
                    <input type="text" id="use-notes" class="form-control" placeholder="e.g., Used for dinner">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="saveConsumption()">
                    <i class="bi bi-check-circle me-2"></i>Mark as Used
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function editItem(pantryId, itemName, quantity, unit) {
    document.getElementById('edit-pantry-id').value = pantryId;
    document.getElementById('edit-item-name').value = itemName;
    document.getElementById('edit-quantity').value = quantity;
    document.getElementById('edit-unit').value = unit;
    new bootstrap.Modal(document.getElementById('editItemModal')).show();
}

// ✨ PROCESS 22: Use item for cooking/consumption
function useItem(pantryId, itemName, quantity, unit) {
    document.getElementById('use-pantry-id').value = pantryId;
    document.getElementById('use-item-name').value = itemName;
    document.getElementById('use-available-qty').textContent = quantity + ' ' + unit;
    document.getElementById('use-quantity').value = '';
    document.getElementById('use-quantity').max = quantity;
    document.getElementById('use-unit').value = unit;
    document.getElementById('use-notes').value = 'Used for cooking';
    new bootstrap.Modal(document.getElementById('useItemModal')).show();
}

// ✨ PROCESS 22: Save consumption to pantry history
function saveConsumption() {
    const pantryId = document.getElementById('use-pantry-id').value;
    const quantity = parseFloat(document.getElementById('use-quantity').value);
    const notes = document.getElementById('use-notes').value || 'Used for cooking';
    
    if (!quantity || quantity <= 0) {
        alert('Please enter a valid quantity');
        return;
    }
    
    fetch('index.php?action=consumePantryItem', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            pantry_id: pantryId,
            quantity: quantity,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('✅ Item marked as used! Check history to view your consumption records.');
            location.reload();
        } else {
            alert('❌ ' + (data.message || 'Failed to mark item as used'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    });
}

function saveQuantity() {
    const pantryId = document.getElementById('edit-pantry-id').value;
    const quantity = document.getElementById('edit-quantity').value;
    
    fetch('index.php?action=updatePantryQuantity', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({pantry_id: pantryId, quantity: quantity})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to update quantity');
        }
    });
}

function deleteItem(pantryId) {
    if (!confirm('Remove this item from your pantry?')) return;
    
    fetch('index.php?action=deletePantryItem', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({pantry_id: pantryId})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('item-' + pantryId).remove();
            location.reload();
        } else {
            alert('Failed to delete item');
        }
    });
}
</script>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
