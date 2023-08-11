<?php

global $client_token, $dsp_theme_options;

$product_id = isset($_REQUEST['product_id']) ? $_REQUEST['product_id'] : '';
$subscription_id = isset($_REQUEST['subscription_id']) ? $_REQUEST['subscription_id'] : '';
$previous_page_url = isset($_REQUEST['previous_page_url']) ? $_REQUEST['previous_page_url'] : '/thankyou/';

if($subscription_id){
    if(!$client_token):
        wp_redirect('/packages');
    endif;
}
else{
    if(!$client_token):
        wp_redirect('/');
    endif;
}

$dsp_subscription_object = new Dotstudiopro_Subscription_Request();
$subscriptions = $dsp_subscription_object->getCompanyProductSummary();

if(!is_wp_error($subscriptions) && !empty($subscriptions['data'])){
    foreach($subscriptions['data'] as $key => $subscription){
        $subscriptions['data'][$key]['hash_value'] = wp_hash($subscription['_id']);
    }

    if($subscription_id){
        $subscription_exists = array_search($subscription_id, array_column($subscriptions['data'], 'hash_value'));
        if(gettype($subscription_exists) == 'integer')
            $subscription_id = $subscriptions['data'][$subscription_exists]['_id'];
        else
            wp_redirect('/');
    }

    if($product_id){

        $product_exists = array_search($product_id, array_column($subscriptions['data'], 'hash_value'));
        if(gettype($product_exists) == 'integer')
            $product_id = $subscriptions['data'][$product_exists]['_id'];
        else
            wp_redirect('/');
    }
}


