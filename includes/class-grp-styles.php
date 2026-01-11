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
    const STYLE_CUSTOMIZATIONS_OPTION = 'grp_review_style_customizations';
    
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
        add_action('wp_ajax_grp_save_style_customization', array($this, 'ajax_save_style_customization'));
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
                'features' => array('shadows', 'rounded_corners', 'gradients'),
                'template' => 'layout1',
            ),
            'classic' => array(
                'name' => __('Classic', 'google-reviews-plugin'),
                'description' => __('Traditional design with clean lines and professional look', 'google-reviews-plugin'),
                'variants' => array('light', 'dark', 'auto'),
                'features' => array('borders', 'clean_typography'),
                'template' => 'layout1',
            ),
            'minimal' => array(
                'name' => __('Minimal', 'google-reviews-plugin'),
                'description' => __('Minimalist design focusing on content', 'google-reviews-plugin'),
                'variants' => array('light', 'dark', 'auto'),
                'features' => array('minimal_spacing', 'clean_typography'),
                'template' => 'layout1',
            ),
            'corporate' => array(
                'name' => __('Corporate', 'google-reviews-plugin'),
                'description' => __('Professional business design with structured layout', 'google-reviews-plugin'),
                'variants' => array('light', 'dark', 'auto'),
                'features' => array('structured_layout', 'professional_colors'),
                'template' => 'layout1',
            ),
            'creative' => array(
                'name' => __('Creative', 'google-reviews-plugin'),
                'description' => __('Artistic design with creative elements and animations', 'google-reviews-plugin'),
                'variants' => array('light', 'dark', 'auto'),
                'features' => array('animations', 'creative_elements', 'gradients'),
                'template' => 'creative-pro',
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
        $light = $this->get_effective_variant_colors($style_name, 'light');
        $dark = $this->get_effective_variant_colors($style_name, 'dark');

        if ($variant === 'auto') {
            $css_vars = "
            .grp-style-{$style_name}.grp-theme-auto {
                --grp-background: {$light['background']};
                --grp-background_alt: {$light['background_alt']};
                --grp-text: {$light['text']};
                --grp-muted: {$light['muted']};
                --grp-border: {$light['border']};
        ";
            if (isset($light['star'])) {
                $css_vars .= "
                --grp-star: {$light['star']};
        ";
            }
            if (isset($light['accent'])) {
                $css_vars .= "
                --grp-accent: {$light['accent']};
        ";
            }
            if (isset($light['card_background'])) {
                $css_vars .= "
                --grp-card_background: {$light['card_background']};
        ";
            }
            if (isset($light['card_radius'])) {
                $radius = is_numeric($light['card_radius']) ? (int) $light['card_radius'] : 0;
                $css_vars .= "
                --grp-card_radius: {$radius}px;
        ";
            }
            if (isset($light['card_shadow'])) {
                $css_vars .= "
                --grp-card_shadow: {$light['card_shadow']};
        ";
            }
            if (!empty($light['font_family'])) {
                $css_vars .= "
                --grp-font_family: {$light['font_family']};
        ";
            }
            if (!empty($light['heading_font_weight'])) {
                $css_vars .= "
                --grp-heading_font_weight: {$light['heading_font_weight']};
        ";
            }
            if (!empty($light['body_font_weight'])) {
                $css_vars .= "
                --grp-body_font_weight: {$light['body_font_weight']};
        ";
            }
            if (!empty($light['body_line_height'])) {
                $css_vars .= "
                --grp-body_line_height: {$light['body_line_height']};
        ";
            }
            if (isset($light['body_letter_spacing'])) {
                $ls = is_numeric($light['body_letter_spacing']) ? (float) $light['body_letter_spacing'] : 0;
                $ls = rtrim(rtrim(number_format($ls, 2, '.', ''), '0'), '.');
                $css_vars .= "
                --grp-body_letter_spacing: {$ls}px;
        ";
            }
            if (isset($light['gradient_blue'])) {
                $css_vars .= "
                --grp-gradient_blue: {$light['gradient_blue']};
                --grp-gradient_red: {$light['gradient_red']};
                --grp-gradient_yellow: {$light['gradient_yellow']};
                --grp-gradient_green: {$light['gradient_green']};
        ";
            }
            $css_vars .= "
            }
            @media (prefers-color-scheme: dark) {
                .grp-style-{$style_name}.grp-theme-auto {
                    --grp-background: {$dark['background']};
                    --grp-background_alt: {$dark['background_alt']};
                    --grp-text: {$dark['text']};
                    --grp-muted: {$dark['muted']};
                    --grp-border: {$dark['border']};
        ";
            if (isset($dark['star'])) {
                $css_vars .= "
                    --grp-star: {$dark['star']};
        ";
            }
            if (isset($dark['accent'])) {
                $css_vars .= "
                    --grp-accent: {$dark['accent']};
        ";
            }
            if (isset($dark['card_background'])) {
                $css_vars .= "
                    --grp-card_background: {$dark['card_background']};
        ";
            }
            if (isset($dark['card_radius'])) {
                $radius = is_numeric($dark['card_radius']) ? (int) $dark['card_radius'] : 0;
                $css_vars .= "
                    --grp-card_radius: {$radius}px;
        ";
            }
            if (isset($dark['card_shadow'])) {
                $css_vars .= "
                    --grp-card_shadow: {$dark['card_shadow']};
        ";
            }
            if (!empty($dark['font_family'])) {
                $css_vars .= "
                    --grp-font_family: {$dark['font_family']};
        ";
            }
            if (!empty($dark['heading_font_weight'])) {
                $css_vars .= "
                    --grp-heading_font_weight: {$dark['heading_font_weight']};
        ";
            }
            if (!empty($dark['body_font_weight'])) {
                $css_vars .= "
                    --grp-body_font_weight: {$dark['body_font_weight']};
        ";
            }
            if (!empty($dark['body_line_height'])) {
                $css_vars .= "
                    --grp-body_line_height: {$dark['body_line_height']};
        ";
            }
            if (isset($dark['body_letter_spacing'])) {
                $ls = is_numeric($dark['body_letter_spacing']) ? (float) $dark['body_letter_spacing'] : 0;
                $ls = rtrim(rtrim(number_format($ls, 2, '.', ''), '0'), '.');
                $css_vars .= "
                    --grp-body_letter_spacing: {$ls}px;
        ";
            }
            if (isset($dark['gradient_blue'])) {
                $css_vars .= "
                    --grp-gradient_blue: {$dark['gradient_blue']};
                    --grp-gradient_red: {$dark['gradient_red']};
                    --grp-gradient_yellow: {$dark['gradient_yellow']};
                    --grp-gradient_green: {$dark['gradient_green']};
        ";
            }
            $css_vars .= "
                }
            }
            ";
            return $css_vars;
        }

        $colors = $variant === 'dark' ? $dark : $light;
        $variant_class = "grp-theme-{$variant}";
        $css_vars = "
        .grp-style-{$style_name}.{$variant_class} {
            --grp-background: {$colors['background']};
            --grp-background_alt: {$colors['background_alt']};
            --grp-text: {$colors['text']};
            --grp-muted: {$colors['muted']};
            --grp-border: {$colors['border']};
        ";
        
        // Add star color if defined
        if (isset($colors['star'])) {
            $css_vars .= "
            --grp-star: {$colors['star']};
        ";
        }
        
        // Add accent color if defined
        if (isset($colors['accent'])) {
            $css_vars .= "
            --grp-accent: {$colors['accent']};
        ";
        }
        
        // Add card background if defined (for corporate style)
        if (isset($colors['card_background'])) {
            $css_vars .= "
            --grp-card_background: {$colors['card_background']};
        ";
        }
        
        // Add gradient colors if defined (for creative style)
        if (isset($colors['gradient_blue'])) {
            $css_vars .= "
            --grp-gradient_blue: {$colors['gradient_blue']};
            --grp-gradient_red: {$colors['gradient_red']};
            --grp-gradient_yellow: {$colors['gradient_yellow']};
            --grp-gradient_green: {$colors['gradient_green']};
        ";
        }

        // Shape + typography vars
        if (isset($colors['card_radius'])) {
            $radius = is_numeric($colors['card_radius']) ? (int) $colors['card_radius'] : 0;
            $css_vars .= "
            --grp-card_radius: {$radius}px;
        ";
        }
        if (isset($colors['card_shadow'])) {
            $shadow = $colors['card_shadow'];
            $css_vars .= "
            --grp-card_shadow: {$shadow};
        ";
        }
        if (isset($colors['font_family']) && $colors['font_family'] !== '') {
            $css_vars .= "
            --grp-font_family: {$colors['font_family']};
        ";
        }
        if (isset($colors['heading_font_weight']) && $colors['heading_font_weight'] !== '') {
            $css_vars .= "
            --grp-heading_font_weight: {$colors['heading_font_weight']};
        ";
        }
        if (isset($colors['body_font_weight']) && $colors['body_font_weight'] !== '') {
            $css_vars .= "
            --grp-body_font_weight: {$colors['body_font_weight']};
        ";
        }
        if (isset($colors['body_line_height']) && $colors['body_line_height'] !== '') {
            $css_vars .= "
            --grp-body_line_height: {$colors['body_line_height']};
        ";
        }
        if (isset($colors['body_letter_spacing'])) {
            $ls = is_numeric($colors['body_letter_spacing']) ? (float) $colors['body_letter_spacing'] : 0;
            $ls = rtrim(rtrim(number_format($ls, 2, '.', ''), '0'), '.');
            $css_vars .= "
            --grp-body_letter_spacing: {$ls}px;
        ";
        }
        
        $css_vars .= "
        }
        ";
        
        return $css_vars;
    }

    /**
     * Get all stored style customizations.
     */
    public function get_style_customizations_all() {
        $all = get_option(self::STYLE_CUSTOMIZATIONS_OPTION, array());
        return is_array($all) ? $all : array();
    }

    /**
     * Get stored style customizations for a style + variant.
     */
    public function get_style_customizations($style, $variant) {
        $style = sanitize_key($style);
        $variant = sanitize_key($variant);
        $all = $this->get_style_customizations_all();
        if (!isset($all[$style]) || !is_array($all[$style])) {
            return array();
        }
        if (!isset($all[$style][$variant]) || !is_array($all[$style][$variant])) {
            return array();
        }
        return $all[$style][$variant];
    }

    /**
     * Defaults used by the style editor UI.
     */
    public function get_style_customization_defaults() {
        $defaults = array();
        foreach ($this->styles as $style_name => $style_data) {
            $defaults[$style_name] = array();
            foreach ($style_data['variants'] as $variant) {
                if ($variant === 'auto') {
                    // Editor will map auto to light/dark behind the scenes.
                    $defaults[$style_name][$variant] = $this->get_variant_colors($style_name, 'light');
                    continue;
                }
                $defaults[$style_name][$variant] = $this->get_variant_colors($style_name, $variant);
            }
        }
        return $defaults;
    }

    private function get_effective_variant_colors($style, $variant) {
        $defaults = $this->get_variant_colors($style, $variant);
        $stored = $this->get_style_customizations($style, $variant);
        if (!is_array($defaults) || empty($defaults) || !is_array($stored) || empty($stored)) {
            return $defaults;
        }
        $stored_whitelisted = array_intersect_key($stored, $defaults);
        return array_merge($defaults, $stored_whitelisted);
    }

    private function sanitize_style_customization_value($key, $value) {
        $key = sanitize_key($key);
        $value = is_string($value) ? trim($value) : '';
        if ($value === '') {
            return '';
        }
        // Prevent CSS injection primitives
        if (preg_match('/[;{}]/', $value)) {
            return '';
        }
        if ($value === 'transparent') {
            return 'transparent';
        }
        if ($value === 'inherit') {
            return 'inherit';
        }
        $hex = sanitize_hex_color($value);
        if ($hex) {
            return $hex;
        }
        // Allow rgb/rgba() (basic validation)
        if (preg_match('/^rgba?\(\\s*\\d{1,3}\\s*,\\s*\\d{1,3}\\s*,\\s*\\d{1,3}(\\s*,\\s*(0|1|0?\\.\\d+)\\s*)?\\)$/', $value)) {
            return $value;
        }

        // Numeric / typography / shape controls
        if ($key === 'card_radius') {
            $n = is_numeric($value) ? (int) $value : null;
            if ($n === null) return '';
            $n = max(0, min(80, $n));
            return (string) $n;
        }
        if ($key === 'heading_font_weight' || $key === 'body_font_weight') {
            $n = is_numeric($value) ? (int) $value : null;
            if ($n === null) return '';
            $allowed = array(300, 400, 500, 600, 700, 800, 900);
            return in_array($n, $allowed, true) ? (string) $n : '';
        }
        if ($key === 'body_line_height') {
            $n = is_numeric($value) ? (float) $value : null;
            if ($n === null) return '';
            if ($n < 1 || $n > 2.5) return '';
            return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
        }
        if ($key === 'body_letter_spacing') {
            $n = is_numeric($value) ? (float) $value : null;
            if ($n === null) return '';
            if ($n < -2 || $n > 10) return '';
            return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
        }
        if ($key === 'font_family') {
            $allowed = array(
                '', 'inherit',
                'Inter, sans-serif',
                'Roboto, sans-serif',
                'Open Sans, sans-serif',
                'Lato, sans-serif',
                'Montserrat, sans-serif',
                'Poppins, sans-serif',
                'Raleway, sans-serif',
                'Nunito, sans-serif',
                'Source Sans Pro, sans-serif',
                'Ubuntu, sans-serif',
                'Work Sans, sans-serif',
                'DM Sans, sans-serif',
                'Georgia, serif',
            );
            return in_array($value, $allowed, true) ? $value : '';
        }
        if ($key === 'card_shadow') {
            if ($value === 'none') return 'none';
            // Very conservative allowlist for box-shadow value
            if (strlen($value) > 140) return '';
            if (!preg_match('/^[0-9a-zA-Z#(),.%\\s+\\-]+$/', $value)) return '';
            return $value;
        }

        return '';
    }

    public function ajax_save_style_customization() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions.', 'google-reviews-plugin')));
        }
        check_ajax_referer('grp_widgets_nonce', 'nonce');

        $style = isset($_POST['style']) ? sanitize_key(wp_unslash($_POST['style'])) : '';
        $variant = isset($_POST['variant']) ? sanitize_key(wp_unslash($_POST['variant'])) : 'light';
        $data = isset($_POST['data']) ? (array) $_POST['data'] : array();

        if (empty($style) || !isset($this->styles[$style])) {
            wp_send_json_error(array('message' => __('Invalid style.', 'google-reviews-plugin')));
        }
        if (!in_array($variant, array('light', 'dark', 'auto'), true)) {
            $variant = 'light';
        }

        $sanitized = array();
        foreach ($data as $key => $val) {
            $key = sanitize_key($key);
            $val = is_string($val) ? wp_unslash($val) : '';
            $clean = $this->sanitize_style_customization_value($key, $val);
            if ($clean !== '') {
                $sanitized[$key] = $clean;
            }
        }

        $all = $this->get_style_customizations_all();
        if (!isset($all[$style]) || !is_array($all[$style])) {
            $all[$style] = array();
        }
        $all[$style][$variant] = $sanitized;
        update_option(self::STYLE_CUSTOMIZATIONS_OPTION, $all, false);

        wp_send_json_success(array(
            'style' => $style,
            'variant' => $variant,
            'data' => $sanitized,
        ));
    }
    
    /**
     * Get modern style CSS
     * Design: High-end SaaS / startup feel. Glassmorphism, subtle motion, dark-mode first.
     */
    private function get_modern_css() {
        $use_theme_font = (bool) get_option('grp_use_theme_font', false);
        $font_family = $use_theme_font ? 'inherit' : "'Inter', 'Space Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";
        
        return "
        .grp-style-modern .grp-reviews {
            font-family: {$font_family};
            background: var(--grp-background);
        }
        
        .grp-style-modern .grp-review {
            background: var(--grp-card_background, rgba(255, 255, 255, 0.08));
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--grp-border, rgba(255, 255, 255, 0.15));
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 24px;
            position: relative;
            transition: all 200ms ease;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        }
        
        .grp-style-modern .grp-review:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.18);
        }
        
        .grp-style-modern .grp-review-avatar {
            position: absolute;
            top: -20px;
            left: 24px;
            z-index: 2;
        }
        
        .grp-style-modern .grp-review-avatar img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--grp-border, rgba(255, 255, 255, 0.15));
            background: var(--grp-background);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 200ms ease;
        }
        
        .grp-style-modern .grp-review:hover .grp-review-avatar img {
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }
        
        .grp-style-modern .grp-review-rating {
            margin-top: 20px;
            margin-bottom: 16px;
        }
        
        .grp-style-modern .grp-star {
            color: var(--grp-star, #FBBC05);
            font-size: clamp(14px, 1.2vw, 16px);
            margin-right: 3px;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
        }
        
        .grp-style-modern .grp-review-text {
            color: var(--grp-text);
            font-size: clamp(14px, 1vw, 15px);
            line-height: 1.6;
            margin-bottom: 20px;
            transition: all 200ms ease;
        }
        
        .grp-style-modern .grp-review-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            opacity: 0.7;
            transform: translateY(4px);
            transition: all 200ms ease;
        }
        
        .grp-style-modern .grp-review:hover .grp-review-meta {
            opacity: 1;
            transform: translateY(0);
        }
        
        .grp-style-modern .grp-review-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .grp-style-modern .grp-author-name {
            font-weight: 600;
            color: var(--grp-text);
            font-size: clamp(13px, 1vw, 14px);
            transition: all 200ms ease;
        }
        
        .grp-style-modern .grp-review-date {
            color: var(--grp-muted);
            font-size: clamp(12px, 0.9vw, 13px);
            transition: all 200ms ease;
        }
        
        .grp-style-modern .grp-review-rating-inline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-left: auto;
        }
        
        .grp-style-modern .grp-review-rating-inline .grp-star {
            font-size: clamp(12px, 1vw, 14px);
            margin-right: 0;
        }
        ";
    }
    
    /**
     * Get classic style CSS
     * Design: Timeless, familiar, trustworthy. Works well for local businesses and traditional brands.
     */
    private function get_classic_css() {
        $use_theme_font = (bool) get_option('grp_use_theme_font', false);
        $font_family_base = $use_theme_font ? 'inherit' : "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
        $font_family_name = $use_theme_font ? 'inherit' : "'Georgia', 'Merriweather', 'Playfair Display', serif";
        
        return "
        .grp-style-classic .grp-reviews {
            font-family: {$font_family_base};
            background: var(--grp-background);
        }
        
        .grp-style-classic .grp-review {
            background: var(--grp-background);
            border: 1px solid var(--grp-border, #D1D5DB);
            border-radius: 4px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: none;
        }
        
        .grp-style-classic .grp-review-rating {
            margin-bottom: 16px;
            margin-top: 0;
        }
        
        .grp-style-classic .grp-star {
            color: var(--grp-star, #FBBC05);
            font-size: 16px;
            margin-right: 3px;
        }
        
        .grp-style-classic .grp-review-text {
            color: var(--grp-text, #111827);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 20px;
            font-family: {$font_family_base};
        }
        
        .grp-style-classic .grp-review-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--grp-border, #D1D5DB);
        }
        
        .grp-style-classic .grp-review-avatar {
            flex-shrink: 0;
        }
        
        .grp-style-classic .grp-review-avatar img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .grp-style-classic .grp-review-author {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }
        
        .grp-style-classic .grp-author-name {
            font-weight: 600;
            color: var(--grp-text, #111827);
            font-size: 15px;
            font-family: {$font_family_name};
            line-height: 1.4;
        }
        
        .grp-style-classic .grp-review-date {
            color: var(--grp-muted);
            font-size: 13px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.4;
        }
        ";
    }
    
    /**
     * Get minimal style CSS
     * Design: Clean, neutral, content-first. Native to modern SaaS dashboards.
     */
    private function get_minimal_css() {
        $use_theme_font = (bool) get_option('grp_use_theme_font', false);
        $font_family = $use_theme_font ? 'inherit' : "-apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', 'Roboto', 'Helvetica Neue', Arial, sans-serif";
        
        return "
        .grp-style-minimal .grp-reviews {
            font-family: {$font_family};
        }
        
        .grp-style-minimal .grp-review {
            background: var(--grp-background);
            border: none;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
            max-width: 420px;
            width: 100%;
        }
        
        .grp-style-minimal .grp-review:last-child {
            margin-bottom: 0;
        }
        
        .grp-style-minimal .grp-review-rating {
            margin-bottom: 12px;
        }
        
        .grp-style-minimal .grp-star {
            color: var(--grp-star, #FBBC05);
            font-size: 16px;
            margin-right: 2px;
            line-height: 1;
        }
        
        .grp-style-minimal .grp-review-text {
            color: var(--grp-text);
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 16px;
            font-weight: 400;
        }
        
        .grp-style-minimal .grp-review-meta {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .grp-style-minimal .grp-review-avatar {
            flex-shrink: 0;
        }
        
        .grp-style-minimal .grp-review-avatar img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            filter: grayscale(100%);
        }
        
        .grp-style-minimal .grp-review-author {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .grp-style-minimal .grp-author-name {
            font-weight: 600;
            color: var(--grp-text);
            font-size: 14px;
            line-height: 1.4;
        }
        
        .grp-style-minimal .grp-review-date {
            color: var(--grp-muted);
            font-size: 13px;
            line-height: 1.4;
        }
        ";
    }
    
    /**
     * Get corporate style CSS
     * Design: Trust, authority, professionalism. Ideal for B2B, finance, legal, engineering.
     */
    private function get_corporate_css() {
        $use_theme_font = (bool) get_option('grp_use_theme_font', false);
        $font_family = $use_theme_font ? 'inherit' : "'IBM Plex Sans', 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";
        
        return "
        .grp-style-corporate .grp-reviews {
            font-family: {$font_family};
            background: var(--grp-background);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .grp-style-corporate .grp-review {
            background: var(--grp-card_background, var(--grp-background_alt));
            border: 1px solid var(--grp-border);
            border-radius: 6px;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .grp-style-corporate .grp-review-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--grp-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .grp-style-corporate .grp-review-header-text {
            font-size: 13px;
            font-weight: 600;
            color: var(--grp-text);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .grp-style-corporate .grp-google-logo {
            width: 20px;
            height: 20px;
            opacity: 0.6;
        }
        
        .grp-style-corporate .grp-review-content {
            padding: 20px;
            flex: 1;
        }
        
        .grp-style-corporate .grp-review-rating {
            margin-bottom: 16px;
            text-align: right;
        }
        
        .grp-style-corporate .grp-star {
            color: var(--grp-star, #FBBC05);
            font-size: 16px;
            margin-left: 2px;
        }
        
        .grp-style-corporate .grp-review-text {
            color: var(--grp-text);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .grp-style-corporate .grp-review-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        
        .grp-style-corporate .grp-review-avatar img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .grp-style-corporate .grp-author-name {
            font-weight: 500;
            color: var(--grp-text);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .grp-style-corporate .grp-review-footer {
            padding: 12px 20px;
            border-top: 1px solid var(--grp-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--grp-background);
        }
        
        .grp-style-corporate .grp-review-date {
            color: var(--grp-muted);
            font-size: 12px;
        }
        
        .grp-style-corporate .grp-verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: var(--grp-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .grp-style-corporate .grp-verified-badge::before {
            content: '✓';
            color: var(--grp-accent, #4285F4);
            font-weight: bold;
        }
        ";
    }
    
    /**
     * Get creative style CSS
     * Design: Personality-forward, eye-catching, social-proof driven. Great for agencies, creatives, ecommerce.
     */
    private function get_creative_css() {
        $use_theme_font = (bool) get_option('grp_use_theme_font', false);
        $font_family = $use_theme_font ? 'inherit' : "'Poppins', 'DM Sans', 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";
        $quote_font_family = $use_theme_font ? 'inherit' : 'Georgia, serif';
        
        return "
        .grp-style-creative .grp-reviews {
            font-family: {$font_family};
        }
        
        .grp-style-creative .grp-review {
            border: none;
            border-radius: 16px;
            padding: 35px 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--grp-gradient_blue, #4285F4), var(--grp-gradient_red, #EA4335));
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .grp-style-creative .grp-review:hover {
            transform: scale(1.02);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
        }
        
        /* Random gradient variations - use data attributes for different colors */
        .grp-style-creative .grp-review[data-gradient='blue'] {
            background: linear-gradient(135deg, #4285F4, #34A853);
        }
        
        .grp-style-creative .grp-review[data-gradient='red'] {
            background: linear-gradient(135deg, #EA4335, #FBBC05);
        }
        
        .grp-style-creative .grp-review[data-gradient='yellow'] {
            background: linear-gradient(135deg, #FBBC05, #EA4335);
        }
        
        .grp-style-creative .grp-review[data-gradient='green'] {
            background: linear-gradient(135deg, #34A853, #4285F4);
        }
        
        .grp-style-creative .grp-review[data-gradient='purple'] {
            background: linear-gradient(135deg, #9C27B0, #E91E63);
        }
        
        .grp-style-creative .grp-review-quote {
            font-size: 48px;
            line-height: 1;
            color: rgba(255, 255, 255, 0.3);
            position: absolute;
            top: 20px;
            left: 25px;
            font-family: {$quote_font_family};
        }
        
        .grp-style-creative .grp-review-rating {
            margin-bottom: 25px;
            text-align: center;
        }
        
        .grp-style-creative .grp-star {
            color: var(--grp-star, #FFFFFF);
            font-size: 32px;
            margin: 0 4px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }
        
        .grp-style-creative .grp-review-text {
            color: var(--grp-text);
            font-size: 20px;
            line-height: 1.6;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
            font-weight: 400;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .grp-style-creative .grp-review-meta {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            position: relative;
            z-index: 1;
        }
        
        .grp-style-creative .grp-review-avatar {
            position: relative;
        }
        
        .grp-style-creative .grp-review-avatar img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.1);
        }
        
        .grp-style-creative .grp-review-author {
            text-align: center;
        }
        
        .grp-style-creative .grp-author-name {
            font-weight: 700;
            color: var(--grp-text);
            font-size: 18px;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 5px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .grp-style-creative .grp-review-date {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        ";
    }
    
    /**
     * Get variant colors
     */
    private function get_variant_colors($style, $variant) {
        // Style-specific color schemes
        $style_schemes = array(
            'minimal' => array(
                'light' => array(
                    'background' => '#FFFFFF',
                    'background_alt' => '#FFFFFF',
                    'text' => '#1F1F1F',
                    'muted' => '#6B7280',
                    'border' => '#E5E7EB',
                    'accent' => '#4285F4',
                    'star' => '#FBBC05'
                ),
                'dark' => array(
                    'background' => '#111111',
                    'background_alt' => '#111111',
                    'text' => '#F5F5F5',
                    'muted' => '#6B7280',
                    'border' => '#374151',
                    'accent' => '#4285F4',
                    'star' => '#FBBC05'
                )
            ),
            'corporate' => array(
                'light' => array(
                    'background' => '#F8FAFC',
                    'background_alt' => '#FFFFFF',
                    'card_background' => '#FFFFFF',
                    'text' => '#111827',
                    'muted' => '#6B7280',
                    'border' => '#E5E7EB',
                    'accent' => '#4285F4',
                    'star' => '#FBBC05'
                ),
                'dark' => array(
                    'background' => '#0B1220',
                    'background_alt' => '#111827',
                    'card_background' => '#111827',
                    'text' => '#F9FAFB',
                    'muted' => '#9CA3AF',
                    'border' => '#374151',
                    'accent' => '#4285F4',
                    'star' => '#FBBC05'
                )
            ),
            'creative' => array(
                'light' => array(
                    'background' => '#FFFFFF',
                    'background_alt' => '#F9FAFB',
                    'text' => '#FFFFFF',
                    'muted' => '#FFFFFF',
                    'border' => 'transparent',
                    'accent' => '#4285F4',
                    'star' => '#FFFFFF',
                    'gradient_blue' => '#4285F4',
                    'gradient_red' => '#EA4335',
                    'gradient_yellow' => '#FBBC05',
                    'gradient_green' => '#34A853'
                ),
                'dark' => array(
                    'background' => '#0F172A',
                    'background_alt' => '#1E293B',
                    'text' => '#FFFFFF',
                    'muted' => '#FFFFFF',
                    'border' => 'transparent',
                    'accent' => '#4285F4',
                    'star' => '#FFFFFF',
                    'gradient_blue' => '#4285F4',
                    'gradient_red' => '#EA4335',
                    'gradient_yellow' => '#FBBC05',
                    'gradient_green' => '#34A853'
                )
            ),
            'modern' => array(
                'light' => array(
                    'background' => '#FFFFFF',
                    'background_alt' => '#F8F9FA',
                    'card_background' => 'rgba(255, 255, 255, 0.95)',
                    'text' => '#1F2937',
                    'muted' => '#6B7280',
                    'border' => 'rgba(0, 0, 0, 0.1)',
                    'accent' => '#4285F4',
                    'star' => '#FBBC05'
                ),
                'dark' => array(
                    'background' => '#0F172A',
                    'background_alt' => '#1E293B',
                    'card_background' => 'rgba(255, 255, 255, 0.08)',
                    'text' => '#F9FAFB',
                    'muted' => '#9CA3AF',
                    'border' => 'rgba(255, 255, 255, 0.15)',
                    'accent' => '#4285F4',
                    'star' => '#FBBC05'
                )
            ),
            'classic' => array(
                'light' => array(
                    'background' => '#FFFFFF',
                    'background_alt' => '#FFFFFF',
                    'text' => '#111827',
                    'muted' => '#6B7280',
                    'border' => '#D1D5DB',
                    'accent' => '#4285F4',
                    'star' => '#FBBC05'
                ),
                'dark' => array(
                    'background' => '#1F2937',
                    'background_alt' => '#374151',
                    'text' => '#F9FAFB',
                    'muted' => '#9CA3AF',
                    'border' => '#4B5563',
                    'accent' => '#4285F4',
                    'star' => '#FBBC05'
                )
            )
        );
        
        // Default color schemes for other styles
        $default_schemes = array(
            'light' => array(
                'background' => '#ffffff',
                'background_alt' => '#f8f9fa',
                'text' => '#333333',
                'muted' => '#666666',
                'border' => '#e0e0e0',
                'accent' => '#007cba',
                'star' => '#ffc107'
            ),
            'dark' => array(
                'background' => '#2d3748',
                'background_alt' => '#1a202c',
                'text' => '#ffffff',
                'muted' => '#a0aec0',
                'border' => '#4a5568',
                'accent' => '#007cba',
                'star' => '#ffc107'
            )
        );

        // Defaults for non-color customization knobs (per style).
        $knobs = array(
            'modern' => array(
                'card_radius' => 14,
                'card_shadow' => '0 8px 32px rgba(0, 0, 0, 0.12)',
                'font_family' => 'inherit',
                'heading_font_weight' => 600,
                'body_font_weight' => 400,
                'body_line_height' => 1.6,
                'body_letter_spacing' => 0
            ),
            'classic' => array(
                'card_radius' => 4,
                'card_shadow' => 'none',
                'font_family' => 'inherit',
                'heading_font_weight' => 600,
                'body_font_weight' => 400,
                'body_line_height' => 1.6,
                'body_letter_spacing' => 0
            ),
            'minimal' => array(
                'card_radius' => 10,
                'card_shadow' => '0 6px 20px rgba(0, 0, 0, 0.06)',
                'font_family' => 'inherit',
                'heading_font_weight' => 600,
                'body_font_weight' => 400,
                'body_line_height' => 1.6,
                'body_letter_spacing' => 0
            ),
            'corporate' => array(
                'card_radius' => 6,
                'card_shadow' => '0 8px 24px rgba(0, 0, 0, 0.10)',
                'font_family' => 'inherit',
                'heading_font_weight' => 700,
                'body_font_weight' => 400,
                'body_line_height' => 1.6,
                'body_letter_spacing' => 0
            ),
            'creative' => array(
                'card_radius' => 16,
                'card_shadow' => '0 8px 24px rgba(0, 0, 0, 0.15)',
                'font_family' => 'inherit',
                'heading_font_weight' => 700,
                'body_font_weight' => 400,
                'body_line_height' => 1.6,
                'body_letter_spacing' => 0
            )
        );

        // Use style-specific colors if available, otherwise use defaults
        $colors = isset($style_schemes[$style][$variant])
            ? $style_schemes[$style][$variant]
            : (isset($default_schemes[$variant]) ? $default_schemes[$variant] : $default_schemes['light']);

        $k = isset($knobs[$style]) ? $knobs[$style] : $knobs['classic'];
        return array_merge($colors, $k);
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
        $use_theme_font = (bool) get_option('grp_use_theme_font', false);
        $font_family = $use_theme_font ? 'inherit' : "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
        
        return "
        .grp-reviews {
            font-family: var(--grp-font_family, {$font_family});
        }
        
        .grp-review {
            position: relative;
            border-radius: var(--grp-card_radius, 0px);
            box-shadow: var(--grp-card_shadow, none);
        }
        
        .grp-review-rating {
            margin-bottom: 10px;
        }
        
        .grp-star {
            display: inline-block;
        }
        
        .grp-review-text {
            margin-bottom: 15px;
            font-weight: var(--grp-body_font_weight, 400);
            line-height: var(--grp-body_line_height, 1.6);
            letter-spacing: var(--grp-body_letter_spacing, 0px);
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
        .grp-author-name,
        .grp-review-header-text {
            font-weight: var(--grp-heading_font_weight, 600);
        }
        ";
    }
}