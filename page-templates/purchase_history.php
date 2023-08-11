<?php

global $client_token, $dsp_theme_options;

if(!$client_token):
    wp_redirect('/');
endif;

get_header();
$dsp_subscription_object = new Dotstudiopro_Subscription_Request();
$user_purchase_history = $dsp_subscription_object->getUserPurchaseHistory($client_token);
?>
<style type="text/css">
    .main-color {
     color: <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
	}
</style>
<div class="custom-container container pt-5 pb-5">
    <div class="row no-gutters">
        <h3 class="page-title mb-2 center_title">Purchase History</h3>
    </div>

    <?php if(is_wp_error($user_purchase_history) || !$user_purchase_history || (empty($user_purchase_history['current_subscription']) && empty($user_purchase_history['current_subscription']['product']))): ?>
        <p class="col-12 text-center">We could not find any purchase history on your account.</p>

	<?php else:
		$active_user_subscription = $user_purchase_history['current_subscription'];
		$name = !empty($active_user_subscription['product']['name']) ? $active_user_subscription['product']['name'] : '';
        $price = !empty($active_user_subscription['product']['price_in_cents']) ? ($active_user_subscription['product']['price_in_cents'] / 100) : '';

        $interval_unit = !empty($active_user_subscription['product']['interval_unit']) ? $active_user_subscription['product']['interval_unit'] : '';
        $interval = !empty($active_user_subscription['product']['interval']) ? $active_user_subscription['product']['interval'] : '';

        if ($interval == 12 && $interval_unit == 'month'):
            $price_period = $price . ' / year';
        elseif ($interval == 1):
            $price_period = $price . ' / ' . $interval_unit;
        else:
            $price_period = $price . ' / ' . $interval . ' ' . $interval_unit;
        endif;

        $active_subscription_information = '<h4 class="current_plan_title pb-3 main-color">Current Plan</h4><div class="current_plan">';
		$active_subscription_information .= '<div class="form-group"><h5 class="main-color">' . $name . ' $' . $price_period . ' (Current)<a href="/packages/" class="ml-4" target="_blank"><i class="fas fa-pencil main-color"></i></a></h5></div>';
        if (!empty($active_user_subscription['delayed_cancel_at']))
            $active_subscription_information .='<p class="pb-4">Your Subscription Will be Cancelled at ' . date('F j, Y, g:i a T', strtotime($active_user_subscription['delayed_cancel_at'])) . '</p>';
        else{
            $active_subscription_information .= '<p class="pb-4">Current subscription period ends at ' . date('F j, Y, g:i a T', strtotime($active_user_subscription['subscription_info']['current_period_ends_at'])) . '</p>';
            if(isset($active_user_subscription['subscription_info']['next_assessment_at']) && !empty($active_user_subscription['subscription_info']['next_assessment_at'])){
				$active_subscription_information .= '<div class="form-group"><h5 class="main-color">Next Billing Date</h5></div>';
				$active_subscription_information .= '<p class="">' . date('F j, Y, g:i a T', strtotime($active_user_subscription['subscription_info']['next_assessment_at'])) . '</p>';	
            }
        }
        $active_subscription_information .= '</div>';
	?>
	
	<div>
        <div class="row no-gutters">
           <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 p-3 payment_history_current_plan_info">
                <div class="active_subscription_information">
                    <?php echo $active_subscription_information; ?>
                </div>
                <div class="purchase_listing pt-5">
                	<h4 class="current_plan_title pb-3 main-color">Purchase Listing</h4>
                	<div class="table-responsive">
	                	<table class="table">
	                		<thead>
	                			<th>Date</th>
								<th>Description</th>
								<th>Service Period</th>
								<th>Payment Method</th>
								<th>Total</th>
								<th></th>
	                		</thead>
	                		<tbody>
								<?php
								foreach($user_purchase_history['history'] as $product):
									if($product['type'] == 'payment'): ?>
	                				<tr>
										<td><?php echo date('m/d/Y', strtotime($product['created_at']));?></td>
										<td><?php echo $product['product_name']; ?></td>
										<td><?php echo date('m/d/Y', strtotime($product['period_start']));?> - <?php echo date('m/d/Y', strtotime($product['period_end'])); ?></td>
										<td><?php if(isset($product['platform']) && ($product['platform'] == 'web' || $product['platform'] == 'desktop_web' || $product['platform'] == 'mobile_web')): ?><img alt="Credit Card" title="Credit Card" src="<?php echo plugins_url() . '/wordpress-subscription-plugin/frontend/assets/images/web.svg' ?>" width="34" height="24" border="0" />&nbsp;&nbsp;<?php echo substr($product['masked_card_number'],-9); ?>
											<?php elseif(isset($product['platform']) && ($product['platform'] == 'apple' || $product['platform'] == 'apple_tv' || $product['platform'] == 'ios')): ?><img alt="Apple TV" title="Apple TV" src="<?php echo plugins_url() . '/wordpress-subscription-plugin/frontend/assets/images/appletv.png' ?>" width="26" border="0" />&nbsp;&nbsp;In-App purchase
											<?php elseif(isset($product['platform']) && ($product['platform'] == 'android' || $product['platform'] == 'android_tv')): ?><img alt="Android TV" title="Android TV" src="<?php echo plugins_url() . '/wordpress-subscription-plugin/frontend/assets/images/android.png' ?>" width="26" border="0" />&nbsp;&nbsp;In-App purchase
											<?php elseif(isset($product['platform']) && ($product['platform'] == 'fire_tv' || $product['platform'] == 'fire')): ?><img alt="Fire TV" title="Fire TV" src="<?php echo plugins_url() . '/wordpress-subscription-plugin/frontend/assets/images/firetv.png' ?>" width="26" border="0" />&nbsp;&nbsp;In-App purchase
											<?php elseif(isset($product['platform']) && ($product['platform'] == 'roku_tv' || $product['platform'] == 'roku')): ?><img alt="Roku TV" title="Roku TV" src="<?php echo plugins_url() . '/wordpress-subscription-plugin/frontend/assets/images/rokutv.png' ?>" width="26" border="0" />&nbsp;&nbsp;In-App purchase
											<?php endif; ?>
										</td>
										<td><?php echo ($product['payment_amount']); ?></td>
										<td><?php if(isset($product['product_type']) && $product['product_type'] == 'svod'): ?>		<a href="javascript:;" class="select_plan" data-subscriptionid="<?php echo $product['dsp_product_id']; ?>" style="color: inherit;"><i class="fa fa-eye" style="font-size: 15px;"></i>
											</a>
											<form action="/package-detail/" id="form_<?php echo $product['dsp_product_id']; ?>" method="POST"><input type="hidden" name="subscription_id" value="<?php echo wp_hash($product['dsp_product_id']); ?>">
											<input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('pack_detail'); ?>">
											</form>
											<?php elseif(isset($product['product_type']) && $product['product_type'] == 'tvod'): ?><a href="/product-details/<?php echo wp_hash($product['dsp_product_id']); ?>" style="color: inherit;"><i class="fa fa-eye" style="font-size: 15px;"></i></a>
											<?php endif; ?>
										</td>
									</tr>
									<?php endif;
								endforeach; ?>
							</tbody>
						</table>
					</div>
                </div>
            </div>
        </div>
    </div>

	<?php endif; ?>
    
</div>

<?php get_footer(); ?>