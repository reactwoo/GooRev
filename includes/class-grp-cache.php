<?php
/**
 * Cache management class
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Cache {
    
    /**
     * Cache group
     */
    const CACHE_GROUP = 'grp_reviews';
    
    /**
     * Default cache duration
     */
    const DEFAULT_DURATION = 3600; // 1 hour
    
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
        add_action('grp_clear_cache', array($this, 'clear_all_cache'));
    }
    
    /**
     * Get cached data
     */
    public function get($key, $group = self::CACHE_GROUP) {
        return wp_cache_get($key, $group);
    }
    
    /**
     * Set cached data
     */
    public function set($key, $data, $duration = self::DEFAULT_DURATION, $group = self::CACHE_GROUP) {
        return wp_cache_set($key, $data, $group, $duration);
    }
    
    /**
     * Delete cached data
     */
    public function delete($key, $group = self::CACHE_GROUP) {
        return wp_cache_delete($key, $group);
    }
    
    /**
     * Clear cache by pattern
     */
    public function clear($pattern = '') {
        if (empty($pattern)) {
            return $this->clear_all_cache();
        }
        
        // Get all cache keys with pattern
        $cache_keys = $this->get_cache_keys($pattern);
        
        foreach ($cache_keys as $key) {
            $this->delete($key);
        }
        
        return true;
    }
    
    /**
     * Clear all cache
     */
    public function clear_all_cache() {
        wp_cache_flush_group(self::CACHE_GROUP);
        
        // Also clear transients
        $this->clear_transients();
        
        return true;
    }
    
    /**
     * Clear transients
     */
    private function clear_transients() {
        global $wpdb;
        
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                '_transient_grp_%',
                '_transient_timeout_grp_%'
            )
        );
    }
    
    /**
     * Get cache keys (simplified implementation)
     */
    private function get_cache_keys($pattern) {
        // This is a simplified implementation
        // In a real scenario, you might want to store cache keys in a separate table
        // or use a more sophisticated caching solution
        
        $keys = array();
        
        // Get from transients
        global $wpdb;
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_grp_' . $pattern . '%'
            )
        );
        
        foreach ($results as $result) {
            $keys[] = str_replace('_transient_grp_', '', $result->option_name);
        }
        
        return $keys;
    }
    
    /**
     * Get cache statistics
     */
    public function get_cache_stats() {
        global $wpdb;
        
        $stats = array(
            'total_keys' => 0,
            'expired_keys' => 0,
            'memory_usage' => 0
        );
        
        // Count total cache keys
        $total = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '_transient_grp_%'"
        );
        $stats['total_keys'] = intval($total);
        
        // Count expired keys
        $expired = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_timeout_grp_%' 
             AND option_value < UNIX_TIMESTAMP()"
        );
        $stats['expired_keys'] = intval($expired);
        
        // Estimate memory usage
        $memory_usage = 0;
        $results = $wpdb->get_results(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name LIKE '_transient_grp_%'"
        );
        
        foreach ($results as $result) {
            $memory_usage += strlen($result->option_value);
        }
        
        $stats['memory_usage'] = $memory_usage;
        
        return $stats;
    }
    
    /**
     * Clean expired cache
     */
    public function clean_expired() {
        global $wpdb;
        
        // Delete expired transients
        $deleted = $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_timeout_grp_%' 
             AND option_value < UNIX_TIMESTAMP()"
        );
        
        // Delete corresponding transient values
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_grp_%' 
             AND option_name NOT IN (
                 SELECT CONCAT('_transient_', SUBSTRING(option_name, 18)) 
                 FROM {$wpdb->options} 
                 WHERE option_name LIKE '_transient_timeout_grp_%'
             )"
        );
        
        return $deleted;
    }
    
    /**
     * Warm up cache
     */
    public function warm_up_cache() {
        // This method can be used to pre-populate cache with frequently accessed data
        $reviews = new GRP_Reviews();
        
        // Cache common review queries
        $common_queries = array(
            array('limit' => 5, 'min_rating' => 4),
            array('limit' => 10, 'min_rating' => 1),
            array('limit' => 3, 'min_rating' => 5),
        );
        
        foreach ($common_queries as $query) {
            $reviews->get_stored_reviews($query);
        }
        
        return true;
    }
    
    /**
     * Get cache key for reviews
     */
    public function get_reviews_cache_key($args) {
        return 'reviews_' . md5(serialize($args));
    }
    
    /**
     * Get cache key for API data
     */
    public function get_api_cache_key($endpoint, $params = array()) {
        return 'api_' . md5($endpoint . serialize($params));
    }
    
    /**
     * Cache API response
     */
    public function cache_api_response($endpoint, $params, $data, $duration = self::DEFAULT_DURATION) {
        $key = $this->get_api_cache_key($endpoint, $params);
        return $this->set($key, $data, $duration);
    }
    
    /**
     * Get cached API response
     */
    public function get_cached_api_response($endpoint, $params) {
        $key = $this->get_api_cache_key($endpoint, $params);
        return $this->get($key);
    }
    
    /**
     * Schedule cache cleanup
     */
    public function schedule_cleanup() {
        if (!wp_next_scheduled('grp_cleanup_cache')) {
            wp_schedule_event(time(), 'daily', 'grp_cleanup_cache');
        }
    }
    
    /**
     * Unschedule cache cleanup
     */
    public function unschedule_cleanup() {
        wp_clear_scheduled_hook('grp_cleanup_cache');
    }
    
    /**
     * Handle cache cleanup cron
     */
    public function handle_cleanup_cron() {
        $this->clean_expired();
    }
    
    /**
     * Get cache configuration
     */
    public function get_cache_config() {
        return array(
            'enabled' => true,
            'default_duration' => self::DEFAULT_DURATION,
            'group' => self::CACHE_GROUP,
            'cleanup_interval' => 'daily',
            'max_memory_usage' => 50 * 1024 * 1024, // 50MB
        );
    }
    
    /**
     * Check if cache is working
     */
    public function is_cache_working() {
        $test_key = 'grp_test_' . time();
        $test_data = 'test_data';
        
        $set_result = $this->set($test_key, $test_data, 60);
        $get_result = $this->get($test_key);
        
        $this->delete($test_key);
        
        return $set_result && $get_result === $test_data;
    }
}