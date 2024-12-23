<?php
/**
 * WordPress Test API
 *
 * @package           WordPressTestAPI
 * @author            Dima Balaban
 * @copyright         2024 Dima Balaban
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Test API
 * Plugin URI:        https://example.com/wordpress-test-api
 * Description:       Test Public or Private API by using COOKIES and X-WP-Nonce for your projects.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Dima Balaban
 * Author URI:        https://example.com
 * Text Domain:       wordpress-test-api
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace WordPressTestAPI;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin class
 *
 * @since 1.0.0
 */
class WordPress_Test_API {

    /**
     * Plugin version.
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * Plugin slug.
     *
     * @var string
     */
    const SLUG = 'wordpress-test-api';

    /**
     * Instance of this class.
     *
     * @var WordPress_Test_API
     */
    private static $instance = null;

    /**
     * Get a single instance of this class.
     *
     * @return WordPress_Test_API
     */
    public static function get_instance(): WordPress_Test_API {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        // Prevent direct instantiation
    }

    /**
     * Initialize the plugin.
     *
     * @return void
     */
    public function init(): void {
        $this->define_constants();
        $this->setup_hooks();
    }

    /**
     * Define plugin constants.
     *
     * @return void
     */
    private function define_constants(): void {
        define('WP_TEST_API_VERSION', self::VERSION);
        define('WP_TEST_API_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('WP_TEST_API_PLUGIN_URL', plugin_dir_url(__FILE__));
    }

    /**
     * Set up WordPress hooks and filters.
     *
     * @return void
     */
    private function setup_hooks(): void {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    /**
     * Add menu page to WordPress admin.
     *
     * @return void
     */
    public function add_admin_menu(): void {
        add_menu_page(
            __('API Test Tools', self::SLUG),
            __('API Test Tools', self::SLUG),
            'manage_options',
            self::SLUG,
            [$this, 'render_admin_page'],
            'dashicons-rest-api',
            100
        );
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * @param string $hook The current admin page.
     * @return void
     */
    public function enqueue_admin_assets(string $hook): void {
        if ($hook !== 'toplevel_page_' . self::SLUG) {
            return;
        }

        wp_enqueue_style(
            self::SLUG . '-admin',
            WP_TEST_API_PLUGIN_URL . 'assets/css/admin.css',
            [],
            self::VERSION
        );

        wp_enqueue_script(
            self::SLUG . '-admin',
            WP_TEST_API_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            self::VERSION,
            true
        );
    }

    /**
     * Render the admin page content.
     *
     * @return void
     */
    public function render_admin_page(): void {
        if (!current_user_can('manage_options')) {
            wp_die(
                esc_html__('You do not have sufficient permissions to access this page.', self::SLUG),
                403
            );
        }

        $data = $this->get_api_credentials();
        
        require_once WP_TEST_API_PLUGIN_DIR . 'templates/admin-page.php';
    }

    /**
     * Get API credentials (cookies and nonce).
     *
     * @return array
     */
    private function get_api_credentials(): array {
        $cookies = [
            'logged_in' => isset($_COOKIE[LOGGED_IN_COOKIE]) ? 
                sanitize_text_field(wp_unslash($_COOKIE[LOGGED_IN_COOKIE])) : '',
            'secure_auth' => isset($_COOKIE[SECURE_AUTH_COOKIE]) ? 
                sanitize_text_field(wp_unslash($_COOKIE[SECURE_AUTH_COOKIE])) : '',
        ];

        $cookie_string = $this->build_cookie_string($cookies);

        return [
            'cookie_string' => $cookie_string,
            'nonce' => wp_create_nonce('wp_rest'),
        ];
    }

    /**
     * Build cookie string from cookie array.
     *
     * @param array $cookies Array of cookies.
     * @return string
     */
    private function build_cookie_string(array $cookies): string {
        $parts = [];

        if (!empty($cookies['logged_in'])) {
            $parts[] = LOGGED_IN_COOKIE . '=' . $cookies['logged_in'];
        }

        if (!empty($cookies['secure_auth'])) {
            $parts[] = SECURE_AUTH_COOKIE . '=' . $cookies['secure_auth'];
        }

        return implode('; ', $parts);
    }
}

// Initialize the plugin
add_action('plugins_loaded', function() {
    $plugin = WordPress_Test_API::get_instance();
    $plugin->init();
});

// Activation hook
register_activation_hook(__FILE__, function() {
    // Add activation tasks if needed
    flush_rewrite_rules();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Add cleanup tasks if needed
    flush_rewrite_rules();
});
