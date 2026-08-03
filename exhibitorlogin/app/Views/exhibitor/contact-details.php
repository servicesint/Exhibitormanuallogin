<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content'); ?>

<div class="content-area">
    <div class="cd2-wrapper">
        <div class="cd2-header">
            <span class="form-kicker">Support Team</span>
            <h2 class="cd2-title">CONTACT DETAILS</h2>
            <p>Reach the right team member for sales, operations, and exhibitor support.</p>
        </div>
        <div class="cd2-grid">
            <?= $contact->page_content ?? '' ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('custom-script') ?>
<?= $this->endSection() ?>