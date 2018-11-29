<?php
get_header();
global $client_token;

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
                    <h3 class="page-title mb-5">Your Current Subscription</h3>
                </div>
                <div class="credit-block container mb-5">
                    <p><?php echo $active_subscription; ?></p>
                    <p><a class="btn btn-primary"  href="/subscriptions/">Manage your Subscriptions</a></p>
                </div>
                <?php
            } else {
                ?>
                <div class="custom-container container pt-5 pb-5">
                    <div class="row no-gutters">
                        <h3 class="page-title mb-5">Make a Payment</h3>
                    </div>
                    <div class="credit-block container mb-5">
                        <form action="/thankyou" class="w-100" name="payment" id="form_payment">
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
                                        <h4>Billing Info</h4>
                                        <div class="form-group credit-group">
                                            <div class="row row-fluid">
                                                <div class="col-md-6">
                                                    <label class="credit_card_label">First Name</label>
                                                    <input type="text" class="form-control credit_card_input" id="first_name" name="first_name" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="credit_card_label">Last Name</label>
                                                    <input type="text" class="form-control credit_card_input" id="last_name" name="last_name" required>
                                                </div>    
                                            </div>
                                            <div class="form-group credit-group">
                                                <label class="credit_card_label">Address</label>
                                                <input type="text" class="form-control credit_card_input" id="billing_address" name="billing_address" required>
                                            </div>
                                            <div class="form-group credit-group">
                                                <label class="credit_card_label">Address 2</label>
                                                <input type="text" class="form-control credit_card_input" id="billing_address_2" name="billing_address_2" >
                                            </div>
                                            <div class="row row-fluid">
                                                <div class="col-md-6">
                                                    <label class="credit_card_label">City</label>
                                                    <input type="text" class="form-control credit_card_input" id="billing_city" name="billing_city" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="credit_card_label">Zipcode</label>
                                                    <input type="text" class="form-control credit_card_input" id="billing_zip" name="billing_zip" required>
                                                </div>    
                                            </div>
                                            <div class="form-group credit-group">
                                                <label class="credit_card_label">Country</label>

                                                <select class="form-control credit_card_input crs-country" id="billing_country" name="billing_country" data-region-id="billing_state" data-default-value="US" data-value="shortcode" required>
                                                    <option>Select Country</option>
                                                </select>
                                            </div>
                                            <div class="form-group credit-group">
                                                <label class="credit_card_label">State / Region</label>
                                                <select class="form-control credit_card_input" id="billing_state" name="billing_state" data-value="shortcode" required></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="wrapper">
                                        <h4>Credit Card Info</h4>
                                        <div class="form-group credit-group">
                                            <div class="form-group-medium">
                                                <label class="credit_card_label">Card Number</label>
                                                <input type="text" class="form-control credit_card_input" id="card_number" name="card_number" maxlength="19" required>
                                            </div>
                                        </div>
                                        <div class="form-group credit-group">
                                            <div class="row row-fluid">
                                                <div class="col-md-6">
                                                    <label class="credit_card_label">Expiry month</label>
                                                    <input type="number" class="form-control credit_card_input" placeholder="<?php echo date('m'); ?>" min="01" max="12" maxlength="2" id="exp_month" maxlength="2" name="exp_month" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="credit_card_label">Expiry year</label>
                                                    <input type="number" class="form-control credit_card_input" placeholder="<?php echo date('Y'); ?>" id="exp_year" maxlength="4" min="<?php echo date('Y'); ?>" max="<?php echo date('Y') + 20; ?>" name="exp_year" required>
                                                </div>    
                                            </div>
                                        </div>
                                        <div class="form-group credit-group">
                                            <div class="form-group-small margin">
                                                <label class="credit_card_label">CVV</label>
                                                <input type="password" class="form-control credit_card_input" maxlength="3" id="cvv" name="cvv" required>
                                            </div>
                                        </div>
                                        <div class="form-group credit-group">
                                            <label class="credit_card_label">Coupon Code</label>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control credit_card_input" id="coupon_code" name="coupon_code">
                                                </div>
                                                <div class="col-sm-6">
                                                    <div id="coupon_wrapper" class="pull-left">
                                                        <a class="btn btn-primary" id="validate_coupon" href="#" data-action="validate_couponcode" data-nonce="<?php echo wp_create_nonce('validate_couponcode'); ?>">Validate Coupon</a>
                                                    </div>
                                                    <div class="coupon-responce pull-left p-2" style="display:none"></div>
                                                </div>
                                                <div class="messages-notices">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-fluid w-100">
                                    <button type="submit" id="submit_cc" class=" btn btn-primary button-submit" data-action="create_payment_profile">Start your subscription</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php
            }
        }
    }
    get_footer();
