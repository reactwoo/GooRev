


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

        add_filter('cron_schedules', array($this, 'add_ten_minute_schedule'));
        add_action('init', array($this, 'maybe_add_coupon_ready_column'));
        add_action('init', array($this, 'maybe_schedule_pending_coupon_cron'));
        add_action('grp_process_pending_coupons', array($this, 'process_pending_coupons'));
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
        $normalized_new_status = $this->normalize_order_status($new_status);
        $normalized_trigger_status = $this->normalize_order_status($trigger_status);
        
        // Only proceed if order reached the trigger status
        if ($normalized_new_status !== $normalized_trigger_status) {
            $this->log_eligibility_reason("order {$order_id} skipped: status transition {$old_status} → {$new_status} (trigger {$trigger_status})");
            return;
        }
        
        // Don't process if invite already exists for this order
        if ($this->invite_exists_for_order($order_id)) {
            $this->log_eligibility_reason("order {$order_id} skipped: invite already exists");
            return;
        }
        
        $order = wc_get_order($order_id);
        if (!$order || !is_a($order, 'WC_Order')) {
            $this->log_eligibility_reason("order {$order_id} skipped: could not load WC_Order (got " . (is_object($order) ? get_class($order) : 'none') . ")");
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
                $this->log_eligibility_reason('order ' . $order->get_id() . ' excluded: status ' . $order->get_status());
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
                $this->log_eligibility_reason('order ' . $order->get_id() . ' excluded: product ' . $product_id);
                return false;
            }
            
            // Check excluded categories
            if (!empty($excluded_categories)) {
                $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));
                if (array_intersect($excluded_categories, $product_categories)) {
                    $this->log_eligibility_reason('order ' . $order->get_id() . ' excluded: category ' . implode(',', $excluded_categories));
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
        $now = current_time('mysql', true);
        error_log('[GRP WooCommerce] send_scheduled_invites fired at ' . current_time('mysql'));

        global $wpdb;
        $table = $wpdb->prefix . 'grp_review_invites';

        $due_invites = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE invite_status = %s AND scheduled_at <= %s",
            'scheduled',
            $now
        ));

        if (empty($due_invites)) {
            error_log('[GRP WooCommerce] no scheduled invites found at ' . $now);
            return;
        }

        foreach ($due_invites as $invite) {
            error_log("[GRP WooCommerce] processing invite {$invite->id} for order {$invite->order_id}");

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

            $sent = $this->send_invite_email($invite, $order);

            error_log("[GRP WooCommerce] invite {$invite->id} status after send: " . ($sent ? 'sent' : 'failed'));

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
        $place_id = $invite->place_id;
        if (empty($place_id)) {
            $place_id = $this->get_place_id_from_location();
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

        list($subject, $body) = $this->prepare_email_template('initial', $order, $invite, array(
            'coupon_code' => '',
            'coupon_value' => $this->get_coupon_value_display()
        ));

        $sent = $this->send_html_email($invite->email, $subject, $body);

        grp_debug_log('Review invite email sent', array(
            'invite_id' => $invite->id,
            'to' => $invite->email,
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
            if ($this->is_incentives_enabled() && $invite->invite_status !== 'rewarded') {
                $ready_at = current_time('mysql', true);
                $ready_at = date('Y-m-d H:i:s', strtotime($ready_at . " +10 minutes"));

                $wpdb->update(
                    $table,
                    array(
                        'invite_status' => 'clicked',
                        'clicked_at' => current_time('mysql', true),
                        'coupon_ready_at' => $ready_at
                    ),
                    array('id' => $invite_id),
                    array('%s', '%s', '%s'),
                    array('%d')
                );

                // The cron job will handle issuing the coupon after the delay
            } else {
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
                    'rewarded_at' => current_time('mysql', true),
                    'coupon_ready_at' => null
                ),
                array('id' => $invite->id),
                array('%s', '%s', '%s', '%s'),
                array('%d')
            );
            
            $order = wc_get_order($invite->order_id);
            $this->send_coupon_email($invite, $order, $coupon_code);
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
     * Send reward email (coupon delivery)
     */
    private function send_coupon_email($invite, $order, $coupon_code) {
        $to = $invite->email;
        list($subject, $body) = $this->prepare_email_template('reward', $order, $invite, array(
            'coupon_code' => $coupon_code,
            'coupon_value' => $this->get_coupon_value_display()
        ));

        $this->send_html_email($to, $subject, $body);

        $this->send_thank_you_email($invite, $order, $coupon_code);
    }

    private function send_thank_you_email($invite, $order, $coupon_code) {
        if (!is_a($order, 'WC_Order')) {
            return;
        }

        list($subject, $body) = $this->prepare_email_template('thank_you', $order, $invite, array(
            'coupon_code' => $coupon_code,
            'coupon_value' => $this->get_coupon_value_display()
        ));

        if (trim(strip_tags($body)) === '') {
            return;
        }

        $this->send_html_email($invite->email, $subject, $body);
    }

    /**
     * Replace merge tags in email content
     */
    private function replace_merge_tags($content, $order, $invite, $review_url, $extra = array()) {
        $customer = $order->get_billing_first_name() ? $order->get_billing_first_name() : __('Valued Customer', 'google-reviews-plugin');
        $store_name = get_bloginfo('name');
        
        $replacements = array(
            '{first_name}' => $customer,
            '{order_id}' => $order->get_id(),
            '{order_date}' => $order->get_date_created()->date_i18n(get_option('date_format')),
            '{review_url}' => $review_url,
            '{store_name}' => $store_name,
            '{coupon_code}' => $extra['coupon_code'] ?? ($invite->coupon_code ?? ''),
            '{coupon_value}' => $extra['coupon_value'] ?? $this->get_coupon_value_display()
        );
        
        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    private function prepare_email_template($type, $order, $invite, $extra = array()) {
        $review_url = $this->generate_tracking_url($invite->id, $invite->place_id);
        $body = $this->get_email_template_body($type);
        $subject = $this->get_email_template_subject($type);

        $merged = $this->replace_merge_tags($body, $order, $invite, $review_url, $extra);

        if ($type === 'initial' && $this->is_incentives_enabled()) {
            $merged .= "\n\n" . $this->get_compliance_disclaimer();
        }

        return array($subject, nl2br($merged));
    }

    private function get_email_template_body($type) {
        $option = $this->get_email_body_option_name($type);
        $fallback = $this->get_default_email_body($type);
        $value = get_option($option, '');
        if (!empty($value)) {
            return $value;
        }
        return $fallback;
    }

    private function get_email_template_subject($type) {
        $option = $this->get_email_subject_option_name($type);
        $fallback = $this->get_default_email_subject($type);
        $value = get_option($option, '');
        if (!empty($value)) {
            return $value;
        }
        return $fallback;
    }

    private function get_email_body_option_name($type) {
        $map = array(
            'initial' => 'grp_wc_email_body',
            'reward' => 'grp_wc_email_body_reward',
            'thank_you' => 'grp_wc_email_body_thank_you',
        );

        return $map[$type] ?? 'grp_wc_email_body';
    }

    private function get_email_subject_option_name($type) {
        $map = array(
            'initial' => 'grp_wc_email_subject',
            'reward' => 'grp_wc_reward_subject',
            'thank_you' => 'grp_wc_thank_you_subject',
        );

        return $map[$type] ?? 'grp_wc_email_subject';
    }

    public function get_default_email_subject($type) {
        $defaults = array(
            'initial' => __('We\'d love your feedback!', 'google-reviews-plugin'),
            'reward' => __('Here is your thank-you coupon', 'google-reviews-plugin'),
            'thank_you' => __('Thanks again for your review!', 'google-reviews-plugin'),
        );

        return $defaults[$type] ?? $defaults['initial'];
    }

    public function get_default_email_body($type = 'initial') {
        $defaults = array(
            'initial' => __('Hi {first_name},

Thank you for your recent order (#{order_id})!

If you\'ve got 30 seconds, we\'d really appreciate an honest Google review. Your feedback helps us serve you better.

Click here to leave your review:
{review_url}

As a thank-you, we\'ll send you a discount code after you visit the review link — no matter what you write.

Thanks again!

{store_name}', 'google-reviews-plugin'),
            'reward' => __('Hey {first_name},

Thank you for taking the time to visit the review link! Here is your coupon code:
{coupon_code}
Good for {coupon_value} off your next purchase with {store_name}.

We appreciate your feedback!', 'google-reviews-plugin'),
            'thank_you' => __('Hi {first_name},

Thanks again for leaving a review. Your coupon {coupon_code} ({coupon_value}) is ready whenever you are.

Enjoy and we hope to serve you again soon!', 'google-reviews-plugin')
        );

        return $defaults[$type] ?? $defaults['initial'];
    }

    private function get_coupon_value_display() {
        $coupon_type = get_option('grp_wc_coupon_type', 'percent');
        $coupon_value = floatval(get_option('grp_wc_coupon_value', 0));

        if ($coupon_type === 'percent') {
            return $coupon_value . '%';
        }

        if (function_exists('wc_price')) {
            return wc_price($coupon_value);
        }

        return $coupon_value;
    }

    private function send_html_email($to, $subject, $body) {
        $headers = array('Content-Type: text/html; charset=UTF-8');
        return wp_mail($to, $subject, $body, $headers);
    }

    private function add_ten_minute_schedule($schedules) {
        $schedules['ten_minutes'] = array(
            'interval' => 600,
            'display' => __('Every Ten Minutes', 'google-reviews-plugin')
        );

        return $schedules;
    }

    public function maybe_schedule_pending_coupon_cron() {
        if (!wp_next_scheduled('grp_process_pending_coupons')) {
            wp_schedule_event(time() + 60, 'ten_minutes', 'grp_process_pending_coupons');
        }
    }

    public function process_pending_coupons() {
        global $wpdb;
        $table = $this->get_invites_table();
        $now = current_time('mysql', true);

        $due = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE invite_status = %s AND coupon_ready_at IS NOT NULL AND coupon_ready_at <= %s",
            'clicked',
            $now
        ));

        if (empty($due)) {
            return;
        }

        foreach ($due as $invite) {
            $this->issue_coupon($invite);
        }
    }

    private function maybe_add_coupon_ready_column() {
        global $wpdb;
        $table = $this->get_invites_table();

        $column = $wpdb->get_var(
            $wpdb->prepare("SHOW COLUMNS FROM {$table} LIKE %s", 'coupon_ready_at')
        );

        if ($column) {
            return;
        }

        $wpdb->query("ALTER TABLE {$table} ADD COLUMN coupon_ready_at datetime DEFAULT NULL AFTER clicked_at");
    }

    private function get_invites_table() {
        global $wpdb;
        return $wpdb->prefix . 'grp_review_invites';
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

    /**
     * Log why an order was rejected for invites.
     */
    private function log_eligibility_reason($message) {
        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::log('[GRP WooCommerce] ' . $message);
            return;
        }

        error_log('[GRP WooCommerce] ' . $message);
    }

    /**
     * Normalize a WooCommerce order status so we can compare values that include or omit the `wc-` prefix.
     */
    private function normalize_order_status($status) {
        $status = (string) $status;
        return preg_replace('~^wc-~i', '', $status);
    }

}

if (defined('WP_CLI') && WP_CLI) {
    class GRP_WooCommerce_Invite_Command extends WP_CLI_Command {
        /**
         * Reschedule recent completed orders for review invites.
         *
         * ## OPTIONS
         *
         * [--count=<number>]
         * : Number of recent orders to process. Default 5.
         *
         * [--status=<status>]
         * : Order status to target. Default 'completed'.
         *
         * [--old_status=<status>]
         * : Previous status to simulate before the transition. Default 'processing'.
         *
         * ## EXAMPLES
         *
         *     wp grp invite reschedule --count=5 --status=completed --old_status=processing
         *
         * @when after_wp_load
         */
        public function reschedule($args, $assoc_args) {
            if (!class_exists('WooCommerce')) {
                WP_CLI::error(__('WooCommerce is not active.', 'google-reviews-plugin'));
                return;
            }

            $count = absint($assoc_args['count'] ?? 5);
            $status = $assoc_args['status'] ?? 'completed';
            $old_status = $assoc_args['old_status'] ?? 'processing';

            $orders = wc_get_orders(array(
                'status' => $status,
                'limit' => $count,
                'orderby' => 'date',
                'order' => 'DESC',
            ));

            if (empty($orders)) {
                WP_CLI::warning(__('No matching orders found for invite rescheduling.', 'google-reviews-plugin'));
                return;
            }

            $woo = GRP_WooCommerce::get_instance();

            foreach ($orders as $order) {
                $order_id = $order->get_id();
                $before = $this->get_invite_id_for_order($order_id);

                $woo->handle_order_status_change($order_id, $old_status, $status);

                $after = $this->get_invite_id_for_order($order_id);

                if ($after && $after !== $before) {
                    WP_CLI::success(sprintf(__('Invite scheduled for order %d (invite ID %d).', 'google-reviews-plugin'), $order_id, $after));
                } elseif ($before) {
                    WP_CLI::log(sprintf(__('Invite already exists for order %d (invite ID %d).', 'google-reviews-plugin'), $order_id, $before));
                } else {
                    WP_CLI::warning(sprintf(__('No invite created for order %d — review eligibility settings.', 'google-reviews-plugin'), $order_id));
                }
            }
        }

        private function get_invite_id_for_order($order_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'grp_review_invites';
            return (int) $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT id FROM {$table} WHERE order_id = %d ORDER BY id DESC LIMIT 1",
                    $order_id
                )
            );
        }
    }

    WP_CLI::add_command('grp invite', 'GRP_WooCommerce_Invite_Command');
}

