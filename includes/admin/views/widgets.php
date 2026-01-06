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
        $button_color = '#ffffff';
    }
    if ($button_color === '') {
        $button_color = '#ffffff';
    }
    if (!empty($button_bg_color) && !preg_match('/^#[0-9A-F]{6}$/i', $button_bg_color)) {
        $button_bg_color = '#2b2b2b';
    }
    if ($button_bg_color === '') {
        $button_bg_color = '#2b2b2b';
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
    $logo_scale = isset($_POST['grp_widget_template_logo_scale']) ? absint($_POST['grp_widget_template_logo_scale']) : 50;
    $max_width = isset($_POST['grp_widget_template_max_width']) ? absint($_POST['grp_widget_template_max_width']) : 0;
    $gradient_start = isset($_POST['grp_widget_template_gradient_start_text']) ? sanitize_text_field($_POST['grp_widget_template_gradient_start_text']) : '#24a1ff';
    $gradient_end = isset($_POST['grp_widget_template_gradient_end_text']) ? sanitize_text_field($_POST['grp_widget_template_gradient_end_text']) : '#ff7b5a';
    $link_color = isset($_POST['grp_widget_template_link_color_text']) ? sanitize_text_field($_POST['grp_widget_template_link_color_text']) : '#ffffff';
    $link_text = isset($_POST['grp_widget_template_link_text']) ? sanitize_text_field($_POST['grp_widget_template_link_text']) : __('Click here', 'google-reviews-plugin');
    if (!preg_match('/^#[0-9A-F]{6}$/i', $gradient_start)) {
        $gradient_start = '#24a1ff';
    }
    if (!preg_match('/^#[0-9A-F]{6}$/i', $gradient_end)) {
        $gradient_end = '#ff7b5a';
    }
    if (!preg_match('/^#[0-9A-F]{6}$/i', $link_color)) {
        $link_color = '#ffffff';
    }
    if ($link_text === '') {
        $link_text = __('Click here', 'google-reviews-plugin');
    }
    $box_shadow_enabled = isset($_POST['grp_widget_template_box_shadow_enabled']) ? 1 : 0;
    $box_shadow_value = isset($_POST['grp_widget_template_box_shadow_value']) ? sanitize_text_field($_POST['grp_widget_template_box_shadow_value']) : '0 18px 35px rgba(0, 0, 0, 0.25)';
    $box_shadow_h = isset($_POST['grp_widget_template_box_shadow_h']) ? sanitize_text_field($_POST['grp_widget_template_box_shadow_h']) : '0';
    $box_shadow_v = isset($_POST['grp_widget_template_box_shadow_v']) ? sanitize_text_field($_POST['grp_widget_template_box_shadow_v']) : '18';
    $box_shadow_blur = isset($_POST['grp_widget_template_box_shadow_blur']) ? sanitize_text_field($_POST['grp_widget_template_box_shadow_blur']) : '35';
    $box_shadow_spread = isset($_POST['grp_widget_template_box_shadow_spread']) ? sanitize_text_field($_POST['grp_widget_template_box_shadow_spread']) : '0';
    $box_shadow_color = isset($_POST['grp_widget_template_box_shadow_color']) ? sanitize_text_field($_POST['grp_widget_template_box_shadow_color']) : '#000000';
    $glass_effect = isset($_POST['grp_widget_template_glass_effect']) ? 1 : 0;
    
    update_option('grp_widget_template_star_color', $star_color);
    update_option('grp_widget_template_star_placement', $star_placement);
    update_option('grp_widget_template_show_logo', $show_logo);
    update_option('grp_widget_template_font_family', $font_family);
    update_option('grp_widget_template_max_height', $max_height);
    update_option('grp_widget_template_gradient_start', $gradient_start);
    update_option('grp_widget_template_gradient_end', $gradient_end);
    update_option('grp_widget_template_link_color', $link_color);
    update_option('grp_widget_template_link_text', $link_text);
    update_option('grp_widget_template_max_width', $max_width);
    update_option('grp_widget_template_box_shadow_enabled', $box_shadow_enabled);
    update_option('grp_widget_template_box_shadow_value', $box_shadow_value);
    update_option('grp_widget_template_box_shadow_h', $box_shadow_h);
    update_option('grp_widget_template_box_shadow_v', $box_shadow_v);
    update_option('grp_widget_template_box_shadow_blur', $box_shadow_blur);
    update_option('grp_widget_template_box_shadow_spread', $box_shadow_spread);
    update_option('grp_widget_template_box_shadow_color', $box_shadow_color);
    update_option('grp_widget_template_logo_scale', $logo_scale);
    update_option('grp_widget_template_glass_effect', $glass_effect);
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
$button_color = get_option('grp_widget_button_default_color', '#ffffff');
$button_bg_color = get_option('grp_widget_button_default_bg_color', '#2b2b2b');
$star_color = get_option('grp_widget_template_star_color', '#FBBD05');
$star_placement = get_option('grp_widget_template_star_placement', 'below');
    $show_logo = get_option('grp_widget_template_show_logo', true);
    $font_family = get_option('grp_widget_template_font_family', '');
    $max_height = get_option('grp_widget_template_max_height', 0);
$logo_scale = get_option('grp_widget_template_logo_scale', 13);
$max_width = get_option('grp_widget_template_max_width', 400);
$gradient_start = get_option('grp_widget_template_gradient_start', '#24a1ff');
$gradient_end = get_option('grp_widget_template_gradient_end', '#ff7b5a');
$link_color = get_option('grp_widget_template_link_color', '#111111');
$link_text = get_option('grp_widget_template_link_text', __('Click here', 'google-reviews-plugin'));
$box_shadow_enabled = get_option('grp_widget_template_box_shadow_enabled', true);
$box_shadow_value = get_option('grp_widget_template_box_shadow_value', '0 18px 35px rgba(0, 0, 0, 0.25)');
$box_shadow_h = get_option('grp_widget_template_box_shadow_h', '0');
$box_shadow_v = get_option('grp_widget_template_box_shadow_v', '18');
$box_shadow_blur = get_option('grp_widget_template_box_shadow_blur', '35');
$box_shadow_spread = get_option('grp_widget_template_box_shadow_spread', '0');
$box_shadow_color = get_option('grp_widget_template_box_shadow_color', '#000000');
$glass_effect = get_option('grp_widget_template_glass_effect', false);
$button_template = GRP_Review_Widgets::get_instance()->sanitize_button_template(get_option('grp_widget_button_default_template', 'basic'));
$button_templates = GRP_Review_Widgets::get_instance()->get_button_templates();
$button_template_definition = isset($button_templates[$button_template]) ? $button_templates[$button_template] : $button_templates['basic'];
$qr_size = get_option('grp_widget_qr_default_size', 135);
if ($qr_size < 135) {
    $qr_size = 135;
}
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
                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                    <p style="margin:0;"><?php esc_html_e('Preview of how your review button will look (updates as you change settings):', 'google-reviews-plugin'); ?></p>
                    <div style="display:flex; align-items:center; gap:6px;">
                        <button type="button" id="grp-template-editor-open" class="button button-secondary" data-pro="<?php echo $is_pro ? '1' : '0'; ?>">
                            <?php esc_html_e('Customize Template', 'google-reviews-plugin'); ?>
                        </button>
                        <?php if (!$is_pro): ?>
                            <span class="grp-template-pro-label">
                                <span class="dashicons dashicons-lock"></span>
                                <?php esc_html_e('Pro only', 'google-reviews-plugin'); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
                $review_widgets = GRP_Review_Widgets::get_instance();
                $preview_template = $review_widgets->get_button_template($button_template);
                if (!$preview_template) {
                    $preview_template = $review_widgets->get_button_template('basic');
                }
                $preview_qr_url = '';
                if ($has_place_id && !empty($preview_template['qr'])) {
                    $qr_size = isset($preview_template['qr_size']) ? absint($preview_template['qr_size']) : 125;
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
                <div id="grp-button-preview" class="grp-preview-shell" style="padding: 30px; background: #f9f9f9; border-radius: 4px; margin: 20px 0; text-align: center;">
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
                    $is_card = isset($preview_template['type']) && $preview_template['type'] === 'card';
                    $preview_subtitle = !empty($preview_template['subtitle']) ? $preview_template['subtitle'] : __('Scan the QR code below to leave a review!', 'google-reviews-plugin');
                    $card_gradient = ($is_card && $button_template === 'creative-pro') ? 'background: linear-gradient(135deg, ' . esc_attr($gradient_start) . ', ' . esc_attr($gradient_end) . ');' : '';
                    $combined_style = trim($card_gradient . ' ' . implode('; ', $preview_styles), '; ');
                    $preview_style_attr = $combined_style ? ' style="' . $combined_style . '"' : '';
                    ?>
                    <?php if ($is_card): ?>
                        <a href="<?php echo esc_url($preview_url); ?>" id="grp-preview-button" class="<?php echo esc_attr(implode(' ', array_merge($preview_classes, array('grp-card-preview', 'grp-star-placement-' . esc_attr($preview_star_placement))))); ?>" target="_blank" rel="noopener"<?php echo $preview_style_attr; ?>>
                            <?php if ($preview_show_logo): ?>
                                <div class="grp-card-logo">G</div>
                            <?php endif; ?>
                            <div class="grp-card-stars" style="color: <?php echo esc_attr($preview_star_color); ?>;"><?php echo esc_html($preview_star_row); ?></div>
                            <div class="grp-card-heading"><?php echo esc_html($button_text); ?></div>
                            <div class="grp-card-subtitle"><?php echo esc_html($preview_subtitle); ?></div>
                            <div class="grp-card-qr">
                                <img src="<?php echo esc_url(!empty($preview_qr_url) ? $preview_qr_url : $blank_qr); ?>" alt="<?php esc_attr_e('QR preview', 'google-reviews-plugin'); ?>">
                            </div>
                            <div class="grp-card-link"><?php echo esc_html($preview_url); ?></div>
                        </a>
                    <?php else: ?>
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
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="grp-settings-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #eee;"><?php esc_html_e('Review Button Widget', 'google-reviews-plugin'); ?></h2>
                
                <table class="form-table">
                    <tbody>
                        <tr class="grp-hidden-setting">
                            <th scope="row">
                                <label for="grp_widget_button_default_template"><?php esc_html_e('Layout', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <div class="grp-template-selector">
                                    <select id="grp_widget_button_default_template" name="grp_widget_button_default_template">
                                        <?php foreach ($button_templates as $key => $template): ?>
                                            <?php
                                            $is_locked = !empty($template['pro']) && !$is_pro;
                                            ?>
                                            <option value="<?php echo esc_attr($key); ?>" <?php selected($button_template, $key); ?> <?php disabled($is_locked); ?>
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
                                <p class="description"><?php esc_html_e('Choose a layout that matches your experience. The card templates stack Google branding, stars, and QR art while Creative Pro unlocks gradient/color control.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                        <tr class="grp-gradient-row grp-hidden-setting">
                            <th scope="row">
                                <label for="grp_widget_template_gradient_start"><?php esc_html_e('Gradient Start', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="color" id="grp_widget_template_gradient_start" name="grp_widget_template_gradient_start" value="<?php echo esc_attr($gradient_start); ?>">
                                <input type="text" id="grp_widget_template_gradient_start_text" name="grp_widget_template_gradient_start_text" value="<?php echo esc_attr($gradient_start); ?>" placeholder="#24a1ff" style="width: 140px; margin-left: 10px;">
                                <p class="description"><?php esc_html_e('Gradient start color for the Creative Pro card (updates live in preview).', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                        <tr class="grp-gradient-row grp-hidden-setting">
                            <th scope="row">
                                <label for="grp_widget_template_gradient_end"><?php esc_html_e('Gradient End', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="color" id="grp_widget_template_gradient_end" name="grp_widget_template_gradient_end" value="<?php echo esc_attr($gradient_end); ?>">
                                <input type="text" id="grp_widget_template_gradient_end_text" name="grp_widget_template_gradient_end_text" value="<?php echo esc_attr($gradient_end); ?>" placeholder="#ff7b5a" style="width: 140px; margin-left: 10px;">
                            </td>
                        </tr>
                        <tr class="grp-hidden-setting">
                            <th scope="row">
                                <label for="grp_widget_template_link_color"><?php esc_html_e('Link Color', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="color" id="grp_widget_template_link_color" name="grp_widget_template_link_color" value="<?php echo esc_attr($link_color); ?>">
                                <input type="text" id="grp_widget_template_link_color_text" name="grp_widget_template_link_color_text" value="<?php echo esc_attr($link_color); ?>" placeholder="#111111" style="width: 140px; margin-left: 10px;">
                            </td>
                        </tr>
                        <tr class="grp-hidden-setting">
                            <th scope="row">
                                <label for="grp_widget_template_link_text"><?php esc_html_e('Link Text', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="grp_widget_template_link_text" name="grp_widget_template_link_text" value="<?php echo esc_attr($link_text); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr class="grp-hidden-setting">
                            <th scope="row">
                                <label for="grp_widget_template_star_color"><?php esc_html_e('Star Color', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="color" id="grp_widget_template_star_color" name="grp_widget_template_star_color" value="<?php echo esc_attr(!empty($star_color) ? $star_color : '#FBBD05'); ?>">
                                <input type="text" id="grp_widget_template_star_color_text" name="grp_widget_template_star_color_text" value="<?php echo esc_attr($star_color); ?>" placeholder="#FBBD05" style="width: 140px; margin-left: 10px;">
                            </td>
                        </tr>
                        <tr class="grp-hidden-setting">
                            <th scope="row">
                                <label for="grp_widget_template_star_placement"><?php esc_html_e('Star Placement', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <select id="grp_widget_template_star_placement" name="grp_widget_template_star_placement">
                                    <option value="below" <?php selected($star_placement, 'below'); ?>><?php esc_html_e('Below QR', 'google-reviews-plugin'); ?></option>
                                    <option value="above" <?php selected($star_placement, 'above'); ?>><?php esc_html_e('Above QR', 'google-reviews-plugin'); ?></option>
                                    <option value="overlay" <?php selected($star_placement, 'overlay'); ?>><?php esc_html_e('Overlay QR', 'google-reviews-plugin'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr class="grp-hidden-setting">
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
                        <tr class="grp-hidden-setting">
                            <th scope="row">
                                <label for="grp_widget_template_logo_scale"><?php esc_html_e('Logo Scale (%)', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="range" min="10" max="100" value="<?php echo esc_attr($logo_scale); ?>" id="grp_widget_template_logo_scale" name="grp_widget_template_logo_scale">
                                <input type="number" min="10" max="100" value="<?php echo esc_attr($logo_scale); ?>" id="grp_widget_template_logo_scale_text" style="width: 80px; margin-left: 10px;">
                            </td>
                        </tr>
                        <tr class="grp-hidden-setting">
                            <th scope="row">
                                <label for="grp_widget_template_font_family"><?php esc_html_e('Font Family', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <select id="grp_widget_template_font_family" name="grp_widget_template_font_family">
                                    <?php
                                    $fonts = array(
                                        'Inter, "Space Grotesk", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif' => __('Inter / Space Grotesk', 'google-reviews-plugin'),
                                        '"Roboto", "Helvetica Neue", sans-serif' => __('Roboto', 'google-reviews-plugin'),
                                        '"Playfair Display", Georgia, serif' => __('Playfair Display', 'google-reviews-plugin'),
                                        '"Lora", Georgia, serif' => __('Lora', 'google-reviews-plugin'),
                                    );
                                    foreach ($fonts as $value => $label): ?>
                                        <option value="<?php echo esc_attr($value); ?>" <?php selected($font_family, $value); ?>><?php echo esc_html($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="grp-hidden-setting">
                            <th scope="row">
                                <label><?php esc_html_e('Box Shadow / Glass', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" id="grp_widget_template_box_shadow_enabled" name="grp_widget_template_box_shadow_enabled" value="1" <?php checked($box_shadow_enabled, true); ?>>
                                    <?php esc_html_e('Enable custom shadow', 'google-reviews-plugin'); ?>
                                </label>
                                <button type="button" id="grp-box-shadow-edit" class="button" style="margin-left: 10px;"><?php esc_html_e('Edit', 'google-reviews-plugin'); ?></button>
                                <input type="hidden" id="grp_widget_template_box_shadow_value" name="grp_widget_template_box_shadow_value" value="<?php echo esc_attr($box_shadow_value); ?>">
                                <input type="hidden" id="grp_widget_template_box_shadow_h" name="grp_widget_template_box_shadow_h" value="<?php echo esc_attr($box_shadow_h); ?>">
                                <input type="hidden" id="grp_widget_template_box_shadow_v" name="grp_widget_template_box_shadow_v" value="<?php echo esc_attr($box_shadow_v); ?>">
                                <input type="hidden" id="grp_widget_template_box_shadow_blur" name="grp_widget_template_box_shadow_blur" value="<?php echo esc_attr($box_shadow_blur); ?>">
                                <input type="hidden" id="grp_widget_template_box_shadow_spread" name="grp_widget_template_box_shadow_spread" value="<?php echo esc_attr($box_shadow_spread); ?>">
                                <input type="hidden" id="grp_widget_template_box_shadow_color" name="grp_widget_template_box_shadow_color" value="<?php echo esc_attr($box_shadow_color); ?>">
                                <p class="description"><?php esc_html_e('Open the editor to adjust the shadow sliders (horizontal, vertical, blur, spread, color).', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                        <tr class="grp-hidden-setting">
                            <th scope="row">
                                <label for="grp_widget_template_max_width"><?php esc_html_e('Max Width', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="number" id="grp_widget_template_max_width" name="grp_widget_template_max_width" value="<?php echo esc_attr($max_width); ?>" min="0" step="10">
                                <span>px</span>
                                <p class="description"><?php esc_html_e('Limit the card width so it fits the container.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                        <tr class="grp-hidden-setting">
                            <th scope="row">
                                <label for="grp_widget_template_max_height"><?php esc_html_e('Max Height', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="number" id="grp_widget_template_max_height" name="grp_widget_template_max_height" value="<?php echo esc_attr($max_height); ?>" min="0" step="1">
                                <span>px</span>
                                <p class="description"><?php esc_html_e('Limit the maximum height of the button/card so it fits specific areas.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                        <tr class="grp-hidden-setting">
                            <th scope="row">
                                <label for="grp_widget_template_glass_effect"><?php esc_html_e('Glass Effect', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <label for="grp_widget_template_glass_effect">
                                    <input type="checkbox" id="grp_widget_template_glass_effect" name="grp_widget_template_glass_effect" value="1" <?php checked($glass_effect, true); ?>>
                                    <?php esc_html_e('Apply backdrop blur / light transparency for premium cards', 'google-reviews-plugin'); ?>
                                </label>
                            </td>
                        </tr>
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
                                <label for="grp_widget_button_default_color"><?php esc_html_e('Text Color', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="color" id="grp_widget_button_default_color" name="grp_widget_button_default_color" value="<?php echo esc_attr(!empty($button_color) ? $button_color : '#ffffff'); ?>">
                                <input type="text" id="grp_widget_button_default_color_text" name="grp_widget_button_default_color_text" value="<?php echo esc_attr($button_color); ?>" placeholder="#ffffff (controlled by modal)" style="width: 150px; margin-left: 10px;">
                                <button type="button" class="button button-small" id="grp-clear-text-color" style="margin-left: 10px;"><?php esc_html_e('Clear', 'google-reviews-plugin'); ?></button>
                                <p class="description"><?php esc_html_e('Leave empty to use default color. The color picker shows a preview color, but empty text field = default.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="grp_widget_button_default_bg_color"><?php esc_html_e('Background Color', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="color" id="grp_widget_button_default_bg_color" name="grp_widget_button_default_bg_color" value="<?php echo esc_attr(!empty($button_bg_color) ? $button_bg_color : '#2b2b2b'); ?>">
                                <input type="text" id="grp_widget_button_default_bg_color_text" name="grp_widget_button_default_bg_color_text" value="<?php echo esc_attr($button_bg_color); ?>" placeholder="#2b2b2b (controlled by modal)" style="width: 150px; margin-left: 10px;">
                                <button type="button" class="button button-small" id="grp-clear-bg-color" style="margin-left: 10px;"><?php esc_html_e('Clear', 'google-reviews-plugin'); ?></button>
                                <p class="description"><?php esc_html_e('Leave empty to use default color. The color picker shows a preview color, but empty text field = default.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
            
            <div class="grp-settings-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #eee;"><?php esc_html_e('QR Code Settings', 'google-reviews-plugin'); ?></h2>
                
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="grp_widget_qr_default_size"><?php esc_html_e('Default QR Code Size', 'google-reviews-plugin'); ?></label>
                            </th>
                            <td>
                                <input type="number" id="grp_widget_qr_default_size" name="grp_widget_qr_default_size" value="<?php echo esc_attr($qr_size); ?>" min="135" max="1000" step="5">
                                <span>px</span>
                                <p class="description"><?php esc_html_e('Default size for generated QR codes.', 'google-reviews-plugin'); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="grp-box-shadow-modal" class="grp-box-shadow-modal" style="display: none;">
                    <div class="grp-box-shadow-modal-content">
                        <h3><?php esc_html_e('Box Shadow Editor', 'google-reviews-plugin'); ?></h3>
                        <label>
                            <?php esc_html_e('Color', 'google-reviews-plugin'); ?>
                            <input type="color" id="grp-box-shadow-color-picker" value="<?php echo esc_attr($box_shadow_color); ?>">
                        </label>
                        <div class="grp-box-shadow-controls">
                            <?php
                            $controls = array(
                                array('name' => 'Horizontal', 'id' => 'h', 'min' => -50, 'max' => 50, 'value' => $box_shadow_h),
                                array('name' => 'Vertical', 'id' => 'v', 'min' => -50, 'max' => 50, 'value' => $box_shadow_v),
                                array('name' => 'Blur', 'id' => 'blur', 'min' => 0, 'max' => 100, 'value' => $box_shadow_blur),
                                array('name' => 'Spread', 'id' => 'spread', 'min' => -50, 'max' => 50, 'value' => $box_shadow_spread),
                            );
                            foreach ($controls as $control): ?>
                                <div class="grp-shadow-control">
                                    <span class="grp-shadow-label"><?php echo esc_html($control['name']); ?></span>
                                    <input type="range" class="grp-shadow-range" data-target="<?php echo esc_attr($control['id']); ?>" min="<?php echo esc_attr($control['min']); ?>" max="<?php echo esc_attr($control['max']); ?>" value="<?php echo esc_attr($control['value']); ?>">
                                    <input type="number" class="grp-shadow-number" data-target="<?php echo esc_attr($control['id']); ?>" min="<?php echo esc_attr($control['min']); ?>" max="<?php echo esc_attr($control['max']); ?>" value="<?php echo esc_attr($control['value']); ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="grp-box-shadow-actions">
                            <button type="button" id="grp-box-shadow-modal-close" class="button"><?php esc_html_e('Done', 'google-reviews-plugin'); ?></button>
                        </div>
                    </div>
                </div>
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
        
        <div id="grp-template-editor-modal" class="grp-template-editor-modal" style="display: none;">
            <div class="grp-template-editor-content">
                <button type="button" class="grp-template-editor-close button-link">×</button>
                <h3><?php esc_html_e('Customize Template', 'google-reviews-plugin'); ?></h3>
                <div id="grp-template-editor-preview" class="grp-template-editor-preview"></div>
                <div class="grp-template-editor-controls">
                    <div class="grp-template-editor-column">
                        <label>
                            <input type="checkbox" id="grp-modal-show-logo">
                            <?php esc_html_e('Show logo/icon', 'google-reviews-plugin'); ?>
                        </label>
                        <label><?php esc_html_e('Logo Scale (%)', 'google-reviews-plugin'); ?>
                            <input type="range" id="grp-modal-logo-scale-slider" min="10" max="100">
                            <input type="number" id="grp-modal-logo-scale-number" min="10" max="100">
                        </label>
                        <label for="grp-modal-font-family"><?php esc_html_e('Font Family', 'google-reviews-plugin'); ?></label>
                        <select id="grp-modal-font-family">
                            <option value='Inter, "Space Grotesk", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif'><?php esc_html_e('Inter / Space Grotesk', 'google-reviews-plugin'); ?></option>
                            <option value='"Roboto", "Helvetica Neue", sans-serif'><?php esc_html_e('Roboto', 'google-reviews-plugin'); ?></option>
                            <option value='"Playfair Display", Georgia, serif'><?php esc_html_e('Playfair Display', 'google-reviews-plugin'); ?></option>
                            <option value='"Lora", Georgia, serif'><?php esc_html_e('Lora', 'google-reviews-plugin'); ?></option>
                        </select>
                        <label><?php esc_html_e('Text Color', 'google-reviews-plugin'); ?>
                            <input type="color" id="grp-modal-text-color">
                            <input type="text" id="grp-modal-text-color-text" style="width: 90px; margin-left: 8px;">
                        </label>
                    </div>
                    <div class="grp-template-editor-column">
                        <label><?php esc_html_e('Background Color', 'google-reviews-plugin'); ?>
                            <input type="color" id="grp-modal-background-color">
                            <input type="text" id="grp-modal-background-color-text" style="width: 90px; margin-left: 8px;">
                        </label>
                        <label><?php esc_html_e('Star Color', 'google-reviews-plugin'); ?>
                            <input type="color" id="grp-modal-star-color">
                            <input type="text" id="grp-modal-star-color-text" style="width: 90px; margin-left: 8px;">
                        </label>
                        <label for="grp-modal-star-placement"><?php esc_html_e('Star Placement', 'google-reviews-plugin'); ?></label>
                        <select id="grp-modal-star-placement">
                            <option value="below"><?php esc_html_e('Below QR', 'google-reviews-plugin'); ?></option>
                            <option value="above"><?php esc_html_e('Above QR', 'google-reviews-plugin'); ?></option>
                            <option value="overlay"><?php esc_html_e('Overlay QR', 'google-reviews-plugin'); ?></option>
                        </select>
                        <label>
                            <input type="checkbox" id="grp-modal-glass-effect">
                            <?php esc_html_e('Glass Effect', 'google-reviews-plugin'); ?>
                        </label>
                    </div>
                </div>
                <div id="grp-template-editor-gradient-section" class="grp-template-editor-section">
                    <strong><?php esc_html_e('Gradient colors for Creative (Pro)', 'google-reviews-plugin'); ?></strong>
                    <label><?php esc_html_e('Gradient Start', 'google-reviews-plugin'); ?>
                        <input type="color" id="grp-modal-gradient-start">
                        <input type="text" id="grp-modal-gradient-start-text" style="width: 90px; margin-left: 8px;">
                    </label>
                    <label><?php esc_html_e('Gradient End', 'google-reviews-plugin'); ?>
                        <input type="color" id="grp-modal-gradient-end">
                        <input type="text" id="grp-modal-gradient-end-text" style="width: 90px; margin-left: 8px;">
                    </label>
                </div>
                <div class="grp-template-editor-footer">
                    <button type="button" class="button" id="grp-template-editor-close"><?php esc_html_e('Done', 'google-reviews-plugin'); ?></button>
                </div>
            </div>
        </div>
        
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
                <li><code>template</code> - <?php esc_html_e('Layout template: basic, qr-badge, google-card, creative-pro (Pro only).', 'google-reviews-plugin'); ?></li>
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

    var $boxShadowEdit = $('#grp-box-shadow-edit');
    var $boxShadowModal = $('#grp-box-shadow-modal');
    var $boxShadowClose = $('#grp-box-shadow-modal-close');
    var $boxShadowRanges = $('.grp-shadow-range');
    var $boxShadowNumbers = $('.grp-shadow-number');
    var $boxShadowColorPicker = $('#grp-box-shadow-color-picker');
    var $hiddenH = $('#grp_widget_template_box_shadow_h');
    var $hiddenV = $('#grp_widget_template_box_shadow_v');
    var $hiddenBlur = $('#grp_widget_template_box_shadow_blur');
    var $hiddenSpread = $('#grp_widget_template_box_shadow_spread');
    var $hiddenColor = $('#grp_widget_template_box_shadow_color');
    var $hiddenValue = $('#grp_widget_template_box_shadow_value');

    function updateBoxShadowString() {
        var h = $hiddenH.val() || 0;
        var v = $hiddenV.val() || 0;
        var blur = $hiddenBlur.val() || 0;
        var spread = $hiddenSpread.val() || 0;
        var color = $hiddenColor.val() || '#000000';
        $hiddenValue.val(h + ' ' + v + ' ' + blur + ' ' + spread + ' ' + color);
    }

    function syncControl(target, value) {
        $boxShadowRanges.filter('[data-target="' + target + '"]').val(value);
        $boxShadowNumbers.filter('[data-target="' + target + '"]').val(value);
        if (target === 'h') $hiddenH.val(value);
        if (target === 'v') $hiddenV.val(value);
        if (target === 'blur') $hiddenBlur.val(value);
        if (target === 'spread') $hiddenSpread.val(value);
        updateBoxShadowString();
    }

    $boxShadowRanges.on('input', function() {
        var target = $(this).data('target');
        syncControl(target, $(this).val());
    });

    $boxShadowNumbers.on('input', function() {
        var target = $(this).data('target');
        syncControl(target, $(this).val());
    });

    $boxShadowColorPicker.on('input', function() {
        $hiddenColor.val($(this).val());
        updateBoxShadowString();
    });

    $boxShadowEdit.on('click', function() {
        $boxShadowModal.show();
        syncControl('h', $hiddenH.val());
        syncControl('v', $hiddenV.val());
        syncControl('blur', $hiddenBlur.val());
        syncControl('spread', $hiddenSpread.val());
        $boxShadowColorPicker.val($hiddenColor.val());
    });

    $boxShadowClose.on('click', function() {
        $boxShadowModal.hide();
    });

    $(window).on('click', function(e) {
        if ($boxShadowModal.is(e.target)) {
            $boxShadowModal.hide();
        }
    });
});
</script>

