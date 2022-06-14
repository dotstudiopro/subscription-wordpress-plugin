<?php


function abs_get_all_videos(){
    global $wpdb;

    $query = "SELECT * FROM `wp_videos`";
    $videos = $wpdb->get_results($query, ARRAY_A);

    return $videos;
}

function abs_get_video_id_by_slug($video_slug){
    global $wpdb;
    $videos = abs_get_all_videos();
    if (!is_array($videos) || !count($videos)) return "";
    $video_id = "";
    foreach ($videos as $video) {
        $detail = maybe_unserialize(base64_decode($video['video_detail']));
        if ($detail['slug'] == $video_slug) {
            $video_id = $video['video_id'];
        }
    }

    return $video_id;

}

function abs_get_videos_by_ids($ids){
    global $wpdb;
    $id_array = explode(",", $ids);
    $placeholders = [];
    foreach($id_array as $id) {
        $placeholders[] = "%s";
    }
    $query = $wpdb->prepare(
        "SELECT * FROM `wp_videos` WHERE `video_id` IN (" . implode(",", $placeholders) . ")",
        $id_array
    );
    $videos_pre = $wpdb->get_results($query, ARRAY_A);
    if (!is_array($videos_pre) || !count($videos_pre)) return array();
    $videos = [];
    foreach ($videos_pre as $video) {
       $video_detail = maybe_unserialize(base64_decode($video['video_detail']));
       foreach($video_detail as $k => $detail) {
        if ($k == "thumb") {
            if (strpos($detail, "https://") === false) {
                $video[$k] = get_option('dsp_cdn_img_url_field') . $detail;
            } else {
                $video[$k] = $detail;
            }
        } else {
            $video[$k] = $detail;
        }
       }
       $videos[] = $video;
    }

    return $videos;

}

function get_channel_videos_by_slug($channel_slug){
    $channel = get_page_by_path( $channel_slug, OBJECT, 'channel' );
    if (!$channel || empty($channel->ID)) {
        $args = array(
           'post_type' => 'page',
           'meta_query' => array(
               array(
                   'key' => 'channel_slug',
                   'value' => $channel_slug,
                   'compare' => '=',
               )
           )
        );
        $query = new WP_Query($args);
        if (empty($query) || empty($query->posts)) return array();
        $page = $query->posts[0];
        $chan_id = get_post_meta($page->ID, 'post_channel', true);
        if (empty($chan_id)) return array();
        $channel = new stdClass;
        $channel->ID = $chan_id;
    }
    $channel_id = $channel->ID;
    $videos_meta = maybe_unserialize(get_post_meta($channel_id, 'chnl_videos', true));
    $all_videos = abs_get_videos_by_ids($videos_meta);

    echo "<!-- GET CHANS \n\n";
        print_r(array($channel_id, $videos_meta, $all_videos));
    echo "\n\n GET CHANS -->";
    return $all_videos;
}

function get_channel_details_by_slug($channel_slug){
    $channel_name = site_url()."/channel/".$channel_slug;
    $channel_id = url_to_postid( $channel_name );
    $content_post = get_post($channel_id);
    $chnl_title = $content_post->post_title;
    $chnl_content = $content_post->post_content;
    $chnl_poster = get_post_meta($channel_id, 'chnl_poster', true);
    $chnl_spotlisgt_poster = get_post_meta($channel_id, 'chnl_spotlisgt_poster', true);
    $chnl_catagories = get_post_meta($channel_id, 'chnl_catagories', true);
    $chnl_id = get_post_meta($channel_id, 'chnl_id', true);
    $chnl_writers = get_post_meta($channel_id, 'chnl_writers', true);
    $chnl_geners = get_post_meta($channel_id, 'chnl_geners', true);
    $chnl_actors = get_post_meta($channel_id, 'chnl_actors', true);
    $chnl_logo = get_post_meta($channel_id, 'chnl_logo', true);
    $chnl_comp_id = get_post_meta($channel_id, 'chnl_comp_id', true);
    $dspro_channel_id = get_post_meta($channel_id, 'dspro_channel_id', true);

    $channel_details = array(
        "chnl_title" => $chnl_title,
        "chnl_content" => $chnl_content,
        "chnl_poster" => $chnl_poster,
        "chnl_spotlisgt_poster" => $chnl_spotlisgt_poster,
        "chnl_id" => $chnl_id,
        "chnl_writers" => $chnl_writers,
        "chnl_geners" => $chnl_geners,
        "chnl_actors" => $chnl_actors,
        "chnl_logo" => $chnl_logo,
        "chnl_comp_id" => $chnl_comp_id,
        "dspro_channel_id" => $dspro_channel_id,
    );

    return $channel_details;
}

function embed_video_by_id($video_id){
    echo "<div class='player custom-video'></div><script src='https://player.dotstudiopro.com/player/".$video_id."?targetelm=.player&companykey=592709ff97f81578165aa389&skin=228b22'></script>";
}

function get_video_by_id($video_id){
    $dspApi = new Dsp_External_Api_Request();
    return $dspApi->get_video_by_id($video_id);
}

