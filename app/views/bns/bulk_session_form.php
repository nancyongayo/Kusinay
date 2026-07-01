<?php
/**
 * BNS - Bulk Session Creation Form
 * Allows BNS to create multiple feeding sessions at once
 */
$pageTitle = 'Create Multiple Sessions';
$activeNav = 'feeding_program';
include __DIR__ . '/../templates/bns_layout.php';

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
                        <a href="index.php?action=feedingProgramList" style="color: #5A7038; text-decoration: none;">Feeding Programs</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="index.php?action=feedingSessions&proposal_id=<?= $proposal['proposal_id'] ?>" style="color: #5A7038; text-decoration: none;">Sessions</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--kn-dark);">Create Multiple Sessions</li>
                </ol>
            </nav>

            <h2 class="mb-2" style="color: var(--kn-dark); font-weight: 700;">
                <i class="bi bi-calendar-range me-2" style="color: #5A7038;"></i>
                Create Multiple Sessions
            </h2>
            <p class="text-muted">Generate feeding sessions for a date range automatically</p>
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

    <!-- Info Alert -->
    <div class="alert" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-left: 4px solid #5A7038; border-radius: 12px;">
        <div class="d-flex align-items-start">
            <i class="bi bi-info-circle-fill me-3" style="color: #5A7038; font-size: 1.5rem;"></i>
            <div>
                <h6 class="mb-2" style="color: var(--kn-dark); font-weight: 600;">How Bulk Session Creation Works</h6>
                <p class="mb-2">This feature automatically creates multiple feeding sessions based on your date range and selected days.</p>
                <p class="mb-0"><strong>Example:</strong> If you select May 28 to June 3 with Monday-Friday checked, the system will create 5 sessions (one for each weekday).</p>
            </div>
        </div>
    </div>

    <form method="POST" action="index.php?action=saveBulkSessions" id="bulkSessionForm">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        <input type="hidden" name="proposal_id" value="<?= $proposal['proposal_id'] ?>">

        <div class="card shadow-sm mb-4" style="border-radius: 16px; border: none; box-shadow: 0 4px 16px rgba(0,0,0,.08) !important;">
            <div class="card-header" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15); border-radius: 16px 16px 0 0 !important;">
                <h5 class="mb-0" style="color: var(--kn-dark); font-weight: 700;">Date Range & Schedule</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="start_date" class="form-control" required
                               value="<?= htmlspecialchars($formData['start_date'] ?? $proposal['start_date'] ?? date('Y-m-d')) ?>"
                               onchange="calculateSessions()">
                        <small class="text-muted">First day of feeding sessions (from proposal)</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" id="end_date" class="form-control" required
                               value="<?= htmlspecialchars($formData['end_date'] ?? $proposal['end_date'] ?? '') ?>"
                               onchange="calculateSessions()">
                        <small class="text-muted">Last day of feeding sessions (from proposal)</small>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Feeding Days <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <?php
                            $days = [
                                'Monday' => 'Mon',
                                'Tuesday' => 'Tue',
                                'Wednesday' => 'Wed',
                                'Thursday' => 'Thu',
                                'Friday' => 'Fri',
                                'Saturday' => 'Sat',
                                'Sunday' => 'Sun'
                            ];
                            $selectedDays = $formData['feeding_days'] ?? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                            
                            foreach ($days as $day => $abbr):
                                $checked = in_array($day, $selectedDays) ? 'checked' : '';
                            ?>
                                <div class="col-auto">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input feeding-day-checkbox" type="checkbox" 
                                               name="feeding_days[]" value="<?= $day ?>" 
                                               id="day_<?= $day ?>" <?= $checked ?>
                                               onchange="calculateSessions()">
                                        <label class="form-check-label" for="day_<?= $day ?>">
                                            <strong><?= $abbr ?></strong>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="text-muted">Select which days of the week to conduct feeding sessions</small>
                    </div>

                    <!-- Session Count Preview -->
                    <div class="col-12">
                        <div class="alert" style="background: linear-gradient(135deg, rgba(196,114,42,.08) 0%, rgba(196,114,42,.04) 100%); border-left: 4px solid #C4722A; border-radius: 12px;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-check me-3" style="color: #C4722A; font-size: 1.5rem;"></i>
                                <div>
                                    <strong style="color: var(--kn-dark);">Sessions to be created:</strong>
                                    <span id="sessionCount" style="color: #C4722A; font-size: 1.2rem; font-weight: 700; margin-left: 0.5rem;">0</span>
                                    <div id="dateRange" class="text-muted small mt-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4" style="border-radius: 16px; border: none; box-shadow: 0 4px 16px rgba(0,0,0,.08) !important;">
            <div class="card-header" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15); border-radius: 16px 16px 0 0 !important;">
                <h5 class="mb-0" style="color: var(--kn-dark); font-weight: 700;">Session Details (Applied to All)</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Activity Name <span class="text-danger">*</span></label>
                        <input type="text" name="activity_name" class="form-control" required
                               placeholder="e.g., Supplementary Feeding Session"
                               value="<?= htmlspecialchars($formData['activity_name'] ?? 'Supplementary Feeding Session') ?>">
                        <small class="text-muted">This name will be used for all generated sessions</small>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Location (Purok/Barangay) <span class="text-danger">*</span></label>
                        <input type="text" name="purok_barangay" class="form-control" required
                               placeholder="e.g., Barangay Health Center"
                               value="<?= htmlspecialchars($formData['purok_barangay'] ?? $proposal['location'] ?? '') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">IEC Age Group (Target Beneficiaries)</label>
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
                            $selectedGroups = $formData['iec_age_group'] ?? [];

                            foreach ($ageGroups as $group):
                                $checked = in_array($group, $selectedGroups) ? 'checked' : '';
                            ?>
                                <div class="col-md-4 col-lg-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="iec_age_group[]" value="<?= htmlspecialchars($group) ?>" 
                                               id="bulk_iec_<?= str_replace(' ', '_', $group) ?>" 
                                               <?= $checked ?>
                                               <?= $group === 'Others' ? 'onchange="toggleOthersInput(this)"' : '' ?>>
                                        <label class="form-check-label" for="bulk_iec_<?= str_replace(' ', '_', $group) ?>">
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
                                   class="form-control" placeholder="e.g., Children with disabilities"
                                   value="<?= htmlspecialchars($formData['iec_others_specify'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Prepared by</label>
                        <input type="text" name="prepared_by" class="form-control"
                               placeholder="Name of person preparing these sessions"
                               value="<?= htmlspecialchars($formData['prepared_by'] ?? (($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? ''))) ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Remarks (Optional)</label>
                        <textarea name="remarks" class="form-control" rows="2"
                                  placeholder="Additional notes (will be applied to all sessions)"><?= htmlspecialchars($formData['remarks'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); color: #fff; border: none; padding: .6rem 1.5rem; border-radius: 10px; font-weight: 600;">
                <i class="bi bi-calendar-plus me-1"></i>
                Generate <span id="submitCount">0</span> Sessions
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

// Calculate number of sessions
function calculateSessions() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const selectedDays = Array.from(document.querySelectorAll('.feeding-day-checkbox:checked')).map(cb => cb.value);
    
    if (!startDate || !endDate || selectedDays.length === 0) {
        document.getElementById('sessionCount').textContent = '0';
        document.getElementById('submitCount').textContent = '0';
        document.getElementById('dateRange').textContent = 'Please select dates and days';
        return;
    }
    
    const start = new Date(startDate);
    const end = new Date(endDate);
    
    if (end < start) {
        document.getElementById('sessionCount').textContent = '0';
        document.getElementById('submitCount').textContent = '0';
        document.getElementById('dateRange').textContent = 'End date must be after start date';
        return;
    }
    
    // Map day names to day numbers (0 = Sunday, 1 = Monday, etc.)
    const dayMap = {
        'Sunday': 0,
        'Monday': 1,
        'Tuesday': 2,
        'Wednesday': 3,
        'Thursday': 4,
        'Friday': 5,
        'Saturday': 6
    };
    
    const selectedDayNumbers = selectedDays.map(day => dayMap[day]);
    
    // Count sessions
    let count = 0;
    let current = new Date(start);
    
    while (current <= end) {
        if (selectedDayNumbers.includes(current.getDay())) {
            count++;
        }
        current.setDate(current.getDate() + 1);
    }
    
    document.getElementById('sessionCount').textContent = count;
    document.getElementById('submitCount').textContent = count;
    
    // Format date range
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    const startFormatted = start.toLocaleDateString('en-US', options);
    const endFormatted = end.toLocaleDateString('en-US', options);
    document.getElementById('dateRange').textContent = `${startFormatted} to ${endFormatted} (${selectedDays.join(', ')})`;
}

// Calculate on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateSessions();
    
    // Check if "Others" is already checked
    const othersCheckbox = document.getElementById('bulk_iec_Others');
    if (othersCheckbox && othersCheckbox.checked) {
        toggleOthersInput(othersCheckbox);
    }
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('start_date').setAttribute('min', today);
    document.getElementById('end_date').setAttribute('min', today);
});

// Form validation
document.getElementById('bulkSessionForm').addEventListener('submit', function(e) {
    const count = parseInt(document.getElementById('sessionCount').textContent);
    
    if (count === 0) {
        e.preventDefault();
        alert('Please select a valid date range and feeding days. No sessions will be created.');
        return false;
    }
    
    if (count > 200) {
        e.preventDefault();
        alert('Too many sessions! Please select a shorter date range. Maximum 200 sessions at a time.');
        return false;
    }
    
    if (!confirm(`This will create ${count} feeding sessions. Continue?`)) {
        e.preventDefault();
        return false;
    }
});
</script>
</body>
</html>
