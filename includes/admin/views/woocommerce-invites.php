<?php
/**
 * WooCommerce Review Invites Reporting Page
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table = $wpdb->prefix . 'grp_review_invites';

// Handle filters
$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';

// Build query
$where = array('1=1');
$query_params = array();

if (!empty($status_filter)) {
    $where[] = 'invite_status = %s';
    $query_params[] = $status_filter;
}

if (!empty($date_from)) {
    $where[] = 'created_at >= %s';
    $query_params[] = $date_from . ' 00:00:00';
}

if (!empty($date_to)) {
    $where[] = 'created_at <= %s';
    $query_params[] = $date_to . ' 23:59:59';
}

$tab = isset($_GET['email_tab']) ? sanitize_text_field($_GET['email_tab']) : '';
$hard_status = '';

if ($tab === 'pending') {
    $where[] = '(invite_status = \'scheduled\' OR (invite_status = \'clicked\' AND coupon_ready_at IS NOT NULL))';
    $hard_status = '?email_tab=pending';
} elseif ($tab === 'clicked') {
    $where[] = 'invite_status = \'clicked\'';
    $hard_status = '?email_tab=clicked';
}

$where_clause = implode(' AND ', $where);

// Get invites
$query = "SELECT * FROM {$table} WHERE {$where_clause} ORDER BY created_at DESC LIMIT 100";
if (!empty($query_params)) {
    $query = $wpdb->prepare($query, $query_params);
}

$invites = $wpdb->get_results($query);

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN invite_status = 'sent' THEN 1 ELSE 0 END) as sent,
    SUM(CASE WHEN invite_status = 'clicked' THEN 1 ELSE 0 END) as clicked,
    SUM(CASE WHEN invite_status = 'rewarded' THEN 1 ELSE 0 END) as rewarded,
    SUM(CASE WHEN invite_status = 'failed' THEN 1 ELSE 0 END) as failed,
    SUM(CASE WHEN invite_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM {$table}";
$stats = $wpdb->get_row($stats_query);

$click_rate = $stats->sent > 0 ? ($stats->clicked / $stats->sent * 100) : 0;

// Build export URLs with current filters
$export_csv_url = admin_url('admin-post.php?action=grp_export_invites_csv');
$export_xls_url = admin_url('admin-post.php?action=grp_export_invites_xls');
if ($date_from) {
    $export_csv_url .= '&date_from=' . urlencode($date_from);
    $export_xls_url .= '&date_from=' . urlencode($date_from);
}
if ($date_to) {
    $export_csv_url .= '&date_to=' . urlencode($date_to);
    $export_xls_url .= '&date_to=' . urlencode($date_to);
}
if ($status_filter) {
    $export_csv_url .= '&status=' . urlencode($status_filter);
    $export_xls_url .= '&status=' . urlencode($status_filter);
}
$export_csv_url = wp_nonce_url($export_csv_url, 'grp_export_invites');
$export_xls_url = wp_nonce_url($export_xls_url, 'grp_export_invites');
?>

<div class="wrap">
    <h1><?php esc_html_e('Review Invites', 'google-reviews-plugin'); ?></h1>
    <div style="margin-bottom: 20px;">
        <a href="?page=google-reviews-woocommerce&tab=invites<?php echo esc_attr($hard_status); ?>" class="grp-invite-tab <?php echo $tab === 'pending' ? 'grp-invite-tab-active' : ''; ?>">
            <?php esc_html_e('Pending Emails', 'google-reviews-plugin'); ?>
        </a>
        <a href="?page=google-reviews-woocommerce&tab=invites&email_tab=clicked" class="grp-invite-tab <?php echo $tab === 'clicked' ? 'grp-invite-tab-active' : ''; ?>">
            <?php esc_html_e('Clicked (ready)', 'google-reviews-plugin'); ?>
        </a>
        <a href="?page=google-reviews-woocommerce&tab=invites" class="grp-invite-tab <?php echo $tab === '' ? 'grp-invite-tab-active' : ''; ?>">
            <?php esc_html_e('All Invites', 'google-reviews-plugin'); ?>
        </a>
    </div>
    
    <!-- Export buttons -->
    <div style="margin: 20px 0; display: flex; gap: 10px; align-items: center;">
        <span style="font-weight: 600;"><?php esc_html_e('Export:', 'google-reviews-plugin'); ?></span>
        <a href="<?php echo esc_url($export_csv_url); ?>" class="button">
            <span class="dashicons dashicons-media-spreadsheet" style="vertical-align: middle; margin-right: 5px;"></span>
            <?php esc_html_e('CSV', 'google-reviews-plugin'); ?>
        </a>
        <a href="<?php echo esc_url($export_xls_url); ?>" class="button">
            <span class="dashicons dashicons-media-spreadsheet" style="vertical-align: middle; margin-right: 5px;"></span>
            <?php esc_html_e('Excel/Google Sheets', 'google-reviews-plugin'); ?>
        </a>
        <span class="description" style="margin-left: 10px;">
            <?php esc_html_e('Exports will include all records matching current filters', 'google-reviews-plugin'); ?>
        </span>
    </div>
    
    <!-- Statistics -->
    <div class="grp-invites-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; margin: 20px 0;">
        <div style="padding: 15px; background: #f5f5f5; border-radius: 4px;">
            <strong><?php esc_html_e('Total Invites', 'google-reviews-plugin'); ?></strong>
            <div style="font-size: 24px; font-weight: bold;"><?php echo esc_html($stats->total); ?></div>
        </div>
        <div style="padding: 15px; background: #f5f5f5; border-radius: 4px;">
            <strong><?php esc_html_e('Sent', 'google-reviews-plugin'); ?></strong>
            <div style="font-size: 24px; font-weight: bold;"><?php echo esc_html($stats->sent); ?></div>
        </div>
        <div style="padding: 15px; background: #f5f5f5; border-radius: 4px;">
            <strong><?php esc_html_e('Clicked', 'google-reviews-plugin'); ?></strong>
            <div style="font-size: 24px; font-weight: bold;"><?php echo esc_html($stats->clicked); ?></div>
            <small><?php echo esc_html(number_format($click_rate, 1)); ?>% CTR</small>
        </div>
        <div style="padding: 15px; background: #f5f5f5; border-radius: 4px;">
            <strong><?php esc_html_e('Rewarded', 'google-reviews-plugin'); ?></strong>
            <div style="font-size: 24px; font-weight: bold;"><?php echo esc_html($stats->rewarded); ?></div>
        </div>
        <div style="padding: 15px; background: #f5f5f5; border-radius: 4px;">
            <strong><?php esc_html_e('Failed', 'google-reviews-plugin'); ?></strong>
            <div style="font-size: 24px; font-weight: bold;"><?php echo esc_html($stats->failed); ?></div>
        </div>
        <div style="padding: 15px; background: #f5f5f5; border-radius: 4px;">
            <strong><?php esc_html_e('Cancelled', 'google-reviews-plugin'); ?></strong>
            <div style="font-size: 24px; font-weight: bold;"><?php echo esc_html($stats->cancelled); ?></div>
        </div>
    </div>
    
    <!-- Filters -->
    <form method="get" action="" style="margin: 20px 0;">
        <input type="hidden" name="page" value="google-reviews-woocommerce">
        <input type="hidden" name="tab" value="invites">
        
        <select name="status">
            <option value=""><?php esc_html_e('All Statuses', 'google-reviews-plugin'); ?></option>
            <option value="scheduled" <?php selected($status_filter, 'scheduled'); ?>><?php esc_html_e('Scheduled', 'google-reviews-plugin'); ?></option>
            <option value="sent" <?php selected($status_filter, 'sent'); ?>><?php esc_html_e('Sent', 'google-reviews-plugin'); ?></option>
            <option value="clicked" <?php selected($status_filter, 'clicked'); ?>><?php esc_html_e('Clicked', 'google-reviews-plugin'); ?></option>
            <option value="rewarded" <?php selected($status_filter, 'rewarded'); ?>><?php esc_html_e('Rewarded', 'google-reviews-plugin'); ?></option>
            <option value="failed" <?php selected($status_filter, 'failed'); ?>><?php esc_html_e('Failed', 'google-reviews-plugin'); ?></option>
            <option value="cancelled" <?php selected($status_filter, 'cancelled'); ?>><?php esc_html_e('Cancelled', 'google-reviews-plugin'); ?></option>
        </select>
        
        <input type="date" name="date_from" value="<?php echo esc_attr($date_from); ?>" placeholder="<?php esc_attr_e('From Date', 'google-reviews-plugin'); ?>">
        <input type="date" name="date_to" value="<?php echo esc_attr($date_to); ?>" placeholder="<?php esc_attr_e('To Date', 'google-reviews-plugin'); ?>">
        
        <button type="submit" class="button"><?php esc_html_e('Filter', 'google-reviews-plugin'); ?></button>
        <a href="?page=google-reviews-woocommerce&tab=invites" class="button"><?php esc_html_e('Reset', 'google-reviews-plugin'); ?></a>
    </form>
    
    <!-- Invites Table -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('ID', 'google-reviews-plugin'); ?></th>
                <th><?php esc_html_e('Order', 'google-reviews-plugin'); ?></th>
                <th><?php esc_html_e('Email', 'google-reviews-plugin'); ?></th>
                <th><?php esc_html_e('Status', 'google-reviews-plugin'); ?></th>
                <th><?php esc_html_e('Scheduled', 'google-reviews-plugin'); ?></th>
                <th><?php esc_html_e('Sent', 'google-reviews-plugin'); ?></th>
                <th><?php esc_html_e('Clicked', 'google-reviews-plugin'); ?></th>
                <th><?php esc_html_e('Coupon', 'google-reviews-plugin'); ?></th>
                <th><?php esc_html_e('Created', 'google-reviews-plugin'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($invites)): ?>
                <tr>
                    <td colspan="9"><?php esc_html_e('No invites found.', 'google-reviews-plugin'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($invites as $invite): ?>
                    <?php
                    $order = wc_get_order($invite->order_id);
                    $order_link = $order ? admin_url('post.php?post=' . $invite->order_id . '&action=edit') : '#';
                    ?>
                    <tr>
                        <td><?php echo esc_html($invite->id); ?></td>
                        <td>
                            <?php if ($order): ?>
                                <a href="<?php echo esc_url($order_link); ?>">#<?php echo esc_html($invite->order_id); ?></a>
                            <?php else: ?>
                                #<?php echo esc_html($invite->order_id); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($invite->email); ?></td>
                        <td>
                            <span class="grp-status-badge grp-status-<?php echo esc_attr($invite->invite_status); ?>">
                                <?php echo esc_html(ucfirst($invite->invite_status)); ?>
                            </span>
                        </td>
                        <td><?php echo $invite->scheduled_at ? esc_html(mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $invite->scheduled_at)) : '-'; ?></td>
                        <td><?php echo $invite->sent_at ? esc_html(mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $invite->sent_at)) : '-'; ?></td>
                        <td><?php echo $invite->clicked_at ? esc_html(mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $invite->clicked_at)) : '-'; ?></td>
                        <td>
                            <?php if (!empty($invite->coupon_code)): ?>
                                <code><?php echo esc_html($invite->coupon_code); ?></code>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html(mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $invite->created_at)); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <style>
    .grp-status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 600;
    }
    .grp-status-scheduled { background: #f0f0f0; color: #666; }
    .grp-status-sent { background: #2271b1; color: #fff; }
    .grp-status-clicked { background: #00a32a; color: #fff; }
    .grp-status-rewarded { background: #00a32a; color: #fff; }
    .grp-status-failed { background: #d63638; color: #fff; }
    .grp-status-cancelled { background: #999; color: #fff; }
    .grp-invite-tab {
        display: inline-block;
        border: 1px solid #ccd0d4;
        padding: 6px 16px;
        border-radius: 999px;
        margin-right: 10px;
        background: #fff;
        text-decoration: none;
        color: #233;
        font-weight: 600;
        transition: all .2s ease;
    }
    .grp-invite-tab-active {
        background: #192f59;
        border-color: #192f59;
        color: #fff;
        box-shadow: 0 6px 25px rgba(25,47,89,.2);
    }
    </style>
</div>

