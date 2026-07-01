<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
$userName = $_SESSION['user_name'] ?? 'there';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Profile - KusiNay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body style="background: #fde8d8; min-height: 100vh; font-family: 'Segoe UI', system-ui, sans-serif;">

<div class="d-flex align-items-center justify-content-center min-vh-100 py-4">

<style>
    /* CSS Variables */
    :root {
        --kn-green: #6B7A3A;
        --kn-orange: #A67C52;
        --kn-dark: #3D4A1E;
        --kn-muted: rgba(61,74,30,.55);
        --kn-cream: #F5EDD6;
    }
    
    /* Decorative background blobs */
    body::before {
        content: '';
        position: fixed;
        top: -120px; left: -120px;
        width: 420px; height: 420px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(107,122,58,0.13) 0%, transparent 70%);
        pointer-events: none;
        z-index: 0;
    }
    body::after {
        content: '';
        position: fixed;
        bottom: -100px; right: -100px;
        width: 360px; height: 360px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(166,124,82,0.06) 0%, transparent 70%);
        pointer-events: none;
        z-index: 0;
    }
    
    /* Maximum landscape/horizontal container */
    .page-card { 
        max-width: 1200px !important; 
        width: 92% !important;
        margin: 0 auto !important;
        padding: 2rem 2.5rem !important;
        background: rgba(255, 252, 245, 0.97);
        border: 1.5px solid rgba(107,122,58,0.18);
        border-radius: 1.5rem;
        box-shadow: 0 20px 60px rgba(61,74,30,0.13), 0 2px 8px rgba(196,114,42,0.06);
        position: relative;
        z-index: 1;
    }
    
    /* Brand logo */
    .brand-logo {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.35rem;
    }
    .brand-logo .logo-circle {
        width: 72px; height: 72px;
        border-radius: 50%;
        background: var(--kn-green);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 16px rgba(107,122,58,0.30);
        overflow: hidden;
    }
    .brand-logo .logo-circle img {
        width: 100%; height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    
    /* Form controls */
    .form-control, .form-select {
        border-color: rgba(107,122,58,0.22);
        background: rgba(245,237,214,0.35);
    }
    .form-control:focus, .form-select:focus {
        border-color: #A67C52;
        box-shadow: 0 0 0 0.2rem rgba(166,124,82,0.12);
        background: #fff;
    }
    
    /* Alerts */
    .alert-kn-error {
        background: rgba(166,124,82,0.08);
        border: 1px solid rgba(166,124,82,0.22);
        color: #6B5438;
        border-radius: 0.75rem;
    }
    
    /* Buttons */
    .btn-primary {
        background-color: var(--kn-green);
        border-color: var(--kn-green);
        font-weight: 600;
    }
    .btn-primary:hover, .btn-primary:focus {
        background-color: #556030;
        border-color: #556030;
    }
    
    /* Two-column layout */
    @media (min-width: 992px) {
        .two-column-layout { 
            display: flex; 
            gap: 3rem; 
            align-items: flex-start; 
        }
        .left-column { flex: 0 0 58%; }
        .right-column { 
            flex: 0 0 calc(42% - 3rem); 
            position: sticky; 
            top: 0.5rem; 
        }
    }
    
    .role-card {
        border: 2px solid rgba(107,122,58,0.18);
        border-radius: .5rem;
        padding: .85rem 1rem;
        cursor: pointer;
        transition: all .2s;
        background: rgba(245,237,214,0.25);
        height: 100%;
        display: flex;
        flex-direction: row;
        align-items: center;
        text-align: left;
        gap: 0.85rem;
    }
    .role-card:hover { 
        border-color: var(--kn-green); 
        background: rgba(107,122,58,0.07); 
        transform: translateY(-1px); 
    }
    .role-card input[type="radio"] { display: none; }
    .role-card.selected { 
        border-color: var(--kn-orange); 
        background: rgba(166,124,82,0.08); 
        box-shadow: 0 2px 8px rgba(166,124,82,0.15);
    }
    .role-icon { 
        font-size: 2.4rem; 
        flex-shrink: 0;
        width: 50px;
        text-align: center;
    }
    .role-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }
    .role-title { 
        font-weight: 700; 
        color: var(--kn-dark); 
        font-size: .92rem; 
        line-height: 1.3; 
    }
    .role-desc { 
        font-size: .8rem; 
        color: var(--kn-muted); 
        line-height: 1.35; 
    }
    .step-badge {
        display: inline-flex; 
        align-items: center; 
        justify-content: center;
        width: 28px; 
        height: 28px; 
        border-radius: 50%;
        background: var(--kn-green); 
        color: #fff;
        font-size: .85rem; 
        font-weight: 700; 
        flex-shrink: 0;
    }
    .step-label { 
        font-weight: 600; 
        color: var(--kn-dark); 
        font-size: .95rem; 
    }
    select.form-select:disabled { opacity: .5; }
    .form-label { 
        font-size: .88rem; 
        margin-bottom: .35rem; 
        font-weight: 600;
        color: var(--kn-dark);
    }
    .form-select, .form-control { 
        font-size: .9rem; 
        padding: .55rem .75rem; 
    }
    .btn-primary {
        font-size: .95rem;
        padding: .65rem 1.15rem;
        font-weight: 600;
    }
    
    /* Mobile: stack vertically */
    @media (max-width: 991px) {
        .two-column-layout { display: block; }
        .left-column, .right-column { flex: none; width: 100%; }
        .right-column { position: static; margin-top: 2rem; }
        .page-card { width: 100% !important; margin: 1rem auto !important; }
    }
