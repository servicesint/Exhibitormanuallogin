<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exhibitor Manuals</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/icons/favicon.ico'); ?>" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.19/build/css/intlTelInput.css" />
    <link rel="stylesheet" href="<?php echo base_url('assets/css/main.css'); ?>">
    <style>
        html {
            overflow-y: scroll;
            scrollbar-gutter: stable
        }
 
        .sidebar {
            overflow-y: auto;
            scrollbar-gutter: stable
        }
 
        .online-forms-status {
            display: flex;
            gap: 20px;
            padding: 10px 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 15px;
            flex-wrap: wrap
        }
 
        .status-item {
            display: flex;
            align-items: center;
            gap: 8px
        }
 
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500
        }
 
        .status-badge.enabled-open {
            background: #d4edda;
            color: #155724
        }
 
        .status-badge.readonly {
            background: #cce5ff;
            color: #004085
        }
 
        .status-badge.hidden {
            background: #f8d7da;
            color: #721c24
        }
 
        .status-badge.fully-hidden {
            background: #e2e3e5;
            color: #383d41
        }

        .readonly-message {
            background: #cce5ff;
            border: 1px solid #004085;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: none
        }
 
        .readonly-message i {
            color: #004085;
            margin-right: 10px
        }
 
        .form-readonly {
            opacity: .7;
            pointer-events: none
        }
 
        .form-readonly .btn-primary,
        .form-readonly .btn-success,
        .form-readonly .btn-danger,
        .form-readonly .btn-warning,
        .form-readonly button[type="submit"] {
            pointer-events: none;
            opacity: .5
        }
 
        .form-readonly input:not([readonly]),
        .form-readonly select:not([disabled]),
        .form-readonly textarea:not([readonly]) {
            pointer-events: none;
            background-color: #e9ecef
        }
 
        .nav-item-hidden {
            display: none !important
        }
 
        #additionalFurnitureNavItem.nav-item-hidden,
        #exhibitorBadgesNavItem.nav-item-hidden,
        #visitorInvitationNavItem.nav-item-hidden,
        #fasciaNavItem.nav-item-hidden,
        #referenceImageNavItem.nav-item-hidden {
            display: none !important
        }
 
        .manual-list {
            list-style: none;
            padding: 0;
            margin: 0
        }
 
        .manual-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px
        }
 
        .manual-list li a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1c2b3a;
            text-decoration: none;
            font-family: 'Lora', serif;
            font-size: 1rem
        }
 
        .nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding-right: 8px
        }
 
        .nav-link .nav-text {
            flex: 1
        }
        .status-circle {
            width: 22px;
            height: 22px;
            min-width: 22px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 12px;
            margin-left: 10px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .status-circle.completed {
            background-color: #28a745 !important; /* Green */
            color: #fff;
        }

        .status-circle.completed i {
            color: #fff;
        }

        .status-circle.pending {
            background-color: #dc3545 !important; /* Red */
            color: #fff;
        }

        .status-circle.pending i {
            color: #fff;
        }

        .status-circle i {
            line-height: 1;
            font-size: 12px;
        }

        :root {
            --brand-primary: #4a72b8;
            --brand-primary-dark: #3d5f9c;
            --brand-accent-bg: #eaf0fb;
            --brand-gradient: linear-gradient(to left, #4a72b8, #3d5f9c);
            --brand-sidebar-bg: #1c2b3a;
            --brand-sidebar-active-bg: #24374a;
        }
 
        body.theme-fireindia {
            --brand-primary: #ed4037;
            --brand-primary-dark: #f47634;
            --brand-accent-bg: #F27900;
            --brand-gradient: linear-gradient(to left, #ed4037, #f47634);
        }
 
        body.theme-drone {
            --brand-primary: #105489;
            --brand-primary-dark: #1478c7;
            --brand-accent-bg: #e8eff5;
            --brand-gradient: linear-gradient(to left, #105489, #1478c7);
        }
 
        .sidebar {
            background-image: var(--brand-gradient);
        }
 
        .top-navbar {
            background-image: var(--brand-gradient);
        }
 
        .nav-link.active,
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
        }
 
        .status-circle.completed {
            background-color: var(--brand-primary);
        }

        .form-closed-message {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 70vh;
            text-align: center;
            padding: 20px;
        }

        .form-closed-message .icon {
            font-size: 48px;
            color: #dc3545;
        }

        .form-closed-message h3 {
            margin-top: 15px;
        }

        .form-closed-message .text-muted {
            color: #6c757d;
        }
        button[type="submit"]:disabled,
        input[type="submit"]:disabled,
        .btn-submit.disabled,
        .submit-btn.disabled,
        .save-btn.disabled,
        .update-btn.disabled {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }

        .form-readonly button[type="submit"],
        .form-readonly input[type="submit"],
        .form-readonly .btn-submit,
        .form-readonly .submit-btn,
        .form-readonly .save-btn,
        .form-readonly .update-btn {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
        #globalLoaderOverlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.6);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        #globalLoaderOverlay.active {
            display: flex;
        }

        #globalLoaderOverlay .global-loader-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            background: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        #globalLoaderOverlay .spinner-border {
            width: 2.5rem;
            height: 2.5rem;
        }

        #globalLoaderOverlay .global-loader-text {
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
 
<?php
$referralTheme = 'default';
 
$eventLogos = [
    'drone'     => 'https://www.droneexpo.in/mailer/FI-1730695201.webp',
    'fireindia' => 'https://www.droneexpo.in/mailer/FI-1730695201.webp',
    'default'   => 'https://www.droneexpo.in/mailer/FI-1730695201.webp',
];
$themedLogo = $eventLogos[$referralTheme] ?? $eventLogos['default'];
?>
 
<body class="theme-<?= esc($referralTheme) ?>">
    <script>
        (function() {
            function detectTheme(value) {
                var v = String(value || '').toLowerCase();
                if (v.indexOf('drone') !== -1) return 'drone';
                if (v.indexOf('fireindia') !== -1) return 'fireindia';
                return 'default';
            }
            try {
                var stored = localStorage.getItem('referral_website');
                var theme = detectTheme(stored);
                var body = document.body;
                body.classList.remove('theme-drone', 'theme-fireindia', 'theme-default');
                body.classList.add('theme-' + theme);
            } catch (e) {
            }
        })();
    </script>
    <div id="globalLoaderOverlay">
        <div class="global-loader-box">
            <span class="spinner-border text-primary" role="status" aria-hidden="true"></span>
            <span class="global-loader-text" id="globalLoaderText">Please wait…</span>
        </div>
    </div>

    <div class="wrapper">
        <div id="sidebar" class="sidebar d-flex flex-column">
            <ul class="nav flex-column" id="sidebarMenuList">
                <li class="nav-item"><a class="nav-link active" href="<?= base_url('dashboard'); ?>"><i class="bi bi-house"></i><span class="nav-text">My Account</span></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('profile'); ?>"><i class="bi bi-people"></i><span class="nav-text">Profile</span><span id="status-profile" class="status-circle pending"><i class="bi bi-x-lg"></i></span></a></li>
                <li class="nav-item" id="casualGstNavItem" style="display:none;"><a class="nav-link" href="<?= base_url('casual-gst'); ?>"><i class="bi bi-file-earmark-text"></i><span class="nav-text">Casual GST Details</span><span id="status-casual_gst" class="status-circle pending"><i class="bi bi-x-lg"></i></span></a></li>
                <li id="fasciaNavItem" style="display:none;"><a class="nav-link" href="#" id="fasciaNavLink"><i class="bi bi-card-text"></i><span class="nav-text" id="fasciaNavText">Fascias</span><span id="status-fascia" class="status-circle pending"><i class="bi bi-x-lg"></i></span></a></li>
                <li id="referenceImageNavItem" style="display:none;"><a class="nav-link" href="<?= base_url('reference-image'); ?>"><i class="bi bi-image"></i><span class="nav-text">Reference Image</span></a></li>
                <li id="additionalFurnitureNavItem" style="display:none;"><a class="nav-link" href="<?= base_url('additional-furniture'); ?>"><i class="bi bi-cart4"></i><span class="nav-text">Additional Furnitures</span><span id="status-additional_furniture" class="status-circle pending"><i class="bi bi-x-lg"></i></span></a></li>
                <li id="exhibitorBadgesNavItem" style="display:none;"><a class="nav-link" href="<?= base_url('exhibitor-badges'); ?>"><i class="bi bi-person-badge"></i><span class="nav-text">Exhibitor Badges</span><span id="status-exhibitor_badges" class="status-circle pending"><i class="bi bi-x-lg"></i></span></a></li>
                <li id="visitorInvitationNavItem" style="display:none;"><a class="nav-link" href="<?= base_url('visitor-invitation'); ?>"><i class="bi bi-person-badge"></i><span class="nav-text">Visitor Invitation</span><span id="status-visitor_ticket_requests" class="status-circle pending"><i class="bi bi-x-lg"></i></span></a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" onclick="downloadWelcomeLetter()"><i class="bi bi-box-arrow-right"></i><span class="nav-text">Download Welcome Letter</span></a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" onclick="downloadParticipationLetter()"><i class="bi bi-people"></i><span class="nav-text">Download Participation Letter</span></a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" onclick="downloadExitPermit()"><i class="bi bi-box-arrow-right"></i><span class="nav-text">Download Exit Permit</span></a></li>
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#important-manuals" role="button" aria-expanded="false">
                        <span><i class="bi bi-journals"></i><span class="nav-text">&nbsp;&nbsp;Important Information</span></span>
                        <i class="bi bi-chevron-down menu-arrow"></i>
                    </a>
                    <div class="collapse submenu" id="important-manuals">
                        <ul class="nav flex-column" id="guidelineMenuList">
                            <li><span class="nav-link text-muted small" id="guidelineMenuLoading">Loading...</span></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        <div class="main-content">
            <nav class="top-navbar">
                <button class="toggle-btn" id="toggleSidebar">☰</button>
                <div class="d-flex justify-content-center">
                    <?php
                    $display = !empty($user_access_data['company_logo']) ? 'd-block' : 'd-none';
                    $profile_pic = !empty($user_access_data['company_logo']) ? base_url('uploads/') . $user_access_data['company_logo'] : '';
                    echo '<img src="https://www.droneexpo.in/mailer/FI-1730695201.webp" width="40px" height="40px" class="' . $display . '">';
                    ?>
                    &nbsp;<h5 class="mt-2 d-md-block d-none" id="headerEventName">Services International</h5>
                </div>
                <div class="dropdown">
                    <span><strong>Hi</strong> <span id="headerUserName"></span></span>
                    <img src="https://www.freeiconspng.com/uploads/no-image-icon-0.png" class="profile-img dropdown-toggle" data-bs-toggle="dropdown" />
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" id="logoutBtn">Logout</a></li>
                    </ul>
                </div>
            </nav>
            <?= $this->renderSection('content') ?>
        </div>
    </div>
    <script>
        const API_BASE_URL = '<?= env('API_BASE_URL') ?>';
        const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
        const ENDPOINTS = {
            guidelines: `${API_BASE_URL}/v1/dashboard/guidelines`,
            fascia: `${API_BASE_URL}/v1/dashboard/fascia-menu`,
            onlineForms: `${API_BASE_URL}/v1/dashboard/online-forms-menu`,
            profile: `${API_BASE_URL}/v1/profile`,
            logout: `${API_BASE_URL}/v1/auth/logout`,
            submissionStatus: `${API_BASE_URL}/v1/dashboard/submission-status`
        };

        function getAuthToken() {
            return localStorage.getItem('api_token') || sessionStorage.getItem('api_token') || '';
        }

        function redirectToDashboard() {
            const token = getAuthToken();
            const dashboardUrl = `${BASE_URL}/dashboard`;
            if (token) {
                document.cookie = `api_token=${token}; path=/; SameSite=Strict`;
                window.location.href = `${dashboardUrl}?token=${encodeURIComponent(token)}`;
                return;
            }
            window.location.href = dashboardUrl;
        }

        function escapeHtml(value) {
            return String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        function showToast(message, type = 'success') {
            if (typeof $.toast === 'function') {
                $.toast({
                    heading: type === 'success' ? 'Success' : type === 'error' ? 'Error' : 'Info',
                    text: message,
                    position: 'bottom-right',
                    loaderBg: '#9EC600',
                    icon: type,
                    hideAfter: 5000,
                    stack: 5,
                    textAlign: 'left',
                    allowToastClose: true
                });
            } else {
                alert(message);
            }
        }
        (function() {
            let activeCount = 0;
            const overlay = document.getElementById('globalLoaderOverlay');
            const textEl = document.getElementById('globalLoaderText');

            window.showLoader = function(message) {
                activeCount++;
                if (textEl) textEl.textContent = message || 'Please wait…';
                if (overlay) overlay.classList.add('active');
            };

            window.hideLoader = function() {
                activeCount = Math.max(0, activeCount - 1);
                if (activeCount === 0 && overlay) {
                    overlay.classList.remove('active');
                }
            };
            window.forceHideLoader = function() {
                activeCount = 0;
                if (overlay) overlay.classList.remove('active');
            };
            window.fetchWithLoader = async function(url, options, message) {
                window.showLoader(message);
                try {
                    return await fetch(url, options);
                } finally {
                    window.hideLoader();
                }
            };
        })();
        (function() {
            const LOADER_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];
            const originalFetch = window.fetch.bind(window);
            window.fetch = function(input, init) {
                init = init || {};
                const method = (init.method || 'GET').toUpperCase();

                let shouldShowLoader = LOADER_METHODS.includes(method);
                if (typeof init.showLoader === 'boolean') {
                    shouldShowLoader = init.showLoader;
                }

                if (!shouldShowLoader) {
                    return originalFetch(input, init);
                }

                window.showLoader(init.loaderMessage);
                return originalFetch(input, init).finally(function() {
                    window.hideLoader();
                });
            };
        })();

        function clearAllCookies() {
            document.cookie.split(';').forEach(cookie => {
                const name = cookie.split('=')[0].trim();
                if (!name) return;
                document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
                document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=${window.location.hostname};`;
                document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.${window.location.hostname};`;
            });
        }

        function downloadPdf(url, fileName) {
            const token = getAuthToken();
            if (!token) return alert('Please log in again.');
            $.ajax({
                url: url,
                method: 'GET',
                xhrFields: {
                    responseType: 'blob'
                },
                loaderMessage: 'Preparing your download…',
                headers: {
                    Authorization: 'Bearer ' + token
                },
                success: function(data, status, xhr) {
                    const cd = xhr.getResponseHeader('Content-Disposition');
                    const name = cd?.includes('filename=') ? cd.split('filename=')[1].replace(/["']/g, '').trim() : fileName;
                    const blobUrl = URL.createObjectURL(new Blob([data], {
                        type: 'application/pdf'
                    }));
                    const a = Object.assign(document.createElement('a'), {
                        href: blobUrl,
                        download: name
                    });
                    a.click();
                    URL.revokeObjectURL(blobUrl);
                },
                error: function(xhr) {
                    if (!xhr.response) {
                        showToast('Pdf not available at this moment.', 'error');
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function() {
                        try {
                            showToast(JSON.parse(reader.result).message || 'Pdf not available at this moment.', 'error');
                        } catch {
                            showToast('Pdf not available at this moment.', 'error');
                        }
                    };
                    reader.readAsText(xhr.response);
                }
            });
        }

        function formatGuidelineTitle(rawTitle) {
            const title = escapeHtml(rawTitle);
            const words = title.split(' ');
            if (words.length > 2) {
                return words.slice(0, 2).join(' ') + '<br>' + words.slice(2).join(' ');
            }
            return title;
        }

        function guidelineUrl(pageUrl) {
            const path = String(pageUrl ?? '').replace(/^\/+/, '');
            return `${BASE_URL}/guidelines/${path}`;
        }

        function buildGuidelineMenuHtml(menus) {
            let html = '';
            menus.forEach(menu => {
                if (!menu.pages || !menu.pages.length) return;
                menu.pages.forEach(page => {
                    html += `<li><a class="nav-link" href="${escapeHtml(guidelineUrl(page.page_url))}"><i class="bi bi-card-text"></i><span class="nav-text">${formatGuidelineTitle(page.page_title)}</span></a></li>`;
                });
            });
            return html;
        }
        async function loadGuidelinesMenu() {
            const listEl = document.getElementById('guidelineMenuList');
            if (!listEl) return;
            const token = getAuthToken();
            if (!token) return;
            try {
                const response = await fetch(ENDPOINTS.guidelines, {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (!result || !result.status || !result.data?.menus?.length) {
                    listEl.innerHTML = `<li><span class="nav-link text-muted small">No guidelines available.</span></li>`;
                    return;
                }
                const html = buildGuidelineMenuHtml(result.data.menus);
                listEl.innerHTML = html || `<li><span class="nav-link text-muted small">No guidelines available.</span></li>`;
            } catch (err) {
                console.error('Failed to load guidelines menu:', err);
                listEl.innerHTML = `<li><span class="nav-link text-danger small">Failed to load guidelines.</span></li>`;
            }
        }
        async function loadFasciaCategory() {
            const token = getAuthToken();
            if (!token) return null;
            try {
                const response = await fetch(ENDPOINTS.fascia, {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (!result || !result.status) {
                    console.warn('Failed to fetch fascia category:', result?.message);
                    return null;
                }
                return result.data;
            } catch (err) {
                console.error('Failed to load fascia category:', err);
                return null;
            }
        }

        function applyFasciaNavItem(fasciaCategory) {
            const navItem = document.getElementById('fasciaNavItem');
            const navLink = document.getElementById('fasciaNavLink');
            const navText = document.getElementById('fasciaNavText');
            const referenceImageNavItem = document.getElementById('referenceImageNavItem');

            if (!navItem || !navLink || !navText) return;

            if (!isFormMenuVisible('fascia')) {
                navItem.classList.add('nav-item-hidden');
                navItem.style.display = 'none';
                if (referenceImageNavItem) {
                    referenceImageNavItem.classList.add('nav-item-hidden');
                    referenceImageNavItem.style.display = 'none';
                }
                return;
            }

            navItem.classList.remove('nav-item-hidden');
            if (referenceImageNavItem) {
                referenceImageNavItem.classList.remove('nav-item-hidden');
            }

            const category = parseInt(fasciaCategory, 10);

            if (category === 2) {
                navText.textContent = 'Upload Stall Design';
                navLink.href = `${BASE_URL}/fascia`;
                navItem.style.display = '';
            } else if (category === 1 || category === 3) {
                navText.textContent = 'Fascia';
                navLink.href = `${BASE_URL}/upload-stand-design`;
                navItem.style.display = '';
            } else {
                navItem.style.display = 'none';
            }

            if (referenceImageNavItem) {
                if (category === 2) {
                    referenceImageNavItem.style.display = 'none';
                } else {
                    referenceImageNavItem.style.display = '';
                }
            }
        }
        const FORM_LABELS = {
            fascia: 'Fascias',
            exhibitor_badges: 'Exhibitor Badges',
            invitation_tickets: 'Invitation Tickets',
            additional_furniture: 'Additional Furniture'
        };
        const ONLINE_FORMS_NAV_MAP = {
            fascia: ['fasciaNavItem', 'referenceImageNavItem'],
            exhibitor_badges: ['exhibitorBadgesNavItem'],
            invitation_tickets: ['visitorInvitationNavItem'],
            additional_furniture: ['additionalFurnitureNavItem']
        };
        const DUE_DATE_FIELD_MAP = {
            fascia: 'fascia_due_date',
            additional_furniture: 'additional_due_date'
        };

        function isDueDatePassed(formName) {
            const dueDates = window.onlineFormsDueDates || {};
            if (!dueDates[formName]) return false;
            const dueDate = new Date(dueDates[formName] + 'T23:59:59');
            return new Date() > dueDate;
        }

        function extractDueDatesFromProfile(profile) {
            const dueDates = {};
            Object.keys(DUE_DATE_FIELD_MAP).forEach(formKey => {
                const profileField = DUE_DATE_FIELD_MAP[formKey];
                if (profile && profile[profileField]) {
                    dueDates[formKey] = profile[profileField];
                }
            });
            return dueDates;
        }
        async function loadOnlineFormsConfig() {
            const token = getAuthToken();
            if (!token) return null;
            try {
                const response = await fetch(ENDPOINTS.onlineForms, {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (!result || !result.status) {
                    console.warn('Failed to fetch online forms config:', result?.message);
                    return null;
                }
                return result.data;
            } catch (err) {
                console.error('Failed to load online forms config:', err);
                return null;
            }
        }
        async function initOnlineFormsConfig() {
            const data = await loadOnlineFormsConfig();
            if (!data) return;
            window.onlineFormsEnableDisable = data.online_forms_enable_disable || null;
            window.onlineFormsOpenClose = data.online_forms_open_close || null;
            applyOnlineFormsOverride();
            updateOnlineFormsStatus();
        }

        function applyOnlineFormsOverride() {
            if (!window.onlineFormsOpenClose) return;
            Object.keys(ONLINE_FORMS_NAV_MAP).forEach(key => {
                const isVisible = isFormMenuVisible(key);
                ONLINE_FORMS_NAV_MAP[key].forEach(navId => {
                    const el = document.getElementById(navId);
                    if (!el) return;
                    if (isVisible) {
                        el.classList.remove('nav-item-hidden');
                    } else {
                        el.classList.add('nav-item-hidden');
                        el.style.display = 'none';
                    }
                    if (isVisible && navId !== 'fasciaNavItem' && navId !== 'referenceImageNavItem') {
                        el.style.display = '';
                    }
                });
            });
        }

        function updateOnlineFormsStatus() {
            const statusBar = document.getElementById('onlineFormsStatus');
            if (!statusBar) return;
            const enableDisable = window.onlineFormsEnableDisable;
            const openClose = window.onlineFormsOpenClose;
            if (!enableDisable || !openClose) {
                statusBar.style.display = 'none';
                return;
            }
            statusBar.style.display = 'flex';
            const formNames = {
                fascia: 'fasciaStatusBadge',
                exhibitor_badges: 'exhibitorBadgesStatusBadge',
                invitation_tickets: 'invitationTicketsStatusBadge',
                additional_furniture: 'additionalFurnitureStatusBadge'
            };

            Object.keys(formNames).forEach(key => {
                const badge = document.getElementById(formNames[key]);
                if (!badge) return;
                const status = getFormStatus(key);

                if (status === 'disabled') {
                    badge.textContent = 'Hidden';
                    badge.className = 'status-badge hidden';
                } else if (status === 'expired') {
                    badge.textContent = 'Closed (View Only - Due Date Passed)';
                    badge.className = 'status-badge readonly';
                } else if (status === 'enabled_open') {
                    badge.textContent = 'Open (Can Purchase)';
                    badge.className = 'status-badge enabled-open';
                } else {
                    badge.textContent = 'Disabled (Read Only)';
                    badge.className = 'status-badge readonly';
                }
            });
        }

        function getFormStatus(formName) {
            const enableDisable = window.onlineFormsEnableDisable;
            const openClose = window.onlineFormsOpenClose;
            if (!enableDisable || !openClose) {
                return isDueDatePassed(formName) ? 'expired' : 'enabled_open';
            }
            const isEnabled = parseInt(enableDisable[formName], 10) === 1;
            const isOpen = parseInt(openClose[formName], 10) === 1;
            if (!isOpen) return 'disabled';
            if (isDueDatePassed(formName)) return 'expired';
            return isEnabled ? 'enabled_open' : 'enabled_closed';
        }

        function isFormMenuVisible(formName) {
            const openClose = window.onlineFormsOpenClose;
            return !openClose || parseInt(openClose[formName], 10) === 1;
        }

        function canPurchase(formName) {
            return getFormStatus(formName) === 'enabled_open';
        }

        function isViewOnly(formName) {
            const status = getFormStatus(formName);
            return status === 'enabled_closed' || status === 'expired';
        }

        function isFormDisabled(formName) {
            return getFormStatus(formName) === 'disabled';
        }

        function applyFormAccess(formName) {
            const status = getFormStatus(formName);
            const messageDiv = document.getElementById('readonlyMessage');
            const formContainer = document.querySelector('.form-readonly-target') || document.querySelector('form');
            if (status === 'enabled_open') {
                if (messageDiv) messageDiv.style.display = 'none';
                if (formContainer) formContainer.classList.remove('form-readonly');
                return status;
            }
            if (status === 'disabled') {
                if (window.location.pathname.includes(formName.replace('_', '-')) || window.location.pathname.includes(formName)) {
                    showToast('This form is currently disabled.', 'error');
                    setTimeout(redirectToDashboard, 1500);
                }
                return status;
            }
            if (status === 'expired') {
                if (formContainer) formContainer.classList.add('form-readonly');
                if (messageDiv) {
                    messageDiv.innerHTML = `<i class="bi bi-info-circle"></i>The due date for <strong>${FORM_LABELS[formName]||formName}</strong> has passed. You can view your existing submission but can no longer make changes.`;
                    messageDiv.style.display = 'block';
                }
                return status;
            }
            if (status === 'enabled_closed') {
                if (formContainer) formContainer.classList.add('form-readonly');
                if (messageDiv) {
                    messageDiv.innerHTML = `<i class="bi bi-info-circle"></i><strong>${FORM_LABELS[formName]||formName}</strong> is disabled. You can only view your existing records.`;
                    messageDiv.style.display = 'block';
                }
            }
            return status;
        }

        function applyCurrentFormAccess() {
            const path = window.location.pathname.replace(/\/+$/, '');
            const formRoutes = {
                fascia: ['/fascia', '/upload-stand-design'],
                exhibitor_badges: ['/exhibitor-badges'],
                additional_furniture: ['/additional-furniture']
            };
            Object.entries(formRoutes).some(([formName, routes]) => {
                if (!routes.some(route => path.endsWith(route))) return false;
                applyFormAccess(formName);
                return true;
            });
        }

        async function loadHeaderProfile() {
            const token = getAuthToken();
            if (!token) return null;
            try {
                const response = await fetch(ENDPOINTS.profile, {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (!response.ok || !result.status) {
                    console.warn('Failed to fetch profile:', result?.message);
                    return null;
                }
                return result.data;
            } catch (err) {
                console.error('Failed to load profile:', err);
                return null;
            }
        }

        function applyHeaderProfile(profile) {
            const nameEl = document.getElementById('headerUserName');
            const nameEl2 = document.getElementById('headerUserNames');
            const eventNameEl = document.getElementById('headerEventName');
            if (nameEl && profile.contact_person) {
                nameEl.textContent = profile.contact_person;
            }
            if (nameEl2 && profile.contact_person) {
                nameEl2.textContent = profile.contact_person;
                nameEl2.style.display = 'inline';
            }
            if (eventNameEl && profile.event_name) {
                eventNameEl.textContent = profile.event_name;
            }
            window.onlineFormsDueDates = extractDueDatesFromProfile(profile);
            applyOnlineFormsOverride();
            updateOnlineFormsStatus();
        }

        function applyBridalAsiaMenu(profile) {
            const navItem = document.getElementById('casualGstNavItem');
            if (!navItem) return;
            const eventName = String(profile?.event_name || '').trim().toLowerCase();
            navItem.style.display = (eventName === 'bridal asia') ? '' : 'none';
        }

        function isVisitorInvitationAllowed(profile) {
            const eventName = String(profile?.event_name || '').trim();
            const exhibitorType = String(profile?.exhibitor_type || '').trim().toLowerCase();
            const alwaysHiddenEvents = ['Drone Expo'];
            const internationalRestrictedEvents = ['Fire India', 'Drone Expo', 'Secure Nation'];
            const isAlwaysHidden = alwaysHiddenEvents.includes(eventName);
            const isInternationalRestricted = internationalRestrictedEvents.includes(eventName) && exhibitorType === 'international';
            return !(isAlwaysHidden || isInternationalRestricted);
        }

        function applyVisitorInvitationVisibility(profile) {
            const navItem = document.getElementById('visitorInvitationNavItem');
            if (!navItem) return;
            const eventName = String(profile?.event_name || '').trim();
            const exhibitorType = String(profile?.exhibitor_type || '').trim().toLowerCase();
            const alwaysHiddenEvents = ['Drone Expo'];
            const internationalRestrictedEvents = ['Fire India', 'Drone Expo', 'Secure Nation'];
            const isAlwaysHidden = alwaysHiddenEvents.includes(eventName);
            const isInternationalRestricted = internationalRestrictedEvents.includes(eventName) && exhibitorType === 'international';

            if (isAlwaysHidden || isInternationalRestricted || !isFormMenuVisible('invitation_tickets')) {
                navItem.classList.add('nav-item-hidden');
                navItem.style.display = 'none';
            } else {
                navItem.classList.remove('nav-item-hidden');
                navItem.style.display = '';
            }
        }

        function guardVisitorInvitationPage(profile) {
            const onVisitorInvitationPage = window.location.pathname.replace(/\/+$/, '').endsWith('visitor-invitation');
            if (!onVisitorInvitationPage) return;
            if (!isVisitorInvitationAllowed(profile)) {
                const main = document.querySelector('.main-content');
                if (main) {
                    main.innerHTML = `<div style="display:flex;align-items:center;justify-content:center;height:70vh;text-align:center;padding:20px;"><div><i class="bi bi-shield-lock" style="font-size:48px;color:#dc3545;"></i><h3 style="margin-top:15px;">You are not authorised to access this page.</h3><p class="text-muted">Redirecting you to the dashboard...</p></div></div>`;
                }
                setTimeout(redirectToDashboard, 2000);
                return;
            }
            if (isFormDisabled('invitation_tickets')) {
                const main = document.querySelector('.main-content');
                if (main) {
                    main.innerHTML = `<div style="display:flex;align-items:center;justify-content:center;height:70vh;text-align:center;padding:20px;"><div><i class="bi bi-shield-lock" style="font-size:48px;color:#dc3545;"></i><h3 style="margin-top:15px;">This section is currently disabled.</h3><p class="text-muted">Redirecting you to the dashboard...</p></div></div>`;
                }
                setTimeout(redirectToDashboard, 2000);
            }
        }

        async function loadSubmissionStatus() {
            const token = getAuthToken();
            if (!token) return;
            try {
                const response = await fetch(ENDPOINTS.submissionStatus, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (!result.status) return;
                Object.keys(result.data).forEach(key => {
                    const el = document.getElementById(`status-${key}`);
                    if (!el) return;
                    const submitted = result.data[key];
                    el.className = 'status-circle ' + (submitted ? 'completed' : 'pending');
                    el.innerHTML = submitted ? '<i class="bi bi-check-lg"></i>' : '<i class="bi bi-x-lg"></i>';
                });
            } catch (err) {
                console.error('Failed to load submission status:', err);
            }
        }

        async function handleLogout(e) {
            e.preventDefault();
            const token = getAuthToken();
            const referralWebsite = localStorage.getItem('referral_website');
            const logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.classList.add('disabled');
                logoutBtn.textContent = 'Logging out...';
            }
            try {
                if (token) {
                    await fetch(ENDPOINTS.logout, {
                        method: 'POST',
                        loaderMessage: 'Logging out…',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    });
                }
            } catch (err) {
                console.error('Logout request failed:', err);
            } finally {
                localStorage.clear();
                sessionStorage.clear();
                clearAllCookies();
                window.location.href = referralWebsite;
            }
        }

        const downloadParticipationLetter = () => downloadPdf(`${API_BASE_URL}/v1/exhibitor/participation-letter/pdf`, 'participation-letter.pdf');
        const downloadWelcomeLetter = () => downloadPdf(`${API_BASE_URL}/v1/exhibitor/welcome-letter/pdf`, 'welcome-letter.pdf');
        const downloadExitPermit = () => downloadPdf(`${API_BASE_URL}/v1/exhibitor/exit-permit/pdf`, 'exit-permit.pdf');

        document.addEventListener('DOMContentLoaded', async () => {
            try {
                loadGuidelinesMenu();
                await initOnlineFormsConfig();
                const fasciaData = await loadFasciaCategory();
                if (fasciaData) {
                    window.fasciaCategory = fasciaData.fascia_category;
                    window.fasciaOptions = fasciaData.fascia_options;
                    applyFasciaNavItem(fasciaData.fascia_category);
                }
                const profile = await loadHeaderProfile();
                if (profile) {
                    window.profileData = profile;
                    applyHeaderProfile(profile);
                    applyBridalAsiaMenu(profile);
                    applyVisitorInvitationVisibility(profile);
                    guardVisitorInvitationPage(profile);
                }
                applyCurrentFormAccess();
                await loadSubmissionStatus();
                const logoutBtn = document.getElementById('logoutBtn');
                if (logoutBtn) {
                    logoutBtn.addEventListener('click', handleLogout);
                }
                applyOnlineFormsOverride();
                if (profile) applyVisitorInvitationVisibility(profile);

                window.__layoutConfigReady = true;
                document.dispatchEvent(new CustomEvent('layoutConfigReady'));
            } catch (error) {
                console.error('Initialization error:', error);
            }
        });

        window.getFormStatus = getFormStatus;
        window.canPurchase = canPurchase;
        window.isViewOnly = isViewOnly;
        window.isFormDisabled = isFormDisabled;
        window.applyFormAccess = applyFormAccess;
        window.applyOnlineFormsOverride = applyOnlineFormsOverride;
        window.isDueDatePassed = isDueDatePassed;
        window.showToast = showToast;
        window.BASE_URL = BASE_URL;
        window.redirectToDashboard = redirectToDashboard;
        window.loadSubmissionStatus = loadSubmissionStatus;
        window.isVisitorInvitationAllowed = isVisitorInvitationAllowed;
        window.guardVisitorInvitationPage = guardVisitorInvitationPage;
        window.downloadParticipationLetter = downloadParticipationLetter;
        window.downloadWelcomeLetter = downloadWelcomeLetter;
        window.downloadExitPermit = downloadExitPermit;

        function isPopupBlocked(popup) {
            return !popup || popup.closed || typeof popup.closed === 'undefined';
        }

        function openPopupSafely(url, target = '_blank', features = '') {
            const popup = window.open(url, target, features);
            if (isPopupBlocked(popup)) {
                return null;
            }
            setTimeout(() => {
                if (popup.closed) {
                    console.warn('Popup was blocked or closed immediately');
                }
            }, 300);
            return popup;
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        (function() {
            const LOADER_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];
            jQuery(document).ajaxSend(function(event, jqXHR, settings) {
                const method = (settings.type || settings.method || 'GET').toUpperCase();
                const isBlobDownload = settings.xhrFields && settings.xhrFields.responseType === 'blob';
                const shouldShowLoader = settings.showLoader !== undefined
                    ? settings.showLoader
                    : (LOADER_METHODS.includes(method) || isBlobDownload);

                settings._loaderShown = shouldShowLoader;
                if (shouldShowLoader && window.showLoader) {
                    window.showLoader(settings.loaderMessage);
                }
            });
            jQuery(document).ajaxComplete(function(event, jqXHR, settings) {
                if (settings._loaderShown && window.hideLoader) {
                    window.hideLoader();
                }
            });
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.19/build/js/intlTelInput.min.js"></script>
    <script src="<?php echo base_url('assets/js/tinymce/tinymce.min.js'); ?>"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.24/dist/sweetalert2.all.min.js"></script>
    <?= view('layout/script-footer') ?>
    <?= $this->renderSection('custom-script') ?>
</body>

</html>