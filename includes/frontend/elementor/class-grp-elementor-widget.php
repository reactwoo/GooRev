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
     * Check if user has pro license
     */
    private function is_pro() {
        $license = new GRP_License();
        return $license->is_pro();
    }

    /**
     * Check if user has free license
     */
    private function is_free() {
        $license = new GRP_License();
        return $license->is_free() || !$license->has_license();
    }

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

        // Style selection - free users can choose non-creative styles
        if ($this->is_pro()) {
            $this->add_control(
                'style',
                array(
                    'label' => __('Style', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'modern',
                    'options' => $this->get_style_options(),
                )
            );
        } else {
            $this->add_control(
                'style',
                array(
                    'label' => __('Style', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'modern',
                    'options' => array(
                        'modern' => __('Modern', 'google-reviews-plugin'),
                        'classic' => __('Classic', 'google-reviews-plugin'),
                        'minimal' => __('Minimal', 'google-reviews-plugin'),
                        'corporate' => __('Corporate', 'google-reviews-plugin'),
                    ),
                    'description' => __('Creative style available in Pro version', 'google-reviews-plugin'),
                )
            );
        }

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

        // Layout selection - free users can choose basic layouts
        if ($this->is_pro()) {
            $this->add_control(
                'layout',
                array(
                    'label' => __('Layout', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'carousel',
                    'options' => array(
                        'carousel' => __('Carousel', 'google-reviews-plugin'),
                        'list' => __('List', 'google-reviews-plugin'),
                        'grid' => __('Grid', 'google-reviews-plugin'),
                        'grid_carousel' => __('Grid Carousel', 'google-reviews-plugin'),
                    ),
                )
            );
        } else {
            $this->add_control(
                'layout',
                array(
                    'label' => __('Layout', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'carousel',
                    'options' => array(
                        'carousel' => __('Carousel (3 columns)', 'google-reviews-plugin'),
                        'list' => __('List', 'google-reviews-plugin'),
                        'grid' => __('Grid', 'google-reviews-plugin'),
                    ),
                    'description' => __('Grid Carousel layout available in Pro version', 'google-reviews-plugin'),
                )
            );
        }

        // Column controls - only for Pro users
        if ($this->is_pro()) {
            $this->add_control(
                'cols_desktop',
                array(
                    'label' => __('Columns (Desktop)', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'default' => 3,
                    'min' => 1,
                    'max' => 6,
                )
            );

            $this->add_control(
                'cols_tablet',
                array(
                    'label' => __('Columns (Tablet)', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'default' => 2,
                    'min' => 1,
                    'max' => 4,
                )
            );

            $this->add_control(
                'cols_mobile',
                array(
                    'label' => __('Columns (Mobile)', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'default' => 1,
                    'min' => 1,
                    'max' => 3,
                )
            );

            $this->add_control(
                'gap',
                array(
                    'label' => __('Gap (px)', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'default' => 20,
                    'min' => 0,
                    'max' => 60,
                )
            );
        } else {
            // Free version - show upgrade notice for column controls
            $this->add_control(
                'free_column_notice',
                array(
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => '<div style="background: #f0f8ff; border: 1px solid #007cba; padding: 10px; margin-bottom: 10px; border-radius: 4px;"><strong>📐 Column Controls</strong><br>Upgrade to Pro to customize column counts and gap spacing for each device. <a href="https://reactwoo.com/google-reviews-plugin-pro/" target="_blank" style="color: #007cba; text-decoration: underline;">Learn More</a></div>',
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                )
            );

            // Hidden controls for free version with fixed values
            $this->add_control(
                'cols_desktop',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'default' => 3, // Force 3 columns for carousel, let grid use its own defaults
                )
            );

            $this->add_control(
                'cols_tablet',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'default' => 2,
                )
            );

            $this->add_control(
                'cols_mobile',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'default' => 1,
                )
            );

            $this->add_control(
                'gap',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'default' => 20,
                )
            );
        }
        
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

        $this->add_control(
            'consistent_height',
            array(
                'label' => __('Consistent Card Height', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'google-reviews-plugin'),
                'label_off' => __('No', 'google-reviews-plugin'),
                'return_value' => 'true',
                'default' => 'false',
                'description' => __('Make all cards the same height for uniform appearance', 'google-reviews-plugin'),
            )
        );

        $this->end_controls_section();
        
        // Carousel Options Section
        if ($this->is_pro()) {
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
        } else {
            // Free version - show upgrade notice for carousel options
            $this->start_controls_section(
                'carousel_section_free',
                array(
                    'label' => __('Carousel Options', 'google-reviews-plugin'),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                )
            );

            $this->add_control(
                'free_carousel_notice',
                array(
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => '<div style="background: #f0f8ff; border: 1px solid #007cba; padding: 10px; margin-bottom: 10px; border-radius: 4px;"><strong>⚙️ Carousel Controls</strong><br>Upgrade to Pro to customize autoplay speed, show/hide dots and arrows. <a href="https://reactwoo.com/google-reviews-plugin-pro/" target="_blank" style="color: #007cba; text-decoration: underline;">Learn More</a></div>',
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                )
            );

            // Hidden controls for free version with default values
            $this->add_control(
                'autoplay',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'default' => 'true',
                )
            );

            $this->add_control(
                'speed',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'default' => 5000,
                )
            );

            $this->add_control(
                'dots',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'default' => 'true',
                )
            );

            $this->add_control(
                'arrows',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'default' => 'true',
                )
            );

            $this->end_controls_section();
        }
        
        // Style Customization Section (style-specific options)
        if ($this->is_pro()) {
            $this->start_controls_section(
                'style_customization_section',
                array(
                    'label' => __('Style Customization', 'google-reviews-plugin'),
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                )
            );

            // Text Color (all styles)
            $this->add_control(
                'custom_text_color',
                array(
                    'label' => __('Text Color', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => array(
                        '{{WRAPPER}} .grp-review-text' => 'color: {{VALUE}} !important;',
                        '{{WRAPPER}} .grp-author-name' => 'color: {{VALUE}} !important;',
                    ),
                )
            );

        // Background Color (all styles except creative)
        $this->add_control(
            'custom_background_color',
            array(
                'label' => __('Card Background Color', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'condition' => array(
                    'style!' => 'creative',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .grp-review' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );

            // Border Color (Classic, Corporate styles)
            $this->add_control(
                'custom_border_color',
                array(
                    'label' => __('Border Color', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'condition' => array(
                        'style' => array('classic', 'corporate'),
                    ),
                    'selectors' => array(
                        '{{WRAPPER}} .grp-review' => 'border-color: {{VALUE}} !important;',
                    ),
                )
            );

            // Accent Color (Modern, Corporate, Minimal styles)
            $this->add_control(
                'custom_accent_color',
                array(
                    'label' => __('Accent Color', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'condition' => array(
                        'style' => array('modern', 'corporate', 'minimal'),
                    ),
                    'selectors' => array(
                        '{{WRAPPER}} .grp-review' => '--grp-accent: {{VALUE}};',
                    ),
                )
            );

            // Star Color (all styles)
            $this->add_control(
                'custom_star_color',
                array(
                    'label' => __('Star Color', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => array(
                        '{{WRAPPER}} .grp-star' => 'color: {{VALUE}} !important;',
                    ),
                )
            );

        // Border Radius (all styles except Classic and Creative)
        $this->add_control(
            'custom_border_radius',
            array(
                'label' => __('Border Radius', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'condition' => array(
                    'style!' => array('classic', 'creative'),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .grp-review' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ),
            )
        );

            // Padding Override
            $this->add_control(
                'custom_padding',
                array(
                    'label' => __('Card Padding', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => array('px', 'em', '%'),
                    'selectors' => array(
                        '{{WRAPPER}} .grp-review' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                    ),
                )
            );

            // Font Size (Body Text)
            $this->add_control(
                'custom_font_size',
                array(
                    'label' => __('Body Text Size', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => array('px', 'em', 'rem'),
                    'range' => array(
                        'px' => array(
                            'min' => 10,
                            'max' => 24,
                            'step' => 1,
                        ),
                        'em' => array(
                            'min' => 0.5,
                            'max' => 2,
                            'step' => 0.1,
                        ),
                    ),
                    'selectors' => array(
                        '{{WRAPPER}} .grp-review-text' => 'font-size: {{SIZE}}{{UNIT}} !important;',
                    ),
                )
            );

        // Name Font Size
        $this->add_control(
            'custom_name_font_size',
            array(
                'label' => __('Name Text Size', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array('px', 'em', 'rem'),
                'range' => array(
                    'px' => array(
                        'min' => 10,
                        'max' => 20,
                        'step' => 1,
                    ),
                    'em' => array(
                        'min' => 0.5,
                        'max' => 1.5,
                        'step' => 0.1,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .grp-author-name' => 'font-size: {{SIZE}}{{UNIT}} !important;',
                ),
            )
        );

        // Text Alignment
        $this->add_control(
            'text_alignment',
            array(
                'label' => __('Text Alignment', 'google-reviews-plugin'),
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
                'selectors' => array(
                    '{{WRAPPER}} .grp-review-text' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .grp-review-meta' => 'text-align: {{VALUE}};',
                ),
            )
        );

        // Typography Controls
        if ($this->is_pro()) {
            $this->add_control(
                'typography_heading',
                array(
                    'label' => __('Typography', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'before',
                )
            );

            // Body Font Family
            $this->add_control(
                'body_font_family',
                array(
                    'label' => __('Body Font Family', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::FONT,
                    'selectors' => array(
                        '{{WRAPPER}} .grp-review-text' => 'font-family: {{VALUE}};',
                    ),
                )
            );

            // Name Font Family (especially for Classic style)
            $this->add_control(
                'name_font_family',
                array(
                    'label' => __('Name Font Family', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::FONT,
                    'condition' => array(
                        'style' => 'classic',
                    ),
                    'selectors' => array(
                        '{{WRAPPER}} .grp-author-name' => 'font-family: {{VALUE}};',
                    ),
                )
            );
        } else {
            $this->add_control(
                'free_typography_notice',
                array(
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => '<div style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 12px; margin-top: 15px; border-radius: 4px;"><strong>🔤 Custom Fonts</strong><br>Choose from Google Fonts and customize typography for the perfect look. <a href="https://reactwoo.com/google-reviews-plugin-pro/" target="_blank" style="color: #007cba; text-decoration: underline;">Upgrade to Pro</a></div>',
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                )
            );

            // Hidden controls for free version
            $this->add_control(
                'body_font_family',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                )
            );

            $this->add_control(
                'name_font_family',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                )
            );
        }

        $this->end_controls_section();
        } else {
            // Free version - show upgrade notice for style customization
            $this->start_controls_section(
                'style_customization_section_free',
                array(
                    'label' => __('Style Customization', 'google-reviews-plugin'),
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                )
            );

            $this->add_control(
                'free_style_customization_notice',
                array(
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => '<div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; margin-bottom: 10px; border-radius: 4px;"><strong>🎨 Advanced Styling</strong><br>Unlock unlimited customization options: colors, fonts, spacing, borders, and more. <a href="https://reactwoo.com/google-reviews-plugin-pro/" target="_blank" style="color: #856404; text-decoration: underline; font-weight: bold;">Upgrade to Pro</a></div>',
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                )
            );

            // Hidden controls for free version with default values
            $this->add_control(
                'custom_text_color',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                )
            );

            $this->add_control(
                'custom_background_color',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                )
            );

            $this->add_control(
                'custom_border_color',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                )
            );

            $this->add_control(
                'custom_accent_color',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                )
            );

            $this->add_control(
                'custom_star_color',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                )
            );

            $this->add_control(
                'custom_border_radius',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                )
            );

            $this->add_control(
                'custom_padding',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                )
            );

            $this->add_control(
                'custom_font_size',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                )
            );

            $this->add_control(
                'custom_name_font_size',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                )
            );

            $this->end_controls_section();
        }

        // Creative Style Controls Section
        $this->start_controls_section(
            'creative_style_section',
            array(
                'label' => __('Creative Style Options', 'google-reviews-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'style' => 'creative',
                ),
            )
        );

        // Advanced Gradient Background Controls (Elementor-style)
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            array(
                'name' => 'creative_background',
                'label' => __('Background', 'google-reviews-plugin'),
                'types' => array('gradient'),
                'selector' => '{{WRAPPER}} .grp-style-creative .grp-review',
                'fields_options' => array(
                    'background' => array(
                        'default' => 'gradient',
                    ),
                    'gradient_type' => array(
                        'default' => 'linear',
                    ),
                    'gradient_angle' => array(
                        'default' => array(
                            'unit' => 'deg',
                            'size' => 135,
                        ),
                    ),
                    'gradient_position' => array(
                        'default' => 'center center',
                    ),
                    'color' => array(
                        'default' => '#4285F4',
                    ),
                    'color_stop' => array(
                        'default' => array(
                            'unit' => '%',
                            'size' => 0,
                        ),
                    ),
                    'color_b' => array(
                        'default' => '#EA4335',
                    ),
                    'color_b_stop' => array(
                        'default' => array(
                            'unit' => '%',
                            'size' => 100,
                        ),
                    ),
                    'gradient_angle' => array(
                        'selectors' => array(
                            '{{WRAPPER}} .grp-style-creative .grp-review' => 'background: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
                        ),
                    ),
                ),
            )
        );

        // Gradient background selector
        $this->add_control(
            'creative_gradient_background',
            array(
                'type' => \Elementor\Controls_Manager::HIDDEN,
                'default' => 'gradient',
                'selectors' => array(
                    '{{WRAPPER}} .grp-style-creative .grp-review' => '{{VALUE}}',
                ),
            )
        );

        // Text Colors (default to white for creative)
        $this->add_control(
            'creative_text_color',
            array(
                'label' => __('Text Color', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .grp-style-creative .grp-review-text' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .grp-style-creative .grp-author-name' => 'color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'creative_date_color',
            array(
                'label' => __('Date Color', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .grp-style-creative .grp-review-date' => 'color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'creative_star_color',
            array(
                'label' => __('Star Color', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFD700',
                'selectors' => array(
                    '{{WRAPPER}} .grp-style-creative .grp-star' => 'color: {{VALUE}} !important;',
                ),
            )
        );

        // Glass Effect (Apple-style)
        $this->add_control(
            'creative_glass_effect',
            array(
                'label' => __('Glass Effect', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'google-reviews-plugin'),
                'label_off' => __('No', 'google-reviews-plugin'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => __('Apple-style glass morphism effect with backdrop blur and transparency', 'google-reviews-plugin'),
            )
        );

        // Box Shadow with proper margins
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'creative_box_shadow',
                'label' => __('Box Shadow', 'google-reviews-plugin'),
                'selector' => '{{WRAPPER}} .grp-style-creative .grp-review',
                'fields_options' => array(
                    'box_shadow' => array(
                        'default' => array(
                            'horizontal' => 0,
                            'vertical' => 4,
                            'blur' => 8,
                            'spread' => 0,
                            'color' => 'rgba(0, 0, 0, 0.1)',
                        ),
                    ),
                ),
            )
        );

        // Border Controls
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            array(
                'name' => 'creative_border',
                'label' => __('Border', 'google-reviews-plugin'),
                'selector' => '{{WRAPPER}} .grp-style-creative .grp-review',
            )
        );

        // Border Radius
        $this->add_control(
            'creative_border_radius',
            array(
                'label' => __('Border Radius', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .grp-style-creative .grp-review' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ),
                'default' => array(
                    'top' => '16',
                    'right' => '16',
                    'bottom' => '16',
                    'left' => '16',
                    'unit' => 'px',
                ),
            )
        );

        // Avatar Size
        $this->add_control(
            'creative_avatar_size',
            array(
                'label' => __('Avatar Size', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 20,
                        'max' => 120,
                        'step' => 4,
                    ),
                ),
                'default' => array(
                    'unit' => 'px',
                    'size' => 80,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .grp-style-creative .grp-review-avatar img' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
                ),
            )
        );

        // Star Size
        $this->add_control(
            'creative_star_size',
            array(
                'label' => __('Star Size', 'google-reviews-plugin'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 12,
                        'max' => 48,
                        'step' => 2,
                    ),
                ),
                'default' => array(
                    'unit' => 'px',
                    'size' => 32,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .grp-style-creative .grp-star' => 'font-size: {{SIZE}}{{UNIT}} !important;',
                ),
            )
        );

        $this->end_controls_section();

        // Typography Section
        if ($this->is_pro()) {
            $this->start_controls_section(
                'typography_section',
                array(
                    'label' => __('Typography', 'google-reviews-plugin'),
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                )
            );

            // Body Font Family
            $this->add_control(
                'body_font_family',
                array(
                    'label' => __('Body Font Family', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::FONT,
                    'selectors' => array(
                        '{{WRAPPER}} .grp-review-text' => 'font-family: {{VALUE}};',
                    ),
                )
            );

            // Name Font Family (especially for Classic style)
            $this->add_control(
                'name_font_family',
                array(
                    'label' => __('Name Font Family', 'google-reviews-plugin'),
                    'type' => \Elementor\Controls_Manager::FONT,
                    'condition' => array(
                        'style' => 'classic',
                    ),
                    'selectors' => array(
                        '{{WRAPPER}} .grp-author-name' => 'font-family: {{VALUE}};',
                    ),
                )
            );

            $this->end_controls_section();
        } else {
            // Free version - show upgrade notice for typography
            $this->start_controls_section(
                'typography_section_free',
                array(
                    'label' => __('Typography', 'google-reviews-plugin'),
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                )
            );

            $this->add_control(
                'free_typography_notice',
                array(
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => '<div style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 12px; margin-bottom: 10px; border-radius: 4px;"><strong>🔤 Custom Fonts</strong><br>Choose from Google Fonts and customize typography for the perfect look. <a href="https://reactwoo.com/google-reviews-plugin-pro/" target="_blank" style="color: #007cba; text-decoration: underline;">Upgrade to Pro</a></div>',
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                )
            );

            // Hidden controls for free version
            $this->add_control(
                'body_font_family',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                )
            );

            $this->add_control(
                'name_font_family',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                )
            );

            $this->end_controls_section();
        }
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
            'cols_desktop' => isset($settings['cols_desktop']) ? $settings['cols_desktop'] : 3,
            'cols_tablet' => isset($settings['cols_tablet']) ? $settings['cols_tablet'] : 2,
            'cols_mobile' => isset($settings['cols_mobile']) ? $settings['cols_mobile'] : 1,
            'gap' => isset($settings['gap']) ? $settings['gap'] : 20,
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
            'consistent_height' => isset($settings['consistent_height']) ? $settings['consistent_height'] : 'false',
            // Creative style specific options
            // Creative background handled by Elementor Group_Control_Background
            'creative_background' => isset($settings['creative_background']) ? $settings['creative_background'] : array(),
            'creative_text_color' => isset($settings['creative_text_color']) ? $settings['creative_text_color'] : '#ffffff',
            'creative_date_color' => isset($settings['creative_date_color']) ? $settings['creative_date_color'] : '#ffffff',
            'creative_star_color' => isset($settings['creative_star_color']) ? $settings['creative_star_color'] : '#FFD700',
            'creative_glass_effect' => isset($settings['creative_glass_effect']) ? $settings['creative_glass_effect'] : 'no',
            'creative_box_shadow' => isset($settings['creative_box_shadow']) ? $settings['creative_box_shadow'] : array(),
            'creative_border' => isset($settings['creative_border']) ? $settings['creative_border'] : array(),
            'creative_border_radius' => isset($settings['creative_border_radius']) ? $settings['creative_border_radius'] : array(),
            'creative_avatar_size' => isset($settings['creative_avatar_size']['size']) ? $settings['creative_avatar_size']['size'] : 80,
            'creative_star_size' => isset($settings['creative_star_size']['size']) ? $settings['creative_star_size']['size'] : 32,
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