</style>

<div class="page-card p-2 p-lg-3">

    <div class="brand-logo mb-1">
        <div class="logo-circle"><img src="public/images/logo.png" alt="KusiNay Logo"></div>
    </div>

    <h2 class="h6 fw-bold text-center mb-0" style="color:var(--kn-dark);font-size:.95rem">
        Welcome, <?= htmlspecialchars($userName) ?>!
    </h2>
    <p class="text-center mb-2" style="color:var(--kn-muted);font-size:.78rem">
        Complete your profile to get started.
    </p>

    <?php if ($errors): ?>
        <div class="alert-kn-error p-3 mb-3">
            <?php foreach ($errors as $e): ?>
                <div>⚠ <?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=saveRoleSelection" method="POST" id="profileForm">

        <div class="two-column-layout">
            
            <!-- LEFT COLUMN: Role Selection -->
            <div class="left-column">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="step-badge">1</span>
                    <span class="step-label">Select your role</span>
                </div>

                <div class="row g-2" id="roleCards">
                    <?php
                    $roles = [
                        ['id' => 2, 'icon' => '🏥', 'title' => 'Nutrition Officer II',  'desc' => 'Validates and approves nutrition reports'],
                        ['id' => 3, 'icon' => '📋', 'title' => 'BNS Staff',             'desc' => 'Encodes nutrition data for the barangay'],
                        ['id' => 4, 'icon' => '👨‍👩‍👧', 'title' => 'Mother / Father', 'desc' => 'Tracks family nutrition and meal plans'],
                        ['id' => 5, 'icon' => '🏛️', 'title' => 'Committee Chair on Health', 'desc' => 'Plans feeding programs for the barangay'],
                        ['id' => 6, 'icon' => '📝', 'title' => 'Committee Secretary', 'desc' => 'Records meeting minutes and documentation'],
                        ['id' => 7, 'icon' => '👔', 'title' => 'Barangay Captain', 'desc' => 'Validates and approves feeding proposals'],
                        ['id' => 8, 'icon' => '🛒', 'title' => 'Market Vendor', 'desc' => 'Manages products and grocery lists'],
                    ];
                    foreach ($roles as $r): ?>
                    <div class="col-12 col-md-6">
                        <label class="role-card w-100" data-role="<?= $r['id'] ?>">
                            <input type="radio" name="role_id" value="<?= $r['id'] ?>" required>
                            <span class="role-icon"><?= $r['icon'] ?></span>
                            <div class="role-content">
                                <span class="role-title"><?= $r['title'] ?></span>
                                <span class="role-desc"><?= $r['desc'] ?></span>
                            </div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- RIGHT COLUMN: Address Form -->
            <div class="right-column">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="step-badge">2</span>
                    <span class="step-label">Your Barangay Address</span>
                </div>

                <div class="row g-2 mb-2">
                    <!-- Region -->
                    <div class="col-12">
                        <label class="form-label" for="region">Region</label>
                        <select id="region" class="form-select" required>
                            <option value="">Select Region ▼</option>
                        </select>
                    </div>
                    <!-- Province -->
                    <div class="col-12">
                        <label class="form-label" for="province">Province</label>
                        <select id="province" class="form-select" disabled>
                            <option value="">Select Province ▼</option>
                        </select>
                    </div>
                    <!-- City/Municipality -->
                    <div class="col-12">
                        <label class="form-label" for="city">City / Municipality</label>
                        <select id="city" class="form-select" disabled>
                            <option value="">Select City/Municipality ▼</option>
                        </select>
                    </div>
                    <!-- Barangay -->
                    <div class="col-12">
                        <label class="form-label" for="barangay">Barangay <span style="color:var(--kn-orange)">*</span></label>
                        <select id="barangay" name="barangay_code" class="form-select" disabled required>
                            <option value="">Select Barangay ▼</option>
                        </select>
                    </div>
                </div>

                <!-- Street / House No. -->
                <div class="mb-2">
                    <label class="form-label" for="address">Street / House No. <span style="color:var(--kn-orange)">*</span></label>
                    <input type="text" id="address" name="address" class="form-control"
                           placeholder="e.g. 123 Rizal St." required>
                    <small style="color:var(--kn-muted);font-size:.72rem">
                        Your address is encrypted and stored securely.
                    </small>
                </div>

                <!-- Hidden full address label -->
                <input type="hidden" id="full_address_label" name="address" value="">

                <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>
                    Complete Profile
                </button>
            </div>

        </div><!-- .two-column-layout -->

    </form>
