<?php
/**
 * Plugin Name: Google Reviews Plugin
 * Plugin URI: https://reactwoo.com/google-reviews-plugin
 * Description: Display Google Business reviews on your WordPress site with beautiful widgets and shortcodes. Free and Pro versions available.
 * Version: 1.0.0
 * Author: ReactWoo Ltd
 * Author URI: https://reactwoo.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: google-reviews-plugin
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GRP_PLUGIN_FILE', __FILE__);
define('GRP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GRP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GRP_PLUGIN_VERSION', '1.0.0');
define('GRP_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Check if we're in admin area
if (is_admin()) {
    require_once GRP_PLUGIN_DIR . 'includes/admin/class-grp-admin.php';
}

// Load the main plugin class
require_once GRP_PLUGIN_DIR . 'includes/class-google-reviews-plugin.php';

// Initialize the plugin
function grp_init() {
    // Use singleton accessor; constructor remains private
    Google_Reviews_Plugin::get_instance();
}
add_action('plugins_loaded', 'grp_init');

// Activation hook
register_activation_hook(__FILE__, 'grp_activate');
function grp_activate() {
    // Create necessary database tables
    require_once GRP_PLUGIN_DIR . 'includes/class-grp-activator.php';
    GRP_Activator::activate();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'grp_deactivate');
function grp_deactivate() {
    // Clean up if needed
    require_once GRP_PLUGIN_DIR . 'includes/class-grp-deactivator.php';
    GRP_Deactivator::deactivate();
}

// Uninstall hook
register_uninstall_hook(__FILE__, 'grp_uninstall');
function grp_uninstall() {
    // Remove all plugin data
    require_once GRP_PLUGIN_DIR . 'includes/class-grp-uninstaller.php';
    GRP_Uninstaller::uninstall();
}