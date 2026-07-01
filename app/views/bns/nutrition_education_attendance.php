<?php
$pageTitle = 'Record Attendance';
$activeNav = 'nutrition_education';
require_once __DIR__ . '/../templates/bns_layout.php';
?>
<style>
:root{--kn-green:#6B7A3A;--kn-orange:#C4722A;--kn-dark:#3D4A1E;}
.session-info{background:rgba(107,122,58,.06);border:1.5px solid rgba(107,122,58,.2);border-radius:10px;padding:1.5rem;margin-bottom:2rem;}
.session-info h5{color:var(--kn-dark);font-weight:700;margin-bottom:.8rem;}
.session-meta{display:flex;gap:1.5rem;flex-wrap:wrap;font-size:.9rem;color:#666;}
.session-meta i{color:var(--kn-green);}
.form-card{background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:10px;padding:1.5rem;margin-bottom:2rem;}
.form-label{font-weight:600;color:var(--kn-dark);font-size:.9rem;margin-bottom:.4rem;}
.form-control,.form-select{border:1.5px solid rgba(107,122,58,.2);border-radius:7px;padding:.6rem .9rem;font-size:.9rem;}
.form-control:focus,.form-select:focus{border-color:var(--kn-green);box-shadow:0 0 0 .2rem rgba(107,122,58,.15);}
.btn-submit{background:var(--kn-green);color:#fff;border:none;border-radius:7px;padding:.65rem 1.5rem;font-weight:600;font-size:.9rem;}
.btn-submit:hover{background:#556030;}
.attendance-table{background:#fff;border:1.5px solid rgba(107,122,58,.15);border-radius:10px;overflow:hidden;}
.attendance-table th{background:rgba(107,122,58,.07);color:var(--kn-dark);font-size:.85rem;font-weight:700;padding:.8rem;border-bottom:2px solid rgba(107,122,58,.15);}
.attendance-table td{font-size:.88rem;padding:.8rem;border-bottom:1px solid rgba(107,122,58,.08);}
.badge-present{background:rgba(40,167,69,.1);color:#28a745;padding:.25rem .6rem;border-radius:5px;font-size:.75rem;font-weight:700;}
</style>

<div class="mb-4">
    <a href="index.php?action=nutritionEducationList" class="text-decoration-none" style="color:var(--kn-green)">
        <i class="bi bi-arrow-left"></i> Back to Sessions
    </a>
</div>

<?php if (isset($_SESSION['flash'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> <?= htmlspecialchars($_SESSION['flash']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash']); endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_SESSION['flash_error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>

<!-- Session Info -->
<div class="session-info">
    <h5><?= htmlspecialchars($session['session_title']) ?></h5>
    <div class="session-meta">
        <span><i class="bi bi-calendar3"></i> <?= date('F j, Y', strtotime($session['session_date'])) ?></span>
        <span><i class="bi bi-clock"></i> <?= date('g:i A', strtotime($session['session_time'])) ?></span>
        <span><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($session['venue']) ?></span>
        <span><i class="bi bi-person-check-fill"></i> <?= count($attendance) ?> attendees</span>
    </div>
    <?php if ($session['status'] === 'Planned'): ?>
    <form method="POST" action="index.php?action=startSession" class="mt-3">
        <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
        <button type="submit" class="btn-submit">
            <i class="bi bi-play-circle"></i> Start Session
        </button>
    </form>
    <?php elseif ($session['status'] === 'Ongoing'): ?>
    <div class="mt-3">
        <span class="badge bg-primary">Session is ongoing</span>
    </div>
    <?php elseif ($session['status'] === 'Completed'): ?>
    <div class="mt-3">
        <span class="badge bg-success">Session completed</span>
    </div>
    <?php endif; ?>
</div>

<!-- Attendance Form -->
<?php if ($session['status'] !== 'Completed' && $session['status'] !== 'Cancelled'): ?>
<div class="form-card">
    <h5 class="fw-bold mb-3">Record Attendance</h5>
    <form method="POST" action="index.php?action=saveAttendance" id="attendanceForm">
        <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label" for="attendeeSearch">Select Attendee <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <div class="input-group">
                        <span class="input-group-text" style="background:rgba(107,122,58,.06);border:1.5px solid rgba(107,122,58,.2);border-right:none">
                            <i class="bi bi-search" style="color:var(--kn-green)"></i>
                        </span>
                        <input type="text"
                               class="form-control"
                               id="attendeeSearch"
                               placeholder="Search by first name, last name, or purok..."
                               style="border-left:none"
                               oninput="filterAttendees()"
                               onfocus="showDropdown()"
                               autocomplete="off"
                               required>
                        <button type="button"
                                class="btn btn-sm"
                                id="clearSearchBtn"
                                style="position:absolute;right:8px;top:50%;transform:translateY(-50%);z-index:10;display:none;background:transparent;border:none;color:#999;padding:0 8px"
                                onclick="clearSearch()"
                                title="Clear search">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>
                    <small class="text-muted" id="searchResultCount" style="display:none;font-size:.75rem;margin-top:.25rem;display:block"></small>
                </div>
                
                <!-- Hidden fields for form submission -->
                <input type="hidden" name="user_id" id="selectedUserId" required>
                <input type="hidden" name="full_name" id="selectedFullName" required>
                <input type="hidden" name="purok" id="selectedPurok">
                
                <!-- Hidden data holder for JavaScript -->
                <script type="application/json" id="attendeeData">
                    <?= json_encode(array_map(function($a) {
                        return [
                            'user_id' => $a['user_id'] ?? '',
                            'full_name' => $a['full_name'] ?? '',
                            'purok' => $a['purok'] ?? ''
                        ];
                    }, $availableAttendees)) ?>
                </script>
                
                <!-- Custom dropdown results -->
                <div id="attendeeDropdown" 
                     style="display:none;position:absolute;z-index:1000;background:#fff;border:1.5px solid rgba(107,122,58,.2);border-radius:7px;max-height:300px;overflow-y:auto;width:calc(50% - 12px);box-shadow:0 4px 12px rgba(0,0,0,.12);margin-top:.25rem">
                    <!-- Results will be populated here -->
                </div>
                
                <?php if (empty($availableAttendees)): ?>
                <small class="text-danger mt-1" style="display:block">No family members found. Add family profiles first.</small>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Topics Discussed <span style="font-weight:400;font-size:.8rem;color:var(--kn-muted)">(check all that apply)</span></label>
                <div class="d-flex flex-wrap gap-3 mt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="topic_pinggang_pinoy" id="topicPP" value="1">
                        <label class="form-check-label fw-semibold" for="topicPP" style="font-size:.9rem">
                            Pinggang Pinoy
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="topic_10_kumainments" id="topic10K" value="1">
                        <label class="form-check-label fw-semibold" for="topic10K" style="font-size:.9rem">
                            10 Kumainments
                        </label>
                    </div>
                    <div class="form-check d-flex align-items-center gap-2">
                        <input class="form-check-input" type="checkbox" id="topicOthersCheck" value="1"
                               onchange="document.getElementById('topicOthersText').style.display = this.checked ? 'inline-block' : 'none'">
                        <label class="form-check-label fw-semibold" for="topicOthersCheck" style="font-size:.9rem">Others:</label>
                        <input type="text" name="topic_others" id="topicOthersText"
                               class="form-control form-control-sm" style="display:none;width:160px"
                               placeholder="Specify…" maxlength="100">
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3" style="display:none">
            <div class="col-md-4">
                <label class="form-label" for="purok">Purok</label>
                <input type="text" id="purokDisplay" class="form-control" readonly disabled>
            </div>
        </div>

        <!-- Hidden: signature auto-set to Present -->
        <input type="hidden" name="signature" value="Present">

        <button type="submit" class="btn-submit" id="submitAttendance" <?= empty($availableAttendees) ? 'disabled' : '' ?>>
            <i class="bi bi-check-circle"></i> Record Attendance
        </button>
    </form>
</div>
<?php endif; ?>

<!-- Attendance List -->
<div class="attendance-table">
    <table class="table mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Purok</th>
                <th style="text-align:center">Pinggang Pinoy</th>
                <th style="text-align:center">10 Kumainments</th>
                <th style="text-align:center">Others</th>
                <th style="text-align:center">Present</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($attendance)): ?>
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    No attendance recorded yet.
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($attendance as $i => $a): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td class="fw-semibold">
                    <?= htmlspecialchars($a['full_name']) ?>
                    <?php if (empty($a['user_id'])): ?>
                    <span style="font-size:.72rem;background:rgba(196,114,42,.12);color:var(--kn-orange);border-radius:4px;padding:.1rem .4rem;margin-left:.3rem;font-weight:600">Walk-in</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($a['purok'] ?? '—') ?></td>
                <td style="text-align:center">
                    <?= $a['topic_pinggang_pinoy'] ? '<i class="bi bi-check-lg" style="color:var(--kn-green);font-size:1.1rem"></i>' : '<span style="color:#ccc">—</span>' ?>
                </td>
                <td style="text-align:center">
                    <?= $a['topic_10_kumainments'] ? '<i class="bi bi-check-lg" style="color:var(--kn-green);font-size:1.1rem"></i>' : '<span style="color:#ccc">—</span>' ?>
                </td>
                <td style="text-align:center;font-size:.82rem">
                    <?= $a['topic_others'] ? htmlspecialchars($a['topic_others']) : '<span style="color:#ccc">—</span>' ?>
                </td>
                <td style="text-align:center">
                    <span class="badge-present">✓ Present</span>
                </td>
                <td><?= date('g:i A', strtotime($a['attended_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($session['status'] === 'Ongoing' && count($attendance) > 0): ?>
<div class="mt-3">
    <form method="POST" action="index.php?action=completeSession">
        <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
        <div class="mb-2">
            <label class="form-label">Session Notes (Optional)</label>
            <textarea name="notes" class="form-control" rows="2" 
                      placeholder="Summary of the session, key points discussed, etc."></textarea>
        </div>
        <button type="submit" class="btn-submit">
            <i class="bi bi-check-circle"></i> Complete Session
        </button>
    </form>
</div>
<?php endif; ?>

<script>
let allAttendeeOptions = [];

function clearSearch() {
    const searchInput = document.getElementById('attendeeSearch');
    const clearBtn = document.getElementById('clearSearchBtn');
    const resultCount = document.getElementById('searchResultCount');
    const dropdown = document.getElementById('attendeeDropdown');
    
    searchInput.value = '';
    clearBtn.style.display = 'none';
    resultCount.style.display = 'none';
    dropdown.style.display = 'none';
    
    // Clear hidden fields
    document.getElementById('selectedUserId').value = '';
    document.getElementById('selectedFullName').value = '';
    document.getElementById('selectedPurok').value = '';
    
    // Disable submit button
    const submitBtn = document.getElementById('submitAttendance');
    if (submitBtn) {
        submitBtn.disabled = true;
    }
    
    searchInput.focus();
}

function showDropdown() {
    const searchInput = document.getElementById('attendeeSearch');
    const term = searchInput.value.trim();
    
    // Only show dropdown if there's text and user hasn't selected yet
    if (term && !document.getElementById('selectedUserId').value) {
        filterAttendees();
    }
}

function hideDropdown() {
    setTimeout(() => {
        document.getElementById('attendeeDropdown').style.display = 'none';
    }, 200);
}

function selectAttendee(userId, name, purok) {
    const searchInput = document.getElementById('attendeeSearch');
    const dropdown = document.getElementById('attendeeDropdown');
    const clearBtn = document.getElementById('clearSearchBtn');
    const resultCount = document.getElementById('searchResultCount');
    
    // Set hidden fields for form submission
    document.getElementById('selectedUserId').value = userId;
    document.getElementById('selectedFullName').value = name;
    document.getElementById('selectedPurok').value = purok || '';
    
    // Set search input to show selected person
    searchInput.value = name + (purok ? ' - ' + purok : '');
    
    // Hide dropdown and show clear button
    dropdown.style.display = 'none';
    clearBtn.style.display = 'block';
    resultCount.style.display = 'none';
    
    // Enable submit button
    const submitBtn = document.getElementById('submitAttendance');
    if (submitBtn) {
        submitBtn.disabled = false;
    }
    
    // Make search input readonly after selection to prevent accidental changes
    searchInput.readOnly = true;
    searchInput.style.backgroundColor = '#f8f9fa';
    searchInput.style.cursor = 'not-allowed';
}

function filterAttendees() {
    const searchInput = document.getElementById('attendeeSearch');
    const dropdown = document.getElementById('attendeeDropdown');
    const clearBtn = document.getElementById('clearSearchBtn');
    const resultCount = document.getElementById('searchResultCount');
    const term = (searchInput?.value || '').toLowerCase().trim();
    
    // If user has already selected someone, don't filter
    if (document.getElementById('selectedUserId').value) {
        return;
    }
    
    // Show/hide clear button
    if (clearBtn) {
        clearBtn.style.display = term ? 'block' : 'none';
    }

    if (!term) {
        dropdown.style.display = 'none';
        resultCount.style.display = 'none';
        return;
    }

    // Filter by first name, last name, or purok
    const searchTerms = term.split(' ').filter(t => t.length > 0);
    const matches = allAttendeeOptions.filter(opt => {
        return searchTerms.every(searchTerm => opt.labelLower.includes(searchTerm));
    });

    // Build dropdown HTML
    let html = '';
    
    if (matches.length === 0) {
        html = `<div style="padding:1rem;text-align:center;color:#dc3545;font-size:.9rem">
                    <i class="bi bi-exclamation-circle me-2"></i>No matching attendee found
                </div>`;
    } else {
        matches.forEach(opt => {
            html += `<div class="dropdown-item-custom" 
                         style="padding:.75rem 1rem;cursor:pointer;border-bottom:1px solid rgba(107,122,58,.08);transition:all .15s"
                         onmouseover="this.style.background='rgba(107,122,58,.06)'"
                         onmouseout="this.style.background='#fff'"
                         onclick="selectAttendee('${opt.value}', '${opt.name.replace(/'/g, "\\'")}', '${opt.purok.replace(/'/g, "\\'")}')">
                        <div style="font-weight:600;color:var(--kn-dark);font-size:.9rem">${opt.name}</div>
                        ${opt.purok ? `<div style="font-size:.75rem;color:#666;margin-top:.15rem"><i class="bi bi-geo-alt"></i> ${opt.purok}</div>` : ''}
                     </div>`;
        });
    }
    
    dropdown.innerHTML = html;
    dropdown.style.display = 'block';

    // Show result count
    if (resultCount) {
        if (matches.length > 0) {
            resultCount.textContent = matches.length === 1 
                ? '✓ 1 match found' 
                : `✓ ${matches.length} matches found`;
            resultCount.style.display = 'block';
            resultCount.style.color = 'var(--kn-green)';
        } else {
            resultCount.textContent = '✗ No matches found';
            resultCount.style.display = 'block';
            resultCount.style.color = '#dc3545';
        }
    }
}

function fillAttendeeInfo() {
    // Not needed anymore since we use hidden fields
}

// Form validation and submission
document.getElementById('attendanceForm')?.addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitAttendance');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Recording...';
});

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    // Load attendee data from JSON
    const dataEl = document.getElementById('attendeeData');
    if (dataEl) {
        try {
            const attendeeList = JSON.parse(dataEl.textContent);
            allAttendeeOptions = attendeeList.map(a => ({
                value: a.user_id,
                name: a.full_name,
                purok: a.purok,
                label: a.full_name + (a.purok ? ' - ' + a.purok : ''),
                labelLower: (a.full_name + ' ' + (a.purok || '')).toLowerCase()
            }));
        } catch (e) {
            console.error('Error parsing attendee data:', e);
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('attendeeDropdown');
        const searchInput = document.getElementById('attendeeSearch');
        const clearBtn = document.getElementById('clearSearchBtn');
        
        if (dropdown && searchInput && 
            !dropdown.contains(e.target) && 
            e.target !== searchInput &&
            e.target !== clearBtn) {
            dropdown.style.display = 'none';
        }
    });
    
    // Handle clear button click to re-enable search
    const searchInput = document.getElementById('attendeeSearch');
    const clearBtn = document.getElementById('clearSearchBtn');
    if (searchInput && clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.readOnly = false;
            searchInput.style.backgroundColor = '';
            searchInput.style.cursor = '';
        });
    }

    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Confirmation for completing session
document.querySelectorAll('form[action*="completeSession"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!confirm('Mark this session as completed? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
});
</script>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
