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
     * Plugin components
     */
    public $api;
    public $reviews;
    public $shortcode;
    public $styles;
    public $cache;
    public $license;
    public $frontend;
    public $admin;
    
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
        require_once GRP_PLUGIN_DIR . 'includes/class-grp-addons.php';
        
        // WooCommerce integration (load class so it can hook into addon actions)
        if (class_exists('WooCommerce')) {
            require_once GRP_PLUGIN_DIR . 'includes/class-grp-woocommerce.php';
        }
        
        // Review Widgets addon (load class so it can hook into addon actions)
        require_once GRP_PLUGIN_DIR . 'includes/class-grp-review-widgets.php';
        
        // Admin classes
		if (is_admin()) {
			require_once GRP_PLUGIN_DIR . 'includes/admin/class-grp-admin.php';
			require_once GRP_PLUGIN_DIR . 'includes/admin/class-grp-onboarding.php';
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
        // Defer initialization to WordPress hooks
        add_action('init', array($this, 'load_textdomain'));
        add_action('init', array($this, 'init_components'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('widgets_init', array($this, 'register_widgets'));
        add_action('elementor/widgets/widgets_registered', array($this, 'register_elementor_widgets'));
        add_action('init', array($this, 'register_gutenberg_blocks'));
        
        // Initialize Review Widgets addon early - it will register its menu on admin_menu hook with priority 20
        // This ensures the parent menu 'google-reviews' exists (registered by GRP_Admin at priority 10)
        if (is_admin() && class_exists('GRP_Review_Widgets')) {
            GRP_Review_Widgets::get_instance();
        }
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
        
        // Initialize addons manager (must be initialized before addons)
        $addons = GRP_Addons::get_instance();
        
        // Initialize WooCommerce integration (class handles addon enable check internally)
        if (class_exists('WooCommerce') && class_exists('GRP_WooCommerce')) {
            GRP_WooCommerce::get_instance();
        }
        
        // Note: Review Widgets addon is initialized on admin_menu hook (priority 20) to ensure parent menu exists
        
        // Initialize frontend
        if (!is_admin()) {
            $this->frontend = new GRP_Frontend();
        } else {
            // Initialize admin (adds menus, settings pages, assets)
            if (class_exists('GRP_Admin')) {
                $this->admin = new GRP_Admin();
            }
            
            // Initialize onboarding wizard
            if (class_exists('GRP_Onboarding')) {
                new GRP_Onboarding();
            }
        }
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
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
        
        // Localize script — expose a single consistent object name
        wp_localize_script('grp-admin', 'grp_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('grp_admin_nonce'),
            'strings' => array(
                'testing_connection' => __('Testing connection...', 'google-reviews-plugin'),
                'connection_success' => __('Connection successful!', 'google-reviews-plugin'),
                'connection_failed' => __('Connection failed. Please check your credentials.', 'google-reviews-plugin'),
                'syncing_reviews' => __('Syncing reviews...', 'google-reviews-plugin'),
                'sync_success' => __('Reviews synced successfully!', 'google-reviews-plugin'),
                'sync_failed' => __('Failed to sync reviews.', 'google-reviews-plugin'),
                'confirm_disconnect' => __('Are you sure you want to disconnect?', 'google-reviews-plugin'),
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