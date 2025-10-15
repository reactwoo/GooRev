<?php
/**
 * Plugin uninstaller
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Uninstaller {
    
    /**
     * Uninstall plugin
     */
    public static function uninstall() {
        // Check if user has permission
        if (!current_user_can('delete_plugins')) {
            return;
        }
        
        // Remove database tables
        self::remove_tables();
        
        // Remove options
        self::remove_options();
        
        // Remove transients
        self::remove_transients();
        
        // Clear scheduled events
        self::clear_scheduled_events();
    }
    
    /**
     * Remove database tables
     */
    private static function remove_tables() {
        global $wpdb;
        
        $tables = array(
            $wpdb->prefix . 'grp_reviews',
            $wpdb->prefix . 'grp_locations',
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }
    }
    
    /**
     * Remove options
     */
    private static function remove_options() {
        $options = array(
            'grp_settings',
            'grp_google_client_id',
            'grp_google_client_secret',
            'grp_google_access_token',
            'grp_google_refresh_token',
            'grp_google_account_id',
            'grp_google_location_id',
            'grp_default_style',
            'grp_default_count',
            'grp_cache_duration',
            'grp_custom_css',
            'grp_custom_js',
            'grp_license_key',
            'grp_license_status',
            'grp_license_data',
            'grp_version',
        );
        
        foreach ($options as $option) {
            delete_option($option);
        }
    }
    
    /**
     * Remove transients
     */
    private static function remove_transients() {
        global $wpdb;
        
        $wpdb->query(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_grp_%' OR option_name LIKE '_transient_timeout_grp_%'"
        );
    }
    
    /**
     * Clear scheduled events
     */
    private static function clear_scheduled_events() {
        wp_clear_scheduled_hook('grp_sync_reviews_cron');
        wp_clear_scheduled_hook('grp_cleanup_cache');
        wp_clear_scheduled_hook('grp_check_license');
    }
}