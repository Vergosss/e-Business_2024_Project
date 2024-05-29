<?php 

class Pi_dpmw_Apply_order_fees{

    protected static $instance = null;

    public $fees_amount = [];

    public static function get_instance( ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    function __construct(){

        if(self::disableOrderPay()) return;
        
        add_action('wp_enqueue_scripts', [$this, 'orderPayScript']);

        add_action( 'wc_ajax_update_fees', [ $this, 'update_order_fees_ajax' ] );

    }

    static function disableOrderPay(){
        return apply_filters('pi_dpmw_disable_order_pay_fees', false);
    }

    function orderPayScript(){
        global $wp;
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        if ( ! is_checkout() ) {
            return;
        }

        if(self::disableOrderPay()) return;

        /**
         * conditional fee plugin also adds this same js so we don't need to add this if we add it leads to double fee addition
         */
        if(is_plugin_active( 'conditional-extra-fees-for-woocommerce-pro/conditional-fees-rule-woocommerce.php')) return;

        if ( is_wc_endpoint_url( 'order-pay' ) && isset( $wp->query_vars['order-pay'] ) && absint( $wp->query_vars['order-pay'] ) > 0 ) {
            $order_id = absint( $wp->query_vars['order-pay'] );

            $order = wc_get_order( $order_id );

            $payment_method = $order->get_payment_method();
            
            wp_enqueue_script( 'dpmw-woocommerce-order-pay', plugin_dir_url( __FILE__ ) . 'js/order-pay-fees.js', array( 'jquery' ), '1.0.0', false );

            wp_localize_script(
                'dpmw-woocommerce-order-pay',
                'pisol_dpmw_checkout_order_id',
                array(
                    'order_id'       => get_query_var( 'order-pay' ),
                    'payment_method' => $payment_method,
                    'update_payment_method_nonce' => wp_create_nonce( 'update-payment-method' ),
                )
            );
        }
    }

    /**
     * update the order pay form based on new order object
     */
    function update_order_fees_ajax(){
        global $wp;
        check_ajax_referer( 'update-payment-method', 'security' );

        if(self::disableOrderPay()) return;

        $payment_method       = isset( $_POST['payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) : ''; 

        $order_id             = isset( $_POST['order_id'] ) ? sanitize_key( $_POST['order_id'] ): 0; 

        $payment_method_title = isset( $_POST['payment_method_title'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_method_title'] ) ) : '';

        if ( $order_id <= 0 ) {
            wp_die();
        }

        $order    = wc_get_order( $order_id );
        $add_fees = [];

        $this->remove_fees( $order );

        // Update payment method record in the database.
        $order->set_payment_method( $payment_method );
        $order->set_payment_method_title( $payment_method_title );
        $order->calculate_totals();
        $order->save();

        $fees = $this->getFees( $order );

        $this->add_gateways_fees( $order, $fees );
            
        // Declare $order again to fetch updates to post meta and serve to payment templte engine.
        $order = wc_get_order( $order_id );

        ob_start();
        $this->woocommerce_order_pay( $order );
        $woocommerce_order_pay = ob_get_clean();

        wp_send_json(
            array(
                'fragments' => $woocommerce_order_pay,
            )
        );

    }

     /**
     * Get the order pay page template as per new order object
     */
    public function woocommerce_order_pay( $order ) {
        $available_gateways = WC()->payment_gateways->get_available_payment_gateways();

        if ( count( $available_gateways ) ) {
            current( $available_gateways )->set_current();
        }
        wc_get_template(
            'checkout/form-pay.php',
            array(
                'order'              => $order,
                'available_gateways' => $available_gateways,
                'order_button_text'  => apply_filters( 'woocommerce_pay_order_button_text', __( 'Pay for order', 'woocommerce' ) ),
            )
        );
    }

    /**
     * Apply all the conditional fees in the order object
     */
    function add_gateways_fees( $order, $fees ){

        if(empty($fees) || !is_array($fees)) return;
        
        $count = 0;

        $fees = apply_filters('pisol_dpmw_fees_filter', $fees, $order);

        foreach($fees as $fee){
            $item_fee[$count] = new WC_Order_Item_Fee();

            $item_fee[$count]->set_name( $fee['name'] );
            $item_fee[$count]->set_amount( $fee['amount'] );
            $item_fee[$count]->set_total( $fee['amount'] );

            /**
             * this is needed as we use legacy_fee_key to store the fees in order meta in variable _legacy_fee_key
             */
            $item_fee[$count]->legacy_fee_key = $fee['id'];

            if($fee['taxable']){
                $item_fee[$count]->set_tax_status( 'taxable' );

                /**
                 * we need this check as standard rate will be missing from the regular tax rate list so it is not allowed to be applied in backend
                 */
                if(isset($fee['tax_class']) && in_array($fee['tax_class'], WC_Tax::get_tax_class_slugs())){
                    $item_fee[$count]->set_tax_class( $fee['tax_class'] );
                }else{
                    error_log($fee['tax_class'].' tax class iS not available in tax list');
                }
            }else{
                $item_fee[$count]->set_tax_status( 'none' );
            }

            // Add Fee item to the order.
            $order->add_item( $item_fee[$count] );
            $count++;
        }
        $order->calculate_totals();
        $order->save();        
    }

    /**
     * Remove all the fees added by the plugin so we can recheck the rule and apply then accordingly 
     */
    public function remove_fees( &$order ) {
        global $wpdb;

        foreach ( $order->get_items( 'fee' ) as $item_id => $item ) {
            $key = $item->get_meta('_legacy_fee_key', true);
            if(!empty($key) && strpos($key, 'pisol-dpmw-fees') !== false){
                $order->remove_item( $item_id );
            }
        }
    }

    function getFees( $cart ){
        $fees = $this->matchedFeesOld($cart);
        $fee_arg = [];
        foreach($fees as $fees){
            $title = $fees->post_title;
            $fees_id = $fees->ID;
            $fees_type = get_post_meta( $fees_id, 'pi_fees_type', true);
            $fees = get_post_meta( $fees_id, 'pi_fees', true);
           
            $total = $this->getSubTotalBasedOnObject( $cart );
            $taxable_val = get_post_meta( $fees_id, 'pi_fees_taxable', true);
            $tax_class = get_post_meta( $fees_id, 'pi_fees_tax_class', true);

            $taxable = $taxable_val === 'yes' ? true : false;

           
                if($fees_type == 'percentage'){
                    
                    $fees_value = $this->evaluate_cost($fees, $fees_id, $cart);

                    $fees_amount = $fees_value * $total  /100;
                
                }else{
                    $fees_amount = $this->evaluate_cost($fees, $fees_id, $cart);
                }

                $fees_amount = apply_filters('pi_dpmw_add_additional_charges',$fees_amount, $fees_id, $cart);
                
                if($fees_amount > 0 || apply_filters('pisol_dpmw_allow_discount', false, $fees_amount)){


                    $fees_amount = pisol_dpmw_multiCurrencyFilters($fees_amount);
                     /**
                     * without this advance way of adding fees with ID
                     * we cant remove wc coupon based on condition
                     * as we cant find which discount is applied
                     */
                    $fee_arg['pisol-dpmw-fees:'.$fees_id] = array(
                        'id' => 'pisol-dpmw-fees:'.$fees_id,
                        'name'=> $title,
                        'amount' => $fees_amount,
                        'taxable' =>  $taxable,
                        'tax_class' => $tax_class 
                    );

                    //$cart->fees_api()->add_fee( $fee_arg );
                }
        }
        return $fee_arg;
    }

    function getSubTotalBasedOnObject( $order ){
        return $order->get_subtotal();
    }

    /**
     * function taken from woocommerce / includes / shipping / flat_rate / class-wc-shipping-flat-rate.php
     * https://docs.woocommerce.com/document/flat-rate-shipping/
     * https://github.com/woocommerce/woocommerce/blob/9431b34f0dc3d1ed7b45807ffde75de4bb58f831/includes/shipping/flat-rate/class-wc-shipping-flat-rate.php
     */
	protected function evaluate_cost( $sum, $fees_id, $cart) {
	
        include_once WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php';

        // Allow 3rd parties to process shipping cost arguments.
        
        $locale         = localeconv();
        $decimals       = array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], ',' );

        $this->short_code_fees_id = $fees_id;
        $this->short_code_cart = $cart;

        

        $sum = do_shortcode( $sum );

        

        // Remove whitespace from string.
        $sum = preg_replace( '/\s+/', '', $sum );

        // Remove locale from string.
        $sum = str_replace( $decimals, '.', $sum );

        // Trim invalid start/end characters.
        $sum = rtrim( ltrim( $sum, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

        // Do the math.
        if($sum){
            try{
                $result = WC_Eval_Math::evaluate( $sum );
                return $result !== false ? $result : 0;
            }catch(Exception $e){
                return 0;
            }
        }
    }

    function matchedFeesOld( $package ){
        $matched_methods = array();
        $args         = array(
            'post_type'      => 'pi_dpmw_rules',
            'posts_per_page' => - 1
        );
        $all_methods        = get_posts( $args );
        foreach ( $all_methods as $method ) {

            $type = get_post_meta($method->ID, 'pi_rule_type', true);

            if($type != 'fees') continue;

            if(!pisol_dpmw_CurrencyValid($method->ID, $package)) continue;
           
            $is_match = $this->matchConditions( $method, $package );
           

            if ( $is_match === true ) {
                $matched_methods[] = $method;
            }
        }

        return $matched_methods;
    }

    public function matchConditions( $method, $package = array() ) {

        if ( empty( $method ) ) {
            return false;
        }

        if ( ! empty( $method ) ) {

            $user_payment_method = $this->getUserSelectedPaymentMethod();

            $payment_methods = get_post_meta($method->ID, 'disable_payment_methods', true);

            if(empty($user_payment_method) || empty($payment_methods) || !is_array($payment_methods) || !in_array($user_payment_method, $payment_methods) ) return false;

            $method_eval_obj = new Pisol_dpmw_method_evaluation( $method, $package );
            $final_condition_match = $method_eval_obj->finalResult();

            if ( $final_condition_match ) {
                return true;
            }
        }

        return false;
    }

    function getUserSelectedPaymentMethod(){
        return $_POST['payment_method'] ?? false;
    }
}
Pi_dpmw_Apply_order_fees::get_instance();