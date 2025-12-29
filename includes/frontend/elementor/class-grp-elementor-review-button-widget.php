<?php
/**
 * Elementor Review Button Widget
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Elementor_Review_Button_Widget extends \Elementor\Widget_Base {
    
    /**
     * Get widget name
     */
    public function get_name() {
        return 'grp-review-button';
    }
    
    /**
     * Get widget title
     */
    public function get_title() {
        return __('Review Button', 'google-reviews-plugin');
    }
    
    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-button';
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
        return ['google', 'review', 'button', 'link'];
    }
    
    /**
     * Register widget controls
     */
    protected function _register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            array(
                'label' => __('Button Settings', 'google-reviews-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );
        
        $this->add_control(
            'button_text',
            array(
                'label' => __('Button Text', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Leave us a review', 'google-reviews-plugin'),
            )
        );
        
        $this->add_control(
            'button_style',
            array(
                'label' => __('Button Style', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'default',
                'options' => array(
                    'default' => __('Default', 'google-reviews-plugin'),
                    'rounded' => __('Rounded', 'google-reviews-plugin'),
                    'outline' => __('Outline', 'google-reviews-plugin'),
                    'minimal' => __('Minimal', 'google-reviews-plugin'),
                ),
            )
        );
        
        $this->add_control(
            'button_size',
            array(
                'label' => __('Button Size', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'medium',
                'options' => array(
                    'small' => __('Small', 'google-reviews-plugin'),
                    'medium' => __('Medium', 'google-reviews-plugin'),
                    'large' => __('Large', 'google-reviews-plugin'),
                ),
            )
        );
        
        $this->add_control(
            'align',
            array(
                'label' => __('Alignment', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => array(
                    'left' => array(
                        'title' => __('Left', 'google-reviews-plugin'),
                        'icon' => 'eicon-text-align-left',
                    ),
                    'center' => array(
                        'title' => __('Center', 'google-reviews-plugin'),
                        'icon' => 'eicon-text-align-center',
                    ),
                    'right' => array(
                        'title' => __('Right', 'google-reviews-plugin'),
                        'icon' => 'eicon-text-align-right',
                    ),
                ),
                'default' => 'left',
                'toggle' => true,
            )
        );
        
        $this->end_controls_section();
        
        // Style Section
        $this->start_controls_section(
            'style_section',
            array(
                'label' => __('Button Style', 'google-reviews-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );
        
        $this->add_control(
            'text_color',
            array(
                'label' => __('Text Color', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .grp-review-button' => 'color: {{VALUE}};',
                ),
            )
        );
        
        $this->add_control(
            'background_color',
            array(
                'label' => __('Background Color', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .grp-review-button' => 'background-color: {{VALUE}};',
                ),
            )
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .grp-review-button',
            )
        );
        
        $this->add_responsive_control(
            'button_padding',
            array(
                'label' => __('Padding', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .grp-review-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .grp-review-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
        
        // Check if Review Widgets addon is enabled
        $addons = GRP_Addons::get_instance();
        if (!$addons->is_addon_enabled('review-widgets')) {
            echo '<p>' . __('Review Widgets addon is not enabled. Please enable it in the Addons page.', 'google-reviews-plugin') . '</p>';
            return;
        }
        
        $widgets = GRP_Review_Widgets::get_instance();
        
        // Build shortcode attributes
        $shortcode_atts = array(
            'text' => $settings['button_text'],
            'style' => $settings['button_style'],
            'size' => $settings['button_size'],
            'align' => $settings['align'],
        );
        
        // Add colors if set
        if (!empty($settings['text_color'])) {
            $shortcode_atts['color'] = $settings['text_color'];
        }
        if (!empty($settings['background_color'])) {
            $shortcode_atts['bg_color'] = $settings['background_color'];
        }
        
        // Render using shortcode
        echo do_shortcode('[grp_review_button ' . $this->build_shortcode_attrs($shortcode_atts) . ']');
    }
    
    /**
     * Build shortcode attributes string
     */
    private function build_shortcode_attrs($atts) {
        $attrs = array();
        foreach ($atts as $key => $value) {
            $attrs[] = $key . '="' . esc_attr($value) . '"';
        }
        return implode(' ', $attrs);
    }
}

