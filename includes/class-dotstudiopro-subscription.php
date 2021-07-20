<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 *
 * @package           Dotstudiopro_Subscription
 * @subpackage        Dotstudiopro_Subscription/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Dotstudiopro_Subscription
 * @subpackage Dotstudiopro_Subscription/includes
 */
class Dotstudiopro_Subscription {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Dotstudiopro_Api_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $Dotstudiopro_Subscription    The string used to uniquely identify this plugin.
     */
    protected $Dotstudiopro_Subscription;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the Dashboard and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->Dotstudiopro_Subscription = 'dotstudiopro-subscription';
        $this->version = '1.1.1';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_frontend_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Dotstudiopro_Api_Loader. Orchestrates the hooks of the plugin.
     * - Dotstudiopro_Api_i18n. Defines internationalization functionality.
     * - Dotstudiopro_Api_Admin. Defines all hooks for the dashboard.
     * - Dotstudiopro_Api_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-dotstudiopro-subscription-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-dotstudiopro-subscription-i18n.php';

        /**
         * Load the existing plugin wordpress-pluginv3
         */
        // require_once plugin_dir_path(dirname(__DIR__)) . 'wordpress-pluginv3\includes\class-dotstudiopro-external-api-requests.php'

        /**
         * The class responsible for external API Request
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-dotstudiopro-subscription-requests.php';

        /**
         * The class responsible for defining all actions that occur in the admin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-dotstudiopro-subscription-admin.php';

        /**
         * The class responsible for defining all actions that occur in the frontend.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'frontend/class-dotstudiopro-subscription-front.php';

        /**
         * The class responsible for load the template
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-template-loader.php';

        $this->loader = new Dotstudiopro_Subscription_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Dotstudiopro_Api_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Dotstudiopro_Subscription_i18n();
        $plugin_i18n->set_domain($this->get_Dotstudiopro_Subscription());

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the dashboard functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Dotstudiopro_Subscription_Admin($this->get_Dotstudiopro_Subscription(), $this->get_version());
        $this->loader->add_filter('template_include', $plugin_admin, 'dsp_subscriptions_template_chooser');
    }

    /**
     * Register all hooks related to frontend functionality of the plugin
     *
     * @since 1.0.0
     * @access private
     */
    private function define_frontend_hooks() {
        $v = $this->get_version();
        $plugin_front = new Dotstudiopro_Subscription_Front($this->get_Dotstudiopro_Subscription(), $v);
        $this->loader->add_action('wp_enqueue_scripts', $plugin_front, 'enqueue_styles');
        $this->loader->add_action('get_footer', $plugin_front, 'enqueue_footer_styles');
        $this->loader->add_action('wp_ajax_validate_couponcode', $plugin_front, 'validate_couponcode');
        $this->loader->add_action('wp_ajax_nopriv_validate_couponcode', $plugin_front, 'validate_couponcode');
        $this->loader->add_action('wp_ajax_create_payment_profile', $plugin_front, 'create_payment_profile');
        $this->loader->add_action('wp_ajax_nopriv_create_payment_profile', $plugin_front, 'create_payment_profile');
        $this->loader->add_action('wp_ajax_complete_payment', $plugin_front, 'complete_payment');
        $this->loader->add_action('wp_ajax_nopriv_complete_payment', $plugin_front, 'complete_payment');
        $this->loader->add_action('wp_ajax_update_payment_profile', $plugin_front, 'update_payment_profile');
        $this->loader->add_action('wp_ajax_nopriv_update_payment_profile', $plugin_front, 'update_payment_profile');
        $this->loader->add_action('wp_ajax_update_subscription', $plugin_front, 'update_subscription');
        $this->loader->add_action('wp_ajax_nopriv_update_subscription', $plugin_front, 'update_subscription');
        $this->loader->add_action('wp_ajax_cancle_subscription', $plugin_front, 'cancle_subscription');
        $this->loader->add_action('wp_ajax_nopriv_cancle_subscription', $plugin_front, 'cancle_subscription');
        $this->loader->add_action('dsp_channel_options_no_subscription', $plugin_front, 'show_more_ways_to_watch');
        // Conditionally run the functionality to create pages based on whether or not it has been run for this version
        // of the plugin
        if (!get_option('dspsubs_pages_created_v' . $v)) {
            $this->loader->add_action('init', $plugin_front, 'create_subscription_pages');
            update_option('dspsubs_pages_created_v' . $v, 1);
        }

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_Dotstudiopro_Subscription() {
        return $this->Dotstudiopro_Subscription;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Dotstudiopro_Api_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}