function get_recommendation_by_video_id($video_id){
    $dspApi = new Dsp_External_Api_Request();
    $recs = $dspApi->get_recommendation('video', $video_id);
    if ( !$recs || empty($recs) || !is_array($recs) || !$recs['success'] ) return null;
    $videos = [];
    foreach($recs['playlist'] as $rec) {
        if (!empty($rec['thumb']) ) {
            if (strpos($rec['thumb'], "https://") === false) {
                $rec['thumb'] = get_option('dsp_cdn_img_url_field') . $rec['thumb'];
            } else {
                $rec['thumb'] = $rec['thumb'];
            }
        }
        $videos[] = $rec;
    }
    return $videos;
}

function abs_register_query_vars( $vars ) {
    $vars[] .= 'video_id';
//    $vars[] .= 'v_category';
//    $vars[] .= 'v_channel';
    return $vars;
}
add_filter( 'query_vars', 'abs_register_query_vars' );

function abs_rewrite_tag() {
    add_rewrite_tag( '%watch-video%', '([^&]+)' );
    add_rewrite_rule( '^watch-video/([^/]*)/?$', 'index.php?video_id=$matches[1]','top' );

//    add_rewrite_tag( '%watch%', '([^&]+)/([^&]+)' );
//    add_rewrite_rule( '^watch\/([^/]*)\/?([^/]*)/?$', 'index.php?v_category=$matches[1]&v_channel=$matches[2]','top' );
//    add_rewrite_tag( '%watch%', '([^&]+)' );
//    add_rewrite_rule( '^watch\/([^/]*)/?', 'index.php?v_category=$matches[1]','top' );


}
add_action('init', 'abs_rewrite_tag', 10, 0);


function abs_rewrite_catch()
{
    global $wp_query;

    if ( array_key_exists( 'video_id', $wp_query->query_vars ) ) {
        include_once (TEMPLATEPATH . '/template-video.php');
        exit;
    }

//    if ( array_key_exists( 'v_category', $wp_query->query_vars ) &&  array_key_exists( 'v_channel', $wp_query->query_vars ) && empty($wp_query->query_vars['v_channel'])) {
//        include_once (TEMPLATEPATH . '/template-video-category.php');
//        exit;
//    }
//
//    if ( array_key_exists( 'v_category', $wp_query->query_vars ) && array_key_exists( 'v_channel', $wp_query->query_vars )) {
//        include_once (TEMPLATEPATH . '/template-video-channel.php');
//        exit;
//    }



}
add_action( 'template_redirect', 'abs_rewrite_catch' );


function video_page_template( $template ) {
    global $wp_query;

    if ( array_key_exists( 'video_id', $wp_query->query_vars ) ) {
        $new_template = locate_template(array('template-video.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
    }

    if ( array_key_exists( 'v_category', $wp_query->query_vars ) && array_key_exists( 'v_channel', $wp_query->query_vars ) ) {
        $new_template = locate_template( array( 'template-video-channel.php' ) );
        if ( !empty( $new_template ) ) {
            return $new_template;
        }
    }

    if ( array_key_exists( 'v_category', $wp_query->query_vars ) ) {
        $new_template = locate_template( array( 'template-video-category.php' ) );
        if ( !empty( $new_template ) ) {
            return $new_template;
        }
    }

    error_log(json_encode($template));
    return $template;
}
add_filter( 'template_include', 'video_page_template', 99 );


//$page = 1;
//$args = array(
//    'post_type' => 'channel',
//    'posts_per_page' => 5,
//    'paged' => $page
//);
//$channels = new WP_Query( $args );
//$videos = [];
//foreach ($channels->posts as $ch){
//    $chVideos = maybe_unserialize(get_post_meta($ch->ID, 'chnl_videos', true));
//    if($chVideos != ""){
//        foreach ($chVideos as $v){
//            $videos[] = ['/watch-video/'.$v['_id'], '/watch-video/'.$v['slug'], '0', 'url', '301', 'url', '0'];
//        }
//    }
//}
//
//
//
//function array2csv(array &$videos){
//    if (count($videos) == 0) {
//        return null;
//    }
//    ob_start();
//    $df = fopen("php://output", 'w');
//    fputcsv($df, array_keys(reset($videos)));
//    foreach ($videos as $row) {
//        fputcsv($df, $row);
//    }
//    fclose($df);
//    return ob_get_clean();
//}
//
//
//function download_send_headers($filename) {
//    // disable caching
//    $now = gmdate("D, d M Y H:i:s");
//    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
//    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
//    header("Last-Modified: {$now} GMT");
//
//    // force download
//    header("Content-Type: application/force-download");
//    header("Content-Type: application/octet-stream");
//    header("Content-Type: application/download");
//
//    // disposition / encoding on response body
//    header("Content-Disposition: attachment;filename={$filename}");
//    header("Content-Transfer-Encoding: binary");
//}
//
//
//download_send_headers("video-redirection-" .$page. ".csv");
//echo array2csv($videos);
//die();