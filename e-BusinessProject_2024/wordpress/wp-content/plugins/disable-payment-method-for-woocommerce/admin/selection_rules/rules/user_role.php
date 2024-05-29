<?php

class Pi_dpmw_selection_rule_user_role{
    function __construct($slug){
        $this->slug = $slug;
        $this->condition = 'user_role';
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
            'name'=>__('User Role'),
            'group'=>'user_related',
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
        echo Pi_dpmw_selection_rule_main::createSelect($this->allUserRoles(), $count, $this->condition,  "multiple",null,'static');
        die;
    }

    function savedDropdown($html, $values, $count){
        $html = Pi_dpmw_selection_rule_main::createSelect($this->allUserRoles(), $count, $this->condition,  "multiple", $values,'static');
        return $html;
    }

    function allUserRoles(){
        $wp_roles = new WP_Roles();
        $roles = array();
       foreach($wp_roles->roles as $key => $role){
        $roles[$key] = $role['name'];
       }
       $roles['guest'] = 'Guest Customer';
       return $roles;
    }

    function conditionCheck($result, $package, $logic, $values){
        
        $or_result = false;
        if(function_exists('wp_get_current_user')){
                    $user = wp_get_current_user();
                    $user_meta = get_userdata($user->ID);
                    if($user_meta){
                        $user_roles = $user_meta->roles;
                    }else{
                        $user_roles = array('guest');
                    }
                        
                        $rule_roles = $values;
                        $intersect = array_intersect($rule_roles, $user_roles);
                        if($logic == 'equal_to'){
                            if(count($intersect) > 0){
                                $or_result = true;
                            }else{
                                $or_result = false;
                            }
                        }else{
                            if(count($intersect) == 0){
                                $or_result = true;
                            }else{
                                $or_result = false;
                            }
                        }
                    
        }
        return  $or_result;
    }
}

new Pi_dpmw_selection_rule_user_role(PI_DPMW_SELECTION_RULE_SLUG);