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

	public static function create_route( \Caldera_Forms_API_Load $api ){
		$api->add_route( new endpoint() );
	}

	public static function pricing_field( array $form ){
		if( ! empty( $form[ 'processors' ] ) ){
			$bundler = self::find_bundler( $form );
			$pricer = self::find_pricer( $form );
		}

		if( ! empty( $bundler ) && ! empty( $pricer ) ){
			$price_field = self::find_by_magic_slug( $pricer[ 'cf-edd-pro-dynamic-pricing-price-field' ], $form );
			if (  $price_field  ) {
				$download_fields = [ ];
				foreach ( $bundler[ 'group' ] as $group ) {
					$download_fields[] = self::find_by_magic_slug( $group[ 'download' ], $form );

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

	protected function find_by_magic_slug( $magic_slug, array $form ){
		$slug = str_replace( '%', '', $magic_slug );
		foreach ( $form[ 'fields' ] as $field ){
			if( $slug === $field[ 'slug' ] ){
				return $field[ 'ID' ];
			}
		}
	}

	protected static function find_bundler( array  $form ){
		foreach (  $form[ 'processors' ]  as $processor ) {
			if ( bundler::get_slug() == $processor[ 'type' ] ) {
				return $processor[ 'config' ];

			}

		}
	}

	protected static function find_pricer( array  $form ){
		foreach (  $form[ 'processors' ]  as $processor ) {
			if ( pricing::get_slug() == $processor[ 'type' ] ) {
				return $processor[ 'config' ];
			}
		}

	}
}