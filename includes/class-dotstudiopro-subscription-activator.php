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

        // create the page if not exists while plugin activated
        if (get_option('packages') == NULL)
            self::add_my_custom_pages('Packages', 'packages');
        if (get_option('credit-card') == NULL)
            self::add_my_custom_pages('Credit Card', 'credit-card');
        if (get_option('payment-profile') == NULL)
            self::add_my_custom_pages('Payment Profile', 'payment-profile');
        if (get_option('thankyou') == NULL)
            self::add_my_custom_pages('Thankyou', 'thankyou');
    }

    public static function add_my_custom_pages($title, $slug, $desc = '', $status = 'publish', $author = 1, $type = 'page') {
        $my_post = array(
            'post_title' => wp_strip_all_tags($title),
            'post_content' => $desc,
            'post_status' => $status,
            'post_name' => $slug,
            'post_author' => $author,
            'post_type' => $type,
        );
        $page_id = wp_insert_post($my_post);
        update_option($slug, $page_id);
    }

}
