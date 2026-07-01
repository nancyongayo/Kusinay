<?php
/**
 * BNS - Feeding Sessions List
 * Shows all sessions for a feeding program
 */
$pageTitle = 'Feeding Sessions';
$activeNav = 'feeding_program';
include __DIR__ . '/../templates/bns_layout.php';
?>

<div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.php?action=feedingProgramList" style="color: #5A7038; text-decoration: none;">Feeding Programs</a>
                        </li>
                        <li class="breadcrumb-item active" style="color: var(--kn-dark);">Sessions</li>
                    </ol>
                </nav>

                <h2 class="mb-2" style="color: var(--kn-dark); font-weight: 700;">
                    <i class="bi bi-calendar-check-fill me-2" style="color: #5A7038;"></i>
                    <?= htmlspecialchars($proposal['proposal_title']) ?>
                </h2>
                <p class="text-muted">
                    <i class="bi bi-calendar-event me-1" style="color: #C4722A;"></i>
                    <?= date('F j', strtotime($proposal['start_date'])) ?> - 
                    <?= date('F j, Y', strtotime($proposal['end_date'])) ?>
                    <span class="mx-2">•</span>
                    <i class="bi bi-people-fill me-1" style="color: #5A7038;"></i>
                    <?= $proposal['num_beneficiaries'] ?> Beneficiaries
                </p>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <a href="index.php?action=bulkSessionForm&proposal_id=<?= $proposal['proposal_id'] ?>" 
                       class="btn" style="background: linear-gradient(135deg, #C4722A 0%, #A85F22 100%); color: #fff; border: none; padding: .6rem 1.2rem; border-radius: 10px 0 0 10px; font-weight: 600;">
                        <i class="bi bi-calendar-range me-1"></i>
                        Create Multiple
                    </a>
                    <a href="index.php?action=feedingSessionForm&proposal_id=<?= $proposal['proposal_id'] ?>" 
                       class="btn" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); color: #fff; border: none; padding: .6rem 1.2rem; border-radius: 0 10px 10px 0; font-weight: 600;">
                        <i class="bi bi-plus-circle me-1"></i>
                        Create Single
                    </a>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert alert-dismissible fade show" style="background: linear-gradient(135deg, rgba(90,112,56,.1) 0%, rgba(90,112,56,.05) 100%); border-left: 4px solid #5A7038; border-radius: 12px; color: var(--kn-dark);">
                <i class="bi bi-check-circle-fill me-2" style="color: #5A7038;"></i>
                <?= htmlspecialchars($_SESSION['flash']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <?php if (empty($sessions)): ?>
            <div class="alert" style="background: linear-gradient(135deg, rgba(196,114,42,.08) 0%, rgba(196,114,42,.04) 100%); border-left: 4px solid #C4722A; border-radius: 12px; color: var(--kn-dark);">
                <i class="bi bi-info-circle me-2" style="color: #C4722A;"></i>
                No feeding sessions yet. Click "Create Session" to schedule your first feeding activity.
            </div>
        <?php else: ?>
            <div class="card border-0" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.08); background: #fff;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15);">
                                <tr>
                                    <th style="color: var(--kn-dark); font-weight: 700; padding: 1rem;">Date</th>
                                    <th style="color: var(--kn-dark); font-weight: 700;">Activity Name</th>
                                    <th style="color: var(--kn-dark); font-weight: 700;">Location</th>
                                    <th style="color: var(--kn-dark); font-weight: 700;">Target Group</th>
                                    <th style="color: var(--kn-dark); font-weight: 700;">Status</th>
                                    <th style="color: var(--kn-dark); font-weight: 700;">Attendance</th>
                                    <th style="color: var(--kn-dark); font-weight: 700;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sessions as $session): ?>
                                    <tr style="border-bottom: 1px solid rgba(0,0,0,.05);">
                                        <td style="padding: 1rem;">
                                            <strong style="color: var(--kn-dark);"><?= date('M j, Y', strtotime($session['session_date'])) ?></strong>
                                        </td>
                                        <td style="color: var(--kn-dark);"><?= htmlspecialchars($session['activity_name']) ?></td>
                                        <td style="color: var(--kn-muted);"><?= htmlspecialchars($session['purok_barangay']) ?></td>
                                        <td>
                                            <?php
                                            $iecGroups = !empty($session['iec_age_group']) ? json_decode($session['iec_age_group'], true) : [];
                                            if (!empty($iecGroups)):
                                                // Replace "Others" with the actual specification in the display
                                                $displayGroups = $iecGroups;
                                                if (!empty($session['iec_others_specify']) && in_array('Others', $displayGroups)) {
                                                    $key = array_search('Others', $displayGroups);
                                                    $displayGroups[$key] = $session['iec_others_specify'];
                                                }
                                                
                                                // Show first 2 groups
                                                $firstTwo = array_slice($displayGroups, 0, 2);
                                                $remaining = count($displayGroups) - 2;
                                                
                                                // Build tooltip content with all groups
                                                $tooltipContent = implode(', ', $displayGroups);
                                            ?>
                                                <span style="color: var(--kn-dark); font-size: .875rem;" 
                                                      data-bs-toggle="tooltip" 
                                                      data-bs-placement="top" 
                                                      title="<?= htmlspecialchars($tooltipContent) ?>">
                                                    <?= htmlspecialchars(implode(', ', $firstTwo)) ?>
                                                    <?php if ($remaining > 0): ?>
                                                        <span class="badge" style="background: linear-gradient(135deg, rgba(90,112,56,.15) 0%, rgba(90,112,56,.1) 100%); color: #5A7038; border: 1px solid rgba(90,112,56,.2); padding: .2rem .5rem; border-radius: 6px; font-size: .75rem;">
                                                            +<?= $remaining ?> more
                                                        </span>
                                                    <?php endif; ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="color: var(--kn-muted); font-style: italic;">Not specified</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusStyles = [
                                                'Scheduled' => 'background: linear-gradient(135deg, rgba(107,114,128,.15) 0%, rgba(107,114,128,.1) 100%); color: #374151; border: 1px solid rgba(107,114,128,.2);',
                                                'Ongoing' => 'background: linear-gradient(135deg, rgba(196,114,42,.15) 0%, rgba(196,114,42,.1) 100%); color: #C4722A; border: 1px solid rgba(196,114,42,.2);',
                                                'Completed' => 'background: linear-gradient(135deg, rgba(90,112,56,.15) 0%, rgba(90,112,56,.1) 100%); color: #5A7038; border: 1px solid rgba(90,112,56,.2);',
                                                'Cancelled' => 'background: linear-gradient(135deg, rgba(220,38,38,.15) 0%, rgba(220,38,38,.1) 100%); color: #dc2626; border: 1px solid rgba(220,38,38,.2);'
                                            ];
                                            $style = $statusStyles[$session['status']] ?? $statusStyles['Scheduled'];
                                            ?>
                                            <span class="badge" style="<?= $style ?> padding: .4rem .8rem; border-radius: 8px; font-weight: 600;">
                                                <?= htmlspecialchars($session['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); padding: .4rem .8rem; border-radius: 8px; font-weight: 600;">
                                                <?= $session['attendance_count'] ?> recorded
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="index.php?action=sessionRSVPList&session_id=<?= $session['session_id'] ?>" 
                                                   class="btn" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); color: #fff; border: none;" title="Attendance">
                                                    <i class="bi bi-check2-square"></i>
                                                </a>
                                                <a href="index.php?action=feedingSessionForm&proposal_id=<?= $proposal['proposal_id'] ?>&session_id=<?= $session['session_id'] ?>" 
                                                   class="btn" style="background: transparent; color: #C4722A; border: 1px solid #C4722A;" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn" style="background: transparent; color: #dc2626; border: 1px solid #dc2626;" 
                                                        onclick="deleteSession(<?= $session['session_id'] ?>)" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Delete Session Form -->
    <form id="deleteSessionForm" method="POST" action="index.php?action=deleteFeedingSession" style="display:none;">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        <input type="hidden" name="session_id" id="deleteSessionId">
    </form>

    <script>
    function deleteSession(sessionId) {
        if (confirm('Are you sure you want to delete this session? All attendance records will also be deleted.')) {
            document.getElementById('deleteSessionId').value = sessionId;
            document.getElementById('deleteSessionForm').submit();
        }
    }
    </script>
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

// Initialize Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
</body>
</html>
