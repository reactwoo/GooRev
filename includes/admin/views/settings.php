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

            <!-- FAQ / Troubleshooting -->
            <div class="grp-faq">
                <h2><?php esc_html_e('Troubleshooting & FAQ', 'google-reviews-plugin'); ?></h2>
                <div class="grp-faq-grid">
                    <div class="grp-faq-item">
                        <details>
                            <summary><?php esc_html_e('Connected but no accounts are listed', 'google-reviews-plugin'); ?></summary>
                            <div class="grp-faq-content">
                                <ul>
                                    <li><?php esc_html_e('Ensure you connected with the Google account that owns/manages the Business Profile.', 'google-reviews-plugin'); ?></li>
                                    <li><?php esc_html_e('Confirm the required Business Profile APIs are enabled in your Google Cloud project.', 'google-reviews-plugin'); ?></li>
                                    <li><?php esc_html_e('If your project shows 0 QPM for Account Management API, request Business Profile API access approval from Google (not a quota increase).', 'google-reviews-plugin'); ?>
                                        <a href="https://developers.google.com/my-business/content/prereqs" target="_blank" rel="noopener"><?php esc_html_e('Request access', 'google-reviews-plugin'); ?></a>
                                    </li>
                                    <li><?php esc_html_e('Click Refresh next to the Account selector after approval.', 'google-reviews-plugin'); ?></li>
                                </ul>
                            </div>
                        </details>
                    </div>
                    <div class="grp-faq-item">
                        <details>
                            <summary><?php esc_html_e('Account selected but no locations found', 'google-reviews-plugin'); ?></summary>
                            <div class="grp-faq-content">
                                <ul>
                                    <li><?php esc_html_e('Verify the selected account actually has locations in Google Business Profile.', 'google-reviews-plugin'); ?></li>
                                    <li><?php esc_html_e('Try another account if you manage multiple organizations.', 'google-reviews-plugin'); ?></li>
                                    <li><?php esc_html_e('Ensure the Business Information API is enabled on your project.', 'google-reviews-plugin'); ?></li>
                                </ul>
                            </div>
                        </details>
                    </div>
                    <div class="grp-faq-item">
                        <details>
                            <summary><?php esc_html_e('Connection failed: Quota exceeded (429, RESOURCE_EXHAUSTED)', 'google-reviews-plugin'); ?></summary>
                            <div class="grp-faq-content">
                                <p><?php esc_html_e('This commonly happens when your Google Cloud project has 0 requests per minute for the Business Profile Account Management API.', 'google-reviews-plugin'); ?></p>
                                <ul>
                                    <li><?php esc_html_e('Do not request a simple quota increase. Instead, complete the Business Profile API prerequisites and request access.', 'google-reviews-plugin'); ?>
                                        <a href="https://developers.google.com/my-business/content/prereqs" target="_blank" rel="noopener"><?php esc_html_e('Follow Google’s prerequisites', 'google-reviews-plugin'); ?></a>
                                    </li>
                                    <li><?php esc_html_e('After approval, wait a few minutes and test the connection again.', 'google-reviews-plugin'); ?></li>
                                </ul>
                            </div>
                        </details>
                    </div>
                    <div class="grp-faq-item">
                        <details>
                            <summary><?php esc_html_e('Where do I enter Client ID and Client Secret?', 'google-reviews-plugin'); ?></summary>
                            <div class="grp-faq-content">
                                <p><?php esc_html_e('Client ID/Secret are part of the Advanced (Pro) section below. Enable the Pro configuration checkbox to unlock those fields and save your credentials.', 'google-reviews-plugin'); ?></p>
                            </div>
                        </details>
                    </div>
                </div>
            </div>
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

/* FAQ two-column collapsible */
.grp-faq {
    margin-top: 30px;
}
.grp-faq-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.grp-faq-item details {
    background: #f9f9f9;
    border: 1px solid #e2e2e2;
    border-radius: 6px;
    padding: 12px 14px;
}
.grp-faq-item summary {
    cursor: pointer;
    font-weight: 600;
    color: #23282d;
}
.grp-faq-content {
    margin-top: 10px;
}
.grp-faq-content ul {
    margin: 0 0 0 18px;
}

@media (max-width: 768px) {
    .grp-settings-container {
        grid-template-columns: 1fr;
    }
    .grp-faq-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Inline script removed in favor of centralized assets/js/admin.js wiring -->