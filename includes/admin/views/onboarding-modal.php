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
            <?php
            // Check if we're returning from OAuth and should show google_connect step
            $onboarding_step = isset($_GET['onboarding_step']) ? sanitize_text_field($_GET['onboarding_step']) : 'welcome';
            $show_welcome = ($onboarding_step === 'welcome');
            $show_google_connect = ($onboarding_step === 'google_connect');
            ?>
            <div class="grp-onboarding-step" data-step="welcome" style="display: <?php echo $show_welcome ? 'block' : 'none'; ?>;">
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
                        
                        <div class="grp-onboarding-field">
                            <label>
                                <input type="checkbox" id="grp-onboarding-has-license" name="has_license" value="1">
                                <?php esc_html_e('I already have a license key', 'google-reviews-plugin'); ?>
                            </label>
                        </div>
                        
                        <div class="grp-onboarding-field grp-onboarding-license-field" style="display: none;">
                            <label for="grp-onboarding-license-key">
                                <?php esc_html_e('License Key', 'google-reviews-plugin'); ?>
                            </label>
                            <input type="text" id="grp-onboarding-license-key" name="license_key" class="regular-text" placeholder="<?php esc_attr_e('Enter your license key', 'google-reviews-plugin'); ?>">
                            <p class="description">
                                <?php esc_html_e('Enter your Pro or Enterprise license key to activate premium features.', 'google-reviews-plugin'); ?>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Step 2: Google Connection -->
            <div class="grp-onboarding-step" data-step="google_connect" style="display: <?php echo $show_google_connect ? 'block' : 'none'; ?>;">
                <div class="grp-onboarding-step-content">
                    <h3><?php esc_html_e('Connect Your Google Account', 'google-reviews-plugin'); ?></h3>
                    <p><?php esc_html_e('Connect your Google Business Profile to fetch and display your reviews.', 'google-reviews-plugin'); ?></p>
                    
                    <div class="grp-onboarding-google-connect">
                        <?php
                        $api = new GRP_API();
                        $is_connected = $api->is_connected();
                        $auth_url = '';
                        $auth_error = '';
                        $error_code = '';
                        $is_404_error = false;
                        $is_503_error = false;
                        if (!$is_connected) {
                            $auth_url_result = $api->get_auth_url();
                            if (is_wp_error($auth_url_result)) {
                                $auth_error = $auth_url_result->get_error_message();
                                $error_code = $auth_url_result->get_error_code();
                                $is_404_error = (
                                    strpos($auth_error, '404') !== false ||
                                    strpos($auth_error, 'not found') !== false ||
                                    strpos($error_code, 'not_found') !== false ||
                                    strpos($error_code, 'endpoint_not_found') !== false ||
                                    strpos($error_code, 'oauth_endpoint_not_found') !== false
                                );
                                $is_503_error = (
                                    strpos($auth_error, '503') !== false ||
                                    strpos($auth_error, 'Service Unavailable') !== false ||
                                    strpos($error_code, 'service_unavailable') !== false
                                );
                                grp_debug_log('Failed to get auth URL in onboarding', array(
                                    'error' => $auth_error,
                                    'error_code' => $error_code,
                                    'is_404' => $is_404_error,
                                    'is_503' => $is_503_error
                                ));
                            } else {
                                $auth_url = $auth_url_result;
                            }
                        }
                        ?>
                        
                        <?php if ($is_connected): ?>
                            <div class="grp-onboarding-success">
                                <span class="dashicons dashicons-yes-alt"></span>
                                <p><?php esc_html_e('Google account is already connected!', 'google-reviews-plugin'); ?></p>
                            </div>
                        <?php elseif (!empty($auth_url)): ?>
                            <a href="<?php echo esc_url($auth_url); ?>" 
                               class="button button-primary button-large grp-connect-google-btn">
                                <span class="dashicons dashicons-google"></span>
                                <?php esc_html_e('Connect Google Account', 'google-reviews-plugin'); ?>
                            </a>
                            <p class="description">
                                <?php esc_html_e('This will redirect you to Google to authorize the connection. After authorization, you\'ll be redirected back to continue setup.', 'google-reviews-plugin'); ?>
                            </p>
                        <?php elseif ($is_503_error): ?>
                            <div class="notice notice-warning" style="border-left-color: #f56e28;">
                                <p>
                                    <strong><?php esc_html_e('Cloud Server Temporarily Unavailable', 'google-reviews-plugin'); ?></strong><br>
                                    <?php esc_html_e('The connection service is currently unavailable (Error 503). This is usually temporary.', 'google-reviews-plugin'); ?>
                                </p>
                            </div>
                            <div class="notice notice-info">
                                <p><strong><?php esc_html_e('What you can do:', 'google-reviews-plugin'); ?></strong></p>
                                <ul style="margin-left: 20px; margin-top: 10px;">
                                    <li><?php esc_html_e('Wait a few minutes and try again - the server may be restarting', 'google-reviews-plugin'); ?></li>
                                    <li><?php esc_html_e('Check back later - server maintenance may be in progress', 'google-reviews-plugin'); ?></li>
                                    <li><?php esc_html_e('Or skip this step and connect later from Settings', 'google-reviews-plugin'); ?></li>
                                </ul>
                            </div>
                            <p>
                                <button type="button" class="button button-primary grp-onboarding-skip" style="margin-left: 0;">
                                    <?php esc_html_e('Skip This Step', 'google-reviews-plugin'); ?>
                                </button>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=google-reviews-settings&skip_onboarding=1')); ?>" 
                                   class="button button-secondary" style="margin-left: 10px;">
                                    <?php esc_html_e('Go to Settings', 'google-reviews-plugin'); ?>
                                </a>
                            </p>
                        <?php elseif ($is_404_error): ?>
                            <div class="notice notice-error">
                                <p>
                                    <strong><?php esc_html_e('Cloud Server Configuration Issue', 'google-reviews-plugin'); ?></strong><br>
                                    <?php esc_html_e('Unable to generate connection URL. The OAuth endpoint was not found on the cloud server.', 'google-reviews-plugin'); ?>
                                </p>
                            </div>
                            <div class="notice notice-info">
                                <p><strong><?php esc_html_e('What you can do:', 'google-reviews-plugin'); ?></strong></p>
                                <ul style="margin-left: 20px; margin-top: 10px;">
                                    <li><?php esc_html_e('Try refreshing this page - the server may have been updated', 'google-reviews-plugin'); ?></li>
                                    <li><?php esc_html_e('Or skip this step and connect later from Settings', 'google-reviews-plugin'); ?></li>
                                </ul>
                            </div>
                            <p>
                                <button type="button" class="button button-primary grp-onboarding-skip" style="margin-left: 0;">
                                    <?php esc_html_e('Skip This Step', 'google-reviews-plugin'); ?>
                                </button>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=google-reviews-settings&skip_onboarding=1')); ?>" 
                                   class="button button-secondary" style="margin-left: 10px;">
                                    <?php esc_html_e('Go to Settings', 'google-reviews-plugin'); ?>
                                </a>
                            </p>
                        <?php else: ?>
                            <div class="notice notice-error">
                                <p>
                                    <?php esc_html_e('Unable to generate connection URL.', 'google-reviews-plugin'); ?>
                                    <?php if (!empty($auth_error)): ?>
                                        <br><strong><?php esc_html_e('Error:', 'google-reviews-plugin'); ?></strong> <?php echo esc_html($auth_error); ?>
                                    <?php endif; ?>
                                    <br><?php esc_html_e('This may be due to the cloud server being temporarily unavailable. You can:', 'google-reviews-plugin'); ?>
                                </p>
                                <ul style="margin-left: 20px; margin-top: 10px;">
                                    <li><?php esc_html_e('Try refreshing this page and clicking "Connect Google Account" again', 'google-reviews-plugin'); ?></li>
                                    <li><?php esc_html_e('Or skip this step and connect later from Settings', 'google-reviews-plugin'); ?></li>
                                </ul>
                            </div>
                        <?php endif; ?>
                            <p>
                                <button type="button" class="button button-primary grp-onboarding-skip" style="margin-left: 0;">
                                    <?php esc_html_e('Skip This Step', 'google-reviews-plugin'); ?>
                                </button>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=google-reviews-settings&skip_onboarding=1')); ?>" 
                                   class="button button-secondary" style="margin-left: 10px;">
                                    <?php esc_html_e('Go to Settings', 'google-reviews-plugin'); ?>
                                </a>
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

