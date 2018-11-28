<?php

get_header();

global $client_token;

$dsp_subscription_object = new Dotstudiopro_Subscription_Request();
$subscriptions = $dsp_subscription_object->getCompanyProductSummary();
$template_class = new Subscription_Listing_Template();

if (!is_wp_error($subscriptions) && !empty($subscriptions['data'])) {
    if ($client_token) {
        $user_subscribe = $dsp_subscription_object->getUserSubscription($client_token);
        if (!is_wp_error($user_subscribe) && $user_subscribe && !empty($user_subscribe['subscriptions'][0]['subscription']['product']['id'])) {
            $template_class->locate_template('package-subscribed');
        } else {
            $template_class->locate_template('package-nonesubscribed');
        }
    } else {
        $template_class->locate_template('package-nonesubscribed');
    }
}

get_footer();
?>
