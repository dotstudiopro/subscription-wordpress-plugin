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
class Dotstudiopro_Subscription_Admin {

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
     * Function to load dynamic templates based on the page slug
     *
     * @global type $post
     * @param type $template
     * @return type
     */
    public function dsp_subscriptions_template_chooser($template) {

        global $post;
        if($post):
            $page_slug = $post->post_name;
            $template_class = new Subscription_Listing_Template();
            if ($page_slug == 'packages') {
                $template = $template_class->locate_template('subscriptions');
            }
            if ($page_slug == 'credit-card') {
                $template = $template_class->locate_template('payment');
            }
            if ($page_slug == 'thankyou') {
                $template = $template_class->locate_template('thankyou');
            }
            if ($page_slug == 'payment-profile') {
                $template = $template_class->locate_template('payment-profile');
            }
            if ($page_slug == 'more-ways-to-watch') {
                $template = $template_class->locate_template('tvod_products');
            }
            if ($page_slug == 'product-details') {
                $template = $template_class->locate_template('product_details');
            }
            if ($page_slug == 'package-detail') {
                $template = $template_class->locate_template('package_detail');
            }
             if ($page_slug == 'purchase-history') {
                $template = $template_class->locate_template('purchase_history');
            }
        endif;
        return $template;
    }

}
