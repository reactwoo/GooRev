<?php
/**
 * Bootstrap for PHPUnit (WP stubs).
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

if (!defined('GRP_PLUGIN_VERSION')) {
    define('GRP_PLUGIN_VERSION', '1.1.0');
}
if (!defined('REACTWOO_REVIEWS_SLUG')) {
    define('REACTWOO_REVIEWS_SLUG', 'reactwoo-reviews');
}
if (!defined('GRP_PLUGIN_FILE')) {
    define('GRP_PLUGIN_FILE', dirname(__DIR__) . '/reactwoo-reviews.php');
}
if (!defined('GRP_PLUGIN_DIR')) {
    define('GRP_PLUGIN_DIR', dirname(__DIR__) . '/');
}
if (!defined('GRP_PLUGIN_URL')) {
    define('GRP_PLUGIN_URL', 'http://example.com/wp-content/plugins/reactwoo-reviews/');
}
if (!defined('GRP_PLUGIN_BASENAME')) {
    define('GRP_PLUGIN_BASENAME', 'reactwoo-reviews/reactwoo-reviews.php');
}

if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}
if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }
}
if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }
}
if (!function_exists('sanitize_key')) {
    function sanitize_key($key) {
        return strtolower(preg_replace('/[^a-z0-9_\-]/', '', (string) $key));
    }
}
if (!function_exists('untrailingslashit')) {
    function untrailingslashit($string) {
        return rtrim((string) $string, '/\\');
    }
}
if (!function_exists('trailingslashit')) {
    function trailingslashit($string) {
        return untrailingslashit($string) . '/';
    }
}
if (!function_exists('home_url')) {
    function home_url($path = '') {
        return 'https://example.com' . $path;
    }
}
if (!function_exists('wp_parse_url')) {
    function wp_parse_url($url, $component = -1) {
        return parse_url($url, $component);
    }
}
if (!function_exists('apply_filters')) {
    function apply_filters($tag, $value) {
        return $value;
    }
}
if (!function_exists('add_filter')) {
    function add_filter($tag, $callback, $priority = 10, $accepted_args = 1) {
        return true;
    }
}
if (!function_exists('plugin_basename')) {
    function plugin_basename($file) {
        return 'reactwoo-reviews/reactwoo-reviews.php';
    }
}

require_once dirname(__DIR__) . '/includes/class-grp-updater.php';
