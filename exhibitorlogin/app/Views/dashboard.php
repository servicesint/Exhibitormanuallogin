<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content') ?>
<div class="content-area">
        <div class="welcome-wrapper">
                <div class="welcome-card">
                         <?= $welcome_note ?>
                </div>
        </div>
</div>
<script>
        function applyHeaderProfile(profile) {
            const nameEl = document.getElementById('headerUserName');
            const nameEl2 = document.getElementById('headerUserNames');
            const eventNameEl = document.getElementById('headerEventName');
            
            if (nameEl && profile.contact_person) {
                nameEl.textContent = profile.contact_person;
            }
            // Also update headerUserNames if it exists
            if (nameEl2 && profile.contact_person) {
                nameEl2.textContent = profile.contact_person;
                nameEl2.style.display = 'inline';
            }
            if (eventNameEl && profile.event_name) {
                eventNameEl.textContent = profile.event_name;
            }
        }
</script>
<?= $this->endSection() ?>