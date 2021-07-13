<?php
get_header();
global $client_token;

$dsp_subscription_object = new Dotstudiopro_Subscription_Request();

if ($client_token) {
    
    $options = null;
    //$user_subscribe = $dsp_subscription_object->getUserProducts($client_token);
    $user_subscribe = $dsp_subscription_object->getUserProducts($client_token, 'inactive');
    
    if ((!is_wp_error($user_subscribe) && $user_subscribe && !empty($user_subscribe['paymentInfo']))) {
        if(isset($user_subscribe['paymentInfo']) && !empty($user_subscribe['paymentInfo'])){
            $cc_info = dsp_parse_cc_info_new($user_subscribe['paymentInfo']);    
        } 
        
        $platform = '';
        if(isset($user_subscribe['platform']) && !empty($user_subscribe['platform'])){
            $platform = $user_subscribe['platform'];
        } 
        $platform = 'web';
        ?>
        <script src="https://www.google.com/recaptcha/api.js"></script>
        <div class="custom-container container pt-5 pb-5">
            <div class="row no-gutters">
                <h3 class="page-title mb-5 center_title">Manage your payment details</h3>
            </div>
            <div class="credit-block container mb-5">
                <?php if ($platform != 'web') { ?>
                    <div class="row row-fluid">
                        <p>You have subscribed to <?php echo bloginfo(); ?> via <?php echo ucfirst($platform); ?>, outside of our website. To update your subscription, please find your subscription settings on that platform.</p>
                    </div>
                <?php } else { ?>
                    <form action="/thankyou/" class="w-100 needs-validation" novalidate name="payment" id="form_payment">
                        <input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('update_payment'); ?>">
                        <div class="row row-fluid">
                            <div class="col-md-6 col-sm-12">
                                <div class="wrapper">
                                    <h4 class="mt-3 mb-3">Billing Info</h4>
                                    <div class="form-group credit-group">
                                        <div class="row form-group credit-group required">
                                            <div class="col-md-6 sm-mb-3">
                                                <label class="credit_card_label" for="first_name">First Name</label>
                                                <input type="text" class="form-control credit_card_input" id="first_name" name="first_name" value="<?php echo $cc_info->first_name ?>" >
                                            </div>
                                            <div class="col-md-6">
                                                <label class="credit_card_label" for="last_name">Last Name</label>
                                                <input type="text" class="form-control credit_card_input" id="last_name" name="last_name" value="<?php echo $cc_info->last_name; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group credit-group required">
                                            <label class="credit_card_label" for="billing_address">Address</label>
                                            <input type="text" class="form-control credit_card_input" id="billing_address" name="billing_address" value="<?php echo $cc_info->billing_address; ?>" required>
                                            <div class="invalid-feedback">
                                                Address field is required.
                                            </div>
                                        </div>
                                        <div class="form-group credit-group">
                                            <label class="credit_card_label">Address 2</label>
                                            <input type="text" class="form-control credit_card_input" id="billing_address_2" name="billing_address_2" value="<?php echo $cc_info->billing_address_2; ?>">
                                        </div>
                                        <div class="row form-group credit-group required">
                                            <div class="col-md-6 sm-mb-3">
                                                <label class="credit_card_label" for="billing_city">City</label>
                                                <input type="text" class="form-control credit_card_input" id="billing_city" name="billing_city" value="<?php echo $cc_info->billing_city; ?>" required>
                                                <div class="invalid-feedback">
                                                    Please select city.
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="credit_card_label" for="billing_zip">Zipcode</label>
                                                <input type="text" class="form-control credit_card_input" id="billing_zip" name="billing_zip" value="<?php echo $cc_info->billing_zip; ?>" required>
                                                <div class="invalid-feedback">
                                                    Zipcode field is required.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group credit-group">
                                            <label class="credit_card_label" for="billing_country">Country</label>
                                            <select class="form-control credit_card_input crs-country" id="billing_country" name="billing_country" data-region-id="billing_state" data-default-value="<?php echo $cc_info->billing_country; ?>" data-value="shortcode" required>
                                                <option value>Select Country</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                Please select country
                                            </div>
                                        </div>
                                        <div class="form-group credit-group">
                                            <label class="credit_card_label" for="billing_state">State / Region</label>
                                            <select class="form-control credit_card_input" id="billing_state" name="billing_state" data-value="shortcode" data-default-value="<?php echo $cc_info->billing_state; ?>" required></select>
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
                                    <div class="your-card pb-2 mb-3">
                                        <div class="bank-name" title="BestBank"><?php echo strtoupper($cc_info->card_type . ' Card'); ?></div>
                                        <div class="chip">
                                            <img src="<?php echo plugins_url() . '/wordpress-subscription-plugin/frontend/assets/images/chip.svg' ?>">
                                        </div>
                                        <div class="data">
                                            <div class="pan"><?php echo $cc_info->card_number; ?></div>
                                            <div class="exp-date-wrapper">
                                                <div class="left-label">EXPIRES END</div>
                                                <div class="exp-date">
                                                    <div class="upper-labels">MONTH/YEAR</div>
                                                    <div class="date"><?php echo str_pad($cc_info->exp_month, 2, '0', STR_PAD_LEFT) . '/' . substr($cc_info->exp_year, 2, 2); ?></div>
                                                </div>
                                            </div>
                                            <div class="name-on-card"><?php echo $cc_info->first_name . ' ' . $cc_info->last_name; ?></div>
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
                                <?php echo do_shortcode("[recaptcha]"); ?>
                                <button type="submit" id="update_cc" class="btn btn-secondary btn-ds-secondary mt-3" data-action="update_payment_profile">Update Your Details</button>
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
        <div class="custom-container container pt-5 pb-5  pt-5 pb-5 center-page-content">
            <div class="row no-gutters">
                <h3 class="page-title mb-5 center_title">Manage your payment details</h3>
                <p class="col-12 text-center">We could not find any active subscriptions or purchases on your account. Your payment profile will be available during and after your next purchase.</p>
                <div class="col-12 text-center pt-3"><a href="/packages" title="Subscribe Now" class="btn btn-secondary btn-ds-secondary">Subscribe Now</a></div>
            </div>
        </div>
        <?php
    }
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
