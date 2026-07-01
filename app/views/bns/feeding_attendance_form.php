<?php
/**
 * BNS - Feeding Attendance Form (Process 16: Participating in Feeding Program)
 * Based on City Health Office attendance form
 */
$pageTitle = 'Attendance - ' . ($session['activity_name'] ?? 'Feeding Session');
$activeNav = 'feeding_program';
include __DIR__ . '/../templates/bns_layout.php';
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
                            <a href="index.php?action=feedingSessions&proposal_id=<?= $session['proposal_id'] ?>" style="color: #5A7038; text-decoration: none; font-weight: 500;">
                                Sessions
                            </a>
                        </li>
                        <li class="breadcrumb-item active" style="color: var(--kn-dark);">Attendance</li>
                    </ol>
                </nav>

                <h2 class="mb-2" style="color: var(--kn-dark); font-weight: 700;">
                    <i class="bi bi-check2-square me-2" style="color: #5A7038;"></i>
                    <?= htmlspecialchars($session['activity_name']) ?>
                </h2>
                <p class="text-muted">
                    <i class="bi bi-calendar-event me-1" style="color: #C4722A;"></i>
                    <?= date('F j, Y', strtotime($session['session_date'])) ?>
                    <span class="mx-2">•</span>
                    <i class="bi bi-geo-alt-fill me-1" style="color: #5A7038;"></i>
                    <?= htmlspecialchars($session['purok_barangay']) ?>
                </p>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <a href="index.php?action=sessionQRCode&session_id=<?= $session['session_id'] ?>" 
                       class="btn" style="background: linear-gradient(135deg, #C4722A 0%, #A85F22 100%); color: #fff; border: none; padding: .6rem 1.2rem; border-radius: 10px 0 0 10px; font-weight: 600;">
                        <i class="bi bi-qr-code me-1"></i>
                        Session QR Code
                    </a>
                    <button type="button" class="btn" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); color: #fff; border: none; border-left: 1px solid rgba(255,255,255,.3); padding: .6rem 1.2rem; border-radius: 0 10px 10px 0; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#addAttendanceModal">
                        <i class="bi bi-plus-circle me-1"></i>
                        Add Manually
                    </button>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['flash']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0" style="background: linear-gradient(135deg, rgba(90,112,56,.12) 0%, rgba(90,112,56,.08) 100%); border-radius: 12px;">
                    <div class="card-body text-center">
                        <h3 class="mb-0" style="color: #5A7038; font-weight: 700;"><?= $stats['total_records'] ?? 0 ?></h3>
                        <small style="color: var(--kn-dark); font-weight: 600;">Total Records</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0" style="background: linear-gradient(135deg, rgba(90,112,56,.12) 0%, rgba(90,112,56,.08) 100%); border-radius: 12px;">
                    <div class="card-body text-center">
                        <h3 class="mb-0" style="color: #5A7038; font-weight: 700;"><?= $stats['present_count'] ?? 0 ?></h3>
                        <small style="color: var(--kn-dark); font-weight: 600;">Present</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0" style="background: linear-gradient(135deg, rgba(196,114,42,.12) 0%, rgba(196,114,42,.08) 100%); border-radius: 12px;">
                    <div class="card-body text-center">
                        <h3 class="mb-0" style="color: #C4722A; font-weight: 700;"><?= $stats['pinggang_pinoy_count'] ?? 0 ?></h3>
                        <small style="color: var(--kn-dark); font-weight: 600;">Pinggang Pinoy</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0" style="background: linear-gradient(135deg, rgba(196,114,42,.12) 0%, rgba(196,114,42,.08) 100%); border-radius: 12px;">
                    <div class="card-body text-center">
                        <h3 class="mb-0" style="color: #C4722A; font-weight: 700;"><?= $stats['id_kumainments_count'] ?? 0 ?></h3>
                        <small style="color: var(--kn-dark); font-weight: 600;">ID Kumainments</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="card border-0" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.08);">
            <div class="card-header" style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15); border-radius: 16px 16px 0 0 !important; padding: 1rem 1.5rem;">
                <h5 class="mb-0" style="color: var(--kn-dark); font-weight: 700;">Attendance List</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($attendanceRecords)): ?>
                    <div class="alert m-3" style="background: linear-gradient(135deg, rgba(196,114,42,.08) 0%, rgba(196,114,42,.04) 100%); border-left: 4px solid #C4722A; border-radius: 12px; color: var(--kn-dark);">
                        <i class="bi bi-info-circle me-2" style="color: #C4722A;"></i>
                        No attendance records yet. Click "Add Participant" to start recording attendance.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead style="background: linear-gradient(135deg, rgba(90,112,56,.08) 0%, rgba(90,112,56,.04) 100%); border-bottom: 2px solid rgba(90,112,56,.15);">
                                <tr>
                                    <th style="width: 40px; color: var(--kn-dark); font-weight: 700; padding: 1rem;">No</th>
                                    <th style="color: var(--kn-dark); font-weight: 700;">Name of Client</th>
                                    <th style="color: var(--kn-dark); font-weight: 700;">Mother/Guardian</th>
                                    <th style="color: var(--kn-dark); font-weight: 700;">Purok</th>
                                    <th style="width: 80px; color: var(--kn-dark); font-weight: 700;" class="text-center">Pinggang Pinoy</th>
                                    <th style="width: 80px; color: var(--kn-dark); font-weight: 700;" class="text-center">ID Kumainments</th>
                                    <th style="color: var(--kn-dark); font-weight: 700;">Others</th>
                                    <th style="width: 80px; color: var(--kn-dark); font-weight: 700;" class="text-center">Present</th>
                                    <th style="width: 100px; color: var(--kn-dark); font-weight: 700;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendanceRecords as $index => $record): ?>
                                    <tr style="border-bottom: 1px solid rgba(0,0,0,.05);">
                                        <td style="padding: 1rem;"><?= $index + 1 ?></td>
                                        <td style="color: var(--kn-dark);"><strong><?= htmlspecialchars($record['name_of_client']) ?></strong></td>
                                        <td style="color: var(--kn-muted);"><?= htmlspecialchars($record['mother_name'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($record['purok'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <?= $record['pinggang_pinoy'] ? '<i class="bi bi-check-circle-fill" style="color: #5A7038;"></i>' : '-' ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $record['id_kumainments'] ? '<i class="bi bi-check-circle-fill" style="color: #5A7038;"></i>' : '-' ?>
                                        </td>
                                        <td><?= htmlspecialchars($record['others'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <?php if ($record['is_present']): ?>
                                                <span class="badge" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); padding: .3rem .6rem; border-radius: 6px; font-weight: 600;">Yes</span>
                                            <?php else: ?>
                                                <span class="badge" style="background: linear-gradient(135deg, rgba(107,114,128,.15) 0%, rgba(107,114,128,.1) 100%); color: #374151; border: 1px solid rgba(107,114,128,.2); padding: .3rem .6rem; border-radius: 6px; font-weight: 600;">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-sm" style="background: transparent; color: #C4722A; border: 1px solid #C4722A; padding: .25rem .5rem;" 
                                                        onclick="editAttendance(<?= htmlspecialchars(json_encode($record)) ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm" style="background: transparent; color: #dc2626; border: 1px solid #dc2626; padding: .25rem .5rem;" 
                                                        onclick="deleteAttendance(<?= $record['attendance_id'] ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4">
            <a href="index.php?action=feedingSessions&proposal_id=<?= $session['proposal_id'] ?>" 
               class="btn" style="background: transparent; color: var(--kn-dark); border: 2px solid #e5e7eb; padding: .6rem 1.5rem; border-radius: 10px; font-weight: 600;">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Sessions
            </a>
        </div>
    </div>

    <!-- Add/Edit Attendance Modal -->
    <div class="modal fade" id="addAttendanceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="index.php?action=saveFeedingAttendance" id="attendanceForm">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
                    <input type="hidden" name="attendance_id" id="attendance_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add Participant</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name of Client <span class="text-danger">*</span></label>
                                <input type="text" name="name_of_client" id="name_of_client" 
                                       class="form-control" required placeholder="Child's name">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Mother/Guardian Name</label>
                                <input type="text" name="mother_name" id="mother_name" 
                                       class="form-control" placeholder="Mother or guardian name">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Purok</label>
                                <input type="text" name="purok" id="purok" 
                                       class="form-control" placeholder="e.g., Purok 1">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Time In</label>
                                <input type="time" name="time_in" id="time_in" class="form-control">
                            </div>

                            <div class="col-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" 
                                           name="pinggang_pinoy" id="pinggang_pinoy" value="1">
                                    <label class="form-check-label" for="pinggang_pinoy">
                                        Pinggang Pinoy
                                    </label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" 
                                           name="id_kumainments" id="id_kumainments" value="1">
                                    <label class="form-check-label" for="id_kumainments">
                                        ID Kumainments
                                    </label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" 
                                           name="is_present" id="is_present" value="1" checked>
                                    <label class="form-check-label" for="is_present">
                                        Present
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Others (Notes)</label>
                                <textarea name="others" id="others" class="form-control" rows="2"
                                          placeholder="Additional notes"></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Meal Received</label>
                                <input type="text" name="meal_received" id="meal_received" 
                                       class="form-control" placeholder="e.g., Rice, vegetables, fish">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn" style="background: transparent; color: var(--kn-dark); border: 2px solid #e5e7eb; padding: .5rem 1.2rem; border-radius: 10px; font-weight: 600;" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%); color: #fff; border: none; padding: .5rem 1.2rem; border-radius: 10px; font-weight: 600;">Save Attendance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteAttendanceForm" method="POST" action="index.php?action=deleteFeedingAttendance" style="display:none;">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        <input type="hidden" name="attendance_id" id="deleteAttendanceId">
    </form>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,.15);">
                <div class="modal-header" style="background: linear-gradient(135deg, rgba(220,38,38,.1) 0%, rgba(220,38,38,.05) 100%); border-bottom: 2px solid rgba(220,38,38,.2); border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title" style="color: #dc2626; font-weight: 700;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 1.5rem;">
                    <div class="text-center mb-3">
                        <i class="bi bi-trash" style="font-size: 3rem; color: #dc2626; opacity: .3;"></i>
                    </div>
                    <p class="text-center mb-0" style="font-size: 1.05rem; color: var(--kn-dark);">
                        Are you sure you want to delete this attendance record?
                    </p>
                    <p class="text-center text-muted small mb-0 mt-2">
                        This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(0,0,0,.1); padding: 1rem 1.5rem;">
                    <button type="button" class="btn" style="background: transparent; color: var(--kn-dark); border: 2px solid #e5e7eb; padding: .5rem 1.2rem; border-radius: 10px; font-weight: 600;" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: #fff; border: none; padding: .5rem 1.2rem; border-radius: 10px; font-weight: 600;" onclick="confirmDelete()">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function editAttendance(record) {
        document.getElementById('modalTitle').textContent = 'Edit Participant';
        document.getElementById('attendance_id').value = record.attendance_id;
        document.getElementById('name_of_client').value = record.name_of_client;
        document.getElementById('mother_name').value = record.mother_name || '';
        document.getElementById('purok').value = record.purok || '';
        document.getElementById('time_in').value = record.time_in || '';
        document.getElementById('pinggang_pinoy').checked = record.pinggang_pinoy == 1;
        document.getElementById('id_kumainments').checked = record.id_kumainments == 1;
        document.getElementById('is_present').checked = record.is_present == 1;
        document.getElementById('others').value = record.others || '';
        document.getElementById('meal_received').value = record.meal_received || '';

        new bootstrap.Modal(document.getElementById('addAttendanceModal')).show();
    }

    function deleteAttendance(attendanceId) {
        // Store the attendance ID for deletion
        document.getElementById('deleteAttendanceId').value = attendanceId;
        
        // Show the modal
        new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
    }

    function confirmDelete() {
        // Close the modal
        bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal')).hide();
        
        // Submit the form
        document.getElementById('deleteAttendanceForm').submit();
    }

    // Reset form when modal is closed
    document.getElementById('addAttendanceModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('attendanceForm').reset();
        document.getElementById('attendance_id').value = '';
        document.getElementById('modalTitle').textContent = 'Add Participant';
        document.getElementById('is_present').checked = true;
    });
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
</script>
</body>
</html>
