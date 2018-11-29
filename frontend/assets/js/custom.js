(function ($) {

    var url = customVars.ajaxurl;
    var loader_gif = customVars.basedir + '/frontend/assets/img/loader.gif';

    if ($('#card_number').length)
        $('#card_number').payform('formatCardNumber');
    if ($('#expire_date').length)
        $('#expire_date').payform('formatCardExpiry');
    if ($('#cvv').length)
        $('#cvv').payform('formatCardCVC');

    /**
     * Display conformation pop-up on update subscription button click after that make ajax call on confirm button click
     */

    $('#update_subscription_button').confirm({
        content: "CLICK BUTTON BELOW TO CONFIRM SUBSCRIPTION CHANGE",
        theme: 'bootstrap',
        animation: 'zoom',
        closeAnimation: 'scale',
        type: 'purple',
        typeAnimated: true,
        buttons: {
            CONFIRM: {
                text: 'CONFIRM',
                action: function () {
                    var subscription_id = $('.update_subscription_id').val();
                    var action = $('#update_subscription_button').data('action');
                    var nonce = $('#update_subscription_button').data('nonce');
                    var client_token = $('#update_subscription_button').data('client');
                    var update_subscription = $.post(
                            url,
                            {
                                'action': action,
                                'subscription_id': subscription_id,
                                'nonce': nonce,
                                'client_token': client_token
                            }
                    );

                    $.alert('<div><img class="activation-img pb-3" src="' + loader_gif + '" style="margin-left:44%;"><p class="text-center"> Processing....</p></div>');
                    setTimeout(function () {
                        $('.jconfirm-buttons').hide();
                    }, 10);

                    update_subscription.done(function (response) {
                        $('.jconfirm-buttons button').trigger('click');
                        $.alert('<h4 class="text-center">Thank You!</h4><br /><p>Your Package is updated successfully.</p>');
                        setTimeout(function () {
                            $('.jconfirm-buttons button').on('click', function () {
                                window.location.reload();
                            });
                        }, 10);
                    });

                    update_subscription.fail(function (response) {
                        $('.jconfirm-buttons button').trigger('click');
                        $.alert('<h4 class="text-center"> Error </h4> <br /><p>' + response.responseJSON.data.message + '</p>');
                        setTimeout(function () {
                            $('.jconfirm-buttons button').on('click', function () {
                                window.location.reload();
                            });
                        }, 10);

                    })
                }
            },
            CANCLE: {
                text: 'CANCLE',
            }
        }
    });

    /**
     * Display conformation pop-up on Cancle subscription button click after that make ajax call on confirm button click
     */
    $('#cancel_subscription_button').confirm({
        content: "CLICK BUTTON BELOW TO CONFIRM CANCLE SUBSCIPTION",
        theme: 'bootstrap',
        animation: 'zoom',
        closeAnimation: 'scale',
        type: 'purple',
        typeAnimated: true,
        buttons: {
            CONFIRM: {
                text: 'CONFIRM',
                action: function () {
                    var action = $('#cancel_subscription_button').data('action');
                    var nonce = $('#cancel_subscription_button').data('nonce');
                    var client_token = $('#cancel_subscription_button').data('client');
                    var cancle_subscription = $.post(
                            url,
                            {
                                'action': action,
                                'nonce': nonce,
                                'client_token': client_token
                            }
                    );

                    $.alert('<div><img class="activation-img pb-3" src="' + loader_gif + '" style="margin-left:44%;"><p class="text-center"> Processing....</p></div>');
                    setTimeout(function () {
                        $('.jconfirm-buttons').hide();
                    }, 10);

                    cancle_subscription.done(function (response) {
                        $('.jconfirm-buttons button').trigger('click');
                        $.alert('<p>We have received your request to cancel your subscription. Your subscription will automatically cancel at the end of your trial period.</p>');
                        setTimeout(function () {
                            $('.jconfirm-buttons button').on('click', function () {
                                window.location.reload();
                            });
                        }, 10);
                    });

                    cancle_subscription.fail(function (response) {
                        $('.jconfirm-buttons button').trigger('click');
                        $.alert('<h4 class="text-center"> Error </h4> <br /><p>' + response.responseJSON.data.message + '</p>');
                        setTimeout(function () {
                            $('.jconfirm-buttons button').on('click', function () {
                                window.location.reload();
                            });
                        }, 10);

                    })
                }
            },
            CANCLE: {
                text: 'CANCLE',
            }
        }
    });


})(jQuery);