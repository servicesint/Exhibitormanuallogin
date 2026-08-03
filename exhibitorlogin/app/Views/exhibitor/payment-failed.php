<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content') ?>
<style>
    .payment-status-box {
        max-width: 560px;
        margin: 40px auto;
        text-align: center;
        padding: 32px 24px;
    }

    .status-icon-failed {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #fdecea;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }

    .status-icon-failed svg {
        width: 36px;
        height: 36px;
    }

    .payment-status-box h4 {
        margin-bottom: 8px;
        color: #c0392b;
    }

    .payment-status-box p.subtext {
        color: #6c757d;
        margin-bottom: 24px;
    }
</style>
<div class="content-area">
    <div class="fascia-box">
        <div class="payment-status-box">
            <div class="status-icon-failed">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="12" fill="#dc3545" />
                    <path d="M8 8L16 16M16 8L8 16" stroke="white" stroke-width="2" stroke-linecap="round" />
                </svg>
            </div>
            <h4>Payment Failed</h4>
            <p class="subtext">We couldn't complete your payment.</p>

            <a href="<?= base_url('additional-furniture') ?>" class="btn btn-submit" id="furnitureListBtn">Go to Furniture List</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('custom-script') ?>
<?= $this->endSection() ?>