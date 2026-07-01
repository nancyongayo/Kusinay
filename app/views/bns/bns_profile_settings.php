<?php
$pageTitle = 'Profile Settings';
$activeNav = 'settings';
require_once __DIR__ . '/../templates/bns_layout.php';
?>
<style>
    .settings-card {
        background: #fff;
        border: none;
        border-radius: 16px;
        overflow: hidden;
        max-width: 720px;
        box-shadow: 0 2px 12px rgba(0,0,0,.08), 0 1px 4px rgba(0,0,0,.04);
    }
    .settings-card-header {
        background: linear-gradient(135deg, rgba(107,122,58,.08) 0%, rgba(107,122,58,.04) 100%);
        border-bottom: 1px solid rgba(107,122,58,.12);
        padding: 1.25rem 1.75rem;
        font-weight: 700;
        font-size: 1.05rem;
        color: var(--kn-dark);
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .settings-card-body { padding: 2rem 1.75rem; }
    .form-label { 
        font-size: .92rem; 
        font-weight: 600; 
        color: var(--kn-dark); 
        margin-bottom: .5rem;
        display: block;
    }
    .form-select, .form-control {
        border: 2px solid rgba(107,122,58,.2);
        border-radius: 10px;
        font-size: .95rem;
        padding: .65rem 1rem;
        transition: all .25s;
        background: #fff;
    }
    .form-select:focus, .form-control:focus {
        border-color: var(--kn-green);
        box-shadow: 0 0 0 4px rgba(107,122,58,.1);
        outline: none;
        background: #fff;
    }
    .form-select:disabled { 
        opacity: .5; 
        cursor: not-allowed;
        background: rgba(107,122,58,.03);
    }
    .hint { 
        font-size: .82rem; 
        color: var(--kn-muted); 
        margin-top: .5rem;
        display: flex;
        align-items: center;
        gap: .35rem;
    }
    .btn-save {
        background: linear-gradient(135deg, var(--kn-green) 0%, var(--kn-green-d) 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: .7rem 2rem;
        font-weight: 600;
        font-size: .95rem;
        cursor: pointer;
        transition: all .25s;
        box-shadow: 0 2px 8px rgba(107,122,58,.25);
        display: inline-flex;
        align-items: center;
        gap: .5rem;
    }
    .btn-save:hover { 
        background: linear-gradient(135deg, var(--kn-green-d) 0%, var(--kn-green) 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(107,122,58,.35);
    }
    .btn-save:active {
        transform: translateY(0);
    }
    .btn-save:disabled {
        opacity: .5;
        cursor: not-allowed;
        transform: none;
    }
    .current-loc {
        background: linear-gradient(135deg, rgba(107,122,58,.08) 0%, rgba(107,122,58,.04) 100%);
        border: 2px solid rgba(107,122,58,.15);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        font-size: .92rem;
        color: var(--kn-dark);
        margin-bottom: 1.75rem;
    }
    .current-loc .loc-label { 
        color: var(--kn-muted); 
        font-size: .78rem; 
        font-weight: 700; 
        text-transform: uppercase; 
        letter-spacing: .08em; 
        margin-bottom: .5rem;
    }
    .current-loc .loc-value {
        font-weight: 600;
        font-size: 1rem;
        color: var(--kn-green);
    }
</style>

<div class="mb-5">
    <h4 class="fw-bold mb-1" style="font-size:1.75rem;color:var(--kn-dark);letter-spacing:-0.02em">Profile Settings</h4>
    <p class="text-muted mb-0" style="font-size:.92rem;color:var(--kn-muted)">Update your barangay location used in reports.</p>
</div>

<div class="settings-card">
    <div class="settings-card-header">
        <i class="bi bi-geo-alt-fill me-2" style="color:var(--kn-green)"></i>Barangay Location
    </div>
    <div class="settings-card-body">

        <?php if ($currentBarangay || $currentMunicipality || $currentProvince): ?>
        <div class="current-loc">
            <div class="loc-label">Current Location</div>
            <div class="loc-value">
                <?= htmlspecialchars(implode(', ', array_filter([
                    $currentBarangay,
                    $currentMunicipality,
                    $currentProvince
                ]))) ?: '<span style="color:var(--kn-muted)">Not set</span>' ?>
            </div>
        </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=saveBnsLocation" id="locationForm">
            <?php echo \Security::csrfField(); ?>

            <div class="row g-3">
                <!-- Region -->
                <div class="col-12 col-sm-6">
                    <label class="form-label" for="region">Region</label>
                    <select id="region" class="form-select">
                        <option value="">— Select Region —</option>
                    </select>
                </div>

                <!-- Province -->
                <div class="col-12 col-sm-6">
                    <label class="form-label" for="province">Province</label>
                    <select id="province" class="form-select" disabled>
                        <option value="">— Select Province —</option>
                    </select>
                </div>

                <!-- City/Municipality -->
                <div class="col-12 col-sm-6">
                    <label class="form-label" for="city">City / Municipality</label>
                    <select id="city" class="form-select" disabled>
                        <option value="">— Select City/Municipality —</option>
                    </select>
                </div>

                <!-- Barangay -->
                <div class="col-12 col-sm-6">
                    <label class="form-label" for="barangay">
                        Barangay <span style="color:var(--kn-orange)">*</span>
                    </label>
                    <select id="barangay" name="barangay_code" class="form-select" disabled required>
                        <option value="">— Select Barangay —</option>
                    </select>
                    <div class="hint"><i class="bi bi-info-circle me-1"></i>This will be used in all your reports (Form C, Accomplishment Report, etc.)</div>
                </div>
            </div>

            <div class="mt-4 d-flex align-items-center gap-3">
                <button type="submit" class="btn-save" id="saveBtn" disabled>
                    <i class="bi bi-check-lg me-1"></i> Save Location
                </button>
                <a href="index.php?action=bnsDashboard" style="font-size:.9rem;color:var(--kn-muted);text-decoration:none">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>

<script>
const BASE = 'https://psgc.gitlab.io/api';

// Current stored code (to auto-select on load)
const storedCode = <?= json_encode($barangayCode ?? '') ?>;

// ── Helpers ──────────────────────────────────────────────────────────────────
async function fetchJSON(url) {
    const res = await fetch(url);
    if (!res.ok) throw new Error('PSGC fetch failed: ' + res.status);
    return res.json();
}

function populateSelect(sel, items, valueKey, labelKey, selectedVal = '') {
    sel.innerHTML = '<option value="">— Select —</option>';
    items
        .sort((a, b) => a[labelKey].localeCompare(b[labelKey]))
        .forEach(item => {
            const opt = document.createElement('option');
            opt.value       = item[valueKey];
            opt.textContent = item[labelKey];
            if (item[valueKey] === selectedVal) opt.selected = true;
            sel.appendChild(opt);
        });
    sel.disabled = false;
}

function resetFrom(...selects) {
    selects.forEach(s => {
        s.innerHTML = '<option value="">— Select —</option>';
        s.disabled  = true;
    });
    checkReady();
}

// ── Load Regions on page load ─────────────────────────────────────────────────
(async () => {
    try {
        const regions = await fetchJSON(`${BASE}/regions.json`);

        if (storedCode) {
            // Auto-resolve the stored barangay code to pre-select all dropdowns
            const brgy = await fetchJSON(`${BASE}/barangays/${storedCode}.json`);
            const cityCode = brgy.cityCode || brgy.municipalityCode || brgy.districtCode || '';
            let regionCode = '', provCode = '';

            if (cityCode) {
                const city = await fetchJSON(`${BASE}/cities-municipalities/${cityCode}.json`);
                provCode   = city.provinceCode || city.districtCode || '';
                regionCode = city.regionCode || '';
            }

            // Populate region and pre-select
            populateSelect(document.getElementById('region'), regions, 'code', 'name', regionCode);

            if (regionCode) {
                const provinces = await fetchJSON(`${BASE}/regions/${regionCode}/provinces.json`);
                populateSelect(document.getElementById('province'), provinces, 'code', 'name', provCode);
            }

            if (provCode) {
                const cities = await fetchJSON(`${BASE}/provinces/${provCode}/cities-municipalities.json`);
                populateSelect(document.getElementById('city'), cities, 'code', 'name', cityCode);
            }

            if (cityCode) {
                const barangays = await fetchJSON(`${BASE}/cities-municipalities/${cityCode}/barangays.json`);
                populateSelect(document.getElementById('barangay'), barangays, 'code', 'name', storedCode);
                checkReady();
            }
        } else {
            populateSelect(document.getElementById('region'), regions, 'code', 'name');
        }
    } catch (e) {
        console.error('PSGC init error:', e);
    }
})();

// ── Cascade: Region → Province ────────────────────────────────────────────────
document.getElementById('region').addEventListener('change', async function () {
    resetFrom(
        document.getElementById('province'),
        document.getElementById('city'),
        document.getElementById('barangay')
    );
    if (!this.value) return;
    try {
        const data = await fetchJSON(`${BASE}/regions/${this.value}/provinces.json`);
        populateSelect(document.getElementById('province'), data, 'code', 'name');
    } catch (e) { console.error(e); }
});

// ── Cascade: Province → City/Municipality ────────────────────────────────────
document.getElementById('province').addEventListener('change', async function () {
    resetFrom(
        document.getElementById('city'),
        document.getElementById('barangay')
    );
    if (!this.value) return;
    try {
        const data = await fetchJSON(`${BASE}/provinces/${this.value}/cities-municipalities.json`);
        populateSelect(document.getElementById('city'), data, 'code', 'name');
    } catch (e) { console.error(e); }
});

// ── Cascade: City → Barangay ──────────────────────────────────────────────────
document.getElementById('city').addEventListener('change', async function () {
    resetFrom(document.getElementById('barangay'));
    if (!this.value) return;
    try {
        const data = await fetchJSON(`${BASE}/cities-municipalities/${this.value}/barangays.json`);
        populateSelect(document.getElementById('barangay'), data, 'code', 'name');
    } catch (e) { console.error(e); }
});

// ── Enable save when barangay is selected ─────────────────────────────────────
document.getElementById('barangay').addEventListener('change', checkReady);

function checkReady() {
    const val = document.getElementById('barangay').value;
    document.getElementById('saveBtn').disabled = !val;
}
</script>
