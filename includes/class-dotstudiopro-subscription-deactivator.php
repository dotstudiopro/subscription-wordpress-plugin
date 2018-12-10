<?php

/**
 * Fired during plugin deactivation
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 * 
 * @package           Dotstudiopro_Subscription
 * @subpackage        Dotstudiopro_Subscription/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Dotstudiopro_Subscription
 * @subpackage Dotstudiopro_Subscription/includes
 */
class Dotstudiopro_Subscription_Deactivator {

    /**
     * The function which handles deactivation of our plugin
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        //flush rewrite rules. Don't want no lingering stuff!
        flush_rewrite_rules();
        //would want to use flush_rewrite_rules only but that does not work for some reason??
        delete_option('rewrite_rules');
        //delete the pages which are created while plugin active
        $delete_pages = array();
        $delete_pages['packages'] = get_option('packages');
        $delete_pages['credit-card'] = get_option('credit-card');
        $delete_pages['payment-profile'] = get_option('payment-profile');
        $delete_pages['thankyou'] = get_option('thankyou');

        foreach ($delete_pages as $key => $delete_page):
            if ($delete_page != NULL) {
                wp_delete_post($delete_page, true);
                delete_option($key);
            }
        endforeach;
    }

}
