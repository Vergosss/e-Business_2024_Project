<?php

class Pi_dpmw_selection_rule_zones{
    function __construct($slug){
        $this->slug = $slug;
        $this->condition = 'zones';
        /* this adds the condition in set of rules dropdown */
        add_filter("pi_".$this->slug."_condition", array($this, 'addRule'));
        
        /* this gives value field to store condition value either select or text box */
        add_action( 'wp_ajax_pi_'.$this->slug.'_value_field_'.$this->condition, array( $this, 'ajaxCall' ) );

        /* This gives our field with saved value */
        add_filter('pi_'.$this->slug.'_saved_values_'.$this->condition, array($this, 'savedDropdown'), 10, 3);

        /* This perform condition check */
        add_filter('pi_'.$this->slug.'_condition_check_'.$this->condition, array($this,'conditionCheck'),10,4);

        /* This gives out logic dropdown */
        add_action('pi_'.$this->slug.'_logic_'.$this->condition, array($this, 'logicDropdown'));

        /* This give saved logic dropdown */
        add_filter('pi_'.$this->slug.'_saved_logic_'.$this->condition, array($this, 'savedLogic'),10,3);
    }

    function addRule($rules){
        $rules[$this->condition] = array(
            'name'=>__('Zones','disable-payment-method-for-woocommerce'),
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
        
            $html .= '<option value="equal_to" '.selected($saved_logic , "equal_to",false ).'>Equal to (=)</option>';
            $html .= '<option value="not_equal_to" '.selected($saved_logic , "not_equal_to",false ).'>Not Equal to (!=)</option>';
           
        
        $html .= '</select>';
        return $html;
    }

    function ajaxCall(){
        if(!current_user_can( 'manage_options' )) {
            return;
            die;
        }
        $count = sanitize_text_field(filter_input(INPUT_POST,'count'));
        echo Pi_dpmw_selection_rule_main::createSelect($this->allZones(), $count, $this->condition, "multiple", null,'static');
        die;
    }

    function savedDropdown($html, $values, $count){
        $html = Pi_dpmw_selection_rule_main::createSelect($this->allZones(), $count,$this->condition, "multiple", $values, 'static');
        return $html;
    }

    function allZones(){
       $zones =  WC_Shipping_Zones::get_zones();
       $all_zones = array();
       foreach ((array) $zones as $key => $zone ) {
        $all_zones[$key] = $zone['zone_name'];
      }
       $non_covered_zone =  WC_Shipping_Zones::get_zone_by("zone_id",0);
       if(is_object($non_covered_zone)){
            $all_zones[0] = $non_covered_zone->get_zone_name();
       }
       return $all_zones;
    }

    public static function getUserSelectedZone($package){

        if(is_a($package, 'WC_Cart')){
            $shipping_packages =  $package->get_shipping_packages();
        
            $shipping_zone = wc_get_shipping_zone( reset( $shipping_packages ) );

            if(is_object($shipping_zone)){
                return $shipping_zone->get_id();
            }
        }elseif(is_a($package, 'WC_Order')){
            return pi_dpmw_store_data_in_order::get_zone_id($package);
        }

        return null;
    }

    function conditionCheck($result, $package, $logic, $values){
        
                    $or_result = false;
                    $user_zone = self::getUserSelectedZone($package);
                    $rule_zone = $values;
                    if($logic == 'equal_to'){
                        if(in_array($user_zone, $rule_zone)){
                            $or_result = true;
                        }else{
                            $or_result = false;
                        }
                    }else{
                        if(in_array($user_zone, $rule_zone)){
                            $or_result = false;
                        }else{
                            $or_result = true;
                        }
                    }
               
        return  $or_result;
    }
}

new Pi_dpmw_selection_rule_zones(PI_DPMW_SELECTION_RULE_SLUG);