<?php
/**
 * Frontend functionality
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Frontend {
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Enqueue frontend scripts
     */
    public function enqueue_scripts() {
        // Only load on pages that have reviews
        if (!$this->page_has_reviews()) {
            return;
        }
        
        wp_enqueue_style(
            'grp-frontend',
            GRP_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            GRP_PLUGIN_VERSION
        );
        
        wp_enqueue_script(
            'grp-frontend',
            GRP_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            GRP_PLUGIN_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('grp-frontend', 'grp_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('grp_nonce'),
            'strings' => array(
                'loading' => __('Loading...', 'google-reviews-plugin'),
                'error' => __('An error occurred. Please try again.', 'google-reviews-plugin'),
            )
        ));

        // Attach custom CSS/JS using WP inline helpers (avoid echoing in head/footer)
        $custom_css = get_option('grp_custom_css', '');
        if (!empty($custom_css)) {
            wp_add_inline_style('grp-frontend', $custom_css);
        }

        $custom_js = get_option('grp_custom_js', '');
        if (!empty($custom_js)) {
            wp_add_inline_script('grp-frontend', $custom_js);
        }
    }
    
    /**
     * Check if page has reviews
     */
    private function page_has_reviews() {
        global $post;
        
        if (!$post) {
            return false;
        }
        
        // Check if post content has shortcodes
        if (has_shortcode($post->post_content, 'google_reviews') || 
            has_shortcode($post->post_content, 'grp_reviews')) {
            return true;
        }
        
        // Check if widgets are active
        if (is_active_widget(false, false, 'grp_widget')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Output custom CSS
     */
    public function output_custom_css() {}
    
    /**
     * Output custom JavaScript
     */
    public function output_custom_js() {}
}