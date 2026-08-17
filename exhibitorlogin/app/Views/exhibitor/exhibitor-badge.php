<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content') ?>

<!-- Readonly Message for View Only Mode -->
<div id="readonlyMessage" class="readonly-message" style="display:none;">
    <i class="bi bi-info-circle"></i>
    <span>Exhibitor Badges form is currently closed. You can only view your existing badges. New badge requests are not allowed.</span>
</div>

<style>
    .content-area {
        max-width: 100% !important;
        margin: 0;
        padding: 28px 20px 48px;
    }

    .badge-wrapper {
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 14px 36px rgba(21, 50, 101, 0.10);
        border: 1px solid #dfe6f2;
        overflow: hidden;
        width: 100%;
    }

    .badge-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding: 28px 32px 22px;
        background: linear-gradient(135deg, #eef3fc 0%, #fafbfd 100%);
        border-bottom: 1px solid #e2e8f1;
    }

    .badge-header h4 {
        font-size: 1.55rem;
        margin: 0 0 6px;
        color: #253345;
        line-height: 1.2;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .badge-header small {
        color: #6b7891;
        font-size: 0.86rem;
    }

    .badge-header>div:last-child {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .badge-date {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #d9e6f7;
        color: #4a72b8;
        font-weight: 700;
        font-size: 0.84rem;
        white-space: nowrap;
    }

    .badge-add-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 11px 22px;
        font-size: 0.9rem;
        font-weight: 700;
        background: #4a72b8;
        border: none;
        color: #fff;
        cursor: pointer;
        transition: background 0.15s ease, transform 0.15s ease;
        white-space: nowrap;
    }

    .badge-add-btn:hover {
        background: #3d5f9c;
        transform: translateY(-1px);
        color: #fff;
    }

    .badge-add-btn.disabled-btn {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
        transform: none;
    }

    .badge-alert {
        margin: 20px 32px 0;
        padding: 14px 18px;
        border-radius: 13px;
        background: #fdf3e0;
        border: 1px solid #f3dfae;
        color: #8a5f0f;
        font-size: 0.85rem;
        line-height: 1.6;
    }

    .badge-alert i {
        margin-right: 8px;
    }

    .table-responsive {
        padding: 24px 32px 32px;
        overflow-x: auto;
        width: 100%;
    }

    #userTable {
        width: 100% !important;
        border-collapse: collapse;
        font-size: 0.86rem;
        border: none !important;
    }

    #userTable thead th {
        text-align: left;
        padding: 13px 20px;
        background: #f8f9fc;
        color: #5c6b81;
        font-weight: 600;
        border: none;
        border-bottom: 1px solid #eef2f8;
        white-space: nowrap;
    }

    #userTable tbody td {
        padding: 13px 20px;
        border: none;
        border-bottom: 1px solid #f1f4f9;
        color: #2b3a4f;
        vertical-align: middle;
    }

    #userTable tbody tr:last-child td {
        border-bottom: none;
    }

    #userTable tbody tr:hover td {
        background: #fafbfe;
    }

    .user-img {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        background: #eef1f6;
        border: 1px solid #e2e8f1;
    }

    .action-btns {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .action-btns .btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        border: none;
        padding: 0;
        font-size: 0.92rem;
        transition: background 0.15s ease, transform 0.15s ease;
    }

    .btn-edit {
        background: #eaf0fb;
        color: #4a72b8;
    }

    .btn-edit:hover {
        background: #dbe6f8;
        color: #3d5f9c;
    }

    .btn-delete {
        background: #fdecec;
        color: #c4574f;
    }

    .btn-delete:hover {
        background: #fbdcdb;
        color: #a8433c;
    }

    .btn-download {
        background: #e6f4ea;
        color: #2f7a42;
    }

    .btn-download:hover {
        background: #d4ecdb;
        color: #266536;
    }

    .action-btns .btn.disabled-btn {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .readonly-message {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 20px;
        display: none;
    }

    .readonly-message i {
        color: #856404;
        margin-right: 10px;
    }

    .form-readonly {
        opacity: 0.7;
        pointer-events: none;
    }

    .form-readonly .btn-primary,
    .form-readonly .btn-success,
    .form-readonly .btn-danger,
    .form-readonly .btn-warning,
    .form-readonly button[type="submit"] {
        pointer-events: none;
        opacity: 0.5;
    }

    .form-readonly input:not([readonly]),
    .form-readonly select:not([disabled]),
    .form-readonly textarea:not([readonly]) {
        pointer-events: none;
        background-color: #e9ecef;
    }

    .dataTables_wrapper {
        padding: 0;
        width: 100% !important;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 15px;
    }

    .dataTables_length select,
    .dataTables_filter input {
        border: 1px solid #e2e8f1;
        border-radius: 9px;
        background: #f8f9fc;
        padding: 6px 10px;
        font-size: 0.85rem;
        color: #2b3a4f;
    }

    .dataTables_filter input:focus,
    .dataTables_length select:focus {
        outline: none;
        border-color: #93b4e8;
        background: #fff;
    }

    .dataTables_info {
        color: #8792a3;
        font-size: 0.82rem;
    }

    .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
        margin-left: 4px;
        border: 1px solid #e2e8f1 !important;
        color: #4a72b8 !important;
        background: #fff !important;
    }

    .dataTables_paginate .paginate_button.current {
        background: #4a72b8 !important;
        border-color: #4a72b8 !important;
        color: #fff !important;
    }

    .dataTables_paginate .paginate_button.disabled {
        color: #c3cad6 !important;
    }

    #badgeModal .modal-content,
    #deleteModal .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(20, 40, 80, 0.2);
    }

    #badgeModal .modal-header {
        background: #fafbfd;
        border-bottom: 1px solid #eef2f8;
        padding: 20px 26px;
    }

    #badgeModal .modal-header h5 {
        font-weight: 700;
        color: #253345;
        font-size: 1.15rem;
        margin: 0;
    }

    #badgeModal .modal-body {
        padding: 26px;
    }

    #badgeModal label {
        display: block;
        margin-bottom: 7px;
        color: #3c4a5e;
        font-weight: 600;
        font-size: 0.88rem;
    }

    #badgeModal .form-control {
        border: 1px solid #e2e8f1;
        border-radius: 11px;
        background: #f8f9fc;
        padding: 11px 15px;
        font-size: 0.9rem;
        color: #2b3a4f;
        transition: border-color 0.15s ease, background 0.15s ease;
    }

    #badgeModal .form-control:focus {
        outline: none;
        box-shadow: none;
        border-color: #93b4e8;
        background: #fff;
    }

    #badgeModal .form-control.is-invalid {
        border-color: #e6a3a3;
        background: #fdf6f6;
    }

    #badgeModal .form-text {
        color: #8792a3;
        font-size: 0.8rem;
        margin-top: 6px;
    }

    #badgeModal .btn-primary {
        border-radius: 999px;
        padding: 11px 24px;
        font-size: 0.9rem;
        font-weight: 700;
        background: #4a72b8;
        border: none;
    }

    #badgeModal .btn-primary:hover {
        background: #3d5f9c;
    }

    #badgeModal .btn-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    #deleteModal .modal-content {
        padding: 6px;
    }

    #deleteModal h5 {
        color: #253345;
        font-weight: 700;
        font-size: 1.05rem;
    }

    #deleteModal .btn-danger {
        border-radius: 999px;
        padding: 9px 20px;
        font-weight: 700;
        background: #c4574f;
        border: none;
    }

    #deleteModal .btn-danger:hover {
        background: #a8433c;
    }

    #deleteModal .btn-secondary {
        border-radius: 999px;
        padding: 9px 20px;
        font-weight: 700;
        background: #eef1f6;
        border: none;
        color: #4a5568;
    }

    #deleteModal .btn-secondary:hover {
        background: #e2e6ee;
    }

    @media (max-width: 700px) {
        .badge-header {
            flex-direction: column;
            align-items: stretch;
        }

        .badge-header>div:last-child {
            justify-content: space-between;
        }

        .badge-add-btn {
            justify-content: center;
        }

        .badge-alert {
            margin: 16px 20px 0;
        }

        .table-responsive {
            padding: 16px;
        }

        #userTable thead th,
        #userTable tbody td {
            padding: 10px 12px;
            font-size: 0.8rem;
        }

        .action-btns .btn {
            width: 28px;
            height: 28px;
            font-size: 0.78rem;
        }
    }
