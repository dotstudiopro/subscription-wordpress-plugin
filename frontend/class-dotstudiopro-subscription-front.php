<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 *
 * @package           Dotstudiopro_Subscription
 * @subpackage        Dotstudiopro_Subscription/admin
 */
class Dotstudiopro_Subscription_Front {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $name    The ID of this plugin.
     */
    private $name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @var      string    $name       The name of this plugin.
     * @var      string    $version    The version of this plugin.
     */
    public function __construct($name, $version) {
        $this->name = $name;
        $this->version = $version;
        $this->dotstudiopro_subscription = new Dotstudiopro_Subscription_Request();
    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() { 
        wp_enqueue_script('countries', plugin_dir_url(__FILE__) . 'assets/js/countries.js', array(), false, true);
        wp_enqueue_script('jquery.payform', plugin_dir_url(__FILE__) . 'assets/js/jquery.payform.min.js', array(), false, true);
        wp_enqueue_script('custom', plugin_dir_url(__FILE__) . 'assets/js/custom.js', array(), false, true);
        wp_enqueue_style('subscription-style', plugin_dir_url(__FILE__) . 'assets/css/subscription-style.css', array(), $this->version, 'all');
    }
}