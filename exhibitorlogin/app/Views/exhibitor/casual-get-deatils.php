<?= $this->extend('layout/main-layout') ?>

<?= $this->section('content') ?>
<div class="content-area">
    <div class="gst2-wrapper">

        <div class="gst2-hero">
            <div>
                <span class="form-kicker">GST Guidance</span>
                <h2>CASUAL GST</h2>
                <p>Everything outside-Gujarat exhibitors need to know before submitting their temporary GST details.</p>
            </div>
            <div class="gst2-hero-badge">
                <i class="bi bi-calendar-check"></i>
                <span>Submit 15 days prior</span>
            </div>
        </div>

        <div class="gst2-layout gst2-layout-beauty">
            <div class="gst2-main">
               
                <h3>Who needs a Casual GST?</h3>

                <p>
                    Exhibitors based outside Gujarat must register as a casual trader in GST.
                    Apply here:
                    <a href="https://reg.gst.gov.in/registration" target="_blank">
                        https://reg.gst.gov.in/registration
                    </a>
                </p>

                <div class="gst2-callout">
                    <i class="bi bi-exclamation-circle"></i>
                    <p>
                        This is mandatory by law. The temporary GST number must be submitted at least
                        <strong>15 days prior</strong> to the event.
                    </p>
                </div>

                <p>
                    Exhibitors registered in Gujarat only need to share their GST number.
                </p>
            </div>

            <div class="gst2-side gst2-side-card">
                <h5>Jurisdiction Details</h5>

                <div class="gst2-tile">
                    <span><i class="bi bi-geo-alt"></i> Address</span>
                    <p>Jade Banquet, Bodakdev, Ahmedabad</p>
                </div>

                <div class="gst2-tile">
                    <span><i class="bi bi-building"></i> Zone</span>
                    <p>Ahmedabad</p>
                </div>

                <div class="gst2-tile">
                    <span><i class="bi bi-file-earmark-text"></i> Commissionerate</span>
                    <p>Ahmedabad-North</p>
                </div>

                <div class="gst2-tile">
                    <span><i class="bi bi-pin-map"></i> Division</span>
                    <p>S.G. Highway West</p>
                </div>

                <div class="gst2-tile">
                    <span><i class="bi bi-geo"></i> Range</span>
                    <p>Range I</p>
                </div>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('custom-script') ?>
<?= $this->endSection() ?>
