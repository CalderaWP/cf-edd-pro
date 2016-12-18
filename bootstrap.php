<?php

/**
 * Load autoloader
 */
add_action( 'plugins_loaded', function(){
	include __DIR__ . '/vendor/autoload.php';
}, 2 );


/**
 * Add bundle builder processor to Caldera Forms
 *
 * @since 0.0.1
 */
add_filter( 'caldera_forms_pre_load_processors', function() {
	\calderawp\cfedd\cf\init::create_processor();
});



add_filter( 'edd_enabled_payment_gateways', function( $gateways ){
	if( ! edd_is_checkout() ){
		$gateways = array_merge( $gateways, cf_eddpro_gateway_definitions() );
	}

	return $gateways;
});

function cf_eddpro_gateway_definitions(){
	return [];
	return [
		\calderawp\cfedd\edd\payment\create::GATEWAY => [
			'admin_label'    => __( 'Caldera Forms', 'cf-edd-pro' ),
			'checkout_label' => __( 'Caldera Forms', 'cf-edd-pro' )
		],
	];
}