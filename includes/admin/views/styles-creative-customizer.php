<script>
// Advanced customizer function for Creative style - Defined globally so it can be called from event handlers
// This function is loaded separately to keep the code organized
window.openCreativeCustomizer = function(style, styleName) {
    // Ensure jQuery is available
    var $ = jQuery;
    
    // Remove existing modal if any
    $('#grp-creative-customizer-modal').remove();
    
    var creativeModalHtml = '<div class="grp-modal-overlay" id="grp-creative-customizer-modal">' +
        '<div class="grp-modal-content grp-creative-customizer-modal-content">' +
        '<div class="grp-modal-header">' +
        '<h2><?php esc_js_e('Advanced Customize', 'google-reviews-plugin'); ?> ' + styleName + '</h2>' +
        '<button class="grp-modal-close" aria-label="<?php esc_attr_e('Close', 'google-reviews-plugin'); ?>">&times;</button>' +
        '</div>' +
        '<div class="grp-modal-body grp-customizer-body">' +
        '<div class="grp-customizer-tabs">' +
        '<button class="grp-customizer-tab-btn active" data-tab="gradients"><?php esc_js_e('Gradients', 'google-reviews-plugin'); ?></button>' +
        '<button class="grp-customizer-tab-btn" data-tab="typography"><?php esc_js_e('Typography', 'google-reviews-plugin'); ?></button>' +
        '<button class="grp-customizer-tab-btn" data-tab="effects"><?php esc_js_e('Effects', 'google-reviews-plugin'); ?></button>' +
        '</div>' +
        
        // Gradients Tab
        '<div class="grp-customizer-tab-content active" data-tab="gradients">' +
        '<h3><?php esc_js_e('Gradient Background Settings', 'google-reviews-plugin'); ?></h3>' +
        '<div class="grp-customizer-control">' +
        '<label><?php esc_js_e('Gradient Type', 'google-reviews-plugin'); ?></label>' +
        '<select id="grp-creative-gradient-type" class="grp-font-select">' +
        '<option value="linear"><?php esc_js_e('Linear', 'google-reviews-plugin'); ?></option>' +
        '<option value="radial"><?php esc_js_e('Radial', 'google-reviews-plugin'); ?></option>' +
        '</select>' +
        '</div>' +
        '<div class="grp-customizer-control" id="grp-gradient-angle-control">' +
        '<label><?php esc_js_e('Gradient Angle', 'google-reviews-plugin'); ?> <span class="grp-value-display" id="grp-gradient-angle-value">90deg</span></label>' +
        '<input type="range" id="grp-creative-gradient-angle" class="grp-range-input" min="0" max="360" value="90" step="1">' +
        '</div>' +
        '<div class="grp-customizer-control" id="grp-gradient-start-position-control">' +
        '<label><?php esc_js_e('Start Position', 'google-reviews-plugin'); ?> <span class="grp-value-display" id="grp-gradient-start-position-value">0%</span></label>' +
        '<input type="range" id="grp-creative-gradient-start-position" class="grp-range-input" min="0" max="100" value="0" step="1">' +
        '</div>' +
        '<div class="grp-customizer-control" id="grp-gradient-end-position-control">' +
        '<label><?php esc_js_e('End Position', 'google-reviews-plugin'); ?> <span class="grp-value-display" id="grp-gradient-end-position-value">100%</span></label>' +
        '<input type="range" id="grp-creative-gradient-end-position" class="grp-range-input" min="0" max="100" value="100" step="1">' +
        '</div>' +
        '<div class="grp-customizer-control">' +
        '<label><?php esc_js_e('Start Color', 'google-reviews-plugin'); ?></label>' +
        '<div class="grp-color-control">' +
        '<input type="color" id="grp-creative-gradient-start" class="grp-color-input" value="#4285F4">' +
        '<input type="text" class="grp-color-text" value="#4285F4" placeholder="#4285F4">' +
        '</div>' +
        '</div>' +
        '<div class="grp-customizer-control">' +
        '<label><?php esc_js_e('End Color', 'google-reviews-plugin'); ?></label>' +
        '<div class="grp-color-control">' +
        '<input type="color" id="grp-creative-gradient-end" class="grp-color-input" value="#EA4335">' +
        '<input type="text" class="grp-color-text" value="#EA4335" placeholder="#EA4335">' +
        '</div>' +
        '</div>' +
        '<div class="grp-customizer-control">' +
        '<label><?php esc_js_e('Gradient Preview', 'google-reviews-plugin'); ?></label>' +
        '<div id="grp-gradient-preview" style="width: 100%; height: 80px; border-radius: 8px; border: 1px solid #ddd; background: linear-gradient(to right, #4285F4, #EA4335);"></div>' +
        '</div>' +
        '</div>' +
        
        // Typography Tab with Google Fonts
        '<div class="grp-customizer-tab-content" data-tab="typography">' +
        '<h3><?php esc_js_e('Typography Settings', 'google-reviews-plugin'); ?></h3>' +
        '<div class="grp-customizer-control">' +
        '<label><?php esc_js_e('Body Font (Google Fonts)', 'google-reviews-plugin'); ?></label>' +
        '<select id="grp-creative-body-font" class="grp-font-select grp-google-font-select">' +
        '<option value=""><?php esc_js_e('Default (Poppins)', 'google-reviews-plugin'); ?></option>' +
        '<option value="Inter">Inter</option>' +
        '<option value="Roboto">Roboto</option>' +
        '<option value="Open Sans">Open Sans</option>' +
        '<option value="Lato">Lato</option>' +
        '<option value="Montserrat">Montserrat</option>' +
        '<option value="DM Sans">DM Sans</option>' +
        '<option value="Playfair Display">Playfair Display</option>' +
        '<option value="Merriweather">Merriweather</option>' +
        '<option value="Raleway">Raleway</option>' +
        '<option value="Oswald">Oswald</option>' +
        '</select>' +
        '<p class="description"><?php esc_js_e('Google Fonts will be automatically loaded', 'google-reviews-plugin'); ?></p>' +
        '</div>' +
        '<div class="grp-customizer-control">' +
        '<label><?php esc_js_e('Name Font (Google Fonts)', 'google-reviews-plugin'); ?></label>' +
        '<select id="grp-creative-name-font" class="grp-font-select grp-google-font-select">' +
        '<option value=""><?php esc_js_e('Default (Poppins)', 'google-reviews-plugin'); ?></option>' +
        '<option value="Inter">Inter</option>' +
        '<option value="Roboto">Roboto</option>' +
        '<option value="Montserrat">Montserrat</option>' +
        '<option value="DM Sans">DM Sans</option>' +
        '<option value="Playfair Display">Playfair Display</option>' +
        '<option value="Oswald">Oswald</option>' +
        '</select>' +
        '</div>' +
        '<div class="grp-customizer-control">' +
        '<label><?php esc_js_e('Body Font Size', 'google-reviews-plugin'); ?> <span class="grp-value-display" id="grp-creative-body-font-size-value">20px</span></label>' +
        '<input type="range" id="grp-creative-body-font-size" class="grp-range-input" min="14" max="28" value="20" step="1">' +
        '</div>' +
        '<div class="grp-customizer-control">' +
        '<label><?php esc_js_e('Name Font Size', 'google-reviews-plugin'); ?> <span class="grp-value-display" id="grp-creative-name-font-size-value">18px</span></label>' +
        '<input type="range" id="grp-creative-name-font-size" class="grp-range-input" min="14" max="24" value="18" step="1">' +
        '</div>' +
        '</div>' +
        
        // Effects Tab
        '<div class="grp-customizer-tab-content" data-tab="effects">' +
        '<h3><?php esc_js_e('Visual Effects', 'google-reviews-plugin'); ?></h3>' +
        '<div class="grp-customizer-control">' +
        '<label><?php esc_js_e('Avatar Size', 'google-reviews-plugin'); ?> <span class="grp-value-display" id="grp-creative-avatar-size-value">80px</span></label>' +
        '<input type="range" id="grp-creative-avatar-size" class="grp-range-input" min="40" max="120" value="80" step="4">' +
        '</div>' +
        '<div class="grp-customizer-control">' +
        '<label><?php esc_js_e('Quote Mark Size', 'google-reviews-plugin'); ?> <span class="grp-value-display" id="grp-creative-quote-size-value">48px</span></label>' +
        '<input type="range" id="grp-creative-quote-size" class="grp-range-input" min="32" max="80" value="48" step="4">' +
        '</div>' +
        '<div class="grp-customizer-control">' +
        '<label><?php esc_js_e('Border Radius', 'google-reviews-plugin'); ?> <span class="grp-value-display" id="grp-creative-border-radius-value">16px</span></label>' +
        '<input type="range" id="grp-creative-border-radius" class="grp-range-input" min="0" max="30" value="16" step="1">' +
        '</div>' +
        '<div class="grp-customizer-control">' +
        '<label><?php esc_js_e('Star Size', 'google-reviews-plugin'); ?> <span class="grp-value-display" id="grp-creative-star-size-value">32px</span></label>' +
        '<input type="range" id="grp-creative-star-size" class="grp-range-input" min="20" max="48" value="32" step="2">' +
        '</div>' +
        '</div>' +
        
        '</div>' +
        '<div class="grp-modal-footer">' +
        '<button class="button grp-preview-css-creative"><?php esc_js_e('Preview CSS', 'google-reviews-plugin'); ?></button>' +
        '<button class="button button-primary grp-save-creative-customizations" data-style="' + style + '"><?php esc_js_e('Save Customizations', 'google-reviews-plugin'); ?></button>' +
        '<button class="button grp-modal-close"><?php esc_js_e('Cancel', 'google-reviews-plugin'); ?></button>' +
        '</div>' +
        '</div>' +
        '</div>';
    
    $('body').append(creativeModalHtml);
    $('#grp-creative-customizer-modal').css('display', 'flex');
    
    // Gradient preview update function
    function updateGradientPreview() {
        var type = $('#grp-creative-gradient-type').val();
        var angle = $('#grp-creative-gradient-angle').val();
        var startPos = $('#grp-creative-gradient-start-position').val();
        var endPos = $('#grp-creative-gradient-end-position').val();
        var start = $('#grp-creative-gradient-start').val();
        var end = $('#grp-creative-gradient-end').val();
        var gradient;
        
        if (type === 'radial') {
            gradient = 'radial-gradient(circle, ' + start + ' ' + startPos + '%, ' + end + ' ' + endPos + '%)';
        } else {
            gradient = 'linear-gradient(' + angle + 'deg, ' + start + ' ' + startPos + '%, ' + end + ' ' + endPos + '%)';
        }
        
        $('#grp-gradient-preview').css('background', gradient);
    }
    
    // Show/hide gradient controls based on type
    function toggleGradientControls() {
        var type = $('#grp-creative-gradient-type').val();
        if (type === 'radial') {
            $('#grp-gradient-angle-control').hide();
        } else {
            $('#grp-gradient-angle-control').show();
        }
    }
    
    // Initialize gradient controls visibility
    toggleGradientControls();
    
    // Color input sync and gradient preview
    $(document).off('input change', '#grp-creative-customizer-modal .grp-color-input, #grp-creative-customizer-modal #grp-creative-gradient-type, #grp-creative-customizer-modal #grp-creative-gradient-angle, #grp-creative-customizer-modal #grp-creative-gradient-start-position, #grp-creative-customizer-modal #grp-creative-gradient-end-position');
    $(document).on('input change', '#grp-creative-customizer-modal .grp-color-input, #grp-creative-customizer-modal #grp-creative-gradient-type, #grp-creative-customizer-modal #grp-creative-gradient-angle, #grp-creative-customizer-modal #grp-creative-gradient-start-position, #grp-creative-customizer-modal #grp-creative-gradient-end-position', function() {
        if ($(this).hasClass('grp-color-input')) {
            $(this).siblings('.grp-color-text').val($(this).val());
        }
        if ($(this).attr('id') === 'grp-creative-gradient-type') {
            toggleGradientControls();
        }
        updateGradientPreview();
    });
    
    $(document).off('input change', '#grp-creative-customizer-modal .grp-color-text');
    $(document).on('input change', '#grp-creative-customizer-modal .grp-color-text', function() {
        var val = $(this).val();
        if (/^#[0-9A-F]{6}$/i.test(val)) {
            $(this).siblings('.grp-color-input').val(val);
            updateGradientPreview();
        }
    });
    
    // Range input value display
    $(document).off('input', '#grp-creative-customizer-modal .grp-range-input');
    $(document).on('input', '#grp-creative-customizer-modal .grp-range-input', function() {
        var value = $(this).val();
        var id = $(this).attr('id');
        var unit = 'px';
        
        // Handle different units for different controls
        if (id === 'grp-creative-gradient-angle') {
            unit = 'deg';
        } else if (id === 'grp-creative-gradient-start-position' || id === 'grp-creative-gradient-end-position') {
            unit = '%';
        }
        
        $(this).closest('.grp-customizer-control').find('.grp-value-display').text(value + unit);
    });
    
    // Tab switching
    $(document).off('click', '#grp-creative-customizer-modal .grp-customizer-tab-btn');
    $(document).on('click', '#grp-creative-customizer-modal .grp-customizer-tab-btn', function() {
        var tab = $(this).data('tab');
        $('#grp-creative-customizer-modal .grp-customizer-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('#grp-creative-customizer-modal .grp-customizer-tab-content').removeClass('active');
        $('#grp-creative-customizer-modal .grp-customizer-tab-content[data-tab="' + tab + '"]').addClass('active');
    });
    
    // Generate CSS function for Creative
    function generateCreativeCss(style) {
        var css = '/* Advanced custom styles for ' + styleName + ' style - Generated on ' + new Date().toLocaleDateString() + ' */\n\n';
        
        // Gradient
        var gradientType = $('#grp-creative-gradient-type').val();
        var gradientAngle = $('#grp-creative-gradient-angle').val();
        var gradientStartPos = $('#grp-creative-gradient-start-position').val();
        var gradientEndPos = $('#grp-creative-gradient-end-position').val();
        var gradientStart = $('#grp-creative-gradient-start').val();
        var gradientEnd = $('#grp-creative-gradient-end').val();
        var gradient;
        
        if (gradientType === 'radial') {
            gradient = 'radial-gradient(circle, ' + gradientStart + ' ' + gradientStartPos + '%, ' + gradientEnd + ' ' + gradientEndPos + '%)';
        } else {
            gradient = 'linear-gradient(' + gradientAngle + 'deg, ' + gradientStart + ' ' + gradientStartPos + '%, ' + gradientEnd + ' ' + gradientEndPos + '%)';
        }
        
        css += '.grp-style-' + style + ' .grp-review {\n';
        css += '    background: ' + gradient + ' !important;\n';
        var borderRadius = $('#grp-creative-border-radius').val();
        if (borderRadius && borderRadius !== '16') {
            css += '    border-radius: ' + borderRadius + 'px !important;\n';
        }
        css += '}\n\n';
        
        // Typography with Google Fonts
        var bodyFont = $('#grp-creative-body-font').val();
        if (bodyFont) {
            css += '@import url(\'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(bodyFont) + ':wght@400;600;700&display=swap\');\n';
            css += '.grp-style-' + style + ' .grp-review-text {\n';
            css += '    font-family: "' + bodyFont + '", sans-serif !important;\n';
        }
        var bodyFontSize = $('#grp-creative-body-font-size').val();
        if (bodyFontSize && bodyFontSize !== '20') {
            if (!bodyFont) css += '.grp-style-' + style + ' .grp-review-text {\n';
            css += '    font-size: ' + bodyFontSize + 'px !important;\n';
        }
        if (bodyFont || (bodyFontSize && bodyFontSize !== '20')) {
            css += '}\n\n';
        }
        
        var nameFont = $('#grp-creative-name-font').val();
        if (nameFont) {
            if (nameFont !== bodyFont) {
                css += '@import url(\'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(nameFont) + ':wght@400;600;700&display=swap\');\n';
            }
            css += '.grp-style-' + style + ' .grp-author-name {\n';
            css += '    font-family: "' + nameFont + '", sans-serif !important;\n';
        }
        var nameFontSize = $('#grp-creative-name-font-size').val();
        if (nameFontSize && nameFontSize !== '18') {
            if (!nameFont) css += '.grp-style-' + style + ' .grp-author-name {\n';
            css += '    font-size: ' + nameFontSize + 'px !important;\n';
        }
        if (nameFont || (nameFontSize && nameFontSize !== '18')) {
            css += '}\n\n';
        }
        
        // Effects
        var avatarSize = $('#grp-creative-avatar-size').val();
        if (avatarSize && avatarSize !== '80') {
            css += '.grp-style-' + style + ' .grp-review-avatar img {\n';
            css += '    width: ' + avatarSize + 'px !important;\n';
            css += '    height: ' + avatarSize + 'px !important;\n';
            css += '}\n\n';
        }
        
        var quoteSize = $('#grp-creative-quote-size').val();
        if (quoteSize && quoteSize !== '48') {
            css += '.grp-style-' + style + ' .grp-review-quote {\n';
            css += '    font-size: ' + quoteSize + 'px !important;\n';
            css += '}\n\n';
        }
        
        var starSize = $('#grp-creative-star-size').val();
        if (starSize && starSize !== '32') {
            css += '.grp-style-' + style + ' .grp-star {\n';
            css += '    font-size: ' + starSize + 'px !important;\n';
            css += '}\n\n';
        }
        
        return css;
    }
    
    // Save Creative customizations
    $(document).off('click', '#grp-creative-customizer-modal .grp-save-creative-customizations');
    $(document).on('click', '#grp-creative-customizer-modal .grp-save-creative-customizations', function() {
        var style = $(this).data('style');
        var css = generateCreativeCss(style);
        
        $.post(ajaxurl, {
            action: 'grp_save_custom_css',
            nonce: '<?php echo wp_create_nonce('grp_admin_nonce'); ?>',
            css: css
        }, function(response) {
            if (response.success) {
                if ($('#grp-custom-css').length) {
                    $('#grp-custom-css').val(css);
                }
                alert('<?php esc_js_e('Creative customizations saved successfully!', 'google-reviews-plugin'); ?>');
                $('#grp-creative-customizer-modal').remove();
            } else {
                alert('<?php esc_js_e('Failed to save customizations.', 'google-reviews-plugin'); ?>');
            }
        });
    });
    
    // Preview CSS for Creative
    $(document).off('click', '#grp-creative-customizer-modal .grp-preview-css-creative');
    $(document).on('click', '#grp-creative-customizer-modal .grp-preview-css-creative', function() {
        var style = $(this).siblings('.grp-save-creative-customizations').data('style');
        var css = generateCreativeCss(style);
        
        var previewModalHtml = '<div class="grp-modal-overlay" id="grp-css-preview-modal" style="display: flex;">' +
            '<div class="grp-modal-content" style="max-width: 900px; width: 90%; display: flex; flex-direction: column; max-height: 90vh;">' +
            '<div class="grp-modal-header">' +
            '<h2><?php esc_js_e('Generated CSS Preview', 'google-reviews-plugin'); ?></h2>' +
            '<button class="grp-modal-close" aria-label="<?php esc_attr_e('Close', 'google-reviews-plugin'); ?>">&times;</button>' +
            '</div>' +
            '<div class="grp-modal-body" style="flex: 1; min-height: 0; overflow-y: auto;">' +
            '<textarea readonly style="width: 100%; height: 400px; font-family: monospace; font-size: 12px; box-sizing: border-box; padding: 10px; border: 1px solid #ddd; border-radius: 4px; resize: none;">' + css + '</textarea>' +
            '</div>' +
            '<div class="grp-modal-footer" style="flex-shrink: 0;">' +
            '<button class="button grp-modal-close"><?php esc_js_e('Close', 'google-reviews-plugin'); ?></button>' +
            '</div>' +
            '</div>' +
            '</div>';
        
        $('body').append(previewModalHtml);
        
        $(document).off('click', '#grp-css-preview-modal .grp-modal-close, #grp-css-preview-modal');
        $(document).on('click', '#grp-css-preview-modal .grp-modal-close, #grp-css-preview-modal', function(e) {
            if ($(e.target).is('#grp-css-preview-modal') || $(e.target).hasClass('grp-modal-close')) {
                $('#grp-css-preview-modal').remove();
            }
        });
        
        $(document).off('click', '#grp-css-preview-modal .grp-modal-content');
        $(document).on('click', '#grp-css-preview-modal .grp-modal-content', function(e) {
            e.stopPropagation();
        });
    });
    
    // Close Creative customizer modal
    $(document).off('click', '#grp-creative-customizer-modal .grp-modal-close, #grp-creative-customizer-modal');
    $(document).on('click', '#grp-creative-customizer-modal .grp-modal-close, #grp-creative-customizer-modal', function(e) {
        if ($(e.target).is('#grp-creative-customizer-modal') || $(e.target).hasClass('grp-modal-close')) {
            $('#grp-creative-customizer-modal').remove();
        }
    });
    
    $(document).off('click', '#grp-creative-customizer-modal .grp-modal-content');
    $(document).on('click', '#grp-creative-customizer-modal .grp-modal-content', function(e) {
        e.stopPropagation();
    });
};
</script>

