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
            <div class='product-detail-name mt-2 ml-4 main-body-txt'>
                <h4><?php echo $name; ?></h4>
            </div>
            <div class='product-detail-duration-price main-body-txt mt-2 mr-4'>
                <h4>Available for <?php echo $price; ?> / <?php echo $duration; ?></h4>
            </div>
        </div>
    </div>
    <form  action="/credit-card/" id="form_<?php echo $product_id; ?>" method="POST">
        <input type="hidden"  name="product_id" value="<?php echo $product_id; ?>">
    </form>
    <div class="row no-gutters pt-5 justify-content-md-center">
        <div class='product-detail-channels row main-body-txt'>
            <?php foreach($channels as $channel): 
                $channel_title = $channel['title'];
                $channel_image = !empty($channel['spotlight_poster']) ? $channel['spotlight_poster'] : $channel['poster'];
            ?>
                <div class="product-detail-channel col-sm-3 col-xs-6">
                    <div class='product-channel-image'>
                        <a href="javascript:void(0)" class="open-channel-detail-modal" data-channel-title="<?php echo $channel_title; ?>" data-channel-image="<?php echo $channel_image;?>">
                            <img src="<?php echo $channel_image ?>" id="channelimg" class="lazy w-100">
                            <!-- <img src="<?php //echo $channel_image.'/480/215' ?>" class="lazy w-100">  -->
                        </a>
                    </div>
                    <div class='product-channel-title'>
                        <?php echo $channel['title']; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="row no-gutters pt-5">
        <div class='product-button-select'>
            <div class='product-button-back'>
                <a href="<?php echo wp_get_referer(); ?>" class="mt-2 mb-2 btn btn-secondary btn-ds-secondary w-100 btn-lg" >Go Back</a>
            </div>
            <div class='product-button-buy'>
                <a href="#" class="mt-2 mb-2 btn btn-secondary btn-ds-secondary w-100 btn-lg product-buy-now" data-productid="<?php echo $product_id;?>">Buy Now</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="channel-detail-modal" class="channel-detail-modal" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      </div>
      <div class="modal-body">
        <img id="imagepreview" class="modal_channel_image lazy w-100">
        <h4 class="modal_channel_title pt-3"></h4>
        <p class="modal_description mt-4"></p>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>