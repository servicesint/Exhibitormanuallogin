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
        :root {
            --brand-primary: #4a72b8;
            --brand-primary-dark: #3d5f9c;
            --brand-accent-bg: #eaf0fb;
            --brand-gradient-start: #4a72b8;
            --brand-gradient-end: #3d5f9c;
            --brand-sidebar-bg: #1c2b3a;
            --brand-sidebar-active-bg: #24374a;
            --brand-login-accent: rgba(74, 114, 184, 0.15);
            --brand-login-accent2: rgba(74, 114, 184, 0.10);
            --brand-btn-bg: #4a72b8;
            --brand-btn-hover: #3d5f9c;
            --brand-btn-shadow: rgba(74, 114, 184, 0.25);
            --brand-input-border: #4a72b8;
            --brand-input-shadow: rgba(74, 114, 184, 0.15);
            --brand-checkbox-bg: #4a72b8;
        }

        body.login-page {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            padding: 40px 36px;
            transition: all 0.3s ease;
        }

        .login-brand {
            display: flex;
            justify-content: center;
            margin-bottom: 24px;
        }

        .login-brand-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #fff;
            background: var(--brand-btn-bg);
            box-shadow: 0 6px 20px var(--brand-btn-shadow);
        }

        .login-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a2332;
            text-align: center;
            margin-bottom: 35px;
            letter-spacing: -0.3px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #6b7a8f;
            text-align: center;
            margin-bottom: 28px;
            line-height: 1.5;
        }

        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #4a5a6e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .login-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .login-input {
            width: 100%;
            height: 46px;
            border: 1.5px solid #dce1e8;
            border-radius: 10px;
            background: #f8f9fb;
            color: #1a2332;
            padding: 0 14px 0 42px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .login-input::placeholder {
            color: #9aa8b8;
        }

        .login-input:focus {
            border-color: var(--brand-input-border);
            box-shadow: 0 0 0 3px var(--brand-input-shadow);
            background: #ffffff;
            outline: none;
        }

        .login-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9aa8b8;
            font-size: 16px;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .login-input:focus~.login-icon {
            color: var(--brand-input-border);
        }

        .login-input-container {
            margin-bottom: 20px;
        }

        .btn-otp {
            width: 100%;
            height: 46px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            background: var(--brand-btn-bg);
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-otp:hover:not(:disabled) {
            background: var(--brand-btn-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px var(--brand-btn-shadow);
        }

        .btn-otp:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .otp-panel {
            margin-top: 20px;
            border-top: 1px solid #e8ecf1;
            padding-top: 20px;
        }

        .otp-panel .form-label {
            margin-bottom: 8px;
        }

        .otp-panel .login-input {
            padding-left: 14px;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 8px;
        }

        .otp-actions {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }

        .btn-verify {
            background: var(--brand-btn-bg);
            color: #fff;
            border: none;
            height: 44px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-verify:hover:not(:disabled) {
            background: var(--brand-btn-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px var(--brand-btn-shadow);
        }

        .btn-verify:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-resend {
            background: transparent;
            color: var(--brand-btn-bg);
            border: 1.5px solid var(--brand-btn-bg);
            height: 44px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-resend:hover:not(:disabled) {
            background: var(--brand-btn-bg);
            color: #fff;
        }

        .btn-resend:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .muted-note {
            font-size: 12px;
            color: #9aa8b8;
            text-align: center;
            margin-top: 12px;
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast-custom {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.12);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 280px;
            max-width: 420px;
            border-left: 4px solid var(--brand-btn-bg);
            animation: slideIn 0.3s ease;
        }

        .toast-custom.error {
            border-left-color: #dc3545;
        }

        .toast-custom.success {
            border-left-color: #28a745;
        }

        .toast-custom.info {
            border-left-color: #17a2b8;
        }

        .toast-custom .toast-icon {
            font-size: 18px;
            color: var(--brand-btn-bg);
        }

        .toast-custom.error .toast-icon {
            color: #dc3545;
        }

        .toast-custom.success .toast-icon {
            color: #28a745;
        }

        .toast-custom.info .toast-icon {
            color: #17a2b8;
        }

        .toast-custom .toast-body-custom {
            flex: 1;
            font-size: 14px;
            color: #1a2332;
        }

        .toast-custom .toast-close {
            background: none;
            border: none;
            font-size: 20px;
            color: #9aa8b8;
            cursor: pointer;
            padding: 0 4px;
        }

        .toast-custom .toast-close:hover {
            color: #4a5a6e;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 28px 20px;
            }
            .login-title {
                font-size: 20px;
            }
        }
    </style>
    <?= brand_style((int) $subEvent['event_id'], (int) $subEvent['sub_event_id']) ?>
</head>

<body class="login-page">

    <div id="toastContainer" class="toast-container"></div>

    <div class="login-card">
        <div class="login-brand">
            <div class="login-brand-icon">
                <i class="fa-solid fa-store"></i>
            </div>
        </div>
        <h2 class="login-title" style="margin-bottom:8px;">Login</h2>
        <p class="login-subtitle"><?= esc($subEvent['sub_event_name'] ?: $subEvent['event_name']) ?></p>

        <form id="otpLoginForm" autocomplete="off">
            <input type="hidden" id="enc_sub_event_id" name="enc_sub_event_id" value="<?= esc($enc_sub_event_id) ?>">

            <div class="login-input-container">
                <label class="form-label" for="identifier">Email / Mobile Number</label>
                <div class="login-input-wrap">
                    <input type="text" id="identifier" name="identifier" class="login-input" placeholder="Enter email or mobile number" required>
                    <i class="fa-solid fa-envelope login-icon"></i>
                </div>
            </div>

            <button type="button" id="sendOtpBtn" class="btn-otp">
                <i class="fa-solid fa-paper-plane"></i> Send OTP
            </button>

            <div class="otp-panel d-none" id="otpStep">
                <label class="form-label" for="otp">Enter 6-digit OTP</label>
                <div class="login-input-wrap">
                    <input type="text" id="otp" name="otp" class="login-input" inputmode="numeric" maxlength="6" placeholder="••••••">
                </div>

                <div class="otp-actions">
                    <button type="button" id="verifyOtpBtn" class="btn-verify">
                        <i class="fa-solid fa-unlock-keyhole"></i> Verify OTP & Login
                    </button>
                    <button type="button" id="resendOtpBtn" class="btn-resend">
                        <i class="fa-solid fa-rotate-right"></i> Resend OTP
                    </button>
                </div>
                <p class="muted-note">Use the OTP sent to your registered email or mobile number.</p>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>

        $(function() {
            let resendCount = parseInt(localStorage.getItem('otp_resend_count') || '0');
            let resendTimer = null;
            const remaining = getRemainingSeconds();
            if (remaining > 0) {
                startResendTimer();
            }

            function showToast(message, type = 'info') {
                var container = document.getElementById('toastContainer');
                var toast = document.createElement('div');
                toast.className = 'toast-custom ' + type;
                
                var iconMap = {
                    'success': 'fa-circle-check',
                    'error': 'fa-circle-xmark',
                    'info': 'fa-circle-info'
                };
                var iconClass = iconMap[type] || 'fa-circle-info';
                
                toast.innerHTML = `
                    <span class="toast-icon"><i class="fa-solid ${iconClass}"></i></span>
                    <span class="toast-body-custom">${message}</span>
                    <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
                `;
                
                container.appendChild(toast);
                
                setTimeout(function() {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 5000);
            }

            function isValidIdentifier(value) {
                var emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value);
                var mobileOk = /^\+?[0-9\s-]{7,20}$/.test(value);
                return emailOk || mobileOk;
            }

            function getRemainingSeconds() {
                var nextAllowedAt = parseInt(
                    localStorage.getItem('otp_next_allowed_at') || '0'
                );
                var remaining = Math.ceil(
                    (nextAllowedAt - Date.now()) / 1000
                );
                return remaining > 0 ? remaining : 0;
            }

            function startResendTimer() {
                resendCount++;
                localStorage.setItem('otp_resend_count', resendCount);
                var seconds = resendCount * 15;
                var nextAllowedAt = Date.now() + (seconds * 1000);
                localStorage.setItem('otp_next_allowed_at', nextAllowedAt);
                
                clearInterval(resendTimer);
                resendTimer = setInterval(function() {
                    var remaining = getRemainingSeconds();
                    if (remaining <= 0) {
                        clearInterval(resendTimer);
                        $('#resendOtpBtn')
                            .prop('disabled', false)
                            .html('<i class="fa-solid fa-rotate-right"></i> Resend OTP');
                        return;
                    }
                    $('#resendOtpBtn')
                        .prop('disabled', true)
                        .html('<i class="fa-solid fa-clock"></i> Resend in ' + remaining + 's');
                }, 1000);
            }

            function sendOtp() {
                var identifier = $.trim($('#identifier').val());
                if (!identifier) {
                    showToast('Please enter your email or mobile number', 'error');
                    $('#identifier').focus();
                    return;
                }
                if (!isValidIdentifier(identifier)) {
                    showToast('Enter a valid email address or mobile number', 'error');
                    $('#identifier').focus();
                    return;
                }
                
                $('#sendOtpBtn')
                    .prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span> Sending...');
                
                $.ajax({
                    url: '<?= env('API_BASE_URL') ?>/auth/v1/send-otp',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        identifier: identifier,
                        enc_sub_event_id: $('#enc_sub_event_id').val(),
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
                                showToast('OTP: ' + res.debug_otp, 'info');
                            }
                        } else {
                            showToast(res.message || 'Unable to send OTP right now.', 'error');
                        }
                    },
                    error: function() {
                        showToast('Unable to contact the server. Please try again.', 'error');
                    },
                    complete: function() {
                        $('#sendOtpBtn')
                            .prop('disabled', false)
                            .html('<i class="fa-solid fa-paper-plane"></i> Send OTP');
                    }
                });
            }

            function verifyOtp() {
                var otp = $.trim($('#otp').val());
                if (!/^\d{6}$/.test(otp)) {
                    showToast('Enter the 6-digit OTP received on your email or mobile number', 'error');
                    $('#otp').focus();
                    return;
                }
                
                $('#verifyOtpBtn')
                    .prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span> Verifying...');
                
                $.ajax({
                    url: '<?= env('API_BASE_URL') ?>/auth/v1/verify-otp',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        identifier: $('#identifier').val(),
                        otp: otp,
                        enc_sub_event_id: $('#enc_sub_event_id').val(),
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            localStorage.removeItem('otp_next_allowed_at');
                            localStorage.removeItem('otp_resend_count');
                            showToast(res.message, 'success');

                            if (res.token) {
                                localStorage.setItem('api_token', res.token);
                                localStorage.setItem('login_url', window.location.href);
                                document.cookie = 'api_token=' + res.token + '; path=/; SameSite=Strict';
                            }
                            
                            setTimeout(function() {
                                window.location.href = '<?= base_url('dashboard') ?>';
                            }, 650);
                        } else {
                            showToast(res.message || 'Incorrect OTP. Please try again.', 'error');
                        }
                    },
                    error: function() {
                        showToast('Unable to verify OTP. Please try again.', 'error');
                    },
                    complete: function() {
                        $('#verifyOtpBtn')
                            .prop('disabled', false)
                            .html('<i class="fa-solid fa-unlock-keyhole"></i> Verify OTP & Login');
                    }
                });
            }

            $('#sendOtpBtn').on('click', function(e) {
                e.preventDefault();
                sendOtp();
            });

            $('#resendOtpBtn').on('click', function(e) {
                e.preventDefault();
                var remaining = getRemainingSeconds();
                if (remaining > 0) {
                    showToast('Please wait ' + remaining + ' seconds before requesting another OTP.', 'error');
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

            // Enter key support
            $('#identifier').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $('#sendOtpBtn').click();
                }
            });

            $('#otp').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $('#verifyOtpBtn').click();
                }
            });

            <?php foreach (['fail' => 'error', 'success' => 'success'] as $flashKey => $toastType): ?>
                <?php if ($flash = session()->getFlashdata($flashKey)): ?>
                    showToast(<?= json_encode(esc($flash), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>, '<?= $toastType ?>');
                <?php endif; ?>
            <?php endforeach; ?>
        });
    </script>
</body>

</html>