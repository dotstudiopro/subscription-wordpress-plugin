<?php

get_header();

global $client_token, $dsp_theme_options;

$product_id = get_query_var( 'product_id');

$product = dsp_get_vod_product_by_id($product_id);

$name = $product->name;
$price = $product->price;

$duration_number = $product->duration->number;
$duration_unit = $product->duration->unit;

$duration = $duration_number . " " . $duration_unit . ($duration_unit > 1 ? "s" : "");

$channels = dsp_get_channels_for_product($product_id);

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
            <div class='product-detail-name main-body-txt'>
                <?php echo $name; ?>
            </div>
            <div class='product-detail-duration-price main-body-txt'>
                Available for <?php echo $duration; ?>. Price:<?php echo $price; ?>
            </div>
        </div>
    </div>
    <div class="row no-gutters pt-5 justify-content-md-center">
        <div class='product-detail-channels row main-body-txt'>
            <?php foreach($channels as $channel): ?>
                <div class="product-detail-channel col-sm-4 col-xs-6">
                    <div class='product-channel-image'>
                        <img src='<?php echo !empty($channel['spotlight_poster']) ? $channel['spotlight_poster'] : $channel['poster']; ?>' />
                    </div>
                    <div class='product-channel-title'>
                        <?php echo $channel['title']; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="row no-gutters pt-5 justify-content-md-center">
        <div class='product-button-select'>
            <div class='product-button-back main-color-bg'>
                Select Different Product
            </div>
            <div class='product-button-buy main-color-bg'>
                Buy Now
            </div>
        </div>
    </div>
</div>