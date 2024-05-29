<?php
use PISOL\DPMW\Session;
class Pi_dpmw_partial_payment_ui{

    protected static $instance = null;

    public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    protected function __construct(){
        $this->count = 0;

         /**
         * show partial payment option on checkout page
         */
        add_action('woocommerce_before_template_part', [$this, 'showPartialPaymentOption'], 10, 1);

        /**
         * This sets the main total of the checkout
         */
        add_filter( 'woocommerce_calculated_total', [$this, 'recalculate_price'], PHP_INT_MAX - 10, 2 );

        add_filter( 'woocommerce_cart_get_total', [$this, 'orderTotalForFrontEnd'], PHP_INT_MAX );

        add_filter( 'woocommerce_cart_totals_order_total_html', [$this, 'cart_total_html'], PHP_INT_MAX, 1 );

        /**
         * this filter should be applied only on the order detail page on admin side, if we applied it globally then it affect some payment gateway like stripe 
         */
        add_filter( 'woocommerce_order_get_total', [$this, 'orderTotalForBackEnd'], 10, 2 );
        
        /**
         * this changes order total at all places except order detail page 
         */
        add_filter( 'woocommerce_get_formatted_order_total', [$this, 'orderTotal'], 10, 2);

         /**
         * show to pay and balance amt data in checkout page when user opt for partial payment
         */
        add_action( 'woocommerce_review_order_after_order_total', [$this, 'partialPaymentDetailInCheckoutTotal'] );

         /**
         * We change the available gateway when partial payment is available and when partial payment is selected
         */
        add_filter('woocommerce_available_payment_gateways', array($this,'filterGateways'), 1);

        /**
         * show remaining payment details in parent order on Thank yor page
         */
        add_action( 'woocommerce_after_order_details', [$this, 'pending_payment_order_detail'], 20, 1 );


        /**
         * redirect to parent order thank your page after completion of payment of partial order
         */
        add_action( 'template_redirect', [ $this, 'redirectToParentOrder' ] );

        /**
         * using this we show paid amt and balance amt detail in Thank your page, order email
         */
        add_filter('woocommerce_get_order_item_totals', [$this, 'addingAdditionalTotalDetail'],10,2);

        /**
         * display deposit detail in backend order detail page
         */
        add_action( 'woocommerce_admin_order_totals_after_tax', [$this, 'deposit_data_display_table_tr'], 20, 1 );

        /**
         * when deposit is paid but remaining is not paid then for the main order payment page we should redirect to remaining order payment page
         * For saferside we have added a redirection as well in template_redirect hook
         */
        add_action('woocommerce_get_checkout_payment_url', [$this, 'checkout_url'], 60, 2);

        add_filter('woocommerce_my_account_my_orders_actions', [$this, 'hide_pay_link'],10,2);

    }

    function partialPaymentRuleAvailable(){
        $enabled = get_option('pi_dpmw_enable_partial_payment', 0);
        $amt = get_option('pi_dpmw_partial_amt', 0);

        $return = empty($enabled) || empty($amt) || !is_numeric($amt) ? false : true;

        return apply_filters('pi_dpmw_enable_partial_payment', $return);
    }

    function is_total_less_then_deposit_amt(){
        $amt = get_option('pi_dpmw_partial_amt', 0);
        $type = get_option('pi_dpmw_partial_amt_calculation', 'fixed');

        if($type != 'fixed') return false;

        if(Session::partialPaymentSelected()){
            $total = Session::getOriginalTotal();
        }else{
            $total = WC()->cart->get_total('f');
        }

        if($amt >= $total) return true;

        return false;
    }

