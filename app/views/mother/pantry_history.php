<?php
$pageTitle = 'Pantry History';
$activeNav = 'pantry';
require_once __DIR__ . '/../templates/mother_layout.php';
?>

<style>
.history-card {
    background: #fff;
    border: 1.5px solid rgba(196,114,42,.15);
    border-radius: 1rem;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: all .3s;
}

.history-card:hover {
    box-shadow: 0 4px 12px rgba(196,114,42,.1);
    transform: translateX(4px);
}

.change-badge {
    padding: .35rem .75rem;
    border-radius: .5rem;
    font-weight: 600;
    font-size: .85rem;
}

.badge-replenishment {
    background: rgba(22,163,74,.1);
    color: #16a34a;
}

.badge-consumption {
    background: rgba(220,38,38,.1);
    color: #dc2626;
}

.badge-adjustment {
    background: rgba(59,130,246,.1);
    color: #2563eb;
}

.badge-expired {
    background: rgba(107,114,128,.1);
    color: #4b5563;
}

.timeline-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: .5rem;
}

.dot-replenishment { background: #16a34a; }
.dot-consumption { background: #dc2626; }
.dot-adjustment { background: #2563eb; }
.dot-expired { background: #6b7280; }

.quantity-change {
    font-size: 1.25rem;
    font-weight: 700;
}

.positive { color: #16a34a; }
.negative { color: #dc2626; }
</style>

<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1" style="color:#C4722A">
                <i class="bi bi-clock-history me-2"></i>Pantry History
            </h4>
            <p class="text-muted mb-0">Track all pantry activities and meal consumption</p>
        </div>
        <a href="index.php?action=pantry" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Pantry
        </a>
    </div>
</div>

<?php if (empty($history)): ?>
<!-- No History -->
<div class="text-center py-5">
    <i class="bi bi-clock-history" style="font-size:5rem;color:#ccc"></i>
    <h5 class="mt-4 mb-2">No History Yet</h5>
    <p class="text-muted mb-4">Start using items from your pantry to track consumption</p>
    <a href="index.php?action=pantry" class="btn" style="background:#C4722A;color:#fff">
        <i class="bi bi-box-seam me-2"></i>Go to Pantry
    </a>
</div>
<?php else: ?>

<!-- Filter Buttons -->
<div class="mb-4 d-flex gap-2 flex-wrap">
    <button class="btn btn-sm btn-outline-secondary filter-btn active" data-filter="all">
        All Activities
    </button>
    <button class="btn btn-sm btn-outline-success filter-btn" data-filter="Replenishment">
        <span class="timeline-dot dot-replenishment"></span>Replenishment
    </button>
    <button class="btn btn-sm btn-outline-danger filter-btn" data-filter="Consumption">
        <span class="timeline-dot dot-consumption"></span>Consumption
    </button>
    <button class="btn btn-sm btn-outline-primary filter-btn" data-filter="Manual Adjustment">
        <span class="timeline-dot dot-adjustment"></span>Adjustments
    </button>
    <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="Expired">
        <span class="timeline-dot dot-expired"></span>Expired
    </button>
</div>

<!-- History Timeline -->
<div class="history-timeline">
    <?php 
    $currentDate = '';
    foreach ($history as $record): 
        $recordDate = date('F j, Y', strtotime($record['action_date']));
        if ($recordDate != $currentDate):
            if ($currentDate != '') echo '</div>'; // Close previous date group
            $currentDate = $recordDate;
    ?>
    <div class="mb-3">
        <h6 class="fw-bold text-muted mb-3">
            <i class="bi bi-calendar3 me-2"></i><?= $recordDate ?>
        </h6>
    </div>
    <?php endif; ?>

    <div class="history-card" data-type="<?= htmlspecialchars($record['change_type']) ?>">
        <div class="row align-items-center">
            <div class="col-md-1 text-center">
                <?php
                $icons = [
                    'Replenishment' => 'bi-plus-circle-fill text-success',
                    'Consumption' => 'bi-dash-circle-fill text-danger',
                    'Manual Adjustment' => 'bi-pencil-fill text-primary',
                    'Expired' => 'bi-x-circle-fill text-secondary'
                ];
                $icon = $icons[$record['change_type']] ?? 'bi-circle-fill';
                ?>
                <i class="bi <?= $icon ?>" style="font-size:1.5rem"></i>
            </div>

            <div class="col-md-5 col-12 mb-2 mb-md-0">
                <h6 class="fw-bold mb-1"><?= htmlspecialchars($record['item_name']) ?></h6>
                <small class="text-muted">
                    <i class="bi bi-tag me-1"></i><?= htmlspecialchars($record['category']) ?>
                </small>
            </div>

            <div class="col-md-2 col-6">
                <span class="change-badge badge-<?= strtolower(str_replace(' ', '-', $record['change_type'])) ?>">
                    <?= htmlspecialchars($record['change_type']) ?>
                </span>
            </div>

            <div class="col-md-2 col-6 text-center">
                <div class="quantity-change <?= $record['quantity_change'] > 0 ? 'positive' : 'negative' ?>">
                    <?= $record['quantity_change'] > 0 ? '+' : '' ?><?= number_format($record['quantity_change'], 1) ?>
                    <small><?= htmlspecialchars($record['unit']) ?></small>
                </div>
            </div>

            <div class="col-md-2 col-12 mt-2 mt-md-0">
                <small class="text-muted d-block">
                    <i class="bi bi-clock me-1"></i><?= date('g:i A', strtotime($record['action_date'])) ?>
                </small>
                <small class="text-muted d-block">
                    <i class="bi bi-person me-1"></i><?= htmlspecialchars($record['first_name']) ?>
                </small>
            </div>
        </div>

        <?php if (!empty($record['notes'])): ?>
        <div class="mt-2 pt-2 border-top" style="border-color:rgba(196,114,42,.1)!important">
            <small class="text-muted">
                <i class="bi bi-chat-left-text me-1"></i><?= htmlspecialchars($record['notes']) ?>
            </small>
        </div>
        <?php endif; ?>
    </div>

    <?php endforeach; ?>
    <?php if ($currentDate != '') echo '</div>'; // Close last date group ?>
</div>

<!-- Statistics Summary -->
<div class="card mt-4" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1rem">
    <div class="card-body">
        <h6 class="fw-bold mb-3" style="color:#C4722A">
            <i class="bi bi-bar-chart me-2"></i>Summary
        </h6>
        <div class="row text-center">
            <div class="col-6 col-md-3 mb-3">
                <div class="fw-bold" style="font-size:1.5rem;color:#16a34a">
                    <?= count(array_filter($history, fn($h) => $h['change_type'] === 'Replenishment')) ?>
                </div>
                <small class="text-muted">Replenishments</small>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="fw-bold" style="font-size:1.5rem;color:#dc2626">
                    <?= count(array_filter($history, fn($h) => $h['change_type'] === 'Consumption')) ?>
                </div>
                <small class="text-muted">Consumptions</small>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="fw-bold" style="font-size:1.5rem;color:#2563eb">
                    <?= count(array_filter($history, fn($h) => $h['change_type'] === 'Manual Adjustment')) ?>
                </div>
                <small class="text-muted">Adjustments</small>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="fw-bold" style="font-size:1.5rem;color:#6b7280">
                    <?= count(array_filter($history, fn($h) => $h['change_type'] === 'Expired')) ?>
                </div>
                <small class="text-muted">Expired</small>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<script>
// Filter functionality
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Update active state
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const filter = this.dataset.filter;
        
        // Show/hide history cards
        document.querySelectorAll('.history-card').forEach(card => {
            if (filter === 'all' || card.dataset.type === filter) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
