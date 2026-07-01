<!-- KusiNay Uniform Button Styles -->
<style>
/* ============================================================================
   KUSINAY BUTTON SYSTEM - Based on Color Palette
   ============================================================================
   Colors:
   - Primary Green: #5A7038 → #4A5D2E
   - Orange Accent: #C4722A → #a85e22
   - Cream: #F5EDD6
   - Dark: #3D4A1E
   ============================================================================ */

/* PRIMARY BUTTON - Green Gradient (Main Actions) */
.btn-kn-primary {
    background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%);
    color: #F5EDD6;
    border: none;
    border-radius: 8px;
    padding: .65rem 1.5rem;
    font-weight: 600;
    font-size: .9rem;
    box-shadow: 0 4px 12px rgba(90, 112, 56, 0.3);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    text-decoration: none;
}

.btn-kn-primary:hover {
    background: linear-gradient(135deg, #4A5D2E 0%, #3A4A24 100%);
    color: #F5EDD6;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(90, 112, 56, 0.4);
}

.btn-kn-primary:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(90, 112, 56, 0.3);
}

/* SECONDARY BUTTON - Orange Gradient (Important Actions) */
.btn-kn-secondary {
    background: linear-gradient(135deg, #C4722A 0%, #a85e22 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: .65rem 1.5rem;
    font-weight: 600;
    font-size: .9rem;
    box-shadow: 0 4px 12px rgba(196, 114, 42, 0.3);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    text-decoration: none;
}

.btn-kn-secondary:hover {
    background: linear-gradient(135deg, #a85e22 0%, #8d4e1c 100%);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(196, 114, 42, 0.4);
}

.btn-kn-secondary:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(196, 114, 42, 0.3);
}

/* OUTLINE BUTTON - Green Outline (Secondary Actions) */
.btn-kn-outline {
    background: transparent;
    color: #5A7038;
    border: 1.5px solid #5A7038;
    border-radius: 8px;
    padding: .6rem 1.5rem;
    font-weight: 600;
    font-size: .9rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    text-decoration: none;
}

.btn-kn-outline:hover {
    background: #5A7038;
    color: #F5EDD6;
    border-color: #5A7038;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(90, 112, 56, 0.2);
}

.btn-kn-outline:active {
    transform: translateY(0);
}

/* DANGER BUTTON - Red (Delete/Cancel Actions) */
.btn-kn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: .65rem 1.5rem;
    font-weight: 600;
    font-size: .9rem;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    text-decoration: none;
}

.btn-kn-danger:hover {
    background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
}

.btn-kn-danger:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

/* SUCCESS BUTTON - Green Solid (Confirm Actions) */
.btn-kn-success {
    background: linear-gradient(135deg, #198754 0%, #157347 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: .65rem 1.5rem;
    font-weight: 600;
    font-size: .9rem;
    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    text-decoration: none;
}

.btn-kn-success:hover {
    background: linear-gradient(135deg, #157347 0%, #146c43 100%);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(25, 135, 84, 0.4);
}

.btn-kn-success:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(25, 135, 84, 0.3);
}

/* SMALL BUTTON VARIANTS */
.btn-kn-sm {
    padding: .5rem 1.25rem;
    font-size: .85rem;
}

/* LARGE BUTTON VARIANTS */
.btn-kn-lg {
    padding: .75rem 2rem;
    font-size: 1rem;
}

/* FULL WIDTH BUTTON */
.btn-kn-block {
    width: 100%;
    justify-content: center;
}

/* DISABLED STATE */
.btn-kn-primary:disabled,
.btn-kn-secondary:disabled,
.btn-kn-outline:disabled,
.btn-kn-danger:disabled,
.btn-kn-success:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

/* BUTTON GROUP */
.btn-kn-group {
    display: flex;
    gap: .75rem;
    flex-wrap: wrap;
}

/* FORM ELEMENTS - Matching Style */
.form-control-kn,
.form-select-kn {
    border-radius: 8px;
    border: 1.5px solid #dee2e6;
    padding: .65rem 1rem;
    font-size: .9rem;
    transition: all 0.3s ease;
}

.form-control-kn:focus,
.form-select-kn:focus {
    border-color: #5A7038;
    box-shadow: 0 0 0 0.2rem rgba(90, 112, 56, 0.25);
    outline: none;
}

/* FORM LABELS */
.form-label-kn {
    font-size: .85rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: .5rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}

.form-label-kn i {
    color: #6c757d;
}

/* LOADING STATE */
.btn-kn-loading {
    position: relative;
    pointer-events: none;
}

.btn-kn-loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid #fff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: btn-spin 0.6s linear infinite;
}

@keyframes btn-spin {
    to { transform: rotate(360deg); }
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .btn-kn-primary,
    .btn-kn-secondary,
    .btn-kn-outline,
    .btn-kn-danger,
    .btn-kn-success {
        padding: .6rem 1.25rem;
        font-size: .85rem;
    }
    
    .btn-kn-group {
        flex-direction: column;
    }
    
    .btn-kn-group > * {
        width: 100%;
    }
}

/* PRINT - Hide all buttons */
@media print {
    .btn-kn-primary,
    .btn-kn-secondary,
    .btn-kn-outline,
    .btn-kn-danger,
    .btn-kn-success,
    .btn-kn-group {
        display: none !important;
    }
}
</style>
