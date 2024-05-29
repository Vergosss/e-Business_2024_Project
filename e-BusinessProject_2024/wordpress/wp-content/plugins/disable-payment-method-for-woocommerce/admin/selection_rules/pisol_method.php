<?php

class Pisol_dpmw_method_evaluation{
    function __construct($method, $package){
        $this->method = $method;
        $this->package = $package;
        $this->results = array();
        $this->result = false;
    }

    function getRules(){
        $rules = get_post_meta($this->method->ID, 'pi_metabox', true);
        return $rules;
    }

    function evaluateRules(){
        $rules = $this->getRules();
        $results = array();
        if(is_array($rules)){
        foreach($rules as $rule){
            if(is_array($rule['pi_value']) && count($rule['pi_value']) > 0){
            $results[] = apply_filters( 'pi_dpmw_condition_check_'.$rule['pi_condition'], false, $this->package, $rule['pi_logic'], $rule['pi_value'] );
            }
        }
        }

        return $results;
    }

    function finalResult(){
        $pi_status  = get_post_meta( $this->method->ID, 'pi_status', true );
        $pi_condition_logic = get_post_meta( $this->method->ID, 'pi_condition_logic', true );

        if ( isset( $pi_status ) && 'off' === $pi_status ) {
			$this->results = array(false);
            $this->result = false;
            return $this->result;
        }

        $this->results = $this->evaluateRules();
        if($pi_condition_logic == 'and' || empty($pi_condition_logic)){
            $this->result = $this->andOperation($this->results);
        }else{
            $this->result = $this->orOperation($this->results);
        }
        return $this->result;
    }

    function andOperation($or_results){
        if(!is_array($or_results)){
            return false;
        }

         if(!is_array($or_results) || in_array(false, $or_results) || count($or_results) < 1){
             return false;
         }else{
             return true;
         }
    }

    function orOperation( $results ){
        if(!is_array($results)) return false;

        if(  in_array(true, $results) ){
            return true;
        }
        return false;
    }
}