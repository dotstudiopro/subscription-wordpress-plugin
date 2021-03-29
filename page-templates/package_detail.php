<?php

get_header();

global $client_token, $dsp_theme_options;

$subscription_id = isset($_REQUEST['subscription_id']) ? $_REQUEST['subscription_id'] : '';



if($subscription_id){

    $product = dsp_get_vod_product_by_id($subscription_id);

    $name = $product->name;

    $price = str_replace("$","",$product->price);
    $interval_unit = !empty($product->duration->unit) ? $product->duration->unit : '';
    $interval = !empty($product->duration->number) ? $product->duration->number : '';
    $price_display = !empty($product->price_display) ? $product->price_display : '';

    $price_period = '';
    $trial_array = '';
    $interval_bottom = $interval_unit;
    if ($price_display == 'total') {
        $price_period = '$'. $price . '<span class="period"> / year</span>';
    } elseif ($price_display == 'monthly') {
        $monthly_price = floor(($price * 100) / $interval) / 100;
        $price_period = '$'. $monthly_price . '<span class="period"> / month</span>';
    } else {
        if ($interval == 12 && $interval_unit == 'month') {
            $monthly_price = floor(($price * 100) / 12) / 100;
            $price_period = '$'.$monthly_price . '<span class="period"> / month ' . '</span>';
            $interval_bottom = "year";
        } else {
            $price_period = '$'.$price . '<span class="period"> / ' . $interval_unit . '</span>';
        }
    }
    $duration = $price_period;
    $product_charigfy_id = $product->chargify_id;

    $main_color = $dsp_theme_options['opt-main-theme-color'];

    ?>
    <style type="text/css">
        .main-color-bg {
            background-color: <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
        }
        .main-body-txt {
            font-family: <?php echo $dsp_theme_options['opt-typography-body']; ?>;
        }
    </style>

    <div class="custom-container container pt-5 pb-5">
        <div class="row no-gutters">
            <div class='product-detail-banner main-color-bg'>
                <div class='product-detail-name mt-2 ml-4 main-body-txt'>
                    <h4><?php echo $name; ?></h4>
                </div>
                <div class='product-detail-duration-price main-body-txt mt-2 mr-4'>
                    <h4>Available for <?php echo $duration; ?></h4>
                </div>
            </div>
        </div>
        <form  action="/credit-card/" id="form_<?php echo $subscription_id; ?>" method="POST">
            <input type="hidden"  name="subscription_id" value="<?php echo $subscription_id; ?>">
        </form>

        <div class="package-detail container pt-5">
            <div class="row">
                <div class="col-sm-6 col-xs-6">
                    <h4>Description</h4>
                    <?php
                    if(isset($product->description) && !empty($product->description)){
                        $description = $product->description;
                    }else{
                        $description = '$'. $price . ' billed ';
                        if($interval != 1){
                            $description .= ' every '.$interval. ' ' .$interval_bottom;
                        }else{
                            $description .=  ($interval_bottom == 'day' ? 'daily' : $interval_bottom . 'ly');
                        }
                        $description .= (empty($trial_array) ? ' after subscription end' : ' after trial period');
                    }
                    ?>
                    <p class="pt-2" style="font-size: 22px;"><?php echo $description; ?></p>
                </div>
                <div class="col-sm-6 col-xs-6">
                    <h4>Billing Expiration</h4>
                    <p class="pt-2" style="font-size: 22px;">Recur Billing continuously until canceled</p>
                </div>
            </div>

        </div>

        <div class="row no-gutters pt-5">
            <div class='product-button-select'>
                <div class='product-button-back'>
                    <a href="<?php echo wp_get_referer(); ?>" class="mt-2 mb-2 btn btn-secondary btn-ds-secondary w-100 btn-lg" >Go Back</a>
                </div>
                <div class='product-button-buy'>
                    <a href="#" class="mt-2 mb-2 btn btn-secondary btn-ds-secondary w-100 btn-lg product-buy-now" data-productid="<?php echo $subscription_id;?>">Buy Now</a>
                </div>
            </div>
        </div>
    </div>

<?php
}else{
    wp_redirect('/packages');
}

get_footer();

?>