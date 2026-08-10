<?php
$uploadBaseUrl = rtrim(env('UPLOAD_BASE_URL'), '/');
$companyLogo = !empty($company_image) ? $uploadBaseUrl . '/' . ltrim($company_image, '/') : '';
$signature = !empty($signature) ? $uploadBaseUrl . '/' . ltrim($signature, '/') : '';
$displayCustomerName = !empty($customer_name) ? $customer_name : 'M/s Services International';
$displayCustomerAddress = $customer_address ?? '';
$displayCustomerGstin = !empty($customer_gstin) ? $customer_gstin : 'N/A';

// Normalize exhibitor type: default to Domestic if not supplied
$exhibitorType = !empty($exhibitor_type) ? strtolower(trim($exhibitor_type)) : 'domestic';
$isInternational = ($exhibitorType === 'international');

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: dejavusans;
            font-size: 12px;
            color: #000;

        }

        .header-table {
            width: 100%;
            border-bottom: 1px solid #000;
            padding-bottom: 8px;
        }

        .company-title {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            letter-spacing: 1px;
        }

        .company-address {
            font-size: 12px;
            text-align: right;
            line-height: 1.5;
            margin-top: 8px;
        }

        .logo-box {
            width: 80px;
            height: 80px;
            text-align: left;
        }

        .logo-box img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .invoice-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 22px;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 35px;
        }

        .info-table td {
            vertical-align: top;
            font-size: 12px;
            line-height: 1.8;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 12px;
        }

        .items-table th {
            font-weight: bold;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .section-heading {
            font-weight: bold;
            text-align: center;
        }

        .summary-row td {
            font-weight: bold;
        }

        .footer-table {
            width: 100%;
            margin-top: 35px;
        }

        .footer-table td {
            vertical-align: top;
            font-size: 12px;
        }

        .signature {
            text-align: right;
            padding-top: 20px;
        }

        .terms {
            margin-top: 50px;
            font-size: 11px;
            line-height: 1.6;
        }

        .note {
            width: 100%;
            text-align: center;
            font-size: 10.5px;
            font-style: italic;
            color: #444444;
            margin-top: 22px;
            padding-top: 6px;
            border-top: 1px solid #e0e0e0;
        }

        /* ---- Bank details box ---- */
        .bank-box {
            width: 100%;
            border: 1px solid #b5b5b5;
            box-sizing: border-box;
            margin-top: 8px;
            page-break-inside: avoid;
        }

        .bank-heading-bar {
            background-color: #1f2d3d;
            color: #ffffff;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 2px;
            padding: 5px 0;
        }

        .bank-payee-line {
            text-align: center;
            font-size: 10.5px;
            font-style: italic;
            color: #333333;
            padding: 6px 14px 2px 14px;
        }

        .bank-inner {
            padding: 2px 14px 10px 14px;
        }

        .bank-columns {
            width: 100%;
            border-collapse: collapse;
        }

        .bank-col-beneficiary {
            width: 58%;
            vertical-align: top;
            padding-right: 12px;
        }

        .bank-col-intermediary {
            width: 42%;
            vertical-align: top;
            padding-left: 12px;
            border-left: 1px solid #d5d5d5;
        }

        .bank-section-title {
            font-size: 10px;
            font-weight: bold;
            color: #1f2d3d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 0 3px 0;
            border-bottom: 1px solid #d5d5d5;
            margin-bottom: 2px;
        }

        .bank-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bank-table td {
            font-size: 10.5px;
            padding: 2.5px 4px;
            vertical-align: top;
            line-height: 1.35;
        }

        .bank-table tr:nth-child(even) td {
            background-color: #f6f7f9;
        }

        .bank-label {
            width: 42%;
            font-weight: bold;
            color: #333333;
        }

        .bank-colon {
            width: 3%;
            color: #333333;
        }

        .bank-value {
            width: 55%;
            color: #000000;
        }

        .pan-gst-strip {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .pan-gst-strip td {
            font-size: 10.5px;
            padding: 5px 10px;
            background-color: #f6f7f9;
            border: 1px solid #e0e0e0;
        }

        .pan-gst-strip .pg-label {
            font-weight: bold;
            color: #1f2d3d;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td style="width: 20%; text-align:left;">
                <div class="logo-box">
                    <img src="<?= esc($companyLogo) ?>" width="90" height="auto">
                </div>
            </td>
            <td style="width: 80%; text-align:right;">
                <div class="company-title">SERVICES INTERNATIONAL</div>

                <div class="company-address">
                    D-4, FIRST FLOOR A-BLOCK, LOCAL SHOPPING COMPLEX,<br>
                    NARAINA VIHAR RING ROAD, NEW DELHI - 110028<br>
                    GSTIN: 07AABFS1981P1ZO
                </div>
            </td>
        </tr>
    </table>
    <div class="invoice-title">PROFORMA INVOICE</div>
    <table class="info-table">
        <tr>
            <td style="width: 55%;">
                <strong><?= esc($displayCustomerName) ?></strong><br>
                <?php if (!empty($displayCustomerAddress)): ?>
                    <?= esc($displayCustomerAddress) ?><br>
                <?php endif; ?>
                GSTIN No.: <?= esc($displayCustomerGstin) ?>
            </td>
            <td style="width: 45%; text-align:right;">
                INVOICE NO. <?= esc($invoice_no) ?><br>
                DATE <?= esc($date) ?>
            </td>
        </tr>
    </table>
    <table class="items-table">
        <tr>
            <th colspan="3">PARTICULARS</th>
            <th><?= esc($currency_text) ?></th>
        </tr>
        <tr>
            <td colspan="4" class="section-heading">
                Charges for Additional Services Provided At <?= esc($event_name) ?>
            </td>
        </tr>
        <tr>
            <th>Description</th>
            <th>Unit Cost/<?= esc($currency_text) ?></th>
            <th>Quantity</th>
            <th>Total</th>
        </tr>
        <?php foreach ($items as $item): ?>
            <?php
            $description = $item['name']
                ?? $item['title']
                ?? $item['item_name']
                ?? $item['product_name']
                ?? 'Item';

            $price     = isset($item['price']) ? (float) $item['price'] : 0;
            $quantity  = isset($item['quantity']) ? (int) $item['quantity'] : 1;
            $lineTotal = $price * $quantity;
            ?>
            <tr>
                <td><?= esc($description) ?></td>
                <td class="center"><?= esc($currency_symbol) ?><?= number_format($price, 2) ?></td>
                <td class="center"><?= $quantity ?></td>
                <td class="right"><?= esc($currency_symbol) ?><?= number_format($lineTotal, 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php for ($i = 0; $i < 3; $i++): ?>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        <?php endfor; ?>
        <tr class="summary-row">
            <td colspan="3"></td>
            <td class="right"><?= esc($currency_symbol) ?><?= number_format($subtotal, 2) ?></td>
        </tr>

        <?php if (!empty($is_same_state)): ?>
            <tr>
                <td colspan="3">CGST(@ 9%)</td>
                <td class="right"><?= esc($currency_symbol) ?><?= number_format($cgst, 2) ?></td>
            </tr>

            <tr>
                <td colspan="3">SGST(@ 9%)</td>
                <td class="right"><?= esc($currency_symbol) ?> <?= number_format($sgst, 2) ?></td>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="3">IGST(@ 18%)</td>
                <td class="right"><?= esc($currency_symbol) ?> <?= number_format($igst, 2) ?></td>
            </tr>
        <?php endif; ?>

        <tr class="summary-row">
            <td colspan="3">Total</td>
            <td class="right">
                <?= esc($currency_symbol) ?> <?= number_format($total, 2) ?>
            </td>
        </tr>
    </table>

    <table class="footer-table">
        <tr>
            <td style="width: 100%;">
                <div class="bank-box">
                    <div class="bank-heading-bar">BANK DETAILS</div>
                    <div class="bank-payee-line">
                        Payment to be made in favor of &ldquo;Services International&rdquo;
                    </div>

                    <div class="bank-inner">
                        <?php if ($isInternational): ?>
                            <table class="bank-columns">
                                <tr>
                                    <td class="bank-col-beneficiary">
                                        <div class="bank-section-title">Beneficiary Bank</div>
                                        <table class="bank-table">
                                            <tr>
                                                <td class="bank-label">Name</td>
                                                <td class="bank-colon">:</td>
                                                <td class="bank-value">Services International</td>
                                            </tr>
                                            <tr>
                                                <td class="bank-label">Bank Name</td>
                                                <td class="bank-colon">:</td>
                                                <td class="bank-value">HDFC Bank</td>
                                            </tr>
                                            <tr>
                                                <td class="bank-label">Address</td>
                                                <td class="bank-colon">:</td>
                                                <td class="bank-value">209-214, Kailash Building 26,
                                                    Kasturba Gandhi Marg, New Delhi - 110001</td>
                                            </tr>
                                            <tr>
                                                <td class="bank-label">Account Number</td>
                                                <td class="bank-colon">:</td>
                                                <td class="bank-value">00032210003368</td>
                                            </tr>
                                            <tr>
                                                <td class="bank-label">Account Type</td>
                                                <td class="bank-colon">:</td>
                                                <td class="bank-value">Current Account</td>
                                            </tr>
                                            <tr>
                                                <td class="bank-label">RTGS/NEFT IFSC</td>
                                                <td class="bank-colon">:</td>
                                                <td class="bank-value">HDFC0000003</td>
                                            </tr>
                                            <tr>
                                                <td class="bank-label">Swift Code</td>
                                                <td class="bank-colon">:</td>
                                                <td class="bank-value">HDFCINBB</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="bank-col-intermediary">
                                        <div class="bank-section-title">Intermediary Bank</div>
                                        <table class="bank-table">
                                            <tr>
                                                <td class="bank-label">Bank</td>
                                                <td class="bank-colon">:</td>
                                                <td class="bank-value">JP Morgan Chase Bank,
                                                    New York</td>
                                            </tr>
                                            <tr>
                                                <td class="bank-label">Account No.</td>
                                                <td class="bank-colon">:</td>
                                                <td class="bank-value">001-1-406717</td>
                                            </tr>
                                            <tr>
                                                <td class="bank-label">Swift Code</td>
                                                <td class="bank-colon">:</td>
                                                <td class="bank-value">CHASUS33</td>
                                            </tr>
                                            <tr>
                                                <td class="bank-label">ABA Code</td>
                                                <td class="bank-colon">:</td>
                                                <td class="bank-value">021000021</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        <?php else: ?>
                            <table class="bank-table">
                                <tr>
                                    <td class="bank-label">Name</td>
                                    <td class="bank-colon">:</td>
                                    <td class="bank-value">Services International</td>
                                </tr>
                                <tr>
                                    <td class="bank-label">Bank Name</td>
                                    <td class="bank-colon">:</td>
                                    <td class="bank-value">IndusInd Bank</td>
                                </tr>
                                <tr>
                                    <td class="bank-label">Address</td>
                                    <td class="bank-colon">:</td>
                                    <td class="bank-value">Unit No. 21, DLF Cross Point, Opp. Galleria
                                        Market, DLF Phase 4, Gurugram - 122002</td>
                                </tr>
                                <tr>
                                    <td class="bank-label">Account Number</td>
                                    <td class="bank-colon">:</td>
                                    <td class="bank-value">259810057161</td>
                                </tr>
                                <tr>
                                    <td class="bank-label">Account Type</td>
                                    <td class="bank-colon">:</td>
                                    <td class="bank-value">Current Account</td>
                                </tr>
                                <tr>
                                    <td class="bank-label">IFSC Code (RTGS/NEFT)</td>
                                    <td class="bank-colon">:</td>
                                    <td class="bank-value">INDB0001617</td>
                                </tr>
                            </table>
                        <?php endif; ?>

                        <table class="pan-gst-strip">
                            <tr>
                                <td style="width:50%;">
                                    <span class="pg-label">PAN No.:</span> AABFS1981P
                                </td>
                                <td style="width:50%;">
                                    <span class="pg-label">GST No.:</span> 07AABFS1981P1ZO
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <div class="terms">
    </div>
    <div class="note">
        
        Note: This is a system generated invoice. No authorised signature required.
    </div>

</body>

</html>