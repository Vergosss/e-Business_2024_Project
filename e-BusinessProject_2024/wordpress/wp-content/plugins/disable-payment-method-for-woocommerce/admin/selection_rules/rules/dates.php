<?php

class Pi_dpmw_selection_rule_dates{
    function __construct($slug){
        $this->slug = $slug;
        $this->condition = 'dates';
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
            'name'=>__('On Selected dates', 'disable-payment-method-for-woocommerce'),
            'group'=>'date_time',
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
        $count = filter_input(INPUT_POST,'count',FILTER_VALIDATE_INT);
        echo self::createTextField($count, null,'dates', $this->condition);
        die;
    }

    function savedDropdown($html, $values, $count){
        $html = self::createTextField($count, $values['dates'],'dates' ,$this->condition );
        return $html;
    }

    static function createTextField($count, $value, $name, $condition =""){

        if(!empty($value)){
            $value_attr = ' value="'.esc_attr($value).'" ';
        }else{
            $value_attr = "";
        }
        $html = '<input readonly type="text" class="form-control multi-date-picker" data-condition="'.$condition.'" name="pi_selection['.$count.'][pi_'.PI_DPMW_SELECTION_RULE_SLUG.'_condition_value]['.$name.']" '.$value_attr.' >';
        return $html;
    }

    function conditionCheck($result, $package, $logic, $values){
        
                    $or_result = false;
                    
                    $dates = $this->getDates($values['dates']);

                    $today_date = current_time('Y/m/d');
                    
                    switch ($logic){
                        case 'equal_to':
                            if(in_array($today_date, $dates)){
                                $or_result = true;
                            }
                        break;

                        case 'not_equal_to':
                            if(!in_array($today_date, $dates)){
                                $or_result = true;
                            }
                        break;
                    }
               
        return  $or_result;
    }

    function getDates( $text_value ){
        $dates = array();
        $values = explode(',', $text_value);
        $dates = array_map( 'trim', $values );
        $dates = array_map( 'strtolower', $dates );
        return $dates;
    }
}

new Pi_dpmw_selection_rule_dates(PI_DPMW_SELECTION_RULE_SLUG);