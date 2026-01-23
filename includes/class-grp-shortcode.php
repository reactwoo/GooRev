<?php
/**
 * Shortcode functionality
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Shortcode {
    
    /**
     * Reviews instance
     */
    private $reviews;
    
    /**
     * Styles instance
     */
    private $styles;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->reviews = new GRP_Reviews();
        $this->styles = new GRP_Styles();
        
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_shortcode('google_reviews', array($this, 'render_shortcode'));
        add_shortcode('grp_reviews', array($this, 'render_shortcode')); // Alternative shortcode
    }
    
    /**
     * Render shortcode
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'style' => 'modern',
            'theme' => 'light',
            'layout' => 'carousel',
            'cols_desktop' => 3,
            'cols_tablet' => 2,
            'cols_mobile' => 1,
            'gap' => 20,
            'count' => 5,
            'min_rating' => 1,
            'max_rating' => 5,
            'sort_by' => 'newest',
            'show_avatar' => 'true',
            'show_date' => 'true',
            'show_rating' => 'true',
            'show_reply' => 'true',
            'autoplay' => 'true',
            'speed' => 5000,
            'dots' => 'true',
            'arrows' => 'true',
            'responsive' => 'true',
            'consistent_height' => 'false',
            // Creative style specific options
            'creative_background' => array(
                'type' => 'linear',
                'angle' => 135,
                'start_color' => '#4285F4',
                'end_color' => '#EA4335'
            ),
            'creative_glass_effect' => 'no',
            'creative_box_shadow' => array(),
            'creative_border' => array(),
            'creative_border_radius' => array(),
            'creative_border_radius_value' => 16,
            'creative_avatar_size' => 80,
            'creative_star_size' => 32,
            'creative_text_color' => '#ffffff',
            'creative_date_color' => '#ffffff',
            'creative_star_color' => '#FFD700',
            // Style customizations
            'custom_text_color' => '',
            'custom_background_color' => '',
            'custom_border_color' => '',
            'custom_accent_color' => '',
            'custom_star_color' => '',
            'custom_font_size' => '',
            'custom_name_font_size' => '',
            'custom_padding' => '',
            'custom_border_radius' => '',
            'custom_box_shadow' => '',
            'custom_text_align' => 'left',
            'custom_avatar_size' => '',
            // Arrow styling
            'arrow_size' => '',
            'arrow_icon_size' => '',
            'arrow_icon_color' => '',
            'arrow_background_color' => '',
            'arrow_hover_background_color' => '',
            'arrow_border_radius' => '',
            'arrow_horizontal_position' => '',
            'arrow_vertical_position' => '',
            'arrow_icon' => '',
            'arrow_box_shadow' => '',
            // Dot styling
            'dot_color' => '',
            'dot_active_color' => '',
            'dot_size' => '',
            'dot_spacing' => '',
            'dot_border_radius' => '',
            'class' => '',
            'id' => ''
        ), $atts, 'google_reviews');
        
        // Convert string booleans to actual booleans
        $atts['show_avatar'] = filter_var($atts['show_avatar'], FILTER_VALIDATE_BOOLEAN);
        $atts['show_date'] = filter_var($atts['show_date'], FILTER_VALIDATE_BOOLEAN);
        $atts['show_rating'] = filter_var($atts['show_rating'], FILTER_VALIDATE_BOOLEAN);
        $atts['show_reply'] = filter_var($atts['show_reply'], FILTER_VALIDATE_BOOLEAN);
        $atts['autoplay'] = filter_var($atts['autoplay'], FILTER_VALIDATE_BOOLEAN);
        $atts['dots'] = filter_var($atts['dots'], FILTER_VALIDATE_BOOLEAN);
        $atts['arrows'] = filter_var($atts['arrows'], FILTER_VALIDATE_BOOLEAN);
        $atts['responsive'] = filter_var($atts['responsive'], FILTER_VALIDATE_BOOLEAN);
        
        // Get reviews from database first
        $reviews = $this->reviews->get_stored_reviews(array(
            'limit' => intval($atts['count']),
            'min_rating' => intval($atts['min_rating']),
            'max_rating' => intval($atts['max_rating']),
            'sort_by' => sanitize_text_field($atts['sort_by'])
        ));
        
        // If no reviews in database, try fetching directly from API as fallback
        if (empty($reviews)) {
            $api = new GRP_API();
            if ($api->is_connected()) {
                $account_id = get_option('grp_google_account_id', '');
                $location_id = get_option('grp_google_location_id', '');
                
                // Clean location_id - remove any prefixes
                if (!empty($location_id)) {
                    $location_id = preg_replace('#^(accounts/[^/]+/)?locations/?#', '', $location_id);
                }
                
                if (!empty($account_id) && !empty($location_id)) {
                    $api_reviews_data = $this->reviews->get_reviews(array(
                        'account_id' => $account_id,
                        'location_id' => $location_id,
                        'limit' => intval($atts['count']),
                        'min_rating' => intval($atts['min_rating']),
                        'max_rating' => intval($atts['max_rating']),
                        'sort_by' => sanitize_text_field($atts['sort_by'])
                    ));
                    
                    if (!is_wp_error($api_reviews_data) && !empty($api_reviews_data)) {
                        $reviews = $api_reviews_data;
                    }
                }
            }
        }
        
        if (empty($reviews)) {
            return $this->render_no_reviews_message();
        }
        
        // Generate unique ID for this instance
        $instance_id = 'grp-' . uniqid();

        // Generate custom CSS for creative style (legacy hook, now empty)
        $custom_css = '';

        // Render based on layout
        if ($atts['layout'] === 'carousel') {
            return $this->render_carousel($reviews, $atts, $instance_id);
        } elseif ($atts['layout'] === 'grid') {
            return $this->render_grid($reviews, $atts, $instance_id);
        } elseif ($atts['layout'] === 'grid_carousel') {
            return $this->render_grid_carousel($reviews, $atts, $instance_id);
        } else {
            return $this->render_list($reviews, $atts, $instance_id);
        }
    }

    /**
     * Build inline CSS variables for styles and layout.
     */
    private function build_style_vars($atts) {
        $style_vars = array();

        // Layout variables
        $style_vars[] = '--grp-cols-desktop:' . intval($atts['cols_desktop']);
        $style_vars[] = '--grp-cols-tablet:' . intval($atts['cols_tablet']);
        $style_vars[] = '--grp-cols-mobile:' . intval($atts['cols_mobile']);
        $style_vars[] = '--grp-cols:' . intval($atts['cols_desktop']);
        $style_vars[] = '--grp-gap:' . intval($atts['gap']) . 'px';

        // Style customizations
        if (!empty($atts['custom_text_color'])) {
            $style_vars[] = '--grp-text-color:' . esc_attr($atts['custom_text_color']);
            $style_vars[] = '--grp-text:' . esc_attr($atts['custom_text_color']);
        }
        if (!empty($atts['custom_background_color'])) {
            $style_vars[] = '--grp-card-bg:' . esc_attr($atts['custom_background_color']);
            $style_vars[] = '--grp-card_background:' . esc_attr($atts['custom_background_color']);
        }
        if (!empty($atts['custom_border_color'])) {
            $style_vars[] = '--grp-border-color:' . esc_attr($atts['custom_border_color']);
            $style_vars[] = '--grp-border:' . esc_attr($atts['custom_border_color']);
        }
        if (!empty($atts['custom_accent_color'])) {
            $style_vars[] = '--grp-accent-color:' . esc_attr($atts['custom_accent_color']);
            $style_vars[] = '--grp-accent:' . esc_attr($atts['custom_accent_color']);
        }
        if (!empty($atts['custom_star_color'])) {
            $style_vars[] = '--grp-star-color:' . esc_attr($atts['custom_star_color']);
            $style_vars[] = '--grp-star:' . esc_attr($atts['custom_star_color']);
        }
        if (!empty($atts['custom_font_size'])) {
            $style_vars[] = '--grp-font-size:' . intval($atts['custom_font_size']) . 'px';
            $style_vars[] = '--grp-body-size:' . intval($atts['custom_font_size']) . 'px';
        }
        if (!empty($atts['custom_name_font_size'])) {
            $style_vars[] = '--grp-name-font-size:' . intval($atts['custom_name_font_size']) . 'px';
            $style_vars[] = '--grp-name-size:' . intval($atts['custom_name_font_size']) . 'px';
        }
        if (isset($atts['custom_padding']) && $atts['custom_padding'] !== '') {
            $style_vars[] = '--grp-card-padding:' . intval($atts['custom_padding']) . 'px';
        }
        if (isset($atts['custom_border_radius']) && $atts['custom_border_radius'] !== '') {
            $style_vars[] = '--grp-card-radius:' . intval($atts['custom_border_radius']) . 'px';
        }
        if (!empty($atts['custom_box_shadow'])) {
            $style_vars[] = '--grp-card-shadow:' . esc_attr($atts['custom_box_shadow']);
        }
        if (!empty($atts['custom_avatar_size'])) {
            $style_vars[] = '--grp-avatar-size:' . intval($atts['custom_avatar_size']) . 'px';
        }
        if (!empty($atts['custom_text_align'])) {
            $align = strtolower($atts['custom_text_align']);
            if (in_array($align, array('left', 'center', 'right'), true)) {
                $style_vars[] = '--grp-text-align:' . $align;
                $style_vars[] = '--grp-meta-justify:' . ($align === 'center' ? 'center' : ($align === 'right' ? 'flex-end' : 'flex-start'));
                $style_vars[] = '--grp-meta-align:' . ($align === 'center' ? 'center' : ($align === 'right' ? 'flex-end' : 'flex-start'));
            }
        }

        // Arrow styling
        if (!empty($atts['arrow_size'])) {
            $style_vars[] = '--grp-arrow-size:' . intval($atts['arrow_size']) . 'px';
        }
        if (!empty($atts['arrow_icon_size'])) {
            $style_vars[] = '--grp-arrow-icon-size:' . intval($atts['arrow_icon_size']) . 'px';
        }
        if (!empty($atts['arrow_icon_color'])) {
            $style_vars[] = '--grp-arrow-icon-color:' . esc_attr($atts['arrow_icon_color']);
            $style_vars[] = '--grp-arrow-color:' . esc_attr($atts['arrow_icon_color']);
        }
        if (!empty($atts['arrow_background_color'])) {
            $style_vars[] = '--grp-arrow-bg:' . esc_attr($atts['arrow_background_color']);
            $style_vars[] = '--grp-arrow-background-color:' . esc_attr($atts['arrow_background_color']);
        }
        if (!empty($atts['arrow_hover_background_color'])) {
            $style_vars[] = '--grp-arrow-hover-bg:' . esc_attr($atts['arrow_hover_background_color']);
            $style_vars[] = '--grp-arrow-hover-background-color:' . esc_attr($atts['arrow_hover_background_color']);
        }
        if (isset($atts['arrow_border_radius'])) {
            $style_vars[] = '--grp-arrow-radius:' . intval($atts['arrow_border_radius']) . '%';
            $style_vars[] = '--grp-arrow-border-radius:' . intval($atts['arrow_border_radius']) . '%';
        }
        if (isset($atts['arrow_horizontal_position'])) {
            $style_vars[] = '--grp-arrow-horizontal:' . intval($atts['arrow_horizontal_position']) . 'px';
        }
        if (isset($atts['arrow_vertical_position'])) {
            $style_vars[] = '--grp-arrow-vertical:' . intval($atts['arrow_vertical_position']) . 'px';
        }
        if (!empty($atts['arrow_box_shadow'])) {
            $style_vars[] = '--grp-arrow-box-shadow:' . esc_attr($atts['arrow_box_shadow']);
        }

        // Dot styling
        if (!empty($atts['dot_color'])) {
            $style_vars[] = '--grp-dot-color:' . esc_attr($atts['dot_color']);
        }
        if (!empty($atts['dot_active_color'])) {
            $style_vars[] = '--grp-dot-active-color:' . esc_attr($atts['dot_active_color']);
        }
        if (!empty($atts['dot_size'])) {
            $style_vars[] = '--grp-dot-size:' . intval($atts['dot_size']) . 'px';
        }
        if (isset($atts['dot_spacing'])) {
            $style_vars[] = '--grp-dot-spacing:' . intval($atts['dot_spacing']) . 'px';
        }
        if (isset($atts['dot_border_radius'])) {
            $style_vars[] = '--grp-dot-radius:' . intval($atts['dot_border_radius']) . '%';
        }

        // Creative style variables (use CSS vars on wrapper for preview/frontend parity)
        if (isset($atts['style']) && $atts['style'] === 'creative') {
            $bg_data = (isset($atts['creative_background']) && is_array($atts['creative_background'])) ? $atts['creative_background'] : array();
            $gradient_type = isset($atts['creative_gradient_type']) ? $atts['creative_gradient_type'] : 'linear';
            $start_color = isset($atts['creative_gradient_start']) ? $atts['creative_gradient_start'] : '#4285F4';
            $end_color = isset($atts['creative_gradient_end']) ? $atts['creative_gradient_end'] : '#EA4335';
            $angle = isset($atts['creative_gradient_angle']) ? intval($atts['creative_gradient_angle']) : 135;
            $start_pos = 0;
            $end_pos = 100;

            if (!empty($bg_data)) {
                if (!empty($bg_data['type'])) {
                    $gradient_type = $bg_data['type'];
                }
                if (!empty($bg_data['start_color'])) {
                    $start_color = $bg_data['start_color'];
                }
                if (!empty($bg_data['end_color'])) {
                    $end_color = $bg_data['end_color'];
                }
                if (!empty($bg_data['color'])) {
                    $start_color = $bg_data['color'];
                }
                if (!empty($bg_data['color_b'])) {
                    $end_color = $bg_data['color_b'];
                }
                if (!empty($bg_data['angle'])) {
                    $angle = intval($bg_data['angle']);
                }
                if (!empty($bg_data['gradient_angle']['size'])) {
                    $angle = intval($bg_data['gradient_angle']['size']);
                }
                if (!empty($bg_data['color_stop']['size'])) {
                    $start_pos = intval($bg_data['color_stop']['size']);
                }
                if (!empty($bg_data['color_b_stop']['size'])) {
                    $end_pos = intval($bg_data['color_b_stop']['size']);
                }
            }

            $gradient = $gradient_type === 'radial'
                ? 'radial-gradient(circle, ' . $start_color . ' ' . $start_pos . '%, ' . $end_color . ' ' . $end_pos . '%)'
                : 'linear-gradient(' . $angle . 'deg, ' . $start_color . ' ' . $start_pos . '%, ' . $end_color . ' ' . $end_pos . '%)';

            $style_vars[] = '--grp-card-bg:' . esc_attr($gradient);
            if (!empty($atts['creative_text_color'])) {
                $style_vars[] = '--grp-text-color:' . esc_attr($atts['creative_text_color']);
                $style_vars[] = '--grp-text:' . esc_attr($atts['creative_text_color']);
            }
            if (!empty($atts['creative_date_color'])) {
                $style_vars[] = '--grp-date-color:' . esc_attr($atts['creative_date_color']);
            }
            if (!empty($atts['creative_star_color'])) {
                $style_vars[] = '--grp-star-color:' . esc_attr($atts['creative_star_color']);
                $style_vars[] = '--grp-star:' . esc_attr($atts['creative_star_color']);
            }
            if (!empty($atts['creative_avatar_size'])) {
                $style_vars[] = '--grp-avatar-size:' . intval($atts['creative_avatar_size']) . 'px';
            }
            if (!empty($atts['creative_star_size'])) {
                $style_vars[] = '--grp-star-size:' . intval($atts['creative_star_size']) . 'px';
            }
            if (!empty($atts['creative_border_radius_value'])) {
                $style_vars[] = '--grp-card-radius:' . intval($atts['creative_border_radius_value']) . 'px';
            } elseif (!empty($atts['creative_border_radius']) && is_array($atts['creative_border_radius'])) {
                $unit = isset($atts['creative_border_radius']['unit']) ? $atts['creative_border_radius']['unit'] : 'px';
                $top = isset($atts['creative_border_radius']['top']) ? $atts['creative_border_radius']['top'] : '16';
                $right = isset($atts['creative_border_radius']['right']) ? $atts['creative_border_radius']['right'] : $top;
                $bottom = isset($atts['creative_border_radius']['bottom']) ? $atts['creative_border_radius']['bottom'] : $top;
                $left = isset($atts['creative_border_radius']['left']) ? $atts['creative_border_radius']['left'] : $top;
                $style_vars[] = '--grp-card-radius:' . $top . $unit . ' ' . $right . $unit . ' ' . $bottom . $unit . ' ' . $left . $unit;
            }
            if (isset($atts['creative_glass_effect']) && $atts['creative_glass_effect'] === 'yes') {
                $style_vars[] = '--grp-card-bg:rgba(255, 255, 255, 0.25)';
                $style_vars[] = '--grp-border-color:rgba(255, 255, 255, 0.3)';
                $style_vars[] = '--grp-creative-blur:20px';
            } else {
                $style_vars[] = '--grp-creative-blur:0px';
            }
        }

        return implode(';', $style_vars);
    }

    private function get_arrow_icons($atts) {
        $icon = isset($atts['arrow_icon']) ? strtolower(trim($atts['arrow_icon'])) : '';
        switch ($icon) {
            case 'double':
                return array('prev' => '«', 'next' => '»');
            case 'arrow':
                return array('prev' => '←', 'next' => '→');
            case 'angle':
                return array('prev' => '❮', 'next' => '❯');
            case 'caret':
                return array('prev' => '‹', 'next' => '›');
            case 'chevron':
            default:
                return array('prev' => '‹', 'next' => '›');
        }
    }

    private function render_dots($reviews, $atts) {
        $cols = isset($atts['cols_desktop']) && intval($atts['cols_desktop']) > 0 ? intval($atts['cols_desktop']) : 3;
        $count = is_array($reviews) ? count($reviews) : 0;
        $pages = max(1, (int) ceil($count / $cols));

        $dots = array();
        for ($i = 0; $i < $pages; $i++) {
            $active = $i === 0 ? ' active' : '';
            $dots[] = '<span class="grp-dot' . $active . '" data-index="' . esc_attr($i) . '"></span>';
        }

        return '<div class="grp-carousel-dots">' . implode('', $dots) . '</div>';
    }
    
    /**
     * Render carousel layout
     */
    private function render_carousel($reviews, $atts, $instance_id) {
        $style_class = 'grp-style-' . sanitize_html_class($atts['style']);
        $theme_class = 'grp-theme-' . sanitize_html_class($atts['theme']);
        $layout_class = 'grp-layout-carousel';
        $responsive_class = $atts['responsive'] ? 'grp-responsive' : '';
        $height_class = $atts['consistent_height'] === 'true' ? 'grp-consistent-height' : '';
        $custom_class = !empty($atts['class']) ? sanitize_html_class($atts['class']) : '';

        $classes = array_filter(array(
            'grp-reviews',
            $style_class,
            $theme_class,
            $layout_class,
            $responsive_class,
            $height_class,
            $custom_class
        ));
        
        $class_string = implode(' ', $classes);
        
        $carousel_options = array(
            'autoplay' => $atts['autoplay'],
            'speed' => intval($atts['speed']),
            'dots' => $atts['dots'],
            'arrows' => $atts['arrows'],
            'responsive' => $atts['responsive'],
            'cols_desktop' => intval($atts['cols_desktop']),
            'cols_tablet' => intval($atts['cols_tablet']),
            'cols_mobile' => intval($atts['cols_mobile']),
            'gap' => intval($atts['gap'])
        );
        
        ob_start();
        $arrow_icons = $this->get_arrow_icons($atts);
        ?>
        <?php if (!empty($custom_css)): ?>
        <style type="text/css"><?php echo $custom_css; ?></style>
        <?php endif; ?>
        <div id="<?php echo esc_attr($instance_id); ?>"
             class="<?php echo esc_attr($class_string); ?>"
             style="<?php echo esc_attr($this->build_style_vars($atts)); ?>"
             data-options="<?php echo esc_attr(json_encode($carousel_options)); ?>">
            
            <div class="grp-carousel-frame">
                <div class="grp-carousel-viewport">
                    <div class="grp-carousel-track">
                        <?php foreach ($reviews as $index => $review): ?>
                            <div class="grp-review-item" data-index="<?php echo $index; ?>">
                                <?php echo $this->render_single_review($review, $atts); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php if ($atts['arrows']): ?>
                    <button class="grp-carousel-prev" aria-label="<?php esc_attr_e('Previous reviews', 'google-reviews-plugin'); ?>">
                        <span class="grp-arrow-left"><?php echo esc_html($arrow_icons['prev']); ?></span>
                    </button>
                    <button class="grp-carousel-next" aria-label="<?php esc_attr_e('Next reviews', 'google-reviews-plugin'); ?>">
                        <span class="grp-arrow-right"><?php echo esc_html($arrow_icons['next']); ?></span>
                    </button>
                <?php endif; ?>
            </div>
            
            <?php if ($atts['dots']): ?>
                <?php echo $this->render_dots($reviews, $atts); ?>
            <?php endif; ?>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Render list layout
     */
    private function render_list($reviews, $atts, $instance_id) {
        $style_class = 'grp-style-' . sanitize_html_class($atts['style']);
        $theme_class = 'grp-theme-' . sanitize_html_class($atts['theme']);
        $layout_class = 'grp-layout-list';
        $responsive_class = $atts['responsive'] ? 'grp-responsive' : '';
        $height_class = $atts['consistent_height'] === 'true' ? 'grp-consistent-height' : '';
        $custom_class = !empty($atts['class']) ? sanitize_html_class($atts['class']) : '';

        $classes = array_filter(array(
            'grp-reviews',
            $style_class,
            $theme_class,
            $layout_class,
            $responsive_class,
            $height_class,
            $custom_class
        ));
        
        $class_string = implode(' ', $classes);
        
        ob_start();
        ?>
        <?php if (!empty($custom_css)): ?>
        <style type="text/css"><?php echo $custom_css; ?></style>
        <?php endif; ?>
        <div id="<?php echo esc_attr($instance_id); ?>" class="<?php echo esc_attr($class_string); ?>" style="<?php echo esc_attr($this->build_style_vars($atts)); ?>">
            <div class="grp-reviews-list">
                <?php foreach ($reviews as $review): ?>
                    <div class="grp-review-item">
                        <?php echo $this->render_single_review($review, $atts); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        
        return ob_get_clean();
    }

    /**
     * Render grid layout
     */
    private function render_grid($reviews, $atts, $instance_id) {
        $style_class = 'grp-style-' . sanitize_html_class($atts['style']);
        $theme_class = 'grp-theme-' . sanitize_html_class($atts['theme']);
        $layout_class = 'grp-layout-grid';
        $responsive_class = $atts['responsive'] ? 'grp-responsive' : '';
        $height_class = $atts['consistent_height'] === 'true' ? 'grp-consistent-height' : '';
        $custom_class = !empty($atts['class']) ? sanitize_html_class($atts['class']) : '';

        $classes = array_filter(array(
            'grp-reviews',
            $style_class,
            $theme_class,
            $layout_class,
            $responsive_class,
            $height_class,
            $custom_class
        ));

        $class_string = implode(' ', $classes);

        ob_start();
        ?>
        <?php if (!empty($custom_css)): ?>
        <style type="text/css"><?php echo $custom_css; ?></style>
        <?php endif; ?>
        <div id="<?php echo esc_attr($instance_id); ?>" class="<?php echo esc_attr($class_string); ?>" style="<?php echo esc_attr($this->build_style_vars($atts)); ?>">
            <div class="grp-reviews-grid">
                <?php foreach ($reviews as $review): ?>
                    <div class="grp-review-item">
                        <?php echo $this->render_single_review($review, $atts); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render grid carousel layout (multi items per view)
     */
    private function render_grid_carousel($reviews, $atts, $instance_id) {
        $style_class = 'grp-style-' . sanitize_html_class($atts['style']);
        $theme_class = 'grp-theme-' . sanitize_html_class($atts['theme']);
        $layout_class = 'grp-layout-grid_carousel';
        $responsive_class = $atts['responsive'] ? 'grp-responsive' : '';
        $height_class = $atts['consistent_height'] === 'true' ? 'grp-consistent-height' : '';
        $custom_class = !empty($atts['class']) ? sanitize_html_class($atts['class']) : '';

        $classes = array_filter(array(
            'grp-reviews',
            $style_class,
            $theme_class,
            $layout_class,
            $responsive_class,
            $height_class,
            $custom_class
        ));

        $class_string = implode(' ', $classes);

        $carousel_options = array(
            'autoplay' => $atts['autoplay'],
            'speed' => intval($atts['speed']),
            'dots' => $atts['dots'],
            'arrows' => $atts['arrows'],
            'responsive' => $atts['responsive'],
            'cols_desktop' => intval($atts['cols_desktop']),
            'cols_tablet' => intval($atts['cols_tablet']),
            'cols_mobile' => intval($atts['cols_mobile']),
            'gap' => intval($atts['gap'])
        );

        ob_start();
        $arrow_icons = $this->get_arrow_icons($atts);
        ?>
        <?php if (!empty($custom_css)): ?>
        <style type="text/css"><?php echo $custom_css; ?></style>
        <?php endif; ?>
        <div id="<?php echo esc_attr($instance_id); ?>"
             class="<?php echo esc_attr($class_string); ?>"
             style="<?php echo esc_attr($this->build_style_vars($atts)); ?>"
             data-options="<?php echo esc_attr(json_encode($carousel_options)); ?>">

            <div class="grp-grid-carousel-viewport">
                <div class="grp-grid-carousel-track">
                    <?php foreach ($reviews as $index => $review): ?>
                        <div class="grp-review-item" data-index="<?php echo $index; ?>">
                            <?php echo $this->render_single_review($review, $atts); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ($atts['arrows']): ?>
                <button class="grp-carousel-prev" aria-label="<?php esc_attr_e('Previous reviews', 'google-reviews-plugin'); ?>">
                    <span class="grp-arrow-left"><?php echo esc_html($arrow_icons['prev']); ?></span>
                </button>
                <button class="grp-carousel-next" aria-label="<?php esc_attr_e('Next reviews', 'google-reviews-plugin'); ?>">
                    <span class="grp-arrow-right"><?php echo esc_html($arrow_icons['next']); ?></span>
                </button>
            <?php endif; ?>

            <?php if ($atts['dots']): ?>
                <?php echo $this->render_dots($reviews, $atts); ?>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render single review
     */
    private function render_single_review($review, $atts) {
        // Special layout handling for modern style (avatar badge on top border)
        $is_modern_style = isset($atts['style']) && $atts['style'] === 'modern';
        $is_corporate_style = isset($atts['style']) && $atts['style'] === 'corporate';

        ob_start();
        ?>
        <div class="grp-review">
            <?php if ($is_corporate_style): ?>
                <div class="grp-review-header">
                    <span class="grp-review-header-text"><?php esc_html_e('Google Reviews', 'google-reviews-plugin'); ?></span>
                    <svg class="grp-google-logo" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                </div>

                <div class="grp-review-content">
                    <?php if ($atts['show_rating']): ?>
                        <div class="grp-review-rating" style="text-align: right;">
                            <?php echo $review['stars_html']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($review['text'])): ?>
                        <div class="grp-review-text">
                            <?php echo wp_kses_post($review['text']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="grp-review-meta">
                        <?php if (!empty($atts['show_avatar']) && filter_var($atts['show_avatar'], FILTER_VALIDATE_BOOLEAN) && !empty($review['author_photo'])): ?>
                            <div class="grp-review-avatar">
                                <img src="<?php echo esc_url($review['author_photo']); ?>"
                                     alt="<?php echo esc_attr($review['author_name']); ?>"
                                     loading="lazy">
                            </div>
                        <?php endif; ?>

                        <div class="grp-author-name"><?php echo esc_html($review['author_name']); ?></div>
                    </div>

                    <?php if ($atts['show_reply'] && !empty($review['reply']['text'])): ?>
                        <div class="grp-review-reply">
                            <div class="grp-reply-header">
                                <strong><?php esc_html_e('Business Response', 'google-reviews-plugin'); ?></strong>
                                <span class="grp-reply-date"><?php echo esc_html($review['reply']['time']); ?></span>
                            </div>
                            <div class="grp-reply-text">
                                <?php echo wp_kses_post($review['reply']['text']); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="grp-review-footer">
                    <?php if ($atts['show_date']): ?>
                        <span class="grp-review-date"><?php echo esc_html($review['time_formatted']); ?></span>
                    <?php endif; ?>
                    <span class="grp-verified-badge"><?php esc_html_e('Verified', 'google-reviews-plugin'); ?></span>
                </div>
            <?php else: ?>
                <?php
                // For modern style, render avatar as a direct child of .grp-review so CSS
                // can pin it to the top border independent of meta/content height.
                if (
                    $is_modern_style
                    && !empty($atts['show_avatar'])
                    && filter_var($atts['show_avatar'], FILTER_VALIDATE_BOOLEAN)
                    && !empty($review['author_photo'])
                ): ?>
                    <div class="grp-review-avatar">
                        <img src="<?php echo esc_url($review['author_photo']); ?>"
                             alt="<?php echo esc_attr($review['author_name']); ?>"
                             loading="lazy">
                    </div>
                <?php endif; ?>

                <?php if ($atts['show_rating']): ?>
                    <div class="grp-review-rating">
                        <?php echo $review['stars_html']; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($review['text'])): ?>
                    <div class="grp-review-text">
                        <?php echo wp_kses_post($review['text']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="grp-review-meta">
                    <?php
                    // For non-modern styles, keep the avatar inside the meta row as before.
                    if (
                        !$is_modern_style
                        && !empty($atts['show_avatar'])
                        && filter_var($atts['show_avatar'], FILTER_VALIDATE_BOOLEAN)
                        && !empty($review['author_photo'])
                    ): ?>
                        <div class="grp-review-avatar">
                            <img src="<?php echo esc_url($review['author_photo']); ?>"
                                 alt="<?php echo esc_attr($review['author_name']); ?>"
                                 loading="lazy">
                        </div>
                    <?php endif; ?>
                    
                    <div class="grp-review-author">
                        <span class="grp-author-name"><?php echo esc_html($review['author_name']); ?></span>
                        
                        <?php if ($atts['show_date']): ?>
                            <span class="grp-review-date"><?php echo esc_html($review['time_formatted']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($atts['show_reply'] && !empty($review['reply']['text'])): ?>
                    <div class="grp-review-reply">
                        <div class="grp-reply-header">
                            <strong><?php esc_html_e('Business Response', 'google-reviews-plugin'); ?></strong>
                            <span class="grp-reply-date"><?php echo esc_html($review['reply']['time']); ?></span>
                        </div>
                        <div class="grp-reply-text">
                            <?php echo wp_kses_post($review['reply']['text']); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Render no reviews message
     */
    private function render_no_reviews_message() {
        ob_start();
        ?>
        <div class="grp-no-reviews">
            <p><?php esc_html_e('No reviews available at the moment.', 'google-reviews-plugin'); ?></p>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Get shortcode documentation
     */
    public function get_shortcode_docs() {
        return array(
            'google_reviews' => array(
                'description' => __('Display Google Business reviews', 'google-reviews-plugin'),
                'attributes' => array(
                    'style' => array(
                        'description' => __('Review display style', 'google-reviews-plugin'),
                        'default' => 'modern',
                        'options' => array('modern', 'classic', 'minimal', 'corporate', 'creative')
                    ),
                    'layout' => array(
                        'description' => __('Display layout', 'google-reviews-plugin'),
                        'default' => 'carousel',
                        'options' => array('carousel', 'list')
                    ),
                    'count' => array(
                        'description' => __('Number of reviews to display', 'google-reviews-plugin'),
                        'default' => '5',
                        'type' => 'number'
                    ),
                    'min_rating' => array(
                        'description' => __('Minimum star rating to display', 'google-reviews-plugin'),
                        'default' => '1',
                        'type' => 'number',
                        'options' => array('1', '2', '3', '4', '5')
                    ),
                    'max_rating' => array(
                        'description' => __('Maximum star rating to display', 'google-reviews-plugin'),
                        'default' => '5',
                        'type' => 'number',
                        'options' => array('1', '2', '3', '4', '5')
                    ),
                    'sort_by' => array(
                        'description' => __('Sort reviews by', 'google-reviews-plugin'),
                        'default' => 'newest',
                        'options' => array('newest', 'oldest', 'highest_rating', 'lowest_rating')
                    ),
                    'show_avatar' => array(
                        'description' => __('Show reviewer avatar', 'google-reviews-plugin'),
                        'default' => 'true',
                        'type' => 'boolean'
                    ),
                    'show_date' => array(
                        'description' => __('Show review date', 'google-reviews-plugin'),
                        'default' => 'true',
                        'type' => 'boolean'
                    ),
                    'show_rating' => array(
                        'description' => __('Show star rating', 'google-reviews-plugin'),
                        'default' => 'true',
                        'type' => 'boolean'
                    ),
                    'show_reply' => array(
                        'description' => __('Show business replies', 'google-reviews-plugin'),
                        'default' => 'true',
                        'type' => 'boolean'
                    ),
                    'autoplay' => array(
                        'description' => __('Enable carousel autoplay', 'google-reviews-plugin'),
                        'default' => 'true',
                        'type' => 'boolean'
                    ),
                    'speed' => array(
                        'description' => __('Carousel speed in milliseconds', 'google-reviews-plugin'),
                        'default' => '5000',
                        'type' => 'number'
                    ),
                    'dots' => array(
                        'description' => __('Show carousel dots', 'google-reviews-plugin'),
                        'default' => 'true',
                        'type' => 'boolean'
                    ),
                    'arrows' => array(
                        'description' => __('Show carousel arrows', 'google-reviews-plugin'),
                        'default' => 'true',
                        'type' => 'boolean'
                    ),
                    'responsive' => array(
                        'description' => __('Enable responsive design', 'google-reviews-plugin'),
                        'default' => 'true',
                        'type' => 'boolean'
                    ),
                    'class' => array(
                        'description' => __('Additional CSS classes', 'google-reviews-plugin'),
                        'default' => '',
                        'type' => 'text'
                    ),
                    'id' => array(
                        'description' => __('Element ID', 'google-reviews-plugin'),
                        'default' => '',
                        'type' => 'text'
                    )
                )
            )
        );
    }
}