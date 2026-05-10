<?php
$pageTitle = 'Register Resident';
$activeNav = 'register_resident';
require_once __DIR__ . '/../templates/bns_layout.php';

$errors  = $_SESSION['errors'] ?? [];
$old     = $_SESSION['old']    ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

$matches  = $matches  ?? [];
$residents = $residents ?? [];

// Pre-fill from family profile "Register" button
$prefillFamilyId = (int)($_GET['prefill_family_id'] ?? 0);
$prefillName     = trim($_GET['prefill_name'] ?? '');
$prefillFirst    = $old['firstName'] ?? '';
$prefillLast     = $old['lastName']  ?? '';
if (!$prefillFirst && !$prefillLast && $prefillName) {
    $parts = explode(', ', $prefillName, 2);
    $prefillLast  = $parts[0] ?? '';
    $prefillFirst = preg_replace('/\s+[A-Z]\.\s*.*$/', '', $parts[1] ?? '');
}

// Stats
$totalResidents  = count($residents);
$pendingLogin    = count(array_filter($residents, fn($r) => $r['force_password_change']));
$profileComplete = count(array_filter($residents, fn($r) => $r['profile_complete']));
?>
<style>
/* ── Page layout ── */
.rr-grid { display:grid; grid-template-columns:380px 1fr; gap:1.5rem; align-items:start; }
@media(max-width:900px){ .rr-grid { grid-template-columns:1fr; } }

