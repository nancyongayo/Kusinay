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
<?php include __DIR__ . '/../templates/header.php'; ?>

<style>
    /* Wider card for role selection */
    @media (min-width: 768px) {
        .col-12.col-sm-10.col-md-8.col-lg-5 { max-width: 640px !important; }
    }
    .role-card {
        border: 2px solid rgba(107,122,58,0.18);
        border-radius: 1rem;
        padding: 1rem 1.25rem;
        cursor: pointer;
        transition: border-color .2s, background .2s, transform .15s;
        background: rgba(245,237,214,0.25);
    }
    .role-card:hover { border-color: var(--kn-green); background: rgba(107,122,58,0.07); transform: translateY(-2px); }
    .role-card input[type="radio"] { display: none; }
    .role-card.selected { border-color: var(--kn-orange); background: rgba(196,114,42,0.08); }
    .role-icon { font-size: 1.6rem; }
    .role-title { font-weight: 700; color: var(--kn-dark); font-size: .95rem; }
    .role-desc  { font-size: .78rem; color: var(--kn-muted); }
    .step-badge {
        display: inline-flex; align-items: center; justify-content: center;
        width: 28px; height: 28px; border-radius: 50%;
        background: var(--kn-green); color: #fff;
        font-size: .8rem; font-weight: 700; flex-shrink: 0;
    }
    .step-label { font-weight: 600; color: var(--kn-dark); font-size: .9rem; }
    select.form-select:disabled { opacity: .5; }
</style>

<div class="page-card p-4 p-md-5">

    <div class="brand-logo mb-3">
        <div class="logo-circle"><img src="public/images/logo.png" alt="KusiNay Logo"></div>
    </div>

    <h2 class="h5 fw-bold text-center mb-1" style="color:var(--kn-dark)">
        Welcome, <?= htmlspecialchars($userName) ?>!
    </h2>
    <p class="text-center mb-4" style="color:var(--kn-muted);font-size:.88rem">
        Complete your profile to get started.
    </p>

    <?php if ($errors): ?>
        <div class="alert-kn-error p-3 mb-3">
            <?php foreach ($errors as $e): ?>
                <div>? <?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=saveRoleSelection" method="POST" id="profileForm">

        <!-- Step 1: Role -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="step-badge">1</span>
            <span class="step-label">Select your role</span>
        </div>

        <div class="row g-2 mb-4" id="roleCards">
            <?php
            $roles = [
                ['id' => 2, 'icon' => ' ', 'title' => 'Nutrition Officer II',  'desc' => 'Validates and approves nutrition reports'],
                ['id' => 3, 'icon' => ' ', 'title' => 'BNS Staff',             'desc' => 'Encodes nutrition data for the barangay'],
                ['id' => 4, 'icon' => ' ', 'title' => 'Mother / Father', 'desc' => 'Tracks family nutrition and meal plans'],
            ];
            foreach ($roles as $r): ?>
            <div class="col-12 col-sm-4">
                <label class="role-card d-flex flex-column align-items-center text-center gap-1 w-100"
                       data-role="<?= $r['id'] ?>">
                    <input type="radio" name="role_id" value="<?= $r['id'] ?>" required>
                    <span class="role-icon"><?= $r['icon'] ?></span>
                    <span class="role-title"><?= $r['title'] ?></span>
                    <span class="role-desc"><?= $r['desc'] ?></span>
                </label>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Step 2: Address via PSGC API -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="step-badge">2</span>
            <span class="step-label">Your Barangay Address</span>
        </div>

        <div class="row g-2 mb-3">
            <!-- Region -->
            <div class="col-12 col-sm-6">
                <label class="form-label" for="region">Region</label>
                <select id="region" class="form-select" required>
                    <option value="">Select Region </option>
                </select>
            </div>
            <!-- Province -->
            <div class="col-12 col-sm-6">
                <label class="form-label" for="province">Province</label>
                <select id="province" class="form-select" disabled>
                    <option value=""> Select Province </option>
                </select>
            </div>
            <!-- City/Municipality -->
            <div class="col-12 col-sm-6">
                <label class="form-label" for="city">City / Municipality</label>
                <select id="city" class="form-select" disabled>
                    <option value="">Select City/Municipality </option>
                </select>
            </div>
            <!-- Barangay -->
            <div class="col-12 col-sm-6">
                <label class="form-label" for="barangay">Barangay <span style="color:var(--kn-orange)">*</span></label>
                <select id="barangay" name="barangay_code" class="form-select" disabled required>
                    <option value="">Select Barangay </option>
                </select>
            </div>
        </div>

        <!-- Street / House No. -->
        <div class="mb-4">
            <label class="form-label" for="address">Street / House No. <span style="color:var(--kn-orange)">*</span></label>
            <input type="text" id="address" name="address" class="form-control"
                   placeholder="e.g. 123 Rizal St." required>
            <small style="color:var(--kn-muted);font-size:.78rem">
                Your address is encrypted and stored securely.
            </small>
        </div>

        <!-- Hidden full address label -->
        <input type="hidden" id="full_address_label" name="address" value="">

        <button type="submit" class="btn btn-primary w-100 btn-lg" id="submitBtn" disabled>
            Complete Profile
        </button>
    </form>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>

<script>
const BASE = 'https://psgc.gitlab.io/api';

// -- Helpers ------------------------------------------------------------------
async function fetchJSON(url) {
    const res = await fetch(url);
    if (!res.ok) throw new Error('PSGC API error: ' + res.status);
    return res.json();
}

function populateSelect(sel, items, valueKey, labelKey) {
    sel.innerHTML = '<option value="">� Select �</option>';
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
        s.innerHTML = '<option value="">� Select �</option>';
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

// -- Cascade: Region ? Province ------------------------------------------------
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

// -- Cascade: Province ? City/Municipality ------------------------------------
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

// -- Cascade: City ? Barangay --------------------------------------------------
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
