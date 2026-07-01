<!-- Reusable Modal Dialogs -->

<!-- Alert Modal -->
<div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="alertModalHeader">
                <h5 class="modal-title" id="alertModalTitle">
                    <i class="bi bi-info-circle me-2"></i>
                    <span id="alertModalTitleText">Alert</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="alertModalBody">
                <!-- Message will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="alertModalOkBtn">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="confirmModalHeader">
                <h5 class="modal-title" id="confirmModalTitle">
                    <i class="bi bi-question-circle me-2"></i>
                    <span id="confirmModalTitleText">Confirm</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">
                <!-- Message will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="confirmModalCancelBtn">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmModalOkBtn">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>Success
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="successModalBody">
                <!-- Message will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>Error
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="errorModalBody">
                <!-- Message will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Warning Modal -->
<div class="modal fade" id="warningModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-circle me-2"></i>Warning
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="warningModalBody">
                <!-- Message will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<style>
.modal-backdrop.show {
    opacity: 0.7;
}

.modal-content {
    border-radius: 1rem;
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.modal-header {
    border-bottom: 1px solid rgba(0,0,0,0.1);
    border-radius: 1rem 1rem 0 0;
    padding: 1.25rem 1.5rem;
}

.modal-body {
    padding: 1.5rem;
    font-size: 1rem;
    line-height: 1.6;
}

.modal-footer {
    border-top: 1px solid rgba(0,0,0,0.1);
    padding: 1rem 1.5rem;
}

#alertModalHeader {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    color: white;
}

#confirmModalHeader {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

#alertModalHeader .btn-close,
#confirmModalHeader .btn-close {
    filter: brightness(0) invert(1);
}
</style>

<script>
/**
 * Custom Modal Dialog Functions
 * Replaces alert() and confirm() with Bootstrap modals
 */

// Show alert modal
function showAlert(message, title = 'Alert', type = 'info') {
    const modal = new bootstrap.Modal(document.getElementById('alertModal'));
    document.getElementById('alertModalTitleText').textContent = title;
    document.getElementById('alertModalBody').innerHTML = formatModalMessage(message);
    
    // Change icon based on type
    const icon = document.querySelector('#alertModalTitle i');
    if (type === 'success') {
        icon.className = 'bi bi-check-circle me-2';
        document.getElementById('alertModalHeader').style.background = 'linear-gradient(135deg, #198754 0%, #146c43 100%)';
    } else if (type === 'error') {
        icon.className = 'bi bi-exclamation-triangle me-2';
        document.getElementById('alertModalHeader').style.background = 'linear-gradient(135deg, #dc3545 0%, #bb2d3b 100%)';
    } else if (type === 'warning') {
        icon.className = 'bi bi-exclamation-circle me-2';
        document.getElementById('alertModalHeader').style.background = 'linear-gradient(135deg, #ffc107 0%, #ffb300 100%)';
    } else {
        icon.className = 'bi bi-info-circle me-2';
        document.getElementById('alertModalHeader').style.background = 'linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%)';
    }
    
    modal.show();
}

// Show confirm modal
function showConfirm(message, title = 'Confirm', onConfirm = null, onCancel = null) {
    return new Promise((resolve) => {
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        const modalEl = document.getElementById('confirmModal');
        
        document.getElementById('confirmModalTitleText').textContent = title;
        document.getElementById('confirmModalBody').innerHTML = formatModalMessage(message);
        
        const okBtn = document.getElementById('confirmModalOkBtn');
        const cancelBtn = document.getElementById('confirmModalCancelBtn');
        
        // Remove old event listeners by cloning
        const newOkBtn = okBtn.cloneNode(true);
        const newCancelBtn = cancelBtn.cloneNode(true);
        okBtn.parentNode.replaceChild(newOkBtn, okBtn);
        cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
        
        // Add new event listeners
        newOkBtn.addEventListener('click', function() {
            modal.hide();
            if (onConfirm) onConfirm();
            resolve(true);
        });
        
        newCancelBtn.addEventListener('click', function() {
            modal.hide();
            if (onCancel) onCancel();
            resolve(false);
        });
        
        // Handle backdrop click
        modalEl.addEventListener('hidden.bs.modal', function handler() {
            resolve(false);
            modalEl.removeEventListener('hidden.bs.modal', handler);
        });
        
        modal.show();
    });
}

// Show success modal
function showSuccess(message, title = 'Success') {
    const modal = new bootstrap.Modal(document.getElementById('successModal'));
    document.getElementById('successModalBody').innerHTML = formatModalMessage(message);
    modal.show();
}

// Show error modal
function showError(message, title = 'Error') {
    const modal = new bootstrap.Modal(document.getElementById('errorModal'));
    document.getElementById('errorModalBody').innerHTML = formatModalMessage(message);
    modal.show();
}

// Show warning modal
function showWarning(message, title = 'Warning') {
    const modal = new bootstrap.Modal(document.getElementById('warningModal'));
    document.getElementById('warningModalBody').innerHTML = formatModalMessage(message);
    modal.show();
}

// Format message (convert newlines to <br>, preserve formatting)
function formatModalMessage(message) {
    if (typeof message === 'string') {
        return message.replace(/\n/g, '<br>');
    }
    return message;
}

// Override native alert (optional - commented out by default)
// window.alert = showAlert;

// Override native confirm (optional - commented out by default)
// window.confirm = showConfirm;
</script>
