<?php
get_header();
$obj = new Dotstudiopro_Subscription_Request();
$subscriptions = $obj->getCompanyProductSummary();

if (!is_wp_error($subscriptions)) {
    if (!empty($subscriptions)) {
        foreach ($subscriptions['data'] as $subscription) {
            $name = !empty($subscription['name']) ? $subscription['name'] : '';
            $price = !empty($subscription['price']) ? $subscription['price'] : '';
            $subscription_id = !empty($subscription['chargify_id']) ? $subscription['chargify_id'] : '';
            $interval_unit = !empty($subscription['duration']['interval_unit']) ? $subscription['duration']['interval_unit'] : '';
            $interval = !empty($subscription['duration']['interval']) ? $subscription['duration']['interval'] : '';
            if ($interval == 12 && $interval_unit == 'month') {
                $price_period = $price . ' / year';
            } elseif ($interval == 1) {
                $price_period = $price . ' / ' . $interval_unit;
            } /* else {
              $price_period = $price . ' / ' . $interval . ' ' . $interval_unit;
              }
              if ($chargify_id && $chargify_id == $current_chargify_id) {
              $active_subs = $name . ' $' . $price_period;
              } else {
              $selected = '';
              if($subsid && $subsid == $current_chargify_id){
              $selected = 'selected';
              }
              $noneactive_options .= '<option '.$selected.' value="' . $current_chargify_id . '">' . $name . ' $' . $price_period . '</option>';
              } */
        }
    }
    ?>
    <div class="custom-container container mb-5">
        <div class="row no-gutters">
            <h3 class="page-title mb-5">Make a Payment</h3>
        </div>
        <div class="credit-block container mb-5">
            <form action="/thankyou" class="w-100">
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
                                    <input type="text" class="form-control credit_card_input" id="address" name="address" required>
                                </div>
                                <div class="form-group credit-group">
                                    <label class="credit_card_label">Address 2</label>
                                    <input type="text" class="form-control credit_card_input" id="address2" name="address2" >
                                </div>
                                <div class="row row-fluid">
                                    <div class="col-md-6">
                                        <label class="credit_card_label">City</label>
                                        <input type="text" class="form-control credit_card_input" id="city" name="city" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="credit_card_label">Zipcode</label>
                                        <input type="text" class="form-control credit_card_input" id="zipcode" name="zipcode" required>
                                    </div>    
                                </div>
                                <div class="form-group credit-group">
                                    <label class="credit_card_label">Country</label>
                                    <select class="form-control credit_card_input crs-country" id="country" name="country" data-region-id="state" data-default-value="US"></select>
                                </div>
                                <div class="form-group credit-group">
                                    <label class="credit_card_label">State / Region</label>
                                    <select class="form-control credit_card_input" id="state" name="state"></select>
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
                                    <input type="text" class="form-control credit_card_input" id="card_number" name="card_number" required>
                                </div>
                                <i class="fa fa-credit-card form-group-img"></i>
                            </div>
                            <div class="form-group credit-group">
                                <label class="credit_card_label">Expire Date</label>
                                <input type="text" class="form-control credit_card_input" id="expire_date" name="expire_date" required>
                            </div>
                            <div class="form-group credit-group">
                                <div class="form-group-small margin">
                                    <label class="credit_card_label">CVV</label>
                                    <input type="text" class="form-control credit_card_input" id="cvv" name="cvv" required>
                                </div>
                            </div>
                            <div class="form-group credit-group">
                                <label class="credit_card_label">Coupon Code</label>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control credit_card_input" id="coupon_code" name="coupon_code">
                                    </div>
                                    <div class="col-sm-6">
                                        <div id="coupon_wrapper">
                                            <a class="btn btn-primary" id="validate_coupon" href="#">Validate Coupon</a>
                                        </div>
                                        <p id="coupon_error"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row-fluid w-100">
                        <button type="submit" id="submit_cc" class=" btn btn-primary button-submit">Start your subscription</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
}
get_footer();
