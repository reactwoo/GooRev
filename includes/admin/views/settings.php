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
            <form method="post" action="options.php" class="grp-form">
                <?php
                settings_fields('grp_settings');
                do_settings_sections('grp_settings');
                ?>
                <!-- Sections and fields are rendered by do_settings_sections('grp_settings') above -->
                
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
            
            <!-- Connection tools (visible when connected) -->
            <?php if ($is_connected): ?>
            <div class="grp-sidebar-card">
                <h3><?php esc_html_e('Connection', 'google-reviews-plugin'); ?></h3>
                <p>
                    <button id="grp-test-connection" class="button">
                        <?php esc_html_e('Test Connection', 'google-reviews-plugin'); ?>
                    </button>
                    <a id="grp-disconnect" href="<?php echo esc_url($disconnect_url); ?>" class="button">
                        <?php esc_html_e('Disconnect', 'google-reviews-plugin'); ?>
                    </a>
                </p>
            </div>
            <?php endif; ?>

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

<!-- Inline script removed in favor of centralized assets/js/admin.js wiring -->