<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content') ?>

<div class="content-area">
    <div class="welcome-wrapper">
        <div class="welcome-card">
            <?= $welcome_note ?>
        </div>
    </div>

    <div class="pending-payments-wrapper" style="max-width:900px; margin:20px auto; padding:0 20px;">
        <div id="pendingPaymentsContainer"></div>
    </div>
</div>

<style>
.item-name-tooltip {
    position: relative;
    display: inline-block;
    cursor: default;
}
.item-name-tooltip .tooltip-bubble {
    visibility: hidden;
    opacity: 0;
    position: absolute;
    bottom: 125%;
    left: 0;
    background: #253345;
    color: #fff;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 0.78rem;
    line-height: 1.3;
    white-space: normal;
    width: max-content;
    max-width: 260px;
    z-index: 50;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: opacity 0.15s ease;
    pointer-events: none;
}
.item-name-tooltip .tooltip-bubble::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 14px;
    border-width: 5px;
    border-style: solid;
    border-color: #253345 transparent transparent transparent;
}
.item-name-tooltip:hover .tooltip-bubble {
    visibility: visible;
    opacity: 1;
}
</style>

<script>
function applyHeaderProfile(profile) {
    const nameEl      = document.getElementById('headerUserName');
    const nameEl2     = document.getElementById('headerUserNames');
    const eventNameEl = document.getElementById('headerEventName');

    if (nameEl && profile.contact_person) nameEl.textContent = profile.contact_person;
    if (nameEl2 && profile.contact_person) {
        nameEl2.textContent  = profile.contact_person;
        nameEl2.style.display = 'inline';
    }
    if (eventNameEl && profile.event_name) eventNameEl.textContent = profile.event_name;
}

function truncateChars(text, charLimit) {
    if (!text) return '';
    if (text.length <= charLimit) return text;
    return text.slice(0, charLimit) + '…';
}

function escapeHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function buildPaymentCard(title, icon, payment, currencySymbol) {
    if (!payment) return '';

    const sourceLabel = '';

    const itemsRows = (payment.items || []).map(item => {
        const fullName  = item.item_name || '';
        const shortName = truncateChars(fullName, 5);

        return `
        <tr>
            <td style="padding:8px 12px;">
                <span class="item-name-tooltip">
                    ${escapeHtml(shortName)}
                    <span class="tooltip-bubble">${escapeHtml(fullName)}</span>
                </span>
            </td>
            <td style="padding:8px 12px;text-align:center;">${item.quantity}</td>
            <td style="padding:8px 12px;text-align:right;">${currencySymbol}${payment.total}</td>
            <td style="padding:8px 12px;text-align:right;">
                <button class="btn btn-primary btn-sm" style="border-radius:999px;padding:8px 24px;background:#4a72b8;border:none;"
                    onclick="window.location.href='<?= base_url('additional-furniture') ?>?view=cart'">
                    <i class="bi bi-cart me-1"></i>
                </button>
            </td>
        </tr>
    `;
    }).join('');

    return `
    <div style="background:#fff;border-radius:16px;box-shadow:0 4px 16px rgba(21,50,101,0.07);border:1px solid #eef2f8;margin-bottom:20px;overflow:hidden;">
        <div style="padding:18px 24px;background:#fafbfd;border-bottom:1px solid #eef2f8;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <i class="${icon}" style="color:#4a72b8;font-size:1.3rem;"></i>
                <strong style="color:#253345;font-size:1rem;">${title}</strong>
                ${sourceLabel}
            </div>
           
        </div>
        <div style="padding:20px 24px;">
            <div class="table-responsive">
                <table style="width:100%;border-collapse:collapse;margin-bottom:16px;">
                    <thead>
                        <tr style="background:#f0f4fa;">
                            <th style="padding:8px 12px;text-align:left;font-size:0.82rem;color:#6b7891;">Item</th>
                            <th style="padding:8px 12px;text-align:center;font-size:0.82rem;color:#6b7891;">Qty</th>
                            <th style="padding:8px 12px;text-align:right;font-size:0.82rem;color:#6b7891;">Total</th>
                            <th style="padding:8px 12px;text-align:right;font-size:0.82rem;color:#6b7891;">Action</th>
                        </tr>
                    </thead>
                    <tbody>${itemsRows}</tbody>
                </table>
            </div>
           
        </div>
    </div>`;
}

async function loadPendingPayments() {
    const token     = localStorage.getItem('api_token');
    const container = document.getElementById('pendingPaymentsContainer');
    if (!token || !container) return;

    try {
        const response = await fetch(`${API_BASE_URL}/v1/dashboard/pending-payments`, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json',
            },
        });

        if (response.status === 401) {
            localStorage.removeItem('api_token');
            window.location.href = '<?= base_url('login') ?>';
            return;
        }

        const result = await response.json();
        if (!result.status) return;

        const { payments, currency_symbol, is_raw_space } = result.data;
        let html = '';

        if (payments.furniture) {
            html += buildPaymentCard('Furniture Pending Payment', 'bi bi-box-seam', payments.furniture, currency_symbol);
        }

        if (is_raw_space && payments.electricity) {
            html += buildPaymentCard('Electricity Pending Payment', 'bi bi-lightning-charge', payments.electricity, currency_symbol);
        }

        if (!html) {
            html = `
            <div style="background:#f8f9fc;border-radius:12px;padding:24px;text-align:center;color:#6b7891;">
                <i class="bi bi-check-circle" style="font-size:2rem;color:#4caf50;display:block;margin-bottom:8px;"></i>
                No pending payments. You're all caught up!
            </div>`;
        }

        container.innerHTML = html;

    } catch (error) {
        console.error('loadPendingPayments error:', error);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    loadPendingPayments();
});
</script>

<?= $this->endSection() ?>