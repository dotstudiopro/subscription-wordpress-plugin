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

            $cache_key = "get_company_product_summary";
            $cache = get_transient($cache_key);
            if ($cache) return $cache;

            $path = 'subscriptions/summary/';

            $headers = array(
                'x-access-token' => $token
            );

            $result = $this->api_request_get($path, null, $headers);
            set_transient($cache_key, $result, 3600);
            return $result;
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

            $cache_key = "get_list_of_channels_in_product_" . $product_id;
            $cache = get_transient($cache_key);
            if ($cache) return $cache;

            $path = 'subscriptions/channels-by-product/' . $product_id;

            $headers = array(
                'x-access-token' => $token
            );

            $result = $this->api_request_get($path, null, $headers);
            set_transient($cache_key, $result, 3600);
            return $result;
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
         * Get user's product.
         * @since 1.1.0
         * @param type $client_token
         * @return type
         */
        public function getUserProducts($client_token, $product_status = null) {

            $token = $this->api_token_check();

            if (!$token && !$client_token)
                return array();

            $path = 'subscriptions/users/products';

            $headers = array(
                'x-access-token' => $token,
                'x-client-token' => $client_token
            );

            $query = '';
            if($product_status)
                $query = array('include_inactive_products' => 'true');

            return $this->api_request_get($path, $query, $headers);
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
         * @param type $coupon_code
         * @param type $billing_address
         * @param type $billing_address_2
         * @param type $billing_city
         * @param type $billing_state
         * @param type $billing_zip
         * @param type $billing_country
         * @return type
         */
        public function createPaymentProfileandSubscribe($client_token, $formData) {

            $token = $this->api_token_check();

            if (!$token && !$client_token && !$formData['subscription_id'])
                return array();

            $path = 'subscriptions/users/create/subscribe_to/' . $formData['subscription_id'];

            $headers = array(
                'content-type'  => 'application/json',
                'x-access-token' => $token,
                'x-client-token' => $client_token
            );

            $query = array('platform' => 'web');

            $body = array(
                "first_name" => $formData['first_name'],
                "last_name" => $formData['last_name'],
                "card_number" => $formData['card_number'],
                "exp_month" => $formData['exp_month'],
                "exp_year" => $formData['exp_year'],
                "cvv" => $formData['cvv'],
                "coupon" => $formData['coupon_code'],
                "billing_address" => $formData['billing_address'],
                "billing_address_2" => $formData['billing_address_2'],
                "billing_city" => $formData['billing_city'],
                "billing_state" => $formData['billing_state'],
                "billing_zip" => $formData['billing_zip'],
                "billing_country" => $formData['billing_country'],
            );

            return $this->api_request_post($path, $query, $headers, json_encode($body));
        }

        /**
         * subscribe user.
         * @since 1.0.0
         * @param $client_token
         * @param $formData
         * @return type
         */
        public function createWithPayment($client_token, $formData) {

            $token = $this->api_token_check();

            if (!$token && !$client_token)
                return array();

            $path = 'subscriptions/users/create_with_payment';

            $headers = array(
                'content-type'  => 'application/json',
                'x-access-token' => $token,
                'x-client-token' => $client_token
            );

            $body = array(
                "first_name" => $formData['first_name'],
                "last_name" => $formData['last_name'],
                "card_number" => $formData['card_number'],
                "exp_month" => $formData['exp_month'],
                "exp_year" => $formData['exp_year'],
                "cvv" => $formData['cvv'],
                "coupon" => $formData['coupon_code'],
                "billing_address" => $formData['billing_address'],
                "billing_address_2" => $formData['billing_address_2'],
                "billing_city" => $formData['billing_city'],
                "billing_state" => $formData['billing_state'],
                "billing_zip" => $formData['billing_zip'],
                "billing_country" => $formData['billing_country'],
            );

            $query = array('platform' => 'web');

            return $this->api_request_post($path, $query, $headers, json_encode($body));
        }

        /**
         * Import Subscribe to.
         * @since 1.0.0
         * @param $client_token
         * @param $formData
         * @return type
         */
        public function importSubscribeTo($client_token, $formData) {

            $token = $this->api_token_check();

            if (!$token && !$client_token)
                return array();

            $path = 'subscriptions/users/import/subscribe_to/'. $formData['subscription_id'];

            $headers = array(
                'content-type'  => 'application/json',
                'x-access-token' => $token,
                'x-client-token' => $client_token
            );

            $query = array('platform' => 'web', 'coupon' => $formData['coupon_code']);

            return $this->api_request_post($path, $query, $headers, json_encode($body));
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
         * @param type $billing_address
         * @param type $billing_address_2
         * @param type $billing_city
         * @param type $billing_state
         * @param type $billing_zip
         * @param type $billing_country
         * @return type
         */
        //public function updatePaymentProfile($client_token, $first_name, $last_name, $card_number, $exp_month, $exp_year, $cvv, $coupon = null, $billing_address, $billing_address_2 = null, $billing_city, $billing_state, $billing_zip, $billing_country) {
        public function updatePaymentProfile($client_token, $formData) {
            $token = $this->api_token_check();

            if (!$token && !$client_token)
                return array();

            $path = 'subscriptions/payment/update';

            $headers = array(
                'content-type'  => 'application/json',
                'x-access-token' => $token,
                'x-client-token' => $client_token
            );


            $body = array(
                "first_name" => $formData['first_name'],
                "last_name" => $formData['last_name'],
                "billing_address" => $formData['billing_address'],
                "billing_address_2" => $formData['billing_address_2'],
                "billing_city" => $formData['billing_city'],
                "billing_state" => $formData['billing_state'],
                "billing_zip" => $formData['billing_zip'],
                "billing_country" => $formData['billing_country'],
                "card_number" => $formData['card_number'],
                "exp_month" => $formData['exp_month'],
                "exp_year" => $formData['exp_year'],
                "cvv" => $formData['cvv'],
            );
            return $this->api_request_put($path, null, $headers, json_encode($body));
        }

        /**
         * function to update subscription for users
         * @since 1.0.0
         * @param type $client_token
         * @param type $subscription_id
         * @return type
         */
        public function updateSubscription($client_token, $subscription_id, $coupon_code = null) {

            $token = $this->api_token_check();

            if (!$token && !$client_token && !$subscription_id)
                return array();

            $path = 'subscriptions/users/subscribe_to/' . $subscription_id;

            $headers = array(
                'x-access-token' => $token,
                'x-client-token' => $client_token
            );

            if(!empty($coupon_code))
                $query = array('platform' => 'web', 'coupon_code' => $coupon_code);
            else
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
                'content-type'  => 'application/json',
                'x-access-token' => $token,
                'x-client-token' => $client_token

            );

            $body = array(
                "coupon" => $coupon,
            );
            return $this->api_request_post($path, null, $headers, json_encode($body));
        }


        /**
         * Get SVOD/TVOD products for a channel
         * @since 1.1.0
         * @param string $channel_id
         * @return object
         */
        public function getProductsByChannel($channel_id) {

            global $client_token;

            $token = $this->api_token_check();

            if (!$token)
                return array();

            $path = 'subscriptions/products-by-channel/' . $channel_id;

            $headers = array(
                'content-type'  => 'application/json',
                'x-access-token' => $token
            );

            if (!empty($client_token)) {
                $headers['x-client-token'] = $client_token;
            }
            return $this->api_request_get($path, null, $headers);
        }

        /**
         * Get details for a product by DSP id
         * @since 1.1.0
         * @param string $product_id
         * @return object
         */
        public function getProductDetails($product_id) {

            $token = $this->api_token_check();

            if (!$token)
                return array();

            $path = '/subscriptions/details/' . $product_id;

            $headers = array(
                'content-type'  => 'application/json',
                'x-access-token' => $token

            );
            return $this->api_request_get($path, null, $headers);
        }

        /**
         * Get user's purchase history.
         * @since 1.1.0
         * @param type $client_token
         * @return type
         */
        public function getUserPurchaseHistory($client_token) {

            $token = $this->api_token_check();

            if (!$token && !$client_token)
                return array();

            $path = 'subscriptions/purchases';

            $headers = array(
                'x-access-token' => $token,
                'x-client-token' => $client_token
            );

            return $this->api_request_get($path, null, $headers);
        }

    }

}