/* ── Form card ── */
.rr-form-card {
    background:#fff;
    border:1.5px solid rgba(107,122,58,.15);
    border-radius:1.25rem;
    overflow:hidden;
    position:sticky;
    top:calc(var(--topbar-h) + 1rem);
}
.rr-form-header {
    background:linear-gradient(135deg, var(--kn-green-xd) 0%, var(--kn-green) 100%);
    padding:1.5rem 1.75rem 1.25rem;
    color:#fff;
}
.rr-form-header h5 { font-size:1.1rem; font-weight:800; margin:0 0 .25rem; color:#fff; }
.rr-form-header p  { font-size:.82rem; color:rgba(245,237,214,.75); margin:0; }
.rr-form-body { padding:1.5rem 1.75rem; }

/* ── Fields ── */
.rr-label { font-size:.82rem; font-weight:700; color:var(--kn-dark); margin-bottom:.3rem; display:block; letter-spacing:.01em; }
.rr-input {
    width:100%;
    border:1.5px solid rgba(107,122,58,.2);
    border-radius:10px;
    padding:.6rem .9rem;
    font-size:.92rem;
    background:#fafaf7;
    color:var(--kn-dark);
    transition:border-color .15s, box-shadow .15s, background .15s;
}
.rr-input:focus { outline:none; border-color:var(--kn-green); box-shadow:0 0 0 3px rgba(107,122,58,.1); background:#fff; }
.rr-input::placeholder { color:rgba(61,74,30,.3); }

/* ── Submit button ── */
.rr-btn-submit {
    width:100%;
    background:linear-gradient(135deg, var(--kn-green) 0%, var(--kn-green-d) 100%);
    color:#fff;
    border:none;
    border-radius:10px;
    padding:.75rem;
    font-weight:700;
    font-size:.95rem;
    cursor:pointer;
    transition:opacity .15s, transform .1s;
    letter-spacing:.01em;
}
.rr-btn-submit:hover { opacity:.92; transform:translateY(-1px); }

/* ── Match panel ── */
.match-panel {
    border:1.5px solid rgba(196,114,42,.35);
    border-radius:.85rem;
    background:rgba(196,114,42,.05);
    padding:1rem 1.1rem;
    margin-bottom:1rem;
}
.match-item { display:flex; align-items:center; justify-content:space-between; gap:.75rem; padding:.45rem 0; border-bottom:1px solid rgba(107,122,58,.08); }
.match-item:last-child { border-bottom:none; }
.match-info strong { display:block; font-size:.88rem; color:var(--kn-dark); }
.match-info span   { font-size:.75rem; color:var(--kn-muted); }
.btn-link-family { background:var(--kn-orange); color:#fff; border:none; border-radius:6px; padding:.28rem .75rem; font-size:.78rem; font-weight:700; cursor:pointer; white-space:nowrap; }
.btn-dismiss { background:transparent; color:var(--kn-muted); border:1px solid rgba(107,122,58,.2); border-radius:6px; padding:.28rem .65rem; font-size:.78rem; cursor:pointer; }

/* ── Right panel ── */
.rr-right-panel { display:flex; flex-direction:column; gap:1rem; }

/* ── Stats row ── */
.rr-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; }
.rr-stat-card {
    background:#fff;
    border:1.5px solid rgba(107,122,58,.12);
    border-radius:1rem;
    padding:1rem 1.1rem;
    text-align:center;
}
.rr-stat-num  { font-size:1.8rem; font-weight:900; color:var(--kn-green); line-height:1; }
.rr-stat-label{ font-size:.72rem; color:var(--kn-muted); margin-top:.2rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; }

/* ── Residents table card ── */
.rr-table-card {
    background:#fff;
    border:1.5px solid rgba(107,122,58,.12);
    border-radius:1.25rem;
    overflow:hidden;
}
.rr-table-head { padding:1rem 1.25rem; border-bottom:1px solid rgba(107,122,58,.1); display:flex; align-items:center; justify-content:space-between; }
.rr-table-head h6 { font-size:.92rem; font-weight:700; color:var(--kn-dark); margin:0; }
.rr-table thead th { background:var(--kn-green); color:var(--kn-cream); font-size:.68rem; text-transform:uppercase; letter-spacing:.04em; border:none; padding:.5rem .75rem; font-weight:700; white-space:nowrap; }
.rr-table tbody td { font-size:.82rem; padding:.55rem .75rem; vertical-align:middle; border-color:rgba(107,122,58,.07); }
.rr-table tbody tr:hover { background:rgba(107,122,58,.03); }
.badge-ok      { background:rgba(107,122,58,.12); color:var(--kn-green); font-size:.68rem; font-weight:700; padding:.18em .5em; border-radius:5px; white-space:nowrap; }
.badge-pending { background:rgba(196,114,42,.12); color:#7a3a10;         font-size:.68rem; font-weight:700; padding:.18em .5em; border-radius:5px; white-space:nowrap; }
.btn-resend {
    background:var(--kn-orange); color:#fff; border:none;
    border-radius:7px; padding:.28rem .75rem; font-size:.78rem;
    font-weight:700; cursor:pointer; transition:background .15s;
    white-space:nowrap;
}
.btn-resend:hover { background:#a85e22; }
</style>

<!-- Page header -->
<div class="d-flex align-items-center gap-2 mb-4">
    <div style="width:40px;height:40px;background:var(--kn-green);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem;flex-shrink:0">
        <i class="bi bi-person-plus-fill"></i>
    </div>
    <div>
        <h4 class="fw-bold mb-0">Resident Registration</h4>
        <p class="text-muted mb-0" style="font-size:.82rem">Register residents and manage their accounts</p>
    </div>
</div>

<div class="rr-grid">

    <!-- ── LEFT: Registration Form ── -->
    <div class="rr-form-card">
        <div class="rr-form-header">
            <h5><i class="bi bi-person-plus-fill me-2"></i>Register a Resident</h5>
            <p>Enter the resident's details to create their account and send login credentials.</p>
        </div>
        <div class="rr-form-body">

            <?php if ($errors): ?>
            <div style="background:rgba(220,53,69,.08);border:1px solid rgba(220,53,69,.25);border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1.1rem">
                <?php foreach ($errors as $e): ?>
                    <div style="font-size:.85rem;color:#842029"><i class="bi bi-exclamation-triangle-fill me-1"></i><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($prefillFamilyId): ?>
            <div style="background:rgba(107,122,58,.08);border:1px solid rgba(107,122,58,.2);border-radius:.75rem;padding:.6rem .9rem;margin-bottom:1rem;font-size:.82rem;color:var(--kn-dark)">
                <i class="bi bi-link-45deg me-1" style="color:var(--kn-green)"></i>
                Will be linked to the selected family profile automatically.
            </div>
            <?php endif; ?>

            <form id="regForm" method="POST" action="index.php?action=registerResident" novalidate>
                <?= \Security::csrfField() ?>
                <input type="hidden" name="confirm_match_family_id" id="confirmMatchFamilyId" value="<?= $prefillFamilyId ?: '' ?>">
                <input type="hidden" name="match_dismissed" id="matchDismissed" value="">

                <!-- Name-match panel -->
                <div id="matchPanel" class="match-panel" style="display:<?= $matches ? 'block' : 'none' ?>">
                    <div style="font-size:.82rem;font-weight:700;color:var(--kn-orange);margin-bottom:.4rem">
                        <i class="bi bi-search me-1"></i> Possible Match Found
                    </div>
                    <p style="font-size:.78rem;color:var(--kn-muted);margin-bottom:.6rem">
                        A family member with this name exists. Link the new account to that family?
                    </p>
                    <div id="matchList">
                        <?php foreach ($matches as $m): ?>
                        <div class="match-item">
                            <div class="match-info">
                                <strong><?= htmlspecialchars($m['first_name'] . ' ' . $m['last_name']) ?></strong>
                                <span><?= $m['role'] === 'Head' ? 'Head of Family' : 'Spouse' ?><?= $m['hh_number'] ? ' · HH# ' . htmlspecialchars($m['hh_number']) : '' ?><?= $m['purok'] ? ' · Purok ' . htmlspecialchars($m['purok']) : '' ?></span>
                            </div>
                            <button type="button" class="btn-link-family" onclick="confirmLink(<?= (int)$m['family_id'] ?>)">
                                <i class="bi bi-link-45deg me-1"></i> Link
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-2 text-end">
                        <button type="button" class="btn-dismiss" onclick="dismissMatch()">No match — skip</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="rr-label" for="first_name">First Name <span style="color:var(--kn-orange)">*</span></label>
                    <input type="text" id="first_name" name="first_name" class="rr-input"
                           placeholder="e.g. Maria" required autocomplete="given-name"
                           value="<?= htmlspecialchars($prefillFirst ?: ($old['firstName'] ?? '')) ?>">
                </div>

                <div class="mb-3">
                    <label class="rr-label" for="last_name">Last Name <span style="color:var(--kn-orange)">*</span></label>
                    <input type="text" id="last_name" name="last_name" class="rr-input"
                           placeholder="e.g. Santos" required autocomplete="family-name"
                           value="<?= htmlspecialchars($prefillLast ?: ($old['lastName'] ?? '')) ?>">
                </div>

                <div class="mb-4">
                    <label class="rr-label" for="email">Email Address <span style="color:var(--kn-orange)">*</span></label>
                    <input type="email" id="email" name="email" class="rr-input"
                           placeholder="resident@example.com" required autocomplete="email"
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                    <div style="font-size:.75rem;color:var(--kn-muted);margin-top:.3rem">
                        <i class="bi bi-send me-1"></i>Credentials will be sent to this address.
                    </div>
                </div>

                <button type="submit" class="rr-btn-submit">
                    <i class="bi bi-person-plus-fill me-2"></i>Create Account &amp; Send Credentials
                </button>
            </form>
        </div>
    </div>

    <!-- ── RIGHT: Stats + Residents List ── -->
    <div class="rr-right-panel">

        <!-- Stats -->
        <div class="rr-stats">
            <div class="rr-stat-card">
                <div class="rr-stat-num"><?= $totalResidents ?></div>
                <div class="rr-stat-label">Total Residents</div>
            </div>
            <div class="rr-stat-card">
                <div class="rr-stat-num" style="color:var(--kn-orange)"><?= $pendingLogin ?></div>
                <div class="rr-stat-label">Awaiting Login</div>
            </div>
            <div class="rr-stat-card">
                <div class="rr-stat-num"><?= $profileComplete ?></div>
                <div class="rr-stat-label">Profile Complete</div>
            </div>
        </div>

        <!-- Residents table -->
        <div class="rr-table-card">
            <div class="rr-table-head">
                <h6><i class="bi bi-people-fill me-2" style="color:var(--kn-green)"></i>Registered Residents</h6>
                <span style="font-size:.78rem;color:var(--kn-muted)"><?= $totalResidents ?> total</span>
            </div>
            <?php if (empty($residents)): ?>
            <div style="padding:3rem;text-align:center;color:var(--kn-muted)">
                <div style="font-size:2.5rem;margin-bottom:.5rem">👥</div>
                <div style="font-size:.9rem">No residents registered yet.</div>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table rr-table mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registered</th>
                            <th class="text-center">Profile</th>
                            <th class="text-center">Login</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($residents as $r): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></strong></td>
                        <td style="color:var(--kn-muted)"><?= htmlspecialchars($r['email']) ?></td>
                        <td style="color:var(--kn-muted)"><?= date('M j, Y', strtotime($r['created_at'])) ?></td>
                        <td class="text-center">
                            <?php if ($r['profile_complete']): ?>
                                <span class="badge-ok"><i class="bi bi-check-circle-fill me-1"></i>Complete</span>
                            <?php else: ?>
                                <span class="badge-pending"><i class="bi bi-clock me-1"></i>Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($r['force_password_change']): ?>
                                <span class="badge-pending"><i class="bi bi-key-fill me-1"></i>Awaiting</span>
                            <?php else: ?>
                                <span class="badge-ok"><i class="bi bi-check-circle-fill me-1"></i>Logged In</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn-resend"
                                    onclick="openResendModal(<?= (int)$r['user_id'] ?>, '<?= htmlspecialchars(addslashes($r['first_name'] . ' ' . $r['last_name'])) ?>', '<?= htmlspecialchars(addslashes($r['email'])) ?>')">
                                <i class="bi bi-envelope-arrow-up me-1"></i>Resend
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </div><!-- /right panel -->
</div><!-- /grid -->

<!-- Resend Modal -->
<div class="modal fade" id="resendModal" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:1rem;border:none">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Resend Credentials</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="font-size:.9rem;color:var(--kn-muted)" id="resendMsg"></div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="index.php?action=resendCredentials" id="resendForm">
                    <?= \Security::csrfField() ?>
                    <input type="hidden" name="resident_id" id="resendResidentId">
                    <button type="submit" class="btn btn-sm" style="background:var(--kn-orange);color:#fff;font-weight:600">
                        <i class="bi bi-envelope-arrow-up me-1"></i>Resend
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function checkNameMatch() {
    const first = document.getElementById('first_name').value.trim();
    const last  = document.getElementById('last_name').value.trim();
    if (!first || !last) return;
    const fd = new FormData();
    fd.append('first_name', first);
    fd.append('last_name',  last);
    fd.append('ajax_name_check', '1');
    fd.append('csrf_token', document.querySelector('[name=csrf_token]').value);
    fetch('index.php?action=registerResident', { method:'POST', body:fd })
        .then(r => r.json())
        .then(matches => { if (matches && matches.length) renderMatches(matches); })
        .catch(() => {});
}
function renderMatches(matches) {
    const list = document.getElementById('matchList');
    list.innerHTML = '';
    matches.forEach(m => {
        const role = m.role === 'Head' ? 'Head of Family' : 'Spouse';
        const hh   = m.hh_number ? ' · HH# ' + m.hh_number : '';
        const pu   = m.purok     ? ' · Purok ' + m.purok    : '';
        list.innerHTML += `<div class="match-item">
            <div class="match-info">
                <strong>${escHtml(m.first_name + ' ' + m.last_name)}</strong>
                <span>${role}${hh}${pu}</span>
            </div>
            <button type="button" class="btn-link-family" onclick="confirmLink(${parseInt(m.family_id)})">
                <i class="bi bi-link-45deg me-1"></i> Link
            </button></div>`;
    });
    document.getElementById('matchPanel').style.display = 'block';
}
function confirmLink(familyId) {
    document.getElementById('confirmMatchFamilyId').value = familyId;
    document.getElementById('matchDismissed').value = '';
    document.getElementById('regForm').submit();
}
function dismissMatch() {
    document.getElementById('matchDismissed').value = '1';
    document.getElementById('confirmMatchFamilyId').value = '';
    document.getElementById('matchPanel').style.display = 'none';
}
function openResendModal(userId, name, email) {
    document.getElementById('resendResidentId').value = userId;
    document.getElementById('resendMsg').innerHTML =
        'This will generate a new temporary password and send it to <strong>' +
        escHtml(email) + '</strong> (' + escHtml(name) + ').<br><br>' +
        'The resident will be required to change their password on next login.';
    new bootstrap.Modal(document.getElementById('resendModal')).show();
}
function escHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
['first_name','last_name'].forEach(id => {
    document.getElementById(id).addEventListener('blur', checkNameMatch);
});
</script>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
