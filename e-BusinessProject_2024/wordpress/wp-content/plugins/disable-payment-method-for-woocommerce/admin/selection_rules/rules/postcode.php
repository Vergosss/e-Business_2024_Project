<?php

class Pi_dpmw_selection_rule_postcode{
    function __construct($slug){
        $this->slug = $slug;
        $this->condition = 'postcode';
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
            'name'=>__('Postcode'),
            'group'=>'location_related',
            'condition'=>$this->condition
        );
        return $rules;
    }

    function logicDropdown(){
        $html = "";
        $html .= 'var pi_logic_'.$this->condition.'= "<select class=\'form-control\' name=\'pi_selection[{count}][pi_'.$this->slug.'_logic]\'>';
    
            $html .= '<option value=\'equal_to\'>Equal to ( = )</option>';
			$html .= '<option value=\'not_equal_to\'>Not Equal to ( != )</option>';
        
        $html .= '</select>";';
        echo wp_kses($html,
                array( 'select'=> array(
                        'name'=>array(), 
                        'class' => array()
                    )
                    ,
                    'option' => array(
                        'value' => array(),
                        'selected' => array()
                    )
                )
            );
    }

    function savedLogic($html_in, $saved_logic, $count){
        $html = "";
        $html .= '<select class="form-control" name="pi_selection['.$count.'][pi_'.$this->slug.'_logic]">';

            $html .= '<option value=\'equal_to\' '.selected($saved_logic , "equal_to",false ).'>Equal to ( = )</option>';
			$html .= '<option value=\'not_equal_to\' '.selected($saved_logic , "not_equal_to",false ).'>Not Equal to ( != )</option>';
        
        
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
        echo Pi_dpmw_selection_rule_main::createTextField($count, $this->condition, null);
        echo $this->description();
        die;
    }

    function savedDropdown($html, $values, $count){
        $html = Pi_dpmw_selection_rule_main::createTextField($count, $this->condition,  $values);
        $html .= $this->description();
        return $html;
    }

    function description(){
        $html = '';
        return $html;
    }


    function conditionCheck($result, $package, $logic, $values){
        
                    $or_result = false;
                    $cart_postcode = $this->getUserPostCode( $package );
                    $rules = $this->getPostCodes( $values[0] );
                    $post_code_matched = $this->postCodeMatched( $cart_postcode, $rules);
                    $rule_cart_postcode = $values[0];
                    switch ($logic){
                        case 'equal_to':
                            if($post_code_matched){
                                $or_result = true;
                            }
                        break;

                        case 'not_equal_to':
                            if($post_code_matched){
                                $or_result = false;
                            }else{
                                $or_result = true;
                            }
                        break;
                    }
               
        return  $or_result;
    }

    function getPostCodes( $text_value ){
        $post_codes = array();
        $values = explode(',', $text_value);
        $post_codes = array_map( 'trim', $values );
        return $post_codes;
    }

    function postCodeMatched( $post_code, $rules){
        
        foreach($rules as $rule){

            $object[] = (object)array(
                'zone_id'=> 1,
                'location_code'=> $rule
            );

            $country = apply_filters('pisol_dpmw_postcode_country', ( function_exists('WC') && is_object(WC()->customer) ? WC()->customer->get_shipping_country() : ''));
            /**
             * this is woocommerce location matcher function
             */
            $match = wc_postcode_location_matcher($post_code, $object, 'zone_id', 'location_code', $country);

            if(count($match) > 0 ) return true;
           
        }

        return false;
    }

    function getUserPostCode( $package ){
        
        $postcode = '';
        if(is_a($package, 'WC_Cart')){
            $postcode = WC()->customer->get_shipping_postcode();
        }elseif(is_a($package, 'WC_Order')){
            $billing_postcode = $package->get_billing_postcode();
            $shipping_postcode = $package->get_shipping_postcode();
            if(empty($shipping_postcode)){
                $postcode = $billing_postcode;
            }else{
                $postcode = $shipping_postcode;
            }
        }
        return $postcode;
    }
}

new Pi_dpmw_selection_rule_postcode(PI_DPMW_SELECTION_RULE_SLUG);