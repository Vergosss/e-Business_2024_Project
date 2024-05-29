<?php 
/**
 * Store Data Order
 *
 * This class will store some extra data in the order as meta data so we can retrive them during order pay time and process the rules
 *
 */

class pi_dpmw_store_data_in_order{

    static $instance;

    static $slug = '_dpmw';

    public static function get_instance(){
        if( is_null( self::$instance ) ){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct(){
        add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'store_data_in_order' ), 10, 2 );
    }

    function store_data_in_order( $order_id, $posted ){
        $order = wc_get_order( $order_id );
        self::zone_id( $order, $posted );
        self::different_shipping_address( $order, $posted );
        self::shipping_methods( $order, $posted );
        $order->save();
    }

    static function zone_id( $order, $posted ){
        $shipping_packages =  WC()->cart->get_shipping_packages();
        $shipping_zone = wc_get_shipping_zone( reset( $shipping_packages ) );

        if(!is_object($shipping_zone)){
            $zone_id = 0;
        }else{
            $zone_id   = $shipping_zone->get_id();
        }
        $order->update_meta_data( self::$slug.'_zone_id', $zone_id );
    }

    static function get_zone_id( $order ){
        $zone_id = $order->get_meta( self::$slug.'_zone_id', true );
        return $zone_id;
    }

    static function different_shipping_address( $order, $posted ){
        $different_shipping_address = isset( $posted['ship_to_different_address'] ) && !empty($posted['ship_to_different_address']) ? 1 : 0;
        $order->update_meta_data( self::$slug.'_different_shipping_address', $different_shipping_address );
    }

    static function is_ship_to_different_address($order){
        $different_shipping_address = $order->get_meta( self::$slug.'_different_shipping_address', true );
        return $different_shipping_address;
    }

    static function shipping_methods( $order, $posted ){
        
        $chosen_method = WC()->session->get( 'chosen_shipping_methods' );
        $order->update_meta_data( self::$slug.'_shipping_methods', $chosen_method );
    }

    static function get_shipping_methods($order){
        $shipping_methods = $order->get_meta( self::$slug.'_shipping_methods', true );
        return $shipping_methods;
    }
}

pi_dpmw_store_data_in_order::get_instance();