<?php
/**
 * Plugin deactivator
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Deactivator {
    
    /**
     * Deactivate plugin
     */
    public static function deactivate() {
        // Clear scheduled events
        self::clear_scheduled_events();
        
        // Clear cache
        self::clear_cache();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Clear scheduled events
     */
    private static function clear_scheduled_events() {
        wp_clear_scheduled_hook('grp_sync_reviews_cron');
        wp_clear_scheduled_hook('grp_cleanup_cache');
        wp_clear_scheduled_hook('grp_check_license');
    }
    
    /**
     * Clear cache
     */
    private static function clear_cache() {
        $cache = new GRP_Cache();
        $cache->clear_all_cache();
    }
}