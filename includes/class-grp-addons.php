<?php
/**
 * Addons Manager Class
 * Manages plugin addons (extensions like WooCommerce integration)
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Addons {
    
    /**
     * Instance
     */
    private static $instance = null;
    
    /**
     * Registered addons
     */
    private $addons = array();
    
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
        $this->register_default_addons();
    }
    
    /**
     * Register default addons
     */
    private function register_default_addons() {
        // WooCommerce Integration Addon
        $this->register_addon('woocommerce', array(
            'name' => __('WooCommerce Integration', 'google-reviews-plugin'),
            'description' => __('Automatically send Google review invites to customers after purchase with optional coupon incentives.', 'google-reviews-plugin'),
            'version' => '1.0.0',
            'requires_pro' => true,
            'requires_plugin' => 'woocommerce/woocommerce.php',
            'icon' => 'dashicons-cart',
            'features' => array(
                __('Post-purchase review invites', 'google-reviews-plugin'),
                __('Automated email sending', 'google-reviews-plugin'),
                __('Optional coupon incentives', 'google-reviews-plugin'),
                __('Click tracking & analytics', 'google-reviews-plugin'),
                __('Order status triggers', 'google-reviews-plugin'),
                __('Customizable email templates', 'google-reviews-plugin'),
            ),
            'settings_page' => 'google-reviews-woocommerce',
        ));
    }
    
    /**
     * Register an addon
     */
    public function register_addon($slug, $args) {
        $defaults = array(
            'name' => '',
            'description' => '',
            'version' => '1.0.0',
            'requires_pro' => false,
            'requires_plugin' => '',
            'icon' => 'dashicons-admin-plugins',
            'features' => array(),
            'settings_page' => '',
        );
        
        $this->addons[$slug] = wp_parse_args($args, $defaults);
    }
    
    /**
     * Get all registered addons
     */
    public function get_addons() {
        return $this->addons;
    }
    
    /**
     * Get a specific addon
     */
    public function get_addon($slug) {
        return isset($this->addons[$slug]) ? $this->addons[$slug] : null;
    }
    
    /**
     * Check if addon is enabled
     */
    public function is_addon_enabled($slug) {
        return (bool) get_option('grp_addon_' . $slug . '_enabled', false);
    }
    
    /**
     * Enable an addon
     */
    public function enable_addon($slug) {
        update_option('grp_addon_' . $slug . '_enabled', true);
        
        // Sync with legacy WooCommerce integration option for backwards compatibility
        if ($slug === 'woocommerce') {
            update_option('grp_wc_integration_enabled', true);
        }
        
        // Trigger action for addon-specific activation
        do_action('grp_addon_enabled', $slug);
    }
    
    /**
     * Disable an addon
     */
    public function disable_addon($slug) {
        update_option('grp_addon_' . $slug . '_enabled', false);
        
        // Sync with legacy WooCommerce integration option for backwards compatibility
        if ($slug === 'woocommerce') {
            update_option('grp_wc_integration_enabled', false);
        }
        
        // Trigger action for addon-specific deactivation
        do_action('grp_addon_disabled', $slug);
    }
    
    /**
     * Check if addon requirements are met
     */
    public function check_addon_requirements($slug) {
        $addon = $this->get_addon($slug);
        if (!$addon) {
            return array('met' => false, 'message' => __('Addon not found', 'google-reviews-plugin'));
        }
        
        $requirements = array('met' => true, 'messages' => array());
        
        // Check Pro license requirement
        if ($addon['requires_pro']) {
            $license = new GRP_License();
            if (!$license->is_pro()) {
                $requirements['met'] = false;
                $requirements['messages'][] = __('Pro or Enterprise license required', 'google-reviews-plugin');
            }
        }
        
        // Check required plugin
        if (!empty($addon['requires_plugin'])) {
            if (!is_plugin_active($addon['requires_plugin'])) {
                $requirements['met'] = false;
                $plugin_name = $this->get_plugin_name($addon['requires_plugin']);
                $requirements['messages'][] = sprintf(__('%s plugin is required', 'google-reviews-plugin'), $plugin_name);
            }
        }
        
        return $requirements;
    }
    
    /**
     * Get plugin name from plugin file path
     */
    private function get_plugin_name($plugin_file) {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $all_plugins = get_plugins();
        if (isset($all_plugins[$plugin_file])) {
            return $all_plugins[$plugin_file]['Name'];
        }
        
        // Fallback: extract from file path
        $parts = explode('/', $plugin_file);
        return ucfirst(str_replace('.php', '', end($parts)));
    }
}

