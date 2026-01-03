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
        var templateMeta = (typeof grpWidgets !== 'undefined' && grpWidgets.button_templates) ? grpWidgets.button_templates : {};
        var templateClassList = Object.keys(templateMeta).map(function(key) {
            return 'grp-review-button-template-' + key;
        });
        var qrCache = {};
        var blankQr = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
        var isPro = typeof grpWidgets !== 'undefined' ? !!grpWidgets.is_pro : false;
        var hasPlaceId = typeof grpWidgets !== 'undefined' ? !!grpWidgets.has_place_id : false;

        window.updateGRPPreview = function() {
            updatePreview();
        };

        function getTemplateData(key) {
            if (templateMeta[key]) {
                return templateMeta[key];
            }
            return templateMeta.basic || {};
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
            $previewBtn.find('.grp-card-qr img').attr('src', src);
        }

        function hidePreviewQr() {
            if ($previewQr.length && $previewQrImg.length) {
                $previewQr.removeClass('has-qr');
                $previewQrImg.attr('src', blankQr);
            }
            $previewBtn.find('.grp-card-qr img').attr('src', blankQr);
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
            var qrHtml = templateData && templateData.qr ? '<img id="grp-preview-qr-img" src="' + blankQr + '" alt="QR">' : '';
            var starHtml = templateData && templateData.stars ? '<div class="grp-context-star-row" style="color:' + options.starColor + ';">' + options.starText + '</div>' : '';

            if (templateType === 'layout1') {
                return '<div class="grp-layout1-preview">' +
                    '<div class="grp-layout1-qr">' + qrHtml + '</div>' +
                    '<div class="grp-layout1-details">' +
                        (options.showLogo ? '<div class="grp-layout1-logo">G</div>' : '') +
                        '<div class="grp-layout1-stars" style="color:' + options.starColor + ';">' + options.starText + '</div>' +
                        '<div class="grp-layout1-title">' + options.title + '</div>' +
                        '<div class="grp-layout1-subtitle">' + options.subtitle + '</div>' +
                        '<div class="grp-layout1-underline">' + buildUnderline(templateData.underline_colors) + '</div>' +
                    '</div>' +
                '</div>';
            }

            if (templateType === 'layout2') {
                var darkClass = templateData.dark ? ' grp-layout-dark' : '';
                return '<div class="grp-layout2-preview' + darkClass + '">' +
                    '<div class="grp-layout2-logo">G</div>' +
                    '<div class="grp-layout2-stars" style="color:' + options.starColor + ';">' + options.starText + '</div>' +
                    '<div class="grp-layout2-heading">' + options.title + '</div>' +
                    '<div class="grp-layout2-subtitle">' + options.subtitle + '</div>' +
                    '<div class="grp-layout2-qr">' + qrHtml + '</div>' +
                    '<div class="grp-layout2-link">' + options.linkText + '</div>' +
                    '<div class="grp-layout2-underline">' + buildUnderline(templateData.underline_colors) + '</div>' +
                '</div>';
            }

            if (templateType === 'card') {
                var inner = '<div class="grp-review-card">';
                if (options.showLogo) {
                    inner += '<div class="grp-card-logo">G</div>';
                }
                inner += '<div class="grp-card-stars" style="color:' + options.starColor + ';">' + options.starText + '</div>';
                inner += '<div class="grp-card-heading">' + options.title + '</div>';
                inner += '<div class="grp-card-subtitle">' + options.subtitle + '</div>';
                inner += '<div class="grp-card-qr">' + qrHtml + '</div>';
                inner += '<div class="grp-card-link">' + options.linkText + '</div>';
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

        function updatePreview() {
            var text = $('#grp_widget_button_default_text').val();
            var style = $('#grp_widget_button_default_style').val();
            var size = $('#grp_widget_button_default_size').val();
            var textColor = $('#grp_widget_button_default_color_text').val();
            var bgColor = $('#grp_widget_button_default_bg_color_text').val();
            var templateKey = $templateSelect.length ? ($templateSelect.val() || 'basic') : 'basic';
            var templateData = getTemplateData(templateKey);
            var starColor = $starColorText.val() || '#FBBD05';
            var starPlacement = $starPlacementSelect.val() || 'below';
            var showLogo = $logoToggle.is(':checked');
            var fontFamily = $fontFamilyInput.val();
            var maxHeight = parseInt($maxHeightInput.val(), 10) || 0;
            var gradientStart = $gradientStartText.length ? $gradientStartText.val() : '#24a1ff';
            var gradientEnd = $gradientEndText.length ? $gradientEndText.val() : '#ff7b5a';
            var gradientStart = $gradientStartText.length ? $gradientStartText.val() : '#24a1ff';
            var gradientEnd = $gradientEndText.length ? $gradientEndText.val() : '#ff7b5a';

            // Update preview button text
            $('#grp-preview-text').text(text || 'Leave us a review');

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

            var styles = [];
            if (textColor && textColor.trim() !== '' && /^#[0-9A-F]{6}$/i.test(textColor)) {
                styles.push('color: ' + textColor);
            }
            if (bgColor && bgColor.trim() !== '' && /^#[0-9A-F]{6}$/i.test(bgColor)) {
                styles.push('background-color: ' + bgColor);
            }
            if (fontFamily) {
                styles.push('font-family: ' + fontFamily);
            }
            if (maxHeight > 0) {
                styles.push('max-height: ' + maxHeight + 'px');
            }
            if (templateData.type === 'card' && templateKey === 'creative-pro' && /^#[0-9A-F]{6}$/i.test(gradientStart) && /^#[0-9A-F]{6}$/i.test(gradientEnd)) {
                styles.push('background: linear-gradient(135deg, ' + gradientStart + ', ' + gradientEnd + ')');
            }
            $previewBtn.attr('style', styles.join('; '));

            var subtitleText = templateData.tagline || templateData.subtitle || 'Scan the QR code below to leave a review!';
            var previewHtml = renderPreviewContent(templateData.type || 'button', templateData, {
                title: escapeHtml(text || 'Leave us a review'),
                subtitle: escapeHtml(subtitleText),
                linkText: escapeHtml(templateData.link_text || 'www.google.com'),
                showLogo: showLogo,
                starColor: starColor,
                starText: templateData.stars ? '★★★★★' : '',
            });
            $previewBtn.html(previewHtml);

            if (templateData.qr) {
                fetchPreviewQr(templateData.qr_size || 120);
            } else {
                hidePreviewQr();
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
        $fontFamilyInput.on('input', updatePreview);
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


