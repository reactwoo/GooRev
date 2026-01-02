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
    // Place ID is now managed in main Settings page, not here
    
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
    update_option('grp_wc_reward_subject', sanitize_text_field($_POST['grp_wc_reward_subject'] ?? ''));
    update_option('grp_wc_email_body_reward', wp_kses_post($_POST['grp_wc_email_body_reward'] ?? ''));
    update_option('grp_wc_thank_you_subject', sanitize_text_field($_POST['grp_wc_thank_you_subject'] ?? ''));
    update_option('grp_wc_email_body_thank_you', wp_kses_post($_POST['grp_wc_email_body_thank_you'] ?? ''));
    
    echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved successfully!', 'google-reviews-plugin') . '</p></div>';
}

// Get current settings
$integration_enabled = get_option('grp_wc_integration_enabled', false);
$invites_enabled = get_option('grp_wc_invites_enabled', false);
$incentives_enabled = get_option('grp_wc_incentives_enabled', false);
$trigger_status = get_option('grp_wc_trigger_status', 'completed');
$delay_days = get_option('grp_wc_invite_delay_days', 7);
$exclude_refunded = get_option('grp_wc_exclude_refunded', true);
$coupon_type = get_option('grp_wc_coupon_type', 'percent');
$coupon_value = get_option('grp_wc_coupon_value', 10);
$coupon_expiry = get_option('grp_wc_coupon_expiry_days', 30);
$coupon_usage_limit = get_option('grp_wc_coupon_usage_limit', 1);
$coupon_individual_use = get_option('grp_wc_coupon_individual_use', true);
$coupon_min_spend = get_option('grp_wc_coupon_min_spend', '');
$coupon_prefix = get_option('grp_wc_coupon_prefix', 'GRP-THANKS-');
$max_coupons_per_customer = get_option('grp_wc_max_coupons_per_customer', 1);
$woo_wc = GRP_WooCommerce::get_instance();
$email_subject = get_option('grp_wc_email_subject', $woo_wc->get_default_email_subject('initial'));
$email_body = get_option('grp_wc_email_body', $woo_wc->get_default_email_body('initial'));
$reward_subject = get_option('grp_wc_reward_subject', $woo_wc->get_default_email_subject('reward'));
$reward_body = get_option('grp_wc_email_body_reward', $woo_wc->get_default_email_body('reward'));
$thank_you_subject = get_option('grp_wc_thank_you_subject', $woo_wc->get_default_email_subject('thank_you'));
$thank_you_body = get_option('grp_wc_email_body_thank_you', $woo_wc->get_default_email_body('thank_you'));

// Get order statuses
$order_statuses = wc_get_order_statuses();

// Check if Place ID is set (required for review invites)
$place_id = get_option('grp_place_id', '');
$place_id_auto = get_option('grp_gbp_place_id_default', '');
$has_place_id = !empty($place_id) || !empty($place_id_auto);

