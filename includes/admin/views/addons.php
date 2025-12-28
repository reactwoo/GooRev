<?php
/**
 * Addons Page View
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

// Handle addon enable/disable
if (isset($_POST['grp_addon_action']) && check_admin_referer('grp_addons_action')) {
    $addon_slug = sanitize_text_field($_POST['grp_addon_slug']);
    $action = sanitize_text_field($_POST['grp_addon_action']);
    
    $addons = GRP_Addons::get_instance();
    
    if ($action === 'enable') {
        $requirements = $addons->check_addon_requirements($addon_slug);
        if ($requirements['met']) {
            $addons->enable_addon($addon_slug);
            echo '<div class="notice notice-success"><p>' . esc_html__('Addon enabled successfully!', 'google-reviews-plugin') . '</p></div>';
        } else {
            echo '<div class="notice notice-error"><p><strong>' . esc_html__('Cannot enable addon:', 'google-reviews-plugin') . '</strong> ' . implode(', ', $requirements['messages']) . '</p></div>';
        }
    } elseif ($action === 'disable') {
        $addons->disable_addon($addon_slug);
        echo '<div class="notice notice-success"><p>' . esc_html__('Addon disabled successfully!', 'google-reviews-plugin') . '</p></div>';
    }
}

if (!class_exists('GRP_Addons')) {
    echo '<div class="notice notice-error"><p>' . esc_html__('Addons system is not available.', 'google-reviews-plugin') . '</p></div>';
    return;
}

$addons_manager = GRP_Addons::get_instance();
$all_addons = $addons_manager->get_addons();
$license = new GRP_License();
$is_pro = $license->is_pro();
?>

<div class="wrap">
    <h1><?php echo esc_html__('Addons', 'google-reviews-plugin'); ?></h1>
    <p class="description"><?php esc_html_e('Extend the Google Reviews Plugin with powerful addons. Enable or disable addons to customize your experience.', 'google-reviews-plugin'); ?></p>
    
    <div class="grp-addons-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; margin: 30px 0;">
        <?php foreach ($all_addons as $slug => $addon): ?>
            <?php
            $is_enabled = $addons_manager->is_addon_enabled($slug);
            $requirements = $addons_manager->check_addon_requirements($slug);
            $can_enable = $requirements['met'];
            ?>
            <div class="grp-addon-card" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; box-shadow: 0 1px 1px rgba(0,0,0,.04); position: relative; <?php echo $is_enabled ? 'border-left: 4px solid #00a32a;' : ''; ?>">
                
                <!-- Addon Header -->
                <div style="display: flex; align-items: flex-start; margin-bottom: 15px;">
                    <div style="font-size: 32px; margin-right: 15px; color: #2271b1;">
                        <span class="dashicons <?php echo esc_attr($addon['icon']); ?>"></span>
                    </div>
                    <div style="flex: 1;">
                        <h2 style="margin: 0 0 5px 0; font-size: 18px;">
                            <?php echo esc_html($addon['name']); ?>
                            <?php if ($is_enabled): ?>
                                <span class="grp-addon-badge-enabled" style="background: #00a32a; color: #fff; font-size: 11px; padding: 3px 8px; border-radius: 3px; margin-left: 8px; text-transform: uppercase; font-weight: 600;">
                                    <?php esc_html_e('Enabled', 'google-reviews-plugin'); ?>
                                </span>
                            <?php endif; ?>
                        </h2>
                        <?php 
                        $requires_enterprise = !empty($addon['requires_enterprise']) && $addon['requires_enterprise'];
                        $requires_pro = !empty($addon['requires_pro']) && $addon['requires_pro'];
                        if ($requires_enterprise): ?>
                            <span class="grp-addon-badge-enterprise" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: #fff; font-size: 11px; padding: 3px 8px; border-radius: 3px; text-transform: uppercase; font-weight: 600; display: inline-block; margin-top: 5px;">
                                <?php esc_html_e('Enterprise', 'google-reviews-plugin'); ?>
                            </span>
                        <?php elseif ($requires_pro): ?>
                            <span class="grp-addon-badge-pro" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; font-size: 11px; padding: 3px 8px; border-radius: 3px; text-transform: uppercase; font-weight: 600; display: inline-block; margin-top: 5px;">
                                <?php esc_html_e('Pro / Enterprise', 'google-reviews-plugin'); ?>
                            </span>
                        <?php else: ?>
                            <span class="grp-addon-badge-free" style="background: #2271b1; color: #fff; font-size: 11px; padding: 3px 8px; border-radius: 3px; text-transform: uppercase; font-weight: 600; display: inline-block; margin-top: 5px;">
                                <?php esc_html_e('Free', 'google-reviews-plugin'); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Addon Description -->
                <p style="color: #646970; margin-bottom: 20px; line-height: 1.6;">
                    <?php echo esc_html($addon['description']); ?>
                </p>
                
                <!-- Features List -->
                <?php if (!empty($addon['features'])): ?>
                    <div style="margin-bottom: 20px;">
                        <strong style="display: block; margin-bottom: 8px; font-size: 13px;"><?php esc_html_e('Features:', 'google-reviews-plugin'); ?></strong>
                        <ul style="margin: 0; padding-left: 20px; color: #50575e;">
                            <?php foreach ($addon['features'] as $feature): ?>
                                <li style="margin-bottom: 5px;"><?php echo esc_html($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <!-- Requirements Check -->
                <?php if (!$can_enable && !empty($requirements['messages'])): ?>
                    <div class="grp-addon-requirements" style="background: #fff3cd; border-left: 4px solid #ffb900; padding: 10px; margin-bottom: 15px; border-radius: 3px;">
                        <strong style="display: block; margin-bottom: 5px; color: #856404;"><?php esc_html_e('Requirements not met:', 'google-reviews-plugin'); ?></strong>
                        <ul style="margin: 0; padding-left: 20px; color: #856404;">
                            <?php foreach ($requirements['messages'] as $message): ?>
                                <li><?php echo esc_html($message); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div style="display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                    <?php if ($is_enabled): ?>
                        <form method="post" action="" style="margin: 0;">
                            <?php wp_nonce_field('grp_addons_action'); ?>
                            <input type="hidden" name="grp_addon_slug" value="<?php echo esc_attr($slug); ?>">
                            <input type="hidden" name="grp_addon_action" value="disable">
                            <button type="submit" class="button"><?php esc_html_e('Disable', 'google-reviews-plugin'); ?></button>
                        </form>
                        <?php if (!empty($addon['settings_page'])): ?>
                            <a href="<?php echo admin_url('admin.php?page=' . esc_attr($addon['settings_page'])); ?>" class="button button-primary">
                                <?php esc_html_e('Configure', 'google-reviews-plugin'); ?>
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <form method="post" action="" style="margin: 0;">
                            <?php wp_nonce_field('grp_addons_action'); ?>
                            <input type="hidden" name="grp_addon_slug" value="<?php echo esc_attr($slug); ?>">
                            <input type="hidden" name="grp_addon_action" value="enable">
                            <button type="submit" class="button button-primary" <?php echo $can_enable ? '' : 'disabled'; ?>>
                                <?php esc_html_e('Enable', 'google-reviews-plugin'); ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                
            </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (empty($all_addons)): ?>
        <div class="notice notice-info">
            <p><?php esc_html_e('No addons available at this time.', 'google-reviews-plugin'); ?></p>
        </div>
    <?php endif; ?>
</div>

<style>
@media (max-width: 768px) {
    .grp-addons-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

