<?php

class Pi_dpmw_selection_rule_coupon{
    function __construct($slug){
        $this->slug = $slug;
        $this->condition = 'coupon';
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
            'name'=>__('Coupon'),
            'group'=>'cart_related',
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
        echo Pi_dpmw_selection_rule_main::createSelect($this->allCoupons(),$count, $this->condition,  "multiple",null,'static');
        die;
    }

    function savedDropdown($html, $values, $count){
        $html = Pi_dpmw_selection_rule_main::createSelect($this->allCoupons(), $count, $this->condition,  "multiple", $values,'static');
        return $html;
    }

    function allCoupons(){
        $args = array(
            'posts_per_page'   => -1,
            'orderby'          => 'title',
            'order'            => 'asc',
            'post_type'        => 'shop_coupon',
            'post_status'      => 'publish',
        );
            
       $coupons = get_posts( $args );
       $all_coupons = array();
       foreach( $coupons as $coupon ){
           $all_coupons[$coupon->ID] = $coupon->post_title;
       }
       return $all_coupons;
    }

    function conditionCheck($result, $package, $logic, $values){
        
                    $or_result = false;
                    $user_coupons = $this->getUserAddedCouponsID( $package );
                    $rule_coupons = $values;
                    $intersect = array_intersect($rule_coupons, $user_coupons);
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
               
        return  $or_result;
    }

    function getUserAddedCouponsID( $package ){
        if(is_a($package, 'WC_Cart')){
            $codes = WC()->cart->get_applied_coupons();
            $user_coupons = array();
            foreach( $codes as $code){
                $coupon_obj = new WC_Coupon($code);
                $user_coupons[] = $coupon_obj->get_id();
            }
            return $user_coupons;
        }elseif(is_a($package, 'WC_Order')){
            if(method_exists($package, 'get_coupon_codes')){
                $codes = $package->get_coupon_codes();
            }else{
                /**
                 * Get get_used_coupons() is deprecated since 3.7
                 */
                $codes = $package->get_used_coupons();
            }

            $user_coupons = array();
            foreach( $codes as $code){
                $coupon_obj = new WC_Coupon($code);
                $user_coupons[] = $coupon_obj->get_id();
            }
            return $user_coupons;
        }
        return [];
    }
}


new Pi_dpmw_selection_rule_coupon(PI_DPMW_SELECTION_RULE_SLUG);