    function showPartialPaymentOption($template_name){

        if($template_name != 'checkout/payment-method.php' || $this->count > 0) return;

        /**
         * For time being we will not allow partial payment on order pay page
         * in future will allow on order pay page but disable on the deposit pay orders 
         */
        if ( is_wc_endpoint_url( 'order-pay' )  ) return;

        $this->count++;

        $rule = $this->partialPaymentRuleAvailable();

        if(empty($rule)) return;

        if($this->is_total_less_then_deposit_amt(  )) return;

        $fees_selected = Session::partialPaymentSelectedInSession();

        $checked = '';

        if($fees_selected){
            $checked = ' checked="checked" ';
        }

        $title = get_option('pi_dpmw_partial_payment_title_checkout', 'Partial payment for the order');

        echo '<label class="pi-cod-deposit-container"><input type="checkbox" name="pi-cod-deposit" class="pi-cod-deposit" value="'.esc_attr($rule).'" '.$checked.'> <span class="pi-checkmark"></span>'.esc_html($title).'</label>';
    }

    /**
     * in future we will have this value coming from within the rule
     */
    function filterGateways($gateways){

        if(!function_exists('WC') || !isset(WC()->cart) || !is_object(WC()->cart)) return $gateways;

        $rule = $this->partialPaymentRuleAvailable();

        if($rule === false) return $gateways;

        $active_to_remove = get_option('pi_dpmw_remove_payment_methods', array());

        if(is_array($active_to_remove)){
            foreach($active_to_remove as $key){
                unset($gateways[$key]);
            }
        }

        if(Session::partialPaymentSelected()){
            $selected_to_remove = get_option('pi_dpmw_remove_payment_methods_selected', array('cod','bacs', 'cheque'));
            if(is_array($selected_to_remove)){
                foreach($selected_to_remove as $key){
                    unset($gateways[$key]);
                }
            }
        }

        return $gateways;
    }

    function partialPaymentDetailInCheckoutTotal(){


        if($this->partialPaymentRuleAvailable() === false) return;

        if(!Session::partialPaymentSelected()) return;

        $amt_to_pay  = Session::getPartialAmtToPay();
        $amt_balance  = Session::getBalanceAmountToPay();

        if($amt_to_pay <= 0) return;
        
        ?>
        <tr class="amount-to-pay">
            <th> <?php echo esc_html( get_option( 'pi_dpmw_txt_to_pay', 'To Pay' ) ); ?></th>
            <td data-title="<?php echo esc_attr( get_option( 'pi_dpmw_txt_to_pay', 'To Pay' ) ); ?>"><?php echo wc_price($amt_to_pay); ?></td>
        </tr>
        <tr class="order-due-payment">
            <th><?php echo esc_html( get_option( 'pi_dpmw_balance_to_pay', 'Due Payment' ) ); ?></th>
            <td data-title="<?php echo esc_attr( get_option( 'pi_dpmw_balance_to_pay', 'Due Payment' ) ); ?>">
                <?php echo wc_price($amt_balance);?>
            </td>
        </tr>
        <?php
    }

    function savePartialAmountInSession($cart, $total, $partial_payment_rule){

        if($partial_payment_rule  === false) return;

        $advance_amount = self::getAdvanceAmountToPay( $partial_payment_rule, $total );
        
        if(Session::partialPaymentSelected() && (!empty($advance_amount) && $advance_amount > 0)){
            $balance_amt = self::getBalanceAmount( $partial_payment_rule, $total, $advance_amount);

            Session::set_all_amt($total, $advance_amount, $balance_amt);

            return $advance_amount;
        }else{
            Session::unset_all_amt();
        } 

    }

    static function getAdvanceAmountToPay( $partial_payment_rule, $total = false ){

        if($total === false){
            $total = WC()->cart->get_total('f');
        }

        $cal_type =  get_option('pi_dpmw_partial_amt_calculation', 'fixed');
        $dep_amt = get_option('pi_dpmw_partial_amt', 0);
        $advance_amount = 0;
        if($cal_type == 'fixed' && !empty($dep_amt)){
            if($dep_amt < $total){
                $advance_amount = $dep_amt;
            }else{
                $advance_amount = $total;
            }
        }elseif(!empty($dep_amt)){
            $advance_amount = $total * $dep_amt / 100;
        }

        return $advance_amount;
    }