</div>

</div><!-- closing min-vh-100 -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<script>
const BASE = 'https://psgc.gitlab.io/api';

// -- Helpers ------------------------------------------------------------------
async function fetchJSON(url) {
    const res = await fetch(url);
    if (!res.ok) throw new Error('PSGC API error: ' + res.status);
    return res.json();
}

function populateSelect(sel, items, valueKey, labelKey) {
    sel.innerHTML = '<option value="">▼ Select ▼</option>';
    items
        .sort((a, b) => a[labelKey].localeCompare(b[labelKey]))
        .forEach(item => {
            const opt = document.createElement('option');
            opt.value       = item[valueKey];
            opt.textContent = item[labelKey];
            sel.appendChild(opt);
        });
    sel.disabled = false;
}

function resetFrom(selects) {
    selects.forEach(s => {
        s.innerHTML = '<option value="">▼ Select ▼</option>';
        s.disabled  = true;
    });
    checkReady();
}

// -- Load Regions on page load -------------------------------------------------
(async () => {
    try {
        const regions = await fetchJSON(`${BASE}/regions.json`);
        populateSelect(document.getElementById('region'), regions, 'code', 'name');
    } catch (e) {
        console.error(e);
    }
})();

// -- Cascade: Region → Province ------------------------------------------------
document.getElementById('region').addEventListener('change', async function () {
    const province = document.getElementById('province');
    const city     = document.getElementById('city');
    const barangay = document.getElementById('barangay');
    resetFrom([province, city, barangay]);
    if (!this.value) return;
    try {
        const data = await fetchJSON(`${BASE}/regions/${this.value}/provinces.json`);
        populateSelect(province, data, 'code', 'name');
    } catch (e) { console.error(e); }
});

// -- Cascade: Province → City/Municipality ------------------------------------
document.getElementById('province').addEventListener('change', async function () {
    const city     = document.getElementById('city');
    const barangay = document.getElementById('barangay');
    resetFrom([city, barangay]);
    if (!this.value) return;
    try {
        const data = await fetchJSON(`${BASE}/provinces/${this.value}/cities-municipalities.json`);
        populateSelect(city, data, 'code', 'name');
    } catch (e) { console.error(e); }
});

// -- Cascade: City → Barangay --------------------------------------------------
document.getElementById('city').addEventListener('change', async function () {
    const barangay = document.getElementById('barangay');
    resetFrom([barangay]);
    if (!this.value) return;
    try {
        const data = await fetchJSON(`${BASE}/cities-municipalities/${this.value}/barangays.json`);
        populateSelect(barangay, data, 'code', 'name');
    } catch (e) { console.error(e); }
});

// -- Role card selection -------------------------------------------------------
document.querySelectorAll('.role-card').forEach(card => {
    card.addEventListener('click', function () {
        document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        this.querySelector('input[type="radio"]').checked = true;
        checkReady();
    });
});

// -- Enable submit when all required fields are filled ------------------------
document.getElementById('barangay').addEventListener('change', checkReady);
document.getElementById('address').addEventListener('input', checkReady);

function checkReady() {
    const roleSelected = !!document.querySelector('input[name="role_id"]:checked');
    const barangay     = document.getElementById('barangay').value;
    const address      = document.getElementById('address').value.trim();
    document.getElementById('submitBtn').disabled = !(roleSelected && barangay && address);
}

// -- Build full address label before submit ------------------------------------
document.getElementById('profileForm').addEventListener('submit', function (e) {
    const region   = document.getElementById('region');
    const province = document.getElementById('province');
    const city     = document.getElementById('city');
    const barangay = document.getElementById('barangay');
    const street   = document.getElementById('address').value.trim();

    const parts = [
        street,
        barangay.options[barangay.selectedIndex]?.text,
        city.options[city.selectedIndex]?.text,
        province.options[province.selectedIndex]?.text,
        region.options[region.selectedIndex]?.text,
    ].filter(Boolean);

    document.getElementById('full_address_label').value = parts.join(', ');
    // Override the address field with the full label
    document.getElementById('address').name = '';
    document.getElementById('full_address_label').name = 'address';
});
</script>

