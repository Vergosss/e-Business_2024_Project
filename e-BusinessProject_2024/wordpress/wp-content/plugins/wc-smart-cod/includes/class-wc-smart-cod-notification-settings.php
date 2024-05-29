<?php

class Wc_Smart_Cod_Notification_Settings {

    private $settings_url;

    public function __construct( $pro_url ) {
        $this->settings_url = $pro_url . '/free-version-notifications/';
    }

    public function get_settings() {
    
		try {

			$headers = array('Content-Type' => 'application/json; charset=utf-8');

			$res = wp_remote_get(
				$this->settings_url,
				array( 'timeout' => 1 )
			);

			if( is_wp_error( $res ) ) {
				return array();
			}

			$ok = ( $res
				&& isset( $res['response'] )
				&& isset( $res['response']['code'] )
				&& $res['response']['code'] === 200
			);
	
			if($ok) {
				return json_decode( $res['body'], true );
			}
		}
		catch(Exception $e) {
		} 
		
		return array(); 
    }
    
}