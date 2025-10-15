<?php
/**
 * Uninstall script for Google Reviews Plugin
 *
 * @package Google_Reviews_Plugin
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Check if user has permission
if (!current_user_can('delete_plugins')) {
    exit;
}

// Include the uninstaller class
require_once plugin_dir_path(__FILE__) . 'includes/class-grp-uninstaller.php';

// Run uninstall
GRP_Uninstaller::uninstall();