<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content') ?>

<div class="content-area">
    <div class="row">
        <div class="col-md-12">
            <div id="pageContentArea">
                <p class="text-muted">Loading...</p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('custom-script') ?>
<script>
    const PAGE_CONTENT_URL_BASE = `${API_BASE_URL}/v1/dashboard/guidelines`;
    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function getAuthToken() {
        return localStorage.getItem('api_token') || sessionStorage.getItem('api_token') || '';
    }

    function getPageUrlFromBrowser() {
        const path = window.location.pathname;
        const segments = path.split('/').filter(Boolean);
        return segments.length ? segments[segments.length - 1] : '';
    }

    async function loadPageContent(pageUrl) {
        const contentArea = document.getElementById('pageContentArea');
        contentArea.innerHTML = '<p class="text-muted">Loading...</p>';
        const token = getAuthToken();
        if (!token) {
            contentArea.innerHTML = '<div class="alert alert-danger">Login token missing. Please login again.</div>';
            return;
        }
        try {
            const response = await fetch(`${PAGE_CONTENT_URL_BASE}/${encodeURIComponent(pageUrl)}`, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            if (!result.status) {
                contentArea.innerHTML = `<div class="alert alert-danger">${escapeHtml(result.message)}</div>`;
                return;
            }
            contentArea.innerHTML = `
                <h4>${escapeHtml(result.data.page_title)}</h4>
                <div class="page-content-body">${result.data.page_content}</div>
            `;
        } catch (err) {
            console.error(err);
            contentArea.innerHTML = '<div class="alert alert-danger">Failed to load content.</div>';
        }
    }

    window.addEventListener('popstate', function() {
        const pageUrl = getPageUrlFromBrowser();
        if (pageUrl) {
            loadPageContent(pageUrl);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const pageUrl = getPageUrlFromBrowser();
        if (pageUrl) {
            loadPageContent(pageUrl);
        } else {
            document.getElementById('pageContentArea').innerHTML =
                '<p class="text-muted">No page specified in URL.</p>';
        }
    });
</script>
<?= $this->endSection() ?>