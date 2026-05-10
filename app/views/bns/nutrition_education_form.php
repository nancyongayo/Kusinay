<?php
$pageTitle = isset($session) ? 'Edit Session' : 'Set Schedule for Nutrition Education';
$activeNav = 'nutrition_education';
require_once __DIR__ . '/../templates/bns_layout.php';

$isEdit = isset($session);

// Helper: file icon by MIME
function fileIcon(string $mime): string {
    if (str_contains($mime, 'pdf'))        return 'bi-file-earmark-pdf-fill';
    if (str_contains($mime, 'word'))       return 'bi-file-earmark-word-fill';
    if (str_contains($mime, 'powerpoint') || str_contains($mime, 'presentation')) return 'bi-file-earmark-ppt-fill';
    if (str_contains($mime, 'image'))      return 'bi-file-earmark-image-fill';
    return 'bi-file-earmark-fill';
}
function formatBytes(int $bytes): string {
    if ($bytes >= 1048576) return round($bytes/1048576, 1) . ' MB';
    if ($bytes >= 1024)    return round($bytes/1024, 1) . ' KB';
    return $bytes . ' B';
}

// Build topics lookup for JS (id => {name, desc, duration, category, materials})
$topicsMap = [];
foreach ($topics as $t) {
    $topicsMap[(int)$t['topic_id']] = [
        'name'      => $t['topic_name'],
        'desc'      => $t['description'],
        'duration'  => (int)$t['duration_minutes'],
        'category'  => $t['category'] ?? '',
    ];
}

// Determine currently selected topic_id for edit mode
// Match saved topic text back to a topic_id
$savedTopic      = $session['topic'] ?? '';
$savedTopicOther = '';
$selectedTopicId = 0;
foreach ($topics as $t) {
    if ($t['topic_name'] === $savedTopic || $t['description'] === $savedTopic) {
        $selectedTopicId = (int)$t['topic_id'];
        break;
    }
}
if ($isEdit && !$selectedTopicId && $savedTopic) {
    $savedTopicOther = $savedTopic; // it was a custom "Other" topic
    $selectedTopicId = -1; // sentinel for "Other"
}
?>
<style>
:root{--kn-green:#6B7A3A;--kn-green-d:#556030;--kn-orange:#C4722A;--kn-dark:#3D4A1E;--kn-muted:rgba(61,74,30,.55);}

.page-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 1.5rem;
    align-items: start;
}
@media (max-width: 1024px) {
    .page-layout { grid-template-columns: 1fr; }
    .topic-sidebar { order: 2; }
}

/* ── Form card ── */
.form-card {
    background: #fff;
    border: 1.5px solid rgba(107,122,58,.15);
    border-radius: 10px;
    padding: 1.75rem;
}
.form-label { font-weight: 600; color: var(--kn-dark); font-size: .9rem; margin-bottom: .4rem; }
.form-control, .form-select {
    border: 1.5px solid rgba(107,122,58,.2);
    border-radius: 7px;
    padding: .55rem .9rem;
    font-size: .9rem;
    transition: border-color .2s;
}
.form-control:focus, .form-select:focus {
    border-color: var(--kn-green);
    box-shadow: 0 0 0 .2rem rgba(107,122,58,.15);
    outline: none;
}
.btn-submit {
    background: var(--kn-green); color: #fff; border: none;
    border-radius: 7px; padding: .6rem 1.5rem;
    font-weight: 600; font-size: .9rem; cursor: pointer; transition: .2s;
}
.btn-submit:hover { background: var(--kn-green-d); }
.btn-cancel {
    background: #fff; color: var(--kn-dark);
    border: 1.5px solid rgba(61,74,30,.3);
    border-radius: 7px; padding: .6rem 1.5rem;
    font-weight: 600; font-size: .9rem; text-decoration: none; transition: .2s;
}
.btn-cancel:hover { background: rgba(61,74,30,.06); color: var(--kn-dark); }

/* ── Topic detail sidebar ── */
.topic-sidebar { position: sticky; top: 1rem; }

.detail-panel {
    background: #fff;
    border: 1.5px solid rgba(107,122,58,.15);
    border-radius: 10px;
    overflow: hidden;
    transition: .2s;
}
.detail-panel-header {
    background: rgba(107,122,58,.06);
    border-bottom: 1.5px solid rgba(107,122,58,.12);
    padding: .85rem 1.1rem;
    font-weight: 700;
    font-size: .88rem;
    color: var(--kn-dark);
    display: flex;
    align-items: center;
    gap: .5rem;
}
.detail-panel-body { padding: 1.1rem; }

