(function ($) {
    if($('#card_number').length)
        $('#card_number').payform('formatCardNumber');
    if($('#expire_date').length)
        $('#expire_date').payform('formatCardExpiry');
    if($('#cvv').length)
        $('#cvv').payform('formatCardCVC');
})(jQuery);