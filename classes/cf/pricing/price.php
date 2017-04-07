<?php
/**
 * Calculates EDD Bundle Dynamic Pricing
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

namespace calderawp\cfedd\cf\pricing;


use calderawp\eddBundleUpdates\handlers\email;

class price {

	/**
	 *
	 * @since 0.0.2
	 *
	 * @var array
	 */
	protected  $bundler_config;

	/**
	 *
	 * @since 0.0.2
	 *
	 * @var array
	 */
	protected $pricer_config;

	protected $form;

	/**
	 * price constructor.
	 *
	 * @since 0.0.2
	 *
	 * @param array $bundler_config
	 * @param array $pricer_config
	 * @param $form
	 */
	public function __construct( array  $bundler_config, array $pricer_config, $form ) {
		$this->bundler_config = $bundler_config;
		$this->pricer_config = $pricer_config;
		$this->form = $form;
	}

	/**
	 *
	 * @since 0.0.2
	 *
	 * @param null $count
	 *
	 * @return mixed|void
	 */
	public function do_math( $count = null ){
		/**
		 * Set dynamic pricing BEFORE math calculation is run
		 *
		 * If return is null rules are followed for math, if not, value is used with no math.
		 *
		 * @since 0.0.2
		 *
		 * @param string|float|int|null $price The calculated price
		 * @param int $count Number of downloads
		 * @param array $form Current form config
		 */
		$early_price = apply_filters( 'cf_edd_pro_pricer_pre_math', null, $count, $this->form );
		if( ! is_null( $early_price ) ){
			return $early_price;
		}

		$price = 0;
		if (  is_null( $count ) && ! empty( $this->bundler_config[ 'group' ] ) ) {
			$downloads = [ ];
			foreach ( $this->bundler_config[ 'group' ] as $field ) {
				$field_id = cf_edd_pro_find_by_magic_slug( $field[ 'download' ], $this->form );
				$_download = \Caldera_Forms::get_field_data( $field_id, $this->form );
				if ( is_numeric( $_download ) && 'download' == get_post_type( $_download ) ) {
					$downloads[] = $_download;
				}
			}

			$count = count( $downloads );
		}else{
			$count = absint( $count );
		}


		if ( ! empty( $this->pricer_config[ 'group' ] ) ) {
			$found = false;
			foreach ( $this->pricer_config[ 'group' ] as $rule ) {
				if( $count == $rule[ 'num_downloads' ] ){
					$price = $rule[ 'cost' ];
					$found = true;
					break;
				}
			}

			if( ! $found ){
				end( $this->pricer_config );
				$key = key( $this->pricer_config[ 'group'] );
				if (  ! empty( $this->pricer_config[ 'group' ][ $key ] ) ) {
					$price = $this->pricer_config[ 'group' ][ $key ][ 'cost' ];
				}else{
					$price = 0;
				}
			}

		}

		/**
		 * Filter dynamic pricing after math calculation is run
		 *
		 * @since 0.0.2
		 *
		 * @param string|float|int $price The calculated price
		 * @param int $count Number of downloads
		 * @param array $form Current form config
		 */
		return apply_filters( 'cf_edd_pro_pricer_math', $price, $count, $this->form );

	}


}