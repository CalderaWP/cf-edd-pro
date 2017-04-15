<?php


namespace calderawp\cfedd\edd\discount;


/**
 * Class field
 * @package calderawp\cfedd\edd\discount
 */
class field {

	/** @var  float */
	protected $price;

	/** @var string  */
	protected $slug = 'edd-discount';

	/**
	 * Add hooks for this class to function
	 *
	 * @since 1.1.0
	 */
	public function add_hooks(){
		add_filter( 'caldera_forms_get_field_types', [ $this, 'register' ] );
		add_filter( 'cf_edd_pro_discount_api_permissions', [ $this, 'api_permissions' ], 10, 2 );
		add_filter( 'caldera_forms_field_attributes', [ $this, 'filter_attrs' ], 10, 3 );
	}


	/**
	 * Register the field type
	 *
	 * @since 1.1.0
	 *
	 * @uses "caldera_forms_get_field_types"
	 *
	 * @param array $fields
	 *
	 * @return mixed
	 */
	public function register( $fields ){
		$fields[  $this->slug ] = [
			'field' => __( 'EDD Discount', 'cf-edd-pro' ),
			'description' => __( 'Apply EDD Discount codes to EDD Payments', 'cf-edd-pro' ),
			'handler' => [ $this, 'handler' ],
			'category' => __( 'eCommerce', 'cf-edd-pro' ),
			'scripts' => [
				CF_EDD_PRO_URL . 'assets/cf-edd-pro-discount.js'
			],
			'file'       => CFCORE_PATH . 'fields/generic-input.php',
			'setup'       => [
				'template' => CF_EDD_PRO_PATH . 'includes/discount-field/config.php',
				'preview'  => CF_EDD_PRO_PATH . 'includes/discount-field/preview.php',
			]
		];

		if( \Caldera_Forms_Render_Assets::should_minify() ){
			$fields[  $this->slug ][ 'scripts' ][0] = CF_EDD_PRO_URL . 'assets/build/cf-edd-pro-discount.min.js';
		}

		return $fields;
	}

	/**
	 * Add data attributes to field
	 *
	 * Inlines the config as JSON
	 *
	 * @uses "caldera_forms_field_attributes" filter
	 *
	 * @since 1.5.0
	 */
	public function filter_attrs( $attrs, $field, $form ){
		if( $this->slug == \Caldera_Forms_Field_Util::get_type( $field, $form ) ){
			$attrs['data-' . $this->slug ] = wp_json_encode( $this->config( $form, $field ) );

		}

		return $attrs;


	}


	/**
	 * Handler for discount code class
	 *
	 * @since 1.1.0
	 *
	 * @param string $value The field value
	 * @param array $field Field config
	 * @param array $form Form config
	 *
	 * @return float|mixed;
	 */
	public function handler( $value, $field, $form){
		/**
		 * Change which discount is used
		 *
		 * If return is a calderawp\cfedd\edd\discount\discount then that object is used to verify/apply discount
		 * If return is a float, that amount is deducted from price
		 * If return is any other type, no discount is applied
		 *
		 * @since 1.1.0
		 *
		 * @param \calderawp\cfedd\edd\discount\discount $discount
		 * @param string $value The discount code used in form
		 * @param array $field Field config
		 * @param array $form Form config
		 */
		$discount = apply_filters( 'cf_edd_pro_discount_get', discount::factory( $value ), $value, $field, $form );
		if( is_float( $discount ) ){
			$this->price = $discount;
		}elseif( ! $discount instanceof discount  || ! $discount->get_ID() ){
			return $value;
		}elseif( $discount instanceof discount ){
			$price_field = $this->get_price_field( $field, $form );
			$price = \Caldera_Forms::get_field_data( $price_field, $form );

			/**
			 * Items to check discount against
			 *
			 * @since 1.1.0
			 *
			 * @param array $items
			 * @param \calderawp\cfedd\edd\discount\discount $discount
			 * @param array $field Field config
			 * @param array $form Form config
			 */
			$items = apply_filters( 'cf_edd_pro_discount_items', [], $discount, $field, $form );
			/**
			 * Change if discount is valid
			 *
			 * @since 1.1.0
			 *
			 * @param bool $is_valid Is discount valid?
			 * @param \calderawp\cfedd\edd\discount\discount $discount
			 * @param array $field Field config
			 * @param array $form Form config
			 */
			$is_valid = apply_filters( 'cf_edd_pro_discount_valid', $discount->check_valid( $items, get_current_user_id(), $price ), $discount, $field, $form ) ;
			if( ! is_wp_error( $is_valid )  ) {
				$this->price = round( $discount->get_discounted_amount( $price ) );
				$discount->increase_usage();
			}

		}else{
			return $value;
		}

		if( ! is_null( $this->price ) ){
			$this->apply_discount( $field, $form );
		}

		return $value;

	}

