<?php
/**
 * Admin interface class
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Admin {
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_grp_test_connection', array($this, 'ajax_test_connection'));
        add_action('wp_ajax_grp_sync_reviews', array($this, 'ajax_sync_reviews'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Google Reviews', 'google-reviews-plugin'),
            __('Google Reviews', 'google-reviews-plugin'),
            'manage_options',
            'google-reviews',
            array($this, 'render_dashboard_page'),
            'dashicons-star-filled',
            30
        );
        
        add_submenu_page(
            'google-reviews',
            __('Dashboard', 'google-reviews-plugin'),
            __('Dashboard', 'google-reviews-plugin'),
            'manage_options',
            'google-reviews',
            array($this, 'render_dashboard_page')
        );
        
        add_submenu_page(
            'google-reviews',
            __('Settings', 'google-reviews-plugin'),
            __('Settings', 'google-reviews-plugin'),
            'manage_options',
            'google-reviews-settings',
            array($this, 'render_settings_page')
        );
        
        add_submenu_page(
            'google-reviews',
            __('Reviews', 'google-reviews-plugin'),
            __('Reviews', 'google-reviews-plugin'),
            'manage_options',
            'google-reviews-reviews',
            array($this, 'render_reviews_page')
        );
        
        add_submenu_page(
            'google-reviews',
            __('Styles', 'google-reviews-plugin'),
            __('Styles', 'google-reviews-plugin'),
            'manage_options',
            'google-reviews-styles',
            array($this, 'render_styles_page')
        );
        
        add_submenu_page(
            'google-reviews',
            __('Help', 'google-reviews-plugin'),
            __('Help', 'google-reviews-plugin'),
            'manage_options',
            'google-reviews-help',
            array($this, 'render_help_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('grp_settings', 'grp_settings');
        
        // Google API settings
        add_settings_section(
            'grp_google_api',
            __('Google API Settings', 'google-reviews-plugin'),
            array($this, 'render_google_api_section'),
            'grp_settings'
        );
        
        add_settings_field(
            'grp_google_client_id',
            __('Client ID', 'google-reviews-plugin'),
            array($this, 'render_client_id_field'),
            'grp_settings',
            'grp_google_api'
        );
        
        add_settings_field(
            'grp_google_client_secret',
            __('Client Secret', 'google-reviews-plugin'),
            array($this, 'render_client_secret_field'),
            'grp_settings',
            'grp_google_api'
        );
        
        // Display settings
        add_settings_section(
            'grp_display',
            __('Display Settings', 'google-reviews-plugin'),
            array($this, 'render_display_section'),
            'grp_settings'
        );
        
        add_settings_field(
            'grp_default_style',
            __('Default Style', 'google-reviews-plugin'),
            array($this, 'render_default_style_field'),
            'grp_settings',
            'grp_display'
        );
        
        add_settings_field(
            'grp_default_count',
            __('Default Review Count', 'google-reviews-plugin'),
            array($this, 'render_default_count_field'),
            'grp_settings',
            'grp_display'
        );
        
        // Cache settings
        add_settings_section(
            'grp_cache',
            __('Cache Settings', 'google-reviews-plugin'),
            array($this, 'render_cache_section'),
            'grp_settings'
        );
        
        add_settings_field(
            'grp_cache_duration',
            __('Cache Duration (seconds)', 'google-reviews-plugin'),
            array($this, 'render_cache_duration_field'),
            'grp_settings',
            'grp_cache'
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'google-reviews') === false) {
            return;
        }
        
        wp_enqueue_style('grp-admin', GRP_PLUGIN_URL . 'assets/css/admin.css', array(), GRP_PLUGIN_VERSION);
        wp_enqueue_script('grp-admin', GRP_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), GRP_PLUGIN_VERSION, true);
        
        wp_localize_script('grp-admin', 'grp_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('grp_admin_nonce'),
            'strings' => array(
                'testing_connection' => __('Testing connection...', 'google-reviews-plugin'),
                'connection_success' => __('Connection successful!', 'google-reviews-plugin'),
                'connection_failed' => __('Connection failed. Please check your credentials.', 'google-reviews-plugin'),
                'syncing_reviews' => __('Syncing reviews...', 'google-reviews-plugin'),
                'sync_success' => __('Reviews synced successfully!', 'google-reviews-plugin'),
                'sync_failed' => __('Failed to sync reviews.', 'google-reviews-plugin'),
            )
        ));
    }
    
    /**
     * Render dashboard page
     */
    public function render_dashboard_page() {
        $api = new GRP_API();
        $reviews = new GRP_Reviews();
        $license = new GRP_License();
        
        $is_connected = $api->is_connected();
        $recent_reviews = $reviews->get_stored_reviews(array('limit' => 5));
        $is_pro = $license->is_pro();
        
        include GRP_PLUGIN_DIR . 'includes/admin/views/dashboard.php';
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        $api = new GRP_API();
        $license = new GRP_License();
        
        $is_connected = $api->is_connected();
        $is_pro = $license->is_pro();
        
        include GRP_PLUGIN_DIR . 'includes/admin/views/settings.php';
    }
    
    /**
     * Render reviews page
     */
    public function render_reviews_page() {
        $reviews = new GRP_Reviews();
        $license = new GRP_License();
        
        $all_reviews = $reviews->get_stored_reviews(array('limit' => 100));
        $is_pro = $license->is_pro();
        
        include GRP_PLUGIN_DIR . 'includes/admin/views/reviews.php';
    }
    
    /**
     * Render styles page
     */
    public function render_styles_page() {
        $styles = new GRP_Styles();
        $license = new GRP_License();
        
        $available_styles = $styles->get_styles();
        $is_pro = $license->is_pro();
        
        include GRP_PLUGIN_DIR . 'includes/admin/views/styles.php';
    }
    
    /**
     * Render help page
     */
    public function render_help_page() {
        $shortcode = new GRP_Shortcode();
        $license = new GRP_License();
        
        $shortcode_docs = $shortcode->get_shortcode_docs();
        $is_pro = $license->is_pro();
        
        include GRP_PLUGIN_DIR . 'includes/admin/views/help.php';
    }
    
    /**
     * Render Google API section
     */
    public function render_google_api_section() {
        echo '<p>' . __('Configure your Google My Business API credentials.', 'google-reviews-plugin') . '</p>';
    }
    
    /**
     * Render client ID field
     */
    public function render_client_id_field() {
        $value = get_option('grp_google_client_id', '');
        echo '<input type="text" name="grp_google_client_id" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . __('Enter your Google OAuth 2.0 Client ID.', 'google-reviews-plugin') . '</p>';
    }
    
    /**
     * Render client secret field
     */
    public function render_client_secret_field() {
        $value = get_option('grp_google_client_secret', '');
        echo '<input type="password" name="grp_google_client_secret" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . __('Enter your Google OAuth 2.0 Client Secret.', 'google-reviews-plugin') . '</p>';
    }
    
    /**
     * Render display section
     */
    public function render_display_section() {
        echo '<p>' . __('Configure default display settings for reviews.', 'google-reviews-plugin') . '</p>';
    }
    
    /**
     * Render default style field
     */
    public function render_default_style_field() {
        $styles = new GRP_Styles();
        $available_styles = $styles->get_styles();
        $value = get_option('grp_default_style', 'modern');
        
        echo '<select name="grp_default_style">';
        foreach ($available_styles as $key => $style) {
            echo '<option value="' . esc_attr($key) . '"' . selected($value, $key, false) . '>' . esc_html($style['name']) . '</option>';
        }
        echo '</select>';
    }
    
    /**
     * Render default count field
     */
    public function render_default_count_field() {
        $value = get_option('grp_default_count', 5);
        echo '<input type="number" name="grp_default_count" value="' . esc_attr($value) . '" min="1" max="50" />';
        echo '<p class="description">' . __('Default number of reviews to display.', 'google-reviews-plugin') . '</p>';
    }
    
    /**
     * Render cache section
     */
    public function render_cache_section() {
        echo '<p>' . __('Configure caching settings for better performance.', 'google-reviews-plugin') . '</p>';
    }
    
    /**
     * Render cache duration field
     */
    public function render_cache_duration_field() {
        $value = get_option('grp_cache_duration', 3600);
        echo '<input type="number" name="grp_cache_duration" value="' . esc_attr($value) . '" min="300" max="86400" />';
        echo '<p class="description">' . __('How long to cache reviews (in seconds).', 'google-reviews-plugin') . '</p>';
    }
    
    /**
     * AJAX handler for testing connection
     */
    public function ajax_test_connection() {
        check_ajax_referer('grp_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'google-reviews-plugin'));
        }
        
        $api = new GRP_API();
        $result = $api->test_connection();
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(__('Connection successful!', 'google-reviews-plugin'));
    }
    
    /**
     * AJAX handler for syncing reviews
     */
    public function ajax_sync_reviews() {
        check_ajax_referer('grp_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'google-reviews-plugin'));
        }
        
        $reviews = new GRP_Reviews();
        $result = $reviews->sync_reviews();
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(__('Reviews synced successfully!', 'google-reviews-plugin'));
    }
}