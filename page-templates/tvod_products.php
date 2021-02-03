<?php
get_header();

global $client_token;

$channel_slug = get_query_var( 'channel_slug');
$post = get_page_by_path($channel_slug, 'OBJECT', 'channel');

$subscriptions = dsp_get_channel_tvod_products(get_post_meta($post->ID, 'dspro_channel_id', true));
echo 'Currently Browsing Page ', $channel_slug;


if (!is_wp_error($subscriptions) && !empty($subscriptions) && is_array($subscriptions)) {
    ?>
    <div class="custom-container container pt-5 pb-5">
        <div class="row no-gutters">
            <h3 class="page-title center_title">PLEASE SELECT YOUR Product</h3>
        </div>
        <div class="row no-gutters pt-5 justify-content-md-center">
            <?php
            foreach ($subscriptions as $subscription):

                if ($subscription['status'] == 'Active'):

                    echo "<pre>";
                    print_r($subscription);
                    die("</pre>");

                    $name = !empty($subscription['name']) ? $subscription['name'] : '';
                    $price = !empty($subscription['price']) ? $subscription['price'] : '';
                    $subscription_id = !empty($subscription['chargify_id']) ? $subscription['chargify_id'] : '';
                    $interval_unit = !empty($subscription['duration']['unit']) ? $subscription['duration']['unit'] : '';
                    $interval = !empty($subscription['duration']['number']) ? $subscription['duration']['number'] : '';
                    if ((int) $interval > 1) $interval_unit .= "s";
                    $price_period = $price . '<span class="period"> / ' . $interval . " " . $interval_unit . '</span>';

                    $button = empty($client_token) ? 'login-link' : 'select_plan';
                    $url = empty($client_token) ?  wp_login_url( get_permalink() ) : '#';
                    ?>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 pr-3 pb-3 sameSize">
                        <form  action="/credit-card/" id="form_<?php echo $subscription_id; ?>" method="POST">
                            <input type="hidden"  name="subscription_id" value="<?php echo $subscription_id; ?>">
                        </form>
                        <div class="card text-xs-center">
                            <div class="card-header">
                                <h4 class="text-center mb-0 text-uppercase">
                                    <?php echo $name ?>
                                </h4>
                            </div>
                            <div class="card-block text-center sameSize-inner">
                                <h5 class="display-4 mx-auto"><?php echo $price_period ?></h5>
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
