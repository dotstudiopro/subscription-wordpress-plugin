<?php
global $client_token;
//if ($client_token && wp_verify_nonce($_POST['thankyou'], 'submit_payment')) {
    get_header();
    ?>
    <div class="custom-container container pt-5 pb-5 thankyou-page">
        <div class="row no-gutters">
            <div class="col-md-12 text-center">
            <i class="fas fa-check-circle display-2 pb-3"></i>
                <h4>Thank You and Welcome!</h4>
                <p>Your payment was successfully processed</p>
                <div class="sb_wrapper">
                    <a href="/" class="btn btn-secondary btn-ds-secondary">Start Browsing</a>
                </div>
            </div>
        </div>
    </div>
    <?php
    get_footer();
//}
//else{
//    wp_redirect('/');
//}
