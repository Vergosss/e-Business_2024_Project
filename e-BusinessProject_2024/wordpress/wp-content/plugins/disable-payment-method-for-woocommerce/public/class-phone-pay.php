<?php 

/**
 * https://wordpress.org/plugins/phonepe-payment-solutions/
 * The Phone pay payment solution plugin runs a recheck on the order total after the payment completion to make sure the total matches the order total.
 * this recheck fails and it gives "Amount Mismatch!" error. to handle this we will modify the get_total() of an wc_order when the request is made by phone pay rest api for the amount matching check.
 */

class Pi_dpmw_phone_pay_support{
    public function __construct(){
        add_filter('woocommerce_order_get_total', array($this, 'phone_pay_support'), 100, 2);
    }

    public function phone_pay_support($total, $order){
        if($this->is_phone_pay_request()){
            $deposit_order_id = $order->get_meta('_generated_deposit_amt_order', true);
            if($deposit_order_id){
                $deposit_order = wc_get_order($deposit_order_id);
                $total = $deposit_order->get_total();
            }
        }

        if(isset($_GET['merchant_transaction_id'])){
            $transaction_id = $order->get_transaction_id();
            $payment_method = strtolower($order->get_payment_method());
            $merchant_transaction_id = $_GET['merchant_transaction_id'];
            if($transaction_id == $merchant_transaction_id && strpos($payment_method, 'phonepe') !== false){
                $deposit_order_id = $order->get_meta('_generated_deposit_amt_order', true);
                if($deposit_order_id){
                    $deposit_order = wc_get_order($deposit_order_id);
                    $total = $deposit_order->get_total();
                }
            }
        }
        
        return $total;
    }

    function is_phone_pay_request() {
        // Get the current request URI
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    
        // Check if the request URI contains your custom route
        return strpos($request_uri, 'wp-json/wp-phonepe/v1/callback') !== false;
    }
}

new Pi_dpmw_phone_pay_support();