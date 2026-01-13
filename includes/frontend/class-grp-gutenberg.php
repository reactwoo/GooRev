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
                'theme' => array(
                    'type' => 'string',
                    'default' => 'light',
                ),
                'layout' => array(
                    'type' => 'string',
                    'default' => 'carousel',
                ),
                'cols_desktop' => array(
                    'type' => 'number',
                    'default' => 3,
                ),
                'cols_tablet' => array(
                    'type' => 'number',
                    'default' => 2,
                ),
                'cols_mobile' => array(
                    'type' => 'number',
                    'default' => 1,
                ),
                'gap' => array(
                    'type' => 'number',
                    'default' => 20,
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
                'theme' => array(
                    'type' => 'string',
                    'default' => 'light',
                ),
                'cols_desktop' => array(
                    'type' => 'number',
                    'default' => 3,
                ),
                'cols_tablet' => array(
                    'type' => 'number',
                    'default' => 2,
                ),
                'cols_mobile' => array(
                    'type' => 'number',
                    'default' => 1,
                ),
                'gap' => array(
                    'type' => 'number',
                    'default' => 20,
                ),
                'custom_text_color' => array(
                    'type' => 'string',
                ),
                'custom_background_color' => array(
                    'type' => 'string',
                ),
                'custom_border_color' => array(
                    'type' => 'string',
                ),
                'custom_accent_color' => array(
                    'type' => 'string',
                ),
                'custom_star_color' => array(
                    'type' => 'string',
                ),
                'custom_font_size' => array(
                    'type' => 'number',
                ),
                'custom_name_font_size' => array(
                    'type' => 'number',
                ),
                'body_font_family' => array(
                    'type' => 'string',
                ),
                'name_font_family' => array(
                    'type' => 'string',
                ),
            ),
        ));
        
        // Register Review Button block if addon is enabled
        $addons = GRP_Addons::get_instance();
        if ($addons->is_addon_enabled('review-widgets')) {
            register_block_type('google-reviews/review-button', array(
                'editor_script' => 'grp-gutenberg-block',
                'editor_style' => 'grp-gutenberg-block-editor',
                'style' => 'grp-review-widgets',
                'render_callback' => array($this, 'render_review_button_block'),
                'attributes' => array(
                    'button_text' => array(
                        'type' => 'string',
                        'default' => __('Leave us a review', 'google-reviews-plugin'),
                    ),
                    'button_style' => array(
                        'type' => 'string',
                        'default' => 'default',
                    ),
                    'button_size' => array(
                        'type' => 'string',
                        'default' => 'medium',
                    ),
                    'align' => array(
                        'type' => 'string',
                        'default' => 'left',
                    ),
                    'text_color' => array(
                        'type' => 'string',
                    ),
                    'background_color' => array(
                        'type' => 'string',
                    ),
                ),
            ));
        }
    }
    
    /**
     * Render review button block
     */
    public function render_review_button_block($attributes) {
        // Ensure attributes are set with defaults
        if (empty($attributes)) {
            $attributes = array();
        }
        
        // Check if Review Widgets addon is enabled
        $addons = GRP_Addons::get_instance();
        if (!$addons->is_addon_enabled('review-widgets')) {
            return '<div class="grp-review-button-block grp-addon-disabled"><p>' . __('Review Widgets addon is not enabled. Please enable it from the Addons page.', 'google-reviews-plugin') . '</p></div>';
        }
        
        // Get default values
        $button_text = isset($attributes['button_text']) ? $attributes['button_text'] : __('Leave us a review', 'google-reviews-plugin');
        $button_style = isset($attributes['button_style']) ? $attributes['button_style'] : 'default';
        $button_size = isset($attributes['button_size']) ? $attributes['button_size'] : 'medium';
        $align = isset($attributes['align']) ? $attributes['align'] : 'left';
        
        try {
            $widgets = GRP_Review_Widgets::get_instance();
            
            // Build shortcode attributes
            $shortcode_atts = array(
                'text' => $button_text,
                'style' => $button_style,
                'size' => $button_size,
                'align' => $align,
            );
            
            // Add colors if set
            if (!empty($attributes['text_color'])) {
                $shortcode_atts['color'] = $attributes['text_color'];
            }
            if (!empty($attributes['background_color'])) {
                $shortcode_atts['bg_color'] = $attributes['background_color'];
            }
            
            // Build shortcode
            $shortcode = '[grp_review_button';
            foreach ($shortcode_atts as $key => $value) {
                $shortcode .= ' ' . $key . '="' . esc_attr($value) . '"';
            }
            $shortcode .= ']';
            
            $output = do_shortcode($shortcode);
            
            // If shortcode returns empty, show error
            if (empty($output)) {
                return '<div class="grp-review-button-block grp-error"><p>' . __('Unable to generate review button. Please check your Place ID settings.', 'google-reviews-plugin') . '</p></div>';
            }
            
            return $output;
        } catch (Exception $e) {
            return '<div class="grp-review-button-block grp-error"><p>' . __('Error rendering review button: ', 'google-reviews-plugin') . esc_html($e->getMessage()) . '</p></div>';
        }
    }
    
    /**
     * Enqueue block editor assets
     */
    public function enqueue_block_editor_assets() {
        // Register dependencies - include server-side-render for newer WordPress versions
        $dependencies = array('wp-blocks', 'wp-element', 'wp-components', 'wp-i18n');
        
        // Add wp-editor for older WordPress versions, wp-block-editor for newer ones
        if (function_exists('wp_enqueue_block_editor_assets')) {
            $dependencies[] = 'wp-block-editor';
        } else {
            $dependencies[] = 'wp-editor';
        }
        
        // Add server-side-render if available (WordPress 5.3+)
        if (function_exists('register_block_type') && class_exists('WP_Block_Editor_Context')) {
            $dependencies[] = 'wp-server-side-render';
        }
        
        wp_enqueue_script(
            'grp-gutenberg-block',
            GRP_PLUGIN_URL . 'assets/js/gutenberg-block.js',
            $dependencies,
            GRP_PLUGIN_VERSION,
            true
        );
        
        wp_enqueue_style(
            'grp-gutenberg-block-editor',
            GRP_PLUGIN_URL . 'assets/css/gutenberg-block-editor.css',
            array('wp-edit-blocks'),
            GRP_PLUGIN_VERSION
        );

        // Inline dynamic CSS variables for styles/variants in the editor
        $styles = new GRP_Styles();
        wp_add_inline_style('grp-gutenberg-block-editor', $styles->get_all_css());
        
        // Localize script
        $addons = GRP_Addons::get_instance();
        $review_button_enabled = $addons->is_addon_enabled('review-widgets');
        
        // Check license status
        $license = new GRP_License();
        $is_pro = $license->is_pro();

        wp_localize_script('grp-gutenberg-block', 'grp_gutenberg', array(
            'styles' => $this->get_style_options(),
            'reviewButtonEnabled' => $review_button_enabled,
            'isPro' => $is_pro,
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

        // Ensure dynamic CSS is available on frontend where block styles are loaded
        $styles = new GRP_Styles();
        wp_add_inline_style('grp-gutenberg-block', $styles->get_all_css());
    }
    
    /**
     * Render reviews block
     */
    public function render_reviews_block($attributes) {
        // Ensure attributes are set with defaults
        if (empty($attributes)) {
            $attributes = array();
        }
        
        // Check if reviews are available
        $reviews = new GRP_Reviews();
        $has_reviews = $reviews->has_reviews();
        
        if (!$has_reviews) {
            return '<div class="grp-gutenberg-block grp-no-reviews"><p>' . __('No reviews available. Please connect your Google Business Profile and sync reviews.', 'google-reviews-plugin') . '</p></div>';
        }
        // Build custom CSS for style overrides
        $custom_css = '';
        if (!empty($attributes['custom_text_color'])) {
            $custom_css .= '.grp-gutenberg-block .grp-review-text, .grp-gutenberg-block .grp-author-name { color: ' . esc_attr($attributes['custom_text_color']) . ' !important; }';
        }
        if (!empty($attributes['custom_background_color'])) {
            $custom_css .= '.grp-gutenberg-block .grp-review { background-color: ' . esc_attr($attributes['custom_background_color']) . ' !important; }';
        }
        if (!empty($attributes['custom_border_color'])) {
            $custom_css .= '.grp-gutenberg-block .grp-review { border-color: ' . esc_attr($attributes['custom_border_color']) . ' !important; }';
        }
        if (!empty($attributes['custom_star_color'])) {
            $custom_css .= '.grp-gutenberg-block .grp-star { color: ' . esc_attr($attributes['custom_star_color']) . ' !important; }';
        }
        if (!empty($attributes['custom_font_size'])) {
            $custom_css .= '.grp-gutenberg-block .grp-review-text { font-size: ' . intval($attributes['custom_font_size']) . 'px !important; }';
        }
        if (!empty($attributes['custom_name_font_size'])) {
            $custom_css .= '.grp-gutenberg-block .grp-author-name { font-size: ' . intval($attributes['custom_name_font_size']) . 'px !important; }';
        }
        if (!empty($attributes['body_font_family'])) {
            $custom_css .= '.grp-gutenberg-block .grp-review-text { font-family: ' . esc_attr($attributes['body_font_family']) . ' !important; }';
        }
        if (!empty($attributes['name_font_family'])) {
            $custom_css .= '.grp-gutenberg-block .grp-author-name { font-family: ' . esc_attr($attributes['name_font_family']) . ' !important; }';
        }
        
        if (!empty($custom_css)) {
            $custom_css = '<style type="text/css">' . $custom_css . '</style>';
        }
        
        $shortcode_atts = array(
            'style' => $attributes['style'],
            'theme' => isset($attributes['theme']) ? $attributes['theme'] : 'light',
            'layout' => $attributes['layout'],
            'cols_desktop' => isset($attributes['cols_desktop']) ? $attributes['cols_desktop'] : 3,
            'cols_tablet' => isset($attributes['cols_tablet']) ? $attributes['cols_tablet'] : 2,
            'cols_mobile' => isset($attributes['cols_mobile']) ? $attributes['cols_mobile'] : 1,
            'gap' => isset($attributes['gap']) ? $attributes['gap'] : 20,
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
        return $custom_css . $shortcode->render_shortcode($shortcode_atts);
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