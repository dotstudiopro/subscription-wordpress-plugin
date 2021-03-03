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
     * Flush rewrite rules and create pages we need
     * @since    1.0.0
     */
    public static function activate() {

        $dsp_subscription = new Dotstudiopro_Subscription();
        $dsp_subscription_admin = new Dotstudiopro_Subscription_Front($dsp_subscription->get_Dotstudiopro_Subscription(), $dsp_subscription->get_version());
        $dsp_subscription_admin->create_subscription_pages();

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