if ($is_connected && !empty($location_id) && !empty($account_id)) {
    // Try to get from stored options first to avoid API calls
    $place_id_display = get_option('grp_gbp_place_id_default', '');
    $location_name = get_option('grp_gbp_location_name', '');
    
    // Only make API call if we don't have both place_id and location name cached
    // This prevents unnecessary API calls on every page load
    if (empty($place_id_display) || empty($location_name)) {
        // First, try to get placeId from single location endpoint (more reliable)
        // This endpoint includes placeId in the readMask
        if (empty($place_id_display)) {
            $location_details = $api->get_location($account_id, $location_id);
            if (!is_wp_error($location_details)) {
                // Handle response structure: could be {location: {...}} or just {...}
                $loc = isset($location_details['location']) ? $location_details['location'] : $location_details;
                
                if (!empty($loc)) {
                    // Get location name if not cached
                    if (empty($location_name)) {
                        $location_name = $loc['title'] ?? $loc['storefrontAddress']['addressLines'][0] ?? ($loc['name'] ?? '');
                        if (!empty($location_name)) {
                            update_option('grp_gbp_location_name', $location_name);
                        }
                    }
                    
                    // Extract placeId from single location response
                    // Business Information API exposes placeId in metadata.placeId
                    if (isset($loc['metadata']['placeId']) && !empty($loc['metadata']['placeId'])) {
                        $place_id_display = $loc['metadata']['placeId'];
                    } elseif (isset($loc['placeId']) && !empty($loc['placeId'])) {
                        $place_id_display = $loc['placeId'];
                    } elseif (isset($loc['place_id']) && !empty($loc['place_id'])) {
                        $place_id_display = $loc['place_id'];
                    } elseif (isset($loc['storefrontAddress']['placeId']) && !empty($loc['storefrontAddress']['placeId'])) {
                        $place_id_display = $loc['storefrontAddress']['placeId'];
                    }
                    
                    if (!empty($place_id_display)) {
                        update_option('grp_gbp_place_id_default', $place_id_display);
                        // If we got both placeId and location_name, we're done - don't call locations list
                        if (!empty($location_name)) {
                            // Skip the fallback - we have everything we need
                            $place_id_display = $place_id_display; // Set flag to skip fallback
                        }
                    } else {
                        // Log for debugging if placeId wasn't found in single location response
                        grp_debug_log('Place ID not found in single location response', array(
                            'location_id' => $location_id,
                            'response_keys' => array_keys($loc),
                            'has_storefrontAddress' => isset($loc['storefrontAddress'])
                        ));
                    }
                }
            } else {
                // Log error but continue to fallback
                grp_debug_log('Single location endpoint failed', array('error' => $location_details->get_error_message()));
            }
        }
        
        // Fallback: Only call locations list if the single-location endpoint failed entirely.
        // If we got a valid location response (even without placeId), there is nothing more
        // we can learn from the list endpoint and it would just be an extra API call.
        if ($location_details instanceof WP_Error) {
            $locations = $api->get_locations($account_id);
            if (is_wp_error($locations)) {
                $api_error = $locations->get_error_message();
                grp_debug_log('Failed to get locations for place_id display', array('error' => $api_error));
            } else {
                $locations_list = isset($locations['locations']) ? $locations['locations'] : (is_array($locations) ? $locations : array());
                $clean_location_id = preg_replace('#^(accounts/[^/]+/)?locations/?#', '', $location_id);
                foreach ($locations_list as $loc) {
                    $loc_name = $loc['name'] ?? '';
                    $loc_id = preg_replace('#^(accounts/[^/]+/)?locations/?#', '', $loc_name);
                    
                    // Match by ID (handle both numeric and resource name formats)
                    $matches = ($loc_id === $clean_location_id || 
                               $loc_name === $location_id || 
                               $loc_id === $location_id ||
                               (is_numeric($clean_location_id) && $loc_id === $clean_location_id) ||
                               (is_numeric($location_id) && $loc_id === $location_id));
                    
                    if ($matches) {
                        // Get location name if not cached
                        if (empty($location_name)) {
                            $location_name = $loc['title'] ?? $loc['storefrontAddress']['addressLines'][0] ?? $loc_name;
                            if (!empty($location_name)) {
                                update_option('grp_gbp_location_name', $location_name);
                            }
                        }
                        
                        // Get placeId if not cached (unlikely to be in list, but check anyway)
                        if (empty($place_id_display)) {
                            // Try multiple possible locations for placeId
                            if (isset($loc['placeId']) && !empty($loc['placeId'])) {
                                $place_id_display = $loc['placeId'];
                            } elseif (isset($loc['place_id']) && !empty($loc['place_id'])) {
                                $place_id_display = $loc['place_id'];
                            } elseif (isset($loc['storefrontAddress']['placeId']) && !empty($loc['storefrontAddress']['placeId'])) {
                                $place_id_display = $loc['storefrontAddress']['placeId'];
                            }
                            
                            // Store for future use
                            if (!empty($place_id_display)) {
                                update_option('grp_gbp_place_id_default', $place_id_display);
                            } else {
                                // Log for debugging
                                grp_debug_log('Place ID not found in location data', array(
                                    'location_id' => $location_id,
                                    'location_keys' => array_keys($loc),
                                    'has_storefrontAddress' => isset($loc['storefrontAddress'])
                                ));
                            }
                        }
                        break;
                    }
                }
            }
        }
    }
}
?>

