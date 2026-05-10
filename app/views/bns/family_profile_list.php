<?php
$pageTitle = 'Family Profiles';
$activeNav = 'family_profiles';
require_once __DIR__ . '/../templates/bns_layout.php';
?>

<style>
    .kn-table-card { background:#fff; border:1.5px solid rgba(107,122,58,.12); border-radius:1rem; overflow:hidden; }
    .kn-table thead th { background:var(--kn-green); color:var(--kn-cream); font-size:.7rem; text-transform:uppercase; letter-spacing:.03em; border:none; padding:.5rem .5rem; white-space:nowrap; font-weight:700; }
    .kn-table tbody td { font-size:.8rem; padding:.5rem .5rem; vertical-align:middle; border-color:rgba(107,122,58,.08); }
    .kn-table tbody tr:hover { background:rgba(107,122,58,.04); }
    .search-input { border:1.5px solid rgba(107,122,58,.25); border-radius:8px; padding:.45rem .9rem; font-size:.92rem; min-width:260px; }
    .search-input:focus { outline:none; border-color:var(--kn-green); box-shadow:0 0 0 3px rgba(107,122,58,.1); }
    .btn-kn { background:var(--kn-green); color:#fff; border:none; border-radius:8px; font-weight:600; font-size:.92rem; padding:.45rem 1.1rem; }
    .btn-kn:hover { background:var(--kn-green-d); color:#fff; }
    .page-link { color:var(--kn-green); font-size:.9rem; }
    .page-item.active .page-link { background:var(--kn-green); border-color:var(--kn-green); }
    .tag { display:inline-block; font-size:.65rem; padding:.12em .35em; border-radius:3px; font-weight:600; }
    .tag-yes { background:#dcfce7; color:#166534; }
    .tag-no  { background:#f1f5f9; color:#94a3b8; }
    .compact-cell { font-size:.75rem; line-height:1.2; }
    .name-cell { font-weight:600; font-size:.82rem; }
    .info-row { display:flex; gap:.3rem; align-items:center; margin-top:.1rem; flex-wrap:wrap; }
    .sub-text { font-size:.68rem; color:var(--kn-muted); }
    .edu-badge { font-size:.65rem; background:rgba(107,122,58,.1); color:var(--kn-green); padding:.08em .35em; border-radius:3px; font-weight:600; white-space:nowrap; }
</style>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-0">Family Profiles</h4>
        <p class="text-muted mb-0" style="font-size:.88rem"><?= number_format($total) ?> record<?= $total != 1 ? 's' : '' ?> found</p>
    </div>
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <form method="GET" action="index.php" class="d-flex gap-2">
            <input type="hidden" name="action" value="familyProfiles">
            <input type="text" name="search" class="search-input"
                   placeholder="Search name, purok, HH#…" value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-kn"><i class="bi bi-search"></i></button>
            <?php if ($search): ?>
                <a href="index.php?action=familyProfiles" class="btn btn-outline-secondary" style="border-radius:8px;font-size:.92rem">Clear</a>
            <?php endif; ?>
        </form>
        <a href="index.php?action=familyProfileForm" class="btn btn-kn">
            <i class="bi bi-plus-lg me-1"></i> Add Family
        </a>
    </div>
</div>

<!-- Table -->
<div class="kn-table-card">
    <div class="table-responsive">
        <table class="table kn-table mb-0">
            <thead>
                <tr>
                    <th style="min-width:70px">HH Number</th>
                    <th style="min-width:45px">Purok</th>
                    <th class="text-center" style="min-width:45px" title="Number of Household Members"># HH</th>
                    <th class="text-center" style="min-width:80px">Children<br><span style="font-weight:400;font-size:.65rem;text-transform:none">0-5/6-23/24-59/60+</span></th>
                    <th style="min-width:130px">Head of Family</th>
                    <th style="min-width:130px">Spouse</th>
                    <th class="text-center" style="min-width:40px" title="Mother Pregnant">Mother<br><span style="font-weight:400;font-size:.65rem;text-transform:none">Pregnant</span></th>
                    <th style="min-width:60px" title="Family Planning Method">Family<br><span style="font-weight:400;font-size:.65rem;text-transform:none">Planning</span></th>
                    <th class="text-center" style="min-width:80px" title="Breastfeed ≤6mos child">Breastfeed<br><span style="font-weight:400;font-size:.65rem;text-transform:none">≤6mos child</span></th>
                    <th style="min-width:60px">Food</th>
                    <th style="min-width:45px">Toilet</th>
                    <th style="min-width:45px">Water</th>
                    <th class="text-center" style="min-width:40px">Salt</th>
                    <th class="text-center" style="min-width:40px">IFR</th>
                    <th style="min-width:60px">Dwelling</th>
                    <th class="text-end" style="min-width:70px">Income</th>
                    <th class="text-center" style="min-width:70px">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($profiles)): ?>
                <tr>
                    <td colspan="17" class="text-center py-5 text-muted">
                        <div style="font-size:2.5rem;margin-bottom:.5rem">📭</div>
                        No family profiles yet.
                        <a href="index.php?action=familyProfileForm" style="color:var(--kn-green)">Add the first one.</a>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($profiles as $fp): ?>
                <tr>
                    <td><strong style="font-size:.8rem"><?= htmlspecialchars($fp['hh_number'] ?? '—') ?></strong></td>
                    <td class="compact-cell"><?= htmlspecialchars($fp['purok'] ?? '—') ?></td>
                    <td class="text-center compact-cell"><strong><?= (int)($fp['num_hh_members'] ?? 0) ?: '—' ?></strong></td>
                    <td class="text-center compact-cell">
                        <?= (int)($fp['children_0_5mos']   ?? 0) ?>/<?= (int)($fp['children_6_23mos']  ?? 0) ?>/<?= (int)($fp['children_24_59mos'] ?? 0) ?>/<?= (int)($fp['children_60plus']   ?? 0) ?>
                    </td>
                    <td>
                        <div class="name-cell"><?= htmlspecialchars($fp['head_name'] ?? '—') ?></div>
                        <div class="info-row">
                            <?php if ($fp['head_occupation'] ?? ''): ?>
                                <span class="sub-text"><?= htmlspecialchars($fp['head_occupation']) ?></span>
                            <?php endif; ?>
                            <?php if ($fp['head_educ'] ?? ''): ?>
                                <span class="edu-badge"><?= htmlspecialchars($fp['head_educ']) ?></span>
                            <?php endif; ?>
                            <?php if (empty($fp['source_user_id'])): ?>
                                <span style="background:#f97316;color:#fff;font-size:.6rem;padding:.15em .4em;border-radius:4px;font-weight:700;vertical-align:middle" title="No resident account linked to this household">
                                    <i class="bi bi-link-45deg"></i> Unlinked
                                </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="compact-cell"><?= htmlspecialchars($fp['wife_name'] ?? '—') ?></div>
                        <div class="info-row">
                            <?php if ($fp['wife_occupation'] ?? ''): ?>
                                <span class="sub-text"><?= htmlspecialchars($fp['wife_occupation']) ?></span>
                            <?php endif; ?>
                            <?php if ($fp['wife_educ'] ?? ''): ?>
                                <span class="edu-badge"><?= htmlspecialchars($fp['wife_educ']) ?></span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="tag <?= $fp['is_mother_prog'] ? 'tag-yes' : 'tag-no' ?>">
                            <?= $fp['is_mother_prog'] ? 'Y' : 'N' ?>
                        </span>
                    </td>
                    <td class="compact-cell" style="font-size:.75rem">
                        <?= htmlspecialchars($fp['fp_label'] ?? '—') ?>
                    </td>
                    <td class="text-center compact-cell">
                        <?php
                        if ($fp['is_erf']) {
                            echo '<span class="tag tag-yes">EBF</span>';
                        } elseif ($fp['is_mixed_milk']) {
                            echo '<span class="tag" style="background:#fef3c7;color:#92400e">Mixed</span>';
                        } elseif ($fp['is_bottle_feeding']) {
                            echo '<span class="tag" style="background:#fee2e2;color:#991b1b">Bottle</span>';
                        } else {
                            echo '<span class="tag tag-no">—</span>';
                        }
                        ?>
                    </td>
                    <td class="compact-cell" style="font-size:.75rem" title="<?= htmlspecialchars($fp['food_activities'] ?? 'None') ?>">
                        <?= htmlspecialchars($fp['food_activities'] ?: '—') ?>
                    </td>
                    <td class="compact-cell" style="font-size:.75rem" title="Toilet: <?= htmlspecialchars($fp['toilet_code'] ?? 'None') ?>">
                        <?= htmlspecialchars($fp['toilet_code'] ?? '—') ?>
                    </td>
                    <td class="compact-cell" style="font-size:.75rem" title="Water: <?= htmlspecialchars($fp['water_code'] ?? 'None') ?>">
                        <?= htmlspecialchars($fp['water_code'] ?? '—') ?>
                    </td>
                    <td class="text-center">
                        <span class="tag <?= $fp['uses_iodized_salt'] ? 'tag-yes' : 'tag-no' ?>">
                            <?= $fp['uses_iodized_salt'] ? 'Y' : 'N' ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="tag <?= $fp['uses_ifr'] ? 'tag-yes' : 'tag-no' ?>">
                            <?= $fp['uses_ifr'] ? 'Y' : 'N' ?>
                        </span>
                    </td>
                    <td class="compact-cell" style="font-size:.75rem"><?= htmlspecialchars($fp['dwelling_label'] ?? '—') ?></td>
                    <td class="text-end compact-cell">
                        <?= $fp['total_income'] ? '₱' . number_format($fp['total_income'], 0) : '—' ?>
                    </td>
                    <td class="text-center" style="white-space:nowrap">
                        <a href="index.php?action=familyProfileForm&id=<?= $fp['family_id'] ?>"
                           class="btn btn-sm btn-outline-secondary me-1" title="Edit" style="border-radius:6px;padding:.2rem .4rem">
                            <i class="bi bi-pencil" style="font-size:.75rem"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" style="border-radius:6px;padding:.2rem .4rem"
                                onclick="confirmDelete(<?= $fp['family_id'] ?>, '<?= htmlspecialchars(addslashes($fp['head_name'] ?? '')) ?>')">
                            <i class="bi bi-trash" style="font-size:.75rem"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<nav class="mt-3 d-flex justify-content-center">
    <ul class="pagination pagination-sm">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                <a class="page-link" href="index.php?action=familyProfiles&page=<?= $p ?>&search=<?= urlencode($search) ?>"><?= $p ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<!-- Delete modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:1rem;border:none">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Delete Family Profile</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-muted" id="deleteMsg"></div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="index.php?action=deleteFamilyProfile">
                    <input type="hidden" name="family_id" id="deleteFamilyId">
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteFamilyId').value = id;
    document.getElementById('deleteMsg').textContent = 'Delete the profile for ' + name + '? This cannot be undone.';
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
