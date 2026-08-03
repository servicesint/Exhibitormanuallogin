<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content'); ?>

<div class="content-area">
    <div class="oi5-wrapper">
        <?= html_entity_decode($other_information->page_content ?? '') ?>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('custom-script') ?>
<?= $this->endSection() ?>
