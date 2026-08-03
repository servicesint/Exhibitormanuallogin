<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content') ?>


<div class="content-area">
    <div class="hotel-section">
        <div class="container">
            <div class="hotel-header text-center">
                <span class="form-kicker">Stay Nearby</span>
                <h4 class="hotel-title">Accommodation / Hotels</h4>
                <p class="text-muted">Here is a list of nearby budget hotels for your convenience.</p>
            </div>
            <?= html_entity_decode($accommodation->page_content ?? '') ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('custom-script') ?>
<?= $this->endSection() ?>
