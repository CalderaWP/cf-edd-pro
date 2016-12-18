<?php


add_action( 'init', function(){
	include __DIR__ . '/vendor/autoload.php';
	$c = new \calderawp\cfedd\edd\payment\create( '11.45', 9868, [ 9870 ] );

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