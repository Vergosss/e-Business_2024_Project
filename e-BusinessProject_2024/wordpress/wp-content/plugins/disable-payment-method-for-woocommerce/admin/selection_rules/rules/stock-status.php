<?php

class Pi_dpmw_selection_rule_stock_status{
    function __construct($slug){
        $this->slug = $slug;
        $this->condition = 'stock_status';
        /* this adds the condition in set of rules dropdown */
        add_filter("pi_".$this->slug."_condition", array($this, 'addRule'));
        
        /* this gives value field blank of populated */
        add_action( 'wp_ajax_pi_'.$this->slug.'_value_field_'.$this->condition, array( $this, 'ajaxCall' ) );


        add_filter('pi_'.$this->slug.'_saved_values_'.$this->condition, array($this, 'savedDropdown'), 10, 3);

        add_filter('pi_'.$this->slug.'_condition_check_'.$this->condition,array($this,'conditionCheck'),10,4);

        add_action('pi_'.$this->slug.'_logic_'.$this->condition, array($this, 'logicDropdown'));

        add_filter('pi_'.$this->slug.'_saved_logic_'.$this->condition, array($this, 'savedLogic'),10,3);
    }

    function addRule($rules){
        $rules[$this->condition] = array(
            'name'=>__('Stock status of product in cart'),
            'group'=>'cart_related',
            'condition'=>$this->condition
        );
        return $rules;
    }

    function logicDropdown(){
        $html = "";
        $html .= 'var pi_logic_'.$this->condition.'= "<select class=\'form-control\' name=\'pi_selection[{count}][pi_'.$this->slug.'_logic]\'>';
    
            $html .= '<option value=\'all-in-stock\'>All product are in stock</option>';
			$html .= '<option value=\'back-order-present\'>There is at least one product on back order</option>';
        
        $html .= '</select>";';
        echo $html;
    }

    function savedLogic($html_in, $saved_logic, $count){
        $html = "";
        $html .= '<select class="form-control" name="pi_selection['.$count.'][pi_'.$this->slug.'_logic]">';

            $html .= '<option value=\'all-in-stock\' '.selected($saved_logic , "all-in-stock",false ).'>All product are in stock</option>';
			$html .= '<option value=\'back-order-present\' '.selected($saved_logic , "back-order-present",false ).'>There is at least one product on back order</option>';
        
        $html .= '</select>';
        return $html;
    }

    function ajaxCall(){
        $cap = Pi_dpmw_Menu::getCapability();
        if(!current_user_can( $cap )) {
            return;
            die;
        }
        $count = sanitize_text_field(filter_input(INPUT_POST,'count'));
        echo '<span style="display:none !important">';
        echo Pi_dpmw_selection_rule_main::createNumberField($count,$this->condition, null,'any');
        echo '</span>';
        die;
    }

    function savedDropdown($html, $values, $count){
        $html = '<span style="display:none !important">';
        $html .= Pi_dpmw_selection_rule_main::createNumberField($count, $this->condition, $values,'any');
        $html .= '</span>';
        return $html;
    }

    function backOrderProductPresent($package){
        $back_order_present = false;
        if(is_a($package, 'WC_Cart')){
            if(function_exists('WC') && is_object(WC()->cart)){
                $products = WC()->cart->get_cart();
                foreach( $products as $product ){
                    $quantity = $product['quantity'];
                    if( $product['data']->is_on_backorder( $quantity ) ){
                        $back_order_present = true;
                    }
                }
            }
        }elseif(is_a($package, 'WC_Order')){
            $products = $package->get_items();
            foreach( $products as $product ){
                $product_obj = $product->get_product();
                $quantity = $product['quantity'];
                if( $product_obj->is_on_backorder( $quantity ) ){
                    $back_order_present = true;
                }
            }
        }

        return $back_order_present;
    }

    function conditionCheck($result, $package, $logic, $values){
        
                    $or_result = false;
                    $back_order_present = $this->backOrderProductPresent($package);
                    switch ($logic){
                        case 'all-in-stock':
                            if($back_order_present){
                                $or_result = false;
                            }else{
                                $or_result = true;
                            }
                        break;

                        case 'back-order-present':
                            if($back_order_present){
                                $or_result = true;
                            }else{
                                $or_result = false;
                            }
                        break;
                    }
               
        return  $or_result;
    }
}

new Pi_dpmw_selection_rule_stock_status(PI_DPMW_SELECTION_RULE_SLUG);