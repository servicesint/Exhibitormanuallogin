<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exhibitor OTP Login</title>
    <link rel="shortcut icon" href="<?= base_url('assets/images/icons/favicon.ico'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body.login-page {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at top, rgba(0, 123, 255, 0.15), transparent 25%),
                linear-gradient(135deg, #07111f 0%, #16263d 45%, #0f172a 100%);
            font-family: "Segoe UI", Arial, sans-serif;
        }

        /* subtle ambient glow blobs for depth */
        body.login-page::before,
        body.login-page::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            filter: blur(70px);
            z-index: 0;
            pointer-events: none;
        }

        body.login-page::before {
            width: 380px;
            height: 380px;
            top: -120px;
            left: -100px;
            background: rgba(56, 189, 248, 0.18);
        }

        body.login-page::after {
            width: 320px;
            height: 320px;
            bottom: -100px;
            right: -80px;
            background: rgba(99, 102, 241, 0.16);
        }

        .login-card {
            position: relative;
            z-index: 1;
            width: min(100%, 460px);
            background: rgba(15, 23, 42, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.45);
            padding: 34px 30px;
            color: #e5eefb;
            backdrop-filter: blur(12px);
        }

        .login-brand {
            display: flex;
            justify-content: center;
            margin-bottom: 16px;
        }

        .login-brand-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #bfdbfe;
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.22), rgba(59, 130, 246, 0.12));
            border: 1px solid rgba(148, 163, 184, 0.22);
            box-shadow: 0 8px 20px rgba(56, 189, 248, 0.12);
        }

        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(59, 130, 246, 0.16);
            color: #bfdbfe;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.18em;
        }

        .login-title {
            font-size: 26px;
            font-weight: 800;
            margin-top: 4px;
            margin-bottom: 6px;
            color: #fff;
            text-align: center;
            letter-spacing: 0.01em;
        }

        .login-subtitle {
            font-size: 14.5px;
            color: #a9b7cc;
            margin-bottom: 24px;
            text-align: center;
            line-height: 1.5;
        }

        .info-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #bfdbfe;
            background: rgba(30, 41, 59, 0.95);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 14px;
        }

        .form-label {
            color: #cbd5e1;
            font-size: 12.5px;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .login-input {
            width: 100%;
            min-height: 48px;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, 0.24);
            background: rgba(30, 41, 59, 0.95);
            color: #fff;
            padding: 12px 14px 12px 42px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }

        .login-input::placeholder {
            color: #64748b;
        }

        .login-input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.15);
            background: #172033;
            color: #fff;
            outline: none;
        }

        .login-input-container {
            margin-bottom: 18px;
        }

        .login-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .login-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 14px;
            pointer-events: none;
            transition: color 0.15s ease;
        }

        .login-input:focus~.login-icon,
        .login-input-wrap:focus-within .login-icon {
            color: #38bdf8;
        }

        .btn-otp {
            min-height: 48px;
            border-radius: 12px;
            font-weight: 600;
            letter-spacing: 0.01em;
            box-shadow: 0 10px 25px rgba(56, 189, 248, 0.15);
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
        }

        .btn-otp:hover:not(:disabled) {
            transform: translateY(-1px);
            filter: brightness(1.05);
        }

        .btn-otp:disabled {
            opacity: 0.75;
        }

        .otp-panel {
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(17, 24, 39, 0.96);
            border-radius: 16px;
            padding: 18px 16px;
            margin-top: 6px;
        }

        /* Numeric OTP field styled like a code entry, without changing its
           id/name/behavior — purely visual (spacing, size, centering). */
        #otp {
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 10px;
            padding-left: 14px;
        }

        .muted-note {
            color: #94a3b8;
            font-size: 12.5px;
            margin-top: 10px;
            text-align: center;
        }

        .helper-link {
            color: #bfdbfe;
            text-decoration: none;
            font-size: 13px;
        }

        .helper-link:hover {
            color: #fff;
            text-decoration: underline;
        }

        .login-divider {
            border-top: 1px solid rgba(148, 163, 184, 0.14);
            margin: 22px 0 16px;
        }
    </style>
</head>

