<?php

class Pi_dpmw_selection_rule_user{
    function __construct($slug){
        $this->slug = $slug;
        $this->condition = 'user';

        add_filter("pi_".$this->slug."_condition", array($this, 'addRule'));

        add_action( 'wp_ajax_pi_'.$this->slug.'_value_field_'.$this->condition, array( $this, 'ajaxCall' ) );

        add_filter('pi_'.$this->slug.'_saved_values_'.$this->condition, array($this, 'savedDropdown'), 10, 3);
        
        add_action( 'wp_ajax_pi_'.$this->slug.'_options_'.$this->condition, array( $this, 'search_user' ) );
        
        add_filter('pi_'.$this->slug.'_condition_check_'.$this->condition, array($this,'conditionCheck'),10,4);

        add_action('pi_'.$this->slug.'_logic_'.$this->condition, array($this, 'logicDropdown'));
        add_filter('pi_'.$this->slug.'_saved_logic_'.$this->condition, array($this, 'savedLogic'),10,3);
    }

    function addRule($rules){
        $rules[$this->condition] = array(
            'name'=>__('User'),
            'group' => "user_related",
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
        echo Pi_dpmw_selection_rule_main::createSelect(array(), $count,$this->condition,  "multiple", null,'dynamic');
        die;
    }

    function savedUsers($values){
        $saved_users = array();
        if(is_array($values)){
            foreach($values as $value){
                $user_obj = get_user_by("ID",$value);
                if(!is_object($user_obj)){
                    $saved_users[$value] = 'ID: '.$value;
                    continue;
                }
                $saved_users[$user_obj->ID] = $user_obj->display_name;
            }
        }
        
        return $saved_users;
    }

    function savedDropdown($html, $values, $count){
        $html = Pi_dpmw_selection_rule_main::createSelect($this->savedUsers($values), $count, $this->condition,  "multiple", $values,'dynamic');
        return $html;
    }

    public function search_user( $x = '', $post_types = array( 'user' ) ) {
        $cap = Pi_dpmw_Menu::getCapability();
		if ( ! current_user_can( $cap ) ) {
			return;
		}

        ob_start();
        
        if(!isset($_GET['keyword'])) die;

		$keyword = isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) : "";

		if ( empty( $keyword ) ) {
			die();
		}
		$arg            = array(
            'search'         => '*'.esc_attr( $keyword ).'*',
            'search_columns' => array(
                'user_login',
                'user_nicename',
                'user_email',
                'user_url',
            ),
        );
        $the_query      = new WP_User_Query( $arg );
        $fount_users = $the_query->get_results();
        
        $found_result = array();
        foreach($fount_users as $fount_user){
            $found_result[] = array(
                'id'=> $fount_user->ID,
                'text'=> $fount_user->display_name
            );
        }
		wp_send_json( $found_result );
		die;
    }

    function conditionCheck($result, $package, $logic, $values){
        
        $or_result = false;
        if(function_exists('wp_get_current_user')){
                    $user = wp_get_current_user();
                    $user_id[] = $user->ID;
                    if(is_array($values)){
                        $rule_users = $values;
                    }else{
                        $rule_users = array();
                    }
                    $intersect = array_intersect($rule_users, $user_id);
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

    function getProductsFromOrder($package){
        $products = $package['contents'];
        $user_products = array();
        foreach($products as $product){
            $product_obj = $product['data'];
            $user_products[] = $product_obj->get_id();
        }
        return $user_products;
    }

}

new Pi_dpmw_selection_rule_user(PI_DPMW_SELECTION_RULE_SLUG);