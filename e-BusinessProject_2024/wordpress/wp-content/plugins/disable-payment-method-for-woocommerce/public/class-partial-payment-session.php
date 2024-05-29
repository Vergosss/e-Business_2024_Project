<?php 

namespace PISOL\DPMW;

class Session{
     /**
     * checks if partial payment option selected
     * if variable pi_partial_payment is set in the session
     */
    static function partialPaymentSelectedInSession(){
        if(function_exists('WC') && is_object(WC()->session)){
            $stored_values = WC()->session->get('pi_partial_payment');

            if($stored_values !== null) return true;

        }
        return false;
    }

     /**
     * we set the partial payment id in the session
     */
    static function partialPaymentSelected(){

        $name = 'pi-cod-deposit';
        if(isset($_POST[$name])){
            $fees_id = $_POST[$name];
            self::saveFeesInSession($fees_id);
            return true;
        }elseif(isset($_POST['post_data'])){
            parse_str($_POST['post_data'], $values);
            
            if(isset($values[$name])){ 
                $fees_id = $values[$name];
                self::saveFeesInSession($fees_id);
                return true;
            }
        }elseif(self::partialPaymentSelectedInSession()){
             return true;
        }

         /**
         * check_ajax_referer was making the All order list as Blank, so we have added extra check so it only runs when we are doing Ajax and for other test it don't run so is_ajax() is added before it
         * Issue was only in Free version but for safety we have added it in pro version as well
         */
        if(is_ajax() && check_ajax_referer( 'update-order-review', 'security',false ) !== false){
            self::removeFeesInSession();
        }
        return false;
    }

    static function saveFeesInSession($fees_id){
        if(function_exists('WC') && is_object(WC()->session)){
            $result = WC()->session->set('pi_partial_payment', $fees_id);
        }
    }

    static function removeFeesInSession(){
        if(function_exists('WC') && is_object(WC()->session)){
            WC()->session->set('pi_partial_payment', null);
        }
    }

    static function getPartialAmtToPay(){
        if(function_exists('WC') && is_object(WC()->session)){
            $advance_amt = WC()->session->get('pi_advance_amount'); 
            return $advance_amt;
        }
    }

    static function setPartialAmtToPay($amt){
        if(function_exists('WC') && is_object(WC()->session)){
            $advance_amt = WC()->session->set('pi_advance_amount', $amt); 
            return $advance_amt;
        }
    }

    static function getOriginalTotal(){
        if(function_exists('WC') && is_object(WC()->session)){
            $total = WC()->session->get('pi_total'); 
            return $total;
        }
    }

    static function setOriginalTotal($amt){
        if(function_exists('WC') && is_object(WC()->session)){
            $total = WC()->session->set('pi_total', $amt); 
            return $total;
        }
    }

    static function getBalanceAmountToPay(){
        if(function_exists('WC') && is_object(WC()->session)){
            $balance_amt = WC()->session->get('pi_balance_amount'); 
            return $balance_amt;
        }
    }

    static function setBalanceAmountToPay( $amt ){
        if(function_exists('WC') && is_object(WC()->session)){
            $balance_amt = WC()->session->set('pi_balance_amount', $amt); 
            return $balance_amt;
        }
    }

    static function unset_all_amt(){
        self::setOriginalTotal(null);
        self::setPartialAmtToPay(null);
        self::setBalanceAmountToPay(null);
    }

    static function set_all_amt($total, $advance_amount, $balance_amt){
        self::setOriginalTotal($total);
        self::setPartialAmtToPay($advance_amount);
        self::setBalanceAmountToPay($balance_amt);
    }
}