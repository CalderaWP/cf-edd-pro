<?php
/**
 * Endpoint for getting EDD Bundle Dynamic Pricing
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
namespace calderawp\cfedd\cf\pricing;



class endpoint implements \Caldera_Forms_API_Route {

	/**
	 * @inheritdoc
	 */
	public function add_routes( $namespace ) {
		register_rest_route( $namespace, '/processors/edd/pricer', [
			'methods' => 'POST',
			'callback' => [ $this, 'check' ],
			'args' => [
				'count' => [
					'required' => true,
					'sanitization_callback' => 'absint',
				],
				'form_id' => [
					'required' => 'true',
					'validation_callback' => [ $this, 'form_exists' ]
				]
			]
		]);
	}

	public function form_exists( $form_id ){
		return ! empty( \Caldera_Forms_Forms::get_form( $form_id ) );
	}

	public function check( \WP_REST_Request $request ){
		$pricer = factory::pricer( \Caldera_Forms_Forms::get_form( $request[ 'form_id' ] ) );
		if( is_object( $pricer ) ){
			$price = $pricer->do_math( $request[ 'count' ] );
			$response = new \Caldera_Forms_API_Response( [ 'price' => $price ], 200 );
			return $response;
		}else{
			return new \Caldera_Forms_API_Error();
		}
	}
}