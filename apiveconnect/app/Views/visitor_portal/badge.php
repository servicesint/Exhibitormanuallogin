<?php
$e = fn($value) => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$name = trim(($badge['salutation'] ? $badge['salutation'] . ' ' : '') . $badge['first_name'] . ' ' . $badge['last_name']);
$accent = $title === 'Delegate' ? '#8e1b1b' : '#105489';
?>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
            color: #222;
        }

        .badge {
            width: 100mm;
            margin: 0 auto;
            border: 1px solid #ccc;
            border-radius: 4mm;
            text-align: center;
            padding-bottom: 6mm;
        }

        .logo {
            padding: 6mm 6mm 2mm 6mm;
        }

        .event {
            font-size: 11pt;
            color: #555;
            padding: 0 6mm;
        }

        .name {
            font-size: 20pt;
            font-weight: bold;
            margin-top: 6mm;
            padding: 0 6mm;
        }

        .meta {
            font-size: 11pt;
            color: #444;
            margin-top: 2mm;
            padding: 0 6mm;
        }

        .code {
            font-size: 10pt;
            color: #666;
            letter-spacing: 1px;
        }

        .role {
            background: <?= $accent ?>;
            color: #fff;
            font-size: 18pt;
            font-weight: bold;
            letter-spacing: 3px;
            padding: 4mm 0;
            margin-top: 5mm;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="badge">
        <div class="logo">
            <?php if (!empty($logo_url)) { ?>
                <img src="<?= $e($logo_url) ?>" style="max-height: 22mm; max-width: 80mm;" />
            <?php } ?>
        </div>
        <div class="event">
            <?= $e($sub_event['sub_event_name'] ?? '') ?>
            <?php if (!empty($sub_event['full_date'])) { ?><br><?= $e($sub_event['full_date']) ?><?php } ?>
            <?php if (!empty($sub_event['venue'])) { ?><br><?= $e($sub_event['venue']) ?><?php } ?>
        </div>

        <div class="name"><?= $e($name) ?></div>
        <?php if (!empty($badge['designation'])) { ?><div class="meta"><?= $e($badge['designation']) ?></div><?php } ?>
        <?php if (!empty($badge['organization_name'])) { ?><div class="meta"><strong><?= $e($badge['organization_name']) ?></strong></div><?php } ?>

        <div style="margin-top: 5mm;">
            <barcode code="<?= $e($vcard) ?>" type="QR" size="1.4" error="M" disableborder="1" />
        </div>
        <div class="code"><?= $e($badge_code) ?></div>

        <div class="role"><?= $e($title) ?></div>
    </div>
</body>

</html>
