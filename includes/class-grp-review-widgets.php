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
        // Check if addon is enabled
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
        
        // AJAX endpoints
        add_action('wp_ajax_grp_generate_qr', array($this, 'ajax_generate_qr'));
        add_action('wp_ajax_nopriv_grp_generate_qr', array($this, 'ajax_generate_qr'));
        
        // Admin hooks
        if (is_admin()) {
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        }
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
        $addons = GRP_Addons::get_instance();
        if (!$addons->is_addon_enabled('review-widgets')) {
            return;
        }
        
        add_submenu_page(
            'google-reviews',
            __('Review Widgets', 'google-reviews-plugin'),
            __('Review Widgets', 'google-reviews-plugin'),
            'manage_options',
            'google-reviews-widgets',
            array($this, 'render_widgets_page')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'google-reviews_page_google-reviews-widgets') {
            return;
        }
        
        wp_enqueue_style('grp-widgets-admin', GRP_PLUGIN_URL . 'assets/css/widgets-admin.css', array(), GRP_PLUGIN_VERSION);
        wp_enqueue_script('grp-widgets-admin', GRP_PLUGIN_URL . 'assets/js/widgets-admin.js', array('jquery'), GRP_PLUGIN_VERSION, true);
        
        wp_localize_script('grp-widgets-admin', 'grpWidgets', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('grp_widgets_nonce'),
        ));
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style('grp-review-widgets', GRP_PLUGIN_URL . 'assets/css/review-widgets.css', array(), GRP_PLUGIN_VERSION);
        wp_enqueue_script('grp-review-widgets', GRP_PLUGIN_URL . 'assets/js/review-widgets.js', array('jquery'), GRP_PLUGIN_VERSION, true);
        
        wp_localize_script('grp-review-widgets', 'grpReviewWidgets', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('grp_widgets_nonce'),
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
        $redirect_id = get_query_var('grp_review_redirect');
        
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
        // Use Google Charts API for QR code generation (free, no library needed)
        $encoded_url = urlencode($url);
        return 'https://chart.googleapis.com/chart?chs=' . $size . 'x' . $size . '&cht=qr&chl=' . $encoded_url;
    }
    
    /**
     * Render review button shortcode
     */
    public function render_review_button_shortcode($atts) {
        $atts = shortcode_atts(array(
            'text' => __('Leave us a review', 'google-reviews-plugin'),
            'style' => 'default',
            'size' => 'medium',
            'color' => '',
            'bg_color' => '',
            'align' => 'left',
            'place_id' => '',
        ), $atts, 'grp_review_button');
        
        $review_url = $this->generate_review_url($atts['place_id'], 'button', 'shortcode');
        
        if (empty($review_url)) {
            return '<p>' . __('Place ID not configured. Please set your Place ID in settings.', 'google-reviews-plugin') . '</p>';
        }
        
        // Check if Pro features are available
        $license = new GRP_License();
        $is_pro = $license->is_pro();
        
        // Build button classes
        $classes = array('grp-review-button', 'grp-review-button-' . $atts['style'], 'grp-review-button-' . $atts['size']);
        if ($atts['align'] !== 'left') {
            $classes[] = 'grp-align-' . $atts['align'];
        }
        
        // Build inline styles
        $styles = array();
        if (!empty($atts['color'])) {
            $styles[] = 'color: ' . esc_attr($atts['color']);
        }
        if (!empty($atts['bg_color'])) {
            $styles[] = 'background-color: ' . esc_attr($atts['bg_color']);
        }
        $style_attr = !empty($styles) ? ' style="' . implode('; ', $styles) . '"' : '';
        
        $button_html = '<div class="grp-review-button-wrapper">';
        $button_html .= '<a href="' . esc_url($review_url) . '" class="' . esc_attr(implode(' ', $classes)) . '" target="_blank" rel="noopener"' . $style_attr . '>';
        $button_html .= '<span class="grp-review-button-icon">⭐</span>';
        $button_html .= '<span class="grp-review-button-text">' . esc_html($atts['text']) . '</span>';
        $button_html .= '</a>';
        $button_html .= '</div>';
        
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
        $html .= '<img src="' . esc_url($qr_url) . '" alt="' . esc_attr__('Scan to leave a review', 'google-reviews-plugin') . '" class="grp-qr-code" style="width: ' . $size . 'px; height: ' . $size . 'px;">';
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
            wp_send_json_error(__('Place ID is required', 'google-reviews-plugin'));
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
        include GRP_PLUGIN_DIR . 'includes/admin/views/widgets.php';
    }
}

