<?php
/**
 * Create various objects and such for calculating EDD Bundle Dynamic Pricing
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
namespace calderawp\cfedd\cf\pricing;


use calderawp\cfedd\cf\init\bundler;
use calderawp\cfedd\cf\init\pricing;


class factory {

	/**
	 * Create price object for form if possible
	 *
	 * @since 0.2.0
	 *
	 * @param array $form Form config
	 *
	 * @return price
	 */
	public static function pricer( array $form ){
		$bundler = $pricer = [];
		if( ! empty( $form[ 'processors' ] ) ){
			$bundler = self::find_bundler( $form );
			$pricer = self::find_pricer( $form );
		}

		if( ! empty( $bundler ) && ! empty( $pricer ) ){
			return new price( $bundler, $pricer, $form );
		}
	}

	/**
	 * Create API route
	 *
	 * @since 0.2.0
	 *
	 *  @param \Caldera_Forms_API_Load $api
	 */
	public static function create_route( \Caldera_Forms_API_Load $api ){
		$api->add_route( new endpoint() );
	}

	/**
	 * Setup pricing field in front-end
	 *
	 * @since 0.2.0
	 *
	 * @param array $form
	 */
	public static function pricing_field( array $form ){
		if( ! empty( $form[ 'processors' ] ) ){
			$bundler = self::find_bundler( $form );
			$pricer = self::find_pricer( $form );
		}

		if( ! empty( $bundler ) && ! empty( $pricer ) ){
			$price_field = cf_edd_pro_find_by_magic_slug( $pricer[ 'cf-edd-pro-dynamic-pricing-price-field' ], $form );
			if (  $price_field  ) {
				$download_fields = [ ];
				foreach ( $bundler[ 'group' ] as $group ) {
					$download_fields[] = cf_edd_pro_find_by_magic_slug( $group[ 'download' ], $form );

				}

				global $current_form_count;

				foreach ( $download_fields as &$field ){
					$field = $field . '_' . $current_form_count;
				}


				wp_enqueue_script( 'cf-edd-pro', CF_EDD_PRO_URL . '/assets/cf-edd-pro.js', [ 'jquery' ], CF_EDD_PRO_VER );
				$vars = [
					'price_field' => $price_field,
					'download_fields' => $download_fields,
					'api' => esc_url_raw( \Caldera_Forms_API_Util::url( 'processors/edd/pricer' ) ),
					'nonce' => wp_create_nonce( 'wp_rest' ),
					'form_id' => $form[ 'ID']
				];
				wp_localize_script( 'cf-edd-pro', 'CF_EDD_PRO', $vars );
			}


		}





	}

	/**
	 * Find config for bundle builder processor
	 *
	 * @since 0.2.0
	 *
	 * @param array $form Form to search in
	 *
	 * @return array|bool
	 */
	protected static function find_bundler( array  $form ){
		foreach (  $form[ 'processors' ]  as $processor ) {
			if ( bundler::get_slug() == $processor[ 'type' ] ) {
				return $processor[ 'config' ];

			}

		}

		return false;

	}

	/**
	 * Find config for dynamic pricing processor
	 *
	 * @since 0.2.0
	 *
	 * @param array $form Form to search in
	 *
	 * @return array|bool
	 */
	protected static function find_pricer( array  $form ){
		foreach (  $form[ 'processors' ]  as $processor ) {
			if ( pricing::get_slug() == $processor[ 'type' ] ) {
				return $processor[ 'config' ];
			}
		}

		return false;

	}

}