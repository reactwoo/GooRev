<?php
/**
 * Plugin Name: ReactWoo Reviews
 * Plugin URI: https://reactwoo.com/reactwoo-reviews
 * Description: Display Google Business reviews on your WordPress site with beautiful widgets and shortcodes. Free and Pro versions available.
 * Version: 1.1.0
 * Author: ReactWoo Ltd
 * Author URI: https://reactwoo.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: google-reviews-plugin
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 6.7
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
define('GRP_PLUGIN_VERSION', '1.1.0');
define('GRP_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('REACTWOO_REVIEWS_SLUG', 'reactwoo-reviews');

// Provide a safe helper for inline JS translations (WP has no esc_js_e)
if (!function_exists('esc_js_e')) {
    /**
     * Echo a translated string escaped for JavaScript contexts.
     * Mirrors esc_html_e but uses esc_js under the hood.
     */
    function esc_js_e($text, $domain = 'default') {
        echo esc_js(__($text, $domain));
    }
}

/**
 * Structured debug logging helper.
 * Only logs if debug logging is enabled in settings.
 *
 * @param string $message The message to log
 * @param mixed  $data    Optional context (array/object preferred)
 * @param string $level   Log level: debug|info|warning|error
 * @return void
 */
if (!function_exists('grp_debug_log')) {
    function grp_debug_log($message, $data = null, $level = 'debug') {
        $debug_enabled = (bool) get_option('grp_enable_debug_logging', false);

        if (!$debug_enabled) {
            return;
        }

        if (!defined('WP_DEBUG_LOG') || !WP_DEBUG_LOG) {
            return;
        }

        $level = in_array($level, array('debug', 'info', 'warning', 'error'), true) ? $level : 'debug';
        $log_message = sprintf('[GRP %s] %s', strtoupper($level), $message);

        if ($data !== null) {
            if (is_array($data) || is_object($data)) {
                $encoded = wp_json_encode($data);
                $log_message .= ' | Data: ' . ($encoded !== false ? $encoded : print_r($data, true));
            } else {
                $log_message .= ' | Data: ' . print_r($data, true);
            }
        }

        error_log($log_message);
    }
}

// Check if we're in admin area
if (is_admin()) {
    require_once GRP_PLUGIN_DIR . 'includes/admin/class-grp-admin.php';
}

// Load the main plugin class
require_once GRP_PLUGIN_DIR . 'includes/class-google-reviews-plugin.php';

// Initialize the plugin
function grp_init() {
    Google_Reviews_Plugin::get_instance();

    if (class_exists('GRP_Updater')) {
        GRP_Updater::init();
    }
}
add_action('plugins_loaded', 'grp_init');

// Activation hook
register_activation_hook(__FILE__, 'grp_activate');
function grp_activate() {
    require_once GRP_PLUGIN_DIR . 'includes/class-grp-activator.php';
    GRP_Activator::activate();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'grp_deactivate');
function grp_deactivate() {
    require_once GRP_PLUGIN_DIR . 'includes/class-grp-deactivator.php';
    GRP_Deactivator::deactivate();
}

// Uninstall hook
register_uninstall_hook(__FILE__, 'grp_uninstall');
function grp_uninstall() {
    require_once GRP_PLUGIN_DIR . 'includes/class-grp-uninstaller.php';
    GRP_Uninstaller::uninstall();
}
