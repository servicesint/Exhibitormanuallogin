<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content') ?>
<style>
    .payment-status-box {
        max-width: 560px;
        margin: 40px auto;
        text-align: center;
        padding: 32px 24px;
    }

    .status-icon-success {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #e6f7ec;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }

    .status-icon-success svg {
        width: 36px;
        height: 36px;
    }

    .payment-status-box h4 {
        margin-bottom: 8px;
        color: #1b7a43;
    }

    .payment-status-box p.subtext {
        color: #6c757d;
        margin-bottom: 24px;
    }
</style>
<div class="content-area">
    <div class="fascia-box">
        <div class="payment-status-box">
            <div class="status-icon-success">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="12" fill="#28a745" />
                    <path d="M7 12.5L10.2 15.7L17 8.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <h4>Payment Successful</h4>
            <p class="subtext">Your order has been confirmed.</p>

            <a href="<?= base_url('additional-furniture') ?>" class="btn btn-submit" id="furnitureListBtn">Go to Furniture List</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('custom-script') ?>
<?= $this->endSection() ?>