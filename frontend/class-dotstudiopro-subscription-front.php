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
        $this->assets_dir = plugin_dir_url(__DIR__) . "frontend/assets/";
        $this->cachebuster = date("YmdHi", filemtime( plugin_dir_path(__FILE__) . 'assets/css/subscription.min.css'));
    }

    /**
     * Register the front-end head stylesheets.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
    }

    /**
     * Register the front-end footer stylesheets.
     *
     * @since    1.0.1
     */
    public function enqueue_footer_styles() {
        wp_enqueue_style('subscription', $this->assets_dir . 'css/subscription.min.css', array(), $this->cachebuster, 'all');
        wp_enqueue_script('jquery.match.height', $this->assets_dir . 'js/jquery.match.height.min.js', array(), $this->cachebuster, true);
        wp_enqueue_script('jquery-confirm', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js', array(), $this->cachebuster, true);
        wp_enqueue_style('jquery-confirm', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css', array(), $this->cachebuster, 'all');
        wp_enqueue_script('subscription-custom', $this->assets_dir . 'js/subscription-custom.min.js', array('jquery-confirm', 'jquery.match.height'), false, true);
        wp_enqueue_script('countries', $this->assets_dir . 'js/countries.min.js', array(), $this->cachebuster, true);
        wp_enqueue_script('jquery.creditCardValidator', $this->assets_dir . 'js/jquery.creditCardValidator.min.js', array(), $this->cachebuster, true);
        wp_localize_script('subscription-custom', 'customVars', array('basedir' => plugin_dir_url(__DIR__), 'ajaxurl' => admin_url('admin-ajax.php')));
    }

    /**
     * Create script tags for deferring the load of scripts and styles as needed
     * @param array $arr The array of files to get script tags for
     *
     * @since    1.0.1
     * @return string|boolean The parsed script tags, or false if something is wrong
     */
    public function footer_script_defer($arr) {
        if (!is_array($arr)) return false;
        if (function_exists('dsp_bootstrap_footer_script_defer')) {
            return dsp_bootstrap_footer_script_defer($arr);
        }
        return false;
    }

    /**
     * Function to validate the couponcode if user has applied the coupon on payment form
     *
     * @global type $client_token
     * @since 1.0.0
     */
    public function validate_couponcode() {
        global $client_token;
        if ($client_token && wp_verify_nonce($_POST['nonce'], 'validate_couponcode')) {
            $coupon = $_POST['coupon'];
            $response = $this->dotstudiopro_subscription->validateCoupon($client_token, $coupon);
            if (is_wp_error($response)) {
                $send_response = array('message' => 'Server Error : ' . $response->get_error_message());
                wp_send_json_error($send_response, 403);
            } elseif (isset($response['success']) && $response['success'] == 1) {
                $send_response = array('message' => 'Your subscription has been created.');
                wp_send_json_success($send_response, 200);
            } else {
                $send_response = array('message' => 'Internal Server Error');
                wp_send_json_error($send_response, 500);
            }
        } else {
            $send_response = array('message' => 'Internal Server Error');
            wp_send_json_error($send_response, 500);
        }
    }

    /**
     * Function to create the user's payment profile for first time subscription
     *
     * @global type $client_token
     * @since 1.0.0
     */
    public function create_payment_profile() {
        global $client_token;
        if ($client_token && wp_verify_nonce($_POST['nonce'], 'submit_payment')) {
            parse_str($_POST['formData'], $formData);
            $response = $this->dotstudiopro_subscription->createPaymentProfileandSubscribe($client_token, $formData);
            if (is_wp_error($response)) {
                $send_response = array('message' => 'Server Error : ' . $response->get_error_message());
                wp_send_json_error($send_response, 403);
            } elseif (isset($response['success']) && $response['success'] == 1) {
                $send_response = array('message' => 'Your subscription has been created.');
                wp_send_json_success($send_response, 200);
            } else {
                $send_response = array('message' => 'Internal Server Error');
                wp_send_json_error($send_response, 500);
            }
        } else {
            $send_response = array('message' => 'Internal Server Error');
            wp_send_json_error($send_response, 500);
        }
    }
    /**
     * Function to create the user's payment profile for first time subscription
     *
     * @global type $client_token
     * @since 1.0.0
     */
    public function update_payment_profile() {
        global $client_token;
        if ($client_token && wp_verify_nonce($_POST['nonce'], 'update_payment')) {
            parse_str($_POST['formData'], $formData);
            $response = $this->dotstudiopro_subscription->updatePaymentProfile($client_token, $formData);
            if (is_wp_error($response)) {
                $send_response = array('message' => 'Server Error : ' . $response->get_error_message());
                wp_send_json_error($send_response, 403);
            } elseif (isset($response['success']) && $response['success'] == 1) {
                $send_response = array('message' => 'Your payment profile has been updated.');
                wp_send_json_success($send_response, 200);
            } else {
                $send_response = array('message' => 'Internal Server Error');
                wp_send_json_error($send_response, 500);
            }
        } else {
            $send_response = array('message' => 'Internal Server Error');
            wp_send_json_error($send_response, 500);
        }
    }

    /**
     * Function to update subscription package
     * @since 1.0.0
     */
    public function update_subscription() {
        global $client_token;
        if ($client_token && wp_verify_nonce($_POST['nonce'], 'upadate_subscription_plan')) {
            $subscription_id = ($_POST['subscription_id']) ? $_POST['subscription_id'] : '';
            $response = $this->dotstudiopro_subscription->updateSubscription($client_token, $subscription_id);
            if (is_wp_error($response)) {
                $send_response = array('message' => 'Server Error : ' . $response->get_error_message());
                wp_send_json_error($send_response, 403);
            } elseif (isset($response['success']) && $response['success'] == 1) {
                $send_response = array('message' => 'Your Package is updated successfully.');
                wp_send_json_success($send_response, 200);
            } else {
                $send_response = array('message' => 'Internal Server Error');
                wp_send_json_error($send_response, 500);
            }
        } else {
            $send_response = array('message' => 'Internal Server Error');
            wp_send_json_error($send_response, 500);
        }
    }

    /**
     * function to cancle subscription
     * @since 1.0.0
     */
    public function cancle_subscription() {
        global $client_token;
        if ($client_token && wp_verify_nonce($_POST['nonce'], 'cancle_subscription_plan')) {
            $response = $this->dotstudiopro_subscription->cancelSubscription($client_token);
            if (is_wp_error($response)) {
                $send_response = array('message' => 'Server Error : ' . $response->get_error_message());
                wp_send_json_error($send_response, 403);
            } elseif (isset($response['success']) && $response['success'] == 1) {
                $send_response = array('message' => 'Your Package is updated successfully.');
                wp_send_json_success($send_response, 200);
            } else {
                $send_response = array('message' => 'Internal Server Error');
                wp_send_json_error($send_response, 500);
            }
        } else {
            $send_response = array('message' => 'Internal Server Error');
            wp_send_json_error($send_response, 500);
        }
    }

}