.detail-empty {
    text-align: center;
    padding: 2rem 1rem;
    color: var(--kn-muted);
    font-size: .88rem;
}
.detail-empty i { font-size: 2rem; display: block; margin-bottom: .5rem; opacity: .4; }

.detail-name {
    font-size: 1rem;
    font-weight: 700;
    color: var(--kn-dark);
    margin-bottom: .5rem;
}
.detail-desc {
    font-size: .88rem;
    color: #555;
    line-height: 1.55;
    margin-bottom: .85rem;
}
.detail-badge {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    background: rgba(107,122,58,.08);
    color: var(--kn-green);
    border-radius: 6px;
    padding: .25rem .65rem;
    font-size: .78rem;
    font-weight: 600;
    margin-right: .4rem;
    margin-bottom: .4rem;
}
.detail-section-label {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--kn-muted);
    margin: .85rem 0 .35rem;
}
</style>

<div class="mb-3">
    <a href="index.php?action=nutritionEducationList" class="text-decoration-none" style="color:var(--kn-green);font-size:.9rem">
        <i class="bi bi-arrow-left"></i> Back to Sessions
    </a>
</div>

<?php if (!empty($_SESSION['errors'])): ?>
<div class="alert alert-danger alert-dismissible fade show mb-3">
    <strong><i class="bi bi-exclamation-triangle me-1"></i>Please fix the following:</strong>
    <ul class="mb-0 mt-1">
        <?php foreach ($_SESSION['errors'] as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['errors']); endif; ?>

