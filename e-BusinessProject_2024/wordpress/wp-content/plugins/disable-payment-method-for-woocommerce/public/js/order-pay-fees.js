(function ($) {
    'use strict';
    jQuery(function ($) {
        const orderPayReferrer = $('input[name="_wp_http_referer"]').val();
        let referrerArr = '';

        if (orderPayReferrer != undefined) {
            referrerArr = orderPayReferrer.split('/');
        }


        $('form#order_review').on('click', 'input[name="payment_method"]', () => {

            updateFees();

        });

        // On page load, if the payment method is not set then set it to the default payment method.
        function updateFees() {
            const order_id = (pisol_dpmw_checkout_order_id.order_id) ? pisol_dpmw_checkout_order_id.order_id : referrerArr[3];

            $('#place_order').prop('disabled', true);

            $("#order_review").addClass('pi-processing');

            var paymentMethod = $('input[name="payment_method"]:checked').val();

            // Get Payment Title and strip out all html tags.
            var paymentMethodTitle = $(`label[for="payment_method_${paymentMethod}"]`).text().replace(/[\t\n]+/g, '').trim();

            // On visiting Pay for order page, take the payment method and payment title which are present in the order.
            /*
            if ('' !== pisol_dpmw_checkout_order_id.payment_method) {
                paymentMethod = pisol_dpmw_checkout_order_id.payment_method;
                paymentMethodTitle = $(`label[for="payment_method_${paymentMethod}"]`).text().replace(/[\t\n]+/g, '').trim();
            }
            */

            const data = {
                payment_method: paymentMethod,
                payment_method_title: paymentMethodTitle,
                order_id: order_id,
                security: pisol_dpmw_checkout_order_id.update_payment_method_nonce,
            };

            // We need to set the payment method blank because when second time when it comes here on changing the payment method it should take that changed value and not the payment method present in the order.
            pisol_dpmw_checkout_order_id.payment_method = '';
            $.post('?wc-ajax=update_fees', data, (response) => {
                $('#place_order').prop('disabled', false);
                
                if (response && response.fragments) {
                    var shop_table = $(response.fragments).find('table.shop_table');
                    $('#order_review .shop_table').replaceWith(shop_table);
                    

                    /**
                     * this part is form the conditional fees plugin incase both the plugin are used simultaneously then this will help in its working as well 
                     */
                    var conditional_fees = $(response.fragments).find('.pi-condition-fees');
                    if($('#order_review .pi-condition-fees').length > 0){
                        $('#order_review .pi-condition-fees').replaceWith(conditional_fees);
                    }else{
                        $('#order_review .shop_table').after(conditional_fees);
                    }

                    /**
                     * this is removed as it broke payment method like stripe as they cant add there iframe after ajax load of content, so now we are loading only table and conditional checkbox by ajax and payment method are same 
                     */
                    //$('#order_review').html(response.fragments);

                    $(`input[name="payment_method"][value=${paymentMethod}]`).prop('checked', true);
                    $(`.payment_method_${paymentMethod}`).css('display', 'block');
                    $(`div.payment_box:not(".payment_method_${paymentMethod}")`).filter(':visible').slideUp(0);
                    $(document.body).trigger('updated_checkout');
                }
            }).always(() => { $("#order_review").removeClass('pi-processing'); });
        }
    });
})(jQuery);