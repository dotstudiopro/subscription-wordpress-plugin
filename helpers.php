<?php

/**
 * Helper functions
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.0
 *
 * @package           Dotstudiopro_Api
 */

/**
  *  Parse credit card information from subscription CC info
  *
  *  @type    function
  *  @since   1.0.0
  *
  *  @param   $sub_info (array) The subscription info array
  *  @return  object The CC info object
  */
function dsp_parse_cc_info( $sub_info ) {
    $cc_info_obj = new stdClass;
    if (empty($sub_info) || empty($sub_info['credit_card'])) return $cc_info_obj;
    $cc_info = $sub_info['credit_card'];
    $cc_info_obj->card_number = !empty($cc_info['masked_card_number']) ? $cc_info['masked_card_number'] : '';
    $cc_info_obj->exp_month = !empty($cc_info['expiration_month']) ? $cc_info['expiration_month'] : '';
    $cc_info_obj->exp_year = !empty($cc_info['expiration_year']) ? $cc_info['expiration_year'] : '';
    $cc_info_obj->first_name = !empty($cc_info['first_name']) ? $cc_info['first_name'] : '';
    $cc_info_obj->last_name = !empty($cc_info['last_name']) ? $cc_info['last_name'] : '';
    $cc_info_obj->billing_city = !empty($cc_info['billing_city']) ? $cc_info['billing_city'] : '';
    $cc_info_obj->billing_state = !empty($cc_info['billing_state']) ? $cc_info['billing_state'] : '';
    $cc_info_obj->billing_zip = !empty($cc_info['billing_zip']) ? $cc_info['billing_zip'] : '';
    $cc_info_obj->billing_country = !empty($cc_info['billing_country']) ? $cc_info['billing_country'] : '';
    $cc_info_obj->billing_address = !empty($cc_info['billing_address']) ? $cc_info['billing_address'] : '';
    $cc_info_obj->billing_address_2 = !empty($cc_info['billing_address_2']) ? $cc_info['billing_address_2'] : '';
    $cc_info_obj->card_type = !empty($cc_info['card_type']) ? $cc_info['card_type'] : '';
    return $cc_info_obj;
}

/**
  *  Get the TVOD products for a given channel
  *
  *  @type    function
  *  @since   1.0.0
  *
  *  @param   $channel_id (string) The channel to get products for
  *  @return  array TVOD products
  */
function dsp_get_channel_tvod_products($channel_id) {
    if (!class_exists('Dotstudiopro_Subscription_Request')) return [];
    $tkey = 'dsp_get_channel_tvod_products:' . $channel_id;
    $current_products = get_transient($tkey);
    if (!empty($current_products)) return $current_products;
    $dsp_subs_api = new Dotstudiopro_Subscription_Request();
    $channel_products = $dsp_subs_api->getProductsByChannel($channel_id);
    $tvod_products = array_values(array_filter($channel_products['products'], function($cp) {
        return $cp && !empty($cp['product_type']) && $cp['product_type'] === 'tvod';
    }));
    set_transient($tkey, $tvod_products, 300);
    return $tvod_products;
}