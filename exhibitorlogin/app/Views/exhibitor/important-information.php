<?= $this->extend('layout/main-layout') ?>

<?= $this->section('content') ?>
<div class="content-area">
    <div class="imp-info-page">
        <div class="imp-wrapper">
            <div class="imp-hero">
                <div>
                    <span class="form-kicker">Event Manual</span>
                    <h4>BRIDAL ASIA ~ THE SYMPHONY OF JEWELS 2026</h4>
                    <p>Jade Luxury Banquets, Ahmedabad, Gujarat</p>
                </div>
                <div class="imp-hero-date">
                    <i class="bi bi-calendar-event"></i>
                    <span>24th - 25th July 2026</span>
                </div>
            </div>
            <?= html_entity_decode($important->page_content ?? '') ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('custom-script') ?>
<?= $this->endSection() ?>
