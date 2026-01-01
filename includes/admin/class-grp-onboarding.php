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
        try {
            // Check if user manually triggered onboarding
            $restart_onboarding = isset($_GET['restart_onboarding']) && $_GET['restart_onboarding'] === '1';
            if ($restart_onboarding) {
                // Reset onboarding status
                delete_option('grp_onboarding_complete');
                delete_user_meta(get_current_user_id(), 'grp_onboarding_dismissed');
                // Note: Modal and assets will be handled by should_show_onboarding check
            }
            
            // Check if user wants to skip onboarding (via URL parameter)
            $skip_onboarding = isset($_GET['skip_onboarding']) && $_GET['skip_onboarding'] === '1';
            if ($skip_onboarding) {
                // Mark onboarding as complete/dismissed
                update_option('grp_onboarding_complete', true);
                delete_user_meta(get_current_user_id(), 'grp_onboarding_dismissed');
                // Redirect to remove the parameter
                wp_safe_redirect(admin_url('admin.php?page=google-reviews-settings'));
                exit;
            }
            
            // Check if onboarding should be shown
            if ($this->should_show_onboarding()) {
                // Show onboarding modal
                add_action('admin_footer', array($this, 'render_onboarding_modal'));
            }
        } catch (Exception $e) {
            error_log('GRP Onboarding: Error in check_onboarding_status: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            // If restart was requested, still try to show modal
            if (isset($_GET['restart_onboarding']) && $_GET['restart_onboarding'] === '1') {
                add_action('admin_footer', array($this, 'render_onboarding_modal'));
            }
        } catch (Error $e) {
            error_log('GRP Onboarding: Fatal error in check_onboarding_status: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
            // If restart was requested, still try to show modal
            if (isset($_GET['restart_onboarding']) && $_GET['restart_onboarding'] === '1') {
                add_action('admin_footer', array($this, 'render_onboarding_modal'));
            }
        }
    }
    
    /**
     * Enqueue onboarding assets
     */
    public function enqueue_assets($hook) {
        // Only on plugin admin pages
        if (strpos($hook, 'google-reviews') === false) {
            return;
        }
        
        // Check if onboarding should be shown (for asset enqueueing purposes)
        try {
            $should_show = $this->should_show_onboarding();
        } catch (Exception $e) {
            error_log('GRP Onboarding: Error checking should_show: ' . $e->getMessage());
            // If there's an error, still try to show onboarding if restart parameter is present
            $should_show = isset($_GET['restart_onboarding']) && $_GET['restart_onboarding'] === '1';
        } catch (Error $e) {
            error_log('GRP Onboarding: Fatal error checking should_show: ' . $e->getMessage());
            $should_show = isset($_GET['restart_onboarding']) && $_GET['restart_onboarding'] === '1';
        }
        
        // Only enqueue assets if onboarding should be shown
        if (!$should_show) {
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
        
        // Check if Google is already connected - handle errors gracefully
        $is_google_connected = false;
        $google_connected = false;
        try {
            if (class_exists('GRP_API')) {
                $api = new GRP_API();
                $is_google_connected = $api->is_connected();
            }
        } catch (Exception $e) {
            error_log('GRP Onboarding: Error checking Google connection: ' . $e->getMessage());
        } catch (Error $e) {
            error_log('GRP Onboarding: Fatal error checking Google connection: ' . $e->getMessage());
        }
        
        $has_account_id = !empty(get_option('grp_google_account_id', ''));
        $has_location_id = !empty(get_option('grp_google_location_id', ''));
        $google_connected = $is_google_connected && ($has_account_id || $has_location_id);
        
        wp_localize_script('grp-onboarding', 'grpOnboarding', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'google_connected' => $google_connected,
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
     * Check if onboarding should be shown (helper method)
     */
    private function should_show_onboarding() {
        try {
            // Only show on plugin admin pages
            if (!isset($_GET['page']) || strpos($_GET['page'], 'google-reviews') === false) {
                return false;
            }
            
            // Don't show on OAuth callback page
            if (isset($_GET['action']) && $_GET['action'] === 'oauth_callback') {
                return false;
            }
            
            // Check if user manually triggered onboarding
            $restart_onboarding = isset($_GET['restart_onboarding']) && $_GET['restart_onboarding'] === '1';
            if ($restart_onboarding) {
                return true;
            }
            
            // Check if onboarding is complete
            $onboarding_complete = get_option('grp_onboarding_complete', false);
            if ($onboarding_complete) {
                return false;
            }
            
            // Check if user dismissed onboarding
            $onboarding_dismissed = get_user_meta(get_current_user_id(), 'grp_onboarding_dismissed', true);
            if ($onboarding_dismissed) {
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            error_log('GRP Onboarding: Error in should_show_onboarding: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            // If restart was requested, always show
            return isset($_GET['restart_onboarding']) && $_GET['restart_onboarding'] === '1';
        } catch (Error $e) {
            error_log('GRP Onboarding: Fatal error in should_show_onboarding: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
            // If restart was requested, always show
            return isset($_GET['restart_onboarding']) && $_GET['restart_onboarding'] === '1';
        }
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
                        
                        // If it's a temporary error (503, timeout, etc.), allow proceeding
                        // User can activate license later from settings
                        $error_code = $result->get_error_code();
                        $is_temporary_error = (
                            strpos($error_code, 'service_unavailable') !== false ||
                            strpos($error_code, 'timeout') !== false ||
                            strpos($error_code, '503') !== false ||
                            strpos($result->get_error_message(), '503') !== false
                        );
                        
                        if ($is_temporary_error) {
                            // Log but continue - license activation can happen later
                            error_log('[GRP Onboarding] Free license activation failed (temporary error), continuing onboarding: ' . $result->get_error_message());
                        } else {
                            // Permanent error - still allow proceeding but show warning
                            error_log('[GRP Onboarding] Free license activation failed, continuing onboarding: ' . $result->get_error_message());
                            // Don't block onboarding - user can activate later
                        }
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
                // Check if already connected
                $api = new GRP_API();
                $is_connected = $api->is_connected();
                $has_account_id = !empty(get_option('grp_google_account_id', ''));
                $has_location_id = !empty(get_option('grp_google_location_id', ''));
                
                if ($is_connected && ($has_account_id || $has_location_id)) {
                    // Already connected, skip to next step
                    wp_send_json_success(array(
                        'message' => __('Google account is already connected!', 'google-reviews-plugin'),
                        'next_step' => 'place_id',
                        'already_connected' => true
                    ));
                } else {
                    // Not connected yet, mark as ready - actual connection happens via existing OAuth flow
                    wp_send_json_success(array(
                        'message' => __('Ready for Google connection', 'google-reviews-plugin'),
                        'next_step' => 'place_id'
                    ));
                }
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
            
            // For 503 errors, return a more specific error code
            if ($response_code === 503) {
                return new WP_Error('license_activation_service_unavailable', __('License server is temporarily unavailable. You can activate your license later from Settings.', 'google-reviews-plugin'));
            }
            
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

