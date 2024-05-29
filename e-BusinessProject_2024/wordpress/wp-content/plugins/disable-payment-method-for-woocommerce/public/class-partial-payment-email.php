<?php

class Pi_dpmw_partial_payment_email{
    protected static $instance = null;
    public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    protected function __construct(){
        $this->email_ids = ['new_order', 'failed_order', 'customer_refunded_order', 'customer_processing_order', 'on_hold_order', 'customer_on_hold_order', 'customer_invoice', 'customer_completed_order', 'cancelled_order'];
        /**
         * show remaining payment details in parent order in email
         */
        add_action( 'woocommerce_email_order_meta', [$this, 'pending_payment_order_detail'], 10, 1 );

        $this->disableEmailForPartialPaidAndPendingPayment();

        
    }

    public function pending_payment_order_detail( $order ) {

        if ( empty( $order->get_meta( '_pi_advance_amount', true ) ) ) {
            return; // hide summary for non deposit orders
        }
        
        wc_get_template( 'order/pending-payment-summary.php', array( 'order' => $order ), '', DISABLE_PAYMENT_METHOD_FOR_WOOCOMMERCE_DOCUMENTATION_PATH );
    }

    function disableEmailForPartialPaidAndPendingPayment(){
        $this->email_ids  = apply_filters('pi_dpmw_disable_email_for_ids', $this->email_ids);
        foreach($this->email_ids as $id){
            add_filter( 'woocommerce_email_enabled_' . $id, [$this, 'is_enabled'],10,3);
        }
    }

    function is_enabled($yes_no, $order, $email){
        if(empty($order) || !is_object($order)) return $yes_no;

        $order_id = $order->get_id();
        $type = $order->get_type();
        if($type == 'pi_pending_amt'){

            if(Pi_dpmw_partial_payment::is_deposit_payment_order( $order_id ) && apply_filters('pi_dpmw_enable_parent_email_on_child_state_change', false, $order, $email)){
                $this->triggerParentEmail( $order, $email );
            }

            return false;
        }
        return $yes_no;
    }

    function triggerParentEmail( $order, $email ){
        $trigger_parent_for_id = 
        ['new_order' => 'WC_Email_New_Order',
        'customer_new_account' => 'WC_Email_Customer_New_Account', 
        'failed_order' => 'WC_Email_Failed_Order', 
        'customer_refunded_order' => 'WC_Email_Customer_Refunded_Order', 'customer_processing_order' => 'WC_Email_Customer_Processing_Order', 'customer_on_hold_order' => 'WC_Email_Customer_On_Hold_Order', 'customer_invoice' => 'WC_Email_Customer_Invoice', 'customer_completed_order' => 'WC_Email_Customer_Completed_Order', 'cancelled_order' => 'WC_Email_Cancelled_Order'];

        if(isset($email->id) && isset($trigger_parent_for_id[$email->id])){
            $parent_order_id = $order->get_parent_id();
            $class = $trigger_parent_for_id[$email->id];
            WC()->mailer()->get_emails()[$class]->trigger( $parent_order_id );
        }
        
    }
}

Pi_dpmw_partial_payment_email::get_instance();