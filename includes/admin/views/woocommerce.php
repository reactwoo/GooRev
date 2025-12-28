<?php
/**
 * WooCommerce Integration Settings Page
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['grp_wc_settings_submit']) && check_admin_referer('grp_wc_settings')) {
    // Save settings
    update_option('grp_wc_integration_enabled', isset($_POST['grp_wc_integration_enabled']));
    update_option('grp_wc_invites_enabled', isset($_POST['grp_wc_invites_enabled']));
    update_option('grp_wc_incentives_enabled', isset($_POST['grp_wc_incentives_enabled']));
    update_option('grp_wc_trigger_status', sanitize_text_field($_POST['grp_wc_trigger_status']));
    update_option('grp_wc_invite_delay_days', absint($_POST['grp_wc_invite_delay_days']));
    update_option('grp_wc_exclude_refunded', isset($_POST['grp_wc_exclude_refunded']));
    update_option('grp_gbp_place_id_default', sanitize_text_field($_POST['grp_gbp_place_id_default']));
    
    // Coupon settings
    update_option('grp_wc_coupon_type', sanitize_text_field($_POST['grp_wc_coupon_type']));
    update_option('grp_wc_coupon_value', floatval($_POST['grp_wc_coupon_value']));
    update_option('grp_wc_coupon_expiry_days', absint($_POST['grp_wc_coupon_expiry_days']));
    update_option('grp_wc_coupon_usage_limit', absint($_POST['grp_wc_coupon_usage_limit']));
    update_option('grp_wc_coupon_individual_use', isset($_POST['grp_wc_coupon_individual_use']));
    update_option('grp_wc_coupon_min_spend', sanitize_text_field($_POST['grp_wc_coupon_min_spend']));
    update_option('grp_wc_coupon_prefix', sanitize_text_field($_POST['grp_wc_coupon_prefix']));
    update_option('grp_wc_max_coupons_per_customer', absint($_POST['grp_wc_max_coupons_per_customer']));
    
    // Email settings
    update_option('grp_wc_email_subject', sanitize_text_field($_POST['grp_wc_email_subject']));
    update_option('grp_wc_email_body', wp_kses_post($_POST['grp_wc_email_body']));
    
    echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved successfully!', 'google-reviews-plugin') . '</p></div>';
}

// Get current settings
$integration_enabled = get_option('grp_wc_integration_enabled', false);
$invites_enabled = get_option('grp_wc_invites_enabled', false);
$incentives_enabled = get_option('grp_wc_incentives_enabled', false);
$trigger_status = get_option('grp_wc_trigger_status', 'completed');
$delay_days = get_option('grp_wc_invite_delay_days', 7);
$exclude_refunded = get_option('grp_wc_exclude_refunded', true);
$place_id = get_option('grp_gbp_place_id_default', '');
$coupon_type = get_option('grp_wc_coupon_type', 'percent');
$coupon_value = get_option('grp_wc_coupon_value', 10);
$coupon_expiry = get_option('grp_wc_coupon_expiry_days', 30);
$coupon_usage_limit = get_option('grp_wc_coupon_usage_limit', 1);
$coupon_individual_use = get_option('grp_wc_coupon_individual_use', true);
$coupon_min_spend = get_option('grp_wc_coupon_min_spend', '');
$coupon_prefix = get_option('grp_wc_coupon_prefix', 'GRP-THANKS-');
$max_coupons_per_customer = get_option('grp_wc_max_coupons_per_customer', 1);
$email_subject = get_option('grp_wc_email_subject', __('We\'d love your feedback!', 'google-reviews-plugin'));
$email_body = get_option('grp_wc_email_body', '');

// Get order statuses
$order_statuses = wc_get_order_statuses();
?>

<div class="wrap">
    <h1><?php echo esc_html__('WooCommerce Integration', 'google-reviews-plugin'); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('grp_wc_settings'); ?>
        
        <table class="form-table">
            <tbody>
                <!-- Integration Toggle -->
                <tr>
                    <th scope="row">
                        <label for="grp_wc_integration_enabled"><?php esc_html_e('Enable WooCommerce Integration', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="grp_wc_integration_enabled" name="grp_wc_integration_enabled" value="1" <?php checked($integration_enabled, true); ?>>
                        <p class="description"><?php esc_html_e('Enable the WooCommerce integration module.', 'google-reviews-plugin'); ?></p>
                    </td>
                </tr>
                
                <!-- Review Invites Toggle -->
                <tr>
                    <th scope="row">
                        <label for="grp_wc_invites_enabled"><?php esc_html_e('Enable Google Review Invites', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="grp_wc_invites_enabled" name="grp_wc_invites_enabled" value="1" <?php checked($invites_enabled, true); ?>>
                        <p class="description"><?php esc_html_e('Automatically send review invite emails to customers after purchase.', 'google-reviews-plugin'); ?></p>
                    </td>
                </tr>
                
                <!-- Coupon Incentives Toggle -->
                <tr>
                    <th scope="row">
                        <label for="grp_wc_incentives_enabled"><?php esc_html_e('Enable Coupon Thank-You', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="grp_wc_incentives_enabled" name="grp_wc_incentives_enabled" value="1" <?php checked($incentives_enabled, true); ?>>
                        <p class="description"><?php esc_html_e('Issue discount coupons as a thank-you after customers click the review link (regardless of review sentiment).', 'google-reviews-plugin'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <h2><?php esc_html_e('Invite Trigger Settings', 'google-reviews-plugin'); ?></h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="grp_wc_trigger_status"><?php esc_html_e('Trigger Order Status', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <select id="grp_wc_trigger_status" name="grp_wc_trigger_status">
                            <?php foreach ($order_statuses as $status_key => $status_label): ?>
                                <option value="<?php echo esc_attr($status_key); ?>" <?php selected($trigger_status, $status_key); ?>>
                                    <?php echo esc_html($status_label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e('Order status that triggers the review invite.', 'google-reviews-plugin'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="grp_wc_invite_delay_days"><?php esc_html_e('Delay', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="grp_wc_invite_delay_days" name="grp_wc_invite_delay_days" value="<?php echo esc_attr($delay_days); ?>" min="0" max="365">
                        <span><?php esc_html_e('days', 'google-reviews-plugin'); ?></span>
                        <p class="description"><?php esc_html_e('Number of days after order status change before sending invite.', 'google-reviews-plugin'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="grp_wc_exclude_refunded"><?php esc_html_e('Exclude Refunded/Cancelled', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="grp_wc_exclude_refunded" name="grp_wc_exclude_refunded" value="1" <?php checked($exclude_refunded, true); ?>>
                        <p class="description"><?php esc_html_e('Do not send invites for refunded or cancelled orders.', 'google-reviews-plugin'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <h2><?php esc_html_e('Google Review Link Settings', 'google-reviews-plugin'); ?></h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="grp_gbp_place_id_default"><?php esc_html_e('Google Business Profile Place ID', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="grp_gbp_place_id_default" name="grp_gbp_place_id_default" value="<?php echo esc_attr($place_id); ?>" class="regular-text">
                        <p class="description">
                            <?php esc_html_e('Your Google Business Profile Place ID. Review URL format: https://search.google.com/local/writereview?placeid={PLACE_ID}', 'google-reviews-plugin'); ?>
                            <br>
                            <a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank"><?php esc_html_e('How to find your Place ID', 'google-reviews-plugin'); ?></a>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <h2><?php esc_html_e('Coupon Settings', 'google-reviews-plugin'); ?></h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="grp_wc_coupon_type"><?php esc_html_e('Coupon Type', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <select id="grp_wc_coupon_type" name="grp_wc_coupon_type">
                            <option value="percent" <?php selected($coupon_type, 'percent'); ?>><?php esc_html_e('Percentage discount', 'google-reviews-plugin'); ?></option>
                            <option value="fixed_cart" <?php selected($coupon_type, 'fixed_cart'); ?>><?php esc_html_e('Fixed cart discount', 'google-reviews-plugin'); ?></option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="grp_wc_coupon_value"><?php esc_html_e('Coupon Value', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="grp_wc_coupon_value" name="grp_wc_coupon_value" value="<?php echo esc_attr($coupon_value); ?>" step="0.01" min="0">
                        <span id="coupon-value-unit"><?php echo $coupon_type === 'percent' ? '%' : get_woocommerce_currency_symbol(); ?></span>
                        <p class="description"><?php esc_html_e('Discount amount.', 'google-reviews-plugin'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="grp_wc_coupon_expiry_days"><?php esc_html_e('Expiry (days from issuance)', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="grp_wc_coupon_expiry_days" name="grp_wc_coupon_expiry_days" value="<?php echo esc_attr($coupon_expiry); ?>" min="0" max="365">
                        <p class="description"><?php esc_html_e('Number of days before coupon expires. Set to 0 for no expiry.', 'google-reviews-plugin'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="grp_wc_coupon_usage_limit"><?php esc_html_e('Usage Limit Per Coupon', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="grp_wc_coupon_usage_limit" name="grp_wc_coupon_usage_limit" value="<?php echo esc_attr($coupon_usage_limit); ?>" min="1">
                        <p class="description"><?php esc_html_e('Maximum number of times each coupon can be used.', 'google-reviews-plugin'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="grp_wc_coupon_individual_use"><?php esc_html_e('Individual Use Only', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="grp_wc_coupon_individual_use" name="grp_wc_coupon_individual_use" value="1" <?php checked($coupon_individual_use, true); ?>>
                        <p class="description"><?php esc_html_e('Coupon cannot be used with other coupons.', 'google-reviews-plugin'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="grp_wc_coupon_min_spend"><?php esc_html_e('Minimum Spend', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="grp_wc_coupon_min_spend" name="grp_wc_coupon_min_spend" value="<?php echo esc_attr($coupon_min_spend); ?>" step="0.01" min="0">
                        <p class="description"><?php esc_html_e('Minimum order total required to use coupon. Leave empty for no minimum.', 'google-reviews-plugin'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="grp_wc_coupon_prefix"><?php esc_html_e('Coupon Code Prefix', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="grp_wc_coupon_prefix" name="grp_wc_coupon_prefix" value="<?php echo esc_attr($coupon_prefix); ?>" class="regular-text">
                        <p class="description"><?php esc_html_e('Prefix for generated coupon codes (e.g., GRP-THANKS-).', 'google-reviews-plugin'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="grp_wc_max_coupons_per_customer"><?php esc_html_e('Max Coupons Per Customer (30 days)', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="grp_wc_max_coupons_per_customer" name="grp_wc_max_coupons_per_customer" value="<?php echo esc_attr($max_coupons_per_customer); ?>" min="0">
                        <p class="description"><?php esc_html_e('Maximum number of coupons a customer can receive within 30 days. Set to 0 for unlimited.', 'google-reviews-plugin'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <h2><?php esc_html_e('Email Template Settings', 'google-reviews-plugin'); ?></h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="grp_wc_email_subject"><?php esc_html_e('Email Subject', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="grp_wc_email_subject" name="grp_wc_email_subject" value="<?php echo esc_attr($email_subject); ?>" class="large-text">
                        <p class="description">
                            <?php esc_html_e('Available merge tags: {first_name}, {order_id}, {order_date}, {store_name}', 'google-reviews-plugin'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="grp_wc_email_body"><?php esc_html_e('Email Body', 'google-reviews-plugin'); ?></label>
                    </th>
                    <td>
                        <?php
                        $editor_settings = array(
                            'textarea_name' => 'grp_wc_email_body',
                            'textarea_rows' => 12,
                            'media_buttons' => false,
                            'teeny' => true
                        );
                        wp_editor($email_body, 'grp_wc_email_body', $editor_settings);
                        ?>
                        <p class="description">
                            <?php esc_html_e('Available merge tags: {first_name}, {order_id}, {order_date}, {review_url}, {store_name}, {coupon_code}, {coupon_value}', 'google-reviews-plugin'); ?>
                            <br>
                            <?php esc_html_e('Compliance disclaimer will be automatically appended if incentives are enabled.', 'google-reviews-plugin'); ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <?php submit_button(__('Save Settings', 'google-reviews-plugin'), 'primary', 'grp_wc_settings_submit'); ?>
    </form>
    
    <script>
    jQuery(document).ready(function($) {
        // Update coupon value unit based on type
        $('#grp_wc_coupon_type').on('change', function() {
            var unit = $(this).val() === 'percent' ? '%' : '<?php echo esc_js(get_woocommerce_currency_symbol()); ?>';
            $('#coupon-value-unit').text(unit);
        });
    });
    </script>
</div>

