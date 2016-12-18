<?php

/**
 * Load autoloader
 */
add_action( 'plugins_loaded', function(){
	include __DIR__ . '/vendor/autoload.php';
	( new \calderawp\cfedd\edd\init() )->add_hooks();
}, 2 );





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