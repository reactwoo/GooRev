<?php
/**
 * Onboarding modal template
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div id="grp-onboarding-modal" class="grp-onboarding-overlay" style="display: none;">
    <div class="grp-onboarding-modal">
        <div class="grp-onboarding-header">
            <h2><?php esc_html_e('Welcome to Google Reviews Plugin!', 'google-reviews-plugin'); ?></h2>
            <p class="grp-onboarding-subtitle"><?php esc_html_e('Let\'s get you set up in just a few steps', 'google-reviews-plugin'); ?></p>
        </div>
        
        <div class="grp-onboarding-content">
            <!-- Step 1: Welcome & Registration -->
            <div class="grp-onboarding-step" data-step="welcome" style="display: block;">
                <div class="grp-onboarding-step-content">
                    <h3><?php esc_html_e('Get Started', 'google-reviews-plugin'); ?></h3>
                    <p><?php esc_html_e('To activate your free license and get started, please provide your details (optional):', 'google-reviews-plugin'); ?></p>
                    
                    <form id="grp-onboarding-welcome-form">
                        <div class="grp-onboarding-field">
                            <label for="grp-onboarding-name">
                                <?php esc_html_e('Your Name', 'google-reviews-plugin'); ?>
                                <span class="optional"><?php esc_html_e('(optional)', 'google-reviews-plugin'); ?></span>
                            </label>
                            <input type="text" id="grp-onboarding-name" name="name" class="regular-text" placeholder="<?php esc_attr_e('John Doe', 'google-reviews-plugin'); ?>">
                        </div>
                        
                        <div class="grp-onboarding-field">
                            <label for="grp-onboarding-email">
                                <?php esc_html_e('Email Address', 'google-reviews-plugin'); ?>
                                <span class="optional"><?php esc_html_e('(optional)', 'google-reviews-plugin'); ?></span>
                            </label>
                            <input type="email" id="grp-onboarding-email" name="email" class="regular-text" placeholder="<?php esc_attr_e('you@example.com', 'google-reviews-plugin'); ?>">
                            <p class="description">
                                <?php esc_html_e('We\'ll use this to activate your free license and send you important updates. You can skip this step if you prefer.', 'google-reviews-plugin'); ?>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Step 2: Google Connection -->
            <div class="grp-onboarding-step" data-step="google_connect" style="display: none;">
                <div class="grp-onboarding-step-content">
                    <h3><?php esc_html_e('Connect Your Google Account', 'google-reviews-plugin'); ?></h3>
                    <p><?php esc_html_e('Connect your Google Business Profile to fetch and display your reviews.', 'google-reviews-plugin'); ?></p>
                    
                    <div class="grp-onboarding-google-connect">
                        <?php
                        $api = new GRP_API();
                        $is_connected = $api->is_connected();
                        ?>
                        
                        <?php if ($is_connected): ?>
                            <div class="grp-onboarding-success">
                                <span class="dashicons dashicons-yes-alt"></span>
                                <p><?php esc_html_e('Google account is already connected!', 'google-reviews-plugin'); ?></p>
                            </div>
                        <?php else: ?>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=google-reviews-settings&action=connect')); ?>" 
                               class="button button-primary button-large grp-connect-google-btn" 
                               target="_blank">
                                <span class="dashicons dashicons-google"></span>
                                <?php esc_html_e('Connect Google Account', 'google-reviews-plugin'); ?>
                            </a>
                            <p class="description">
                                <?php esc_html_e('This will open the settings page where you can connect your Google account. Once connected, return here to continue.', 'google-reviews-plugin'); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Step 3: Place ID -->
            <div class="grp-onboarding-step" data-step="place_id" style="display: none;">
                <div class="grp-onboarding-step-content">
                    <h3><?php esc_html_e('Place ID (Optional)', 'google-reviews-plugin'); ?></h3>
                    <p><?php esc_html_e('Place ID is only needed if you want to send review invite links to customers. You can skip this if you only want to display existing reviews.', 'google-reviews-plugin'); ?></p>
                    
                    <div class="grp-onboarding-place-id-info">
                        <div class="grp-onboarding-info-box">
                            <h4><?php esc_html_e('Why is Place ID needed?', 'google-reviews-plugin'); ?></h4>
                            <p><?php esc_html_e('Place ID is required to generate "Leave a Review" links that take customers directly to your Google Business Profile review page. This is only needed for the WooCommerce review invite feature.', 'google-reviews-plugin'); ?></p>
                            <p><strong><?php esc_html_e('Note:', 'google-reviews-plugin'); ?></strong> <?php esc_html_e('Reviews are fetched automatically using your Business Profile location. Place ID is only for generating invite links.', 'google-reviews-plugin'); ?></p>
                        </div>
                        
                        <div class="grp-onboarding-field">
                            <label for="grp-onboarding-place-id">
                                <?php esc_html_e('Place ID', 'google-reviews-plugin'); ?>
                                <span class="optional"><?php esc_html_e('(optional)', 'google-reviews-plugin'); ?></span>
                            </label>
                            <input type="text" 
                                   id="grp-onboarding-place-id" 
                                   name="place_id" 
                                   class="regular-text" 
                                   placeholder="ChIJN1t_tDeuEmsRUsoyG83frY4"
                                   pattern="[A-Za-z0-9_-]+">
                            <p class="description">
                                <?php esc_html_e('Leave this blank if you don\'t plan to send review invites. You can add it later in WooCommerce settings.', 'google-reviews-plugin'); ?>
                                <br>
                                <a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank">
                                    <?php esc_html_e('How to find your Place ID', 'google-reviews-plugin'); ?>
                                </a>
                            </p>
                        </div>
                        
                        <div class="grp-onboarding-visual-guide">
                            <h4><?php esc_html_e('How to find your Place ID:', 'google-reviews-plugin'); ?></h4>
                            <ol>
                                <li><?php esc_html_e('Go to', 'google-reviews-plugin'); ?> <a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank"><?php esc_html_e('Google\'s Place ID finder', 'google-reviews-plugin'); ?></a></li>
                                <li><?php esc_html_e('Search for your business name or address', 'google-reviews-plugin'); ?></li>
                                <li><?php esc_html_e('Copy the Place ID (starts with "ChIJ...")', 'google-reviews-plugin'); ?></li>
                                <li><?php esc_html_e('Paste it in the field above', 'google-reviews-plugin'); ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grp-onboarding-footer">
            <button type="button" class="button grp-onboarding-skip"><?php esc_html_e('Skip Setup', 'google-reviews-plugin'); ?></button>
            <div class="grp-onboarding-nav">
                <button type="button" class="button grp-onboarding-back" style="display: none;"><?php esc_html_e('Back', 'google-reviews-plugin'); ?></button>
                <button type="button" class="button button-primary grp-onboarding-next"><?php esc_html_e('Next', 'google-reviews-plugin'); ?></button>
            </div>
        </div>
    </div>
</div>

