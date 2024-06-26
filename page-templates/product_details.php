<?php

get_header();

global $client_token, $dsp_theme_options;

$dsp_subscription_object = new Dotstudiopro_Subscription_Request();
$subscriptions = $dsp_subscription_object->getCompanyProductSummary();

$product_id = get_query_var( 'product_id');

if(!is_wp_error($subscriptions) && !empty($subscriptions['data'])){
    foreach($subscriptions['data'] as $key => $subscription){
        $subscriptions['data'][$key]['hash_value'] = wp_hash($subscription['_id']);
    }

    if($product_id){
        $product_exists = array_search($product_id, array_column($subscriptions['data'], 'hash_value'));
        if(gettype($product_exists) == 'integer')
            $product_id = $subscriptions['data'][$product_exists]['_id'];
        else
            wp_redirect('/');
    }
}

$product = dsp_get_vod_product_by_id($product_id);

$name = $product->name;
$price = $product->price;

$duration_number = $product->duration->number;
$duration_unit = $product->duration->unit;

$duration = $duration_number . " " . $duration_unit . ($duration_unit > 1 ? "s" : "");

$channels = dsp_get_channels_for_product($product_id);

$main_color = $dsp_theme_options['opt-main-theme-color'];

$previous_page_url = isset($_REQUEST['previous_page_url']) ? $_REQUEST['previous_page_url'] : '/thankyou/';

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
    <div class="row no-gutters pb-5">
        <div class='product-detail-banner main-color-bg'>
            <div class='product-detail-name mt-2 ml-4 main-body-txt'>
                <h3><?php echo $name; ?></h3>
            </div>
            <div class='product-detail-duration-price main-body-txt mt-2 mr-4'>
                <h3>Available for <?php echo $price; ?> / <?php echo $duration; ?></h3>
            </div>
        </div>
    </div>
    <form  action="/credit-card/" id="form_<?php echo $product_id; ?>" method="POST">
        <input type="hidden"  name="product_id" value="<?php echo wp_hash($product_id); ?>">
        <input type="hidden"  name="previous_page_url" value="<?php echo $previous_page_url; ?>">
        <input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('credit_card_page'); ?>">
    </form>
    <div class="row no-gutters pt-5 justify-content-md-center">
        <div class='product-detail-channels row main-body-txt'>
            <?php foreach($channels as $channel):
                $channel_title = $channel['title'];
                $channel_description = !empty($channel['description']) ? $channel['description'] : "";
                $channel_directors = array_filter(!empty($channel['directors']) ? $channel['directors'] : []);
                $channel_actors = array_filter(!empty($channel['actors']) ? $channel['actors'] : []);

                $channel_image = "https://defaultdspmedia.cachefly.net/images/5bd9ea4cd57fdf6513eb27f1";
                if (!empty($channel['spotlight_poster'])) {
                    $channel_image = $channel['spotlight_poster'];
                } else if (!empty($channel['poster'])) {
                    $channel_image = $channel['poster'];
                }
            ?>
                <div class="product-detail-channel col-12 col-sm-6 col-md-4 col-lg-3 col-xl-3 pb-3 pr-3">
                    <div class='product-channel-image'>
                        <a href="javascript:void(0)" class="open-channel-detail-modal" data-channel-title="<?php echo $channel_title; ?>" data-channel-description="<?php echo $channel_description; ?>" data-channel-image="<?php echo $channel_image;?>" data-channel-actors='<?php echo json_encode($channel_actors); ?>' data-channel-directors='<?php echo json_encode($channel_directors); ?>'>
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
    <div class="col-md-12 text-center pt-5 pb-5">
        <a href="<?php echo wp_get_referer(); ?>" class="mt-2 mb-2 btn btn-secondary btn-ds-secondary w-25 btn-lg" >Go Back</a>
        <a href="#" class="mt-2 mb-2 btn btn-secondary btn-ds-secondary btn-lg product-buy-now w-25" data-productid="<?php echo $product_id;?>">Buy Now</a>
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
        <hr class='modal_director_actor_buffer hidden'/>
        <h4 class='modal_directors_title hidden'>Directors</h4>
        <p class="modal_channel_directors mt-4"></p>
        <h4 class='modal_actors_title hidden'>Actors</h4>
        <p class="modal_channel_actors mt-4"></p>
        <hr class='modal_description_buffer hidden'/>
        <p class="modal_channel_description mt-4"></p>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>