<?php if (!empty($_SESSION['flash'])): ?>
<div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($_SESSION['flash']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash']); endif; ?>

<div class="page-layout">

    <!-- ── Left: Form ── -->
    <div>
        <div class="form-card">
            <h4 class="fw-bold mb-1"><?= $isEdit ? 'Edit Session' : 'Set Schedule for Nutrition Education' ?></h4>
            <p class="text-muted mb-4" style="font-size:.85rem">
                Fill in the session details. Select a topic from the dropdown to see its description on the right.
            </p>

            <form method="POST" action="index.php?action=saveSession" id="sessionForm" enctype="multipart/form-data">
                <?php if ($isEdit): ?>
                <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
                <?php endif; ?>

                <!-- Session Title -->
                <div class="mb-3">
                    <label class="form-label">Session Title <span class="text-danger">*</span></label>
                    <input type="text" name="session_title" class="form-control"
                           value="<?= htmlspecialchars($session['session_title'] ?? '') ?>"
                           placeholder="e.g., 10 Kumainments Workshop" required>
                </div>

                <!-- Date & Time -->
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="form-label">Session Date <span class="text-danger">*</span></label>
                        <input type="date" name="session_date" class="form-control"
                               value="<?= $session['session_date'] ?? '' ?>"
                               <?= !$isEdit ? 'min="' . date('Y-m-d') . '"' : '' ?> required>
                        <small class="text-muted" style="font-size:.78rem"><?= !$isEdit ? 'Cannot be in the past' : 'You can keep the original date' ?></small>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Session Time <span class="text-danger">*</span></label>
                        <input type="time" name="session_time" class="form-control"
                               value="<?= $session['session_time'] ?? '' ?>" required>
                    </div>
                </div>

                <!-- Venue -->
                <div class="mb-3">
                    <label class="form-label">Venue <span class="text-danger">*</span></label>
                    <input type="text" name="venue" class="form-control"
                           value="<?= htmlspecialchars($session['venue'] ?? '') ?>"
                           placeholder="e.g., Barangay Hall, Health Center" required>
                </div>

                <!-- Topic dropdown -->
                <div class="mb-2">
                    <label class="form-label">Topic <span class="text-danger">*</span></label>
                    <select name="topic_id" id="topicSelect" class="form-select" required
                            onchange="onTopicChange(this)">
                        <option value="">— Select a topic —</option>
                        <?php foreach ($topics as $t): ?>
                        <option value="<?= $t['topic_id'] ?>"
                                <?= $selectedTopicId === (int)$t['topic_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['topic_name']) ?>
                        </option>
                        <?php endforeach; ?>
                        <option value="other" <?= $selectedTopicId === -1 ? 'selected' : '' ?>>
                            Other (specify below)
                        </option>
                    </select>
                    <!-- Hidden field that stores the actual topic text sent to the controller -->
                    <input type="hidden" name="topic" id="topicValue"
                           value="<?= htmlspecialchars($savedTopic) ?>">
                </div>

                <!-- "Other" custom topic input — shown only when Other is selected -->
                <div class="mb-3" id="otherTopicWrap" style="display:<?= $selectedTopicId === -1 ? 'block' : 'none' ?>">
                    <input type="text" id="otherTopicInput" class="form-control"
                           placeholder="Describe the topic…"
                           value="<?= htmlspecialchars($savedTopicOther) ?>"
                           oninput="document.getElementById('topicValue').value = this.value">
                    <small class="text-muted" style="font-size:.78rem">Enter a custom topic not in the list above</small>
                </div>

                <!-- Target Group & Max Participants -->
                <div class="row mb-3">
                    <div class="col-sm-8">
                        <label class="form-label">Target Group</label>
                        <select name="target_group" class="form-select">
                            <option value="">All families</option>
                            <?php foreach (['Pregnant women','Mothers with 0-23 mos','Mothers with 0-59 mos','Fathers','Adolescents','Elderly','Adults','Others'] as $tg): ?>
                            <option value="<?= $tg ?>" <?= ($session['target_group'] ?? '') === $tg ? 'selected' : '' ?>><?= $tg ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label">Max Participants</label>
                        <input type="number" name="max_participants" class="form-control"
                               value="<?= $session['max_participants'] ?? '' ?>"
                               placeholder="Optional" min="1">
                    </div>
                </div>

                <!-- File Uploads -->
                <div class="mb-3">
                    <label class="form-label">Handouts / Materials <span style="font-weight:400;font-size:.8rem;color:var(--kn-muted)">(optional · max 5 files)</span></label>

                    <?php if ($isEdit && !empty($existingMaterials)): ?>
                    <div id="existingFiles" class="mb-2">
                        <?php foreach ($existingMaterials as $m): ?>
                        <div class="d-flex align-items-center gap-2 mb-1 p-2" style="background:rgba(107,122,58,.04);border:1px solid rgba(107,122,58,.15);border-radius:7px">
                            <i class="bi <?= fileIcon($m['file_type']) ?>" style="color:var(--kn-green);font-size:1.1rem;flex-shrink:0"></i>
                            <span style="font-size:.88rem;color:var(--kn-dark);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                <?= htmlspecialchars($m['file_name']) ?>
                            </span>
                            <span style="font-size:.75rem;color:var(--kn-muted);flex-shrink:0"><?= formatBytes($m['file_size']) ?></span>
                            <button type="button"
                                    onclick="deleteMaterialInline(<?= $m['material_id'] ?>, <?= $session['session_id'] ?>)"
                                    style="background:none;border:none;color:#dc3545;cursor:pointer;padding:.1rem .3rem" title="Delete">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- File list (queued for new session) -->
                    <div id="filePreviewList" class="mb-2"></div>

                    <!-- Toolbar -->
                    <div style="border:1.5px solid rgba(107,122,58,.25);border-radius:9px;overflow:hidden">
                        <div style="background:rgba(107,122,58,.06);border-bottom:1.5px solid rgba(107,122,58,.12);padding:.4rem .65rem;display:flex;align-items:center;gap:.5rem">
                            <button type="button" onclick="openFilePickerModal()"
                                    title="Add a file"
                                    style="width:30px;height:30px;border-radius:50%;background:var(--kn-green);color:#fff;border:none;font-size:1.2rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;flex-shrink:0">
                                +
                            </button>
                            <span style="font-size:.78rem;color:var(--kn-muted)">Add file</span>
                            <span style="margin-left:auto;font-size:.75rem;color:var(--kn-muted)">Max 10 MB · Up to 5 files</span>
                        </div>
                        <!-- Drop area -->
                        <div id="dropZone"
                             style="padding:1.25rem 1rem;text-align:center;cursor:pointer;transition:.2s;background:#fff;min-height:72px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.3rem"
                             onclick="openFilePickerModal()"
                             ondragover="event.preventDefault();this.style.background='rgba(107,122,58,.06)'"
                             ondragleave="this.style.background='#fff'"
                             ondrop="handleFileDrop(event)">
                            <i class="bi bi-arrow-down-circle" style="font-size:1.5rem;color:rgba(107,122,58,.35)"></i>
                            <div style="font-size:.85rem;color:var(--kn-muted)">You can drag and drop files here to add them.</div>
                            <?php if (!$isEdit): ?>
                            <div style="font-size:.72rem;color:rgba(107,122,58,.5)">Files will be saved when you click "Set Schedule"</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Hidden real file input (accumulates files) -->
                    <input type="file" name="materialFiles[]" id="materialFiles"
                           multiple
                           accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                           style="display:none">

                    <div id="fileCountError" class="mt-1" style="display:none;color:#dc3545;font-size:.82rem">
                        <i class="bi bi-exclamation-triangle me-1"></i>Maximum 5 files allowed.
                    </div>

                    <?php if ($isEdit): ?>
                    <div class="mt-2">
                        <button type="button" onclick="submitUpload()" id="uploadBtn"
                                style="display:none;align-items:center;gap:.4rem;background:var(--kn-green);color:#fff;border:none;border-radius:8px;padding:.38rem 1rem;font-size:.88rem;font-weight:600;cursor:pointer">
                            <i class="bi bi-upload"></i> Upload Files
                        </button>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Notes -->
                <div class="mb-4">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"
                              placeholder="Additional notes or reminders"><?= htmlspecialchars($session['notes'] ?? '') ?></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="bi bi-check-circle me-1"></i><?= $isEdit ? 'Update Session' : 'Set Schedule' ?>
                    </button>
                    <a href="index.php?action=nutritionEducationList" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <!-- ── Right: Topic Detail Panel ── -->
    <div class="topic-sidebar">
        <div class="detail-panel">
            <div class="detail-panel-header">
                <i class="bi bi-info-circle-fill" style="color:var(--kn-green)"></i>
                Topic Details
            </div>
            <div class="detail-panel-body" id="topicDetailBody">
                <div class="detail-empty" id="detailEmpty">
                    <i class="bi bi-book"></i>
                    Select a topic from the dropdown to see its details here.
                </div>
                <div id="detailContent" style="display:none">
                    <div class="detail-name" id="detailName"></div>
                    <div class="detail-desc" id="detailDesc"></div>
                    <div>
                        <span class="detail-badge" id="detailDuration">
                            <i class="bi bi-clock"></i> Suggested duration: <span></span>
                        </span>
                        <span class="detail-badge" id="detailCategory">
                            <i class="bi bi-tag"></i> <span></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div><!-- /page-layout -->

<?php if (isset($_SESSION['form_data'])): unset($_SESSION['form_data']); endif; ?>

<!-- ── File Picker Modal ── -->
<div id="filePickerModal"
     style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.55);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:.85rem;width:92vw;max-width:520px;box-shadow:0 8px 40px rgba(0,0,0,.25);overflow:hidden">
        <!-- Header -->
        <div style="background:rgba(107,122,58,.06);border-bottom:1.5px solid rgba(107,122,58,.12);padding:.75rem 1.1rem;display:flex;align-items:center;justify-content:space-between">
            <span style="font-weight:700;font-size:.95rem;color:var(--kn-dark)">
                <i class="bi bi-upload me-2" style="color:var(--kn-green)"></i>Upload a file
            </span>
            <button onclick="closeFilePickerModal()"
                    style="background:none;border:none;font-size:1.3rem;color:#888;cursor:pointer;line-height:1">&times;</button>
        </div>
        <!-- Body -->
        <div style="padding:1.25rem">
            <div class="mb-3">
                <label style="font-size:.88rem;font-weight:600;color:var(--kn-dark);display:block;margin-bottom:.35rem">Attachment</label>
                <input type="file" id="pickerFileInput"
                       accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                       style="width:100%;border:1.5px solid rgba(107,122,58,.25);border-radius:7px;padding:.3rem .5rem;font-size:.88rem;background:#fff"
                       onchange="onPickerFileChange(this)">
            </div>
            <div class="mb-3">
                <label style="font-size:.88rem;font-weight:600;color:var(--kn-dark);display:block;margin-bottom:.35rem">Save as</label>
                <input type="text" id="pickerSaveAs" class="form-control"
                       placeholder="Filename (optional — leave blank to use original)"
                       style="font-size:.88rem">
            </div>
            <div id="pickerError" style="display:none;color:#dc3545;font-size:.82rem;margin-bottom:.75rem">
                <i class="bi bi-exclamation-triangle me-1"></i><span></span>
            </div>
            <div style="display:flex;gap:.65rem;justify-content:flex-end">
                <button type="button" onclick="closeFilePickerModal()"
                        style="background:#fff;color:var(--kn-dark);border:1.5px solid rgba(61,74,30,.25);border-radius:7px;padding:.45rem 1.1rem;font-size:.88rem;font-weight:600;cursor:pointer">
                    Cancel
                </button>
                <button type="button" onclick="confirmPickerFile()" id="pickerConfirmBtn" disabled
                        style="background:var(--kn-green);color:#fff;border:none;border-radius:7px;padding:.45rem 1.25rem;font-size:.88rem;font-weight:600;cursor:pointer;opacity:.6">
                    <i class="bi bi-upload me-1"></i> Upload this file
                </button>
            </div>
        </div>
    </div>
</div>

<?php if ($isEdit): ?>
<!-- Hidden upload form outside sessionForm to avoid nested form issue -->
<form method="POST" action="index.php?action=uploadMaterial"
      enctype="multipart/form-data" id="uploadMaterialForm" style="display:none">
    <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
    <input type="file" name="materialFiles[]" id="hiddenMaterialFiles" multiple>
</form>

<!-- Hidden delete form -->
<form method="POST" action="index.php?action=deleteMaterial"
      id="deleteMaterialForm" style="display:none">
    <input type="hidden" name="material_id" id="deleteMaterialId">
    <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
</form>
<?php endif; ?>

<script>
// Topics data from PHP
const TOPICS = <?= json_encode($topicsMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

function onTopicChange(sel) {
    const val = sel.value;
    const otherWrap   = document.getElementById('otherTopicWrap');
    const topicValue  = document.getElementById('topicValue');
    const otherInput  = document.getElementById('otherTopicInput');

    if (val === 'other') {
        otherWrap.style.display = 'block';
        topicValue.value = otherInput.value;
        showDetailEmpty();
        // Auto-fill title hint
        document.querySelector('[name="session_title"]').placeholder = 'Enter your session title…';
    } else if (val === '') {
        otherWrap.style.display = 'none';
        topicValue.value = '';
        showDetailEmpty();
    } else {
        otherWrap.style.display = 'none';
        const t = TOPICS[parseInt(val)];
        if (t) {
            // Set hidden topic value to the topic name
            topicValue.value = t.name;

            // Auto-fill session title if empty
            const titleField = document.querySelector('[name="session_title"]');
            if (!titleField.value.trim()) {
                titleField.value = t.name + ' Session';
            }

            // Show detail panel
            showDetailContent(t);
        }
    }
}

function showDetailContent(t) {
    document.getElementById('detailEmpty').style.display   = 'none';
    document.getElementById('detailContent').style.display = 'block';
    document.getElementById('detailName').textContent = t.name;
    document.getElementById('detailDesc').textContent = t.desc || '—';
    document.getElementById('detailDuration').querySelector('span').textContent = t.duration + ' min';
    const catEl = document.getElementById('detailCategory');
    if (t.category) {
        catEl.querySelector('span').textContent = t.category;
        catEl.style.display = 'inline-flex';
    } else {
        catEl.style.display = 'none';
    }
}

function showDetailEmpty() {
    document.getElementById('detailEmpty').style.display   = 'block';
    document.getElementById('detailContent').style.display = 'none';
}

// On page load: if editing and a topic is already selected, show its details
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('topicSelect');
    if (sel.value && sel.value !== 'other' && sel.value !== '') {
        const t = TOPICS[parseInt(sel.value)];
        if (t) showDetailContent(t);
    }

    // Auto-dismiss alerts
    document.querySelectorAll('.alert').forEach(a => {
        setTimeout(() => new bootstrap.Alert(a).close(), 5000);
    });
});

