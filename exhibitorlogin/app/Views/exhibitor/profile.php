<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content') ?>

<div class="content-area">
    <div class="profile-page">
        <div class="profile-hero">
            <div class="profile-hero-main">
                <h3>Profile</h3>
                <p>Review your company details and update the brand information used for event promotions.</p>
            </div>
            <div class="profile-hero-side">
                <span class="due-pill">Due Date: &nbsp;<span id="profileDueDate">--</span></span>
                <button type="button" class="btn profile-edit-btn">
                    <i class="bi bi-pencil-square"></i> Edit
                </button>
            </div>
        </div>

        <div id="profileLoading" class="text-center py-4">
            <span class="spinner-border spinner-border-sm me-2"></span> Loading profile...
        </div>

        <!-- Validation Summary -->
        <div id="validationSummary" class="validation-summary" style="display: none;">
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>Please fix the following errors:</strong>
                <ul id="errorList"></ul>
            </div>
        </div>

        <form id="profileForm" class="profile-form d-none" method="post" enctype="multipart/form-data">
            <div id="profileStatus" class="profile-status" style="margin-bottom:10px"></div>

            <div class="profile-card mb-4">
                <div class="profile-section-head">
                    <h4>Company Details</h4>
                </div>
                <div class="profile-grid">
                    <div class="profile-field">
                        <label for="profile_brand_name" class="form-label">Brand Name</label>
                        <input type="text" class="form-control" name="brand_name" id="profile_brand_name" readonly autocomplete="off">
                    </div>
                    <div class="profile-field">
                        <label class="form-label" for="profile_company_name">Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="company_name" id="profile_company_name" readonly required autocomplete="off">
                    </div>
                    <div class="profile-field">
                        <label class="form-label" for="profile_phone_number">Telephone No. <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="phone_number" id="profile_phone_number" readonly required autocomplete="off">
                    </div>
                    <div class="profile-field">
                        <label class="form-label" for="profile_stand_number">Stand No.</label>
                        <input type="text" class="form-control" name="stand_number" id="profile_stand_number" readonly>
                    </div>
                    <div class="profile-field">
                        <label for="profile_area" class="form-label">Area</label>
                        <input type="text" class="form-control" id="profile_area" name="area" readonly inputmode="numeric">
                    </div>

                    <div class="profile-field">
                        <label class="form-label" for="gst_number">GST Number.</label>
                        <input type="text" class="form-control" name="gst_number" id="gst_number" readonly>
                    </div>

                    <!-- Casual GST field: visibility controlled per-event in JS (see init()) -->
                    <div class="profile-field" id="casualGstField">
                        <label for="casual_gst_number" class="form-label">Casual GST Number</label>
                        <input type="text" class="form-control" id="casual_gst_number" name="casual_gst_number" readonly>
                    </div>

                    <div class="profile-field">
                        <label for="profile_contact_person" class="form-label">Contact Person</label>
                        <input type="text" class="form-control" name="contact_person" id="profile_contact_person" readonly autocomplete="off">
                    </div>
                    <div class="profile-field">
                        <label for="profile_mobile_number" class="form-label">Mobile No.</label>
                        <input type="text" class="form-control" name="mobile_number" id="profile_mobile_number" readonly autocomplete="off">
                    </div>
                    <div class="profile-field">
                        <label for="profile_email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="profile_email" readonly autocomplete="off">
                    </div>
                    <div class="profile-field">
                        <label for="profile_address" class="form-label">Address</label>
                        <input type="text" class="form-control" name="address" id="profile_address" readonly autocomplete="off">
                    </div>
                </div>
            </div>

            <!-- Event-specific stepper section gets injected here after the profile API call resolves -->
            <div id="dynamicEventSection"></div>
        </form>
    </div>
</div>

<div id="profileEditRequestModal" class="profile-edit-modal d-none" aria-hidden="true">
    <div class="profile-edit-dialog">
        <div class="profile-edit-header">
            <h5>Edit Profile Request</h5>
            <button type="button" class="modal-close-btn" aria-label="Close">&times;</button>
        </div>
        <div class="profile-edit-body">
            <label for="edit_request_detail" class="form-label">Detail</label>
            <textarea id="edit_request_detail" class="form-control" rows="5"
                placeholder="Enter Details to Edit Profile"></textarea>
        </div>
        <div class="profile-edit-footer">
            <button type="button" id="sendEditRequest" class="btn btn-dark">Send Request</button>
        </div>
    </div>
</div>

<!-- Restart Confirmation Modal -->
<div id="restartModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restart Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to restart? All unsaved changes will be lost.</p>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="confirmRestart">
                    <label class="form-check-label" for="confirmRestart">
                        I understand that all changes will be cleared
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRestartBtn">Yes, Restart</button>
            </div>
        </div>
    </div>
</div>

