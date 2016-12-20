<?php
use \calderawp\cfedd\init;
/**
 * Load autoloader and initializing class
 */
add_action( 'plugins_loaded', function(){
	include __DIR__ . '/vendor/autoload.php';
	init::get_instance()->add_cf_hooks();
	init::get_instance()->add_edd_hooks();
}, 2 );



/**
 * NEED THESE?




add_filter( 'edd_enabled_payment_gateways', function( $gateways ){
	if( ! edd_is_checkout() ){
		$gateways = array_merge( $gateways, cf_eddpro_gateway_definitions() );
	}

	return $gateways;
});

function cf_eddpro_gateway_definitions(){
	return [];
	return [
		\calderawp\cfedd\edd\payment\create\bundle::GATEWAY => [
			'admin_label'    => __( 'Caldera Forms', 'cf-edd-pro' ),
			'checkout_label' => __( 'Caldera Forms', 'cf-edd-pro' )
		],
	];
}
 **/