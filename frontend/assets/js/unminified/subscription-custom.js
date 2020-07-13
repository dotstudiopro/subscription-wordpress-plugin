var success_img = customVars.basedir + '/frontend/assets/images/true.png';
var loader_gif = customVars.basedir + '/frontend/assets/images/loading.gif';
var error_img = customVars.basedir + '/frontend/assets/images/false.png';
var url = customVars.ajaxurl;

jQuery(document).ready(function() {

    (function($) {

        $('.sameSize, .sameSize-inner').matchHeight({
            byRow: true,
            property: 'height',
            target: null,
            remove: false
        })
        $('.sameSize-inner').matchHeight({
            byRow: true,
            property: 'height',
            target: null,
            remove: false
        })

        if ($('#card_number').length) {
            var current_card = "";
            $('#card_number').validateCreditCard(function(e) {
                if (e.valid) {
                    $(this).addClass('valid');
                } else {
                    $(this).removeClass('valid');
                }
                if (typeof e.card_type != 'undefined' && e.card_type != null && e.card_type.name != '') {
                    if (current_card != "" && current_card != e.card_type.name) {
                        $(this).removeClass(current_card);
                        $(this).addClass(e.card_type.name);
                    }
                    current_card = e.card_type.name
                    $(this).addClass(e.card_type.name);
                } else if (e.card_type == null && current_card != "") {
                    $(this).removeClass(current_card);
                }

            });
        }

        /**
         * Submit the form of the page on subscription list page  after selecting the package
         */
        $('.select_plan').each(function(index) {
            $(this).on('click', function(e) {
                e.preventDefault();
                var form_id = $(this).data('subscriptionid');
                $('form#form_' + form_id).submit();
            });
        });

        /**
         * Action to load login pop-up if user is not logged-in
         */
        $('.login-link').on('click', function(e) {
            e.preventDefault();
            $('#a0LoginButton').click();
        });

        /**
         * AJAX action to validate the coupon
         */
        $('#validate_coupon').on('click', function(e) {
            e.preventDefault();
            $(this).prop('disabled', true);
            var coupon = $('#coupon_code').val();
            if (!coupon) {
                $('.messages-notices').html('<p class="error">Please add coupon code</p>');
                return;
            }
            var action = $(this).data('action');
            var nonce = $(this).data('nonce');
            $('.coupon-responce').show();
            $('.coupon-responce').html('<img class="activation-img" src="' + loader_gif + '">');
            var validate_coupon = $.post(
                url, {
                    'action': action,
                    'coupon': $('#coupon_code').val(),
                    'nonce': nonce
                }
            );
            validate_coupon.done(function(response) {
                $(this).prop('disabled', false);
                $('.coupon-responce').html('<img class="activation-img" src="' + success_img + '">');
                $('.coupon-messages-notices').removeClass('error').addClass('success').html('<p class="mb-0">' + response.data.message + '</p>');
            });

            validate_coupon.fail(function(response) {
                $(this).prop('disabled', false);
                $('.coupon-responce').html('<img class="activation-img" src="' + error_img + '">');
                $('.coupon-messages-notices').removeClass('success').addClass('error').html('<p class="mb-0">' + response.responseJSON.data.message + '</p>');
            })
        });

        /**
         *  AJAX action to submit the credit card details to subscribe the subscription
         */
        $('button#submit_cc').on('click', function(e) {
            e.preventDefault();
            var form = $('#form_payment')[0];
            var validated = validatePaymentForm(e, form);
            if (validated) {
                customOverlay(true);
                showSnacksBar(true);
                var action = $(this).data('action');
                var nonce = $('#nonce').val();
                var formData = $('#form_payment').serialize();
                $('#snackbar').html('Sending your request...');
                var submit_form = $.post(
                    url, {
                        'action': action,
                        'formData': formData,
                        'nonce': nonce
                    }
                );
                submit_form.done(function(response) {
                    customOverlay(false);
                    showSnacksBar(false);
                    $('.cc-messages-notices').removeClass('error').addClass('success').html('<p>Payment Received...Please wait...</p>');
                    window.location.href = $('#form_payment').attr('action');
                    var url = $('#form_payment').attr('action');
                    var form = $('<form action="' + url + '" method="post">' +
                        '<input type="hidden" name="thankyou" value="' + nonce + '" />' +
                        '</form>');
                    $('body').append(form);
                    form.submit();
                });

                submit_form.fail(function(response) {
                    customOverlay(false);
                    showSnacksBar(false);
                    $('.cc-messages-notices').removeClass('success').addClass('error').html('<p class="mb-0">' + response.responseJSON.data.message + '</p>')
                })
            }
        });

        /**
         *  AJAX action to submit the credit card details to subscribe the subscription
         */
        $('button#update_cc').on('click', function(e) {
            e.preventDefault();
            var form = $('#form_payment')[0];
            var validated = validatePaymentForm(e, form);
            if (validated) {
                customOverlay(true);
                showSnacksBar(true);
                var action = $(this).data('action');
                var nonce = $('#nonce').val();
                var formData = $('#form_payment').serialize();
                $('#snackbar').html('Sending your request...');
                var submit_form = $.post(
                    url, {
                        'action': action,
                        'formData': formData,
                        'nonce': nonce
                    }
                );
                submit_form.done(function(response) {
                    $('#snackbar').html('Your details are upto date...Please wait...');
                    $('.cc-messages-notices').removeClass('error').addClass('success').html('<p>Your details are upto date...Please wait...</p>');
                    window.location.href = $('#form_payment').attr('action');
                    var url = $('#form_payment').attr('action');
                    var form = $('<form action="' + url + '" method="post">' +
                        '<input type="hidden" name="payment-profile" value="' + nonce + '" />' +
                        '</form>');
                    $('body').append(form);
                    form.submit();
                });

                submit_form.fail(function(response) {
                    $('#snackbar').html('Something went wrong...');
                    $('.cc-messages-notices').removeClass('success').addClass('error').html('<p class="mb-0">' + response.responseJSON.data.message + '</p>')
                })
            }
            setTimeout(function() {
                customOverlay(false);
                showSnacksBar(false);
            }, 3000);
        });


        /**
         * Function to load overlay on clisk of "Subscribe" button on payment page
         * @param {type} display
         * @returns {undefined}
         */
        function customOverlay(display) {

            var docHeight = $(document).height();
            if (display)
                $("body").append("<div id='overlay'></div>");
            else
                $("div#overlay").remove();
            $("#overlay")
                .height(docHeight)
                .css({
                    'opacity': 0.4,
                    'position': 'absolute',
                    'top': 0,
                    'left': 0,
                    'background-color': 'black',
                    'width': '100%',
                    'z-index': 5000
                });
        }

        /**
         * Display conformation pop-up on update subscription button click after that make ajax call on confirm button click
         */

        $('#update_subscription_button').confirm({
            content: "Click button below to confirm subscripition change.",
            boxWidth: '350px',
            useBootstrap: false,
            theme: 'custom',
            animation: 'zoom',
            closeAnimation: 'scale',
            typeAnimated: true,
            buttons: {
                CONFIRM: {
                    text: 'CONFIRM',
                    btnClass: 'btn btn-secondary btn-ds-secondary',
                    action: function() {
                        var subscription_id = $('.update_subscription_id').val();
                        var action = $('#update_subscription_button').data('action');
                        var nonce = $('#update_subscription_button').data('nonce');
                        var update_subscription = $.post(
                            url, {
                                'action': action,
                                'subscription_id': subscription_id,
                                'nonce': nonce
                            }
                        );

                        $.alert({
                            title: 'Update Subscription',
                            boxWidth: '350px',
                            columnClass: 'loader',
                            useBootstrap: false,
                            theme: 'custom',
                            animation: 'zoom',
                            closeAnimation: 'scale',
                            typeAnimated: true,
                            content: '<img class="activation-img pt-3 pb-3" src="' + loader_gif + '"><p class="text-center"> Processing....</p>'
                        });
                        setTimeout(function() {
                            $('.jconfirm-buttons').hide();
                        }, 10);

                        update_subscription.done(function(response) {
                            $('.jconfirm-buttons button').trigger('click');
                            $.alert({
                                title: 'Update Subscription',
                                boxWidth: '350px',
                                columnClass: 'loader',
                                useBootstrap: false,
                                theme: 'custom',
                                animation: 'zoom',
                                closeAnimation: 'scale',
                                typeAnimated: true,
                                content: '<h4>Thank You!</h4><p class="mb-0">Your Package is updated successfully.</p>'
                            });
                            setTimeout(function() {
                                $('.jconfirm-buttons button').on('click', function() {
                                    window.location.reload();
                                });
                            }, 10);
                        });

                        update_subscription.fail(function(response) {
                            $('.jconfirm-buttons button').trigger('click');
                            $.alert({
                                title: 'Update Subscription',
                                boxWidth: '350px',
                                columnClass: 'loader',
                                useBootstrap: false,
                                theme: 'custom',
                                animation: 'zoom',
                                closeAnimation: 'scale',
                                typeAnimated: true,
                                content: '<h4> Error</h4><p class="mb-0">' + response.responseJSON.data.message + '</p>'
                            });
                            setTimeout(function() {
                                $('.jconfirm-buttons button').on('click', function() {
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
            content: "Click button below to confirm cancle Subscription.",
            boxWidth: '350px',
            useBootstrap: false,
            theme: 'custom',
            animation: 'zoom',
            closeAnimation: 'scale',
            typeAnimated: true,
            buttons: {
                CONFIRM: {
                    text: 'CONFIRM',
                    btnClass: 'btn btn-secondary btn-ds-secondary',
                    action: function() {
                        var action = $('#cancel_subscription_button').data('action');
                        var nonce = $('#cancel_subscription_button').data('nonce');
                        var cancle_subscription = $.post(
                            url, {
                                'action': action,
                                'nonce': nonce
                            }
                        );
                        $.alert({
                            title: 'Update Subscription',
                            boxWidth: '350px',
                            columnClass: 'loader',
                            useBootstrap: false,
                            theme: 'custom',
                            animation: 'zoom',
                            closeAnimation: 'scale',
                            typeAnimated: true,
                            content: '<img class="activation-img pt-3 pb-3" src="' + loader_gif + '"><p class="text-center"> Processing....</p>'
                        });
                        setTimeout(function() {
                            $('.jconfirm-buttons').hide();
                        }, 10);

                        cancle_subscription.done(function(response) {
                            $('.jconfirm-buttons button').trigger('click');
                            $.alert({
                                title: 'Update Subscription',
                                boxWidth: '350px',
                                columnClass: 'loader',
                                useBootstrap: false,
                                theme: 'custom',
                                animation: 'zoom',
                                closeAnimation: 'scale',
                                typeAnimated: true,
                                content: '<p>We have received your request to cancel your subscription. Your subscription will automatically cancel at the end of your trial period.</p>'
                            });
                            setTimeout(function() {
                                $('.jconfirm-buttons button').on('click', function() {
                                    window.location.reload();
                                });
                            }, 10);
                        });

                        cancle_subscription.done(function(response) {
                            $('.jconfirm-buttons button').trigger('click');
                            $.alert({
                                title: 'Update Subscription',
                                boxWidth: '350px',
                                columnClass: 'loader',
                                useBootstrap: false,
                                theme: 'custom',
                                animation: 'zoom',
                                closeAnimation: 'scale',
                                typeAnimated: true,
                                content: '<h4>Thank You!</h4><p class="mb-0">Your Package is updated successfully.</p>'
                            });
                            setTimeout(function() {
                                $('.jconfirm-buttons button').on('click', function() {
                                    window.location.reload();
                                });
                            }, 10);
                        });


                        cancle_subscription.fail(function(response) {
                            $('.jconfirm-buttons button').trigger('click');
                            $.alert({
                                title: 'Update Subscription',
                                boxWidth: '350px',
                                columnClass: 'loader',
                                useBootstrap: false,
                                theme: 'custom',
                                animation: 'zoom',
                                closeAnimation: 'scale',
                                typeAnimated: true,
                                content: '<h4>Error</h4><p class="mb-0">' + response.responseJSON.data.message + '</p>'
                            });
                            setTimeout(function() {
                                $('.jconfirm-buttons button').on('click', function() {
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
});

function validatePaymentForm(event, form) {
    if (form.checkValidity() === false) {
        var invalidFields = jQuery(form).find(":invalid").each(function(index, node) {
            jQuery(node).nextAll("div.invalid-feedback").show();
        });
        event.preventDefault();
        event.stopPropagation();
        form.classList.add('invalid');
        return false;
    }
    jQuery("div.invalid-feedback").hide();
    form.classList.add('valid');
    return true;

}

function showSnacksBar(action) {
    // Get the snackbar DIV
    var x = document.getElementById("snackbar");
    x.className = "show";
    if (!action)
        x.className = x.className.replace("show", "");
}
