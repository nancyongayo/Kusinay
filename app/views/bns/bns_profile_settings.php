<?php
$pageTitle = 'Profile Settings';
$activeNav = 'settings';
require_once __DIR__ . '/../templates/bns_layout.php';
?>
<style>
    .settings-card {
        background: #fff;
        border: 1.5px solid rgba(107,122,58,.15);
        border-radius: 1rem;
        overflow: hidden;
        max-width: 640px;
    }
    .settings-card-header {
        background: rgba(107,122,58,.06);
        border-bottom: 1.5px solid rgba(107,122,58,.12);
        padding: .85rem 1.35rem;
        font-weight: 700;
        font-size: .95rem;
        color: var(--kn-dark);
    }
    .settings-card-body { padding: 1.5rem 1.35rem; }
    .form-label { font-size: .92rem; font-weight: 600; color: var(--kn-dark); margin-bottom: .3rem; }
    .form-select, .form-control {
        border: 1.5px solid rgba(107,122,58,.25);
        border-radius: 8px;
        font-size: .95rem;
        padding: .5rem .85rem;
        transition: .2s;
    }
    .form-select:focus, .form-control:focus {
        border-color: var(--kn-green);
        box-shadow: 0 0 0 3px rgba(107,122,58,.12);
        outline: none;
    }
    .form-select:disabled { opacity: .5; cursor: not-allowed; }
    .hint { font-size: .8rem; color: var(--kn-muted); margin-top: .25rem; }
    .btn-save {
        background: var(--kn-green);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: .55rem 1.75rem;
        font-weight: 600;
        font-size: .95rem;
        cursor: pointer;
        transition: .2s;
    }
    .btn-save:hover { background: var(--kn-green-d); }
    .current-loc {
        background: rgba(107,122,58,.06);
        border: 1.5px solid rgba(107,122,58,.15);
        border-radius: 8px;
        padding: .65rem 1rem;
        font-size: .88rem;
        color: var(--kn-dark);
        margin-bottom: 1.25rem;
    }
    .current-loc .loc-label { color: var(--kn-muted); font-size: .78rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .3rem; }
</style>

<div class="mb-4">
    <h4 class="fw-bold mb-1">Profile Settings</h4>
    <p class="text-muted mb-0" style="font-size:.92rem">Update your barangay location used in reports.</p>
</div>

<div class="settings-card">
    <div class="settings-card-header">
        <i class="bi bi-geo-alt-fill me-2" style="color:var(--kn-green)"></i>Barangay Location
    </div>
    <div class="settings-card-body">

        <?php if ($currentBarangay || $currentMunicipality || $currentProvince): ?>
        <div class="current-loc">
            <div class="loc-label">Current Location</div>
            <div>
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
