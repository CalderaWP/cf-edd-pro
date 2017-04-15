<?php


namespace calderawp\cfedd\edd\discount;


/**
 * Class endpoint
 * @package calderawp\cfedd\discount
 */
class endpoint  implements \Caldera_Forms_API_Route {

	/**
	 * @inheritdoc
	 * @since 1.1.0
	 */
	public function add_routes( $namespace ) {
		register_rest_route( $namespace, '/processors/edd/discount', [
			'methods' => 'GET',
			'callback' => [ $this, 'by_code' ],
			'permissions_callback' => [ $this, 'permissions' ],
			'args' => [
				'code' => [
					'required' => true,
				],
				'items' => [
					'required' => false,
					'default' => [],
					'type' => 'array',
					'sanitize_callback' => [ $this, 'prepare_items' ]
				],
				'total' => [
					'required' => true,
					'type' => 'float'
				],
				'nonce' => [
					'required' => false,
					'type' => 'string'
				],
			]
		]);
		register_rest_route( $namespace, '/processors/edd/discount/(?P<id>[\w-]+)', [
			'methods' => 'GET',
			'callback' => [ $this, 'by_id' ],
			'permissions_callback' => [ $this, 'permissions' ],
			'args' => [
				'items' => [
					'required' => false,
					'default' => [],
					'type' => 'array',
					'sanitize_callback' => [ $this, 'prepare_items' ]
				],
				'nonce' => [
					'required' => false,
					'type' => 'string'
				],
				'total' => [
					'required' => true,
					'type' => 'float'
				],
			]
		]);
	}

	/**
	 * Force items to be an array
	 *
	 * @since 1.1.0
	 *
	 * @todo move this into Caldera Forms Core
	 * https://github.com/CalderaWP/Caldera-Forms/issues/1485
	 *
	 * @param mixed $items
	 *
	 * @return array
	 */
	public function prepare_items( $items ){
		if( ! is_array( $items ) ){
			return [];
		}

		foreach ( $items as $i => &$item ){
			if( is_numeric( $item ) ){
				$item = absint( $item );
			}else{
				unset( $items[$i] );
			}

		}

		return $items;
	}

	public function permissions( \WP_REST_Request $request ){
		return apply_filters( 'cf_edd_pro_discount_api_permissions', false, $request );
	}

	/**
	 * Check discount by code
	 *
	 * @since 1.1.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function by_code( \WP_REST_Request $request ){
		$discount = discount::factory( $request[ 'code' ] );
		return $this->check_code( $request, $discount );

	}

	/**
	 * Check discount by ID
	 *
	 * @since 1.1.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function by_id( \WP_REST_Request $request ){
		$discount = discount::factory( $request[ 'code' ] );
		return $this->check_code( $request, $discount );


	}

	/**
	 * Check the code
	 * @param \WP_REST_Request $request
	 * @param discount $discount
	 *
	 * @return \WP_REST_Response|\WP_REST_Response
	 */
	protected function check_code( \WP_REST_Request $request, discount $discount )
	{
		$items = $request->get_param( 'items' );
		$price = $request->get_param( 'price' );
		$valid = $discount->check_valid( $items, get_current_user_id(), $price );
		if( is_wp_error( $valid ) ){
			return rest_ensure_response( $valid );
		}


		do_action( 'cf_edd_pro_discount_validated_via_api', $discount, $items, $price );
		return rest_ensure_response( [
			'message' => __( 'Discount code applied', 'cf-edd-pro' ),
			'amount' => $discount->get_amount(),
			'type'   => $discount->get_type()
		] );
	}


}