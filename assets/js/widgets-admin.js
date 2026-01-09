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
        var $gradientStartInput = $('#grp_widget_template_gradient_start');
        var $gradientEndInput = $('#grp_widget_template_gradient_end');
        var $gradientStartText = $('#grp_widget_template_gradient_start_text');
        var $gradientEndText = $('#grp_widget_template_gradient_end_text');
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
        var $modalGradientStart = $('#grp-modal-gradient-start');
        var $modalGradientStartText = $('#grp-modal-gradient-start-text');
        var $modalGradientEnd = $('#grp-modal-gradient-end');
        var $modalGradientEndText = $('#grp-modal-gradient-end-text');
        var $modalLinkText = $('#grp-modal-link-text');
        var $modalLinkColor = $('#grp-modal-link-color');
        var $modalLinkColorText = $('#grp-modal-link-color-text');
        var $modalLinkRows = $('.grp-modal-link-row');
        var $modalBoxShadowEnabled = $('#grp-modal-box-shadow-enabled');
        var $modalBoxShadowEdit = $('#grp-template-editor-box-shadow-edit');
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
            var angle = customization.gradient_angle || 135;
            var startPos = customization.gradient_start_pos || 0;
            var endPos = customization.gradient_end_pos || 100;
            return type + '-gradient(' + angle + 'deg, ' + customization.gradient_start + ' ' + startPos + '%, ' + customization.gradient_end + ' ' + endPos + '%)';
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
            var modalGradientStartValue = $modalGradientStartText.val() || '#24a1ff';
            var modalGradientEndValue = $modalGradientEndText.val() || '#ff7b5a';

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

            var previewHtml = '<div class="' + previewWrapperClass + '"' + previewWrapperAttr + '>' +
                renderPreviewContent(templateData.type || 'button', templateData, {
                title: escapeHtml($('#grp_widget_button_default_text').val() || 'Leave us a review'),
                subtitle: escapeHtml(templateData.subtitle || ''),
                linkText: escapeHtml($linkTextInput.length ? $linkTextInput.val() : 'Click here'),
                showLogo: $modalShowLogo.is(':checked'),
                starColor: modalStarColorValue,
                starText: templateData.stars ? '★★★★★' : '',
                logoIconUrl: logoUrls.icon || '',
                logoClassicUrl: logoUrls.classic || '',
                reviewUrl: previewUrl,
                linkColor: $linkColorText.length ? ($linkColorText.val() || '#111111') : '#111111',
                logoScale: modalLogoScale,
                showLink: templateData.show_link !== false,
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
            if (modalTemplateKey === 'creative-pro' && isValidHex(modalGradientStartValue) && isValidHex(modalGradientEndValue)) {
                modalRootStyles.push('background: linear-gradient(135deg, ' + modalGradientStartValue + ', ' + modalGradientEndValue + ')');
                modalRootStyles.push('color: #fff');
            }
            $modalTemplateRoot.attr('style', modalRootStyles.join('; '));

            if ($modalGlassEffect.is(':checked')) {
                $modalTemplateRoot.addClass('grp-glass-effect');
            } else {
                $modalTemplateRoot.removeClass('grp-glass-effect');
            }
        }

        function closeTemplateModal() {
            $templateEditorModal.removeClass('grp-template-active');
            $templateEditorModal.css('display', 'none');
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
        $gradientStartInput.on('change', function() {
            var color = $(this).val();
            $gradientStartText.val(color);
            updatePreview();
        });
        $gradientStartText.on('input', function() {
            var val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $gradientStartInput.val(val);
            }
            updatePreview();
        });
        $gradientEndInput.on('change', function() {
            var color = $(this).val();
            $gradientEndText.val(color);
            updatePreview();
        });
        $gradientEndText.on('input', function() {
            var val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $gradientEndInput.val(val);
            }
            updatePreview();
        });
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

        $templateEditorModal.on('click', function(e) {
            if ($(e.target).is($templateEditorModal)) {
                closeTemplateModal();
            }
        });

        $('.grp-template-editor-close, #grp-template-editor-close').on('click', closeTemplateModal);

        $modalShowLogo.on('change', function() {
            $logoToggle.prop('checked', $(this).is(':checked'));
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
            var gradientStart = customization.gradient_start || '#0091ff';
            var gradientEnd = customization.gradient_end || '#612c1f';
            $modalGradientStart.val(gradientStart);
            $modalGradientStartText.val(gradientStart);
            $gradientStartInput.val(gradientStart);
            $gradientStartText.val(gradientStart);
            $modalGradientEnd.val(gradientEnd);
            $modalGradientEndText.val(gradientEnd);
            $gradientEndInput.val(gradientEnd);
            $gradientEndText.val(gradientEnd);
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
        }

        $modalTextColor.on('change', function() {
            var color = $(this).val();
            syncTextColor(color);
            updatePreview();
            updateModalPreview();
        });

        $modalTextColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                syncTextColor(value);
                updatePreview();
                updateModalPreview();
            }
        });

        $modalLinkText.on('input', function() {
            var value = $(this).val();
            syncLinkText(value);
            updatePreview();
            updateModalPreview();
        });

        $modalLinkColor.on('change', function() {
            var color = $(this).val();
            syncLinkColor(color);
            updatePreview();
            updateModalPreview();
        });

        $modalLinkColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                syncLinkColor(value);
                updatePreview();
                updateModalPreview();
            }
        });

        $modalBackgroundColor.on('change', function() {
            var color = $(this).val();
            syncBackgroundColor(color);
            updatePreview();
            updateModalPreview();
        });

        $modalBackgroundColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                syncBackgroundColor(value);
                updatePreview();
                updateModalPreview();
            }
        });

        $modalGlassEffect.on('change', function() {
            $glassCheckbox.prop('checked', $(this).is(':checked'));
            updatePreview();
            updateModalPreview();
        });

        $modalBoxShadowEnabled.on('change', function() {
            $boxShadowCheckbox.prop('checked', $(this).is(':checked'));
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
                updatePreview();
                updateModalPreview();
            }
        });

        $modalStarColor.on('change', function() {
            var color = $(this).val();
            $modalStarColorText.val(color);
            $starColorInput.val(color);
            $starColorText.val(color);
            updatePreview();
            updateModalPreview();
        });

        $modalStarColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                $modalStarColor.val(value);
                $starColorInput.val(value);
                $starColorText.val(value);
                updatePreview();
                updateModalPreview();
            }
        });

        $modalStarPlacement.on('change', function() {
            $starPlacementSelect.val($(this).val());
            updatePreview();
            updateModalPreview();
        });

        $modalFontFamily.on('change', function() {
            $fontFamilyInput.val($(this).val());
            updatePreview();
            updateModalPreview();
        });

        $modalGradientStart.on('change', function() {
            var color = $(this).val();
            $modalGradientStartText.val(color);
            $gradientStartInput.val(color);
            $gradientStartText.val(color);
            updatePreview();
            updateModalPreview();
        });

        $modalGradientStartText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                $modalGradientStart.val(value);
                $gradientStartInput.val(value);
                $gradientStartText.val(value);
                updatePreview();
                updateModalPreview();
            }
        });

        $modalGradientEnd.on('change', function() {
            var color = $(this).val();
            $modalGradientEndText.val(color);
            $gradientEndInput.val(color);
            $gradientEndText.val(color);
            updatePreview();
            updateModalPreview();
        });

        $modalGradientEndText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                $modalGradientEnd.val(value);
                $gradientEndInput.val(value);
                $gradientEndText.val(value);
                updatePreview();
                updateModalPreview();
            }
        });
        $maxWidthInput.on('input', updatePreview);
        $boxShadowCheckbox.on('change', updatePreview);
        $boxShadowValue.on('input', updatePreview);
        $glassCheckbox.on('change', updatePreview);
        $fontFamilyInput.on('change input', updatePreview);
        $maxHeightInput.on('input', updatePreview);

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


