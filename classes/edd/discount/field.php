<?php


namespace calderawp\cfedd\edd\discount;
use calderawp\eddBundleUpdates\handlers\email;


/**
 * Class field
 * @package calderawp\cfedd\edd\discount
 */
class field {

	/** @var  float */
	protected $price;

	/** @var string  */
	protected $slug = 'edd-discount';

	public function add_hooks(){
		add_filter( 'caldera_forms_get_field_types', [ $this, 'register' ] );
		add_filter( 'cf_edd_pro_discount_api_permissions', [ $this, 'api_permissions' ], 10, 2 );
		add_filter( 'caldera_forms_field_attributes', [ $this, 'filter_attrs' ], 10, 3 );
		//add_filter( 'cf_edd_pro_payment_total', [ $this, 'filter_price' ], 10, 3 );
	}

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

	public function filter_attrs( $attrs, $field, $form ){
		if( $this->slug == \Caldera_Forms_Field_Util::get_type( $field, $form ) ){
			$attrs['data-' . $this->slug ] = wp_json_encode( $this->config( $form, $field ) );

		}

		return $attrs;


	}


	/**
	 * @param string $value The field value
	 * @param array $field Field config
	 * @param array $form Form config
	 *
	 * @return float|mixed;
	 */
	public function handler( $value, $field, $form){
		$discount = discount::factory( $value );
		if( ! $discount->get_ID() ){
			return $value;
		}
		$price_field = $this->get_price_field( $field, $form );
		$price = \Caldera_Forms::get_field_data( $price_field, $form );
		if( $discount->check_valid( get_current_user_id() ) ) {
			$this->price = $value = round( $discount->get_discounted_amount( $price ) );
			\Caldera_Forms::set_field_data( $price_field, $value, $form );
		}

		return $value;
	}

	public function filter_price(  $total, $config, $form ){

		if( ! is_null( $this->price ) ){
			return $this->price;
		}
		return $total;
	}

	public function api_permissions( $allowed, $request ){
		if( ! empty( $request[ 'nonce'] ) && ! $allowed ){
			$allowed = wp_verify_nonce( $request[ 'nonce' ], $this->nonce_action( ) );

		}

		if( ! $allowed ){
			$allowed = wp_verify_nonce( 'wp_rest', $request[ '_wpnonce' ] );
		}

		return $allowed;
	}

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

	protected function nonce_action( ){
		return nonces::nonce_action();
	}

	/**
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

	protected function get_price_field( $field, $form ){
		if( ! empty( $field[ 'config' ][ 'price_field' ] ) ){
			$field = cf_edd_pro_find_by_magic_slug( $field[ 'config' ][ 'price_field' ], $form  );
			return $field;
		}
		return false;
	}

}