<?php
use PISOL\DPMW\Session;
class Pi_dpmw_partial_payment{

    protected static $instance = null;

    public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    protected function __construct(){
		/**
         * add partial payment details in parent order meta
         */
		add_action( 'woocommerce_checkout_update_order_meta', [$this, 'manage_order'], 10, 2 );

        /** we added this so online payment order completion email have sub order details */
        add_action( 'woocommerce_order_status_completed_notification', array( $this, 'manage_deposit_orders_online' ), 10, 2 );
        add_action( 'woocommerce_order_status_cancelled_to_processing_notification', array( $this, 'manage_deposit_orders_online' ), 10, 2 );
		add_action( 'woocommerce_order_status_failed_to_processing_notification', array( $this, 'manage_deposit_orders_online' ), 10, 2 );
		add_action( 'woocommerce_order_status_on-hold_to_processing_notification', array( $this, 'manage_deposit_orders_online' ), 10, 2 );
		add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'manage_deposit_orders_online' ), 10, 2 );
        /** End */

		/**
         * Generate deposit orders for online payment
         */
		add_action( 'woocommerce_payment_complete', [$this, 'manage_deposit_orders'], 10, 1 );

		/**
         * Generate deposit order for offline payment
         */
		add_filter( 'woocommerce_cod_process_payment_order_status', [$this, 'offline_deposit_orders'], 10, 2 );
        add_filter( 'woocommerce_bacs_process_payment_order_status', [$this, 'offline_deposit_orders'], 10, 2 );
        add_filter( 'woocommerce_cheque_process_payment_order_status', [$this, 'offline_deposit_orders'], 10, 2 );


        add_filter('woocommerce_order_class',  array($this, 'order_class'), 10, 3 );

         /**
         * register deposit post type
         */
        add_action( 'init', [$this, 'register_order_type'] );

		/**
         * apply partial payment extra fees
         */
        add_action('woocommerce_cart_calculate_fees' , array($this,'addfees'));

        /**
         * This is needed to force regeneration of order when user unchecks the partial payment option and then does the payment (earlier he was doing the payment with the partial payment option checked so the order was generated with the partial payment option checked and the order was not generated again when he unchecked the partial payment option and did the payment)
         */
        add_filter( 'woocommerce_cart_hash', [$this, 'cart_hash'], PHP_INT_MAX);
    }

    function cart_hash($hash){
        if(Session::partialPaymentSelected()){
            $hash = $hash . "pp";
        }
        return $hash;
    }

	public function manage_order( $orderId, $data ) {
        $order = wc_get_order( $orderId );
		
		if(Session::partialPaymentSelected()){
			$original_total = Session::getOriginalTotal();
			$advance_amount = Session::getPartialAmtToPay();
			$balance_amount = Session::getBalanceAmountToPay();
			
			$order->update_meta_data( '_pi_original_total', $original_total, true );
			$order->update_meta_data( '_pi_advance_amount', $advance_amount, true );
			$order->update_meta_data( '_pi_balance_amount', $balance_amount, true );

		}
        $order->save();
    }

    public function manage_deposit_orders_online( $order_id, $order ) {
        if(empty($order)) return;

        if ( self::isDepositOrder( $order ) && $order->get_meta( '_generate_deposit_orders', true ) != 1 ) {
            // create deposit orders based on parent order
            $this->generate_deposit_order( $order );
        }
    }

	public function manage_deposit_orders( $orderId ) {

        $order = wc_get_order( $orderId );
        if ( self::isDepositOrder( $order ) && $order->get_meta( '_generate_deposit_orders', true ) != 1 ) {
            // create deposit orders based on parent order
            $this->generate_deposit_order( $order );
        }
        
    }

	public function offline_deposit_orders(  $status, $order ) {

        if ( self::isDepositOrder( $order ) && $order->get_meta( '_generate_deposit_orders', true ) != 1 ) {
            // create deposit orders based on parent order
            $this->generate_deposit_order( $order );
			return 'wc-partial-paid';
        }
		
		return $status;
    }

	static function isDepositOrder( $order){
		if ( !empty( $order->get_meta( '_pi_advance_amount', true ) ) ) {
			return true;
		}
		return false;
	}

	function generate_deposit_order( $order ){
		$parent_order_id = $order->get_id();

		$pending_amt_order_id = self::create_pending_payment_order( $parent_order_id );

        $partial_amt_order_id = self::create_partial_payment_order( $parent_order_id );

		$order->update_meta_data( '_generate_deposit_orders', 1, true );
        $order->save();
	}

	static function create_pending_payment_order( $parent_order_id ){
        $order = wc_get_order( $parent_order_id );
        $pending_payment_order_id = $order->get_meta( '_generated_balance_amt_order', true );

        //require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-shop-deposit-order.php';

        $balance_amount = $order->get_meta('_pi_balance_amount', true);

        if(empty($balance_amount)) return;

        if(empty($pending_payment_order_id)){
            $DueOrder = new PIShopDeposit();
            // Order details
            $DueOrder->set_customer_id( $order->get_user_id() );
            $DueOrder->set_parent_id( $order->get_id() );

            // Fee items
            $item = new WC_Order_Item_Fee();
            $item->set_name( __('Due Payment for order #','disable-payment-method-for-woocommerce') . $order->get_id() . '-2' );
            $item->set_total_tax( 0 );
			$item->set_tax_status( 'none' );
            $item->set_total( $balance_amount );
            $item->save();
            $DueOrder->add_item( $item );
            $DueOrder->calculate_totals();
            $DueOrder->update_meta_data( '_deposit_id', $order->get_id() . '-2', true );
            $DueOrder->update_meta_data( '_deposit_order_type', 'pending_payment', true );
            $DueOrder->set_status( 'pending' );
			$currency = $order->get_currency('f');
            $DueOrder->set_currency($currency);
            $pending_payment_order_id = $DueOrder->save();

            if(!empty($pending_payment_order_id)){
				$order->update_meta_data( '_generated_balance_amt_order', $pending_payment_order_id, true );
                $order->save();
            }
        }

        return $pending_payment_order_id;
    }

    static function create_partial_payment_order( $parent_order_id ){
        $order = wc_get_order( $parent_order_id );
        $deposit_payment_order_id = $order->get_meta( '_generated_deposit_amt_order', true );

        //require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-shop-deposit-order.php';

        $advance_amount = $order->get_meta('_pi_advance_amount', true);
        if(empty($advance_amount)) return;

        if(empty($deposit_payment_order_id)){

            $offline_payment_gatway_ids = ['bacs', 'cheque', 'cod'];
            $first_deposit_order_status = 'completed';

            $available_gateways = WC()->payment_gateways->get_available_payment_gateways();

			$payment_method = $order->get_payment_method();
            $transaction_id = $order->get_transaction_id();

            if( in_array($payment_method, $offline_payment_gatway_ids)){
                $first_deposit_order_status = 'on-hold';
            }

            $DueOrder = new PIShopDeposit();
            // Order details
            $DueOrder->set_customer_id( $order->get_user_id() );
            $DueOrder->set_parent_id( $order->get_id() );

            // Fee items
            $item = new WC_Order_Item_Fee();
            $item->set_name( __('Partial payment for order #','disable-payment-method-for-woocommerce') . $order->get_id() . '-1' );
            $item->set_total_tax( 0 );
            $item->set_tax_status( 'none' );
            $item->set_total( $advance_amount );
            $item->save();
            $DueOrder->add_item( $item );
            $DueOrder->set_total( $advance_amount );
            $DueOrder->calculate_totals();
			$DueOrder->set_payment_method( $payment_method );
            $DueOrder->set_transaction_id( $transaction_id );
            $DueOrder->update_meta_data( '_deposit_id', $order->get_id() . '-1', true );
            $DueOrder->update_meta_data( '_deposit_order_type', 'deposit_payment', true );
			$currency = $order->get_currency('f');
			$DueOrder->set_currency($currency);
            $DueOrder->set_status( $first_deposit_order_status );
            
            $deposit_payment_order_id = $DueOrder->save();

            if(!empty($deposit_payment_order_id)){
				$order->update_meta_data( '_generated_deposit_amt_order', $deposit_payment_order_id, true );
                $order->set_status( 'partial-paid' );

                /**
                 * we will set the main order back to original total after payment so the that way all report is correct as order is gone back to original total and we dont have to change the total display any more 
                 */
                $original_total = $order->get_meta('_pi_original_total', true);
                $order->set_total($original_total);
                
                $order->save();
            }
        }

        return $deposit_payment_order_id;
    }

    function order_class($classname, $order_type, $order_id ){
        if( $order_type == 'pi_pending_amt' ) {
          return 'PIShopDeposit';
        }
        return $classname;
    }
      
	function addfees($cart){
		$obj = Pi_dpmw_partial_payment_ui::get_instance();

        $partial_payment_rule = $obj->partialPaymentRuleAvailable();

        if(!$partial_payment_rule) return;

        $fees_selected = Session::partialPaymentSelected();

        if(empty($fees_selected)) return;

        $partial_payment_fees = 0; // In future we can give extra charge option in free version as well

        if( empty($partial_payment_fees) ) return;

        $label = get_option('pi_dpmw_partial_pay_fees', 'Partial payment fees');
        $fee_arg = array(
            'id' => 'pisol-dpmw-fees:'.$partial_payment_rule,
            'name'=> $label,
            'amount' => $partial_payment_fees,
            /*'taxable' =>  $taxable,
            'tax_class' => $tax_class */
        );
        $cart->fees_api()->add_fee( $fee_arg );
    }  
   
	/**
	 * register post type for deposit
	 *
	 * @return void
	 */
	public function register_order_type() {
		wc_register_order_type(
			'pi_pending_amt',
			array(
				'labels'                           => array(
					'name'      => __( 'Pending Payments','disable-payment-method-for-woocommerce'),
					'menu_name' => _x( 'Pending Payments', 'Admin menu name','disable-payment-method-for-woocommerce'),
				),
				'description'                      => __( 'This is where store pending Payments are stored.','disable-payment-method-for-woocommerce' ),
				'public'                           => false,
				'show_ui'                          => true,
				'capability_type'                  => 'shop_order',
				'map_meta_cap'                     => true,
				'publicly_queryable'               => false,
				'exclude_from_search'              => true,
				'show_in_menu'                     => current_user_can( 'manage_woocommerce' ) ? 'woocommerce' : true,
				'hierarchical'                     => false,
				'show_in_nav_menus'                => false,
				'capabilities'                     => array(
					'create_posts' => 'do_not_allow',
				),
				'query_var'                        => false,
				'supports'                         => array( 'title', 'custom-fields' ),
				'has_archive'                      => false,

				// wc_register_order_type() params
				'exclude_from_orders_screen'       => true,
				'add_order_meta_boxes'             => true,
				'exclude_from_order_count'         => true,
				'exclude_from_order_views'         => true,
				'exclude_from_order_webhooks'      => true,
				'exclude_from_order_reports'       => true,
				'exclude_from_order_sales_reports' => false,
				//   'class_name'                       => 'ShopDeposit',

			)
		);
	}

	static function is_deposit_payment_order( $order_id ){
        $order = wc_get_order( $order_id );
		$type = $order->get_meta('_deposit_order_type', true);
        return $type == 'deposit_payment' ? true : false;
    }

    static function is_remaining_payment_order( $order_id ){
        $order = wc_get_order( $order_id );
		$type = $order->get_meta('_deposit_order_type', true);
        return $type == 'pending_payment' ? true : false;
    }

    static function is_payment_done( $order_id ){
        $order = wc_get_order( $order_id );

        if(!is_object($order)) return false;

		$order_status = $order->get_status(); // Get the order status
        if ($order_status === 'completed' && $order->is_paid()) return true;

        return false;
    }

}

Pi_dpmw_partial_payment::get_instance();
