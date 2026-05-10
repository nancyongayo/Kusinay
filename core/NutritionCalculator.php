<?php
/**
 * NutritionCalculator — Pure logic, no DB access.
 * Classifies nutritional status using WHO 2006 Z-score boundaries
 * and BMI for adults/seniors.
 */
class NutritionCalculator {

    // ── Child Z-score Classification ─────────────────────────────────────────

    /**
     * Classify Weight-for-Age (WFA).
     * @param float $weight  Weight in kg
     * @param array $ref     Row from ref_zscore_wfa {sd_neg3, sd_neg2, sd_pos2, sd_pos3}
     * @return string  SUW | UW | Normal | OW
     */
    public static function classifyWFA(float $weight, array $ref): string {
        if ($weight < $ref['sd_neg3'])  return 'SUW';   // Severely Underweight
        if ($weight < $ref['sd_neg2'])  return 'UW';    // Underweight
        if ($weight <= $ref['sd_pos2']) return 'Normal';
        return 'OW';                                     // Overweight
    }

    /**
     * Classify Height/Length-for-Age (HFA).
     * @param float $height  Height in cm
     * @param array $ref     Row from ref_zscore_hfa
     * @return string  SSt | St | Normal | Tall
     */
    public static function classifyHFA(float $height, array $ref): string {
        if ($height < $ref['sd_neg3'])  return 'SSt';   // Severely Stunted
        if ($height < $ref['sd_neg2'])  return 'St';    // Stunted
        if ($height <= $ref['sd_pos2']) return 'Normal';
        return 'Tall';
    }

    /**
     * Classify Weight-for-Height (WFH).
     * @param float $weight  Weight in kg
     * @param array $ref     Row from ref_zscore_wfh
     * @return string  SAM | MAM | Normal | OW | Ob
     */
    public static function classifyWFH(float $weight, array $ref): string {
        if ($weight < $ref['sd_neg3'])  return 'SAM';   // Severe Acute Malnutrition
        if ($weight < $ref['sd_neg2'])  return 'MAM';   // Moderate Acute Malnutrition
        if ($weight <= $ref['sd_pos2']) return 'Normal';
        if ($weight <= $ref['sd_pos3']) return 'OW';    // Overweight
        return 'Ob';                                     // Obese
    }

    // ── BMI (Adults / Seniors / Maternal) ────────────────────────────────────

    /**
     * Compute BMI.
     * @param float $weight  kg
     * @param float $height  cm
     */
    public static function calcBMI(float $weight, float $height): float {
        if ($height <= 0) return 0.0;
        $heightM = $height / 100.0;
        return round($weight / ($heightM * $heightM), 2);
    }

    /**
     * Classify BMI status (WHO / Philippine DOH standard).
     * Used for adults and maternal.
     */
    public static function classifyBMI(float $bmi): string {
        if ($bmi < 18.5)  return 'Underweight';
        if ($bmi < 25.0)  return 'Normal';
        if ($bmi < 30.0)  return 'Overweight';
        return 'Obese';
    }

    /**
     * Classify BMI for elderly (60+ years) using senior-specific cutoffs.
     * < 18.5       → Underweight
     * 18.5 – 22.9  → At Risk / Low Normal
     * 23.0 – 27.9  → Normal (Healthy for seniors)
     * 28.0 – 31.9  → Overweight
     * ≥ 32.0       → Obese
     */
    public static function classifyBMISenior(float $bmi): string {
        if ($bmi < 18.5)  return 'Underweight';
        if ($bmi < 23.0)  return 'At Risk';
        if ($bmi < 28.0)  return 'Normal';
        if ($bmi < 32.0)  return 'Overweight';
        return 'Obese';
    }

    // ── Auto-flag helpers ─────────────────────────────────────────────────────

    /**
     * Returns true if the child needs monitoring (any abnormal status).
     */
    public static function childNeedsMonitoring(?string $wfa, ?string $hfa, ?string $wfh): bool {
        $abnormal = ['SUW','UW','SSt','St','SAM','MAM','OW','Ob'];
        return in_array($wfa, $abnormal) || in_array($hfa, $abnormal) || in_array($wfh, $abnormal);
    }

    /**
     * Returns true if child is at-risk (goes to Form C / P12).
     * At-risk = severely or moderately malnourished/stunted/wasted.
     */
    public static function childIsAtRisk(?string $wfa, ?string $hfa, ?string $wfh): bool {
        $atRisk = ['SUW','UW','SSt','St','SAM','MAM'];
        return in_array($wfa, $atRisk) || in_array($hfa, $atRisk) || in_array($wfh, $atRisk);
    }

    /**
     * Returns true if adult/senior needs monitoring.
     */
    public static function adultNeedsMonitoring(string $bmiStatus): bool {
        return $bmiStatus !== 'Normal';
    }

    // ── Age helpers ───────────────────────────────────────────────────────────

    /**
     * Compute age in completed months from DOB to assessment date.
     */
    public static function ageInMonths(string $dob, string $assessmentDate): int {
        $birth  = new DateTime($dob);
        $assess = new DateTime($assessmentDate);
        $diff   = $birth->diff($assess);
        return ($diff->y * 12) + $diff->m;
    }

    /**
     * Compute age in completed years.
     */
    public static function ageInYears(string $dob, string $assessmentDate): int {
        $birth  = new DateTime($dob);
        $assess = new DateTime($assessmentDate);
        return (int) $birth->diff($assess)->y;
    }

    // ── Status label helpers (for display) ───────────────────────────────────

    public static function wfaLabel(string $status): string {
        return match($status) {
            'SUW'    => 'Severely Underweight',
            'UW'     => 'Underweight',
            'Normal' => 'Normal',
            'OW'     => 'Overweight',
            default  => $status,
        };
    }

    public static function hfaLabel(string $status): string {
        return match($status) {
            'SSt'    => 'Severely Stunted',
            'St'     => 'Stunted',
            'Normal' => 'Normal',
            'Tall'   => 'Tall',
            default  => $status,
        };
    }

    public static function wfhLabel(string $status): string {
        return match($status) {
            'SAM'    => 'Severely Wasted (SAM)',
            'MAM'    => 'Moderately Wasted (MAM)',
            'Normal' => 'Normal',
            'OW'     => 'Overweight',
            'Ob'     => 'Obese',
            default  => $status,
        };
    }

    public static function statusBadgeColor(string $status): string {
        return match($status) {
            'SUW','SSt','SAM' => 'danger',
            'UW','St','MAM'   => 'warning',
            'Normal'          => 'success',
            'OW','Ob','Overweight','Obese' => 'info',
            'Tall'            => 'primary',
            default           => 'secondary',
        };
    }
}
