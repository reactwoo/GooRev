/**
 * Review Widgets Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Update preview function
        function updatePreview() {
            var text = $('#grp_widget_button_default_text').val();
            var style = $('#grp_widget_button_default_style').val();
            var size = $('#grp_widget_button_default_size').val();
            var textColor = $('#grp_widget_button_default_color_text').val();
            var bgColor = $('#grp_widget_button_default_bg_color_text').val();
            
            // Update preview button text
            $('#grp-preview-text').text(text);
            
            // Update preview button classes
            var $previewBtn = $('#grp-preview-button');
            $previewBtn.removeClass('grp-review-button-default grp-review-button-rounded grp-review-button-outline grp-review-button-minimal');
            $previewBtn.removeClass('grp-review-button-small grp-review-button-medium grp-review-button-large');
            $previewBtn.addClass('grp-review-button-' + style);
            $previewBtn.addClass('grp-review-button-' + size);
            
            // Update preview button styles
            var styles = [];
            if (textColor && /^#[0-9A-F]{6}$/i.test(textColor)) {
                styles.push('color: ' + textColor);
            }
            if (bgColor && /^#[0-9A-F]{6}$/i.test(bgColor)) {
                styles.push('background-color: ' + bgColor);
            }
            $previewBtn.attr('style', styles.join('; '));
        }
        
        // Color picker sync and preview updates
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
        
        // Clear color buttons
        $('#grp-clear-text-color').on('click', function() {
            $('#grp_widget_button_default_color_text').val('');
            updatePreview();
        });
        
        $('#grp-clear-bg-color').on('click', function() {
            $('#grp_widget_button_default_bg_color_text').val('');
            updatePreview();
        });
        
        // Update preview on text, style, and size changes
        $('#grp_widget_button_default_text').on('input', updatePreview);
        $('#grp_widget_button_default_style').on('change', updatePreview);
        $('#grp_widget_button_default_size').on('change', updatePreview);
        
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


