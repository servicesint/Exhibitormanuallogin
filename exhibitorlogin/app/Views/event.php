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
        .loading-text,
        .error-text {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            font-size: 16px;
            color: #555;
        }
        .error-text {
            color: #b23b3b;
        }
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
    <div class="container" id="subEventsContainer">
        <div class="loading-text" id="subEventsLoading">Loading events...</div>
    </div>
    <script>
        function getEncryptedEventIdFromUrl() {
            const pathParts = window.location.pathname.split('/').filter(Boolean);
            const eventIndex = pathParts.indexOf('event');
            if (eventIndex !== -1 && pathParts[eventIndex + 1]) {
                return decodeURIComponent(pathParts[eventIndex + 1]);
            }
            return '';
        }
        const API_BASE_URL = '<?= env('API_BASE_URL') ?>';
        const ENCRYPTED_EVENT_ID = getEncryptedEventIdFromUrl();
        const DEFAULT_IMAGE = '<?= base_url('assets/images/new-default.jpg') ?>';
        const BASE_URL = '<?= rtrim(base_url(), '/') ?>';

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderSubEvents(subEvents) {
            const container = document.getElementById('subEventsContainer');
            if (!Array.isArray(subEvents) || subEvents.length === 0) {
                container.innerHTML = '<div class="error-text">No events found.</div>';
                return;
            }
            let html = '';
            subEvents.forEach(function(subEvent) {
                const logo = subEvent.sub_event_logo || DEFAULT_IMAGE;
                const loginUrl = `${BASE_URL}/login/${encodeURIComponent(subEvent.sub_event_id)}`;
                html += `
                        <div class="card">
                            <img src="${escapeHtml(logo)}" alt="Sub Event Image" onerror="this.src='${escapeHtml(DEFAULT_IMAGE)}'">
                            <h3>${escapeHtml(subEvent.sub_event_name)}</h3>
                            <a href="${escapeHtml(loginUrl)}" class="btn">
                                EXHIBITOR LOGIN
                            </a>
                        </div>
                    `;
            });
            container.innerHTML = html;
        }

        async function fetchSubEvents() {
            const container = document.getElementById('subEventsContainer');
            if (!ENCRYPTED_EVENT_ID) {
                container.innerHTML = '<div class="error-text">Invalid event link.</div>';
                return;
            }
            try {
                const response = await fetch(`${API_BASE_URL}/v1/exhibitor-login/${encodeURIComponent(ENCRYPTED_EVENT_ID)}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (!response.ok || !(result.status || result.success)) {
                    container.innerHTML = `<div class="error-text">${escapeHtml(result.message || 'Unable to load events.')}</div>`;
                    return;
                }
                if (result.data && result.data.event_name) {
                    document.title = result.data.event_name + ' - Exhibitor Login';
                }
                renderSubEvents(result.data ? result.data.sub_events : []);
            } catch (error) {
                console.error('Failed to fetch sub events:', error);
                container.innerHTML = '<div class="error-text">Network error. Please try again.</div>';
            }
        }

        document.addEventListener('DOMContentLoaded', fetchSubEvents);
    </script>
</body>
</html>