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
        
        // Review Inbox Addon
        $this->register_addon('review-inbox', array(
            'name' => __('Review Inbox', 'google-reviews-plugin'),
            'description' => __('Centralized review management inbox with filters, status tracking, and review organization.', 'google-reviews-plugin'),
            'version' => '1.0.0',
            'requires_pro' => true,
            'requires_plugin' => '',
            'icon' => 'dashicons-email-alt',
            'features' => array(
                __('Unified inbox for all reviews', 'google-reviews-plugin'),
                __('Star rating filters', 'google-reviews-plugin'),
                __('Keyword search', 'google-reviews-plugin'),
                __('Internal status tracking', 'google-reviews-plugin'),
                __('Scheduled review sync', 'google-reviews-plugin'),
                __('New/unread review indicators', 'google-reviews-plugin'),
            ),
            'settings_page' => 'google-reviews-inbox',
        ));
        
        // Reply to Reviews Addon
        $this->register_addon('review-replies', array(
            'name' => __('Review Replies', 'google-reviews-plugin'),
            'description' => __('Reply to Google reviews directly from WordPress with approval workflows and templates.', 'google-reviews-plugin'),
            'version' => '1.0.0',
            'requires_enterprise' => true,
            'requires_plugin' => '',
            'icon' => 'dashicons-format-chat',
            'features' => array(
                __('Reply directly from WordPress', 'google-reviews-plugin'),
                __('Draft and approval workflow', 'google-reviews-plugin'),
                __('Reply templates library', 'google-reviews-plugin'),
                __('Multi-user permissions', 'google-reviews-plugin'),
                __('Agency and client role support', 'google-reviews-plugin'),
            ),
            'settings_page' => 'google-reviews-replies',
        ));
        
        // AI Assisted Replies Addon
        $this->register_addon('ai-replies', array(
            'name' => __('AI Assisted Replies', 'google-reviews-plugin'),
            'description' => __('AI-powered reply suggestions with compliance guardrails and human-in-the-loop approval.', 'google-reviews-plugin'),
            'version' => '1.0.0',
            'requires_enterprise' => true,
            'requires_plugin' => '',
            'icon' => 'dashicons-star-filled',
            'features' => array(
                __('AI-powered reply suggestions', 'google-reviews-plugin'),
                __('Compliance guardrails', 'google-reviews-plugin'),
                __('Human-in-the-loop approval', 'google-reviews-plugin'),
                __('Custom business tone', 'google-reviews-plugin'),
                __('AI credit management', 'google-reviews-plugin'),
            ),
            'settings_page' => 'google-reviews-ai',
        ));
        
        // Alerts & Routing Integrations Addon
        $this->register_addon('alerts-routing', array(
            'name' => __('Alerts & Routing', 'google-reviews-plugin'),
            'description' => __('Integrate with Slack, Teams, email, and helpdesk systems for automated alerts and ticket creation.', 'google-reviews-plugin'),
            'version' => '1.0.0',
            'requires_pro' => true,
            'requires_plugin' => '',
            'icon' => 'dashicons-bell',
            'features' => array(
                __('Slack/Teams notifications', 'google-reviews-plugin'),
                __('Email alerts', 'google-reviews-plugin'),
                __('Helpdesk integration (Zendesk, Freshdesk)', 'google-reviews-plugin'),
                __('CRM integration (HubSpot)', 'google-reviews-plugin'),
                __('Custom alert rules', 'google-reviews-plugin'),
            ),
            'settings_page' => 'google-reviews-alerts',
        ));
        
        // Request Review Widgets Addon
        $this->register_addon('review-widgets', array(
            'name' => __('Review Request Widgets', 'google-reviews-plugin'),
            'description' => __('QR codes, review buttons, and on-site widgets to request reviews from customers.', 'google-reviews-plugin'),
            'version' => '1.0.0',
            'requires_pro' => false, // Free → Pro upgrade path
            'requires_plugin' => '',
            'icon' => 'dashicons-star-empty',
            'features' => array(
                __('QR code generator per location', 'google-reviews-plugin'),
                __('"Leave us a review" buttons', 'google-reviews-plugin'),
                __('Click tracking & analytics', 'google-reviews-plugin'),
                __('Conversion tracking', 'google-reviews-plugin'),
                __('Widget customization', 'google-reviews-plugin'),
            ),
            'settings_page' => 'google-reviews-widgets',
        ));
        
        // Multi-Location / Agency Console Addon
        $this->register_addon('multi-location', array(
            'name' => __('Multi-Location Console', 'google-reviews-plugin'),
            'description' => __('Manage multiple Google Business Profile locations with health metrics and centralized control.', 'google-reviews-plugin'),
            'version' => '1.0.0',
            'requires_enterprise' => true,
            'requires_plugin' => '',
            'icon' => 'dashicons-admin-multisite',
            'features' => array(
                __('Manage multiple GBP locations', 'google-reviews-plugin'),
                __('Location health dashboard', 'google-reviews-plugin'),
                __('Average rating trends', 'google-reviews-plugin'),
                __('Response time metrics', 'google-reviews-plugin'),
                __('Agency client management', 'google-reviews-plugin'),
            ),
            'settings_page' => 'google-reviews-multi-location',
        ));
        
        // Policy / Safety Toolkit Addon
        $this->register_addon('policy-toolkit', array(
            'name' => __('Policy & Safety Toolkit', 'google-reviews-plugin'),
            'description' => __('Centralized response guidelines, compliance checks, and audit logs for review management.', 'google-reviews-plugin'),
            'version' => '1.0.0',
            'requires_enterprise' => true,
            'requires_plugin' => '',
            'icon' => 'dashicons-shield',
            'features' => array(
                __('Centralized response guidelines', 'google-reviews-plugin'),
                __('Blocklist phrases', 'google-reviews-plugin'),
                __('Compliance checks', 'google-reviews-plugin'),
                __('Audit log', 'google-reviews-plugin'),
                __('Agency compliance tracking', 'google-reviews-plugin'),
            ),
            'settings_page' => 'google-reviews-policy',
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
            'requires_enterprise' => false,
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
            return array('met' => false, 'messages' => array(__('Addon not found', 'google-reviews-plugin')));
        }
        
        $requirements = array('met' => true, 'messages' => array());
        $license = new GRP_License();
        
        // Check Enterprise license requirement
        if (!empty($addon['requires_enterprise']) && $addon['requires_enterprise']) {
            if (!$license->is_enterprise()) {
                $requirements['met'] = false;
                $requirements['messages'][] = __('Enterprise license required', 'google-reviews-plugin');
            }
        }
        // Check Pro license requirement (Pro or Enterprise)
        elseif (!empty($addon['requires_pro']) && $addon['requires_pro']) {
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

