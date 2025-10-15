<?php
/**
 * Gutenberg integration
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Gutenberg {
    
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
        add_action('init', array($this, 'register_blocks'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
        add_action('enqueue_block_assets', array($this, 'enqueue_block_assets'));
    }
    
    /**
     * Register Gutenberg blocks
     */
    public function register_blocks() {
        if (!function_exists('register_block_type')) {
            return;
        }
        
        register_block_type('google-reviews/reviews', array(
            'editor_script' => 'grp-gutenberg-block',
            'editor_style' => 'grp-gutenberg-block-editor',
            'style' => 'grp-gutenberg-block',
            'render_callback' => array($this, 'render_reviews_block'),
            'attributes' => array(
                'style' => array(
                    'type' => 'string',
                    'default' => 'modern',
                ),
                'layout' => array(
                    'type' => 'string',
                    'default' => 'carousel',
                ),
                'count' => array(
                    'type' => 'number',
                    'default' => 5,
                ),
                'min_rating' => array(
                    'type' => 'number',
                    'default' => 1,
                ),
                'max_rating' => array(
                    'type' => 'number',
                    'default' => 5,
                ),
                'sort_by' => array(
                    'type' => 'string',
                    'default' => 'newest',
                ),
                'show_avatar' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'show_date' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'show_rating' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'show_reply' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'autoplay' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'speed' => array(
                    'type' => 'number',
                    'default' => 5000,
                ),
                'dots' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'arrows' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
            ),
        ));
    }
    
    /**
     * Enqueue block editor assets
     */
    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'grp-gutenberg-block',
            GRP_PLUGIN_URL . 'assets/js/gutenberg-block.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
            GRP_PLUGIN_VERSION,
            true
        );
        
        wp_enqueue_style(
            'grp-gutenberg-block-editor',
            GRP_PLUGIN_URL . 'assets/css/gutenberg-block-editor.css',
            array('wp-edit-blocks'),
            GRP_PLUGIN_VERSION
        );
        
        // Localize script
        wp_localize_script('grp-gutenberg-block', 'grp_gutenberg', array(
            'styles' => $this->get_style_options(),
            'strings' => array(
                'block_title' => __('Google Reviews', 'google-reviews-plugin'),
                'block_description' => __('Display Google Business reviews', 'google-reviews-plugin'),
            )
        ));
    }
    
    /**
     * Enqueue block assets
     */
    public function enqueue_block_assets() {
        wp_enqueue_style(
            'grp-gutenberg-block',
            GRP_PLUGIN_URL . 'assets/css/gutenberg-block.css',
            array(),
            GRP_PLUGIN_VERSION
        );
    }
    
    /**
     * Render reviews block
     */
    public function render_reviews_block($attributes) {
        $shortcode_atts = array(
            'style' => $attributes['style'],
            'layout' => $attributes['layout'],
            'count' => $attributes['count'],
            'min_rating' => $attributes['min_rating'],
            'max_rating' => $attributes['max_rating'],
            'sort_by' => $attributes['sort_by'],
            'show_avatar' => $attributes['show_avatar'] ? 'true' : 'false',
            'show_date' => $attributes['show_date'] ? 'true' : 'false',
            'show_rating' => $attributes['show_rating'] ? 'true' : 'false',
            'show_reply' => $attributes['show_reply'] ? 'true' : 'false',
            'autoplay' => $attributes['autoplay'] ? 'true' : 'false',
            'speed' => $attributes['speed'],
            'dots' => $attributes['dots'] ? 'true' : 'false',
            'arrows' => $attributes['arrows'] ? 'true' : 'false',
            'class' => 'grp-gutenberg-block'
        );
        
        $shortcode = new GRP_Shortcode();
        return $shortcode->render_shortcode($shortcode_atts);
    }
    
    /**
     * Get style options
     */
    private function get_style_options() {
        $styles = new GRP_Styles();
        $available_styles = $styles->get_styles();
        
        $options = array();
        foreach ($available_styles as $key => $style) {
            $options[] = array(
                'value' => $key,
                'label' => $style['name'],
                'description' => $style['description']
            );
        }
        
        return $options;
    }
}