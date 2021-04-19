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
        $price_period = '$'. $price . '/ year';
    } elseif ($price_display == 'monthly') {
        $monthly_price = floor(($price * 100) / $interval) / 100;
        $price_period = '$'. $monthly_price . '/ month';
    } else {
        if ($interval == 12 && $interval_unit == 'month') {
            $monthly_price = floor(($price * 100) / 12) / 100;
            $price_period = '$'.$monthly_price . '/ month';
            $interval_bottom = "year";
        } else {
            $price_period = '$'.$price . '/ ' . $interval_unit;
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
                    <h3><?php echo $name; ?></h3>
                </div>
                <div class='product-detail-duration-price main-body-txt mt-2 mr-4'>
                    <h3>Available for <?php echo $duration; ?></h3>
                </div>
            </div>
        </div>
        <form  action="/credit-card/" id="form_<?php echo $subscription_id; ?>" method="POST">
            <input type="hidden"  name="subscription_id" value="<?php echo $subscription_id; ?>">
        </form>

        <div class="package-detail">
            <div class="row">
                <div class="col-sm-6 col-xs-6 pt-5">
                    <div class="ml-5">
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
                        <h5 class="pt-1"><?php echo $description; ?></h5>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-6 pt-5">
                    <div class="ml-5">
                        <h4>Billing Expiration</h4>
                        <h5 class="pt-1">Recur Billing continuously until canceled</h5>
                    </div>
                </div>
            </div>
        </div>

         <div class="col-md-12 text-center pt-5 pb-5">
            <a href="<?php echo wp_get_referer(); ?>" class="mt-2 mb-2 btn btn-secondary btn-ds-secondary btn-lg w-25" >Go Back</a>
            <a href="#" class="mt-2 mb-2 btn btn-secondary btn-ds-secondary btn-lg product-buy-now w-25" data-productid="<?php echo $subscription_id;?>">Buy Now</a>
        </div>

    </div>

<?php
}else{
    wp_redirect('/packages');
}

get_footer();

?>