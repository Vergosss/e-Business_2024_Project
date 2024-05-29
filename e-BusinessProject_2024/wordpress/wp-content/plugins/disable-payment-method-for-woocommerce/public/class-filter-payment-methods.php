<?php

class pisol_dpmw_filter_payment_methods{
    function __construct(){
        add_filter('woocommerce_available_payment_gateways', [$this, 'filterPaymentMethods']);
    }

    function filterPaymentMethods($gateways){

        if (is_wc_endpoint_url('order-pay')) {
            $order_id_from_url = get_query_var('order-pay');
            $order = wc_get_order( $order_id_from_url );

            $matched_removal_rules = self::matchedDisablingRules( $order );

            $gateways = self::removeGateways($gateways, $matched_removal_rules);

            return $gateways;
    
        }

        $package = WC()->cart;
        $matched_removal_rules = $this->matchedDisablingRules( $package );

        $gateways = $this->removeGateways($gateways, $matched_removal_rules);

        return $gateways;
    }

    function matchedDisablingRules($package){
        $matched_methods = array();
        $args         = array(
            'post_type'      => 'pi_dpmw_rules',
            'posts_per_page' => - 1
        );
        $all_methods        = get_posts( $args );
        foreach ( $all_methods as $method ) {

            $type = get_post_meta($method->ID, 'pi_rule_type', true);

            if(!empty($type) && $type != 'disable') continue;

            if(!pisol_dpmw_CurrencyValid($method->ID)) continue;

            $is_match = $this->matchConditions( $method, $package );

            if ( $is_match === true ) {
                $matched_methods[] = $method;
            }
        }

        return $matched_methods;
    }

    function matchConditions( $method = array(), $package = array() ) {

        if ( empty( $method ) ) {
            return false;
        }

        if ( ! empty( $method ) ) {
            $method_eval_obj = new Pisol_dpmw_method_evaluation($method, $package);
            $final_condition_match = $method_eval_obj->finalResult();

            if ( $final_condition_match ) {
                return true;
            }
        }

        return false;
    }

    function removeGateways($gateways, $matched_removal_rules){
        foreach($matched_removal_rules as $rule){
            $remove_methods = get_post_meta($rule->ID, 'disable_payment_methods', true);

            foreach($remove_methods as $remove_method){
                if(isset($gateways[$remove_method])){
                    unset($gateways[$remove_method]);
                }
            }
        }
        return $gateways;
    }
}

new pisol_dpmw_filter_payment_methods();