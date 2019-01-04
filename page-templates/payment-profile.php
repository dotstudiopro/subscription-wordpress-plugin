<?php
get_header();
global $client_token;

$dsp_subscription_object = new Dotstudiopro_Subscription_Request();



if ($client_token) {
    $subscription_id = null;
    $options = null;
    $user_subscribe = $dsp_subscription_object->getUserSubscription($client_token);
    if (!is_wp_error($user_subscribe) && $user_subscribe && !empty($user_subscribe['subscriptions'][0]['subscription']['product']['id'])) {
        $subscription_id = $user_subscribe['subscriptions'][0]['subscription']['product']['id'];
        $chargify_id = $user_subscribe['subscriptions'][0]['subscription']['credit_card']['id'];
        $card_number = !empty($user_subscribe['subscriptions'][0]['subscription']['credit_card']['masked_card_number']) ? $user_subscribe['subscriptions'][0]['subscription']['credit_card']['masked_card_number'] : '';
        $exp_month = !empty($user_subscribe['subscriptions'][0]['subscription']['credit_card']['expiration_month']) ? $user_subscribe['subscriptions'][0]['subscription']['credit_card']['expiration_month'] : '';
        $exp_year = !empty($user_subscribe['subscriptions'][0]['subscription']['credit_card']['expiration_year']) ? $user_subscribe['subscriptions'][0]['subscription']['credit_card']['expiration_year'] : '';
        $first_name = !empty($user_subscribe['subscriptions'][0]['subscription']['credit_card']['first_name']) ? $user_subscribe['subscriptions'][0]['subscription']['credit_card']['first_name'] : '';
        $last_name = !empty($user_subscribe['subscriptions'][0]['subscription']['credit_card']['last_name']) ? $user_subscribe['subscriptions'][0]['subscription']['credit_card']['last_name'] : '';
        $billing_city = !empty($user_subscribe['subscriptions'][0]['subscription']['credit_card']['billing_city']) ? $user_subscribe['subscriptions'][0]['subscription']['credit_card']['billing_city'] : '';
        $billing_state = !empty($user_subscribe['subscriptions'][0]['subscription']['credit_card']['billing_state']) ? $user_subscribe['subscriptions'][0]['subscription']['credit_card']['billing_state'] : '';
        $billing_zip = !empty($user_subscribe['subscriptions'][0]['subscription']['credit_card']['billing_zip']) ? $user_subscribe['subscriptions'][0]['subscription']['credit_card']['billing_zip'] : '';
        $billing_country = !empty($user_subscribe['subscriptions'][0]['subscription']['credit_card']['billing_country']) ? $user_subscribe['subscriptions'][0]['subscription']['credit_card']['billing_country'] : '';
        $billing_address = !empty($user_subscribe['subscriptions'][0]['subscription']['credit_card']['billing_address']) ? $user_subscribe['subscriptions'][0]['subscription']['credit_card']['billing_address'] : '';
        $billing_address_2 = !empty($user_subscribe['subscriptions'][0]['subscription']['credit_card']['billing_address_2']) ? $user_subscribe['subscriptions'][0]['subscription']['credit_card']['billing_address_2'] : '';
        $card_type = !empty($user_subscribe['subscriptions'][0]['subscription']['credit_card']['card_type']) ? $user_subscribe['subscriptions'][0]['subscription']['credit_card']['card_type'] : '';
        $plateform = !empty($user_subscribe['subscriptions'][0]['subscription']['platform']) ? $user_subscribe['subscriptions'][0]['subscription']['platform'] : '';
        ?>
        <div class="custom-container container pt-5 pb-5">
            <div class="row no-gutters">
                <h3 class="page-title mb-5 center_title">Manage your payment details</h3>
            </div>
            <div class="credit-block container mb-5">
                <?php if ($plateform != 'web') { ?>
                    <div class="row row-fluid">
                        <p>As you do not have subscribed using the website plateform, so you can not update your payment details using the website. Please use the relavant plateform that you have used to subscribe the packages.</p>
                        <p>For more information please visit <a href="/packages" title="Subscription Packages">Packages</a></p>
                    </div>
                <?php } else { ?>
                    <form action="/thankyou/" class="w-100 needs-validation" novalidate name="payment" id="form_payment">
                        <input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('update_payment'); ?>">
                        <div class="row row-fluid">
                            <div class="col-md-6 col-sm-6">
                                <div class="wrapper">
                                    <h4 class="mt-3 mb-3">Billing Info</h4>
                                    <div class="form-group credit-group">
                                        <div class="row form-group credit-group required">
                                            <div class="col-md-6 sm-mb-3">
                                                <label class="credit_card_label" for="first_name">First Name</label>
                                                <input type="text" class="form-control credit_card_input" readonly id="first_name" name="first_name" value="<?php echo $first_name ?>" >
                                            </div>
                                            <div class="col-md-6">
                                                <label class="credit_card_label" for="last_name">Last Name</label>
                                                <input type="text" class="form-control credit_card_input" readonly id="last_name" name="last_name" value="<?php echo $last_name; ?>">
                                            </div>    
                                        </div>
                                        <div class="form-group credit-group required">
                                            <label class="credit_card_label" for="billing_address">Address</label>
                                            <input type="text" class="form-control credit_card_input" id="billing_address" name="billing_address" value="<?php echo $billing_address; ?>" required>
                                            <div class="invalid-feedback">
                                                Address field is required.
                                            </div>
                                        </div>
                                        <div class="form-group credit-group">
                                            <label class="credit_card_label">Address 2</label>
                                            <input type="text" class="form-control credit_card_input" id="billing_address_2" name="billing_address_2" value="<?php echo $billing_address_2; ?>">
                                        </div>
                                        <div class="row row-fluid required">
                                            <div class="col-md-6 sm-mb-3">
                                                <label class="credit_card_label" for="billing_city">City</label>
                                                <input type="text" class="form-control credit_card_input" id="billing_city" name="billing_city" value="<?php echo $billing_city; ?>" required>
                                                <div class="invalid-feedback">
                                                    Please select city.
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="credit_card_label" for="billing_zip">Zipcode</label>
                                                <input type="text" class="form-control credit_card_input" id="billing_zip" name="billing_zip" value="<?php echo $billing_zip; ?>" required>
                                                <div class="invalid-feedback">
                                                    Zipcode field is required.
                                                </div>
                                            </div>    
                                        </div>
                                        <div class="form-group credit-group">
                                            <label class="credit_card_label" for="billing_country">Country</label>
                                            <select class="form-control credit_card_input crs-country" id="billing_country" name="billing_country" data-region-id="billing_state" data-default-value="<?php echo $billing_country; ?>" data-value="shortcode" required>
                                                <option value>Select Country</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                Please select country
                                            </div>
                                        </div>
                                        <div class="form-group credit-group">
                                            <label class="credit_card_label" for="billing_state">State / Region</label>
                                            <select class="form-control credit_card_input" id="billing_state" name="billing_state" data-value="shortcode" data-default-value="<?php echo $billing_state; ?>" required></select>
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
                                    <div class="your-card">
                                        <div class="bank-name" title="BestBank"><?php echo strtoupper($card_type . ' Card'); ?></div>
                                        <div class="chip">
                                            <img src="<?php echo plugin_dir_url() . 'wordpress-subscription-plugin/frontend/assets/images/chip.svg' ?>">
                                        </div>
                                        <div class="data">
                                            <div class="pan"><?php echo $card_number; ?></div>
                                            <div class="exp-date-wrapper">
                                                <div class="left-label">EXPIRES END</div>
                                                <div class="exp-date">
                                                    <div class="upper-labels">MONTH/YEAR</div>
                                                    <div class="date"><?php echo str_pad($exp_month, 2, '0', STR_PAD_LEFT) . '/' . substr($exp_year, 2, 2); ?></div>
                                                </div>
                                            </div>
                                            <div class="name-on-card"><?php echo $first_name . ' ' . $last_name; ?></div>
                                        </div>
                                        <div class="lines-down"></div>
                                        <div class="lines-up"></div>
                                    </div>
                                    <!--                                <div class="form-group credit-group mt-2">
                                                                        <div class="form-group-medium">
                                                                            <label class="lbl-checkbox-cc">Update Credit card information</label>
                                                                            <label class="switch">
                                                                                <input id="checkbox_cc" type="checkbox" name="dsp_video_autoplay_field">
                                                                                <span class="switch-slider round"></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>-->
                                    <!--<div class="credit_card_info" style="display: none;">-->
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
                                                    for ($month = 1; $month <= 12; $month++) {
                                                        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
                                                        $monthName = date('M', mktime(0, 0, 0, $month, 10));
                                                        echo '<option value="' . $month . '">' . $month . ' (' . $monthName . ')</option>';
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
                                                    for ($year = date('Y'); $year <= date('Y') + 25; $year++) {
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
                                </div>
                            </div>
                            <div class="mx-auto">
                                <button type="submit" id="update_cc" class="btn btn-secondary btn-ds-secondary" data-action="update_payment_profile">Update Your Details</button>
                            </div>
                        </div>
                        <div class="row row-fluid">
                            <div class="cc-messages-notices m-3">
                            </div>
                        </div>
                    </form>
                <?php } ?>
            </div>
        </div>
        <div id="snackbar"></div>
        <?php
    } else {
        ?>
        <div class="custom-container container pt-5 pb-5">
            <div class="row no-gutters">
                <h3 class="page-title mb-5 center_title">Manage your payment details</h3>
                <h5>As per our records we could not find any active subscriptions under your account, so you don't have any payment profile has been created yet.</h5>
                <div class="col-12 text-center pt-3"><a href="/packages" title="Subscribe Now" class="btn btn-secondary btn-ds-secondary">Subscribe Now</a></div>
            </div>
        </div>
        <?php
    }
}
get_footer();
