<?php
/**
 * License management class
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_License {
    
    /**
     * License API endpoint
     */
    const LICENSE_API_URL = 'https://license.reactwoo.com/';
    
    /**
     * License statuses
     */
    const STATUS_VALID = 'valid';
    const STATUS_INVALID = 'invalid';
    const STATUS_EXPIRED = 'expired';
    const STATUS_DEACTIVATED = 'deactivated';
    
    /**
     * Static flag to track if hooks have been registered
     */
    private static $hooks_registered = false;
    
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
        // Only register hooks once to prevent duplicates
        if (self::$hooks_registered) {
            return;
        }
        
        add_action('admin_init', array($this, 'handle_license_actions'));
        add_action('grp_check_license', array($this, 'check_license_status'));
        add_action('admin_notices', array($this, 'show_license_notices'));
        
        self::$hooks_registered = true;
    }
    
    /**
     * Get license key
     */
    public function get_license_key() {
        return get_option('grp_license_key', '');
    }
    
    /**
     * Get license status
     */
    public function get_license_status() {
        return get_option('grp_license_status', self::STATUS_INVALID);
    }
    
    /**
     * Get license data
     */
    public function get_license_data() {
        return get_option('grp_license_data', array());
    }
    
    /**
     * Check if any valid license is active (free, pro, or enterprise)
     */
    public function has_license() {
        $status = $this->get_license_status();
        return $status === self::STATUS_VALID;
    }
    
    /**
     * Check if pro version is active (pro or enterprise)
     */
    public function is_pro() {
        if (!$this->has_license()) {
            return false;
        }
        
        $license_data = $this->get_license_data();
        $package_type = $license_data['packageType'] ?? $license_data['package_type'] ?? '';
        
        // Free licenses are not "pro"
        $package_type_lower = strtolower($package_type);
        if (in_array($package_type_lower, array('free', 'goorev-free', 'basic'))) {
            return false;
        }
        
        // Pro or Enterprise are both "pro" tier
        return true;
    }
    
    /**
     * Check if free license is active
     */
    public function is_free() {
        if (!$this->has_license()) {
            return false;
        }
        
        $license_data = $this->get_license_data();
        $package_type = $license_data['packageType'] ?? $license_data['package_type'] ?? '';
        
        // Check for free package type
        $package_type_lower = strtolower($package_type);
        return in_array($package_type_lower, array('free', 'goorev-free', 'basic'));
    }
    
    /**
     * Check if enterprise version is active
     */
    public function is_enterprise() {
        if (!$this->is_pro()) {
            return false;
        }
        
        $license_data = $this->get_license_data();
        $package_type = $license_data['packageType'] ?? $license_data['package_type'] ?? '';
        
        // Check for enterprise package type
        return in_array(strtolower($package_type), array('enterprise', 'goorev-enterprise', 'enterprise'));
    }
    
    /**
     * Activate license
     */
    public function activate_license($license_key) {
        $response = $this->make_license_request('activate', array(
            'license_key' => $license_key,
            'site_url' => home_url(),
            'plugin_version' => GRP_PLUGIN_VERSION,
            'plugin_slug' => 'goorev'
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        if ($response['success']) {
            update_option('grp_license_key', $license_key);
            update_option('grp_license_status', self::STATUS_VALID);
            
            // License server returns: { success: true, accessToken, refreshToken, expires_at }
            $license_data = array(
                'accessToken' => $response['accessToken'] ?? $response['access_token'] ?? '',
                'refreshToken' => $response['refreshToken'] ?? $response['refresh_token'] ?? '',
                'packageType' => $response['packageType'] ?? $response['package_type'] ?? '',
                'pluginSlug' => $response['pluginSlug'] ?? $response['plugin_slug'] ?? 'goorev',
                'expires_at' => $response['expires_at'] ?? $response['expires_in'] ?? null,
                'license_id' => $response['license']['id'] ?? $response['licenseId'] ?? null
            );
            update_option('grp_license_data', $license_data);
            
            // Store JWT token separately for API requests
            $jwt_token = $response['accessToken'] ?? $response['access_token'] ?? '';
            if (!empty($jwt_token)) {
                update_option('grp_license_jwt_token', $jwt_token);
            }
            
            // Schedule license check
            $this->schedule_license_check();
            
            return true;
        }
        
        return new WP_Error('activation_failed', $response['message'] ?? __('License activation failed', 'google-reviews-plugin'));
    }
    
    /**
     * Activate free license (for WordPress.org compliance - no registration required)
     */
    public function activate_free_license() {
        // Get site domain
        $domain = parse_url(home_url(), PHP_URL_HOST);
        
        // Build request body
        $body = array(
            'domain' => $domain,
            'plugin' => 'goorev',
            'plugin_version' => GRP_PLUGIN_VERSION
        );
        
        // SECURITY: Add HMAC signature for authentication
        $plugin_secret = defined('GRP_PLUGIN_SECRET') ? GRP_PLUGIN_SECRET : 'goorev-free-license-secret-key-2024';
        $timestamp = time();
        $signature = hash_hmac('sha256', $domain . ':goorev:' . $timestamp, $plugin_secret);
        
        // Call license server to create/activate free license
        $response = wp_remote_post(self::LICENSE_API_URL . 'api/v1/license/activate-free', array(
            'body' => json_encode(array(
                'domain' => $domain,
                'plugin' => 'goorev',
                'plugin_version' => GRP_PLUGIN_VERSION
            )),
            'timeout' => 15,
            'headers' => array(
                'Content-Type' => 'application/json',
                'X-HMAC-Signature' => $signature,
                'X-Timestamp' => $timestamp,
                'User-Agent' => 'GooRev-Plugin/' . GRP_PLUGIN_VERSION
            )
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        
        if (isset($data['success']) && $data['success']) {
            // Store license key and data
            if (isset($data['license_key'])) {
                update_option('grp_license_key', $data['license_key']);
            }
            if (isset($data['license_data'])) {
                update_option('grp_license_data', $data['license_data']);
                update_option('grp_license_status', self::STATUS_VALID);
                
                // Store JWT token separately for API requests
                if (isset($data['license_data']['accessToken'])) {
                    update_option('grp_license_jwt_token', $data['license_data']['accessToken']);
                }
            }
            
            // Schedule license check
            $this->schedule_license_check();
            
            return true;
        }
        
        return new WP_Error('free_license_activation_failed', $data['error'] ?? $data['message'] ?? __('Failed to activate free license', 'google-reviews-plugin'));
    }
    
    /**
     * Deactivate license
     */
    public function deactivate_license() {
        $license_key = $this->get_license_key();
        
        if (empty($license_key)) {
            return true;
        }
        
        $response = $this->make_license_request('deactivate', array(
            'license_key' => $license_key,
            'site_url' => home_url(),
            'plugin_slug' => 'goorev'
        ));
        
        // Always deactivate locally, even if remote request fails
        delete_option('grp_license_key');
        delete_option('grp_license_status');
        delete_option('grp_license_data');
        
        $this->unschedule_license_check();
        
        return true;
    }
    
    /**
     * Check license status
     */
    public function check_license_status() {
        $license_key = $this->get_license_key();
        
        if (empty($license_key)) {
            return false;
        }
        
        $response = $this->make_license_request('check', array(
            'license_key' => $license_key,
            'site_url' => home_url(),
            'plugin_slug' => 'goorev'
        ));
        
        if (is_wp_error($response)) {
            return false;
        }
        
        if ($response['success']) {
            // License server returns status in response
            $status = $response['status'] ?? $response['data']['status'] ?? self::STATUS_INVALID;
            update_option('grp_license_status', $status);
            
            // Update license data if provided
            if (isset($response['data'])) {
                update_option('grp_license_data', $response['data']);
            }
            
            // Update JWT token if provided
            if (isset($response['accessToken']) || isset($response['access_token'])) {
                $token = $response['accessToken'] ?? $response['access_token'];
                update_option('grp_license_jwt_token', $token);
            }
            
            return $status === self::STATUS_VALID;
        }
        
        // If check fails, mark as invalid
        update_option('grp_license_status', self::STATUS_INVALID);
        return false;
    }
    
    /**
     * Make license API request
     */
    private function make_license_request($action, $data) {
        // License server uses direct routes:
        // - /activate (POST) - for activation (validates and returns tokens)
        // - /deactivate (POST) - for deactivation
        // - For check, we use activate endpoint which validates the license
        $endpoint_map = array(
            'activate' => 'activate',
            'check' => 'activate', // Use activate endpoint to validate
            'deactivate' => 'deactivate'
        );
        
        $endpoint = $endpoint_map[$action] ?? $action;
        $url = self::LICENSE_API_URL . $endpoint;
        
        // Convert data to JSON
        // License server expects: licenseKey (or license_key), domain, pluginVersion (or plugin_version), pluginSlug (or plugin_slug)
        // Extract domain from URL (remove protocol and path)
        $site_url = $data['site_url'] ?? $data['domain'] ?? home_url();
        $domain = $site_url;
        if (preg_match('#^https?://([^/]+)#', $site_url, $matches)) {
            $domain = $matches[1];
        } elseif (strpos($site_url, '://') === false && strpos($site_url, '/') === false) {
            // Already a domain
            $domain = $site_url;
        }
        // Remove www. prefix if present (optional, but cleaner)
        $domain = preg_replace('#^www\.#', '', $domain);
        
        $request_data = array(
            'licenseKey' => $data['license_key'] ?? $data['licenseKey'] ?? '',
            'domain' => $domain,
            'pluginVersion' => $data['plugin_version'] ?? $data['pluginVersion'] ?? GRP_PLUGIN_VERSION,
            'pluginSlug' => $data['plugin_slug'] ?? $data['pluginSlug'] ?? 'goorev'
        );
        
        $body = wp_json_encode($request_data);
        
        $response = wp_remote_post($url, array(
            'body' => $body,
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            )
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $response_body = wp_remote_retrieve_body($response);
        $status_code = wp_remote_retrieve_response_code($response);
        
        $decoded = json_decode($response_body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('invalid_response', __('Invalid response from license server', 'google-reviews-plugin'));
        }
        
        // Handle error responses
        if ($status_code >= 400) {
            $error_message = isset($decoded['error']) ? $decoded['error'] : __('License server error', 'google-reviews-plugin');
            if (isset($decoded['details'])) {
                $error_message .= ': ' . $decoded['details'];
            }
            return new WP_Error('license_server_error', $error_message);
        }
        
        // License server response format:
        // - /activate returns: { success: true, accessToken, refreshToken, expires_at }
        // - /deactivate returns: { success: true, message: '...' }
        // - For check, we'll use activate endpoint which validates and returns tokens
        
        if ($action === 'activate') {
            // Response already has success: true and tokens
            return $decoded;
        }
        
        // For check action, use activate endpoint to validate
        if ($action === 'check') {
            // Use activate endpoint which validates the license
            // If it returns success, license is valid
            if (isset($decoded['success']) && $decoded['success']) {
                return array(
                    'success' => true,
                    'status' => self::STATUS_VALID,
                    'data' => $decoded
                );
            } else {
                return array(
                    'success' => false,
                    'status' => self::STATUS_INVALID,
                    'data' => $decoded
                );
            }
        }
        
        // For deactivate, response already has success
        if ($action === 'deactivate') {
            return $decoded;
        }
        
        return $decoded;
    }
    
    /**
     * Handle license actions
     */
    public function handle_license_actions() {
        if (!isset($_POST['grp_license_action'])) {
            return;
        }
        
        if (!current_user_can('manage_options')) {
            return;
        }
        
        check_admin_referer('grp_license_nonce');
        
        $action = sanitize_text_field($_POST['grp_license_action']);
        
        switch ($action) {
            case 'activate':
                $license_key = sanitize_text_field($_POST['grp_license_key']);
                $result = $this->activate_license($license_key);
                
                if (is_wp_error($result)) {
                    add_action('admin_notices', function() use ($result) {
                        echo '<div class="notice notice-error"><p>' . esc_html($result->get_error_message()) . '</p></div>';
                    });
                } else {
                    add_action('admin_notices', function() {
                        echo '<div class="notice notice-success"><p>' . esc_html__('License activated successfully!', 'google-reviews-plugin') . '</p></div>';
                    });
                }
                break;
                
            case 'deactivate':
                $this->deactivate_license();
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-success"><p>' . esc_html__('License deactivated successfully!', 'google-reviews-plugin') . '</p></div>';
                });
                break;
                
            case 'check':
                $result = $this->check_license_status();
                if ($result) {
                    add_action('admin_notices', function() {
                        echo '<div class="notice notice-success"><p>' . esc_html__('License token refreshed successfully!', 'google-reviews-plugin') . '</p></div>';
                    });
                } else {
                    add_action('admin_notices', function() {
                        echo '<div class="notice notice-error"><p>' . esc_html__('Failed to refresh license token. Please reactivate your license.', 'google-reviews-plugin') . '</p></div>';
                    });
                }
                break;
        }
    }
    
    /**
     * Show license notices
     */
    public function show_license_notices() {
        $screen = get_current_screen();
        
        if (!$screen || strpos($screen->id, 'google-reviews') === false) {
            return;
        }
        
        $status = $this->get_license_status();
        
        if ($status === self::STATUS_INVALID) {
            echo '<div class="notice notice-warning"><p>';
            printf(
                __('Google Reviews Plugin Pro features are not available. <a href="%s">Enter your license key</a> to unlock all features.', 'google-reviews-plugin'),
                admin_url('admin.php?page=google-reviews-settings&tab=license')
            );
            echo '</p></div>';
        } elseif ($status === self::STATUS_EXPIRED) {
            echo '<div class="notice notice-error"><p>';
            printf(
                __('Your license has expired. <a href="%s">Renew your license</a> to continue using Pro features.', 'google-reviews-plugin'),
                admin_url('admin.php?page=google-reviews-settings&tab=license')
            );
            echo '</p></div>';
        }
    }
    
    /**
     * Schedule license check
     */
    public function schedule_license_check() {
        if (!wp_next_scheduled('grp_check_license')) {
            wp_schedule_event(time(), 'daily', 'grp_check_license');
        }
    }
    
    /**
     * Unschedule license check
     */
    public function unschedule_license_check() {
        wp_clear_scheduled_hook('grp_check_license');
    }
    
    /**
     * Get pro features
     */
    public function get_pro_features() {
        return array(
            'multiple_locations' => array(
                'name' => __('Multiple Locations', 'google-reviews-plugin'),
                'description' => __('Connect multiple Google Business locations', 'google-reviews-plugin')
            ),
            'product_integration' => array(
                'name' => __('Product Integration', 'google-reviews-plugin'),
                'description' => __('Link reviews to specific products or services', 'google-reviews-plugin')
            ),
            'advanced_customization' => array(
                'name' => __('Advanced Customization', 'google-reviews-plugin'),
                'description' => __('Custom CSS, advanced styling options, and template builder', 'google-reviews-plugin')
            ),
            'analytics_dashboard' => array(
                'name' => __('Analytics Dashboard', 'google-reviews-plugin'),
                'description' => __('Detailed analytics and performance insights', 'google-reviews-plugin')
            ),
            'review_management' => array(
                'name' => __('Review Management', 'google-reviews-plugin'),
                'description' => __('Curate, moderate, and manage reviews', 'google-reviews-plugin')
            ),
            'white_label' => array(
                'name' => __('White Label', 'google-reviews-plugin'),
                'description' => __('Remove branding and customize admin interface', 'google-reviews-plugin')
            ),
            'priority_support' => array(
                'name' => __('Priority Support', 'google-reviews-plugin'),
                'description' => __('Get priority support and faster response times', 'google-reviews-plugin')
            ),
            'api_access' => array(
                'name' => __('API Access', 'google-reviews-plugin'),
                'description' => __('Full REST API access for custom integrations', 'google-reviews-plugin')
            )
        );
    }
    
    /**
     * Check if feature is available
     */
    public function is_feature_available($feature) {
        if (!$this->is_pro()) {
            return false;
        }
        
        $license_data = $this->get_license_data();
        $features = $license_data['features'] ?? array();
        
        return in_array($feature, $features);
    }
    
    /**
     * Get JWT token
     */
    public function get_jwt_token() {
        return get_option('grp_license_jwt_token', '');
    }
    
    /**
     * Get license expiration date
     */
    public function get_license_expiration() {
        $license_data = $this->get_license_data();
        return $license_data['expires_at'] ?? $license_data['expires'] ?? null;
    }
    
    /**
     * Check if license is expiring soon
     */
    public function is_license_expiring_soon($days = 30) {
        $expiration = $this->get_license_expiration();
        
        if (!$expiration) {
            return false;
        }
        
        $expiration_date = new DateTime($expiration);
        $now = new DateTime();
        $diff = $now->diff($expiration_date);
        
        return $diff->days <= $days;
    }
    
    /**
     * Get upgrade URL
     */
    public function get_upgrade_url() {
        return 'https://reactwoo.com/google-reviews-plugin-pro/';
    }
    
    /**
     * Get renewal URL
     */
    public function get_renewal_url() {
        $license_data = $this->get_license_data();
        $license_key = $this->get_license_key();
        
        return add_query_arg(array(
            'license_key' => $license_key,
            'renewal' => '1'
        ), $this->get_upgrade_url());
    }
}