<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/NutritionAssessmentModel.php';
require_once __DIR__ . '/../../core/NutritionCalculator.php';

class NutritionAssessmentController {

    private PDO $db;
    private NutritionAssessmentModel $model;

    public function __construct() {
        $this->db    = getDBConnection();
        $this->model = new NutritionAssessmentModel($this->db);
    }

    private function requireBNS(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BNS Staff') {
            header('Location: index.php?action=login'); exit;
        }
    }

    // ── Process 3: Data Encoding — Qualified Lists ────────────────────────────

    public function showDataEncoding(): void {
        $this->requireBNS();
        $bnsId = $_SESSION['user_id'];
        $tab   = $_GET['tab'] ?? 'children';

        $children = $this->model->getQualifiedChildren($bnsId);
        $maternal = $this->model->getQualifiedMaternal($bnsId);
        $seniors  = $this->model->getQualifiedSeniors($bnsId);
        $recent   = $this->model->getRecentByBns($bnsId, 20);

        $pageTitle = 'Resident Assessment';
        $activeNav = 'data_encoding';
        include __DIR__ . '/../views/bns/data_encoding.php';
    }

    // ── Process 4 + 5: Assessment Form & Save ────────────────────────────────

    public function showAssessmentForm(): void {
        $this->requireBNS();
        $bnsId = $_SESSION['user_id'];

        $type    = $_GET['type']     ?? 'child';   // child | maternal | senior
        $childId = (int)($_GET['child_id'] ?? 0);
        $userId  = (int)($_GET['user_id']  ?? 0);

        $subject = null;

        if ($type === 'child' && $childId) {
            $stmt = $this->db->prepare("
                SELECT hc.*, h.purok,
                       CONCAT(u.first_name,' ',u.last_name) AS caregiver_name,
                       TRIM(CONCAT(
                           COALESCE(hc.last_name, ''), ', ',
                           COALESCE(hc.first_name, ''),
                           IF(hc.middle_name IS NOT NULL AND hc.middle_name != '', CONCAT(' ', hc.middle_name), ''),
                           IF(hc.suffix IS NOT NULL AND hc.suffix != '', CONCAT(' ', hc.suffix), '')
                       )) AS full_name
                FROM household_children hc
                JOIN households h ON h.household_id = hc.household_id
                JOIN household_members hm ON hm.household_id = h.household_id
                JOIN users u ON u.user_id = hm.user_id
                WHERE hc.child_id = :cid LIMIT 1
            ");
            $stmt->execute([':cid' => $childId]);
            $subject = $stmt->fetch(PDO::FETCH_ASSOC);
        } elseif ($type === 'child' && isset($_GET['fm_member_id'])) {
            // BNS-added child from family_members
            $fmId = (int)$_GET['fm_member_id'];
            $stmt = $this->db->prepare("
                SELECT fm.member_id AS fm_member_id,
                       NULL AS child_id,
                       TRIM(CONCAT(
                           COALESCE(fm.last_name, ''), ', ',
                           COALESCE(fm.first_name, ''),
                           IF(fm.middle_name IS NOT NULL AND fm.middle_name != '', CONCAT(' ', fm.middle_name), ''),
                           IF(fm.suffix IS NOT NULL AND fm.suffix != '', CONCAT(' ', fm.suffix), '')
                       )) AS full_name,
                       fm.sex, fm.dob,
                       fp.purok,
                       COALESCE(
                           (SELECT CONCAT(hm_head.last_name, ', ', hm_head.first_name)
                            FROM family_members hm_head
                            WHERE hm_head.family_id = fp.family_id AND hm_head.role = 'Head' LIMIT 1),
                           (SELECT CONCAT(u2.first_name,' ',u2.last_name)
                            FROM users u2 WHERE u2.user_id = fp.source_user_id LIMIT 1),
                           'BNS Encoded'
                       ) AS caregiver_name
                FROM family_members fm
                JOIN family_profiles fp ON fp.family_id = fm.family_id
                WHERE fm.member_id = :fmid AND fm.role = 'Child'
                LIMIT 1
            ");
            $stmt->execute([':fmid' => $fmId]);
            $subject = $stmt->fetch(PDO::FETCH_ASSOC);
        } elseif (in_array($type, ['maternal','senior']) && $userId) {
            $stmt = $this->db->prepare("
                SELECT u.user_id, CONCAT(u.first_name,' ',u.last_name) AS full_name,
                       u.gender AS sex, u.birthdate AS dob,
                       COALESCE(h.purok, fp2.purok) AS purok,
                       uhp.pregnancy_status, uhp.breastfeeding_status
                FROM users u
                LEFT JOIN household_members hm ON hm.user_id = u.user_id
                LEFT JOIN households h ON h.household_id = hm.household_id
                LEFT JOIN family_profiles fp2 ON fp2.source_user_id = u.user_id
                LEFT JOIN user_health_profiles uhp ON uhp.user_id = u.user_id
                WHERE u.user_id = :uid LIMIT 1
            ");
            $stmt->execute([':uid' => $userId]);
            $subject = $stmt->fetch(PDO::FETCH_ASSOC);
        } elseif ($type === 'maternal' && isset($_GET['fm_member_id'])) {
            // BNS-encoded female member (no user account) — look up from family_members
            // role can be 'Head' or 'Wife' depending on who is female in the household
            $fmId = (int)$_GET['fm_member_id'];
            $stmt = $this->db->prepare("
                SELECT fm.member_id AS fm_member_id,
                       NULL AS user_id,
                       TRIM(CONCAT(
                           COALESCE(fm.last_name, ''), ', ',
                           COALESCE(fm.first_name, ''),
                           IF(fm.middle_name IS NOT NULL AND fm.middle_name != '', CONCAT(' ', fm.middle_name), '')
                       )) AS full_name,
                       fm.sex, fm.dob,
                       fp.purok,
                       NULL AS pregnancy_status,
                       'EBF (Exclusive Breastfeeding)' AS breastfeeding_status
                FROM family_members fm
                JOIN family_profiles fp ON fp.family_id = fm.family_id
                WHERE fm.member_id = :fmid AND fm.role IN ('Head','Wife')
                LIMIT 1
            ");
            $stmt->execute([':fmid' => $fmId]);
            $subject = $stmt->fetch(PDO::FETCH_ASSOC);
        } elseif ($type === 'senior' && isset($_GET['fm_member_id'])) {
            // BNS-encoded senior (no user account) — look up from family_members
            $fmId = (int)$_GET['fm_member_id'];
            $stmt = $this->db->prepare("
                SELECT fm.member_id AS fm_member_id,
                       NULL AS user_id,
                       TRIM(CONCAT(
                           COALESCE(fm.last_name, ''), ', ',
                           COALESCE(fm.first_name, ''),
                           IF(fm.middle_name IS NOT NULL AND fm.middle_name != '', CONCAT(' ', fm.middle_name), '')
                       )) AS full_name,
                       fm.sex, fm.dob,
                       fp.purok
                FROM family_members fm
                JOIN family_profiles fp ON fp.family_id = fm.family_id
                WHERE fm.member_id = :fmid AND fm.role IN ('Head','Wife')
                LIMIT 1
            ");
            $stmt->execute([':fmid' => $fmId]);
            $subject = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if (!$subject) {
            $_SESSION['flash_error'] = 'Subject not found.';
            header('Location: index.php?action=dataEncoding'); exit;
        }

        $pageTitle = 'Assessment Form';
        $activeNav = 'data_encoding';
        include __DIR__ . '/../views/bns/assessment_form.php';
    }

    // ── Process 4 + 5: Save Assessment ───────────────────────────────────────

    public function saveAssessment(): void {
        $this->requireBNS();
        $bnsId = $_SESSION['user_id'];

        $type   = $_POST['assessed_type'] ?? 'child';
        $weight = (float) ($_POST['weight_kg'] ?? 0);
        $height = (float) ($_POST['height_cm'] ?? 0);
        $dob    = trim($_POST['dob'] ?? '');
        // Normalize sex: accept 'Male'/'Female' or 'M'/'F'
        $sexRaw = trim($_POST['sex'] ?? '');
        $sex    = match(strtolower($sexRaw)) {
            'male', 'm'   => 'M',
            'female', 'f' => 'F',
            default       => $sexRaw,
        };
        $date   = trim($_POST['assessment_date'] ?? date('Y-m-d'));

        if (!$weight || !$height || !$dob || !$sex) {
            $_SESSION['errors'] = ['Weight, height, date of birth, and sex are required.'];
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?action=dataEncoding'));
            exit;
        }

        $data = [
            'bns_id'          => $bnsId,
            'assessed_type'   => $type,
            'child_id'        => ($type === 'child') ? ((int)($_POST['child_id']     ?? 0) ?: null) : null,
            'fm_member_id'    => ($type === 'child')
                                    ? ((int)($_POST['fm_member_id'] ?? 0) ?: null)
                                    : ((int)($_POST['fm_member_id'] ?? 0) ?: null), // also for maternal BNS-only wives
            'user_id'         => ($type !== 'child') ? ((int)($_POST['user_id'] ?? 0) ?: null) : null,
            'full_name'       => trim($_POST['full_name']      ?? ''),
            'sex'             => $sex,
            'dob'             => $dob,
            'weight_kg'       => $weight,
            'height_cm'       => $height,
            'muac_cm'         => !empty($_POST['muac_cm']) ? (float)$_POST['muac_cm'] : null,
            'assessment_date' => $date,
            'caregiver_name'  => trim($_POST['caregiver_name'] ?? '') ?: null,
            'purok'           => trim($_POST['purok']          ?? '') ?: null,
            'remarks'         => trim($_POST['remarks']        ?? '') ?: null,
            // Maternal-specific fields
            'lmp'             => !empty($_POST['lmp'])             ? trim($_POST['lmp'])             : null,
            'edc'             => !empty($_POST['edc'])             ? trim($_POST['edc'])             : null,
            'pre_preg_weight' => !empty($_POST['pre_preg_weight']) ? (float)$_POST['pre_preg_weight'] : null,
            'aog_months'      => !empty($_POST['aog_months'])      ? (int)$_POST['aog_months']       : null,
            'philhealth'      => isset($_POST['philhealth']) && $_POST['philhealth'] !== '' ? (int)$_POST['philhealth'] : null,
            'is_4ps'          => isset($_POST['is_4ps'])     && $_POST['is_4ps']     !== '' ? (int)$_POST['is_4ps']     : null,
        ];

        // ── Process 4: Run the logic engine ──────────────────────────────────
        if ($type === 'child') {
            $ageMonths = NutritionCalculator::ageInMonths($dob, $date);
            $data['age_in_months'] = $ageMonths;
            $data['age_in_years']  = null;

            // WFA
            $wfaRef = $this->model->getWFARef(min($ageMonths, 59), $sex);
            $data['wfa_status'] = $wfaRef ? NutritionCalculator::classifyWFA($weight, $wfaRef) : null;

            // HFA
            $hfaRef = $this->model->getHFARef(min($ageMonths, 59), $sex);
            $data['hfa_status'] = $hfaRef ? NutritionCalculator::classifyHFA($height, $hfaRef) : null;

            // WFH
            $wfhRef = $this->model->getWFHRef($height, $sex);
            $data['wfh_status'] = $wfhRef ? NutritionCalculator::classifyWFH($weight, $wfhRef) : null;

            $data['bmi']        = null;
            $data['bmi_status'] = null;

            // Auto-flag
            $data['needs_monitoring'] = NutritionCalculator::childNeedsMonitoring(
                $data['wfa_status'], $data['hfa_status'], $data['wfh_status']
            );
            $data['is_at_risk'] = NutritionCalculator::childIsAtRisk(
                $data['wfa_status'], $data['hfa_status'], $data['wfh_status']
            );

        } else {
            // Maternal or Senior — use BMI
            $ageYears = NutritionCalculator::ageInYears($dob, $date);
            $data['age_in_months'] = null;
            $data['age_in_years']  = $ageYears;

            $bmi = NutritionCalculator::calcBMI($weight, $height);
            $bmiStatus = $type === 'senior'
                ? NutritionCalculator::classifyBMISenior($bmi)
                : NutritionCalculator::classifyBMI($bmi);

            $data['bmi']        = $bmi;
            $data['bmi_status'] = $bmiStatus;
            $data['wfa_status'] = null;
            $data['hfa_status'] = null;
            $data['wfh_status'] = null;

            // Weight gain tracking for pregnant women
            $prePregWeight = $data['pre_preg_weight'] ?? null;
            if ($type === 'maternal' && $prePregWeight && $prePregWeight > 0) {
                $gain = round($weight - $prePregWeight, 2);
                $data['weight_gain_kg'] = $gain;
                // IOM recommended ranges (kg) by pre-pregnancy BMI
                $preBmi = NutritionCalculator::calcBMI($prePregWeight, $height);
                if ($preBmi < 18.5)      $range = ['min' => 12.7, 'max' => 18.1];
                elseif ($preBmi < 25.0)  $range = ['min' => 11.3, 'max' => 15.9];
                elseif ($preBmi < 30.0)  $range = ['min' =>  6.8, 'max' => 11.3];
                else                     $range = ['min' =>  5.0, 'max' =>  9.1];
                if ($gain < $range['min'])      $data['weight_gain_status'] = 'Low';
                elseif ($gain > $range['max'])  $data['weight_gain_status'] = 'High';
                else                            $data['weight_gain_status'] = 'Normal';
            } else {
                $data['weight_gain_kg']     = null;
                $data['weight_gain_status'] = null;
            }

            $data['needs_monitoring'] = NutritionCalculator::adultNeedsMonitoring($bmiStatus);
            $data['is_at_risk']       = ($bmiStatus === 'Underweight');
        }

        // ── Process 5: Save + auto-flag ───────────────────────────────────────
        $assessmentId = $this->model->save($data);

        $_SESSION['flash'] = 'Assessment saved successfully.' .
            ($data['is_at_risk'] ? ' ⚠️ This individual has been flagged as at-risk.' : '');
        header('Location: index.php?action=dataEncoding&tab=' . $type);
        exit;
    }

    // ── Pregnant Women Masterlist (Form M-1.a) ────────────────────────────────

    public function showPregnantMasterlist(): void {
        $this->requireBNS();
        $bnsId   = $_SESSION['user_id'];
        $year    = (int)($_GET['year']    ?? date('Y'));
        $quarter = (int)($_GET['quarter'] ?? ceil(date('n') / 3));
        $bnsName = $_SESSION['user_name'] ?? 'BNS Staff';
        $imgBase = htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));

        $records = $this->model->getPregnantMasterlist($bnsId, (string)$quarter, (string)$year);

        $pageTitle = 'Pregnant Women Masterlist';
        $activeNav = 'data_encoding';
        include __DIR__ . '/../views/bns/pregnant_masterlist.php';
    }

    // ── Lactating Mothers Masterlist ──────────────────────────────────────────

    public function showLactatingMasterlist(): void {
        $this->requireBNS();
        $bnsId   = $_SESSION['user_id'];
        $year    = (int)($_GET['year']    ?? date('Y'));
        $quarter = (int)($_GET['quarter'] ?? ceil(date('n') / 3));
        $bnsName = $_SESSION['user_name'] ?? 'BNS Staff';
        $imgBase = htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));

        $records = $this->model->getLactatingMasterlist($bnsId, (string)$quarter, (string)$year);

        $pageTitle = 'Lactating Mothers Masterlist';
        $activeNav = 'data_encoding';
        include __DIR__ . '/../views/bns/lactating_masterlist.php';
    }

    // ── Elderly Citizens Masterlist ────────────────────────────────────────────

    public function showSeniorMasterlist(): void {
        $this->requireBNS();
        $bnsId   = $_SESSION['user_id'];
        $year    = (int)($_GET['year']    ?? date('Y'));
        $quarter = (int)($_GET['quarter'] ?? ceil(date('n') / 3));
        $bnsName = $_SESSION['user_name'] ?? 'BNS Staff';
        $imgBase = htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));

        $records = $this->model->getSeniorMasterlist($bnsId, (string)$quarter, (string)$year);

        $pageTitle = 'Elderly Citizens Masterlist';
        $activeNav = 'data_encoding';
        include __DIR__ . '/../views/bns/senior_masterlist.php';
    }

    // ── OPT Plus Results View ─────────────────────────────────────────────────

    public function showOPTResults(): void {
        $this->requireBNS();
        $bnsId = $_SESSION['user_id'];
        $year  = (int)($_GET['year'] ?? date('Y'));

        $results = $this->model->getOPTResults($bnsId, (string)$year);

        $pageTitle = 'OPT Plus Results';
        $activeNav = 'data_encoding';
        include __DIR__ . '/../views/bns/opt_results.php';
    }

    // ── Form C Report ─────────────────────────────────────────────────────────

    public function showFormC(): void {
        $this->requireBNS();
        $bnsId = $_SESSION['user_id'];
        $year  = (int)($_GET['year'] ?? date('Y'));

        $records = $this->model->getFormC($bnsId, (string)$year);
        $summary = $this->model->getFormCSummary($bnsId, (string)$year);

        // Resolve barangay/municipality/province from PSGC using stored barangay_code
        $locationBarangay    = '';
        $locationMunicipality = '';
        $locationProvince    = '';

        // Always fetch fresh from DB (session may not have it yet)
        $stmt = $this->db->prepare("SELECT barangay_code FROM user_profiles WHERE user_id = ?");
        $stmt->execute([$bnsId]);
        $barangayCode = $stmt->fetchColumn() ?: ($_SESSION['barangay_code'] ?? '');

        if ($barangayCode) {
            $location = $this->resolvePsgcLocation($barangayCode);
            $locationBarangay     = $location['barangay'];
            $locationMunicipality = $location['municipality'];
            $locationProvince     = $location['province'];
        }

        $pageTitle = 'OPT Plus Form C – At-risk Children';
        $activeNav = 'data_encoding';
        include __DIR__ . '/../views/bns/form_c_report.php';
    }

    /**
     * Resolve a PSGC barangay code into barangay, municipality, and province names.
     * Uses the public PSGC API with a short timeout so it never blocks the page.
     */
    private function resolvePsgcLocation(string $code): array {
        $base    = 'https://psgc.gitlab.io/api';
        $result  = ['barangay' => '', 'municipality' => '', 'province' => ''];

        try {
            $ctx = stream_context_create(['http' => ['timeout' => 5]]);

            $brgyJson = @file_get_contents("{$base}/barangays/{$code}.json", false, $ctx);
            if (!$brgyJson) return $result;
            $brgy = json_decode($brgyJson, true);
            $result['barangay'] = $brgy['name'] ?? '';

            $cityCode = $brgy['cityCode'] ?? $brgy['municipalityCode'] ?? $brgy['districtCode'] ?? '';
            if (!$cityCode) return $result;

            $cityJson = @file_get_contents("{$base}/cities-municipalities/{$cityCode}.json", false, $ctx);
            if (!$cityJson) return $result;
            $city = json_decode($cityJson, true);
            $result['municipality'] = $city['name'] ?? '';

            $provCode = $city['provinceCode'] ?? $city['districtCode'] ?? '';
            if (!$provCode) return $result;

            $provJson = @file_get_contents("{$base}/provinces/{$provCode}.json", false, $ctx);
            if (!$provJson) return $result;
            $prov = json_decode($provJson, true);
            $result['province'] = $prov['name'] ?? '';

        } catch (\Throwable $e) {
            // Silently fail — location fields will just be empty
        }

        return $result;
    }

    // ── Monitoring List (all tabs) ────────────────────────────────────────────

    public function showP12(): void {
        $this->requireBNS();
        $bnsId = $_SESSION['user_id'];
        $year  = (int)($_GET['year'] ?? date('Y'));
        $tab   = $_GET['tab'] ?? 'uw_st';

        // All 9 NNC monitoring lists — each with its SQL filter and display title
        $lists = [
            'age_0_23'   => [
                'title'  => 'Monitoring List for Children 0–23 Months Old',
                'filter' => "age_in_months BETWEEN 0 AND 23 AND needs_monitoring = 1",
                'cols'   => ['wfa','hfa','wfh','muac'],
            ],
            'mam'        => [
                'title'  => 'Monitoring List for Moderately Wasted (MAM) Children 0–59 Months Old',
                'filter' => "wfh_status = 'MAM'",
                'cols'   => ['height','weight','muac'],
            ],
            'sam'        => [
                'title'  => 'Monitoring List for Severely Wasted (SAM) Children 0–59 Months Old',
                'filter' => "wfh_status = 'SAM'",
                'cols'   => ['height','weight','muac'],
            ],
            'ow_ob'      => [
                'title'  => 'Monitoring List for Overweight or Obese Children 0–59 Months Old',
                'filter' => "wfh_status IN ('OW','Ob')",
                'cols'   => ['height','weight'],
            ],
            'uw_st'      => [
                'title'  => 'Monitoring List for Moderately or Severely Underweight + Moderately or Severely Stunted Children 0–59 Months Old',
                'filter' => "(wfa_status IN ('UW','SUW') OR hfa_status IN ('St','SSt'))",
                'cols'   => ['height','weight'],
            ],
            'stunted'    => [
                'title'  => 'Monitoring List for Moderately or Severely Stunted Children 0–59 Months Old',
                'filter' => "hfa_status IN ('St','SSt')",
                'cols'   => ['height','weight'],
            ],
            'st_wasted'  => [
                'title'  => 'Monitoring List for Moderately or Severely Stunted + Moderately or Severely Wasted Children 0–59 Months Old',
                'filter' => "hfa_status IN ('St','SSt') AND wfh_status IN ('MAM','SAM')",
                'cols'   => ['height','weight'],
            ],
            'st_ow'      => [
                'title'  => 'Monitoring List for Moderately or Severely Stunted + Overweight or Obese Children 0–59 Months Old',
                'filter' => "hfa_status IN ('St','SSt') AND wfh_status IN ('OW','Ob')",
                'cols'   => ['height','weight'],
            ],
            'muac'       => [
                'title'  => 'Monitoring List for Children Measured Using Mid-Upper Arm Circumference (MUAC)',
                'filter' => "muac_cm IS NOT NULL",
                'cols'   => ['muac'],
            ],
        ];

        // Load records for the active tab only (lazy load)
        $records = $this->model->getMonitoringList($bnsId, $lists[$tab]['filter'], (string)$year);

        // Load counts for all tabs (for badges)
        $counts = [];
        foreach ($lists as $key => $def) {
            $counts[$key] = count($this->model->getMonitoringList($bnsId, $def['filter'], (string)$year));
        }

        $pageTitle = 'Monitoring List';
        $activeNav = 'data_encoding';
        include __DIR__ . '/../views/bns/p12_monitoring.php';
    }

    // ── Monitoring List — All 9 lists in one printable page ──────────────────

    public function showP12All(): void {
        $this->requireBNS();
        $bnsId = $_SESSION['user_id'];
        $year  = (int)($_GET['year'] ?? date('Y'));

        $lists = [
            'age_0_23'  => ['title' => 'Monitoring List for Children 0–23 Months Old',                                                                                                    'filter' => "age_in_months BETWEEN 0 AND 23 AND needs_monitoring = 1"],
            'mam'       => ['title' => 'Monitoring List for Moderately Wasted (MAM) Children 0–59 Months Old',                                                                            'filter' => "wfh_status = 'MAM'"],
            'sam'       => ['title' => 'Monitoring List for Severely Wasted (SAM) Children 0–59 Months Old',                                                                              'filter' => "wfh_status = 'SAM'"],
            'ow_ob'     => ['title' => 'Monitoring List for Overweight or Obese Children 0–59 Months Old',                                                                                'filter' => "wfh_status IN ('OW','Ob')"],
            'uw_st'     => ['title' => 'Monitoring List for Moderately or Severely Underweight + Stunted Children 0–59 Months Old',                                                       'filter' => "(wfa_status IN ('UW','SUW') OR hfa_status IN ('St','SSt'))"],
            'stunted'   => ['title' => 'Monitoring List for Moderately or Severely Stunted Children 0–59 Months Old',                                                                     'filter' => "hfa_status IN ('St','SSt')"],
            'st_wasted' => ['title' => 'Monitoring List for Moderately or Severely Stunted + Wasted Children 0–59 Months Old',                                                            'filter' => "hfa_status IN ('St','SSt') AND wfh_status IN ('MAM','SAM')"],
            'st_ow'     => ['title' => 'Monitoring List for Moderately or Severely Stunted + Overweight or Obese Children 0–59 Months Old',                                               'filter' => "hfa_status IN ('St','SSt') AND wfh_status IN ('OW','Ob')"],
            'muac'      => ['title' => 'Monitoring List for Children Measured Using Mid-Upper Arm Circumference (MUAC)',                                                                   'filter' => "muac_cm IS NOT NULL"],
        ];

        // Load records for all 9 lists
        $allLists = [];
        foreach ($lists as $key => $def) {
            $allLists[$key] = [
                'title'   => $def['title'],
                'records' => $this->model->getMonitoringList($bnsId, $def['filter'], (string)$year),
            ];
        }

        $pageTitle = 'All Monitoring Lists';
        $activeNav = 'data_encoding';
        include __DIR__ . '/../views/bns/p12_monitoring_all.php';
    }

    // ── AJAX: Preview assessment result ──────────────────────────────────────

    public function previewAssessment(): void {
        $this->requireBNS();
        header('Content-Type: application/json');

        $type   = $_POST['type']   ?? 'child';
        $weight = (float)($_POST['weight_kg'] ?? 0);
        $height = (float)($_POST['height_cm'] ?? 0);
        $dob    = trim($_POST['dob'] ?? '');
        $sexRaw = trim($_POST['sex'] ?? '');
        $sex    = match(strtolower($sexRaw)) {
            'male', 'm'   => 'M',
            'female', 'f' => 'F',
            default       => $sexRaw,
        };
        $date   = trim($_POST['assessment_date'] ?? date('Y-m-d'));

        if (!$weight || !$height || !$dob || !$sex) {
            echo json_encode(['error' => 'Missing data']); return;
        }

        $result = [];

        if ($type === 'child') {
            $ageMonths = NutritionCalculator::ageInMonths($dob, $date);
            $result['age_months'] = $ageMonths;

            $wfaRef = $this->model->getWFARef(min($ageMonths, 59), $sex);
            $hfaRef = $this->model->getHFARef(min($ageMonths, 59), $sex);
            $wfhRef = $this->model->getWFHRef($height, $sex);

            $wfa = $wfaRef ? NutritionCalculator::classifyWFA($weight, $wfaRef) : 'N/A';
            $hfa = $hfaRef ? NutritionCalculator::classifyHFA($height, $hfaRef) : 'N/A';
            $wfh = $wfhRef ? NutritionCalculator::classifyWFH($weight, $wfhRef) : 'N/A';

            $result['wfa']       = $wfa;
            $result['wfa_label'] = NutritionCalculator::wfaLabel($wfa);
            $result['wfa_color'] = NutritionCalculator::statusBadgeColor($wfa);
            $result['hfa']       = $hfa;
            $result['hfa_label'] = NutritionCalculator::hfaLabel($hfa);
            $result['hfa_color'] = NutritionCalculator::statusBadgeColor($hfa);
            $result['wfh']       = $wfh;
            $result['wfh_label'] = NutritionCalculator::wfhLabel($wfh);
            $result['wfh_color'] = NutritionCalculator::statusBadgeColor($wfh);
            $result['needs_monitoring'] = NutritionCalculator::childNeedsMonitoring($wfa, $hfa, $wfh);
            $result['is_at_risk']       = NutritionCalculator::childIsAtRisk($wfa, $hfa, $wfh);
        } else {
            $bmi       = NutritionCalculator::calcBMI($weight, $height);
            $bmiStatus = $type === 'senior'
                ? NutritionCalculator::classifyBMISenior($bmi)
                : NutritionCalculator::classifyBMI($bmi);
            $result['bmi']              = $bmi;
            $result['bmi_status']       = $bmiStatus;
            $result['bmi_color']        = NutritionCalculator::statusBadgeColor($bmiStatus);
            $result['needs_monitoring'] = NutritionCalculator::adultNeedsMonitoring($bmiStatus);
            $result['is_at_risk']       = ($bmiStatus === 'Underweight');
        }

        echo json_encode($result);
    }

    // ── Save Follow-up Visit ──────────────────────────────────────────────────

    public function saveFollowUp(): void {
        $this->requireBNS();
        $bnsId = $_SESSION['user_id'];

        $this->model->saveFollowUp([
            'assessment_id'      => (int)($_POST['assessment_id']      ?? 0),
            'visit_month_number' => (int)($_POST['visit_month_number'] ?? 1),
            'visit_date'         => trim($_POST['visit_date']          ?? ''),
            'intervention_done'  => trim($_POST['intervention_done']   ?? '') ?: null,
            'nutritional_status' => trim($_POST['nutritional_status']  ?? '') ?: null,
            'recorded_by'        => $bnsId,
        ]);

        $_SESSION['flash'] = 'Follow-up visit recorded.';
        header('Location: index.php?action=p12Monitoring'); exit;
    }
}
