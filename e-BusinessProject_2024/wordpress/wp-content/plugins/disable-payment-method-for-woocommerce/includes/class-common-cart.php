<?php

if(!function_exists('pisol_wpml_dpmw_object')){
function pisol_wpml_dpmw_object( $object_id, $type, $lang = '' ) {
    if(empty($lang)){
        $current_language = apply_filters( 'wpml_current_language', NULL );
    }else{
        $current_language = $lang;
    }
    // if array
    if( is_array( $object_id ) ){
        $translated_object_ids = array();
        foreach ( $object_id as $id ) {
            $translated_object_ids[] = apply_filters( 'wpml_object_id', $id, $type, true, $current_language );
        }
        return $translated_object_ids;
    }
    // if string
    elseif( is_string( $object_id ) ) {
        // check if we have a comma separated ID string
        $is_comma_separated = strpos( $object_id,"," );
 
        if( $is_comma_separated !== FALSE ) {
            // explode the comma to create an array of IDs
            $object_id     = explode( ',', $object_id );
 
            $translated_object_ids = array();
            foreach ( $object_id as $id ) {
                $translated_object_ids[] = apply_filters ( 'wpml_object_id', $id, $type, true, $current_language );
            }
 
            // make sure the output is a comma separated string (the same way it came in!)
            return implode ( ',', $translated_object_ids );
        }
        // if we don't find a comma in the string then this is a single ID
        else {
            return apply_filters( 'wpml_object_id', intval( $object_id ), $type, true, $current_language );
        }
    }
    // if int
    else {
        return apply_filters( 'wpml_object_id', $object_id, $type, true, $current_language );
    }
}
}

if(!function_exists('pisol_dpmw_revertToBaseCurrency')){
    function pisol_dpmw_revertToBaseCurrency( $price ){
        if( empty($price) ) return $price;

        /**
         * for doing currency conversion of fees for multiple currency plugin
         * https://wordpress.org/plugins/woo-multi-currency/
         */
        if(function_exists('wmc_revert_price')){
            $price = wmc_revert_price( $price );
        }

        /**
         * WPML
         */
        global $woocommerce_wpml;
        if(class_exists('woocommerce_wpml') && is_object($woocommerce_wpml) && isset($woocommerce_wpml->multi_currency) && is_object($woocommerce_wpml->multi_currency) ){
            $price = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $price );
        }

        /**
         * https://wordpress.org/plugins/woocommerce-currency-switcher/
         */

        global $WOOCS;
        if(class_exists('WOOCS') && is_object($WOOCS)){
            $price =  $WOOCS->woocs_back_convert_price($price);
        }

        

        return $price;
    }
}

if(!function_exists('pisol_dpmw_multiCurrencyFilters')):
    function pisol_dpmw_multiCurrencyFilters($price){
        if( empty($price) ) return $price;
        /**
         * for doing currency conversion of fees for multiple currency plugin
         * https://wordpress.org/plugins/woo-multi-currency/
         */
        $price = apply_filters( 'wmc_change_raw_price', $price );

        /**
         * WPML multi currency
         */
        $price = apply_filters( 'wcml_raw_price_amount', (float)$price);
        
        /**
         * https://wordpress.org/plugins/woocommerce-currency-switcher/
         * Documentation: https://currency-switcher.com/function/woocs-woocs_exchange_value/
         */
        global $WOOCS;
        if(class_exists('WOOCS') && is_object($WOOCS)){
            $price =  $WOOCS->woocs_exchange_value($price);
        }

        return $price;
    }
endif;


if(!function_exists('pisol_dpmw_CurrencyValid')):
    function pisol_dpmw_CurrencyValid($rule_id){
        $valid = true;

        $pi_currency = get_post_meta( $rule_id, 'pi_currency', true );

        if(empty($pi_currency) || !is_array($pi_currency)){
            return $valid;
        }

        $current_currency = apply_filters('pi_dpmw_current_currency', get_woocommerce_currency());

        if(!in_array($current_currency, $pi_currency)){
            $valid = false;
        }

        return $valid;;
    }
endif;