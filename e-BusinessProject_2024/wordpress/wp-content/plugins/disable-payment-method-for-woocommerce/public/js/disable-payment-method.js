(function ($) {
    'use strict';

    function paymentMethod() {
        this.init = function () {
            this.detectPaymentChange();
            this.codDepositChange();
        }

        this.detectPaymentChange = function () {
            var parent = this;
            jQuery('body').on('change', 'input[name="payment_method"]', function () {
                parent.cartReload();
            });
        }

        this.cartReload = function () {
            jQuery("body").trigger('update_checkout');
        }

        this.codDepositChange = function () {
            var parent = this;
            jQuery('body').on('change', 'input[name="pi-cod-deposit"]', function () {
                parent.cartReload();
            });
        }
    }

    jQuery(function () {
        var paymentMethod_Obj = new paymentMethod();
        paymentMethod_Obj.init();
    });

})(jQuery);