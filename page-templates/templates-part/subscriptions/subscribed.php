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
        <h3 class="page-title mb-5 center_title">Subscription information</h3>
    </div>

    <?php
    if (!is_wp_error($subscriptions) && !empty($subscriptions['data'])) {
        $active_subscription_information = '';
        $update_subscription_information = '';
        $cancle_subscription_information = '';
        $platform_error = '';
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

                if ($active_subscription_id == $subscription_id) {
                    $active_subscription_information .= '<div class="form-group"><h5>' . $name . ' $' . $price_period . ' (Current)</h5></div>';
                    if (!empty($user_subscribe['subscriptions'][0]['subscription']['delayed_cancel_at']))
                        $active_subscription_information .='<p>Your Subscription Will be Cancelled at' . date('F j, Y, g:i a T', strtotime($user_subscribe['subscriptions'][0]['subscription']['delayed_cancel_at'])) . '</p>';
                    else
                        $active_subscription_information .= '<p>Current period ends at' . date('F j, Y, g:i a T', strtotime($user_subscribe['subscriptions'][0]['subscription']['current_period_ends_at'])) . '</p>';
                }
                else {
                    if ($platform == 'web') {
                        $update_subscription_information .= '<option value="' . $subscription_id . '">' . $name . ' $' . $price_period . '</option>';
                        if (empty($user_subscribe['subscriptions'][0]['subscription']['delayed_cancel_at']))
                            $cancle_subscription_information = '<button id="cancel_subscription_button" data-title="Cancle Subscription" data-nonce=' . wp_create_nonce('cancle_subscription_plan') . ' data-action="cancle_subscription" class="vc_btn3-color-blue btn btn-danger">CANCEL SUBSCRIPTION</button>';
                    }
                    else {
                        $platform_error ='<p>Your subscription was created through ' . $platform . ' so you will need to manage it on that platform.</p>';
                    }
                }
            endif;
        endforeach;
        ?>
        <div class="container">
            <div class="row no-gutters justify-content-md-center">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 p-5 border border-secondary subscription_info text-center">
                    <div class="active_subscription_information">
                        <?php echo $active_subscription_information; ?>
                    </div>
                    <?php if ($platform_error == null) { ?>
                        <div class="update_subscription_information">
                            <form class="update_subscription_form" action="/upgrade-subscription"  method="POST">
                                <div class="row">
                                	<div class="col-sm-4">
                                  </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <select class="form-control update_subscription_id" name="update_subscription_id">
                                                <?php echo $update_subscription_information; ?>
                                            </select>
                                        </div>
                                    </div>
                             
                                </div>
                            </form>
                        </div>
                        <?php if ($cancle_subscription_information != null) { ?>
                            <div class="cancle_subscription_information text-center">
                                        <div class="form-group">
                                            <button data-title="Update Subscription" data-nonce='<?php echo wp_create_nonce('upadate_subscription_plan'); ?>' data-action='update_subscription' id="update_subscription_button" class="btn btn-secondary btn-ds-secondary">UPGRADE</button>
                                        </div>
                                    
                                <?php echo $cancle_subscription_information; ?>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="platform_error">
                            <?php echo $platform_error; ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="subscription-not-found text-center">
            <h3> Currently Subscription plan is not activated for this site or something went wrong. Please contact administrator</h3>
        </div>
        <?php
    }
    ?>
</div>
<?php
get_footer();
