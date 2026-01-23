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
        
        // Filter block attributes before REST API validation to prevent 400 errors
        add_filter('rest_pre_dispatch', array($this, 'sanitize_block_attributes'), 10, 3);
    }
    
    /**
     * Sanitize block attributes before REST API validation
     * This prevents 400 errors from type mismatches
     * Note: This filter runs before WordPress validates attributes against the schema
     */
    public function sanitize_block_attributes($result, $server, $request) {
        // Only process block-renderer requests for our block
        $route = $request->get_route();
        if (!$route || strpos($route, '/wp/v2/block-renderer/google-reviews/reviews') === false) {
            return $result;
        }
        
        try {
            // Get attributes from request
            $params = $request->get_params();
            if (isset($params['attributes']) && is_array($params['attributes'])) {
                $attributes = $params['attributes'];
                $modified = false;
                
                // Sanitize numeric attributes - convert empty strings/null to defaults
        $numeric_attrs = array('count', 'min_rating', 'max_rating', 'speed', 'cols_desktop', 'cols_tablet', 'cols_mobile', 'gap', 
                               'custom_font_size', 'custom_name_font_size', 'custom_padding', 'custom_border_radius', 'custom_avatar_size',
                               'arrow_size', 'arrow_icon_size', 'arrow_border_radius', 'arrow_horizontal_position', 'arrow_vertical_position',
                               'dot_size', 'dot_spacing', 'dot_border_radius', 'creative_avatar_size', 'creative_star_size', 'creative_gradient_angle',
                               'creative_border_radius_value');
                foreach ($numeric_attrs as $attr) {
                    if (isset($attributes[$attr])) {
                        // Convert empty string, null, or non-numeric - remove to use default
                        if ($attributes[$attr] === '' || $attributes[$attr] === null || (!is_numeric($attributes[$attr]) && $attributes[$attr] !== 0)) {
                            unset($attributes[$attr]); // Remove invalid value, let default be used
                            $modified = true;
                        } elseif (is_numeric($attributes[$attr])) {
                            $attributes[$attr] = intval($attributes[$attr]);
                            $modified = true;
                        }
                    }
                }
                
                // Sanitize boolean attributes
                $boolean_attrs = array('show_avatar', 'show_date', 'show_rating', 'show_reply', 'autoplay', 'dots', 'arrows', 'loop', 'consistent_height');
                foreach ($boolean_attrs as $attr) {
                    if (isset($attributes[$attr])) {
                        if ($attributes[$attr] === '' || $attributes[$attr] === null) {
                            unset($attributes[$attr]);
                            $modified = true;
                        } elseif (!is_bool($attributes[$attr])) {
                            $attributes[$attr] = filter_var($attributes[$attr], FILTER_VALIDATE_BOOLEAN);
                            $modified = true;
                        }
                    }
                }
                
                // Update request with sanitized attributes if modified
                if ($modified) {
                    $params['attributes'] = $attributes;
                    // Use reflection to update request params (WordPress doesn't provide a direct setter)
                    $request->set_param('attributes', $attributes);
                }
            }
        } catch (Exception $e) {
            // Log but don't break the request
            if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                error_log('GRP Gutenberg: Error sanitizing attributes: ' . $e->getMessage());
            }
        }
        
        return $result;
    }
    
    /**
     * Register Gutenberg blocks
     */
    public function register_blocks() {
        if (!function_exists('register_block_type')) {
            return;
        }

        // Register block styles so editor_style can load inside the iframe.
        wp_register_style(
            'grp-gutenberg-block-editor',
            GRP_PLUGIN_URL . 'assets/css/gutenberg-block-editor.css',
            array('wp-edit-blocks'),
            GRP_PLUGIN_VERSION
        );
        wp_register_style(
            'grp-gutenberg-block',
            GRP_PLUGIN_URL . 'assets/css/gutenberg-block.css',
            array(),
            GRP_PLUGIN_VERSION
        );
        
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
                'loop' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'consistent_height' => array(
                    'type' => 'boolean',
                    'default' => false,
                ),
                'custom_text_color' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                'custom_background_color' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                'custom_border_color' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                'custom_accent_color' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                'custom_star_color' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                'custom_font_size' => array(
                    'type' => 'number',
                    'default' => 0,
                ),
                'custom_name_font_size' => array(
                    'type' => 'number',
                    'default' => 0,
                ),
                'body_font_family' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                'name_font_family' => array(
                    'type' => 'string',
                    'default' => '',
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
                'creative_border_radius_value' => array(
                    'type' => 'number',
                    'default' => 16,
                ),
                // Note: creative_box_shadow, creative_border, creative_border_radius are objects
                // They are handled via CSS in Elementor/Gutenberg editor but not passed via REST API
                // Removed from REST API validation to prevent 400 errors
                'creative_avatar_size' => array(
                    'type' => 'number',
                    'default' => 80,
                ),
                'creative_star_size' => array(
                    'type' => 'number',
                    'default' => 32,
                ),
                'arrow_size' => array(
                    'type' => 'number',
                    'default' => 40,
                ),
                'arrow_icon_size' => array(
                    'type' => 'number',
                    'default' => 18,
                ),
                'arrow_background_color' => array(
                    'type' => 'string',
                    'default' => 'rgba(0, 0, 0, 0.5)',
                ),
                'arrow_hover_background_color' => array(
                    'type' => 'string',
                    'default' => 'rgba(0, 0, 0, 0.7)',
                ),
                'arrow_icon_color' => array(
                    'type' => 'string',
                    'default' => '#ffffff',
                ),
                'arrow_border_radius' => array(
                    'type' => 'number',
                    'default' => 50,
                ),
                'arrow_horizontal_position' => array(
                    'type' => 'number',
                    'default' => 0,
                ),
                'arrow_vertical_position' => array(
                    'type' => 'number',
                    'default' => 0,
                ),
                'arrow_icon' => array(
                    'type' => 'string',
                    'default' => 'chevron',
                ),
                'arrow_box_shadow' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                // Dot styling attributes
                'dot_color' => array(
                    'type' => 'string',
                    'default' => '#ccc',
                ),
                'dot_active_color' => array(
                    'type' => 'string',
                    'default' => '#007cba',
                ),
                'dot_size' => array(
                    'type' => 'number',
                    'default' => 12,
                ),
                'dot_spacing' => array(
                    'type' => 'number',
                    'default' => 8,
                ),
                'dot_border_radius' => array(
                    'type' => 'number',
                    'default' => 50,
                ),
                'custom_padding' => array(
                    'type' => 'number',
                    'default' => 16,
                ),
                'custom_border_radius' => array(
                    'type' => 'number',
                    'default' => 8,
                ),
                'custom_box_shadow' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                'custom_text_align' => array(
                    'type' => 'string',
                    'default' => 'left',
                ),
                'custom_avatar_size' => array(
                    'type' => 'number',
                    'default' => 40,
                ),
                // Additional attributes that might be sent but aren't critical
                'creative_background' => array(
                    'type' => 'object',
                    'default' => array(),
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
        // Handle both array and query string formats from REST API
        if (empty($attributes)) {
            $attributes = array();
        }
        
        if (!is_array($attributes)) {
            // If it's not an array, try to parse it
            if (is_string($attributes)) {
                parse_str($attributes, $attributes);
            } else {
                $attributes = (array) $attributes;
            }
        }
        
        // Handle nested attribute arrays from REST API
        if (isset($attributes['attributes']) && is_array($attributes['attributes'])) {
            $attributes = array_merge($attributes, $attributes['attributes']);
            unset($attributes['attributes']);
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
            // The shortcode uses 'template' parameter, not 'style'
            // Map Gutenberg 'style' to shortcode 'template'
            $template_mapped = $button_style;
            // Valid templates: 'basic', 'modern', 'elegant', 'bold', 'minimalist', 'card', 'creative', 'layout1', 'layout2', 'layout3', 'creative-pro'
            // Valid styles (for button CSS): 'default', 'rounded', 'outline', 'minimal'
            $valid_templates = array('basic', 'modern', 'elegant', 'bold', 'minimalist', 'card', 'creative', 'layout1', 'layout2', 'layout3', 'creative-pro');
            $valid_styles = array('default', 'rounded', 'outline', 'minimal');
            
            // If it's a template name, use it as template; if it's a style, use 'basic' template with that style
            if (in_array($button_style, $valid_templates, true)) {
                $template_mapped = $button_style;
                $button_style_value = 'default'; // Use default style for template-based buttons
            } elseif (in_array($button_style, $valid_styles, true)) {
                $template_mapped = 'basic'; // Use basic template for style-based buttons
                $button_style_value = $button_style;
            } else {
                // Default to basic template
                $template_mapped = 'basic';
                $button_style_value = 'default';
            }
            
            $shortcode_atts = array(
                'text' => $button_text,
                'template' => $template_mapped,
                'style' => $button_style_value,
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

        // Ensure both frontend and editor styles are loaded in the editor iframe.
        wp_enqueue_style('grp-gutenberg-block');
        wp_enqueue_style('grp-gutenberg-block-editor');

        // Inline dynamic CSS variables for styles/variants in the editor
        $styles = new GRP_Styles();
        wp_add_inline_style('grp-gutenberg-block', $styles->get_all_css());
        wp_add_inline_style('grp-gutenberg-block-editor', $styles->get_all_css());
        
        // Localize script
        $addons = GRP_Addons::get_instance();
        $review_button_enabled = $addons->is_addon_enabled('review-widgets');
        
        // Check license status - use the same method as Elementor widget
        $license = new GRP_License();
        $is_pro = $license->is_pro();
        
        // Debug: Log license status for troubleshooting
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('GRP Gutenberg License Check: is_pro = ' . ($is_pro ? 'true' : 'false'));
        }

        wp_localize_script('grp-gutenberg-block', 'grp_gutenberg', array(
            'styles' => $this->get_style_options(),
            'reviewButtonEnabled' => $review_button_enabled,
            'isPro' => (bool) $is_pro, // Force boolean conversion - PHP may pass as string '1' or '0'
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

        // Ensure editor stylesheet is loaded inside the block editor iframe.
        if (is_admin()) {
            wp_enqueue_style(
                'grp-gutenberg-block-editor',
                GRP_PLUGIN_URL . 'assets/css/gutenberg-block-editor.css',
                array('wp-edit-blocks'),
                GRP_PLUGIN_VERSION
            );
            wp_add_inline_style('grp-gutenberg-block-editor', $styles->get_all_css());
        }
    }
    
    /**
     * Render reviews block
     */
    public function render_reviews_block($attributes) {
        // Error handling wrapper for debugging
        try {
            // Log raw attributes for debugging (only in debug mode)
            if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                error_log('GRP Gutenberg SSR: Raw attributes received: ' . print_r($attributes, true));
            }
            
            // Ensure attributes are set with defaults
            // Handle both array and query string formats from REST API
            if (empty($attributes)) {
                $attributes = array();
            }
            
            if (!is_array($attributes)) {
                // If it's not an array, try to parse it
                if (is_string($attributes)) {
                    parse_str($attributes, $attributes);
                } else {
                    $attributes = (array) $attributes;
                }
            }
            
            // Handle nested attribute arrays from REST API (attributes[key] format in query strings)
            // WordPress REST API may pass attributes as a nested structure
            if (isset($attributes['attributes']) && is_array($attributes['attributes'])) {
                $attributes = array_merge($attributes, $attributes['attributes']);
                unset($attributes['attributes']);
            }
            
            // Handle query string format attributes[key]=value that parse_str creates
            // This creates nested arrays that need to be flattened
            $flattened = array();
            foreach ($attributes as $key => $value) {
                // Skip non-string keys
                if (!is_string($key)) {
                    $flattened[$key] = $value;
                    continue;
                }
                
                // Handle keys like 'attributes[style]' - parse_str creates nested array
                if (strpos($key, '[') !== false && strpos($key, ']') !== false) {
                    // This is a nested key format - skip it as it's already been handled
                    continue;
                }
                
                // Handle object-type attributes - convert to empty array if invalid
                if (in_array($key, array('creative_box_shadow', 'creative_border', 'creative_border_radius', 'creative_background'), true)) {
                    // These are objects - ensure they're arrays
                    if (!is_array($value)) {
                        $flattened[$key] = array();
                    } else {
                        $flattened[$key] = $value;
                    }
                    continue;
                }
                
                $flattened[$key] = $value;
            }
            
            $attributes = $flattened;
            
            // Log sanitized attributes for debugging
            if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                error_log('GRP Gutenberg SSR: Sanitized attributes: ' . print_r($attributes, true));
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
            // Style customization defaults
            'custom_text_color' => '',
            'custom_background_color' => '',
            'custom_border_color' => '',
            'custom_accent_color' => '',
            'custom_star_color' => '',
            'custom_font_size' => 0,
            'custom_name_font_size' => 0,
            // Arrow styling defaults
            'arrow_size' => 40,
            'arrow_icon_size' => 18,
            'arrow_background_color' => 'rgba(0, 0, 0, 0.5)',
            'arrow_hover_background_color' => 'rgba(0, 0, 0, 0.7)',
            'arrow_icon_color' => '#ffffff',
            'arrow_border_radius' => 50,
            'arrow_horizontal_position' => 0,
            'arrow_vertical_position' => 0,
            // Dot styling defaults
            'dot_color' => '#ccc',
            'dot_active_color' => '#007cba',
            'dot_size' => 12,
            'dot_spacing' => 8,
            'dot_border_radius' => 50,
        ));
        
        // Sanitize and validate all attributes
        // Numbers: cast to int, allow 0 or empty for optional fields
                $numeric_attrs = array('count', 'min_rating', 'max_rating', 'speed', 'cols_desktop', 'cols_tablet', 'cols_mobile', 'gap',
                                       'custom_font_size', 'custom_name_font_size', 'custom_padding', 'custom_border_radius', 'custom_avatar_size',
                                       'arrow_size', 'arrow_icon_size', 'arrow_border_radius', 'arrow_horizontal_position', 'arrow_vertical_position',
                                       'dot_size', 'dot_spacing', 'dot_border_radius', 'creative_avatar_size', 'creative_star_size', 'creative_gradient_angle',
                                       'creative_border_radius_value');
        foreach ($numeric_attrs as $attr) {
            if (isset($attributes[$attr])) {
                // Allow empty string, null, or 0 - convert to appropriate default
                if ($attributes[$attr] === '' || $attributes[$attr] === null) {
                    // Use default from wp_parse_args above
                    continue;
                }
                $attributes[$attr] = is_numeric($attributes[$attr]) ? intval($attributes[$attr]) : 0;
            }
        }
        
        // Booleans: convert to proper boolean
        $boolean_attrs = array('show_avatar', 'show_date', 'show_rating', 'show_reply', 'autoplay', 'dots', 'arrows', 'loop', 'consistent_height');
        foreach ($boolean_attrs as $attr) {
            if (isset($attributes[$attr])) {
                $attributes[$attr] = filter_var($attributes[$attr], FILTER_VALIDATE_BOOLEAN);
            }
        }
        
        // Strings: sanitize text fields and allow empty for colors
        $string_attrs = array('style', 'theme', 'layout', 'sort_by', 'custom_text_color', 'custom_background_color', 
                              'custom_border_color', 'custom_accent_color', 'custom_star_color', 'custom_box_shadow', 'custom_text_align',
                              'body_font_family', 'name_font_family', 'arrow_background_color', 'arrow_hover_background_color',
                              'arrow_icon_color', 'arrow_icon', 'arrow_box_shadow', 'dot_color', 'dot_active_color',
                              'creative_gradient_type', 'creative_gradient_start', 'creative_gradient_end', 'creative_text_color',
                              'creative_date_color', 'creative_star_color', 'creative_glass_effect');
        foreach ($string_attrs as $attr) {
            if (isset($attributes[$attr])) {
                // Allow empty strings for all string fields (especially colors)
                if ($attributes[$attr] === null) {
                    // Use default from wp_parse_args
                    continue;
                }
                // Sanitize but preserve empty strings
                $attributes[$attr] = is_string($attributes[$attr]) ? sanitize_text_field($attributes[$attr]) : '';
            }
        }
        
        // Check if reviews are available
        $reviews_instance = new GRP_Reviews();
        $stored_reviews = $reviews_instance->get_stored_reviews(array('limit' => 1));

        if (empty($stored_reviews)) {
            return '<div class="grp-gutenberg-block grp-no-reviews"><p>' . __('No reviews available. Please connect your Google Business Profile and sync reviews.', 'google-reviews-plugin') . '</p></div>';
        }
        // Build custom CSS for style overrides
        $custom_css = '';

        // Style customizations - use CSS variables (set on wrapper) instead of !important rules
        // The CSS variables are already set in $css_vars and applied to the wrapper
        // This ensures styles apply both in editor preview and frontend
        if (!empty($attributes['body_font_family'])) {
            $custom_css .= '.grp-gutenberg-block .grp-review-text { font-family: ' . esc_attr($attributes['body_font_family']) . ' !important; }';
        }
        if (!empty($attributes['name_font_family'])) {
            $custom_css .= '.grp-gutenberg-block .grp-author-name { font-family: ' . esc_attr($attributes['name_font_family']) . ' !important; }';
        }
        
        // Build CSS variables for style customization (for live preview)
        $css_vars = array();
        
        // Custom colors - use both naming conventions for compatibility
        if (!empty($attributes['custom_text_color'])) {
            $css_vars[] = '--grp-text-color: ' . esc_attr($attributes['custom_text_color']);
            $css_vars[] = '--grp-text: ' . esc_attr($attributes['custom_text_color']);
        }
        if (!empty($attributes['custom_background_color'])) {
            $css_vars[] = '--grp-card-bg: ' . esc_attr($attributes['custom_background_color']);
            $css_vars[] = '--grp-card_background: ' . esc_attr($attributes['custom_background_color']);
        }
        if (!empty($attributes['custom_border_color'])) {
            $css_vars[] = '--grp-border-color: ' . esc_attr($attributes['custom_border_color']);
            $css_vars[] = '--grp-border: ' . esc_attr($attributes['custom_border_color']);
        }
        if (!empty($attributes['custom_accent_color'])) {
            $css_vars[] = '--grp-accent-color: ' . esc_attr($attributes['custom_accent_color']);
            $css_vars[] = '--grp-accent: ' . esc_attr($attributes['custom_accent_color']);
        }
        if (!empty($attributes['custom_star_color'])) {
            $css_vars[] = '--grp-star-color: ' . esc_attr($attributes['custom_star_color']);
            $css_vars[] = '--grp-star: ' . esc_attr($attributes['custom_star_color']);
        }
        
        // Font sizes
        if (!empty($attributes['custom_font_size'])) {
            $css_vars[] = '--grp-font-size: ' . intval($attributes['custom_font_size']) . 'px';
        }
        if (!empty($attributes['custom_name_font_size'])) {
            $css_vars[] = '--grp-name-font-size: ' . intval($attributes['custom_name_font_size']) . 'px';
        }
        
        // Column and gap variables
        $css_vars[] = '--grp-cols-desktop: ' . (isset($attributes['cols_desktop']) && $attributes['cols_desktop'] > 0 ? intval($attributes['cols_desktop']) : 3);
        $css_vars[] = '--grp-cols-tablet: ' . (isset($attributes['cols_tablet']) && $attributes['cols_tablet'] > 0 ? intval($attributes['cols_tablet']) : 2);
        $css_vars[] = '--grp-cols-mobile: ' . (isset($attributes['cols_mobile']) && $attributes['cols_mobile'] > 0 ? intval($attributes['cols_mobile']) : 1);
        $css_vars[] = '--grp-cols: ' . (isset($attributes['cols_desktop']) && $attributes['cols_desktop'] > 0 ? intval($attributes['cols_desktop']) : 3);
        $css_vars[] = '--grp-gap: ' . (isset($attributes['gap']) && $attributes['gap'] > 0 ? intval($attributes['gap']) : 20) . 'px';
        
        // Arrow styling variables (match Elementor naming)
        if (!empty($attributes['arrow_size'])) {
            $css_vars[] = '--grp-arrow-size: ' . intval($attributes['arrow_size']) . 'px';
        }
        if (!empty($attributes['arrow_icon_size'])) {
            $css_vars[] = '--grp-arrow-icon-size: ' . intval($attributes['arrow_icon_size']) . 'px';
        }
        if (!empty($attributes['arrow_icon_color'])) {
            $css_vars[] = '--grp-arrow-icon-color: ' . esc_attr($attributes['arrow_icon_color']);
            $css_vars[] = '--grp-arrow-color: ' . esc_attr($attributes['arrow_icon_color']);
        }
        if (!empty($attributes['arrow_background_color'])) {
            $css_vars[] = '--grp-arrow-bg: ' . esc_attr($attributes['arrow_background_color']);
            $css_vars[] = '--grp-arrow-background-color: ' . esc_attr($attributes['arrow_background_color']);
        }
        if (!empty($attributes['arrow_hover_background_color'])) {
            $css_vars[] = '--grp-arrow-hover-bg: ' . esc_attr($attributes['arrow_hover_background_color']);
            $css_vars[] = '--grp-arrow-hover-background-color: ' . esc_attr($attributes['arrow_hover_background_color']);
        }
        if (isset($attributes['arrow_border_radius'])) {
            $css_vars[] = '--grp-arrow-radius: ' . intval($attributes['arrow_border_radius']) . '%';
            $css_vars[] = '--grp-arrow-border-radius: ' . intval($attributes['arrow_border_radius']) . '%';
        }
        if (isset($attributes['arrow_horizontal_position'])) {
            $css_vars[] = '--grp-arrow-horizontal: ' . intval($attributes['arrow_horizontal_position']) . 'px';
        }
        if (isset($attributes['arrow_vertical_position'])) {
            $css_vars[] = '--grp-arrow-vertical: ' . intval($attributes['arrow_vertical_position']) . 'px';
        }
        
        $css_vars_string = !empty($css_vars) ? ' style="' . esc_attr(implode('; ', $css_vars)) . '"' : '';
        
        if (!empty($custom_css)) {
            $custom_css = '<style type="text/css">' . $custom_css . '</style>';
        }
        
        $shortcode_atts = array(
            'style' => isset($attributes['style']) ? $attributes['style'] : 'modern',
            'theme' => isset($attributes['theme']) ? $attributes['theme'] : 'light',
            'layout' => isset($attributes['layout']) ? $attributes['layout'] : 'carousel',
            // Ensure column defaults are correct - free users should get 3 columns for carousel
            'cols_desktop' => isset($attributes['cols_desktop']) && $attributes['cols_desktop'] > 0 ? intval($attributes['cols_desktop']) : 3,
            'cols_tablet' => isset($attributes['cols_tablet']) && $attributes['cols_tablet'] > 0 ? intval($attributes['cols_tablet']) : 2,
            'cols_mobile' => isset($attributes['cols_mobile']) && $attributes['cols_mobile'] > 0 ? intval($attributes['cols_mobile']) : 1,
            'gap' => isset($attributes['gap']) && $attributes['gap'] > 0 ? intval($attributes['gap']) : 20,
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
            'loop' => isset($attributes['loop']) && $attributes['loop'] ? 'true' : 'false',
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
            'creative_border_radius_value' => isset($attributes['creative_border_radius_value']) ? $attributes['creative_border_radius_value'] : 16,
            'creative_box_shadow' => isset($attributes['creative_box_shadow']) ? $attributes['creative_box_shadow'] : array(),
            'creative_border' => isset($attributes['creative_border']) ? $attributes['creative_border'] : array(),
            'creative_border_radius' => isset($attributes['creative_border_radius']) ? $attributes['creative_border_radius'] : array(),
            'creative_avatar_size' => isset($attributes['creative_avatar_size']) ? $attributes['creative_avatar_size'] : 80,
            'creative_star_size' => isset($attributes['creative_star_size']) ? $attributes['creative_star_size'] : 32,
            'arrow_size' => isset($attributes['arrow_size']) ? $attributes['arrow_size'] : 40,
            'arrow_icon_size' => isset($attributes['arrow_icon_size']) ? $attributes['arrow_icon_size'] : 18,
            'arrow_background_color' => isset($attributes['arrow_background_color']) ? $attributes['arrow_background_color'] : 'rgba(0, 0, 0, 0.5)',
            'arrow_hover_background_color' => isset($attributes['arrow_hover_background_color']) ? $attributes['arrow_hover_background_color'] : 'rgba(0, 0, 0, 0.7)',
            'arrow_icon_color' => isset($attributes['arrow_icon_color']) ? $attributes['arrow_icon_color'] : '#ffffff',
            'arrow_border_radius' => isset($attributes['arrow_border_radius']) ? $attributes['arrow_border_radius'] : 50,
            'arrow_horizontal_position' => isset($attributes['arrow_horizontal_position']) ? $attributes['arrow_horizontal_position'] : 0,
            'arrow_vertical_position' => isset($attributes['arrow_vertical_position']) ? $attributes['arrow_vertical_position'] : 0,
            'arrow_icon' => isset($attributes['arrow_icon']) ? $attributes['arrow_icon'] : 'chevron',
            'arrow_box_shadow' => isset($attributes['arrow_box_shadow']) ? $attributes['arrow_box_shadow'] : '',
            // Pass dot styling to shortcode renderer
            'dot_color' => isset($attributes['dot_color']) && $attributes['dot_color'] !== '' ? $attributes['dot_color'] : '#ccc',
            'dot_active_color' => isset($attributes['dot_active_color']) && $attributes['dot_active_color'] !== '' ? $attributes['dot_active_color'] : '#007cba',
            'dot_size' => isset($attributes['dot_size']) && $attributes['dot_size'] > 0 ? intval($attributes['dot_size']) : 12,
            'dot_spacing' => isset($attributes['dot_spacing']) && $attributes['dot_spacing'] >= 0 ? intval($attributes['dot_spacing']) : 8,
            'dot_border_radius' => isset($attributes['dot_border_radius']) && $attributes['dot_border_radius'] >= 0 ? intval($attributes['dot_border_radius']) : 50,
            // Pass style customizations to shortcode renderer
            'custom_text_color' => isset($attributes['custom_text_color']) ? $attributes['custom_text_color'] : '',
            'custom_background_color' => isset($attributes['custom_background_color']) ? $attributes['custom_background_color'] : '',
            'custom_border_color' => isset($attributes['custom_border_color']) ? $attributes['custom_border_color'] : '',
            'custom_accent_color' => isset($attributes['custom_accent_color']) ? $attributes['custom_accent_color'] : '',
            'custom_star_color' => isset($attributes['custom_star_color']) ? $attributes['custom_star_color'] : '',
            'custom_font_size' => isset($attributes['custom_font_size']) ? $attributes['custom_font_size'] : '',
            'custom_name_font_size' => isset($attributes['custom_name_font_size']) ? $attributes['custom_name_font_size'] : '',
            'custom_padding' => isset($attributes['custom_padding']) ? $attributes['custom_padding'] : '',
            'custom_border_radius' => isset($attributes['custom_border_radius']) ? $attributes['custom_border_radius'] : '',
            'custom_box_shadow' => isset($attributes['custom_box_shadow']) ? $attributes['custom_box_shadow'] : '',
            'custom_text_align' => isset($attributes['custom_text_align']) ? $attributes['custom_text_align'] : 'left',
            'custom_avatar_size' => isset($attributes['custom_avatar_size']) ? $attributes['custom_avatar_size'] : '',
            'class' => 'grp-gutenberg-block'
        );
        
        $shortcode = new GRP_Shortcode();
        $shortcode_output = $shortcode->render_shortcode($shortcode_atts);
        
        // CSS variables are now applied directly in the shortcode renderer
        // No need to inject them here - they're already in the output
        return $custom_css . $shortcode_output;
        } catch (Exception $e) {
            // Log detailed error for debugging
            $error_message = 'GRP Gutenberg Block Error: ' . $e->getMessage();
            $error_message .= "\nStack trace: " . $e->getTraceAsString();
            $error_message .= "\nAttributes received: " . print_r($attributes, true);
            error_log($error_message);
            
            // Return WP_Error for REST API calls (SSR) or HTML for direct calls
            if (defined('REST_REQUEST') && REST_REQUEST) {
                return new WP_Error(
                    'grp_render_error',
                    __('Error rendering Google Reviews block: ', 'google-reviews-plugin') . $e->getMessage(),
                    array(
                        'status' => 500,
                        'error_details' => $error_message
                    )
                );
            }
            
            return '<div class="grp-gutenberg-block grp-error"><p>' . __('Error loading reviews block. Please check your settings.', 'google-reviews-plugin') . '</p><p><small>' . esc_html($e->getMessage()) . '</small></p></div>';
        } catch (Error $e) {
            // Catch PHP 7+ errors (TypeError, etc.)
            $error_message = 'GRP Gutenberg Block Fatal Error: ' . $e->getMessage();
            $error_message .= "\nStack trace: " . $e->getTraceAsString();
            $error_message .= "\nAttributes received: " . print_r($attributes, true);
            error_log($error_message);
            
            if (defined('REST_REQUEST') && REST_REQUEST) {
                return new WP_Error(
                    'grp_render_fatal_error',
                    __('Fatal error rendering Google Reviews block: ', 'google-reviews-plugin') . $e->getMessage(),
                    array(
                        'status' => 500,
                        'error_details' => $error_message
                    )
                );
            }
            
            return '<div class="grp-gutenberg-block grp-error"><p>' . __('Fatal error loading reviews block.', 'google-reviews-plugin') . '</p><p><small>' . esc_html($e->getMessage()) . '</small></p></div>';
        }
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