// Prevent double-submit
document.getElementById('sessionForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';
});

// ── Edit mode: upload via separate form outside sessionForm ───────────────────
function submitUpload() {
    // Called from confirmPickerFile() in edit mode — form already submitted there
    // This is a no-op fallback
}

function deleteMaterialInline(materialId, sessionId) {
    if (!confirm('Delete this file?')) return;
    document.getElementById('deleteMaterialId').value = materialId;
    document.getElementById('deleteMaterialForm').submit();
}

// Close file picker modal on backdrop click or Escape
document.getElementById('filePickerModal').addEventListener('click', function(e) {
    if (e.target === this) closeFilePickerModal();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeFilePickerModal();
});

// ── File picker modal ────────────────────────────────────────────────────────
const MAX_FILES = 5;
const isEditMode = <?= $isEdit ? 'true' : 'false' ?>;
let queuedFiles = []; // accumulates File objects for new session

function openFilePickerModal() {
    const total = queuedFiles.length + <?= $isEdit ? 'document.querySelectorAll("#existingFiles > div").length' : '0' ?>;
    if (total >= MAX_FILES) {
        alert('Maximum ' + MAX_FILES + ' files allowed.');
        return;
    }
    document.getElementById('pickerFileInput').value = '';
    document.getElementById('pickerSaveAs').value = '';
    document.getElementById('pickerError').style.display = 'none';
    document.getElementById('pickerConfirmBtn').disabled = true;
    document.getElementById('pickerConfirmBtn').style.opacity = '.6';
    document.getElementById('filePickerModal').style.display = 'flex';
}

