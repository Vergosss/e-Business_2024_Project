<?php

class Pi_dpmw_selection_rule_day_of_week{
    function __construct($slug){
        $this->slug = $slug;
        $this->condition = 'day_of_week';
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
            'name'=>__('Day of week'),
            'group'=>'date_time',
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
        echo Pi_dpmw_selection_rule_main::createSelect($this->daysOfWeek(), $count, $this->condition,  "multiple",null,'static');
        die;
    }

    function savedDropdown($html, $values, $count){
        $html = Pi_dpmw_selection_rule_main::createSelect($this->daysOfWeek(), $count,$this->condition,  "multiple", $values,'static');
        return $html;
    }

    function daysOfWeek(){
       $days = array(
            0 => __('Sunday'),
            1 => __('Monday'),
            2 => __('Tuesday'),
            3 => __('Wednesday'),
            4 => __('Thursday'),
            5 => __('Friday'),
            6 => __('Saturday'),
       );
       return $days;
    }

    function conditionCheck($result, $package, $logic, $values){
        
                    $or_result = false;
                    $current_day = current_time('w');
                    
                    $rule_days = $values;
                    
                    if($logic == 'equal_to'){
                        if(in_array($current_day, $rule_days)){
                            $or_result = true;
                        }else{
                            $or_result = false;
                        }
                    }else{
                        if(!in_array($current_day, $rule_days)){
                            $or_result = true;
                        }else{
                            $or_result = false;
                        }
                    }
               
        return  $or_result;
    }

    function getUserAddedClasses(){
        $products = WC()->cart->get_cart();
        $user_classes = array();
        foreach($products as $product){
            $product_obj = $product['data'];
            $class = $product_obj->get_shipping_class_id();
            if( !empty($class) ){ 
                $user_classes[] = $product_obj->get_shipping_class_id();
            }
        }
        return $user_classes;
    }
}


new Pi_dpmw_selection_rule_day_of_week(PI_DPMW_SELECTION_RULE_SLUG);