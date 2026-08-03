<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
        }

        .container {
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }

        h1 {
            font-size: 120px;
            margin: 0;
            font-weight: 700;
        }

        p {
            font-size: 18px;
            margin: 10px 0 30px;
            opacity: 0.9;
        }

        .btn {
            display: inline-block;
            padding: 12px 28px;
            border-radius: 30px;
            background: #fff;
            color: #333;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn:hover {
            background: #f1f1f1;
            transform: translateY(-2px);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .emoji {
            font-size: 60px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="emoji">😕</div>
        <h1>404</h1>
        <p>Oops! The page you are looking for doesn't exist.</p>
        <?php if (!empty($enc_id)): ?>
            <a href="<?= base_url('login/' . $enc_id) ?>" class="btn">
                Go to Login
            </a>
        <?php else: ?>
            <a href="<?= base_url('login/' . session()->get('enc_sub_event_id')) ?>" class="btn">
                Go Home
            </a>
        <?php endif; ?>
    </div>
</body>

</html>