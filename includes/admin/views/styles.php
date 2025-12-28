<?php
/**
 * Styles view
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

// License class should be autoloaded, but ensure it exists
if (!class_exists('GRP_License')) {
    // Try to find and load it
    $license_file = GRP_PLUGIN_DIR . 'includes/class-grp-license.php';
    if (file_exists($license_file)) {
        require_once $license_file;
    }
}
?>

<div class="wrap grp-styles-page">
    <h1><?php esc_html_e('Review Styles', 'google-reviews-plugin'); ?></h1>
    
    <div class="grp-styles-container">
        <div class="grp-styles-main">
            <div class="grp-styles-grid">
                <?php foreach ($available_styles as $key => $style): 
                    // Determine preview structure based on style
                    $is_corporate = ($key === 'corporate');
                    $is_creative = ($key === 'creative');
                    $is_modern = ($key === 'modern');
                    $gradient = $is_creative ? ['blue', 'red', 'yellow', 'green', 'purple'][array_rand(['blue', 'red', 'yellow', 'green', 'purple'])] : '';
                ?>
                    <div class="grp-style-card" data-style="<?php echo esc_attr($key); ?>">
                        <div class="grp-style-preview grp-style-<?php echo esc_attr($key); ?> grp-theme-light">
                            <?php if ($is_corporate): ?>
                                <!-- Corporate style with header/footer -->
                                <div class="grp-review">
                                    <div class="grp-review-header">
                                        <span class="grp-review-header-text"><?php esc_html_e('Google Reviews', 'google-reviews-plugin'); ?></span>
                                        <svg class="grp-google-logo" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                        </svg>
                                    </div>
                                    <div class="grp-review-content">
                                        <div class="grp-review-rating" style="text-align: right;">
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
                                                <img src="https://i.pravatar.cc/40?img=12" alt="User" style="display: block; width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                            </div>
                                            <div class="grp-author-name"><?php esc_html_e('John Doe', 'google-reviews-plugin'); ?></div>
                                        </div>
                                    </div>
                                    <div class="grp-review-footer">
                                        <span class="grp-review-date"><?php echo date('M j, Y'); ?></span>
                                        <span class="grp-verified-badge"><?php esc_html_e('Verified', 'google-reviews-plugin'); ?></span>
                                    </div>
                                </div>
                            <?php elseif ($is_creative): ?>
                                <!-- Creative style with gradient and quote -->
                                <div class="grp-review" data-gradient="<?php echo esc_attr($gradient); ?>">
                                    <div class="grp-review-quote">"</div>
                                    <div class="grp-review-rating" style="text-align: center;">
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
                                            <img src="https://i.pravatar.cc/80?img=12" alt="User" style="display: block; width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                                        </div>
                                        <div class="grp-review-author">
                                            <span class="grp-author-name"><?php esc_html_e('John Doe', 'google-reviews-plugin'); ?></span>
                                            <span class="grp-review-date"><?php echo date('M j, Y'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif ($is_modern): ?>
                                <!-- Modern style with overlapping avatar -->
                                <div class="grp-review">
                                    <div class="grp-review-avatar">
                                        <img src="https://i.pravatar.cc/48?img=12" alt="User" style="display: block; width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                                    </div>
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
                                        <div class="grp-review-author">
                                            <span class="grp-author-name"><?php esc_html_e('John Doe', 'google-reviews-plugin'); ?></span>
                                            <span class="grp-review-date"><?php echo date('M j, Y'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Default structure for Minimal and Classic -->
                                <div class="grp-review">
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
                                            <img src="https://i.pravatar.cc/40?img=12" alt="User" style="display: block; width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                        </div>
                                        <div class="grp-review-author">
                                            <span class="grp-author-name"><?php esc_html_e('John Doe', 'google-reviews-plugin'); ?></span>
                                            <span class="grp-review-date"><?php echo date('M j, Y'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
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
                                <?php
                                $license = new GRP_License();
                                $is_pro = $license->is_pro();
                                if ($is_pro): ?>
                                    <button class="button grp-customize-style" 
                                            data-style="<?php echo esc_attr($key); ?>">
                                        <?php esc_html_e('Customize', 'google-reviews-plugin'); ?>
                                    </button>
                                <?php else: ?>
                                    <button class="button grp-customize-style grp-pro-feature" 
                                            data-style="<?php echo esc_attr($key); ?>"
                                            title="<?php esc_attr_e('Customize is a Pro feature. Upgrade to unlock advanced styling options.', 'google-reviews-plugin'); ?>">
                                        <?php esc_html_e('Customize', 'google-reviews-plugin'); ?>
                                        <span class="grp-pro-badge"><?php esc_html_e('Pro', 'google-reviews-plugin'); ?></span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="grp-styles-sidebar">
            <!-- Custom CSS Editor (Pro/Enterprise only) -->
            <?php
            $license = new GRP_License();
            $is_pro = $license->is_pro();
            if ($is_pro): ?>
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
            <?php else: ?>
            <div class="grp-sidebar-card grp-pro-card">
                <h3><?php esc_html_e('Custom CSS Editor', 'google-reviews-plugin'); ?> <span class="grp-pro-badge-inline"><?php esc_html_e('Pro', 'google-reviews-plugin'); ?></span></h3>
                <p><?php esc_html_e('Unlock advanced customization options with Pro:', 'google-reviews-plugin'); ?></p>
                <ul>
                    <li>✓ <?php esc_html_e('Custom CSS Editor', 'google-reviews-plugin'); ?></li>
                    <li>✓ <?php esc_html_e('Visual Style Customizer', 'google-reviews-plugin'); ?></li>
                    <li>✓ <?php esc_html_e('Advanced Typography Controls', 'google-reviews-plugin'); ?></li>
                    <li>✓ <?php esc_html_e('Color Overrides', 'google-reviews-plugin'); ?></li>
                </ul>
                <a href="https://reactwoo.com/google-reviews-plugin-pro/" class="button button-primary" target="_blank">
                    <?php esc_html_e('Upgrade to Pro', 'google-reviews-plugin'); ?>
                </a>
            </div>
            <?php endif; ?>
            
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

.grp-style-preview .grp-review-avatar img {
    display: block !important;
    width: 40px !important;
    height: 40px !important;
    border-radius: 50% !important;
    object-fit: cover !important;
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

/* Pro badge styles */
.grp-pro-badge {
    display: inline-block;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-size: 10px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 3px;
    margin-left: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    vertical-align: middle;
}

