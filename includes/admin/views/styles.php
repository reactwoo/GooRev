<?php
/**
 * Styles view
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap grp-styles-page">
    <h1><?php esc_html_e('Review Styles', 'google-reviews-plugin'); ?></h1>
    
    <div class="grp-styles-container">
        <div class="grp-styles-main">
            <div class="grp-styles-grid">
                <?php foreach ($available_styles as $key => $style): ?>
                    <div class="grp-style-card" data-style="<?php echo esc_attr($key); ?>">
                        <div class="grp-style-preview">
                            <div class="grp-preview-review grp-style-<?php echo esc_attr($key); ?>">
                                <div class="grp-review-rating">
                                    <span class="grp-star grp-star-full">★</span>
                                    <span class="grp-star grp-star-full">★</span>
                                    <span class="grp-star grp-star-full">★</span>
                                    <span class="grp-star grp-star-full">★</span>
                                    <span class="grp-star grp-star-full">★</span>
                                </div>
                                <div class="grp-review-text">
                                    <?php esc_html_e('This is a sample review to demonstrate the style. The review text will appear here with proper formatting and styling.', 'google-reviews-plugin'); ?>
                                </div>
                                <div class="grp-review-meta">
                                    <div class="grp-review-avatar">
                                        <img src="https://via.placeholder.com/40x40/007cba/ffffff?text=U" alt="User">
                                    </div>
                                    <div class="grp-review-author">
                                        <span class="grp-author-name"><?php esc_html_e('John Doe', 'google-reviews-plugin'); ?></span>
                                        <span class="grp-review-date"><?php echo date('M j, Y'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grp-style-info">
                            <h3><?php echo esc_html($style['name']); ?></h3>
                            <p><?php echo esc_html($style['description']); ?></p>
                            
                            <div class="grp-style-variants">
                                <h4><?php esc_html_e('Variants', 'google-reviews-plugin'); ?></h4>
                                <div class="grp-variant-buttons">
                                    <?php foreach ($style['variants'] as $variant): ?>
                                        <button class="grp-variant-btn <?php echo $variant === 'light' ? 'active' : ''; ?>" 
                                                data-variant="<?php echo esc_attr($variant); ?>">
                                            <?php echo esc_html(ucfirst($variant)); ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="grp-style-features">
                                <h4><?php esc_html_e('Features', 'google-reviews-plugin'); ?></h4>
                                <ul>
                                    <?php foreach ($style['features'] as $feature): ?>
                                        <li><?php echo esc_html(ucwords(str_replace('_', ' ', $feature))); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            
                            <div class="grp-style-actions">
                                <button class="button button-primary grp-use-style" 
                                        data-style="<?php echo esc_attr($key); ?>">
                                    <?php esc_html_e('Use This Style', 'google-reviews-plugin'); ?>
                                </button>
                                <button class="button grp-customize-style" 
                                        data-style="<?php echo esc_attr($key); ?>">
                                    <?php esc_html_e('Customize', 'google-reviews-plugin'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="grp-styles-sidebar">
            <!-- Custom CSS Editor -->
            <div class="grp-sidebar-card">
                <h3><?php esc_html_e('Custom CSS', 'google-reviews-plugin'); ?></h3>
                <p><?php esc_html_e('Add your own custom CSS to further customize the appearance of your reviews.', 'google-reviews-plugin'); ?></p>
                
                <textarea id="grp-custom-css" 
                          rows="10" 
                          placeholder="<?php esc_attr_e('/* Add your custom CSS here */', 'google-reviews-plugin'); ?>"><?php echo esc_textarea(get_option('grp_custom_css', '')); ?></textarea>
                
                <div class="grp-css-actions">
                    <button id="grp-save-css" class="button button-primary">
                        <?php esc_html_e('Save CSS', 'google-reviews-plugin'); ?>
                    </button>
                    <button id="grp-reset-css" class="button">
                        <?php esc_html_e('Reset', 'google-reviews-plugin'); ?>
                    </button>
                </div>
            </div>
            
            <!-- Style Settings -->
            <div class="grp-sidebar-card">
                <h3><?php esc_html_e('Style Settings', 'google-reviews-plugin'); ?></h3>
                
                <form id="grp-style-settings">
                    <div class="grp-form-group">
                        <label for="grp-default-style"><?php esc_html_e('Default Style', 'google-reviews-plugin'); ?></label>
                        <select id="grp-default-style" name="default_style">
                            <?php foreach ($available_styles as $key => $style): ?>
                                <option value="<?php echo esc_attr($key); ?>" 
                                        <?php selected(get_option('grp_default_style', 'modern'), $key); ?>>
                                    <?php echo esc_html($style['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="grp-form-group">
                        <label for="grp-enable-animations">
                            <input type="checkbox" id="grp-enable-animations" name="enable_animations" value="1" 
                                   <?php checked(get_option('grp_enable_animations', 1), 1); ?>>
                            <?php esc_html_e('Enable Animations', 'google-reviews-plugin'); ?>
                        </label>
                    </div>
                    
                    <div class="grp-form-group">
                        <label for="grp-enable-hover-effects">
                            <input type="checkbox" id="grp-enable-hover-effects" name="enable_hover_effects" value="1" 
                                   <?php checked(get_option('grp_enable_hover_effects', 1), 1); ?>>
                            <?php esc_html_e('Enable Hover Effects', 'google-reviews-plugin'); ?>
                        </label>
                    </div>
                    
                    <button type="submit" class="button button-primary">
                        <?php esc_html_e('Save Settings', 'google-reviews-plugin'); ?>
                    </button>
                </form>
            </div>
            
            <!-- Pro Features -->
            <?php if (!$is_pro): ?>
            <div class="grp-sidebar-card grp-pro-card">
                <h3><?php esc_html_e('Pro Features', 'google-reviews-plugin'); ?></h3>
                <p><?php esc_html_e('Unlock advanced styling options with Pro version.', 'google-reviews-plugin'); ?></p>
                <ul>
                    <li>✓ <?php esc_html_e('Custom Template Builder', 'google-reviews-plugin'); ?></li>
                    <li>✓ <?php esc_html_e('Advanced Color Controls', 'google-reviews-plugin'); ?></li>
                    <li>✓ <?php esc_html_e('Animation Library', 'google-reviews-plugin'); ?></li>
                    <li>✓ <?php esc_html_e('Style Presets', 'google-reviews-plugin'); ?></li>
                    <li>✓ <?php esc_html_e('White Label Options', 'google-reviews-plugin'); ?></li>
                </ul>
                <a href="https://reactwoo.com/google-reviews-plugin-pro/" class="button button-primary" target="_blank">
                    <?php esc_html_e('Upgrade to Pro', 'google-reviews-plugin'); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.grp-styles-page {
    max-width: 1400px;
}

.grp-styles-container {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 30px;
    margin-top: 20px;
}

.grp-styles-main {
    background: #fff;
}

.grp-styles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 30px;
}

.grp-style-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.grp-style-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.grp-style-preview {
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
}

.grp-preview-review {
    max-width: 300px;
    margin: 0 auto;
}

.grp-style-info {
    padding: 20px;
}

.grp-style-info h3 {
    margin-top: 0;
    color: #23282d;
    border-bottom: none;
    padding-bottom: 0;
}

.grp-style-info p {
    color: #666;
    margin-bottom: 20px;
}

.grp-style-variants {
    margin-bottom: 20px;
}

.grp-style-variants h4 {
    margin-bottom: 10px;
    color: #23282d;
    font-size: 14px;
}

.grp-variant-buttons {
    display: flex;
    gap: 5px;
}

.grp-variant-btn {
    padding: 5px 10px;
    border: 1px solid #ddd;
    background: #f9f9f9;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s ease;
}

.grp-variant-btn.active {
    background: #0073aa;
    color: white;
    border-color: #0073aa;
}

.grp-variant-btn:hover {
    background: #e1e1e1;
}

.grp-variant-btn.active:hover {
    background: #005a87;
}

.grp-style-features {
    margin-bottom: 20px;
}

.grp-style-features h4 {
    margin-bottom: 10px;
    color: #23282d;
    font-size: 14px;
}

.grp-style-features ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.grp-style-features li {
    padding: 3px 0;
    color: #666;
    font-size: 13px;
}

.grp-style-features li:before {
    content: '✓';
    color: #46b450;
    margin-right: 8px;
}

.grp-style-actions {
    display: flex;
    gap: 10px;
}

.grp-style-actions .button {
    flex: 1;
    text-align: center;
}

.grp-styles-sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.grp-sidebar-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
}

