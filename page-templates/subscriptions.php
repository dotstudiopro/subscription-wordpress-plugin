<?php

get_header();

global $client_token;

$dsp_subscription_object = new Dotstudiopro_Subscription_Request();
$subscriptions = $dsp_subscription_object->getCompanyProductSummary();
$template_class = new Subscription_Listing_Template();

if (!is_wp_error($subscriptions) && !empty($subscriptions['data'])) {
    if ($client_token) {
        $user_subscribe = $dsp_subscription_object->getUserProducts($client_token);
        if (!is_wp_error($user_subscribe) && $user_subscribe && !empty($user_subscribe['products']['svod'][0]['product']['id'])) {
            $template_class->locate_template('templates-part/subscriptions/subscribed');
        } else {
            $template_class->locate_template('templates-part/subscriptions/nonesubscribed');
        }
    } else {
        $template_class->locate_template('templates-part/subscriptions/nonesubscribed');
    }
}

get_footer();
?>
