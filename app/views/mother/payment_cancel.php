<?php
$pageTitle = 'Payment Cancelled';
$activeNav = 'grocery_lists';
require_once __DIR__ . '/../templates/mother_layout.php';
?>

<style>
.cancel-container {
    max-width: 700px;
    margin: 4rem auto;
    text-align: center;
}

.cancel-icon {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    animation: shakeX .6s ease-out;
}

.cancel-icon i {
    font-size: 4rem;
    color: #fff;
}

@keyframes shakeX {
    0%, 100% {
        transform: translateX(0);
    }
    10%, 30%, 50%, 70%, 90% {
        transform: translateX(-10px);
    }
    20%, 40%, 60%, 80% {
        transform: translateX(10px);
    }
}

.cancel-card {
    background: #fff;
    border: 2px solid rgba(255, 193, 7, .2);
    border-radius: 1.5rem;
    padding: 3rem;
    box-shadow: 0 8px 32px rgba(255, 193, 7, .1);
}
</style>

<div class="cancel-container">
    <div class="cancel-card">
        <div class="cancel-icon">
            <i class="bi bi-x-lg"></i>
        </div>
        
        <h2 class="fw-bold mb-3" style="color:#ffc107">Payment Cancelled</h2>
        <p class="text-muted mb-4" style="font-size:1.1rem">
            Your payment was cancelled. No charges were made to your account.
        </p>
        
        <div class="alert alert-warning mb-4" style="background:rgba(255,193,7,.05);border:1px solid rgba(255,193,7,.2)">
            <div class="d-flex align-items-start gap-3">
                <div><i class="bi bi-info-circle fs-4" style="color:#ffc107"></i></div>
                <div class="text-start">
                    <h6 class="fw-bold mb-2">Why was my payment cancelled?</h6>
                    <ul class="small mb-0 ps-3">
                        <li>You clicked the cancel or back button</li>
                        <li>The payment session expired</li>
                        <li>There was an issue with your payment method</li>
                        <li>You chose to review your order again</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="d-flex flex-column gap-2 mt-4">
            <a href="index.php?action=groceryLists" class="btn btn-primary btn-lg" style="background:#C4722A;border:none">
                <i class="bi bi-arrow-left me-2"></i>Back to Shopping Lists
            </a>
            <a href="index.php?action=groceryListForm" class="btn btn-outline-secondary">
                <i class="bi bi-plus-circle me-2"></i>Create New Shopping List
            </a>
            <a href="index.php?action=paymentHistory" class="btn btn-link text-muted" style="text-decoration:none">
                <i class="bi bi-clock-history me-1"></i>View Payment History
            </a>
        </div>
        
        <hr class="my-4" style="opacity:.1">
        
        <div class="small text-muted">
            <p class="mb-2">
                <i class="bi bi-shield-check me-1"></i>
                No charges were made. Your payment information is safe.
            </p>
            <p class="mb-0">
                <i class="bi bi-question-circle me-1"></i>
                Need help? Contact our support team.
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