.grp-sidebar-card h3 {
    margin-top: 0;
    color: #23282d;
    border-bottom: none;
    padding-bottom: 0;
}

.grp-sidebar-card textarea {
    width: 100%;
    font-family: monospace;
    font-size: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    resize: vertical;
}

.grp-css-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.grp-css-actions .button {
    flex: 1;
}

.grp-form-group {
    margin-bottom: 15px;
}

.grp-form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #23282d;
}

.grp-form-group input[type="checkbox"] {
    margin-right: 8px;
}

.grp-form-group select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.grp-pro-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.grp-pro-card h3,
.grp-pro-card p,
.grp-pro-card li {
    color: white;
}

.grp-pro-card ul {
    list-style: none;
    padding: 0;
    margin: 15px 0;
}

.grp-pro-card li {
    padding: 5px 0;
}

@media (max-width: 1024px) {
    .grp-styles-container {
        grid-template-columns: 1fr;
    }
    
    .grp-styles-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .grp-style-actions {
        flex-direction: column;
    }
    
    .grp-css-actions {
        flex-direction: column;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Variant switching
    $('.grp-variant-btn').on('click', function() {
        var $btn = $(this);
        var $card = $btn.closest('.grp-style-card');
        var variant = $btn.data('variant');
        
        // Update active button
        $card.find('.grp-variant-btn').removeClass('active');
        $btn.addClass('active');
        
        // Update preview
        var $preview = $card.find('.grp-preview-review');
        $preview.removeClass('grp-light grp-dark').addClass('grp-' + variant);
    });
    
    // Use style
    $('.grp-use-style').on('click', function() {
        var style = $(this).data('style');
        
        $.post(ajaxurl, {
            action: 'grp_set_default_style',
            nonce: grp_admin_ajax.nonce,
            style: style
        }, function(response) {
            if (response.success) {
                alert('<?php esc_js('Default style updated!', 'google-reviews-plugin'); ?>');
            } else {
                alert('<?php esc_js('Failed to update style.', 'google-reviews-plugin'); ?>');
            }
        });
    });
    
    // Save custom CSS
    $('#grp-save-css').on('click', function() {
        var css = $('#grp-custom-css').val();
        
        $.post(ajaxurl, {
            action: 'grp_save_custom_css',
            nonce: grp_admin_ajax.nonce,
            css: css
        }, function(response) {
            if (response.success) {
                alert('<?php esc_js('Custom CSS saved!', 'google-reviews-plugin'); ?>');
            } else {
                alert('<?php esc_js('Failed to save CSS.', 'google-reviews-plugin'); ?>');
            }
        });
    });
    
    // Reset CSS
    $('#grp-reset-css').on('click', function() {
        if (confirm('<?php esc_js('Are you sure you want to reset the custom CSS?', 'google-reviews-plugin'); ?>')) {
            $('#grp-custom-css').val('');
        }
    });
    
    // Save style settings
    $('#grp-style-settings').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.post(ajaxurl, {
            action: 'grp_save_style_settings',
            nonce: grp_admin_ajax.nonce,
            data: formData
        }, function(response) {
            if (response.success) {
                alert('<?php esc_js('Settings saved!', 'google-reviews-plugin'); ?>');
            } else {
                alert('<?php esc_js('Failed to save settings.', 'google-reviews-plugin'); ?>');
            }
        });
    });
});
</script>