function closeFilePickerModal() {
    document.getElementById('filePickerModal').style.display = 'none';
}

function onPickerFileChange(input) {
    const btn = document.getElementById('pickerConfirmBtn');
    const err = document.getElementById('pickerError');
    if (!input.files.length) { btn.disabled = true; btn.style.opacity = '.6'; return; }

    const f = input.files[0];
    // Validate size
    if (f.size > 10 * 1024 * 1024) {
        err.querySelector('span').textContent = 'File exceeds 10 MB limit.';
        err.style.display = 'block';
        btn.disabled = true; btn.style.opacity = '.6';
        return;
    }
    err.style.display = 'none';
    // Pre-fill Save as with original name
    if (!document.getElementById('pickerSaveAs').value) {
        document.getElementById('pickerSaveAs').value = f.name;
    }
    btn.disabled = false; btn.style.opacity = '1';
}

function confirmPickerFile() {
    const input   = document.getElementById('pickerFileInput');
    const saveAs  = document.getElementById('pickerSaveAs').value.trim() || input.files[0].name;
    const file    = input.files[0];

    if (!file) return;

    if (isEditMode) {
        // Edit mode: submit immediately via hidden form
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('hiddenMaterialFiles').files = dt.files;
        closeFilePickerModal();
        document.getElementById('uploadMaterialForm').submit();
    } else {
        // New session: queue the file
        // Rename the file object using a new File with the saveAs name
        const renamed = new File([file], saveAs, { type: file.type });
        queuedFiles.push(renamed);
        syncQueuedFilesToInput();
        renderQueuedFiles();
        closeFilePickerModal();
    }
}

