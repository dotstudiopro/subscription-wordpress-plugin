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
        wp_enqueue_script('jquery.creditCardValidator', plugin_dir_url(__FILE__) . 'assets/js/jquery.creditCardValidator.js', array(), false, true);
        wp_enqueue_script('jquery-confirm', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js', array(), false, true);
        wp_enqueue_script('custom', plugin_dir_url(__FILE__) . 'assets/js/custom.js', array(), false, true);
        wp_localize_script('custom', 'customVars', array('basedir' => plugin_dir_url(__DIR__), 'ajaxurl' => admin_url('admin-ajax.php')));
        wp_enqueue_style('subscription-style', plugin_dir_url(__FILE__) . 'assets/css/subscription-style.css', array(), $this->version, 'all');
        wp_enqueue_style('jquery-confirm', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css', array(), $this->version, 'all');
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