if($client_token && wp_verify_nonce($_POST['nonce'], 'credit_card_page')){

    $dsp_subscription_object = new Dotstudiopro_Subscription_Request();
    //$user_products = $dsp_subscription_object->getUserProducts($client_token);
    $user_products = $dsp_subscription_object->getUserProducts($client_token, 'inactive');
    $template_class = new Subscription_Listing_Template();

    $credit_card_info = array();

    $user_subscribe_to_svod_product = false;
   
    // if(!is_wp_error($user_products) && $user_products){
    //     if(isset($user_products['products']['svod']) && !empty($user_products['products']['svod'][0]['product']['id'])){
    //         $credit_card_info = !empty($user_products['paymentInfo']) ? $user_products['paymentInfo'] : array();
    //         $user_subscribe_to_svod_product = true;
    //     }else if(isset($user_products['products']['tvod']) && !empty($user_products['products']['tvod'][0]['product']['id'])){
    //         $credit_card_info = !empty($user_products['paymentInfo']) ? $user_products['paymentInfo'] : array();
    //     }
    //     else if(!is_wp_error($check_for_inactive_subscription) && $check_for_inactive_subscription){
    //       $credit_card_info = !empty($check_for_inactive_subscription['paymentInfo']) ? $check_for_inactive_subscription['paymentInfo'] : array();
    //     }
    // }

    if(!is_wp_error($user_products) && $user_products){
        $credit_card_info = !empty($user_products['paymentInfo']) ? $user_products['paymentInfo'] : array();
        foreach ($user_products['products']['svod'] as $svod_products) {
          if($svod_products['state'] == 'active'){
            $user_subscribe_to_svod_product = true;
            break;
          }
        }
    }

    if($product_id){
        $product = dsp_get_vod_product_by_id($product_id);
        $name = $product->name;
        $price = $product->price;
        $duration_number = $product->duration->number;
        $duration_unit = $product->duration->unit;
        $duration = $price .' / '. $duration_number . " " . $duration_unit . ($duration_unit > 1 ? "s" : "");
        $product_charigfy_id = $product->chargify_id;
        $user_subscribe_to_svod_product = false;
    }
    else if($subscription_id){
       $product = dsp_get_vod_product_by_id($subscription_id);
       $name = $product->name;

       $price = str_replace("$","",$product->price);
       $interval_unit = !empty($product->duration->unit) ? $product->duration->unit : '';
       $interval = !empty($product->duration->number) ? $product->duration->number : '';
       $price_display = !empty($product->price_display) ? $product->price_display : '';

       $price_period = '';
        if ($price_display == 'total') {
            $price_period = '$'. $price . '<span class="period"> / year</span>';
        } elseif ($price_display == 'monthly') {
            $monthly_price = floor(($price * 100) / $interval) / 100;
            $price_period = '$'. $monthly_price . '<span class="period"> / month</span>';
        } else {
            if ($interval == 12 && $interval_unit == 'month') {
                $monthly_price = floor(($price * 100) / 12) / 100;
                $price_period = '$'.$monthly_price . '<span class="period"> / month ' . '</span>';
            } else {
                $price_period = '$'.$price . '<span class="period"> / ' . $interval_unit . '</span>';
            }
        }
        $duration = $price_period;
        $product_charigfy_id = $product->chargify_id;
        $active_subscription = $name . ' ' . $price_period;
    }else{
        wp_redirect('/');
    }

    get_header();

    if($user_subscribe_to_svod_product){
      $purchaseMessage = "Start your subscription";
    ?>
    <div class="custom-container container pt-5 pb-5 center-page-content">
        <div class="row no-gutters">
            <h3 class="page-title mb-5 center_title">Your Current Subscription</h3>
        <div class="credit-block container mb-5 text-center">
            <p><?php echo $active_subscription; ?></p>
            <p><a class="btn btn-secondary btn-ds-secondary"  href="/packages">Manage your Subscriptions</a></p>
        </div>
        </div>
     </div>
    <?php
    }else{
      $purchaseMessage = "Complete Purchase";
    ?>
    <style type="text/css">
        .main-color-bg {
            background-color: <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
        }
        .main-color {
            color: <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
        }
        .main-body-txt {
            font-family: <?php echo $dsp_theme_options['opt-typography-body']; ?>;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <div class="custom-container container pt-5 pb-5">
        <div class="row no-gutters">
            <h3 class="page-title mb-5 center_title">Make a Payment</h3>
        </div>
        <div class="row no-gutters">
            <div class='product-detail-banner main-color-bg'>
                <div class='product-detail-name mt-2 ml-4 main-body-txt'>
                    <h3><?php echo $name; ?></h3>
                </div>
                <div class='product-detail-duration-price main-body-txt mt-2 mr-4'>
                    <h3>Available for <?php echo $duration; ?></h3>
                </div>
            </div>
        </div>
        <div class="payment_information">
            <?php
            if(!empty($credit_card_info)){
            ?>
            <div class="credit-block container mt-3 mb-5 bill_info">
              <div class="row row-fluid complete_payment ">
                  <form  action="<?php echo $previous_page_url;?>" class="w-100" id="form_complete_payment" name="form_complete_payment">
                      <input type="hidden"  name="subscription_id" value="<?php echo $product_charigfy_id; ?>">
                      <input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('submit_complete_payment'); ?>">
                  
                      <div class="row row-fluid">
                          <div class="col-md-6 col-sm-12 pp-pr pt-3">
                              <div class="wrapper">
                                  <h4 class="mt-3 mb-3">Billing Info<a href="/payment-profile/" class="ml-4" target="_blank"><i class="fas fa-pencil main-color"></i></a></h4>
                                  <div class="form-group credit-group">
                                      <div class="form-group credit-group">
                                          <label class="credit_card_label_info" for="first_name">First Name</label>
                                          <span class="billing_value"><?php echo ($credit_card_info['first_name']) ? :"&nbsp;";?></span>
                                      </div>
                                      <div class="form-group credit-group">
                                          <label class="credit_card_label_info" for="last_name">Last Name</label>
                                          <span class="billing_value"><?php echo ($credit_card_info['last_name']) ? :"&nbsp;";?></span>
                                      </div>
                                      <div class="form-group credit-group">
                                          <label class="credit_card_label_info" for="billing_address">Address</label>
                                          <span class="billing_value"><?php echo ($credit_card_info['billing_address']) ? :"&nbsp;";?></span>
                                      </div>
                                      <div class="form-group credit-group">
                                          <label class="credit_card_label_info" for="billing_address_2">Address 2</label>
                                          <span class="billing_value"><?php echo ($credit_card_info['billing_address_2']) ? :"&nbsp;";?></span>
                                      </div>
                                      <div class="form-group credit-group">
                                          <label class="credit_card_label_info" for="billing_city">City</label>
                                          <span class="billing_value"><?php echo ($credit_card_info['billing_city']) ? :"&nbsp;";?></span>
                                      </div>
                                      <div class="form-group credit-group">
                                          <label class="credit_card_label_info" for="billing_zip">Zip Code</label>
                                          <span class="billing_value"><?php echo ($credit_card_info['billing_zip']) ? :"&nbsp;";?></span>
                                      </div>
                                      <div class="form-group credit-group">
                                          <label class="credit_card_label_info" for="billing_country">Country</label>
                                          <span class="billing_value"><?php echo ($credit_card_info['billing_country']) ? :"&nbsp;";?></span>
                                      </div>
                                      <div class="form-group credit-group">
                                          <label class="credit_card_label_info" for="billing_state">State / Region</label>
                                          <span class="billing_value"><?php echo ($credit_card_info['billing_state']) ? :"&nbsp;";?></span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-6 col-sm-12 b-info pt-3">
                              <div class="wrapper">
                                  <h4 class="mt-3 mb-3">Credit Card Info <a href="/payment-profile/" class="ml-4" target="_blank"><i class="fas fa-pencil main-color"></i></a></h4>
                                  <div class="your-card pb-2 mb-3" style="margin: unset;">
                                      <div class="bank-name" title="BestBank"><?php echo strtoupper($credit_card_info['card_type'] . ' Card'); ?></div>
                                      <div class="chip">
                                          <img src="<?php echo plugins_url() . '/wordpress-subscription-plugin/frontend/assets/images/chip.svg' ?>">
                                      </div>
                                      <div class="data">
                                          <div class="pan"><?php echo $credit_card_info['masked_card_number']; ?></div>
                                          <div class="exp-date-wrapper">
                                              <div class="left-label">EXPIRES END</div>
                                              <div class="exp-date">
                                                  <div class="upper-labels">MONTH/YEAR</div>
                                                  <div class="date"><?php echo str_pad($credit_card_info['expiration_month'], 2, '0', STR_PAD_LEFT) . '/' . substr($credit_card_info['expiration_year'], 2, 2); ?></div>
                                              </div>
                                          </div>
                                          <div class="name-on-card"><?php echo $credit_card_info['first_name']. ' ' . $credit_card_info['last_name']; ?></div>
                                      </div>
                                      <div class="lines-down"></div>
                                      <div class="lines-up"></div>
                                  </div>
                                  <div class="card_info">
                                      <h5 class="pt-2 pb-2">CARD NUMBER</h5>
                                      <div class="card_number_info">
                                          <p class="mr-5 pull-left"><?php echo $credit_card_info['masked_card_number']; ?></p>
                                          <p><?php echo strtoupper($credit_card_info['card_type']); ?></p>
                                      </div>
                                  </div>
                                  <div class="coupon_code_info pt-3">
                                      <h5 class="credit_card_label">Coupon Code</h5>
                                      <div class="row">
                                          <div class="col-sm-6 sm-mb-3">
                                              <input type="text" class="form-control credit_card_input" id="coupon_code" name="coupon_code">
                                          </div>
                                          <div class="col-sm-6">
                                              <div id="coupon_wrapper" class="pull-left">
                                                  <a class="btn btn-secondary btn-ds-secondary" id="validate_coupon" href="#" data-action="validate_couponcode" data-nonce="<?php echo wp_create_nonce('validate_couponcode'); ?>">Validate Coupon</a>
                                              </div>
                                              <div class="coupon-responce pull-left p-2" style="display:none"></div>
                                          </div>
                                          <div class="coupon-messages-notices m-3">

                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <?php echo do_shortcode("[recaptcha]"); ?>
                    </form>
                  </div>
                  <div class="mx-auto pt-3">
                      <button type="submit" id="complete_payment" class="btn btn-secondary btn-ds-secondary mt-3" data-action="complete_payment" data-previouspageurl="<?php echo $previous_page_url; ?>">Complete Payment</button>
                  </div>
              </div>
              <div class="row row-fluid">
                  <div class="cc-messages-notices m-3">
                  </div>
              </div>
            </div>
            <?php
            }else{
            ?>
            <div class="credit-block mb-5 container">
                <form action="<?php echo $previous_page_url;?>" class="w-100 needs-validation" novalidate name="payment" id="form_payment">
                    <div class="row row-fluid">
                        <div class="form-group credit-group select w-100 ml-3 mr-3">
                            <input type="hidden" name="subscription_id" id="subscription_id" value="<?php echo $product_charigfy_id; ?>">
                            <input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('submit_payment'); ?>">
                        </div>
                    </div>
                    <div class="row row-fluid">
                        <div class="col-md-6 col-sm-12">
                            <div class="wrapper">
                                <h4 class="mt-3 mb-3">Billing Info</h4>
                                <div class="form-group credit-group">
                                    <div class="row form-group credit-group required">
                                        <div class="col-md-6 sm-mb-3">
                                            <label class="credit_card_label" for="first_name">First Name</label>
                                            <input type="text" class="form-control credit_card_input" id="first_name" name="first_name" required>
                                            <div class="invalid-feedback">
                                                First name is required.
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="credit_card_label" for="last_name">Last Name</label>
                                            <input type="text" class="form-control credit_card_input" id="last_name" name="last_name" required>
                                            <div class="invalid-feedback">
                                                Last name is required.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group credit-group required">
                                        <label class="credit_card_label" for="billing_address">Address</label>
                                        <input type="text" class="form-control credit_card_input" id="billing_address" name="billing_address" required>
                                        <div class="invalid-feedback">
                                            Address field is required.
                                        </div>
                                    </div>
                                    <div class="form-group credit-group">
                                        <label class="credit_card_label">Address 2</label>
                                        <input type="text" class="form-control credit_card_input" id="billing_address_2" name="billing_address_2" >
                                    </div>
                                    <div class="row form-group credit-group required">
                                        <div class="col-md-6 sm-mb-3">
                                            <label class="credit_card_label" for="billing_city">City</label>
                                            <input type="text" class="form-control credit_card_input" id="billing_city" name="billing_city" required>
                                            <div class="invalid-feedback">
                                                Please select city.
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="credit_card_label" for="billing_zip">Zipcode</label>
                                            <input type="text" class="form-control credit_card_input" id="billing_zip" name="billing_zip" required>
                                            <div class="invalid-feedback">
                                                Zipcode field is required.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group credit-group">
                                        <label class="credit_card_label" for="billing_country">Country</label>
                                        <select class="form-control credit_card_input crs-country" id="billing_country" name="billing_country" data-region-id="billing_state" data-default-value="US" data-value="shortcode" required>
                                            <option value>Select Country</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select country
                                        </div>
                                    </div>
                                    <div class="form-group credit-group required">
                                        <label class="credit_card_label" for="billing_state">State / Region</label>
                                        <select class="form-control credit_card_input" id="billing_state" name="billing_state" data-value="shortcode" required></select>
                                        <div class="invalid-feedback">
                                            Please select state/region
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="wrapper">
                                <h4 class="mt-3 mb-3">Credit Card Info</h4>
                                <div class="form-group credit-group required">
                                    <div class="form-group-medium">
                                        <label class="credit_card_label" for="card_number">Card Number</label>
                                        <input type="text" class="form-control credit_card_input" id="card_number" name="card_number" maxlength="19" required>
                                        <div class="invalid-feedback">
                                            Invalid card Number.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group credit-group required">
                                    <div class="row row-fluid">
                                        <div class="col-md-6 sm-mb-3">
                                            <label class="credit_card_label" for="exp_month">Expiry month</label>
                                            <select name="exp_month" id="exp_month" class="form-control credit_card_input" required>
                                                <option value>Select Month</option>
                                                <?php
                                                for($month = 1; $month <= 12; $month++){
                                                    $month = str_pad($month, 2, '0', STR_PAD_LEFT);
                                                    $monthName = date('M', mktime(0, 0, 0, $month, 10));
                                                    echo '<option value="' . $month . '">' . $month . ' ('. $monthName . ')</option>';
                                                }
                                                ?>
                                            </select>
                                            <div class="invalid-feedback">
                                                Please select card expiry month
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="credit_card_label" for="exp_year">Expiry year</label>
                                            <select name="exp_year" id="exp_year" class="form-control credit_card_input" required>
                                                <option value>Select Year</option>
                                                <?php
                                                for($year = date('Y'); $year <= date('Y') + 25; $year++){
                                                    echo '<option value="' . $year . '">' . $year . '</option>';
                                                }
                                                ?>
                                            </select>
                                            <div class="invalid-feedback">
                                                Please select card expiry year
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group credit-group required">
                                    <div class="form-group-small margin">
                                        <label class="credit_card_label" for="cvv">CVV</label>
                                        <input type="password" class="form-control credit_card_input" maxlength="4" id="cvv" name="cvv" required>
                                        <div class="invalid-feedback">
                                            Invalid CVV.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group credit-group">
                                    <label class="credit_card_label">Coupon Code</label>
                                    <div class="row">
                                        <div class="col-sm-6 sm-mb-3">
                                            <input type="text" class="form-control credit_card_input" id="coupon_code" name="coupon_code">
                                        </div>
                                        <div class="col-sm-6">
                                            <div id="coupon_wrapper" class="pull-left">
                                                <a class="btn btn-secondary btn-ds-secondary" id="validate_coupon" href="#" data-action="validate_couponcode" data-nonce="<?php echo wp_create_nonce('validate_couponcode'); ?>">Validate Coupon</a>
                                            </div>
                                            <div class="coupon-responce pull-left p-2" style="display:none"></div>
                                        </div>
                                        <div class="coupon-messages-notices m-3">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mx-auto">
                            <?php echo do_shortcode("[recaptcha]"); ?>
                            <button type="submit" id="submit_cc" class="btn btn-secondary btn-ds-secondary" data-action="create_payment_profile" data-previouspageurl="<?php echo $previous_page_url; ?>"><?php echo $purchaseMessage; ?></button>
                        </div>
                    </div>
                    <div class="row row-fluid">
                        <div class="cc-messages-notices m-3">
                        </div>
                    </div>
                </form>
            </div>
            <?php
            }
            ?>
            <div id="snackbar"></div>
        </div>
    </div>

    <?php
    }

}else{
    wp_redirect('/');
}
?>
<script>
    jQuery(window).load(function() {
        if(jQuery("div.g-recaptcha").length) {
            grecaptcha.execute();
        }
    });
</script>
<?php
get_footer();
