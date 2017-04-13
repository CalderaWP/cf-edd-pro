<?php


namespace calderawp\cfedd\edd\discount;


/**
 * Class nonces
 * @package calderawp\cfedd\edd\discount
 */
class nonces {

	/**
	 * @return array
	 */
	public static function create(){
		return [
			'nonce' => wp_create_nonce( static::nonce_action() ),
			'rest_nonce' => wp_create_nonce( 'wp_rest' )
		];
	}

	public static function nonce_action( ){
		return 'cf_edd_discount';
	}

}