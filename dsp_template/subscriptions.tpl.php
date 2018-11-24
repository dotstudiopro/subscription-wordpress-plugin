<?php
get_header();

$obj = new Dotstudiopro_Subscription_Request();
$subscriptions = $obj->getCompanyProductSummary();
?>
<div class="custom-container container pt-5 pb-5">
    <div class="row no-gutters">
        <h3 class="page-title">Select a Plan</h3>
    </div>
    <div class="row no-gutters pt-5">
        <?php
        if (!is_wp_error($subscriptions)) {
            foreach ($subscriptions['data'] as $subscription) {
                $name = !empty($subscription['name']) ? $subscription['name'] : '';
                $price = !empty($subscription['price']) ? $subscription['price'] : '';
                $subs_id = !empty($subscription['chargify_id']) ? $subscription['chargify_id'] : '';
                $trial = '';
                $hidden = '';
                $extra_class = '';
                $trial_array = array(
                    'interval' => !empty($subscription['trial']['interval']) ? $subscription['trial']['interval'] : '',
                    'interval_unit' => !empty($subscription['trial']['interval_unit']) ? $subscription['trial']['interval_unit'] : '',
                    'trial_price' => !empty($subscription['trial']['trial_price']) ? $subscription['trial']['trial_price'] : 'Free Trial'
                );
                if (!empty($trial_array)) {
                    $trial = implode(' ', $trial_array);
                }
                $interval_unit = !empty($subscription['duration']['interval_unit']) ? $subscription['duration']['interval_unit'] : '';
                $interval = !empty($subscription['duration']['interval']) ? $subscription['duration']['interval'] : '';
                $price_period = '';
                $interval_bottom = $interval_unit;
                if ($interval == 12 && $interval_unit == 'month') {
                    $monthly_price = floor(($price * 100) / 12) / 100;
                    $price_period = $monthly_price . '<span class="period"> / month ' . '</span>';
                    $interval_bottom = "year";
                } elseif ($interval == 1) {
                    $price_period = $price . '<span class="period"> / ' . $interval_unit . '</span>';
                } else {
                    $price_period = $price . '<span class="period"> / ' . $interval . ' ' . $interval_unit . '</span>';
                }
                ?>
                <form  action="/credit-card/" id="form_<?php echo $subs_id; ?>" method="POST"><input type="hidden"  name="subscription_id" value="<?php echo $subs_id; ?>"></form>
                <div class="col-xs-12 col-lg-3">
                    <div class="card text-xs-center">
                        <div class="card-header">
                            <h4 class="card-title"> 
                                <?php echo $name ?>
                            </h4>
                            <span <?php echo ($subs_id != '4665053') ? 'style="display:none;"' : ''; ?> >Most Popular</span>
                        </div>
                        <div class="card-block">
                            <h5 class="display-4"><span class="currency">$</span><?php echo $price_period ?></h5>
                            <a href="#" class="btn btn-gradient mt-2">Try Free</a>
                            <p>$<?php echo $price; ?> billed <?php echo ($interval_bottom == 'day' ? 'daily' : $interval_bottom . 'ly'); ?> after trial period</p>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo 'error!';
            echo '<pre>';
            print_r($subscriptions);
        }
        ?>
    </div>
</div>
<?php
get_footer();
