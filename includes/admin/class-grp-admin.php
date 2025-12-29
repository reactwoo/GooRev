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
        add_action('admin_init', array($this, 'maybe_handle_oauth'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_grp_test_connection', array($this, 'ajax_test_connection'));
        add_action('wp_ajax_grp_sync_reviews', array($this, 'ajax_sync_reviews'));
        add_action('admin_post_grp_disconnect', array($this, 'handle_disconnect'));
        // New AJAX endpoints for accounts/locations selection
        add_action('wp_ajax_grp_list_accounts', array($this, 'ajax_list_accounts'));
        add_action('wp_ajax_grp_list_locations', array($this, 'ajax_list_locations'));
        add_action('wp_ajax_grp_save_custom_css', array($this, 'ajax_save_custom_css'));
        add_action('admin_post_grp_export_invites_csv', array($this, 'export_invites_csv'));
        add_action('admin_post_grp_export_invites_xls', array($this, 'export_invites_xls'));
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
        
        add_submenu_page(
            'google-reviews',
            __('Addons', 'google-reviews-plugin'),
            __('Addons', 'google-reviews-plugin'),
            'manage_options',
            'google-reviews-addons',
            array($this, 'render_addons_page')
        );
        
        // Add WooCommerce submenu only if addon is enabled
        if (class_exists('GRP_Addons')) {
            $addons = GRP_Addons::get_instance();
            if ($addons->is_addon_enabled('woocommerce')) {
                add_submenu_page(
                    'google-reviews',
                    __('WooCommerce', 'google-reviews-plugin'),
                    __('WooCommerce', 'google-reviews-plugin'),
                    'manage_options',
                    'google-reviews-woocommerce',
                    array($this, 'render_woocommerce_page')
                );
            }
        }
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        // Settings group
        register_setting('grp_settings', 'grp_settings');
        // Individually stored options used throughout the plugin
        register_setting('grp_settings', 'grp_google_client_id', array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));
        register_setting('grp_settings', 'grp_google_client_secret', array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));
        register_setting('grp_settings', 'grp_google_account_id', array('type' => 'string', 'sanitize_callback' => function($value) { return $value !== null ? sanitize_text_field($value) : ''; }));
        register_setting('grp_settings', 'grp_google_location_id', array('type' => 'string', 'sanitize_callback' => array($this, 'sanitize_location_id')));
        
        // Hook to save account/location labels after settings are saved
        add_action('update_option_grp_google_account_id', array($this, 'save_account_label'), 10, 2);
        add_action('update_option_grp_google_location_id', array($this, 'save_location_label'), 10, 2);
        register_setting('grp_settings', 'grp_default_style', array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));
        register_setting('grp_settings', 'grp_default_count', array('type' => 'integer', 'sanitize_callback' => 'absint'));
        register_setting('grp_settings', 'grp_cache_duration', array('type' => 'integer', 'sanitize_callback' => 'absint'));
        register_setting('grp_settings', 'grp_enable_debug_logging', array('type' => 'boolean', 'sanitize_callback' => function($v){return (bool) $v;}));
        register_setting('grp_settings', 'grp_use_theme_font', array('type' => 'boolean', 'sanitize_callback' => function($v){return (bool) $v;}));
        register_setting('grp_settings', 'grp_place_id', array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));
        register_setting('grp_settings', 'grp_custom_css', array('type' => 'string'));
        register_setting('grp_settings', 'grp_custom_js', array('type' => 'string'));
        
        // Google API settings
        add_settings_section(
            'grp_google_api',
            __('Google API Settings', 'google-reviews-plugin'),
            array($this, 'render_google_api_section'),
            'grp_settings'
        );

        // Business selection (account/location)
        add_settings_section(
            'grp_google_selection',
            __('Business & Location', 'google-reviews-plugin'),
            array($this, 'render_selection_section'),
            'grp_settings'
        );

        add_settings_field(
            'grp_google_account_id',
            __('Account', 'google-reviews-plugin'),
            array($this, 'render_account_select_field'),
            'grp_settings',
            'grp_google_selection'
        );

        add_settings_field(
            'grp_google_location_id',
            __('Location', 'google-reviews-plugin'),
            array($this, 'render_location_select_field'),
            'grp_settings',
            'grp_google_selection'
        );

        add_settings_field(
            'grp_place_id',
            __('Place ID', 'google-reviews-plugin'),
            array($this, 'render_place_id_field'),
            'grp_settings',
            'grp_google_selection'
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
        
        add_settings_field(
            'grp_use_theme_font',
            __('Use Theme Font', 'google-reviews-plugin'),
            array($this, 'render_use_theme_font_field'),
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
        
        // Debug settings
        add_settings_section(
            'grp_debug',
            __('Debug Settings', 'google-reviews-plugin'),
            array($this, 'render_debug_section'),
            'grp_settings'
        );
        
        add_settings_field(
            'grp_enable_debug_logging',
            __('Enable Debug Logging', 'google-reviews-plugin'),
            array($this, 'render_debug_logging_field'),
            'grp_settings',
            'grp_debug'
        );
        
        // Enterprise/Custom credentials section (moved to bottom as Enterprise feature)
        register_setting('grp_settings', 'grp_enable_pro_features', array('type' => 'boolean', 'sanitize_callback' => function($v){return (bool) $v;}));
        add_settings_section(
            'grp_enterprise_credentials',
            __('Enterprise: Custom Google Credentials', 'google-reviews-plugin'),
            array($this, 'render_pro_section'),
            'grp_settings'
        );
        add_settings_field(
            'grp_enable_pro_features',
            __('Enable Custom Credentials', 'google-reviews-plugin'),
            array($this, 'render_pro_enable_field'),
            'grp_settings',
            'grp_enterprise_credentials'
        );
        add_settings_field(
            'grp_google_client_id',
            __('Client ID', 'google-reviews-plugin'),
            array($this, 'render_client_id_field'),
            'grp_settings',
            'grp_enterprise_credentials'
        );
        add_settings_field(
            'grp_google_client_secret',
            __('Client Secret', 'google-reviews-plugin'),
            array($this, 'render_client_secret_field'),
            'grp_settings',
            'grp_enterprise_credentials'
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
            'is_connected' => (new GRP_API())->is_connected(),
            'saved_account_id' => get_option('grp_google_account_id', ''),
            'saved_location_id' => get_option('grp_google_location_id', ''),
            'strings' => array(
                'testing_connection' => __('Testing connection...', 'google-reviews-plugin'),
                'connection_success' => __('Connection successful!', 'google-reviews-plugin'),
                'connection_failed' => __('Connection failed. Please check your credentials.', 'google-reviews-plugin'),
                'syncing_reviews' => __('Syncing reviews...', 'google-reviews-plugin'),
                'sync_success' => __('Reviews synced successfully!', 'google-reviews-plugin'),
                'sync_failed' => __('Failed to sync reviews.', 'google-reviews-plugin'),
                'confirm_disconnect' => __('Are you sure you want to disconnect?', 'google-reviews-plugin'),
                'loading' => __('Loading...', 'google-reviews-plugin'),
                'select_account' => __('Select an account', 'google-reviews-plugin'),
                'select_location' => __('Select a location', 'google-reviews-plugin')
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

        // Surface settings API notices
        settings_errors();

        // Provide OAuth controls
        $auth_url = '';
        $disconnect_url = wp_nonce_url(admin_url('admin-post.php?action=grp_disconnect'), 'grp_disconnect');
        if (!$is_connected) {
            $auth_url_result = $api->get_auth_url();
            $using_api_server = $api->is_using_api_server();
            
            if (is_wp_error($auth_url_result)) {
                // Show error but still provide connect button if possible
                echo '<div class="notice notice-error"><p><strong>' . esc_html__('Configuration Error', 'google-reviews-plugin') . ':</strong> ' . esc_html($auth_url_result->get_error_message()) . '</p></div>';
                
                // If using API server and it failed, suggest checking server or using custom credentials
                if ($using_api_server) {
                    echo '<div class="notice notice-warning"><p>';
                    echo esc_html__('The cloud server may be unavailable. You can:', 'google-reviews-plugin');
                    echo '<ul style="margin-left: 20px; margin-top: 10px;">';
                    echo '<li>' . esc_html__('Wait a moment and try again', 'google-reviews-plugin') . '</li>';
                    echo '<li>' . esc_html__('Enable custom credentials in the Enterprise section below to use your own Google Cloud Project', 'google-reviews-plugin') . '</li>';
                    echo '</ul>';
                    echo '</p></div>';
                } else {
                    echo '<p class="description">' . esc_html__('Please check your Google Cloud credentials in the Enterprise section below.', 'google-reviews-plugin') . '</p>';
                }
                
                // Still try to show connect button if we can generate a URL (for custom credentials)
                if (!$using_api_server) {
                    $client_id = get_option('grp_google_client_id', '');
                    $pro_enabled = (bool) get_option('grp_enable_pro_features', false);
                    if ($pro_enabled && !empty($client_id)) {
                        // Try direct Google OAuth URL
                        $state = wp_create_nonce('grp_oauth_state');
                        update_option('grp_oauth_state', $state);
                        $auth_url = add_query_arg(array(
                            'client_id' => $client_id,
                            'redirect_uri' => admin_url('admin.php?page=google-reviews-settings&action=oauth_callback'),
                            'scope' => 'https://www.googleapis.com/auth/business.manage',
                            'response_type' => 'code',
                            'access_type' => 'offline',
                            'prompt' => 'consent',
                            'state' => $state
                        ), 'https://accounts.google.com/o/oauth2/v2/auth');
                    }
                }
            } else {
                $auth_url = $auth_url_result;
                if ($using_api_server) {
                    echo '<div class="notice notice-success"><p><strong>' . esc_html__('Easy Setup Available!', 'google-reviews-plugin') . '</strong> ' . esc_html__('Click below to connect using our pre-configured setup. No Google Cloud Project setup required!', 'google-reviews-plugin') . '</p></div>';
                } else {
                    echo '<div class="notice notice-info"><p>' . esc_html__('Connect your Google account to start syncing reviews.', 'google-reviews-plugin') . '</p></div>';
                }
            }
            
            // Always show connect button if we have a URL
            if (!empty($auth_url)) {
                echo '<p><a class="button button-primary" href="' . esc_url($auth_url) . '">' . esc_html__('Connect Google Account', 'google-reviews-plugin') . '</a></p>';
            }
        } else {
            echo '<div class="notice notice-success"><p>' . esc_html__('Google account connected.', 'google-reviews-plugin') . '</p></div>';
            echo '<p>'
                . '<button id="grp-test-connection" class="button">' . esc_html__('Test Connection', 'google-reviews-plugin') . '</button> '
                . '<a class="button" href="' . esc_url($disconnect_url) . '">' . esc_html__('Disconnect', 'google-reviews-plugin') . '</a>'
                . '</p>';
        }
        
        include GRP_PLUGIN_DIR . 'includes/admin/views/settings.php';
    }

    /**
     * Handle OAuth callback and notices
     */
    public function maybe_handle_oauth() {
        if (!is_admin()) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        $action = isset($_GET['action']) ? sanitize_text_field(wp_unslash($_GET['action'])) : '';

        if ($page !== 'google-reviews-settings' || $action !== 'oauth_callback') {
            return;
        }

        $state = isset($_GET['state']) ? sanitize_text_field(wp_unslash($_GET['state'])) : '';
        $oauth_success = isset($_GET['oauth_success']) ? sanitize_text_field(wp_unslash($_GET['oauth_success'])) : '';
        $oauth_error = isset($_GET['oauth_error']) ? sanitize_text_field(wp_unslash($_GET['oauth_error'])) : '';
        $error = isset($_GET['error']) ? sanitize_text_field(wp_unslash($_GET['error'])) : '';
        $code = isset($_GET['code']) ? sanitize_text_field(wp_unslash($_GET['code'])) : '';

        // Only process OAuth callback if we have OAuth-related parameters
        // This prevents state validation errors when just viewing the settings page
        if (empty($oauth_success) && empty($oauth_error) && empty($error) && empty($code)) {
            return;
        }

        // Verify state matches stored state (only if we have a state parameter and we're processing OAuth)
        // Skip state validation if we already have tokens (connection already successful)
        if (!empty($state) && (!empty($oauth_success) || !empty($code))) {
            $stored_state = get_option('grp_oauth_state', '');
            // If state doesn't exist but we already have tokens, skip validation (already connected)
            if (empty($stored_state)) {
                $existing_token = get_option('grp_google_access_token', '');
                if (!empty($existing_token)) {
                    // Already connected, skip OAuth flow
                    return;
                }
            }
            if (empty($stored_state) || $stored_state !== $state) {
                add_settings_error('grp_settings', 'grp_oauth_state_mismatch', __('Invalid OAuth state. Please try connecting again.', 'google-reviews-plugin'), 'error');
                return;
            }
        }

        // Handle OAuth error from cloud server redirect
        if (!empty($oauth_error) || !empty($error)) {
            $error_message = !empty($error) ? urldecode($error) : __('OAuth authentication failed.', 'google-reviews-plugin');
            delete_option('grp_oauth_state');
            add_settings_error('grp_settings', 'grp_oauth_error', sprintf(__('OAuth error: %s', 'google-reviews-plugin'), $error_message), 'error');
            return;
        }

        // Handle OAuth success - retrieve tokens from cloud server
        if (!empty($oauth_success) && !empty($state)) {
            $api = new GRP_API();
            
            // Retrieve tokens from cloud server using state
            $tokens = $api->retrieve_oauth_tokens($state);
            
            if (is_wp_error($tokens)) {
                delete_option('grp_oauth_state');
                add_settings_error('grp_settings', 'grp_oauth_fail', $tokens->get_error_message(), 'error');
            } elseif (isset($tokens['access_token'])) {
                // Store tokens
                update_option('grp_google_access_token', $tokens['access_token']);
                // Only update refresh_token if provided (Google doesn't always return it on reconnection)
                if (isset($tokens['refresh_token']) && !empty($tokens['refresh_token'])) {
                    update_option('grp_google_refresh_token', $tokens['refresh_token']);
                } else {
                    // Log warning if no refresh token provided
                    grp_debug_log('OAuth reconnection: No refresh_token in response', array(
                        'has_access_token' => !empty($tokens['access_token']),
                        'response_keys' => array_keys($tokens)
                    ));
                }
                
                // Clear stored state
                delete_option('grp_oauth_state');
                
                add_settings_error('grp_settings', 'grp_oauth_success', __('Successfully connected to Google.', 'google-reviews-plugin'), 'updated');
            } else {
                delete_option('grp_oauth_state');
                add_settings_error('grp_settings', 'grp_oauth_fail', __('Failed to retrieve OAuth tokens. Please try connecting again.', 'google-reviews-plugin'), 'error');
            }
            return;
        }

        // Legacy flow: Direct code exchange (for backward compatibility with custom credentials)
        if (!empty($code)) {
            // Clear stored state
            delete_option('grp_oauth_state');

            $api = new GRP_API();
            $ok = $api->exchange_code_for_tokens($code);
            if ($ok) {
                add_settings_error('grp_settings', 'grp_oauth_success', __('Successfully connected to Google.', 'google-reviews-plugin'), 'updated');
            } else {
                // Get more detailed error message if available
                $error_msg = __('Failed to exchange authorization code for tokens.', 'google-reviews-plugin');
                
                // Check if there's a more specific error from the API
                $last_error = $api->get_last_error();
                if ($last_error && is_wp_error($last_error)) {
                    $error_msg = $last_error->get_error_message();
                }
                
                add_settings_error('grp_settings', 'grp_oauth_fail', $error_msg, 'error');
            }
            return;
        }

        // No code and no success - something went wrong
        delete_option('grp_oauth_state');
        add_settings_error('grp_settings', 'grp_oauth_no_code', __('No authorization code or success signal received.', 'google-reviews-plugin'), 'error');
    }

    /**
     * Disconnect handler
     */
    public function handle_disconnect() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'google-reviews-plugin'));
        }

        check_admin_referer('grp_disconnect');

        $api = new GRP_API();
        $api->disconnect();

        wp_safe_redirect(admin_url('admin.php?page=google-reviews-settings'));
        exit;
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
     * Render Addons page
     */
    public function render_addons_page() {
        require_once GRP_PLUGIN_DIR . 'includes/admin/views/addons.php';
    }
    
    /**
     * Render WooCommerce page
     */
    public function render_woocommerce_page() {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            echo '<div class="wrap"><h1>' . esc_html__('WooCommerce', 'google-reviews-plugin') . '</h1>';
            echo '<div class="notice notice-error"><p>' . esc_html__('WooCommerce is not active. Please install and activate WooCommerce to use this feature.', 'google-reviews-plugin') . '</p></div></div>';
            return;
        }
        
        // Check Pro/Enterprise license
        $license = new GRP_License();
        if (!$license->is_pro()) {
            echo '<div class="wrap"><h1>' . esc_html__('WooCommerce', 'google-reviews-plugin') . '</h1>';
            echo '<div class="notice notice-error"><p>' . esc_html__('This feature requires a Pro or Enterprise license. Please upgrade to use the WooCommerce integration.', 'google-reviews-plugin') . '</p></div></div>';
            return;
        }
        
        require_once GRP_PLUGIN_DIR . 'includes/admin/views/woocommerce.php';
    }
    
    /**
     * Render Google API section
     */
    public function render_google_api_section() {
        $api = new GRP_API();
        $using_api_server = $api->is_using_api_server();
        $license = new GRP_License();
        $is_pro = $license->is_pro();
        
        if ($using_api_server && !$is_pro) {
            // Free tier with default credentials - simplified instructions
            echo '<div class="notice notice-success" style="margin-bottom:15px;"><p>';
            echo '<strong>' . esc_html__('Easy Setup Available!', 'google-reviews-plugin') . '</strong><br>';
            echo esc_html__('You can connect to Google Business Profile using our pre-configured setup. No need to create your own Google Cloud project!', 'google-reviews-plugin');
            echo '</p></div>';
            
            echo '<p>' . __('Simply click "Connect Google Account" below to get started. You\'ll be asked to authorize the plugin to access your Google Business Profile reviews.', 'google-reviews-plugin') . '</p>';
            
            echo '<div class="notice notice-info" style="margin-top:12px;"><p>';
            echo '<strong>' . esc_html__('Using Your Own Credentials?', 'google-reviews-plugin') . '</strong><br>';
            echo esc_html__('Pro users can optionally use their own Google Cloud Project credentials for more control. Enable the "Advanced (Pro)" section below to enter custom Client ID and Secret.', 'google-reviews-plugin');
            echo '</p></div>';
        } else {
            // Pro tier or custom credentials - show full instructions
            echo '<p>' . __('Configure your Google Business Profile connection.', 'google-reviews-plugin') . '</p>';
            
            if (!$is_pro) {
                echo '<p><a target="_blank" rel="noopener" href="https://console.cloud.google.com/">' . esc_html__('Open Google Cloud Console', 'google-reviews-plugin') . '</a></p>';
                echo '<ol style="margin-left:20px;">'
                    . '<li>' . esc_html__('Create/select a GCP project', 'google-reviews-plugin') . '</li>'
                    . '<li>'
                        . esc_html__('Enable the required Business Profile APIs:', 'google-reviews-plugin')
                        . '<ul style="margin-top:6px; list-style:disc; margin-left:20px;">'
                            . '<li><a target="_blank" rel="noopener" href="https://console.developers.google.com/apis/api/businessprofile.googleapis.com/overview">' . esc_html__('Business Profile API', 'google-reviews-plugin') . '</a></li>'
                            . '<li><a target="_blank" rel="noopener" href="https://console.developers.google.com/apis/api/mybusinessbusinessinformation.googleapis.com/overview">' . esc_html__('Business Profile Business Information API', 'google-reviews-plugin') . '</a></li>'
                            . '<li><a target="_blank" rel="noopener" href="https://console.developers.google.com/apis/api/businessprofileperformance.googleapis.com/overview">' . esc_html__('Business Profile Performance API', 'google-reviews-plugin') . '</a></li>'
                        . '</ul>'
                    . '</li>'
                    . '<li>' . esc_html__('Ensure billing is enabled for your project.', 'google-reviews-plugin') . '</li>'
                    . '<li>' . esc_html__('Configure OAuth consent screen', 'google-reviews-plugin') . '</li>'
                    . '<li>' . esc_html__('Create OAuth 2.0 Client (Web application)', 'google-reviews-plugin') . '</li>'
                    . '<li>' . sprintf(esc_html__('Add Authorized redirect URI: %s', 'google-reviews-plugin'), esc_html(admin_url('admin.php?page=google-reviews-settings&action=oauth_callback'))) . '</li>'
                    . '<li>' . sprintf(esc_html__('Ensure scope is granted: %s', 'google-reviews-plugin'), '<code>https://www.googleapis.com/auth/business.manage</code>') . '</li>'
                . '</ol>';
            }
            
            echo '<div class="notice notice-info" style="margin-top:12px;"><p>';
            $license = new GRP_License();
            $is_enterprise = $license->is_enterprise();
            
            if ($is_enterprise) {
                echo esc_html__('Enterprise licenses can use custom Google Cloud credentials to bypass the cloud server, or use the default setup (no configuration needed).', 'google-reviews-plugin');
            } elseif ($is_pro) {
                echo esc_html__('Pro users must use the default setup (cloud server). Enterprise licenses can use custom credentials for more control.', 'google-reviews-plugin');
            } else {
                echo esc_html__('If you see "Requests per minute = 0" for a Business Profile API, your project is not yet approved for that API. Request access using the official prerequisites page (do not just request a quota increase).', 'google-reviews-plugin')
                    . ' <a target="_blank" rel="noopener" href="https://developers.google.com/my-business/content/prereqs">'
                    . esc_html__('Request Business Profile API access', 'google-reviews-plugin')
                    . '</a>.';
            }
            echo '</p><p>';
            echo esc_html__('Use OAuth 2.0 user consent with the Google account that owns/manages the Business Profile. Service accounts are not supported for these endpoints.', 'google-reviews-plugin');
            echo '</p></div>';
        }
    }

    /**
     * Render Pro section description
     */
    public function render_pro_section() {
        echo '<p>' . __('This section is only for Enterprise users who want to use their own Google Cloud Project credentials for maximum control over API quotas and usage.', 'google-reviews-plugin') . '</p>';
        echo '<p class="description">' . __('<strong>Note:</strong> You do not need to configure this section unless you specifically want to use your own Google Cloud Project. The default setup works for both free and Pro users.', 'google-reviews-plugin') . '</p>';
    }

    /**
     * Render Pro enable toggle
     */
    public function render_pro_enable_field() {
        $license = new GRP_License();
        $is_enterprise = $license->is_enterprise();
        $has_license = $license->has_license();
        
        // Only Enterprise can use custom credentials
        if ($is_enterprise) {
            $enabled = (bool) get_option('grp_enable_pro_features', false);
            $label = __('Use my own Google Cloud Project credentials', 'google-reviews-plugin');
            
            echo '<label><input type="checkbox" name="grp_enable_pro_features" value="1" ' . checked(true, $enabled, false) . ' /> ' . esc_html($label) . '</label>';
            echo '<p class="description">' . esc_html__('Enterprise licenses can use custom Google Cloud credentials to bypass the cloud server. When enabled, you can enter your own Client ID and Client Secret below.', 'google-reviews-plugin') . '</p>';
            echo '<p class="description"><strong>' . esc_html__('Why use custom credentials?', 'google-reviews-plugin') . '</strong><br>';
            echo esc_html__('Enterprise users may want to use their own Google Cloud Project to:', 'google-reviews-plugin');
            echo '<ul style="margin-left: 20px; margin-top: 5px;">';
            echo '<li>' . esc_html__('Have full control over API quotas', 'google-reviews-plugin') . '</li>';
            echo '<li>' . esc_html__('Monitor usage in their own Google Cloud Console', 'google-reviews-plugin') . '</li>';
            echo '<li>' . esc_html__('Use their organization\'s existing Google Cloud Project', 'google-reviews-plugin') . '</li>';
            echo '</ul></p>';
        } else {
            // Free and Pro users
            if ($has_license) {
                $is_free = $license->is_free();
                if ($is_free) {
                    echo '<p class="description"><strong>' . esc_html__('Free License Active', 'google-reviews-plugin') . '</strong><br>';
                    echo esc_html__('Free licenses use the cloud server for easy setup. You can also enter your own Google Cloud credentials below to use direct API calls. Upgrade to Enterprise for full control.', 'google-reviews-plugin') . '</p>';
                } else {
                    echo '<p class="description"><strong>' . esc_html__('Pro License Active', 'google-reviews-plugin') . '</strong><br>';
                    echo esc_html__('Pro licenses must use the cloud server. Upgrade to Enterprise to use custom Google Cloud credentials.', 'google-reviews-plugin') . '</p>';
                }
            } else {
                // No license - WordPress.org compliant: Allow custom credentials
                echo '<p class="description"><strong>' . esc_html__('Setup Options', 'google-reviews-plugin') . '</strong><br>';
                echo esc_html__('You can use this plugin in two ways:', 'google-reviews-plugin') . '</p>';
                echo '<ul style="margin-left: 20px; margin-top: 5px;">';
                echo '<li><strong>' . esc_html__('Option 1 (Recommended):', 'google-reviews-plugin') . '</strong> ' . esc_html__('Activate a free license to use our cloud server (no Google Cloud setup required).', 'google-reviews-plugin') . '</li>';
                echo '<li><strong>' . esc_html__('Option 2:', 'google-reviews-plugin') . '</strong> ' . esc_html__('Enter your own Google Cloud credentials below to use direct API calls (requires Google Cloud Project setup).', 'google-reviews-plugin') . '</li>';
                echo '</ul>';
                echo '<p class="description">' . esc_html__('Pro and Enterprise licenses unlock advanced features. Enterprise licenses can use custom credentials to bypass the cloud server.', 'google-reviews-plugin') . '</p>';
            }
        }
    }

    /**
     * Render selection section description
     */
    public function render_selection_section() {
        $api = new GRP_API();
        if (!$api->is_connected()) {
            echo '<p>' . esc_html__('Connect your Google account first to select an account and location.', 'google-reviews-plugin') . '</p>';
            return;
        }
        echo '<p>' . esc_html__('Select the Business Profile account and location to use for reviews.', 'google-reviews-plugin') . '</p>';
    }

    /**
     * Render account select field
     */
    public function render_account_select_field() {
        $api = new GRP_API();
        $connected = $api->is_connected();
        $saved_id = get_option('grp_google_account_id', '');
        $saved_label = get_option('grp_google_account_label', '');
        echo '<select id="grp-account-select" name="grp_google_account_id" ' . (!$connected ? 'disabled' : '') . ' class="regular-text">';
        if ($saved_id) {
            $display_label = !empty($saved_label) ? $saved_label : $saved_id;
            echo '<option value="' . esc_attr($saved_id) . '" data-label="' . esc_attr($saved_label) . '" selected>' . esc_html($display_label) . '</option>';
        } else {
            echo '<option value="">' . esc_html__('Click Refresh to load accounts', 'google-reviews-plugin') . '</option>';
        }
        echo '</select> ';
        echo '<button type="button" id="grp-refresh-accounts" class="button" ' . (!$connected ? 'disabled' : '') . '>' . esc_html__('Refresh', 'google-reviews-plugin') . '</button>';
        if (!$connected) {
            echo '<p class="description">' . esc_html__('You must connect your Google account before accounts can be listed.', 'google-reviews-plugin') . '</p>';
        }
    }

    /**
     * Render location select field
     */
    public function render_location_select_field() {
        $api = new GRP_API();
        $connected = $api->is_connected();
        $saved_id = get_option('grp_google_location_id', '');
        $saved_label = get_option('grp_google_location_label', '');
        echo '<select id="grp-location-select" name="grp_google_location_id" ' . (!$connected ? 'disabled' : '') . ' class="regular-text">';
        echo '<option value="">' . esc_html__('Select an account first', 'google-reviews-plugin') . '</option>';
        if ($saved_id) {
            $display_label = !empty($saved_label) ? $saved_label : $saved_id;
            echo '<option value="' . esc_attr($saved_id) . '" data-label="' . esc_attr($saved_label) . '" selected>' . esc_html($display_label) . '</option>';
        }
        echo '</select>';
    }
    
    /**
     * Render Place ID field
     */
    public function render_place_id_field() {
        $api = new GRP_API();
        $is_connected = $api->is_connected();
        $location_id = get_option('grp_google_location_id', '');
        $account_id = get_option('grp_google_account_id', '');
        $place_id = get_option('grp_place_id', '');
        $place_id_auto = get_option('grp_gbp_place_id_default', '');
        $location_name = get_option('grp_gbp_location_name', '');
        
        // Use auto-detected Place ID if manual one is empty
        $display_place_id = $place_id ?: $place_id_auto;
        
        echo '<input type="text" id="grp-place-id" name="grp_place_id" value="' . esc_attr($place_id) . '" class="regular-text" placeholder="ChIJ..." pattern="[A-Za-z0-9_-]+" />';
        
        if (!empty($display_place_id) && !$place_id) {
            echo '<p class="description" style="margin-top: 5px;">';
            echo '<strong>' . esc_html__('Auto-detected:', 'google-reviews-plugin') . '</strong> ';
            echo '<code>' . esc_html($display_place_id) . '</code>';
            echo '</p>';
        }
        
        echo '<p class="description" style="margin-top: 8px;">';
        echo esc_html__('Place ID is only needed for generating "Leave a Review" links (e.g., for WooCommerce review invites). Reviews are fetched automatically using your Business Profile location.', 'google-reviews-plugin');
        echo ' <a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank">' . esc_html__('How to find your Place ID', 'google-reviews-plugin') . '</a>';
        echo '</p>';
        
        if ($is_connected && !empty($location_id) && !empty($account_id)) {
            // Try to auto-detect if not set
            if (empty($display_place_id)) {
                $location_details = $api->get_location($account_id, $location_id);
                if (!is_wp_error($location_details)) {
                    $loc = isset($location_details['location']) ? $location_details['location'] : $location_details;
                    if (!empty($loc)) {
                        $detected_place_id = '';
                        if (isset($loc['metadata']['placeId']) && !empty($loc['metadata']['placeId'])) {
                            $detected_place_id = $loc['metadata']['placeId'];
                        } elseif (isset($loc['placeId']) && !empty($loc['placeId'])) {
                            $detected_place_id = $loc['placeId'];
                        }
                        
                        if (!empty($detected_place_id)) {
                            update_option('grp_gbp_place_id_default', $detected_place_id);
                            echo '<p class="description" style="color: #46b450; margin-top: 5px;">';
                            echo '<strong>' . esc_html__('Auto-detected:', 'google-reviews-plugin') . '</strong> ';
                            echo '<code>' . esc_html($detected_place_id) . '</code>';
                            echo '</p>';
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Render client ID field
     */
    public function render_client_id_field() {
        $value = get_option('grp_google_client_id', '');
        $pro_enabled = (bool) get_option('grp_enable_pro_features', false);
        $api = new GRP_API();
        $using_api_server = $api->is_using_api_server();
        
        echo '<input type="text" name="grp_google_client_id" value="' . esc_attr($value) . '" class="regular-text" ' . ($pro_enabled ? '' : 'disabled') . ' />';
        
        if ($pro_enabled) {
            echo '<p class="description">' . __('Enter your Google OAuth 2.0 Client ID.', 'google-reviews-plugin') . ' '
                . '<a target="_blank" rel="noopener" href="https://console.cloud.google.com/apis/credentials">' . esc_html__('Get it in Google Cloud Console → Credentials', 'google-reviews-plugin') . '</a>'
                . '</p>';
        } else {
            if ($using_api_server) {
                echo '<p class="description">' . esc_html__('Using API server for OAuth. Enable the option above to use your own Client ID.', 'google-reviews-plugin') . '</p>';
            } else {
                echo '<p class="description">' . __('Enter your Google OAuth 2.0 Client ID. Enable the option above first.', 'google-reviews-plugin') . '</p>';
            }
        }
    }
    
    /**
     * Render client secret field
     */
    public function render_client_secret_field() {
        $value = get_option('grp_google_client_secret', '');
        $pro_enabled = (bool) get_option('grp_enable_pro_features', false);
        $api = new GRP_API();
        $using_api_server = $api->is_using_api_server();
        
        echo '<input type="password" name="grp_google_client_secret" value="' . esc_attr($value) . '" class="regular-text" ' . ($pro_enabled ? '' : 'disabled') . ' />';
        
        if ($pro_enabled) {
            echo '<p class="description">' . __('Enter your Google OAuth 2.0 Client Secret.', 'google-reviews-plugin') . ' '
                . '<a target="_blank" rel="noopener" href="https://console.cloud.google.com/apis/credentials">' . esc_html__('Find it in Google Cloud Console → Credentials', 'google-reviews-plugin') . '</a>'
                . '</p>';
        } else {
            if ($using_api_server) {
                echo '<p class="description">' . esc_html__('Using API server for OAuth. Enable the option above to use your own Client Secret.', 'google-reviews-plugin') . '</p>';
            } else {
                echo '<p class="description">' . __('Enter your Google OAuth 2.0 Client Secret. Enable the option above first.', 'google-reviews-plugin') . '</p>';
            }
        }
    }
    
    /**
     * Render display section
     */
    public function render_display_section() {
        echo '<p>' . __('Configure default display settings for reviews.', 'google-reviews-plugin') . '</p>';
    }
    
    /**
     * Render use theme font field
     */
    public function render_use_theme_font_field() {
        $enabled = (bool) get_option('grp_use_theme_font', false);
        echo '<label><input type="checkbox" name="grp_use_theme_font" value="1" ' . checked(true, $enabled, false) . ' /> ' . esc_html__('Use theme font family instead of template fonts', 'google-reviews-plugin') . '</label>';
        echo '<p class="description">' . __('When enabled, reviews will inherit your theme\'s font family instead of using the template-defined fonts. This helps maintain consistency with your site\'s typography.', 'google-reviews-plugin') . '</p>';
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
     * Render debug section description
     */
    public function render_debug_section() {
        echo '<p>' . __('Configure debug logging settings. When enabled, detailed error and debug information will be logged to WordPress debug.log.', 'google-reviews-plugin') . '</p>';
    }
    
    /**
     * Render debug logging field
     */
    public function render_debug_logging_field() {
        $enabled = (bool) get_option('grp_enable_debug_logging', false);
        $debug_log_path = '';
        
        if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            if (defined('WP_DEBUG_LOG_FILE')) {
                $debug_log_path = WP_DEBUG_LOG_FILE;
            } else {
                $debug_log_path = WP_CONTENT_DIR . '/debug.log';
            }
        }
        
        echo '<label><input type="checkbox" name="grp_enable_debug_logging" value="1" ' . checked(true, $enabled, false) . ' /> ' . esc_html__('Enable debug logging to debug.log', 'google-reviews-plugin') . '</label>';
        echo '<p class="description">' . __('When enabled, detailed error messages and debug information will be logged. This should only be enabled when troubleshooting issues.', 'google-reviews-plugin') . '</p>';
        
        if ($enabled && !empty($debug_log_path)) {
            $log_exists = file_exists($debug_log_path);
            $log_size = $log_exists ? size_format(filesize($debug_log_path)) : '';
            echo '<p class="description" style="margin-top: 10px;">';
            echo '<strong>' . esc_html__('Debug Log Location:', 'google-reviews-plugin') . '</strong> ';
            echo '<code>' . esc_html($debug_log_path) . '</code>';
            if ($log_exists) {
                echo ' <span style="color: #666;">(' . esc_html__('Size:', 'google-reviews-plugin') . ' ' . esc_html($log_size) . ')</span>';
            }
            echo '</p>';
        } elseif ($enabled) {
            echo '<p class="description" style="margin-top: 10px; color: #d63638;">';
            echo '<strong>' . esc_html__('Warning:', 'google-reviews-plugin') . '</strong> ';
            echo esc_html__('WP_DEBUG_LOG is not enabled in wp-config.php. Add define(\'WP_DEBUG_LOG\', true); to enable debug logging.', 'google-reviews-plugin');
            echo '</p>';
        }
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
     * AJAX: List GBP accounts for the connected user
     */
    public function ajax_list_accounts() {
        check_ajax_referer('grp_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'google-reviews-plugin'));
        }
        
        // Check if this is a forced refresh
        $force = isset($_POST['force']) && (int) $_POST['force'] === 1;
        
        // Define cache key (used whether force or not)
        $cache_key = 'grp_accounts_list';
        
        // Check for cached accounts first (cache for 10 minutes - accounts don't change often)
        // Skip cache if force refresh is requested
        if (!$force) {
            $cached_accounts = get_transient($cache_key);
            if ($cached_accounts !== false) {
                wp_send_json_success(array('accounts' => $cached_accounts, 'cached' => true));
            }
        }
        
        $api = new GRP_API();
        if (!$api->is_connected()) {
            wp_send_json_error(__('Not connected to Google', 'google-reviews-plugin'));
        }
        $resp = $api->get_accounts();
        if (is_wp_error($resp)) {
            // If rate limited, try to return cached data even if expired
            if ($resp->get_error_code() === 'rate_limit') {
                $stale_cache = get_option('grp_accounts_cache_stale', false);
                if ($stale_cache !== false) {
                    wp_send_json_success(array('accounts' => $stale_cache, 'cached' => true, 'stale' => true));
                }
            }
            wp_send_json_error($resp->get_error_message());
        }
        $accounts = array();
        $list = array();
        if (isset($resp['accounts']) && is_array($resp['accounts'])) {
            $list = $resp['accounts'];
        } elseif (is_array($resp)) {
            $list = $resp;
        }
        foreach ($list as $acc) {
            $name = isset($acc['name']) ? $acc['name'] : '';
            $id = $name ? preg_replace('#^accounts/+?#', '', $name) : '';
            // Also extract just the numeric ID if it's a full resource name
            $numeric_id = '';
            if (preg_match('#/(\d+)$#', $id, $matches)) {
                $numeric_id = $matches[1];
            } elseif (is_numeric($id)) {
                $numeric_id = $id;
            }
            $label = isset($acc['accountName']) ? $acc['accountName'] : ($name ?: $id);
            if (!empty($id)) {
                $accounts[] = array('id' => $id, 'label' => $label, 'numeric_id' => $numeric_id);
            }
        }
        
        // Cache accounts for 10 minutes
        set_transient($cache_key, $accounts, 10 * MINUTE_IN_SECONDS);
        // Also store as persistent cache for fallback during rate limits
        update_option('grp_accounts_cache_stale', $accounts);
        
        wp_send_json_success(array('accounts' => $accounts));
    }

    /**
     * AJAX: List locations for a given account id
     */
    public function ajax_list_locations() {
        check_ajax_referer('grp_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'google-reviews-plugin'));
        }
        $account_id = isset($_POST['account_id']) ? sanitize_text_field(wp_unslash($_POST['account_id'])) : '';
        if (empty($account_id)) {
            wp_send_json_error(__('Missing account id', 'google-reviews-plugin'));
        }
        
        // Check if this is a forced refresh
        $force = isset($_POST['force']) && (int) $_POST['force'] === 1;
        
        // Check for cached locations first (cache for 5 minutes to reduce API calls)
        // Skip cache if force refresh is requested
        if (!$force) {
            $cache_key = 'grp_locations_' . md5($account_id);
            $cached_locations = get_transient($cache_key);
            if ($cached_locations !== false) {
                wp_send_json_success(array('locations' => $cached_locations, 'cached' => true));
            }
        } else {
            $cache_key = 'grp_locations_' . md5($account_id);
        }
        
        $api = new GRP_API();
        if (!$api->is_connected()) {
            wp_send_json_error(__('Not connected to Google', 'google-reviews-plugin'));
        }
        $resp = $api->get_locations($account_id);
        if (is_wp_error($resp)) {
            // If rate limited, try to return cached data even if expired
            if ($resp->get_error_code() === 'rate_limit') {
                $stale_cache = get_option('grp_locations_cache_' . md5($account_id), false);
                if ($stale_cache !== false) {
                    wp_send_json_success(array('locations' => $stale_cache, 'cached' => true, 'stale' => true));
                }
            }
            wp_send_json_error($resp->get_error_message());
        }
        $locations = array();
        $list = array();
        if (isset($resp['locations']) && is_array($resp['locations'])) {
            $list = $resp['locations'];
        } elseif (is_array($resp)) {
            $list = $resp;
        }
        foreach ($list as $loc) {
            $name = isset($loc['name']) ? $loc['name'] : '';
            $loc_id = '';
            if ($name && preg_match('#/locations/([^/]+)$#', $name, $m)) {
                $loc_id = $m[1];
            } elseif (!empty($name)) {
                // Clean the name - remove any prefixes
                $loc_id = preg_replace('#^(accounts/[^/]+/)?locations/?#', '', $name);
            }
            // Ensure loc_id is clean (no prefixes)
            $loc_id = preg_replace('#^(accounts/[^/]+/)?locations/?#', '', $loc_id);
            
            $label = isset($loc['title']) ? $loc['title'] : (isset($loc['locationName']) ? $loc['locationName'] : ($name ?: $loc_id));
            // Also extract just the numeric ID if it's a full resource name
            $numeric_id = '';
            if (is_numeric($loc_id)) {
                $numeric_id = $loc_id;
            } elseif (preg_match('#/(\d+)$#', $loc_id, $matches)) {
                $numeric_id = $matches[1];
            }
            if (!empty($loc_id)) {
                $locations[] = array('id' => $loc_id, 'label' => $label, 'numeric_id' => $numeric_id);
                // Also save label if this matches the saved location ID
                $saved_location_id = get_option('grp_google_location_id', '');
                if ($saved_location_id === $loc_id || $saved_location_id === $numeric_id) {
                    update_option('grp_google_location_label', $label);
                }
            }
        }
        
        // Cache locations for 5 minutes
        set_transient($cache_key, $locations, 5 * MINUTE_IN_SECONDS);
        // Also store as persistent cache for fallback during rate limits
        update_option('grp_locations_cache_' . md5($account_id), $locations);
        
        wp_send_json_success(array('locations' => $locations));
    }
    
    /**
     * Export review invites to CSV
     */
    public function export_invites_csv() {
        check_admin_referer('grp_export_invites');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'google-reviews-plugin'));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'grp_review_invites';
        
        // Get filter parameters
        $date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
        $date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';
        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
        
        // Build query
        $where = array('1=1');
        $prepare_values = array();
        
        if (!empty($date_from)) {
            $where[] = 'created_at >= %s';
            $prepare_values[] = $date_from . ' 00:00:00';
        }
        if (!empty($date_to)) {
            $where[] = 'created_at <= %s';
            $prepare_values[] = $date_to . ' 23:59:59';
        }
        if (!empty($status)) {
            $where[] = 'invite_status = %s';
            $prepare_values[] = $status;
        }
        
        $where_clause = implode(' AND ', $where);
        $query = "SELECT * FROM {$table} WHERE {$where_clause} ORDER BY created_at DESC";
        
        if (!empty($prepare_values)) {
            $query = $wpdb->prepare($query, $prepare_values);
        }
        
        $invites = $wpdb->get_results($query);
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=review-invites-' . date('Y-m-d') . '.csv');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Output BOM for UTF-8
        echo "\xEF\xBB\xBF";
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // CSV headers
        $headers = array(
            __('ID', 'google-reviews-plugin'),
            __('Order ID', 'google-reviews-plugin'),
            __('Email', 'google-reviews-plugin'),
            __('Status', 'google-reviews-plugin'),
            __('Scheduled At', 'google-reviews-plugin'),
            __('Sent At', 'google-reviews-plugin'),
            __('Clicked At', 'google-reviews-plugin'),
            __('Coupon Code', 'google-reviews-plugin'),
            __('Rewarded At', 'google-reviews-plugin'),
            __('Created At', 'google-reviews-plugin'),
        );
        fputcsv($output, $headers);
        
        // CSV rows
        foreach ($invites as $invite) {
            $row = array(
                $invite->id,
                $invite->order_id,
                $invite->email,
                $invite->invite_status,
                $invite->scheduled_at ?: '',
                $invite->sent_at ?: '',
                $invite->clicked_at ?: '',
                $invite->coupon_code ?: '',
                $invite->rewarded_at ?: '',
                $invite->created_at,
            );
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Export review invites to XLS (Excel/Google Sheets compatible)
     */
    public function export_invites_xls() {
        check_admin_referer('grp_export_invites');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'google-reviews-plugin'));
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'grp_review_invites';
        
        // Get filter parameters
        $date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
        $date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';
        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
        
        // Build query
        $where = array('1=1');
        $prepare_values = array();
        
        if (!empty($date_from)) {
            $where[] = 'created_at >= %s';
            $prepare_values[] = $date_from . ' 00:00:00';
        }
        if (!empty($date_to)) {
            $where[] = 'created_at <= %s';
            $prepare_values[] = $date_to . ' 23:59:59';
        }
        if (!empty($status)) {
            $where[] = 'invite_status = %s';
            $prepare_values[] = $status;
        }
        
        $where_clause = implode(' AND ', $where);
        $query = "SELECT * FROM {$table} WHERE {$where_clause} ORDER BY created_at DESC";
        
        if (!empty($prepare_values)) {
            $query = $wpdb->prepare($query, $prepare_values);
        }
        
        $invites = $wpdb->get_results($query);
        
        // Set headers for XLS download (Excel/Google Sheets compatible TSV format)
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=review-invites-' . date('Y-m-d') . '.xls');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Output BOM for UTF-8
        echo "\xEF\xBB\xBF";
        
        // XLS headers (tab-separated)
        $headers = array(
            __('ID', 'google-reviews-plugin'),
            __('Order ID', 'google-reviews-plugin'),
            __('Email', 'google-reviews-plugin'),
            __('Status', 'google-reviews-plugin'),
            __('Scheduled At', 'google-reviews-plugin'),
            __('Sent At', 'google-reviews-plugin'),
            __('Clicked At', 'google-reviews-plugin'),
            __('Coupon Code', 'google-reviews-plugin'),
            __('Rewarded At', 'google-reviews-plugin'),
            __('Created At', 'google-reviews-plugin'),
        );
        echo implode("\t", $headers) . "\n";
        
        // XLS rows
        foreach ($invites as $invite) {
            $row = array(
                $invite->id,
                $invite->order_id,
                $invite->email,
                $invite->invite_status,
                $invite->scheduled_at ?: '',
                $invite->sent_at ?: '',
                $invite->clicked_at ?: '',
                $invite->coupon_code ?: '',
                $invite->rewarded_at ?: '',
                $invite->created_at,
            );
            // Escape tabs and newlines in cell values
            $row = array_map(function($cell) {
                return str_replace(array("\t", "\n", "\r"), array(' ', ' ', ' '), $cell);
            }, $row);
            echo implode("\t", $row) . "\n";
        }
        
        exit;
    }
    
    /**
     * AJAX handler for saving custom CSS
     */
    public function ajax_save_custom_css() {
        check_ajax_referer('grp_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions.', 'google-reviews-plugin')));
            return;
        }
        
        $css = isset($_POST['css']) ? wp_strip_all_tags($_POST['css']) : '';
        
        // Save custom CSS
        update_option('grp_custom_css', $css);
        
        wp_send_json_success(array(
            'message' => __('Custom CSS saved successfully.', 'google-reviews-plugin'),
            'css' => $css
        ));
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
    
    /**
     * Sanitize location ID - remove any prefixes
     */
    public function sanitize_location_id($value) {
        if ($value === null) {
            return '';
        }
        $sanitized = sanitize_text_field($value);
        // Remove any location resource name prefixes
        $sanitized = preg_replace('#^(accounts/[^/]+/)?locations/?#', '', $sanitized);
        return $sanitized;
    }
    
    /**
     * Save account label when account ID is saved
     */
    public function save_account_label($old_value, $value) {
        // Try to get label from cached accounts
        $cached_accounts = get_transient('grp_accounts_list');
        if ($cached_accounts && is_array($cached_accounts)) {
            foreach ($cached_accounts as $acc) {
                if (isset($acc['id']) && $acc['id'] === $value) {
                    if (isset($acc['label'])) {
                        update_option('grp_google_account_label', $acc['label']);
                        return;
                    }
                }
                // Also check numeric ID
                if (isset($acc['numeric_id']) && $acc['numeric_id'] === $value) {
                    if (isset($acc['label'])) {
                        update_option('grp_google_account_label', $acc['label']);
                        return;
                    }
                }
            }
        }
        // If not found in cache, try stale cache
        $stale_accounts = get_option('grp_accounts_cache_stale', array());
        if ($stale_accounts && is_array($stale_accounts)) {
            foreach ($stale_accounts as $acc) {
                if (isset($acc['id']) && $acc['id'] === $value) {
                    if (isset($acc['label'])) {
                        update_option('grp_google_account_label', $acc['label']);
                        return;
                    }
                }
                if (isset($acc['numeric_id']) && $acc['numeric_id'] === $value) {
                    if (isset($acc['label'])) {
                        update_option('grp_google_account_label', $acc['label']);
                        return;
                    }
                }
            }
        }
        // If label not found, clear it
        if (empty($value)) {
            delete_option('grp_google_account_label');
        }
    }
    
    /**
     * Save location label when location ID is saved
     */
    public function save_location_label($old_value, $value) {
        $account_id = get_option('grp_google_account_id', '');
        if (empty($account_id)) {
            return;
        }
        
        // Try to get label from cached locations
        $cache_key = 'grp_locations_' . md5($account_id);
        $cached_locations = get_transient($cache_key);
        if ($cached_locations && is_array($cached_locations)) {
            foreach ($cached_locations as $loc) {
                if (isset($loc['id']) && $loc['id'] === $value) {
                    if (isset($loc['label'])) {
                        update_option('grp_google_location_label', $loc['label']);
                        return;
                    }
                }
            }
        }
        // If not found in cache, try stale cache
        $stale_locations = get_option('grp_locations_cache_' . md5($account_id), array());
        if ($stale_locations && is_array($stale_locations)) {
            foreach ($stale_locations as $loc) {
                if (isset($loc['id']) && $loc['id'] === $value) {
                    if (isset($loc['label'])) {
                        update_option('grp_google_location_label', $loc['label']);
                        return;
                    }
                }
            }
        }
        // If label not found, clear it
        if (empty($value)) {
            delete_option('grp_google_location_label');
        }
    }
}