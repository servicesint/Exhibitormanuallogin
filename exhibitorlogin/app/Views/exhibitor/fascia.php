<?= $this->extend('layout/main-layout') ?>
<?= $this->section('content') ?>

<style>
    .readonly-message {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 20px;
        display: none;
    }

    .readonly-message i {
        color: #856404;
        margin-right: 10px;
    }

    .form-readonly {
        opacity: 0.7;
        pointer-events: none;
    }

    .form-readonly .btn-primary,
    .form-readonly .btn-success,
    .form-readonly .btn-danger,
    .form-readonly .btn-warning,
    .form-readonly button[type="submit"] {
        pointer-events: none;
        opacity: 0.5;
    }

    .form-readonly input:not([readonly]),
    .form-readonly select:not([disabled]),
    .form-readonly textarea:not([readonly]) {
        pointer-events: none;
        background-color: #e9ecef;
    }

    .fascia-btn.disabled-btn {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .fascia-upload-btn.disabled-btn {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .fascia-select-wrap select:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .fascia-group-hidden {
        display: none !important;
    }

    .fascia-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 28px 20px 48px;
    }

    .fascia-card {
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 12px 32px rgba(21, 50, 101, 0.06);
        overflow: hidden;
        border: 1px solid #eef2f8;
    }

    .fascia-card-head {
        padding: 30px 32px 24px;
        background: #fafbfd;
        border-bottom: 1px solid #eef2f8;
    }

    .fascia-kicker {
        display: inline-block;
        margin-bottom: 8px;
        color: #5b7bab;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .fascia-title {
        font-size: 1.7rem;
        margin: 0 0 8px;
        color: #253345;
        line-height: 1.2;
        font-weight: 700;
    }

    .fascia-card-head p {
        margin: 0;
        color: #6b7891;
        max-width: 560px;
        font-size: 0.94rem;
        line-height: 1.65;
    }

    .fascia-form-layout {
        padding: 28px 32px 32px;
    }

    .fascia-section-label {
        display: block;
        margin-bottom: 8px;
        color: #3c4a5e;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .fascia-select-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 16px;
        border: 1px solid #e2e8f1;
        border-radius: 12px;
        background: #f8f9fc;
        transition: border-color 0.15s ease;
    }

    .fascia-select-wrap:focus-within {
        border-color: #93b4e8;
        background: #fff;
    }

    .fascia-select-wrap i {
        color: #7091c4;
        font-size: 1.05rem;
    }

    .fascia-select-wrap select {
        border: none;
        background: transparent;
        width: 100%;
        font-size: 0.94rem;
        color: #2b3a4f;
        min-height: 28px;
        padding: 0;
    }

    .fascia-select-wrap select:focus {
        outline: none;
        box-shadow: none;
    }

    .fascia-select-wrap select:disabled {
        opacity: 1;
        background: transparent;
        color: #2b3a4f;
        -webkit-text-fill-color: #2b3a4f;
        cursor: default;
    }

    .scheme-select-pending {
        visibility: hidden;
    }

    .fascia-group {
        margin-bottom: 18px;
    }

    .fascia-input-wrap {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 11px 16px;
        border: 1px solid #e2e8f1;
        border-radius: 12px;
        background: #f8f9fc;
        transition: border-color 0.15s ease;
    }

    .fascia-input-wrap:focus-within {
        border-color: #93b4e8;
        background: #fff;
    }

    .fascia-input-wrap i {
        color: #7091c4;
        font-size: 1.05rem;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .fascia-input-wrap input,
    .fascia-input-wrap textarea {
        border: none;
        background: transparent;
        width: 100%;
        font-size: 0.92rem;
        color: #2b3a4f;
        padding: 0;
    }

    .fascia-input-wrap textarea {
        resize: vertical;
        min-height: 66px;
    }

    .fascia-input-wrap input:focus,
    .fascia-input-wrap textarea:focus {
        outline: none;
        box-shadow: none;
    }

    .fascia-input-wrap.is-invalid-wrap {
        border-color: #e6a3a3;
        background: #fdf6f6;
    }

    .validation-error {
        color: #c4574f;
        font-size: 0.8em;
        margin-top: 5px;
        display: block;
    }

    .electricity-price-note {
        margin-top: 6px;
        font-size: 0.84rem;
        color: #4a72b8;
        font-weight: 600;
        display: none;
    }

    .fascia-upload {
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

    .fascia-upload-icon {
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

    .fascia-upload-copy {
        flex: 1 1 220px;
    }

    .fascia-upload-copy strong {
        display: block;
        margin-bottom: 3px;
        font-weight: 600;
        color: #2b3a4f;
        font-size: 0.9rem;
    }

    .fascia-upload-copy span {
        display: block;
        color: #7c879a;
        font-size: 0.84rem;
        line-height: 1.55;
    }

    .fascia-upload-btn {
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

    .fascia-upload-btn:hover {
        background: #3d5f9c;
    }

    .fascia-upload-btn.disabled-btn {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .fascia-upload input[type="file"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    .fascia-file-name {
        display: block;
        color: #8792a3;
        font-size: 0.82rem;
        width: 100%;
    }

    .fascia-preview-block {
        margin-top: 12px;
    }

    .preview-wrap {
        position: relative;
        display: inline-block;
    }

    .fascia-design-preview {
        cursor: pointer;
        border-radius: 10px;
        transition: opacity 0.15s ease;
    }

    .fascia-design-preview:hover {
        opacity: 0.88;
    }

    .pdf-preview-box {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: #fff;
        border: 1px solid #e2e8f1;
        border-radius: 12px;
        cursor: pointer;
        max-width: 290px;
        transition: border-color 0.15s ease;
    }

    .pdf-preview-box:hover {
        border-color: #b9c8dd;
    }

    .pdf-preview-box i {
        font-size: 1.6rem;
        color: #c4574f;
        flex-shrink: 0;
    }

    .pdf-preview-box span {
        font-size: 0.82rem;
        color: #5c6b81;
        font-weight: 600;
        word-break: break-all;
    }

    .fabricator-card {
        background: #f8f9fc;
        border: 1px solid #e9edf5;
        border-radius: 16px;
        padding: 22px 24px;
        margin-top: 6px;
    }

    .fabricator-card-title {
        display: flex;
        align-items: center;
        gap: 9px;
        font-weight: 600;
        color: #2b3a4f;
        font-size: 0.96rem;
        margin-bottom: 18px;
    }

    .fabricator-card-title i {
        color: #5b7bab;
        font-size: 1.05rem;
    }

    .fab-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 14px;
    }

    .fab-col-2 {
        grid-column: span 2;
    }

    .fab-col-5 {
        grid-column: span 5;
    }

    .fab-col-6 {
        grid-column: span 6;
    }

    .fab-label {
        display: block;
        margin-bottom: 5px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #7c879a;
    }

    .fab-input-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border: 1px solid #e2e8f1;
        border-radius: 12px;
        background: #fff;
        transition: border-color 0.15s ease;
    }

    .fab-input-wrap:focus-within {
        border-color: #93b4e8;
    }

    .fab-input-wrap i {
        color: #7091c4;
        font-size: 0.98rem;
        flex-shrink: 0;
    }

    .fab-input-wrap input,
    .fab-input-wrap textarea {
        border: none;
        background: transparent;
        width: 100%;
        font-size: 0.9rem;
        color: #2b3a4f;
        padding: 0;
    }

    .fab-input-wrap input:focus,
    .fab-input-wrap textarea:focus {
        outline: none;
        box-shadow: none;
    }

    .fab-input-wrap textarea {
        resize: vertical;
        min-height: 42px;
    }

    .fascia-notes {
        background: #fdf9f0;
        border: 1px solid #f0e4c8;
        border-radius: 14px;
        padding: 16px 18px;
        margin-top: 6px;
    }

    .fascia-notes-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #8a6d2e;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }

    .note-info {
        margin-bottom: 6px;
        color: #7a6a45;
        font-size: 0.84rem;
        line-height: 1.55;
    }

    .note-info:last-child {
        margin-bottom: 0;
    }

    .fascia-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }

    .fascia-btn {
        min-width: 150px;
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

    .fascia-btn:hover {
        background: #3d5f9c;
    }

    .fascia-btn.disabled-btn {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .status-badge {
        font-size: 0.72rem;
        vertical-align: middle;
        margin-left: 6px;
        text-transform: capitalize;
        font-weight: 600;
    }

    .rejection-reason-box {
        margin-top: 10px;
        padding: 10px 14px;
        background: #fdf3f2;
        border: 1px solid #f2c9c5;
        border-radius: 10px;
        color: #9c3a32;
        font-size: 0.84rem;
        line-height: 1.5;
        display: none;
    }

    .rejection-reason-box strong {
        display: block;
        margin-bottom: 3px;
        font-weight: 700;
    }

    .guidelines-content {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 4px 0;
    }

    .guideline-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 12px 16px;
        background: #f8f9fc;
        border-radius: 10px;
        border-left: 3px solid #4a72b8;
    }

    .guideline-item i {
        font-size: 1.2rem;
        margin-top: 2px;
        flex-shrink: 0;
        color: #4a72b8;
    }

    .guideline-item i.text-warning {
        color: #ffc107;
    }

    .guideline-item i.text-danger {
        color: #dc3545;
    }

    .guideline-item strong {
        display: block;
        color: #253345;
        font-size: 0.9rem;
        margin-bottom: 2px;
    }

    .guideline-item p {
        margin: 0;
        color: #6b7891;
        font-size: 0.85rem;
        line-height: 1.5;
    }

    #designGuidelinesModal .modal-content {
        border: none;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(20, 40, 80, 0.18);
    }

    #designGuidelinesModal .modal-header {
        background: #f8f9fc;
        padding: 18px 24px;
        border-bottom: 1px solid #eef2f8;
    }

    #designGuidelinesModal .modal-title {
        color: #253345;
        font-weight: 700;
        font-size: 1.05rem;
        display: flex;
        align-items: center;
    }

    #designGuidelinesModal .modal-body {
        padding: 24px;
        background: #fff;
    }

    #designGuidelinesModal .modal-footer {
        border-top: 1px solid #eef2f8;
        padding: 14px 24px;
        background: #fff;
    }

    #designGuidelinesModal .btn-primary {
        border-radius: 999px;
        padding: 10px 28px;
        font-weight: 600;
        background: #4a72b8;
        border: none;
    }

    #designGuidelinesModal .btn-primary:hover {
        background: #3d5f9c;
    }

    @media (max-width: 700px) {

        .fascia-card-head,
        .fascia-form-layout {
            padding-left: 20px;
            padding-right: 20px;
        }

        .fab-col-2,
        .fab-col-5,
        .fab-col-6 {
            grid-column: span 12;
        }
    }

    @media (max-width: 520px) {
        .fascia-card-head {
            padding: 20px 16px 14px;
        }

        .fascia-wrapper {
            padding: 16px 12px 28px;
        }

        .fascia-form-layout {
            padding: 20px 16px 24px;
        }

        .fascia-upload {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }

        .fascia-upload-btn {
            width: 100%;
        }
    }

    #designPreviewModal .modal-content {
        border: none;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(20, 40, 80, 0.18);
    }

    #designPreviewModal .modal-header {
        background: #f8f9fc;
        padding: 16px 22px;
        border-bottom: 1px solid #eef2f8;
    }

    #designPreviewModal .modal-title {
        color: #2b3a4f;
        font-weight: 600;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    #designPreviewModal .modal-title i {
        color: #5b7bab;
        font-size: 1.05rem;
    }

    #designPreviewModal .modal-body {
        padding: 24px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 280px;
    }

    #designPreviewModal .preview-modal-image-wrap {
        position: relative;
        display: inline-block;
        border-radius: 12px;
        overflow: hidden;
        background: #f8f9fc;
        padding: 8px;
    }

    #designPreviewModalImg {
        display: none;
        border-radius: 8px;
        max-width: 100%;
        max-height: 65vh;
    }

    #designPreviewModalPdf {
        display: none;
        width: 100%;
        height: 68vh;
        border: none;
        border-radius: 8px;
    }

    #designPreviewModal .modal-footer {
        border-top: 1px solid #eef2f8;
        padding: 14px 22px;
        background: #fff;
    }

    #designPreviewModal .download-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        border-radius: 999px;
        background: #f2f5fb;
        color: #4a72b8;
        font-weight: 600;
        font-size: 0.86rem;
        text-decoration: none;
        border: 1px solid #e2e8f1;
        transition: background 0.15s ease;
    }

    #designPreviewModal .download-btn:hover {
        background: #e7edf8;
    }

    .fascia-card-head-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 8px;
    }

    .fascia-card-head-top .fascia-title {
        margin-bottom: 0;
    }

    .badge-date {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        background: #eaf0fb;
        border-radius: 999px;
        font-size: 0.9rem;
        color: #2b3a4f;
        font-weight: 500;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .badge-date i {
        color: #5b7bab;
    }

    .badge-date strong {
        font-weight: 700;
    }

    .fascia-preview-block {
        margin-top: 12px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
    }

    .preview-wrap {
        position: relative;
        display: inline-block;
        flex-shrink: 0;
    }

    .rejection-reason-box {
        margin-top: 0;
        padding: 10px 14px;
        background: #fdf3f2;
        border: 1px solid #f2c9c5;
        border-radius: 10px;
        color: #9c3a32;
        font-size: 0.84rem;
        line-height: 1.5;
        display: none;
        flex: 1 1 280px;
        max-width: 320px;
    }

    .rejection-reason-box strong {
        display: block;
        margin-bottom: 3px;
        font-weight: 700;
    }
</style>

<?php
$saved = $saved_fascia ?? [];
$selectedScheme = (int) ($saved['stall_type_id'] ?? 0);
if (!in_array($selectedScheme, [1, 2, 3], true)) {
    $selectedScheme = 1;
}

?>

<div class="content-area">
    <div class="fascia-wrapper">
        <div id="readonlyMessage" class="readonly-message" style="display:none;">
            <i class="bi bi-info-circle"></i>
            <span>Fascias form is currently closed. You can only view your existing submissions. New submissions are not allowed.</span>
        </div>
        <div class="fascia-card">
            <div class="fascia-card-head">
                <div class="fascia-card-head-top">
                    <div>
                        <span class="fascia-kicker">Stand Setup</span>
                        <h4 class="fascia-title" id="fasciaTitle">Fascia &amp; Stand Details</h4>
                    </div>
                    <span class="badge-date">
                        <i class="bi bi-calendar-event"></i>
                        Due Date:&nbsp;<strong id="fasciaduedate">--</strong>
                    </span>
                </div>
                <p id="fasciaSubtitle">Tell us how your stand will be built so we can prepare the right fascia board, layout, and approvals ahead of the event.</p>
            </div>
            <div class="fascia-form-layout">
                <form id="shellForm" method="post" style="display:none;" enctype="multipart/form-data">
                    <input type="hidden" name="fascia_category" value="Shell Space">
                    <div class="fascia-group">
                        <label class="fascia-section-label">Precise wording to appear on Fascia Board (Stand Number &amp; Company Name/Brand Name)</label>
                        <div class="fascia-input-wrap">
                            <i class="bi bi-signpost-2"></i>
                            <textarea class="form-control" name="fascia_board_text" maxlength="26"><?= esc($saved['fascia_board_text'] ?? '') ?></textarea>
                        </div>
                        <small class="text-muted">Maximum 26 characters</small>
                    </div>
                    <div class="fascia-group">
                        <label class="fascia-section-label">Stand Layout Details (e.g. corner stand, 2 side walls / 3 side walls)</label>
                        <div class="fascia-input-wrap">
                            <i class="bi bi-bounding-box"></i>
                            <textarea class="form-control" name="stall_open_side"><?= esc($saved['stall_open_side'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="fascia-actions">
                        <button type="submit" class="btn fascia-btn" id="shellSubmitBtn">Submit</button>
                    </div>
                </form>
                <form id="rawForm" method="post" style="display:none;" enctype="multipart/form-data">
                    <input type="hidden" name="fascia_category" value="Raw Space">
                    <div class="fascia-group">
                        <label class="fascia-section-label">Electricity Requirement in KW</label>
                        <div class="fascia-input-wrap">
                            <i class="bi bi-lightning-charge"></i>
                            <input
                                type="text"
                                class="form-control"
                                name="electricity_requirement"
                                id="electricityRequirement"
                                inputmode="numeric"
                                maxlength="6"
                                value="<?= !empty($saved['electricity_requirement']) ? esc((int) $saved['electricity_requirement']) : '' ?>">
                        </div>
                        <small class="electricity-price-note" id="electricityPriceNote"></small>
                    </div>
                    <div class="fascia-group">
                        <label class="fascia-section-label">Upload the stall 3D design/render. (.pdf, .jpg, .jpeg or .png — Max 2 MB)</label>
                        <div class="fascia-upload">
                            <input type="file" id="fasciaDesignInput" name="fascia_design" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="fascia-upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                            <div class="fascia-upload-copy">
                                <strong>Select your design file</strong>
                                <span>PDF, JPG, PNG formats accepted. Max size 2MB.</span>
                            </div>
                            <label for="fasciaDesignInput" class="fascia-upload-btn" id="uploadBtnLabel">Choose File</label>
                            <span class="fascia-file-name">No file chosen</span>
                        </div>
                        <div class="fascia-preview-block">
                            <label class="fascia-section-label mb-2" style="width:100%;">
                                Design Preview
                                <span id="designStatus" class="badge status-badge"><?= esc(ucfirst(strtolower($saved['fascia_design_status'] ?? $saved['status'] ?? ''))) ?></span>
                            </label>
                            <div class="preview-wrap" id="fasciaPreviewContainer" style="<?= empty($saved['fascia_design']) ? 'display:none;' : '' ?>"></div>
                            <div id="designRejectionReason" class="rejection-reason-box"></div>
                        </div>
                    </div>
                    <div class="fascia-group">
                        <label class="fascia-section-label">Fabricator Details</label>
                        <div class="fabricator-card">
                            <div class="fabricator-card-title">
                                <i class="bi bi-person-badge"></i> Fabricator Contact Information
                            </div>
                            <div class="fab-grid">
                                <div class="fab-col-2">
                                    <label class="fab-label">Salutation</label>
                                    <div class="fab-input-wrap">
                                        <i class="bi bi-person"></i>
                                        <select class="form-control" name="salutation">
                                            <option value="">Select</option>
                                            <?php
                                            $salutations = ['Mr.', 'Ms.'];
                                            foreach ($salutations as $s):
                                            ?>
                                                <option value="<?= esc($s) ?>" <?= (($saved['salutation'] ?? '') === $s) ? 'selected' : '' ?>>
                                                    <?= esc($s) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="fab-col-5">
                                    <label class="fab-label">First Name</label>
                                    <div class="fab-input-wrap">
                                        <i class="bi bi-person-lines-fill"></i>
                                        <input type="text" class="form-control" name="first_name" placeholder="First Name" value="<?= esc($saved['first_name'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="fab-col-5">
                                    <label class="fab-label">Last Name</label>
                                    <div class="fab-input-wrap">
                                        <i class="bi bi-person-lines-fill"></i>
                                        <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="<?= esc($saved['last_name'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="fab-col-6">
                                    <label class="fab-label">Company Name</label>
                                    <div class="fab-input-wrap">
                                        <i class="bi bi-building"></i>
                                        <input type="text" class="form-control" name="fabricator_company_name" placeholder="Company Name" maxlength="26" value="<?= esc($saved['fabricator_company_name'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="fab-col-6">
                                    <label class="fab-label">Contact Number</label>
                                    <div class="fab-input-wrap">
                                        <i class="bi bi-telephone"></i>
                                        <input type="text" class="form-control" name="mobile_number" placeholder="Contact Number" value="<?= esc($saved['mobile_number'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="fab-col-6">
                                    <label class="fab-label">Email</label>
                                    <div class="fab-input-wrap">
                                        <i class="bi bi-envelope"></i>
                                        <input type="email" class="form-control" name="email" placeholder="Email" value="<?= esc($saved['email'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="fascia-group" id="rawNotesBlock">
                        <div class="fascia-notes" id="fasciaNotesContent"></div>
                    </div>
                    <div class="fascia-actions">
                        <button type="submit" class="btn fascia-btn" id="rawSubmitBtn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="designPreviewModal" tabindex="-1" aria-labelledby="designPreviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="designPreviewModalLabel">
                            <i class="bi bi-image"></i> Design Preview
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="preview-modal-image-wrap">
                            <img id="designPreviewModalImg" src="" alt="Design preview">
                            <iframe id="designPreviewModalPdf" src=""></iframe>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a id="designPreviewDownloadBtn" href="#" download class="download-btn" target="_blank" rel="noopener">
                            <i class="bi bi-download"></i> Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="designGuidelinesModal" tabindex="-1" aria-labelledby="designGuidelinesModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="designGuidelinesModalLabel">
                            <i class="bi bi-info-circle-fill text-primary me-2"></i>Design Upload Guidelines
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="guidelinesCloseBtn"></button>
                    </div>
                    <div class="modal-body">
                        <div class="guidelines-content">
                            <div class="guideline-item">
                                <i class="bi bi-rulers"></i>
                                <div>
                                    <strong>Stall Dimensions</strong>
                                    <p>Mention stall dimensions clearly (Width × Depth × Height).</p>
                                </div>
                            </div>
                            <div class="guideline-item">
                                <i class="bi bi-box"></i>
                                <div>
                                    <strong>3D Design/Render</strong>
                                    <p>Upload the stall 3D design/render.</p>
                                </div>
                            </div>
                            <div class="guideline-item">
                                <i class="bi bi-layout-three-columns"></i>
                                <div>
                                    <strong>Open Side(s)</strong>
                                    <p>Mark the open side(s) in the layout.</p>
                                </div>
                            </div>
                            <div class="guideline-item">
                                <i class="bi bi-arrow-up-circle"></i>
                                <div>
                                    <strong>Height Limit</strong>
                                    <p>Ensure the stall height is within the permissible limit.</p>
                                </div>
                            </div>
                            <div class="guideline-item">
                                <i class="bi bi-lightning-charge text-warning"></i>
                                <div>
                                    <strong>Electricity Load</strong>
                                    <p>Order and pay for the required electricity load.</p>
                                </div>
                            </div>
                            <div class="guideline-item">
                                <i class="bi bi-exclamation-triangle text-danger"></i>
                                <div>
                                    <strong>Important Note</strong>
                                    <p>Enter the electricity load accurately; excess consumption during the event will be charged additionally.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="guidelinesOkBtn">
                            <i class="bi bi-check-circle me-1"></i> OK, I Understand
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>
<script>
    $(function() {
        var guidelinesShown = false;
        var fileInputTriggered = false;

        function showGuidelinesModal() {
            if (guidelinesShown) {
                return true;
            }
            var modal = new bootstrap.Modal(document.getElementById('designGuidelinesModal'));
            modal.show();
            return false;
        }

        $('#uploadBtnLabel').on('click', function(e) {
            if (isViewOnly) {
                e.preventDefault();
                return;
            }
            if (!guidelinesShown) {
                e.preventDefault();
                var modal = new bootstrap.Modal(document.getElementById('designGuidelinesModal'));
                modal.show();
            }
        });

        $('#guidelinesOkBtn, #guidelinesCloseBtn').on('click', function() {
            guidelinesShown = true;
            var modal = bootstrap.Modal.getInstance(document.getElementById('designGuidelinesModal'));
            if (modal) {
                modal.hide();
            }
            setTimeout(function() {
                document.getElementById('fasciaDesignInput').click();
            }, 300);
        });

        $('#designGuidelinesModal').on('hidden.bs.modal', function() {
            if (!guidelinesShown) {
                guidelinesShown = true;
                setTimeout(function() {
                    document.getElementById('fasciaDesignInput').click();
                }, 300);
            }
        });

        $('#fasciaDesignInput').on('click', function(e) {
            if (!guidelinesShown && !isViewOnly) {
                e.preventDefault();
                var modal = new bootstrap.Modal(document.getElementById('designGuidelinesModal'));
                modal.show();
            }
        });

        const $shellForm = $("#shellForm");
        const $rawForm = $("#rawForm");
        const $rawNotesBlock = $("#rawNotesBlock");
        const API_BASE_URL = '<?= env('API_BASE_URL') ?>';
        const FASCIA_URL = `${API_BASE_URL}/v1/dashboard/fascia`;
        const ADD_TO_CART_URL = `${API_BASE_URL}/v1/cart/add`;
        const ELECTRICITY_ITEM_URL = `${API_BASE_URL}/v1/dashboard/electricity-item`;
        const CART_PAGE_PATH = '/additional-furniture';

        let ELECTRICITY_ITEM_ID = null;
        let ELECTRICITY_RATE_PER_KW = null;
        let electricityItemLoaded = false;
        let pendingElectricityQuantity = null;

        let isViewOnly = false;
        let electricityDebounceTimer = null;
        let lastElectricityQuantitySynced = null;

        function renderFasciaNotes(rawText) {
            const $notes = $('#fasciaNotesContent');
            if (!rawText) {
                $notes.empty();
                return;
            }
            $notes.html(rawText);
        }

        function checkFasciaStatus() {
            let status = 'enabled_open';
            if (window.getFormStatus) {
                status = window.getFormStatus('fascia');
            } else if (window.onlineFormsEnableDisable) {
                const enabled = parseInt(window.onlineFormsEnableDisable.fascia, 10) === 1;
                const open = parseInt(window.onlineFormsOpenClose.fascia, 10) === 1;
                if (!enabled) status = 'disabled';
                else if (!open) status = 'enabled_closed';
            }
            if (status === 'disabled') {
                if (window.showToast) {
                    window.showToast('Fascias form is currently disabled.', 'error');
                } else {
                    alert('Fascias form is currently disabled.');
                }
                setTimeout(function() {
                    window.location.href = window.BASE_URL || '/dashboard';
                }, 1500);
                return false;
            }

            if (status === 'enabled_closed' || status === 'expired') {
                isViewOnly = true;
                const msgDiv = document.getElementById('readonlyMessage');
                if (msgDiv) {
                    if (status === 'expired') {
                        msgDiv.querySelector('span').textContent = 'The due date for Fascias has passed. You can view your existing submission but can no longer make changes.';
                    }
                    msgDiv.style.display = 'block';
                }
                $shellForm.find('input, textarea, select').prop('disabled', true);
                $rawForm.find('input, textarea, select').prop('disabled', true);
                $('#shellSubmitBtn, #rawSubmitBtn').addClass('disabled-btn').prop('disabled', true);
                $('#fasciaDesignInput').prop('disabled', true);
                $('#uploadBtnLabel').addClass('disabled-btn');
                if (window.showToast) {
                    const msg = status === 'expired' ?
                        'Due date has passed. View only mode.' :
                        'View only mode. You can view your existing submissions only.';
                    window.showToast(msg, 'warning');
                }
            }

            return true;
        }

        function renderFasciaDueDate() {
            const dueDate = window.onlineFormsDueDates && window.onlineFormsDueDates.fascia;
            const $dueDateEl = $('#fasciaduedate');

            if (!dueDate) {
                $dueDateEl.text('--');
                return;
            }

            let formatted = dueDate;
            if (window.moment) {
                const m = moment(dueDate);
                if (m.isValid()) {
                    formatted = m.format('DD MMM YYYY');
                }
            }
            $dueDateEl.text(formatted);
        }

        function setRawSubmitDisabled(disabled) {
            const $btn = $('#rawSubmitBtn');
            $btn.prop('disabled', disabled);
            $btn.toggleClass('disabled-btn', disabled);
        }

        function getAuthToken() {
            return localStorage.getItem('api_token') || sessionStorage.getItem('api_token') || '';
        }

        function fetchElectricityItem() {
            const token = getAuthToken();
            return $.ajax({
                url: ELECTRICITY_ITEM_URL,
                type: 'GET',
                headers: {
                    Authorization: token ? `Bearer ${token}` : ''
                },
                dataType: 'json'
            }).then(function(response) {
                if (response && response.status && response.data) {
                    ELECTRICITY_ITEM_ID = response.data.item_id;
                    ELECTRICITY_RATE_PER_KW = parseFloat(response.data.rate_per_kw);
                    electricityItemLoaded = true;
                } else {
                    electricityItemLoaded = false;
                    if (window.showToast) {
                        window.showToast((response && response.message) || 'Electricity item not configured.', 'error');
                    }
                }
            }).catch(function() {
                electricityItemLoaded = false;
                if (window.showToast) window.showToast('Unable to load electricity pricing.', 'error');
            }).always(function() {
                if (pendingElectricityQuantity) {
                    const qty = pendingElectricityQuantity;
                    pendingElectricityQuantity = null;
                    handleElectricityInputChange(qty);
                }
            });
        }

        function updateElectricityPriceNote(quantity) {
            const $note = $('#electricityPriceNote');
            if (!quantity || quantity <= 0 || !electricityItemLoaded) {
                $note.hide().text('');
                return;
            }
            const price = quantity * ELECTRICITY_RATE_PER_KW;
            $note.text(`${quantity} KW × ₹${ELECTRICITY_RATE_PER_KW} = ₹${price.toFixed(2)} — added to cart`).show();
        }

        function getCartPageUrl() {
            const base = (window.BASE_URL || '').replace(/\/$/, '');
            return `${base}${CART_PAGE_PATH}?view=cart`;
        }

        function notifyElectricityAddedToCart(quantity) {
            const price = quantity * ELECTRICITY_RATE_PER_KW;
            const message = `Electricity (${quantity} KW, ₹${price.toFixed(2)}) added to your cart. Please visit the cart page to complete payment.`;
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Added to Cart',
                    text: message,
                    toast: true,
                    position: 'top-end',
                    timer: 4000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            } else if (window.showToast) {
                window.showToast(message, 'success');
            }
        }

        function addElectricityToCart(quantity) {
            if (!electricityItemLoaded || !ELECTRICITY_ITEM_ID) {
                if (window.showToast) window.showToast('Electricity pricing not loaded yet. Please retry.', 'error');
                return;
            }
            const token = getAuthToken();
            if (!token) {
                if (window.showToast) window.showToast('Login token missing. Please login again.', 'error');
                return;
            }
            return $.ajax({
                url: ADD_TO_CART_URL,
                type: 'POST',
                headers: {
                    Authorization: `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify({
                    item_id: ELECTRICITY_ITEM_ID,
                    quantity: quantity
                }),
                dataType: 'json'
            }).then(function(response) {
                if (response && response.status) {
                    lastElectricityQuantitySynced = quantity;
                    updateElectricityPriceNote(quantity);

                    window.dispatchEvent(new CustomEvent('cartUpdated', {
                        detail: response.data || {}
                    }));

                    if (window.refreshCart) {
                        window.refreshCart();
                    }

                    notifyElectricityAddedToCart(quantity);
                    return response;
                }

                throw new Error(
                    (response && response.message) ||
                    'Unable to add electricity to cart.'
                );
            }).catch(function(xhr) {
                const msg = xhr?.responseJSON?.message ||
                    xhr?.message ||
                    'Unable to add electricity to cart.';

                throw new Error(msg);
            });
        }

        function handleElectricityInputChange(forcedQuantity) {
            const raw = forcedQuantity !== undefined
                ? String(forcedQuantity)
                : $('#electricityRequirement').val();

            const quantity = parseInt(raw, 10);

            if (electricityDebounceTimer) {
                clearTimeout(electricityDebounceTimer);
                electricityDebounceTimer = null;
            }

            if (!raw || isNaN(quantity) || quantity <= 0) {
                updateElectricityPriceNote(0);
                return;
            }

            if (!electricityItemLoaded) {
                pendingElectricityQuantity = quantity;
                return;
            }

            updateElectricityPriceNote(quantity);
        }

        document.getElementById("fasciaDesignInput").addEventListener("change", function() {
            const file = this.files[0];
            const fileNameEl = document.querySelector(".fascia-file-name");

            if (!file) {
                fileNameEl.innerText = "No file chosen";
                return;
            }

            setRawSubmitDisabled(true);

            const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            const maxSizeBytes = 2 * 1024 * 1024;
            const ext = file.name.split('.').pop().toLowerCase();

            if (!allowedExtensions.includes(ext)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid file type',
                    text: 'Only .pdf, .jpg, .jpeg or .png files are allowed.'
                });
                this.value = '';
                fileNameEl.innerText = "No file chosen";
                setRawSubmitDisabled(false);
                return;
            }

            if (file.size > maxSizeBytes) {
                Swal.fire({
                    icon: 'error',
                    title: 'File too large',
                    text: 'Maximum file size is 2 MB.'
                });
                this.value = '';
                fileNameEl.innerText = "No file chosen";
                setRawSubmitDisabled(false);
                return;
            }

            fileNameEl.innerText = file.name;
            setRawSubmitDisabled(false);
        });
        $(document).on('input', '#electricityRequirement', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            handleElectricityInputChange();
        });
        $(document).on('paste', '#electricityRequirement', function(e) {
            e.preventDefault();
            const pasted = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
            const digitsOnly = pasted.replace(/[^0-9]/g, '');
            document.execCommand('insertText', false, digitsOnly);
            handleElectricityInputChange();
        });

        function isPdfUrl(url) {
            return /\.pdf(\?.*)?$/i.test(url || '');
        }
        const UPLOAD_BASE_URL = '<?= rtrim(env('UPLOAD_BASE_URL'), '/') ?>';

        function buildFileUrl(value) {
            if (!value) return '';
            if (/^https?:\/\//i.test(value)) {
                return value;
            }
            const relativePath = String(value).replace(/^\/+/, '');
            return `${UPLOAD_BASE_URL}/${relativePath}`;
        }

        function renderFasciaPreview(fileUrl) {
            const $container = $('#fasciaPreviewContainer');
            fileUrl = buildFileUrl(fileUrl);
            if (!fileUrl) {
                $container.hide().empty();
                return;
            }
            $container.show();
            if (isPdfUrl(fileUrl)) {
                const fileName = fileUrl.split('/').pop().split('?')[0];
                $container.html(`
                    <div class="pdf-preview-box" data-file-url="${fileUrl}">
                        <i class="bi bi-file-earmark-pdf-fill"></i>
                        <span>${fileName}</span>
                    </div>
                `);
            } else {
                $container.html(`
                    <img
                        class="fascia-design-preview"
                        src="${fileUrl}"
                        alt="Uploaded design preview"
                        data-file-url="${fileUrl}"
                        style="max-width:160px; max-height:120px; border:1px solid #e2e8f1;">
                `);
            }
        }

        function openPreviewModal(fileUrl) {
            const $img = $('#designPreviewModalImg');
            const $pdf = $('#designPreviewModalPdf');
            if (isPdfUrl(fileUrl)) {
                $img.hide().attr('src', '');
                $pdf.show().attr('src', fileUrl);
            } else {
                $pdf.hide().attr('src', '');
                $img.show().attr('src', fileUrl);
            }
            $('#designPreviewDownloadBtn').attr('href', fileUrl);
            const modal = new bootstrap.Modal(document.getElementById('designPreviewModal'));
            modal.show();
        }
        $(document).on('click', '.fascia-design-preview, .pdf-preview-box', function() {
            openPreviewModal($(this).data('file-url'));
        });

        function applyDesignStatus(status) {
            const $statusEl = $('#designStatus');
            const normalized = String(status || '').trim().toLowerCase();
            const displayText = normalized ? normalized.charAt(0).toUpperCase() + normalized.slice(1) : '';
            $statusEl.text(displayText);
            $statusEl.removeClass('bg-secondary bg-danger bg-success bg-warning');
            if (normalized === 'pending') {
                $statusEl.addClass('bg-danger');
            } else if (normalized === 'approved') {
                $statusEl.addClass('bg-success');
            } else if (normalized === 'rejected') {
                $statusEl.addClass('bg-danger');
            } else {
                $statusEl.addClass('bg-secondary');
            }
        }

        function showRejectionReason(status, reason, otherReason) {
            const $reasonBox = $('#designRejectionReason');
            const normalized = String(status || '').trim().toLowerCase();

            if (normalized !== 'rejected') {
                $reasonBox.hide().empty();
                return;
            }

            const finalReason = (otherReason && String(otherReason).trim()) || (reason && String(reason).trim()) || '';
            if (finalReason) {
                $reasonBox.html(`<strong>Rejection Reason:</strong>${finalReason}`).show();
            } else {
                $reasonBox.hide().empty();
            }
        }

        function getUrlScheme() {
            const segments = window.location.pathname.split('/').filter(Boolean);
            const lastSegment = (segments[segments.length - 1] || '').toLowerCase();
            if (lastSegment === 'fascia') {
                return 2;
            }
            if (lastSegment === 'upload-stand-design') {
                return 3;
            }
            return null;
        }
        const urlScheme = getUrlScheme();
        let currentScheme = urlScheme || <?= (int) $selectedScheme ?>;

        $.validator.addMethod('fasciaFileType', function(value, element) {
            if (!element.files || element.files.length === 0) return true;
            const allowed = ['pdf', 'jpg', 'jpeg', 'png'];
            const ext = element.files[0].name.split('.').pop().toLowerCase();
            return allowed.includes(ext);
        }, 'Only .pdf, .jpg, .jpeg or .png files are allowed');

        $.validator.addMethod('fasciaFileSize', function(value, element) {
            if (!element.files || element.files.length === 0) return true;
            const maxBytes = 2 * 1024 * 1024;
            return element.files[0].size <= maxBytes;
        }, 'Maximum file size is 2 MB');
        const validatorDefaults = {
            errorClass: 'validation-error',
            errorElement: 'div',
            errorPlacement(error, element) {
                error.insertAfter(element.closest('.fascia-input-wrap, .fascia-upload, .fab-input-wrap'));
            },
            highlight(element) {
                $(element).closest('.fascia-input-wrap, .fab-input-wrap').addClass('is-invalid-wrap');
            },
            unhighlight(element) {
                $(element).closest('.fascia-input-wrap, .fab-input-wrap').removeClass('is-invalid-wrap');
            },
            invalidHandler(event, validator) {
                if (validator.numberOfInvalids()) {
                    const firstError = validator.errorList[0]?.message || 'Please fix the highlighted fields before submitting.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation error',
                        text: firstError
                    });
                }
            },
            submitHandler(form) {
                submitFasciaForm(form);
            }
        };

        function resetForm($form) {
            const validator = $form.validate();
            validator.resetForm();
            $form.find('.is-invalid-wrap').removeClass('is-invalid-wrap');
        }

        function toggleForms() {
            $shellForm.hide();
            $rawForm.hide();
            $rawNotesBlock.hide();
            resetForm($shellForm);
            resetForm($rawForm);

            if (currentScheme === 3) {
                $shellForm.show();
            } else if (currentScheme === 2) {
                $rawForm.show();
                $rawNotesBlock.show();
            }
        }

        function hydrateSavedFascia(saved) {
            if (!saved) return;
            const category = parseInt(saved.stall_type_id, 10) || 0;
            if (urlScheme) {
                currentScheme = urlScheme;
            } else if (category === 3) {
                currentScheme = 3;
            } else if (category === 2) {
                currentScheme = 2;
            } else {
                currentScheme = 1;
            }
            toggleForms();
            const fasciaDesign = saved.fascia_design || '';
            const designStatus = saved.fascia_design_status || saved.status;
            applyDesignStatus(designStatus);
            showRejectionReason(designStatus, saved.reason, saved.other_reason);
            renderFasciaPreview(fasciaDesign);
            const $rawDesignField = $rawForm.find('[name="fascia_design"]');
            if ($rawForm.data('validator')) {
                if (fasciaDesign) {
                    $rawDesignField.rules('remove', 'required');
                } else {
                    $rawDesignField.rules('add', {
                        required: true
                    });
                }
                $rawForm.validate().element($rawDesignField);
            }
            const fieldMap = {
                fascia_board_text: saved.fascia_board_text || '',
                stall_open_side: saved.stall_open_side || '',
                electricity_requirement: saved.electricity_requirement ? String(parseInt(saved.electricity_requirement, 10)) : '',
                salutation: saved.salutation || '',
                first_name: saved.first_name || '',
                last_name: saved.last_name || '',
                fabricator_company_name: saved.fabricator_company_name || '',
                mobile_number: saved.mobile_number || '',
                email: saved.email || ''
            };
            Object.entries(fieldMap).forEach(([name, value]) => {
                const $field = $form => $form.find(`[name="${name}"]`);
                $field($shellForm).val(value);
                $field($rawForm).val(value);
            });
            if (fieldMap.electricity_requirement) {
                const initialQty = parseInt(fieldMap.electricity_requirement, 10);
                if (!isNaN(initialQty) && initialQty > 0) {
                    if (electricityItemLoaded) {
                        lastElectricityQuantitySynced = initialQty;
                        updateElectricityPriceNote(initialQty);
                    } else {
                        pendingElectricityQuantity = initialQty;
                    }
                }
            }
        }

        function fetchSavedFascia() {
            const token = getAuthToken();
            $.ajax({
                url: FASCIA_URL,
                type: 'GET',
                headers: {
                    Authorization: token ? `Bearer ${token}` : ''
                },
                dataType: 'json',
                success(response) {
                    const saved = response?.data || {};
                    if (saved && Object.keys(saved).length) {
                        hydrateSavedFascia(saved);
                    }
                    renderFasciaNotes(response?.raw_text || '');
                }
            });
        }

        function submitFasciaForm(form) {
            if (isViewOnly) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Form closed',
                    text: 'Cannot submit. Form is closed.'
                });
                return false;
            }
            const $form = $(form);
            const $submitBtn = $form.find('button[type="submit"]');
            const fasciaCategory = $form.find('[name="fascia_category"]').val();
            const isRawSpaceSubmit = fasciaCategory === 'Raw Space';
            $submitBtn.prop('disabled', true).addClass('disabled-btn');
            const formData = new FormData(form);
            const token = getAuthToken();
            $.ajax({
                url: FASCIA_URL,
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
                        if (isRawSpaceSubmit) {
                            const electricityQuantity = parseInt(
                                $form.find('[name="electricity_requirement"]').val(),
                                10
                            );

                            if (!electricityQuantity || electricityQuantity <= 0) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Electricity quantity missing',
                                    text: 'Please enter a valid electricity requirement.'
                                });
                                return;
                            }

                            const addToCartAfterSubmit = function() {
                                return addElectricityToCart(electricityQuantity);
                            };

                            const cartRequest = electricityItemLoaded
                                ? addToCartAfterSubmit()
                                : fetchElectricityItem().then(function() {
                                    if (!electricityItemLoaded || !ELECTRICITY_ITEM_ID) {
                                        throw new Error('Electricity item is not configured.');
                                    }
                                    return addToCartAfterSubmit();
                                });

                            cartRequest.then(function(cartResponse) {
                                if (cartResponse && cartResponse.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Details Submitted',
                                        text: 'Electricity charges have been added to your cart. Would you like to pay now or pay later?',
                                        showDenyButton: true,
                                        confirmButtonText: 'Pay Now',
                                        denyButtonText: 'Pay Later',
                                        allowOutsideClick: false,
                                        allowEscapeKey: false
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = getCartPageUrl();
                                        }
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Details Submitted',
                                        text: (cartResponse && cartResponse.message)
                                            ? 'Details were submitted, but electricity could not be added to the cart: ' + cartResponse.message
                                            : 'Details were submitted, but electricity could not be added to the cart.'
                                    });
                                }
                            }).catch(function(error) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Details Submitted',
                                    text: error?.message ||
                                        'Details were submitted, but electricity could not be added to the cart.'
                                });
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Your action was completed.',
                                timer: 5000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                        $('#fasciaDesignInput').val('');
                        $('.fascia-file-name').text('No file chosen');
                        if (response.data) {
                            hydrateSavedFascia(response.data);
                        } else {
                            fetchSavedFascia();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Submission failed',
                            text: message
                        });
                    }
                },
                error(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'Server error. Please try again.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                },
                complete() {
                    $submitBtn.prop('disabled', false).removeClass('disabled-btn');
                }
            });
            return false;
        }

        function initValidator($form, rules, messages) {
            $form.validate($.extend(true, {}, validatorDefaults, {
                rules,
                messages
            }));
        }

        initValidator($shellForm, {
            fascia_board_text: {
                required: true,
                minlength: 3
            },
            stall_open_side: {
                required: true,
                minlength: 3
            }
        }, {
            fascia_board_text: {
                required: 'Enter fascia text',
                minlength: 'Enter at least 3 characters'
            },
            stall_open_side: {
                required: 'Enter layout details',
                minlength: 'Enter at least 3 characters'
            }
        });

        const hasExistingDesign = <?= !empty($saved['fascia_design']) ? 'true' : 'false' ?>;
        const rawRules = {
            electricity_requirement: {
                required: true,
                digits: true
            },
            salutation: {
                required: true
            },
            first_name: {
                required: true,
                minlength: 2
            },
            last_name: {
                required: true,
                minlength: 2
            },
            fabricator_company_name: {
                required: true,
                minlength: 2,
                maxlength: 26
            },
            mobile_number: {
                required: true,
                digits: true,
                minlength: 7,
                maxlength: 15
            },
            email: {
                required: true,
                email: true
            },
            fascia_design: {
                required: !hasExistingDesign,
                accept: false,
                fasciaFileType: true,
                fasciaFileSize: true
            }
        };

        initValidator($rawForm, rawRules, {
            electricity_requirement: {
                required: 'Enter electricity requirement',
                digits: 'Enter numbers only, no decimals'
            },
            salutation: {
                required: 'Please select a salutation'
            },
            first_name: {
                required: 'Enter first name',
                minlength: 'Enter at least 2 characters'
            },
            last_name: {
                required: 'Enter last name',
                minlength: 'Enter at least 2 characters'
            },
            fabricator_company_name: {
                required: 'Enter company name',
                minlength: 'Enter at least 2 characters',
                maxlength: 'Maximum 26 characters allowed'
            },
            mobile_number: {
                required: 'Enter contact number',
                digits: 'Enter only digits',
                minlength: 'Enter at least 7 digits',
                maxlength: 'Enter at most 15 digits'
            },
            email: {
                required: 'Enter email address',
                email: 'Enter a valid email address'
            },
            fascia_design: {
                required: 'Upload design file',
                fasciaFileType: 'Only .pdf, .jpg, .jpeg or .png files are allowed',
                fasciaFileSize: 'Maximum file size is 2 MB'
            }
        });

        toggleForms();
        applyDesignStatus('<?= esc($saved['fascia_design_status'] ?? $saved['status'] ?? '') ?>');
        showRejectionReason(
            '<?= esc($saved['fascia_design_status'] ?? $saved['status'] ?? '') ?>',
            '<?= esc($saved['reason'] ?? '') ?>',
            '<?= esc($saved['other_reason'] ?? '') ?>'
        );
        renderFasciaPreview('<?= !empty($saved['fascia_design']) ? esc($saved['fascia_design']) : '' ?>');
        hydrateSavedFascia(<?= json_encode($saved) ?>);

        if (!<?= json_encode(!empty($saved)) ?>) {
            fetchSavedFascia();
        }

        fetchElectricityItem();

        function runOnLayoutReady() {
            checkFasciaStatus();
            renderFasciaDueDate();
        }
        setTimeout(runOnLayoutReady, 500);
        document.addEventListener('layoutConfigReady', runOnLayoutReady);
        if (window.__layoutConfigReady) {
            runOnLayoutReady();
        }

        function updateFasciaHeaderText() {
            const $title = $('#fasciaTitle');
            const $subtitle = $('#fasciaSubtitle');
            if (urlScheme === 2) {
                $title.text('Raw Space & Stand Details');
                $subtitle.text('Tell us how your raw space stand will be designed and constructed so we can review the layout, coordinate approvals, and ensure a smooth build-up before the event.');
            } else {
                $title.html('Fascia & Stand Details');
                $subtitle.text('Tell us how your stand will be built so we can prepare the right fascia board, layout, and approvals ahead of the event.');
            }
        }

        updateFasciaHeaderText();
    });
</script>

<?= $this->endSection() ?>
<?= $this->section('custom-script') ?>
<?= $this->endSection() ?>