<?php
$pageTitle = 'My Residents';
$activeNav = 'resident_list';
require_once __DIR__ . '/../templates/bns_layout.php';
?>

<style>
.kn-table-card { background:#fff; border:1.5px solid rgba(107,122,58,.12); border-radius:1rem; overflow:hidden; }
.kn-table thead th { background:var(--kn-green); color:var(--kn-cream); font-size:.7rem; text-transform:uppercase; letter-spacing:.03em; border:none; padding:.55rem .75rem; white-space:nowrap; font-weight:700; }
.kn-table tbody td { font-size:.85rem; padding:.55rem .75rem; vertical-align:middle; border-color:rgba(107,122,58,.08); }
.kn-table tbody tr:hover { background:rgba(107,122,58,.04); }
.badge-pending { background:rgba(196,114,42,.15); color:#7a3a10; font-size:.7rem; font-weight:700; padding:.2em .55em; border-radius:5px; white-space:nowrap; }
.badge-done    { background:rgba(107,122,58,.12); color:var(--kn-green); font-size:.7rem; font-weight:700; padding:.2em .55em; border-radius:5px; white-space:nowrap; }
.btn-resend { background:var(--kn-orange); color:#fff; border:none; border-radius:7px; padding:.3rem .85rem; font-size:.82rem; font-weight:600; cursor:pointer; transition:background .15s; }
.btn-resend:hover { background:#a85e22; }
.btn-add { background:var(--kn-green); color:#fff; border:none; border-radius:8px; padding:.45rem 1.1rem; font-weight:600; font-size:.92rem; text-decoration:none; }
.btn-add:hover { background:var(--kn-green-d); color:#fff; }
</style>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-0">My Residents</h4>
        <p class="text-muted mb-0" style="font-size:.88rem">
            <?= count($residents) ?> resident<?= count($residents) !== 1 ? 's' : '' ?> registered by you
        </p>
    </div>
    <a href="index.php?action=registerResident" class="btn-add">
        <i class="bi bi-person-plus-fill me-1"></i> Register Resident
    </a>
</div>

<!-- Table -->
<div class="kn-table-card">
    <div class="table-responsive">
        <table class="table kn-table mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registered</th>
                    <th class="text-center">Profile</th>
                    <th class="text-center">First Login</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($residents)): ?>
                <tr>
                    <td colspan="6" class="text-center py-5" style="color:var(--kn-muted)">
                        <div style="font-size:2.5rem;margin-bottom:.5rem">👥</div>
                        No residents registered yet.
                        <a href="index.php?action=registerResident" style="color:var(--kn-green)">Register the first one.</a>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($residents as $r): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></strong>
                    </td>
                    <td style="font-size:.82rem;color:var(--kn-muted)">
                        <?= htmlspecialchars($r['email']) ?>
                    </td>
                    <td style="font-size:.82rem;color:var(--kn-muted)">
                        <?= date('M j, Y', strtotime($r['created_at'])) ?>
                    </td>
                    <td class="text-center">
                        <?php if ($r['profile_complete']): ?>
                            <span class="badge-done"><i class="bi bi-check-circle-fill me-1"></i>Complete</span>
                        <?php else: ?>
                            <span class="badge-pending"><i class="bi bi-clock me-1"></i>Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if ($r['force_password_change']): ?>
                            <span class="badge-pending" title="Resident has not yet logged in and changed their password">
                                <i class="bi bi-key-fill me-1"></i>Awaiting Login
                            </span>
                        <?php else: ?>
                            <span class="badge-done"><i class="bi bi-check-circle-fill me-1"></i>Logged In</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <!-- Resend Credentials -->
                        <button type="button" class="btn-resend"
                                onclick="openResendModal(<?= (int)$r['user_id'] ?>, '<?= htmlspecialchars(addslashes($r['first_name'] . ' ' . $r['last_name'])) ?>', '<?= htmlspecialchars(addslashes($r['email'])) ?>')">
                            <i class="bi bi-envelope-arrow-up me-1"></i> Resend
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Resend Credentials Modal -->
<div class="modal fade" id="resendModal" tabindex="-1" aria-labelledby="resendModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:1rem;border:none">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" id="resendModalLabel">Resend Credentials</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="font-size:.9rem;color:var(--kn-muted)" id="resendMsg"></div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="index.php?action=resendCredentials" id="resendForm">
                    <?= \Security::csrfField() ?>
                    <input type="hidden" name="resident_id" id="resendResidentId">
                    <button type="submit" class="btn btn-sm" style="background:var(--kn-orange);color:#fff;font-weight:600">
                        <i class="bi bi-envelope-arrow-up me-1"></i> Resend
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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
</script>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
