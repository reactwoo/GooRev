<?php
/**
 * Help view
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap grp-help-page">
    <h1><?php esc_html_e('Help & Documentation', 'google-reviews-plugin'); ?></h1>
    
    <div class="grp-help-container">
        <div class="grp-help-main">
            <!-- Quick Start -->
            <div class="grp-help-section">
                <h2><?php esc_html_e('Quick Start Guide', 'google-reviews-plugin'); ?></h2>
                <div class="grp-help-content">
                    <ol>
                        <li>
                            <strong><?php esc_html_e('Configure Google API', 'google-reviews-plugin'); ?></strong>
                            <p><?php esc_html_e('Go to Settings and enter your Google OAuth 2.0 credentials.', 'google-reviews-plugin'); ?></p>
                        </li>
                        <li>
                            <strong><?php esc_html_e('Connect Your Account', 'google-reviews-plugin'); ?></strong>
                            <p><?php esc_html_e('Click the "Connect Account" button to authorize the plugin.', 'google-reviews-plugin'); ?></p>
                        </li>
                        <li>
                            <strong><?php esc_html_e('Sync Reviews', 'google-reviews-plugin'); ?></strong>
                            <p><?php esc_html_e('Click "Sync Reviews" to import your Google Business reviews.', 'google-reviews-plugin'); ?></p>
                        </li>
                        <li>
                            <strong><?php esc_html_e('Display Reviews', 'google-reviews-plugin'); ?></strong>
                            <p><?php esc_html_e('Use shortcodes, widgets, or page builders to display reviews on your site.', 'google-reviews-plugin'); ?></p>
                        </li>
                    </ol>
                </div>
            </div>
            
            <!-- Shortcodes -->
            <div class="grp-help-section">
                <h2><?php esc_html_e('Shortcodes', 'google-reviews-plugin'); ?></h2>
                <div class="grp-help-content">
                    <p><?php esc_html_e('Use these shortcodes to display reviews anywhere on your site:', 'google-reviews-plugin'); ?></p>
                    
                    <h3><?php esc_html_e('Basic Shortcode', 'google-reviews-plugin'); ?></h3>
                    <div class="grp-code-block">
                        <code>[google_reviews]</code>
                    </div>
                    
                    <h3><?php esc_html_e('Advanced Shortcode', 'google-reviews-plugin'); ?></h3>
                    <div class="grp-code-block">
                        <code>[google_reviews style="modern" layout="carousel" count="5" min_rating="4"]</code>
                    </div>
                    
                    <h3><?php esc_html_e('Available Parameters', 'google-reviews-plugin'); ?></h3>
                    <div class="grp-parameters-table">
                        <table>
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('Parameter', 'google-reviews-plugin'); ?></th>
                                    <th><?php esc_html_e('Description', 'google-reviews-plugin'); ?></th>
                                    <th><?php esc_html_e('Default', 'google-reviews-plugin'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>style</code></td>
                                    <td><?php esc_html_e('Review display style', 'google-reviews-plugin'); ?></td>
                                    <td>modern</td>
                                </tr>
                                <tr>
                                    <td><code>layout</code></td>
                                    <td><?php esc_html_e('Display layout (carousel or list)', 'google-reviews-plugin'); ?></td>
                                    <td>carousel</td>
                                </tr>
                                <tr>
                                    <td><code>count</code></td>
                                    <td><?php esc_html_e('Number of reviews to display', 'google-reviews-plugin'); ?></td>
                                    <td>5</td>
                                </tr>
                                <tr>
                                    <td><code>min_rating</code></td>
                                    <td><?php esc_html_e('Minimum star rating to display', 'google-reviews-plugin'); ?></td>
                                    <td>1</td>
                                </tr>
                                <tr>
                                    <td><code>max_rating</code></td>
                                    <td><?php esc_html_e('Maximum star rating to display', 'google-reviews-plugin'); ?></td>
                                    <td>5</td>
                                </tr>
                                <tr>
                                    <td><code>sort_by</code></td>
                                    <td><?php esc_html_e('Sort reviews by (newest, oldest, highest_rating, lowest_rating)', 'google-reviews-plugin'); ?></td>
                                    <td>newest</td>
                                </tr>
                                <tr>
                                    <td><code>show_avatar</code></td>
                                    <td><?php esc_html_e('Show reviewer avatar (true/false)', 'google-reviews-plugin'); ?></td>
                                    <td>true</td>
                                </tr>
                                <tr>
                                    <td><code>show_date</code></td>
                                    <td><?php esc_html_e('Show review date (true/false)', 'google-reviews-plugin'); ?></td>
                                    <td>true</td>
                                </tr>
                                <tr>
                                    <td><code>show_rating</code></td>
                                    <td><?php esc_html_e('Show star rating (true/false)', 'google-reviews-plugin'); ?></td>
                                    <td>true</td>
                                </tr>
                                <tr>
                                    <td><code>show_reply</code></td>
                                    <td><?php esc_html_e('Show business replies (true/false)', 'google-reviews-plugin'); ?></td>
                                    <td>true</td>
                                </tr>
                                <tr>
                                    <td><code>autoplay</code></td>
                                    <td><?php esc_html_e('Enable carousel autoplay (true/false)', 'google-reviews-plugin'); ?></td>
                                    <td>true</td>
                                </tr>
                                <tr>
                                    <td><code>speed</code></td>
                                    <td><?php esc_html_e('Carousel speed in milliseconds', 'google-reviews-plugin'); ?></td>
                                    <td>5000</td>
                                </tr>
                                <tr>
                                    <td><code>dots</code></td>
                                    <td><?php esc_html_e('Show carousel dots (true/false)', 'google-reviews-plugin'); ?></td>
                                    <td>true</td>
                                </tr>
                                <tr>
                                    <td><code>arrows</code></td>
                                    <td><?php esc_html_e('Show carousel arrows (true/false)', 'google-reviews-plugin'); ?></td>
                                    <td>true</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Widgets -->
            <div class="grp-help-section">
                <h2><?php esc_html_e('Widgets', 'google-reviews-plugin'); ?></h2>
                <div class="grp-help-content">
                    <p><?php esc_html_e('Add the Google Reviews widget to your sidebar or widget areas:', 'google-reviews-plugin'); ?></p>
                    <ol>
                        <li><?php esc_html_e('Go to Appearance > Widgets', 'google-reviews-plugin'); ?></li>
                        <li><?php esc_html_e('Find the "Google Reviews" widget', 'google-reviews-plugin'); ?></li>
                        <li><?php esc_html_e('Drag it to your desired widget area', 'google-reviews-plugin'); ?></li>
                        <li><?php esc_html_e('Configure the display options', 'google-reviews-plugin'); ?></li>
                        <li><?php esc_html_e('Save the widget', 'google-reviews-plugin'); ?></li>
                    </ol>
                </div>
            </div>
            
            <!-- Page Builders -->
            <div class="grp-help-section">
                <h2><?php esc_html_e('Page Builders', 'google-reviews-plugin'); ?></h2>
                <div class="grp-help-content">
                    <h3><?php esc_html_e('Elementor', 'google-reviews-plugin'); ?></h3>
                    <ol>
                        <li><?php esc_html_e('Edit a page with Elementor', 'google-reviews-plugin'); ?></li>
                        <li><?php esc_html_e('Search for "Google Reviews" in the widget panel', 'google-reviews-plugin'); ?></li>
                        <li><?php esc_html_e('Drag the widget to your page', 'google-reviews-plugin'); ?></li>
                        <li><?php esc_html_e('Configure the settings in the widget panel', 'google-reviews-plugin'); ?></li>
                    </ol>
                    
                    <h3><?php esc_html_e('Gutenberg', 'google-reviews-plugin'); ?></h3>
                    <ol>
                        <li><?php esc_html_e('Edit a page or post', 'google-reviews-plugin'); ?></li>
                        <li><?php esc_html_e('Click the "+" button to add a block', 'google-reviews-plugin'); ?></li>
                        <li><?php esc_html_e('Search for "Google Reviews"', 'google-reviews-plugin'); ?></li>
                        <li><?php esc_html_e('Add the block and configure the settings', 'google-reviews-plugin'); ?></li>
                    </ol>
                </div>
            </div>
            
            <!-- Troubleshooting -->
            <div class="grp-help-section">
                <h2><?php esc_html_e('Troubleshooting', 'google-reviews-plugin'); ?></h2>
                <div class="grp-help-content">
                    <h3><?php esc_html_e('Common Issues', 'google-reviews-plugin'); ?></h3>
                    
                    <div class="grp-faq-item">
                        <h4><?php esc_html_e('Reviews are not displaying', 'google-reviews-plugin'); ?></h4>
                        <ul>
                            <li><?php esc_html_e('Check if your Google API credentials are correct', 'google-reviews-plugin'); ?></li>
                            <li><?php esc_html_e('Ensure your account is connected', 'google-reviews-plugin'); ?></li>
                            <li><?php esc_html_e('Try syncing reviews manually', 'google-reviews-plugin'); ?></li>
                            <li><?php esc_html_e('Check if there are reviews available for your business', 'google-reviews-plugin'); ?></li>
                        </ul>
                    </div>
                    
                    <div class="grp-faq-item">
                        <h4><?php esc_html_e('Connection failed', 'google-reviews-plugin'); ?></h4>
                        <ul>
                            <li><?php esc_html_e('Verify your Client ID and Client Secret', 'google-reviews-plugin'); ?></li>
                            <li><?php esc_html_e('Check if the redirect URI is correct', 'google-reviews-plugin'); ?></li>
                            <li><?php esc_html_e('Ensure the Google My Business API is enabled', 'google-reviews-plugin'); ?></li>
                            <li><?php esc_html_e('Check your internet connection', 'google-reviews-plugin'); ?></li>
                        </ul>
                    </div>
                    
                    <div class="grp-faq-item">
                        <h4><?php esc_html_e('Styling issues', 'google-reviews-plugin'); ?></h4>
                        <ul>
                            <li><?php esc_html_e('Check if your theme CSS is conflicting', 'google-reviews-plugin'); ?></li>
                            <li><?php esc_html_e('Try using custom CSS to override styles', 'google-reviews-plugin'); ?></li>
                            <li><?php esc_html_e('Clear any caching plugins', 'google-reviews-plugin'); ?></li>
                            <li><?php esc_html_e('Check browser developer tools for errors', 'google-reviews-plugin'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grp-help-sidebar">
            <!-- Support -->
            <div class="grp-sidebar-card">
                <h3><?php esc_html_e('Need Help?', 'google-reviews-plugin'); ?></h3>
                <p><?php esc_html_e('Get support from our team:', 'google-reviews-plugin'); ?></p>
                <ul>
                    <li><a href="mailto:support@reactwoo.com"><?php esc_html_e('Email Support', 'google-reviews-plugin'); ?></a></li>
                    <li><a href="https://reactwoo.com/support" target="_blank"><?php esc_html_e('Support Portal', 'google-reviews-plugin'); ?></a></li>
                    <li><a href="https://reactwoo.com/docs" target="_blank"><?php esc_html_e('Documentation', 'google-reviews-plugin'); ?></a></li>
                </ul>
            </div>
            
            <!-- Pro Features -->
            <?php if (!$is_pro): ?>
            <div class="grp-sidebar-card grp-pro-card">
                <h3><?php esc_html_e('Upgrade to Pro', 'google-reviews-plugin'); ?></h3>
                <p><?php esc_html_e('Unlock advanced features:', 'google-reviews-plugin'); ?></p>
                <ul>
                    <li>✓ <?php esc_html_e('Multiple Locations', 'google-reviews-plugin'); ?></li>
                    <li>✓ <?php esc_html_e('Product Integration', 'google-reviews-plugin'); ?></li>
                    <li>✓ <?php esc_html_e('Advanced Customization', 'google-reviews-plugin'); ?></li>
                    <li>✓ <?php esc_html_e('Analytics Dashboard', 'google-reviews-plugin'); ?></li>
                    <li>✓ <?php esc_html_e('Priority Support', 'google-reviews-plugin'); ?></li>
                </ul>
                <a href="https://reactwoo.com/google-reviews-plugin-pro/" class="button button-primary" target="_blank">
                    <?php esc_html_e('Upgrade Now', 'google-reviews-plugin'); ?>
                </a>
            </div>
            <?php endif; ?>
            
            <!-- Resources -->
            <div class="grp-sidebar-card">
                <h3><?php esc_html_e('Resources', 'google-reviews-plugin'); ?></h3>
                <ul>
                    <li><a href="https://developers.google.com/my-business" target="_blank"><?php esc_html_e('Google My Business API', 'google-reviews-plugin'); ?></a></li>
                    <li><a href="https://reactwoo.com/blog" target="_blank"><?php esc_html_e('Blog & Tutorials', 'google-reviews-plugin'); ?></a></li>
                    <li><a href="https://reactwoo.com/changelog" target="_blank"><?php esc_html_e('Changelog', 'google-reviews-plugin'); ?></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.grp-help-page {
    max-width: 1200px;
}

.grp-help-container {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 30px;
    margin-top: 20px;
}

.grp-help-main {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 30px;
}

.grp-help-section {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid #eee;
}

.grp-help-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.grp-help-section h2 {
    color: #23282d;
    margin-top: 0;
    margin-bottom: 20px;
    border-bottom: none;
    padding-bottom: 0;
}

.grp-help-section h3 {
    color: #23282d;
    margin-top: 25px;
    margin-bottom: 15px;
    border-bottom: none;
    padding-bottom: 0;
}

.grp-help-section h4 {
    color: #23282d;
    margin-top: 20px;
    margin-bottom: 10px;
    border-bottom: none;
    padding-bottom: 0;
}

.grp-help-content ol {
    padding-left: 20px;
}

.grp-help-content li {
    margin-bottom: 10px;
}

.grp-code-block {
    background: #f1f1f1;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin: 15px 0;
    font-family: monospace;
    overflow-x: auto;
}

.grp-code-block code {
    background: none;
    padding: 0;
    font-size: 14px;
}

.grp-parameters-table {
    overflow-x: auto;
    margin: 20px 0;
}

.grp-parameters-table table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.grp-parameters-table th,
.grp-parameters-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.grp-parameters-table th {
    background: #f9f9f9;
    font-weight: 600;
    color: #23282d;
}

.grp-parameters-table code {
    background: #f1f1f1;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
    font-size: 12px;
}

.grp-faq-item {
    margin-bottom: 25px;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 4px;
    border-left: 4px solid #007cba;
}

.grp-faq-item h4 {
    margin-top: 0;
    color: #23282d;
}

.grp-faq-item ul {
    margin: 10px 0;
    padding-left: 20px;
}

.grp-help-sidebar {
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
    border-bottom: none;
    padding-bottom: 0;
}

.grp-sidebar-card ul {
    list-style: none;
    padding: 0;
    margin: 15px 0;
}

.grp-sidebar-card li {
    padding: 5px 0;
}

.grp-sidebar-card a {
    color: #0073aa;
    text-decoration: none;
}

.grp-sidebar-card a:hover {
    text-decoration: underline;
}

.grp-pro-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.grp-pro-card h3,
.grp-pro-card p,
.grp-pro-card li,
.grp-pro-card a {
    color: white;
}

.grp-pro-card a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .grp-help-container {
        grid-template-columns: 1fr;
    }
    
    .grp-help-main {
        padding: 20px;
    }
    
    .grp-parameters-table {
        font-size: 12px;
    }
}
</style>