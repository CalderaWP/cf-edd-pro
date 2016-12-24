<?php
/**
 * Bundle builder processor
 *
 * @package CF_EDD_Pro
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

namespace calderawp\cfedd\cf;




use calderawp\cfedd\edd\payment\create\bundle;

class bundler extends processor {

	/**
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public function pre_processor( array $config, array $form, $proccesid ) {
		$this->set_data_object_initial( $config, $form );
		$errors = $this->data_object->get_errors();
		if ( ! empty( $errors ) ) {
			return $errors;
		}
		$downloads = $this->prepare_bundle( $config, $form );
		$this->setup_transata( $proccesid );
		$this->add_downloads_to_transdata( $downloads, $proccesid );

		$errors = $this->data_object->get_errors();
		if ( ! empty( $errors ) ) {
			return $errors;
		}
	}

	/**
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public function processor( array $config, array $form, $proccesid ) {


		$return = [];
		$downloads = $this->get_downloads_from_transdata( $proccesid );
		if( ! empty( $downloads ) ){
			$bundle_id = $this->data_object->get_value( 'cf-edd-bundle-id' );
			$this->add_bundle_id_to_transdata( $bundle_id, $proccesid );
			$total = $this->get_price_from_transdata( $proccesid );
			if ( ! $total ) {
				$total = $this->data_object->get_value( 'cf-edd-pro-total' );
			}

			$create = new bundle( $total, $bundle_id, $downloads );
			$this->add_payment_id_to_transdata( $create->get_payment_id(), $proccesid );
			global  $transdata;
			$return = $this->prepare_return( $create->get_payment(), $transdata, $proccesid );


		}

		return $return;

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
		$downloads = array_filter( $downloads );

		$min = $this->data_object->get_value( 'cf-edd-pro-min' );
		$max = $this->data_object->get_value( 'cf-edd-pro-max' );
		if( empty( $min ) || ! is_numeric( $min ) ){
			$min = count( $config[ 'group' ] );
		}
		if( empty( $max ) || ! is_numeric( $max ) ){
			$max = count( $config[ 'group' ] );
		}


        if( false == apply_filters( 'cf_edd_pro_bypass_min_check', false, $form  ) && $min > count( $downloads ) ){
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

		if( false == apply_filters( 'cf_edd_pro_bypass_max_check', false, $form  ) && $max > count( $downloads ) ){
			/**
			 * Change error message for when there are too many downloads in bundle
			 *
			 * @since 0.0.1
			 *
			 * @param string $message Error message to show
			 * @param array $config Processor config
			 * @param array $form Form Config
			 */
			$this->data_object->add_error( apply_filters( 'cf_edd_pro_bundle_too_many_error', __( 'Too many downloads downloads added to bundle', 'cf-edd-pro' ), $config, $form ) );

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