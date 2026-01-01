<?php
/**
 * Dashboard view
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap grp-dashboard">
    <h1><?php esc_html_e('Google Reviews Dashboard', 'google-reviews-plugin'); ?></h1>
    
    <div class="grp-dashboard-grid">
        <!-- Connection Status -->
        <div class="grp-dashboard-card">
            <h2><?php esc_html_e('Connection Status', 'google-reviews-plugin'); ?></h2>
            <div class="grp-status-indicator">
                <?php if ($is_connected): ?>
                    <span class="grp-status-connected">✓ <?php esc_html_e('Connected', 'google-reviews-plugin'); ?></span>
                <?php else: ?>
                    <span class="grp-status-disconnected">✗ <?php esc_html_e('Not Connected', 'google-reviews-plugin'); ?></span>
                <?php endif; ?>
            </div>
            <?php if (!$is_connected): ?>
                <p><?php esc_html_e('Connect your Google Business account to start displaying reviews.', 'google-reviews-plugin'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=google-reviews-settings'); ?>" class="button button-primary">
                    <?php esc_html_e('Connect Account', 'google-reviews-plugin'); ?>
                </a>
            <?php endif; ?>
        </div>
        
        <!-- Recent Reviews -->
        <div class="grp-dashboard-card">
            <h2><?php esc_html_e('Recent Reviews', 'google-reviews-plugin'); ?></h2>
            <?php if (!empty($recent_reviews)): ?>
                <div class="grp-recent-reviews">
                    <?php foreach (array_slice($recent_reviews, 0, 3) as $review): ?>
                        <div class="grp-review-summary">
                            <div class="grp-review-rating">
                                <?php echo $review['stars_html']; ?>
                            </div>
                            <div class="grp-review-text">
                                <?php echo wp_kses_post(wp_trim_words($review['text'], 15)); ?>
                            </div>
                            <div class="grp-review-author">
                                <?php echo esc_html($review['author_name']); ?>
                                <span class="grp-review-date"><?php echo esc_html($review['time_formatted']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="<?php echo admin_url('admin.php?page=google-reviews-reviews'); ?>" class="button">
                    <?php esc_html_e('View All Reviews', 'google-reviews-plugin'); ?>
                </a>
            <?php else: ?>
                <p><?php esc_html_e('No reviews found. Sync your reviews to get started.', 'google-reviews-plugin'); ?></p>
                <?php if ($is_connected): ?>
                    <button id="grp-sync-reviews" class="button button-primary">
                        <?php esc_html_e('Sync Reviews', 'google-reviews-plugin'); ?>
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- Quick Stats -->
        <div class="grp-dashboard-card">
            <h2><?php esc_html_e('Quick Stats', 'google-reviews-plugin'); ?></h2>
            <div class="grp-stats-grid">
                <div class="grp-stat">
                    <span class="grp-stat-number"><?php echo count($recent_reviews); ?></span>
                    <span class="grp-stat-label"><?php esc_html_e('Total Reviews', 'google-reviews-plugin'); ?></span>
                </div>
                <div class="grp-stat">
                    <span class="grp-stat-number">
                        <?php
                        $avg_rating = 0;
                        if (!empty($recent_reviews)) {
                            $total_rating = array_sum(wp_list_pluck($recent_reviews, 'rating'));
                            $avg_rating = round($total_rating / count($recent_reviews), 1);
                        }
                        echo $avg_rating;
                        ?>
                    </span>
                    <span class="grp-stat-label"><?php esc_html_e('Average Rating', 'google-reviews-plugin'); ?></span>
                </div>
            </div>
        </div>
        
        <!-- WooCommerce Integration Stats -->
        <?php
        // Check if WooCommerce addon is enabled
        $woocommerce_stats = null;
        if (class_exists('GRP_Addons')) {
            $addons = GRP_Addons::get_instance();
            if ($addons->is_addon_enabled('woocommerce') && class_exists('WooCommerce')) {
                global $wpdb;
                $invites_table = $wpdb->prefix . 'grp_review_invites';
                
                // Count total invites sent (sent, clicked, or rewarded)
                $invites_sent = $wpdb->get_var(
                    "SELECT COUNT(*) FROM {$invites_table} 
                    WHERE invite_status IN ('sent', 'clicked', 'rewarded')"
                );
                
                // Count total coupons created
                $coupons_created = $wpdb->get_var(
                    "SELECT COUNT(*) FROM {$invites_table} 
                    WHERE coupon_id IS NOT NULL AND coupon_id > 0"
                );
                
                // Count invites scheduled (pending)
                $invites_scheduled = $wpdb->get_var(
                    "SELECT COUNT(*) FROM {$invites_table} 
                    WHERE invite_status = 'scheduled'"
                );
                
                $woocommerce_stats = array(
                    'invites_sent' => (int) $invites_sent,
                    'coupons_created' => (int) $coupons_created,
                    'invites_scheduled' => (int) $invites_scheduled
                );
            }
        }
        ?>
        
        <?php if ($woocommerce_stats !== null): ?>
        <div class="grp-dashboard-card grp-woocommerce-stats">
            <h2>
                <span class="dashicons dashicons-cart" style="vertical-align: middle; margin-right: 5px;"></span>
                <?php esc_html_e('WooCommerce Integration', 'google-reviews-plugin'); ?>
            </h2>
            <div class="grp-stats-grid">
                <div class="grp-stat">
                    <span class="grp-stat-number"><?php echo number_format($woocommerce_stats['invites_sent']); ?></span>
                    <span class="grp-stat-label"><?php esc_html_e('Invites Sent', 'google-reviews-plugin'); ?></span>
                </div>
                <div class="grp-stat">
                    <span class="grp-stat-number"><?php echo number_format($woocommerce_stats['coupons_created']); ?></span>
                    <span class="grp-stat-label"><?php esc_html_e('Coupons Created', 'google-reviews-plugin'); ?></span>
                </div>
                <div class="grp-stat">
                    <span class="grp-stat-number"><?php echo number_format($woocommerce_stats['invites_scheduled']); ?></span>
                    <span class="grp-stat-label"><?php esc_html_e('Scheduled', 'google-reviews-plugin'); ?></span>
                </div>
            </div>
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                <a href="<?php echo admin_url('admin.php?page=google-reviews-woocommerce&tab=invites'); ?>" class="button">
                    <?php esc_html_e('View Review Invites', 'google-reviews-plugin'); ?>
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Pro Features -->
        <?php if (!$is_pro): ?>
        <div class="grp-dashboard-card grp-pro-card">
            <h2><?php esc_html_e('Upgrade to Pro', 'google-reviews-plugin'); ?></h2>
            <p><?php esc_html_e('Unlock advanced features and customization options.', 'google-reviews-plugin'); ?></p>
            <ul class="grp-pro-features">
                <li>✓ <?php esc_html_e('Multiple Locations', 'google-reviews-plugin'); ?></li>
                <li>✓ <?php esc_html_e('Product Integration', 'google-reviews-plugin'); ?></li>
                <li>✓ <?php esc_html_e('Advanced Customization', 'google-reviews-plugin'); ?></li>
                <li>✓ <?php esc_html_e('Analytics Dashboard', 'google-reviews-plugin'); ?></li>
            </ul>
            <a href="https://reactwoo.com/google-reviews-plugin-pro/" class="button button-primary" target="_blank">
                <?php esc_html_e('Upgrade Now', 'google-reviews-plugin'); ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Quick Actions -->
    <div class="grp-quick-actions">
        <h2><?php esc_html_e('Quick Actions', 'google-reviews-plugin'); ?></h2>
        <div class="grp-action-buttons">
            <a href="<?php echo admin_url('admin.php?page=google-reviews-settings'); ?>" class="button">
                <?php esc_html_e('Settings', 'google-reviews-plugin'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=google-reviews-styles'); ?>" class="button">
                <?php esc_html_e('Customize Styles', 'google-reviews-plugin'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=google-reviews-help'); ?>" class="button">
                <?php esc_html_e('Help & Documentation', 'google-reviews-plugin'); ?>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=google-reviews&restart_onboarding=1')); ?>" 
               id="grp-restart-wizard" 
               class="button" 
               title="<?php esc_attr_e('Restart the setup wizard', 'google-reviews-plugin'); ?>">
                <span class="dashicons dashicons-update" style="vertical-align: middle; margin-top: -2px;"></span>
                <?php esc_html_e('Setup Wizard', 'google-reviews-plugin'); ?>
            </a>
            <?php if ($is_connected): ?>
                <button id="grp-test-connection" class="button">
                    <?php esc_html_e('Test Connection', 'google-reviews-plugin'); ?>
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.grp-dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.grp-dashboard-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.grp-dashboard-card h2 {
    margin-top: 0;
    color: #23282d;
}

.grp-status-indicator {
    margin: 10px 0;
}

.grp-status-connected {
    color: #46b450;
    font-weight: bold;
}

.grp-status-disconnected {
    color: #dc3232;
    font-weight: bold;
}

.grp-recent-reviews {
    margin: 15px 0;
}

.grp-review-summary {
    border-bottom: 1px solid #eee;
    padding: 10px 0;
}

.grp-review-summary:last-child {
    border-bottom: none;
}

.grp-review-rating {
    margin-bottom: 5px;
}

.grp-review-text {
    color: #666;
    font-size: 14px;
    margin-bottom: 5px;
}

.grp-review-author {
    font-size: 12px;
    color: #999;
}

.grp-review-date {
    margin-left: 10px;
}

.grp-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 20px;
    margin: 15px 0;
}

.grp-woocommerce-stats .grp-stats-grid {
    grid-template-columns: repeat(3, 1fr);
}

.grp-stat {
    text-align: center;
}

.grp-stat-number {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #0073aa;
}

.grp-stat-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
}

.grp-pro-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.grp-pro-card h2,
.grp-pro-card p {
    color: white;
}

.grp-pro-features {
    list-style: none;
    padding: 0;
    margin: 15px 0;
}

.grp-pro-features li {
    padding: 5px 0;
}

.grp-quick-actions {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin-top: 20px;
}

.grp-action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 15px;
}
</style>