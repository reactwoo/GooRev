<?php
/**
 * Main plugin class
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class Google_Reviews_Plugin {
    
    /**
     * Plugin instance
     */
    private static $instance = null;
    
    /**
     * Get plugin instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        $this->load_textdomain();
        $this->init_components();
    }
    
    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        // Core classes
        require_once GRP_PLUGIN_DIR . 'includes/class-grp-api.php';
        require_once GRP_PLUGIN_DIR . 'includes/class-grp-reviews.php';
        require_once GRP_PLUGIN_DIR . 'includes/class-grp-shortcode.php';
        require_once GRP_PLUGIN_DIR . 'includes/class-grp-widget.php';
        require_once GRP_PLUGIN_DIR . 'includes/class-grp-styles.php';
        require_once GRP_PLUGIN_DIR . 'includes/class-grp-cache.php';
        require_once GRP_PLUGIN_DIR . 'includes/class-grp-license.php';
        
        // Admin classes
        if (is_admin()) {
            require_once GRP_PLUGIN_DIR . 'includes/admin/class-grp-admin.php';
            require_once GRP_PLUGIN_DIR . 'includes/admin/class-grp-settings.php';
            require_once GRP_PLUGIN_DIR . 'includes/admin/class-grp-dashboard.php';
        }
        
        // Frontend classes
        require_once GRP_PLUGIN_DIR . 'includes/frontend/class-grp-frontend.php';
        require_once GRP_PLUGIN_DIR . 'includes/frontend/class-grp-elementor.php';
        require_once GRP_PLUGIN_DIR . 'includes/frontend/class-grp-gutenberg.php';
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'init_components'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('widgets_init', array($this, 'register_widgets'));
        add_action('elementor/widgets/widgets_registered', array($this, 'register_elementor_widgets'));
        add_action('init', array($this, 'register_gutenberg_blocks'));
    }
    
    /**
     * Initialize components
     */
    public function init_components() {
        // Initialize API
        $this->api = new GRP_API();
        
        // Initialize reviews manager
        $this->reviews = new GRP_Reviews();
        
        // Initialize shortcode
        $this->shortcode = new GRP_Shortcode();
        
        // Initialize styles
        $this->styles = new GRP_Styles();
        
        // Initialize cache
        $this->cache = new GRP_Cache();
        
        // Initialize license manager
        $this->license = new GRP_License();
        
        // Initialize frontend
        if (!is_admin()) {
            $this->frontend = new GRP_Frontend();
        }
    }
    
    /**
     * Load plugin textdomain
     */
    private function load_textdomain() {
        load_plugin_textdomain(
            'google-reviews-plugin',
            false,
            dirname(plugin_basename(GRP_PLUGIN_FILE)) . '/languages'
        );
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'grp-frontend',
            GRP_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            GRP_PLUGIN_VERSION
        );
        
        wp_enqueue_script(
            'grp-frontend',
            GRP_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            GRP_PLUGIN_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('grp-frontend', 'grp_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('grp_nonce'),
            'strings' => array(
                'loading' => __('Loading...', 'google-reviews-plugin'),
                'error' => __('An error occurred. Please try again.', 'google-reviews-plugin'),
            )
        ));
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our admin pages
        if (strpos($hook, 'google-reviews') === false) {
            return;
        }
        
        wp_enqueue_style(
            'grp-admin',
            GRP_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            GRP_PLUGIN_VERSION
        );
        
        wp_enqueue_script(
            'grp-admin',
            GRP_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            GRP_PLUGIN_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('grp-admin', 'grp_admin_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('grp_admin_nonce'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this item?', 'google-reviews-plugin'),
                'saving' => __('Saving...', 'google-reviews-plugin'),
                'saved' => __('Saved!', 'google-reviews-plugin'),
            )
        ));
    }
    
    /**
     * Register widgets
     */
    public function register_widgets() {
        register_widget('GRP_Widget');
    }
    
    /**
     * Register Elementor widgets
     */
    public function register_elementor_widgets() {
        if (class_exists('GRP_Elementor')) {
            $elementor = new GRP_Elementor();
            $elementor->register_widgets();
        }
    }
    
    /**
     * Register Gutenberg blocks
     */
    public function register_gutenberg_blocks() {
        if (class_exists('GRP_Gutenberg')) {
            $gutenberg = new GRP_Gutenberg();
            $gutenberg->register_blocks();
        }
    }
    
    /**
     * Get plugin option
     */
    public function get_option($key, $default = false) {
        $options = get_option('grp_settings', array());
        return isset($options[$key]) ? $options[$key] : $default;
    }
    
    /**
     * Update plugin option
     */
    public function update_option($key, $value) {
        $options = get_option('grp_settings', array());
        $options[$key] = $value;
        update_option('grp_settings', $options);
    }
    
    /**
     * Check if pro version is active
     */
    public function is_pro() {
        return $this->license->is_pro();
    }
    
    /**
     * Get plugin version
     */
    public function get_version() {
        return GRP_PLUGIN_VERSION;
    }
}