<?php
global $client_token;
if(!$client_token):
    wp_redirect('/packages');
endif;

get_header();
$dsp_subscription_object = new Dotstudiopro_Subscription_Request();
$subscriptions = $dsp_subscription_object->getCompanyProductSummary();


if ($client_token) {
    $subscription_id = null;
    $options = null;
    $user_subscribe = $dsp_subscription_object->getUserSubscription($client_token);
    if (!is_wp_error($user_subscribe) && $user_subscribe && !empty($user_subscribe['subscriptions'][0]['subscription']['product']['id'])) {
        $subscription_id = $user_subscribe['subscriptions'][0]['subscription']['product']['id'];
    }


    $post_subscription_id = isset($_REQUEST['subscription_id']) ? $_REQUEST['subscription_id'] : '';

    if (!is_wp_error($subscriptions)) {
        if (!empty($subscriptions)) {
            foreach ($subscriptions['data'] as $subscription) {
                $name = !empty($subscription['name']) ? $subscription['name'] : '';
                $price = !empty($subscription['price']) ? $subscription['price'] : '';
                $new_subscription_id = !empty($subscription['chargify_id']) ? $subscription['chargify_id'] : '';
                $interval_unit = !empty($subscription['duration']['interval_unit']) ? $subscription['duration']['interval_unit'] : '';
                $interval = !empty($subscription['duration']['interval']) ? $subscription['duration']['interval'] : '';
                if ($interval == 12 && $interval_unit == 'month') {
                    $price_period = $price . ' / year';
                } elseif ($interval == 1) {
                    $price_period = $price . ' / ' . $interval_unit;
                } else {
                    $price_period = $price . ' / ' . $interval . ' ' . $interval_unit;
                }
                if ($subscription_id && $subscription_id == $new_subscription_id) {
                    $active_subscription = $name . ' $' . $price_period;
                } else {
                    $selected = '';
                    if ($post_subscription_id && $post_subscription_id == $new_subscription_id) {
                        $selected = 'selected';
                    }
                    $options .= '<option ' . $selected . ' value="' . $new_subscription_id . '">' . $name . ' $' . $price_period . '</option>';
                }
            }
        }
        if ($subscription_id) {
            ?>
            <div class="custom-container container pt-5 pb-5">
                <div class="row no-gutters">
                    <h3 class="page-title mb-5 center_title">Your Current Subscription</h3>
                </div>
                <div class="credit-block container mb-5">
                    <p><?php echo $active_subscription; ?></p>
                    <p><a class="btn btn-primary"  href="/packages">Manage your Subscriptions</a></p>
                </div>
                <?php
            } else {
                ?>
                <div class="custom-container container pt-5 pb-5">
                    <div class="row no-gutters">
                        <h3 class="page-title mb-5 center_title">Make a Payment</h3>
                    </div>
                    <div class="credit-block mb-5 container">
                        <form action="/thankyou/" class="w-100 needs-validation" novalidate name="payment" id="form_payment">
                            <div class="row row-fluid">
                                <div class="form-group credit-group select w-100 ml-3 mr-3">
                                    <select class="form-control" id="subscription_id" name="subscription_id">
                                        <?php echo $options; ?>
                                    </select>
                                    <input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('submit_payment'); ?>">
                                </div>
                            </div>
                            <div class="row row-fluid">
                                <div class="col-md-6 col-sm-6">
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
                                <div class="col-md-6 col-sm-6">
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
                                    <button type="submit" id="submit_cc" class="btn btn-secondary btn-ds-secondary" data-action="create_payment_profile">Start your subscription</button>
                                </div>
                            </div>
                            <div class="row row-fluid">
                                <div class="cc-messages-notices m-3">
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="snackbar"></div>
                <?php
            }
        }
    }
    get_footer();
