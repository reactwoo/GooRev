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
    const LICENSE_API_URL = 'https://yourwebsite.com/wp-json/grp-license/v1/';
    
    /**
     * License statuses
     */
    const STATUS_VALID = 'valid';
    const STATUS_INVALID = 'invalid';
    const STATUS_EXPIRED = 'expired';
    const STATUS_DEACTIVATED = 'deactivated';
    
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
        add_action('admin_init', array($this, 'handle_license_actions'));
        add_action('grp_check_license', array($this, 'check_license_status'));
        add_action('admin_notices', array($this, 'show_license_notices'));
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
     * Check if pro version is active
     */
    public function is_pro() {
        $status = $this->get_license_status();
        return $status === self::STATUS_VALID;
    }
    
    /**
     * Activate license
     */
    public function activate_license($license_key) {
        $response = $this->make_license_request('activate', array(
            'license_key' => $license_key,
            'site_url' => home_url(),
            'plugin_version' => GRP_PLUGIN_VERSION
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        if ($response['success']) {
            update_option('grp_license_key', $license_key);
            update_option('grp_license_status', self::STATUS_VALID);
            update_option('grp_license_data', $response['data']);
            
            // Schedule license check
            $this->schedule_license_check();
            
            return true;
        }
        
        return new WP_Error('activation_failed', $response['message'] ?? __('License activation failed', 'google-reviews-plugin'));
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
            'site_url' => home_url()
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
            'site_url' => home_url()
        ));
        
        if (is_wp_error($response)) {
            return false;
        }
        
        if ($response['success']) {
            $status = $response['data']['status'] ?? self::STATUS_INVALID;
            update_option('grp_license_status', $status);
            update_option('grp_license_data', $response['data']);
            
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
        $url = self::LICENSE_API_URL . $action;
        
        $response = wp_remote_post($url, array(
            'body' => $data,
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json'
            )
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('invalid_response', __('Invalid response from license server', 'google-reviews-plugin'));
        }
        
        return $data;
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
                $this->check_license_status();
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-info"><p>' . esc_html__('License status checked.', 'google-reviews-plugin') . '</p></div>';
                });
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
     * Get license expiration date
     */
    public function get_license_expiration() {
        $license_data = $this->get_license_data();
        return $license_data['expires'] ?? null;
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
        return 'https://yourwebsite.com/google-reviews-plugin-pro/';
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