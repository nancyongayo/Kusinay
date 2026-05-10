<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Security.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../../core/Mailer.php';

class FamilyProfileController {

    private PDO $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    private function requireBNS(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['BNS Staff', 'Admin'])) {
            header('Location: index.php?action=login'); exit;
        }
    }

    // ── Lookup helpers ────────────────────────────────────────────────────────

    public function getLookups(): array {
        $get = fn(string $sql) => $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return [
            'toilet_types'    => $get("SELECT id, code, label FROM ref_toilet_types ORDER BY id"),
            'water_sources'   => $get("SELECT id, code, label FROM ref_water_sources ORDER BY id"),
            'dwelling_types'  => $get("SELECT id, label FROM ref_dwelling_types ORDER BY id"),
            'food_activities' => $get("SELECT id, code, label FROM ref_food_activities ORDER BY id"),
            'fp_methods'      => $get("SELECT id, label FROM ref_fp_methods ORDER BY id"),
            'educ_levels'     => $get("SELECT id, label FROM ref_educ_levels ORDER BY id"),
            'nutri_statuses'  => $get("SELECT id, label FROM ref_nutri_statuses ORDER BY id"),
        ];
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function showDashboard(): void {
        $this->requireBNS();
        $bnsId = $_SESSION['user_id'];

        try {
            $s = $this->db->prepare("SELECT COUNT(*) FROM family_profiles WHERE bns_id = ?");
            $s->execute([$bnsId]); $totalFamilies = $s->fetchColumn();

            $s = $this->db->prepare("
                SELECT COALESCE(SUM(
                    COALESCE(children_0_5mos,0) + COALESCE(children_6_23mos,0) + 
                    COALESCE(children_24_59mos,0) + COALESCE(children_60plus,0)
                ), 0)
                FROM family_profiles WHERE bns_id = ?
            ");
            $s->execute([$bnsId]); $totalChildren = $s->fetchColumn();

            // If all age-group columns are 0/NULL, fall back to counting family_members children
            if ((int)$totalChildren === 0) {
                $s = $this->db->prepare("
                    SELECT COUNT(*)
                    FROM family_members fm
                    JOIN family_profiles fp ON fp.family_id = fm.family_id
                    WHERE fp.bns_id = ? AND fm.role = 'Child'
                ");
                $s->execute([$bnsId]);
                $totalChildren = $s->fetchColumn();
            }

            // Income removed from dashboard
        } catch (PDOException $e) {
        }

        include __DIR__ . '/../views/bns/bns_dashboard.php';
    }

    // ── List ──────────────────────────────────────────────────────────────────

    public function listProfiles(): void {
        $this->requireBNS();
        $bnsId  = $_SESSION['user_id'];
        $search = trim($_GET['search'] ?? '');
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 15;
        $offset = ($page - 1) * $limit;

        $where  = "WHERE fp.bns_id = :bns";
        $params = [':bns' => $bnsId];

        if ($search) {
            $where .= " AND (hm.last_name LIKE :s OR hm.first_name LIKE :s2 OR fp.hh_number LIKE :s3 OR fp.purok LIKE :s4)";
            $params[':s']  = "%$search%";
            $params[':s2'] = "%$search%";
            $params[':s3'] = "%$search%";
            $params[':s4'] = "%$search%";
        }

        $cs = $this->db->prepare("
            SELECT COUNT(DISTINCT fp.family_id)
            FROM family_profiles fp
            LEFT JOIN family_members hm ON hm.family_id = fp.family_id AND hm.role = 'Head'
            $where
        ");
        $cs->execute($params);
        $total      = $cs->fetchColumn();
        $totalPages = max(1, ceil($total / $limit));

        $params[':limit']  = $limit;
        $params[':offset'] = $offset;

        $stmt = $this->db->prepare("
            SELECT fp.*,
                   CONCAT(hm.last_name, ', ', hm.first_name, 
                          CASE WHEN hm.middle_name IS NOT NULL AND hm.middle_name != '' 
                               THEN CONCAT(' ', SUBSTRING(hm.middle_name, 1, 1), '.') 
                               ELSE '' END,
                          CASE WHEN hm.suffix IS NOT NULL AND hm.suffix != '' 
                               THEN CONCAT(' ', hm.suffix) 
                               ELSE '' END) AS head_name,
                   hm.occupation AS head_occupation,
                   el.label      AS head_educ,
                   CONCAT(wm.last_name, ', ', wm.first_name,
                          CASE WHEN wm.middle_name IS NOT NULL AND wm.middle_name != '' 
                               THEN CONCAT(' ', SUBSTRING(wm.middle_name, 1, 1), '.') 
                               ELSE '' END,
                          CASE WHEN wm.suffix IS NOT NULL AND wm.suffix != '' 
                               THEN CONCAT(' ', wm.suffix) 
                               ELSE '' END) AS wife_name,
                   wm.occupation AS wife_occupation,
                   el2.label     AS wife_educ,
                   rtt.code      AS toilet_code,
                   rws.code      AS water_code,
                   rdt.label     AS dwelling_label,
                   rfp.label     AS fp_label,
                   (SELECT GROUP_CONCAT(rfa.code SEPARATOR ', ')
                    FROM family_food_activities ffa
                    JOIN ref_food_activities rfa ON ffa.activity_id = rfa.id
                    WHERE ffa.family_id = fp.family_id) AS food_activities
            FROM family_profiles fp
            LEFT JOIN family_members hm      ON hm.family_id = fp.family_id AND hm.role = 'Head'
            LEFT JOIN family_members wm      ON wm.family_id = fp.family_id AND wm.role = 'Wife'
            LEFT JOIN ref_educ_levels el     ON el.id = hm.educ_level_id
            LEFT JOIN ref_educ_levels el2    ON el2.id = wm.educ_level_id
            LEFT JOIN ref_toilet_types rtt   ON rtt.id = fp.toilet_type_id
            LEFT JOIN ref_water_sources rws  ON rws.id = fp.water_source_id
            LEFT JOIN ref_dwelling_types rdt ON rdt.id = fp.dwelling_type_id
            LEFT JOIN ref_fp_methods rfp     ON rfp.id = fp.fp_method_id
            $where
            GROUP BY fp.family_id
            ORDER BY fp.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decrypt income for display
        foreach ($profiles as &$p) { /* income is now plaintext */ }
        unset($p);

        include __DIR__ . '/../views/bns/family_profile_list.php';
    }

    // ── Show form ─────────────────────────────────────────────────────────────

    public function showForm(): void {
        $this->requireBNS();
        $familyId = (int)($_GET['id'] ?? 0);
        $family   = null;
        $members  = [];
        $selectedFoodAct = [];

        if ($familyId) {
            $s = $this->db->prepare("SELECT * FROM family_profiles WHERE family_id = ? AND bns_id = ?");
            $s->execute([$familyId, $_SESSION['user_id']]);
            $family = $s->fetch(PDO::FETCH_ASSOC);
            if (!$family) { header('Location: index.php?action=familyProfiles'); exit; }

            // Load resident email from linked user account (if any)
            if (!empty($family['source_user_id'])) {
                $emailStmt = $this->db->prepare("SELECT email FROM users WHERE user_id = ?");
                $emailStmt->execute([$family['source_user_id']]);
                $family['head_email'] = $emailStmt->fetchColumn() ?: '';
            } else {
                $family['head_email'] = '';
            }

            $s = $this->db->prepare("SELECT * FROM family_members WHERE family_id = ? AND role IN ('Head','Wife') ORDER BY role DESC");
            $s->execute([$familyId]); $members = $s->fetchAll(PDO::FETCH_ASSOC);

            $s = $this->db->prepare("SELECT * FROM family_members WHERE family_id = ? AND role = 'Child' ORDER BY sort_order ASC");
            $s->execute([$familyId]); $children = $s->fetchAll(PDO::FETCH_ASSOC);

            $s = $this->db->prepare("SELECT activity_id FROM family_food_activities WHERE family_id = ?");
            $s->execute([$familyId]); $selectedFoodAct = array_column($s->fetchAll(PDO::FETCH_ASSOC), 'activity_id');
        }

        $lookups = $this->getLookups();

        include __DIR__ . '/../views/bns/family_profile_form.php';
    }

    // ── Save ──────────────────────────────────────────────────────────────────

    public function saveProfile(): void {
        $this->requireBNS();
        $bnsId    = $_SESSION['user_id'];
        $familyId = (int)($_POST['family_id'] ?? 0);

        $headLastName = trim($_POST['head_last_name'] ?? '');
        $headFirstName = trim($_POST['head_first_name'] ?? '');
        if (!$headLastName || !$headFirstName) {
            $_SESSION['errors'] = ['Last Name and First Name of Household Head are required.'];
            $url = $familyId ? "index.php?action=familyProfileForm&id=$familyId" : 'index.php?action=familyProfileForm';
            header("Location: $url"); exit;
        }

        // Derive boolean flags from dropdown values
        // The breastfeeding/pregnancy flags apply to the FEMALE member of the household,
        // regardless of whether she is the Head or the Spouse.
        $headSex  = trim($_POST['head_sex']  ?? '');
        $wifeSex  = trim($_POST['wife_sex']  ?? '');

        // Determine which role is female
        // If Head is Female → use head_pregnancy_status / head_breastfeeding_status (same fields as wife_ since the form
        // always puts these in the "Socio-Economic" section as wife_* regardless of who is female)
        // The form always stores these as wife_pregnancy_status / wife_breastfeeding_status
        $wifePregnancyStatus     = trim($_POST['wife_pregnancy_status']     ?? '');
        $wifeBreastfeedingStatus = trim($_POST['wife_breastfeeding_status'] ?? '');

        // Only set maternal flags if the person they refer to is actually Female.
        // The "wife_*" fields in the form represent the female member of the couple,
        // but we must confirm: if Head is Female, the female is the Head (role=Head, sex=F).
        // If Spouse is Female, the female is the Spouse (role=Wife, sex=F).
        // Either way the form fields are the same — the flags are valid as long as
        // at least one female exists in the household.
        $hasFemale = ($headSex === 'F' || $wifeSex === 'F');

        $isMotherProg  = ($hasFemale && str_contains($wifePregnancyStatus, 'Pregnant') && $wifePregnancyStatus !== 'Not Pregnant') ? 1 : 0;
        $isErf         = ($hasFemale && $wifeBreastfeedingStatus === 'EBF (Exclusive Breastfeeding)') ? 1 : 0;
        $isMixedMilk   = ($hasFemale && $wifeBreastfeedingStatus === 'Mixed Feeding')   ? 1 : 0;
        $isBottle      = ($hasFemale && $wifeBreastfeedingStatus === 'Bottle Feeding')   ? 1 : 0;

        $core = [
            'hh_number'          => trim($_POST['hh_number']         ?? '') ?: null,
            'purok'              => trim($_POST['purok']              ?? '') ?: null,
            'num_hh_members'     => $_POST['num_hh_members']          ?? null ?: null,
            'children_0_5mos'    => $_POST['children_0_5mos']         ?? null ?: null,
            'children_6_23mos'   => $_POST['children_6_23mos']        ?? null ?: null,
            'children_24_59mos'  => $_POST['children_24_59mos']       ?? null ?: null,
            'children_60plus'    => $_POST['children_60plus']          ?? null ?: null,
            'is_mother_prog'     => $isMotherProg,
            'fp_method_id'       => $_POST['fp_method_id']            ?? null ?: null,
            'fp_method_other'    => !empty($_POST['fp_method_other']) ? trim($_POST['fp_method_other']) : null,
            'is_erf'             => $isErf,
            'is_mixed_milk'      => $isMixedMilk,
            'is_bottle_feeding'  => $isBottle,
            'toilet_type_id'     => $_POST['toilet_type_id']          ?? null ?: null,
            'water_source_id'    => $_POST['water_source_id']         ?? null ?: null,
            'uses_iodized_salt'  => isset($_POST['uses_iodized_salt']) ? 1 : 0,
            'uses_ifr'           => isset($_POST['uses_ifr'])          ? 1 : 0,
            'dwelling_type_id'   => $_POST['dwelling_type_id']        ?? null ?: null,
            'total_income'       => isset($_POST['total_income']) && $_POST['total_income'] !== '' ? $_POST['total_income'] : null,
            'remarks'            => trim($_POST['remarks']             ?? '') ?: null,
        ];

        $this->db->beginTransaction();
        try {
            if ($familyId) {
                $set  = implode(', ', array_map(fn($k) => "`$k` = :$k", array_keys($core)));
                $stmt = $this->db->prepare("UPDATE family_profiles SET $set WHERE family_id = :family_id AND bns_id = :bns_id");
                $core['family_id'] = $familyId;
                $core['bns_id']    = $bnsId;
                $stmt->execute($core);
            } else {
                $core['bns_id'] = $bnsId;
                $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($core)));
                $vals = implode(', ', array_map(fn($k) => ":$k",  array_keys($core)));
                $stmt = $this->db->prepare("INSERT INTO family_profiles ($cols) VALUES ($vals)");
                $stmt->execute($core);
                $familyId = (int)$this->db->lastInsertId();
            }

            $this->saveMembers($familyId);
            $this->saveFoodActivities($familyId);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['errors'] = ['Save failed: ' . $e->getMessage()];
            header('Location: index.php?action=familyProfileForm' . ($familyId ? "&id=$familyId" : ''));
            exit;
        }

        $_SESSION['flash'] = 'Family profile saved successfully.';

        // ── Auto-register resident if email provided and no account linked yet ──
        $headEmail = strtolower(trim($_POST['head_email'] ?? ''));
        if ($headEmail && filter_var($headEmail, FILTER_VALIDATE_EMAIL)) {
            // Check if this family already has a linked account
            $linked = $this->db->prepare("SELECT source_user_id FROM family_profiles WHERE family_id = ?");
            $linked->execute([$familyId]);
            $existingLink = $linked->fetchColumn();

            if (!$existingLink) {
                $userModel = new UserModel();
                if (!$userModel->emailExists($headEmail)) {
                    try {
                        $headFirstName = trim($_POST['head_first_name'] ?? '');
                        $headLastName  = trim($_POST['head_last_name']  ?? '');

                        $result = $userModel->registerByBns([
                            'first_name' => $headFirstName,
                            'last_name'  => $headLastName,
                            'email'      => $headEmail,
                        ], $bnsId);

                        $newUserId  = $result['user_id'];
                        $setupToken = $result['setup_token'];

                        // Link the new account to this family profile
                        $this->db->prepare("UPDATE family_profiles SET source_user_id = ? WHERE family_id = ?")
                                 ->execute([$newUserId, $familyId]);

                        // Log it
                        $this->db->prepare("INSERT INTO system_logs (user_id, action_type, description, ip_address) VALUES (?,?,?,?)")
                                 ->execute([$bnsId, 'BNS_RESIDENT_CREATED', "Auto-registered from family profile: {$headEmail} (user_id={$newUserId})", $_SERVER['REMOTE_ADDR'] ?? '']);

                        // Send invite email
                        $mailer = new Mailer();
                        $sent   = $mailer->sendResidentInviteEmail($headEmail, "$headFirstName $headLastName", $setupToken);

                        if ($sent) {
                            $_SESSION['flash'] = 'Family profile saved. Resident account created and setup link sent to ' . $headEmail . '.';
                        } else {
                            $setupLink = 'http://localhost/KusiNay(Capstone)/index.php?action=setupAccount&token=' . urlencode($setupToken);
                            $_SESSION['flash'] = 'Family profile saved. Resident account created. '
                                . 'Email could not be sent — setup link: <a href="' . htmlspecialchars($setupLink) . '" style="color:var(--kn-orange)">' . htmlspecialchars($setupLink) . '</a>';
                        }
                    } catch (\Exception $e) {
                        error_log('Auto-register error: ' . $e->getMessage());
                        $_SESSION['flash'] = 'Family profile saved. Could not create resident account: ' . $e->getMessage();
                    }
                } else {
                    // Email exists — just link if not already linked
                    $existingUser = $this->db->prepare("SELECT user_id FROM users WHERE email = ?");
                    $existingUser->execute([$headEmail]);
                    $uid = $existingUser->fetchColumn();
                    if ($uid) {
                        $this->db->prepare("UPDATE family_profiles SET source_user_id = ? WHERE family_id = ? AND source_user_id IS NULL")
                                 ->execute([$uid, $familyId]);
                        $_SESSION['flash'] = 'Family profile saved. Existing account for ' . $headEmail . ' linked to this household.';
                    }
                }
            }
        }

        header('Location: index.php?action=familyProfiles'); exit;
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function deleteProfile(): void {
        $this->requireBNS();
        $familyId = (int)($_POST['family_id'] ?? 0);
        $this->db->prepare("DELETE FROM family_profiles WHERE family_id = ? AND bns_id = ?")
                 ->execute([$familyId, $_SESSION['user_id']]);
        $_SESSION['flash'] = 'Family profile deleted.';
        header('Location: index.php?action=familyProfiles'); exit;
    }

    // ── Private save helpers ──────────────────────────────────────────────────

    private function saveMembers(int $familyId): void {
        $this->db->prepare("DELETE FROM family_members WHERE family_id = ?")->execute([$familyId]);

        // Save Head and Spouse
        $roles = ['Head', 'Wife'];
        foreach ($roles as $i => $role) {
            $key  = strtolower($role);
            
            // Get separated name fields
            $lastName   = trim($_POST["{$key}_last_name"] ?? '');
            $firstName  = trim($_POST["{$key}_first_name"] ?? '');
            $middleName = trim($_POST["{$key}_middle_name"] ?? '');
            $suffix     = trim($_POST["{$key}_suffix"] ?? '');
            
            if (!$lastName && !$firstName) continue; // Skip if no name provided

            $this->db->prepare("
                INSERT INTO family_members (family_id, role, last_name, first_name, middle_name, suffix, sex, civil_status, occupation, educ_level_id, dob, sort_order)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ")->execute([
                $familyId,
                $role,
                $lastName ?: null,
                $firstName ?: null,
                $middleName ?: null,
                $suffix ?: null,
                $_POST["{$key}_sex"]          ?? null ?: null,
                $_POST["{$key}_civil_status"] ?? null ?: null,
                trim($_POST["{$key}_occupation"] ?? '') ?: null,
                $_POST["{$key}_educ_id"]      ?? null ?: null,
                trim($_POST["{$key}_dob"]     ?? '') ?: null,
                $i,
            ]);
        }

        // Save Children (dynamically - child1, child2, child3, etc.)
        $childIndex = 1;
        $sortOrder = 10;
        while (isset($_POST["child{$childIndex}_last_name"]) || isset($_POST["child{$childIndex}_first_name"])) {
            $lastName   = trim($_POST["child{$childIndex}_last_name"] ?? '');
            $firstName  = trim($_POST["child{$childIndex}_first_name"] ?? '');
            $middleName = trim($_POST["child{$childIndex}_middle_name"] ?? '');
            $suffix     = trim($_POST["child{$childIndex}_suffix"] ?? '');
            
            if ($lastName || $firstName) {
                $this->db->prepare("
                    INSERT INTO family_members (family_id, role, last_name, first_name, middle_name, suffix, sex, dob, sort_order)
                    VALUES (?, 'Child', ?, ?, ?, ?, ?, ?, ?)
                ")->execute([
                    $familyId,
                    $lastName ?: null,
                    $firstName ?: null,
                    $middleName ?: null,
                    $suffix ?: null,
                    $_POST["child{$childIndex}_sex"] ?? null ?: null,
                    $_POST["child{$childIndex}_dob"] ?? null ?: null,
                    $sortOrder++,
                ]);
            }
            $childIndex++;
        }
    }

    private function saveFoodActivities(int $familyId): void {
        $this->db->prepare("DELETE FROM family_food_activities WHERE family_id = ?")->execute([$familyId]);
        $ids = array_filter(array_map('intval', (array)($_POST['food_activity_ids'] ?? [])));
        $stmt = $this->db->prepare("INSERT INTO family_food_activities (family_id, activity_id) VALUES (?, ?)");
        foreach ($ids as $aid) { $stmt->execute([$familyId, $aid]); }
    }

    // ── BNS Profile Settings ──────────────────────────────────────────────────

    public function showSettings(): void {
        $this->requireBNS();
        $bnsId = $_SESSION['user_id'];

        // Fetch stored barangay_code
        $stmt = $this->db->prepare("SELECT barangay_code FROM user_profiles WHERE user_id = ?");
        $stmt->execute([$bnsId]);
        $barangayCode = $stmt->fetchColumn() ?: '';

        // Resolve current names server-side for display
        $currentBarangay    = '';
        $currentMunicipality = '';
        $currentProvince    = '';

        if ($barangayCode) {
            $loc = $this->resolvePsgcLocation($barangayCode);
            $currentBarangay     = $loc['barangay'];
            $currentMunicipality = $loc['municipality'];
            $currentProvince     = $loc['province'];
        }

        $pageTitle = 'Profile Settings';
        $activeNav = 'settings';
        include __DIR__ . '/../views/bns/bns_profile_settings.php';
    }

    public function saveLocation(): void {
        $this->requireBNS();
        Security::verifyCsrf();

        $bnsId        = $_SESSION['user_id'];
        $barangayCode = trim($_POST['barangay_code'] ?? '');

        if (!$barangayCode) {
            $_SESSION['errors'] = ['Please select a barangay.'];
            header('Location: index.php?action=bnsSettings'); exit;
        }

        $this->db->prepare("
            INSERT INTO user_profiles (user_id, barangay_code, profile_complete)
            VALUES (?, ?, 1)
            ON DUPLICATE KEY UPDATE barangay_code = VALUES(barangay_code)
        ")->execute([$bnsId, $barangayCode]);

        // Update session so reports reflect the change immediately
        $_SESSION['barangay_code'] = $barangayCode;
        $_SESSION['flash'] = 'Location updated successfully.';
        header('Location: index.php?action=bnsSettings'); exit;
    }

    private function resolvePsgcLocation(string $code): array {
        $base   = 'https://psgc.gitlab.io/api';
        $result = ['barangay' => '', 'municipality' => '', 'province' => ''];
        try {
            $ctx      = stream_context_create(['http' => ['timeout' => 5]]);
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
            if ($provJson) {
                $prov = json_decode($provJson, true);
                $result['province'] = $prov['name'] ?? '';
            }
        } catch (\Throwable $e) { /* silent fail */ }
        return $result;
    }
}
