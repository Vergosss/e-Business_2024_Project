<?php
class Pi_dpmw_Apply_fees{
    function __construct(){
        add_action('woocommerce_cart_calculate_fees' , array($this,'addfees'));

        add_action( 'woocommerce_new_order_item', [$this, 'add_fee_id_to_order_meta'], 10, 3 );
    }

    function addfees($cart){
        $fees = $this->matchedShippingMethodsOld($cart);

        foreach($fees as $fees){
            $title = $fees->post_title;
            $fees_id = $fees->ID;
            $fees_type = get_post_meta( $fees_id, 'pi_fees_type', true);
            $fees = get_post_meta( $fees_id, 'pi_fees', true);
           
            $total = pisol_dpmw_revertToBaseCurrency($cart->get_displayed_subtotal());
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
                    $fee_arg = array(
                        'id' => 'pisol-dpmw-fees:'.$fees_id,
                        'name'=> $title,
                        'amount' => $fees_amount,
                        'taxable' =>  $taxable,
                        'tax_class' => $tax_class 
                    );

                    $cart->fees_api()->add_fee( $fee_arg );
                }
        }
       
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


    function matchedShippingMethodsOld( $package ){
        $matched_methods = array();
        $args         = array(
            'post_type'      => 'pi_dpmw_rules',
            'posts_per_page' => - 1
        );
        $all_methods        = get_posts( $args );
        foreach ( $all_methods as $method ) {

            $type = get_post_meta($method->ID, 'pi_rule_type', true);

            if($type != 'fees') continue;

            if(!pisol_dpmw_CurrencyValid($method->ID)) continue;
           
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

        if(function_exists('WC') && isset(WC()->session) && is_object(WC()->session)) {
            
            $chosen_payment_method = WC()->session->get('chosen_payment_method');

            if(!empty($chosen_payment_method)){
                return $chosen_payment_method;
            }
        }
        
        if(!isset($_POST['post_data']) && !isset($_POST['payment_method'])) return false;
        
        if(isset($_POST['payment_method'])){
            $values['payment_method'] = $_POST['payment_method'];
        }else{
            parse_str($_POST['post_data'], $values);
        }
        
        if(!empty($values['payment_method'])){
            $selected_method = $values['payment_method'];
            return $selected_method;
        }
        return false;
    }
    
    function add_fee_id_to_order_meta($item_id, $item, $order_id){
        global $wpdb;
        $table = $wpdb->prefix.'woocommerce_order_itemmeta';

        if( method_exists($item, 'get_type') && $item->get_type() == 'fee'){

            if(isset($item->legacy_fee_key) && !empty($item->legacy_fee_key)){
                
                $data = [
                    'order_item_id' => $item_id,
                    'meta_key' => '_legacy_fee_key',
                    'meta_value' => $item->legacy_fee_key
                ];
                $wpdb->insert($table, $data);
            }

            $data2 = [
                'order_item_id' => $item_id,
                'meta_key' => '_fee_order_id',
                'meta_value' => $order_id
            ];
            $wpdb->insert($table, $data2);
        }

       
    }
}

new Pi_dpmw_Apply_fees();