<?php
/**
 * Review Widgets Settings Page
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['grp_widgets_submit']) && check_admin_referer('grp_widgets_settings')) {
    // Save widget settings
    update_option('grp_widget_button_default_text', sanitize_text_field($_POST['grp_widget_button_default_text']));
    update_option('grp_widget_button_default_style', sanitize_text_field($_POST['grp_widget_button_default_style']));
    update_option('grp_widget_button_default_size', sanitize_text_field($_POST['grp_widget_button_default_size']));
    
    // For colors, use the text input value (which can be empty), not the color picker value
    $button_color = isset($_POST['grp_widget_button_default_color_text']) ? sanitize_text_field($_POST['grp_widget_button_default_color_text']) : '';
    $button_bg_color = isset($_POST['grp_widget_button_default_bg_color_text']) ? sanitize_text_field($_POST['grp_widget_button_default_bg_color_text']) : '';
    
    // Only save if not empty and valid hex color
    if (!empty($button_color) && !preg_match('/^#[0-9A-F]{6}$/i', $button_color)) {
        $button_color = '';
    }
    if (!empty($button_bg_color) && !preg_match('/^#[0-9A-F]{6}$/i', $button_bg_color)) {
        $button_bg_color = '';
    }
    
    update_option('grp_widget_button_default_color', $button_color);
    update_option('grp_widget_button_default_bg_color', $button_bg_color);
    
    $star_color = isset($_POST['grp_widget_template_star_color_text']) ? sanitize_text_field($_POST['grp_widget_template_star_color_text']) : '#FBBD05';
    if (!preg_match('/^#[0-9A-F]{6}$/i', $star_color)) {
        $star_color = '#FBBD05';
    }
    $star_placement_allowed = array('above', 'below', 'overlay');
    $star_placement = isset($_POST['grp_widget_template_star_placement']) ? sanitize_text_field($_POST['grp_widget_template_star_placement']) : 'below';
    if (!in_array($star_placement, $star_placement_allowed, true)) {
        $star_placement = 'below';
    }
    $show_logo = isset($_POST['grp_widget_template_show_logo']) ? 1 : 0;
    $font_family = isset($_POST['grp_widget_template_font_family']) ? sanitize_text_field($_POST['grp_widget_template_font_family']) : '';
    $max_height = isset($_POST['grp_widget_template_max_height']) ? absint($_POST['grp_widget_template_max_height']) : 0;
    
    update_option('grp_widget_template_star_color', $star_color);
    update_option('grp_widget_template_star_placement', $star_placement);
    update_option('grp_widget_template_show_logo', $show_logo);
    update_option('grp_widget_template_font_family', $font_family);
    update_option('grp_widget_template_max_height', $max_height);
    $template = isset($_POST['grp_widget_button_default_template']) ? sanitize_text_field($_POST['grp_widget_button_default_template']) : 'basic';
    $template = GRP_Review_Widgets::get_instance()->sanitize_button_template($template);
    update_option('grp_widget_button_default_template', $template);
    update_option('grp_widget_qr_default_size', absint($_POST['grp_widget_qr_default_size']));
    update_option('grp_widget_tracking_enabled', isset($_POST['grp_widget_tracking_enabled']));
    
    echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved successfully!', 'google-reviews-plugin') . '</p></div>';
}

// Get current settings
$button_text = get_option('grp_widget_button_default_text', __('Leave us a review', 'google-reviews-plugin'));
$button_style = get_option('grp_widget_button_default_style', 'default');
$button_size = get_option('grp_widget_button_default_size', 'medium');
$button_color = get_option('grp_widget_button_default_color', '');
$button_bg_color = get_option('grp_widget_button_default_bg_color', '');
$star_color = get_option('grp_widget_template_star_color', '#FBBD05');
$star_placement = get_option('grp_widget_template_star_placement', 'below');
$show_logo = get_option('grp_widget_template_show_logo', true);
$font_family = get_option('grp_widget_template_font_family', '');
$max_height = get_option('grp_widget_template_max_height', '');
$button_template = GRP_Review_Widgets::get_instance()->sanitize_button_template(get_option('grp_widget_button_default_template', 'basic'));
$button_templates = GRP_Review_Widgets::get_instance()->get_button_templates();
$button_template_definition = isset($button_templates[$button_template]) ? $button_templates[$button_template] : $button_templates['basic'];
$qr_size = get_option('grp_widget_qr_default_size', 200);
$tracking_enabled = get_option('grp_widget_tracking_enabled', true);

// Get Place ID
$place_id = get_option('grp_place_id', '');
$place_id_auto = get_option('grp_gbp_place_id_default', '');
$has_place_id = !empty($place_id) || !empty($place_id_auto);

// Get stats
global $wpdb;
$clicks_table = $wpdb->prefix . 'grp_widget_clicks';
$total_clicks = $wpdb->get_var("SELECT COUNT(*) FROM {$clicks_table}");
$converted_clicks = $wpdb->get_var("SELECT COUNT(*) FROM {$clicks_table} WHERE converted = 1");

$license = new GRP_License();
$is_pro = $license->is_pro();

?>

<div class="wrap">
    <h1><?php esc_html_e('Review Request Widgets', 'google-reviews-plugin'); ?></h1>
    
    <nav class="nav-tab-wrapper">
        <a href="?page=google-reviews-widgets&tab=widgets" class="nav-tab <?php echo (!isset($_GET['tab']) || $_GET['tab'] === 'widgets') ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Widgets', 'google-reviews-plugin'); ?>
        </a>
        <a href="?page=google-reviews-widgets&tab=qr" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'qr') ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('QR Codes', 'google-reviews-plugin'); ?>
        </a>
        <a href="?page=google-reviews-widgets&tab=analytics" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'analytics') ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Analytics', 'google-reviews-plugin'); ?>
            <?php if (!$is_pro): ?>
                <span class="dashicons dashicons-lock" style="font-size: 12px; vertical-align: middle; margin-left: 3px;"></span>
            <?php endif; ?>
        </a>
    </nav>
    
    <?php if (!$has_place_id): ?>
        <div class="notice notice-warning is-dismissible" style="margin-top: 20px;">
            <p>
                <strong><?php esc_html_e('Place ID Required:', 'google-reviews-plugin'); ?></strong>
                <?php esc_html_e('Please set your Place ID in the', 'google-reviews-plugin'); ?>
                <a href="<?php echo admin_url('admin.php?page=google-reviews-settings'); ?>"><?php esc_html_e('Settings', 'google-reviews-plugin'); ?></a>
                <?php esc_html_e('page to generate review links.', 'google-reviews-plugin'); ?>
            </p>
        </div>
    <?php endif; ?>
    
    <?php
    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'widgets';
    
    if ($active_tab === 'qr'): ?>
        <!-- QR Code Generator Tab -->
        <div class="grp-settings-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2 style="margin-top: 0;"><?php esc_html_e('QR Code Generator', 'google-reviews-plugin'); ?></h2>
            
            <?php if ($has_place_id): ?>
                <div class="grp-qr-generator">
                    <div class="grp-qr-controls" style="margin-bottom: 20px;">
                        <label for="grp-qr-size">
                            <?php esc_html_e('QR Code Size:', 'google-reviews-plugin'); ?>
                            <input type="number" id="grp-qr-size" value="<?php echo esc_attr($qr_size); ?>" min="100" max="1000" step="50" style="width: 80px; margin-left: 10px;">
                            <span>px</span>
                        </label>
                        <button type="button" id="grp-generate-qr" class="button button-primary" style="margin-left: 15px;">
                            <?php esc_html_e('Generate QR Code', 'google-reviews-plugin'); ?>
                        </button>
                    </div>
                    
                    <div id="grp-qr-preview" style="text-align: center; padding: 20px; background: #f9f9f9; border-radius: 4px; min-height: 300px; display: flex; align-items: center; justify-content: center;">
                        <p style="color: #666;"><?php esc_html_e('Click "Generate QR Code" to create your QR code', 'google-reviews-plugin'); ?></p>
                    </div>
                    
                    <div id="grp-qr-download" style="margin-top: 20px; text-align: center; display: none;">
                        <a href="#" id="grp-qr-download-link" class="button" download="google-review-qr-code.png">
                            <?php esc_html_e('Download QR Code', 'google-reviews-plugin'); ?>
                        </a>
                        <p class="description" style="margin-top: 10px;">
                            <?php esc_html_e('Right-click the QR code above and select "Save image as..." to download, or use the download button.', 'google-reviews-plugin'); ?>
                        </p>
                    </div>
                </div>
                
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
                    <h3><?php esc_html_e('Shortcode', 'google-reviews-plugin'); ?></h3>
                    <p><?php esc_html_e('Use this shortcode to display a QR code anywhere on your site:', 'google-reviews-plugin'); ?></p>
                    <div style="background: #f5f5f5; padding: 15px; border-radius: 4px; margin: 10px 0;">
                        <code>[grp_review_qr size="<?php echo esc_attr($qr_size); ?>" caption="<?php esc_attr_e('Scan to leave a review', 'google-reviews-plugin'); ?>"]</code>
                        <button type="button" class="button grp-copy-shortcode" data-shortcode='[grp_review_qr size="<?php echo esc_attr($qr_size); ?>" caption="<?php esc_attr_e('Scan to leave a review', 'google-reviews-plugin'); ?>"]' style="margin-left: 10px;">
                            <?php esc_html_e('Copy', 'google-reviews-plugin'); ?>
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <p><?php esc_html_e('Please configure your Place ID in Settings to generate QR codes.', 'google-reviews-plugin'); ?></p>
            <?php endif; ?>
        </div>
        
    <?php elseif ($active_tab === 'analytics'): ?>
        <!-- Analytics Tab -->
        <?php if (!$is_pro): ?>
            <div class="grp-settings-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <h2 style="margin-top: 0;"><?php esc_html_e('Analytics & Tracking', 'google-reviews-plugin'); ?></h2>
                <p><?php esc_html_e('Advanced analytics and conversion tracking are available in the Pro version.', 'google-reviews-plugin'); ?></p>
                <div style="background: #f0f6fc; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
                    <h3 style="margin-top: 0;"><?php esc_html_e('Pro Features:', 'google-reviews-plugin'); ?></h3>
                    <ul>
                        <li>✓ <?php esc_html_e('Detailed click analytics', 'google-reviews-plugin'); ?></li>
                        <li>✓ <?php esc_html_e('Conversion tracking (click → review)', 'google-reviews-plugin'); ?></li>
                        <li>✓ <?php esc_html_e('Widget performance metrics', 'google-reviews-plugin'); ?></li>
                        <li>✓ <?php esc_html_e('Export analytics data', 'google-reviews-plugin'); ?></li>
                        <li>✓ <?php esc_html_e('Time-based reports', 'google-reviews-plugin'); ?></li>
                    </ul>
                    <a href="https://reactwoo.com/google-reviews-plugin-pro/" class="button button-primary" target="_blank" style="margin-top: 15px;">
                        <?php esc_html_e('Upgrade to Pro', 'google-reviews-plugin'); ?>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="grp-settings-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <h2 style="margin-top: 0;"><?php esc_html_e('Analytics Overview', 'google-reviews-plugin'); ?></h2>
                
                <div class="grp-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
                    <div class="grp-stat-card" style="background: #f9f9f9; padding: 20px; border-radius: 4px; text-align: center;">
                        <div style="font-size: 32px; font-weight: bold; color: #0073aa; margin-bottom: 10px;">
                            <?php echo number_format($total_clicks); ?>
                        </div>
                        <div style="color: #666; font-size: 14px;">
                            <?php esc_html_e('Total Clicks', 'google-reviews-plugin'); ?>
                        </div>
                    </div>
                    
                    <div class="grp-stat-card" style="background: #f9f9f9; padding: 20px; border-radius: 4px; text-align: center;">
                        <div style="font-size: 32px; font-weight: bold; color: #46b450; margin-bottom: 10px;">
                            <?php echo number_format($converted_clicks); ?>
                        </div>
                        <div style="color: #666; font-size: 14px;">
                            <?php esc_html_e('Conversions', 'google-reviews-plugin'); ?>
                        </div>
                    </div>
                    
                    <div class="grp-stat-card" style="background: #f9f9f9; padding: 20px; border-radius: 4px; text-align: center;">
                        <div style="font-size: 32px; font-weight: bold; color: #dc3232; margin-bottom: 10px;">
                            <?php 
                            $conversion_rate = $total_clicks > 0 ? round(($converted_clicks / $total_clicks) * 100, 1) : 0;
                            echo $conversion_rate . '%';
                            ?>
                        </div>
                        <div style="color: #666; font-size: 14px;">
                            <?php esc_html_e('Conversion Rate', 'google-reviews-plugin'); ?>
                        </div>
                    </div>
                </div>
                
                <p class="description">
                    <?php esc_html_e('Note: Conversion tracking requires manual verification. Conversions are marked when a review is detected for your location.', 'google-reviews-plugin'); ?>
                </p>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- Widgets Tab -->
        <form method="post" action="">
            <?php wp_nonce_field('grp_widgets_settings'); ?>
            
            <!-- Preview Section at Top -->
            <div class="grp-settings-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <h2 style="margin-top: 0;"><?php esc_html_e('Preview', 'google-reviews-plugin'); ?></h2>
                <p><?php esc_html_e('Preview of how your review button will look (updates as you change settings):', 'google-reviews-plugin'); ?></p>
                <?php
                $review_widgets = GRP_Review_Widgets::get_instance();
                $preview_template = $review_widgets->get_button_template($button_template);
                if (!$preview_template) {
                    $preview_template = $review_widgets->get_button_template('basic');
                }
                $preview_qr_url = '';
                if ($has_place_id && !empty($preview_template['qr'])) {
                    $qr_size = isset($preview_template['qr_size']) ? absint($preview_template['qr_size']) : 96;
                    $preview_qr_url = $review_widgets->generate_qr_code($preview_url, $qr_size);
                }
                $preview_tagline = !empty($preview_template['tagline']) ? $preview_template['tagline'] : '';
                $preview_star_row = !empty($preview_template['stars']) ? implode(' ', array_fill(0, 5, '★')) : '';
                $preview_star_color = $star_color;
                $preview_star_placement = $star_placement;
                $preview_show_logo = (bool) $show_logo;
                $preview_font_family = $font_family;
                $preview_max_height = absint($max_height);
                $blank_qr = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
                ?>
                <div id="grp-button-preview" style="padding: 30px; background: #f9f9f9; border-radius: 4px; margin: 20px 0; text-align: center;">
                    <?php
                    $preview_url = $has_place_id ? GRP_Review_Widgets::get_instance()->generate_review_url('', 'button', 'preview') : '#';
                    $preview_classes = array('grp-review-button', 'grp-review-button-' . $button_style, 'grp-review-button-' . $button_size);
                    $preview_classes[] = 'grp-review-button-template-' . sanitize_title($button_template);
                    $preview_styles = array();
                    if (!empty($button_color)) {
                        $preview_styles[] = 'color: ' . esc_attr($button_color);
                    }
                    if (!empty($button_bg_color)) {
                        $preview_styles[] = 'background-color: ' . esc_attr($button_bg_color);
                    }
                    if (!empty($preview_font_family)) {
                        $preview_styles[] = 'font-family: ' . esc_attr($preview_font_family);
                    }
                    if ($preview_max_height > 0) {
                        $preview_styles[] = 'max-height: ' . $preview_max_height . 'px';
                    }
                    $preview_style_attr = !empty($preview_styles) ? ' style="' . implode('; ', $preview_styles) . '"' : '';
                    ?>
                    <a href="<?php echo esc_url($preview_url); ?>" id="grp-preview-button" class="<?php echo esc_attr(implode(' ', array_merge($preview_classes, array('grp-star-placement-' . esc_attr($preview_star_placement))))); ?>" target="_blank" rel="noopener"<?php echo $preview_style_attr; ?>>
                        <span class="grp-preview-star-row" id="grp-preview-star-row" style="color: <?php echo esc_attr($preview_star_color); ?>;"><?php echo esc_html($preview_star_row); ?></span>
                        <span class="grp-preview-qr <?php echo !empty($preview_qr_url) ? 'has-qr' : ''; ?>" id="grp-preview-qr">
                            <img id="grp-preview-qr-img" src="<?php echo esc_url(!empty($preview_qr_url) ? $preview_qr_url : $blank_qr); ?>" alt="<?php esc_attr_e('QR preview', 'google-reviews-plugin'); ?>">
                            <span class="grp-preview-qr-placeholder"><?php esc_html_e('QR', 'google-reviews-plugin'); ?></span>
                        </span>
                        <div class="grp-preview-content">
                            <?php if ($preview_show_logo): ?>
                                <span class="grp-review-button-icon">⭐</span>
                            <?php endif; ?>
                            <span class="grp-review-button-text" id="grp-preview-text"><?php echo esc_html($button_text); ?></span>
                            <span class="grp-preview-tagline" id="grp-preview-tagline"><?php echo esc_html($preview_tagline); ?></span>
                        </div>
                    </a>
                </div>
            </div>
            
            <div class="grp-settings-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #eee;"><?php esc_html_e('Review Button Widget', 'google-reviews-plugin'); ?></h2>
                
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="grp_widget_button_default_text"><?php esc_html_e('Button Text', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="grp_widget_button_default_text" name="grp_widget_button_default_text" value="<?php echo esc_attr($button_text); ?>" class="regular-text">
                                <p class="description"><?php esc_html_e('Default text for review buttons.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="grp_widget_button_default_style"><?php esc_html_e('Button Style', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <select id="grp_widget_button_default_style" name="grp_widget_button_default_style">
                                    <option value="default" <?php selected($button_style, 'default'); ?>><?php esc_html_e('Default', 'google-reviews-plugin'); ?></option>
                                    <option value="rounded" <?php selected($button_style, 'rounded'); ?>><?php esc_html_e('Rounded', 'google-reviews-plugin'); ?></option>
                                    <option value="outline" <?php selected($button_style, 'outline'); ?>><?php esc_html_e('Outline', 'google-reviews-plugin'); ?></option>
                                    <option value="minimal" <?php selected($button_style, 'minimal'); ?>><?php esc_html_e('Minimal', 'google-reviews-plugin'); ?></option>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="grp_widget_button_default_size"><?php esc_html_e('Button Size', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <select id="grp_widget_button_default_size" name="grp_widget_button_default_size">
                                    <option value="small" <?php selected($button_size, 'small'); ?>><?php esc_html_e('Small', 'google-reviews-plugin'); ?></option>
                                    <option value="medium" <?php selected($button_size, 'medium'); ?>><?php esc_html_e('Medium', 'google-reviews-plugin'); ?></option>
                                    <option value="large" <?php selected($button_size, 'large'); ?>><?php esc_html_e('Large', 'google-reviews-plugin'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="grp_widget_template_star_color"><?php esc_html_e('Star Color', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="color" id="grp_widget_template_star_color" name="grp_widget_template_star_color" value="<?php echo esc_attr(!empty($star_color) ? $star_color : '#FBBD05'); ?>">
                                <input type="text" id="grp_widget_template_star_color_text" name="grp_widget_template_star_color_text" value="<?php echo esc_attr($star_color); ?>" placeholder="#FBBD05" style="width: 140px; margin-left: 10px;">
                                <p class="description"><?php esc_html_e('Control the color of the star row inside the template.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="grp_widget_template_star_placement"><?php esc_html_e('Star Placement', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <select id="grp_widget_template_star_placement" name="grp_widget_template_star_placement">
                                    <option value="below" <?php selected($star_placement, 'below'); ?>><?php esc_html_e('Below QR', 'google-reviews-plugin'); ?></option>
                                    <option value="above" <?php selected($star_placement, 'above'); ?>><?php esc_html_e('Above QR', 'google-reviews-plugin'); ?></option>
                                    <option value="overlay" <?php selected($star_placement, 'overlay'); ?>><?php esc_html_e('Overlay QR', 'google-reviews-plugin'); ?></option>
                                </select>
                                <p class="description"><?php esc_html_e('Position the star row relative to the QR/graphic area.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="grp_widget_template_show_logo"><?php esc_html_e('Show Logo/Icon', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <label for="grp_widget_template_show_logo">
                                    <input type="checkbox" id="grp_widget_template_show_logo" name="grp_widget_template_show_logo" value="1" <?php checked($show_logo, true); ?>>
                                    <?php esc_html_e('Display the logo/icon inside the template', 'google-reviews-plugin'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="grp_widget_template_font_family"><?php esc_html_e('Font Family', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="grp_widget_template_font_family" name="grp_widget_template_font_family" value="<?php echo esc_attr($font_family); ?>" placeholder="e.g. 'Inter', sans-serif" class="regular-text">
                                <p class="description"><?php esc_html_e('Override the font used inside the template.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="grp_widget_template_max_height"><?php esc_html_e('Max Height', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="number" id="grp_widget_template_max_height" name="grp_widget_template_max_height" value="<?php echo esc_attr($max_height); ?>" min="0" step="1">
                                <span>px</span>
                                <p class="description"><?php esc_html_e('Limit the maximum height of the button/card so it fits specific areas.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="grp_widget_button_default_template"><?php esc_html_e('Button Layout', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <div class="grp-template-selector">
                                    <select id="grp_widget_button_default_template" name="grp_widget_button_default_template">
                                        <?php foreach ($button_templates as $key => $template): ?>
                                            <option value="<?php echo esc_attr($key); ?>" <?php selected($button_template, $key); ?>
                                                    data-description="<?php echo esc_attr($template['description']); ?>"
                                                    data-qr="<?php echo !empty($template['qr']) ? '1' : '0'; ?>"
                                                    data-qr-size="<?php echo esc_attr(isset($template['qr_size']) ? $template['qr_size'] : ''); ?>"
                                                    data-pro="<?php echo !empty($template['pro']) ? '1' : '0'; ?>">
                                                <?php echo esc_html($template['name']); ?>
                                                <?php if (!empty($template['pro'])): ?><?php esc_html_e(' (Pro)', 'google-reviews-plugin'); ?><?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <p class="description" id="grp-template-selected-description"><?php echo esc_html($button_template_definition['description']); ?></p>
                                <p class="description grp-template-pro-note" id="grp-template-pro-note"><?php if (!empty($button_template_definition['pro']) && !$is_pro): ?><?php esc_html_e('Upgrade to Pro to unlock extra layout controls.', 'google-reviews-plugin'); ?><?php endif; ?></p>
                                <p class="description"><?php esc_html_e('Choose a layout that matches your experience. The barcode templates integrate QR art from our barcode creator, and the Canva Edition is a Pro-only design with extra editable controls.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="grp_widget_button_default_color"><?php esc_html_e('Text Color', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="color" id="grp_widget_button_default_color" name="grp_widget_button_default_color" value="<?php echo esc_attr(!empty($button_color) ? $button_color : '#ffffff'); ?>">
                                <input type="text" id="grp_widget_button_default_color_text" name="grp_widget_button_default_color_text" value="<?php echo esc_attr($button_color); ?>" placeholder="#ffffff (leave empty for default)" style="width: 150px; margin-left: 10px;">
                                <button type="button" class="button button-small" id="grp-clear-text-color" style="margin-left: 10px;"><?php esc_html_e('Clear', 'google-reviews-plugin'); ?></button>
                                <p class="description"><?php esc_html_e('Leave empty to use default color. The color picker shows a preview color, but empty text field = default.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="grp_widget_button_default_bg_color"><?php esc_html_e('Background Color', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="color" id="grp_widget_button_default_bg_color" name="grp_widget_button_default_bg_color" value="<?php echo esc_attr(!empty($button_bg_color) ? $button_bg_color : '#0073aa'); ?>">
                                <input type="text" id="grp_widget_button_default_bg_color_text" name="grp_widget_button_default_bg_color_text" value="<?php echo esc_attr($button_bg_color); ?>" placeholder="#0073aa (leave empty for default)" style="width: 150px; margin-left: 10px;">
                                <button type="button" class="button button-small" id="grp-clear-bg-color" style="margin-left: 10px;"><?php esc_html_e('Clear', 'google-reviews-plugin'); ?></button>
                                <p class="description"><?php esc_html_e('Leave empty to use default color. The color picker shows a preview color, but empty text field = default.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="grp-settings-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #eee;"><?php esc_html_e('QR Code Settings', 'google-reviews-plugin'); ?></h2>
                
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="grp_widget_qr_default_size"><?php esc_html_e('Default QR Code Size', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="number" id="grp_widget_qr_default_size" name="grp_widget_qr_default_size" value="<?php echo esc_attr($qr_size); ?>" min="100" max="1000" step="50">
                                <span>px</span>
                                <p class="description"><?php esc_html_e('Default size for generated QR codes.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="grp-settings-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #eee;"><?php esc_html_e('Tracking Settings', 'google-reviews-plugin'); ?></h2>
                
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="grp_widget_tracking_enabled"><?php esc_html_e('Enable Click Tracking', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="grp_widget_tracking_enabled" name="grp_widget_tracking_enabled" value="1" <?php checked($tracking_enabled, true); ?>>
                                <p class="description"><?php esc_html_e('Track clicks on review buttons and QR codes for analytics.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <?php submit_button(__('Save Settings', 'google-reviews-plugin'), 'primary', 'grp_widgets_submit', false); ?>
        </form>
        
        <div class="grp-settings-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2 style="margin-top: 0;"><?php esc_html_e('Shortcodes', 'google-reviews-plugin'); ?></h2>
            
            <h3><?php esc_html_e('Review Button', 'google-reviews-plugin'); ?></h3>
            <p><?php esc_html_e('Use this shortcode to display a review button:', 'google-reviews-plugin'); ?></p>
            <div style="background: #f5f5f5; padding: 15px; border-radius: 4px; margin: 10px 0;">
                <code>[grp_review_button]</code>
                <button type="button" class="button grp-copy-shortcode" data-shortcode="[grp_review_button]" style="margin-left: 10px;">
                    <?php esc_html_e('Copy', 'google-reviews-plugin'); ?>
                </button>
            </div>
            
            <h4 style="margin-top: 20px;"><?php esc_html_e('Available Attributes:', 'google-reviews-plugin'); ?></h4>
            <ul style="list-style: disc; margin-left: 20px;">
                <li><code>text</code> - <?php esc_html_e('Button text (default: "Leave us a review")', 'google-reviews-plugin'); ?></li>
                <li><code>style</code> - <?php esc_html_e('Button style: default, rounded, outline, minimal', 'google-reviews-plugin'); ?></li>
                <li><code>size</code> - <?php esc_html_e('Button size: small, medium, large', 'google-reviews-plugin'); ?></li>
                <li><code>color</code> - <?php esc_html_e('Text color (hex code)', 'google-reviews-plugin'); ?></li>
                <li><code>bg_color</code> - <?php esc_html_e('Background color (hex code)', 'google-reviews-plugin'); ?></li>
                <li><code>align</code> - <?php esc_html_e('Alignment: left, center, right', 'google-reviews-plugin'); ?></li>
                <li><code>template</code> - <?php esc_html_e('Layout template: basic, qr-badge, barcode-panel, canva (Pro only).', 'google-reviews-plugin'); ?></li>
                <li><code>place_id</code> - <?php esc_html_e('Override Place ID (optional)', 'google-reviews-plugin'); ?></li>
            </ul>
            
            <div style="background: #f5f5f5; padding: 15px; border-radius: 4px; margin: 15px 0;">
                <strong><?php esc_html_e('Example:', 'google-reviews-plugin'); ?></strong><br>
                <code>[grp_review_button text="Rate us on Google" style="rounded" size="large" align="center" template="qr-badge"]</code>
                <button type="button" class="button grp-copy-shortcode" data-shortcode='[grp_review_button text="Rate us on Google" style="rounded" size="large" align="center" template="qr-badge"]' style="margin-left: 10px;">
                    <?php esc_html_e('Copy', 'google-reviews-plugin'); ?>
                </button>
            </div>
            
            <h3 style="margin-top: 30px;"><?php esc_html_e('QR Code', 'google-reviews-plugin'); ?></h3>
            <p><?php esc_html_e('Use this shortcode to display a QR code:', 'google-reviews-plugin'); ?></p>
            <div style="background: #f5f5f5; padding: 15px; border-radius: 4px; margin: 10px 0;">
                <code>[grp_review_qr]</code>
                <button type="button" class="button grp-copy-shortcode" data-shortcode="[grp_review_qr]" style="margin-left: 10px;">
                    <?php esc_html_e('Copy', 'google-reviews-plugin'); ?>
                </button>
            </div>
            
            <h4 style="margin-top: 20px;"><?php esc_html_e('Available Attributes:', 'google-reviews-plugin'); ?></h4>
            <ul style="list-style: disc; margin-left: 20px;">
                <li><code>size</code> - <?php esc_html_e('QR code size in pixels (default: 200)', 'google-reviews-plugin'); ?></li>
                <li><code>caption</code> - <?php esc_html_e('Caption text below QR code', 'google-reviews-plugin'); ?></li>
                <li><code>place_id</code> - <?php esc_html_e('Override Place ID (optional)', 'google-reviews-plugin'); ?></li>
            </ul>
        </div>
        
    <?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    // Ensure preview is initialized on page load
    // This will be called by widgets-admin.js but we ensure it runs if script loads late
    if (typeof window.updateGRPPreview === 'undefined') {
        // Wait a moment for widgets-admin.js to load, then initialize preview
        setTimeout(function() {
            if ($('#grp-preview-button').length) {
                // Trigger preview update
                $('#grp_widget_button_default_text').trigger('input');
            }
        }, 100);
    }
});
</script>

