<?php

/**
 * Fired during plugin activation
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 *
 * @package           Dotstudiopro_Subscription
 * @subpackage        Dotstudiopro_Subscription/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Dotstudiopro_Subscription
 * @subpackage Dotstudiopro_Subscription/includes
 */
class Dotstudiopro_Subscription_Activator {

    /**
     * Flush rewrite rules
     * @since    1.0.0
     */
    public static function activate() {
        //flush rewrite rules. Just to make sure our rewrite rules from an earlier activation are applied again!
        flush_rewrite_rules();
        //would want to use flush_rewrite_rules only but that does not work for some reason??
        delete_option('rewrite_rules');

        $dsp_subscription = new Dotstudiopro_Subscription();
        $dsp_subscription_admin = new Dotstudiopro_Subscription_Front($dsp_subscription->get_Dotstudiopro_Subscription(), $dsp_subscription->get_version());
        // Ensure we have defined the API basename and that the plugin is active, just to make sure that we don't end up with errors for the constant
        // being undefined here
        if (!defined('DOTSTUDIOPRO_API_BASENAME') || !is_plugin_active(DOTSTUDIOPRO_API_BASENAME) and current_user_can('activate_plugins')) {
            // Stop activation redirect and show error
            wp_die('Sorry, but this plugin requires the "dotstudioPRO API" plugin to be installed and active. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
        }
        self::add_my_custom_pages('Packages', 'packages');
        self::add_my_custom_pages('Credit Card', 'credit-card');
        self::add_my_custom_pages('Payment Profile', 'payment-profile');
        self::add_my_custom_pages('Thankyou', 'thankyou');
    }
    
    public static function add_my_custom_pages($title, $slug, $desc = '', $status = 'publish', $author = 1, $type = 'page'){
        $my_post = array(
            'post_title'    => wp_strip_all_tags($title),
            'post_content'  => $desc,
            'post_status'   => $status,
            'post_name'     => 'packages',
            'post_author'   => $author,
            'post_type'     => $type,
        );
        wp_insert_post( $my_post );
    }
}
