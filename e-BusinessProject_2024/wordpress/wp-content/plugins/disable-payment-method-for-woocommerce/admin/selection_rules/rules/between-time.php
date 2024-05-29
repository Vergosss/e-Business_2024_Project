<?php

class Pi_dpmw_selection_rule_between_time{
    
    public $slug;
    public $condition;

    function __construct($slug){
        $this->slug = $slug;
        $this->condition = 'between_time';
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
            'name'=>__('Between time range', 'disable-payment-method-for-woocommerce'),
            'group'=>'date_time',
            'condition'=>$this->condition
        );
        return $rules;
    }

    function logicDropdown(){
        $html = "";
        $html .= 'var pi_logic_'.$this->condition.'= "<select class=\'form-control\' name=\'pi_selection[{count}][pi_'.$this->slug.'_logic]\'>';
    
            $html .= '<option value=\'Between\'>Between start and end time</option>';
            $html .= '<option value=\'outside\'>Outside the range</option>';
			
        $html .= '</select>";';
        echo $html;
    }

    function savedLogic($html_in, $saved_logic, $count){
        $html = "";
        $html .= '<select class="form-control" name="pi_selection['.$count.'][pi_'.$this->slug.'_logic]">';

        $html .= '<option value=\'Between\' '.selected($saved_logic , "Between",false ).'>Between start and end time</option>';
        $html .= '<option value=\'outside\'  '.selected($saved_logic , "outside",false ).'>Outside the range</option>';
        
        $html .= '</select>';
        return $html;
    }

    function ajaxCall(){
        if(!current_user_can( 'manage_options' )) {
            return;
            die;
        }
        $count = sanitize_text_field(filter_input(INPUT_POST,'count'));
        $html_start = self::createTextField($count, null,'start_time', $this->condition);
        $html_end =  self::createTextField($count, null, 'end_time', $this->condition);
        echo self::bootstrapRow($html_start, $html_end);
        die;

    }

    static function bootstrapRow($left, $right){
        return sprintf('<div class="row"><div class="col-6">Start time <br>%s <a href="javascript:void(0)" class="pi-clear-time">Clear value</a></div><div class="col-6">End time<br>%s<a href="javascript:void(0)" class="pi-clear-time">Clear value</a></div></div>', $left, $right);
    }

    function savedDropdown($html, $values, $count){
        $html_start = self::createTextField($count, $values['start_time'],'start_time' ,$this->condition );
        $html_end = self::createTextField($count, $values['end_time'], 'end_time', $this->condition);
        return self::bootstrapRow($html_start,$html_end);
    }

    static function createTextField($count, $value, $name, $condition =""){

        if(!empty($value)){
            $value_attr = ' value="'.esc_attr($value).'" ';
        }else{
            $value_attr = "";
        }
        $html = '<input readonly type="text" class="form-control time-picker" data-condition="'.$condition.'" name="pi_selection['.$count.'][pi_'.PI_DPMW_SELECTION_RULE_SLUG.'_condition_value]['.$name.']" '.$value_attr.' >';
        return $html;
    }

   
    function conditionCheck($result, $package, $logic, $values){
        
        $or_result = false;

        $start_time = !empty($values['start_time']) ? $values['start_time'] : "";
        $end_time = !empty($values['end_time']) ? $values['end_time'] : "";

        if($logic == 'outside'){
            if(!self::is_in_between($start_time, $end_time)){
                $or_result = true;
            }
        }else{
            if(self::is_in_between($start_time, $end_time)){
                $or_result = true;
            }
        }

        
                               
        return  $or_result;
    }

    static function is_in_between($start_time, $end_time){
        $or_result = false;
        $current_time = current_time('h:i A');

        if(!empty($start_time) && !empty($end_time)){
            if(strtotime($current_time) >= strtotime($start_time) && strtotime($end_time) >= strtotime($current_time)){
                return true;
            }else{
                return false;
            }
        }elseif(empty($start_time) && empty($end_time)){
            return false;
        }elseif(!empty($start_time) && empty($end_time)){
            if(strtotime($current_time) >= strtotime($start_time)){
                return true;
            }else{
                return false;
            }
        }elseif(empty($start_time) && !empty($end_time)){
            if(strtotime($end_time) >= strtotime($current_time)){
                return true;
            }else{
                return false;
            }
        }

        return $or_result;

    }
}


new Pi_dpmw_selection_rule_between_time(PI_DPMW_SELECTION_RULE_SLUG);