</style>

<div class="content-area">
    <div class="badge-wrapper">
        <div class="badge-header">
            <div>
                <h4>EXHIBITOR BADGES</h4>
                <small>Manage Exhibitor Records</small>
            </div>
            <div>
                <span class="badge-date"><i class="bi bi-calendar-event"></i> Due: &nbsp;<span id="exhibitorbadgeduedate">--</span></span>
                <span class="badge-date" id="badgeCountDisplay" style="display:none;">
                    <i class="bi bi-people"></i> <span id="badgeCountText">--</span>
                </span>
                <button type="button" id="addBadgeBtn" class="btn badge-add-btn ms-3">
                    <i class="bi bi-plus-lg"></i> Add Exhibitor
                </button>
            </div>
        </div>

        <div class="badge-alert mt-3" id="badgesNoteContainer">
            <span id="badgesNoteText">Loading note...</span>
        </div>

        <div class="table-responsive">
            <table id="userTable" class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="badgeTableBody">
                    <tr>
                        <td colspan="5" class="text-center">Loading badges...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Add/Edit -->
<div class="modal fade" id="badgeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitle">Add Exhibitor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="badgeForm"
                    action="<?= base_url('api/exhibitor-badge') ?>"
                    method="post"
                    enctype="multipart/form-data"
                    novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name">First Name</label>
                            <input type="text" name="fname" id="first_name" class="form-control" minlength="2" maxlength="50" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name">Last Name</label>
                            <input type="text" name="lname" id="last_name" class="form-control" maxlength="50">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" maxlength="255">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="whatsapp">Mobile Number</label>
                            <input type="text" name="mobile" id="whatsapp" class="form-control" maxlength="15" pattern="\d*">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="photo">Badge Image</label>
                            <input type="file" name="photo" id="photo" class="form-control" accept="image/jpeg,image/png">
                            <div class="form-text">
                                Allowed: jpg, jpeg, png. Max size: 1MB. Min width: 300px and Max width is 1000px.
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" id="badgeSubmitBtn" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080"></div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content text-center p-3">
            <h5>Delete this record?</h5>
            <div class="mt-3 d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('custom-script') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

