<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content') ?>

<style>
    .readonly-message {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 20px;
        display: none;
    }

    .cart-item-image {
        position: relative;
        overflow: hidden;
    }

    .cart-item-image.out-of-stock img {
        filter: blur(2.5px);
        opacity: 0.55;
    }

    .cart-item-image .out-of-stock-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background: rgba(0, 0, 0, 0.35);
        color: #fff;
        font-size: 0.55rem;
        font-weight: 700;
        line-height: 1.05;
        border-radius: 10px;
    }

    .additional-furniture-cart-item {
        align-items: flex-start;
    }

    .cart-item-actions {
        align-self: center;
        flex-shrink: 0;
    }

    .furn-img-wrap {
        position: relative;
    }

    .furn-img-wrap.out-of-stock .view-img {
        filter: blur(2.5px);
        opacity: 0.6;
    }

    .out-of-stock-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background: rgba(0, 0, 0, 0.25);
        color: #fff;
        font-size: 15px !important;
        font-weight: 700;
        line-height: 1.1;
        padding: 4px;
        border-radius: 12px;
    }

    tr.out-of-stock-row .qty-input,
    tr.out-of-stock-row .input-group .btn,
    tr.out-of-stock-row .btn-add {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
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

    .btn-add.disabled-btn {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .qty-btn.disabled-btn {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .furniture-section-hidden {
        display: none !important;
    }

    .cart-section-hidden {
        display: none !important;
    }

    .content-area {
        max-width: 100%;
        margin: 0;
        padding: 28px 28px 48px;
    }

    .furn-wrapper {
        width: 100%;
    }

    .furn-wrapper {
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 14px 36px rgba(21, 50, 101, 0.10);
        border: 1px solid #dfe6f2;
        overflow: hidden;
        padding: 28px 32px 32px;
    }

    .furn-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 22px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eef2f8;
    }

    .furn-header h4 {
        font-size: 1.45rem;
        margin: 0;
        color: #253345;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .furn-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .furn-actions .btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border-radius: 999px;
        padding: 9px 18px;
        font-size: 0.86rem;
        font-weight: 600;
        border: 1px solid #e2e8f1;
        background: #f8f9fc;
        color: #3c4a5e;
        transition: background 0.15s ease, transform 0.15s ease;
    }

    .furn-actions .btn:hover {
        background: #eef1f6;
        transform: translateY(-1px);
    }

    .furn-actions .btn-success {
        background: #4a72b8;
        border-color: #4a72b8;
        color: #fff;
    }

    .furn-actions .btn-success:hover {
        background: #3d5f9c;
    }

    .furn-actions .btn-outline-dark {
        background: #fff;
        border-color: #d9e6f7;
        color: #4a72b8;
    }

    .furn-actions .btn-outline-dark:hover {
        background: #eaf0fb;
    }

    .furn-actions .btn.disabled-btn {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .furn-actions .btn-warning {
        background: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }

    .furn-actions .btn-warning:hover {
        background: #e0a800;
        border-color: #e0a800;
    }

    .furn-actions .btn-warning .badge {
        font-size: 0.65rem;
        padding: 2px 6px;
    }

    #cartCountBadge,
    #pendingCountBadge {
        border-radius: 999px;
        font-size: 0.72rem;
        padding: 3px 7px;
    }

    .furniture-end {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        background: #eaf0fb;
        border-radius: 999px;
        font-size: 0.9rem;
        color: #2b3a4f;
        font-weight: 500;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .furniture-end i {
        color: #5b7bab;
    }

    .furniture-end strong {
        font-weight: 700;
    }

    .pending-orders-modal .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(20, 40, 80, 0.2);
    }

    .pending-orders-modal .modal-header {
        background: #fafbfd;
        border-bottom: 1px solid #eef2f8;
        padding: 20px 26px;
    }

    .pending-orders-modal .modal-title {
        font-weight: 700;
        color: #253345;
        font-size: 1.1rem;
    }

    .pending-orders-modal .modal-body {
        padding: 24px 26px;
    }

    .pending-order-item {
        border-bottom: 1px solid #eef2f8;
        padding: 15px 0;
    }

    .pending-order-item:last-child {
        border-bottom: none;
    }

    .pending-order-item .order-number {
        font-weight: 700;
        color: #253345;
    }

    .pending-order-item .order-total {
        font-weight: 600;
        color: #4a72b8;
    }

    .pending-order-item .order-date {
        color: #8792a3;
        font-size: 0.85rem;
    }

    .pending-order-item .status-badge {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 999px;
    }

    .pending-order-item .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }

    .pending-order-item .status-badge.failed {
        background: #f8d7da;
        color: #721c24;
    }

    .pending-count-badge {
        background: #dc3545;
        color: #fff;
        border-radius: 999px;
        padding: 2px 8px;
        font-size: 0.7rem;
        margin-left: 4px;
    }

    .no-pending-orders {
        text-align: center;
        padding: 40px 20px;
        color: #6b7891;
    }

    .no-pending-orders i {
        font-size: 48px;
        color: #d7dce4;
        margin-bottom: 15px;
    }

    .btn-pay-now {
        border-radius: 999px !important;
        padding: 4px 14px !important;
        font-size: 0.8rem !important;
        font-weight: 600 !important;
        background: #28a745 !important;
        border: none !important;
        color: #fff !important;
    }

    .btn-pay-now:hover {
        background: #218838 !important;
    }

    .furn-table,
    #ordersPage .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.86rem;
    }

    .furn-table thead th,
    #ordersPage .table thead th {
        text-align: left;
        padding: 13px 16px;
        background: #f8f9fc;
        color: #5c6b81;
        font-weight: 600;
        border: none;
        border-bottom: 1px solid #eef2f8;
        white-space: nowrap;
    }

    .furn-table tbody td,
    #ordersPage .table tbody td {
        padding: 13px 16px;
        border: none;
        border-bottom: 1px solid #f1f4f9;
        color: #2b3a4f;
        vertical-align: middle;
    }

    .furn-table tbody tr:last-child td,
    #ordersPage .table tbody tr:last-child td {
        border-bottom: none;
    }

    .furn-table tbody tr:hover td,
    #ordersPage .table tbody tr:hover td {
        background: #fafbfe;
    }

    .furn-img-wrap {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f1;
        background: #f8f9fc;
    }

    .furn-img-wrap .view-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
        display: block;
    }

    .item-name-cell {
        max-width: 220px;
    }

    .item-name-truncate {
        font-weight: 600;
        color: #2b3a4f;
    }

    .furn-table .input-group {
        border: 1px solid #e2e8f1;
        border-radius: 11px;
        background: #f8f9fc;
        overflow: hidden;
    }

    .furn-table .input-group .btn {
        border: none;
        background: #eaf0fb;
        color: #4a72b8;
        font-weight: 700;
        border-radius: 0;
    }

    .furn-table .input-group .btn:hover {
        background: #dbe6f8;
    }

    .furn-table .input-group .btn:disabled {
        background: #f1f4f9;
        color: #b7c0cf;
    }

    .furn-table .qty-input {
        border: none;
        background: transparent;
        text-align: center;
        font-weight: 600;
        color: #2b3a4f;
    }

    .furn-table .qty-input:focus {
        outline: none;
        box-shadow: none;
    }

    .btn-add {
        border-radius: 999px !important;
        padding: 8px 16px !important;
        font-weight: 700;
        background: #4a72b8 !important;
        border: none !important;
        color: #fff !important;
    }

    .btn-add:hover {
        background: #3d5f9c !important;
    }

    .badge.bg-secondary {
        border-radius: 999px;
        font-weight: 700;
        font-size: 0.74rem;
        padding: 5px 11px;
        background: #eaf0fb !important;
        color: #4a72b8 !important;
    }

    .top-bar {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 22px;
        padding-bottom: 18px;
        border-bottom: 1px solid #eef2f8;
    }

    .top-bar h5 {
        margin: 0;
        color: #253345;
        font-weight: 700;
        font-size: 1.2rem;
    }

    .top-bar .btn {
        border-radius: 999px;
        padding: 8px 16px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid #e2e8f1;
        background: #f8f9fc;
        color: #3c4a5e;
    }

    .top-bar .btn:hover {
        background: #eef1f6;
    }

    .cart-card,
    .payment-card,
    .neft-card {
        background: #fafbfd;
        border: 1px solid #e2e8f1;
        border-radius: 16px;
        padding: 22px;
    }

    .cart-card h6,
    .payment-card h6 {
        color: #253345;
        font-weight: 700;
        margin-bottom: 16px;
        font-size: 1rem;
    }

    .additional-furniture-cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        border-bottom: 1px solid #eef2f8;
    }

    .additional-furniture-cart-item:last-child {
        border-bottom: none;
    }

    .cart-item-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cart-item-image {
        width: 52px;
        height: 52px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e2e8f1;
        background: #fff;
        flex-shrink: 0;
    }

    .cart-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cart-item-info strong {
        color: #2b3a4f;
        font-size: 0.9rem;
    }

    .cart-item-meta {
        color: #8792a3;
        font-size: 0.8rem;
        margin-top: 2px;
    }

    .cart-item-actions {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .btn-remove-cart {
        width: 32px;
        height: 32px;
        border-radius: 9px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fdecec !important;
        border: none !important;
        color: #b5443c !important;
        padding: 0 !important;
    }

    .btn-remove-cart:hover {
        background: #fbdcdb !important;
    }

    .cart-total p {
        display: flex;
        justify-content: space-between;
        color: #5c6b81;
        margin-bottom: 8px;
        font-size: 0.88rem;
    }

    .cart-total p.fw-bold {
        color: #253345;
        border-top: 1px solid #e2e8f1;
        padding-top: 10px;
        margin-top: 6px;
    }

    #checkoutButton,
    #quotationButton {
        border-radius: 999px;
        padding: 11px 18px;
        font-weight: 700;
        border: none;
        margin-top: 8px;
    }

    #checkoutButton {
        background: #4a72b8;
    }

    #checkoutButton:hover:not(:disabled) {
        background: #3d5f9c;
    }

    #quotationButton {
        background: #253345;
    }

    #quotationButton:hover:not(:disabled) {
        background: #1a2532;
    }

    #checkoutButton:disabled,
    #quotationButton:disabled {
        background: #d7dce4;
        color: #9aa4b2;
    }

    .neft-card label {
        display: block;
        margin-bottom: 7px;
        color: #3c4a5e;
        font-weight: 600;
        font-size: 0.86rem;
    }

    .neft-card .form-control {
        border: 1px solid #e2e8f1;
        border-radius: 11px;
        background: #fff;
        padding: 10px 14px;
        font-size: 0.88rem;
        color: #2b3a4f;
    }

    .neft-card .form-control:focus {
        outline: none;
        box-shadow: none;
        border-color: #93b4e8;
    }

    .neft-card .form-control[readonly] {
        background: #f1f4f9;
        color: #6b7891;
    }

    #neftSubmitBtn {
        border-radius: 999px;
        padding: 10px 22px;
        font-weight: 700;
        border: none;
        background: #4a72b8;
    }

    #neftSubmitBtn:hover {
        background: #3d5f9c;
    }

    .image-preview-overlay {
        position: fixed;
        inset: 0;
        background: rgba(21, 30, 48, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.15s ease;
        z-index: 1090;
        pointer-events: none;
    }

    .image-preview-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .image-preview-box {
        pointer-events: auto;
        width: 510px;
        max-width: 90vw;
        background: #fff;
        border-radius: 14px;
        padding: 16px 18px;
        box-shadow: 0 20px 50px rgba(20, 40, 80, 0.25);
        height: auto;
    }

    .modalImage-preview {
        max-width: 100%;
    }

    .image-preview-title {
        font-weight: 700;
        color: #253345;
        margin-bottom: 6px;
    }

    .image-preview-desc {
        color: #6b7891;
        font-size: 0.85rem;
        margin: 0;
        line-height: 1.5;
        max-height: 130px;
        overflow-y: auto;
        word-break: break-word;
    }

    #orderDetailModal .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(20, 40, 80, 0.2);
    }

    #orderDetailModal .modal-header {
        background: #fafbfd;
        border-bottom: 1px solid #eef2f8;
        padding: 20px 26px;
    }

    #orderDetailModal .modal-title {
        font-weight: 700;
        color: #253345;
        font-size: 1.1rem;
    }

    #orderDetailModal .modal-body {
        padding: 24px 26px;
    }

    #downloadInvoiceBtn {
        border-radius: 999px !important;
        font-weight: 700;
    }

    #ordersPagination {
        flex-wrap: wrap;
        gap: 12px;
    }

    .pagination-info {
        color: #8792a3;
        font-size: 0.82rem;
    }

    .pagination-modern {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pagination-modern .page-arrow,
    .pagination-modern .page-btn {
        min-width: 30px;
        height: 30px;
        padding: 0 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid #e2e8f1;
        background: #fff;
        color: #4a72b8;
        font-size: 0.82rem;
        font-weight: 600;
        transition: background 0.15s ease, color 0.15s ease;
    }

    .pagination-modern .page-arrow {
        color: #6b7891;
    }

    .pagination-modern .page-arrow:hover:not(:disabled),
    .pagination-modern .page-btn:hover:not(.active) {
        background: #eef1f6;
    }

    .pagination-modern .page-btn.active {
        background: #4a72b8;
        border-color: #4a72b8;
        color: #fff;
    }

    .pagination-modern .page-arrow:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    @media (max-width: 700px) {
        .furn-wrapper {
            padding: 20px;
        }

        .furn-header {
            flex-direction: column;
            align-items: stretch;
        }

        .top-bar {
            flex-wrap: wrap;
        }
    }

    .furniture-optout-row {
        margin-bottom: 18px;
        padding-bottom: 18px;
        border-bottom: 1px solid #eef2f8;
    }

    .furniture-optout-check {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .furniture-optout-check .form-check-input {
        width: 2.4em;
        height: 1.3em;
        cursor: pointer;
    }

    .furniture-optout-check .form-check-input:checked {
        background-color: #1fae74;
        border-color: #1fae74;
    }

    .furniture-optout-check .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(31, 174, 116, 0.25);
        border-color: #1fae74;
    }

    .furniture-optout-check .form-check-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #3c4a5e;
        cursor: pointer;
    }

    .furniture-optedout-card {
        max-width: 520px;
        margin: 40px auto;
        padding: 48px 36px;
        text-align: center;
    }

    .furniture-optedout-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #e6f4ea;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 22px;
    }

    .furniture-optedout-icon i {
        font-size: 38px;
        color: #1fae74;
    }

    .furniture-optedout-card h5 {
        color: #253345;
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 10px;
    }

    .furniture-optedout-card p {
        color: #6b7891;
        font-size: 0.92rem;
        line-height: 1.6;
        margin-bottom: 0;
    }

    .furniture-optedout-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 22px;
        padding: 14px 18px;
        border-radius: 13px;
        background: #fdf3e0;
        border: 1px solid #f3dfae;
        color: #8a5f0f;
        font-size: 0.85rem;
        line-height: 1.55;
        text-align: left;
    }

    .furniture-optedout-note i {
        font-size: 18px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .furniture-opted-out .furniture-section-wrapper {
        display: none !important;
    }

    .furniture-opted-out .furniture-optout-row {
        display: none !important;
    }

    .furniture-opted-out .furniture-optedout-card-wrapper {
        display: block !important;
    }

    .furniture-optedout-card-wrapper {
        display: none;
    }

    .pending-order-item {
        border: 1px solid #eef0f3;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        background: #fff;
        transition: box-shadow 0.2s ease;
    }

    .pending-order-item:hover {
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
    }

    .order-number {
        font-weight: 700;
        font-size: 1.05rem;
        color: #1e293b;
    }

    .order-date {
        font-size: 0.85rem;
        color: #94a3b8;
        margin-top: 2px;
    }

    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.03em;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.success {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge.failed {
        background: #fee2e2;
        color: #991b1b;
    }

    .order-total {
        font-size: 1.15rem;
        font-weight: 700;
        color: #2563eb;
    }

    .btn-pay-now {
        background: #16a34a;
        color: #fff;
        border-radius: 8px;
        border: none;
        padding: 6px 14px;
        font-weight: 600;
    }

    .btn-pay-now:hover {
        background: #15803d;
        color: #fff;
    }

    .order-items-list {
        border-top: 1px dashed #e2e8f0;
        padding-top: 10px;
        margin-top: 10px;
    }

    .order-item-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 0;
    }

    .order-item-thumb {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 8px;
        background: #f1f5f9;
        flex-shrink: 0;
    }

    .order-item-info {
        flex: 1;
        min-width: 0;
    }

    .order-item-name {
        font-size: 0.88rem;
        font-weight: 600;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .order-item-meta {
        font-size: 0.78rem;
        color: #94a3b8;
    }

    .order-item-total {
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e293b;
        white-space: nowrap;
    }

    .early-bird-tag {
        background: #fff7ed;
        color: #c2410c;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 10px;
    }

    .btn-download-receipt {
        border-radius: 999px !important;
        padding: 4px 14px !important;
        font-size: 0.8rem !important;
        font-weight: 600 !important;
        background: #4a72b8 !important;
        border: none !important;
        color: #fff !important;
        margin-top: 8px;
    }

    .btn-download-receipt:hover {
        background: #3d5f9c !important;
        color: #fff !important;
    }
</style>

<div class="content-area">
    <div class="furn-wrapper">
        <div id="readonlyMessage" class="readonly-message" style="display:none;">
            <i class="bi bi-info-circle"></i>
            <span>Additional Furniture form is currently closed. You can only view your past orders. New purchases are not allowed.</span>
        </div>

        <div id="mainPage">
            <div class="furn-header">
                <h4>ADDITIONAL FURNITURE</h4>
                <div class="furn-actions">

                    <button class="btn btn-light" onclick="showCart()">🛒 Cart <span id="cartCountBadge" class="badge bg-danger ms-1 d-none">0</span></button>
                    <button class="btn btn-warning" onclick="showPendingOrders()">
                        ⏳ Pending
                        <span id="pendingCountBadge" class="badge bg-danger ms-1 d-none">0</span>
                    </button>
                    <button class="btn btn-success" onclick="showOrders()">Past Orders</button>
                    <button class="btn btn-outline-dark" onclick="showNeft()">NEFT Transfer</button>
                </div>
            </div>

            <div class="furniture-optout-row">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="form-check form-switch furniture-optout-check">
                        <input class="form-check-input" type="checkbox" id="furnitureOptOutCheckbox">
                        <label class="form-check-label" for="furnitureOptOutCheckbox">
                            I Don't Need Additional Furniture
                        </label>
                    </div>
                    <span class="furniture-end">
                        <i class="bi bi-calendar-event"></i>
                        Due Date:&nbsp;<strong id="furnitureDueDate">--</strong>
                    </span>
                </div>
            </div>

            <div class="furniture-optedout-card-wrapper">
                <div class="furniture-optedout-card">
                    <div class="furniture-optedout-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h5>You've Opted Out of Additional Furniture</h5>
                    <p>You indicated that you don't need additional furniture for your stand, so this section has been disabled for your account.</p>
                    <div class="furniture-optedout-note">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Changed your mind? Please contact your event coordinator to have this preference reset so you can browse and order furniture again.</span>
                    </div>
                </div>
            </div>

            <div class="furniture-section-wrapper">
                <?php $inventory = $inventory ?? []; ?>
                <div class="table-responsive" id="furnitureSection">
                    <table class="table furn-table" id="inventoryTable">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="furnitureTableBody">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="cartPage" class="d-none">
            <div class="top-bar">
                <button onclick="showMain()" class="btn btn-light">← Back</button>
                <h5>Order Summary</h5>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <div class="cart-card">
                        <h6>Cart Items <span id="cartCountHeader" class="badge bg-secondary ms-2 d-none">0</span></h6>
                        <div id="cartItemsContainer">
                            <p class="text-muted">Your cart is empty. Add furniture items from the list above.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="payment-card" id="cartSection">
                        <h6>Total</h6>
                        <div id="cartSummary" class="cart-total mb-3">
                            <p>Subtotal <span>₹0</span></p>
                            <p>GST (18%) <span>₹0</span></p>
                            <p class="fw-bold fs-5">Total <span>₹0</span></p>
                        </div>
                        <button id="checkoutButton" type="button" class="btn btn-dark w-100" disabled>Proceed to Checkout</button>
                        <button id="quotationButton" type="button" class="btn btn-dark w-100" disabled>Generate Quotation</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="ordersPage" class="d-none">
            <div class="top-bar">
                <button onclick="showMain()" class="btn btn-light">← Back</button>
                <h5>Past Orders</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order No.</th>
                            <!-- <th>Items</th> -->
                            <th>Total</th>
                            <th>Payment Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="ordersContainer">

                    </tbody>
                </table>
            </div>
            <nav id="ordersPagination" class="d-flex justify-content-between align-items-center mt-3"></nav>
        </div>
        <div id="neftPage" class="d-none">
            <div class="top-bar">
                <button onclick="showMain()" class="btn btn-light">← Back</button>
                <h5>NEFT Transfer</h5>
            </div>
            <form class="neft-form" id="neftForm" action="">
                <div class="neft-card">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Select Quotation No.</label>
                            <select class="form-control" id="neftQuotationSelect" name="qid">
                                <option value="">-- Loading quotations --</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Quotation Amount</label>
                            <input type="text" class="form-control" name="quotation_amount" id="neftQuotationAmount" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Amount Transfer</label>
                            <input type="text" class="form-control" name="amount_transfer" id="neftAmountTransfer">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Reference No.</label>
                        <input type="text" class="form-control" name="reference_no" id="neftReferenceNo">
                    </div>
                    <div class="mb-3">
                        <label>Reason for difference</label>
                        <textarea class="form-control" name="reason_for_difference" id="neftReasonForDifference" rows="3"></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-dark" id="neftSubmitBtn">Submit</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

<div class="modal fade pending-orders-modal" id="pendingOrdersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clock-history me-2"></i>Pending Orders
                    <span id="pendingModalCount" class="badge bg-warning text-dark ms-2">0</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="pendingOrdersBody">
                <div class="text-center py-4">
                    <span class="spinner-border spinner-border-sm"></span> Loading pending orders...
                </div>
            </div>
        </div>
    </div>
</div>

<div class="image-preview-overlay" id="imagePreviewOverlay">
    <div class="image-preview-box">
        <img id="modalImage" class="modalImage-preview">
        <div class="image-preview-title" id="modalImageTitle"></div>
        <p class="image-preview-desc" id="modalImageDesc"></p>
    </div>
</div>

<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;"></div>
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <div class="d-flex align-items-center">
                   
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body" id="orderDetailBody">

            </div>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    (function() {
        'use strict';

        function checkAdditionalFurnitureStatus() {
            let status = 'enabled_open';
            if (window.getFormStatus) {
                status = window.getFormStatus('additional_furniture');
            } else if (window.onlineFormsEnableDisable) {
                const enabled = parseInt(window.onlineFormsEnableDisable.additional_furniture, 10) === 1;
                const open = parseInt(window.onlineFormsOpenClose.additional_furniture, 10) === 1;
                if (!enabled) status = 'disabled';
                else if (!open) status = 'enabled_closed';
            }
            if (status === 'disabled') {
                if (window.showToast) {
                    window.showToast('Additional Furniture form is currently disabled.', 'error');
                } else {
                    alert('Additional Furniture form is currently disabled.');
                }
                setTimeout(function() {
                    window.location.href = window.BASE_URL || '/dashboard';
                }, 1500);
                return false;
            }

            if (status === 'enabled_closed' || status === 'expired') {
                const msgDiv = document.getElementById('readonlyMessage');
                if (msgDiv) {
                    if (status === 'expired') {
                        msgDiv.querySelector('span').textContent = 'The due date for Additional Furniture has passed. You can view your past orders but new purchases are not allowed.';
                    }
                    msgDiv.style.display = 'block';
                }

                document.querySelectorAll('.btn-add').forEach(function(btn) {
                    btn.classList.add('disabled-btn');
                    btn.setAttribute('disabled', true);
                });

                document.querySelectorAll('.qty-btn').forEach(function(btn) {
                    btn.classList.add('disabled-btn');
                    btn.setAttribute('disabled', true);
                });

                document.querySelectorAll('.qty-input').forEach(function(input) {
                    input.setAttribute('readonly', true);
                    input.setAttribute('disabled', true);
                });

                document.querySelectorAll('.furn-actions .btn-light').forEach(function(btn) {
                    btn.classList.add('disabled-btn');
                    btn.setAttribute('disabled', true);
                });

                document.getElementById('cartSection').classList.add('form-readonly');
                if (window.showToast) {
                    const msg = status === 'expired' ?
                        'Due date has passed. View only mode.' :
                        'View only mode. You can view your past orders only.';
                    window.showToast(msg, 'warning');
                }
                return true;
            }
            return true;
        }

        function renderFurnitureDueDate() {
            const dueDate = window.onlineFormsDueDates && window.onlineFormsDueDates.additional_furniture;
            const el = document.getElementById('furnitureDueDate');
            if (!el) return;

            if (!dueDate) {
                el.textContent = '--';
                return;
            }

            let formatted = dueDate;
            if (window.moment) {
                const m = moment(dueDate);
                if (m.isValid()) {
                    formatted = m.format('Do MMM YYYY');
                }
            }
            el.textContent = formatted;
        }

        const API_BASE_URL = '<?= env('API_BASE_URL') ?>';
        const UPLOAD_BASE_URL = '<?= env('UPLOAD_BASE_URL') ?>';
        const LOGIN_URL = '<?= base_url('login') ?>';
        const APP_BASE_URL = window.location.origin;
        const ENDPOINTS = {
            furniture: `${API_BASE_URL}/v1/cart/furniture`,
            add: `${API_BASE_URL}/v1/cart/add`,
            items: `${API_BASE_URL}/v1/cart/items`,
            remove: `${API_BASE_URL}/v1/cart/remove`,
            quotations: `${API_BASE_URL}/v1/dashboard/get_quotations`,
            quotationDetails: `${API_BASE_URL}/v1/dashboard/get_quotation_details`,
            saveNeft: `${API_BASE_URL}/v1/dashboard/save_neft_transfer`,
            furnitureOptOut: `${API_BASE_URL}/v1/dashboard/furniture-opt-out`,
            pendingOrders: `${API_BASE_URL}/v1/orders/pending`,
            downloadQuotation: (qid) => `${API_BASE_URL}/v1/dashboard/download_quotation/${qid}`,
        };
        const PLACEHOLDER_IMG = 'https://via.placeholder.com/80';
        const state = {
            token: localStorage.getItem('api_token'),
            currencySymbol: '₹',
            orders: {
                all: [],
                currentPage: 1,
                pageSize: 10,
            },
            isOptedOut: false,
            pendingOrders: [],
        };

        const dom = {};

        function cacheDom() {
            dom.toastContainer = document.getElementById('toastContainer');
            dom.mainPage = document.getElementById('mainPage');
            dom.cartPage = document.getElementById('cartPage');
            dom.ordersPage = document.getElementById('ordersPage');
            dom.neftPage = document.getElementById('neftPage');
            dom.furnitureTableBody = document.getElementById('furnitureTableBody');
            dom.cartItemsContainer = document.getElementById('cartItemsContainer');
            dom.cartSummary = document.getElementById('cartSummary');
            dom.checkoutButton = document.getElementById('checkoutButton');
            dom.quotationButton = document.getElementById('quotationButton');
            dom.cartCountBadge = document.getElementById('cartCountBadge');
            dom.cartCountHeader = document.getElementById('cartCountHeader');
            dom.modalImage = document.getElementById('modalImage');
            dom.modalImageTitle = document.getElementById('modalImageTitle');
            dom.modalImageDesc = document.getElementById('modalImageDesc');
            dom.ordersPagination = document.getElementById('ordersPagination');
            dom.downloadInvoiceBtn = document.getElementById('downloadInvoiceBtn');
            dom.furnitureSection = document.getElementById('furnitureSection');
            dom.furnitureOptOutCheckbox = document.getElementById('furnitureOptOutCheckbox');
            dom.furnitureOptedOutMessage = document.querySelector('.furniture-optedout-card-wrapper');
            dom.furnitureOptoutRow = document.querySelector('.furniture-optout-row');
            dom.furnitureSectionWrapper = document.querySelector('.furniture-section-wrapper');
            dom.readonlyMessage = document.getElementById('readonlyMessage');
            dom.pendingCountBadge = document.getElementById('pendingCountBadge');
            dom.pendingOrdersBody = document.getElementById('pendingOrdersBody');
            dom.pendingModalCount = document.getElementById('pendingModalCount');
        }

        function showToast(message, type = 'success') {
            if (window.showToast) {
                window.showToast(message, type);
                return;
            }
            if (!dom.toastContainer) return;
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-bg-${type} border-0`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>`;
            dom.toastContainer.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl, {
                delay: 4000
            });
            toast.show();
            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        }

        function money(amount) {
            return `${state.currencySymbol} ${amount}`;
        }

        function resolveImage(path) {
            return path ? (UPLOAD_BASE_URL + path) : PLACEHOLDER_IMG;
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function redirectToLogin() {
            localStorage.removeItem('api_token');
            window.location.href = LOGIN_URL;
        }

        async function apiCall(url, {
            method = 'GET',
            body = null,
            contentType = 'application/x-www-form-urlencoded'
        } = {}) {
            if (!state.token) {
                redirectToLogin();
                return null;
            }

            const headers = {
                'Authorization': 'Bearer ' + state.token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            };

            const options = {
                method,
                headers
            };

            if (body) {
                if (contentType === 'application/json') {
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
                showToast('Network error. Please try again.', 'danger');
                return null;
            }

            if (response.status === 401) {
                redirectToLogin();
                return null;
            }

            try {
                return await response.json();
            } catch (err) {
                showToast('Unexpected server response.', 'danger');
                return null;
            }
        }

        function hideAll() {
            [dom.mainPage, dom.cartPage, dom.ordersPage, dom.neftPage]
            .forEach(el => el && el.classList.add('d-none'));
        }

        function showMain() {
            hideAll();
            dom.mainPage?.classList.remove('d-none');
        }

        async function showCart() {
            hideAll();
            dom.cartPage?.classList.remove('d-none');
            await loadCartItems();
        }

        function showOrders() {
            hideAll();
            dom.ordersPage?.classList.remove('d-none');
            loadPastOrders();
        }

        function showNeft() {
            hideAll();
            dom.neftPage?.classList.remove('d-none');
            loadQuotations();
        }
        window.showNeft = showNeft;

        async function showPendingOrders() {
            const modal = new bootstrap.Modal(document.getElementById('pendingOrdersModal'));
            modal.show();
            await loadPendingOrders();
        }
        window.showPendingOrders = showPendingOrders;

        async function loadPendingOrders() {
            if (!dom.pendingOrdersBody) return;

            dom.pendingOrdersBody.innerHTML = `
        <div class="text-center py-4">
            <span class="spinner-border spinner-border-sm"></span> Loading pending orders...
        </div>`;

            const result = await apiCall(ENDPOINTS.pendingOrders);

            if (!result || !result.status) {
                dom.pendingOrdersBody.innerHTML = `
            <div class="no-pending-orders">
                <i class="bi bi-exclamation-circle"></i>
                <p class="text-muted">Unable to load pending orders.</p>
            </div>`;
                return;
            }

            const order = result.data?.order || null;
            state.pendingOrder = order;

            const count = order ? 1 : 0;
            if (dom.pendingCountBadge) {
                dom.pendingCountBadge.textContent = count;
                dom.pendingCountBadge.classList.toggle('d-none', count === 0);
            }
            if (dom.pendingModalCount) {
                dom.pendingModalCount.textContent = count;
            }

            if (!order) {
                dom.pendingOrdersBody.innerHTML = `
            <div class="no-pending-orders">
                <i class="bi bi-check-circle"></i>
                <h6 class="text-success">No Pending Orders</h6>
                <p class="text-muted">You don't have any orders with pending payment.</p>
            </div>`;
                return;
            }

            state.currencySymbol = result.data.currency_symbol || '₹';

            const statusClassMap = {
                draft: 'pending',
                sent: 'pending',
                accepted: 'success',
                rejected: 'failed'
            };

            const PLACEHOLDER_NAME = 'Item unavailable';
            const statusLabel = order.status_label || 'unknown';
            const statusClass = statusClassMap[statusLabel] || 'pending';
            const items = order.items || [];
            const itemsCount = order.items_count || items.length;
            const amount = order.amount ?? order.q_amount ?? 0;

            const pendingQid = order.qid ?? order.id ?? null;
            const receiptButtonHtml = pendingQid ?
                `<button type="button" class="btn btn-download-receipt btn-download-receipt-pending" data-qid="${pendingQid}">⬇ Receipt</button>` :
                '';

            let itemsHtml = '';
            if (items.length) {
                itemsHtml = `
            <div class="order-items-list mt-2">
                ${items.map(item => `
                    <div class="order-item-row">
                        <div class="order-item-info">
                            <div class="order-item-name">
                                ${item.item_name || `<span class="text-muted fst-italic">${PLACEHOLDER_NAME}</span>`}
                            </div>
                            <div class="order-item-meta">
                                Qty: ${item.quantity || 1}
                            </div>
                        </div>
                       
                    </div>
                `).join('')}
            </div>`;
            }

            dom.pendingOrdersBody.innerHTML = `
        <div class="pending-order-item">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div>
                    <div class="order-number">#${order.ref_no || order.id}</div>
                    <div class="order-date">
                        <i class="bi bi-calendar3 me-1"></i>
                        ${order.added_date || 'N/A'}
                    </div>
                    <div class="mt-1">
                        <span class="status-badge ${statusClass}">${statusLabel.toUpperCase()}</span>
                        <span class="ms-2 text-muted small">${itemsCount} item${itemsCount !== 1 ? 's' : ''}</span>
                    </div>
                    ${order.remarks ? `<div class="text-muted small mt-1">${order.remarks}</div>` : ''}
                </div>
                <div class="text-end">
                    <div class="order-total">${money(amount)}</div>
                    ${receiptButtonHtml}
                </div>
            </div>
            ${itemsHtml}
        </div>`;
        }

        window.retryPayment = async function(orderId, orderNumber) {
            try {
                showToast(`Redirecting to payment for order #${orderNumber}...`, 'info');

                const result = await apiCall(ORDER_ENDPOINTS.checkout, {
                    method: 'POST',
                    body: {
                        payment_method: 'razorpay',
                        order_id: orderId,
                        success_url: APP_BASE_URL + '/payment/success',
                        failed_url: APP_BASE_URL + '/payment/failed',
                        callback_url: ORDER_ENDPOINTS.razorpayCallback
                    },
                });

                if (!result || !result.status) {
                    showToast(result?.message || 'Failed to initiate payment.', 'danger');
                    return;
                }

                const data = result.data;
                if (!window.Razorpay) {
                    showToast('Razorpay checkout script not loaded.', 'danger');
                    return;
                }

                const options = {
                    key: data.razorpay_key,
                    amount: data.amount,
                    currency: data.currency,
                    name: 'Your Company Name',
                    description: `Order Payment - ${orderNumber}`,
                    order_id: data.razorpay_order_id,
                    handler: async function(response) {
                        const verifyResult = await apiCall(data.callback_url || ORDER_ENDPOINTS.razorpayCallback, {
                            method: 'POST',
                            contentType: 'application/json',
                            body: {
                                order_id: data.order_id,
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_signature: response.razorpay_signature
                            },
                        });

                        if (!verifyResult || !verifyResult.status) {
                            showToast(verifyResult?.message || 'Payment verification failed.', 'danger');
                            return;
                        }

                        if (verifyResult.data?.redirect_url) {
                            window.location.href = verifyResult.data.redirect_url;
                            return;
                        }

                        showToast('Payment successful!', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('pendingOrdersModal'))?.hide();
                        await loadPendingOrders();
                        await loadPastOrders();
                    },
                    modal: {
                        ondismiss: function() {
                            showToast('Payment cancelled.', 'warning');
                        }
                    },
                    theme: {
                        color: '#3399cc'
                    }
                };
                const razorpay = new Razorpay(options);
                razorpay.open();

            } catch (error) {
                showToast('Failed to process payment. Please try again.', 'danger');
            }
        };

        async function loadQuotations() {
            const select = document.getElementById('neftQuotationSelect');
            if (!select) return;
            select.innerHTML = '<option value="">-- Loading quotations --</option>';
            const result = await apiCall(ENDPOINTS.quotations);
            if (!result || !result.status) {
                select.innerHTML = '<option value="">-- Failed to load quotations --</option>';
                showToast('Failed to load quotations', 'danger');
                return;
            }
            const quotations = result.data?.quotations || [];
            if (!quotations.length) {
                select.innerHTML = '<option value="">-- No quotations found --</option>';
                return;
            }
            select.innerHTML = '<option value="">-- Select Quotation --</option>' +
                quotations.map(q => `<option value="${q.qid}">${q.ref_no} - ${money(q.amount)}</option>`).join('');
        }

        async function loadNeftQuotationDetails(qid) {
            if (!qid) {
                document.getElementById('neftQuotationAmount').value = '';
                document.getElementById('neftAmountTransfer').value = '';
                document.getElementById('neftReferenceNo').value = '';
                document.getElementById('neftReasonForDifference').value = '';
                return;
            }

            const result = await apiCall(`${ENDPOINTS.quotationDetails}/${qid}`);
            if (!result || !result.status) {
                showToast('Failed to load quotation details', 'danger');
                return;
            }

            const quote = result.data?.quote;
            document.getElementById('neftQuotationAmount').value = quote?.amount || '';
        }
        document.getElementById('neftAmountTransfer').addEventListener('input', function() {
            const amountTransfer = parseFloat(this.value);
            const quotationAmount = parseFloat(document.getElementById('neftQuotationAmount').value);
            const reasonField = document.getElementById('neftReasonForDifference');
            const reasonWrapper = reasonField.closest('.mb-3');
            if (!quotationAmount || isNaN(amountTransfer)) return;
            if (amountTransfer > quotationAmount) {
                this.value = '';
                showToast(`Amount Transfer cannot exceed Quotation Amount (${state.currencySymbol} ${quotationAmount})`, 'danger');
                return;
            }
            if (amountTransfer < quotationAmount && amountTransfer > 0) {
                reasonField.dataset.required = 'true';
                reasonWrapper.querySelector('label').innerHTML = 'Reason for difference <span class="text-danger">*</span>';
            } else {
                reasonField.dataset.required = 'false';
                reasonField.onblur = null;
                reasonWrapper.querySelector('label').textContent = 'Reason for difference';
            }
        });
        async function submitNeftForm(e) {
            e.preventDefault();
            const qid = document.getElementById('neftQuotationSelect').value;
            const amountTransfer = parseFloat(document.getElementById('neftAmountTransfer').value);
            const quotationAmount = parseFloat(document.getElementById('neftQuotationAmount').value);
            const referenceNo = document.getElementById('neftReferenceNo').value;
            const reasonForDiff = document.getElementById('neftReasonForDifference').value;
            if (!qid) {
                showToast('Please select a quotation', 'danger');
                return;
            }
            if (!amountTransfer || isNaN(amountTransfer)) {
                showToast('Please enter a valid amount', 'danger');
                return;
            }
            if (amountTransfer > quotationAmount) {
                showToast(`Amount Transfer cannot be greater than Quotation Amount (${state.currencySymbol} ${quotationAmount})`, 'danger');
                return;
            }
            if (amountTransfer < quotationAmount && !reasonForDiff.trim()) {
                showToast('Reason for difference is required when amount is less than quotation amount', 'danger');
                document.getElementById('neftReasonForDifference').focus();
                return;
            }
            if (!referenceNo.trim()) {
                showToast('Please enter reference number', 'danger');
                return;
            }
            const payload = {
                qid: parseInt(qid),
                amount_transfer: amountTransfer,
                reference_no: referenceNo.trim(),
                reason_for_difference: reasonForDiff.trim()
            };
            const btn = document.getElementById('neftSubmitBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            const result = await apiCall(ENDPOINTS.saveNeft, {
                method: 'POST',
                body: payload,
                contentType: 'application/json'
            });
            btn.disabled = false;
            btn.innerHTML = 'Submit';
            if (!result || !result.status) {
                showToast(result?.message || 'Failed to save NEFT transfer', 'danger');
                return;
            }
            showToast('NEFT transfer saved successfully', 'success');
            document.getElementById('neftForm').reset();
        }

        function changeQty(btn, change) {
            const input = btn.parentElement.querySelector('input');
            let value = (parseInt(input.value, 10) || 1) + change;
            input.value = Math.max(1, value);
        }
        window.changeQty = changeQty;

        function buildFurnitureRow(item) {
            const imageUrl = resolveImage(item.item_image);
            const priceHtml = item.is_early_bird ?
                `<span class="text-muted text-decoration-line-through me-2">${money(item.sale_price)}</span>
                <span class="text-success fw-bold">${money(item.price)}</span>` :
                `<span class="fw-bold">${money(item.price)}</span>`;
            const description = item.description || item.item_description || 'No description available.';
            const altText = truncateText(item.item_name, 20);
            const displayName = truncateText(item.item_name, 35);
            const isOutOfStock = item.is_deleted === true || item.is_deleted === 1 || item.is_deleted === '1';
            const imgWrapClass = isOutOfStock ? 'furn-img-wrap out-of-stock' : 'furn-img-wrap';
            const rowClass = isOutOfStock ? 'out-of-stock-row' : '';
            const outOfStockOverlay = isOutOfStock ?
                `<div class="out-of-stock-overlay">Out of<br>Stock</div>` : '';
            const disabledAttr = isOutOfStock ? 'disabled' : '';
            return `
                <tr data-item-id="${item.id}" class="${rowClass}">
                    <td>
                        <div class="${imgWrapClass}">
                            <img src="${imageUrl}" alt="${escapeHtml(altText)}" class="view-img"
    data-img="${imageUrl}"
    data-title="${escapeHtml(item.item_name)}"
    data-desc="${escapeHtml(description)}">
                            ${outOfStockOverlay}
                        </div>
                        ${isOutOfStock ? '<div class="text-danger small mt-1 fw-bold text-center">This product is out of stock</div>' : ''}
                    </td>
                    <td class="item-name-cell">
                        <span class="item-name-truncate" title="${escapeHtml(item.item_name)}">
                            ${escapeHtml(displayName)}
                        </span>
                    </td>
                    <td>${priceHtml}</td>
                    <td>
                        <div class="input-group" style="max-width:90px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="changeQty(this, -1)" ${disabledAttr} disabled>-</button>
                            <input type="text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                class="form-control form-control-sm text-center qty-input"
                                value="1"
                                maxlength="3"
                                ${disabledAttr}>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="changeQty(this, 1)" ${disabledAttr}>+</button>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary btn-add" ${disabledAttr}>
                            <i class="fa fa-cart-plus"></i> Add
                        </button>
                    </td>
                </tr>`;
        }

        function truncateText(text, maxLen = 20) {
            if (!text) return '';
            const str = String(text).trim();
            return str.length > maxLen ? str.slice(0, maxLen).trim() + '…' : str;
        }

        function changeQty(button, delta) {
            const inputGroup = button.closest('.input-group');
            const input = inputGroup.querySelector('.qty-input');
            let current = parseInt(input.value, 10);
            if (isNaN(current)) current = 1;
            let newValue = current + delta;
            if (newValue < 1) newValue = 1;
            input.value = newValue;
            updateDecreaseButtonState(input);
        }
        document.addEventListener('input', function(e) {
            if (e.target.matches('.input-group input[type="number"]')) {
                let val = parseInt(e.target.value, 10);
                if (isNaN(val) || val < 1) {
                    e.target.value = 1;
                }
            }
        });
        document.addEventListener('keypress', function(e) {
            if (e.target.matches('.qty-input')) {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            }
        });
        document.addEventListener('paste', function(e) {
            if (e.target.matches('.qty-input')) {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData).getData('text');
                const digitsOnly = pasted.replace(/\D/g, '');
                if (digitsOnly) {
                    e.target.value = digitsOnly;
                }
            }
        });
        document.addEventListener('input', function(e) {
            if (e.target.matches('.qty-input')) {
                let val = e.target.value.replace(/\D/g, '');
                e.target.value = val;
            }
        });

        document.addEventListener('blur', function(e) {
            if (e.target.matches('.qty-input')) {
                let val = parseInt(e.target.value, 10);
                if (isNaN(val) || val < 1) {
                    val = 1;
                }
                e.target.value = val;
                updateDecreaseButtonState(e.target);
            }
        }, true);

        function updateDecreaseButtonState(input) {
            const inputGroup = input.closest('.input-group');
            const decreaseBtn = inputGroup.querySelector('button[onclick*="-1"]');
            if (decreaseBtn) {
                decreaseBtn.disabled = (parseInt(input.value, 10) <= 1);
            }
        }

        function showFurnitureOptedOutState() {
            state.isOptedOut = true;

            if (dom.furnitureSectionWrapper) {
                dom.furnitureSectionWrapper.style.display = 'none';
            }
            if (dom.furnitureOptoutRow) {
                dom.furnitureOptoutRow.style.display = 'none';
            }
            if (dom.furnitureOptedOutMessage) {
                dom.furnitureOptedOutMessage.style.display = 'block';
            }
            if (dom.mainPage) {
                dom.mainPage.classList.remove('d-none');
            }
            if (dom.readonlyMessage) {
                dom.readonlyMessage.style.display = 'none';
            }
            if (dom.checkoutButton) {
                dom.checkoutButton.disabled = true;
            }
            if (dom.quotationButton) {
                dom.quotationButton.disabled = true;
            }
            showToast('You have opted out of additional furniture.', 'info');
        }

        function showFurnitureEnabledState() {
            state.isOptedOut = false;

            if (dom.furnitureSectionWrapper) {
                dom.furnitureSectionWrapper.style.display = 'block';
            }
            if (dom.furnitureOptoutRow) {
                dom.furnitureOptoutRow.style.display = 'block';
            }
            if (dom.furnitureOptedOutMessage) {
                dom.furnitureOptedOutMessage.style.display = 'none';
            }
            if (dom.furnitureOptOutCheckbox) {
                dom.furnitureOptOutCheckbox.checked = false;
                dom.furnitureOptOutCheckbox.disabled = false;
            }
        }

        async function checkFurnitureOptOutStatus() {
            try {
                const result = await apiCall(ENDPOINTS.furniture);
                if (!result || !result.status) {
                    return;
                }
                const isOptedOut = Number(result.data?.is_need_additional_furniture) === 0;
                if (isOptedOut) {
                    showFurnitureOptedOutState();
                } else {
                    showFurnitureEnabledState();
                }

                if (!isOptedOut) {
                    loadFurnitureList();
                }
            } catch (error) {
               
            }
        }

        async function handleFurnitureOptOutChange(e) {
            if (!e.target.checked) {
                e.target.checked = true;
                return;
            }

            const confirmResult = await Swal.fire({
                title: 'Are you sure?',
                text: "You're indicating that you don't need additional furniture. This action cannot be undone from here.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, I don\'t need it',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545'
            });

            if (!confirmResult.isConfirmed) {
                e.target.checked = false;
                return;
            }

            e.target.disabled = true;
            const result = await apiCall(ENDPOINTS.furnitureOptOut, {
                method: 'POST',
                body: {
                    is_need_additional_furniture: 0
                },
                contentType: 'application/json'
            });

            if (!result || !result.status) {
                showToast(result?.message || 'Failed to save your preference. Please try again.', 'danger');
                e.target.checked = false;
                e.target.disabled = false;
                return;
            }

            showToast(result.message || 'Preference saved successfully.', 'success');
            showFurnitureOptedOutState();
        }

        async function loadFurnitureList() {
            if (!dom.furnitureTableBody) return;

            dom.furnitureTableBody.innerHTML = `
            <tr><td colspan="5" class="text-center py-4">
                <span class="spinner-border spinner-border-sm"></span> Loading furniture list...
            </td></tr>`;

            const result = await apiCall(ENDPOINTS.furniture);
            if (!result) return;

            const isOptedOut = Number(result.data?.is_need_additional_furniture) === 0;
            if (isOptedOut) {
                showFurnitureOptedOutState();
                return;
            }

            if (!result.status || !result.data?.inventory?.length) {
                dom.furnitureTableBody.innerHTML = `
                <tr><td colspan="5" class="text-center text-muted py-4">No furniture items available.</td></tr>`;
                return;
            }

            state.currencySymbol = result.data.currency_symbol || '₹';
            dom.furnitureTableBody.innerHTML = result.data.inventory.map(buildFurnitureRow).join('');
        }

        function buildCartRow(item) {
            const imageUrl = resolveImage(item.product_img);
            const lineTotal = (item.price * item.quantity).toFixed(2);
            const isOutOfStock = item.is_deleted === true || item.is_deleted === 1 || item.is_deleted === '1';
            const imgClass = isOutOfStock ? 'cart-item-image out-of-stock' : 'cart-item-image';
            const outOfStockNote = isOutOfStock ?
                `<div class="text-danger small fw-bold mt-1">This product is out of stock. Please remove it to proceed.</div>` : '';

            return `
                <div class="additional-furniture-cart-item" data-cart-id="${item.id}">
                    <div class="cart-item-info">
                        <div class="${imgClass}" style="position:relative;">
                            <img src="${imageUrl}" alt="${item.item_name}">
                            ${isOutOfStock ? '<div class="out-of-stock-overlay" style="font-size:0.5rem;">Out of<br>Stock</div>' : ''}
                        </div>
                        <div>
                            <strong>${item.item_name}</strong>
                            <div class="cart-item-meta">${money(item.price)} × ${item.quantity}</div>
                            ${outOfStockNote}
                        </div>
                    </div>
                    <div class="cart-item-actions">
                        <strong>${money(lineTotal)}</strong>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-cart"
                                data-cart-id="${item.id}" aria-label="Delete item">🗑</button>
                    </div>
                </div>`;
        }
        state.cartHasOutOfStock = false;
        async function loadCartItems() {
            if (!dom.cartItemsContainer) return;
            const result = await apiCall(ENDPOINTS.items);
            if (!result || !result.status) {
                renderCartError();
                return;
            }
            state.currencySymbol = result.data.currency_symbol || '₹';
            const items = result.data.items || [];
            updateCartCount(items.reduce((sum, item) => sum + (item.quantity || 0), 0));
            if (!items.length) {
                state.cartHasOutOfStock = false;
                renderEmptyCart();
                return;
            }
            dom.cartItemsContainer.innerHTML = items.map(buildCartRow).join('');
            dom.cartSummary.innerHTML = `
                <p>Subtotal <span>${money(result.data.subtotal)}</span></p>
                <p>GST (18%) <span>${money(result.data.tax)}</span></p>
                <p class="fw-bold fs-5">Total <span>${money(result.data.total)}</span></p>`;
            const hasOutOfStockItem = items.some(item =>
                item.is_deleted === true || item.is_deleted === 1 || item.is_deleted === '1'
            );
            state.cartHasOutOfStock = hasOutOfStockItem;
            if (hasOutOfStockItem || state.isOptedOut) {
                dom.checkoutButton.disabled = true;
                dom.quotationButton.disabled = true;
            } else {
                dom.checkoutButton.disabled = false;
                dom.quotationButton.disabled = false;
            }
        }

        function updateCartCount(count) {
            if (!dom.cartCountBadge || !dom.cartCountHeader) return;
            const visible = count > 0;
            dom.cartCountBadge.textContent = count;
            dom.cartCountHeader.textContent = count;
            dom.cartCountBadge.classList.toggle('d-none', !visible);
            dom.cartCountHeader.classList.toggle('d-none', !visible);
        }

        function renderEmptyCart() {
            dom.cartItemsContainer.innerHTML =
                '<p class="text-muted">Your cart is empty. Add furniture items from the list above.</p>';
            dom.cartSummary.innerHTML = `
                <p>Subtotal <span>${money(0)}</span></p>
                <p>GST (18%) <span>${money(0)}</span></p>
                <p class="fw-bold fs-5">Total <span>${money(0)}</span></p>`;
            dom.checkoutButton.disabled = true;
            dom.quotationButton.disabled = true;
        }

        function renderCartError() {
            updateCartCount(0);
            dom.cartItemsContainer.innerHTML = '<p class="text-danger">Unable to load cart.</p>';
            dom.checkoutButton.disabled = true;
            dom.quotationButton.disabled = true;
        }

        async function addToCart({
            item_id,
            quantity
        }) {
            if (state.isOptedOut) {
                showToast('You have opted out of additional furniture.', 'warning');
                return;
            }
            const result = await apiCall(ENDPOINTS.add, {
                method: 'POST',
                body: {
                    item_id,
                    quantity
                },
            });
            if (!result) return;
            if (!result.status) {
                showToast(result.message || 'Could not add item to cart.', 'danger');
                return;
            }
            showToast(result.message || 'Item added to cart.', 'success');
            await loadCartItems();
        }

        async function removeCartItem(itemId) {
            const confirmDelete = await Swal.fire({
                title: 'Are you sure?',
                text: "You want to remove this item from cart.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545'
            });

            if (!confirmDelete.isConfirmed) {
                return;
            }
            const result = await apiCall(ENDPOINTS.remove, {
                method: 'POST',
                body: {
                    item_id: itemId
                },
            });
            if (!result) return;
            if (!result.status) {
                showToast(result.message || 'Unable to remove item.', 'danger');
                return;
            }
            await loadCartItems();
        }

        const ORDER_ENDPOINTS = {
            checkout: `${API_BASE_URL}/v1/orders/checkout`,
            razorpayCallback: `${API_BASE_URL}/v1/payment/razorpay/callback`,
            list: `${API_BASE_URL}/v1/orders/list`,
            detail: (id) => `${API_BASE_URL}/v1/orders/${id}`,
            quotation: `${API_BASE_URL}/v1/orders/quotation`,
            invoice: (encId) => `${API_BASE_URL}/v1/orders/${encId}/invoice_download`,
        };

        async function placeOrder() {
            if (state.isOptedOut) {
                showToast('You have opted out of additional furniture.', 'warning');
                return;
            }
            if (state.cartHasOutOfStock) {
                showToast('Please remove out-of-stock items from your cart before proceeding.', 'danger');
                return;
            }
            const checkoutBtn = document.getElementById('checkoutButton');
            if (checkoutBtn) {
                checkoutBtn.disabled = true;
                checkoutBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating Payment...';
            }
            const result = await apiCall(ORDER_ENDPOINTS.checkout, {
                method: 'POST',
                body: {
                    payment_method: 'razorpay',
                    success_url: window.location.origin + '/payment/success',
                    failed_url: window.location.origin + '/payment/failed',
                    callback_url: ORDER_ENDPOINTS.razorpayCallback
                },
            });
            if (checkoutBtn) {
                checkoutBtn.disabled = false;
                checkoutBtn.innerHTML = 'Proceed to Checkout';
            }
            if (!result) return;
            if (!result.status) {
                showToast(result.message || 'Failed to create payment.', 'danger');
                return;
            }
            const data = result.data;
            if (!window.Razorpay) {
                showToast('Razorpay checkout script not loaded.', 'danger');
                return;
            }
            const options = {
                key: data.razorpay_key,
                amount: data.amount,
                currency: data.currency,
                name: 'Your Company Name',
                description: 'Order Payment',
                order_id: data.razorpay_order_id,
                handler: async function(response) {
                    const verifyResult = await apiCall(data.callback_url || ORDER_ENDPOINTS.razorpayCallback, {
                        method: 'POST',
                        contentType: 'application/json',
                        body: {
                            order_id: data.order_id,
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_signature: response.razorpay_signature
                        },
                    });
                    
                    if (!verifyResult || !verifyResult.status) {
                        showToast(
                            verifyResult?.message || 'Payment verification failed.',
                            'danger'
                        );
                        return;
                    }
                    if (verifyResult.data?.redirect_url) {
                        window.location.href = verifyResult.data.redirect_url;
                        return;
                    }
                    showToast('Payment successful. Order placed successfully.', 'success');
                    await loadCartItems();
                    await loadPendingOrders();
                    setTimeout(() => {
                        showOrders();
                    }, 800);
                },
                modal: {
                    ondismiss: function() {
                        showToast('Payment cancelled.', 'warning');
                    }
                },
                theme: {
                    color: '#3399cc'
                }
            };
            const razorpay = new Razorpay(options);
            razorpay.open();
        }
        const INVOICE_ELIGIBLE_STATUSES = ['paid', 'completed', 'success'];

        function isInvoiceAvailable(order) {
            const status = (order.payment_status || '').toLowerCase();
            return INVOICE_ELIGIBLE_STATUSES.includes(status);
        }

        function getEncId(order) {
            const encId = order.enc_id ?? order.encId ?? order.encrypted_id ?? order.enc ?? null;
            if (!encId) {
               
            }
            return encId;
        }

        function buildOrderRow(order, index) {
            const currencySym = order.currency === 'USD' ? '$' : '₹';
            const itemsSummary = order.items_count ?
                `${order.items_count} item${order.items_count > 1 ? 's' : ''}` :
                '-';
            const invoiceButtonHtml = isInvoiceAvailable(order) ?
                `<button type="button" class="btn btn-sm btn-outline-primary btn-download-invoice" data-enc-id="${getEncId(order) || ''}">
                    ⬇ Reciept
                </button>` :
                '';
            return `
                <tr data-order-id="${order.id}">
                    <td>${index + 1}</td>
                    <td>${order.order_number}</td>
                   
                    <td>${currencySym}${order.total}</td>
                    <td><span class="badge bg-secondary text-uppercase">${order.payment_status}</span></td>
                    <td>${order.created_at}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-dark btn-view-order" data-order-id="${order.id}">
                            View
                        </button>
                        ${invoiceButtonHtml}
                    </td>
                </tr>`;
        }

        function renderOrdersPage() {
            const tbody = document.getElementById('ordersContainer');
            if (!tbody) return;
            const {
                all,
                pageSize
            } = state.orders;
            if (!all.length) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4">No orders found.</td></tr>`;
                if (dom.ordersPagination) dom.ordersPagination.innerHTML = '';
                return;
            }
            const totalPages = Math.max(1, Math.ceil(all.length / pageSize));
            const page = Math.min(Math.max(1, state.orders.currentPage), totalPages);
            state.orders.currentPage = page;
            const startIndex = (page - 1) * pageSize;
            const pageItems = all.slice(startIndex, startIndex + pageSize);
            tbody.innerHTML = pageItems
                .map((order, i) => buildOrderRow(order, startIndex + i))
                .join('');
            renderOrdersPaginationControls(page, totalPages, all.length, pageSize);
        }

        function renderOrdersPaginationControls(page, totalPages, totalItems, pageSize) {
            if (!dom.ordersPagination) return;
            const startItem = (page - 1) * pageSize + 1;
            const endItem = Math.min(page * pageSize, totalItems);
            const MAX_VISIBLE = 5;
            let pages = [];
            if (totalPages <= MAX_VISIBLE) {
                pages = Array.from({
                    length: totalPages
                }, (_, i) => i + 1);
            } else {
                const start = Math.max(1, Math.min(page - 2, totalPages - MAX_VISIBLE + 1));
                pages = Array.from({
                    length: MAX_VISIBLE
                }, (_, i) => start + i);
            }

            const pageButtons = pages.map(p => `
                <button type="button"
                    class="page-btn ${p === page ? 'active' : ''} btn-order-page"
                    data-page="${p}">${p}</button>`).join('');

            dom.ordersPagination.innerHTML = `
                <div class="pagination-info">
                    Showing ${startItem}-${endItem} of ${totalItems} orders
                </div>
                <div class="pagination-modern">
                    <button type="button" class="page-arrow btn-order-prev" ${page === 1 ? 'disabled' : ''} aria-label="Previous page">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    ${pageButtons}
                    <button type="button" class="page-arrow btn-order-next" ${page === totalPages ? 'disabled' : ''} aria-label="Next page">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>`;
        }

        async function loadPastOrders() {
            const tbody = document.getElementById('ordersContainer');
            if (!tbody) return;
            tbody.innerHTML = `
                <tr><td colspan="7" class="text-center py-4">
                    <span class="spinner-border spinner-border-sm"></span> Loading orders...
                </td></tr>`;

            const result = await apiCall(ORDER_ENDPOINTS.list);
            if (!result) return;

            if (!result.status || !result.data?.orders?.length) {
                state.orders.all = [];
                tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4">No orders found.</td></tr>`;
                if (dom.ordersPagination) dom.ordersPagination.innerHTML = '';
                return;
            }

            state.orders.all = result.data.orders;
            
            state.orders.currentPage = 1;
            renderOrdersPage();
        }

        async function generateQuotation() {
            if (state.isOptedOut) {
                showToast('You have opted out of additional furniture.', 'warning');
                return;
            }
            if (state.cartHasOutOfStock) {
                showToast('Please remove out-of-stock items from your cart before proceeding.', 'danger');
                return;
            }
            const btn = document.getElementById('quotationButton');
            if (!state.token) {
                redirectToLogin();
                return;
            }
            try {
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Downloading...';
                }
                const response = await fetch(ORDER_ENDPOINTS.quotation, {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + state.token,
                        'Accept': 'application/pdf'
                    }
                });
                if (response.status === 401) {
                    redirectToLogin();
                    return;
                }
                if (!response.ok) {
                    const contentType = response.headers.get('content-type') || '';
                    let message = 'Quotation download failed.';

                    if (contentType.includes('application/json')) {
                        const errorData = await response.json();
                        message = errorData.message || message;
                    }
                    throw new Error(message);
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'Proforma-Invoice.pdf';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                showToast('Quotation downloaded.', 'success');
                await loadCartItems();
                await loadFurnitureList();
            } catch (err) {
                showToast(err.message || 'Something went wrong while downloading quotation.', 'danger');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = 'Generate Quotation';
                }
            }
        }

        async function downloadPdf(url, filename, btn, originalLabel) {
            if (!state.token) {
                redirectToLogin();
                return;
            }
            try {
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Downloading...';
                }
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + state.token,
                        'Accept': 'application/pdf'
                    }
                });
                if (response.status === 401) {
                    redirectToLogin();
                    return;
                }
                if (!response.ok) {
                    const contentType = response.headers.get('content-type') || '';
                    let message = 'Download failed.';
                    if (contentType.includes('application/json')) {
                        const errorData = await response.json();
                        message = errorData.message || message;
                    }
                    throw new Error(message);
                }
                const blob = await response.blob();
                const objUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = objUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(objUrl);
                showToast('Invoice downloaded.', 'success');
            } catch (err) {
               
                showToast(err.message || 'Something went wrong while downloading invoice.', 'danger');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalLabel || '⬇ Download Invoice';
                }
            }
        }

        async function downloadOrderInvoice(encId, btn, originalLabel) {
            if (!encId) {
                showToast('Missing invoice reference for this order.', 'danger');
                return;
            }
            await downloadPdf(
                ORDER_ENDPOINTS.invoice(encId),
                `Invoice.pdf`,
                btn,
                originalLabel
            );
        }

        async function downloadQuotationByQid(qid, btn, originalLabel) {
            if (!qid) {
                showToast('Missing quotation reference for this order.', 'danger');
                return;
            }
            await downloadPdf(
                ENDPOINTS.downloadQuotation(qid),
                `Pending-Invoice.pdf`,
                btn,
                originalLabel
            );
        }

        function buildOrderDetailHtml(order) {
            const currencySym = order.currency === 'USD' ? '$' : '₹';
            const itemsRows = (order.items || []).map(item => {
                const imageUrl = item.item_image ? (UPLOAD_BASE_URL + item.item_image) : 'https://via.placeholder.com/60';
                return `
        <tr>
            <td><img src="${imageUrl}" alt="${item.item_name}" style="width:50px;height:50px;object-fit:cover;"></td>
            <td>${item.item_name}</td>
            <td>${currencySym}${item.unit_price}</td>
            <td>${item.quantity}</td>
            <td>${currencySym}${item.line_total}</td>
        </tr>`;
            }).join('');
            return `
    <div class="mb-3">
        <div class="row">
            <div class="col-md-6">
                <strong>Company:</strong> ${order.company_name || '-'}
            </div>
            <div class="col-md-6 text-md-end">
                <strong>Exhibition:</strong> ${order.exhibition_name || '-'}
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <strong>Order Number:</strong> ${order.order_number}
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <strong>Order Date:</strong> ${order.created_at}
            </div>
            <div class="col-md-6 text-md-end">
                <strong>Payment Status:</strong>
                <span class="badge bg-secondary text-uppercase">${order.payment_status}</span>
            </div>
        </div>
        ${order.payment_method ? `
        <div class="row mt-2">
            <div class="col-md-6">
                <strong>Payment Method:</strong> ${order.payment_method.toUpperCase()}
            </div>
            ${order.payment_reference ? `
            <div class="col-md-6 text-md-end">
                <strong>Reference:</strong> ${order.payment_reference}
            </div>` : ''}
        </div>` : ''}
        ${order.amount_transferred ? `
        <div class="row mt-2">
            <div class="col-md-6">
                <strong>Amount Transferred:</strong> ${currencySym}${order.amount_transferred}
            </div>
            ${order.reason_for_difference ? `
            <div class="col-md-6 text-md-end">
                <strong>Reason for Difference:</strong> ${order.reason_for_difference}
            </div>` : ''}
        </div>` : ''}
        ${!isInvoiceAvailable(order) ? `
        <div class="alert alert-warning mt-3 mb-0 py-2 px-3">
            Invoice will be available once payment is confirmed. Current status:
            <strong class="text-uppercase">${order.payment_status}</strong>
        </div>` : ''}
    </div>

    <hr>

    <h6>Items</h6>
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                ${itemsRows || '<tr><td colspan="5" class="text-center text-muted">No items found.</td></tr>'}
            </tbody>
        </table>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-8 offset-md-4">
            <table class="table table-borderless mb-0">
                <tr>
                    <td>Subtotal</td>
                    <td class="text-end">${currencySym}${order.subtotal}</td>
                </tr>
                <tr>
                    <td>GST (18%)</td>
                    <td class="text-end">${currencySym}${order.tax}</td>
                </tr>
                <tr class="fw-bold fs-5">
                    <td>Total</td>
                    <td class="text-end">${currencySym}${order.total}</td>
                </tr>
            </table>
        </div>
    </div>
    `;
        }


        async function viewOrderDetail(orderId) {
            const modalBody = document.getElementById('orderDetailBody');
            const modalEl = document.getElementById('orderDetailModal');
            const downloadBtn = dom.downloadInvoiceBtn || document.getElementById('downloadInvoiceBtn');
            if (!modalBody || !modalEl) return;
            modalBody.innerHTML = `
        <div class="text-center py-4">
            <span class="spinner-border spinner-border-sm"></span> Loading order details...
        </div>`;

            if (downloadBtn) downloadBtn.classList.add('d-none');
            new bootstrap.Modal(modalEl).show();
            const result = await apiCall(ORDER_ENDPOINTS.detail(orderId));
            if (!result || !result.status) {
                modalBody.innerHTML = `<p class="text-danger text-center py-4">${result?.message || 'Unable to load order details.'}</p>`;
                return;
            }

            const order = result.data.order;
            modalBody.innerHTML = buildOrderDetailHtml(order);

            if (downloadBtn) {
                if (isInvoiceAvailable(order)) {
                    downloadBtn.classList.remove('d-none');
                    downloadBtn.innerHTML = '⬇ Download Invoice';
                    downloadBtn.onclick = () => downloadOrderInvoice(getEncId(order), downloadBtn, '⬇ Download Invoice');
                } else {
                    downloadBtn.classList.add('d-none');
                    downloadBtn.onclick = null;
                }
            }
        }

        let imageModalCloseTimer = null;

        function getImagePreviewOverlay() {
            return document.getElementById('imagePreviewOverlay');
        }

        function showImageModal(imgBtn) {
            clearTimeout(imageModalCloseTimer);
            if (!dom.modalImage) return;
            dom.modalImage.src = imgBtn.dataset.img;
            if (dom.modalImageTitle) dom.modalImageTitle.textContent = imgBtn.dataset.title || '';
            if (dom.modalImageDesc) dom.modalImageDesc.textContent = imgBtn.dataset.desc || '';
            getImagePreviewOverlay()?.classList.add('show');
        }

        function scheduleHideImageModal() {
            clearTimeout(imageModalCloseTimer);
            imageModalCloseTimer = setTimeout(() => {
                getImagePreviewOverlay()?.classList.remove('show');
            }, 200);
        }

        function bindImageHoverEvents() {
            document.addEventListener('mouseover', (event) => {
                const imgBtn = event.target.closest('.view-img');
                if (imgBtn) {
                    showImageModal(imgBtn);
                }
            });

            document.addEventListener('mouseout', (event) => {
                const imgBtn = event.target.closest('.view-img');
                const boxEl = document.querySelector('.image-preview-box');
                const leavingToBox = boxEl && boxEl.contains(event.relatedTarget);
                if (imgBtn && !leavingToBox) {
                    scheduleHideImageModal();
                }
            });

            const boxEl = document.querySelector('.image-preview-box');
            if (boxEl) {
                boxEl.addEventListener('mouseenter', () => clearTimeout(imageModalCloseTimer));
                boxEl.addEventListener('mouseleave', scheduleHideImageModal);
            }
        }

        function bindEvents() {
            bindImageHoverEvents();
            dom.furnitureOptOutCheckbox?.addEventListener('change', handleFurnitureOptOutChange);
            document.addEventListener('click', (event) => {
                const addBtn = event.target.closest('.btn-add');
                if (addBtn) {
                    const row = addBtn.closest('tr');
                    addToCart({
                        item_id: parseInt(row.dataset.itemId, 10),
                        quantity: parseInt(row.querySelector('input').value, 10) || 1,
                    });
                    return;
                }

                const removeBtn = event.target.closest('.btn-remove-cart');
                if (removeBtn) {
                    removeCartItem(removeBtn.dataset.cartId);
                    return;
                }
                const checkoutBtn = event.target.closest('#checkoutButton');
                if (checkoutBtn) {
                    placeOrder();
                    return;
                }
                const quotationBtn = event.target.closest('#quotationButton');
                if (quotationBtn) {
                    generateQuotation();
                    return;
                }

                const viewOrderBtn = event.target.closest('.btn-view-order');
                if (viewOrderBtn) {
                    viewOrderDetail(viewOrderBtn.dataset.orderId);
                    return;
                }

                const downloadInvoiceRowBtn = event.target.closest('.btn-download-invoice');
                if (downloadInvoiceRowBtn) {
                    const originalLabel = downloadInvoiceRowBtn.innerHTML;
                    downloadOrderInvoice(downloadInvoiceRowBtn.dataset.encId, downloadInvoiceRowBtn, originalLabel);
                    return;
                }

                const downloadReceiptBtn = event.target.closest('.btn-download-receipt-pending');
                if (downloadReceiptBtn) {
                    const originalLabel = downloadReceiptBtn.innerHTML;
                    downloadQuotationByQid(downloadReceiptBtn.dataset.qid, downloadReceiptBtn, originalLabel);
                    return;
                }

                const prevBtn = event.target.closest('.btn-order-prev');
                if (prevBtn) {
                    state.orders.currentPage -= 1;
                    renderOrdersPage();
                    return;
                }

                const nextBtn = event.target.closest('.btn-order-next');
                if (nextBtn) {
                    state.orders.currentPage += 1;
                    renderOrdersPage();
                    return;
                }

                const pageBtn = event.target.closest('.btn-order-page');
                if (pageBtn) {
                    state.orders.currentPage = parseInt(pageBtn.dataset.page, 10);
                    renderOrdersPage();
                    return;
                }
            });

            document.addEventListener('submit', (event) => {
                if (event.target.id !== 'neftForm') return;
                submitNeftForm(event);
            });

            document.addEventListener('change', (event) => {
                if (event.target.id === 'neftQuotationSelect') {
                    loadNeftQuotationDetails(event.target.value);
                }
            });
        }

        function initializeDropZones() {
            return;
        }

        window.showMain = showMain;
        window.showCart = showCart;
        window.showOrders = showOrders;
        window.showNeft = showNeft;
        window.showPendingOrders = showPendingOrders;
        window.loadQuotations = loadQuotations;
        window.loadNeftQuotationDetails = loadNeftQuotationDetails;
        window.submitNeftForm = submitNeftForm;

        document.addEventListener('DOMContentLoaded', async function() {
            cacheDom();
            if (!state.token) {
                redirectToLogin();
                return;
            }
            await checkFurnitureOptOutStatus();
            const accessGranted = checkAdditionalFurnitureStatus();
            renderFurnitureDueDate();
            if (!accessGranted) {
                return;
            }
            bindEvents();
            loadCartItems();
            await loadPendingOrders();

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('view') === 'cart' && !state.isOptedOut) {
                showCart();
            }
        });

        document.addEventListener('layoutConfigReady', function() {
            checkAdditionalFurnitureStatus();
            renderFurnitureDueDate();
        });
        if (window.__layoutConfigReady) {
            checkAdditionalFurnitureStatus();
            renderFurnitureDueDate();
        }

    })();
</script>
<?= $this->endSection() ?>