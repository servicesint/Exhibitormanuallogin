<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content') ?>
<style>
    .ref-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 28px 20px 48px;
    }

    .ref-card {
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 12px 32px rgba(21, 50, 101, 0.06);
        overflow: hidden;
        border: 1px solid #eef2f8;
    }

    .ref-card-head {
        padding: 30px 32px 24px;
        background: #fafbfd;
        border-bottom: 1px solid #eef2f8;
    }

    .ref-kicker {
        display: inline-block;
        margin-bottom: 8px;
        color: #5b7bab;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .ref-title {
        font-size: 1.7rem;
        margin: 0 0 8px;
        color: #253345;
        line-height: 1.2;
        font-weight: 700;
    }

    .ref-card-head p {
        margin: 0;
        color: #6b7891;
        max-width: 560px;
        font-size: 0.94rem;
        line-height: 1.65;
    }

    .ref-body {
        padding: 30px 32px 36px;
        display: flex;
        justify-content: center;
    }

    .ref-image-wrap {
        position: relative;
        display: inline-block;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e2e8f1;
        background: #f8f9fc;
        cursor: pointer;
        max-width: 100%;
    }

    .ref-image-wrap img {
        display: block;
        max-width: 100%;
        max-height: 520px;
        transition: opacity 0.15s ease;
    }

    .ref-image-wrap:hover img {
        opacity: 0.9;
    }

    .ref-zoom-hint {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.55);
        color: #fff;
        font-size: 0.78rem;
        padding: 4px 12px;
        border-radius: 999px;
    }

    .ref-empty,
    .ref-loading {
        padding: 60px 20px;
        text-align: center;
        color: #97a2b3;
        font-size: 0.94rem;
    }

    .ref-download-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-top: 18px;
        padding: 10px 18px;
        border-radius: 999px;
        background: #eaf0fb;
        color: #4a72b8;
        font-weight: 600;
        font-size: 0.86rem;
        text-decoration: none;
        border: 1px solid #d9e6f7;
        transition: background 0.15s ease;
    }

    .ref-download-btn:hover {
        background: #dbe6f8;
    }

    .ref-body-inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    #refImageModal .modal-content {
        border: none;
        border-radius: 18px;
        overflow: hidden;
        background: transparent;
        box-shadow: none;
    }

    #refImageModal .modal-body {
        padding: 0;
        background: transparent;
        display: flex;
        justify-content: center;
    }

    #refImageModal img {
        max-width: 100%;
        max-height: 85vh;
        border-radius: 12px;
    }

    #refImageModal .btn-close {
        position: absolute;
        top: 12px;
        right: 12px;
        background-color: #fff;
        border-radius: 50%;
        opacity: 0.9;
        padding: 8px;
        z-index: 10;
    }

    @media (max-width: 700px) {

        .ref-card-head,
        .ref-body {
            padding-left: 20px;
            padding-right: 20px;
        }
    }

    @media (max-width: 520px) {
        .ref-card-head {
            padding: 20px 16px 14px;
        }

        .ref-wrapper {
            padding: 16px 12px 28px;
        }

        .ref-body {
            padding: 20px 16px 28px;
        }
    }
</style>

<div class="content-area">
    <div class="ref-wrapper">
        <div class="ref-card">
            <div class="ref-card-head">
                <span class="ref-kicker">Design Reference</span>
                <h4 class="ref-title">Reference Image</h4>
                <p>Use this reference image as a guide while preparing your stand design submission.</p>
            </div>
            <div class="ref-body">
                <div class="ref-body-inner" id="refImageContainer">
                    <div class="ref-loading">Loading reference image...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Zoom modal -->
<div class="modal fade" id="refImageModal" tabindex="-1" aria-labelledby="refImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <img id="refImageModalImg" src="" alt="Reference image (enlarged)">
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('custom-script') ?>
<script>
    (function() {
        const API_BASE_URL = '<?= env('API_BASE_URL') ?>';
        const REFERENCE_IMAGE_URL = `${API_BASE_URL}/v1/reference-image`;
       
        function getAuthToken() {
            return localStorage.getItem('api_token') || sessionStorage.getItem('api_token') || '';
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        async function loadReferenceImage() {
            const container = document.getElementById('refImageContainer');
            const token = getAuthToken();

            if (!token) {
                container.innerHTML = '<div class="ref-empty">Login token missing. Please login again.</div>';
                return;
            }

            try {
                const response = await fetch(REFERENCE_IMAGE_URL, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();

                if (!result.status || !result.data?.image_url) {
                    container.innerHTML = `<div class="ref-empty">${escapeHtml(result.message || 'Reference image not available.')}</div>`;
                    return;
                }

                const imageUrl = result.data.image_url;

                container.innerHTML = `
                    <div class="ref-image-wrap" id="refImageWrap" data-bs-toggle="modal" data-bs-target="#refImageModal">
                        <img src="${escapeHtml(imageUrl)}" alt="Reference image">
                        <span class="ref-zoom-hint">Click to zoom</span>
                    </div>
                    <a href="${escapeHtml(imageUrl)}" class="ref-download-btn" download target="_blank" rel="noopener">
                        <i class="bi bi-download"></i> Download reference image
                    </a>
                `;

                document.getElementById('refImageWrap').addEventListener('click', function() {
                    document.getElementById('refImageModalImg').src = imageUrl;
                });
            } catch (err) {
                console.error(err);
                container.innerHTML = '<div class="ref-empty">Failed to load reference image.</div>';
            }
        }

        document.addEventListener('DOMContentLoaded', loadReferenceImage);
    })();
</script>
<?= $this->endSection() ?>