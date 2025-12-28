<?php
/**
 * Styles management class
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_Styles {
    
    /**
     * Available styles
     */
    private $styles = array();
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_styles();
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('wp_head', array($this, 'output_custom_css'));
        add_action('admin_head', array($this, 'output_admin_css'));
    }
    
    /**
     * Initialize available styles
     */
    private function init_styles() {
        $this->styles = array(
            'modern' => array(
                'name' => __('Modern', 'google-reviews-plugin'),
                'description' => __('Clean and contemporary design with subtle shadows', 'google-reviews-plugin'),
                'variants' => array('light', 'dark', 'auto'),
                'features' => array('shadows', 'rounded_corners', 'gradients')
            ),
            'classic' => array(
                'name' => __('Classic', 'google-reviews-plugin'),
                'description' => __('Traditional design with clean lines and professional look', 'google-reviews-plugin'),
                'variants' => array('light', 'dark', 'auto'),
                'features' => array('borders', 'clean_typography')
            ),
            'minimal' => array(
                'name' => __('Minimal', 'google-reviews-plugin'),
                'description' => __('Minimalist design focusing on content', 'google-reviews-plugin'),
                'variants' => array('light', 'dark', 'auto'),
                'features' => array('minimal_spacing', 'clean_typography')
            ),
            'corporate' => array(
                'name' => __('Corporate', 'google-reviews-plugin'),
                'description' => __('Professional business design with structured layout', 'google-reviews-plugin'),
                'variants' => array('light', 'dark', 'auto'),
                'features' => array('structured_layout', 'professional_colors')
            ),
            'creative' => array(
                'name' => __('Creative', 'google-reviews-plugin'),
                'description' => __('Artistic design with creative elements and animations', 'google-reviews-plugin'),
                'variants' => array('light', 'dark', 'auto'),
                'features' => array('animations', 'creative_elements', 'gradients')
            )
        );
    }
    
    /**
     * Get all available styles
     */
    public function get_styles() {
        return $this->styles;
    }
    
    /**
     * Get specific style
     */
    public function get_style($style_name) {
        return isset($this->styles[$style_name]) ? $this->styles[$style_name] : false;
    }
    
    /**
     * Get style CSS
     */
    public function get_style_css($style_name, $variant = 'light') {
        $style = $this->get_style($style_name);
        if (!$style) {
            return '';
        }

        $css = '';

        // Define CSS variables for this style+variant
        $css .= $this->get_variant_css_variables($style_name, $variant);

        // Append structural CSS that relies on the variables
        switch ($style_name) {
            case 'modern':
                $css .= $this->get_modern_css();
                break;
            case 'classic':
                $css .= $this->get_classic_css();
                break;
            case 'minimal':
                $css .= $this->get_minimal_css();
                break;
            case 'corporate':
                $css .= $this->get_corporate_css();
                break;
            case 'creative':
                $css .= $this->get_creative_css();
                break;
        }

        return $css;
    }

    /**
     * Get CSS variable definitions for a style/variant combination.
     * Supports 'auto' using prefers-color-scheme to switch variables.
     */
    private function get_variant_css_variables($style_name, $variant) {
        // Use generic light/dark palettes; can be customized per style later
        $light = $this->get_variant_colors($style_name, 'light');
        $dark = $this->get_variant_colors($style_name, 'dark');

        if ($variant === 'auto') {
            return "
            .grp-style-{$style_name}.grp-theme-auto {
                --grp-background: {$light['background']};
                --grp-background_alt: {$light['background_alt']};
                --grp-text: {$light['text']};
                --grp-muted: {$light['muted']};
                --grp-border: {$light['border']};
            }
            @media (prefers-color-scheme: dark) {
                .grp-style-{$style_name}.grp-theme-auto {
                    --grp-background: {$dark['background']};
                    --grp-background_alt: {$dark['background_alt']};
                    --grp-text: {$dark['text']};
                    --grp-muted: {$dark['muted']};
                    --grp-border: {$dark['border']};
                }
            }
            ";
        }

        $colors = $variant === 'dark' ? $dark : $light;
        $variant_class = "grp-theme-{$variant}";
        return "
        .grp-style-{$style_name}.{$variant_class} {
            --grp-background: {$colors['background']};
            --grp-background_alt: {$colors['background_alt']};
            --grp-text: {$colors['text']};
            --grp-muted: {$colors['muted']};
            --grp-border: {$colors['border']};
        }
        ";
    }
    
    /**
     * Get modern style CSS
     */
    private function get_modern_css() {
        return "
        .grp-style-modern .grp-review {
            background: var(--grp-background);
            border: 1px solid var(--grp-border);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .grp-style-modern .grp-review:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        
        .grp-style-modern .grp-review-rating {
            margin-bottom: 15px;
        }
        
        .grp-style-modern .grp-star {
            color: #ffc107;
            font-size: 18px;
            margin-right: 2px;
        }
        
        .grp-style-modern .grp-review-text {
            color: var(--grp-text);
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .grp-style-modern .grp-review-meta {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .grp-style-modern .grp-review-avatar img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .grp-style-modern .grp-author-name {
            font-weight: 600;
            color: var(--grp-text);
        }
        
        .grp-style-modern .grp-review-date {
            color: var(--grp-muted);
            font-size: 14px;
        }
        ";
    }
    
    /**
     * Get classic style CSS
     */
    private function get_classic_css() {
        return "
        .grp-style-classic .grp-review {
            background: var(--grp-background);
            border: 2px solid var(--grp-border);
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .grp-style-classic .grp-review-rating {
            margin-bottom: 15px;
        }
        
        .grp-style-classic .grp-star {
            color: #ffc107;
            font-size: 16px;
            margin-right: 2px;
        }
        
        .grp-style-classic .grp-review-text {
            color: var(--grp-text);
            font-size: 15px;
            line-height: 1.5;
            margin-bottom: 15px;
            font-style: italic;
        }
        
        .grp-style-classic .grp-review-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            border-top: 1px solid var(--grp-border);
            padding-top: 15px;
        }
        
        .grp-style-classic .grp-review-avatar img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .grp-style-classic .grp-author-name {
            font-weight: 500;
            color: var(--grp-text);
        }
        
        .grp-style-classic .grp-review-date {
            color: var(--grp-muted);
            font-size: 13px;
        }
        ";
    }
    
    /**
     * Get minimal style CSS
     */
    private function get_minimal_css() {
        return "
        .grp-style-minimal .grp-review {
            background: var(--grp-background);
            border: none;
            border-radius: 0;
            padding: 15px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--grp-border);
        }
        
        .grp-style-minimal .grp-review:last-child {
            border-bottom: none;
        }
        
        .grp-style-minimal .grp-review-rating {
            margin-bottom: 10px;
        }
        
        .grp-style-minimal .grp-star {
            color: #ffc107;
            font-size: 14px;
            margin-right: 1px;
        }
        
        .grp-style-minimal .grp-review-text {
            color: var(--grp-text);
            font-size: 14px;
            line-height: 1.4;
            margin-bottom: 10px;
        }
        
        .grp-style-minimal .grp-review-meta {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .grp-style-minimal .grp-review-avatar img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .grp-style-minimal .grp-author-name {
            font-weight: 400;
            color: var(--grp-text);
            font-size: 14px;
        }
        
        .grp-style-minimal .grp-review-date {
            color: var(--grp-muted);
            font-size: 12px;
        }
        ";
    }
    
    /**
     * Get corporate style CSS
     */
    private function get_corporate_css() {
        return "
        .grp-style-corporate .grp-review {
            background: var(--grp-background);
            border: 1px solid var(--grp-border);
            border-radius: 6px;
            padding: 25px;
            margin-bottom: 20px;
            position: relative;
        }
        
        .grp-style-corporate .grp-review::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #007cba, #005a87);
            border-radius: 6px 6px 0 0;
        }
        
        .grp-style-corporate .grp-review-rating {
            margin-bottom: 20px;
        }
        
        .grp-style-corporate .grp-star {
            color: #ffc107;
            font-size: 16px;
            margin-right: 2px;
        }
        
        .grp-style-corporate .grp-review-text {
            color: var(--grp-text);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .grp-style-corporate .grp-review-meta {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .grp-style-corporate .grp-review-avatar img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--grp-border);
        }
        
        .grp-style-corporate .grp-author-name {
            font-weight: 600;
            color: var(--grp-text);
            font-size: 16px;
        }
        
        .grp-style-corporate .grp-review-date {
            color: var(--grp-muted);
            font-size: 14px;
        }
        ";
    }
    
    /**
     * Get creative style CSS
     */
    private function get_creative_css() {
        return "
        .grp-style-creative .grp-review {
            background: linear-gradient(135deg, var(--grp-background), var(--grp-background_alt));
            border: none;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.6s ease-out;
        }
        
        .grp-style-creative .grp-review::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }
        
        .grp-style-creative .grp-review-rating {
            margin-bottom: 20px;
        }
        
        .grp-style-creative .grp-star {
            color: #ffc107;
            font-size: 20px;
            margin-right: 3px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .grp-style-creative .grp-review-text {
            color: var(--grp-text);
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .grp-style-creative .grp-review-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
            z-index: 1;
        }
        
        .grp-style-creative .grp-review-avatar img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .grp-style-creative .grp-author-name {
            font-weight: 700;
            color: var(--grp-text);
            font-size: 16px;
        }
        
        .grp-style-creative .grp-review-date {
            color: var(--grp-muted);
            font-size: 14px;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes shimmer {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }
            100% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
            }
        }
        ";
    }
    
    /**
     * Get variant colors
     */
    private function get_variant_colors($style, $variant) {
        $color_schemes = array(
            'light' => array(
                'background' => '#ffffff',
                'background_alt' => '#f8f9fa',
                'text' => '#333333',
                'muted' => '#666666',
                'border' => '#e0e0e0'
            ),
            'dark' => array(
                'background' => '#2d3748',
                'background_alt' => '#1a202c',
                'text' => '#ffffff',
                'muted' => '#a0aec0',
                'border' => '#4a5568'
            )
        );
        
        return isset($color_schemes[$variant]) ? $color_schemes[$variant] : $color_schemes['light'];
    }
    
    /**
     * Get carousel CSS
     */
    public function get_carousel_css() {
        return "
        .grp-layout-carousel .grp-carousel-container {
            position: relative;
            overflow: hidden;
        }
        
        .grp-layout-carousel .grp-carousel-wrapper {
            display: flex;
            transition: transform 0.5s ease;
        }
        
        .grp-layout-carousel .grp-review-item {
            flex: 0 0 100%;
            padding: 0 10px;
        }
        
        .grp-layout-carousel .grp-carousel-prev,
        .grp-layout-carousel .grp-carousel-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease;
        }
        
        .grp-layout-carousel .grp-carousel-prev:hover,
        .grp-layout-carousel .grp-carousel-next:hover {
            background: rgba(0, 0, 0, 0.7);
        }
        
        .grp-layout-carousel .grp-carousel-prev {
            left: 10px;
        }
        
        .grp-layout-carousel .grp-carousel-next {
            right: 10px;
        }
        
        .grp-layout-carousel .grp-carousel-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }
        
        .grp-layout-carousel .grp-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: none;
            background: #ccc;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .grp-layout-carousel .grp-dot.active {
            background: #007cba;
        }
        
        .grp-layout-carousel .grp-dot:hover {
            background: #999;
        }
        ";
    }
    
    /**
     * Get responsive CSS
     */
    public function get_responsive_css() {
        return "
        .grp-responsive .grp-review {
            margin-bottom: 15px;
        }
        
        .grp-responsive .grp-review-text {
            font-size: 14px;
        }
        
        .grp-responsive .grp-review-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        
        .grp-responsive .grp-review-avatar img {
            width: 35px;
            height: 35px;
        }
        
        @media (max-width: 768px) {
            .grp-layout-carousel .grp-carousel-prev,
            .grp-layout-carousel .grp-carousel-next {
                width: 35px;
                height: 35px;
            }
            
            .grp-layout-carousel .grp-carousel-prev {
                left: 5px;
            }
            
            .grp-layout-carousel .grp-carousel-next {
                right: 5px;
            }
        }
        ";
    }
    
    /**
     * Output custom CSS
     */
    public function output_custom_css() {
        $custom_css = get_option('grp_custom_css', '');
        if (!empty($custom_css)) {
            echo '<style type="text/css">' . $custom_css . '</style>';
        }
    }
    
    /**
     * Output admin CSS
     */
    public function output_admin_css() {
        // Get base CSS
        $css = $this->get_base_css();
        
        // Add all style CSS for previews (all variants so switcher works)
        foreach ($this->styles as $style_name => $style_data) {
            foreach ($style_data['variants'] as $variant) {
                $css .= $this->get_style_css($style_name, $variant);
            }
        }
        
        // Add admin-specific styles for previews
        $css .= "
        .grp-style-preview {
            padding: 20px;
            margin: 10px 0;
        }
        .grp-admin-preview {
            max-width: 400px;
            margin: 20px 0;
        }
        .grp-admin-preview .grp-review {
            margin-bottom: 15px;
        }
        ";
        
        echo '<style type="text/css">' . $css . '</style>';
    }
    
    /**
     * Get all CSS
     */
    public function get_all_css() {
        $css = '';
        
        // Base styles
        $css .= $this->get_base_css();
        
        // Style variants
        foreach ($this->styles as $style_name => $style_data) {
            foreach ($style_data['variants'] as $variant) {
                $css .= $this->get_style_css($style_name, $variant);
            }
        }
        
        // Carousel styles
        $css .= $this->get_carousel_css();
        
        // Responsive styles
        $css .= $this->get_responsive_css();
        
        return $css;
    }
    
    /**
     * Get base CSS
     */
    private function get_base_css() {
        return "
        .grp-reviews {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .grp-review {
            position: relative;
        }
        
        .grp-review-rating {
            margin-bottom: 10px;
        }
        
        .grp-star {
            display: inline-block;
        }
        
        .grp-review-text {
            margin-bottom: 15px;
        }
        
        .grp-review-meta {
            display: flex;
            align-items: center;
        }
        
        .grp-review-avatar img {
            border-radius: 50%;
            object-fit: cover;
        }
        
        .grp-review-reply {
            margin-top: 15px;
            padding: 15px;
            background: var(--grp-background_alt);
            border-radius: 8px;
            border-left: 4px solid #007cba;
        }
        
        .grp-reply-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .grp-reply-text {
            color: var(--grp-muted);
        }
        
        .grp-no-reviews {
            text-align: center;
            padding: 40px 20px;
            color: var(--grp-muted);
        }
        ";
    }
}