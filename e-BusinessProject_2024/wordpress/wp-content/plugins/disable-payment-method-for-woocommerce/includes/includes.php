<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/review.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/help.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-common-cart.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/pisol.class.form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-store-data-order.php';

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-disable-payment-method-for-woocommerce-menu.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-payment-method-disable-rules.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-add-disable-payment-rule.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/selection_rules/includes.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cod-deposit.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-third-party-support.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/advance-fees/admin.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/extra-setting.php';

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-filter-payment-methods.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-apply-fees.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-partial-payment-ui.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-partial-payment.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-partial-payment-order-state.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-partial-payment-email.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-partial-payment-session.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-apply-order-fees.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-phone-pay.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wc-partial-order-pay.php';

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'block/includes.php';

/**
 * load this file on wp_loaded as it has class defination that is extension of WC_order which will throw error if loaded before
 */
add_action('wp_loaded', function(){
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-shop-deposit-order.php';
});