.grp-pro-badge-inline {
    display: inline-block;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-size: 11px;
    font-weight: bold;
    padding: 3px 8px;
    border-radius: 3px;
    margin-left: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    vertical-align: middle;
}

.grp-customize-style.grp-pro-feature {
    position: relative;
    opacity: 0.9;
}

.grp-customize-style.grp-pro-feature:hover {
    opacity: 1;
}

/* Upgrade modal specific styles */
.grp-upgrade-modal-content {
    max-width: 600px;
}

.grp-upgrade-features {
    margin: 20px 0;
}

.grp-upgrade-features h3 {
    font-size: 16px;
    margin-bottom: 15px;
    color: #333;
}

.grp-upgrade-features ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.grp-upgrade-features li {
    padding: 8px 0;
    font-size: 14px;
    color: #555;
    border-bottom: 1px solid #eee;
}

.grp-upgrade-features li:last-child {
    border-bottom: none;
}

.grp-upgrade-features li:before {
    content: '✓';
    color: #46b450;
    font-weight: bold;
    margin-right: 10px;
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

/* Usage Modal Styles */
.grp-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 100000;
    overflow-y: auto;
}

.grp-modal-content {
    background: #fff;
    margin: 50px auto;
    max-width: 700px;
    border-radius: 4px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.grp-modal-header {
    padding: 20px 25px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.grp-modal-header h2 {
    margin: 0;
    font-size: 20px;
}

.grp-modal-close {
    background: none;
    border: none;
    font-size: 28px;
    line-height: 1;
    cursor: pointer;
    color: #666;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.grp-modal-close:hover {
    color: #000;
}

.grp-modal-body {
    padding: 25px;
}

.grp-modal-intro {
    margin-bottom: 20px;
    font-size: 14px;
    color: #555;
}

.grp-usage-tabs {
    display: flex;
    border-bottom: 2px solid #ddd;
    margin-bottom: 20px;
}

.grp-tab-btn {
    background: none;
    border: none;
    padding: 12px 20px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    color: #666;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    transition: all 0.2s;
}

.grp-tab-btn:hover {
    color: #2271b1;
}

.grp-tab-btn.active {
    color: #2271b1;
    border-bottom-color: #2271b1;
}

.grp-tab-content {
    display: none;
}

.grp-tab-content.active {
    display: block;
}

.grp-tab-content h3 {
    margin-top: 0;
    font-size: 16px;
}

.grp-tab-content ol {
    margin-left: 20px;
}

.grp-tab-content li {
    margin-bottom: 10px;
    line-height: 1.6;
}

.grp-code-block {
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin: 15px 0;
    position: relative;
}

.grp-code-block code {
    display: block;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    color: #333;
    word-break: break-all;
    margin-bottom: 10px;
}

.grp-code-block.grp-code-example {
    background: #fff8e1;
    border-color: #ffc107;
}

.grp-code-block .grp-copy-shortcode {
    margin-top: 10px;
}

.grp-code-block .grp-copy-shortcode.copied {
    background: #46b450;
}

.grp-tab-content .description {
    font-size: 13px;
    color: #666;
    font-style: italic;
    margin-top: -5px;
}

.grp-modal-footer {
    padding: 15px 25px;
    border-top: 1px solid #ddd;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.grp-modal-footer .button {
    margin: 0;
    padding: 8px 16px;
    white-space: nowrap;
    min-width: auto;
}

/* Customizer Modal Styles */
.grp-customizer-modal-content {
    max-width: 700px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}

.grp-customizer-body {
    overflow-y: auto;
    flex: 1;
    max-height: calc(90vh - 150px);
}

.grp-customizer-tabs {
    display: flex;
    border-bottom: 2px solid #ddd;
    margin-bottom: 20px;
}

.grp-customizer-tab-btn {
    background: none;
    border: none;
    padding: 12px 20px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    color: #666;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    transition: all 0.2s;
}

.grp-customizer-tab-btn:hover {
    color: #2271b1;
}

.grp-customizer-tab-btn.active {
    color: #2271b1;
    border-bottom-color: #2271b1;
}

.grp-customizer-tab-content {
    display: none;
}

.grp-customizer-tab-content.active {
    display: block;
}

.grp-customizer-tab-content h3 {
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 16px;
    color: #333;
}

.grp-customizer-control {
    margin-bottom: 25px;
}

.grp-customizer-control label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #555;
    font-size: 14px;
}

.grp-customizer-control .grp-value-display {
    font-weight: normal;
    color: #2271b1;
    margin-left: 8px;
}

.grp-color-control {
    display: flex;
    gap: 10px;
    align-items: center;
}

.grp-color-control .grp-color-input {
    width: 60px;
    height: 40px;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    padding: 2px;
}

.grp-color-control .grp-color-text {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: monospace;
    font-size: 13px;
}

.grp-font-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    background: white;
}

