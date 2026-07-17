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
     * Minimum Elementor version for modern registration APIs.
     */
    const MIN_ELEMENTOR_VERSION = '3.5.0';

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
        add_action('elementor/widgets/register', array($this, 'register_widgets'));
        add_action('elementor/elements/categories_registered', array($this, 'add_widget_categories'));
        add_action('elementor/frontend/after_enqueue_styles', array($this, 'enqueue_styles'));
    }

    /**
     * Whether Elementor meets the minimum version.
     *
     * @return bool
     */
    private function is_elementor_compatible() {
        if (!defined('ELEMENTOR_VERSION')) {
            return class_exists('\\Elementor\\Plugin');
        }
        return version_compare(ELEMENTOR_VERSION, self::MIN_ELEMENTOR_VERSION, '>=');
    }

    /**
     * Register Elementor widgets (Elementor 3.5+ / 4.x API).
     *
     * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
     */
    public function register_widgets($widgets_manager = null) {
        if (!class_exists('Elementor\\Widget_Base')) {
            return;
        }

        if (!$this->is_elementor_compatible()) {
            return;
        }

        require_once GRP_PLUGIN_DIR . 'includes/frontend/elementor/class-grp-elementor-widget.php';

        if ($widgets_manager && method_exists($widgets_manager, 'register')) {
            $widgets_manager->register(new GRP_Elementor_Widget());
        } else {
            \Elementor\Plugin::instance()->widgets_manager->register(new GRP_Elementor_Widget());
        }

        $addons = GRP_Addons::get_instance();
        if ($addons->is_addon_enabled('review-widgets')) {
            require_once GRP_PLUGIN_DIR . 'includes/frontend/elementor/class-grp-elementor-review-button-widget.php';
            $button = new GRP_Elementor_Review_Button_Widget();
            if ($widgets_manager && method_exists($widgets_manager, 'register')) {
                $widgets_manager->register($button);
            } else {
                \Elementor\Plugin::instance()->widgets_manager->register($button);
            }
        }
    }

    /**
     * Add widget categories
     *
     * @param \Elementor\Elements_Manager $elements_manager Elements manager.
     */
    public function add_widget_categories($elements_manager) {
        $elements_manager->add_category(
            'google-reviews',
            array(
                'title' => __('Google Reviews', 'google-reviews-plugin'),
                'icon'  => 'eicon-star',
            )
        );
    }

    /**
     * Register Elementor frontend stylesheet handle (also used by get_style_depends).
     */
    public function enqueue_styles() {
        wp_register_style(
            'grp-elementor',
            GRP_PLUGIN_URL . 'assets/css/elementor.css',
            array('grp-frontend'),
            GRP_PLUGIN_VERSION
        );
        wp_enqueue_style('grp-elementor');
    }
}
