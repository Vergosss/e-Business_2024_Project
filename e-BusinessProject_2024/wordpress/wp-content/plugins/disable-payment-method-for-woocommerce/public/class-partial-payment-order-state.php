<?php

class Pi_dpmw_partial_payment_order_state{
    protected static $instance = null;

    public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    protected function __construct(){
        add_action('woocommerce_order_status_changed', [$this, 'stateManager'], 10, 4);
    }

    function stateManager($order_id, $from, $to, $order){
        $type = $order->get_type( );

        if($type == 'pi_pending_amt'){
            $parent_order_id = $order->get_parent_id();
            $parent_order = wc_get_order( $parent_order_id );
            if(Pi_dpmw_partial_payment::is_deposit_payment_order( $order_id )){
                
                $same_state = ['pending', 'on-hold', 'cancelled', 'failed', 'refunded', 'processing'];
                if(in_array($to, $same_state)){
                    $parent_order->set_status($to);
                    $parent_order->save();
                }elseif($to == 'completed'){
                    $parent_order->set_status('partial-paid');
                    $parent_order->save();
                }

            }
        }
    }

}
Pi_dpmw_partial_payment_order_state::get_instance();