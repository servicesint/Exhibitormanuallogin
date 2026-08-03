<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exhibitor Login</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&family=Poppins:wght@300;400&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f4f4f4;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            padding: 0 20px;
        }

        .card {
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            transition: 0.3s ease;
            text-align: center;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        .card img {
            width: 100%;
            height: 420px;
            object-fit: cover;
        }

        .card h3 {
            font-family: 'Playfair Display', serif;
            letter-spacing: 2px;
            font-size: 20px;
            margin: 15px 0;
            color: #222;
        }

        .btn {
            background: #000;
            color: #fff;
            padding: 14px;
            display: block;
            margin: 15px;
            text-decoration: none;
            font-size: 14px;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn:hover {
            background: #333;
        }

        /* RESPONSIVE */
        @media(max-width: 992px) {
            .container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width: 600px) {
            .container {
                grid-template-columns: 1fr;
            }

            .card img {
                height: 300px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <?php foreach ($subevents as $sub_event): ?>
            <div class="card">
                <?php
                $img = $sub_event->sub_event_logo ?? '';
                $src = !empty($img) ? $img : 'new-default.jpg';
                ?>

                <img src="<?= base_url('assets/images/' . $src) ?>" alt="Sub Event Image">
                <h3><?= $sub_event->sub_event_name; ?></h3>
                <a href="<?= base_url('login/' . encryptData($sub_event->sub_event_id)) ?>" class="btn">
                    EXHIBITOR LOGIN
                </a>
            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>