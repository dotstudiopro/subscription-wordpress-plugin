<?php
get_header();

global $client_token;

$dsp_subscription_object = new Dotstudiopro_Subscription_Request();
$subscriptions = $dsp_subscription_object->getCompanyProductSummary();

if (!is_wp_error($subscriptions) && !empty($subscriptions['data'])) {
    ?>
    <div class="custom-container container pt-5 pb-5">
        <div class="row no-gutters">
            <h3 class="page-title center_title">PLEASE SELECT YOUR PLAN</h3>
        </div>
        <div class="row no-gutters pt-5 justify-content-md-center">
            <?php
            foreach ($subscriptions['data'] as $subscription):

                if ($subscription['status'] == 'Active' && $subscription['product_type'] == 'svod'):

                    $dsp_subscription_id = !empty($subscription['_id']) ? $subscription['_id'] : '';
                    $name = !empty($subscription['name']) ? $subscription['name'] : '';
                    $price = !empty($subscription['price']) ? $subscription['price'] : '';
                    $subscription_id = !empty($subscription['chargify_id']) ? $subscription['chargify_id'] : '';
                    $interval_unit = !empty($subscription['duration']['interval_unit']) ? $subscription['duration']['interval_unit'] : '';
                    $interval = !empty($subscription['duration']['interval']) ? $subscription['duration']['interval'] : '';
                    $trial_array = '';
                    if ($subscription['trial'] != null) {
                        $trial_array = array(
                            'interval' => !empty($subscription['trial']['interval']) ? $subscription['trial']['interval'] : '',
                            'interval_unit' => !empty($subscription['trial']['interval_unit']) ? $subscription['trial']['interval_unit'] : '',
                            'trial_price' => !empty($subscription['trial']['trial_price']) ? $subscription['trial']['trial_price'] : ''
                        );
                    }
                    $price_period = '';
                    $interval_bottom = $interval_unit;
                    if ($interval == 12 && $interval_unit == 'month') {
                        $monthly_price = floor(($price * 100) / 12) / 100;
                        $price_period = $monthly_price . '<span class="period"> /<br>month ' . '</span>';
                        $interval_bottom = "year";
                    } elseif ($interval == 1) {
                        $price_period = $price . '<span class="period"> /<br>' . $interval_unit . '</span>';
                    } else {
                        $price_period = $price . '<span class="period"> /<br>' . $interval . ' ' . $interval_unit . '</span>';
                    }
                    $button = !$client_token ? 'login-link' : 'select_plan';
                    $url = !$client_token ?  wp_login_url( get_permalink() ) : '#';
                    ?>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 pr-3 pb-3 sameSize">
                        <form  action="/package-detail/" id="form_<?php echo $subscription_id; ?>" method="POST">
                            <input type="hidden"  name="subscription_id" value="<?php echo $dsp_subscription_id; ?>">
                        </form>
                        <div class="card text-xs-center">
                            <div class="card-header">
                                <h4 class="text-center mb-0 text-uppercase"> 
                                    <?php echo $name ?>
                                </h4>
                            </div>
                            <div class="card-block text-center sameSize-inner">
                                <h5 class="display-4 mx-auto"><span class="currency">$</span><?php echo $price_period ?></h5>
                                <?php
                                if (!empty($trial_array)):
                                    ?>
                                    <a href="#" class="mt-2 mb-2 btn btn-secondary btn-ds-secondary w-100 btn-lg <?php echo $button; ?>" data-subscriptionid="<?php echo $subscription_id; ?>">Try Free for <?php echo $trial_array['interval'] . ' ' . $trial_array['interval_unit'] ?></a>
                                    <?php if (!empty($trial_array['trial_price'])): ?>
                                        <p class="trial_price">You need to pay $<?php echo $trial_array['trial_price'] ?> to activate trial period</p>
                                    <?php endif; ?>
                                    <?php
                                else:
                                    ?>
                                    <a href="<?php echo $url; ?>" class="mt-2 mb-2 btn btn-secondary btn-ds-secondary w-100 btn-lg <?php echo $button; ?>" data-subscriptionid="<?php echo $subscription_id; ?>">Subscribe now</a>
                                <?php
                                endif;
                                ?>
                                <p>$<?php echo $price; ?> billed <?php
                                    if($interval != 1){
                                        echo ' every '.$interval. ' ' .$interval_bottom;
                                    }else{
                                        echo ($interval_bottom == 'day' ? 'daily' : $interval_bottom . 'ly');
                                    }
                                    echo (empty($trial_array) ? ' after subscription' : ' after trial period')
                                    ?> 
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php
                endif;
            endforeach;
            ?>
        </div>
    </div>    
    <?php
}
else {
    ?>
    <div class="subscription-not-found">
        <h3> Currently Subscription plan is not activated for this site or something went wrong. Please contact administrator</h3>
    </div>
    <?php
}
get_footer();
