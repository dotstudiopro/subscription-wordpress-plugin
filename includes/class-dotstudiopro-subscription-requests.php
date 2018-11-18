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

    }

}