<?php
/**
 * Bundle builder processor
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

namespace calderawp\cfedd\cf;


use calderawp\cfedd\edd\create\payment\bundle;

class bundler extends processor {

	/**
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public function pre_processor( array $config, array $form, $proccesid ) {
		$this->set_data_object_initial( $config, $form );
		$downloads = $this->prepare_bundle( $config, $form );
		$errors = $this->data_object->get_errors();
		if ( ! empty( $errors ) ) {
			return $errors;
		}
		$this->setup_transata( $proccesid );
		$this->add_downloads_to_transdata( $downloads, $proccesid );
	}

	/**
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public function processor( array $config, array $form, $proccesid ) {
		$return = [
			'payment_id' => false,
			'first_name'=> false,
			'last_name'=> false,
			'email'=> false,
			'user_id'=> false,
			'customer_id'=> false,
		];
		$this->setup_transata( $proccesid );
		$downloads = $this->get_downloads_from_transdata( $proccesid );
		if( ! empty( $downloads ) ){
			$bundle_id = $this->data_object->get_value( 'cf-edd-bundle-id' );
			$this->add_bundle_id_to_transdata( $bundle_id, $proccesid );
			$payment = new bundle( $this->data_object->get_value( 'cf-edd-pro-total' ), $bundle_id, $downloads, array() );
			$this->add_payment_id_to_transdata(  $payment->get_payment_id(), $proccesid );

			global  $transdata;

			foreach ( $return as $field ){
				$transdata[ $proccesid ][ 'meta'] [ $this->slug ][ $field ] = $payment->$field;
				$return[ $field ] = $payment->$field;
			}

			return $return;

		}

	}

	/**
	 * Prepare our custom bundle
	 *
	 * @since 0.0.1
	 *
	 * @param array $config
	 * @param $form
	 *
	 * @return array
	 */
	protected function prepare_bundle(  array  $config, $form ){
		$downloads = $this->get_downloads( $config, $form );

		$min = $this->data_object->get_value( 'cf-edd-pro-min' );
		$max = $this->data_object->get_value( 'cf-edd-pro-max' );
		if( empty( $min ) || ! is_numeric( $min ) ){
			$min = count( $config[ 'group' ] );
		}
		if( empty( $max ) || ! is_numeric( $max ) ){
			$max = count( $config[ 'group' ] );
		}
		if( $min < count( $downloads ) ){
			/**
			 * Change error message for when there are not enough downloads are in bundle
			 *
			 * @since 0.0.1
			 *
			 * @param string $message Error message to show
			 * @param array $config Processor config
			 * @param array $form Form Config
			 */
			$this->data_object->add_error( apply_filters( 'cf_edd_pro_bundle_not_enough_error', __( 'Not enough downloads added to bundle', 'cf-edd-pro' ), $config, $form ) );
		}

		if( $max > count( $downloads ) ){
			/**
			 * Change error message for when there are too many downloads in bundle
			 *
			 * @since 0.0.1
			 *
			 * @param string $message Error message to show
			 * @param array $config Processor config
			 * @param array $form Form Config
			 */
			$this->data_object->add_error( apply_filters( 'cf_edd_pro_bundle_too_many_error', __( 'Too many downlaods downloads added to bundle', 'cf-edd-pro' ), $config, $form ) );

		}

		return $downloads;

	}

	/**
	 * Get downloads from submission
	 *
	 * @since 0.0.1
	 *
	 * @param array $config Processor config
	 * @param array $form Form config
	 *
	 * @return array
	 */
	protected function get_downloads( array  $config, $form ){
		$downloads = [];
		if( ! empty( $config[ 'group' ] ) ){
			foreach ( $config[ 'group'  ] as $group_field ){
				if( ! empty( $group_field[ 'download' ] ) ){
					$downloads[] = \Caldera_Forms::do_magic_tags( $group_field[ 'download' ], null, $form );
				}
			}
		}

		return $downloads;

	}

}