function syncQueuedFilesToInput() {
    const dt = new DataTransfer();
    queuedFiles.forEach(f => dt.items.add(f));
    document.getElementById('materialFiles').files = dt.files;
}

function removeQueuedFile(idx) {
    queuedFiles.splice(idx, 1);
    syncQueuedFilesToInput();
    renderQueuedFiles();
}

function renderQueuedFiles() {
    const list = document.getElementById('filePreviewList');
    list.innerHTML = '';
    queuedFiles.forEach((f, i) => {
        const size = f.size >= 1048576 ? (f.size/1048576).toFixed(1)+' MB' :
                     f.size >= 1024    ? (f.size/1024).toFixed(1)+' KB' : f.size+' B';
        const div = document.createElement('div');
        div.className = 'd-flex align-items-center gap-2 mb-1 p-2';
        div.style.cssText = 'background:rgba(107,122,58,.04);border:1px solid rgba(107,122,58,.15);border-radius:7px';
        div.innerHTML = `
            <i class="bi bi-file-earmark" style="color:var(--kn-green);font-size:1rem;flex-shrink:0"></i>
            <span style="font-size:.88rem;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${f.name}</span>
            <span style="font-size:.75rem;color:var(--kn-muted);flex-shrink:0">${size}</span>
            <button type="button" onclick="removeQueuedFile(${i})"
                    style="background:none;border:none;color:#dc3545;cursor:pointer;padding:.1rem .3rem">
                <i class="bi bi-trash3"></i>
            </button>`;
        list.appendChild(div);
    });
}

function handleFileDrop(e) {
    e.preventDefault();
    document.getElementById('dropZone').style.background = '#fff';
    const files = e.dataTransfer.files;
    if (!files.length) return;

    // Open picker modal pre-filled with the dropped file
    const input = document.getElementById('pickerFileInput');
    const dt = new DataTransfer();
    dt.items.add(files[0]); // one at a time
    input.files = dt.files;
    onPickerFileChange(input);
    openFilePickerModal();
}
</script>

<?php require_once __DIR__ . '/../templates/bns_layout_end.php'; ?>