.grp-range-input {
    width: 100%;
    height: 6px;
    border-radius: 3px;
    background: #ddd;
    outline: none;
    -webkit-appearance: none;
}

.grp-range-input::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #2271b1;
    cursor: pointer;
}

.grp-range-input::-moz-range-thumb {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #2271b1;
    cursor: pointer;
    border: none;
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
        
        // Update preview with theme class
        var $preview = $card.find('.grp-style-preview');
        $preview.removeClass('grp-theme-light grp-theme-dark grp-theme-auto').addClass('grp-theme-' + variant);
        
        // For creative style, add random gradient data attribute
        if ($preview.hasClass('grp-style-creative')) {
            var gradients = ['blue', 'red', 'yellow', 'green', 'purple'];
            var randomGradient = gradients[Math.floor(Math.random() * gradients.length)];
            $preview.find('.grp-review').attr('data-gradient', randomGradient);
        }
    });
    
    // Use style - show usage modal
    $('.grp-use-style').on('click', function(e) {
        e.preventDefault();
        var style = $(this).data('style');
        var styleName = $(this).closest('.grp-style-card').find('h3').text();
        var shortcode = '[google_reviews style="' + style + '"]';
        
        // Create and show modal
        var modalHtml = '<div class="grp-modal-overlay" id="grp-use-style-modal">' +
            '<div class="grp-modal-content">' +
            '<div class="grp-modal-header">' +
            '<h2><?php esc_js_e('How to Use This Style', 'google-reviews-plugin'); ?></h2>' +
            '<button class="grp-modal-close" aria-label="<?php esc_attr_e('Close', 'google-reviews-plugin'); ?>">&times;</button>' +
            '</div>' +
            '<div class="grp-modal-body">' +
            '<p class="grp-modal-intro"><?php esc_js_e('You can use the', 'google-reviews-plugin'); ?> <strong>' + styleName + '</strong> <?php esc_js_e('style in several ways:', 'google-reviews-plugin'); ?></p>' +
            
            '<div class="grp-usage-tabs">' +
            '<button class="grp-tab-btn active" data-tab="shortcode"><?php esc_js_e('Shortcode', 'google-reviews-plugin'); ?></button>' +
            '<button class="grp-tab-btn" data-tab="gutenberg"><?php esc_js_e('Gutenberg', 'google-reviews-plugin'); ?></button>' +
            '<button class="grp-tab-btn" data-tab="elementor"><?php esc_js_e('Elementor', 'google-reviews-plugin'); ?></button>' +
            '</div>' +
            
            '<div class="grp-tab-content active" data-tab="shortcode">' +
            '<h3><?php esc_js_e('Using Shortcode', 'google-reviews-plugin'); ?></h3>' +
            '<p><?php esc_js_e('Copy and paste this shortcode anywhere on your site:', 'google-reviews-plugin'); ?></p>' +
            '<div class="grp-code-block">' +
            '<code id="grp-shortcode-text">' + shortcode + '</code>' +
            '<button class="button button-primary grp-copy-shortcode" data-copy="' + shortcode + '"><?php esc_js_e('Copy Shortcode', 'google-reviews-plugin'); ?></button>' +
            '</div>' +
            '<p class="description"><?php esc_js_e('You can also add attributes like count, layout, theme, etc. Example:', 'google-reviews-plugin'); ?></p>' +
            '<div class="grp-code-block grp-code-example">' +
            '<code>' + shortcode.replace('"]', '" count="5" layout="carousel" theme="light"]') + '</code>' +
            '</div>' +
            '</div>' +
            
            '<div class="grp-tab-content" data-tab="gutenberg">' +
            '<h3><?php esc_js_e('Using Gutenberg Block', 'google-reviews-plugin'); ?></h3>' +
            '<ol>' +
            '<li><?php esc_js_e('Go to any page or post editor', 'google-reviews-plugin'); ?></li>' +
            '<li><?php esc_js_e('Click the + button to add a new block', 'google-reviews-plugin'); ?></li>' +
            '<li><?php esc_js_e('Search for "Google Reviews" and select the block', 'google-reviews-plugin'); ?></li>' +
            '<li><?php esc_js_e('In the block settings sidebar, select', 'google-reviews-plugin'); ?> <strong>' + styleName + '</strong> <?php esc_js_e('from the Style dropdown', 'google-reviews-plugin'); ?></li>' +
            '<li><?php esc_js_e('Customize other options as needed (layout, count, theme, etc.)', 'google-reviews-plugin'); ?></li>' +
            '</ol>' +
            '</div>' +
            
            '<div class="grp-tab-content" data-tab="elementor">' +
            '<h3><?php esc_js_e('Using Elementor Widget', 'google-reviews-plugin'); ?></h3>' +
            '<ol>' +
            '<li><?php esc_js_e('Edit a page with Elementor', 'google-reviews-plugin'); ?></li>' +
            '<li><?php esc_js_e('Search for "Google Reviews" in the widget panel', 'google-reviews-plugin'); ?></li>' +
            '<li><?php esc_js_e('Drag the Google Reviews widget to your page', 'google-reviews-plugin'); ?></li>' +
            '<li><?php esc_js_e('In the Content tab, select', 'google-reviews-plugin'); ?> <strong>' + styleName + '</strong> <?php esc_js_e('from the Style dropdown', 'google-reviews-plugin'); ?></li>' +
            '<li><?php esc_js_e('Customize layout, colors, fonts, and other options in the Style tab', 'google-reviews-plugin'); ?></li>' +
            '</ol>' +
            '</div>' +
            
            '</div>' +
            '<div class="grp-modal-footer">' +
            '<button class="button button-primary grp-modal-close"><?php esc_js_e('Got it!', 'google-reviews-plugin'); ?></button>' +
            '</div>' +
            '</div>' +
            '</div>';
        
        $('body').append(modalHtml);
        $('#grp-use-style-modal').show();
        
        // Close modal handlers (use delegated events since modal is dynamically added)
        $(document).off('click', '#grp-use-style-modal .grp-modal-close');
        $(document).on('click', '#grp-use-style-modal .grp-modal-close', function(e) {
            e.preventDefault();
            $('#grp-use-style-modal').hide().remove();
        });
        
        // Close on overlay click (but not on modal content)
        $(document).off('click', '#grp-use-style-modal');
        $(document).on('click', '#grp-use-style-modal', function(e) {
            if ($(e.target).is('#grp-use-style-modal')) {
                $('#grp-use-style-modal').fadeOut(200, function() {
                    $(this).remove();
                });
            }
        });
        
        // Prevent modal content clicks from closing
        $(document).off('click', '#grp-use-style-modal .grp-modal-content');
        $(document).on('click', '#grp-use-style-modal .grp-modal-content', function(e) {
            e.stopPropagation();
        });
        
        // Tab switching (delegated)
        $(document).off('click', '#grp-use-style-modal .grp-tab-btn');
        $(document).on('click', '#grp-use-style-modal .grp-tab-btn', function() {
            var tab = $(this).data('tab');
            $('#grp-use-style-modal .grp-tab-btn').removeClass('active');
            $(this).addClass('active');
            $('#grp-use-style-modal .grp-tab-content').removeClass('active');
            $('#grp-use-style-modal .grp-tab-content[data-tab="' + tab + '"]').addClass('active');
        });
        
        // Copy shortcode to clipboard (delegated)
        $(document).off('click', '#grp-use-style-modal .grp-copy-shortcode');
        $(document).on('click', '#grp-use-style-modal .grp-copy-shortcode', function() {
            var shortcode = $(this).data('copy');
            var $button = $(this);
            var originalText = $button.text();
            
            // Create temporary textarea for copying
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(shortcode).select();
            
            try {
                document.execCommand('copy');
                $button.text('<?php esc_js_e('Copied!', 'google-reviews-plugin'); ?>').addClass('copied');
                setTimeout(function() {
                    $button.text(originalText).removeClass('copied');
                }, 2000);
            } catch (err) {
                alert('<?php esc_js_e('Failed to copy. Please copy manually.', 'google-reviews-plugin'); ?>');
            }
            
            $temp.remove();
        });
    });
    
    // Customize button for Pro users - open visual style customizer modal
    $('.grp-customize-style:not(.grp-pro-feature)').on('click', function(e) {
        e.preventDefault();
        var style = $(this).data('style');
        var styleName = $(this).closest('.grp-style-card').find('h3').text();
        
        // Load existing custom CSS values if any
        var existingCss = $('#grp-custom-css').val() || '';
        
        // Create customizer modal
        var customizerModalHtml = '<div class="grp-modal-overlay" id="grp-customizer-modal">' +
            '<div class="grp-modal-content grp-customizer-modal-content">' +
            '<div class="grp-modal-header">' +
            '<h2><?php esc_js_e('Customize', 'google-reviews-plugin'); ?> ' + styleName + '</h2>' +
            '<button class="grp-modal-close" aria-label="<?php esc_attr_e('Close', 'google-reviews-plugin'); ?>">&times;</button>' +
            '</div>' +
            '<div class="grp-modal-body grp-customizer-body">' +
            '<div class="grp-customizer-tabs">' +
            '<button class="grp-customizer-tab-btn active" data-tab="colors"><?php esc_js_e('Colors', 'google-reviews-plugin'); ?></button>' +
            '<button class="grp-customizer-tab-btn" data-tab="typography"><?php esc_js_e('Typography', 'google-reviews-plugin'); ?></button>' +
            '<button class="grp-customizer-tab-btn" data-tab="spacing"><?php esc_js_e('Spacing', 'google-reviews-plugin'); ?></button>' +
            '</div>' +
            
            // Colors Tab
            '<div class="grp-customizer-tab-content active" data-tab="colors">' +
            '<h3><?php esc_js_e('Color Settings', 'google-reviews-plugin'); ?></h3>' +
            '<div class="grp-customizer-control">' +
            '<label><?php esc_js_e('Text Color', 'google-reviews-plugin'); ?></label>' +
            '<div class="grp-color-control">' +
            '<input type="color" id="grp-custom-text-color" class="grp-color-input" value="#111827">' +
            '<input type="text" class="grp-color-text" value="#111827" placeholder="#111827">' +
            '</div>' +
            '</div>' +
            '<div class="grp-customizer-control">' +
            '<label><?php esc_js_e('Background Color', 'google-reviews-plugin'); ?></label>' +
            '<div class="grp-color-control">' +
            '<input type="color" id="grp-custom-bg-color" class="grp-color-input" value="#FFFFFF">' +
            '<input type="text" class="grp-color-text" value="#FFFFFF" placeholder="#FFFFFF">' +
            '</div>' +
            '</div>' +
            '<div class="grp-customizer-control">' +
            '<label><?php esc_js_e('Star Color', 'google-reviews-plugin'); ?></label>' +
            '<div class="grp-color-control">' +
            '<input type="color" id="grp-custom-star-color" class="grp-color-input" value="#FBBC05">' +
            '<input type="text" class="grp-color-text" value="#FBBC05" placeholder="#FBBC05">' +
            '</div>' +
            '</div>' +
            '<div class="grp-customizer-control">' +
            '<label><?php esc_js_e('Accent Color', 'google-reviews-plugin'); ?></label>' +
            '<div class="grp-color-control">' +
            '<input type="color" id="grp-custom-accent-color" class="grp-color-input" value="#4285F4">' +
            '<input type="text" class="grp-color-text" value="#4285F4" placeholder="#4285F4">' +
            '</div>' +
            '</div>' +
            '<div class="grp-customizer-control">' +
            '<label><?php esc_js_e('Border Color', 'google-reviews-plugin'); ?></label>' +
            '<div class="grp-color-control">' +
            '<input type="color" id="grp-custom-border-color" class="grp-color-input" value="#D1D5DB">' +
            '<input type="text" class="grp-color-text" value="#D1D5DB" placeholder="#D1D5DB">' +
            '</div>' +
            '</div>' +
            '</div>' +
            
            // Typography Tab
            '<div class="grp-customizer-tab-content" data-tab="typography">' +
            '<h3><?php esc_js_e('Typography Settings', 'google-reviews-plugin'); ?></h3>' +
            '<div class="grp-customizer-control">' +
            '<label><?php esc_js_e('Body Font Family', 'google-reviews-plugin'); ?></label>' +
            '<select id="grp-custom-body-font" class="grp-font-select">' +
            '<option value=""><?php esc_js_e('Inherit (Theme Font)', 'google-reviews-plugin'); ?></option>' +
            '<option value="Inter, sans-serif">Inter</option>' +
            '<option value="Roboto, sans-serif">Roboto</option>' +
            '<option value="Open Sans, sans-serif">Open Sans</option>' +
            '<option value="Lato, sans-serif">Lato</option>' +
            '<option value="Montserrat, sans-serif">Montserrat</option>' +
            '<option value="Poppins, sans-serif">Poppins</option>' +
            '<option value="Playfair Display, serif">Playfair Display</option>' +
            '<option value="Georgia, serif">Georgia</option>' +
            '<option value="Merriweather, serif">Merriweather</option>' +
            '</select>' +
            '</div>' +
            '<div class="grp-customizer-control">' +
            '<label><?php esc_js_e('Name Font Family', 'google-reviews-plugin'); ?></label>' +
            '<select id="grp-custom-name-font" class="grp-font-select">' +
            '<option value=""><?php esc_js_e('Inherit (Theme Font)', 'google-reviews-plugin'); ?></option>' +
            '<option value="Inter, sans-serif">Inter</option>' +
            '<option value="Roboto, sans-serif">Roboto</option>' +
            '<option value="Open Sans, sans-serif">Open Sans</option>' +
            '<option value="Playfair Display, serif">Playfair Display</option>' +
            '<option value="Georgia, serif">Georgia</option>' +
            '<option value="Merriweather, serif">Merriweather</option>' +
            '</select>' +
            '</div>' +
            '<div class="grp-customizer-control">' +
            '<label><?php esc_js_e('Body Font Size', 'google-reviews-plugin'); ?> <span class="grp-value-display" id="grp-body-font-size-value">15px</span></label>' +
            '<input type="range" id="grp-custom-body-font-size" class="grp-range-input" min="10" max="24" value="15" step="1">' +
            '</div>' +
            '<div class="grp-customizer-control">' +
            '<label><?php esc_js_e('Name Font Size', 'google-reviews-plugin'); ?> <span class="grp-value-display" id="grp-name-font-size-value">14px</span></label>' +
            '<input type="range" id="grp-custom-name-font-size" class="grp-range-input" min="10" max="20" value="14" step="1">' +
            '</div>' +
            '</div>' +
            
            // Spacing Tab
            '<div class="grp-customizer-tab-content" data-tab="spacing">' +
            '<h3><?php esc_js_e('Spacing Settings', 'google-reviews-plugin'); ?></h3>' +
            '<div class="grp-customizer-control">' +
            '<label><?php esc_js_e('Border Radius', 'google-reviews-plugin'); ?> <span class="grp-value-display" id="grp-border-radius-value">8px</span></label>' +
            '<input type="range" id="grp-custom-border-radius" class="grp-range-input" min="0" max="30" value="8" step="1">' +
            '</div>' +
            '<div class="grp-customizer-control">' +
            '<label><?php esc_js_e('Padding', 'google-reviews-plugin'); ?> <span class="grp-value-display" id="grp-padding-value">20px</span></label>' +
            '<input type="range" id="grp-custom-padding" class="grp-range-input" min="10" max="50" value="20" step="2">' +
            '</div>' +
            '</div>' +
            
            '</div>' +
            '<div class="grp-modal-footer">' +
            '<button class="button grp-preview-css"><?php esc_js_e('Preview CSS', 'google-reviews-plugin'); ?></button>' +
            '<button class="button button-primary grp-save-customizations" data-style="' + style + '"><?php esc_js_e('Save Customizations', 'google-reviews-plugin'); ?></button>' +
            '<button class="button grp-modal-close"><?php esc_js_e('Cancel', 'google-reviews-plugin'); ?></button>' +
            '</div>' +
            '</div>' +
            '</div>';
        
        $('body').append(customizerModalHtml);
        $('#grp-customizer-modal').css('display', 'flex');
        
        // Function to generate CSS from form values (must be defined before use)
        function generateCustomCss(style) {
            var css = '/* Custom styles for ' + styleName + ' style - Generated on ' + new Date().toLocaleDateString() + ' */\n\n';
            css += '.grp-style-' + style + ' .grp-review {\n';
            
            var bgColor = $('#grp-custom-bg-color').val();
            if (bgColor && bgColor !== '#FFFFFF') {
                css += '    background-color: ' + bgColor + ' !important;\n';
            }
            
            var borderColor = $('#grp-custom-border-color').val();
            if (borderColor && borderColor !== '#D1D5DB') {
                css += '    border-color: ' + borderColor + ' !important;\n';
            }
            
            var borderRadius = $('#grp-custom-border-radius').val();
            if (borderRadius && borderRadius !== '8') {
                css += '    border-radius: ' + borderRadius + 'px !important;\n';
            }
            
            var padding = $('#grp-custom-padding').val();
            if (padding && padding !== '20') {
                css += '    padding: ' + padding + 'px !important;\n';
            }
            
            css += '}\n\n';
            
            css += '.grp-style-' + style + ' .grp-review-text,\n';
            css += '.grp-style-' + style + ' .grp-author-name {\n';
            var textColor = $('#grp-custom-text-color').val();
            if (textColor && textColor !== '#111827') {
                css += '    color: ' + textColor + ' !important;\n';
            }
            var bodyFont = $('#grp-custom-body-font').val();
            if (bodyFont) {
                css += '    font-family: ' + bodyFont + ' !important;\n';
            }
            var bodyFontSize = $('#grp-custom-body-font-size').val();
            if (bodyFontSize && bodyFontSize !== '15') {
                css += '    font-size: ' + bodyFontSize + 'px !important;\n';
            }
            css += '}\n\n';
            
            css += '.grp-style-' + style + ' .grp-author-name {\n';
            var nameFont = $('#grp-custom-name-font').val();
            if (nameFont) {
                css += '    font-family: ' + nameFont + ' !important;\n';
            }
            var nameFontSize = $('#grp-custom-name-font-size').val();
            if (nameFontSize && nameFontSize !== '14') {
                css += '    font-size: ' + nameFontSize + 'px !important;\n';
            }
            css += '}\n\n';
            
            css += '.grp-style-' + style + ' .grp-star {\n';
            var starColor = $('#grp-custom-star-color').val();
            if (starColor && starColor !== '#FBBC05') {
                css += '    color: ' + starColor + ' !important;\n';
            }
            css += '}\n\n';
            
            var accentColor = $('#grp-custom-accent-color').val();
            if (accentColor && accentColor !== '#4285F4') {
                css += '.grp-style-' + style + ' .grp-review {\n';
                css += '    --grp-accent: ' + accentColor + ';\n';
                css += '}\n';
            }
            
            return css;
        }
        
        // Color input sync
        $(document).off('input change', '#grp-customizer-modal .grp-color-input');
        $(document).on('input change', '#grp-customizer-modal .grp-color-input', function() {
            $(this).siblings('.grp-color-text').val($(this).val());
        });
        
        $(document).off('input change', '#grp-customizer-modal .grp-color-text');
        $(document).on('input change', '#grp-customizer-modal .grp-color-text', function() {
            var val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $(this).siblings('.grp-color-input').val(val);
            }
        });
        
        // Range input value display
        $(document).off('input', '#grp-customizer-modal .grp-range-input');
        $(document).on('input', '#grp-customizer-modal .grp-range-input', function() {
            var value = $(this).val();
            var unit = $(this).attr('id').includes('font-size') ? 'px' : 'px';
            $(this).closest('.grp-customizer-control').find('.grp-value-display').text(value + unit);
        });
        
        // Tab switching
        $(document).off('click', '#grp-customizer-modal .grp-customizer-tab-btn');
        $(document).on('click', '#grp-customizer-modal .grp-customizer-tab-btn', function() {
            var tab = $(this).data('tab');
            $('#grp-customizer-modal .grp-customizer-tab-btn').removeClass('active');
            $(this).addClass('active');
            $('#grp-customizer-modal .grp-customizer-tab-content').removeClass('active');
            $('#grp-customizer-modal .grp-customizer-tab-content[data-tab="' + tab + '"]').addClass('active');
        });
        
        // Generate and save CSS
        $(document).off('click', '#grp-customizer-modal .grp-save-customizations');
        $(document).on('click', '#grp-customizer-modal .grp-save-customizations', function() {
            var style = $(this).data('style');
            var css = generateCustomCss(style);
            
            $.post(ajaxurl, {
                action: 'grp_save_custom_css',
                nonce: grp_admin.nonce,
                css: css
            }, function(response) {
                if (response.success) {
                    // Update the CSS textarea if it exists
                    if ($('#grp-custom-css').length) {
                        $('#grp-custom-css').val(css);
                    }
                    alert('<?php esc_js_e('Customizations saved successfully!', 'google-reviews-plugin'); ?>');
                    $('#grp-customizer-modal').hide().remove();
                } else {
                    alert('<?php esc_js_e('Failed to save customizations.', 'google-reviews-plugin'); ?>');
                }
            });
        });
        
        // Preview CSS
        $(document).off('click', '#grp-customizer-modal .grp-preview-css');
        $(document).on('click', '#grp-customizer-modal .grp-preview-css', function() {
            var style = $(this).siblings('.grp-save-customizations').data('style');
            var css = generateCustomCss(style);
            
            var previewModalHtml = '<div class="grp-modal-overlay" id="grp-css-preview-modal">' +
                '<div class="grp-modal-content" style="max-width: 800px;">' +
                '<div class="grp-modal-header">' +
                '<h2><?php esc_js_e('Generated CSS Preview', 'google-reviews-plugin'); ?></h2>' +
                '<button class="grp-modal-close" aria-label="<?php esc_attr_e('Close', 'google-reviews-plugin'); ?>">&times;</button>' +
                '</div>' +
                '<div class="grp-modal-body">' +
                '<textarea readonly style="width: 100%; height: 400px; font-family: monospace; font-size: 12px;">' + css + '</textarea>' +
                '</div>' +
                '<div class="grp-modal-footer">' +
                '<button class="button grp-modal-close"><?php esc_js_e('Close', 'google-reviews-plugin'); ?></button>' +
                '</div>' +
                '</div>' +
                '</div>';
            
            $('body').append(previewModalHtml);
            $('#grp-css-preview-modal').show();
            
            // Close preview modal
            $(document).off('click', '#grp-css-preview-modal .grp-modal-close, #grp-css-preview-modal');
            $(document).on('click', '#grp-css-preview-modal .grp-modal-close, #grp-css-preview-modal', function(e) {
                if ($(e.target).is('#grp-css-preview-modal') || $(e.target).hasClass('grp-modal-close')) {
                    $('#grp-css-preview-modal').hide().remove();
                }
            });
        });
        
        // Close modal
        $(document).off('click', '#grp-customizer-modal .grp-modal-close, #grp-customizer-modal');
        $(document).on('click', '#grp-customizer-modal .grp-modal-close, #grp-customizer-modal', function(e) {
            if ($(e.target).is('#grp-customizer-modal') || $(e.target).hasClass('grp-modal-close')) {
                $('#grp-customizer-modal').hide().remove();
            }
        });
        
        $(document).off('click', '#grp-customizer-modal .grp-modal-content');
        $(document).on('click', '#grp-customizer-modal .grp-modal-content', function(e) {
            e.stopPropagation();
        });
    });
    
    // Customize button - show Pro upgrade modal for free users
    $('.grp-customize-style.grp-pro-feature').on('click', function(e) {
        e.preventDefault();
        var style = $(this).data('style');
        var styleName = $(this).closest('.grp-style-card').find('h3').text();
        
        var upgradeModalHtml = '<div class="grp-modal-overlay" id="grp-upgrade-modal">' +
            '<div class="grp-modal-content grp-upgrade-modal-content">' +
            '<div class="grp-modal-header">' +
            '<h2><?php esc_js_e('Unlock Style Customization', 'google-reviews-plugin'); ?></h2>' +
            '<button class="grp-modal-close" aria-label="<?php esc_attr_e('Close', 'google-reviews-plugin'); ?>">&times;</button>' +
            '</div>' +
            '<div class="grp-modal-body">' +
            '<p class="grp-modal-intro"><?php esc_js_e('The', 'google-reviews-plugin'); ?> <strong>' + styleName + '</strong> <?php esc_js_e('style customization is a Pro feature.', 'google-reviews-plugin'); ?></p>' +
            '<div class="grp-upgrade-features">' +
            '<h3><?php esc_js_e('With Pro, you get:', 'google-reviews-plugin'); ?></h3>' +
            '<ul>' +
            '<li>✓ <?php esc_js_e('Visual Style Customizer with live preview', 'google-reviews-plugin'); ?></li>' +
            '<li>✓ <?php esc_js_e('Custom CSS Editor', 'google-reviews-plugin'); ?></li>' +
            '<li>✓ <?php esc_js_e('Color Overrides (text, background, accent, stars)', 'google-reviews-plugin'); ?></li>' +
            '<li>✓ <?php esc_js_e('Typography Controls (font sizes, font families)', 'google-reviews-plugin'); ?></li>' +
            '<li>✓ <?php esc_js_e('Advanced Spacing & Layout Controls', 'google-reviews-plugin'); ?></li>' +
            '<li>✓ <?php esc_js_e('Multiple Locations Support', 'google-reviews-plugin'); ?></li>' +
            '<li>✓ <?php esc_js_e('Priority Support', 'google-reviews-plugin'); ?></li>' +
            '</ul>' +
            '</div>' +
            '</div>' +
            '<div class="grp-modal-footer">' +
            '<a href="https://reactwoo.com/google-reviews-plugin-pro/" class="button button-primary" target="_blank"><?php esc_js_e('Upgrade to Pro', 'google-reviews-plugin'); ?></a>' +
            '<button class="button grp-modal-close"><?php esc_js_e('Maybe Later', 'google-reviews-plugin'); ?></button>' +
            '</div>' +
            '</div>' +
            '</div>';
        
        $('body').append(upgradeModalHtml);
        $('#grp-upgrade-modal').show();
        
        // Close modal handlers
        $(document).off('click', '#grp-upgrade-modal .grp-modal-close');
        $(document).on('click', '#grp-upgrade-modal .grp-modal-close', function(e) {
            e.preventDefault();
            $('#grp-upgrade-modal').hide().remove();
        });
        
        $(document).off('click', '#grp-upgrade-modal');
        $(document).on('click', '#grp-upgrade-modal', function(e) {
            if ($(e.target).is('#grp-upgrade-modal')) {
                $('#grp-upgrade-modal').fadeOut(200, function() {
                    $(this).remove();
                });
            }
        });
        
        $(document).off('click', '#grp-upgrade-modal .grp-modal-content');
        $(document).on('click', '#grp-upgrade-modal .grp-modal-content', function(e) {
            e.stopPropagation();
        });
    });
    
    // Save custom CSS
    $('#grp-save-css').on('click', function() {
        var css = $('#grp-custom-css').val();
        
        $.post(ajaxurl, {
            action: 'grp_save_custom_css',
            nonce: '<?php echo wp_create_nonce('grp_admin_nonce'); ?>',
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
            nonce: grp_admin.nonce,
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