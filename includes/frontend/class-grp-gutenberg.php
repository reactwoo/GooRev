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
                'consistent_height' => array(
                    'type' => 'boolean',
                    'default' => false,
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
                // Creative style specific attributes
                'creative_gradient_type' => array(
                    'type' => 'string',
                    'default' => 'linear',
                ),
                'creative_gradient_angle' => array(
                    'type' => 'number',
                    'default' => 135,
                ),
                'creative_gradient_start' => array(
                    'type' => 'string',
                    'default' => '#4285F4',
                ),
                'creative_gradient_end' => array(
                    'type' => 'string',
                    'default' => '#EA4335',
                ),
                'creative_text_color' => array(
                    'type' => 'string',
                    'default' => '#ffffff',
                ),
                'creative_date_color' => array(
                    'type' => 'string',
                    'default' => '#ffffff',
                ),
                'creative_star_color' => array(
                    'type' => 'string',
                    'default' => '#FFD700',
                ),
                'creative_glass_effect' => array(
                    'type' => 'string',
                    'default' => 'no',
                ),
                'creative_box_shadow' => array(
                    'type' => 'object',
                    'default' => array(),
                ),
                'creative_border' => array(
                    'type' => 'object',
                    'default' => array(),
                ),
                'creative_border_radius' => array(
                    'type' => 'object',
                    'default' => array(),
                ),
                'creative_avatar_size' => array(
                    'type' => 'number',
                    'default' => 80,
                ),
                'creative_star_size' => array(
                    'type' => 'number',
                    'default' => 32,
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
        // Register dependencies - updated for WordPress compatibility
        $dependencies = array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-components');

        // Add wp-block-editor for newer WordPress versions (contains InspectorControls)
        // wp-editor for older WordPress versions
        if (function_exists('wp_enqueue_block_editor_assets')) {
            $dependencies[] = 'wp-block-editor';
        }
        // Always include wp-editor as fallback for InspectorControls in older versions
        $dependencies[] = 'wp-editor';

        // Always try to add server-side-render for previews
        // In WordPress 5.3+, it's wp-server-side-render
        // In older versions, it may be in wp-editor
        $server_side_render_available = wp_script_is('wp-server-side-render', 'registered');
        if (!$server_side_render_available) {
            // Check if it exists in wp-editor for older versions
            $scripts = wp_scripts();
            if ($scripts && isset($scripts->registered['wp-editor'])) {
                // wp-editor contains ServerSideRender in older WordPress versions
                $server_side_render_available = true;
            }
        }
        
        if ($server_side_render_available) {
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
        if (empty($attributes) || !is_array($attributes)) {
            $attributes = array();
        }
        
        // Set default values for all required attributes
        $attributes = wp_parse_args($attributes, array(
            'style' => 'modern',
            'theme' => 'light',
            'layout' => 'carousel',
            'count' => 5,
            'min_rating' => 1,
            'max_rating' => 5,
            'sort_by' => 'newest',
            'show_avatar' => true,
            'show_date' => true,
            'show_rating' => true,
            'show_reply' => true,
            'autoplay' => true,
            'speed' => 5000,
            'dots' => true,
            'arrows' => true,
            'consistent_height' => false,
            'cols_desktop' => 3,
            'cols_tablet' => 2,
            'cols_mobile' => 1,
            'gap' => 20,
        ));
        
        // Ensure boolean values are properly converted
        $attributes['show_avatar'] = filter_var($attributes['show_avatar'], FILTER_VALIDATE_BOOLEAN);
        $attributes['show_date'] = filter_var($attributes['show_date'], FILTER_VALIDATE_BOOLEAN);
        $attributes['show_rating'] = filter_var($attributes['show_rating'], FILTER_VALIDATE_BOOLEAN);
        $attributes['show_reply'] = filter_var($attributes['show_reply'], FILTER_VALIDATE_BOOLEAN);
        $attributes['autoplay'] = filter_var($attributes['autoplay'], FILTER_VALIDATE_BOOLEAN);
        $attributes['dots'] = filter_var($attributes['dots'], FILTER_VALIDATE_BOOLEAN);
        $attributes['arrows'] = filter_var($attributes['arrows'], FILTER_VALIDATE_BOOLEAN);
        $attributes['consistent_height'] = filter_var($attributes['consistent_height'], FILTER_VALIDATE_BOOLEAN);
        
        // Check if reviews are available
        $reviews_instance = new GRP_Reviews();
        $stored_reviews = $reviews_instance->get_stored_reviews(array('limit' => 1));

        if (empty($stored_reviews)) {
            return '<div class="grp-gutenberg-block grp-no-reviews"><p>' . __('No reviews available. Please connect your Google Business Profile and sync reviews.', 'google-reviews-plugin') . '</p></div>';
        }
        // Build custom CSS for style overrides
        $custom_css = '';

        // Creative style gradient background
        if (isset($attributes['style']) && $attributes['style'] === 'creative') {
            $bg_data = isset($attributes['creative_background']) ? $attributes['creative_background'] : array();
            $gradient_type = isset($bg_data['type']) ? $bg_data['type'] : 'linear';
            $start_color = isset($bg_data['start_color']) ? $bg_data['start_color'] : '#4285F4';
            $end_color = isset($bg_data['end_color']) ? $bg_data['end_color'] : '#EA4335';

            // Also check for Elementor-style data structure
            if (empty($start_color) && isset($bg_data['color'])) {
                $start_color = $bg_data['color'];
            }
            if (empty($end_color) && isset($bg_data['color_b'])) {
                $end_color = $bg_data['color_b'];
            }
            if ($gradient_type === 'linear' && isset($bg_data['gradient_angle'])) {
                $angle = isset($bg_data['gradient_angle']['size']) ? intval($bg_data['gradient_angle']['size']) : 135;
            } elseif ($gradient_type === 'linear') {
                $angle = isset($bg_data['angle']) ? intval($bg_data['angle']) : 135;
            }

            // Only apply gradient if custom values are set
            if ($gradient_type === 'linear') {
                if ($angle !== 135 || $start_color !== '#4285F4' || $end_color !== '#EA4335') {
                    $custom_css .= '.grp-gutenberg-block .grp-style-creative .grp-review { background: linear-gradient(' . $angle . 'deg, ' . esc_attr($start_color) . ' 0%, ' . esc_attr($end_color) . ' 100%) !important; }';
                }
            } else {
                if ($start_color !== '#4285F4' || $end_color !== '#EA4335') {
                    $custom_css .= '.grp-gutenberg-block .grp-style-creative .grp-review { background: radial-gradient(circle, ' . esc_attr($start_color) . ' 0%, ' . esc_attr($end_color) . ' 100%) !important; }';
                }
            }
            // Always apply background properties
            $custom_css .= '.grp-gutenberg-block .grp-style-creative .grp-review { background-size: cover; background-repeat: no-repeat; background-attachment: initial; }';

            // Glass effect
            if (isset($attributes['creative_glass_effect']) && $attributes['creative_glass_effect'] === 'yes') {
                $custom_css .= '.grp-gutenberg-block .grp-style-creative .grp-review { background: rgba(255, 255, 255, 0.25) !important; border: 1px solid rgba(255, 255, 255, 0.3) !important; backdrop-filter: blur(20px) !important; -webkit-backdrop-filter: blur(20px) !important; }';
            }
        }

        // Creative styles
        if (isset($attributes['creative_text_color'])) {
            $custom_css .= '.grp-gutenberg-block .grp-style-creative .grp-review-text, .grp-gutenberg-block .grp-style-creative .grp-author-name { color: ' . esc_attr($attributes['creative_text_color']) . ' !important; }';
        }
        if (isset($attributes['creative_date_color'])) {
            $custom_css .= '.grp-gutenberg-block .grp-style-creative .grp-review-date { color: ' . esc_attr($attributes['creative_date_color']) . ' !important; }';
        }
        if (isset($attributes['creative_star_color'])) {
            $custom_css .= '.grp-gutenberg-block .grp-style-creative .grp-star { color: ' . esc_attr($attributes['creative_star_color']) . ' !important; }';
        }
        if (isset($attributes['creative_avatar_size'])) {
            $avatar_size = intval($attributes['creative_avatar_size']);
            $custom_css .= '.grp-gutenberg-block .grp-style-creative .grp-review-avatar img { width: ' . $avatar_size . 'px !important; height: ' . $avatar_size . 'px !important; }';
        }
        if (isset($attributes['creative_star_size'])) {
            $star_size = intval($attributes['creative_star_size']);
            $custom_css .= '.grp-gutenberg-block .grp-style-creative .grp-star { font-size: ' . $star_size . 'px !important; }';
        }

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
            'style' => isset($attributes['style']) ? $attributes['style'] : 'modern',
            'theme' => isset($attributes['theme']) ? $attributes['theme'] : 'light',
            'layout' => isset($attributes['layout']) ? $attributes['layout'] : 'carousel',
            'cols_desktop' => isset($attributes['cols_desktop']) ? $attributes['cols_desktop'] : 3,
            'cols_tablet' => isset($attributes['cols_tablet']) ? $attributes['cols_tablet'] : 2,
            'cols_mobile' => isset($attributes['cols_mobile']) ? $attributes['cols_mobile'] : 1,
            'gap' => isset($attributes['gap']) ? $attributes['gap'] : 20,
            'count' => isset($attributes['count']) ? $attributes['count'] : 10,
            'min_rating' => isset($attributes['min_rating']) ? $attributes['min_rating'] : 1,
            'max_rating' => isset($attributes['max_rating']) ? $attributes['max_rating'] : 5,
            'sort_by' => isset($attributes['sort_by']) ? $attributes['sort_by'] : 'newest',
            'show_avatar' => isset($attributes['show_avatar']) && $attributes['show_avatar'] ? 'true' : 'false',
            'show_date' => isset($attributes['show_date']) && $attributes['show_date'] ? 'true' : 'false',
            'show_rating' => isset($attributes['show_rating']) && $attributes['show_rating'] ? 'true' : 'false',
            'show_reply' => isset($attributes['show_reply']) && $attributes['show_reply'] ? 'true' : 'false',
            'autoplay' => isset($attributes['autoplay']) && $attributes['autoplay'] ? 'true' : 'false',
            'speed' => isset($attributes['speed']) ? $attributes['speed'] : 5000,
            'dots' => isset($attributes['dots']) && $attributes['dots'] ? 'true' : 'false',
            'arrows' => isset($attributes['arrows']) && $attributes['arrows'] ? 'true' : 'false',
            'consistent_height' => isset($attributes['consistent_height']) && $attributes['consistent_height'] ? 'true' : 'false',
            // Creative style specific options
            'creative_gradient_type' => isset($attributes['creative_gradient_type']) ? $attributes['creative_gradient_type'] : 'linear',
            'creative_gradient_angle' => isset($attributes['creative_gradient_angle']) ? $attributes['creative_gradient_angle'] : 135,
            'creative_gradient_start' => isset($attributes['creative_gradient_start']) ? $attributes['creative_gradient_start'] : '#4285F4',
            'creative_gradient_end' => isset($attributes['creative_gradient_end']) ? $attributes['creative_gradient_end'] : '#EA4335',
            'creative_text_color' => isset($attributes['creative_text_color']) ? $attributes['creative_text_color'] : '#ffffff',
            'creative_date_color' => isset($attributes['creative_date_color']) ? $attributes['creative_date_color'] : '#ffffff',
            'creative_star_color' => isset($attributes['creative_star_color']) ? $attributes['creative_star_color'] : '#FFD700',
            'creative_glass_effect' => isset($attributes['creative_glass_effect']) ? $attributes['creative_glass_effect'] : 'no',
            'creative_box_shadow' => isset($attributes['creative_box_shadow']) ? $attributes['creative_box_shadow'] : array(),
            'creative_border' => isset($attributes['creative_border']) ? $attributes['creative_border'] : array(),
            'creative_border_radius' => isset($attributes['creative_border_radius']) ? $attributes['creative_border_radius'] : array(),
            'creative_avatar_size' => isset($attributes['creative_avatar_size']) ? $attributes['creative_avatar_size'] : 80,
            'creative_star_size' => isset($attributes['creative_star_size']) ? $attributes['creative_star_size'] : 32,
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