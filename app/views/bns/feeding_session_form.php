<?php
/**
 * BNS - Feeding Session Form (Process 15: Conducting Feeding Program)
 * Based on City Health Office Nutrition Division form
 */
$pageTitle = isset($session) ? 'Edit Feeding Session' : 'Create Feeding Session';
$activeNav = 'feeding_program';
include __DIR__ . '/../templates/bns_layout.php';

$isEdit = isset($session);
$formData = $_SESSION['form_data'] ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['form_data'], $_SESSION['errors']);
?>

<div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.php?action=feedingProgramList" style="color: #5A7038; text-decoration: none; font-weight: 500;">Feeding Programs</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="index.php?action=feedingSessions&proposal_id=<?= $proposal['proposal_id'] ?>" style="color: #5A7038; text-decoration: none; font-weight: 500;">
                                Sessions
                            </a>
                        </li>
                        <li class="breadcrumb-item active" style="color: var(--kn-dark);"><?= $isEdit ? 'Edit' : 'Create' ?></li>
                    </ol>
                </nav>

                <h2 class="mb-0" style="color: var(--kn-dark); font-weight: 700;">
                    <i class="bi bi-calendar-plus me-2" style="color: #5A7038;"></i>
                    <?= $isEdit ? 'Edit' : 'Create' ?> Feeding Session
                </h2>
                <p class="text-muted"><?= htmlspecialchars($proposal['proposal_title']) ?></p>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=saveFeedingSession">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="proposal_id" value="<?= $proposal['proposal_id'] ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
            <?php endif; ?>

            <div class="card shadow-sm mb-4" style="border-radius: 16px; border: none; box-shadow: 0 4px 16px rgba(0,0,0,.08) !important;">
                <div class="card-header" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15); border-radius: 16px 16px 0 0 !important;">
                    <h5 class="mb-0" style="color: var(--kn-dark); font-weight: 700;">Session Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="session_date" class="form-control" required
                                   value="<?= htmlspecialchars($formData['session_date'] ?? $session['session_date'] ?? date('Y-m-d')) ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <?php
                                $currentStatus = $formData['status'] ?? $session['status'] ?? 'Scheduled';
                                foreach (['Scheduled', 'Ongoing', 'Completed', 'Cancelled'] as $status):
                                ?>
                                    <option value="<?= $status ?>" <?= $currentStatus === $status ? 'selected' : '' ?>>
                                        <?= $status ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Name of Activity <span class="text-danger">*</span></label>
                            <input type="text" name="activity_name" class="form-control" required
                                   placeholder="e.g., Supplementary Feeding Session"
                                   value="<?= htmlspecialchars($formData['activity_name'] ?? $session['activity_name'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Purok/Barangay <span class="text-danger">*</span></label>
                            <input type="text" name="purok_barangay" class="form-control" required
                                   placeholder="e.g., Purok 1, Barangay Bayabas"
                                   value="<?= htmlspecialchars($formData['purok_barangay'] ?? $session['purok_barangay'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">IEC Age Group (please check)</label>
                            <div class="row g-2">
                                <?php
                                $ageGroups = [
                                    'Pregnant women',
                                    'Lactating mothers',
                                    'Senior Citizens',
                                    'Mothers with 0-23 mos. PS',
                                    'Mothers with 24-59 mos. PS',
                                    'Adolescents',
                                    'Adults',
                                    'PWD',
                                    'Others'
                                ];
                                $selectedGroups = [];
                                if (!empty($session['iec_age_group'])) {
                                    $selectedGroups = json_decode($session['iec_age_group'], true) ?: [];
                                }
                                if (!empty($formData['iec_age_group'])) {
                                    $selectedGroups = $formData['iec_age_group'];
                                }

                                foreach ($ageGroups as $group):
                                    $checked = in_array($group, $selectedGroups) ? 'checked' : '';
                                ?>
                                    <div class="col-md-4 col-lg-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="iec_age_group[]" value="<?= htmlspecialchars($group) ?>" 
                                                   id="iec_<?= str_replace(' ', '_', $group) ?>" 
                                                   <?= $checked ?>
                                                   <?= $group === 'Others' ? 'onchange="toggleOthersInput(this)"' : '' ?>>
                                            <label class="form-check-label" for="iec_<?= str_replace(' ', '_', $group) ?>">
                                                <?= htmlspecialchars($group) ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Others specification input -->
                            <div id="othersInputContainer" class="mt-3" style="display: none;">
                                <label class="form-label">Please specify "Others":</label>
                                <input type="text" name="iec_others_specify" id="iec_others_specify" 
                                       class="form-control" placeholder="e.g., Children with disabilities, Elderly with chronic illness"
                                       value="<?= htmlspecialchars($formData['iec_others_specify'] ?? $session['iec_others_specify'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Prepared by</label>
                            <input type="text" name="prepared_by" class="form-control"
                                   placeholder="Name of person who prepared this form"
                                   value="<?= htmlspecialchars($formData['prepared_by'] ?? $session['prepared_by'] ?? (($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? ''))) ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3"
                                      placeholder="Additional notes or remarks"><?= htmlspecialchars($formData['remarks'] ?? $session['remarks'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); color: #fff; border: none; padding: .6rem 1.5rem; border-radius: 10px; font-weight: 600;">
                    <i class="bi bi-save me-1"></i>
                    <?= $isEdit ? 'Update' : 'Create' ?> Session
                </button>
                <a href="index.php?action=feedingSessions&proposal_id=<?= $proposal['proposal_id'] ?>" 
                   class="btn" style="background: transparent; color: var(--kn-dark); border: 2px solid #e5e7eb; padding: .6rem 1.5rem; border-radius: 10px; font-weight: 600;">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
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

// Toggle "Others" input field
function toggleOthersInput(checkbox) {
    const container = document.getElementById('othersInputContainer');
    const input = document.getElementById('iec_others_specify');
    
    if (checkbox.checked) {
        container.style.display = 'block';
        input.required = true;
    } else {
        container.style.display = 'none';
        input.required = false;
        input.value = '';
    }
}

// Check if "Others" is already checked on page load
document.addEventListener('DOMContentLoaded', function() {
    const othersCheckbox = document.getElementById('iec_Others');
    if (othersCheckbox && othersCheckbox.checked) {
        toggleOthersInput(othersCheckbox);
    }
});
</script>
</body>
</html>
