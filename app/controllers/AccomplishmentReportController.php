<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/AccomplishmentReportModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class AccomplishmentReportController {

    private PDO $db;
    private AccomplishmentReportModel $model;
    private NotificationModel $notifModel;

    public function __construct() {
        $this->db         = getDBConnection();
        $this->model      = new AccomplishmentReportModel($this->db);
        $this->notifModel = new NotificationModel($this->db);
    }

    private function requireBNS(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BNS Staff') {
            header('Location: index.php?action=login'); exit;
        }
    }

    private function requireNO2(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Nutrition Officer II') {
            header('Location: index.php?action=login'); exit;
        }
    }

    // ── Process 6: BNS — Generate / Edit Report ───────────────────────────────

    public function showReport(): void {
        $this->requireBNS();
        $bnsId = $_SESSION['user_id'];
        $month = (int)($_GET['month'] ?? date('n'));
        $year  = (int)($_GET['year']  ?? date('Y'));

        $report      = $this->model->getOrCreate($bnsId, $month, $year);
        $reports     = $this->model->getByBns($bnsId);
        $attachments = $this->getAttachments((int)$report['report_id']);

        $monthName = date('F', mktime(0,0,0,$month,1,$year));
        $pageTitle = 'Monthly Accomplishment Report';
        $activeNav = 'accomplishment';
        include __DIR__ . '/../views/bns/accomplishment_report.php';
    }

    public function saveReport(): void {
        $this->requireBNS();
        $bnsId    = $_SESSION['user_id'];
        $reportId = (int)($_POST['report_id'] ?? 0);

        // Verify ownership and editability
        $report = $this->model->getById($reportId);
        if (!$report || $report['bns_id'] != $bnsId || in_array($report['status'], ['Submitted','Approved'])) {
            $_SESSION['flash_error'] = 'Cannot edit this report.';
            header('Location: index.php?action=accomplishmentReport'); exit;
        }

        $this->model->save($reportId, $_POST);
        
        $message = $report['status'] === 'Returned' 
            ? 'Report changes saved successfully.' 
            : 'Report saved as draft.';
        $_SESSION['flash'] = $message;
        
        header("Location: index.php?action=accomplishmentReport&month={$report['report_month']}&year={$report['report_year']}");
        exit;
    }

    public function submitReport(): void {
        $this->requireBNS();
        $bnsId    = $_SESSION['user_id'];
        $reportId = (int)($_POST['report_id'] ?? 0);

        $report = $this->model->getById($reportId);
        if (!$report || $report['bns_id'] != $bnsId) {
            $_SESSION['flash_error'] = 'Report not found.';
            header('Location: index.php?action=accomplishmentReport'); exit;
        }

        // Verify report can be submitted (Draft or Returned status)
        if (!in_array($report['status'], ['Draft', 'Returned'])) {
            $_SESSION['flash_error'] = 'This report cannot be submitted.';
            header("Location: index.php?action=accomplishmentReport&month={$report['report_month']}&year={$report['report_year']}");
            exit;
        }

        // Save latest data first
        $this->model->save($reportId, $_POST);
        $this->model->submit($reportId);

        // Notify all Nutrition Officer II users
        $no2Users = $this->db->query("SELECT user_id FROM users WHERE role_id = 2")->fetchAll(PDO::FETCH_COLUMN);
        $bnsName  = $_SESSION['user_name'] ?? 'BNS Staff';
        $monthName = date('F', mktime(0,0,0,$report['report_month'],1,$report['report_year']));
        $isResubmit = $report['status'] === 'Returned';
        
        foreach ($no2Users as $no2Id) {
            $message = $isResubmit 
                ? "$bnsName has resubmitted the corrected Monthly Accomplishment Report for $monthName {$report['report_year']}."
                : "$bnsName has submitted the Monthly Accomplishment Report for $monthName {$report['report_year']}.";
            
            $this->notifModel->create(
                (int)$no2Id,
                'report_submitted',
                $reportId,
                $message
            );
        }

        $message = $isResubmit 
            ? 'Report resubmitted successfully. The Nutrition Officer II has been notified.'
            : 'Report submitted successfully. The Nutrition Officer II has been notified.';
        $_SESSION['flash'] = $message;
        
        header("Location: index.php?action=accomplishmentReport&month={$report['report_month']}&year={$report['report_year']}");
        exit;
    }

    // ── Process 7: Nutrition Officer II — Review Reports ─────────────────────

    public function showNO2Dashboard(): void {
        $this->requireNO2();
        $pending = $this->model->getPendingForNO2();
        $all     = $this->model->getAllForNO2();

        $pageTitle = 'Report Validation';
        $activeNav = 'validation';
        include __DIR__ . '/../views/nutrition/report_validation.php';
    }

    public function showReportDetail(): void {
        $this->requireNO2();
        $reportId = (int)($_GET['report_id'] ?? 0);
        $report   = $this->model->getById($reportId);

        if (!$report) {
            header('Location: index.php?action=reportValidation'); exit;
        }

        // Load BNS name
        $bnsStmt = $this->db->prepare("SELECT first_name, last_name FROM users WHERE user_id = ?");
        $bnsStmt->execute([$report['bns_id']]);
        $bns = $bnsStmt->fetch(PDO::FETCH_ASSOC);

        // Load attachments for this report
        $attStmt = $this->db->prepare("SELECT * FROM report_attachments WHERE report_id = ? ORDER BY uploaded_at DESC");
        $attStmt->execute([$reportId]);
        $attachments = $attStmt->fetchAll(PDO::FETCH_ASSOC);

        $monthName = date('F', mktime(0,0,0,$report['report_month'],1,$report['report_year']));
        $pageTitle = 'Report Detail';
        $activeNav = 'validation';
        include __DIR__ . '/../views/nutrition/report_detail_no2.php';
    }

    public function approveReport(): void {
        $this->requireNO2();
        $no2Id    = $_SESSION['user_id'];
        $reportId = (int)($_POST['report_id'] ?? 0);

        $report = $this->model->getById($reportId);
        if (!$report || $report['status'] !== 'Submitted') {
            $_SESSION['flash_error'] = 'Report cannot be approved.';
            header('Location: index.php?action=reportValidation'); exit;
        }

        $this->model->approve($reportId, $no2Id, $_POST['signature'] ?? null);

        // Notify BNS
        $monthName = date('F', mktime(0,0,0,$report['report_month'],1,$report['report_year']));
        $this->notifModel->create(
            (int)$report['bns_id'],
            'report_approved',
            $reportId,
            "Your Monthly Accomplishment Report for $monthName {$report['report_year']} has been approved."
        );

        $_SESSION['flash'] = 'Report approved successfully.';
        header('Location: index.php?action=reportValidation'); exit;
    }

    public function returnReport(): void {
        $this->requireNO2();
        $no2Id    = $_SESSION['user_id'];
        $reportId = (int)($_POST['report_id'] ?? 0);
        $reason   = trim($_POST['return_reason'] ?? '');

        if (!$reason) {
            $_SESSION['flash_error'] = 'Please provide a reason for returning the report.';
            header("Location: index.php?action=reportDetail&report_id=$reportId"); exit;
        }

        $report = $this->model->getById($reportId);
        if (!$report || $report['status'] !== 'Submitted') {
            $_SESSION['flash_error'] = 'Report cannot be returned.';
            header('Location: index.php?action=reportValidation'); exit;
        }

        $this->model->returnReport($reportId, $no2Id, $reason);

        // Notify BNS
        $monthName = date('F', mktime(0,0,0,$report['report_month'],1,$report['report_year']));
        $this->notifModel->create(
            (int)$report['bns_id'],
            'report_returned',
            $reportId,
            "Your Monthly Accomplishment Report for $monthName {$report['report_year']} was returned for correction: $reason"
        );

        $_SESSION['flash'] = 'Report returned to BNS for correction.';
        header('Location: index.php?action=reportValidation'); exit;
    }

    // ── Attachments ───────────────────────────────────────────────────────────

    private function getAttachments(int $reportId): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM report_attachments WHERE report_id = ? ORDER BY uploaded_at DESC"
        );
        $stmt->execute([$reportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function uploadAttachment(): void {
        $this->requireBNS();
        $bnsId    = $_SESSION['user_id'];
        $reportId = (int)($_POST['report_id'] ?? 0);
        $label    = trim($_POST['attachment_label'] ?? '');

        $report = $this->model->getById($reportId);
        if (!$report || $report['bns_id'] != $bnsId) {
            $_SESSION['flash_error'] = 'Report not found.';
            header('Location: index.php?action=accomplishmentReport'); exit;
        }

        // Only allow upload on Draft or Returned
        if (!in_array($report['status'], ['Draft', 'Returned'])) {
            $_SESSION['flash_error'] = 'Cannot add attachments to a submitted or approved report.';
            header("Location: index.php?action=accomplishmentReport&month={$report['report_month']}&year={$report['report_year']}");
            exit;
        }

        if (empty($_FILES['attachment_file']['tmp_name'])) {
            $_SESSION['flash_error'] = 'No file selected.';
            header("Location: index.php?action=accomplishmentReport&month={$report['report_month']}&year={$report['report_year']}");
            exit;
        }

        $file     = $_FILES['attachment_file'];
        $origName = basename($file['name']);
        $size     = (int)$file['size'];
        $mime     = mime_content_type($file['tmp_name']);

        // Validate: max 10 MB, allowed types
        $allowed = [
            'application/pdf', 'image/jpeg', 'image/png', 'image/gif',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        if ($size > 10 * 1024 * 1024) {
            $_SESSION['flash_error'] = 'File too large. Maximum size is 10 MB.';
            header("Location: index.php?action=accomplishmentReport&month={$report['report_month']}&year={$report['report_year']}");
            exit;
        }
        if (!in_array($mime, $allowed)) {
            $_SESSION['flash_error'] = 'Invalid file type. Allowed: PDF, images, Word, Excel.';
            header("Location: index.php?action=accomplishmentReport&month={$report['report_month']}&year={$report['report_year']}");
            exit;
        }

        // Store in uploads/report_attachments/{report_id}/
        $uploadDir = __DIR__ . '/../../uploads/report_attachments/' . $reportId . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Sanitize filename and make unique
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
        $stored   = $safeName . '_' . time() . '.' . $ext;
        $destPath = $uploadDir . $stored;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $_SESSION['flash_error'] = 'Failed to save file. Please try again.';
            header("Location: index.php?action=accomplishmentReport&month={$report['report_month']}&year={$report['report_year']}");
            exit;
        }

        // Save record
        $relPath = 'uploads/report_attachments/' . $reportId . '/' . $stored;
        $stmt = $this->db->prepare(
            "INSERT INTO report_attachments (report_id, bns_id, file_name, file_path, file_size, file_type, label)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$reportId, $bnsId, $origName, $relPath, $size, $mime, $label ?: null]);

        $_SESSION['flash'] = 'Attachment uploaded successfully.';
        header("Location: index.php?action=accomplishmentReport&month={$report['report_month']}&year={$report['report_year']}");
        exit;
    }

    public function deleteAttachment(): void {
        $this->requireBNS();
        $bnsId        = $_SESSION['user_id'];
        $attachmentId = (int)($_POST['attachment_id'] ?? 0);

        $stmt = $this->db->prepare("SELECT * FROM report_attachments WHERE attachment_id = ?");
        $stmt->execute([$attachmentId]);
        $att = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$att || $att['bns_id'] != $bnsId) {
            $_SESSION['flash_error'] = 'Attachment not found.';
            header('Location: index.php?action=accomplishmentReport'); exit;
        }

        // Check report is still editable
        $report = $this->model->getById((int)$att['report_id']);
        if (!$report || !in_array($report['status'], ['Draft', 'Returned'])) {
            $_SESSION['flash_error'] = 'Cannot remove attachments from a submitted or approved report.';
            header("Location: index.php?action=accomplishmentReport&month={$report['report_month']}&year={$report['report_year']}");
            exit;
        }

        // Delete physical file
        $fullPath = __DIR__ . '/../../' . $att['file_path'];
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        // Delete DB record
        $del = $this->db->prepare("DELETE FROM report_attachments WHERE attachment_id = ?");
        $del->execute([$attachmentId]);

        $_SESSION['flash'] = 'Attachment removed.';
        header("Location: index.php?action=accomplishmentReport&month={$report['report_month']}&year={$report['report_year']}");
        exit;
    }
}
