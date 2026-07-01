<?php
/**
 * BNS - Feeding Program List
 * Shows approved feeding programs assigned to this BNS
 */
$pageTitle = 'Feeding Programs';
$activeNav = 'feeding_program';
include __DIR__ . '/../templates/bns_layout.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-0" style="color: var(--kn-dark); font-weight: 700;">
                <i class="bi bi-heart-pulse-fill me-2" style="color: #C4722A;"></i>
                Approved Feeding Programs
            </h2>
            <p class="text-muted">Conduct feeding sessions and track attendance</p>
        </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-success alert-dismissible fade show" style="background: linear-gradient(135deg, rgba(90,112,56,.1) 0%, rgba(90,112,56,.05) 100%); border-left: 4px solid #5A7038; border-radius: 12px;">
            <i class="bi bi-check-circle-fill me-2" style="color: #5A7038;"></i>
            <?= htmlspecialchars($_SESSION['flash']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?php if (empty($approvedPrograms)): ?>
        <div class="alert alert-info" style="background: linear-gradient(135deg, rgba(196,114,42,.08) 0%, rgba(196,114,42,.04) 100%); border-left: 4px solid #C4722A; border-radius: 12px; color: var(--kn-dark);">
            <i class="bi bi-info-circle me-2" style="color: #C4722A;"></i>
            No approved feeding programs yet. Programs will appear here once approved by the Barangay Captain.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($approvedPrograms as $program): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.08); transition: all .3s ease; background: #fff;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title mb-0 fw-bold" style="color: var(--kn-dark); font-size: 1.1rem;">
                                    <?= htmlspecialchars($program['proposal_title']) ?>
                                </h5>
                                <span class="badge" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); padding: .4rem .8rem; border-radius: 8px; font-weight: 600;">Approved</span>
                            </div>

                            <div class="mb-3">
                                <p class="text-muted small mb-2 d-flex align-items-center">
                                    <i class="bi bi-calendar-event me-2" style="color: #C4722A; font-size: 1.1rem;"></i>
                                    <span><?= date('M j', strtotime($program['start_date'])) ?> - <?= date('M j, Y', strtotime($program['end_date'])) ?></span>
                                </p>

                                <p class="text-muted small mb-2 d-flex align-items-center">
                                    <i class="bi bi-people-fill me-2" style="color: #5A7038; font-size: 1.1rem;"></i>
                                    <span><strong><?= $program['num_beneficiaries'] ?></strong> Beneficiaries</span>
                                </p>

                                <p class="text-muted small mb-0 d-flex align-items-center">
                                    <i class="bi bi-tag-fill me-2" style="color: #C4722A; font-size: 1.1rem;"></i>
                                    <span><?= htmlspecialchars($program['program_type']) ?></span>
                                </p>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <a href="index.php?action=feedingSessions&proposal_id=<?= $program['proposal_id'] ?>" 
                                   class="btn btn-sm" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); color: #fff; border: none; padding: .6rem 1rem; border-radius: 10px; font-weight: 600; transition: all .3s ease;">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    View Sessions
                                </a>
                                <a href="index.php?action=bnsRecoveryStatus&proposal_id=<?= $program['proposal_id'] ?>"
                                   class="btn btn-sm" style="background: linear-gradient(135deg, #C4722A 0%, #A85D22 100%); color: #fff; border: none; padding: .6rem 1rem; border-radius: 10px; font-weight: 600;">
                                    <i class="bi bi-heart-pulse me-1"></i>
                                    Recovery Status
                                </a>
                                <a href="index.php?action=viewProposal&proposal_id=<?= $program['proposal_id'] ?>" 
                                   class="btn btn-sm" style="background: transparent; color: var(--kn-dark); border: 2px solid #e5e7eb; padding: .6rem 1rem; border-radius: 10px; font-weight: 600; transition: all .3s ease;">
                                    <i class="bi bi-file-text me-1"></i>
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,.12) !important;
}
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(90,112,56,.3);
}
.btn:active {
    transform: translateY(0);
}
</style>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('show');
    document.getElementById('sidebarOverlay').classList.toggle('show');
});
document.getElementById('sidebarOverlay')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.remove('show');
    document.getElementById('sidebarOverlay').classList.remove('show');
});
</script>
</body>
</html>
