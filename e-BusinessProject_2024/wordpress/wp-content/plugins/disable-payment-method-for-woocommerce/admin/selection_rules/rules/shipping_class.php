<?php

class Pi_dpmw_selection_rule_shipping_class{
    function __construct($slug){
        $this->slug = $slug;
        $this->condition = 'shipping_class';
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
            'name'=>__('Shipping Class'),
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
            $html .= '<option value=\'only_have_this_classes_product\'>Only have this class product</option>';
           
        
        $html .= '</select>";';
        echo $html;
    }

    function savedLogic($html_in, $saved_logic, $count){
        $html = "";
        $html .= '<select class="form-control" name="pi_selection['.$count.'][pi_'.$this->slug.'_logic]">';
        
            $html .= '<option value="equal_to" '.selected($saved_logic , "equal_to",false ).'>Equal to (=)</option>';
            $html .= '<option value="not_equal_to" '.selected($saved_logic , "not_equal_to",false ).'>Not Equal to (!=)</option>';
            $html .= '<option value="only_have_this_classes_product" '.selected($saved_logic , "only_have_this_classes_product",false ).'>Only have this class product</option>';
           
        
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
        echo Pi_dpmw_selection_rule_main::createSelect($this->allShippingClasses(), $count, $this->condition,  "multiple",null,'static');
        die;
    }

    function savedDropdown($html, $values, $count){
        $html = Pi_dpmw_selection_rule_main::createSelect($this->allShippingClasses(), $count, $this->condition,  "multiple", $values,'static');
        return $html;
    }

    function allShippingClasses(){
       $all_shipping_classes_obj = WC()->shipping->get_shipping_classes();
        
       $all_shipping_classes = array();
       foreach( $all_shipping_classes_obj as $obj ){
           $all_shipping_classes[$obj->term_id] = $obj->name;
       }
       return $all_shipping_classes;
    }

    function conditionCheck($result, $package, $logic, $values){
        
                    $or_result = false;
                    $user_classes = $this->getUserAddedClasses($package);
                    $rule_classes = pisol_wpml_dpmw_object($values, 'product_shipping_class');
                    $intersect = array_intersect($rule_classes, $user_classes);
                    if($logic == 'equal_to'){
                        if(count($intersect) > 0){
                            $or_result = true;
                        }else{
                            $or_result = false;
                        }
                    }elseif($logic == 'not_equal_to'){
                        if(count($intersect) == 0){
                            $or_result = true;
                        }else{
                            $or_result = false;
                        }
                    }elseif($logic == 'only_have_this_classes_product'){
                        foreach($user_classes as $user_class){
                            if(!in_array($user_class, $rule_classes)){
                                return false;
                            }
                        }
                        return true;
                    }
               
        return  $or_result;
    }

    function getUserAddedClasses( $package ){
        if(is_a($package, 'WC_Cart')){
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
        }elseif(is_a($package, 'WC_Order')){
            $products = $package->get_items();
            $user_classes = array();
            foreach($products as $product){
                $product_obj = $product->get_product();
                $class = $product_obj->get_shipping_class_id();
                if( !empty($class) ){ 
                    $user_classes[] = $product_obj->get_shipping_class_id();
                }
            }
            return $user_classes;
        }
        return [];
    }
}


new Pi_dpmw_selection_rule_shipping_class(PI_DPMW_SELECTION_RULE_SLUG);