<div class="wrap">
    <h1><?php echo esc_html__('WooCommerce', 'google-reviews-plugin'); ?></h1>
    
    <nav class="nav-tab-wrapper">
        <a href="?page=google-reviews-woocommerce&tab=settings" class="nav-tab <?php echo (!isset($_GET['tab']) || $_GET['tab'] === 'settings') ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Settings', 'google-reviews-plugin'); ?>
        </a>
        <a href="?page=google-reviews-woocommerce&tab=invites" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'invites') ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Review Invites', 'google-reviews-plugin'); ?>
        </a>
    </nav>
    
    <?php
    $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'settings';
    if ($tab === 'invites') {
        // Load invites reporting view
        include GRP_PLUGIN_DIR . 'includes/admin/views/woocommerce-invites.php';
        return;
    }
    ?>
    
    <!-- Settings Tab -->
    
    <form method="post" action="">
        <?php wp_nonce_field('grp_wc_settings'); ?>
        
        <!-- WooCommerce Integration Section -->
        <div class="grp-settings-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #eee;"><?php esc_html_e('WooCommerce Integration', 'google-reviews-plugin'); ?></h2>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="grp_wc_integration_enabled"><?php esc_html_e('Enable WooCommerce Integration', 'google-reviews-plugin'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="grp_wc_integration_enabled" name="grp_wc_integration_enabled" value="1" <?php checked($integration_enabled, true); ?>>
                            <p class="description"><?php esc_html_e('Enable the WooCommerce integration module.', 'google-reviews-plugin'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="grp_wc_invites_enabled"><?php esc_html_e('Enable Google Review Invites', 'google-reviews-plugin'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="grp_wc_invites_enabled" name="grp_wc_invites_enabled" value="1" <?php checked($invites_enabled, true); ?>>
                            <p class="description"><?php esc_html_e('Automatically send review invite emails to customers after purchase.', 'google-reviews-plugin'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="grp_wc_incentives_enabled"><?php esc_html_e('Enable Coupon Thank-You', 'google-reviews-plugin'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="grp_wc_incentives_enabled" name="grp_wc_incentives_enabled" value="1" <?php checked($incentives_enabled, true); ?>>
                            <p class="description"><?php esc_html_e('Issue discount coupons as a thank-you after customers click the review link (regardless of review sentiment).', 'google-reviews-plugin'); ?></p>
                        </td>
                    </tr>
                    
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
        </div>
        
        <!-- Coupon Settings Section -->
        <div class="grp-settings-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #eee;"><?php esc_html_e('Coupon Settings', 'google-reviews-plugin'); ?></h2>
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
        </div>
        
        <!-- Email Template Settings Section -->
        <div class="grp-settings-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #eee;"><?php esc_html_e('Email Template Settings', 'google-reviews-plugin'); ?></h2>

            <?php
            $editor_settings_base = array(
                'textarea_rows' => 10,
                'media_buttons' => false,
                'teeny' => true,
            );
            ?>

            <div class="grp-email-tabs">
                <button class="grp-email-tab active" type="button" data-template="initial"><?php esc_html_e('Initial Invite', 'google-reviews-plugin'); ?></button>
                <button class="grp-email-tab" type="button" data-template="reward"><?php esc_html_e('Coupon Reward', 'google-reviews-plugin'); ?></button>
                <button class="grp-email-tab" type="button" data-template="thank-you"><?php esc_html_e('Thank You', 'google-reviews-plugin'); ?></button>
            </div>

            <div class="grp-email-template-block grp-email-template-initial" data-template="initial">
                <label for="grp_wc_email_subject"><?php esc_html_e('Subject', 'google-reviews-plugin'); ?></label>
                <input type="text" id="grp_wc_email_subject" name="grp_wc_email_subject" value="<?php echo esc_attr($email_subject); ?>" class="large-text">
                <p class="description"><?php esc_html_e('Available merge tags: {first_name}, {order_id}, {order_date}, {store_name}, {review_url}, {coupon_value}', 'google-reviews-plugin'); ?></p>
                <label for="grp_wc_email_body"><?php esc_html_e('Body', 'google-reviews-plugin'); ?></label>
                <?php
                $initial_editor_settings = array_merge($editor_settings_base, array('textarea_name' => 'grp_wc_email_body'));
                wp_editor($email_body, 'grp_wc_email_body', $initial_editor_settings);
                ?>
                <p class="description"><?php esc_html_e('Compliance disclaimer will be appended automatically when incentives are enabled.', 'google-reviews-plugin'); ?></p>
            </div>

            <div class="grp-email-template-block grp-email-template-reward" data-template="reward">
                <label for="grp_wc_reward_subject"><?php esc_html_e('Subject', 'google-reviews-plugin'); ?></label>
                <input type="text" id="grp_wc_reward_subject" name="grp_wc_reward_subject" value="<?php echo esc_attr($reward_subject); ?>" class="large-text">
                <p class="description"><?php esc_html_e('Available merge tags: {first_name}, {order_id}, {store_name}, {coupon_code}, {coupon_value}', 'google-reviews-plugin'); ?></p>
                <label for="grp_wc_email_body_reward"><?php esc_html_e('Body', 'google-reviews-plugin'); ?></label>
                <?php
                $reward_editor_settings = array_merge($editor_settings_base, array('textarea_name' => 'grp_wc_email_body_reward'));
                wp_editor($reward_body, 'grp_wc_email_body_reward', $reward_editor_settings);
                ?>
            </div>

            <div class="grp-email-template-block grp-email-template-thank-you" data-template="thank-you">
                <label for="grp_wc_thank_you_subject"><?php esc_html_e('Subject', 'google-reviews-plugin'); ?></label>
                <input type="text" id="grp_wc_thank_you_subject" name="grp_wc_thank_you_subject" value="<?php echo esc_attr($thank_you_subject); ?>" class="large-text">
                <p class="description"><?php esc_html_e('Available merge tags: {first_name}, {order_id}, {store_name}, {coupon_code}, {coupon_value}', 'google-reviews-plugin'); ?></p>
                <label for="grp_wc_email_body_thank_you"><?php esc_html_e('Body', 'google-reviews-plugin'); ?></label>
                <?php
                $thank_you_editor_settings = array_merge($editor_settings_base, array('textarea_name' => 'grp_wc_email_body_thank_you'));
                wp_editor($thank_you_body, 'grp_wc_email_body_thank_you', $thank_you_editor_settings);
                ?>
            </div>
        </div>
        
        <?php submit_button(__('Save Settings', 'google-reviews-plugin'), 'primary', 'grp_wc_settings_submit'); ?>
    </form>
    
    <script>
    jQuery(document).ready(function($) {
        // Update coupon value unit based on type
        $('#grp_wc_coupon_type').on('change', function() {
            var unit = $(this).val() === 'percent' ? '%' : '<?php echo esc_js(get_woocommerce_currency_symbol()); ?>';
            $('#coupon-value-unit').text(unit);
        });

        $('.grp-email-tab').on('click', function() {
            var template = $(this).data('template');
            $('.grp-email-tab').removeClass('active');
            $(this).addClass('active');
            $('.grp-email-template-block').hide();
            $('.grp-email-template-' + template).show();
        });

        $('.grp-email-template-block').hide();
        $('.grp-email-template-initial').show();
    });
    </script>
    <style>
        .grp-email-template-block {
            background: #fdfdfd;
            border: 1px solid #e1e5eb;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(15, 38, 78, 0.05);
            padding: 20px 24px 18px;
            margin-bottom: 20px;
        }

        .grp-email-template-block h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.15em;
            color: #192f59;
        }

        .grp-email-template-block label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            color: #333;
        }

        .grp-email-template-block input.large-text {
            border: 1px solid #ccd0d4;
            padding: 8px 10px;
            border-radius: 6px;
            background: #fff;
        }
        .grp-email-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }

        .grp-email-tab {
            border: 1px solid #ccd0d4;
            background: #fff;
            padding: 8px 18px;
            border-radius: 28px;
            cursor: pointer;
            font-weight: 600;
            color: #4b5361;
            transition: all .2s ease;
        }

        .grp-email-tab.active {
            background: #192f59;
            color: #fff;
            border-color: #192f59;
            box-shadow: 0 5px 18px rgba(25, 47, 89, 0.2);
        }
    </style>
</div>
