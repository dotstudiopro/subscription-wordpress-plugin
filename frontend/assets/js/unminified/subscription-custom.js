var success_img = customVars.assets_dir + 'images/true.png';
var loader_gif = customVars.assets_dir + 'images/loading.gif';
var error_img = customVars.assets_dir + 'images/false.png';
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
         * Submit the form of the page on tvod package detail page after selecting the product
         */
        $('.tvod_product_select').each(function(index) {
            $(this).on('click', function(e) {
                e.preventDefault();
                var form_id = $(this).data('subscriptionid');
                $('form#form_' + form_id).submit();
            });
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
                var previous_page_url = $(this).data('previouspageurl');
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
                        '<input type="hidden" name="previous_page_url" value="' + previous_page_url + '" />' +
                        '</form>');
                    $('body').append(form);
                    form.submit();
                });

                submit_form.fail(function(response) {
                    if($("div.g-recaptcha").length) {
                        grecaptcha.execute();
                    }
                    customOverlay(false);
                    showSnacksBar(false);
                    $('.cc-messages-notices').removeClass('success').addClass('error').html('<p class="mb-0">' + response.responseJSON.data.message + '</p>')
                })
            }
        });

        /**
         *  AJAX action to creates a new customer object in Chargify corresponding to an existing user in dotstudioPRO's Braintree account
         */
        $('button#complete_payment').on('click', function(e) {
            e.preventDefault();
            var form = $('#form_complete_payment')[0];
            customOverlay(true);
            showSnacksBar(true);
            var action = $(this).data('action');
            var previous_page_url = $(this).data('previouspageurl');
            var nonce = $('#nonce').val();
            var formData = $('#form_complete_payment').serialize();
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
                var url = $('#form_complete_payment').attr('action');
                var form = $('<form action="' + url + '" method="post">' +
                    '<input type="hidden" name="thankyou" value="' + nonce + '" />' +
                    '<input type="hidden" name="previous_page_url" value="' + previous_page_url + '" />' +
                    '</form>');
                $('body').append(form);
                form.submit();
            });
            submit_form.fail(function(response) {
                if($("div.g-recaptcha").length) {
                    grecaptcha.execute();
                }
                customOverlay(false);
                showSnacksBar(false);
                $('.cc-messages-notices').removeClass('success').addClass('error').html('<p class="mb-0">' + response.responseJSON.data.message + '</p>')
            })
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
                    if($("div.g-recaptcha").length) {
                        grecaptcha.execute();
                    }
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
                    text: 'CANCEL',
                }
            }
        });

        /**
         * Display conformation pop-up on Cancle subscription button click after that make ajax call on confirm button click
         */
        $('#cancel_subscription_button').confirm({
            content: "Click button below to confirm cancel Subscription.",
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
                    text: 'CANCEL',
                }
            }
        });

        /**
         * Code to open the modal popup when click on product channel
         */

        $(".open-channel-detail-modal").on("click", function() {
            var channel_name = $(this).data('channel-title');
            var channel_description = $(this).data('channel-description');
            var channel_actors = $(this).data('channel-actors');
            var channel_directors = $(this).data('channel-directors');
            var channel_image = $(this).data('channel-image');

            const channel_actors_formatted = channel_actors && channel_actors.length && channel_actors.join(', ');
            const channel_directors_formatted = channel_directors && channel_directors.length && channel_directors.join(', ');

            var modal = $('#channel-detail-modal');
            // Hide our buffers in case we don't need to show them
            modal.find('.modal_description_buffer').addClass('hidden');
            modal.find('.modal_director_actor_buffer').addClass('hidden');
            // Hide section titles in case we don't need them
            modal.find('.modal_actors_title').addClass('hidden');
            modal.find('.modal_directors_title').addClass('hidden');
            // Clear the populated text in sections to avoid showing bad data from
            // a previous selection
            modal.find('.modal_channel_description').text("");
            modal.find('.modal_channel_actors').text("");
            modal.find('.modal_channel_directors').text("");

            modal.find('.modal_channel_title').text(channel_name);
            modal.find('.modal_channel_image').attr('src', channel_image);
            if (channel_description && channel_description.length) {
                modal.find('.modal_description_buffer').removeClass('hidden');
                modal.find('.modal_channel_description').text(channel_description);
            }
            if (channel_actors_formatted || channel_directors_formatted) {
                modal.find('.modal_director_actor_buffer').removeClass('hidden');
                if (channel_actors_formatted) {
                    modal.find('.modal_channel_actors').text(channel_actors_formatted);
                    modal.find('.modal_actors_title').removeClass('hidden');
                }
                if (channel_directors_formatted) {
                    modal.find('.modal_channel_directors').text(channel_directors_formatted);
                    modal.find('.modal_directors_title').removeClass('hidden');
                }
            }
            $('#channel-detail-modal').appendTo("body").modal('show');
        });


        /**
         * Code to Submit the form when click on buy now button on product page
         */
        $('.product-buy-now').on('click', function(e) {
            e.preventDefault();
            var form_id = $(this).data('productid');
            $('form#form_' + form_id).submit();
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