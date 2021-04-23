<?php
global $client_token;
$previous_page_url = isset($_REQUEST['previous_page_url']) ? $_REQUEST['previous_page_url'] : '';
if ($client_token && (wp_verify_nonce($_POST['thankyou'], 'submit_payment') || wp_verify_nonce($_POST['thankyou'], 'submit_complete_payment'))) {
    get_header();
    ?>
    <div class="custom-container container pt-5 pb-5 thankyou-page center-page-content">
        <div class="row no-gutters">
            <div class="col-md-12 text-center">
            <i class="fas fa-check-circle display-2 pb-3"></i>
                <h4>Thank You and Welcome!</h4>
                <p>Your payment was successfully processed</p>
                <div class="sb_wrapper">
                    <?php if(isset($previous_page_url) && !empty($previous_page_url)): ?>
                        <a href="<?php echo $previous_page_url; ?>" class="btn btn-secondary btn-ds-secondary">Start Exploring</a>
                    <?php else: ?>
                        <a href="/" class="btn btn-secondary btn-ds-secondary">Start Exploring</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    get_footer();
}
else{
    wp_redirect('/');
}
