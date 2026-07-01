<?php
$pageTitle = 'Payment Successful';
$activeNav = 'grocery_lists';
require_once __DIR__ . '/../templates/mother_layout.php';
?>

<style>
.success-container {
    max-width: 700px;
    margin: 4rem auto;
    text-align: center;
}

.success-icon {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    animation: scaleIn .6s ease-out;
}

.success-icon i {
    font-size: 4rem;
    color: #fff;
}

@keyframes scaleIn {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.success-card {
    background: #fff;
    border: 2px solid rgba(40, 167, 69, .2);
    border-radius: 1.5rem;
    padding: 3rem;
    box-shadow: 0 8px 32px rgba(40, 167, 69, .1);
}

.confetti {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    pointer-events: none;
    z-index: 9999;
}
</style>

<div class="success-container">
    <div class="success-card">
        <div class="success-icon">
            <i class="bi bi-check-lg"></i>
        </div>
        
        <h2 class="fw-bold mb-3" style="color:#28a745">Payment Successful!</h2>
        <p class="text-muted mb-4" style="font-size:1.1rem">
            Your payment has been processed successfully. Thank you for your purchase!
        </p>
        
        <?php if ($transaction): ?>
        <div class="alert alert-success mb-4" style="background:rgba(40,167,69,.05);border:1px solid rgba(40,167,69,.2)">
            <div class="row g-3 text-start">
                <div class="col-6">
                    <div class="small text-muted">Transaction ID</div>
                    <div class="fw-bold">#<?= str_pad($transaction['transaction_id'], 8, '0', STR_PAD_LEFT) ?></div>
                </div>
                <div class="col-6">
                    <div class="small text-muted">Amount Paid</div>
                    <div class="fw-bold" style="color:#28a745">₱<?= number_format($transaction['net_amount'], 2) ?></div>
                </div>
                <div class="col-6">
                    <div class="small text-muted">Payment Method</div>
                    <div class="fw-bold text-capitalize"><?= htmlspecialchars($transaction['payment_method']) ?></div>
                </div>
                <div class="col-6">
                    <div class="small text-muted">Date & Time</div>
                    <div class="fw-bold"><?= date('M d, Y h:i A', strtotime($transaction['paid_at'] ?? $transaction['created_at'])) ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="d-flex flex-column gap-2 mt-4">
            <a href="index.php?action=groceryLists" class="btn btn-success btn-lg">
                <i class="bi bi-list-check me-2"></i>View My Shopping Lists
            </a>
            <?php if ($transaction): ?>
            <a href="index.php?action=viewTransaction&id=<?= $transaction['transaction_id'] ?>" 
               class="btn btn-outline-success">
                <i class="bi bi-receipt me-2"></i>View Receipt
            </a>
            <?php endif; ?>
            <a href="index.php?action=paymentHistory" class="btn btn-link text-muted" style="text-decoration:none">
                <i class="bi bi-clock-history me-1"></i>Payment History
            </a>
        </div>
        
        <hr class="my-4" style="opacity:.1">
        
        <div class="small text-muted">
            <p class="mb-2">
                <i class="bi bi-envelope me-1"></i>
                A confirmation email has been sent to your registered email address.
            </p>
            <p class="mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Your grocery items have been marked as purchased.
            </p>
        </div>
    </div>
</div>

<script>
// Simple confetti effect
function createConfetti() {
    const colors = ['#28a745', '#20c997', '#C4722A', '#ffc107'];
    const confettiCount = 50;
    
    for (let i = 0; i < confettiCount; i++) {
        const confetti = document.createElement('div');
        confetti.style.position = 'fixed';
        confetti.style.width = '10px';
        confetti.style.height = '10px';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.left = Math.random() * 100 + '%';
        confetti.style.top = '-10px';
        confetti.style.opacity = Math.random();
        confetti.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
        confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
        confetti.style.pointerEvents = 'none';
        confetti.style.zIndex = '9999';
        
        document.body.appendChild(confetti);
        
        const duration = Math.random() * 3 + 2;
        const delay = Math.random() * 0.5;
        
        confetti.animate([
            { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
            { transform: `translateY(${window.innerHeight + 50}px) rotate(${Math.random() * 720}deg)`, opacity: 0 }
        ], {
            duration: duration * 1000,
            delay: delay * 1000,
            easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
        }).onfinish = () => confetti.remove();
    }
}

// Trigger confetti on page load
window.addEventListener('load', function() {
    createConfetti();
    setTimeout(createConfetti, 500);
});
</script>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
