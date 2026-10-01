<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Coming Soon</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
        }

        body {
            font-family: "Arial", sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;

            background:
                radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.08), transparent 30%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.06), transparent 30%),
                linear-gradient(135deg, #111827, #1f2937, #111827);

            color: #fff;
        }

        /* Background circles */
        body::before,
        body::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            filter: blur(2px);
            opacity: 0.15;
            pointer-events: none;
        }

        body::before {
            width: 350px;
            height: 350px;
            background: #ffffff;
            top: -150px;
            left: -100px;
        }

        body::after {
            width: 400px;
            height: 400px;
            background: #ffffff;
            bottom: -200px;
            right: -120px;
        }

        .coming-soon {
            position: relative;
            z-index: 2;
            width: 90%;
            max-width: 700px;
            text-align: center;
            padding: 60px 40px;

            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;

            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);

            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.35);
        }

        /* Logo */
        .logo {
            margin-bottom: 35px;
        }

        .logo img {
            max-width: 180px;
            max-height: 80px;
            object-fit: contain;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .coming-soon h1 {
            font-size: 64px;
            line-height: 1.1;
            font-weight: 700;
            margin-bottom: 20px;

            background: linear-gradient(
                90deg,
                #ffffff,
                #d1d5db,
                #ffffff
            );

            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .coming-soon p {
            font-size: 19px;
            line-height: 1.7;
            color: #d1d5db;
            margin-bottom: 35px;
        }

        /* Loader */
        .loader {
            width: 55px;
            height: 55px;
            margin: 0 auto;

            border: 4px solid rgba(255, 255, 255, 0.2);
            border-top: 4px solid #ffffff;
            border-right: 4px solid #ffffff;

            border-radius: 50%;

            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            margin-top: 20px;
            font-size: 13px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #9ca3af;
        }

        /* Footer */
        .footer {
            margin-top: 45px;
            font-size: 13px;
            color: #9ca3af;
        }

        /* Mobile */
        @media (max-width: 600px) {

            .coming-soon {
                padding: 45px 25px;
                border-radius: 18px;
            }

            .coming-soon h1 {
                font-size: 42px;
            }

            .coming-soon p {
                font-size: 16px;
            }

            .logo img {
                max-width: 140px;
            }
        }
    </style>
</head>

<body>

    <div class="coming-soon">


        <h1>Coming Soon</h1>


        <div class="loader"></div>

        <div class="loading-text">
            Please wait...
        </div>

        <div class="footer">
            &copy; <?= date('Y') ?> All Rights Reserved.
        </div>

    </div>

</body>
</html>