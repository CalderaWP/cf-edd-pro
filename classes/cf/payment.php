<?php
/**
 * EDD payment processor
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */


namespace calderawp\cfedd\cf;



use calderawp\cfedd\edd\payment\create\regular;

class payment extends processor {

	/**
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public function pre_processor( array $config, array $form, $proccesid ) {
		$this->set_data_object_initial( $config, $form );
		if ( ! empty( $errors ) ) {
			return $errors;
		}



		if ( ! empty( $errors ) ) {
			return $errors;
		}

	}

	/**
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public function processor( array $config, array $form, $proccesid ) {
		$total = $this->get_price_from_transdata( $proccesid );
		if ( ! $total ) {
			$total = $this->data_object->get_value( 'cf-edd-pro-payment-total' );
		}

		if( 'on' == $this->data_object->get_value( 'cf-edd-use-bundle-builder' ) ){
			$bundler = true;
			$download_id = $this->get_bundle_id_from_transdata( $proccesid );
			$payment_id = $this->get_payment_id_from_transdata( $proccesid );
			if( false == $download_id ){
				$this->data_object->add_error( __( 'EDD Payment processor could not detect bundle properly (download id)', 'cf-edd-pro' ) );
			}
			if( false == $payment_id ){
				$this->data_object->add_error( __( 'EDD Payment processor could not detect bundle properly (payment id)', 'cf-edd-pro' ) );
			}
		}else{
			$bundler = false;
			$download_id = $this->data_object->get_value( 'cf-edd-pro-payment-download' );
			$payment_details = [];
			$payment_id = ( new regular( $total, [ $download_id ], $payment_details ) )->get_payment_id();
			$this->add_payment_id_to_transdata( $payment_id, $proccesid );

		}


		if( $bundler ){
			$payment = new \calderawp\cfedd\edd\payment\payment( $payment_id );
		}else{
			$payment = new \calderawp\cfedd\edd\payment\payment( $payment_id );
		}

		$this->add_payment_id_to_transdata( $payment_id, $proccesid );
		$new_status = $this->data_object->get_value( 'cf-edd-pro-payment-status' );
		if( null == $new_status ){
			$new_status = 'complete';
		}

		if( $new_status !== $payment->status ){
			$payment = $payment->update_status( $new_status );

		}

		if( 'on' === $this->data_object->get_value( 'cf-edd-use-bundle-builder' ) ){
			add_filter( 'caldera_forms_submit_redirect', [ $this, 'redirect_to_payment_details' ], 25, 3 );
		}

		global $transdata;

		/**
		 * Runs after an EDD payment is created using Caldera Forms
		 *
		 * @since 1.1.0
		 *
		 * @param  \calderawp\cfedd\edd\payment\payment Payment Object (extends EDD_Payment)
		 * @param \Caldera_Forms_Processor_Get_Data $data Processor data
		 * @param array $config Processor config
		 * @param array $form Form config
		 */
		do_action( 'cf_cf_edd_pro_payment_created', $payment, $this->data_object, $config, $form );

		return $this->prepare_return( $payment, $transdata, $proccesid );

	}

	/**
	 * Redirect to payment details
	 *
	 * @uses "caldera_forms_submit_redirect" filter
	 *
	 * @since 0.0.1
	 *
	 * @param $url
	 * @param $form
	 * @param $processid
	 *
	 * @return string|void
	 */
	public function redirect_to_payment_details( $url, $form, $processid  ){
		$payment = $this->get_payment_id_from_transdata( $processid );
		$url = esc_url( get_permalink( edd_get_option( 'purchase_history_page', 'purchase-history' ) ) );
		if( is_numeric( $payment ) ){
			$key = edd_get_payment_key( $payment );
			$url = add_query_arg( 'payment_key', $key, $url );
		}

		return $url;

	}
}