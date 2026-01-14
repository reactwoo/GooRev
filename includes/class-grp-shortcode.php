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
            'creative_avatar_size' => 80,
            'creative_star_size' => 32,
            'creative_text_color' => '#ffffff',
            'creative_date_color' => '#ffffff',
            'creative_star_color' => '#FFD700',
            'creative_glass_effect' => 'no',
            'creative_box_shadow' => array(),
            'creative_border' => array(),
            'creative_border_radius' => array(),
            'creative_avatar_size' => 80,
            'creative_star_size' => 32,
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

        // Generate custom CSS for creative style (Elementor handles gradients)
        $custom_css = '';
        if ($atts['style'] === 'creative') {
            // Glass effect (Apple-style)
            if (isset($atts['creative_glass_effect']) && $atts['creative_glass_effect'] === 'yes') {
                $custom_css .= '#' . esc_attr($instance_id) . ' .grp-style-creative .grp-review { background: rgba(255, 255, 255, 0.25) !important; border: 1px solid rgba(255, 255, 255, 0.3) !important; backdrop-filter: blur(20px) !important; -webkit-backdrop-filter: blur(20px) !important; }';
            }

            // Creative text colors
            if (isset($atts['creative_text_color'])) {
                $custom_css .= '#' . esc_attr($instance_id) . ' .grp-style-creative .grp-review-text, #' . esc_attr($instance_id) . ' .grp-style-creative .grp-author-name { color: ' . esc_attr($atts['creative_text_color']) . ' !important; }';
            }
            if (isset($atts['creative_date_color'])) {
                $custom_css .= '#' . esc_attr($instance_id) . ' .grp-style-creative .grp-review-date { color: ' . esc_attr($atts['creative_date_color']) . ' !important; }';
            }
            if (isset($atts['creative_star_color'])) {
                $custom_css .= '#' . esc_attr($instance_id) . ' .grp-style-creative .grp-star { color: ' . esc_attr($atts['creative_star_color']) . ' !important; }';
            }

            // Creative sizes
            if (isset($atts['creative_avatar_size'])) {
                $avatar_size = intval($atts['creative_avatar_size']);
                $custom_css .= '#' . esc_attr($instance_id) . ' .grp-style-creative .grp-review-avatar img { width: ' . $avatar_size . 'px !important; height: ' . $avatar_size . 'px !important; }';
            }
            if (isset($atts['creative_star_size'])) {
                $star_size = intval($atts['creative_star_size']);
                $custom_css .= '#' . esc_attr($instance_id) . ' .grp-style-creative .grp-star { font-size: ' . $star_size . 'px !important; }';
            }
        }

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
            'responsive' => $atts['responsive']
        );
        
        ob_start();
        ?>
        <?php if (!empty($custom_css)): ?>
        <style type="text/css"><?php echo $custom_css; ?></style>
        <?php endif; ?>
        <div id="<?php echo esc_attr($instance_id); ?>"
             class="<?php echo esc_attr($class_string); ?>"
             data-options="<?php echo esc_attr(json_encode($carousel_options)); ?>">
            
            <div class="grp-carousel-container">
                <div class="grp-carousel-wrapper">
                    <?php foreach ($reviews as $index => $review): ?>
                        <div class="grp-review-item" data-index="<?php echo $index; ?>">
                            <?php echo $this->render_single_review($review, $atts); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($atts['arrows']): ?>
                    <button class="grp-carousel-prev" aria-label="<?php esc_attr_e('Previous reviews', 'google-reviews-plugin'); ?>">
                        <span class="grp-arrow-left">‹</span>
                    </button>
                    <button class="grp-carousel-next" aria-label="<?php esc_attr_e('Next reviews', 'google-reviews-plugin'); ?>">
                        <span class="grp-arrow-right">›</span>
                    </button>
                <?php endif; ?>
            </div>
            
            <?php if ($atts['dots']): ?>
                <div class="grp-carousel-dots">
                    <?php foreach ($reviews as $index => $review): ?>
                        <button class="grp-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                                data-index="<?php echo $index; ?>"
                                aria-label="<?php printf(esc_attr__('Go to review %d', 'google-reviews-plugin'), $index + 1); ?>">
                        </button>
                    <?php endforeach; ?>
                </div>
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
        <div id="<?php echo esc_attr($instance_id); ?>" class="<?php echo esc_attr($class_string); ?>">
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

        $style_inline = sprintf(
            '--grp-cols-desktop:%d;--grp-cols-tablet:%d;--grp-cols-mobile:%d;--grp-gap:%dpx;',
            intval($atts['cols_desktop']),
            intval($atts['cols_tablet']),
            intval($atts['cols_mobile']),
            intval($atts['gap'])
        );

        ob_start();
        ?>
        <?php if (!empty($custom_css)): ?>
        <style type="text/css"><?php echo $custom_css; ?></style>
        <?php endif; ?>
        <div id="<?php echo esc_attr($instance_id); ?>" class="<?php echo esc_attr($class_string); ?>" style="<?php echo esc_attr($style_inline); ?>">
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
        ?>
        <?php if (!empty($custom_css)): ?>
        <style type="text/css"><?php echo $custom_css; ?></style>
        <?php endif; ?>
        <div id="<?php echo esc_attr($instance_id); ?>"
             class="<?php echo esc_attr($class_string); ?>"
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
                    <span class="grp-arrow-left">‹</span>
                </button>
                <button class="grp-carousel-next" aria-label="<?php esc_attr_e('Next reviews', 'google-reviews-plugin'); ?>">
                    <span class="grp-arrow-right">›</span>
                </button>
            <?php endif; ?>

            <?php if ($atts['dots']): ?>
                <div class="grp-carousel-dots"></div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render single review
     */
    private function render_single_review($review, $atts) {
        ob_start();
        ?>
        <div class="grp-review">
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
                <?php if ($atts['show_avatar'] && !empty($review['author_photo'])): ?>
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