	/**
	 * Apply discount
	 *
	 * Must set $this->price first!
	 *
	 * @since 1.1.0
	 *
	 * @param array $field Field config
	 * @param array $form Form config
	 */
	protected function apply_discount( $field, $form ){
		$price_field = $this->get_price_field( $field, $form );
		\Caldera_Forms::set_field_data( $price_field, $this->price, $form );
	}


	/**
	 * Use nonce to check API permissions on discount code check
	 *
	 * @since 1.1.0
	 *
	 * @uses "cf_edd_pro_discount_api_permissions" filter
	 *
	 * @param bool $allowed
	 * @param \WP_REST_Request $request
	 *
	 * @return false|int
	 */
	public function api_permissions( $allowed, $request ){
		if( ! empty( $request[ 'nonce'] ) && ! $allowed ){
			$allowed = wp_verify_nonce( $request[ 'nonce' ], nonces::nonce_action() );

		}

		if( ! $allowed ){
			$allowed = wp_verify_nonce( 'wp_rest', $request[ '_wpnonce' ] );
		}

		return $allowed;
	}

	/**
	 * Add inline JS for handling discount code display
	 * 
	 * @since 1.1.0
	 */
	protected function add_inline_js( $form, $field ){
		$field_id = $field[ 'ID' ];
		$form_id = $form[ 'ID'];
		$config   = $this->config( $form, $field, $field_id, $form_id );
		$script = '
			if( undefined == typeof  window.CFEDD_Field ){
				window.CFEDD_Field = {}
			}
			window.CFEDD_Field[ ' . $form_id . '] = ' . wp_json_encode( $config ) . ';';
		
		if( method_exists( '\Caldera_Forms_Render_Util' , 'add_inline_script') ){
			\Caldera_Forms_Render_Util::add_inline_script( $script, $form );
		}else{
			$script = sprintf( "<script type='text/javascript'>\n%s\n</script>\n", $script );
			\Caldera_Forms_Render_Util::add_inline_data( $script, $form );
		}
	}

	/**
	 * Create config for field
	 *
	 * @since 1.1.0
	 *
	 * @param $form
	 * @param $field
	 *
	 * @return array
	 */
	protected function config( $form, $field ){
		$config = [
			'field'      => $field[ 'ID'],
			'form'       => $form[ 'ID'],
			'url'        => \Caldera_Forms_API_Util::url( 'processors/edd/discount' ),
			'id_attr'    => \Caldera_Forms_Field_Util::get_base_id( $field, null, $form ),
			'price_field' => $this->get_price_field( $field, $form ),
			'form_count' =>  \Caldera_Forms_Render_Util::get_current_form_count(),
		];

		$config = array_merge( $config, nonces::create() );

		return $config;
	}

	/**
	 * Get the price field
	 *
	 * @since 1.1.0
	 *
	 * @param array $field Config for discount code field
	 * @param array $form Form config
	 *
	 * @return bool|mixed
	 */
	protected function get_price_field( $field, $form ){
		if( ! empty( $field[ 'config' ][ 'price_field' ] ) ){
			$field = cf_edd_pro_find_by_magic_slug( $field[ 'config' ][ 'price_field' ], $form  );
			return $field;
		}
		return false;
	}

}