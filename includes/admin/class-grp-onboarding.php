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
        add_action('wp_ajax_grp_onboarding_step', array($this, 'handle_onboarding_step'));
        add_action('wp_ajax_grp_skip_onboarding', array($this, 'skip_onboarding'));
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
     * Handle onboarding step submission
     */
    public function handle_onboarding_step() {
        check_ajax_referer('grp_onboarding_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'google-reviews-plugin')));
        }
        
        $step = isset($_POST['step']) ? sanitize_text_field($_POST['step']) : '';
        $data = isset($_POST['data']) ? $_POST['data'] : array();
        
        switch ($step) {
            case 'welcome':
                // Step 1: Collect name/email and activate free license
                $name = isset($data['name']) ? sanitize_text_field($data['name']) : '';
                $email = isset($data['email']) ? sanitize_email($data['email']) : '';
                
                // Always try to activate free license (email/name are optional)
                $result = $this->activate_free_license($name, $email);
                if (is_wp_error($result)) {
                    // Log error but don't block onboarding - free license might already exist
                    error_log('[GRP Onboarding] Free license activation: ' . $result->get_error_message());
                    // Continue anyway - user might already have a license
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
        // Get site domain
        $domain = parse_url(home_url(), PHP_URL_HOST);
        
        // Build request body (email and name are optional)
        // SECURITY: Never send package_type - server will force it to 'goorev-free'
        $body = array(
            'domain' => $domain,
            'plugin' => 'goorev',
            'plugin_version' => GRP_PLUGIN_VERSION
        );
        
        if (!empty($name)) {
            $body['name'] = $name;
        }
        
        if (!empty($email)) {
            $body['email'] = $email;
        }
        
        // SECURITY: Add HMAC signature for authentication
        // Use a shared secret that matches the server's GOOREV_PLUGIN_SECRET
        $plugin_secret = 'goorev-free-license-secret-key-2024'; // Should match server env var
        $timestamp = time();
        $signature = hash_hmac('sha256', $domain . ':goorev:' . $timestamp, $plugin_secret);
        
        $body['signature'] = $signature;
        $body['timestamp'] = $timestamp;
        
        // Call license server to create/activate free license
        $response = wp_remote_post(GRP_License::LICENSE_API_URL . 'api/v1/license/activate-free', array(
            'body' => json_encode($body),
            'timeout' => 15,
            'headers' => array(
                'Content-Type' => 'application/json',
                'User-Agent' => 'GooRev-Plugin/' . GRP_PLUGIN_VERSION
            )
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['success']) && $data['success']) {
            // Store license key and data
            if (isset($data['license_key'])) {
                update_option('grp_license_key', $data['license_key']);
            }
            if (isset($data['license_data'])) {
                update_option('grp_license_data', $data['license_data']);
                update_option('grp_license_status', GRP_License::STATUS_VALID);
            }
            
            return true;
        }
        
        return new WP_Error('license_activation_failed', $data['message'] ?? __('Failed to activate free license', 'google-reviews-plugin'));
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
}

