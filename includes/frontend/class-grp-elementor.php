<?php
/**
 * Elementor integration
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Elementor {
    
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
        add_action('elementor/widgets/widgets_registered', array($this, 'register_widgets'));
        add_action('elementor/elements/categories_registered', array($this, 'add_widget_categories'));
        add_action('elementor/frontend/after_enqueue_styles', array($this, 'enqueue_styles'));
    }
    
    /**
     * Register Elementor widgets
     */
    public function register_widgets() {
        if (!class_exists('Elementor\Widget_Base')) {
            return;
        }
        
        require_once GRP_PLUGIN_DIR . 'includes/frontend/elementor/class-grp-elementor-widget.php';
        
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new GRP_Elementor_Widget());
        
        // Register Review Button widget if addon is enabled
        $addons = GRP_Addons::get_instance();
        if ($addons->is_addon_enabled('review-widgets')) {
            require_once GRP_PLUGIN_DIR . 'includes/frontend/elementor/class-grp-elementor-review-button-widget.php';
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new GRP_Elementor_Review_Button_Widget());
        }
    }
    
    /**
     * Add widget categories
     */
    public function add_widget_categories($elements_manager) {
        $elements_manager->add_category(
            'google-reviews',
            array(
                'title' => __('Google Reviews', 'google-reviews-plugin'),
                'icon' => 'fa fa-star',
            )
        );
    }
    
    /**
     * Enqueue Elementor styles
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'grp-elementor',
            GRP_PLUGIN_URL . 'assets/css/elementor.css',
            array(),
            GRP_PLUGIN_VERSION
        );
    }
}