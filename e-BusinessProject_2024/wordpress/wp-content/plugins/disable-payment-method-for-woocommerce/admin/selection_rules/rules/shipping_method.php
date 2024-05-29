<?php

class Pi_dpmw_selection_rule_shipping_method{
    function __construct($slug){
        $this->slug = $slug;
        $this->condition = 'shipping_method';
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
            'name'=>__('Shipping method'),
            'group'=>'delivery_method',
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
                    ),
                    'optgroup' => array(
                        'label' => array()
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
        if(!current_user_can( 'manage_options' )) {
            return;
            die;
        }
        $count = sanitize_text_field(filter_input(INPUT_POST,'count'));
        echo Pi_dpmw_selection_rule_main::createTextField($count, $this->condition, null);
        echo 'Watch video on how to find shipping method system name'; 
        pisol_help::youtube('jPmY_Nltuxg', 'Shipping method name', 560, 315, true); 
        die;
    }

    function savedDropdown($html, $values, $count){
        $html = Pi_dpmw_selection_rule_main::createTextField($count, $this->condition,  $values);
        $html .= 'Watch video on how to find shipping method system name'; 
        $html .= pisol_help::youtube('jPmY_Nltuxg', 'Shipping method name', 560, 315, false); 
        return $html;
    }

    function userSelectedShippingMethod($package){

        if(is_a($package, 'WC_Cart')){
            if(!function_exists('WC') || !is_object(WC()->session)) return array();

            $chosen_method = WC()->session->get( 'chosen_shipping_methods' );
            return isset($chosen_method[0]) ? $chosen_method[0]: "";
        }elseif(is_a($package, 'WC_Order')){
            $chosen_method = pi_dpmw_store_data_in_order::get_shipping_methods($package);
            return isset($chosen_method[0]) ? $chosen_method[0]: "";
        }
    }

    function conditionCheck($result, $package, $logic, $values){
        
                    $or_result = false;
                    $chosen_method = $this->userSelectedShippingMethod($package);
                    $rule_methods = self::stringToArrayConvertor($values[0]);
                    switch ($logic){
                        case 'equal_to':
                            if(in_array($chosen_method, $rule_methods)){
                                $or_result = true;
                            }
                        break;

                        case 'not_equal_to':
                            if(!in_array($chosen_method, $rule_methods)){
                                $or_result = true;
                            }
                        break;
                    }
               
        return  $or_result;
    }

    static function stringToArrayConvertor($value){
        if(empty($value)) return array();

        $array = array_map('trim', explode(',', $value));

        return $array;
    }
}

new Pi_dpmw_selection_rule_shipping_method(PI_DPMW_SELECTION_RULE_SLUG);