    /**
     * Fees_amt adjustment is given so we can calculate exact adv amt even when option is not selected for our description part
     */
    static function getBalanceAmount( $partial_payment_rule, $total, $advance_amount = false ){
        if($advance_amount === false){
            $advance_amount = self::getAdvanceAmountToPay( $partial_payment_rule, $total );
        }

        $balance_amt = $total - $advance_amount;

        return $balance_amt;
    }

    public function pending_payment_order_detail( $order ) {

        if ( empty( $order->get_meta( '_generated_deposit_amt_order', true ) ) ) {
            return; // hide summary for non deposit orders
        }
        wc_get_template( 'order/pending-payment-summary.php', array( 'order' => $order ), '', DISABLE_PAYMENT_METHOD_FOR_WOOCOMMERCE_DOCUMENTATION_PATH );
    }

    function redirectToParentOrder() {
		if( is_wc_endpoint_url( 'order-received' ) ) {
            global $wp;
			$order_id = $wp->query_vars['order-received'];
            $order = wc_get_order($order_id);
            if(empty($order)) return;
            $type = $order->get_type();
            if($type == 'pi_pending_amt'){
                $parent_order_id = $order->get_parent_id();
                $parent_order = wc_get_order( $parent_order_id );
                $url = wc_get_endpoint_url( 'order-received', $parent_order_id, wc_get_checkout_url() );
                $order_received_url = add_query_arg( 'key', $parent_order->get_order_key(), $url );
                wp_safe_redirect( $order_received_url );
            }
		}

        
	}

    function addingAdditionalTotalDetail($total_rows, $order){
        $order_id = $order->get_id();
        $balance_amount = $order->get_meta('_pi_balance_amount', true);
        $advance_amount = $order->get_meta('_pi_advance_amount', true);

        if(!empty($balance_amount) && !empty($advance_amount)){

            $paid_amt_title = get_option('pi_dpmw_paid_amt', 'Paid amount');
            $balance_amt_title = get_option('pi_dpmw_balance_amt', 'Balance amount');
            
            $formatted_adv_amt = wc_price( $advance_amount, array( 'currency' => $order->get_currency() ) );
            $total_rows['paid_amt'] = ['label' => $paid_amt_title, 'value' => $formatted_adv_amt];

            $formatted_balance = wc_price( $balance_amount , array( 'currency' => $order->get_currency() ) );
            
            $total_rows['balance_amt'] = ['label' => $balance_amt_title, 'value' => $formatted_balance];
        }

        return $total_rows;
    }

    function deposit_data_display_table_tr( $order_id ){
        $order = wc_get_order($order_id);
        $deposit_order_id = $order->get_meta('_generated_deposit_amt_order', true);
        $balance_order_id = $order->get_meta( '_generated_balance_amt_order', true);
        

        if(!empty($deposit_order_id) && !empty($balance_order_id)){

            $deposit_order = wc_get_order( $deposit_order_id );
            $balance_order = wc_get_order( $balance_order_id  );

            $formatted_advance_amount = wc_price( $deposit_order->get_total(), array( 'currency' => $order->get_currency() ) );

            $formatted_balance = wc_price( $balance_order->get_total() , array( 'currency' => $order->get_currency() ) );
        ?>
        <tr>
			<td class="label" style="color:#2f982f;"><?php esc_html_e( 'Deposit paid', 'disable-payment-method-for-woocommerce');?>:</td>

			<td width="1%"></td>
			<td class="total"  style="color:#2f982f;">
				<?php echo $formatted_advance_amount; ?>
			</td>
        </tr>
        <tr>
			<td class="label"  style="color:#f00;"><?php esc_html_e( 'Due Amount', 'disable-payment-method-for-woocommerce' );?>:</td>
			<td width="1%"></td>
			<td class="total" style="color:#f00;">
				<?php echo $formatted_balance; ?>
			</td>
		</tr>
        <?php
        }
    }

