<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content'); ?>

<div class="content-area">

    <div class="esc-header-wrap">
        <span class="form-kicker">Verified Vendors</span>
        <h2 class="esc-title">EMPANELLED STAND CONTRACTOR / FABRICATOR</h2>
        <p class="esc-subtitle">Verified contractors for stall design & fabrication</p>
    </div>

    <?= html_entity_decode($empanelled_sand->page_content ?? '') ?>

</div>

<?= $this->endSection() ?>
<?= $this->section('custom-script') ?>
<?= $this->endSection() ?>
