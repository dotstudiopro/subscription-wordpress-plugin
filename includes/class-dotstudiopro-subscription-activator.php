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
        if (!is_plugin_active(DOTSTUDIOPRO_API_BASENAME) and current_user_can('activate_plugins')) {
            // Stop activation redirect and show error
            wp_die('Sorry, but this plugin requires the "dotstudioPRO API" plugin to be installed and active. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
        }
    }
}
