<?php
/**
 * Reviews management class
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Reviews {
    
    /**
     * API instance
     */
    private $api;
    
    /**
     * Cache instance
     */
    private $cache;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->api = new GRP_API();
        $this->cache = new GRP_Cache();
        
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('wp_ajax_grp_sync_reviews', array($this, 'ajax_sync_reviews'));
        add_action('wp_ajax_grp_get_reviews', array($this, 'ajax_get_reviews'));
        add_action('grp_sync_reviews_cron', array($this, 'sync_reviews_cron'));
    }
    
    /**
     * Get reviews from Google API
     */
    public function get_reviews($args = array()) {
        $defaults = array(
            'account_id' => get_option('grp_google_account_id', ''),
            'location_id' => get_option('grp_google_location_id', ''),
            'limit' => 10,
            'min_rating' => 1,
            'max_rating' => 5,
            'sort_by' => 'newest',
            'cache_duration' => 3600 // 1 hour
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // Check cache first
        $cache_key = 'grp_reviews_' . md5(serialize($args));
        $cached_reviews = $this->cache->get($cache_key);
        
        if ($cached_reviews !== false) {
            return $cached_reviews;
        }
        
        // Get reviews from API
        $reviews_data = $this->api->get_reviews($args['account_id'], $args['location_id'], $args['limit']);
        
        if (is_wp_error($reviews_data)) {
            return $reviews_data;
        }
        
        // Normalize the response format - extract reviews array if needed
        $reviews_list = array();
        if (isset($reviews_data['reviews']) && is_array($reviews_data['reviews'])) {
            $reviews_list = $reviews_data['reviews'];
        } elseif (is_array($reviews_data) && isset($reviews_data[0]) && isset($reviews_data[0]['reviewId'])) {
            // If it's already an array of reviews
            $reviews_list = $reviews_data;
        }
        
        // Process reviews - process_reviews expects {'reviews': [...]}
        $normalized_data = array('reviews' => $reviews_list);
        $reviews = $this->process_reviews($normalized_data, $args);
        
        // Cache the results
        $this->cache->set($cache_key, $reviews, $args['cache_duration']);
        
        return $reviews;
    }
    
    /**
     * Process reviews data from API
     */
    private function process_reviews($reviews_data, $args) {
        if (!isset($reviews_data['reviews']) || !is_array($reviews_data['reviews'])) {
            return array();
        }
        
        $processed_reviews = array();
        
        foreach ($reviews_data['reviews'] as $review) {
            $processed_review = $this->process_single_review($review);
            
            // Apply filters
            if ($this->should_include_review($processed_review, $args)) {
                $processed_reviews[] = $processed_review;
            }
        }
        
        // Sort reviews
        $processed_reviews = $this->sort_reviews($processed_reviews, $args['sort_by']);
        
        return $processed_reviews;
    }
    
    /**
     * Process single review
     */
    private function process_single_review($review) {
        // Extract rating - handle multiple possible field names and formats
        $rating = 0;
        
        // Try different possible field names
        $rating_value = null;
        if (isset($review['starRating'])) {
            $rating_value = $review['starRating'];
        } elseif (isset($review['rating'])) {
            $rating_value = $review['rating'];
        } elseif (isset($review['star_rating'])) {
            $rating_value = $review['star_rating'];
        }
        
        if ($rating_value !== null) {
            if (is_numeric($rating_value)) {
                $rating = (int) $rating_value;
            } elseif (is_string($rating_value)) {
                // Handle enum format like "FIVE", "FOUR", etc.
                $rating_map = array(
                    'ONE' => 1,
                    'TWO' => 2,
                    'THREE' => 3,
                    'FOUR' => 4,
                    'FIVE' => 5,
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5
                );
                $rating = isset($rating_map[strtoupper($rating_value)]) ? $rating_map[strtoupper($rating_value)] : 0;
            }
        }
        
        // Ensure rating is between 1 and 5
        if ($rating < 1 || $rating > 5) {
            $rating = 0; // Keep as 0 if invalid
        }
        
        $processed = array(
            'id' => $review['reviewId'] ?? '',
            'author_name' => $review['reviewer']['displayName'] ?? '',
            'author_photo' => $review['reviewer']['profilePhotoUrl'] ?? '',
            'rating' => $rating,
            'text' => $review['comment'] ?? '',
            'time' => $review['createTime'] ?? '',
            'update_time' => $review['updateTime'] ?? '',
            'review_url' => $review['reviewUrl'] ?? '',
            'is_anonymous' => $review['reviewer']['isAnonymous'] ?? false,
            'reply' => isset($review['reviewReply']) ? array(
                'text' => $review['reviewReply']['comment'] ?? '',
                'time' => $review['reviewReply']['updateTime'] ?? '',
            ) : null
        );
        
        // Format timestamps
        $processed['time_formatted'] = $this->format_timestamp($processed['time']);
        $processed['update_time_formatted'] = $this->format_timestamp($processed['update_time']);
        
        // Generate star HTML
        $processed['stars_html'] = $this->generate_stars_html($processed['rating']);
        
        return $processed;
    }
    
    /**
     * Check if review should be included based on filters
     */
    private function should_include_review($review, $args) {
        // Rating filter
        if ($review['rating'] < $args['min_rating'] || $review['rating'] > $args['max_rating']) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Sort reviews
     */
    private function sort_reviews($reviews, $sort_by) {
        switch ($sort_by) {
            case 'newest':
                usort($reviews, function($a, $b) {
                    return strtotime($b['time']) - strtotime($a['time']);
                });
                break;
                
            case 'oldest':
                usort($reviews, function($a, $b) {
                    return strtotime($a['time']) - strtotime($b['time']);
                });
                break;
                
            case 'highest_rating':
                usort($reviews, function($a, $b) {
                    return $b['rating'] - $a['rating'];
                });
                break;
                
            case 'lowest_rating':
                usort($reviews, function($a, $b) {
                    return $a['rating'] - $b['rating'];
                });
                break;
        }
        
        return $reviews;
    }
    
    /**
     * Format timestamp
     */
    private function format_timestamp($timestamp) {
        if (empty($timestamp)) {
            return '';
        }
        
        $date = new DateTime($timestamp);
        return $date->format(get_option('date_format'));
    }
    
    /**
     * Generate stars HTML
     */
    private function generate_stars_html($rating) {
        // Ensure rating is valid (1-5)
        $rating = max(0, min(5, (int) $rating));
        
        $stars = '';
        $full_stars = floor($rating);
        $half_star = ($rating - $full_stars) >= 0.5;
        
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $full_stars) {
                $stars .= '<span class="grp-star grp-star-full">★</span>';
            } elseif ($i === $full_stars + 1 && $half_star) {
                $stars .= '<span class="grp-star grp-star-half">☆</span>';
            } else {
                $stars .= '<span class="grp-star grp-star-empty">☆</span>';
            }
        }
        
        return $stars;
    }
    
    /**
     * Sync reviews from Google
     */
    public function sync_reviews() {
        if (!$this->api->is_connected()) {
            return new WP_Error('not_connected', __('Google API not connected', 'google-reviews-plugin'));
        }
        
        $account_id = get_option('grp_google_account_id', '');
        $location_id = get_option('grp_google_location_id', '');
        
        if (empty($account_id) || empty($location_id)) {
            return new WP_Error('no_location', __('No location selected', 'google-reviews-plugin'));
        }
        
        // Clean location_id - remove any prefixes (should already be clean, but ensure it)
        $location_id = preg_replace('#^(accounts/[^/]+/)?locations/?#', '', $location_id);
        
        $reviews_data = $this->api->get_reviews($account_id, $location_id, 100);
        
        if (is_wp_error($reviews_data)) {
            return $reviews_data;
        }
        
        // Store reviews in database - normalize the response format first
        // The API might return {reviews: [...]} or just [...]
        $normalized_data = array();
        if (isset($reviews_data['reviews']) && is_array($reviews_data['reviews'])) {
            $normalized_data['reviews'] = $reviews_data['reviews'];
        } elseif (is_array($reviews_data)) {
            // If it's already an array of reviews, wrap it
            $normalized_data['reviews'] = $reviews_data;
        }
        
        // Store reviews in database
        $this->store_reviews($normalized_data);
        
        // Clear cache
        $this->cache->clear('grp_reviews_');
        
        return true;
    }
    
    /**
     * Store reviews in database
     */
    private function store_reviews($reviews_data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'grp_reviews';
        
        // Clear existing reviews
        $wpdb->query("DELETE FROM {$table_name}");
        
        // Handle different response formats
        $reviews_list = array();
        if (isset($reviews_data['reviews']) && is_array($reviews_data['reviews'])) {
            $reviews_list = $reviews_data['reviews'];
        } elseif (is_array($reviews_data) && isset($reviews_data[0]) && isset($reviews_data[0]['reviewId'])) {
            // If it's already an array of reviews
            $reviews_list = $reviews_data;
        }
        
        if (empty($reviews_list)) {
            return;
        }
        
        foreach ($reviews_list as $review) {
            // Extract rating - handle multiple possible field names and formats
            $rating = 0;
            
            // Try different possible field names (Google API v4 uses 'starRating')
            $rating_value = null;
            if (isset($review['starRating'])) {
                $rating_value = $review['starRating'];
            } elseif (isset($review['rating'])) {
                $rating_value = $review['rating'];
            } elseif (isset($review['star_rating'])) {
                $rating_value = $review['star_rating'];
            }
            
            if ($rating_value !== null) {
                if (is_numeric($rating_value)) {
                    $rating = (int) $rating_value;
                } elseif (is_string($rating_value)) {
                    // Handle enum format like "FIVE", "FOUR", etc.
                    $rating_map = array(
                        'ONE' => 1,
                        'TWO' => 2,
                        'THREE' => 3,
                        'FOUR' => 4,
                        'FIVE' => 5,
                        '1' => 1,
                        '2' => 2,
                        '3' => 3,
                        '4' => 4,
                        '5' => 5
                    );
                    $rating = isset($rating_map[strtoupper($rating_value)]) ? $rating_map[strtoupper($rating_value)] : 0;
                }
            }
            
            // Ensure rating is between 1 and 5
            if ($rating < 1 || $rating > 5) {
                $rating = 0; // Invalid rating
            }
            
            $wpdb->insert(
                $table_name,
                array(
                    'review_id' => $review['reviewId'] ?? '',
                    'author_name' => $review['reviewer']['displayName'] ?? '',
                    'author_photo' => $review['reviewer']['profilePhotoUrl'] ?? '',
                    'rating' => $rating,
                    'text' => $review['comment'] ?? '',
                    'time' => $review['createTime'] ?? '',
                    'update_time' => $review['updateTime'] ?? '',
                    'review_url' => $review['reviewUrl'] ?? '',
                    'is_anonymous' => $review['reviewer']['isAnonymous'] ?? false,
                    'reply' => isset($review['reviewReply']) ? json_encode($review['reviewReply']) : null,
                    'created_at' => current_time('mysql')
                ),
                array(
                    '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s'
                )
            );
        }
    }
    
    /**
     * Get reviews from database
     */
    public function get_stored_reviews($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'limit' => 10,
            'min_rating' => 1,
            'max_rating' => 5,
            'sort_by' => 'newest'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $table_name = $wpdb->prefix . 'grp_reviews';
        
        $where_conditions = array();
        $where_values = array();
        
        // Rating filter
        if ($args['min_rating'] > 1) {
            $where_conditions[] = 'rating >= %d';
            $where_values[] = $args['min_rating'];
        }
        
        if ($args['max_rating'] < 5) {
            $where_conditions[] = 'rating <= %d';
            $where_values[] = $args['max_rating'];
        }
        
        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        }
        
        // Sort order
        $order_by = 'ORDER BY time DESC';
        switch ($args['sort_by']) {
            case 'oldest':
                $order_by = 'ORDER BY time ASC';
                break;
            case 'highest_rating':
                $order_by = 'ORDER BY rating DESC, time DESC';
                break;
            case 'lowest_rating':
                $order_by = 'ORDER BY rating ASC, time DESC';
                break;
        }
        
        $limit_clause = 'LIMIT ' . intval($args['limit']);
        
        $query = "SELECT * FROM {$table_name} {$where_clause} {$order_by} {$limit_clause}";
        
        if (!empty($where_values)) {
            $query = $wpdb->prepare($query, $where_values);
        }
        
        $results = $wpdb->get_results($query);
        
        $reviews = array();
        foreach ($results as $row) {
            $review = array(
                'id' => $row->review_id,
                'author_name' => $row->author_name,
                'author_photo' => $row->author_photo,
                'rating' => intval($row->rating),
                'text' => $row->text,
                'time' => $row->time,
                'update_time' => $row->update_time,
                'review_url' => $row->review_url,
                'is_anonymous' => (bool) $row->is_anonymous,
                'reply' => $row->reply ? json_decode($row->reply, true) : null
            );
            
            $review['time_formatted'] = $this->format_timestamp($review['time']);
            $review['update_time_formatted'] = $this->format_timestamp($review['update_time']);
            $review['stars_html'] = $this->generate_stars_html($review['rating']);
            
            $reviews[] = $review;
        }
        
        return $reviews;
    }
    
    /**
     * AJAX handler for syncing reviews
     */
    public function ajax_sync_reviews() {
        check_ajax_referer('grp_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'google-reviews-plugin'));
        }
        
        $result = $this->sync_reviews();
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(__('Reviews synced successfully', 'google-reviews-plugin'));
    }
    
    /**
     * AJAX handler for getting reviews
     */
    public function ajax_get_reviews() {
        check_ajax_referer('grp_nonce', 'nonce');
        
        $args = array(
            'limit' => intval($_POST['limit'] ?? 10),
            'min_rating' => intval($_POST['min_rating'] ?? 1),
            'max_rating' => intval($_POST['max_rating'] ?? 5),
            'sort_by' => sanitize_text_field($_POST['sort_by'] ?? 'newest')
        );
        
        $reviews = $this->get_stored_reviews($args);
        
        wp_send_json_success($reviews);
    }
    
    /**
     * Cron job for syncing reviews
     */
    public function sync_reviews_cron() {
        $this->sync_reviews();
    }
    
    /**
     * Schedule automatic sync
     */
    public function schedule_sync($interval = 'hourly') {
        if (!wp_next_scheduled('grp_sync_reviews_cron')) {
            wp_schedule_event(time(), $interval, 'grp_sync_reviews_cron');
        }
    }
    
    /**
     * Unschedule automatic sync
     */
    public function unschedule_sync() {
        wp_clear_scheduled_hook('grp_sync_reviews_cron');
    }
}