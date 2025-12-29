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
    
    <div style="margin: 20px 0;">
        <button id="grp-restart-wizard" class="button" title="<?php esc_attr_e('Restart the setup wizard', 'google-reviews-plugin'); ?>">
            <span class="dashicons dashicons-update" style="vertical-align: middle; margin-top: -2px;"></span>
            <?php esc_html_e('Restart Setup Wizard', 'google-reviews-plugin'); ?>
        </button>
    </div>
    
    <div class="grp-settings-container">
        <div class="grp-settings-main">
            <form method="post" action="options.php" class="grp-form">
                <?php
                settings_fields('grp_settings');
                
                // Get all settings sections
                global $wp_settings_sections, $wp_settings_fields;
                $page = 'grp_settings';
                
                if (isset($wp_settings_sections[$page])) {
                    foreach ((array) $wp_settings_sections[$page] as $section) {
                        // Check if this is the Enterprise credentials section
                        if ($section['id'] === 'grp_enterprise_credentials') {
                            // Wrap Enterprise section in grp-settings-section div
                            echo '<div class="grp-settings-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">';
                        }
                        
                        // Render section title
                        if ($section['title']) {
                            if ($section['id'] === 'grp_enterprise_credentials') {
                                echo '<h2 class="grp-section-title" style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #eee;">' . esc_html($section['title']) . '</h2>';
                            } else {
                                echo "<h2>{$section['title']}</h2>\n";
                            }
                        }
                        
                        // Render section description
                        if ($section['callback']) {
                            call_user_func($section['callback'], $section);
                        }
                        
                        // Render fields
                        if (!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']])) {
                            continue;
                        }
                        
                        echo '<table class="form-table" role="presentation">';
                        do_settings_fields($page, $section['id']);
                        echo '</table>';
                        
                        // Close Enterprise section wrapper
                        if ($section['id'] === 'grp_enterprise_credentials') {
                            echo '</div>';
                        }
                    }
                }
                ?>
                
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
                                    <li><?php esc_html_e('Click Refresh next to the Account selector to reload accounts.', 'google-reviews-plugin'); ?></li>
                                    <li><?php esc_html_e('If using custom credentials, confirm the required Business Profile APIs are enabled in your Google Cloud project.', 'google-reviews-plugin'); ?></li>
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
                                </ul>
                            </div>
                        </details>
                    </div>
                    <div class="grp-faq-item">
                        <details>
                            <summary><?php esc_html_e('Do I need to create a Google Cloud Project?', 'google-reviews-plugin'); ?></summary>
                            <div class="grp-faq-content">
                                <p><?php esc_html_e('No! By default, the plugin uses our API server for OAuth. Simply click "Connect Google Account" to get started.', 'google-reviews-plugin'); ?></p>
                                <p><?php esc_html_e('Custom Google Cloud credentials are only needed if you want to use your own project for enterprise-level control over API quotas and usage.', 'google-reviews-plugin'); ?></p>
                            </div>
                        </details>
                    </div>
                    <div class="grp-faq-item">
                        <details>
                            <summary><?php esc_html_e('Where do I enter Client ID and Client Secret?', 'google-reviews-plugin'); ?></summary>
                            <div class="grp-faq-content">
                                <p><?php esc_html_e('Client ID/Secret are only needed if you want to use your own Google Cloud Project. They are in the "Enterprise: Custom Google Credentials" section below.', 'google-reviews-plugin'); ?></p>
                                <p><?php esc_html_e('Most users do not need to configure this - the default setup works without any credentials.', 'google-reviews-plugin'); ?></p>
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
                <?php
                $license = new GRP_License();
                $license_key = $license->get_license_key();
                $license_status = $license->get_license_status();
                ?>
                <?php
                $license = new GRP_License();
                $has_license = $license->has_license();
                $is_free = $license->is_free();
                ?>
                <?php if ($has_license): ?>
                    <div class="grp-license-status">
                        <?php if ($is_free): ?>
                            <span class="grp-status-connected">✓ <?php esc_html_e('Free License Active', 'google-reviews-plugin'); ?></span>
                        <?php elseif ($is_pro): ?>
                            <span class="grp-status-connected">✓ <?php esc_html_e('Pro License Active', 'google-reviews-plugin'); ?></span>
                        <?php else: ?>
                            <span class="grp-status-connected">✓ <?php esc_html_e('Enterprise License Active', 'google-reviews-plugin'); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($is_free): ?>
                        <p><?php esc_html_e('You have access to free features. Upgrade to Pro or Enterprise for advanced features.', 'google-reviews-plugin'); ?></p>
                    <?php elseif ($is_pro): ?>
                        <p><?php esc_html_e('You have access to all Pro features.', 'google-reviews-plugin'); ?></p>
                    <?php else: ?>
                        <p><?php esc_html_e('You have access to all Enterprise features including AI-powered review responses.', 'google-reviews-plugin'); ?></p>
                    <?php endif; ?>
                    <form method="post" action="" style="margin-top: 15px;">
                        <?php wp_nonce_field('grp_license_nonce'); ?>
                        <input type="hidden" name="grp_license_action" value="check">
                        <button type="submit" class="button" style="width: 100%; margin-bottom: 5px;">
                            <?php esc_html_e('Refresh Token', 'google-reviews-plugin'); ?>
                        </button>
                    </form>
                    <form method="post" action="" style="margin-top: 5px;">
                        <?php wp_nonce_field('grp_license_nonce'); ?>
                        <input type="hidden" name="grp_license_action" value="deactivate">
                        <button type="submit" class="button" onclick="return confirm('<?php esc_attr_e('Are you sure you want to deactivate your license?', 'google-reviews-plugin'); ?>');" style="width: 100%;">
                            <?php esc_html_e('Deactivate License', 'google-reviews-plugin'); ?>
                        </button>
                    </form>
           <?php else: ?>
               <div class="grp-license-status">
                   <span class="grp-status-disconnected">✗ <?php esc_html_e('No License Active', 'google-reviews-plugin'); ?></span>
               </div>
               <p><?php esc_html_e('To connect to Google easily, activate a free license (no credit card required). You can also use your own Google Cloud credentials below.', 'google-reviews-plugin'); ?></p>
               <p style="margin-top: 10px; font-size: 12px; color: #666;">
                   <?php esc_html_e('Pro and Enterprise licenses unlock advanced features like multiple locations, analytics, and AI-powered review responses.', 'google-reviews-plugin'); ?>
               </p>
               
               <!-- Free License Activation -->
               <div style="margin-top: 15px; padding: 15px; background: #f0f6fc; border: 1px solid #c6d9f0; border-radius: 4px;">
                   <h4 style="margin-top: 0; margin-bottom: 10px;"><?php esc_html_e('Get Free License', 'google-reviews-plugin'); ?></h4>
                   <p style="margin: 0 0 10px 0; font-size: 13px;">
                       <?php esc_html_e('Activate a free license to use our cloud server (no Google Cloud setup needed).', 'google-reviews-plugin'); ?>
                   </p>
                   <form method="post" action="" id="grp-free-license-form">
                       <?php wp_nonce_field('grp_license_nonce'); ?>
                       <input type="hidden" name="grp_license_action" value="activate_free">
                       <button type="submit" class="button button-primary" style="width: 100%;">
                           <?php esc_html_e('Activate Free License', 'google-reviews-plugin'); ?>
                       </button>
                   </form>
               </div>
               
               <!-- Or Pro/Enterprise License -->
               <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                   <p style="margin: 0 0 10px 0; font-size: 13px; color: #666;">
                       <strong><?php esc_html_e('Or enter a Pro/Enterprise license key:', 'google-reviews-plugin'); ?></strong>
                   </p>
                   <form method="post" action="">
                       <?php wp_nonce_field('grp_license_nonce'); ?>
                       <input type="hidden" name="grp_license_action" value="activate">
                       <input type="text" name="grp_license_key" value="<?php echo esc_attr($license_key); ?>" placeholder="<?php esc_attr_e('Enter Pro/Enterprise license key', 'google-reviews-plugin'); ?>" class="regular-text" style="width: 100%; margin-bottom: 10px;" />
                       <button type="submit" class="button" style="width: 100%;">
                           <?php esc_html_e('Activate License', 'google-reviews-plugin'); ?>
                       </button>
                   </form>
                   <p style="margin-top: 10px; font-size: 12px;">
                       <a href="https://reactwoo.com/google-reviews-plugin-pro/" target="_blank">
                           <?php esc_html_e('Get a Pro/Enterprise license →', 'google-reviews-plugin'); ?>
                       </a>
                   </p>
               </div>
                <?php endif; ?>
            </div>
            
            <!-- Quick Start -->
            <div class="grp-sidebar-card">
                <h3><?php esc_html_e('Quick Start', 'google-reviews-plugin'); ?></h3>
                <ol>
                    <li><?php esc_html_e('Connect your Google Business account', 'google-reviews-plugin'); ?></li>
                    <li><?php esc_html_e('Select your business location', 'google-reviews-plugin'); ?></li>
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