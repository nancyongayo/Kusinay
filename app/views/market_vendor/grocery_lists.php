<?php
$pageTitle = 'Grocery Lists';
$activeNav = 'grocery_list';
include __DIR__ . '/../templates/market_vendor_layout.php';
?>

<style>
    .list-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        border: 2px solid rgba(255,140,66,.15);
        transition: all .3s ease;
        margin-bottom: 1rem;
    }
    .list-card:hover {
        border-color: #FF8C42;
        box-shadow: 0 6px 20px rgba(255,140,66,.15);
    }
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #666;
    }
    .empty-state i {
        font-size: 4rem;
        color: #FF8C42;
        margin-bottom: 1rem;
    }
</style>

<div class="mb-4">
    <h4 class="mb-2" style="color: #333; font-weight: 700;">Grocery Lists from Families</h4>
    <p style="color: #666; font-size: .9rem;">View what families in your community need to buy</p>
</div>

<div class="empty-state">
    <i class="bi bi-list-check"></i>
    <h5 style="color: #333; font-weight: 600;">No grocery lists available</h5>
    <p>When families create grocery lists, they will appear here.</p>
</div>

<?php include __DIR__ . '/../templates/market_vendor_layout_end.php'; ?>
