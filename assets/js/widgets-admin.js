/**
 * Review Widgets Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Color picker sync
        $('#grp_widget_button_default_color').on('change', function() {
            $('#grp_widget_button_default_color_text').val($(this).val());
        });
        
        $('#grp_widget_button_default_color_text').on('input', function() {
            var val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $('#grp_widget_button_default_color').val(val);
            }
        });
        
        $('#grp_widget_button_default_bg_color').on('change', function() {
            $('#grp_widget_button_default_bg_color_text').val($(this).val());
        });
        
        $('#grp_widget_button_default_bg_color_text').on('input', function() {
            var val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $('#grp_widget_button_default_bg_color').val(val);
            }
        });
        
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
        $('#grp-generate-qr').on('click', function() {
            var $btn = $(this);
            var originalText = $btn.text();
            $btn.prop('disabled', true).text('Generating...');
            
            var size = $('#grp-qr-size').val();
            
            $.post(grpWidgets.ajax_url, {
                action: 'grp_generate_qr',
                nonce: grpWidgets.nonce,
                size: size
            }, function(response) {
                if (response.success) {
                    $('#grp-qr-preview').html('<img src="' + response.data.qr_url + '" alt="QR Code" style="max-width: 100%;">');
                    $('#grp-qr-download').show();
                    $('#grp-qr-download-link').attr('href', response.data.qr_url);
                } else {
                    alert(response.data || 'Failed to generate QR code');
                }
            }).always(function() {
                $btn.prop('disabled', false).text(originalText);
            });
        });
    });
    
})(jQuery);