<script>
    (function() {
        'use strict';

        if (window.__EXHIBITOR_BADGE_SCRIPT_LOADED__) {
            return;
        }
        window.__EXHIBITOR_BADGE_SCRIPT_LOADED__ = true;
        const DEBUG_DUE_DATE = true;
        const UPLOAD_BASE_URL = '<?= rtrim(env('UPLOAD_BASE_URL'), '/') ?>';
        const BADGE_API_BASE_URL = '<?= env('API_BASE_URL') ?>';
        const BADGES_URL = '<?= env('API_BASE_URL') ?>/v1/exhibitor-badges';
        const PROFILE_URL = '<?= env('API_BASE_URL') ?>/v1/profile';
        let deleteRecordId = null;
        let isViewOnly = false;

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            if (!container || typeof bootstrap === 'undefined') {
                alert(message);
                return;
            }
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-bg-${type} border-0`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            container.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl, {
                delay: 4000
            });
            toast.show();
            toastEl.addEventListener('hidden.bs.toast', function() {
                toastEl.remove();
            });
        }

        function getBadgeModal() {
            const badgeModalEl = document.getElementById('badgeModal');
            return bootstrap.Modal.getOrCreateInstance(badgeModalEl);
        }

        function resetBadgeForm() {
            const form = document.getElementById('badgeForm');
            if (!form) return;
            form.reset();
            form.querySelectorAll('.is-invalid').forEach(function(el) {
                el.classList.remove('is-invalid');
            });
            const photo = document.getElementById('photo');
            if (photo) photo.required = true;
        }

        function openAdd() {
            if (isViewOnly) {
                showToast('Cannot add new badges. Form is closed.', 'warning');
                return;
            }
            document.getElementById('modalTitle').innerText = 'Add Exhibitor';
            resetBadgeForm();
            const form = document.getElementById('badgeForm');
            form.removeAttribute('data-edit-id');
            const photo = document.getElementById('photo');
            if (photo) photo.required = true;
            getBadgeModal().show();
        }

        function openEdit(data) {
            if (isViewOnly) {
                showToast('Cannot edit badges. Form is closed.', 'warning');
                return;
            }
            document.getElementById('modalTitle').innerText = 'Edit Exhibitor';
            resetBadgeForm();
            const form = document.getElementById('badgeForm');
            if (data && data.id) {
                form.setAttribute('data-edit-id', data.id);
            }
            document.getElementById('first_name').value = data.fname || '';
            document.getElementById('last_name').value = data.lname || '';
            document.getElementById('email').value = data.email || '';
            document.getElementById('whatsapp').value = data.mobile || '';
            const photo = document.getElementById('photo');
            if (photo) photo.required = false;
            getBadgeModal().show();
        }

        function openDelete(id) {
            deleteRecordId = id;
            const deleteModalEl = document.getElementById('deleteModal');
            bootstrap.Modal.getOrCreateInstance(deleteModalEl).show();
        }

        function validateBadgeForm() {
            const form = document.getElementById('badgeForm');
            const firstName = document.getElementById('first_name');
            const email = document.getElementById('email');
            const whatsapp = document.getElementById('whatsapp');
            const photo = document.getElementById('photo');
            const minNameLength = 2;
            const maxNameLength = 50;
            const minPhoneLength = 10;
            const maxPhoneLength = 12;
            const maxFileSize = 1048576;
            const allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];
            firstName.classList.remove('is-invalid');
            email.classList.remove('is-invalid');
            whatsapp.classList.remove('is-invalid');
            photo.classList.remove('is-invalid');
            const firstNameValue = firstName.value.trim();
            const emailValue = email.value.trim();
            const phoneValue = whatsapp.value.trim();
            const file = photo.files[0];
            const isEdit = form.hasAttribute('data-edit-id');
            if (!firstNameValue || firstNameValue.length < minNameLength) {
                firstName.classList.add('is-invalid');
                showToast('First Name is required and must be at least 2 characters.', 'danger');
                firstName.focus();
                return false;
            }
            if (firstNameValue.length > maxNameLength) {
                firstName.classList.add('is-invalid');
                showToast('First Name cannot exceed 50 characters.', 'danger');
                firstName.focus();
                return false;
            }
            if (!emailValue && !phoneValue) {
                email.classList.add('is-invalid');
                whatsapp.classList.add('is-invalid');
                showToast('Please provide either an email or WhatsApp number.', 'danger');
                email.focus();
                return false;
            }
            if (emailValue && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
                email.classList.add('is-invalid');
                showToast('Please enter a valid email address.', 'danger');
                email.focus();
                return false;
            }
            if (emailValue && emailValue.length > 255) {
                email.classList.add('is-invalid');
                showToast('Email cannot exceed 255 characters.', 'danger');
                email.focus();
                return false;
            }
            if (phoneValue) {
                if (!/^\d+$/.test(phoneValue)) {
                    whatsapp.classList.add('is-invalid');
                    showToast('Whatsapp number must contain only digits.', 'danger');
                    whatsapp.focus();
                    return false;
                }
                if (phoneValue.length < minPhoneLength || phoneValue.length > maxPhoneLength) {
                    whatsapp.classList.add('is-invalid');
                    showToast('Whatsapp number must be between 10 and 12 digits.', 'danger');
                    whatsapp.focus();
                    return false;
                }
            }
            if (!isEdit && !file) {
                photo.classList.add('is-invalid');
                showToast('Please upload a photo for the badge.', 'danger');
                photo.focus();
                return false;
            }
            if (file) {
                if (!allowedTypes.includes(file.type)) {
                    photo.classList.add('is-invalid');
                    showToast('Photo must be a JPG or PNG image.', 'danger');
                    photo.focus();
                    return false;
                }
                if (file.size > maxFileSize) {
                    photo.classList.add('is-invalid');
                    showToast('Photo must be smaller than 1 MB.', 'danger');
                    photo.focus();
                    return false;
                }
            }
            return true;
        }
        const MIN_IMAGE_WIDTH = 300;
        const MAX_IMAGE_WIDTH = 1000;

        function validateImageDimensions(file) {
            return new Promise((resolve) => {
                const img = new Image();
                const objectUrl = URL.createObjectURL(file);
                img.onload = function() {
                    URL.revokeObjectURL(objectUrl);
                    resolve({
                        width: img.naturalWidth,
                        height: img.naturalHeight
                    });
                };
                img.onerror = function() {
                    URL.revokeObjectURL(objectUrl);
                    resolve(null);
                };
                img.src = objectUrl;
            });
        }

        document.addEventListener('change', async function(e) {
            if (e.target.id === 'photo') {
                const photo = e.target;
                const file = photo.files[0];
                if (!file) return;
                const dimensions = await validateImageDimensions(file);
                if (!dimensions) {
                    photo.classList.add('is-invalid');
                    showToast('Unable to read the selected image. Please choose a valid image file.', 'danger');
                    photo.value = '';
                    return;
                }
                if (dimensions.width < MIN_IMAGE_WIDTH) {
                    photo.classList.add('is-invalid');
                    showToast(`Photo width must be at least ${MIN_IMAGE_WIDTH}px. Selected image is ${dimensions.width}px wide.`, 'danger');
                    photo.value = '';
                    return;
                }
                if (dimensions.width > MAX_IMAGE_WIDTH) {
                    photo.classList.add('is-invalid');
                    showToast(`Photo width must not exceed ${MAX_IMAGE_WIDTH}px. Selected image is ${dimensions.width}px wide.`, 'danger');
                    photo.value = '';
                    return;
                }
                photo.classList.remove('is-invalid');
            }
        });

        function getAuthToken() {
            return localStorage.getItem('api_token') || sessionStorage.getItem('api_token') || '';
        }

        async function apiCall(url, {
            method = 'GET',
            body = null,
            contentType = 'application/x-www-form-urlencoded'
        } = {}) {
            const token = getAuthToken();
            if (!token) {
                console.error('api_token missing');
                showToast('Login token missing. Please login again.', 'danger');
                return null;
            }
            const headers = {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };
            const options = {
                method,
                headers
            };
            if (body) {
                if (body instanceof FormData) {
                    options.body = body;
                } else if (contentType === 'application/json') {
                    headers['Content-Type'] = 'application/json';
                    options.body = JSON.stringify(body);
                } else {
                    headers['Content-Type'] = 'application/x-www-form-urlencoded';
                    options.body = new URLSearchParams(body);
                }
            }
            let response;
            try {
                response = await fetch(url, options);
            } catch (err) {
                console.error(err);
                showToast('Network error. Please try again.', 'danger');
                return null;
            }
            if (response.status === 401) {
                showToast('Session expired. Please login again.', 'danger');
                return null;
            }
            let result = null;
            try {
                result = await response.json();
            } catch (err) {
                console.error(err);
                showToast('Unexpected server response.', 'danger');
                return null;
            }
            if (!response.ok) {
                showToast(result.message || 'Something went wrong.', 'danger');
                return null;
            }
            return result;
        }

        async function submitBadgeForm(event) {
            event.preventDefault();
            event.stopPropagation();

            if (isViewOnly) {
                showToast('Cannot submit. Form is closed.', 'warning');
                return;
            }

            const form = event.currentTarget;
            const submitBtn = document.getElementById('badgeSubmitBtn');
            const originalLabel = submitBtn.innerHTML;

            if (!validateBadgeForm()) return;

            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Saving...';

            const formData = new FormData(form);
            const isEdit = form.hasAttribute('data-edit-id');
            const editId = form.getAttribute('data-edit-id');
            let actionUrl;
            if (isEdit && editId) {
                actionUrl = `${BADGE_API_BASE_URL}/v1/exhibitor-update/${encodeURIComponent(editId)}/update`;
            } else {
                actionUrl = `${BADGE_API_BASE_URL}/v1/exhibitor-badges`;
            }

            try {
                const response = await apiCall(actionUrl, {
                    method: 'POST',
                    body: formData,
                    contentType: 'multipart/form-data'
                });
                if (!response) return;
                const message = response.message || ((response.status || response.success) ? (isEdit ? 'Badge updated successfully.' : 'Badge submitted successfully.') : 'Submission failed.');
                if (response.status || response.success) {
                    showToast(message, 'success');
                    form.reset();
                    form.removeAttribute('data-edit-id');
                    getBadgeModal().hide();
                    window.location.reload();
                } else {
                    showToast(message, 'danger');
                }
            } catch (error) {
                console.error(error);
                showToast(error.message || 'Unable to submit badge. Please try again.', 'danger');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalLabel;
            }
        }

        async function confirmDelete() {
            if (!deleteRecordId) {
                showToast('Invalid record selected.', 'danger');
                return;
            }
            const actionUrl = `${BADGE_API_BASE_URL}/v1/exhibitor-badges-delete/${encodeURIComponent(deleteRecordId)}/delete`;
            const result = await apiCall(actionUrl, {
                method: 'POST',
                body: {
                    _method: 'DELETE'
                },
                contentType: 'application/json'
            });
            if (!result) return;
            if (result.success || result.status === true) {
                showToast(result.message || 'Badge deleted successfully.', 'success');
                const deleteModalEl = document.getElementById('deleteModal');
                bootstrap.Modal.getOrCreateInstance(deleteModalEl).hide();
                deleteRecordId = null;
                fetchBadges();
            } else {
                showToast(result.message || 'Unable to delete badge.', 'danger');
            }
        }

        async function downloadBadgePdf(encryptedId, displayName) {
            const token = getAuthToken();
            if (!token) {
                showToast('Login token missing. Please login again.', 'danger');
                return;
            }
            if (!encryptedId) {
                showToast('Invalid badge reference.', 'danger');
                return;
            }
            const url = `${BADGE_API_BASE_URL}/v1/exhibitor-badges/${encodeURIComponent(encryptedId)}/download`;

            if (window.showLoader) window.showLoader('Preparing badge download…');

            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/pdf',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (response.status === 401) {
                    showToast('Session expired. Please login again.', 'danger');
                    return;
                }
                if (!response.ok) {
                    let message = 'Unable to download badge.';
                    try {
                        const errJson = await response.json();
                        message = errJson.message || message;
                    } catch (e) {}
                    showToast(message, 'danger');
                    return;
                }
                const blob = await response.blob();
                const blobUrl = window.URL.createObjectURL(blob);
                const safeName = (displayName || 'exhibitor').trim().replace(/\s+/g, '_');
                const a = document.createElement('a');
                a.href = blobUrl;
                a.download = `Badge-${safeName}.pdf`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                setTimeout(() => window.URL.revokeObjectURL(blobUrl), 1000);
            } catch (error) {
                console.error(error);
                showToast('Network error while downloading badge.', 'danger');
            } finally {
                if (window.hideLoader) window.hideLoader();
            }
        }

        async function fetchBadges() {
            const token = getAuthToken();
            if (!token) {
                showToast('Login token missing. Please login again.', 'danger');
                return;
            }
            try {
                const response = await fetch(BADGES_URL, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const result = await response.json();

                if (DEBUG_DUE_DATE) {
                    console.log('[DEBUG] Badges API result:', result);
                }

                if (response.ok && (result.status || result.success)) {
                    renderBadges(result.data || []);
                    const manualSetup = result.manual_setup || (result.data && result.data.manual_setup) || null;
                    if (manualSetup) {
                        handleManualSetupData(manualSetup);
                    }
                    // --- NEW ---
                    if (result.badge_summary) {
                        renderBadgeCount(result.badge_summary);
                    }
                    // --- END NEW ---
                } else {
                    showToast(result.message || 'Unable to fetch badges.', 'danger');
                    renderBadges([]);
                }
            } catch (error) {
                console.error(error);
                showToast('Network error while fetching badges.', 'danger');
                renderBadges([]);
            }
        }

        // --- NEW ---
        function renderBadgeCount(summary) {
            const display = document.getElementById('badgeCountDisplay');
            const text = document.getElementById('badgeCountText');
            if (!display || !text) return;

            const created = summary.badges_created ?? 0;

            if (summary.is_unlimited) {
                text.innerText = `${created} created`;
            } else {
                const limit = summary.badge_limit ?? 0;
                const left = summary.badges_left ?? Math.max(0, limit - created);
                text.innerText = `${created} of ${limit} created (${left} left)`;
            }
            display.style.display = 'inline-flex';

            // Disable "Add Exhibitor" once the limit is reached (unless already view-only/disabled)
            const addBtn = document.getElementById('addBadgeBtn');
            if (!summary.is_unlimited && (summary.badges_left ?? 0) <= 0 && addBtn) {
                addBtn.classList.add('disabled-btn');
                addBtn.title = 'Badge limit reached';
            }
        }

        // Fetches the actual due date from /v1/profile (not from the badges list endpoint)
        async function fetchExhibitorBadgeAccess() {
            const token = getAuthToken();
            if (!token) return;
            try {
                const response = await fetch(PROFILE_URL, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                    }
                });
                const result = await response.json();

                if (DEBUG_DUE_DATE) {
                    console.log('[DEBUG] Profile API result:', result);
                }

                if (!response.ok || !result.status) {
                    console.warn('[DEBUG] Failed to fetch profile for due date.');
                    return;
                }

                const profile = result.data || {};

                // Guessed field names -- confirm the exact one from [DEBUG] Profile API result
                // in the console, then trim this fallback chain down to just that key.
                const rawDueDate =
                    profile.exhibitor_badge_due_date ??
                    profile.exhibitor_badges_due_date ??
                    profile.badges_due_date ??
                    profile.exhibitor_due_date ??
                    null;

                if (DEBUG_DUE_DATE) {
                    console.log('[DEBUG] resolved exhibitor badge due date:', rawDueDate);
                }

                const dueDateEl = document.getElementById('exhibitorbadgeduedate');
                if (dueDateEl) {
                    dueDateEl.innerText = formatDueDate(rawDueDate);
                }

                applyBadgeAccessRules(rawDueDate);
            } catch (err) {
                console.error('[DEBUG] Error fetching profile for due date:', err);
            }
        }

        // Applies view-only lockdown when the due date has passed
        // Applies view-only lockdown when the due date has passed
        function applyBadgeAccessRules(rawDueDate) {
            let pastDue = false;
            if (rawDueDate) {
                const dueDateObj = new Date(rawDueDate);
                if (!isNaN(dueDateObj.getTime())) {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    dueDateObj.setHours(0, 0, 0, 0);
                    pastDue = today > dueDateObj;
                }
            }

            if (!pastDue) return;
            isViewOnly = true;
            document.getElementById('readonlyMessage').style.display = 'block';
            const addBtn = document.getElementById('addBadgeBtn');
            if (addBtn) {
                addBtn.style.display = 'none';
            }
            document.querySelectorAll('#badgeForm input, #badgeForm select, #badgeForm textarea').forEach(el => {
                el.setAttribute('readonly', true);
                el.setAttribute('disabled', true);
            });
            const submitBtn = document.getElementById('badgeSubmitBtn');
            if (submitBtn) {
                submitBtn.setAttribute('disabled', true);
                submitBtn.style.opacity = '0.5';
                submitBtn.style.cursor = 'not-allowed';
            }
            showToast('The due date has passed. View only mode.', 'warning');
            if (window.__lastRenderedBadges) {
                renderBadges(window.__lastRenderedBadges);
            }
        }

        // Now only handles notes/colors/admin enable-disable -- NOT the due date
        function handleManualSetupData(manualSetup) {
    const noteText = document.getElementById('badgesNoteText');
    if (noteText && manualSetup.badges_note) {
        noteText.innerHTML = manualSetup.badges_note;
    } else if (noteText) {
        noteText.innerHTML = 'No note available.';
    }

    // ✅ REMOVED: exhibitor_badge_color, vendor_badge_color, etc.
    // These are now handled by the model/buildBadgeViewData method

    const status = manualSetup.form_status || 'enabled_open';
    if (status === 'disabled') {
        isViewOnly = true;
        document.getElementById('readonlyMessage').style.display = 'block';
        document.getElementById('readonlyMessage').innerHTML = 
            '<i class="bi bi-info-circle"></i> This form is currently disabled. You can view existing badges but cannot add, edit, or delete.';
        
        const addBtn = document.getElementById('addBadgeBtn');
        if (addBtn) {
            addBtn.classList.add('disabled-btn');
            addBtn.title = 'Form is disabled';
        }
        
        document.querySelectorAll('#badgeForm input, #badgeForm select, #badgeForm textarea').forEach(el => {
            el.setAttribute('readonly', true);
            el.setAttribute('disabled', true);
        });
        
        const submitBtn = document.getElementById('badgeSubmitBtn');
        if (submitBtn) {
            submitBtn.setAttribute('disabled', true);
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
        }
        
        showToast('This form is currently disabled.', 'warning');
        return;
    }
    
    if (status === 'enabled_closed') {
        isViewOnly = true;
        document.getElementById('readonlyMessage').style.display = 'block';
        document.getElementById('addBadgeBtn').classList.add('disabled-btn');
        document.querySelectorAll('#badgeForm input, #badgeForm select, #badgeForm textarea').forEach(el => {
            el.setAttribute('readonly', true);
            el.setAttribute('disabled', true);
        });
        const submitBtn = document.getElementById('badgeSubmitBtn');
        if (submitBtn) {
            submitBtn.setAttribute('disabled', true);
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
        }
    }
}

        function formatDueDate(dateStr) {
            if (!dateStr) return '--';
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return dateStr;
            return d.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function renderBadges(badges) {
            window.__lastRenderedBadges = badges; // cache so applyBadgeAccessRules can re-render later
            const tbody = document.getElementById('badgeTableBody');
            if (!tbody) return;
            if (!Array.isArray(badges) || badges.length === 0) {
                tbody.innerHTML = "";
                reloadDataTable();
                return;
            }
            let html = '';
            badges.forEach(function(badge, index) {
                const encryptedId = badge.encrypted_id || '';
                const salutation = badge.salutation || '';
                const fname = badge.fname || '';
                const lname = badge.lname || '';
                const fullName = badge.full_name || `${fname} ${lname}`.trim();
                const email = badge.email || '';

                let photoUrl = '';
                if (badge.photo_url) {
                    photoUrl = badge.photo_url;
                } else if (badge.exhibitor_image) {
                    const relativePath = String(badge.exhibitor_image).replace(/^\/+/, '');
                    photoUrl = `${UPLOAD_BASE_URL}/${relativePath}`;
                }

                html += `
        <tr>
            <td>${index + 1}</td>
            <td>
                <img src="${escapeAttr(photoUrl)}" class="user-img" alt="User Photo" onerror="this.src=''">
            </td>
            <td>${escapeHtml(fullName)}</td>
            <td>${escapeHtml(email)}</td>
            <td>
                <div class="action-btns">
                    <button type="button" class="btn btn-edit editBadgeBtn ${isViewOnly ? 'disabled-btn' : ''}"
                        data-id="${escapeAttr(encryptedId)}"
                        data-salutation="${escapeAttr(salutation)}"
                        data-fname="${escapeAttr(fname)}"
                        data-lname="${escapeAttr(lname)}"
                        data-email="${escapeAttr(email)}"
                        data-mobile="${escapeAttr(badge.mobile || '')}"
                        ${isViewOnly ? 'disabled' : ''}>
                        <i class="bi bi-pencil"></i>
                    </button>
                   
                    <button type="button" class="btn btn-download downloadBadgeBtn"
                        data-id="${escapeAttr(encryptedId)}"
                        data-fullname="${escapeAttr(fullName)}">
                        <i class="bi bi-download"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
            });
            tbody.innerHTML = html;
            bindBadgeButtons();
            reloadDataTable();
        }

        function bindBadgeButtons() {
            document.querySelectorAll('.editBadgeBtn:not(.disabled-btn)').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    openEdit({
                        id: this.dataset.id,
                        fname: this.dataset.fname,
                        lname: this.dataset.lname,
                        email: this.dataset.email,
                        mobile: this.dataset.mobile
                    });
                });
            });

            document.querySelectorAll('.deleteBadgeBtn:not(.disabled-btn)').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    openDelete(this.dataset.id);
                });
            });

            document.querySelectorAll('.downloadBadgeBtn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    downloadBadgePdf(this.dataset.id, this.dataset.fullname);
                });
            });
        }

        function reloadDataTable() {
            if (!window.jQuery || !$.fn.DataTable) {
                console.warn('DataTables not loaded.');
                return;
            }
            if ($.fn.DataTable.isDataTable('#userTable')) {
                $('#userTable').DataTable().destroy();
            }
            $('#userTable').DataTable({
                destroy: true,
                pageLength: 10,
                ordering: true,
                autoWidth: false,
                responsive: true,
                searching: true,
                info: true,
                paging: true,
                dom: "<'row mb-3'<'col-md-6'l><'col-md-6 text-end'f>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row mt-3'<'col-md-5'i><'col-md-7 text-end'p>>",
                language: {
                    emptyTable: "No badges found.",
                    zeroRecords: "No badges found.",
                    search: "",
                    searchPlaceholder: "Search here"
                },
                columnDefs: [{
                    orderable: false,
                    targets: [1, 4]
                }]
            });
        }

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function escapeAttr(value) {
            return escapeHtml(value);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const addBadgeBtn = document.getElementById('addBadgeBtn');
            const badgeForm = document.getElementById('badgeForm');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

            if (addBadgeBtn) {
                addBadgeBtn.addEventListener('click', openAdd);
            }

            if (badgeForm) {
                badgeForm.addEventListener('submit', submitBadgeForm);
            }

            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', confirmDelete);
            }

            fetchBadges();
            fetchExhibitorBadgeAccess(); // Fetches due date from /v1/profile and applies view-only lock
        });

        window.openAdd = openAdd;
        window.openEdit = openEdit;
        window.openDelete = openDelete;
        window.confirmDelete = confirmDelete;
    })();
</script>

<?= $this->endSection() ?>
