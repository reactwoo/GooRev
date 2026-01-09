/**
 * Review Widgets Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        var $templateSelect = $('#grp_widget_button_default_template');
        var $templateDescription = $('#grp-template-selected-description');
        var $templateProNote = $('#grp-template-pro-note');
        var $previewBtn = $('#grp-preview-button');
        var $previewQr = $('#grp-preview-qr');
        var $previewQrImg = $('#grp-preview-qr-img');
        var $previewTagline = $('#grp-preview-tagline');
        var $previewStarRow = $('#grp-preview-star-row');
        var $starColorInput = $('#grp_widget_template_star_color');
        var $starColorText = $('#grp_widget_template_star_color_text');
        var $starPlacementSelect = $('#grp_widget_template_star_placement');
        var $logoToggle = $('#grp_widget_template_show_logo');
        var $fontFamilyInput = $('#grp_widget_template_font_family');
        var $maxHeightInput = $('#grp_widget_template_max_height');
        var $linkColorInput = $('#grp_widget_template_link_color');
        var $linkColorText = $('#grp_widget_template_link_color_text');
        var $linkTextInput = $('#grp_widget_template_link_text');
        var $maxWidthInput = $('#grp_widget_template_max_width');
        var $boxShadowCheckbox = $('#grp_widget_template_box_shadow_enabled');
        var $boxShadowValue = $('#grp_widget_template_box_shadow_value');
        var $boxShadowEditTrigger = $('#grp-box-shadow-edit');
        var $bgColorRow = $('.grp-bg-color-row');
        var $glassCheckbox = $('#grp_widget_template_glass_effect');
        var $templateEditorModal = $('#grp-template-editor-modal');
        var $templateEditorPreview = $('#grp-template-editor-preview');
        var $modalShowLogo = $('#grp-modal-show-logo');
        var $modalLogoScaleSlider = $('#grp-modal-logo-scale-slider');
        var $modalLogoScaleNumber = $('#grp-modal-logo-scale-number');
        var $modalStarColor = $('#grp-modal-star-color');
        var $modalStarColorText = $('#grp-modal-star-color-text');
        var $modalStarPlacement = $('#grp-modal-star-placement');
        var $modalFontFamily = $('#grp-modal-font-family');
        var $modalTextColor = $('#grp-modal-text-color');
        var $modalTextColorText = $('#grp-modal-text-color-text');
        var $modalBackgroundColor = $('#grp-modal-background-color');
        var $modalBackgroundColorText = $('#grp-modal-background-color-text');
        var $modalGlassEffect = $('#grp-modal-glass-effect');
        var $modalGradientSection = $('#grp-template-editor-gradient-section');
        var $gradientSummaryPreview = $('#grp-gradient-summary-preview');
        var $gradientEditorOpen = $('#grp-gradient-editor-open');
        var $gradientEditorModal = $('#grp-gradient-editor-modal');
        var $gradientEditorClose = $('.grp-gradient-editor-close');
        var $gradientEditorDone = $('#grp-gradient-editor-done');
        var $gradientEditorCancel = $('#grp-gradient-editor-cancel');
        var $gradientEditorType = $('#grp-gradient-type');
        var $gradientEditorAngle = $('#grp-gradient-angle');
        var $gradientEditorAngleNumber = $('#grp-gradient-angle-number');
        var $gradientEditorStartPos = $('#grp-gradient-start-pos');
        var $gradientEditorStartPosNumber = $('#grp-gradient-start-pos-number');
        var $gradientEditorEndPos = $('#grp-gradient-end-pos');
        var $gradientEditorEndPosNumber = $('#grp-gradient-end-pos-number');
        var $gradientEditorStartColor = $('#grp-gradient-start-color');
        var $gradientEditorStartColorText = $('#grp-gradient-start-color-text');
        var $gradientEditorStartOpacity = $('#grp-gradient-start-opacity');
        var $gradientEditorStartOpacityValue = $('#grp-gradient-start-opacity-value');
        var $gradientEditorEndColor = $('#grp-gradient-end-color');
        var $gradientEditorEndColorText = $('#grp-gradient-end-color-text');
        var $gradientEditorEndOpacity = $('#grp-gradient-end-opacity');
        var $gradientEditorEndOpacityValue = $('#grp-gradient-end-opacity-value');
        var $gradientEditorPreview = $('#grp-gradient-editor-preview');
        var $modalLinkText = $('#grp-modal-link-text');
        var $modalLinkColor = $('#grp-modal-link-color');
        var $modalLinkColorText = $('#grp-modal-link-color-text');
        var $modalLinkRows = $('.grp-modal-link-row');
        var $modalTextColorOpacity = $('#grp-modal-text-color-opacity');
        var $modalTextColorOpacityValue = $('#grp-modal-text-color-opacity-value');
        var $modalBackgroundColorOpacity = $('#grp-modal-background-color-opacity');
        var $modalBackgroundColorOpacityValue = $('#grp-modal-background-color-opacity-value');
        var $modalStarColorOpacity = $('#grp-modal-star-color-opacity');
        var $modalStarColorOpacityValue = $('#grp-modal-star-color-opacity-value');
        var $modalLinkColorOpacity = $('#grp-modal-link-color-opacity');
        var $modalLinkColorOpacityValue = $('#grp-modal-link-color-opacity-value');
        var $modalBoxShadowEnabled = $('#grp-modal-box-shadow-enabled');
        var $modalBoxShadowEdit = $('#grp-template-editor-box-shadow-edit');
        var $modalMessageText = $('#grp-modal-message-text');
        var $modalPaddingTop = $('#grp-modal-padding-top');
        var $modalPaddingRight = $('#grp-modal-padding-right');
        var $modalPaddingBottom = $('#grp-modal-padding-bottom');
        var $modalPaddingLeft = $('#grp-modal-padding-left');
        var $modalBorderTopLeft = $('#grp-modal-border-top-left');
        var $modalBorderTopRight = $('#grp-modal-border-top-right');
        var $modalBorderBottomRight = $('#grp-modal-border-bottom-right');
        var $modalBorderBottomLeft = $('#grp-modal-border-bottom-left');
        var $templateProBadge = $('#grp-template-pro-badge');
        var $logoScaleSlider = $('#grp_widget_template_logo_scale');
        var $logoScaleText = $('#grp_widget_template_logo_scale_text');
        var $gradientRows = $('.grp-gradient-row');
        var $customizeButton = $('#grp-template-editor-open');
        var $buttonStyleRows = $('.grp-button-style-row');
        var $buttonSizeRows = $('.grp-button-size-row');
        var $buttonTextColorRows = $('.grp-button-text-color-row');
        var templateMeta = (typeof grpWidgets !== 'undefined' && grpWidgets.button_templates) ? grpWidgets.button_templates : {};
        var templateClassList = Object.keys(templateMeta).map(function(key) {
            return 'grp-review-button-template-' + key;
        });
        var templateCustomizationDefaults = (typeof grpWidgets !== 'undefined' && grpWidgets.template_customization_defaults) ? grpWidgets.template_customization_defaults : {};
        var templateCustomizations = (typeof grpWidgets !== 'undefined' && grpWidgets.template_customizations) ? grpWidgets.template_customizations : {};
        var logoScaleTouched = false;
        var modalTemplateKey = $templateSelect.length ? ($templateSelect.val() || 'basic') : 'basic';
        var qrCache = {};
        var blankQr = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
        var isPro = false;
        var hasPlaceId = false;
        var logoUrls = {};
        var modalDirty = false;
        if (typeof grpWidgets !== 'undefined') {
            isPro = !!grpWidgets.is_pro;
            hasPlaceId = !!grpWidgets.has_place_id;
            logoUrls = grpWidgets.logo_urls || {};
            if (!isPro && grpWidgets.license_status) {
                var licenseData = grpWidgets.license_data || {};
                var packageType = (licenseData.packageType || licenseData.package_type || '').toLowerCase();
                var proPackages = ['pro', 'enterprise', 'goorev-pro', 'goorev-enterprise', 'pro_with_restricts'];
                if (proPackages.indexOf(packageType) !== -1) {
                    isPro = true;
                }
            }
        }

        window.updateGRPPreview = function() {
            updatePreview();
        };

        function getTemplateData(key) {
            if (templateMeta[key]) {
                return templateMeta[key];
            }
            return templateMeta.basic || {};
        }

        function safeTrimValue($element) {
            if (!$element || !$element.length) {
                return '';
            }
            var value = $element.val();
            if (typeof value === 'string') {
                return value.trim();
            }
            return value || '';
        }

        function updateTemplateDescription(key) {
            var templateData = getTemplateData(key);
            if ($templateDescription.length) {
                $templateDescription.text(templateData.description || '');
            }
            if ($templateProNote.length) {
                var text = '';
                if (templateData.pro && !isPro) {
                    if (typeof grpWidgets !== 'undefined' && grpWidgets.strings && grpWidgets.strings.templateProMessage) {
                        text = grpWidgets.strings.templateProMessage;
                    }
                }
                $templateProNote.text(text).toggle(!!text);
            }
        }

        function getTemplateCustomization(key) {
            var defaults = templateCustomizationDefaults[key] || templateCustomizationDefaults.basic || {};
            var stored = templateCustomizations[key] || {};
            return $.extend({}, defaults, stored);
        }

        function ensureTemplateCustomizationEntry(key) {
            var templateKey = key || modalTemplateKey || ($templateSelect.length ? ($templateSelect.val() || 'basic') : 'basic');
            if (!templateKey) {
                return null;
            }
            if (!templateCustomizations[templateKey]) {
                templateCustomizations[templateKey] = {};
            }
            return templateCustomizations[templateKey];
        }

        function updateTemplateCustomizationValue(field, value, key) {
            var entry = ensureTemplateCustomizationEntry(key);
            if (!entry) {
                return;
            }
            if (entry[field] === value) {
                return;
            }
            entry[field] = value;
            modalDirty = true;
        }

        function parsePositiveInt(value) {
            var parsed = parseInt(value, 10);
            if (isNaN(parsed) || parsed < 0) {
                return 0;
            }
            return parsed;
        }

        function clampValue(value, min, max) {
            var parsed = parseInt(value, 10);
            if (isNaN(parsed)) {
                parsed = typeof min === 'number' ? min : 0;
            }
            if (typeof min === 'number') {
                parsed = Math.max(parsed, min);
            }
            if (typeof max === 'number') {
                parsed = Math.min(parsed, max);
            }
            return parsed;
        }

        function bindModalNumberInput($input, fieldName) {
            if (!$input || !$input.length) {
                return;
            }
            $input.on('input', function() {
                var value = parsePositiveInt($(this).val());
                updateTemplateCustomizationValue(fieldName, value);
                updatePreview();
                updateModalPreview();
            });
        }

        function combineColorWithOpacity(color, opacity, fallback) {
            if (!color) {
                return fallback || '';
            }
            var hex = color.replace('#', '');
            if (/^(?:[0-9a-f]{3}|[0-9a-f]{6})$/i.test(hex)) {
                var r, g, b;
                if (hex.length === 3) {
                    r = parseInt(hex.charAt(0) + hex.charAt(0), 16);
                    g = parseInt(hex.charAt(1) + hex.charAt(1), 16);
                    b = parseInt(hex.charAt(2) + hex.charAt(2), 16);
                } else {
                    r = parseInt(hex.substr(0, 2), 16);
                    g = parseInt(hex.substr(2, 2), 16);
                    b = parseInt(hex.substr(4, 2), 16);
                }
                var opacityValue = (typeof opacity === 'undefined' || opacity === '' || opacity === null) ? 100 : parseInt(opacity, 10);
                if (isNaN(opacityValue)) {
                    opacityValue = 100;
                }
                if (opacityValue >= 100) {
                    return '#' + hex;
                }
                var alpha = (opacityValue / 100).toFixed(2);
                return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
            }
            return color;
        }

        function buildGradientBackground(customization) {
            if (!customization.gradient_start || !customization.gradient_end) {
                return '';
            }
            var type = customization.gradient_type || 'linear';
            var angle = clampValue(customization.gradient_angle, 0, 360) || 135;
            var startPos = clampValue(customization.gradient_start_pos, 0, 100);
            var endPos = clampValue(customization.gradient_end_pos, 0, 100);
            var startColor = combineColorWithOpacity(customization.gradient_start, customization.gradient_start_opacity, customization.gradient_start);
            var endColor = combineColorWithOpacity(customization.gradient_end, customization.gradient_end_opacity, customization.gradient_end);
            if (!startColor || !endColor) {
                return '';
            }
            return type + '-gradient(' + angle + 'deg, ' + startColor + ' ' + startPos + '%, ' + endColor + ' ' + endPos + '%)';
        }

        function updateGradientSummaryPreview(customization) {
            if (!$gradientSummaryPreview.length) {
                return;
            }
            var config = customization || getTemplateCustomization(modalTemplateKey);
            var gradient = buildGradientBackground(config);
            if (gradient) {
                $gradientSummaryPreview.css('background', gradient);
            } else {
                var fallback = combineColorWithOpacity(config.background_color, config.background_color_opacity, '#2b2b2b');
                $gradientSummaryPreview.css('background', fallback || '#2b2b2b');
            }
        }

        function buildWrapperStyle(customization, templateKey) {
            var styles = [];
            var paddingTop = customization.padding_top || 0;
            var paddingRight = customization.padding_right || 0;
            var paddingBottom = customization.padding_bottom || 0;
            var paddingLeft = customization.padding_left || 0;
            styles.push('padding:' + paddingTop + 'px ' + paddingRight + 'px ' + paddingBottom + 'px ' + paddingLeft + 'px');
            var radius = [
                (customization.border_radius_top_left || 0) + 'px',
                (customization.border_radius_top_right || 0) + 'px',
                (customization.border_radius_bottom_right || 0) + 'px',
                (customization.border_radius_bottom_left || 0) + 'px'
            ].join(' ');
            styles.push('border-radius:' + radius);
            if (customization.font_family) {
                styles.push('font-family:' + customization.font_family);
            }
            if (customization.max_width && customization.max_width > 0) {
                styles.push('max-width:' + customization.max_width + 'px');
            }
            if (customization.max_height && customization.max_height > 0) {
                styles.push('max-height:' + customization.max_height + 'px');
            }
            if (customization.box_shadow_enabled && customization.box_shadow_value) {
                styles.push('box-shadow:' + customization.box_shadow_value);
            } else {
                styles.push('box-shadow:none');
            }
            var customBackground = buildGradientBackground(customization);
            if (templateKey === 'creative-pro' && customBackground) {
                styles.push('background:' + customBackground);
            } else {
                var bgColor = combineColorWithOpacity(customization.background_color, customization.background_color_opacity, '');
                if (bgColor) {
                    styles.push('background-color:' + bgColor);
                }
            }
            var textColor = combineColorWithOpacity(customization.text_color, customization.text_color_opacity, '');
            if (textColor) {
                styles.push('color:' + textColor);
            }
            return styles.join('; ');
        }

        function showPreviewQr(src) {
            if ($previewQr.length && $previewQrImg.length) {
                $previewQrImg.attr('src', src);
                $previewQr.addClass('has-qr');
            }
            $previewBtn.find('.grp-card-qr img, .grp-layout1-qr img, .grp-layout2-qr img').attr('src', src);
        }

        function hidePreviewQr() {
            if ($previewQr.length && $previewQrImg.length) {
                $previewQr.removeClass('has-qr');
                $previewQrImg.attr('src', blankQr);
            }
            $previewBtn.find('.grp-card-qr img, .grp-layout1-qr img, .grp-layout2-qr img').attr('src', blankQr);
        }

        function fetchPreviewQr(size) {
            size = parseInt(size, 10) || 100;
            if (!hasPlaceId || typeof grpWidgets === 'undefined' || !grpWidgets.ajax_url) {
                hidePreviewQr();
                return;
            }

            if (qrCache[size]) {
                showPreviewQr(qrCache[size]);
                return;
            }

            $.ajax({
                url: grpWidgets.ajax_url,
                type: 'POST',
                data: {
                    action: 'grp_generate_qr',
                    nonce: grpWidgets.nonce,
                    size: size
                },
                success: function(response) {
                    if (response.success && response.data && response.data.qr_url) {
                        qrCache[size] = response.data.qr_url;
                        showPreviewQr(response.data.qr_url);
                    } else {
                        hidePreviewQr();
                    }
                },
                error: function() {
                    hidePreviewQr();
                }
            });
        }

        function escapeHtml(text) {
            return $('<div>').text(text).html();
        }

        function buildUnderline(colors) {
            var html = '';
            if (!Array.isArray(colors)) {
                return html;
            }
            colors.forEach(function(color) {
                if (color) {
                    html += '<span style="background: ' + color + ';"></span>';
                }
            });
            return html;
        }

        function renderPreviewContent(templateType, templateData, options) {
            var qrHtml = templateData && templateData.qr ? '<div class="grp-qr-frame"><img id="grp-preview-qr-img" src="' + blankQr + '" alt="QR"></div>' : '';
            var linkColor = options.linkColor || '#111111';
            var showLink = options.showLink !== false;
            var linkHtml = showLink && options.reviewUrl ? '<a href="' + options.reviewUrl + '" target="_blank" rel="noopener" style="color:' + linkColor + ';">' + escapeHtml(options.linkText || 'Click here') + '</a>' : '';
            var wrapperStyleAttr = options.wrapperStyle ? ' style="' + options.wrapperStyle + '"' : '';

            if (templateType === 'layout1') {
                return '<div class="grp-layout1-preview"' + wrapperStyleAttr + '>' +
                    '<div class="grp-layout1-qr">' + qrHtml + '</div>' +
                    '<div class="grp-layout1-details">' +
                        (options.showLogo && options.logoIconUrl ? '<img src="' + options.logoIconUrl + '" class="grp-layout1-logo-img" alt="Google" style="width:' + options.logoScale + '%;">' : '') +
                        '<div class="grp-layout1-stars" style="color:' + options.starColor + ';">' + options.starText + '</div>' +
                        '<div class="grp-layout1-title">' + options.title + '</div>' +
                        '<div class="grp-layout1-subtitle">' + options.subtitle + '</div>' +
                        '<div class="grp-layout1-underline">' + buildUnderline(templateData.underline_colors) + '</div>' +
                        (linkHtml ? '<div class="grp-layout1-link" style="color:' + linkColor + ';">' + linkHtml + '</div>' : '') +
                    '</div>' +
                '</div>';
            }

            if (templateType === 'layout2') {
                var darkClass = templateData.dark ? ' grp-layout-dark' : '';
                return '<div class="grp-layout2-preview' + darkClass + '"' + wrapperStyleAttr + '>' +
                    (options.showLogo && options.logoClassicUrl ? '<img src="' + options.logoClassicUrl + '" class="grp-layout2-logo-img" alt="Google" style="width:' + options.logoScale + '%;">' : '') +
                    '<div class="grp-layout2-stars-qr">' +
                        '<div class="grp-layout2-stars" style="color:' + options.starColor + ';">' + options.starText + '</div>' +
                        '<div class="grp-layout2-qr">' + qrHtml + '</div>' +
                    '</div>' +
                    '<div class="grp-layout2-heading">' + options.title + '</div>' +
                    '<div class="grp-layout2-subtitle">' + options.subtitle + '</div>' +
                    '<div class="grp-layout2-link" style="color:' + linkColor + ';">' + linkHtml + '</div>' +
                    '<div class="grp-layout2-underline">' + buildUnderline(templateData.underline_colors) + '</div>' +
                '</div>';
            }

            if (templateType === 'card') {
                var inner = '<div class="grp-review-card"' + wrapperStyleAttr + '>';
                if (options.showLogo && options.logoClassicUrl) {
                    inner += '<img src="' + options.logoClassicUrl + '" class="grp-card-logo-img" alt="Google" style="width:' + options.logoScale + '%;">';
                }
                inner += '<div class="grp-card-stars" style="color:' + options.starColor + ';">' + options.starText + '</div>';
                inner += '<div class="grp-card-heading">' + options.title + '</div>';
                inner += '<div class="grp-card-subtitle">' + options.subtitle + '</div>';
                inner += '<div class="grp-card-qr">' + qrHtml + '</div>';
                inner += '<div class="grp-card-link">' + linkHtml + '</div>';
                inner += '</div>';
                return inner;
            }

            return '<span class="grp-preview-star-row" style="color:' + options.starColor + ';">' + options.starText + '</span>' +
                '<span class="grp-preview-qr" id="grp-preview-qr">' + qrHtml + '</span>' +
                '<div class="grp-preview-content">' +
                    (options.showLogo ? '<span class="grp-review-button-icon">⭐</span>' : '') +
                    '<span class="grp-review-button-text" id="grp-preview-text">' + options.title + '</span>' +
                    '<span class="grp-preview-tagline" id="grp-preview-tagline">' + options.subtitle + '</span>' +
                '</div>';
        }

        function toggleGradientControls(templateKey) {
            if (templateKey === 'creative-pro') {
                $gradientRows.show();
            } else {
                $gradientRows.hide();
            }
        }

        function toggleBackgroundColorRow(templateKey) {
            if (!$bgColorRow.length) {
                return;
            }
            var templateData = getTemplateData(templateKey);
            $bgColorRow.toggle(templateData.type === 'button');
        }

        function toggleButtonControls(templateKey) {
            if (!$buttonStyleRows.length && !$buttonSizeRows.length && !$buttonTextColorRows.length) {
                return;
            }
            var templateData = getTemplateData(templateKey);
            var showButtonSettings = templateData.type === 'button';
            $buttonStyleRows.toggle(showButtonSettings);
            $buttonSizeRows.toggle(showButtonSettings);
            $buttonTextColorRows.toggle(showButtonSettings);
        }

        function toggleLinkRows(templateKey) {
            if (!$modalLinkRows.length) {
                return;
            }
            var templateData = getTemplateData(templateKey);
            $modalLinkRows.toggle(templateData.show_link !== false);
        }

        function isValidHex(color) {
            return /^#[0-9A-F]{6}$/i.test(color);
        }

        function populateTemplateModal() {
            modalTemplateKey = $templateSelect.length ? ($templateSelect.val() || 'basic') : 'basic';
            var templateData = getTemplateData(modalTemplateKey);
            var customization = getTemplateCustomization(modalTemplateKey);
            var isCreative = modalTemplateKey === 'creative-pro';

            $modalGradientSection.toggle(isCreative);
            toggleLinkRows(modalTemplateKey);
            applyCustomizationToDom(modalTemplateKey);

            console.log('[GRP DEBUG] populateTemplateModal', {
                template: modalTemplateKey,
                showLink: templateData.show_link !== false,
                isCreative: isCreative,
                customization: customization
            });
            $modalBoxShadowEnabled.prop('checked', !!customization.box_shadow_enabled);
            updateModalPreview();
        }

        function updateModalPreview() {
            if (!$templateEditorPreview.length) {
                return;
            }
            var templateData = getTemplateData(modalTemplateKey || 'basic');
            var previewUrl = $previewBtn.attr('href') || '#';
            var modalLogoScale = parseInt($modalLogoScaleSlider.val(), 10);
            if (isNaN(modalLogoScale) || modalLogoScale <= 0) {
                modalLogoScale = 50;
            }
            var modalStarColorValue = $modalStarColorText.val() || '#FBBD05';
            if (!isValidHex(modalStarColorValue)) {
                modalStarColorValue = '#FBBD05';
            }
            var modalTextColorValue = $modalTextColorText.val() || '#ffffff';
            if (!isValidHex(modalTextColorValue)) {
                modalTextColorValue = '#ffffff';
            }
            var modalBackgroundColorValue = $modalBackgroundColorText.val() || '#2b2b2b';
            if (!isValidHex(modalBackgroundColorValue)) {
                modalBackgroundColorValue = '#2b2b2b';
            }
            var modalMaxWidth = parseInt($maxWidthInput.val(), 10) || 0;
            var modalMaxHeight = parseInt($maxHeightInput.val(), 10) || 0;
            var isModalButton = templateData.type === 'button';
            var modalStarPlacementValue = $modalStarPlacement.val() || 'below';
            var previewWrapperStyles = [];
            if (isModalButton && modalMaxWidth > 0) {
                previewWrapperStyles.push('max-width: ' + modalMaxWidth + 'px');
            }
            if (isModalButton && modalMaxHeight > 0) {
                previewWrapperStyles.push('max-height: ' + modalMaxHeight + 'px');
            }
            var previewWrapperClass = 'grp-template-modal-preview-inner grp-star-placement-' + modalStarPlacementValue;
            var previewWrapperAttr = previewWrapperStyles.length ? ' style="' + previewWrapperStyles.join('; ') + '"' : '';

            var modalCustomization = getTemplateCustomization(modalTemplateKey);
            var modalWrapperStyle = buildWrapperStyle(modalCustomization, modalTemplateKey);
            var modalMessageText = modalCustomization.message_text || templateData.subtitle || templateData.tagline || '';
            var modalLinkText = modalCustomization.link_text || safeTrimValue($linkTextInput) || 'Click here';
            var previewHtml = '<div class="' + previewWrapperClass + '"' + previewWrapperAttr + '>' +
                renderPreviewContent(templateData.type || 'button', templateData, {
                title: escapeHtml($('#grp_widget_button_default_text').val() || 'Leave us a review'),
                subtitle: escapeHtml(modalMessageText),
                linkText: escapeHtml(modalLinkText),
                showLogo: $modalShowLogo.is(':checked'),
                starColor: modalStarColorValue,
                starText: templateData.stars ? '★★★★★' : '',
                logoIconUrl: logoUrls.icon || '',
                logoClassicUrl: logoUrls.classic || '',
                reviewUrl: previewUrl,
                linkColor: $linkColorText.length ? ($linkColorText.val() || '#111111') : '#111111',
                logoScale: modalLogoScale,
                showLink: templateData.show_link !== false,
                wrapperStyle: modalWrapperStyle
            }) + '</div>';
            $templateEditorPreview.html(previewHtml);
            var $modalPreviewInner = $templateEditorPreview.find('.grp-template-modal-preview-inner');
            var $modalTemplateRoot = $modalPreviewInner.children().first();
            var modalRootStyles = [];
            if (isValidHex(modalTextColorValue)) {
                modalRootStyles.push('color: ' + modalTextColorValue);
            }
            if (isValidHex(modalBackgroundColorValue)) {
                modalRootStyles.push('background-color: ' + modalBackgroundColorValue);
            }
            if ($modalFontFamily.val()) {
                modalRootStyles.push('font-family: ' + $modalFontFamily.val());
            }
            if (isModalButton && modalMaxHeight > 0) {
                modalRootStyles.push('max-height: ' + modalMaxHeight + 'px');
            }
            if (isModalButton && modalMaxWidth > 0) {
                modalRootStyles.push('max-width: ' + modalMaxWidth + 'px');
            }
            var trimmedBoxShadow = safeTrimValue($boxShadowValue);
            if ($modalBoxShadowEnabled.is(':checked') && trimmedBoxShadow) {
                modalRootStyles.push('box-shadow: ' + trimmedBoxShadow);
            } else {
                modalRootStyles.push('box-shadow: none');
            }
            var combinedModalStyles = modalRootStyles.slice();
            if (modalWrapperStyle) {
                combinedModalStyles.push(modalWrapperStyle);
            }
            $modalTemplateRoot.attr('style', combinedModalStyles.join('; '));

            if ($modalGlassEffect.is(':checked')) {
                $modalTemplateRoot.addClass('grp-glass-effect');
            } else {
                $modalTemplateRoot.removeClass('grp-glass-effect');
            }
            $templateEditorPreview.toggleClass('grp-glass-preview', $modalGlassEffect.is(':checked'));
            updateGradientSummaryPreview(modalCustomization);
        }

        function persistTemplateCustomizations(templateKey) {
            templateKey = templateKey || modalTemplateKey;
            if (!modalDirty || !templateKey || typeof grpWidgets === 'undefined' || !grpWidgets.ajax_url) {
                return;
            }
            var customization = templateCustomizations[templateKey];
            if (!customization) {
                return;
            }
            $.ajax({
                url: grpWidgets.ajax_url,
                type: 'POST',
                data: {
                    action: 'grp_save_template_customization',
                    nonce: grpWidgets.nonce,
                    template: templateKey,
                    customizations: JSON.stringify(customization)
                },
                success: function(response) {
                    if (response.success && response.data && response.data.customizations) {
                        templateCustomizations[templateKey] = response.data.customizations;
                        modalDirty = false;
                        updateGradientSummaryPreview(response.data.customizations);
                        updatePreview();
                        updateModalPreview();
                    } else {
                        console.warn('Failed to save template customizations', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Template customization save failed', status, error);
                }
            });
        }

        function populateGradientEditor(customization) {
            var config = customization || getTemplateCustomization(modalTemplateKey);
            var angle = clampValue(config.gradient_angle, 0, 360) || 135;
            $gradientEditorType.val(config.gradient_type || 'linear');
            $gradientEditorAngle.val(angle);
            $gradientEditorAngleNumber.val(angle);
            $gradientEditorStartPos.val(clampValue(config.gradient_start_pos, 0, 100));
            $gradientEditorStartPosNumber.val(clampValue(config.gradient_start_pos, 0, 100));
            $gradientEditorEndPos.val(clampValue(config.gradient_end_pos, 0, 100));
            $gradientEditorEndPosNumber.val(clampValue(config.gradient_end_pos, 0, 100));
            var startColor = config.gradient_start || '#0091ff';
            var endColor = config.gradient_end || '#612c1f';
            $gradientEditorStartColor.val(startColor);
            $gradientEditorStartColorText.val(startColor);
            $gradientEditorEndColor.val(endColor);
            $gradientEditorEndColorText.val(endColor);
            var startOpacity = clampValue(config.gradient_start_opacity, 0, 100) || 100;
            var endOpacity = clampValue(config.gradient_end_opacity, 0, 100) || 100;
            $gradientEditorStartOpacity.val(startOpacity);
            $gradientEditorStartOpacityValue.text(startOpacity + '%');
            $gradientEditorEndOpacity.val(endOpacity);
            $gradientEditorEndOpacityValue.text(endOpacity + '%');
            updateGradientEditorPreview();
        }

        function updateGradientEditorPreview() {
            var previewData = {
                gradient_type: $gradientEditorType.val(),
                gradient_angle: clampValue($gradientEditorAngleNumber.val(), 0, 360) || 135,
                gradient_start_pos: clampValue($gradientEditorStartPosNumber.val(), 0, 100),
                gradient_end_pos: clampValue($gradientEditorEndPosNumber.val(), 0, 100),
                gradient_start: $gradientEditorStartColor.val(),
                gradient_end: $gradientEditorEndColor.val(),
                gradient_start_opacity: clampValue($gradientEditorStartOpacity.val(), 0, 100),
                gradient_end_opacity: clampValue($gradientEditorEndOpacity.val(), 0, 100),
            };
            var previewGradient = buildGradientBackground(previewData);
            if (previewGradient) {
                $gradientEditorPreview.css('background', previewGradient);
            } else {
                $gradientEditorPreview.css('background', '#232323');
            }
        }

        function bindGradientEditorRange($range, $number, min, max) {
            if (!$range.length || !$number.length) {
                return;
            }
            $range.on('input change', function() {
                var value = clampValue($(this).val(), min, max);
                $(this).val(value);
                $number.val(value);
                updateGradientEditorPreview();
            });
            $number.on('input change', function() {
                var value = clampValue($(this).val(), min, max);
                $(this).val(value);
                $range.val(value);
                updateGradientEditorPreview();
            });
        }

        function openGradientEditor() {
            populateGradientEditor();
            if ($gradientEditorModal.length) {
                $gradientEditorModal.css('display', 'flex');
            }
        }

        function closeGradientEditor() {
            if ($gradientEditorModal.length) {
                $gradientEditorModal.hide();
            }
        }

        function closeTemplateModal() {
            persistTemplateCustomizations(modalTemplateKey);
            $templateEditorModal.removeClass('grp-template-active');
            $templateEditorModal.css('display', 'none');
            if ($gradientEditorModal.is(':visible')) {
                $gradientEditorModal.hide();
            }
        }
        
        function updatePreview() {
            var text = $('#grp_widget_button_default_text').val();
            var style = $('#grp_widget_button_default_style').val();
            var size = $('#grp_widget_button_default_size').val();
            var templateKey = $templateSelect.length ? ($templateSelect.val() || 'basic') : 'basic';
            var templateData = getTemplateData(templateKey);
            applyCustomizationToDom(templateKey);
            var customization = getTemplateCustomization(templateKey);
            var previewUrl = $previewBtn.attr('href') || '#';
            var starPlacement = customization.star_placement || 'below';
            var normalizedPlacement = starPlacement === 'bottom' ? 'below' : starPlacement;
            var linkText = customization.link_text || safeTrimValue($linkTextInput) || 'Click here';
            var subtitleText = customization.message_text || templateData.subtitle || templateData.tagline || 'Scan the QR code to leave a review!';
            var logoScale = customization.logo_scale || parseInt($logoScaleSlider.val(), 10) || 30;
            var glassEffect = !!customization.glass_effect;
            var computedTextColor = combineColorWithOpacity(customization.text_color, customization.text_color_opacity, '#ffffff');
            var computedBackgroundColor = combineColorWithOpacity(customization.background_color, customization.background_color_opacity, '#2b2b2b');
            var computedStarColor = combineColorWithOpacity(customization.star_color, customization.star_color_opacity, '#FBBD05');
            var computedLinkColor = combineColorWithOpacity(customization.link_color, customization.link_color_opacity, '#ffffff');

            // Update template description/pro note
            updateTemplateDescription(templateKey);

            var classes = [
                'grp-review-button',
                'grp-review-button-template-' + templateKey,
                'grp-star-placement-' + normalizedPlacement
            ];
            if (templateData.type === 'button') {
                classes.splice(1, 0, 'grp-review-button-' + style);
                classes.splice(2, 0, 'grp-review-button-' + size);
            }
            $previewBtn.attr('class', classes.join(' '));
            if (glassEffect) {
                $previewBtn.addClass('grp-glass-effect');
            } else {
                $previewBtn.removeClass('grp-glass-effect');
            }

            var styles = [];
            if (computedTextColor) {
                styles.push('color: ' + computedTextColor);
            }
            if (templateData.type === 'button') {
                if (computedBackgroundColor) {
                    styles.push('background-color: ' + computedBackgroundColor);
                }
                if (customization.font_family) {
                    styles.push('font-family: ' + customization.font_family);
                }
                if (customization.max_height > 0) {
                    styles.push('max-height: ' + customization.max_height + 'px');
                }
                if (customization.max_width > 0) {
                    styles.push('max-width: ' + customization.max_width + 'px');
                }
                if (customization.box_shadow_enabled && customization.box_shadow_value) {
                    styles.push('box-shadow: ' + customization.box_shadow_value);
                }
            }
            $previewBtn.attr('style', styles.join('; '));

            var wrapperStyle = buildWrapperStyle(customization, templateKey);
            var previewHtml = renderPreviewContent(templateData.type || 'button', templateData, {
                title: escapeHtml(text || 'Leave us a review'),
                subtitle: escapeHtml(subtitleText),
                linkText: escapeHtml(linkText),
                showLogo: !!customization.show_logo,
                starColor: computedStarColor,
                starText: templateData.stars ? '★★★★★' : '',
                logoIconUrl: logoUrls.icon || '',
                logoClassicUrl: logoUrls.classic || '',
                reviewUrl: previewUrl,
                linkColor: computedLinkColor,
                logoScale: logoScale,
                showLink: templateData.show_link !== false,
                wrapperStyle: wrapperStyle
            });
            $previewBtn.html(previewHtml);

            toggleGradientControls(templateKey);
            toggleBackgroundColorRow(templateKey);
            toggleButtonControls(templateKey);
            toggleLinkRows(templateKey);
            updateCustomizeButtonVisibility(templateKey);
            updateModalPreview();
            updateGradientSummaryPreview(customization);

            var sanitizedPreviewUrl = String(previewUrl || '').trim();
            if (templateData.qr && hasPlaceId && sanitizedPreviewUrl && sanitizedPreviewUrl !== '#') {
                fetchPreviewQr(templateData.qr_size || 135);
            } else {
                hidePreviewQr();
            }
        }

        function updateCustomizeButtonVisibility(templateKey) {
            if (!$customizeButton.length) {
                return;
            }
            var templateData = getTemplateData(templateKey);
            if (templateData.type === 'button') {
                $customizeButton.hide();
                if ($templateProBadge.length) {
                    $templateProBadge.hide();
                }
                return;
            }
            $customizeButton.show();
            var requiresPro = !!templateData.pro;
            $customizeButton.attr('data-template-key', templateKey);
            $customizeButton.attr('data-template-pro', requiresPro ? '1' : '0');
            $customizeButton.attr('data-is-pro', isPro ? '1' : '0');
            if (requiresPro && !isPro) {
                if ($templateProBadge.length) {
                    $templateProBadge.show();
                }
                $customizeButton.attr('title', 'Available in Pro only');
            } else {
                if ($templateProBadge.length) {
                    $templateProBadge.hide();
                }
                $customizeButton.attr('title', 'Customize this layout');
            }
        }

        // Color picker sync and preview font/color updates
        $('#grp_widget_button_default_color').on('change', function() {
            var color = $(this).val();
            $('#grp_widget_button_default_color_text').val(color);
            updatePreview();
        });
        
        $('#grp_widget_button_default_color_text').on('input', function() {
            var val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $('#grp_widget_button_default_color').val(val);
            }
            updatePreview();
        });
        
        $('#grp_widget_button_default_bg_color').on('change', function() {
            var color = $(this).val();
            $('#grp_widget_button_default_bg_color_text').val(color);
            updatePreview();
        });
        
        $('#grp_widget_button_default_bg_color_text').on('input', function() {
            var val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $('#grp_widget_button_default_bg_color').val(val);
            }
            updatePreview();
        });

        // Star color sync
        $starColorInput.on('change', function() {
            var color = $(this).val();
            $starColorText.val(color);
            updatePreview();
        });

        $starColorText.on('input', function() {
            var val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $starColorInput.val(val);
            }
            updatePreview();
        });
        
        $linkColorInput.on('change', function() {
            var color = $(this).val();
            $linkColorText.val(color);
            updatePreview();
        });

        $linkColorText.on('input', function() {
            var val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $linkColorInput.val(val);
            }
            updatePreview();
        });

        $linkTextInput.on('input', function() {
            updatePreview();
        });

        $logoScaleSlider.on('input', function() {
            var value = $(this).val();
            logoScaleTouched = true;
            $logoScaleText.val(value);
            updatePreview();
        });

        $logoScaleText.on('input', function() {
            var value = $(this).val();
            if (value === '') {
                return;
            }
            logoScaleTouched = true;
            if (!isNaN(value) && value >= 10 && value <= 100) {
                $logoScaleSlider.val(value);
            }
            updatePreview();
        });
        
        // Clear color buttons
        $('#grp-clear-text-color').on('click', function() {
            $('#grp_widget_button_default_color_text').val('');
            updatePreview();
        });
        
        $('#grp-clear-bg-color').on('click', function() {
            $('#grp_widget_button_default_bg_color_text').val('');
            updatePreview();
        });
        
        // Update preview triggers
        $('#grp_widget_button_default_text').on('input', updatePreview);
        $('#grp_widget_button_default_style').on('change', updatePreview);
        $('#grp_widget_button_default_size').on('change', updatePreview);
        $templateSelect.on('change', updatePreview);
        $starPlacementSelect.on('change', updatePreview);
        $logoToggle.on('change', updatePreview);
        $('#grp-template-editor-open').on('click', function() {
            if (!isPro) {
                var proMessage = (typeof grpWidgets !== 'undefined' && grpWidgets.strings && grpWidgets.strings.templateProMessage) ? grpWidgets.strings.templateProMessage : 'Upgrade to Pro to customize templates.';
                alert(proMessage);
                return;
            }
            if (!$templateEditorModal.length) {
                return;
            }
            console.log('[GRP DEBUG] Customize modal click', {
                isPro: isPro,
                licenseStatus: typeof grpWidgets !== 'undefined' ? grpWidgets.license_status : undefined,
                licensePackage: typeof grpWidgets !== 'undefined' ? (grpWidgets.license_data || {}).packageType : undefined,
            });
            populateTemplateModal();
        $templateEditorModal.css('display', 'flex');
        $templateEditorModal.addClass('grp-template-active');
        });

        window.grpOpenTemplateModal = function(templateKey) {
            if (!templateKey) {
                templateKey = $templateSelect.length ? ($templateSelect.val() || 'basic') : 'basic';
            }
            modalTemplateKey = templateKey;
            populateTemplateModal();
            $templateEditorModal.css('display', 'flex');
            $templateEditorModal.addClass('grp-template-active');
        };

        $templateEditorModal.on('click', function(e) {
            if ($(e.target).is($templateEditorModal)) {
                closeTemplateModal();
            }
        });

        $('.grp-template-editor-close, #grp-template-editor-close').on('click', closeTemplateModal);

        $gradientEditorOpen.on('click', function() {
            if (!isPro) {
                var proMessage = (typeof grpWidgets !== 'undefined' && grpWidgets.strings && grpWidgets.strings.templateProMessage) ? grpWidgets.strings.templateProMessage : 'Upgrade to Pro to edit gradients.';
                alert(proMessage);
                return;
            }
            openGradientEditor();
        });

        $gradientEditorClose.on('click', closeGradientEditor);
        $gradientEditorCancel.on('click', closeGradientEditor);
        $gradientEditorModal.on('click', function(e) {
            if ($(e.target).is($gradientEditorModal)) {
                closeGradientEditor();
            }
        });
        $gradientEditorType.on('change', updateGradientEditorPreview);
        bindGradientEditorRange($gradientEditorAngle, $gradientEditorAngleNumber, 0, 360);
        bindGradientEditorRange($gradientEditorStartPos, $gradientEditorStartPosNumber, 0, 100);
        bindGradientEditorRange($gradientEditorEndPos, $gradientEditorEndPosNumber, 0, 100);
        $gradientEditorStartColor.on('change', function() {
            var color = $(this).val();
            $gradientEditorStartColorText.val(color);
            updateGradientEditorPreview();
        });
        $gradientEditorStartColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                $gradientEditorStartColor.val(value);
                updateGradientEditorPreview();
            }
        });
        $gradientEditorEndColor.on('change', function() {
            var color = $(this).val();
            $gradientEditorEndColorText.val(color);
            updateGradientEditorPreview();
        });
        $gradientEditorEndColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                $gradientEditorEndColor.val(value);
                updateGradientEditorPreview();
            }
        });
        $gradientEditorStartOpacity.on('input change', function() {
            var value = clampValue($(this).val(), 0, 100);
            $(this).val(value);
            $gradientEditorStartOpacityValue.text(value + '%');
            updateGradientEditorPreview();
        });
        $gradientEditorEndOpacity.on('input change', function() {
            var value = clampValue($(this).val(), 0, 100);
            $(this).val(value);
            $gradientEditorEndOpacityValue.text(value + '%');
            updateGradientEditorPreview();
        });
        $gradientEditorDone.on('click', function() {
            var templateKey = modalTemplateKey;
            var startColor = $gradientEditorStartColor.val();
            var endColor = $gradientEditorEndColor.val();
            updateTemplateCustomizationValue('gradient_type', $gradientEditorType.val(), templateKey);
            updateTemplateCustomizationValue('gradient_angle', clampValue($gradientEditorAngleNumber.val(), 0, 360), templateKey);
            updateTemplateCustomizationValue('gradient_start_pos', clampValue($gradientEditorStartPosNumber.val(), 0, 100), templateKey);
            updateTemplateCustomizationValue('gradient_end_pos', clampValue($gradientEditorEndPosNumber.val(), 0, 100), templateKey);
            updateTemplateCustomizationValue('gradient_start', startColor, templateKey);
            updateTemplateCustomizationValue('gradient_end', endColor, templateKey);
            updateTemplateCustomizationValue('gradient_start_opacity', clampValue($gradientEditorStartOpacity.val(), 0, 100), templateKey);
            updateTemplateCustomizationValue('gradient_end_opacity', clampValue($gradientEditorEndOpacity.val(), 0, 100), templateKey);
            $gradientStartInput.val(startColor);
            $gradientStartText.val(startColor);
            $gradientEndInput.val(endColor);
            $gradientEndText.val(endColor);
            updateGradientSummaryPreview(getTemplateCustomization(templateKey));
            updatePreview();
            updateModalPreview();
            closeGradientEditor();
        });

        $modalShowLogo.on('change', function() {
            $logoToggle.prop('checked', $(this).is(':checked'));
            updateTemplateCustomizationValue('show_logo', $(this).is(':checked') ? 1 : 0);
            updatePreview();
            updateModalPreview();
        });

        function syncLogoScale(value) {
            $modalLogoScaleSlider.val(value);
            $modalLogoScaleNumber.val(value);
            $logoScaleSlider.val(value);
            $logoScaleText.val(value);
        }

        function syncTextColor(value) {
            $modalTextColor.val(value);
            $modalTextColorText.val(value);
            $('#grp_widget_button_default_color').val(value);
            $('#grp_widget_button_default_color_text').val(value);
        }

        function syncBackgroundColor(value) {
            $modalBackgroundColor.val(value);
            $modalBackgroundColorText.val(value);
            $('#grp_widget_button_default_bg_color').val(value);
            $('#grp_widget_button_default_bg_color_text').val(value);
        }

        function syncLinkText(value) {
            $modalLinkText.val(value);
            $linkTextInput.val(value);
        }

        function syncLinkColor(value) {
            $modalLinkColor.val(value);
            $modalLinkColorText.val(value);
            $linkColorInput.val(value);
            $linkColorText.val(value);
        }

        function applyCustomizationToDom(templateKey) {
            var customization = getTemplateCustomization(templateKey);
            if (!customization) {
                return;
            }
            $logoToggle.prop('checked', !!customization.show_logo);
            syncLogoScale(customization.logo_scale || 30);
            $fontFamilyInput.val(customization.font_family || '');
            $modalFontFamily.val(customization.font_family || '');
            syncTextColor(customization.text_color || '#ffffff');
            syncBackgroundColor(customization.background_color || '#2b2b2b');
            var starColor = customization.star_color || '#FBBD05';
            $modalStarColor.val(starColor);
            $modalStarColorText.val(starColor);
            $starColorInput.val(starColor);
            $starColorText.val(starColor);
            var linkColor = customization.link_color || '#ffffff';
            $modalLinkColor.val(linkColor);
            $modalLinkColorText.val(linkColor);
            $linkColorInput.val(linkColor);
            $linkColorText.val(linkColor);
            var linkText = customization.link_text || '';
            $modalLinkText.val(linkText);
            $linkTextInput.val(linkText);
            var starPlacementValue = customization.star_placement || 'below';
            $modalStarPlacement.val(starPlacementValue);
            $starPlacementSelect.val(starPlacementValue);
            $glassCheckbox.prop('checked', !!customization.glass_effect);
            $modalGlassEffect.prop('checked', !!customization.glass_effect);
            $boxShadowCheckbox.prop('checked', !!customization.box_shadow_enabled);
            $modalBoxShadowEnabled.prop('checked', !!customization.box_shadow_enabled);
            $boxShadowValue.val(customization.box_shadow_value || '');
            $maxWidthInput.val(customization.max_width || 0);
            $maxHeightInput.val(customization.max_height || 0);
            if ($('#grp-modal-message-text').length) {
                $('#grp-modal-message-text').val(customization.message_text || '');
            }
            if ($('#grp-modal-padding-top').length) {
                $('#grp-modal-padding-top').val(customization.padding_top || 0);
                $('#grp-modal-padding-right').val(customization.padding_right || 0);
                $('#grp-modal-padding-bottom').val(customization.padding_bottom || 0);
                $('#grp-modal-padding-left').val(customization.padding_left || 0);
            }
            if ($('#grp-modal-border-top-left').length) {
                $('#grp-modal-border-top-left').val(customization.border_radius_top_left || 0);
                $('#grp-modal-border-top-right').val(customization.border_radius_top_right || 0);
                $('#grp-modal-border-bottom-right').val(customization.border_radius_bottom_right || 0);
                $('#grp-modal-border-bottom-left').val(customization.border_radius_bottom_left || 0);
            }
            if ($modalTextColorOpacity.length) {
                var textOpacity = clampValue(customization.text_color_opacity, 0, 100) || 100;
                $modalTextColorOpacity.val(textOpacity);
                $modalTextColorOpacityValue.text(textOpacity + '%');
            }
            if ($modalBackgroundColorOpacity.length) {
                var bgOpacity = clampValue(customization.background_color_opacity, 0, 100) || 100;
                $modalBackgroundColorOpacity.val(bgOpacity);
                $modalBackgroundColorOpacityValue.text(bgOpacity + '%');
            }
            if ($modalStarColorOpacity.length) {
                var starOpacity = clampValue(customization.star_color_opacity, 0, 100) || 100;
                $modalStarColorOpacity.val(starOpacity);
                $modalStarColorOpacityValue.text(starOpacity + '%');
            }
            if ($modalLinkColorOpacity.length) {
                var linkOpacity = clampValue(customization.link_color_opacity, 0, 100) || 100;
                $modalLinkColorOpacity.val(linkOpacity);
                $modalLinkColorOpacityValue.text(linkOpacity + '%');
            }
            updateGradientSummaryPreview(customization);
        }

        $modalTextColor.on('change', function() {
            var color = $(this).val();
            syncTextColor(color);
            updateTemplateCustomizationValue('text_color', color);
            updatePreview();
            updateModalPreview();
        });

        $modalTextColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                syncTextColor(value);
                updateTemplateCustomizationValue('text_color', value);
                updatePreview();
                updateModalPreview();
            }
        });

        $modalTextColorOpacity.on('input change', function() {
            var value = clampValue($(this).val(), 0, 100);
            $(this).val(value);
            $modalTextColorOpacityValue.text(value + '%');
            updateTemplateCustomizationValue('text_color_opacity', value);
            updatePreview();
            updateModalPreview();
        });

        $modalLinkText.on('input', function() {
            var value = $(this).val();
            syncLinkText(value);
            updateTemplateCustomizationValue('link_text', value);
            updatePreview();
            updateModalPreview();
        });

        $modalLinkColor.on('change', function() {
            var color = $(this).val();
            syncLinkColor(color);
            updateTemplateCustomizationValue('link_color', color);
            updatePreview();
            updateModalPreview();
        });

        $modalLinkColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                syncLinkColor(value);
                updateTemplateCustomizationValue('link_color', value);
                updatePreview();
                updateModalPreview();
            }
        });

        $modalLinkColorOpacity.on('input change', function() {
            var value = clampValue($(this).val(), 0, 100);
            $(this).val(value);
            $modalLinkColorOpacityValue.text(value + '%');
            updateTemplateCustomizationValue('link_color_opacity', value);
            updatePreview();
            updateModalPreview();
        });

        $modalBackgroundColor.on('change', function() {
            var color = $(this).val();
            syncBackgroundColor(color);
            updateTemplateCustomizationValue('background_color', color);
            updatePreview();
            updateModalPreview();
        });

        $modalBackgroundColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                syncBackgroundColor(value);
                updateTemplateCustomizationValue('background_color', value);
                updatePreview();
                updateModalPreview();
            }
        });

        $modalBackgroundColorOpacity.on('input change', function() {
            var value = clampValue($(this).val(), 0, 100);
            $(this).val(value);
            $modalBackgroundColorOpacityValue.text(value + '%');
            updateTemplateCustomizationValue('background_color_opacity', value);
            updatePreview();
            updateModalPreview();
        });

        $modalGlassEffect.on('change', function() {
            $glassCheckbox.prop('checked', $(this).is(':checked'));
            updateTemplateCustomizationValue('glass_effect', $(this).is(':checked') ? 1 : 0);
            updatePreview();
            updateModalPreview();
        });

        $modalBoxShadowEnabled.on('change', function() {
            $boxShadowCheckbox.prop('checked', $(this).is(':checked'));
            updateTemplateCustomizationValue('box_shadow_enabled', $(this).is(':checked') ? 1 : 0);
            updatePreview();
            updateModalPreview();
        });

        $modalBoxShadowEdit.on('click', function() {
            $boxShadowEditTrigger.trigger('click');
        });

        $modalLogoScaleSlider.on('input', function() {
            var value = $(this).val();
            logoScaleTouched = true;
            syncLogoScale(value);
            updateTemplateCustomizationValue('logo_scale', parsePositiveInt(value));
            updatePreview();
            updateModalPreview();
        });

        $modalLogoScaleNumber.on('input', function() {
            var value = $(this).val();
            if (value === '') {
                return;
            }
            if (!isNaN(value) && value >= 10 && value <= 100) {
                logoScaleTouched = true;
                syncLogoScale(value);
                updateTemplateCustomizationValue('logo_scale', parsePositiveInt(value));
                updatePreview();
                updateModalPreview();
            }
        });

        $modalStarColor.on('change', function() {
            var color = $(this).val();
            $modalStarColorText.val(color);
            $starColorInput.val(color);
            $starColorText.val(color);
            updateTemplateCustomizationValue('star_color', color);
            updatePreview();
            updateModalPreview();
        });

        $modalStarColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                $modalStarColor.val(value);
                $starColorInput.val(value);
                $starColorText.val(value);
                updateTemplateCustomizationValue('star_color', value);
                updatePreview();
                updateModalPreview();
            }
        });

        $modalStarColorOpacity.on('input change', function() {
            var value = clampValue($(this).val(), 0, 100);
            $(this).val(value);
            $modalStarColorOpacityValue.text(value + '%');
            updateTemplateCustomizationValue('star_color_opacity', value);
            updatePreview();
            updateModalPreview();
        });

        $modalStarPlacement.on('change', function() {
            $starPlacementSelect.val($(this).val());
            updateTemplateCustomizationValue('star_placement', $(this).val());
            updatePreview();
            updateModalPreview();
        });

        $modalMessageText.on('input', function() {
            updateTemplateCustomizationValue('message_text', $(this).val());
            updatePreview();
            updateModalPreview();
        });

        bindModalNumberInput($modalPaddingTop, 'padding_top');
        bindModalNumberInput($modalPaddingRight, 'padding_right');
        bindModalNumberInput($modalPaddingBottom, 'padding_bottom');
        bindModalNumberInput($modalPaddingLeft, 'padding_left');
        bindModalNumberInput($modalBorderTopLeft, 'border_radius_top_left');
        bindModalNumberInput($modalBorderTopRight, 'border_radius_top_right');
        bindModalNumberInput($modalBorderBottomRight, 'border_radius_bottom_right');
        bindModalNumberInput($modalBorderBottomLeft, 'border_radius_bottom_left');

        $modalFontFamily.on('change', function() {
            $fontFamilyInput.val($(this).val());
            updatePreview();
            updateModalPreview();
        });

        $maxWidthInput.on('input', function() {
            var value = parsePositiveInt($(this).val());
            updateTemplateCustomizationValue('max_width', value);
            updatePreview();
        });
        $maxHeightInput.on('input', function() {
            var value = parsePositiveInt($(this).val());
            updateTemplateCustomizationValue('max_height', value);
            updatePreview();
        });
        $fontFamilyInput.on('change input', function() {
            var value = $(this).val();
            updateTemplateCustomizationValue('font_family', value);
            updatePreview();
        });
        $boxShadowCheckbox.on('change', function() {
            var enabled = $(this).is(':checked') ? 1 : 0;
            updateTemplateCustomizationValue('box_shadow_enabled', enabled);
            updatePreview();
        });
        $boxShadowValue.on('input', function() {
            updateTemplateCustomizationValue('box_shadow_value', $(this).val());
            updatePreview();
        });
        $glassCheckbox.on('change', function() {
            updateTemplateCustomizationValue('glass_effect', $(this).is(':checked') ? 1 : 0);
            updatePreview();
        });

        // Initialize preview on load
        updatePreview();
        
        // Copy shortcode
        $('.grp-copy-shortcode').on('click', function() {
            var shortcode = $(this).data('shortcode');
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(shortcode).select();
            document.execCommand('copy');
            $temp.remove();
            $(this).text('Copied!');
            var $btn = $(this);
            setTimeout(function() {
                $btn.text('Copy');
            }, 2000);
        });
        
        // Generate QR code
        $('#grp-generate-qr').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var originalText = $btn.text();
            $btn.prop('disabled', true).text('Generating...');
            
            var size = $('#grp-qr-size').val();
            
            // Check if grpWidgets is defined
            if (typeof grpWidgets === 'undefined') {
                console.error('grpWidgets is not defined. Make sure widgets-admin.js is properly enqueued.');
                alert('An error occurred. Please refresh the page and try again.');
                $btn.prop('disabled', false).text(originalText);
                return;
            }
            
            $.ajax({
                url: grpWidgets.ajax_url,
                type: 'POST',
                data: {
                    action: 'grp_generate_qr',
                    nonce: grpWidgets.nonce,
                    size: size
                },
                success: function(response) {
                    if (response.success) {
                        $('#grp-qr-preview').html('<img src="' + response.data.qr_url + '" alt="QR Code" style="max-width: 100%;">');
                        $('#grp-qr-download').show();
                        $('#grp-qr-download-link').attr('href', response.data.qr_url);
                    } else {
                        var errorMsg = response.data || 'Failed to generate QR code';
                        alert(errorMsg);
                        console.error('QR Code generation failed:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('QR Code generation error:', error, xhr.responseText);
                    var errorMsg = 'An error occurred while generating the QR code. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.data) {
                        errorMsg = xhr.responseJSON.data;
                    }
                    alert(errorMsg);
                },
                complete: function() {
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        });
    });
    
})(jQuery);


