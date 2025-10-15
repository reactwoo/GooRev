<?php
/**
 * WordPress widget class
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Widget extends WP_Widget {
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'grp_widget',
            __('Google Reviews', 'google-reviews-plugin'),
            array(
                'description' => __('Display Google Business reviews', 'google-reviews-plugin'),
                'classname' => 'grp-widget'
            )
        );
    }
    
    /**
     * Widget output
     */
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        // Get shortcode attributes
        $shortcode_atts = array(
            'style' => $instance['style'] ?? 'modern',
            'layout' => $instance['layout'] ?? 'list',
            'count' => $instance['count'] ?? 5,
            'min_rating' => $instance['min_rating'] ?? 1,
            'max_rating' => $instance['max_rating'] ?? 5,
            'sort_by' => $instance['sort_by'] ?? 'newest',
            'show_avatar' => $instance['show_avatar'] ?? 'true',
            'show_date' => $instance['show_date'] ?? 'true',
            'show_rating' => $instance['show_rating'] ?? 'true',
            'show_reply' => $instance['show_reply'] ?? 'true',
            'class' => 'grp-widget-content'
        );
        
        // Render shortcode
        $shortcode = new GRP_Shortcode();
        echo $shortcode->render_shortcode($shortcode_atts);
        
        echo $args['after_widget'];
    }
    
    /**
     * Widget form
     */
    public function form($instance) {
        $defaults = array(
            'title' => '',
            'style' => 'modern',
            'layout' => 'list',
            'count' => 5,
            'min_rating' => 1,
            'max_rating' => 5,
            'sort_by' => 'newest',
            'show_avatar' => 'true',
            'show_date' => 'true',
            'show_rating' => 'true',
            'show_reply' => 'true'
        );
        
        $instance = wp_parse_args($instance, $defaults);
        
        $styles = new GRP_Styles();
        $available_styles = $styles->get_styles();
        ?>
        
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:', 'google-reviews-plugin'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('style'); ?>"><?php esc_html_e('Style:', 'google-reviews-plugin'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>">
                <?php foreach ($available_styles as $key => $style): ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($instance['style'], $key); ?>>
                        <?php echo esc_html($style['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('layout'); ?>"><?php esc_html_e('Layout:', 'google-reviews-plugin'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>">
                <option value="list" <?php selected($instance['layout'], 'list'); ?>><?php esc_html_e('List', 'google-reviews-plugin'); ?></option>
                <option value="carousel" <?php selected($instance['layout'], 'carousel'); ?>><?php esc_html_e('Carousel', 'google-reviews-plugin'); ?></option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php esc_html_e('Number of reviews:', 'google-reviews-plugin'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" min="1" max="20" value="<?php echo esc_attr($instance['count']); ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('min_rating'); ?>"><?php esc_html_e('Minimum rating:', 'google-reviews-plugin'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('min_rating'); ?>" name="<?php echo $this->get_field_name('min_rating'); ?>">
                <option value="1" <?php selected($instance['min_rating'], '1'); ?>>1 <?php esc_html_e('star', 'google-reviews-plugin'); ?></option>
                <option value="2" <?php selected($instance['min_rating'], '2'); ?>>2 <?php esc_html_e('stars', 'google-reviews-plugin'); ?></option>
                <option value="3" <?php selected($instance['min_rating'], '3'); ?>>3 <?php esc_html_e('stars', 'google-reviews-plugin'); ?></option>
                <option value="4" <?php selected($instance['min_rating'], '4'); ?>>4 <?php esc_html_e('stars', 'google-reviews-plugin'); ?></option>
                <option value="5" <?php selected($instance['min_rating'], '5'); ?>>5 <?php esc_html_e('stars', 'google-reviews-plugin'); ?></option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('sort_by'); ?>"><?php esc_html_e('Sort by:', 'google-reviews-plugin'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('sort_by'); ?>" name="<?php echo $this->get_field_name('sort_by'); ?>">
                <option value="newest" <?php selected($instance['sort_by'], 'newest'); ?>><?php esc_html_e('Newest first', 'google-reviews-plugin'); ?></option>
                <option value="oldest" <?php selected($instance['sort_by'], 'oldest'); ?>><?php esc_html_e('Oldest first', 'google-reviews-plugin'); ?></option>
                <option value="highest_rating" <?php selected($instance['sort_by'], 'highest_rating'); ?>><?php esc_html_e('Highest rating', 'google-reviews-plugin'); ?></option>
                <option value="lowest_rating" <?php selected($instance['sort_by'], 'lowest_rating'); ?>><?php esc_html_e('Lowest rating', 'google-reviews-plugin'); ?></option>
            </select>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($instance['show_avatar'], 'true'); ?> id="<?php echo $this->get_field_id('show_avatar'); ?>" name="<?php echo $this->get_field_name('show_avatar'); ?>" value="true" />
            <label for="<?php echo $this->get_field_id('show_avatar'); ?>"><?php esc_html_e('Show reviewer avatar', 'google-reviews-plugin'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($instance['show_date'], 'true'); ?> id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" value="true" />
            <label for="<?php echo $this->get_field_id('show_date'); ?>"><?php esc_html_e('Show review date', 'google-reviews-plugin'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($instance['show_rating'], 'true'); ?> id="<?php echo $this->get_field_id('show_rating'); ?>" name="<?php echo $this->get_field_name('show_rating'); ?>" value="true" />
            <label for="<?php echo $this->get_field_id('show_rating'); ?>"><?php esc_html_e('Show star rating', 'google-reviews-plugin'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($instance['show_reply'], 'true'); ?> id="<?php echo $this->get_field_id('show_reply'); ?>" name="<?php echo $this->get_field_name('show_reply'); ?>" value="true" />
            <label for="<?php echo $this->get_field_id('show_reply'); ?>"><?php esc_html_e('Show business replies', 'google-reviews-plugin'); ?></label>
        </p>
        
        <?php
    }
    
    /**
     * Update widget
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['style'] = sanitize_text_field($new_instance['style']);
        $instance['layout'] = sanitize_text_field($new_instance['layout']);
        $instance['count'] = intval($new_instance['count']);
        $instance['min_rating'] = intval($new_instance['min_rating']);
        $instance['max_rating'] = intval($new_instance['max_rating']);
        $instance['sort_by'] = sanitize_text_field($new_instance['sort_by']);
        $instance['show_avatar'] = isset($new_instance['show_avatar']) ? 'true' : 'false';
        $instance['show_date'] = isset($new_instance['show_date']) ? 'true' : 'false';
        $instance['show_rating'] = isset($new_instance['show_rating']) ? 'true' : 'false';
        $instance['show_reply'] = isset($new_instance['show_reply']) ? 'true' : 'false';
        
        return $instance;
    }
}