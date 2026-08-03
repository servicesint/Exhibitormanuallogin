<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proforma Invoice</title>
    <style>
        body {
            font-family: dejavusans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .company-header {
            text-align: right;
            margin-bottom: 15px;
        }

        .company-header h3 {
            margin: 0;
            font-size: 16px;
        }

        .company-header p {
            margin: 2px 0;
            font-size: 11px;
        }

        h1.title {
            text-align: center;
            text-decoration: underline;
            font-size: 20px;
            margin: 20px 0;
        }

        .invoice-meta {
            width: 100%;
            margin-bottom: 15px;
        }

        .invoice-meta td {
            vertical-align: top;
            font-size: 12px;
            padding: 2px 0;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.items th,
        table.items td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 12px;
        }

        table.items th {
            background: #f0f0f0;
        }

        .footer-note {
            margin-top: 60px;
            font-style: italic;
            font-size: 11px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="company-header">
        <h3>SERVICES INTERNATIONAL</h3>
        <p>D-4, FIRST FLOOR A-BLOCK, LOCAL SHOPPING COMPLEX,</p>
        <p>NARAINA VIHAR RING ROAD, NEW DELHI - 110028</p>
        <p>GSTIN: 07AABFS1981P1ZO</p>
    </div>

    <h1 class="title">PROFORMA INVOICE</h1>

    <table class="invoice-meta">
        <tr>
            <td style="width:60%;">
                <strong>M/s <?= esc($vendor_name ?? '') ?></strong><br>
                GSTIN No.: <?= esc($vendor_gstin ?? 'N/A') ?>
            </td>
            <td style="width:40%; text-align:right;">
                INVOICE NO. <?= esc($invoice_number ?? '') ?><br>
                DATE <?= esc($invoice_date ?? '') ?>
            </td>
        </tr>
    </table>

    <table class="items">
        <tr>
            <th colspan="2">PARTICULARS</th>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center; font-weight:bold;">
                Charges for Additional Services Provided at <?= esc($event_name ?? '') ?>
            </td>
        </tr>
        <tr>
            <th>Product Name</th>
            <th style="width:150px;">Quantity</th>
        </tr>
        <?php foreach (($items ?? []) as $item): ?>
            <tr>
                <td><?= esc($item['item_name'] ?? '') ?></td>
                <td><?= esc($item['qty'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p style="margin-top:20px;">Pan No. <?= esc($pan_no ?? '') ?></p>
    <!-- <p style="text-align:right; margin-top:40px;">For Services International<br><br>(Authorised Signature)</p> -->

    <p class="footer-note">Note: This is a system generated invoice. No authorised signature required.</p>
</body>

</html>