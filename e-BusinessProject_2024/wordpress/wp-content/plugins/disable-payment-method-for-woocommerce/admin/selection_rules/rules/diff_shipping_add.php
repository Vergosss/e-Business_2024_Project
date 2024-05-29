<?php

class Pi_dpmw_selection_rule_diff_shipping_add{
    function __construct($slug){
        $this->slug = $slug;
        $this->condition = 'diff_shipping_add';
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

    static function datePluginInstalled(){
        if(is_plugin_active( 'pi-woocommerce-order-date-time-and-type-pro/pi-woocommerce-order-date-time-and-type-pro.php') ) return true;

        return false;
    }

    function addRule($rules){
        $rules[$this->condition] = array(
            'name'=>__('Different shipping address', 'conditional-fees-rule-woocommerce'),
            'group'=>'location_related',
            'condition'=>$this->condition
        );
        return $rules;
    }

    function logicDropdown(){
        $html = "";
        $html .= 'var pi_logic_'.$this->condition.'= "<select class=\'form-control\' name=\'pi_selection[{count}][pi_'.$this->slug.'_logic]\'>';
        
            $html .= '<option value=\'equal_to\'>Want a different shipping address</option>';
            $html .= '<option value=\'not_equal_to\'>Dont Want a different shipping address</option>';
           
        
        $html .= '</select>";';
        echo $html;
    }

    function savedLogic($html_in, $saved_logic, $count){
        $html = "";
        $html .= '<select class="form-control" name="pi_selection['.$count.'][pi_'.$this->slug.'_logic]">';
        
            $html .= '<option value="equal_to" '.selected($saved_logic , "equal_to",false ).'>Want a different shipping address</option>';
            $html .= '<option value="not_equal_to" '.selected($saved_logic , "not_equal_to",false ).'>Done Want a different shipping address</option>';
           
        
        $html .= '</select>';
        return $html;
    }

    function ajaxCall(){
        if(!current_user_can( 'manage_options' )) {
            return;
            die;
        }
        $count = filter_input(INPUT_POST,'count',FILTER_VALIDATE_INT);
        echo Pi_dpmw_selection_rule_main::createHiddenField($count, $this->condition, 1);
        
        die;
    }

    function savedDropdown($html, $values, $count){
        $html = Pi_dpmw_selection_rule_main::createHiddenField($count, $this->condition, 1);
        return $html;

    }

    function getShippingAddPreference($package){
        if(is_a($package, 'WC_Cart')){
            if(!isset($_POST['post_data']) && !isset($_POST['ship_to_different_address'])) return 0;

            if(isset($_POST['ship_to_different_address'])){
                $values['ship_to_different_address'] = $_POST['ship_to_different_address'];
            }else{
                parse_str($_POST['post_data'], $values);
            }

            if(!empty($values['ship_to_different_address'])){
                return $values['ship_to_different_address'];
            }
        }elseif(is_a($package, 'WC_Order')){
            return pi_dpmw_store_data_in_order::is_ship_to_different_address($package);
        }
        
        return 0;
    }

    function conditionCheck($result, $package, $logic, $values){
                    
                    $or_result = false;
                    
                        $add_preference = $this->getShippingAddPreference($package);
                        
                        $rule_delivery_type = $values;
                        if($logic == 'equal_to'){
                            if($add_preference == 1){
                                $or_result = true;
                            }else{
                                $or_result = false;
                            }
                        }else{
                            if($add_preference == 0){
                                $or_result = true;
                            }else{
                                $or_result = false;
                            }
                        }
                    
               
        return  $or_result;
    }
}

new Pi_dpmw_selection_rule_diff_shipping_add(PI_DPMW_SELECTION_RULE_SLUG);
