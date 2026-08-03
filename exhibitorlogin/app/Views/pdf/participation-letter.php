<?php
$companyName = $company_name ?? 'M/S SI';
$eventYear = $event_year ?? date('Y');
$eventVenue = $event_venue ?? '';
$stallNo = $stall_no ?? '';
$letterDate = $letter_date ?? date('d F Y');
$signatoryName = $signatory_name ?? 'Payal Paul';
$signatoryMobile = $signatory_mobile ?? '+91-9354688923';
$signatoryEmail = $signatory_email ?? 'ppaul@servintonline.com';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drone Expo & Conference</title>
</head>

<body>
    <div style="padding:20px;">
        <img src="./assets/images/silogo.png" width="100" style="float:right;" />
        <div style="clear:both;"></div>
        <h4 style="text-align:right;"><?= esc($letterDate) ?></h4>
        <div style="clear:both;"></div><br><br>

        <div style="margin-top:20px;">
            <h2 style="border-bottom:1px solid #000;text-align:center; ">TO WHOMSOEVER IT MAY CONCERN</h2>
            <br>
            <p style="font-size:18px;">This is to certify that <b><?= esc($companyName) ?></b> is participating in <b><?= esc($eventYear) ?></b>, to be held at <?= esc($eventVenue ?: 'the venue specified by the organizer') ?>. Stall No. <?= esc($stallNo ?: 'TBD') ?> has been allocated for their participation. </p>
        </div>

        <img src="./assets/images/sign.png" width="120" />
        <p style="font-size:18px;margin-top:-10px">Best Regards,<br><b><?= esc($signatoryName) ?></b><br>Mob: <?= esc($signatoryMobile) ?><br>Email: <?= esc($signatoryEmail) ?></p>


    </div>
    <div style="margin-top:110px;">
        <p>D-4, A-Block, LSC, Naraina Vihar, Ring Road, New Delhi-110028, India<br>Tel: +91 11 45055500 Email: info@servintonline.com</p>
        <p><b>www.servintonline.com</b></p>
    </div>
    <div>
        <p style="font-size: 18px; color: #0099CC; text-align: right; margin-top: -60px;">EXHIBITIONS<br>CONFERENCES</p>
    </div>
</body>

</html>