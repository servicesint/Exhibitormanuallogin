<?php
$safe = function ($value) {
    return esc((string)($value ?? ''));
};

$primary = $theme_primary ?? '#8b0000';

if (!empty($badge_background)) {
    $backgroundCss = "
        background-image:url('" . $safe($badge_background) . "');
        background-size:100% 100%;
        background-position:center center;
        background-repeat:no-repeat;
    ";
} else {
    $backgroundCss = "
        background-color:{$primary};
    ";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: 100mm 125mm;
            margin: 0;
        }

        html,
        body {
            width: 100mm;
            height: 125mm;
            margin: 0;
            padding: 0;
            font-family: dejavusans;
        }

        * {
            box-sizing: border-box;
        }

        .badge-wrap {
            width: 100mm;
            height: 125mm;
            margin: 0;
            padding: 0;
            <?= $backgroundCss ?>color: #ffffff;
        }

        .badge-table {
            width: 100mm;
            border-collapse: collapse;
            border-spacing: 0;
        }

        .exhibitor-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .company-name {
            font-family: dejavuserif;
            font-size: 8px;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .qr-wrapper {
            width: 30mm;
            height: 30mm;
            background: #ffffff;
        }
    </style>

</head>

<body>

    <div class="badge-wrap">
        <table class="badge-table" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td style="height:26mm;">&nbsp;</td>
            </tr>
            <tr>
                <td align="center" valign="middle" style="height:32mm;">
                    <?php if (!empty($photo)): ?>
                        <img src="<?= $safe($photo) ?>" width="113" height="113" style="width:30mm;height:30mm;">
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td align="center" valign="middle" style="height:12mm;color: white;">
                    <div class="exhibitor-name"><?= $safe($full_name) ?></div>
                </td>
            </tr>
            <?php if (!empty($company_name)): ?>
                <tr>
                    <td align="center" valign="middle" style="height:8mm;color: white;">
                        <div class="company-name">(<?= $safe($company_name) ?>)</div>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <td align="center" valign="middle" style="height:32mm;">
                    <?php if (!empty($qr)): ?>
                        <table cellpadding="4" cellspacing="0" align="center" class="qr-wrapper">
                            <tr>
                                <td align="center" valign="middle">
                                    <img src="<?= $safe($qr) ?>"
                                        width="106" height="106"
                                        style="width:28mm;height:28mm;">
                                </td>
                            </tr>
                        </table>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td style="height:15mm;">&nbsp;</td>
            </tr>
        </table>
    </div>

</body>
</html>