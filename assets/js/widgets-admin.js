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
        var $logoScaleSlider = $('#grp_widget_template_logo_scale');
        var $logoScaleText = $('#grp_widget_template_logo_scale_text');
        var $gradientRows = $('.grp-gradient-row');
        var templateMeta = (typeof grpWidgets !== 'undefined' && grpWidgets.button_templates) ? grpWidgets.button_templates : {};
        var templateClassList = Object.keys(templateMeta).map(function(key) {
            return 'grp-review-button-template-' + key;
        });
        var logoScaleTouched = false;
        var modalTemplateKey = $templateSelect.length ? ($templateSelect.val() || 'basic') : 'basic';
        var qrCache = {};
        var blankQr = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
        var isPro = typeof grpWidgets !== 'undefined' ? !!grpWidgets.is_pro : false;
        var hasPlaceId = typeof grpWidgets !== 'undefined' ? !!grpWidgets.has_place_id : false;
        var logoUrls = (typeof grpWidgets !== 'undefined' && grpWidgets.logo_urls) ? grpWidgets.logo_urls : {};

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

            if (templateType === 'layout1') {
                return '<div class="grp-layout1-preview">' +
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
                return '<div class="grp-layout2-preview' + darkClass + '">' +
                    (options.showLogo && options.logoClassicUrl ? '<img src="' + options.logoClassicUrl + '" class="grp-layout2-logo-img" alt="Google" style="width:' + options.logoScale + '%;">' : '') +
                    '<div class="grp-layout2-stars" style="color:' + options.starColor + ';">' + options.starText + '</div>' +
                    '<div class="grp-layout2-heading">' + options.title + '</div>' +
                    '<div class="grp-layout2-subtitle">' + options.subtitle + '</div>' +
                    '<div class="grp-layout2-qr">' + qrHtml + '</div>' +
                    '<div class="grp-layout2-link" style="color:' + linkColor + ';">' + linkHtml + '</div>' +
                    '<div class="grp-layout2-underline">' + buildUnderline(templateData.underline_colors) + '</div>' +
                '</div>';
            }

            if (templateType === 'card') {
                var inner = '<div class="grp-review-card">';
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

        function isValidHex(color) {
            return /^#[0-9A-F]{6}$/i.test(color);
        }

        function populateTemplateModal() {
            modalTemplateKey = $templateSelect.length ? ($templateSelect.val() || 'basic') : 'basic';
            var templateData = getTemplateData(modalTemplateKey);
            var isCreative = modalTemplateKey === 'creative-pro';

            $modalGradientSection.toggle(isCreative);

            $modalShowLogo.prop('checked', $logoToggle.is(':checked'));
            var logoScaleValue = parseInt($logoScaleSlider.val(), 10);
            if (isNaN(logoScaleValue) || logoScaleValue <= 0) {
                logoScaleValue = 50;
            }
            $modalLogoScaleSlider.val(logoScaleValue);
            $modalLogoScaleNumber.val(logoScaleValue);

            var starColorValue = $starColorText.val() || '#FBBD05';
            $modalStarColor.val(starColorValue);
            $modalStarColorText.val(starColorValue);
            $modalStarPlacement.val($starPlacementSelect.val() || 'below');

            $modalFontFamily.val($fontFamilyInput.val() || '');

            var textColorValue = $modalTextColorText.val() || $('#grp_widget_button_default_color_text').val() || '#ffffff';
            $modalTextColor.val(textColorValue);
            $modalTextColorText.val(textColorValue);

            var backgroundColorValue = $modalBackgroundColorText.val() || $('#grp_widget_button_default_bg_color_text').val() || '#2b2b2b';
            $modalBackgroundColor.val(backgroundColorValue);
            $modalBackgroundColorText.val(backgroundColorValue);

            $modalGlassEffect.prop('checked', $glassCheckbox.is(':checked'));

            var gradientStartValue = $gradientStartText.val() || '#24a1ff';
            var gradientEndValue = $gradientEndText.val() || '#ff7b5a';
            $modalGradientStart.val(gradientStartValue);
            $modalGradientStartText.val(gradientStartValue);
            $modalGradientEnd.val(gradientEndValue);
            $modalGradientEndText.val(gradientEndValue);

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

            var previewHtml = renderPreviewContent(templateData.type || 'button', templateData, {
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
            });
            $templateEditorPreview.html(previewHtml);

            if (modalTemplateKey === 'creative-pro' && isValidHex(modalGradientStartValue) && isValidHex(modalGradientEndValue)) {
                $templateEditorPreview.css('background', 'linear-gradient(135deg, ' + modalGradientStartValue + ', ' + modalGradientEndValue + ')');
                $templateEditorPreview.css('color', '#fff');
            } else {
                $templateEditorPreview.css('background', modalBackgroundColorValue);
                $templateEditorPreview.css('color', modalTextColorValue);
            }

            if ($modalGlassEffect.is(':checked')) {
                $templateEditorPreview.addClass('grp-glass-effect');
            } else {
                $templateEditorPreview.removeClass('grp-glass-effect');
            }
        }

        function closeTemplateModal() {
            $templateEditorModal.removeClass('grp-template-active');
        }

        function updatePreview() {
            var text = $('#grp_widget_button_default_text').val();
            var style = $('#grp_widget_button_default_style').val();
            var size = $('#grp_widget_button_default_size').val();
            var textColor = safeTrimValue($('#grp_widget_button_default_color_text'));
            var bgColor = safeTrimValue($('#grp_widget_button_default_bg_color_text'));
            var templateKey = $templateSelect.length ? ($templateSelect.val() || 'basic') : 'basic';
            var templateData = getTemplateData(templateKey);
            var previewUrl = $previewBtn.attr('href') || '#';
            var starColor = $starColorText.val() || '#FBBD05';
            var starPlacement = $starPlacementSelect.val() || 'below';
            var showLogo = $logoToggle.is(':checked');
            var fontFamily = $fontFamilyInput.val();
            var maxHeight = parseInt($maxHeightInput.val(), 10) || 0;
            var maxWidth = parseInt($maxWidthInput.val(), 10) || 0;
            var linkColorRaw = $linkColorText.length ? safeTrimValue($linkColorText) : '';
            var linkColor = linkColorRaw || '#ffffff';
            var gradientStart = $gradientStartText.length ? $gradientStartText.val() : '#24a1ff';
            var gradientEnd = $gradientEndText.length ? $gradientEndText.val() : '#ff7b5a';
            var logoScale = parseInt($logoScaleSlider.val(), 10);
            if (!logoScaleTouched && templateKey === 'layout1') {
                logoScale = 15;
                $logoScaleSlider.val(logoScale);
                $logoScaleText.val(logoScale);
            }
            if (isNaN(logoScale) || logoScale <= 0) {
                logoScale = 50;
            }
            var linkText = safeTrimValue($linkTextInput) || 'Click here';
            var boxShadowEnabled = $boxShadowCheckbox.is(':checked');
            var boxShadowValue = safeTrimValue($boxShadowValue);
            var glassEffect = $glassCheckbox.is(':checked');

            // Update template description/pro note
            updateTemplateDescription(templateKey);

            var classes = [
                'grp-review-button',
                'grp-review-button-' + style,
                'grp-review-button-' + size,
                'grp-review-button-template-' + templateKey,
                'grp-star-placement-' + starPlacement
            ];
            $previewBtn.attr('class', classes.join(' '));
            if (glassEffect) {
                $previewBtn.addClass('grp-glass-effect');
            } else {
                $previewBtn.removeClass('grp-glass-effect');
            }

            var styles = [];
            if (textColor && /^#[0-9A-F]{6}$/i.test(textColor)) {
                styles.push('color: ' + textColor);
            }
            if (bgColor && /^#[0-9A-F]{6}$/i.test(bgColor)) {
                styles.push('background-color: ' + bgColor);
            }
            if (fontFamily) {
                styles.push('font-family: ' + fontFamily);
            }
            if (maxHeight > 0) {
                styles.push('max-height: ' + maxHeight + 'px');
            }
            if (maxWidth > 0) {
                styles.push('max-width: ' + maxWidth + 'px');
            }
            if (boxShadowEnabled && boxShadowValue) {
                styles.push('box-shadow: ' + boxShadowValue);
            }
            if (templateData.type === 'card' && templateKey === 'creative-pro' && /^#[0-9A-F]{6}$/i.test(gradientStart) && /^#[0-9A-F]{6}$/i.test(gradientEnd)) {
                styles.push('background: linear-gradient(135deg, ' + gradientStart + ', ' + gradientEnd + ')');
            }
            $previewBtn.attr('style', styles.join('; '));

            var subtitleText = templateData.tagline || templateData.subtitle || 'Scan the QR code below to leave a review!';
            var previewHtml = renderPreviewContent(templateData.type || 'button', templateData, {
                title: escapeHtml(text || 'Leave us a review'),
                subtitle: escapeHtml(subtitleText),
                linkText: escapeHtml(linkText),
                showLogo: showLogo,
                starColor: starColor,
                starText: templateData.stars ? '★★★★★' : '',
                logoIconUrl: logoUrls.icon || '',
                logoClassicUrl: logoUrls.classic || '',
                reviewUrl: previewUrl,
                linkColor: linkColor,
                logoScale: logoScale,
                showLink: templateData.show_link !== false,
            });
            $previewBtn.html(previewHtml);

            var sanitizedPreviewUrl = String(previewUrl || '').trim();
            if (templateData.qr && hasPlaceId && sanitizedPreviewUrl && sanitizedPreviewUrl !== '#') {
                fetchPreviewQr(templateData.qr_size || 135);
            } else {
                hidePreviewQr();
            }

            toggleGradientControls(templateKey);
            updateModalPreview();
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
            populateTemplateModal();
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


