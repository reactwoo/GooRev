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
            if (!$previewQr.length || !$previewQrImg.length) {
                return;
            }
            $previewQrImg.attr('src', src);
            $previewQr.addClass('has-qr');
        }

        function hidePreviewQr() {
            if (!$previewQr.length || !$previewQrImg.length) {
                return;
            }
            $previewQr.removeClass('has-qr');
            $previewQrImg.attr('src', blankQr);
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

            // Update preview button text
            $('#grp-preview-text').text(text || 'Leave us a review');

            // Update preview button classes
            $previewBtn.removeClass('grp-review-button-default grp-review-button-rounded grp-review-button-outline grp-review-button-minimal');
            $previewBtn.removeClass('grp-review-button-small grp-review-button-medium grp-review-button-large');
            $previewBtn.removeClass('grp-star-placement-above grp-star-placement-below grp-star-placement-overlay');
            $previewBtn.addClass('grp-review-button-' + style);
            $previewBtn.addClass('grp-review-button-' + size);
            if (templateClassList.length) {
                $previewBtn.removeClass(templateClassList.join(' '));
            }
            $previewBtn.addClass('grp-review-button-template-' + templateKey);
            $previewBtn.addClass('grp-star-placement-' + starPlacement);

            // Update template description/pro note
            updateTemplateDescription(templateKey);

            // Update preview tagline/stars
            var tagline = templateData.tagline || '';
            var stars = templateData.stars ? '★★★★★' : '';
            if ($previewTagline.length) {
                $previewTagline.text(tagline).toggle(!!tagline);
            }
            if ($previewStarRow.length) {
                $previewStarRow.text(stars).toggle(!!stars).css('color', starColor);
            }

            // Toggle logo
            if (showLogo) {
                $previewBtn.find('.grp-review-button-icon').show();
            } else {
                $previewBtn.find('.grp-review-button-icon').hide();
            }

            // Update preview button styles - clear inline styles first
            $previewBtn.attr('style', '');
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
            if (styles.length > 0) {
                $previewBtn.attr('style', styles.join('; '));
            }

            // Update QR preview if needed
            if (templateData.qr) {
                fetchPreviewQr(templateData.qr_size || 100);
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


