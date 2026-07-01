<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Project Proposal — <?= htmlspecialchars($proposal['proposal_title']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
            padding: 0;
            margin: 0;
        }

        /* ── Page layout ── */
        .page {
            width: 8.5in;
            min-height: 11in;
            margin: 0 auto;
            padding: .75in .85in .75in .85in;
            background: #fff;
            box-shadow: none;
        }

        /* ── Letterhead ── */
        .letterhead {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            border-bottom: 3px double #000;
            padding-bottom: .5rem;
            margin-bottom: 1.2rem;
        }
        .letterhead-logo {
            width: 70px;
            height: 70px;
            border: 2px solid #8B4513;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9pt;
            text-align: center;
            color: #8B4513;
            font-weight: bold;
            flex-shrink: 0;
        }
        .letterhead-text {
            text-align: center;
            flex: 1;
        }
        .letterhead-text .republic {
            font-size: 10pt;
        }
        .letterhead-text .city {
            font-size: 11pt;
            font-weight: bold;
        }
        .letterhead-text .barangay {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .letterhead-text .office {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* ── Document title ── */
        .doc-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 1rem 0 1.2rem;
            text-decoration: underline;
        }

        /* ── Section headings ── */
        .section-heading {
            font-size: 11pt;
            font-weight: bold;
            margin: 1rem 0 .4rem;
        }

        /* ── Identifying info list ── */
        .info-list {
            list-style: disc;
            padding-left: 1.5rem;
            margin-bottom: .5rem;
        }
        .info-list li {
            margin-bottom: .2rem;
            font-size: 11pt;
        }
        .info-list li strong {
            font-weight: bold;
        }

        /* ── Body text ── */
        .body-text {
            font-size: 11pt;
            line-height: 1.6;
            text-align: justify;
            margin-bottom: .5rem;
        }

        /* ── Budget table ── */
        .budget-table {
            width: 100%;
            border-collapse: collapse;
            margin: .5rem 0 1rem;
            font-size: 10.5pt;
        }
        .budget-table th {
            border: 1px solid #000;
            padding: .35rem .5rem;
            text-align: center;
            font-weight: bold;
            background: #f5f5f5;
        }
        .budget-table td {
            border: 1px solid #000;
            padding: .35rem .5rem;
        }
        .budget-table td.amount {
            text-align: right;
        }
        .budget-table td.center {
            text-align: center;
        }
        .budget-table tr.total-row td {
            font-weight: bold;
        }

        /* ── Signature block ── */
        .signature-section {
            margin-top: 2rem;
        }
        .signature-block {
            margin-bottom: 2rem;
        }
        .signature-label {
            font-size: 11pt;
            margin-bottom: .5rem;
        }
        .signature-image {
            height: 55px;
            display: block;
            margin: .3rem 0 0 .5rem;
        }
        .signature-name {
            font-size: 11pt;
            font-weight: bold;
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: .2rem;
            min-width: 200px;
            margin-top: .2rem;
        }
        .signature-title {
            font-size: 10.5pt;
        }

        /* ── Print controls (hidden) ── */
        .print-controls {
            display: none !important;
        }

        body {
            background: #fff;
            padding-top: 0;
        }

        @media print {
            .print-controls { 
                display: none !important; 
                visibility: hidden !important;
                height: 0 !important;
                overflow: hidden !important;
            }
            body {
                background: #fff !important;
                padding-top: 0 !important;
                margin: 0 !important;
            }
            .page {
                width: 100%;
                padding: .6in .75in;
                margin: 0 !important;
                box-shadow: none !important;
            }
            @page {
                size: letter portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>

<?php
$budgetItems = !empty($proposal['budget_items'])
    ? json_decode($proposal['budget_items'], true) ?? []
    : [];

// Get approved validation (for signature)
$approvedValidation = null;
foreach (array_reverse($validations ?? []) as $v) {
    if ($v['decision'] === 'Approved') {
        $approvedValidation = $v;
        break;
    }
}

$barangayName = $proposal['location'] ?? 'Bayabas';
$cityName     = 'City of Davao';
$numBen       = (int)$proposal['num_beneficiaries'];
$implDays     = (int)($proposal['implementation_days'] ?? 120);
$startDate    = $proposal['start_date'] ? date('F Y', strtotime($proposal['start_date'])) : '—';
?>

<!-- ── PAGE 1 ── -->
<div class="page">

    <!-- Letterhead -->
    <div class="letterhead">
        <div class="letterhead-logo">BRGY<br>SEAL</div>
        <div class="letterhead-text">
            <div class="republic">Republic of the Philippines</div>
            <div class="city"><?= htmlspecialchars($cityName) ?></div>
            <div class="barangay">Barangay <?= htmlspecialchars($barangayName) ?></div>
            <div class="office">Office of the Sangguniang Barangay</div>
        </div>
        <div class="letterhead-logo">NNC<br>LOGO</div>
    </div>

    <!-- Document Title -->
    <div class="doc-title">
        Project Proposal: <?= htmlspecialchars($proposal['program_type']) ?>
    </div>

    <!-- I. Identifying Information -->
    <div class="section-heading">I. Identifying Information</div>
    <ul class="info-list">
        <li><strong>Project Title:</strong> <?= htmlspecialchars($proposal['proposal_title']) ?></li>
        <li><strong>Proponent:</strong> <?= htmlspecialchars($proposal['proponent'] ?? 'Committee on Health, Sangguniang Barangay') ?></li>
        <li><strong>Location:</strong> <?= htmlspecialchars($proposal['location'] ?? $barangayName . ' Health Center, ' . $cityName) ?></li>
        <li><strong>Target Beneficiaries:</strong> <?= htmlspecialchars($proposal['target_beneficiaries']) ?></li>
        <li><strong>Implementation Period:</strong> <?= $implDays ?> Days</li>
        <li><strong>Funding Source:</strong> <?= htmlspecialchars($proposal['funding_source'] ?? 'Barangay BCPC Fund') ?></li>
        <li><strong>Date:</strong> <?= $startDate ?></li>
    </ul>

    <!-- II. Background and Rationale -->
    <div class="section-heading">II. Background and Rationale</div>
    <?php foreach (explode("\n", $proposal['rationale'] ?? '') as $para): ?>
        <?php if (trim($para)): ?>
            <p class="body-text"><?= htmlspecialchars(trim($para)) ?></p>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- III. Project Description -->
    <?php if (!empty($proposal['implementation_plan'])): ?>
    <div class="section-heading">III. Project Description</div>
    <?php foreach (explode("\n", $proposal['implementation_plan']) as $para): ?>
        <?php if (trim($para)): ?>
            <p class="body-text"><?= htmlspecialchars(trim($para)) ?></p>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php endif; ?>

    <!-- IV. Goals and Objectives -->
    <div class="section-heading">IV. Project Goals and Objectives</div>
    <?php
    $objectives = $proposal['objectives'] ?? '';
    $objLines   = array_filter(array_map('trim', explode("\n", $objectives)));
    ?>
    <ul class="info-list">
        <?php foreach ($objLines as $obj): ?>
            <li><?= htmlspecialchars(ltrim($obj, '-•·* ')) ?></li>
        <?php endforeach; ?>
    </ul>

</div><!-- end page 1 -->

<!-- ── PAGE 2 ── -->
<div class="page" style="page-break-before: always;">

    <!-- Letterhead (repeated on page 2) -->
    <div class="letterhead">
        <div class="letterhead-logo">BRGY<br>SEAL</div>
        <div class="letterhead-text">
            <div class="republic">Republic of the Philippines</div>
            <div class="city"><?= htmlspecialchars($cityName) ?></div>
            <div class="barangay">Barangay <?= htmlspecialchars($barangayName) ?></div>
            <div class="office">Office of the Sangguniang Barangay</div>
        </div>
        <div class="letterhead-logo">NNC<br>LOGO</div>
    </div>

    <!-- V. Budgetary Requirements -->
    <div class="section-heading">V. Budgetary Requirements</div>

    <?php if (!empty($budgetItems)): ?>
    <table class="budget-table">
        <thead>
            <tr>
                <th style="width:35%">Item Description</th>
                <th style="width:20%">Daily Cost per Child</th>
                <th style="width:30%">Computation<br>(Rate × <?= $numBen ?> Children × <?= $implDays ?> Days)</th>
                <th style="width:15%">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($budgetItems as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['item']) ?></td>
                <td class="center">₱<?= number_format($item['daily_cost'], 2) ?></td>
                <td class="center">
                    ₱<?= number_format($item['daily_cost'], 2) ?> × <?= $numBen ?> × <?= $implDays ?>
                </td>
                <td class="amount">₱<?= number_format($item['total'] ?? 0, 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td><strong>TOTAL:</strong></td>
                <td class="center">
                    ₱<?= number_format(array_sum(array_column($budgetItems, 'daily_cost')), 2) ?>
                </td>
                <td></td>
                <td class="amount">₱<?= number_format($proposal['estimated_budget'], 2) ?></td>
            </tr>
        </tfoot>
    </table>
    <?php else: ?>
    <p class="body-text">Estimated Budget: <strong>₱<?= number_format($proposal['estimated_budget'], 2) ?></strong></p>
    <?php endif; ?>

    <!-- Signature Section -->
    <div class="signature-section">

        <!-- Prepared by: Committee Chair -->
        <div class="signature-block">
            <div class="signature-label"><strong>Prepared by:</strong></div>
            <br>
            <div class="signature-name">
                <?= htmlspecialchars($proposal['creator_first_name'] . ' ' . $proposal['creator_last_name']) ?>
            </div>
            <div class="signature-title">
                Vice Chairperson, Committee on Health
            </div>
        </div>

        <!-- Approved by: Barangay Captain (with digital signature if available) -->
        <div class="signature-block">
            <div class="signature-label"><strong>Approved by:</strong></div>
            <?php if ($approvedValidation && !empty($approvedValidation['signature_data'])): ?>
                <img src="<?= htmlspecialchars($approvedValidation['signature_data']) ?>"
                     class="signature-image" alt="Signature">
            <?php else: ?>
                <br><br>
            <?php endif; ?>
            <div class="signature-name">
                <?php if ($approvedValidation): ?>
                    <?= htmlspecialchars($approvedValidation['validator_first_name'] . ' ' . $approvedValidation['validator_last_name']) ?>
                <?php else: ?>
                    _______________________________
                <?php endif; ?>
            </div>
            <div class="signature-title">Chairperson, Punong Barangay</div>
        </div>

    </div>

</div><!-- end page 2 -->

<script>
// Auto-trigger print dialog immediately when page loads
window.addEventListener('load', function() {
    setTimeout(function() {
        window.print();
    }, 100);
});
</script>

</body>
</html>
