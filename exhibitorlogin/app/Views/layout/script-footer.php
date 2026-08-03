<script>
    $(document).ready(function() {
        const sidebar = $("#sidebar");
        const toggleBtn = $("#toggleSidebar");
        toggleBtn.on("click", function() {
            sidebar.toggleClass("collapsed");
            sidebar.toggleClass("show");
        });
        $(document).on("click", function(event) {
            if (!$(event.target).closest("#sidebar, #toggleSidebar").length) {
                $("#sidebar").removeClass("show");
            }
        });
        $('.nav-link[data-bs-toggle="collapse"]').on('click', function() {
            const targetId = $(this).attr('href').substring(1); // Get the ID of the target submenu
            $('.submenu').each(function() {
                if (this.id !== targetId) {
                    $(this).removeClass('show'); // Collapse other open submenus
                }
            });
        });
        $('.submenu .nav-link.active').each(function() {
            const submenu = $(this).closest('.submenu');
            if (submenu.length) {
                submenu.addClass('show');
            }
        });
        $('.nav-link').on('click', function() {
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            if ($(this).data('bs-toggle') === 'collapse') {
                $(this).removeClass('active');
            }
        });
        $(".nav-link").on("click", handleClick);
        restoreActiveMenu();
        $('[data-bs-toggle="tooltip"]').each(function() {
            new bootstrap.Tooltip(this);
        });
        $('.applySelect2').select2({
            placeholder: "Select",
            theme: 'bootstrap-5',
            width: '100%'
        });
        let currentYear = new Date().getFullYear();
        $(".datepicker-input").datepicker({
            dateFormat: 'dd/mm/yy',
            autoSize: true,
            showAnim: 'clip',
            changeMonth: true,
            changeYear: true,
            yearRange: `2015:${(currentYear + 2)}`,
            defaultDate: +7
        });
        $(".datepicker-input").on("input", function(e) {
            const $input = $(this);
            let field_id = this.id;
            const $errorElement = $(`#${field_id}-error`);
            let errorMessage = '';
            let digits = $input.val().replace(/[^\d]/g, '');
            let formattedValue = '';
            digits = digits.substring(0, 8);
            if (digits.length >= 1) {
                formattedValue += digits.substring(0, 2);
            }
            if (digits.length >= 3) {
                formattedValue += '/' + digits.substring(2, 4);
            }
            if (digits.length >= 5) {
                formattedValue += '/' + digits.substring(4, 8);
            }
            $input.val(formattedValue);
        });
        initializeDropZones();
        window.initIntlMobileFields = function() {
            const fields = document.querySelectorAll('.intl-mobile');
            fields.forEach(field => {
                initializeIntlTelInput(field);
            });
        };
        window.initIntlMobileFields();
        const itiObserver = new MutationObserver((mutations) => {
            mutations.forEach(m => {
                m.addedNodes.forEach(node => {
                    if (!(node instanceof HTMLElement)) return;
                    const newMobiles = node.matches('.intl-mobile') ? [node] : Array.from(node.querySelectorAll('.intl-mobile'));
                    if (newMobiles.length) {
                        newMobiles.forEach(el => initializeIntlTelInput(el));
                    }
                });
            });
        });
        itiObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
        $('.salutation-input-container').each(function() {
            initializeSalutationInput(this);
        });
        $('.copy-btn').copyText();
    });

    function clearActiveClasses() {
        $(".nav-link.active").removeClass("active");
        $(".collapse.show").removeClass("show");
    }

    function restoreActiveMenu() {
        clearActiveClasses();

        const currentPath = window.location.pathname.replace(/\/+$/, '');
        const navLinks = $(".nav-link");
        let foundActive = false;

        navLinks.each(function() {
            const link = $(this);
            const linkHref = link.attr("href").replace(/\/+$/, '');

            const url = new URL(linkHref, window.location.origin);
            const pathWithoutHost = url.pathname;

            if (pathWithoutHost === currentPath) {
                link.addClass("active");
                foundActive = true;
                const parentCollapse = link.closest(".collapse");
                if (parentCollapse.length) {
                    parentCollapse.addClass("show");
                    const parentLink = parentCollapse.prev(".nav-link");
                    if (parentLink.length) {
                        parentLink.addClass("active");
                    }
                }
            }
        });
        if (!foundActive) {
            const activeModule = localStorage.getItem("activeModule");
            const activeSubModule = localStorage.getItem("activeSubModule");

            if (activeModule) {
                const activeLink = $(`.nav-link[href="${activeModule}"]`);
                if (activeLink.length) {
                    activeLink.addClass("active");
                    const parentCollapse = activeLink.closest(".collapse");
                    if (parentCollapse.length) {
                        parentCollapse.addClass("show");
                        const parentLink = parentCollapse.prev(".nav-link");
                        if (parentLink.length) {
                            parentLink.addClass("active");
                        }
                    }
                }
            }

            if (activeSubModule) {
                const activeSubLink = $(`.nav-link[href="${activeSubModule}"]`);
                if (activeSubLink.length) {
                    activeSubLink.addClass("active");
                    const parentCollapse = activeSubLink.closest(".collapse");
                    if (parentCollapse.length) {
                        parentCollapse.addClass("show");
                        const parentLink = parentCollapse.prev(".nav-link");
                        if (parentLink.length) {
                            parentLink.addClass("active");
                        }
                    }
                }
            }
        }
    }

    function handleClick(event) {
        const target = $(event.target).closest('.nav-link');
        if (!target.length) return;
        const href = target.attr("href");
        const isModule = target.data("bs-toggle") === "collapse";
        if (isModule) {
            localStorage.setItem("activeModule", href);
            localStorage.removeItem("activeSubModule");
        } else {
            localStorage.setItem("activeSubModule", href);
            const parentCollapse = target.closest(".collapse");
            if (parentCollapse.length) {
                localStorage.setItem("activeModule", `#${parentCollapse.attr("id")}`);
            } else {
                localStorage.removeItem("activeModule");
            }
        }
    }
    $(window).on("popstate", restoreActiveMenu);
    const showModal = (modalId) => {
        const modalElement = $(`#${modalId}`)[0];
        if (modalElement) {
            $(modalElement).attr('aria-hidden', 'false'); // Ensure the modal is visible
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
            modalInstance.show();
        } else {
            console.error(`Modal not found: ${modalId}`);
        }
    };
    const hideModal = (modalId) => {
        const modalElement = $(`#${modalId}`)[0];
        if (modalElement) {
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
                $(modalElement).attr('aria-hidden', 'true'); // Ensure the modal is hidden
            } else {
                console.error(`No modal instance found for: ${modalId}`);
            }
        } else {
            console.error(`Modal not found: ${modalId}`);
        }
    };

    function resetForm(formId) {
        $(formId)[0].reset();
        $(formId).find('select').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).val(null).trigger('change');
            }
        });
    }

    function showAlert(icon, text, reload_page = true, hideAfter = 2000) {
        const validIcons = ['success', 'error', 'info', 'warning', 'alert'];
        icon = icon.toLowerCase();
        if (!validIcons.includes(icon)) {
            console.log('Invalid icon!', icon);
            return;
        }
        const heading = icon.charAt(0).toUpperCase() + icon.slice(1);
        let stackNumber = (icon === 'error' || icon === 'info' || icon === 'warning') ? 20 : 1;
        if (text.length > 0) {
            $.toast({
                heading: heading,
                text: text,
                icon: icon,
                position: 'top-right',
                hideAfter: (icon !== 'success') ? 10000 : hideAfter,
                stack: stackNumber,
                afterHidden: function() {
                    if (icon === 'success' && reload_page === true) {
                        window.location.reload();
                    }
                }
            });
        }
    }

    function showThumbnail(dropZoneElement, file) {
        let thumb = dropZoneElement.querySelector(".drop-zone__thumb");
        const prompt = dropZoneElement.querySelector(".drop-zone__prompt");
        if (prompt) {
            prompt.remove();
        }
        if (!thumb) {
            thumb = document.createElement("div");
            thumb.classList.add("drop-zone__thumb");
            dropZoneElement.appendChild(thumb);
        }
        thumb.innerHTML = "";
        if (file && typeof file === 'object' && file.type && file.type.startsWith("image/")) {
            thumb.dataset.label = file.name;
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => {
                const img = document.createElement("img");
                img.src = reader.result;
                img.alt = "Preview";
                thumb.appendChild(img);
            };
        } else {
            thumb.dataset.label = "Current Image";
            const img = document.createElement("img");
            img.src = file;
            img.alt = "Preview";
            thumb.appendChild(img);
        }
    }
    $('.modal').on('hidden.bs.modal', function() {
        const form = $(this).find('form')[0];
        if (form) {
            form.reset();
            $(form).find('select').val(null).trigger('change');
            $(form).find('.drop-zone__thumb').remove();
            $(form).find('.drop-zone').each(function() {
                if (!$(this).find('.drop-zone__prompt').length) {
                    $(this).prepend('<span class="drop-zone__prompt">Drop file here or click to upload</span>');
                }
            });
        }
    });
    let $clickedBtn = null;
    $(document).on('click', 'button[type="submit"], input[type="submit"]', function() {
        $clickedBtn = $(this);
    });

    function showLoadingIndicator(form) {
        const $form = $(form);
        const $btn = $clickedBtn && $clickedBtn.closest('form')[0] === $form[0] ?
            $clickedBtn :
            $form.find('[type="submit"]').first();
        if ($btn.length) {
            $btn.data('original-text', $btn.is('input') ? $btn.val() : $btn.text());
            const loadingText = 'Please wait...';
            $btn.prop('disabled', true);
            if ($btn.is('input')) $btn.val(loadingText);
            else $btn.text(loadingText);
        }
    }

    function hideLoadingIndicator(form) {
        const $form = $(form);
        const $btn = $clickedBtn && $clickedBtn.closest('form')[0] === $form[0] ? $clickedBtn : $form.find('[type="submit"]').first();
        if ($btn.length) {
            const originalText = $btn.data('original-text') || 'Submit';
            $btn.prop('disabled', false);
            if ($btn.is('input')) $btn.val(originalText);
            else $btn.text(originalText);
        }
        $clickedBtn = null;
    }
    $.validator.addMethod("notDefaultColor", function(value, element) {
        return value.toLowerCase() !== "#000000";
    }, "Please choose a color.");
    $.validator.addMethod("filesize", function(value, element, param) {
        if (element.files.length === 0) return true;
        const maxBytes = param * 1024 * 1024;

        return element.files[0].size <= maxBytes;
    }, function(param, element) {
        return `File must be less than ${param}MB`;
    });
    $.validator.addMethod('validateCustomDate', function(value, element) {
        const m = moment(value, 'DD/MM/YYYY', true);
        if (!m.isValid()) {
            $.validator.messages.validateCustomDate = 'Invalid date format or value. Please use dd/mm/yyyy.';
            return false;
        }
        const year = m.year();
        if (year < 1900) {
            $.validator.messages.validateCustomDate = 'Year must be greater 1900.';
            return false;
        }
        return true;
    }, 'Please enter a valid date.');
    $.validator.addMethod('validateMobileNumber', function(value, element) {
        if (element.dataset.type == 'optional') return true;
        if (!value) return false;
        if (!window.intlTelInputUtils) return true; // skip strict validation until utils loaded
        const inst = itiInstances.get(element.id) || (window.intlTelInputGlobals ? window.intlTelInputGlobals.getInstance(element) : null) || iti;
        if (!inst) {
            const digits = value.replace(/\D/g, '');
            return digits.length >= 7 && digits.length <= 15;
        }
        try {
            return inst.isValidNumber();
        } catch (e) {
            const digits = value.replace(/\D/g, '');
            return digits.length >= 7 && digits.length <= 15;
        }
    }, function(value, element) {
        if (element.dataset.type == 'optional') return true;
        const inst = itiInstances.get(element.id) || (window.intlTelInputGlobals ? window.intlTelInputGlobals.getInstance(element) : null) || iti;
        if (inst && inst.getSelectedCountryData()) {
            const countryName = inst.getSelectedCountryData().name;
            return `Please enter a valid mobile number for ${countryName}`;
        }
        return 'Please enter a valid mobile number';
    });
    $.validator.addMethod('maxMobileLength', function(value, element, param) {
        if (!value || value.length === 0) {
            return true;
        }
        const digitsOnly = value.replace(/\D/g, '');
        return digitsOnly.length <= param;
    }, function(param, element) {
        return `Mobile number cannot exceed ${param} digits`;
    });
    $.validator.addMethod('minMobileLength', function(value, element, param) {
        if (!value || value.length === 0) {
            return false;
        }
        const digitsOnly = value.replace(/\D/g, '');
        return digitsOnly.length >= param;
    }, function(param, element) {
        return `Mobile number must be at least ${param} digits`;
    });
    $.validator.addMethod(
        "multiEmail",
        function(value, element) {
            if (this.optional(element)) {
                return true;
            }
            const emails = value.split(',');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            for (let i = 0; i < emails.length; i++) {
                if (!emailRegex.test(emails[i].trim())) {
                    return false;
                }
            }
            return true;
        },
        "Please enter valid email address(es), separated by commas."
    );
    const mobile_number_fields = document.querySelectorAll(".intl-mobile");
    const country_code_field = document.querySelector("#country-code");
    let iti = null;
    const itiInstances = new Map();

    function initializeIntlTelInput(input) {
        if (!input) return null;
        if (itiInstances.has(input.id)) {
            return itiInstances.get(input.id);
        }
        const instance = window.intlTelInput(input, {
            preferredCountries: ["in"],
            initialCountry: "in",
            placeholderNumberType: "MOBILE",
            formatOnDisplay: false,
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.19/build/js/utils.js"
        });
        if (!iti) {
            iti = instance;
        }
        itiInstances.set(input.id, instance);
        let associatedCountryField = null;
        const group = input.closest('.contact-person-group') || input.closest('form');
        if (group) {
            associatedCountryField = group.querySelector('.intl-country-code');
        }
        if (!associatedCountryField && input.id === 'mobile-for-app') {
            associatedCountryField = document.querySelector('#country-code-for-app');
        }
        if (associatedCountryField) {
            associatedCountryField.value = "+" + instance.getSelectedCountryData().dialCode;
        }
        waitForUtils(() => validatePhoneNumber());
        input.addEventListener("countrychange", function() {
            const inst = itiInstances.get(input.id);
            if (inst) {
                const countryData = inst.getSelectedCountryData();
                let cf = null;
                const grp = input.closest('.contact-person-group') || input.closest('form');
                if (grp) {
                    cf = grp.querySelector('.intl-country-code');
                }
                if (!cf && input.id === 'mobile-for-app') {
                    cf = document.querySelector('#country-code-for-app');
                }
                if (cf) cf.value = "+" + countryData.dialCode;
                waitForUtils(() => {
                    validatePhoneNumber(input);
                    triggerMobileValidation(input);
                });
            }
        });
        input.addEventListener("input", function() {
            let digitsOnly = this.value.replace(/\D/g, "");
            if (digitsOnly.length > 15) {
                digitsOnly = digitsOnly.substring(0, 15);
            }
            this.value = digitsOnly;
            waitForUtils(() => {
                validatePhoneNumber(input);
            });
        });
        return instance;
    }
    window.safeInitializeITI = function(fieldId) {
        const el = document.getElementById(fieldId);
        if (el) {
            initializeIntlTelInput(el);
        }
    };

    function triggerMobileValidation(input) {
        if (input) {
            const $f = $(input);
            if ($f.length && $f.closest('form').data('validator')) {
                $f.valid();
            }
            return;
        }
        $('.contact-mobile').each(function() {
            const $f = $(this);
            if ($f.closest('form').data('validator')) {
                $f.valid();
            }
        });
    }

    function waitForUtils(callback) {
        if (window.intlTelInputUtils) {
            callback();
        } else {
            setTimeout(() => waitForUtils(callback), 50);
        }
    }

    function validatePhoneNumber(input) {
        if (!window.intlTelInputUtils) {
            return false;
        }
        let inst = null;
        if (input) {
            inst = itiInstances.get(input.id) || window.intlTelInputGlobals?.getInstance(input);
        } else if (iti) {
            inst = iti;
        }
        if (!inst) return false;
        try {
            return inst.isValidNumber();
        } catch (e) {
            return false;
        }
    }

    (function($) {
        $.fn.copyText = function(options) {
            const settings = $.extend({
                tooltipSuccess: 'Copied!',
                tooltipError: 'Failed!',
                tooltipEmpty: 'Empty!',
                tooltipDefault: 'Copy'
            }, options);
            const getAutoText = (btn, wrapper) => {
                let customValue = btn.data('copy-value');
                if (customValue) return customValue;
                let target = wrapper.find('.copy-text').first();
                if (!target.length) {
                    target = wrapper.find('input, textarea, p, span, div, h1, h2, h3, h4, h5, h6').first();
                }
                if (!target.length) return '';
                return target.is('input, textarea') ? target.val() : target.text().trim();
            };
            const showTooltip = (btn, message) => {
                btn.attr('data-bs-original-title', message).tooltip('show');
                setTimeout(() => {
                    btn.attr('data-bs-original-title', settings.tooltipDefault);
                }, 2000);
            };

            return this.each(function() {
                const btn = $(this);
                btn.on('click', function(e) {
                    e.preventDefault();
                    const wrapper = btn.closest('.copy-wrapper');
                    const text = getAutoText(btn, wrapper);
                    if (!text) {
                        showTooltip(btn, settings.tooltipEmpty);
                        return;
                    }
                    navigator.clipboard.writeText(text)
                        .then(() => showTooltip(btn, settings.tooltipSuccess))
                        .catch(() => showTooltip(btn, settings.tooltipError));
                });
            });
        };
    })(jQuery);
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(link) {
            const target = document.querySelector(link.getAttribute('href'));
            target.addEventListener('shown.bs.collapse', function() {
                link.setAttribute('aria-expanded', 'true');
            });
            target.addEventListener('hidden.bs.collapse', function() {
                link.setAttribute('aria-expanded', 'false');
            });

        });
    });
</script>
</body>

</html>