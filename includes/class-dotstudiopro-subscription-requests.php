<?php

/**
 * Register all Request for external Aips
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 * 
 * @package           Dotstudiopro_Api
 * @subpackage        Dotstudiopro_Api/includes
 */
if (class_exists('Dsp_External_Api_Request')) {

    class Dotstudiopro_Subscription_Request extends Dsp_External_Api_Request {

        public function __construct() {
            parent::__construct();
        }

        /**
         * View information about subscription plans, and manage subscriptions 
         * for customers.
         * 
         * @since 1.0.0
         * @access public
         */
        public function getCompanyProductSummary() {
            $token = $this->api_token_check();

            if (!$token)
                return array();

            $path = 'subscriptions/summary/';

            $headers = array(
                'x-access-token' => $token
            );

            return $this->api_request_get($path, null, $headers);
        }

        /**
         * Get list of channels in a product.
         * @since 1.0.0
         * @param type $product_id
         * @return type
         */
        public function getListofChannelsinProduct($product_id) {

            $token = $this->api_token_check();

            if (!$token)
                return array();

            $path = 'subscriptions/channels-by-product/' . $product_id;

            $headers = array(
                'x-access-token' => $token
            );

            return $this->api_request_get($path, null, $headers);
        }

        /**
         * Get user's subscriptions.
         * @since 1.0.0
         * @param type $client_token
         * @return type
         */
        public function getUserSubscription($client_token) {

            $token = $this->api_token_check();

            if (!$token && !$client_token)
                return array();

            $path = 'subscriptions/users/active_subscriptions';

            $headers = array(
                'x-access-token' => $token,
                'x-client-token' => $client_token
            );

            return $this->api_request_get($path, null, $headers);
        }

        /**
         * Create payment profile and subscribe.
         * @since 1.0.0
         * @param type $subscription_id
         * @param type $client_token
         * @param type $first_name
         * @param type $last_name
         * @param type $card_number
         * @param type $exp_month
         * @param type $exp_year
         * @param type $cvv
         * @param type $coupon
         * @param type $billing_address
         * @param type $billing_address_2
         * @param type $billing_city
         * @param type $billing_state
         * @param type $billing_zip
         * @param type $billing_country
         * @return type
         */
        public function createPaymentProfileandSubscribe($subscription_id, $client_token, $first_name, $last_name, $card_number, $exp_month, $exp_year, $cvv, $coupon = null, $billing_address, $billing_address_2 = null, $billing_city, $billing_state, $billing_zip, $billing_country) {

            $token = $this->api_token_check();

            if (!$token && !$client_token && !$subscription_id)
                return array();

            $path = 'subscriptions/users/create/subscribe_to/' . $subscription_id;

            $headers = array(
                'x-access-token' => $token,
                'x-client-token' => $client_token
            );

            $query = array('platform' => 'web');

            $body = array(
                "first_name" => $first_name,
                "last_name" => $last_name,
                "card_number" => $card_number,
                "exp_month" => $exp_month,
                "exp_year" => $exp_year,
                "cvv" => $cvv,
                "coupon" => $coupon,
                "billing_address" => $billing_address,
                "billing_address_2" => $billing_address_2,
                "billing_city" => $billing_city,
                "billing_state" => $billing_state,
                "billing_zip" => $billing_zip,
                "billing_country" => $billing_country,
            );

            return $this->api_request_post($path, $query, $headers, $body);
        }

        /**
         * function to update payment profile.
         * @since 1.0.0
         * @param type $client_token
         * @param type $first_name
         * @param type $last_name
         * @param type $card_number
         * @param type $exp_month
         * @param type $exp_year
         * @param type $cvv
         * @param type $coupon
         * @param type $billing_address
         * @param type $billing_address_2
         * @param type $billing_city
         * @param type $billing_state
         * @param type $billing_zip
         * @param type $billing_country
         * @return type
         */
        public function updatePaymentProfile($client_token, $first_name, $last_name, $card_number, $exp_month, $exp_year, $cvv, $coupon = null, $billing_address, $billing_address_2 = null, $billing_city, $billing_state, $billing_zip, $billing_country) {

            $token = $this->api_token_check();

            if (!$token && !$client_token)
                return array();

            $path = 'subscriptions/payment/update';

            $headers = array(
                'x-access-token' => $token,
                'x-client-token' => $client_token
            );


            $body = array(
                "first_name" => $first_name,
                "last_name" => $last_name,
                "card_number" => $card_number,
                "exp_month" => $exp_month,
                "exp_year" => $exp_year,
                "cvv" => $cvv,
                "coupon" => $coupon,
                "billing_address" => $billing_address,
                "billing_address_2" => $billing_address_2,
                "billing_city" => $billing_city,
                "billing_state" => $billing_state,
                "billing_zip" => $billing_zip,
                "billing_country" => $billing_country,
            );

            return $this->api_request_put($path, null, $headers, $body);
        }

        /**
         * function to update subscription for users
         * @since 1.0.0
         * @param type $client_token
         * @param type $subscription_id
         * @return type
         */
        public function updateSubscription($client_token, $subscription_id) {

            $token = $this->api_token_check();

            if (!$token && !$client_token && !$subscription_id)
                return array();

            $path = 'subscriptions/users/subscribe_to/' . $subscription_id;

            $headers = array(
                'x-access-token' => $token,
                'x-client-token' => $client_token
            );

            $query = array('platform' => 'web');

            return $this->api_request_post($path, $query, $headers, null);
        }

        /**
         * Cancel subscription.
         * @since 1.0.0
         * @param type $client_token
         * @return type
         */
        public function cancelSubscription($client_token) {

            $token = $this->api_token_check();

            if (!$token && !$client_token)
                return array();

            $path = 'subscriptions/users/cancel';

            $headers = array(
                'x-access-token' => $token,
                'x-client-token' => $client_token
            );

            return $this->api_request_post($path, null, $headers, null);
        }

        /**
         * function to Validate coupon
         * @since 1.0.0
         * @param type $client_token
         * @param type $coupon
         * @return type
         */
        public function validateCoupon($client_token, $coupon) {

            $token = $this->api_token_check();

            if (!$token && !$client_token && !$coupon)
                return array();

            $path = 'subscriptions/validate_coupon';

            $headers = array(
                'x-access-token' => $token,
                'x-client-token' => $client_token
            );

            return $this->api_request_post($path, null, $headers, null);
        }

    }

}