<?php

class Pi_dpmw_selection_rule_product_tag{
    function __construct($slug){
        $this->slug = $slug;
        $this->condition = 'product_tag';

        add_filter("pi_".$this->slug."_condition", array($this, 'addRule'));

        add_action( 'wp_ajax_pi_'.$this->slug.'_value_field_'.$this->condition, array( $this, 'ajaxCall' ) );

        add_filter('pi_'.$this->slug.'_saved_values_'.$this->condition, array($this, 'savedDropdown'), 10, 3);
        
        add_action( 'wp_ajax_pi_'.$this->slug.'_options_'.$this->condition, array( $this, 'search_tag' ) );
        
        add_filter('pi_'.$this->slug.'_condition_check_'.$this->condition, array($this,'conditionCheck'),10,4);

        add_action('pi_'.$this->slug.'_logic_'.$this->condition, array($this, 'logicDropdown'));
        add_filter('pi_'.$this->slug.'_saved_logic_'.$this->condition, array($this, 'savedLogic'),10,3);
    }

    function addRule($rules){
        $rules[$this->condition] = array(
            'name'=>__('Product tag', 'disable-payment-method-for-woocommerce'),
            'group'=>'product_related',
            'condition'=>$this->condition      
        );
        return $rules;
    }

    function logicDropdown(){
        $html = "";
        $html .= 'var pi_logic_'.$this->condition.'= "<select class=\'form-control\' name=\'pi_selection[{count}][pi_'.$this->slug.'_logic]\'>';
        
        $html .= '<option value=\'equal_to\'>Present in cart</option>';
        $html .= '<option value=\'not_equal_to\'>Not present in cart</option>';
       
        
        $html .= '</select>";';
        echo $html;
    }

    function savedLogic($html_in, $saved_logic, $count){
        $html = "";
        $html .= '<select class="form-control" name="pi_selection['.$count.'][pi_'.$this->slug.'_logic]">';
        
            $html .= '<option value="equal_to" '.selected($saved_logic , "equal_to",false ).'>Present in cart</option>';
            $html .= '<option value="not_equal_to" '.selected($saved_logic , "not_equal_to",false ).'>Not present in cart</option>';
           
        
        $html .= '</select>';
        return $html;
    }


    function ajaxCall(){
        if(!current_user_can( 'manage_options' )) {
            return;
            die;
        }
        $count = sanitize_text_field(filter_input(INPUT_POST,'count'));
        echo Pi_dpmw_selection_rule_main::createSelect(array(),$count,$this->condition,  "multiple", null,'dynamic');
        die;
    }

    function savedProductsTags($values){
        $saved_products_tags = array();
        if(is_array($values)){
            foreach($values as $value){
                $term = get_term($value, 'product_tag');

                if($term === false || is_wp_error($term)) continue;

                $saved_products_tags[$value] = $term->name;
            }
        }
        
        return $saved_products_tags;
    }

    function savedDropdown($html, $values, $count){
        $html = Pi_dpmw_selection_rule_main::createSelect($this->savedProductsTags($values),$count,$this->condition,  "multiple", $values,'dynamic');
        return $html;
    }

    public function search_tag( $x = '', $post_types = array( 'product' ) ) {
		if ( !current_user_can( 'manage_options' ) ) {
			return;
		}

        ob_start();
        
        if(!isset($_GET['keyword'])) die;

		$keyword = isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) : "";

		if ( empty( $keyword ) ) {
			die();
		}

        $arg = array(
            'taxonomy'   => 'product_tag',
            'name__like' => $keyword,
            'fields'     => 'all',
            'hide_empty' => false,
        );

		$tags      = get_terms($arg);
        $found_tags = array();
		foreach($tags as $key => $tag){
            if(!is_object($tag)) continue;
            $found_tags[] = array('id'=> $tag->term_id, 'text'=> $tag->name);
        }
		wp_send_json( $found_tags );
		die;
    }

    function conditionCheck($result, $package, $logic, $values){
        
        $or_result = false;

        $user_products = $this->getProductsTagFromOrder($package);
        if(is_array($values)){
            $rule_products = $values;
        }else{
            $rule_products = array();
        }
        $intersect = array_intersect($rule_products, $user_products);
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

    function getProductsTagFromOrder( $package ){

        if ( ! did_action( 'wp_loaded' ) ) return array();
        
        if(is_a($package, 'WC_Cart')){

            $products = WC()->cart->get_cart();
            $user_products_tags = array();
            foreach($products as $product){
                $product_id = $product['product_id'];
                $tags = wp_get_post_terms( $product_id, 'product_tag' );
                if($tags === false || is_wp_error($tags)) continue;
                foreach($tags as $tag){
                    $user_products_tags[] = $tag->term_id;
                }
            }
            return $user_products_tags;

        }elseif(is_a($package, 'WC_Order')){

            $products = $package->get_items();
            $user_products_tags = array();
            foreach($products as $product){
                $product_id = $product->get_product_id();
                $tags = wp_get_post_terms( $product_id, 'product_tag' );
                if($tags === false || is_wp_error($tags)) continue;
                foreach($tags as $tag){
                    $user_products_tags[] = $tag->term_id;
                }
            }
            return $user_products_tags;

        }

        return [];
    }

}

new Pi_dpmw_selection_rule_product_tag(PI_DPMW_SELECTION_RULE_SLUG);