<style>
    .pdi-wrapper {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 15px;
        background: #fff;
    }

    .pdi-parent-row {
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #eee;
    }

    .pdi-parent-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .pdi-checkbox-label {
        display: block;
        margin-bottom: 4px;
        cursor: pointer;
    }

    .pdi-parent-label {
        font-weight: 600;
        margin-bottom: 6px;
    }

    .pdi-child-label {
        padding-left: 28px;
        font-weight: 400;
    }

    .pdi-child-label input[type="checkbox"] {
        margin-right: 6px;
    }

    .pdi-parent-label input[type="checkbox"] {
        margin-right: 8px;
    }

    .pdi-children-list {
        padding-left: 20px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 4px 15px;
    }

    .pdi-others-input {
        margin-top: 6px;
        max-width: 300px;
    }

    .ps-panel {
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .ps-panel.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .ps-stepper-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 25px;
        padding: 10px 0;
        position: relative;
    }

    .ps-step-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        position: relative;
    }

    .ps-step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid #dee2e6;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
    }

    .ps-step-circle.active {
        border-color: #0d6efd;
        background: #0d6efd;
        color: #fff;
    }

    .ps-step-circle.done {
        border-color: #198754;
        background: #198754;
        color: #fff;
    }

    .ps-step-circle.has-error {
        border-color: #dc3545;
        background: #dc3545;
        color: #fff;
        animation: shake 0.5s ease;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        75% {
            transform: translateX(5px);
        }
    }

    .ps-step-num {
        font-size: 14px;
        font-weight: 600;
    }

    .ps-step-icon {
        display: none;
    }

    .ps-step-circle.active .ps-step-num,
    .ps-step-circle.done .ps-step-num {
        display: none;
    }

    .ps-step-circle.active .ps-step-icon,
    .ps-step-circle.done .ps-step-icon {
        display: inline;
    }

    .ps-step-label {
        font-size: 12px;
        color: #6c757d;
        margin-top: 6px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .ps-step-label.active {
        color: #0d6efd;
        font-weight: 600;
    }

    .ps-step-label.done {
        color: #198754;
    }

    .ps-step-label.has-error {
        color: #dc3545;
    }

    .ps-step-connector {
        flex: 1;
        height: 2px;
        background: #dee2e6;
        margin: 0 -5px;
        margin-bottom: 25px;
        transition: background 0.3s ease;
    }

    .ps-step-connector.done {
        background: #198754;
    }

    .ps-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #dee2e6;
    }

    .ps-hint {
        font-size: 13px;
        color: #6c757d;
    }

    .ps-upload-box {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        background: #f8f9fa;
        transition: all 0.3s ease;
        flex-wrap: wrap;
    }

    .ps-upload-box.has-error {
        border-color: #dc3545;
        background: #fff5f5;
    }

    .ps-upload-icon {
        font-size: 28px;
        color: #6c757d;
    }

    .ps-upload-meta {
        flex: 1;
        min-width: 150px;
    }

    .ps-upload-meta strong {
        display: block;
        font-size: 14px;
    }

    .ps-file-name {
        font-size: 13px;
        color: #6c757d;
    }

    .ps-current-link {
        display: inline-block;
        margin-top: 4px;
        font-size: 13px;
    }

    .ps-choose-btn {
        white-space: nowrap;
    }

    .ps-contact-stack {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 400px;
    }

    .ps-contact-row {
        width: 100%;
    }

    .profile-edit-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1050;
    }

    .profile-edit-modal.d-none {
        display: none !important;
    }

    .profile-edit-dialog {
        background: #fff;
        border-radius: 8px;
        max-width: 500px;
        width: 90%;
        padding: 20px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .profile-edit-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .modal-close-btn {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
    }

    .profile-edit-footer {
        margin-top: 15px;
        text-align: right;
    }

    @media (max-width: 768px) {
        .pdi-children-list {
            grid-template-columns: 1fr;
        }

        .ps-stepper-nav {
            flex-wrap: wrap;
            gap: 10px;
        }

        .ps-step-col {
            flex: 0 0 auto;
        }

        .ps-step-connector {
            display: none;
        }
    }

    form[data-locked="true"] .ps-panel {
        opacity: 0.85;
    }

    form[data-locked="true"] input:disabled,
    form[data-locked="true"] textarea:disabled,
    form[data-locked="true"] select:disabled {
        background-color: #f5f5f5;
        cursor: not-allowed;
        opacity: 0.7;
    }

    form[data-locked="true"] .ps-btn-next {
        opacity: 0.7;
    }

    form[data-locked="true"] .ps-footer .btn {
        pointer-events: auto;
    }
</style>

<script>
    (function() {
        'use strict';
        const UPLOAD_BASE_URL = '<?= rtrim(env('UPLOAD_BASE_URL'), '/') ?>';
        const PROFILE_URL = '<?= env('API_BASE_URL') ?>/v1/profile';
        const PROFILE_SAVE_URL = '<?= env('API_BASE_URL') ?>/v1/dashboard/profile/save';
        const PRODUCT_CATEGORIES_URL = '<?= env('API_BASE_URL') ?>/v1/product-categories';
        const resolveUrl = v => v ? `${UPLOAD_BASE_URL}/${v}` : '';

        let current = 0;
        let totalSteps = 0;
        let stepValidations = {};
        let profileData = null;

        const CERTIFICATE_MOMENTO_FIELD = {
            label: 'Certificate/Memento Name',
            name: 'name_on_certificate_memento',
            placeholder: 'Enter the name to be printed on the certificate/memento.',
            required: false,
            minlength: 0,
            maxlength: 150,
            profileKey: 'name_on_certificate_memento',
            type: 'text'
        };

        const EVENT_SECTIONS = {
            'Drone Expo': {
                fields: [{
                        label: 'Company Product Specialization*',
                        name: 'company_product_specialization',
                        placeholder: 'Write a short description about company product specialization.',
                        required: true,
                        minlength: 20,
                        maxlength: 500,
                        profileKey: 'company_product_specialization'
                    },
                    {
                        label: 'Company Profile*',
                        name: 'brand_profile',
                        placeholder: 'Write a short description about Company profile.',
                        required: true,
                        minlength: 20,
                        maxlength: 1000,
                        profileKey: 'brand_profile'
                    },
                    CERTIFICATE_MOMENTO_FIELD,
                ],
                uploads: [{
                    name: 'brand_logo',
                    label: 'Company Logo*',
                    required: true,
                    profileKey: 'brand_logo'
                }],
                hasContact: false,
                hasProductCategories: false,
                showCasualGst: false,
                steps: [{
                        label: 'Company info',
                        icon: 'bi-building'
                    },
                    {
                        label: 'Uploads',
                        icon: 'bi-cloud-upload'
                    },
                ],
            },
            'Secure Nation': {
                fields: [{
                        label: 'Company Product Specialization*',
                        name: 'company_product_specialization',
                        placeholder: 'Write a short description about company product specialization.',
                        required: true,
                        minlength: 20,
                        maxlength: 500,
                        profileKey: 'company_product_specialization'
                    },
                    {
                        label: 'Company Profile*',
                        name: 'brand_profile',
                        placeholder: 'Write a short description about Company profile.',
                        required: true,
                        minlength: 20,
                        maxlength: 1000,
                        profileKey: 'brand_profile'
                    },
                    CERTIFICATE_MOMENTO_FIELD,
                ],
                uploads: [{
                    name: 'brand_logo',
                    label: 'Company Logo*',
                    required: true,
                    profileKey: 'brand_logo'
                }],
                hasContact: false,
                hasProductCategories: false,
                showCasualGst: false,
                steps: [{
                        label: 'Company info',
                        icon: 'bi-building'
                    },
                    {
                        label: 'Uploads',
                        icon: 'bi-cloud-upload'
                    },
                ],
            },
            'Bridal Asia': {
                fields: [{
                        label: 'Please share text regarding your brand. This text will be used for marketing & other promotional activities.*',
                        name: 'company_product_specialization',
                        placeholder: 'Write a short description about company product specialization.',
                        required: true,
                        minlength: 20,
                        maxlength: 500,
                        profileKey: 'company_product_specialization'
                    },
                    CERTIFICATE_MOMENTO_FIELD,
                ],
                uploads: [{
                    name: 'brand_logo',
                    label: 'Upload Brand Logo (Max Size 1 MB)',
                    required: true,
                    profileKey: 'brand_logo'
                }],
                hasContact: false,
                hasProductCategories: false,
                showCasualGst: true,
                steps: [{
                        label: 'Company info',
                        icon: 'bi-building'
                    },
                    {
                        label: 'Uploads',
                        icon: 'bi-cloud-upload'
                    },
                ],
            },
            'Fire India': {
                fields: [{
                        label: 'Company Product Specialization*',
                        name: 'company_product_specialization',
                        placeholder: 'Write a short description about company product specialization.',
                        required: true,
                        minlength: 20,
                        maxlength: 500,
                        profileKey: 'company_product_specialization'
                    },
                    {
                        label: 'Company Profile*',
                        name: 'brand_profile',
                        placeholder: 'Write a short description about Company profile.',
                        required: true,
                        minlength: 20,
                        maxlength: 1000,
                        profileKey: 'brand_profile'
                    },
                    CERTIFICATE_MOMENTO_FIELD,
                ],
                uploads: [{
                    name: 'brand_logo',
                    label: 'Company Logo*',
                    required: true,
                    profileKey: 'brand_logo'
                }],
                hasContact: false,
                hasProductCategories: true,
                showCasualGst: false,
                steps: [{
                        label: 'Company info',
                        icon: 'bi-building'
                    },
                    {
                        label: 'Products',
                        icon: 'bi-tools'
                    },
                    {
                        label: 'Uploads',
                        icon: 'bi-cloud-upload'
                    },
                ],
            },
        };

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
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
                    allowToastClose: true,
                });
            } else {
                alert(message);
            }
        }

        function getAuthToken() {
            return localStorage.getItem('api_token') || sessionStorage.getItem('api_token') || '';
        }

        async function fetchProfile() {
            const token = getAuthToken();
            if (!token) {
                showToast('Login token missing. Please login again.', 'error');
                return null;
            }
            try {
                const response = await fetch(PROFILE_URL, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const result = await response.json();
                if (response.ok && result.status) {
                    return result.data;
                }
                showToast(result.message || 'Unable to fetch profile.', 'error');
                return null;
            } catch (error) {
                showToast('Network error while fetching profile.', 'error');
                return null;
            }
        }

        async function fetchProductCategories() {
            const token = getAuthToken();
            try {
                const response = await fetch(PRODUCT_CATEGORIES_URL, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const result = await response.json();
                if (response.ok && result.status) {
                    return result.data || [];
                }
                console.warn('Could not load product categories:', result.message);
                return [];
            } catch (error) {
                console.error(error);
                return [];
            }
        }

        function buildProductCategoriesHtml(categories, profile, panelIndex) {
            let rowsHtml = '';

            categories.forEach(parent => {
                const children = parent.children || [];
                const allChildrenSelected = children.length > 0 && children.every(child => child.selected === true);

                rowsHtml += `
                        <div class="pdi-parent-row">
                            <label class="pdi-checkbox-label pdi-parent-label">
                                <input type="checkbox" class="pdi-parent-checkbox" data-parent-id="${parent.id}" ${allChildrenSelected ? 'checked' : ''}>
                                <strong>${escapeHtml(parent.name)}</strong>
                            </label>
                            <div class="pdi-children-list">
                    `;

                children.forEach(child => {
                    const checked = child.selected ? 'checked' : '';
                    rowsHtml += `
                        <label class="pdi-checkbox-label pdi-child-label">
                            <input type="checkbox" class="pdi-child-checkbox"
                                name="product_deals_in[]" value="${child.id}"
                                data-parent-id="${parent.id}" ${checked}>
                            ${escapeHtml(child.name)}
                        </label>
                    `;
                });

                rowsHtml += `
                    </div>
                </div>
            `;
            });

            const otherValue = profile.product_deals_in_other || '';
            rowsHtml += `
            <div class="pdi-parent-row">
                
                <input type="text" class="form-control pdi-others-input"
                    id="product_deals_in_other" name="product_deals_in_other"
                    value="${escapeHtml(otherValue)}"
                    placeholder="Please specify"
                    ${otherValue ? '' : 'style="display:none;"'}>
            </div>
        `;

            return `
            <div class="profile-field mb-3">
                <label class="form-label">Product Deals In*</label>
                <div class="pdi-wrapper" id="productDealsInWrapper">
                    ${rowsHtml}
                </div>
                <div class="invalid-feedback" id="productDealsInError"></div>
            </div>
        `;
        }

        function setupProductCategoryInteractions() {
            document.querySelectorAll('.pdi-parent-checkbox').forEach(parentCb => {
                parentCb.addEventListener('change', function() {
                    const parentId = this.dataset.parentId;
                    document.querySelectorAll(`.pdi-child-checkbox[data-parent-id="${parentId}"]`)
                        .forEach(childCb => childCb.checked = parentCb.checked);
                });
            });

            document.querySelectorAll('.pdi-child-checkbox').forEach(childCb => {
                childCb.addEventListener('change', function() {
                    const parentId = this.dataset.parentId;
                    const parentCb = document.querySelector(`.pdi-parent-checkbox[data-parent-id="${parentId}"]`);
                    const siblings = document.querySelectorAll(`.pdi-child-checkbox[data-parent-id="${parentId}"]`);
                    if (parentCb) {
                        parentCb.checked = Array.from(siblings).every(c => c.checked);
                    }
                });
            });

            const othersCb = document.getElementById('pdiOthersCheckbox');
            const othersInput = document.getElementById('product_deals_in_other');
            if (othersCb && othersInput) {
                othersCb.addEventListener('change', function() {
                    othersInput.style.display = this.checked ? '' : 'none';
                    if (!this.checked) othersInput.value = '';
                });
            }
        }

        const MONTH_ABBR = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];

        function formatDueDate(dateStr) {
            if (!dateStr) return 'Not set';
            const d = new Date(dateStr + 'T00:00:00');
            if (isNaN(d.getTime())) return 'Not set';
            const day = d.getDate();
            const suffix = (day % 10 === 1 && day !== 11) ? 'st' :
                (day % 10 === 2 && day !== 12) ? 'nd' :
                (day % 10 === 3 && day !== 13) ? 'rd' :
                'th';
            return `${day}${suffix} ${MONTH_ABBR[d.getMonth()]} ${d.getFullYear()}`;
        }

        function isDueDatePassed(dateStr) {
            if (!dateStr) return false;
            const due = new Date(dateStr + 'T23:59:59');
            if (isNaN(due.getTime())) return false;
            return new Date() > due;
        }

        function applyDueDateLock(profile) {
            if (!isDueDatePassed(profile.manual_due_date)) return;
            const statusDiv = document.getElementById('profileStatus');
            if (statusDiv) {
                statusDiv.innerHTML = `
            <div class="alert alert-warning mb-0">
                <i class="bi bi-lock-fill"></i>
                The submission due date (${formatDueDate(profile.manual_due_date)}) has passed.
                This form is now locked and cannot be edited.
            </div>
        `;
            }
            form.querySelectorAll('input:not([type="file"]), textarea, select').forEach(el => {
                el.setAttribute('disabled', 'disabled');
            });
            form.querySelectorAll('input[type="file"]').forEach(input => {
                input.setAttribute('disabled', 'disabled');
                const uploadBox = input.closest('.ps-upload-box');
                if (uploadBox) {
                    const chooseBtn = uploadBox.querySelector('.ps-choose-btn');
                    if (chooseBtn) chooseBtn.style.display = 'none';
                }
            });
            form.querySelectorAll('.profile-save-btn, button[type="submit"]').forEach(btn => {
                btn.style.display = 'none';
            });
            document.querySelector('.profile-edit-btn')?.style.setProperty('display', 'none');
            form.querySelectorAll('.ps-btn-restart').forEach(btn => {
                btn.style.display = 'none';
            });
            form.querySelectorAll('.ps-btn-next').forEach(btn => {
                btn.innerHTML = '<i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-secondary');
                btn.style.cursor = 'default';
            });
            form.dataset.locked = 'true';
        }

        function populateBaseFields(profile) {
            document.getElementById('profile_brand_name').value = profile.brand_name || '';
            document.getElementById('profile_company_name').value = profile.organisation_name || '';
            document.getElementById('profile_phone_number').value = profile.landline || '';
            document.getElementById('profile_stand_number').value = profile.stall_number || '';
            document.getElementById('profile_area').value = profile.stall_size || '21';
            document.getElementById('gst_number').value = profile.gst_number || profile.gst || '';
            document.getElementById('casual_gst_number').value = profile.casual_gst_number || profile.casual_gst || '';
            document.getElementById('profile_contact_person').value = profile.contact_person || '';
            document.getElementById('profile_mobile_number').value = profile.contact_number || '';
            document.getElementById('profile_email').value = profile.contact_email || '';
            document.getElementById('profile_address').value = profile.organisation_address || '';
            const dueDateEl = document.getElementById('profileDueDate');
            if (dueDateEl) {
                dueDateEl.textContent = formatDueDate(profile.manual_due_date);
            }
        }

        function buildEventSectionHtml(sectionConfig, profile) {
            const steps = sectionConfig.steps;
            const totalStepsLocal = steps.length;
            const hasContact = sectionConfig.hasContact;
            const hasProductCategories = sectionConfig.hasProductCategories || false;

            let stepNavHtml = `<div class="ps-stepper-nav" id="psStepNav" role="tablist" aria-label="Profile form steps">`;
            steps.forEach((step, i) => {
                stepNavHtml += `
                <div class="ps-step-col">
                    <button type="button" class="ps-step-circle ${i === 0 ? 'active' : ''}"
                        id="ps-sc-${i}" role="tab" aria-controls="ps-panel-${i}"
                        aria-selected="${i === 0 ? 'true' : 'false'}"
                        tabindex="${i === 0 ? '0' : '-1'}" data-step="${i}">
                        <i class="bi ${step.icon} ps-step-icon" aria-hidden="true"></i>
                        <span class="ps-step-num">${i + 1}</span>
                    </button>
                    <span class="ps-step-label ${i === 0 ? 'active' : ''}" id="ps-sl-${i}">
                        ${escapeHtml(step.label)}
                    </span>
                </div>
                ${i < totalStepsLocal - 1 ? `<div class="ps-step-connector" id="ps-conn-${i}"></div>` : ''}
            `;
            });
            stepNavHtml += `</div>`;

            let fieldsHtml = '';
            sectionConfig.fields.forEach(field => {
                const value = profile[field.profileKey] || '';
                const isTextInput = field.type === 'text';
                fieldsHtml += `
                <div class="profile-field mb-3">
                    <label for="${field.name}" class="form-label">${escapeHtml(field.label)}</label>
                    ${isTextInput
                        ? `<input type="text" name="${field.name}" id="${field.name}" class="form-control"
                               data-required="${field.required ? 'true' : 'false'}"
                               data-minlength="${field.minlength || 0}"
                               data-maxlength="${field.maxlength || 0}"
                               placeholder="${escapeHtml(field.placeholder)}" value="${escapeHtml(value)}">`
                        : `<textarea name="${field.name}" id="${field.name}" class="form-control profile-textarea" rows="5"
                               data-required="${field.required ? 'true' : 'false'}"
                               data-minlength="${field.minlength || 0}"
                               data-maxlength="${field.maxlength || 0}"
                               placeholder="${escapeHtml(field.placeholder)}">${escapeHtml(value)}</textarea>`
                    }
                    <div class="invalid-feedback" id="${field.name}Error"></div>
                    ${field.minlength ? `<small class="text-muted">Minimum ${field.minlength} characters</small>` : ''}
                </div>
            `;
            });

            let uploadsHtml = '';
            sectionConfig.uploads.forEach(upload => {
                const current = resolveUrl(profile[upload.profileKey]);
                uploadsHtml += `
                <div class="profile-field mb-3">
                    <label for="${upload.name}" class="form-label">${escapeHtml(upload.label)}</label>
                    <div class="profile-upload ps-upload-box">
                        <input type="file" class="form-control visually-hidden" name="${upload.name}" id="${upload.name}"
                            accept="image/*" data-required="${upload.required ? 'true' : 'false'}">
                        <div class="ps-upload-icon"><i class="bi bi-image" aria-hidden="true"></i></div>
                        <div class="ps-upload-meta">
                            <strong>Upload logo file</strong>
                            <span class="ps-file-name" id="${upload.name}Name">No file chosen</span>
                            ${current ? `<a href="${escapeHtml(current)}" target="_blank" class="ps-current-link">
                                <i class="bi bi-eye me-1" aria-hidden="true"></i>
                                View current ${escapeHtml(upload.name.replace(/_/g, ' '))}
                            </a>` : ''}
                        </div>
                        <label for="${upload.name}" class="btn ps-choose-btn">
                            <i class="bi bi-folder2-open me-1" aria-hidden="true"></i> Choose File
                        </label>
                    </div>
                    <div class="invalid-feedback" id="${upload.name}Error"></div>
                </div>
            `;
            });

            let contactHtml = '';
            if (hasContact) {
                const name = profile.contact_person || '';
                const number = profile.contact_number || '';
                const email = profile.contact_email || '';
                const contactPanelIndex = hasProductCategories ? 3 : 2;
                contactHtml = `
                <div class="ps-panel" id="ps-panel-${contactPanelIndex}" role="tabpanel" aria-labelledby="ps-sc-${contactPanelIndex}">
                    <div class="profile-field mb-3">
                        <label class="form-label">Contact Details</label>
                        <div class="ps-contact-stack">
                            <div class="input-group ps-contact-row">
                                <span class="input-group-text"><i class="bi bi-person" aria-hidden="true"></i></span>
                                <input type="text" class="form-control contact-details" name="contact_person_name"
                                    id="contact_person_name" value="${escapeHtml(name)}"
                                    placeholder="Contact person's name" data-required="true" data-minlength="3" autocomplete="off">
                            </div>
                            <div class="input-group ps-contact-row">
                                <span class="input-group-text"><i class="bi bi-telephone" aria-hidden="true"></i></span>
                                <input type="text" class="form-control contact-details" name="contact_person_number"
                                    id="contact_person_number" value="${escapeHtml(number)}"
                                    placeholder="Contact person's number" data-required="true" data-pattern="^[0-9]{10}$" autocomplete="off">
                            </div>
                            <div class="input-group ps-contact-row">
                                <span class="input-group-text"><i class="bi bi-envelope" aria-hidden="true"></i></span>
                                <input type="email" class="form-control contact-details" name="contact_person_email"
                                    id="contact_person_email" value="${escapeHtml(email)}"
                                    placeholder="Contact person's email" data-required="true" data-type="email" autocomplete="off">
                            </div>
                        </div>
                        <div class="invalid-feedback" id="contactError"></div>
                    </div>
                    <div class="ps-footer">
                        <button type="button" class="btn btn-outline-secondary ps-btn-back" data-target="${contactPanelIndex - 1}">
                            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
                        </button>
                        <span class="ps-hint">Step ${contactPanelIndex + 1} of ${totalStepsLocal}</span>
                        <button type="submit" class="btn btn-primary profile-save-btn">
                            <i class="bi bi-check2 me-1" aria-hidden="true"></i> Save
                        </button>
                    </div>
                </div>
            `;
            }

            let cardHtml = `
            <div class="profile-card ps-card">
                <div class="ps-panel active" id="ps-panel-0" role="tabpanel" aria-labelledby="ps-sc-0">
                    ${fieldsHtml}
                    <div class="ps-footer">
                        <span class="ps-hint">Step 1 of ${totalStepsLocal}</span>
                        <button type="button" class="btn btn-primary ps-btn-next" data-target="1">
                            Next <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
        `;

            if (hasProductCategories) {
                cardHtml += `
                <div class="ps-panel" id="ps-panel-1" role="tabpanel" aria-labelledby="ps-sc-1">
                    <div id="productsPanelContent"></div>
                    <div class="ps-footer">
                        <button type="button" class="btn btn-outline-secondary ps-btn-back" data-target="0">
                            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
                        </button>
                        <span class="ps-hint">Step 2 of ${totalStepsLocal}</span>
                        <button type="button" class="btn btn-primary ps-btn-next" data-target="2">
                            Next <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="ps-panel" id="ps-panel-2" role="tabpanel" aria-labelledby="ps-sc-2">
                    ${uploadsHtml}
                    <div class="ps-footer">
                        <button type="button" class="btn btn-outline-secondary ps-btn-back" data-target="1">
                            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
                        </button>
                        <span class="ps-hint">Step 3 of ${totalStepsLocal}</span>
                        ${hasContact
                            ? `<button type="button" class="btn btn-primary ps-btn-next" data-target="3">Next <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i></button>`
                            : `<button type="submit" class="btn btn-primary profile-save-btn"><i class="bi bi-check2 me-1" aria-hidden="true"></i> Save</button>`
                        }
                    </div>
                </div>
            `;
            } else {
                cardHtml += `
                <div class="ps-panel" id="ps-panel-1" role="tabpanel" aria-labelledby="ps-sc-1">
                    ${uploadsHtml}
                    <div class="ps-footer">
                        <button type="button" class="btn btn-outline-secondary ps-btn-back" data-target="0">
                            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
                        </button>
                        <span class="ps-hint">Step 2 of ${totalStepsLocal}</span>
                        ${hasContact
                            ? `<button type="button" class="btn btn-primary ps-btn-next" data-target="2">Next <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i></button>`
                            : `<button type="submit" class="btn btn-primary profile-save-btn"><i class="bi bi-check2 me-1" aria-hidden="true"></i> Save</button>`
                        }
                    </div>
                </div>
            `;
            }

            cardHtml += contactHtml;
            cardHtml += `</div>`;

            return stepNavHtml + cardHtml;
        }

        function clearValidationErrors() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.classList.remove('show');
                el.textContent = '';
            });
            document.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
            document.getElementById('validationSummary')?.style.setProperty('display', 'none');
        }

        function validateField(field) {
            let isValid = true;
            let errorMessage = '';
            const value = field.value.trim();
            const required = field.getAttribute('data-required') === 'true';
            const minlength = parseInt(field.getAttribute('data-minlength')) || 0;
            const maxlength = parseInt(field.getAttribute('data-maxlength')) || 0;
            const pattern = field.getAttribute('data-pattern');
            const type = field.getAttribute('data-type');

            if (required && !value) {
                isValid = false;
                errorMessage = 'This field is required';
            } else if (value && minlength > 0 && value.length < minlength) {
                isValid = false;
                errorMessage = `Minimum ${minlength} characters required`;
            } else if (value && maxlength > 0 && value.length > maxlength) {
                isValid = false;
                errorMessage = `Maximum ${maxlength} characters allowed`;
            } else if (value && pattern) {
                const regex = new RegExp(pattern);
                if (!regex.test(value)) {
                    isValid = false;
                    errorMessage = 'Invalid format';
                }
            } else if (value && type === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid email address';
                }
            } else if (value && field.id === 'contact_person_number') {
                const phoneRegex = /^[0-9]{10}$/;
                if (!phoneRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid 10-digit phone number';
                }
            }

            const errorDiv = document.getElementById(field.id + 'Error') ||
                document.querySelector(`#${field.id} ~ .invalid-feedback`) ||
                document.getElementById('contactError');

            if (!isValid) {
                field.classList.add('is-invalid');
                if (errorDiv) {
                    errorDiv.textContent = errorMessage;
                    errorDiv.classList.add('show');
                }
            } else {
                field.classList.remove('is-invalid');
                if (errorDiv) errorDiv.classList.remove('show');
            }
            return isValid;
        }

        function validateFileUpload(input) {
            let isValid = true;
            const required = input.getAttribute('data-required') === 'true';
            const hasCurrentFile = input.closest('.ps-upload-box')?.querySelector('.ps-current-link') !== null;
            const hasNewFile = input.files && input.files.length > 0;
            if (required && !hasCurrentFile && !hasNewFile) {
                isValid = false;
                const uploadBox = input.closest('.ps-upload-box');
                uploadBox?.classList.add('has-error');
                const errorDiv = document.getElementById(input.id + 'Error');
                if (errorDiv) {
                    errorDiv.textContent = 'File upload is required';
                    errorDiv.classList.add('show');
                }
            } else {
                const uploadBox = input.closest('.ps-upload-box');
                uploadBox?.classList.remove('has-error');
                const errorDiv = document.getElementById(input.id + 'Error');
                if (errorDiv) errorDiv.classList.remove('show');
            }
            return isValid;
        }

        function validateProductCategoriesStep() {
            const anyChecked = document.querySelectorAll('.pdi-child-checkbox:checked').length > 0;
            const othersCb = document.getElementById('pdiOthersCheckbox');
            const othersInput = document.getElementById('product_deals_in_other');
            const othersFilled = othersCb?.checked && othersInput?.value?.trim();
            const isValid = anyChecked || othersFilled;
            const errorDiv = document.getElementById('productDealsInError');
            if (!isValid && errorDiv) {
                errorDiv.textContent = 'Please select at least one product category';
                errorDiv.classList.add('show');
            } else if (errorDiv) {
                errorDiv.classList.remove('show');
            }
            return isValid;
        }

        function validateCurrentStep() {
            clearValidationErrors();
            const currentPanel = document.getElementById(`ps-panel-${current}`);
            if (!currentPanel) return true;
            let isValid = true;
            const errors = [];

            const isProductsPanel = currentPanel.querySelector('#productDealsInWrapper') !== null;
            if (isProductsPanel) {
                if (!validateProductCategoriesStep()) {
                    isValid = false;
                    errors.push('Please select at least one product category');
                }
            }

            currentPanel.querySelectorAll('textarea, input[type="text"], input[type="email"], input[type="tel"]').forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                    const label = field.closest('.profile-field')?.querySelector('.form-label')?.textContent || field.placeholder || field.id;
                    errors.push(`${label} is invalid`);
                }
            });

            currentPanel.querySelectorAll('input[type="file"]').forEach(input => {
                if (!validateFileUpload(input)) {
                    isValid = false;
                    const label = input.closest('.profile-field')?.querySelector('.form-label')?.textContent || input.id;
                    errors.push(`${label} is required`);
                }
            });

            if (!isValid && errors.length > 0) {
                const summaryDiv = document.getElementById('validationSummary');
                const errorList = document.getElementById('errorList');
                if (summaryDiv && errorList) {
                    errorList.innerHTML = errors.map(err => `<li>${escapeHtml(err)}</li>`).join('');
                    summaryDiv.style.display = 'block';
                    const stepCircle = document.getElementById(`ps-sc-${current}`);
                    const stepLabel = document.getElementById(`ps-sl-${current}`);
                    stepCircle?.classList.add('has-error');
                    stepLabel?.classList.add('has-error');
                    setTimeout(() => {
                        stepCircle?.classList.remove('has-error');
                        stepLabel?.classList.remove('has-error');
                    }, 3000);
                }
            }
            stepValidations[current] = isValid;
            return isValid;
        }

        function goTo(n) {
            if (n === current) return;
            const isLocked = form.dataset.locked === 'true';
            if (!isLocked && n > current && !validateCurrentStep()) return;
            totalSteps = document.querySelectorAll('.ps-panel').length;
            const prevPanel = document.getElementById('ps-panel-' + current);
            const prevCircle = document.getElementById('ps-sc-' + current);
            const prevLabel = document.getElementById('ps-sl-' + current);
            prevPanel?.classList.remove('active');
            prevCircle?.classList.remove('active');
            prevLabel?.classList.remove('active');
            if (n > current) {
                prevCircle?.classList.add('done');
                prevLabel?.classList.add('done');
            } else {
                prevCircle?.classList.remove('done');
                prevLabel?.classList.remove('done');
            }
            current = n;
            const nextPanel = document.getElementById('ps-panel-' + current);
            const nextCircle = document.getElementById('ps-sc-' + current);
            const nextLabel = document.getElementById('ps-sl-' + current);
            nextPanel?.classList.add('active');
            if (nextCircle) {
                nextCircle.classList.remove('done');
                nextCircle.classList.add('active');
                nextCircle.setAttribute('aria-selected', 'true');
                nextCircle.removeAttribute('tabindex');
            }
            if (nextLabel) {
                nextLabel.classList.remove('done');
                nextLabel.classList.add('active');
            }
            for (let i = 0; i < totalSteps - 1; i++) {
                const conn = document.getElementById('ps-conn-' + i);
                conn?.classList.toggle('done', i < current);
            }
            document.querySelectorAll('.ps-step-circle').forEach((btn, idx) => {
                if (idx !== current) {
                    btn.setAttribute('aria-selected', 'false');
                    btn.setAttribute('tabindex', '-1');
                }
            });
        }

        function restartForm() {
            const modal = new bootstrap.Modal(document.getElementById('restartModal'));
            modal.show();
            document.getElementById('confirmRestartBtn').onclick = function() {
                const confirmCheckbox = document.getElementById('confirmRestart');
                if (!confirmCheckbox.checked) {
                    showToast('Please confirm that you want to restart', 'error');
                    return;
                }
                const form = document.getElementById('profileForm');
                form.reset();
                document.querySelectorAll('input[type="file"]').forEach(input => {
                    input.value = '';
                    const fileNameSpan = document.getElementById(input.id + 'Name');
                    if (fileNameSpan) fileNameSpan.textContent = 'No file chosen';
                });
                clearValidationErrors();
                current = 0;
                totalSteps = document.querySelectorAll('.ps-panel').length;
                for (let i = 0; i < totalSteps; i++) {
                    const panel = document.getElementById(`ps-panel-${i}`);
                    const circle = document.getElementById(`ps-sc-${i}`);
                    const label = document.getElementById(`ps-sl-${i}`);
                    const connector = document.getElementById(`ps-conn-${i}`);
                    if (i === 0) {
                        panel?.classList.add('active');
                        circle?.classList.remove('done', 'has-error');
                        circle?.classList.add('active');
                        label?.classList.remove('done', 'has-error');
                        label?.classList.add('active');
                    } else {
                        panel?.classList.remove('active');
                        circle?.classList.remove('active', 'done', 'has-error');
                        label?.classList.remove('active', 'done', 'has-error');
                    }
                    connector?.classList.remove('done');
                }
                showToast('Form has been reset', 'info');
                modal.hide();
                confirmCheckbox.checked = false;
            };
        }

        function setupRealTimeValidation() {
            document.querySelectorAll('textarea, input[type="text"], input[type="email"], input[type="tel"]').forEach(field => {
                field.addEventListener('input', function() {
                    validateField(this);
                    const stepCircle = document.getElementById(`ps-sc-${current}`);
                    if (stepCircle) stepCircle.classList.toggle('has-error', this.classList.contains('is-invalid'));
                });
                field.addEventListener('blur', function() {
                    validateField(this);
                });
            });
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', function() {
                    validateFileUpload(this);
                    const fileNameSpan = document.getElementById(this.id + 'Name');
                    if (fileNameSpan) fileNameSpan.textContent = this.files[0]?.name || 'No file chosen';
                });
            });
        }

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.ps-btn-next, .ps-btn-back');
            if (btn) {
                e.preventDefault();
                const target = parseInt(btn.dataset.target, 10);
                if (!isNaN(target)) goTo(target);
            }
            const restartBtn = e.target.closest('.ps-btn-restart');
            if (restartBtn) {
                e.preventDefault();
                restartForm();
            }
        });

        const form = document.getElementById('profileForm');

        function bindSubmitHandler() {
            const submitBtns = form.querySelectorAll('button[type="submit"]');
            let isSubmitting = false;

            function setSubmitButtons(enabled) {
                submitBtns.forEach(btn => {
                    if (!btn.dataset.originalHtml) btn.dataset.originalHtml = btn.innerHTML;
                    btn.disabled = !enabled;
                    btn.innerHTML = enabled ? btn.dataset.originalHtml : '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';
                });
            }

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (form.dataset.locked === 'true') {
                    showToast('The due date has passed. This form is locked and cannot be submitted.', 'error');
                    return;
                }

                document.querySelectorAll('input[name="product_deals_in[]"]').forEach(function(checkbox) {
                    checkbox.removeAttribute('disabled');
                });

                let allValid = true;

                for (let i = 0; i <= current; i++) {
                    const tempCurrent = current;
                    current = i;
                    if (!validateCurrentStep()) {
                        allValid = false;
                        goTo(i);
                        const label = document.getElementById(`ps-sl-${i}`);
                        showToast(`Please fix errors in ${label?.textContent || 'Step ' + (i + 1)}`, 'error');
                        break;
                    }
                    current = tempCurrent;
                }

                if (!allValid) return;
                if (isSubmitting) return;
                isSubmitting = true;
                setSubmitButtons(false);

                const token = getAuthToken();
                if (!token) {
                    showToast('Login token missing. Please login again.', 'error');
                    isSubmitting = false;
                    setSubmitButtons(true);
                    return;
                }

                const fd = new FormData(form);

                try {
                    const res = await fetch(PROFILE_SAVE_URL, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                        },
                        body: fd,
                    });
                    const data = await res.json().catch(() => null);

                    if (!res.ok || (data && data.status === false)) {
                        let errorMsg = (data && (data.message || JSON.stringify(data.errors))) ||
                            ('Error: ' + (res.status || 'unknown'));
                        if (data?.errors && typeof data.errors === 'object') {
                            errorMsg = Object.values(data.errors).join(', ');
                        }
                        showToast(errorMsg, 'error');
                        return;
                    }

                    showToast((data && data.message) || 'Profile saved successfully!', 'success');
                    window.dispatchEvent(new CustomEvent('profileUpdated', {
                        detail: (data && data.data) || {}
                    }));
                    setTimeout(() => window.location.reload(), 2000);
                } catch (err) {
                    showToast(err.message || 'Network error', 'error');
                } finally {
                    isSubmitting = false;
                    setSubmitButtons(true);
                }
            });
        }

        const editModal = document.getElementById('profileEditRequestModal');
        const editTA = document.getElementById('edit_request_detail');
        const sendEditBtn = document.getElementById('sendEditRequest');

        function toggleEditModal(show) {
            if (!editModal) return;
            editModal.classList.toggle('d-none', !show);
            editModal.setAttribute('aria-hidden', show ? 'false' : 'true');
            if (show) editTA?.focus();
        }
        document.querySelector('.profile-edit-btn')?.addEventListener('click', () => toggleEditModal(true));
        editModal?.querySelectorAll('.modal-close-btn').forEach(btn => btn.addEventListener('click', () => toggleEditModal(false)));
        sendEditBtn?.addEventListener('click', () => {
            const detail = editTA?.value.trim();
            if (!detail) {
                showToast('Please enter the details to edit profile.', 'error');
                return;
            }
            showToast('Profile edit request sent.', 'success');
            if (editTA) editTA.value = '';
            toggleEditModal(false);
        });

        async function init() {
            const profile = await fetchProfile();
            document.getElementById('profileLoading')?.remove();
            if (!profile) {
                showToast('Could not load profile.', 'error');
                return;
            }
            profileData = profile;
            populateBaseFields(profile);

            const eventName = profile.event_name || '';
            const sectionConfig = EVENT_SECTIONS[eventName];

            const showCasualGst = sectionConfig ? sectionConfig.showCasualGst !== false : true;
            if (!showCasualGst) {
                document.getElementById('casualGstField')?.classList.add('d-none');
            }

            if (sectionConfig) {
                document.getElementById('dynamicEventSection').innerHTML = buildEventSectionHtml(sectionConfig, profile);

                if (sectionConfig.hasProductCategories) {
                    const categories = await fetchProductCategories();
                    const productsPanel = document.getElementById('productsPanelContent');
                    if (categories && categories.length > 0) {
                        if (productsPanel) {
                            productsPanel.innerHTML = buildProductCategoriesHtml(categories, profile, 1);
                            setupProductCategoryInteractions();
                        }
                    } else {
                        console.warn('Product categories API returned no data:', categories);
                        if (productsPanel) {
                            productsPanel.innerHTML = '<p class="text-danger">Unable to load product categories. Please refresh the page or contact support.</p>';
                        }
                    }
                }
            }
            form.classList.remove('d-none');
            totalSteps = document.querySelectorAll('.ps-panel').length;
            setupRealTimeValidation();
            bindSubmitHandler();
            applyDueDateLock(profile);
        }
        init();
    })();
</script>

<?= $this->endSection() ?>
<?= $this->section('custom-script') ?>
<?= $this->endSection() ?>