<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drone Expo & Conference</title>
</head>

<body>
    <div style="padding:20px;">
        <div style="margin-top:0px;">
            <h2 style="background:#000;color:#fff;text-align:center;">PASS OUT "EXIT PERMIT" FORM</h2>
            <p style="font-size:16px;text-align:center;">
                <b>TO BE SUBMITTED IN TRIPLET DULY STAMPED BY EXHIBITORS</b>
            </p>
            <p style="font-size:16px;">
                Dear Sir,<br><br>
                Please allow exit of the following materials/exhibits from Banglore<br><br>

                (Please give details of loose items/number of packed boxes)
            </p>
            <table style="width:100%;text-align:center;border-collapse: collapse;" border="1">
                <tr>
                    <td width="40">1. </td>
                    <td></td>
                </tr>
                <tr>
                    <td>2. </td>
                    <td></td>
                </tr>

                <tr>
                    <td>3. </td>
                    <td></td>
                </tr>

                <tr>
                    <td>4. </td>
                    <td></td>
                </tr>

                <tr>
                    <td>5. </td>
                    <td></td>
                </tr>

                <tr>
                    <td>6. </td>
                    <td></td>
                </tr>

                <tr>
                    <td>7. </td>
                    <td></td>
                </tr>

                <tr>
                    <td>8. </td>
                    <td></td>
                </tr>

                <tr>
                    <td>9. </td>
                    <td></td>
                </tr>

                <tr>
                    <td>10. </td>
                    <td></td>
                </tr>

            </table>

            <p style="font-size:16px;">
                These exhibits/materials to <b><?= esc($exitPermitData['organisation_name'] ?? '') ?></b> and stall Number <b><?= esc($exitPermitData['stall_number'] ?? '') ?></b> who are participating
                /providing services in the Drone Expo
            </p>
            <table style="width:100%;text-align:left;border-collapse: collapse;" border="1">
                <tr>
                    <td>Name of Organizer:</td>
                    <td><b>SERVICES INTERNATIONAL</b></td>
                </tr>
                <tr>
                    <td>Place:</td>
                    <td><b>Bangalore</b></td>
                </tr>

                <tr>
                    <td>Date:</td>
                    <td><b><?= date("jS M Y") ?></b></td>
                </tr>
            </table>
        </div>

        <br><br>
        <div style="width:48%;float:left;">
            <p>Name: ___________________<br>
                Designation: _____________<br>
                Signature: _______________</p>
            <br><br>
            <p>Date: <?= date("jS M Y") ?></p>

        </div>
        <div style="width:50%;float:left;">
            <p style="text-align:right;margin-top:70px;">(Authorized Signatory)</p>
            <br><br>
            <p style="text-align:right;margin-top:0px;"><b>SERVICES INTERNATIONAL</b></p>
        </div>
    </div>
    <div style="margin-top:30px;">

    </div>
    <div>
    </div>
</body>

</html>