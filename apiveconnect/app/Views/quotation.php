<?php
$uploadBaseUrl = rtrim(env('UPLOAD_BASE_URL'), '/');
$companyLogo = !empty($company_image) ? $uploadBaseUrl . '/' . ltrim($company_image, '/') : '';
$signature = !empty($signature) ? $uploadBaseUrl . '/' . ltrim($signature, '/') : '';

$safe = function ($value) {
    return esc((string) ($value ?? ''));
};

$displayCustomerName = !empty($customer_name) ? $customer_name : 'M/s Services International';
$displayCustomerAddress = $customer_address ?? '';
$displayCustomerGstin = !empty($customer_gstin) ? $customer_gstin : 'N/A';

$invoiceTitle = isset($invoice_title) && $invoice_title !== '' ? $invoice_title : 'PROFORMA INVOICE';

$invoice_no        = $invoice_no        ?? '';
$date              = $date              ?? '';
$currency_text     = $currency_text     ?? '';
$currency_symbol   = $currency_symbol   ?? '';
$event_name        = $event_name        ?? '';
$payment_method    = $payment_method    ?? '';
$payment_reference = $payment_reference ?? '';
$is_same_state     = !empty($is_same_state);

$subtotal = is_numeric($subtotal ?? null) ? (float) $subtotal : 0.0;
$cgst     = is_numeric($cgst ?? null)     ? (float) $cgst     : 0.0;
$sgst     = is_numeric($sgst ?? null)     ? (float) $sgst     : 0.0;
$igst     = is_numeric($igst ?? null)     ? (float) $igst     : 0.0;
$total    = is_numeric($total ?? null)    ? (float) $total    : 0.0;

$items = (isset($items) && is_array($items)) ? $items : [];
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
            border-bottom: 2px solid #0b5ca8;
            padding-bottom: 10px;
        }

        .company-title {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            letter-spacing: 1px;
            color: #0b5ca8;
        }

        .company-address {
            font-size: 11px;
            text-align: right;
            line-height: 1.5;
            margin-top: 6px;
            color: #0b5ca8;
        }

        .logo-box {
            width: 90px;
            height: 90px;
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
            margin-top: 30px;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .invoice-subtitle {
            text-align: center;
            font-size: 13px;
            margin-bottom: 25px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 25px;
        }

        .info-table td {
            vertical-align: top;
            font-size: 12px;
            line-height: 1.8;
        }

        .info-left {
            font-weight: bold;
            color: #0b5ca8;
        }
        .info-right {
            text-align: right;
            font-weight: bold;
            color: #0b5ca8;
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
            margin-top: 30px;
        }

        .footer-table td {
            vertical-align: top;
            font-size: 12px;
        }

        .signature {
            text-align: right;
            padding-top: 20px;
        }

        .note {
            margin-top: 25px;
            font-size: 11px;
            font-style: italic;
            text-align: center;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td style="width: 20%; text-align:left;">
                <div class="logo-box">
                    <img src="<?= $safe($companyLogo) ?>" width="90" height="auto">
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

    <div class="invoice-title"><?= $safe($invoiceTitle) ?></div>
    <?php if (!empty($event_name)): ?>
        <div class="invoice-subtitle"><?= $safe($event_name) ?></div>
    <?php endif; ?>

    <table class="info-table">
        <tr>
            <td style="width: 55%;" class="info-left">
                <?= $safe($displayCustomerName) ?><br>
                <?php if (!empty($displayCustomerAddress)): ?>
                    <span style="font-weight: normal; color: #000;"><?= $safe($displayCustomerAddress) ?></span><br>
                <?php endif; ?>
                <span style="font-weight: normal; color: #000;">GSTIN No.: <?= $safe($displayCustomerGstin) ?></span>
            </td>
            <td style="width: 45%;" class="info-right">
                INVOICE NO. <?= $safe($invoice_no) ?><br>
                DATE <?= $safe($date) ?>
                <?php if (!empty($payment_method)): ?>
                    <br><span style="font-weight: normal; color: #000;">PAYMENT METHOD: <?= $safe(strtoupper($payment_method)) ?></span>
                <?php endif; ?>
                <?php if (!empty($payment_reference)): ?>
                    <br><span style="font-weight: normal; color: #000;">REFERENCE NO.: <?= $safe($payment_reference) ?></span>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <tr>
            <th colspan="3">PARTICULARS</th>
            <th><?= $safe($currency_text) ?></th>
        </tr>
        <tr>
            <td colspan="4" class="section-heading">
                Charges for Additional Services Provided
                <?php if (!empty($event_name)): ?> At <?= $safe($event_name) ?><?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Description</th>
            <th>Unit Cost/<?= $safe($currency_text) ?></th>
            <th>Quantity</th>
            <th>Total</th>
        </tr>
        <?php foreach ($items as $item): ?>
            <?php
            if (is_object($item)) { $item = (array) $item; }
            if (!is_array($item)) { continue; }

            $description = $item['name'] ?? $item['title'] ?? $item['item_name'] ?? $item['product_name'] ?? 'Item';
            $quantity = isset($item['quantity']) && is_numeric($item['quantity']) ? (int) $item['quantity'] : 1;
            $price = isset($item['price']) && is_numeric($item['price'])
                ? (float) $item['price']
                : (isset($item['unit_price']) && is_numeric($item['unit_price']) ? (float) $item['unit_price'] : 0.0);
            $lineTotal = isset($item['line_total']) && is_numeric($item['line_total']) ? (float) $item['line_total'] : $price * $quantity;
            ?>
            <tr>
                <td><?= $safe($description) ?></td>
                <td class="center"><?= $safe($currency_symbol) ?> <?= number_format($price, 2) ?></td>
                <td class="center"><?= $quantity ?></td>
                <td class="right"><?= $safe($currency_symbol) ?> <?= number_format($lineTotal, 2) ?></td>
            </tr>
        <?php endforeach; ?>

        <?php for ($i = 0; $i < 3; $i++): ?>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <?php endfor; ?>

        <tr class="summary-row">
            <td colspan="3"></td>
            <td class="right"><?= $safe($currency_symbol) ?> <?= number_format($subtotal, 2) ?></td>
        </tr>

        <?php if ($is_same_state): ?>
            <tr>
                <td colspan="3">CGST(@ 9%)</td>
                <td class="right"><?= $safe($currency_symbol) ?> <?= number_format($cgst, 2) ?></td>
            </tr>
            <tr>
                <td colspan="3">SGST(@ 9%)</td>
                <td class="right"><?= $safe($currency_symbol) ?> <?= number_format($sgst, 2) ?></td>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="3">IGST(@ 18%)</td>
                <td class="right"><?= $safe($currency_symbol) ?> <?= number_format($igst, 2) ?></td>
            </tr>
        <?php endif; ?>

        <tr class="summary-row">
            <td colspan="3">Total</td>
            <td class="right"><?= $safe($currency_symbol) ?> <?= number_format($total, 2) ?></td>
        </tr>
    </table>

    <table class="footer-table">
        <tr>
            <td style="width: 50%; text-align:left;">
                Pan No. AABFS1981P
            </td>
            <td style="width: 50%; text-align:right;" class="signature">
                 Services International<br><br>
            </td>
        </tr>
    </table>

    <div class="note">
        Note: This is a system generated invoice. No authorised signature required.
    </div>

</body>

</html>