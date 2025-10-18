<?php
/**
 * Elementor widget class
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Elementor_Widget extends \Elementor\Widget_Base {
    
    /**
     * Get widget name
     */
    public function get_name() {
        return 'grp-reviews';
    }
    
    /**
     * Get widget title
     */
    public function get_title() {
        return __('Google Reviews', 'google-reviews-plugin');
    }
    
    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-star';
    }
    
    /**
     * Get widget categories
     */
    public function get_categories() {
        return ['google-reviews'];
    }
    
    /**
     * Get widget keywords
     */
    public function get_keywords() {
        return ['google', 'reviews', 'testimonials', 'ratings'];
    }
    
    /**
     * Register widget controls
     */
    protected function _register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            array(
                'label' => __('Content', 'google-reviews-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );
        
        $this->add_control(
            'style',
            array(
                'label' => __('Style', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'modern',
                'options' => $this->get_style_options(),
            )
        );

        $this->add_control(
            'theme',
            array(
                'label' => __('Theme', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'light',
                'options' => array(
                    'light' => __('Light', 'google-reviews-plugin'),
                    'dark' => __('Dark', 'google-reviews-plugin'),
                    'auto' => __('Auto', 'google-reviews-plugin'),
                ),
            )
        );
        
        $this->add_control(
            'layout',
            array(
                'label' => __('Layout', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'carousel',
                'options' => array(
                    'carousel' => __('Carousel', 'google-reviews-plugin'),
                    'list' => __('List', 'google-reviews-plugin'),
                ),
            )
        );
        
        $this->add_control(
            'count',
            array(
                'label' => __('Number of Reviews', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 5,
                'min' => 1,
                'max' => 50,
            )
        );
        
        $this->add_control(
            'min_rating',
            array(
                'label' => __('Minimum Rating', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '1',
                'options' => array(
                    '1' => '1 Star',
                    '2' => '2 Stars',
                    '3' => '3 Stars',
                    '4' => '4 Stars',
                    '5' => '5 Stars',
                ),
            )
        );
        
        $this->add_control(
            'max_rating',
            array(
                'label' => __('Maximum Rating', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '5',
                'options' => array(
                    '1' => '1 Star',
                    '2' => '2 Stars',
                    '3' => '3 Stars',
                    '4' => '4 Stars',
                    '5' => '5 Stars',
                ),
            )
        );
        
        $this->add_control(
            'sort_by',
            array(
                'label' => __('Sort By', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'newest',
                'options' => array(
                    'newest' => __('Newest First', 'google-reviews-plugin'),
                    'oldest' => __('Oldest First', 'google-reviews-plugin'),
                    'highest_rating' => __('Highest Rating', 'google-reviews-plugin'),
                    'lowest_rating' => __('Lowest Rating', 'google-reviews-plugin'),
                ),
            )
        );
        
        $this->end_controls_section();
        
        // Display Options Section
        $this->start_controls_section(
            'display_section',
            array(
                'label' => __('Display Options', 'google-reviews-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );
        
        $this->add_control(
            'show_avatar',
            array(
                'label' => __('Show Avatar', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'google-reviews-plugin'),
                'label_off' => __('No', 'google-reviews-plugin'),
                'return_value' => 'true',
                'default' => 'true',
            )
        );
        
        $this->add_control(
            'show_date',
            array(
                'label' => __('Show Date', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'google-reviews-plugin'),
                'label_off' => __('No', 'google-reviews-plugin'),
                'return_value' => 'true',
                'default' => 'true',
            )
        );
        
        $this->add_control(
            'show_rating',
            array(
                'label' => __('Show Rating', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'google-reviews-plugin'),
                'label_off' => __('No', 'google-reviews-plugin'),
                'return_value' => 'true',
                'default' => 'true',
            )
        );
        
        $this->add_control(
            'show_reply',
            array(
                'label' => __('Show Business Reply', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'google-reviews-plugin'),
                'label_off' => __('No', 'google-reviews-plugin'),
                'return_value' => 'true',
                'default' => 'true',
            )
        );
        
        $this->end_controls_section();
        
        // Carousel Options Section
        $this->start_controls_section(
            'carousel_section',
            array(
                'label' => __('Carousel Options', 'google-reviews-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' => array(
                    'layout' => 'carousel',
                ),
            )
        );
        
        $this->add_control(
            'autoplay',
            array(
                'label' => __('Autoplay', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'google-reviews-plugin'),
                'label_off' => __('No', 'google-reviews-plugin'),
                'return_value' => 'true',
                'default' => 'true',
            )
        );
        
        $this->add_control(
            'speed',
            array(
                'label' => __('Speed (ms)', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 5000,
                'min' => 1000,
                'max' => 10000,
                'step' => 500,
                'condition' => array(
                    'autoplay' => 'true',
                ),
            )
        );
        
        $this->add_control(
            'dots',
            array(
                'label' => __('Show Dots', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'google-reviews-plugin'),
                'label_off' => __('No', 'google-reviews-plugin'),
                'return_value' => 'true',
                'default' => 'true',
            )
        );
        
        $this->add_control(
            'arrows',
            array(
                'label' => __('Show Arrows', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'google-reviews-plugin'),
                'label_off' => __('No', 'google-reviews-plugin'),
                'return_value' => 'true',
                'default' => 'true',
            )
        );
        
        $this->end_controls_section();
        
        // Style Section
        $this->start_controls_section(
            'style_section',
            array(
                'label' => __('Style', 'google-reviews-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );
        
        $this->add_control(
            'text_color',
            array(
                'label' => __('Text Color', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .grp-review-text' => 'color: {{VALUE}}',
                ),
            )
        );
        
        $this->add_control(
            'background_color',
            array(
                'label' => __('Background Color', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .grp-review' => 'background-color: {{VALUE}}',
                ),
            )
        );
        
        $this->add_control(
            'border_color',
            array(
                'label' => __('Border Color', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .grp-review' => 'border-color: {{VALUE}}',
                ),
            )
        );
        
        $this->add_control(
            'border_radius',
            array(
                'label' => __('Border Radius', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .grp-review' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        
        $this->add_control(
            'padding',
            array(
                'label' => __('Padding', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .grp-review' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        
        $this->end_controls_section();
    }
    
    /**
     * Render widget output
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $shortcode_atts = array(
            'style' => $settings['style'],
            'theme' => isset($settings['theme']) ? $settings['theme'] : 'light',
            'layout' => $settings['layout'],
            'count' => $settings['count'],
            'min_rating' => $settings['min_rating'],
            'max_rating' => $settings['max_rating'],
            'sort_by' => $settings['sort_by'],
            'show_avatar' => $settings['show_avatar'],
            'show_date' => $settings['show_date'],
            'show_rating' => $settings['show_rating'],
            'show_reply' => $settings['show_reply'],
            'autoplay' => $settings['autoplay'],
            'speed' => $settings['speed'],
            'dots' => $settings['dots'],
            'arrows' => $settings['arrows'],
            'class' => 'grp-elementor-widget'
        );
        
        $shortcode = new GRP_Shortcode();
        echo $shortcode->render_shortcode($shortcode_atts);
    }
    
    /**
     * Get style options
     */
    private function get_style_options() {
        $styles = new GRP_Styles();
        $available_styles = $styles->get_styles();
        
        $options = array();
        foreach ($available_styles as $key => $style) {
            $options[$key] = $style['name'];
        }
        
        return $options;
    }
}