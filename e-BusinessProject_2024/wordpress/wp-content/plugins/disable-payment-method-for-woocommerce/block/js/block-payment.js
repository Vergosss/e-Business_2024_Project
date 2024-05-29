/**
 * when the block loads set-active-payment-method is not called by WooCommerce core so we have added this to call that hook so that we can set the payment method in the backend on loading
 */
document.addEventListener('DOMContentLoaded', function () {
    const store = wp.data.select( wc.wcBlocksData.PAYMENT_STORE_KEY );
    var payment_method = store.getActivePaymentMethod();
    var data = { 'value': payment_method };
    wp.hooks.doAction('pisol_initial_checkout_load', data);
});

/**
 * this reads the hook of set-active-payment-method and then update the payment method in the backend
 */
wp.hooks.addAction('experimental__woocommerce_blocks-checkout-set-active-payment-method', 'woocommerce-block-checkout', (data) => {
    pisol_set_payment_method(data.value);
}); 

wp.hooks.addAction('pisol_initial_checkout_load', 'woocommerce-block-checkout', (data) => {
    pisol_set_payment_method(data.value);
});

function pisol_set_payment_method(payment_method){
    if (typeof payment_method == 'undefined') return;
    if (payment_method == '') return;
    var data = { payment_method: payment_method };
    document.body.classList.add('pi-dpmw-processing');
    wc.blocksCheckout.extensionCartUpdate({
        namespace: 'pisol_set_payment_method',
        data: data
    }).then( function(  ) {
        document.body.classList.remove('pi-dpmw-processing');
    });
}

