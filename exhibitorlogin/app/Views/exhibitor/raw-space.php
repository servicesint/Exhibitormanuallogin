<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content') ?>
<div class="content-area">
    <div class="cgx-wrapper">
        <div class="cgx-header">
            <span class="form-kicker">Manual</span>
            <h4>GUIDELINES FOR RAW SPACE</h4>
            <p class="cgx-sub">Choose a section to view detailed rules</p>
        </div>

        <ul class="nav nav-tabs cgx-tabs" id="cgxTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-exhibitor">
                    <i class="bi bi-person-check"></i> Exhibitors
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-edc">
                    <i class="bi bi-tools"></i> EDC / Contractors
                </button>
            </li>
        </ul>

        <div class="tab-content cgx-content">
            <div class="tab-pane fade show active" id="tab-exhibitor">
                <section id="rawspace" class="cgx-section-card">
                    <i class="bi bi-bounding-box"></i>
                    <div>
                        <h5>What is a Raw Space?</h5>
                        <p>
                            Raw space means a blank floor area rented to build a custom exhibition stand.
                            It offers maximum flexibility for bespoke designs.
                        </p>
                    </div>
                </section>

                <section id="approval">
                    <h5>Stand Plan Approval</h5>
                    <div class="cgx-card">
                        <p>
                            All exhibitors must submit stand plans before the deadline for approval.
                            No build-up without approval. Onsite approval may attract a fine of
                            <strong>INR 25,000 per design</strong>.
                        </p>
                        <div class="cgx-alert">
                            <i class="bi bi-stopwatch"></i> Response time: within 72 hours after submission
                        </div>
                    </div>
                </section>

                <section id="height" class="cgx-section-card">
                    <i class="bi bi-arrows-vertical"></i>
                    <div>
                        <h5>Construction Height</h5>
                        <p>Mandatory height: <strong>3 meters</strong> (including platform).</p>
                    </div>
                </section>

                <section id="electricity">
                    <h5>Electricity Allocation</h5>
                    <div class="table-responsive">
                        <table class="table cgx-table">
                            <thead>
                                <tr>
                                    <th>Stall Area</th>
                                    <th>24 sq m</th>
                                    <th>30 sq m</th>
                                    <th>36+ sq m</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>Load</th>
                                    <td>1KW</td>
                                    <td>1KW</td>
                                    <td>2KW</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <div class="tab-pane fade" id="tab-edc">
                <section>
                    <h5>Exhibitor Designated Contractor (EDC)</h5>
                    <div class="cgx-card">
                        <p>
                            Exhibitors can appoint contractors (EDC/ODC) for installation and dismantling.
                            Contractors must comply with all exhibition rules and safety regulations.
                        </p>
                        <p>
                            These guidelines ensure a safe and consistent exhibition environment.
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab');
        if (tab) {
            const trigger = document.querySelector(`[data-bs-target="#tab-${tab}"]`);
            if (trigger) new bootstrap.Tab(trigger).show();
        }
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(btn => {
            btn.addEventListener('shown.bs.tab', function(e) {
                const id = e.target.getAttribute('data-bs-target').replace('#tab-', '');
                history.replaceState(null, null, '?tab=' + id);
            });
        });
    });
</script>

<?= $this->endSection() ?>
<?= $this->section('custom-script') ?>
<?= $this->endSection() ?>