<body class="login-page">
    <div class="position-fixed top-0 end-0 p-3" style="z-index:1080">
        <div id="appToast" class="toast align-items-center text-white bg-danger border-0">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <div class="login-card">
        <div class="login-brand">
            <div class="login-brand-icon">
                <i class="fa-solid fa-store"></i>
            </div>
        </div>
        <h2 class="login-title">Exhibitor Login</h2>
        <p class="login-subtitle">Send a OTP to your registered email or mobile number.</p>
        <form id="otpLoginForm" autocomplete="off">
            <?= csrf_field() ?>
            <input type="hidden" id="enc_sub_event_id" name="enc_sub_event_id" value="<?= esc($enc_sub_event_id) ?>">
            <input type="hidden" id="referral_website" name="referral_website" value="<?= esc(session()->get('referreral_website') ?? '') ?>">
            <div class="login-input-container">
                <label class="form-label" for="identifier">Email / Mobile Number</label>
                <div class="login-input-wrap">
                    <input type="text" id="identifier" name="identifier" class="login-input" placeholder="Enter email or mobile number" required>
                    <i class="fa-solid fa-envelope login-icon"></i>
                </div>
            </div>
            <div class="d-grid mt-2 mb-2">
                <button type="button" id="sendOtpBtn" class="btn btn-primary btn-otp"><i class="fa-solid fa-paper-plane me-2"></i>Send OTP</button>
            </div>
            <div class="otp-panel d-none" id="otpStep">
                <label class="form-label" for="otp">Enter 6-digit OTP</label>
                <input type="text" id="otp" name="otp" class="login-input" inputmode="numeric" maxlength="6" placeholder="••••••">
                <div class="d-grid mt-3 gap-2">
                    <button type="button" id="verifyOtpBtn" class="btn btn-success btn-otp"><i class="fa-solid fa-unlock-keyhole me-2"></i>Verify OTP & Login</button>
                    <button type="button" id="resendOtpBtn" class="btn btn-outline-light btn-otp"><i class="fa-solid fa-rotate-right me-2"></i>Resend OTP</button>
                </div>
                <p class="muted-note">Use the OTP sent to your registered email or mobile number.</p>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const csrfName = '<?= csrf_token() ?>';
        const csrfHash = '<?= csrf_hash() ?>';

        $(function() {
            $.ajax({
                url: '<?= base_url('/guestlogin') ?>',
                type: 'GET',
                dataType: 'json',       
                success: function(res) {
                    console.log(res);
                    if (res.status === 'success' && res.data && res.data.referreral_website) {
                        localStorage.setItem('reference_website', res.data.referreral_website);
                    }
                },
                error: function() {
                    console.warn('Unable to fetch reference website.');
                }
            });

            const toastEl = document.getElementById('appToast');
            const toastMsg = $('#toastMessage');
            const toast = new bootstrap.Toast(toastEl, {
                delay: 4000
            });
            let resendCount = parseInt(localStorage.getItem('otp_resend_count') || '0');
            let resendTimer = null;
            const remaining = getRemainingSeconds();
            if (remaining > 0) {
                startResendTimer();
            }

            function showToast(message, type = 'danger') {
                $(toastEl)
                    .removeClass('bg-danger bg-success bg-warning bg-info')
                    .addClass('bg-' + type);
                toastMsg.text(message);
                toast.show();
            }

            function isValidIdentifier(value) {
                const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value);
                const mobileOk = /^\+?[0-9\s-]{7,20}$/.test(value);
                return emailOk || mobileOk;
            }

            function getRemainingSeconds() {
                const nextAllowedAt = parseInt(
                    localStorage.getItem('otp_next_allowed_at') || '0'
                );
                const remaining = Math.ceil(
                    (nextAllowedAt - Date.now()) / 1000
                );
                return remaining > 0 ? remaining : 0;
            }

            function startResendTimer() {
                resendCount++;
                localStorage.setItem(
                    'otp_resend_count',
                    resendCount
                );
                let seconds = resendCount * 15;
                const nextAllowedAt = Date.now() + (seconds * 1000);
                localStorage.setItem(
                    'otp_next_allowed_at',
                    nextAllowedAt
                );
                clearInterval(resendTimer);
                resendTimer = setInterval(function() {
                    let remaining = getRemainingSeconds();
                    if (remaining <= 0) {
                        clearInterval(resendTimer);
                        $('#resendOtpBtn')
                            .prop('disabled', false)
                            .html(
                                '<i class="fa-solid fa-rotate-right me-2"></i>Resend OTP'
                            );
                        return;
                    }
                    $('#resendOtpBtn')
                        .prop('disabled', true)
                        .html(
                            '<i class="fa-solid fa-clock me-2"></i>Resend in ' +
                            remaining +
                            's'
                        );

                }, 1000);
            }

            function sendOtp() {
                const identifier = $.trim($('#identifier').val());
                if (!identifier) {
                    showToast('Please enter your email or mobile number');
                    $('#identifier').focus();
                    return;
                }
                if (!isValidIdentifier(identifier)) {
                    showToast('Enter a valid email address or mobile number');
                    $('#identifier').focus();
                    return;
                }
                $('#sendOtpBtn')
                    .prop('disabled', true)
                    .html(
                        '<span class="spinner-border spinner-border-sm me-2"></span>Sending...'
                    );
                $.ajax({
                    url: '<?= env('API_BASE_URL') ?>/auth/v1/send-otp',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        identifier: identifier,
                        enc_sub_event_id: $('#enc_sub_event_id').val(),
                        referral_website: $('#referral_website').val(),
                        [csrfName]: csrfHash
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#otpStep').removeClass('d-none');
                            $('#sendOtpBtn').addClass('d-none');
                            $('#identifier').prop('disabled', true);
                            $('#otp').focus();
                            startResendTimer();
                            showToast(res.message, 'success');
                            if (res.debug_otp) {
                                showToast(
                                    'Otp Send Successfully',
                                    'info'
                                );
                            }
                        } else {
                            showToast(
                                res.message ||
                                'Unable to send OTP right now.',
                                'danger'
                            );
                        }
                    },
                    error: function() {
                        showToast(
                            'Unable to contact the server. Please try again.',
                            'danger'
                        );
                    },
                    complete: function() {
                        $('#sendOtpBtn')
                            .prop('disabled', false)
                            .html(
                                '<i class="fa-solid fa-paper-plane me-2"></i>Send OTP'
                            );
                    }
                });
            }

            function verifyOtp() {
                const otp = $.trim($('#otp').val());
                if (!/^\d{6}$/.test(otp)) {
                    showToast(
                        'Enter the 6-digit OTP received on your email or mobile number'
                    );
                    $('#otp').focus();
                    return;
                }
                $('#verifyOtpBtn')
                    .prop('disabled', true)
                    .html(
                        '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...'
                    );
                $.ajax({
                    url: '<?= env('API_BASE_URL') ?>/auth/v1/verify-otp',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        identifier: $('#identifier').val(),
                        otp: otp,
                        enc_sub_event_id: $('#enc_sub_event_id').val(),
                        referral_website: $('#referral_website').val(),
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            const referral = $('#referral_website').val();
                            localStorage.removeItem('otp_next_allowed_at');
                            localStorage.removeItem('otp_resend_count');
                            showToast(res.message, 'success');
                            if (res && res.token) {
                                localStorage.setItem('api_token', res.token);
                                localStorage.setItem('reference_website', referral);
                                document.cookie = 'api_token=' + res.token + '; path=/; SameSite=Strict';
                            }
                            setTimeout(function() {
                                const token = res.token ?? '';
                                const redirect = '<?= base_url('dashboard') ?>';
                                window.location.href = redirect + '?token=' + token;
                            }, 650);
                        } else {
                            showToast(
                                res.message ||
                                'Incorrect OTP. Please try again.',
                                'danger'
                            );
                        }
                    },
                    error: function() {
                        showToast(
                            'Unable to verify OTP. Please try again.',
                            'danger'
                        );
                    },
                    complete: function() {
                        $('#verifyOtpBtn')
                            .prop('disabled', false)
                            .html(
                                '<i class="fa-solid fa-unlock-keyhole me-2"></i>Verify OTP & Login'
                            );
                    }
                });
            }

            $('#sendOtpBtn').on('click', function(e) {
                e.preventDefault();
                sendOtp();
            });

            $('#resendOtpBtn').on('click', function(e) {
                e.preventDefault();
                const remaining = getRemainingSeconds();
                if (remaining > 0) {
                    showToast(
                        'Please wait ' +
                        remaining +
                        ' seconds before requesting another OTP.'
                    );
                    return false;
                }
                sendOtp();
            });

            $('#verifyOtpBtn').on('click', function(e) {
                e.preventDefault();
                verifyOtp();
            });

            $('#otp').on('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 6);
            });

            <?php if (session()->getFlashdata('fail')): ?>
                showToast("<?= session()->getFlashdata('fail') ?>", 'danger');
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                showToast("<?= session()->getFlashdata('success') ?>", 'success');
            <?php endif; ?>

        });
    </script>
</body>

</html>