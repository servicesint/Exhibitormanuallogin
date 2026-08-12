<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content') ?>

<style>
    .ticket-page-wrapper {
        max-width: 1000px;
        margin: 0 auto;
        padding: 28px 20px 48px;
    }

    .ticket-card {
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 14px 36px rgba(21, 50, 101, 0.10);
        overflow: hidden;
        border: 1px solid #dfe6f2;
    }

    .ticket-card-head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding: 28px 32px 22px;
        background: linear-gradient(135deg, #eef3fc 0%, #fafbfd 100%);
        border-bottom: 1px solid #e2e8f1;
    }

    .ticket-kicker {
        display: inline-block;
        margin-bottom: 8px;
        color: #4a72b8;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .ticket-title {
        font-size: 1.55rem;
        margin: 0 0 8px;
        color: #253345;
        line-height: 1.2;
        font-weight: 700;
    }

    .ticket-card-head p {
        margin: 0;
        color: #6b7891;
        max-width: 480px;
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .ticket-head-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
    }

    .ticket-summary-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #d9e6f7;
        color: #4a72b8;
        font-weight: 700;
        font-size: 0.86rem;
        white-space: nowrap;
    }

    .apply-btn {
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

    .apply-btn:hover {
        background: #3d5f9c;
        transform: translateY(-1px);
    }

    .apply-btn.disabled-btn {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
        transform: none;
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

    .ticket-history-scroll {
        overflow-x: auto;
    }

    .ticket-history-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.86rem;
    }

    .ticket-history-table th {
        text-align: left;
        padding: 13px 20px;
        background: #f8f9fc;
        color: #5c6b81;
        font-weight: 600;
        border-bottom: 1px solid #eef2f8;
        white-space: nowrap;
    }

    .ticket-history-table td {
        padding: 15px 20px;
        border-bottom: 1px solid #f1f4f9;
        color: #2b3a4f;
        vertical-align: top;
    }

    .ticket-history-table tr:last-child td {
        border-bottom: none;
    }

    .ticket-history-table tr:hover td {
        background: #fafbfe;
    }

    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 11px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .status-chip.pending {
        background: #fdf3e0;
        color: #a5710f;
    }

    .status-chip.approved {
        background: #e6f4ea;
        color: #2f7a42;
    }

    .status-chip.rejected {
        background: #fdecec;
        color: #c4574f;
    }

    .ticket-history-empty {
        padding: 40px 20px;
        text-align: center;
        color: #97a2b3;
        font-size: 0.9rem;
    }

    #ticketModal .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(20, 40, 80, 0.2);
    }

    #ticketModal .modal-header {
        background: #fafbfd;
        border-bottom: 1px solid #eef2f8;
        padding: 20px 26px;
    }

    #ticketModal .modal-title {
        font-weight: 700;
        color: #253345;
        font-size: 1.15rem;
        display: flex;
        align-items: center;
        gap: 9px;
    }

    #ticketModal .modal-title i {
        color: #4a72b8;
    }

    #ticketModal .modal-body {
        padding: 26px;
    }

    .ticket-section-title {
        display: flex;
        align-items: center;
        gap: 9px;
        font-weight: 600;
        color: #2b3a4f;
        font-size: 0.96rem;
        margin: 20px 0 14px;
    }

    .ticket-section-title:first-child {
        margin-top: 0;
    }

    .ticket-section-title i {
        color: #5b7bab;
        font-size: 1.02rem;
    }

    .ticket-two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .ticket-three-col {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 16px;
    }

    .ticket-group {
        margin-bottom: 16px;
    }

    .ticket-group label {
        display: block;
        margin-bottom: 7px;
        color: #3c4a5e;
        font-weight: 600;
        font-size: 0.88rem;
    }

    .ticket-input,
    .ticket-select {
        display: block;
        width: 100%;
        padding: 11px 15px;
        border: 1px solid #e2e8f1;
        border-radius: 11px;
        background: #f8f9fc;
        font-size: 0.9rem;
        color: #2b3a4f;
        transition: border-color 0.15s ease, background 0.15s ease;
    }

    .ticket-input::placeholder {
        color: #a3aec2;
    }

    .ticket-input:focus,
    .ticket-select:focus {
        outline: none;
        box-shadow: none;
        border-color: #93b4e8;
        background: #fff;
    }

    .ticket-input.is-invalid,
    .ticket-select.is-invalid {
        border-color: #e6a3a3;
        background: #fdf6f6;
    }

    .ticket-input:disabled,
    .ticket-select:disabled {
        background: #eef1f6;
        color: #97a2b3;
        cursor: not-allowed;
    }

    .validation-error {
        color: #c4574f;
        font-size: 0.78em;
        margin-top: 5px;
        display: block;
    }

    .qty-box {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 7px;
        border: 1px solid #e2e8f1;
        border-radius: 11px;
        background: #f8f9fc;
        max-width: 240px;
        transition: border-color 0.15s ease;
    }

    .qty-box:focus-within {
        border-color: #93b4e8;
        background: #fff;
    }

    .qty-btn {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: none;
        background: #eaf0fb;
        color: #4a72b8;
        font-size: 1.02rem;
        font-weight: 700;
        cursor: pointer;
        flex-shrink: 0;
        transition: background 0.15s ease;
    }

    .qty-btn:hover {
        background: #dbe6f8;
    }

    .qty-btn.disabled-btn {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .qty-input {
        border: none;
        background: transparent;
        text-align: center;
        font-size: 0.98rem;
        font-weight: 600;
        color: #2b3a4f;
        width: 100%;
        padding: 6px 0;
    }

    .qty-input:focus {
        outline: none;
        box-shadow: none;
    }

    .qty-input:disabled {
        background: transparent;
        color: #97a2b3;
    }

    .qty-hint {
        margin-top: 7px;
        color: #8792a3;
        font-size: 0.8rem;
    }

    .ticket-note {
        background: #f2f5fb;
        border: 1px solid #e2e8f1;
        border-radius: 13px;
        padding: 13px 15px;
        color: #5c6b81;
        font-size: 0.84rem;
        line-height: 1.55;
        margin-top: 4px;
    }

    #ticketModal .modal-footer {
        border-top: 1px solid #eef2f8;
        padding: 16px 26px;
        background: #fff;
    }

    .ticket-btn {
        min-width: 160px;
        border-radius: 999px;
        padding: 11px 22px;
        font-size: 0.9rem;
        font-weight: 700;
        background: #4a72b8;
        border: none;
        color: #fff;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .ticket-btn:hover {
        background: #3d5f9c;
    }

    .ticket-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .form-readonly {
        opacity: 0.7;
        pointer-events: none;
    }

    .form-readonly .ticket-btn,
    .form-readonly .apply-btn,
    .form-readonly .qty-btn {
        pointer-events: none;
        opacity: 0.5;
    }

    .form-readonly .ticket-input,
    .form-readonly .ticket-select {
        pointer-events: none;
        background-color: #e9ecef;
    }

    @media (max-width: 700px) {
        .ticket-card-head {
            flex-direction: column;
            align-items: stretch;
        }

        .ticket-head-right {
            align-items: stretch;
        }

        .apply-btn {
            justify-content: center;
        }

        .ticket-two-col,
        .ticket-three-col {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="content-area">
    <div class="ticket-page-wrapper">

        <div id="readonlyMessage" class="readonly-message" style="display:none;">
            <i class="bi bi-info-circle"></i>
            <span id="readonlyMessageText">Visitor Invitation form is currently closed. You can only view your existing requests. New requests are not allowed.</span>
        </div>

        <div class="ticket-card">
            <div class="ticket-card-head">
                <div>
                    <span class="ticket-kicker">Visitor Passes</span>
                    <h4 class="ticket-title">Visitor Invitation Tickets</h4>
                    <p>Track your submitted invitation ticket requests, their delivery location, and approval status.</p>
                </div>
                <div class="ticket-head-right">
                    <span class="ticket-summary-pill" id="dueDatePill"><i class="bi bi-calendar-event"></i> Due:&nbsp;<span id="visitorInvitationDueDate">--</span></span>
                    <span class="ticket-summary-pill" id="totalQtyPill"><i class="bi bi-ticket-perforated"></i> Total Quantity requested: 0</span>
                    <button type="button" class="apply-btn" id="applyTicketsBtn" data-bs-toggle="modal" data-bs-target="#ticketModal">
                        <i class="bi bi-plus-circle"></i> <span id="applyBtnText">Apply for Tickets</span>
                    </button>
                </div>
            </div>
            <div class="ticket-history-scroll">
                <table class="ticket-history-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Quantity</th>
                            <th>Location</th>
                            <th>Pincode</th>
                            
                        </tr>
                    </thead>
                    <tbody id="ticketHistoryBody">
                        <tr>
                            <td colspan="5" class="ticket-history-empty">Loading your requests...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketModalLabel">
                    <i class="bi bi-ticket-perforated"></i> <span id="modalTitle">Apply for Invitation Tickets</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="ticketForm" method="post">
                    <input type="hidden" id="requestId" name="request_id" value="">

                    <div class="ticket-section-title"><i class="bi bi-ticket-perforated"></i> Ticket Quantity</div>
                    <div class="ticket-group">
                        <label for="ticketQuantity">Number of Invitation Tickets</label>
                        <div class="qty-box">
                            <button type="button" class="qty-btn" id="qtyMinus">−</button>
                            <input type="text" class="qty-input" id="ticketQuantity" name="quantity" inputmode="numeric" pattern="[0-9]*" value="1" maxlength="4">
                            <button type="button" class="qty-btn" id="qtyPlus">+</button>
                        </div>
                        <div class="qty-hint">Maximum 2000 tickets. You can type a number directly or use the +/− buttons.</div>
                    </div>

                    <div class="ticket-section-title"><i class="bi bi-geo-alt"></i> Address Details</div>
                    <div class="ticket-group">
                        <label for="addressLine">Address</label>
                        <input type="text" id="addressLine" name="address" class="ticket-input" placeholder="House no., street, area">
                    </div>
                    <div class="ticket-three-col">
                        <div class="ticket-group">
                            <label for="addressCountry">Country</label>
                            <select id="addressCountry" name="country" class="ticket-select">
                                <option value="">Loading...</option>
                            </select>
                        </div>
                        <div class="ticket-group">
                            <label for="addressState">State</label>
                            <select id="addressState" name="state" class="ticket-select" disabled>
                                <option value="">Select State</option>
                            </select>
                        </div>
                        <div class="ticket-group">
                            <label for="addressCity">City</label>
                            <select id="addressCity" name="city" class="ticket-select" disabled>
                                <option value="">Select City</option>
                            </select>
                        </div>
                    </div>
                    <div class="ticket-two-col">
                        <div class="ticket-group">
                            <label for="addressPincode">Pincode</label>
                            <input type="text" id="addressPincode" name="pincode" class="ticket-input" inputmode="numeric" pattern="[0-9]*" maxlength="6" placeholder="Enter pincode">
                        </div>
                    </div>

                    <div class="ticket-note">Tickets will be generated and sent once your request is approved.</div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="ticketForm" class="btn ticket-btn" id="submitTicketBtn">Submit Request</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>
<script>
    $(function() {
        const $ticketForm = $("#ticketForm");
        const API_BASE_URL = '<?= env('API_BASE_URL') ?>';
        const TICKET_URL = `${API_BASE_URL}/v1/dashboard/visitor-tickets`;
        const TICKET_LIST_URL = `${API_BASE_URL}/v1/dashboard/visitor-tickets/list`;
        const TICKET_UPDATE_URL = `${API_BASE_URL}/v1/dashboard/visitor-tickets/update`;
        const COUNTRIES_URL = `${API_BASE_URL}/v1/locations/countries`;
        const STATES_URL = `${API_BASE_URL}/v1/locations/states`;
        const CITIES_URL = `${API_BASE_URL}/v1/locations/cities`;
        const PROFILE_URL = `${API_BASE_URL}/v1/profile`;

        // Only India is allowed as a country for this form. Its id gets
        // cached here once loadCountries() resolves it from the API response.
        const ALLOWED_COUNTRY_NAME = 'india';
        let allowedCountryId = null;

        const MAX_QTY = 2000;
        const $qtyInput = $("#ticketQuantity");
        let isViewOnly = false;
        let statusChecked = false;
        let existingRequest = null;
        let isUpdateMode = false;

        function getTicketFormStatus() {
            if (window.getFormStatus && typeof window.getFormStatus === 'function') {
                return window.getFormStatus('invitation_tickets');
            }
            if (window.manualSetup) {
                const enableDisable = window.manualSetup.online_forms_enable_disable || {};
                const openClose = window.manualSetup.online_forms_open_close || {};
                const enabled = parseInt(enableDisable.invitation_tickets, 10) === 1;
                const open = parseInt(openClose.invitation_tickets, 10) === 1;
                if (!open) return 'disabled';
                return enabled ? 'enabled_open' : 'enabled_closed';
            }
            return 'enabled_open';
        }

        function applyViewOnlyMode(message) {
            isViewOnly = true;
            const msgDiv = document.getElementById('readonlyMessage');
            if (msgDiv) {
                msgDiv.style.display = 'block';
            }
            const applyBtn = document.getElementById('applyTicketsBtn');
            if (applyBtn) {
                applyBtn.style.display = 'none';
            }
            document.querySelectorAll('#ticketForm .ticket-input, #ticketForm .ticket-select, #ticketForm .qty-input').forEach(el => {
                el.setAttribute('readonly', true);
                el.setAttribute('disabled', true);
            });
            document.querySelectorAll('.qty-btn').forEach(btn => {
                btn.classList.add('disabled-btn');
                btn.setAttribute('disabled', true);
            });
            const submitBtn = document.getElementById('submitTicketBtn');
            if (submitBtn) {
                submitBtn.setAttribute('disabled', true);
                submitBtn.style.opacity = '0.5';
                submitBtn.style.cursor = 'not-allowed';
            }
            if (window.showToast) {
                window.showToast(message, 'warning');
            }
        }

        function checkTicketFormStatus() {
            if (statusChecked) return;
            statusChecked = true;
            const status = getTicketFormStatus();
            console.log('Visitor Invitation Form Status:', status);
            if (status === 'disabled') {
                if (window.showToast) {
                    window.showToast('This form is currently disabled.', 'error');
                } else {
                    alert('This form is currently disabled.');
                }
                setTimeout(() => {
                    if (window.redirectToDashboard) {
                        window.redirectToDashboard();
                    } else {
                        window.location.href = (window.BASE_URL || '') + '/dashboard';
                    }
                }, 1500);
                return false;
            }
            if (status === 'enabled_closed') {
                applyViewOnlyMode('View only mode. You can view your existing requests only.');
            }
            return true;
        }

        function getAuthToken() {
            return localStorage.getItem('api_token') || sessionStorage.getItem('api_token') || '';
        }

        function showToastMsg(message, type = 'success') {
            if (window.showToast) {
                window.showToast(message, type);
            } else if (typeof $.toast === 'function') {
                $.toast({
                    heading: type === 'error' ? 'Error' : 'Success',
                    text: message,
                    icon: type === 'error' ? 'error' : 'success',
                    showHideTransition: 'slide',
                    position: 'top-right',
                    hideAfter: 3500,
                    loader: false,
                });
            } else {
                alert(message);
            }
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr.replace(' ', 'T'));
            if (isNaN(d.getTime())) return dateStr;
            return d.toLocaleDateString('en-IN', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function formatDueDate(dateStr) {
            if (!dateStr) return '--';
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return dateStr;
            return d.toLocaleDateString('en-IN', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function loadVisitorInvitationDueDate() {
            $.ajax({
                url: PROFILE_URL,
                type: 'GET',
                headers: {
                    Authorization: getAuthToken() ? `Bearer ${getAuthToken()}` : ''
                },
                dataType: 'json',
                success(response) {
                    const data = response?.data || {};
                    const dueDate = data.visitor_invitation_due_date || null;
                    $("#visitorInvitationDueDate").text(formatDueDate(dueDate));
                    if (dueDate) {
                        const dueDateObj = new Date(dueDate);
                        if (!isNaN(dueDateObj.getTime())) {
                            const today = new Date();
                            today.setHours(0, 0, 0, 0);
                            dueDateObj.setHours(0, 0, 0, 0);
                            if (today > dueDateObj && !isViewOnly) {
                                applyViewOnlyMode('The due date has passed. View only mode.');
                            }
                        }
                    }
                },
                error() {
                    $("#visitorInvitationDueDate").text('--');
                }
            });
        }

        function loadTicketHistory() {
            $.ajax({
                url: TICKET_LIST_URL,
                type: 'GET',
                headers: {
                    Authorization: getAuthToken() ? `Bearer ${getAuthToken()}` : ''
                },
                dataType: 'json',
                success(response) {
                    const data = response?.data || {};
                    const requests = data.requests || [];
                    const totalQty = data.total_quantity || 0;
                    $("#totalQtyPill").html(`<i class="bi bi-ticket-perforated"></i> Total Quantity requested: ${totalQty}`);
                    const $body = $("#ticketHistoryBody");
                    if (!requests.length) {
                        $body.html('<tr><td colspan="5" class="ticket-history-empty">No ticket requests submitted yet. Click "Apply for Tickets" to get started.</td></tr>');
                        existingRequest = null;
                        isUpdateMode = false;
                        updateApplyButton(false);
                        return;
                    }

                    let html = '';
                    requests.forEach(row => {
                        const status = String(row.status || 'pending').toLowerCase();
                        html += `
                            <tr>
                                <td>${formatDate(row.created_at)}</td>
                                <td>${escapeHtml(row.quantity)}</td>
                                <td>${escapeHtml(row.location || '-')}</td>
                                <td>${escapeHtml(row.pincode || '-')}</td>
                               
                            </tr>`;
                    });
                    $body.html(html);

                    // Store the first request for update
                    if (requests.length > 0) {
                        const firstReq = requests[0];
                        existingRequest = {
                            id: firstReq.id,
                            quantity: firstReq.quantity,
                            address: firstReq.location || '',
                            country: firstReq.country_id || '',
                            state: firstReq.state_id || '',
                            city: firstReq.city_id || '',
                            pincode: firstReq.pincode || ''
                        };
                        isUpdateMode = true;
                        updateApplyButton(true);
                    }
                },
                error() {
                    $("#ticketHistoryBody").html('<tr><td colspan="5" class="ticket-history-empty">Failed to load your requests.</td></tr>');
                }
            });
        }

        function updateApplyButton(hasExisting) {
            const applyBtn = document.getElementById('applyTicketsBtn');
            const btnText = document.getElementById('applyBtnText');
            const modalTitle = document.getElementById('modalTitle');

            if (hasExisting) {
                btnText.textContent = 'Update Request';
                modalTitle.textContent = 'Update Invitation Tickets';
                if (applyBtn) {
                    applyBtn.innerHTML = `<i class="bi bi-pencil-square"></i> <span id="applyBtnText">Update Request</span>`;
                }
                isUpdateMode = true;
            } else {
                btnText.textContent = 'Apply for Tickets';
                modalTitle.textContent = 'Apply for Invitation Tickets';
                if (applyBtn) {
                    applyBtn.innerHTML = `<i class="bi bi-plus-circle"></i> <span id="applyBtnText">Apply for Tickets</span>`;
                }
                isUpdateMode = false;
            }
        }

        function clampQty(value) {
            let num = parseInt(value, 10);
            if (isNaN(num) || num < 1) num = 1;
            if (num > MAX_QTY) num = MAX_QTY;
            return num;
        }

        $(document).on('input', '#ticketQuantity', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $(document).on('blur', '#ticketQuantity', function() {
            $(this).val(clampQty($(this).val()));
        });

        $("#qtyMinus").on('click', function() {
            if (isViewOnly) return;
            $qtyInput.val(clampQty((parseInt($qtyInput.val(), 10) || 1) - 1));
        });

        $("#qtyPlus").on('click', function() {
            if (isViewOnly) return;
            $qtyInput.val(clampQty((parseInt($qtyInput.val(), 10) || 1) + 1));
        });

        $(document).on('input', '#addressPincode', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // ---- Country: locked to India only ----
        // Instead of listing every country returned by the API, we find the
        // "India" entry, show only that as the single option, and auto-select
        // it. The states dropdown is then loaded on behalf of India only, so
        // no other country's states/cities can ever surface in this form.
        function loadCountries() {
            $.ajax({
                url: COUNTRIES_URL,
                type: 'GET',
                headers: {
                    Authorization: getAuthToken() ? `Bearer ${getAuthToken()}` : ''
                },
                dataType: 'json',
                success(response) {
                    const countries = response?.data || [];
                    const $country = $("#addressCountry");
                    const india = countries.find(
                        c => String(c.name || '').trim().toLowerCase() === ALLOWED_COUNTRY_NAME
                    );

                    $country.html('');
                    if (india) {
                        allowedCountryId = india.id;
                        $country.append(`<option value="${india.id}" selected>${india.name}</option>`);
                        // Auto-load states for India right away.
                        $country.val(india.id).trigger('change');
                    } else {
                        $country.append('<option value="">India not available</option>');
                        showToastMsg('India could not be found in the location list.', 'error');
                    }
                },
                error() {
                    showToastMsg('Failed to load countries.', 'error');
                }
            });
        }

        $(document).on('change', '#addressCountry', function() {
            const countryId = $(this).val();
            const $state = $("#addressState");
            const $city = $("#addressCity");
            $state.html('<option value="">Select State</option>').prop('disabled', true);
            $city.html('<option value="">Select City</option>').prop('disabled', true);

            // Safety net: only ever fetch states for the allowed (India) id.
            if (!countryId || (allowedCountryId && countryId != allowedCountryId)) return;

            $.ajax({
                url: STATES_URL,
                type: 'GET',
                data: {
                    country_id: countryId
                },
                headers: {
                    Authorization: getAuthToken() ? `Bearer ${getAuthToken()}` : ''
                },
                dataType: 'json',
                success(response) {
                    const states = response?.data || [];
                    states.forEach(s => {
                        $state.append(`<option value="${s.id}">${s.name}</option>`);
                    });
                    $state.prop('disabled', false);
                    if (existingRequest && existingRequest.country == countryId) {
                        $state.val(existingRequest.state);
                    }
                },
                error() {
                    showToastMsg('Failed to load states.', 'error');
                }
            });
        });

        $(document).on('change', '#addressState', function() {
            const stateId = $(this).val();
            const $city = $("#addressCity");
            $city.html('<option value="">Select City</option>').prop('disabled', true);
            if (!stateId) return;
            $.ajax({
                url: CITIES_URL,
                type: 'GET',
                data: {
                    state_id: stateId
                },
                headers: {
                    Authorization: getAuthToken() ? `Bearer ${getAuthToken()}` : ''
                },
                dataType: 'json',
                success(response) {
                    const cities = response?.data || [];
                    cities.forEach(c => {
                        $city.append(`<option value="${c.id}">${c.name}</option>`);
                    });
                    $city.prop('disabled', false);
                    if (existingRequest && existingRequest.state == stateId) {
                        $city.val(existingRequest.city);
                    }
                },
                error() {
                    showToastMsg('Failed to load cities.', 'error');
                }
            });
        });
        loadCountries();
        loadTicketHistory();
        loadVisitorInvitationDueDate();
        $ticketForm.validate({
            errorClass: 'validation-error',
            errorElement: 'div',
            errorPlacement(error, element) {
                error.insertAfter(element.closest('.ticket-group').find('.ticket-input, .ticket-select, .qty-box'));
            },
            highlight(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight(element) {
                $(element).removeClass('is-invalid');
            },
            rules: {
                quantity: {
                    required: true,
                    digits: true,
                    min: 1,
                    max: MAX_QTY
                },
                address: {
                    required: true,
                    minlength: 3
                },
                country: {
                    required: true
                },
                state: {
                    required: true
                },
                city: {
                    required: true
                },
                pincode: {
                    required: true,
                    digits: true,
                    minlength: 6,
                    maxlength: 6
                }
            },
            messages: {
                quantity: {
                    required: 'Enter number of tickets',
                    digits: 'Enter a valid number',
                    min: 'Minimum 1 ticket required',
                    max: `Maximum ${MAX_QTY} tickets allowed`
                },
                address: {
                    required: 'Enter address',
                    minlength: 'Enter at least 3 characters'
                },
                country: {
                    required: 'Select a country'
                },
                state: {
                    required: 'Select a state'
                },
                city: {
                    required: 'Select a city'
                },
                pincode: {
                    required: 'Enter pincode',
                    digits: 'Enter digits only',
                    minlength: 'Enter a valid 6-digit pincode',
                    maxlength: 'Enter a valid 6-digit pincode'
                }
            },
            submitHandler(form) {
                if (isViewOnly) {
                    showToastMsg('Cannot submit. Form is closed.', 'warning');
                    return false;
                }
                if (isUpdateMode && existingRequest) {
                    updateTicketRequest(form);
                } else {
                    submitTicketRequest(form);
                }
            }
        });

        function resetModalForm() {
            $ticketForm[0].reset();
            $("#addressState").html('<option value="">Select State</option>').prop('disabled', true);
            $("#addressCity").html('<option value="">Select City</option>').prop('disabled', true);
            $qtyInput.val(1);
            $ticketForm.validate().resetForm();
            $ticketForm.find('.is-invalid').removeClass('is-invalid');
            $("#requestId").val('');
            isUpdateMode = false;
            updateApplyButton(!!existingRequest);
            // Re-select India and reload its states after a reset, since the
            // native form reset() would otherwise clear the country select too.
            if (allowedCountryId) {
                $("#addressCountry").val(allowedCountryId).trigger('change');
            }
        }

        function populateModalForUpdate() {
            if (!existingRequest) return;

            console.log('Existing Request:', existingRequest); // Debug

            $("#requestId").val(existingRequest.id);
            $qtyInput.val(existingRequest.quantity || 1);
            $("#addressLine").val(existingRequest.address || '');
            $("#addressPincode").val(existingRequest.pincode || '');

            // Country is always India for this form, so just reload states
            // for the locked-in country id and let the existing state/city
            // (if any) get selected once the lists arrive.
            if (allowedCountryId) {
                $("#addressCountry").val(allowedCountryId).trigger('change');

                setTimeout(function() {
                    if (existingRequest.state) {
                        $("#addressState").val(existingRequest.state).trigger('change');
                    }
                    setTimeout(function() {
                        if (existingRequest.city) {
                            $("#addressCity").val(existingRequest.city);
                        }
                    }, 300);
                }, 300);
            }
        }

        function submitTicketRequest(form) {
            const formData = $(form).serialize();
            const token = getAuthToken();

            $.ajax({
                url: TICKET_URL,
                type: 'POST',
                headers: {
                    Authorization: token ? `Bearer ${token}` : ''
                },
                data: formData,
                dataType: 'json',
                success(response) {
                    const message = response.message || (response.status ? 'Request submitted successfully' : 'Submission failed');
                    if (response.status) {
                        showToastMsg(message, 'success');
                        resetModalForm();
                        loadTicketHistory();
                        const modalEl = document.getElementById('ticketModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                    } else {
                        showToastMsg(message, 'error');
                    }
                },
                error(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'Server error. Please try again.';
                    showToastMsg(errorMessage, 'error');
                }
            });
            return false;
        }

        function updateTicketRequest(form) {
            const formData = $(form).serialize();
            const token = getAuthToken();

            console.log('Form Data:', formData); // Debug - check what's being sent

            $.ajax({
                url: `${API_BASE_URL}/v1/dashboard/visitor-tickets/update`,
                type: 'POST',
                headers: {
                    'Authorization': token ? `Bearer ${token}` : '',
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json'
                },
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log('Update Response:', response);
                    if (response.status) {
                        showToastMsg(response.message || 'Request updated successfully', 'success');
                        resetModalForm();
                        loadTicketHistory();
                        const modalEl = document.getElementById('ticketModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                    } else {
                        showToastMsg(response.message || 'Update failed', 'error');
                    }
                },
                error: function(xhr) {
                    console.log('Error Response:', xhr);
                    let errorMessage = 'Server error. Please try again.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors);
                            errorMessage = errors.join(', ');
                        }
                    }
                    showToastMsg(errorMessage, 'error');
                }
            });
            return false;
        }

        // When modal is shown, populate if update mode
        document.getElementById('ticketModal').addEventListener('show.bs.modal', function() {
            if (isUpdateMode && existingRequest) {
                populateModalForUpdate();
            } else {
                resetModalForm();
            }
        });

        document.getElementById('ticketModal').addEventListener('hidden.bs.modal', resetModalForm);

        setTimeout(function() {
            checkTicketFormStatus();
        }, 800);

        document.addEventListener('layoutConfigReady', function() {
            statusChecked = false;
            checkTicketFormStatus();
        });

        window.loadTicketHistory = loadTicketHistory;
        window.checkTicketFormStatus = checkTicketFormStatus;
        window.loadVisitorInvitationDueDate = loadVisitorInvitationDueDate;

    });
</script>

<?= $this->endSection() ?>
<?= $this->section('custom-script') ?>
<?= $this->endSection() ?>
