<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content') ?>
<style>
    .gst-form-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 28px 20px 48px;
    }

    .gst-card {
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 12px 32px rgba(21, 50, 101, 0.06);
        overflow: hidden;
        border: 1px solid #eef2f8;
    }

    .gst-card-head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
        padding: 30px 32px 24px;
        background: #fafbfd;
        border-bottom: 1px solid #eef2f8;
    }

    .form-kicker {
        display: inline-block;
        margin-bottom: 8px;
        color: #5b7bab;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .gst-title {
        font-size: 1.7rem;
        margin: 0 0 8px;
        color: #253345;
        line-height: 1.2;
        font-weight: 700;
    }

    .gst-card-head p {
        margin: 0;
        color: #6b7891;
        max-width: 560px;
        font-size: 0.94rem;
        line-height: 1.65;
    }

    .gst-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        border-radius: 999px;
        background: #eaf0fb;
        color: #4a72b8;
        font-weight: 600;
        font-size: 0.86rem;
        white-space: nowrap;
    }

    .gst-status-pill.is-submitted {
        background: #e6f4ea;
        color: #2f7a42;
    }

    .gst-info-panel {
        margin: 22px 32px 0;
        padding: 20px 22px;
        background: #fdf9f0;
        border: 1px solid #f0e4c8;
        border-radius: 16px;
        color: #6b5a34;
    }

    .gst-info-panel h6 {
        margin: 0 0 10px;
        font-weight: 600;
        color: #8a6d2e;
        font-size: 0.94rem;
    }

    .gst-info-panel p {
        margin: 0 0 10px;
        line-height: 1.65;
        font-size: 0.86rem;
        color: #6b5a34;
    }

    .gst-info-panel p:last-child {
        margin-bottom: 0;
    }

    .gst-info-panel a {
        color: #4a72b8;
        font-weight: 600;
        text-decoration: underline;
    }

    .gst-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 5px 20px;
        margin-top: 10px;
        font-size: 0.84rem;
    }

    .gst-info-grid div strong {
        color: #4a3b1c;
    }

    .gst-form-layout {
        padding: 26px 32px 32px;
    }

    .gst-form-panel {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .gst-two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .gst-group {
        margin-bottom: 18px;
    }

    .gst-group label {
        display: block;
        margin-bottom: 8px;
        color: #3c4a5e;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .gst-input {
        display: block;
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e2e8f1;
        border-radius: 12px;
        background: #f8f9fc;
        font-size: 0.92rem;
        color: #2b3a4f;
        transition: border-color 0.15s ease, background 0.15s ease;
    }

    .gst-input::placeholder {
        color: #a3aec2;
    }

    .gst-input:focus {
        outline: none;
        box-shadow: none;
        border-color: #93b4e8;
        background: #fff;
    }

    .gst-input.is-invalid {
        border-color: #e6a3a3;
        background: #fdf6f6;
    }

    .gst-input:disabled {
        background: #eef1f6;
        color: #97a2b3;
        cursor: not-allowed;
    }

    .validation-error {
        color: #c4574f;
        font-size: 0.8em;
        margin-top: 5px;
        display: block;
    }

    .gst-same-city-box {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px 18px;
        background: #f2f7fd;
        border: 1px solid #d9e6f7;
        border-radius: 16px;
        margin-bottom: 22px;
    }

    .gst-same-city-box input[type="checkbox"] {
        margin-top: 3px;
        width: 18px;
        height: 18px;
        accent-color: #4a72b8;
        cursor: pointer;
        flex-shrink: 0;
    }

    .gst-same-city-box label {
        margin: 0;
        cursor: pointer;
    }

    .gst-same-city-box strong {
        display: block;
        color: #2b3a4f;
        font-size: 0.92rem;
        margin-bottom: 3px;
    }

    .gst-same-city-box span {
        display: block;
        color: #6b7891;
        font-size: 0.84rem;
        line-height: 1.55;
    }

    .gst-upload {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        padding: 16px 18px;
        border: 1.5px dashed #d9e1ec;
        border-radius: 16px;
        background: #f8f9fc;
        position: relative;
    }

    .gst-upload-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #eaf0fb;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #5b7bab;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .gst-upload-copy {
        flex: 1 1 220px;
    }

    .gst-upload-copy strong {
        display: block;
        margin-bottom: 3px;
        font-weight: 600;
        color: #2b3a4f;
        font-size: 0.9rem;
    }

    .gst-upload-copy span {
        display: block;
        color: #7c879a;
        font-size: 0.84rem;
        line-height: 1.55;
    }

    .upload-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 130px;
        padding: 9px 16px;
        border-radius: 999px;
        background: #4a72b8;
        color: #fff;
        cursor: pointer;
        transition: background 0.15s ease;
        border: none;
        font-size: 0.88rem;
        font-weight: 600;
        flex-shrink: 0;
    }

    .upload-btn:hover {
        background: #3d5f9c;
    }

    .gst-upload input[type="file"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    .file-name {
        display: block;
        color: #8792a3;
        font-size: 0.82rem;
        width: 100%;
    }

    .view-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        color: #4a72b8;
        font-weight: 600;
        font-size: 0.86rem;
        text-decoration: none;
    }

    .view-link:hover {
        text-decoration: underline;
    }

    .gst-note {
        background: #f2f5fb;
        border: 1px solid #e2e8f1;
        border-radius: 14px;
        padding: 14px 16px;
        color: #5c6b81;
        font-size: 0.86rem;
        line-height: 1.6;
    }

    .gst-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }

    .gst-btn {
        min-width: 160px;
        border-radius: 999px;
        padding: 11px 22px;
        font-size: 0.92rem;
        font-weight: 600;
        background: #4a72b8;
        border: none;
        color: #fff;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .gst-btn:hover {
        background: #3d5f9c;
    }

    .gst-submitted-box {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 24px 26px;
        background: #f2f9f4;
        border: 1px solid #cfe9d6;
        border-radius: 18px;
    }

    .gst-submitted-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #dcf0e1;
        color: #2f7a42;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .gst-submitted-content strong {
        display: block;
        color: #245a34;
        font-size: 1rem;
        margin-bottom: 6px;
    }

    .gst-submitted-content p {
        margin: 0 0 4px;
        color: #3f7350;
        font-size: 0.88rem;
        line-height: 1.6;
    }

    .gst-submitted-content .view-link {
        color: #2f7a42;
    }

    .gst-edit-btn {
        margin-top: 14px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 999px;
        border: 1px solid #cfe9d6;
        background: #fff;
        color: #2f7a42;
        font-size: 0.84rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .gst-edit-btn:hover {
        background: #eef9f0;
    }

    @media (max-width: 700px) {

        .gst-card-head,
        .gst-form-layout {
            padding-left: 20px;
            padding-right: 20px;
        }

        .gst-info-panel {
            margin-left: 20px;
            margin-right: 20px;
        }

        .gst-info-grid,
        .gst-two-col {
            grid-template-columns: 1fr;
        }

        .gst-status-pill {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 520px) {
        .gst-card-head {
            padding: 20px 16px 14px;
        }

        .gst-form-wrapper {
            padding: 16px 12px 28px;
        }

        .gst-info-panel {
            margin-left: 16px;
            margin-right: 16px;
            padding: 16px 16px;
        }

        .gst-form-layout {
            padding: 20px 16px 24px;
        }

        .gst-upload {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }

        .upload-btn {
            width: 100%;
        }
    }
</style>
<div class="content-area">
    <div class="gst-form-wrapper">

        <div class="gst-card">
            <div class="gst-card-head">
                <div>
                    <span class="form-kicker">Tax Registration</span>
                    <h4 class="gst-title">Casual GST Details</h4>
                    <p>Submit your temporary GST details to complete your event participation. Please ensure your certificate, trade name, and GST number match exactly.</p>
                </div>
                <span class="gst-status-pill" id="gstStatusPill"><i class="bi bi-clock-history"></i> Loading...</span>
            </div>

            <div class="gst-info-panel" id="gstInfoPanel">
                <h6>Who needs a casual GST?</h6>
                <p>Exhibitors based outside of the event city need to register themselves as a casual trader in GST — <a href="https://reg.gst.gov.in/registration" target="_blank" rel="noopener">https://reg.gst.gov.in/registration</a></p>
                <p>This is mandatory by law. It is imperative that this temporary GST no. is provided to Services International at least <u>15 days prior to the event</u>. You can input this number under the Profile Section of this portal.</p>
                <p>Exhibitors who are registered in the same city/state as the event just need to share their GST No.</p>
                <p><strong>You may require the following information to apply for the Casual GST No.</strong></p>
                <div class="gst-info-grid" id="gstInfoGrid"></div>
            </div>

            <div class="gst-form-layout">

                <!-- Shown when GST is already submitted -->
                <div class="gst-submitted-box" id="gstSubmittedBox" style="display:none;">
                    <div class="gst-submitted-icon"><i class="bi bi-check-circle-fill"></i></div>
                    <div class="gst-submitted-content" id="gstSubmittedContent">
                        <strong>Your casual GST details have been submitted</strong>
                        <p id="gstSubmittedTradeName"></p>
                        <p id="gstSubmittedNumber"></p>
                        <a href="#" class="view-link" id="gstSubmittedViewLink" target="_blank" rel="noopener" style="display:none;">
                            <i class="bi bi-eye"></i> View uploaded certificate
                        </a>
                        <br>
                        <button type="button" class="gst-edit-btn" id="gstEditBtn">
                            <i class="bi bi-pencil"></i> Edit Details
                        </button>
                    </div>
                </div>

                <!-- Actual form -->
                <form class="gst-form-panel" id="gstForm" method="post" enctype="multipart/form-data" style="display:none;">

                    <!-- Shown only if exhibitor's city matches the event's city -->
                    <div class="gst-same-city-box" id="sameCityBox" style="display:none;">
                        <input type="checkbox" id="sameAsCityCheckbox" name="same_as_city">
                        <label for="sameAsCityCheckbox">
                            <strong>I am registered in the same city as the event</strong>
                            <span>Since your registered address matches the event location, you don't need to submit a casual GST. Just check this box to confirm.</span>
                        </label>
                    </div>

                    <div class="gst-two-col" id="gstFieldsBlock">
                        <div class="gst-group">
                            <label for="casualTradeName">Casual Trade Name</label>
                            <input type="text" id="casualTradeName" name="casual_trade_name" class="gst-input" placeholder="Enter your casual trade name">
                        </div>

                        <div class="gst-group">
                            <label for="casualGstNumber">Casual GST Number</label>
                            <input type="text" id="casualGstNumber" name="casual_gst_number" class="gst-input" placeholder="Enter your GST number">
                        </div>
                    </div>

                    <div class="gst-group" id="gstUploadBlock">
                        <label>Upload GST Certificate</label>
                        <div class="gst-upload">
                            <input type="file" id="fileUpload" name="casual_gst_certificate" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="gst-upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                            <div class="gst-upload-copy">
                                <strong>Select your GST certificate</strong>
                                <span>PDF, JPG, PNG formats accepted. Max size 2MB.</span>
                            </div>
                            <label for="fileUpload" class="upload-btn">Choose File</label>
                            <span class="file-name">No file chosen</span>
                        </div>
                        <a href="#" class="view-link" id="viewCertificateLink" target="_blank" rel="noopener" style="display:none;">
                            <i class="bi bi-eye"></i> View uploaded certificate
                        </a>
                    </div>

                    <div class="gst-note">Make sure the uploaded file is clear and the GST details are visible. This helps speed up approval.</div>

                    <div class="gst-actions">
                        <button type="submit" class="btn gst-btn">Update Details</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>
<script>
  
    const UPLOAD_BASE_URL = '<?= rtrim(env('UPLOAD_BASE_URL'), '/') ?>';
    const resolveUrl = v => v ? `${UPLOAD_BASE_URL}/${v}` : '';
    $(function() {
        const $gstForm = $("#gstForm");
        const API_BASE_URL = '<?= env('API_BASE_URL') ?>';
        const GST_URL = `${API_BASE_URL}/v1/dashboard/casual-gst-details`;

        let isSameCity = false;
        let isSubmitted = false;

        document.getElementById("fileUpload").addEventListener("change", function() {
            const fileName = this.files[0]?.name || "No file chosen";
            document.querySelector(".file-name").innerText = fileName;
        });

        $.validator.addMethod('gstFileType', function(value, element) {
            if (!element.files || element.files.length === 0) return true;
            const allowed = ['pdf', 'jpg', 'jpeg', 'png'];
            const ext = element.files[0].name.split('.').pop().toLowerCase();
            return allowed.includes(ext);
        }, 'Only .pdf, .jpg, .jpeg or .png files are allowed');

        $.validator.addMethod('gstFileSize', function(value, element) {
            if (!element.files || element.files.length === 0) return true;
            const maxBytes = 2 * 1024 * 1024;
            return element.files[0].size <= maxBytes;
        }, 'Maximum file size is 2 MB');

        $(document).on('change', '#sameAsCityCheckbox', function() {
            const checked = $(this).is(':checked');
            $("#gstFieldsBlock, #gstUploadBlock").toggle(!checked);

            if ($gstForm.data('validator')) {
                if (checked) {
                    $("#casualTradeName").rules('remove', 'required');
                    $("#casualGstNumber").rules('remove', 'required minlength maxlength');
                    $("#fileUpload").rules('remove', 'required');
                } else {
                    $("#casualTradeName").rules('add', {
                        required: true,
                        minlength: 2
                    });
                    $("#casualGstNumber").rules('add', {
                        required: true,
                        minlength: 15,
                        maxlength: 15
                    });
                    if (!hasExistingCertificate()) {
                        $("#fileUpload").rules('add', {
                            required: true
                        });
                    }
                }
            }
        });

        function hasExistingCertificate() {
            return $("#viewCertificateLink").is(':visible');
        }

        $gstForm.validate({
            errorClass: 'validation-error',
            errorElement: 'div',
            errorPlacement(error, element) {
                error.insertAfter(element.closest('.gst-group').find('.gst-input, .gst-upload'));
            },
            highlight(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight(element) {
                $(element).removeClass('is-invalid');
            },
            rules: {
                casual_trade_name: {
                    required: true,
                    minlength: 2
                },
                casual_gst_number: {
                    required: true,
                    minlength: 15,
                    maxlength: 15
                },
                casual_gst_certificate: {
                    required: true,
                    accept: false,
                    gstFileType: true,
                    gstFileSize: true
                }
            },
            messages: {
                casual_trade_name: {
                    required: 'Enter your casual trade name',
                    minlength: 'Enter at least 2 characters'
                },
                casual_gst_number: {
                    required: 'Enter your GST number',
                    minlength: 'GST number must be 15 characters',
                    maxlength: 'GST number must be 15 characters'
                },
                casual_gst_certificate: {
                    required: 'Upload your GST certificate',
                    gstFileType: 'Only .pdf, .jpg, .jpeg or .png files are allowed',
                    gstFileSize: 'Maximum file size is 2 MB'
                }
            },
            submitHandler(form) {
                submitGstForm(form);
            }
        });

        function renderEventInfo(event) {
            if (!event) return;
            const rows = [
                ['Address Exhibition Hall', event.venue],
                ['State', event.venue_state],
                ['City', event.venue_city],
                ['Event Name', event.event_name],
            ];
            let html = '';
            rows.forEach(([label, value]) => {
                if (!value) return;
                html += `<div><strong>${label}:</strong> ${value}</div>`;
            });
            $("#gstInfoGrid").html(html);
        }

        function showSubmittedState(saved) {
            $("#gstForm").hide();
            $("#gstSubmittedBox").show();
            const $statusPill = $("#gstStatusPill");
            $statusPill.html('<i class="bi bi-check-circle"></i> Submitted').addClass('is-submitted');
            if (saved?.same_as_city == 1) {
                $("#gstSubmittedTradeName").text('Marked as registered in the same city as the event.');
                $("#gstSubmittedNumber").hide();
                $("#gstSubmittedViewLink").hide();
            } else {
                $("#gstSubmittedTradeName").text(`Trade Name: ${saved?.casual_trade_name || '-'}`);
                $("#gstSubmittedNumber").text(`GST Number: ${saved?.casual_gst_number || '-'}`).show();
                if (saved?.casual_gst_certificate) {
                    $("#gstSubmittedViewLink").attr('href', resolveUrl(saved.casual_gst_certificate)).show();
                } else {
                    $("#gstSubmittedViewLink").hide();
                }
            }
        }

        function showForm(saved) {
            $("#gstSubmittedBox").hide();
            $("#gstForm").show();

            const $statusPill = $("#gstStatusPill");
            $statusPill.html('<i class="bi bi-clock-history"></i> Pending Update').removeClass('is-submitted');

            if (isSameCity) {
                $("#sameCityBox").show();
            }

            if (saved) {
                $("#casualTradeName").val(saved.casual_trade_name || '');
                $("#casualGstNumber").val(saved.casual_gst_number || '');

                if (saved.same_as_city == 1) {
                    $("#sameAsCityCheckbox").prop('checked', true).trigger('change');
                }

                const certificateUrl = resolveUrl(saved.casual_gst_certificate);
                const $viewLink = $("#viewCertificateLink");
                if (certificateUrl) {
                    $viewLink.attr('href', certificateUrl).show();
                } else {
                    $viewLink.attr('href', '#').hide();
                }

                if ($gstForm.data('validator')) {
                    const $certificateField = $("#fileUpload");
                    if (certificateUrl) {
                        $certificateField.rules('remove', 'required');
                    } else {
                        $certificateField.rules('add', {
                            required: true
                        });
                    }
                    $gstForm.validate().element($certificateField);
                }
            }
        }

        $(document).on('click', '#gstEditBtn', function() {
            showForm(window.__savedGst || null);
        });

        function fetchCasualGstDetails() {
            const token = localStorage.getItem('api_token') || sessionStorage.getItem('api_token') || '';
            $.ajax({
                url: GST_URL,
                type: 'GET',
                headers: {
                    Authorization: token ? `Bearer ${token}` : ''
                },
                dataType: 'json',
                success(response) {
                    const data = response?.data || {};
                    isSameCity = !!data.is_same_city;
                    isSubmitted = !!data.is_submitted;
                    window.__savedGst = data.saved_gst || null;
                    renderEventInfo(data.event_details);
                    if (isSubmitted) {
                        showSubmittedState(data.saved_gst);
                    } else {
                        showForm(data.saved_gst);
                    }
                },
                error() {
                    $("#gstForm").show();
                    $("#gstStatusPill").html('<i class="bi bi-clock-history"></i> Pending Update');
                }
            });
        }

        function submitGstForm(form) {
            const formData = new FormData(form);
            const token = localStorage.getItem('api_token') || sessionStorage.getItem('api_token') || '';
            $.ajax({
                url: GST_URL,
                type: 'POST',
                headers: {
                    Authorization: token ? `Bearer ${token}` : ''
                },
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success(response) {
                    const message = response.message || (response.status ? 'Submitted successfully' : 'Submission failed');
                    if (response.status || response.success) {
                        alert(message);
                        fetchCasualGstDetails();
                    } else {
                        alert(message);
                    }
                },
                error(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'Server error. Please try again.';
                    alert(errorMessage);
                }
            });
            return false;
        }

        fetchCasualGstDetails();
    });
</script>
<?= $this->endSection() ?>
<?= $this->section('custom-script') ?>
<?= $this->endSection() ?>