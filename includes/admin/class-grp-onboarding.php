<?php
/**
 * Onboarding wizard class
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Onboarding {
    
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
        add_action('admin_init', array($this, 'check_onboarding_status'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_ajax_grp_onboarding_step', array($this, 'ajax_handle_onboarding_step'));
        add_action('wp_ajax_grp_skip_onboarding', array($this, 'skip_onboarding'));
        add_action('wp_ajax_grp_restart_onboarding', array($this, 'ajax_restart_onboarding'));
    }
    
    /**
     * Check if onboarding should be shown
     */
    public function check_onboarding_status() {
        // Only show on plugin admin pages
        if (!isset($_GET['page']) || strpos($_GET['page'], 'google-reviews') === false) {
            return;
        }
        
        // Don't show on OAuth callback page
        if (isset($_GET['action']) && $_GET['action'] === 'oauth_callback') {
            return;
        }
        
        // Check if user manually triggered onboarding
        $restart_onboarding = isset($_GET['restart_onboarding']) && $_GET['restart_onboarding'] === '1';
        if ($restart_onboarding) {
            // Reset onboarding status
            delete_option('grp_onboarding_complete');
            delete_user_meta(get_current_user_id(), 'grp_onboarding_dismissed');
            // Show onboarding modal
            add_action('admin_footer', array($this, 'render_onboarding_modal'));
            // Enqueue assets
            add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
            return;
        }
        
        // Check if onboarding is complete
        $onboarding_complete = get_option('grp_onboarding_complete', false);
        if ($onboarding_complete) {
            return;
        }
        
        // Check if user dismissed onboarding
        $onboarding_dismissed = get_user_meta(get_current_user_id(), 'grp_onboarding_dismissed', true);
        if ($onboarding_dismissed) {
            return;
        }
        
        // Show onboarding modal
        add_action('admin_footer', array($this, 'render_onboarding_modal'));
    }
    
    /**
     * Enqueue onboarding assets
     */
    public function enqueue_assets($hook) {
        // Only on plugin admin pages
        if (strpos($hook, 'google-reviews') === false) {
            return;
        }
        
        wp_enqueue_style(
            'grp-onboarding',
            GRP_PLUGIN_URL . 'assets/css/onboarding.css',
            array(),
            GRP_PLUGIN_VERSION
        );
        
        wp_enqueue_script(
            'grp-onboarding',
            GRP_PLUGIN_URL . 'assets/js/onboarding.js',
            array('jquery'),
            GRP_PLUGIN_VERSION,
            true
        );
        
        wp_localize_script('grp-onboarding', 'grpOnboarding', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('grp_onboarding_nonce'),
            'strings' => array(
                'next' => __('Next', 'google-reviews-plugin'),
                'back' => __('Back', 'google-reviews-plugin'),
                'skip' => __('Skip Setup', 'google-reviews-plugin'),
                'finish' => __('Finish Setup', 'google-reviews-plugin'),
                'loading' => __('Loading...', 'google-reviews-plugin'),
            )
        ));
    }
    
    /**
     * Render onboarding modal
     */
    public function render_onboarding_modal() {
        include GRP_PLUGIN_DIR . 'includes/admin/views/onboarding-modal.php';
    }
    
    /**
     * AJAX handler for onboarding steps
     */
    public function ajax_handle_onboarding_step() {
        check_ajax_referer('grp_onboarding_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'google-reviews-plugin')));
        }
        
        $step = isset($_POST['step']) ? sanitize_text_field($_POST['step']) : '';
        $data = isset($_POST['data']) ? map_deep($_POST['data'], 'sanitize_text_field') : array();
        
        switch ($step) {
            case 'welcome':
                // Step 1: Welcome and optional registration
                $name = $data['name'] ?? '';
                $email = $data['email'] ?? '';
                $has_license = isset($data['has_license']) && ($data['has_license'] === true || $data['has_license'] === '1' || $data['has_license'] === 'true');
                $license_key = isset($data['license_key']) ? sanitize_text_field($data['license_key']) : '';
        
                // Validate email if provided
                if (!empty($email) && !is_email($email)) {
                    wp_send_json_error(array('message' => __('Please enter a valid email address or leave it blank.', 'google-reviews-plugin')));
                }
        
                // Handle license key if provided
                if ($has_license && !empty($license_key)) {
                    $license = new GRP_License();
                    $result = $license->activate_license($license_key);
                    if (is_wp_error($result)) {
                        grp_debug_log('Onboarding license activation failed', array(
                            'error' => $result->get_error_message(),
                            'code' => $result->get_error_code(),
                            'license_key_length' => strlen($license_key)
                        ));
                        wp_send_json_error(array('message' => sprintf(__('Failed to activate license: %s', 'google-reviews-plugin'), $result->get_error_message())));
                    }
                } elseif (!empty($email)) {
                    // Only activate free license if no license key was provided and email is provided
                    $result = $this->activate_free_license($name, $email);
                    if (is_wp_error($result)) {
                        grp_debug_log('Onboarding free license activation failed', array(
                            'error' => $result->get_error_message(),
                            'code' => $result->get_error_code()
                        ));
                        wp_send_json_error(array('message' => $result->get_error_message()));
                    }
                } else {
                    // Email is optional - allow proceeding without free license if user skipped
                    // They can activate it later from settings
                }
                
                wp_send_json_success(array(
                    'message' => __('Welcome step completed', 'google-reviews-plugin'),
                    'next_step' => 'google_connect'
                ));
                break;
                
            case 'google_connect':
                // Step 2: Google OAuth connection
                // Just mark as ready - actual connection happens via existing OAuth flow
                wp_send_json_success(array(
                    'message' => __('Ready for Google connection', 'google-reviews-plugin'),
                    'next_step' => 'place_id'
                ));
                break;
                
            case 'place_id':
                // Step 3: Place ID input (optional)
                $place_id = isset($data['place_id']) ? sanitize_text_field($data['place_id']) : '';
                
                if (!empty($place_id)) {
                    update_option('grp_place_id', $place_id);
                }
                
                // Mark onboarding as complete
                update_option('grp_onboarding_complete', true);
                
                wp_send_json_success(array(
                    'message' => __('Onboarding complete!', 'google-reviews-plugin'),
                    'redirect' => admin_url('admin.php?page=google-reviews-settings')
                ));
                break;
                
            default:
                wp_send_json_error(array('message' => __('Invalid step', 'google-reviews-plugin')));
        }
    }
    
    /**
     * Activate free license
     */
    private function activate_free_license($name, $email) {
        // Get site domain - handle parse_url returning null (PHP 8.1+)
        $domain = parse_url(home_url(), PHP_URL_HOST);
        if (empty($domain)) {
            return new WP_Error('invalid_domain', __('Unable to determine site domain. Please check your site URL settings.', 'google-reviews-plugin'));
        }
        
        // Build request body (email and name are optional)
        // SECURITY: Never send package_type - server will force it to 'goorev-free'
        $request_body = array(
            'domain' => $domain,
            'plugin' => 'goorev',
            'plugin_version' => GRP_PLUGIN_VERSION
        );
        
        if (!empty($name)) {
            $request_body['name'] = $name;
        }
        
        if (!empty($email)) {
            $request_body['email'] = $email;
        }
        
        // SECURITY: Add HMAC signature for authentication
        // Use the same secret as the license class
        $plugin_secret = defined('GRP_PLUGIN_SECRET') ? GRP_PLUGIN_SECRET : 'goorev-free-license-secret-key-2024';
        $timestamp = time();
        $signature = hash_hmac('sha256', $domain . ':goorev:' . $timestamp, $plugin_secret);
        
        // Add signature and timestamp to request body (server expects them in body, not headers)
        $request_body['signature'] = $signature;
        $request_body['timestamp'] = $timestamp;
        
        // Call license server to create/activate free license
        $response = wp_remote_post(GRP_License::LICENSE_API_URL . 'api/v1/license/activate-free', array(
            'body' => json_encode($request_body),
            'timeout' => 15,
            'headers' => array(
                'Content-Type' => 'application/json',
                'User-Agent' => 'GooRev-Plugin/' . GRP_PLUGIN_VERSION
            )
        ));
        
        if (is_wp_error($response)) {
            grp_debug_log('Free license activation failed - wp_remote_post error', array(
                'error' => $response->get_error_message(),
                'code' => $response->get_error_code()
            ));
            return $response;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        
        // Log response for debugging
        grp_debug_log('Free license activation response', array(
            'code' => $response_code,
            'body' => $response_body,
            'decoded' => $data
        ));
        
        if ($response_code >= 400) {
            $error_message = isset($data['error']) ? $data['error'] : (isset($data['message']) ? $data['message'] : __('License server returned an error', 'google-reviews-plugin'));
            return new WP_Error('license_activation_failed', $error_message);
        }
        
        if (isset($data['success']) && $data['success']) {
            // Store license key and data
            if (isset($data['license_key'])) {
                update_option('grp_license_key', $data['license_key']);
            }
            if (isset($data['license_data'])) {
                update_option('grp_license_data', $data['license_data']);
                update_option('grp_license_status', GRP_License::STATUS_VALID);
                
                // Store JWT token separately for API requests
                // accessToken can be a string or an object with a 'token' property
                if (isset($data['license_data']['accessToken'])) {
                    $access_token = $data['license_data']['accessToken'];
                    // Handle both formats: string token or object with token property
                    if (is_array($access_token) && isset($access_token['token'])) {
                        update_option('grp_license_jwt_token', $access_token['token']);
                    } elseif (is_string($access_token)) {
                        update_option('grp_license_jwt_token', $access_token);
                    }
                }
            }
            
            return true;
        }
        
        // If we get here, activation failed
        $error_message = isset($data['error']) ? $data['error'] : (isset($data['message']) ? $data['message'] : __('Failed to activate free license', 'google-reviews-plugin'));
        return new WP_Error('license_activation_failed', $error_message);
    }
    
    /**
     * Skip onboarding
     */
    public function skip_onboarding() {
        check_ajax_referer('grp_onboarding_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'google-reviews-plugin')));
        }
        
        // Mark as dismissed for this user
        update_user_meta(get_current_user_id(), 'grp_onboarding_dismissed', true);
        update_option('grp_onboarding_complete', true);
        
        wp_send_json_success(array(
            'message' => __('Onboarding skipped', 'google-reviews-plugin')
        ));
    }
    
    /**
     * AJAX handler to restart onboarding
     */
    public function ajax_restart_onboarding() {
        check_ajax_referer('grp_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'google-reviews-plugin')));
        }
        
        // Reset onboarding status
        delete_option('grp_onboarding_complete');
        delete_user_meta(get_current_user_id(), 'grp_onboarding_dismissed');
        
        wp_send_json_success(array(
            'message' => __('Onboarding wizard restarted. Please refresh the page.', 'google-reviews-plugin'),
            'redirect' => admin_url('admin.php?page=google-reviews&restart_onboarding=1')
        ));
    }
}