    function recalculate_price($total, $cart){
		$fees_selected = Session::partialPaymentSelected();

        if(empty($fees_selected)) return $total;

		$partial_payment_rule = $this->partialPaymentRuleAvailable();

        if($partial_payment_rule  === false) return $total;

        $this->savePartialAmountInSession($cart, $total, $partial_payment_rule);
        
        $advance_amount = self::getAdvanceAmountToPay( $partial_payment_rule, $total );
		return $advance_amount;
	}

    public function cart_total_html( $cart_total ) {
        $fees_selected = Session::partialPaymentSelected();

        if(empty($fees_selected)) return $cart_total;

        $total = Session::getOriginalTotal();

        return '<strong>'.wc_price( $total ).'</strong>';
    }

    public function orderTotal( $formatted_total, $order){
        $original_total = $order->get_meta('_pi_original_total', true);

        if(!empty($original_total)) return wc_price( $original_total, array( 'currency' => $order->get_currency() ) );;

        return  $formatted_total;
    }

    public function orderTotalForBackEnd( $total, $order){
        if(function_exists('get_current_screen')){
            $screen = get_current_screen();
            if(empty($screen)) return $total;

            if(isset($screen->id) && ($screen->id == 'shop_order' || 'woocommerce_page_wc-orders' == $screen->id)){
                $original_total = $order->get_meta('_pi_original_total', true);
            
                if(!empty($original_total)) return $original_total;
            }
        }

        return  $total;
    }

    public function orderTotalForFrontEnd( $total ){
        if(Session::partialPaymentSelected()){
            return Session::getPartialAmtToPay();
        }

        return  $total;
    }

    function parentOrderPaymentUrl($order){
            
        $deposit_order_id = $order->get_meta('_generated_deposit_amt_order', true);
        $balance_order_id = $order->get_meta( '_generated_balance_amt_order', true);

        if(empty($deposit_order_id) && empty($balance_order_id)) return '';

        if(Pi_dpmw_partial_payment::is_payment_done( $deposit_order_id ) && !Pi_dpmw_partial_payment::is_payment_done( $balance_order_id )){
            $balance_order = wc_get_order( $balance_order_id );
            $pay_url = wc_get_endpoint_url( 'order-pay', $balance_order_id, wc_get_checkout_url() );
            $pay_url = add_query_arg(
                array(
                    'pay_for_order' => 'true',
                    'key'           => $balance_order->get_order_key(),
                ),
                $pay_url
            );
            return $pay_url;
        }

        if(!Pi_dpmw_partial_payment::is_payment_done( $deposit_order_id ) && Pi_dpmw_partial_payment::is_payment_done( $balance_order_id )){
            $deposit_order = wc_get_order( $deposit_order_id );
            $pay_url = wc_get_endpoint_url( 'order-pay', $deposit_order_id, wc_get_checkout_url() );
            $pay_url = add_query_arg(
                array(
                    'pay_for_order' => 'true',
                    'key'           => $deposit_order->get_order_key(),
                ),
                $pay_url
            );
            return $pay_url;
        }

        if(Pi_dpmw_partial_payment::is_payment_done( $deposit_order_id ) && Pi_dpmw_partial_payment::is_payment_done( $balance_order_id )){
            return '';
        }
        

        return '';
    }

    function checkout_url($url, $order){
        if(Pi_dpmw_partial_payment::isDepositOrder( $order)){
            $new_url = $this->parentOrderPaymentUrl($order);
            if(!empty($new_url)){
                return $new_url;
            }
        }

        return $url;
    }

    function hide_pay_link($actions, $order){
        if(Pi_dpmw_partial_payment::isDepositOrder( $order)){
           if(isset($actions['pay']['url']) && empty($actions['pay']['url'])){
               unset($actions['pay']);
           }
        }
        return $actions;
    }
}

Pi_dpmw_partial_payment_ui::get_instance();