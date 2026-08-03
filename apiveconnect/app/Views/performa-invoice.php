<?php
$uploadBaseUrl = rtrim(env('UPLOAD_BASE_URL'), '/');
$companyLogo = !empty($company_image) ? $uploadBaseUrl . '/' . ltrim($company_image, '/') : '';
$signature = !empty($signature) ? $uploadBaseUrl . '/' . ltrim($signature, '/') : '';
$displayCustomerName = !empty($customer_name) ? $customer_name : 'M/s Services International';
$displayCustomerAddress = $customer_address ?? '';
$displayCustomerGstin = !empty($customer_gstin) ? $customer_gstin : 'N/A';
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
            font-size: 24px;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 35px;
            margin-bottom: 30px;
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
            text-align: center;
            padding-top: 20px;
        }

        .terms {
            margin-top: 50px;
            font-size: 11px;
            line-height: 1.6;
        }

        .note {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            width: 100%;
            text-align: center;
            font-size: 11px;
            font-style: italic;
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
            <td style="width: 50%; text-align:left;">
                <strong>Bank Account Details:</strong>
                <br>services International<br>
                Bank Name: IndusInd Bank <br>
                Payee Name: Services International
                <br>
                Account Number: 259810057161<br>
                Account Type: Current Account<br>
                IFSC Code (RTGS/NEFT): INDB0001617<br>
                SWIFT Code: INDBINBBXXX<br>
                Bank Address: Unit No. 21, DLF Cross Point, Opp. Galleria Market, DLF Phase 4, Gurugram -
                122002<br>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align:left;">
                <strong>Pan No. AABFS1981P</strong>
            </td>
            <td style="width: 50%; text-align:right;" class="signature">
                For Services International<br><br><br>
                <img src="<?= esc($signature) ?>"
                    width="110"
                    style="max-width:110px; height:auto; object-fit:contain;"><br>
                (Authorised Signature)
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