<?php

global $client_token, $dsp_theme_options;

if(!$client_token):
    wp_redirect('/');
endif;

get_header();
$dsp_subscription_object = new Dotstudiopro_Subscription_Request();

//$user_subscribe = $dsp_subscription_object->getUserProducts($client_token);
$user_subscribe_all = $dsp_subscription_object->getUserProducts($client_token, 'inactive');

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

    <?php if(is_wp_error($user_subscribe_all) || !$user_subscribe_all || (empty($user_subscribe_all['products']['svod']) && empty($user_subscribe_all['products']['tvod']))): ?>
        <p class="col-12 text-center">We could not find any purchase history on your account.</p>

	<?php else: 

		$active_subscription_information = '';

		$active_user_subscription = array();
		foreach ($user_subscribe_all['products']['svod'] as $svod_products) {
          if($svod_products['state'] == 'active'){
            $active_user_subscription = $svod_products;
            break;
          }
        }

		if (!empty($active_user_subscription)) {

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
	            $active_subscription_information .= '<p class="pb-4">Current period ends at ' . date('F j, Y, g:i a T', strtotime($active_user_subscription['current_period_ends_at'])) . '</p>';
	            if(isset($active_user_subscription['next_assessment_at']) && !empty($active_user_subscription['next_assessment_at'])){
	            	$active_subscription_information .= '<div class="form-group"><h5 class="main-color">Next Billing Date</h5></div>';
	            	$active_subscription_information .= '<p class="">' . date('F j, Y, g:i a T', strtotime($active_user_subscription['next_assessment_at'])) . '</p>';	
	            }
	        }
	        $active_subscription_information .= '</div>';
	     }

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
	                			<th>Total</th>
	                		</thead>
	                		<tbody>
	                			<?php foreach($user_subscribe_all['products']['svod'] as $svod_product):?>
	                				<tr>
	                					<td><?php echo date('m/d/Y', strtotime($svod_product['activated_at']));?></td>
	                					<td><?php echo $svod_product['product']['name']; ?></td>
	                					<?php if (!empty($svod_product['canceled_at']))
	                							$service_period_end = $svod_product['canceled_at'];
	                						  else
	                						  	$service_period_end = $svod_product['current_period_ends_at'];
	                					?>
	                					<td><?php echo date('m/d/Y', strtotime($svod_product['activated_at']));?> - <?php echo date('m/d/Y', strtotime($service_period_end)); ?></td>
	                					<td>$<?php echo ($svod_product['product']['price_in_cents'] / 100); ?></td>
	                				</tr>
	                			<?php endforeach; ?>
	                			<?php foreach($user_subscribe_all['products']['tvod'] as $tvod_product):?>
	                				<tr>
	                					<td><?php echo date('m/d/Y', strtotime($tvod_product['activated_at']));?></td>
	                					<td><?php echo $tvod_product['product']['name']; ?></td>
	                					<td><?php echo date('m/d/Y', strtotime($tvod_product['activated_at']));?> - <?php echo date('m/d/Y', strtotime($tvod_product['expires_at'])); ?></td>
	                					<td>$<?php echo ($tvod_product['product']['initial_charge_in_cents'] / 100); ?></td>
	                				</tr>
	                			<?php endforeach; ?>
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