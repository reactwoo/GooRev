<?php
/**
 * Review Request Widgets Class
 * Handles QR codes, review buttons, and on-site widgets to request reviews
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Review_Widgets {
    
    /**
     * Instance
     */
    private static $instance = null;
	    
	    /**
	     * Button templates
	     */
	    private $button_templates = array();
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_button_templates();
        // Hook into addon enable/disable actions
        add_action('grp_addon_enabled', array($this, 'handle_addon_enabled'));
        add_action('grp_addon_disabled', array($this, 'handle_addon_disabled'));
        
        $this->init_hooks();
    }
    
    /**
     * Handle addon enabled action
     */
    public function handle_addon_enabled($slug) {
        if ($slug === 'review-widgets') {
            // Create database table if needed
            $this->maybe_create_tables();
            // Flag to flush rewrite rules on next page load
            update_option('grp_widgets_flush_rewrite_rules', '1');
        }
    }
    
    /**
     * Handle addon disabled action
     */
    public function handle_addon_disabled($slug) {
        if ($slug === 'review-widgets') {
            // Cleanup if needed
        }
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Always register admin menu hook (so we can check addon status inside)
        // Use priority 20 to ensure parent menu 'google-reviews' exists (registered by GRP_Admin at priority 10)
        if (is_admin()) {
            add_action('admin_menu', array($this, 'add_admin_menu'), 20);
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        }
        
        // Always register AJAX endpoints (they check addon status internally)
        add_action('wp_ajax_grp_generate_qr', array($this, 'ajax_generate_qr'));
        add_action('wp_ajax_nopriv_grp_generate_qr', array($this, 'ajax_generate_qr'));
        add_action('wp_ajax_grp_track_widget_click', array($this, 'ajax_track_widget_click'));
        add_action('wp_ajax_nopriv_grp_track_widget_click', array($this, 'ajax_track_widget_click'));
        
        // Check if addon is enabled for frontend/admin functionality
        $addons = GRP_Addons::get_instance();
        if (!$addons->is_addon_enabled('review-widgets')) {
            return;
        }
        
        // Register shortcodes
        add_shortcode('grp_review_button', array($this, 'render_review_button_shortcode'));
        add_shortcode('grp_review_qr', array($this, 'render_qr_code_shortcode'));
        
        // Register redirect endpoint for click tracking
        add_action('init', array($this, 'register_redirect_endpoint'));
        add_action('template_redirect', array($this, 'handle_review_redirect'));
        
        // Enqueue frontend assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
    }
    
    /**
     * Create database tables if needed
     */
    private function maybe_create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Widget clicks tracking table
        $clicks_table = $wpdb->prefix . 'grp_widget_clicks';
        
        $sql = "CREATE TABLE IF NOT EXISTS $clicks_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            widget_type varchar(50) NOT NULL,
            widget_id varchar(100) DEFAULT NULL,
            review_url text NOT NULL,
            clicked_at datetime DEFAULT CURRENT_TIMESTAMP,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            referrer text DEFAULT NULL,
            converted tinyint(1) DEFAULT 0,
            converted_at datetime DEFAULT NULL,
            meta longtext DEFAULT NULL,
            PRIMARY KEY (id),
            KEY widget_type (widget_type),
            KEY widget_id (widget_id),
            KEY clicked_at (clicked_at),
            KEY converted (converted)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Always register the menu item (so page exists), but render function will check if addon is enabled
        $hook = add_submenu_page(
            'google-reviews',
            __('Review Widgets', 'google-reviews-plugin'),
            __('Review Widgets', 'google-reviews-plugin'),
            'manage_options',
            'google-reviews-widgets',
            array($this, 'render_widgets_page')
        );
        
        // Debug: Log menu registration (remove in production)
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[GRP Review Widgets] Menu registered with hook: ' . $hook);
        }
    }
    
    /**
     * Initialize button templates
     */
    private function init_button_templates() {
        $this->button_templates = array(
            'basic' => array(
                'name' => __('Clean Button', 'google-reviews-plugin'),
                'description' => __('Simple button with icon and text.', 'google-reviews-plugin'),
                'type' => 'button',
                'pro' => false,
                'qr' => false,
            ),
            'layout1' => array(
                'name' => __('Layout 1', 'google-reviews-plugin'),
                'description' => __('Horizontal card with QR to the left and Google branding to the right.', 'google-reviews-plugin'),
                'type' => 'layout1',
                'pro' => false,
                'qr' => true,
                'stars' => true,
                'subtitle' => __('Scan the QR code to leave a review!', 'google-reviews-plugin'),
                'underline_colors' => array('#4285f4', '#ea4335', '#fbbc05'),
            ),
            'layout2' => array(
                'name' => __('Layout 2', 'google-reviews-plugin'),
                'description' => __('Stacked light card with Google G, stars, instructions, and QR art.', 'google-reviews-plugin'),
                'type' => 'layout2',
                'pro' => false,
                'qr' => true,
                'stars' => true,
                'subtitle' => __('Scan the QR code below to leave a review!', 'google-reviews-plugin'),
                'underline_colors' => array('#4285f4', '#f4b400', '#0f9d58'),
                'link_text' => __('www.google.com', 'google-reviews-plugin'),
            ),
            'layout3' => array(
                'name' => __('Layout 3', 'google-reviews-plugin'),
                'description' => __('Stacked dark card with the same elements plus a colorful underline.', 'google-reviews-plugin'),
                'type' => 'layout2',
                'dark' => true,
                'pro' => false,
                'qr' => true,
                'stars' => true,
                'subtitle' => __('Tap or scan to leave a review!', 'google-reviews-plugin'),
                'underline_colors' => array('#4285f4', '#ea4335', '#0f9d58'),
                'link_text' => __('www.google.com', 'google-reviews-plugin'),
            ),
            'creative-pro' => array(
                'name' => __('Creative (Pro Only)', 'google-reviews-plugin'),
                'description' => __('Premium gradient card with Google-inspired branding and extra controls.', 'google-reviews-plugin'),
                'type' => 'card',
                'pro' => true,
                'qr' => true,
                'stars' => true,
                'subtitle' => __('Scan the QR code below or tap to review instantly.', 'google-reviews-plugin'),
                'link_text' => __('www.google.com', 'google-reviews-plugin'),
            ),
        );
    }

    /**
     * Get all registered button templates
     */
    public function get_button_templates() {
        return $this->button_templates;
    }

    /**
     * Get template metadata
     */
    public function get_button_template($key) {
        return isset($this->button_templates[$key]) ? $this->button_templates[$key] : false;
    }

    /**
     * Sanitize template key and fallback to default if invalid
     */
    public function sanitize_button_template($key) {
        $key = sanitize_title($key);
        if (isset($this->button_templates[$key])) {
            if (!empty($this->button_templates[$key]['pro'])) {
                $license = new GRP_License();
                if (!$license->is_pro()) {
                    return 'basic';
                }
            }
            return $key;
        }
        return 'basic';
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'google-reviews_page_google-reviews-widgets') {
            return;
        }
        
        wp_enqueue_style('grp-widgets-admin', GRP_PLUGIN_URL . 'assets/css/widgets-admin.css', array(), GRP_PLUGIN_VERSION);
        // Also load frontend styles in admin for preview
        wp_enqueue_style('grp-review-widgets', GRP_PLUGIN_URL . 'assets/css/review-widgets.css', array(), GRP_PLUGIN_VERSION);
        wp_enqueue_script('grp-widgets-admin', GRP_PLUGIN_URL . 'assets/js/widgets-admin.js', array('jquery'), GRP_PLUGIN_VERSION, true);
        
        $place_id = get_option('grp_place_id', '');
        $place_id_auto = get_option('grp_gbp_place_id_default', '');
        $has_place_id = !empty($place_id) || !empty($place_id_auto);
        $license = new GRP_License();

        wp_localize_script('grp-widgets-admin', 'grpWidgets', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('grp_widgets_nonce'),
            'tracking_enabled' => get_option('grp_widget_tracking_enabled', true),
            'has_place_id' => $has_place_id,
            'is_pro' => $license->is_pro(),
            'button_templates' => $this->get_button_templates(),
            'logo_urls' => array(
                'icon' => GRP_PLUGIN_URL . 'assets/images/google-icon.svg',
                'classic' => GRP_PLUGIN_URL . 'assets/images/google-wordmark.svg',
            ),
            'strings' => array(
                'templateProMessage' => __('Upgrade to Pro to unlock this layout.', 'google-reviews-plugin'),
                'qrPreviewError' => __('Unable to refresh the QR preview. Please try again.', 'google-reviews-plugin'),
            ),
        ));
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style('grp-review-widgets', GRP_PLUGIN_URL . 'assets/css/review-widgets.css', array(), GRP_PLUGIN_VERSION);
        wp_enqueue_script('grp-review-widgets', GRP_PLUGIN_URL . 'assets/js/review-widgets.js', array('jquery'), GRP_PLUGIN_VERSION, true);
        
        $tracking_enabled = get_option('grp_widget_tracking_enabled', true);
        
        wp_localize_script('grp-review-widgets', 'grpReviewWidgets', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('grp_widgets_nonce'),
            'tracking_enabled' => $tracking_enabled,
        ));
    }
    
    /**
     * Register redirect endpoint for click tracking
     */
    public function register_redirect_endpoint() {
        add_rewrite_rule('^grp-review-redirect/([^/]+)/?', 'index.php?grp_review_redirect=$matches[1]', 'top');
        add_rewrite_tag('%grp_review_redirect%', '([^&]+)');
    }
    
    /**
     * Handle review redirect with click tracking
     */
    public function handle_review_redirect() {
        // Check if this is our redirect endpoint
        $redirect_id = get_query_var('grp_review_redirect');
        if (empty($redirect_id)) {
            $redirect_id = isset($_GET['grp_review_redirect']) ? sanitize_text_field($_GET['grp_review_redirect']) : '';
        }
        
        if (!$redirect_id) {
            return;
        }
        
        // Decode redirect ID to get review URL and widget info
        $redirect_data = $this->decode_redirect_id($redirect_id);
        
        if (!$redirect_data || !isset($redirect_data['url'])) {
            wp_die(__('Invalid review link', 'google-reviews-plugin'));
        }
        
        // Track click
        $this->track_click($redirect_data);
        
        // Redirect to Google review URL
        wp_redirect($redirect_data['url'], 302);
        exit;
    }
    
    /**
     * Track widget click
     */
    private function track_click($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'grp_widget_clicks';
        
        $wpdb->insert(
            $table,
            array(
                'widget_type' => isset($data['type']) ? $data['type'] : 'unknown',
                'widget_id' => isset($data['widget_id']) ? $data['widget_id'] : null,
                'review_url' => $data['url'],
                'ip_address' => $this->get_client_ip(),
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
                'referrer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
                'meta' => json_encode($data),
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Get client IP address
     */
    private function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }
    
    /**
     * Generate redirect ID
     */
    private function generate_redirect_id($data) {
        $encoded = base64_encode(json_encode($data));
        return urlencode($encoded);
    }
    
    /**
     * Decode redirect ID
     */
    private function decode_redirect_id($id) {
        $decoded = base64_decode(urldecode($id));
        return json_decode($decoded, true);
    }
    
    /**
     * Generate review URL with tracking
     */
    public function generate_review_url($place_id = '', $widget_type = 'button', $widget_id = null) {
        // Get Place ID from settings if not provided
        if (empty($place_id)) {
            $place_id = get_option('grp_place_id', '');
            if (empty($place_id)) {
                $place_id = get_option('grp_gbp_place_id_default', '');
            }
        }
        
        if (empty($place_id)) {
            return '';
        }
        
        // Generate Google review URL
        $review_url = 'https://search.google.com/local/writereview?placeid=' . urlencode($place_id);
        
        // Create tracking redirect
        $redirect_data = array(
            'url' => $review_url,
            'type' => $widget_type,
            'widget_id' => $widget_id,
            'place_id' => $place_id,
        );
        
        $redirect_id = $this->generate_redirect_id($redirect_data);
        $tracking_url = home_url('/grp-review-redirect/' . $redirect_id);
        
        return $tracking_url;
    }
    
    /**
     * Generate QR code
     */
    public function generate_qr_code($url, $size = 200) {
        // Use QRServer API (Google Charts API was deprecated in Jan 2024)
        // QRServer is free, reliable, and supports long URLs
        $encoded_url = urlencode($url);
        return 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . $encoded_url . '&format=png';
    }
    
    /**
     * Render review button shortcode
     */
    public function render_review_button_shortcode($atts) {
        $default_template = get_option('grp_widget_button_default_template', 'basic');
        $atts = shortcode_atts(array(
            'text' => __('Leave us a review', 'google-reviews-plugin'),
            'style' => 'default',
            'size' => 'medium',
            'color' => '',
            'bg_color' => '',
            'align' => 'left',
            'place_id' => '',
            'template' => $default_template,
            'star_color' => '',
            'star_placement' => '',
            'show_logo' => '',
            'font_family' => '',
            'max_height' => '',
            'gradient_start' => '',
            'gradient_end' => '',
            'link_color' => '',
        ), $atts, 'grp_review_button');

        $template = $this->sanitize_button_template($atts['template']);
        $review_url = $this->generate_review_url($atts['place_id'], 'button', 'shortcode');

        if (empty($review_url)) {
            return '<p>' . __('Place ID not configured. Please set your Place ID in settings.', 'google-reviews-plugin') . '</p>';
        }

        $logo_icon_url = GRP_PLUGIN_URL . 'assets/images/google-icon-logo-svgrepo-com.svg';
        $logo_full_url = GRP_PLUGIN_URL . 'assets/images/google-2015-logo-svgrepo-com.svg';
        $stored_max_width = absint(get_option('grp_widget_template_max_width', 0));
        $box_shadow_enabled = get_option('grp_widget_template_box_shadow_enabled', true);
        $box_shadow_value = get_option('grp_widget_template_box_shadow_value', '0 18px 35px rgba(0, 0, 0, 0.25)');
        $glass_effect = get_option('grp_widget_template_glass_effect', false);

        $template_data = $this->get_button_template($template);
        if (!$template_data) {
            $template = 'basic';
            $template_data = $this->get_button_template($template);
        }

        $license = new GRP_License();
        $is_pro = $license->is_pro();
        if (!empty($template_data['pro']) && !$is_pro) {
            $template = 'basic';
            $template_data = $this->get_button_template($template);
        }

        $stored_star_color = sanitize_hex_color(get_option('grp_widget_template_star_color', '#FBBD05'));
        $stored_star_placement = get_option('grp_widget_template_star_placement', 'below');
        $stored_show_logo = get_option('grp_widget_template_show_logo', true);
        $stored_font_family = get_option('grp_widget_template_font_family', '');
        $stored_max_height = absint(get_option('grp_widget_template_max_height', 0));
        $stored_gradient_start = sanitize_hex_color(get_option('grp_widget_template_gradient_start', '#24a1ff'));
        $stored_gradient_end = sanitize_hex_color(get_option('grp_widget_template_gradient_end', '#ff7b5a'));
        $stored_link_color = sanitize_hex_color(get_option('grp_widget_template_link_color', '#111111'));
        $stored_link_text = sanitize_text_field(get_option('grp_widget_template_link_text', __('Click here', 'google-reviews-plugin')));
        if ($stored_link_text === '') {
            $stored_link_text = __('Click here', 'google-reviews-plugin');
        }
        $stored_logo_scale = absint(get_option('grp_widget_template_logo_scale', 50));
        if ($stored_logo_scale < 10) {
            $stored_logo_scale = 10;
        }
        if ($stored_logo_scale > 100) {
            $stored_logo_scale = 100;
        }
        $logo_scale_attr = ' style="width: ' . esc_attr($stored_logo_scale) . '%;"';

        $star_color = sanitize_hex_color($atts['star_color']) ?: $stored_star_color;
        if (!$star_color) {
            $star_color = '#FBBD05';
        }

        $allowed_placements = array('above', 'below', 'overlay');
        $star_placement = in_array($atts['star_placement'], $allowed_placements, true) ? $atts['star_placement'] : $stored_star_placement;
        if (!in_array($star_placement, $allowed_placements, true)) {
            $star_placement = 'below';
        }

        if ($atts['show_logo'] !== '') {
            $show_logo = filter_var($atts['show_logo'], FILTER_VALIDATE_BOOLEAN);
        } else {
            $show_logo = (bool) $stored_show_logo;
        }

        $font_family = $atts['font_family'] ?: $stored_font_family;
        $max_height = $atts['max_height'] !== '' ? absint($atts['max_height']) : $stored_max_height;
        $gradient_start = sanitize_hex_color($atts['gradient_start']) ?: $stored_gradient_start;
        $gradient_end = sanitize_hex_color($atts['gradient_end']) ?: $stored_gradient_end;
        if (!$gradient_start) {
            $gradient_start = '#24a1ff';
        }
        if (!$gradient_end) {
            $gradient_end = '#ff7b5a';
        }
        $link_color = sanitize_hex_color($atts['link_color']) ?: $stored_link_color;
        if (!$link_color) {
            $link_color = '#111111';
        }

        $classes = array('grp-review-button', 'grp-review-button-' . $atts['style'], 'grp-review-button-' . $atts['size']);
        if ($atts['align'] !== 'left') {
            $classes[] = 'grp-align-' . $atts['align'];
        }
        $classes[] = 'grp-review-button-template-' . sanitize_title($template);
        $classes[] = 'grp-star-placement-' . $star_placement;

        $inline_styles = array();
        if (!empty($atts['color'])) {
            $inline_styles[] = 'color: ' . esc_attr($atts['color']);
        }
        if (!empty($atts['bg_color'])) {
            $inline_styles[] = 'background-color: ' . esc_attr($atts['bg_color']);
        }
        if (!empty($font_family)) {
            $inline_styles[] = 'font-family: ' . esc_attr($font_family);
        }
        if ($max_height > 0) {
            $inline_styles[] = 'max-height: ' . $max_height . 'px';
        }
        if ($stored_max_width > 0) {
            $inline_styles[] = 'max-width: ' . $stored_max_width . 'px';
        }
        if ($box_shadow_enabled && !empty($box_shadow_value)) {
            $inline_styles[] = 'box-shadow: ' . esc_attr($box_shadow_value);
        }
        if ($glass_effect) {
            $classes[] = 'grp-glass-effect';
        }

        $is_card = isset($template_data['type']) && $template_data['type'] === 'card';
        if ($is_card && $template === 'creative-pro') {
            $inline_styles[] = 'background: linear-gradient(135deg, ' . esc_attr($gradient_start) . ', ' . esc_attr($gradient_end) . ')';
        }

        $style_attr = '';
        if (!empty($inline_styles)) {
            $style_attr = ' style="' . implode('; ', $inline_styles) . '"';
        }

        $star_text = implode(' ', array_fill(0, 5, '★'));
        $button_star_html = '';
        if (!empty($template_data['stars'])) {
            $button_star_html = '<span class="grp-review-button-star-row" style="color: ' . esc_attr($star_color) . ';">' . esc_html($star_text) . '</span>';
        }
        $card_star_html = '';
        if (!empty($template_data['stars'])) {
            $card_star_html = '<div class="grp-card-stars" style="color: ' . esc_attr($star_color) . ';">' . esc_html($star_text) . '</div>';
        }

        $qr_image = '';
        if (!empty($template_data['qr']) && !empty($review_url)) {
            $qr_size = isset($template_data['qr_size']) ? absint($template_data['qr_size']) : 100;
            $qr_url = $this->generate_qr_code($review_url, $qr_size);
            $qr_image = '<img src="' . esc_url($qr_url) . '" alt="' . esc_attr__('Scan to leave a review', 'google-reviews-plugin') . '">';
        }
        $qr_frame = $qr_image ? '<div class="grp-qr-frame">' . $qr_image . '</div>' : '';

        $tagline = !empty($template_data['tagline']) ? $template_data['tagline'] : '';
        $subtitle = !empty($template_data['subtitle']) ? $template_data['subtitle'] : __('Scan the QR code below to leave a review!', 'google-reviews-plugin');
        $link_text = !empty($template_data['link_text']) ? $template_data['link_text'] : __('Click here', 'google-reviews-plugin');
        if ($stored_link_text) {
            $link_text = $stored_link_text;
        }
        $link_html = '<a href="' . esc_url($review_url) . '" target="_blank" rel="noopener" style="color: ' . esc_attr($link_color) . ';">' . esc_html($link_text) . '</a>';

        $button_html = '';
        if (isset($template_data['type']) && $template_data['type'] === 'layout1') {
            $underline_colors = isset($template_data['underline_colors']) ? $template_data['underline_colors'] : array('#4285f4', '#ea4335', '#fbbc05');
            $button_html .= '<div class="grp-review-button-wrapper grp-layout-wrapper">';
            $button_html .= '<a href="' . esc_url($review_url) . '" class="' . esc_attr(implode(' ', $classes)) . ' grp-layout1"' . $style_attr . '>';
            $button_html .= '<div class="grp-layout1-qr">' . $qr_frame . '</div>';
            $button_html .= '<div class="grp-layout1-details">';
            if ($show_logo && !empty($logo_icon_url)) {
                $button_html .= '<img src="' . esc_url($logo_icon_url) . '" alt="Google logo" class="grp-layout1-logo-img"' . $logo_scale_attr . '>';
            }
            $button_html .= '<div class="grp-layout1-stars" style="color: ' . esc_attr($star_color) . ';">' . esc_html($star_text) . '</div>';
            $button_html .= '<div class="grp-layout1-title">' . esc_html($atts['text']) . '</div>';
            $button_html .= '<div class="grp-layout1-subtitle">' . esc_html($template_data['subtitle']) . '</div>';
            $button_html .= '<div class="grp-layout1-underline">';
            foreach ($underline_colors as $color) {
                $button_html .= '<span style="background: ' . esc_attr($color) . ';"></span>';
            }
            $button_html .= '</div>';
            if (!empty($link_html)) {
                $button_html .= '<div class="grp-layout1-link">' . $link_html . '</div>';
            }
            $button_html .= '</div>';
            $button_html .= '</a>';
            $button_html .= '</div>';
        } elseif (isset($template_data['type']) && $template_data['type'] === 'layout2') {
            $dark_class = !empty($template_data['dark']) ? ' grp-layout-dark' : '';
            $underline_colors = isset($template_data['underline_colors']) ? $template_data['underline_colors'] : array('#4285f4', '#ea4335', '#fbbc05');
            $button_html .= '<div class="grp-review-button-wrapper grp-layout-wrapper">';
            $button_html .= '<a href="' . esc_url($review_url) . '" class="' . esc_attr(implode(' ', $classes)) . ' grp-layout2' . $dark_class . '"' . $style_attr . '>';
            if ($show_logo && !empty($logo_full_url)) {
                $button_html .= '<img src="' . esc_url($logo_full_url) . '" alt="Google logo" class="grp-layout2-logo-img"' . $logo_scale_attr . '>';
            }
            $button_html .= '<div class="grp-layout2-stars" style="color: ' . esc_attr($star_color) . ';">' . esc_html($star_text) . '</div>';
            $button_html .= '<div class="grp-layout2-heading">' . esc_html($atts['text']) . '</div>';
            $button_html .= '<div class="grp-layout2-subtitle">' . esc_html($template_data['subtitle']) . '</div>';
            $button_html .= '<div class="grp-layout2-qr">' . $qr_frame . '</div>';
            $button_html .= '<div class="grp-layout2-link">' . $link_html . '</div>';
            $button_html .= '<div class="grp-layout2-underline">';
            foreach ($underline_colors as $color) {
                $button_html .= '<span style="background: ' . esc_attr($color) . ';"></span>';
            }
            $button_html .= '</div>';
            $button_html .= '</a>';
            $button_html .= '</div>';
        } elseif ($is_card) {
            $card_inner = '<div class="grp-review-card">';
            if ($show_logo && !empty($logo_full_url)) {
                $card_inner .= '<img src="' . esc_url($logo_full_url) . '" alt="Google logo" class="grp-card-logo-img"' . $logo_scale_attr . '>';
            }
            $card_inner .= $card_star_html;
            $card_inner .= '<div class="grp-card-heading">' . esc_html($atts['text']) . '</div>';
            if ($tagline) {
                $card_inner .= '<div class="grp-card-subtitle">' . esc_html($tagline) . '</div>';
            } else {
                $card_inner .= '<div class="grp-card-subtitle">' . esc_html($subtitle) . '</div>';
            }
            if ($qr_frame) {
                $card_inner .= '<div class="grp-card-qr">' . $qr_frame . '</div>';
            }
            $card_inner .= '<div class="grp-card-link">' . $link_html . '</div>';
            $card_inner .= '</div>';

            $button_html .= '<div class="grp-review-button-wrapper grp-card-wrapper">';
            $button_html .= '<a href="' . esc_url($review_url) . '" class="' . esc_attr(implode(' ', $classes)) . '" target="_blank" rel="noopener"' . $style_attr . '>';
            $button_html .= $card_inner;
            $button_html .= '</a>';
            $button_html .= '</div>';
        } else {
            $qr_html = $qr_frame ? '<span class="grp-review-button-qr">' . $qr_frame . '</span>' : '';
            $tagline_html = '';
            if (!empty($tagline)) {
                $tagline_html = '<span class="grp-review-button-tagline">' . esc_html($tagline) . '</span>';
            }

            $button_html .= '<div class="grp-review-button-wrapper">';
            $button_html .= '<a href="' . esc_url($review_url) . '" class="' . esc_attr(implode(' ', $classes)) . '" target="_blank" rel="noopener"' . $style_attr . '>';
            $button_html .= $button_star_html;
            $button_html .= $qr_html;
            $button_html .= '<div class="grp-review-button-content">';
            if ($show_logo) {
                $button_html .= '<span class="grp-review-button-icon">⭐</span>';
            }
            $button_html .= '<span class="grp-review-button-text">' . esc_html($atts['text']) . '</span>';
            $button_html .= $tagline_html;
            $button_html .= '</div>';
            $button_html .= '</a>';
            $button_html .= '</div>';
        }

        return $button_html;
    }
    
    /**
     * Render QR code shortcode
     */
    public function render_qr_code_shortcode($atts) {
        $atts = shortcode_atts(array(
            'size' => '200',
            'place_id' => '',
            'caption' => '',
        ), $atts, 'grp_review_qr');
        
        $review_url = $this->generate_review_url($atts['place_id'], 'qr', 'shortcode');
        
        if (empty($review_url)) {
            return '<p>' . __('Place ID not configured. Please set your Place ID in settings.', 'google-reviews-plugin') . '</p>';
        }
        
        $size = absint($atts['size']);
        $qr_url = $this->generate_qr_code($review_url, $size);
        
        $html = '<div class="grp-qr-code-wrapper">';
        $html .= '<div class="grp-qr-frame">';
        $html .= '<img src="' . esc_url($qr_url) . '" alt="' . esc_attr__('Scan to leave a review', 'google-reviews-plugin') . '" class="grp-qr-code" style="width: ' . $size . 'px; height: ' . $size . 'px;">';
        $html .= '</div>';
        if (!empty($atts['caption'])) {
            $html .= '<p class="grp-qr-caption">' . esc_html($atts['caption']) . '</p>';
        }
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * AJAX: Generate QR code
     */
    public function ajax_generate_qr() {
        check_ajax_referer('grp_widgets_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions', 'google-reviews-plugin'));
        }
        
        $place_id = isset($_POST['place_id']) ? sanitize_text_field($_POST['place_id']) : '';
        $size = isset($_POST['size']) ? absint($_POST['size']) : 200;
        
        if (empty($place_id)) {
            // Try to get from settings
            $place_id = get_option('grp_place_id', '');
            if (empty($place_id)) {
                $place_id = get_option('grp_gbp_place_id_default', '');
            }
        }
        
        if (empty($place_id)) {
            wp_send_json_error(__('Place ID is required. Please set your Place ID in Settings.', 'google-reviews-plugin'));
        }
        
        $review_url = $this->generate_review_url($place_id, 'qr', 'admin');
        $qr_url = $this->generate_qr_code($review_url, $size);
        
        wp_send_json_success(array(
            'qr_url' => $qr_url,
            'review_url' => $review_url,
        ));
    }
    
    /**
     * Render widgets settings page
     */
    public function render_widgets_page() {
        // WordPress already checks manage_options capability via add_submenu_page, but double-check for safety
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'google-reviews-plugin'), '', array('response' => 403));
        }
        
        // Check if addon is enabled
        $addons = GRP_Addons::get_instance();
        if (!$addons->is_addon_enabled('review-widgets')) {
            // Show a friendly message instead of wp_die
            echo '<div class="wrap">';
            echo '<h1>' . esc_html__('Review Request Widgets', 'google-reviews-plugin') . '</h1>';
            echo '<div class="notice notice-warning"><p>';
            echo '<strong>' . esc_html__('Review Widgets addon is not enabled.', 'google-reviews-plugin') . '</strong><br>';
            echo esc_html__('Please enable it from the', 'google-reviews-plugin') . ' ';
            echo '<a href="' . esc_url(admin_url('admin.php?page=google-reviews-addons')) . '">';
            echo esc_html__('Addons page', 'google-reviews-plugin');
            echo '</a>.';
            echo '</p></div>';
            echo '</div>';
            return;
        }
        
        include GRP_PLUGIN_DIR . 'includes/admin/views/widgets.php';
    }
}

