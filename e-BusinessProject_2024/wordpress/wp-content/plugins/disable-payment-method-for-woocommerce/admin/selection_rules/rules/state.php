<?php

class Pi_dpmw_selection_rule_state{
    function __construct($slug){
        $this->slug = $slug;
        $this->condition = 'state';
        /* this adds the condition in set of rules dropdown */
        add_filter("pi_".$this->slug."_condition", array($this, 'addRule'));
        
        /* this gives value field to store condition value either select or text box */
        add_action( 'wp_ajax_pi_'.$this->slug.'_value_field_'.$this->condition, array( $this, 'ajaxCall' ) );

        /* This gives our field with saved value */
        add_filter('pi_'.$this->slug.'_saved_values_'.$this->condition, array($this, 'savedDropdown'), 10, 3);

        /* This perform condition check */
        add_filter('pi_'.$this->slug.'_condition_check_'.$this->condition,array($this,'conditionCheck'),10,4);

        /* This gives out logic dropdown */
        add_action('pi_'.$this->slug.'_logic_'.$this->condition, array($this, 'logicDropdown'));

        /* This give saved logic dropdown */
        add_filter('pi_'.$this->slug.'_saved_logic_'.$this->condition, array($this, 'savedLogic'),10,3);
    }

    function addRule($rules){
        $rules[$this->condition] = array(
            'name'=>__('State'),
            'group'=>'location_related',
            'condition'=>$this->condition
        );
        return $rules;
    }

    function logicDropdown(){
        $html = "";
        $html .= 'var pi_logic_'.$this->condition.'= "<select class=\'form-control\' name=\'pi_selection[{count}][pi_'.$this->slug.'_logic]\'>';
        
            $html .= '<option value=\'equal_to\'>Equal to (=)</option>';
            $html .= '<option value=\'not_equal_to\'>Not Equal to (!=)</option>';
           
        
        $html .= '</select>";';
        echo $html;
    }

    function savedLogic($html_in, $saved_logic, $count){
        $html = "";
        $html .= '<select class="form-control" name="pi_selection['.$count.'][pi_'.$this->slug.'_logic]">';
        
            $html .= '<option value="equal_to" '.selected($saved_logic , "equal_to",false ).'>Equal to (=)</option>';
            $html .= '<option value="not_equal_to" '.selected($saved_logic , "not_equal_to",false ).'>Not Equal to (!=)</option>';
           
        
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
        echo Pi_dpmw_selection_rule_main::createSelect($this->allCountries(), $count, $this->condition,  "multiple",null,'static');
        die;
    }

    function savedDropdown($html, $values, $count){
        $html = Pi_dpmw_selection_rule_main::createSelect($this->allCountries(), $count, $this->condition,  "multiple", $values,'static');
        return $html;
    }

    function allCountries(){
        $countries_obj = new WC_Countries();
       $states_array =  $countries_obj->get_states();
        $states = $this->getStates($states_array);
       return $states;
    }

    function getStates($states_array){
        $final = array();
        foreach($states_array as $country_code => $states){
            $country_name = WC()->countries->countries[$country_code];
            foreach($states as $state_code => $state_name){
                $final[$country_code.":".$state_code] = $country_name.' &gt; '.$state_name;
            }
        }
        return $final;
    }

    function conditionCheck($result, $package, $logic, $values){
        
                    $or_result = false;
                    $user_state = $this->getUserState( $package );
                    $rule_states = $this->separateState($values);
                    if($logic == 'equal_to'){
                        if(in_array($user_state, $rule_states)){
                            $or_result = true;
                        }else{
                            $or_result = false;
                        }
                    }else{
                        if(in_array($user_state, $rule_states)){
                            $or_result = false;
                        }else{
                            $or_result = true;
                        }
                    }
               
        return  $or_result;
    }

    function getUserState( $package ){
        $state = '';
        if(is_a($package, 'WC_Cart')){
            $state = WC()->customer->get_shipping_state();
        }elseif(is_a($package, 'WC_Order')){
            $billing_state = $package->get_billing_state();
            $shipping_state = $package->get_shipping_state();
            if(empty($shipping_state)){
                $state = $billing_state;
            }else{
                $state = $shipping_state;
            }
        }
        return $state;
    }

    function separateState($countries_states){
        $value = array();
        if(is_array($countries_states)){
        foreach($countries_states as $countries_state){
            $array = explode(":",$countries_state);
            $value[] = $array[1];
        }
        }
        return $value;
    }
}

new Pi_dpmw_selection_rule_state(PI_DPMW_SELECTION_RULE_SLUG);