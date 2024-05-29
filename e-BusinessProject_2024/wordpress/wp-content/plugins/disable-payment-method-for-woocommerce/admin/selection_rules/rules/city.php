<?php

class Pi_dpmw_selection_rule_city{
    function __construct($slug){
        $this->slug = $slug;
        $this->condition = 'city';
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
            'name'=>__('City/Town'),
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
        echo $html;
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
        die;
    }

    function savedDropdown($html, $values, $count){
        $html = Pi_dpmw_selection_rule_main::createTextField($count, $this->condition,  $values);
        return $html;
    }

    function conditionCheck($result, $package, $logic, $values){
        
                    $or_result = false;
                    $cart_city = $this->get_user_city($package);
                    $rules = isset($values[0]) && !empty($values[0]) ? $values[0] : "";
                    if(empty($rules)) return $or_result;

                    $city_matched = $this->cityMatched( $cart_city, $rules);
                    
                    switch ($logic){
                        case 'equal_to':
                            if($city_matched){
                                $or_result = true;
                            }
                        break;

                        case 'not_equal_to':
                            if($city_matched){
                                $or_result = false;
                            }else{
                                $or_result = true;
                            }
                        break;
                    }
               
        return  $or_result;
    }

    function get_user_city($package){
        $state = '';
        if(is_a($package, 'WC_Cart')){
            $state = function_exists('WC') && is_object(WC()->customer) ? WC()->customer->get_shipping_city() : '';
        }elseif(is_a($package, 'WC_Order')){
            $billing_state = $package->get_billing_city();
            $shipping_state = $package->get_shipping_city();
            if(empty($shipping_state)){
                $state = $billing_state;
            }else{
                $state = $shipping_state;
            }
        }
        return $state;
    }

    function cityMatched( $cart_city, $rules_city){
        $cities_array = $this->getCities($rules_city);

        if(in_array(strtolower($cart_city), $cities_array)) return true;

        return false;
        
    }

    function getCities( $text_value ){
        $cities = array();
        $values = explode(',', $text_value);
        $cities = array_map( 'trim', $values );
        $cities = array_map( 'strtolower', $cities );
        return $cities;
    }
}

new Pi_dpmw_selection_rule_city(PI_DPMW_SELECTION_RULE_SLUG);