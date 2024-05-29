<?php

class Pi_dpmw_order_pay_page{
    protected static $instance = null;

    public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    protected function __construct(){

        add_filter('woocommerce_order_get_total', array($this, 'pi_dpmw_order_get_total'), 10, 2);
    }

    function pi_dpmw_order_get_total($total, $order){

        $nonce_value = wc_get_var( $_REQUEST['woocommerce-pay-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

        if ( ! wp_verify_nonce( $nonce_value, 'woocommerce-pay' ) ) {
            return $total;
        }

        if(!Pi_dpmw_partial_payment::isDepositOrder( $order)) return $total;

        $adv_amt = $order->get_meta('_pi_advance_amount');
        if(!empty($adv_amt) && is_numeric($adv_amt) && $adv_amt > 0){
            $total = $adv_amt;
            //error_log($total);
        }
        
        return $total;
    }
}

Pi_dpmw_order_pay_page::get_instance();
