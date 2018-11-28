<?php
global $client_token;
get_header();
$dsp_subscription_object = new Dotstudiopro_Subscription_Request();
$subscriptions = $dsp_subscription_object->getCompanyProductSummary();
$user_subscribe = $dsp_subscription_object->getUserSubscription($client_token);
if (!is_wp_error($user_subscribe) && $user_subscribe && !empty($user_subscribe['subscriptions'][0]['subscription']['product']['id'])) {
    $active_subscription_id = $user_subscribe['subscriptions'][0]['subscription']['product']['id'];
    $platform = !empty($user_subscribe['subscriptions'][0]['subscription']['platform']) ? $user_subscribe['subscriptions'][0]['subscription']['platform'] : "none";
}
?>
<div class="custom-container container pt-5 pb-5">
    <div class="row no-gutters">
        <h3 class="page-title mb-5">Subscription information</h3>
    </div>

    <?php
    if (!is_wp_error($subscriptions) && !empty($subscriptions['data'])) {
        ?>
        <div class="container">
            <div class="row no-gutters">
                <div class="col-md-12">
                    <?php
                    foreach ($subscriptions['data'] as $subscription):

                        if ($subscription['status'] == 'Active'):
                            $name = !empty($subscription['name']) ? $subscription['name'] : '';
                            $price = !empty($subscription['price']) ? $subscription['price'] : '';
                            $subscription_id = !empty($subscription['chargify_id']) ? $subscription['chargify_id'] : '';
                            $interval_unit = !empty($subscription['duration']['interval_unit']) ? $subscription['duration']['interval_unit'] : '';
                            $interval = !empty($subscription['duration']['interval']) ? $subscription['duration']['interval'] : '';

                            if ($interval == 12 && $interval_unit == 'month'):
                                $price_period = $price . ' / year';
                            elseif ($interval == 1):
                                $price_period = $price . ' / ' . $interval_unit;
                            else:
                                $price_period = $price . ' / ' . $interval . ' ' . $interval_unit;
                            endif;
                            ?>

                            <?php if ($active_subscription_id == $subscription_id) { ?>
                                <div class="form-group">
                                    <h5><?php echo $name . ' $' . $price_period . ' (Current)'; ?></h5>
                                </div>
                                <?php if (!empty($subs[0]->subscription->delayed_cancel_at)) { ?>   
                                    <p>Your Subscription Will be Cancelled at <?php echo date('F j, Y, g:i a T', strtotime($user_subscribe['subscriptions'][0]['subscription']['delayed_cancel_at'])) ?></p>
                                <?php } else { ?>
                                    <p>Current period ends at <?php echo date('F j, Y, g:i a T', strtotime($user_subscribe['subscriptions'][0]['subscription']['current_period_ends_at'])) ?></p>
                                    <?php
                                }
                            } else {
                                if ($platform == 'web' && empty($user_subscribe['subscriptions'][0]['subscription']['delayed_cancel_at'])) {
                                    ?>
                                    <h4 class="pt-3">Upgrade Subscription</h4>
                                    <form  action="/upgrade-subscription"  method="POST">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <select class="form-control" name="subscription_id">
                                                        <option value="<?php echo $subscription_id ?>"><?php echo $name . ' $' . $price_period ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <button type="submit" class="vc_btn3-color-blue btn btn-primary">UPGRADE</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="clearfix"></div>
                                    <div class="cancel_subs text-left">
                                        <button type="submit" id="cancel_subs"  class="vc_btn3-color-blue btn btn-danger">CANCEL SUBSCRIPTION</button>
                                    </div>
                                    <?php
                                } else {
                                    if (empty($user_subscribe['subscriptions'][0]['subscription']['delayed_cancel_at'])) {
                                        ?>
                                        <p>Your subscription was created through <?php echo $platform; ?> so you will need to manage it on that platform.</p>
                                        <?php
                                    }
                                }
                            }
                        endif;
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="subscription-not-found">
            <h3> Currently Subscription plan is not activated for this site or something went wrong. Please contact administrator</h3>
        </div>
        <?php
    }
    ?>
</div>
<?php
get_footer();
