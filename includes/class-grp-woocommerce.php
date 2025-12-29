<?php
/**
 * WooCommerce Integration Class
 * Handles post-purchase Google review invites and coupon incentives
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_WooCommerce {
    
    /**
     * Instance
     */
    private static $instance = null;
    
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
        // Hook into addon enable/disable actions
        add_action('grp_addon_enabled', array($this, 'handle_addon_enabled'));
        add_action('grp_addon_disabled', array($this, 'handle_addon_disabled'));
        
        $this->init_hooks();
    }
    
    /**
     * Handle addon enabled action
     */
    public function handle_addon_enabled($slug) {
        if ($slug === 'woocommerce') {
            // Schedule cron event when addon is enabled
            if (!wp_next_scheduled('grp_send_review_invites')) {
                wp_schedule_event(time(), 'hourly', 'grp_send_review_invites');
            }
        }
    }
    
    /**
     * Handle addon disabled action
     */
    public function handle_addon_disabled($slug) {
        if ($slug === 'woocommerce') {
            // Unschedule cron event when addon is disabled
            $timestamp = wp_next_scheduled('grp_send_review_invites');
            if ($timestamp) {
                wp_unschedule_event($timestamp, 'grp_send_review_invites');
            }
        }
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Check if addon is enabled (this check is also done at class load, but double-check here)
        $addons = GRP_Addons::get_instance();
        if (!$addons->is_addon_enabled('woocommerce')) {
            return;
        }
        
        // Check if WooCommerce is active and integration is enabled
        if (!$this->is_woocommerce_active() || !$this->is_integration_enabled()) {
            return;
        }
        
        // Only proceed if Pro or Enterprise license is active
        $license = new GRP_License();
        if (!$license->is_pro()) {
            return;
        }
        
        // Hook into order status changes
        add_action('woocommerce_order_status_changed', array($this, 'handle_order_status_change'), 10, 3);
        
        // Schedule cron to send invites
        add_action('grp_send_review_invites', array($this, 'send_scheduled_invites'));
        
        // Register redirect endpoint for click tracking
        add_action('init', array($this, 'register_redirect_endpoint'));
        add_action('template_redirect', array($this, 'handle_review_redirect'));
        
        // Schedule cron event
        if (!wp_next_scheduled('grp_send_review_invites')) {
            wp_schedule_event(time(), 'hourly', 'grp_send_review_invites');
        }
    }
    
    /**
     * Check if WooCommerce is active
     */
    public function is_woocommerce_active() {
        return class_exists('WooCommerce');
    }
    
    /**
     * Check if integration is enabled
     * Now checks addon enabled state instead of separate option
     */
    public function is_integration_enabled() {
        // Check addon enabled state (primary check)
        $addons = GRP_Addons::get_instance();
        if (!$addons->is_addon_enabled('woocommerce')) {
            return false;
        }
        
        // Also check legacy option for backwards compatibility
        // This allows users who had it enabled before addon system to continue
        return (bool) get_option('grp_wc_integration_enabled', true);
    }
    
    /**
     * Check if review invites are enabled
     */
    public function is_invites_enabled() {
        return (bool) get_option('grp_wc_invites_enabled', false);
    }
    
    /**
     * Check if coupon incentives are enabled
     */
    public function is_incentives_enabled() {
        return (bool) get_option('grp_wc_incentives_enabled', false);
    }
    
    /**
     * Handle order status change
     */
    public function handle_order_status_change($order_id, $old_status, $new_status) {
        $trigger_status = get_option('grp_wc_trigger_status', 'completed');
        
        // Only proceed if order reached the trigger status
        if ($new_status !== $trigger_status) {
            return;
        }
        
        // Don't process if invite already exists for this order
        if ($this->invite_exists_for_order($order_id)) {
            return;
        }
        
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }
        
        // Check exclusions
        if (!$this->is_order_eligible($order)) {
            return;
        }
        
        // Schedule invite
        $this->schedule_invite($order);
    }
    
    /**
     * Check if invite already exists for order
     */
    private function invite_exists_for_order($order_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'grp_review_invites';
        
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE order_id = %d",
            $order_id
        ));
        
        return $exists > 0;
    }
    
    /**
     * Check if order is eligible for invite
     */
    private function is_order_eligible($order) {
        // Check if refunded/cancelled exclusion is enabled
        if (get_option('grp_wc_exclude_refunded', true)) {
            if ($order->get_status() === 'refunded' || $order->get_status() === 'cancelled') {
                return false;
            }
        }
        
        // Check excluded products/categories
        $excluded_products = get_option('grp_wc_excluded_products', array());
        $excluded_categories = get_option('grp_wc_excluded_categories', array());
        
        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            
            // Check excluded products
            if (!empty($excluded_products) && in_array($product_id, $excluded_products)) {
                return false;
            }
            
            // Check excluded categories
            if (!empty($excluded_categories)) {
                $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));
                if (array_intersect($excluded_categories, $product_categories)) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Schedule invite
     */
    private function schedule_invite($order) {
        global $wpdb;
        $table = $wpdb->prefix . 'grp_review_invites';
        
        $delay_days = absint(get_option('grp_wc_invite_delay_days', 7));
        $scheduled_at = current_time('mysql', true);
        $scheduled_at = date('Y-m-d H:i:s', strtotime($scheduled_at . " +{$delay_days} days"));
        
        // Get place_id from connected location
        $place_id = $this->get_place_id_from_location();
        $location_id = get_option('grp_google_location_id', '');
        
        $data = array(
            'order_id' => $order->get_id(),
            'user_id' => $order->get_user_id() ? $order->get_user_id() : null,
            'email' => $order->get_billing_email(),
            'location_id' => $location_id,
            'place_id' => $place_id,
            'invite_status' => 'scheduled',
            'scheduled_at' => $scheduled_at,
        );
        
        $wpdb->insert($table, $data, array('%d', '%d', '%s', '%s', '%s', '%s', '%s'));
        
        grp_debug_log('Review invite scheduled', array(
            'order_id' => $order->get_id(),
            'scheduled_at' => $scheduled_at
        ));
    }
    
    /**
     * Send scheduled invites
     */
    public function send_scheduled_invites() {
        global $wpdb;
        $table = $wpdb->prefix . 'grp_review_invites';
        
        // Get invites that are due to be sent
        $due_invites = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} 
            WHERE invite_status = 'scheduled' 
            AND scheduled_at <= %s",
            current_time('mysql', true)
        ));
        
        foreach ($due_invites as $invite) {
            // Verify order is still eligible
            $order = wc_get_order($invite->order_id);
            if (!$order || !$this->is_order_eligible($order)) {
                $wpdb->update(
                    $table,
                    array('invite_status' => 'cancelled'),
                    array('id' => $invite->id),
                    array('%s'),
                    array('%d')
                );
                continue;
            }
            
            // Send invite email
            $sent = $this->send_invite_email($invite, $order);
            
            if ($sent) {
                $wpdb->update(
                    $table,
                    array(
                        'invite_status' => 'sent',
                        'sent_at' => current_time('mysql', true)
                    ),
                    array('id' => $invite->id),
                    array('%s', '%s'),
                    array('%d')
                );
            } else {
                $wpdb->update(
                    $table,
                    array('invite_status' => 'failed'),
                    array('id' => $invite->id),
                    array('%s'),
                    array('%d')
                );
            }
        }
    }
    
    /**
     * Send invite email
     */
    private function send_invite_email($invite, $order) {
        // Get place_id from invite or fetch from location if not stored
        $place_id = $invite->place_id;
        if (empty($place_id)) {
            $place_id = $this->get_place_id_from_location();
            // Update invite with place_id if we found it
            if (!empty($place_id)) {
                global $wpdb;
                $table = $wpdb->prefix . 'grp_review_invites';
                $wpdb->update(
                    $table,
                    array('place_id' => $place_id),
                    array('id' => $invite->id),
                    array('%s'),
                    array('%d')
                );
            }
        }
        
        if (empty($place_id)) {
            grp_debug_log('Cannot send invite: place_id is empty', array('invite_id' => $invite->id));
            return false;
        }
        
        // Generate review URL with tracking
        $review_url = $this->generate_tracking_url($invite->id, $place_id);
        
        // Get email template
        $subject = get_option('grp_wc_email_subject', __('We\'d love your feedback!', 'google-reviews-plugin'));
        $body = get_option('grp_wc_email_body', $this->get_default_email_body());
        
        // Replace merge tags
        $subject = $this->replace_merge_tags($subject, $order, $invite, $review_url);
        $body = $this->replace_merge_tags($body, $order, $invite, $review_url);
        
        // Add compliance disclaimer if incentives enabled
        if ($this->is_incentives_enabled()) {
            $body .= "\n\n" . $this->get_compliance_disclaimer();
        }
        
        // Convert line breaks to HTML
        $body = nl2br($body);
        
        // Send email
        $to = $invite->email;
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        $sent = wp_mail($to, $subject, $body, $headers);
        
        grp_debug_log('Review invite email sent', array(
            'invite_id' => $invite->id,
            'to' => $to,
            'sent' => $sent
        ));
        
        return $sent;
    }
    
    /**
     * Generate tracking URL for review redirect
     */
    private function generate_tracking_url($invite_id, $place_id) {
        $auth_salt = defined('AUTH_SALT') ? AUTH_SALT : 'default-salt-change-in-wp-config';
        $signature_data = $invite_id . '|' . $place_id;
        $signature = hash_hmac('sha256', $signature_data, $auth_salt);
        
        return add_query_arg(array(
            'rw_review_redirect' => $invite_id,
            'sig' => $signature,
            'place_id' => $place_id
        ), home_url('/'));
    }
    
    /**
     * Register redirect endpoint
     */
    public function register_redirect_endpoint() {
        // Already handled via query vars in template_redirect
    }
    
    /**
     * Handle review redirect
     */
    public function handle_review_redirect() {
        if (!isset($_GET['rw_review_redirect']) || !isset($_GET['sig']) || !isset($_GET['place_id'])) {
            return;
        }
        
        $invite_id = intval($_GET['rw_review_redirect']);
        $signature = sanitize_text_field($_GET['sig']);
        $place_id = sanitize_text_field($_GET['place_id']);
        
        // Verify signature
        $auth_salt = defined('AUTH_SALT') ? AUTH_SALT : 'default-salt-change-in-wp-config';
        $signature_data = $invite_id . '|' . $place_id;
        $expected_sig = hash_hmac('sha256', $signature_data, $auth_salt);
        
        if (!hash_equals($expected_sig, $signature)) {
            grp_debug_log('Invalid redirect signature', array(
                'invite_id' => $invite_id,
                'received_sig' => substr($signature, 0, 8) . '...',
                'expected_sig' => substr($expected_sig, 0, 8) . '...'
            ));
            wp_die(__('Invalid signature.', 'google-reviews-plugin'), __('Error', 'google-reviews-plugin'), array('response' => 403));
        }
        
        // Update invite status
        global $wpdb;
        $table = $wpdb->prefix . 'grp_review_invites';
        
        $invite = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d",
            $invite_id
        ));
        
        if (!$invite) {
            wp_die(__('Invite not found.', 'google-reviews-plugin'), __('Error', 'google-reviews-plugin'), array('response' => 404));
        }
        
        // Mark as clicked if not already
        if ($invite->invite_status === 'sent') {
            $wpdb->update(
                $table,
                array(
                    'invite_status' => 'clicked',
                    'clicked_at' => current_time('mysql', true)
                ),
                array('id' => $invite_id),
                array('%s', '%s'),
                array('%d')
            );
            
            // Issue coupon if incentives enabled and not already rewarded
            if ($this->is_incentives_enabled() && $invite->invite_status !== 'rewarded') {
                $this->issue_coupon($invite);
            }
        }
        
        // Redirect to Google review URL
        $review_url = 'https://search.google.com/local/writereview?placeid=' . urlencode($place_id);
        wp_redirect($review_url);
        exit;
    }
    
    /**
     * Issue coupon for invite
     */
    private function issue_coupon($invite) {
        global $wpdb;
        $table = $wpdb->prefix . 'grp_review_invites';
        
        // Check if already rewarded
        if ($invite->invite_status === 'rewarded' && !empty($invite->coupon_code)) {
            return;
        }
        
        // Check max coupons per customer (anti-abuse)
        $max_per_customer = absint(get_option('grp_wc_max_coupons_per_customer', 1));
        if ($max_per_customer > 0 && $invite->user_id) {
            $recent_coupons = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} 
                WHERE user_id = %d 
                AND rewarded_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                AND coupon_code IS NOT NULL",
                $invite->user_id
            ));
            
            if ($recent_coupons >= $max_per_customer) {
                grp_debug_log('Max coupons per customer reached', array('user_id' => $invite->user_id));
                return;
            }
        }
        
        // Create coupon
        $coupon_code = $this->create_coupon($invite);
        
        if ($coupon_code) {
            $wpdb->update(
                $table,
                array(
                    'invite_status' => 'rewarded',
                    'coupon_code' => $coupon_code,
                    'rewarded_at' => current_time('mysql', true)
                ),
                array('id' => $invite->id),
                array('%s', '%s', '%s'),
                array('%d')
            );
            
            // Send coupon email
            $this->send_coupon_email($invite, $coupon_code);
        }
    }
    
    /**
     * Create WooCommerce coupon
     */
    private function create_coupon($invite) {
        if (!function_exists('wc_create_coupon')) {
            return false;
        }
        
        $prefix = get_option('grp_wc_coupon_prefix', 'GRP-THANKS-');
        $code = $prefix . strtoupper(wp_generate_password(8, false));
        
        $coupon_type = get_option('grp_wc_coupon_type', 'percent');
        $coupon_value = floatval(get_option('grp_wc_coupon_value', 10));
        $expiry_days = absint(get_option('grp_wc_coupon_expiry_days', 30));
        
        $coupon = new WC_Coupon();
        $coupon->set_code($code);
        $coupon->set_discount_type($coupon_type);
        $coupon->set_amount($coupon_value);
        $coupon->set_individual_use(get_option('grp_wc_coupon_individual_use', true));
        $coupon->set_usage_limit(get_option('grp_wc_coupon_usage_limit', 1));
        
        if ($expiry_days > 0) {
            $expiry_date = date('Y-m-d', strtotime("+{$expiry_days} days"));
            $coupon->set_date_expires(strtotime($expiry_date));
        }
        
        $min_spend = get_option('grp_wc_coupon_min_spend', '');
        if (!empty($min_spend)) {
            $coupon->set_minimum_amount(floatval($min_spend));
        }
        
        // Set restricted products/categories if configured
        $restricted_products = get_option('grp_wc_coupon_restricted_products', array());
        $restricted_categories = get_option('grp_wc_coupon_restricted_categories', array());
        
        if (!empty($restricted_products)) {
            $coupon->set_product_ids($restricted_products);
        }
        
        if (!empty($restricted_categories)) {
            $coupon->set_product_categories($restricted_categories);
        }
        
        $coupon_id = $coupon->save();
        
        if ($coupon_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'grp_review_invites';
            $wpdb->update(
                $table,
                array('coupon_id' => $coupon_id),
                array('id' => $invite->id),
                array('%d'),
                array('%d')
            );
            
            return $code;
        }
        
        return false;
    }
    
    /**
     * Send coupon email
     */
    private function send_coupon_email($invite, $coupon_code) {
        $to = $invite->email;
        $subject = __('Your Thank-You Discount Code', 'google-reviews-plugin');
        
        $coupon_value = floatval(get_option('grp_wc_coupon_value', 10));
        $coupon_type = get_option('grp_wc_coupon_type', 'percent');
        $value_display = $coupon_type === 'percent' ? $coupon_value . '%' : wc_price($coupon_value);
        
        $message = sprintf(
            __('Thank you for your review! Here\'s your discount code: %s (%s off)', 'google-reviews-plugin'),
            $coupon_code,
            $value_display
        );
        
        wp_mail($to, $subject, $message);
    }
    
    /**
     * Replace merge tags in email content
     */
    private function replace_merge_tags($content, $order, $invite, $review_url) {
        $customer = $order->get_billing_first_name() ? $order->get_billing_first_name() : __('Valued Customer', 'google-reviews-plugin');
        $store_name = get_bloginfo('name');
        
        $replacements = array(
            '{first_name}' => $customer,
            '{order_id}' => $order->get_id(),
            '{order_date}' => $order->get_date_created()->date_i18n(get_option('date_format')),
            '{review_url}' => $review_url,
            '{store_name}' => $store_name,
            '{coupon_code}' => $invite->coupon_code ? $invite->coupon_code : '',
            '{coupon_value}' => '',
        );
        
        // Add coupon value if available
        if ($invite->coupon_code) {
            $coupon_value = floatval(get_option('grp_wc_coupon_value', 10));
            $coupon_type = get_option('grp_wc_coupon_type', 'percent');
            $replacements['{coupon_value}'] = $coupon_type === 'percent' ? $coupon_value . '%' : wc_price($coupon_value);
        }
        
        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
    
    /**
     * Get default email body
     */
    private function get_default_email_body() {
        $default = get_option('grp_wc_email_body', '');
        if (!empty($default)) {
            return $default;
        }
        
        return __('Hi {first_name},

Thank you for your recent order (#{order_id})!

If you\'ve got 30 seconds, we\'d really appreciate an honest Google review. Your feedback helps us serve you better.

Click here to leave your review:
{review_url}

As a thank-you, we\'ll send you a discount code after you visit the review link — no matter what you write.

Thanks again!

{store_name}', 'google-reviews-plugin');
    }
    
    /**
     * Get compliance disclaimer
     */
    private function get_compliance_disclaimer() {
        return __('\n---\nNote: The discount code is provided as a thank-you for taking the time to leave a review, regardless of the review content or rating. We value all honest feedback.', 'google-reviews-plugin');
    }
    
    /**
     * Get place_id from connected Google Business Profile location
     */
    private function get_place_id_from_location() {
        // Check for Place ID from settings page (manual or auto-detected)
        $place_id = get_option('grp_place_id', '');
        if (!empty($place_id)) {
            return $place_id;
        }
        
        // Fallback to auto-detected Place ID
        $place_id_auto = get_option('grp_gbp_place_id_default', '');
        if (!empty($place_id_auto)) {
            return $place_id_auto;
        }
        
        $location_id = get_option('grp_google_location_id', '');
        $account_id = get_option('grp_google_account_id', '');
        
        if (empty($location_id) || empty($account_id)) {
            return '';
        }
        
        // Try to get place_id from stored location data or fetch from API
        $api = new GRP_API();
        if (!$api->is_connected()) {
            return '';
        }
        
        // Check for cached place_id first to avoid API calls
        $cached_place_id = get_option('grp_gbp_place_id_default', '');
        if (!empty($cached_place_id)) {
            // Still try to refresh, but return cached if API fails
        }
        
        // Try to get placeId from a single location details call (placeId is only available in single location endpoint)
        // This is more reliable than trying to get it from the locations list
        $location_details = $api->get_location($account_id, $location_id);
        if (!is_wp_error($location_details)) {
            // Handle response structure: could be {location: {...}} or just {...}
            $location = isset($location_details['location']) ? $location_details['location'] : $location_details;
            
            if (!empty($location)) {
                // Extract placeId from location details
                // Business Information API exposes placeId in metadata.placeId
                $place_id = '';
                if (isset($location['metadata']['placeId']) && !empty($location['metadata']['placeId'])) {
                    $place_id = $location['metadata']['placeId'];
                } elseif (isset($location['placeId']) && !empty($location['placeId'])) {
                    $place_id = $location['placeId'];
                } elseif (isset($location['place_id']) && !empty($location['place_id'])) {
                    $place_id = $location['place_id'];
                } elseif (isset($location['storefrontAddress']['placeId']) && !empty($location['storefrontAddress']['placeId'])) {
                    $place_id = $location['storefrontAddress']['placeId'];
                }
                
                if (!empty($place_id)) {
                    // Store it for future use to avoid API calls
                    update_option('grp_gbp_place_id_default', $place_id);
                    grp_debug_log('Place ID found via single location endpoint', array('place_id' => $place_id));
                    return $place_id;
                } else {
                    // Log for debugging
                    grp_debug_log('Place ID not found in single location response', array(
                        'location_id' => $location_id,
                        'response_keys' => array_keys($location),
                        'has_storefrontAddress' => isset($location['storefrontAddress'])
                    ));
                }
            }
        } else {
            grp_debug_log('Single location endpoint failed', array('error' => $location_details->get_error_message()));
        }
        
        // At this point the single-location call succeeded but did not contain any placeId.
        // The locations list endpoint does not expose placeId for this project either,
        // so calling it again would just add another API call without new information.
        // If we have a cached place_id, return that; otherwise, give up quietly.
        if (!empty($cached_place_id)) {
            return $cached_place_id;
        }
        
        return '';
    }
}

