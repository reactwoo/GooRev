<?php
/**
 * Plugin activator
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Activator {
    
    /**
     * Activate plugin
     */
    public static function activate() {
        // Create database tables
        self::create_tables();
        
        // Set default options
        self::set_default_options();
        
        // Schedule cron events
        self::schedule_cron_events();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Reviews table
        $table_name = $wpdb->prefix . 'grp_reviews';
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            review_id varchar(255) NOT NULL,
            author_name varchar(255) NOT NULL,
            author_photo varchar(500) DEFAULT '',
            rating int(1) NOT NULL,
            text text,
            time datetime DEFAULT NULL,
            update_time datetime DEFAULT NULL,
            review_url varchar(500) DEFAULT '',
            is_anonymous tinyint(1) DEFAULT 0,
            reply text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY review_id (review_id),
            KEY rating (rating),
            KEY time (time)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Locations table (for Pro version)
        $locations_table = $wpdb->prefix . 'grp_locations';
        
        $sql_locations = "CREATE TABLE $locations_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            location_id varchar(255) NOT NULL,
            account_id varchar(255) NOT NULL,
            name varchar(255) NOT NULL,
            address text,
            phone varchar(50),
            website varchar(500),
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY location_id (location_id),
            KEY account_id (account_id),
            KEY is_active (is_active)
        ) $charset_collate;";
        
        dbDelta($sql_locations);
        
        // Review invites table (for WooCommerce integration)
        $invites_table = $wpdb->prefix . 'grp_review_invites';
        
        $sql_invites = "CREATE TABLE $invites_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            order_id bigint(20) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            email varchar(255) NOT NULL,
            location_id varchar(255) DEFAULT NULL,
            place_id varchar(255) DEFAULT NULL,
            invite_status enum('scheduled','sent','clicked','rewarded','cancelled','failed') DEFAULT 'scheduled',
            scheduled_at datetime NOT NULL,
            sent_at datetime DEFAULT NULL,
            clicked_at datetime DEFAULT NULL,
            coupon_id bigint(20) DEFAULT NULL,
            coupon_code varchar(100) DEFAULT NULL,
            rewarded_at datetime DEFAULT NULL,
            meta longtext DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY order_id (order_id),
            KEY user_id (user_id),
            KEY email (email),
            KEY invite_status (invite_status),
            KEY scheduled_at (scheduled_at),
            KEY coupon_code (coupon_code)
        ) $charset_collate;";
        
        dbDelta($sql_invites);
        
        // Widget clicks tracking table (for Review Widgets addon)
        $clicks_table = $wpdb->prefix . 'grp_widget_clicks';
        
        $sql_clicks = "CREATE TABLE $clicks_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            widget_type varchar(50) NOT NULL,
            widget_id varchar(100) DEFAULT NULL,
            review_url text NOT NULL,
            clicked_at datetime DEFAULT CURRENT_TIMESTAMP,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            referrer text DEFAULT NULL,
            converted tinyint(1) DEFAULT 0,
            converted_at datetime DEFAULT NULL,
            meta longtext DEFAULT NULL,
            PRIMARY KEY (id),
            KEY widget_type (widget_type),
            KEY widget_id (widget_id),
            KEY clicked_at (clicked_at),
            KEY converted (converted)
        ) $charset_collate;";
        
        dbDelta($sql_clicks);
    }
    
    /**
     * Set default options
     */
    private static function set_default_options() {
        $default_options = array(
            'grp_default_style' => 'modern',
            'grp_default_count' => 5,
            'grp_cache_duration' => 3600,
            'grp_version' => GRP_PLUGIN_VERSION,
        );
        
        foreach ($default_options as $option => $value) {
            if (get_option($option) === false) {
                add_option($option, $value);
            }
        }
    }
    
    /**
     * Schedule cron events
     */
    private static function schedule_cron_events() {
        // Schedule review sync
        if (!wp_next_scheduled('grp_sync_reviews_cron')) {
            wp_schedule_event(time(), 'hourly', 'grp_sync_reviews_cron');
        }
        
        // Schedule cache cleanup
        if (!wp_next_scheduled('grp_cleanup_cache')) {
            wp_schedule_event(time(), 'daily', 'grp_cleanup_cache');
        }
        
        // Schedule license check
        if (!wp_next_scheduled('grp_check_license')) {
            wp_schedule_event(time(), 'daily', 'grp_check_license');
        }
        
        // Note: Review invites cron is scheduled when WooCommerce addon is enabled
        // See GRP_WooCommerce::handle_addon_enabled()
    }
}