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
        
        // Get reviews
        $reviews = $this->reviews->get_stored_reviews(array(
            'limit' => intval($atts['count']),
            'min_rating' => intval($atts['min_rating']),
            'max_rating' => intval($atts['max_rating']),
            'sort_by' => sanitize_text_field($atts['sort_by'])
        ));
        
        if (empty($reviews)) {
            return $this->render_no_reviews_message();
        }
        
        // Generate unique ID for this instance
        $instance_id = 'grp-' . uniqid();
        
        // Render based on layout
        if ($atts['layout'] === 'carousel') {
            return $this->render_carousel($reviews, $atts, $instance_id);
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
        $custom_class = !empty($atts['class']) ? sanitize_html_class($atts['class']) : '';
        
        $classes = array_filter(array(
            'grp-reviews',
            $style_class,
            $theme_class,
            $layout_class,
            $responsive_class,
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
        $custom_class = !empty($atts['class']) ? sanitize_html_class($atts['class']) : '';
        
        $classes = array_filter(array(
            'grp-reviews',
            $style_class,
            $theme_class,
            $layout_class,
            $responsive_class,
            $custom_class
        ));
        
        $class_string = implode(' ', $classes);
        
        ob_start();
        ?>
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