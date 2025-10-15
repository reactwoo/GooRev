<?php
/**
 * Settings view
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap grp-settings">
    <h1><?php esc_html_e('Google Reviews Settings', 'google-reviews-plugin'); ?></h1>
    
    <div class="grp-settings-container">
        <div class="grp-settings-main">
            <form method="post" action="options.php">
                <?php
                settings_fields('grp_settings');
                do_settings_sections('grp_settings');
                ?>
                
                <h2><?php esc_html_e('Google API Configuration', 'google-reviews-plugin'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('Client ID', 'google-reviews-plugin'); ?></th>
                        <td>
                            <input type="text" name="grp_google_client_id" value="<?php echo esc_attr(get_option('grp_google_client_id', '')); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e('Enter your Google OAuth 2.0 Client ID.', 'google-reviews-plugin'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Client Secret', 'google-reviews-plugin'); ?></th>
                        <td>
                            <input type="password" name="grp_google_client_secret" value="<?php echo esc_attr(get_option('grp_google_client_secret', '')); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e('Enter your Google OAuth 2.0 Client Secret.', 'google-reviews-plugin'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <?php if ($is_connected): ?>
                    <div class="grp-connection-status">
                        <h3><?php esc_html_e('Connection Status', 'google-reviews-plugin'); ?></h3>
                        <div class="grp-status-connected">
                            ✓ <?php esc_html_e('Connected to Google My Business', 'google-reviews-plugin'); ?>
                        </div>
                        <p>
                            <button id="grp-test-connection" class="button"><?php esc_html_e('Test Connection', 'google-reviews-plugin'); ?></button>
                            <button id="grp-disconnect" class="button button-secondary"><?php esc_html_e('Disconnect', 'google-reviews-plugin'); ?></button>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="grp-connection-setup">
                        <h3><?php esc_html_e('Connect to Google My Business', 'google-reviews-plugin'); ?></h3>
                        <p><?php esc_html_e('To connect your Google Business account, you need to:', 'google-reviews-plugin'); ?></p>
                        <ol>
                            <li><?php esc_html_e('Create a Google Cloud Project', 'google-reviews-plugin'); ?></li>
                            <li><?php esc_html_e('Enable the Google My Business API', 'google-reviews-plugin'); ?></li>
                            <li><?php esc_html_e('Create OAuth 2.0 credentials', 'google-reviews-plugin'); ?></li>
                            <li><?php esc_html_e('Add the redirect URI:', 'google-reviews-plugin'); ?> <code><?php echo admin_url('admin.php?page=google-reviews-settings&action=oauth_callback'); ?></code></li>
                        </ol>
                        <p>
                            <a href="https://console.developers.google.com/" target="_blank" class="button button-primary">
                                <?php esc_html_e('Open Google Cloud Console', 'google-reviews-plugin'); ?>
                            </a>
                        </p>
                    </div>
                <?php endif; ?>
                
                <h2><?php esc_html_e('Display Settings', 'google-reviews-plugin'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('Default Style', 'google-reviews-plugin'); ?></th>
                        <td>
                            <?php
                            $styles = new GRP_Styles();
                            $available_styles = $styles->get_styles();
                            $default_style = get_option('grp_default_style', 'modern');
                            ?>
                            <select name="grp_default_style">
                                <?php foreach ($available_styles as $key => $style): ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($default_style, $key); ?>>
                                        <?php echo esc_html($style['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php esc_html_e('Default style for displaying reviews.', 'google-reviews-plugin'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Default Review Count', 'google-reviews-plugin'); ?></th>
                        <td>
                            <input type="number" name="grp_default_count" value="<?php echo esc_attr(get_option('grp_default_count', 5)); ?>" min="1" max="50" />
                            <p class="description"><?php esc_html_e('Default number of reviews to display.', 'google-reviews-plugin'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <h2><?php esc_html_e('Cache Settings', 'google-reviews-plugin'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('Cache Duration', 'google-reviews-plugin'); ?></th>
                        <td>
                            <input type="number" name="grp_cache_duration" value="<?php echo esc_attr(get_option('grp_cache_duration', 3600)); ?>" min="300" max="86400" />
                            <p class="description"><?php esc_html_e('How long to cache reviews (in seconds).', 'google-reviews-plugin'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        
        <div class="grp-settings-sidebar">
            <!-- License Section -->
            <div class="grp-sidebar-card">
                <h3><?php esc_html_e('License', 'google-reviews-plugin'); ?></h3>
                <?php if ($is_pro): ?>
                    <div class="grp-license-status">
                        <span class="grp-status-connected">✓ <?php esc_html_e('Pro License Active', 'google-reviews-plugin'); ?></span>
                    </div>
                    <p><?php esc_html_e('You have access to all Pro features.', 'google-reviews-plugin'); ?></p>
                <?php else: ?>
                    <div class="grp-license-status">
                        <span class="grp-status-disconnected">✗ <?php esc_html_e('Free Version', 'google-reviews-plugin'); ?></span>
                    </div>
                    <p><?php esc_html_e('Upgrade to Pro for advanced features.', 'google-reviews-plugin'); ?></p>
                    <a href="https://reactwoo.com/google-reviews-plugin-pro/" class="button button-primary" target="_blank">
                        <?php esc_html_e('Upgrade to Pro', 'google-reviews-plugin'); ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Quick Start -->
            <div class="grp-sidebar-card">
                <h3><?php esc_html_e('Quick Start', 'google-reviews-plugin'); ?></h3>
                <ol>
                    <li><?php esc_html_e('Configure Google API credentials', 'google-reviews-plugin'); ?></li>
                    <li><?php esc_html_e('Connect your Google Business account', 'google-reviews-plugin'); ?></li>
                    <li><?php esc_html_e('Sync your reviews', 'google-reviews-plugin'); ?></li>
                    <li><?php esc_html_e('Add shortcode to your pages', 'google-reviews-plugin'); ?></li>
                </ol>
                <p>
                    <code>[google_reviews]</code>
                </p>
            </div>
            
            <!-- Support -->
            <div class="grp-sidebar-card">
                <h3><?php esc_html_e('Need Help?', 'google-reviews-plugin'); ?></h3>
                <p><?php esc_html_e('Check out our documentation and support resources.', 'google-reviews-plugin'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=google-reviews-help'); ?>" class="button">
                    <?php esc_html_e('View Help', 'google-reviews-plugin'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.grp-settings-container {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    margin-top: 20px;
}

.grp-settings-main {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
}

.grp-settings-sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.grp-sidebar-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
}

.grp-sidebar-card h3 {
    margin-top: 0;
    color: #23282d;
}

.grp-connection-status,
.grp-connection-setup {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin: 20px 0;
}

.grp-status-connected {
    color: #46b450;
    font-weight: bold;
}

.grp-status-disconnected {
    color: #dc3232;
    font-weight: bold;
}

.grp-license-status {
    margin: 10px 0;
}

@media (max-width: 768px) {
    .grp-settings-container {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Test connection
    $('#grp-test-connection').on('click', function() {
        var button = $(this);
        button.prop('disabled', true).text('<?php esc_js_e('Testing...', 'google-reviews-plugin'); ?>');
        
        $.post(ajaxurl, {
            action: 'grp_test_connection',
            nonce: grp_admin.nonce
        }, function(response) {
            if (response.success) {
                alert('<?php esc_js_e('Connection successful!', 'google-reviews-plugin'); ?>');
            } else {
                alert('<?php esc_js_e('Connection failed: ', 'google-reviews-plugin'); ?>' + response.data);
            }
        }).always(function() {
            button.prop('disabled', false).text('<?php esc_js_e('Test Connection', 'google-reviews-plugin'); ?>');
        });
    });
    
    // Disconnect
    $('#grp-disconnect').on('click', function() {
        if (confirm('<?php esc_js_e('Are you sure you want to disconnect?', 'google-reviews-plugin'); ?>')) {
            // Add disconnect functionality here
            location.reload();
        }
    });
});
</script>