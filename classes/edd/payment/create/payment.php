<?php
/**
 * Base class for payment creation classes
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */


namespace calderawp\cfedd\edd\payment\create;


use calderawp\cfedd\edd\create\payment\regular;

abstract  class payment {

	/**
	 * ID of created payment
	 *
	 * @since 0.0.1
	 *
	 * @var int
	 */
	protected $payment_id;



	/**
	 * Payment Object
	 *
	 * @since 0.0.1
	 *
	 * @var \calderawp\cfedd\edd\payment\payment;
	 */
	protected $payment;


	/**
	 * Custom payment gateway ID
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	const GATEWAY = 		'cf_edd_pro';


	/**
	 * Get created payment ID
	 *
	 * @since 0.0.1
	 *
	 * @return int
	 */
	public function get_payment_id(){
		return $this->payment_id;
	}

	/**
	 * Get created payment
	 *
	 * @since 0.0.1
	 *
	 * @return \calderawp\cfedd\edd\payment\payment
	 */
	public function get_payment(){
		if( ! $this->payment && is_numeric( $this->payment_id ) ){
			$this->payment = new \calderawp\cfedd\edd\payment\payment( $this->payment_id );
		}
		return $this->payment;
	}

	/**
	 * Setup payment object
	 *
	 * @since 0.1.0
	 *
	 * @param $total
	 * @param array $downloads Optional. Array of download IDs
	 * @param $payment_details
	 * @param string $status
	 *
	 * @return \calderawp\cfedd\edd\payment\payment
	 */
	public function setup_payment( $total, array $downloads = [], $payment_details, $status = 'pending' ){
		$payment        = new \calderawp\cfedd\edd\payment\payment();

		$payment->gateway = self::GATEWAY;
		$payment->status = $status;


		foreach( [
			'user_id' => get_current_user_id(),
			'customer_id' => 0,
			'ip' => caldera_forms_get_ip(),
			'has_unlimited_downloads' => true,
			'user_info' => false
		] as $field => $default ){
			if( 'user_info' == $field && empty( $payment_details[ 'user_info' ] ) ){
				$payment_details[ 'user_info' ] = $this->user_info_from_id( $payment->user_id );
			}
			if( ! empty( $payment_details[ $field ] ) ){
				$payment->$field = $payment_details[ $field ];
			}else{
				$payment->$field = $default;
			}

		}

		foreach( [
			'email',
			'first_name',
			'last_name'
		] as $field ){
			if( isset( $payment->user_info[ $field ] ) ){
				$payment->$field = $payment->user_info[ $field ];
			}

		}


		if( ! $payment->customer_id ){
			$customer = $this->find_customer( $payment->user_id, $payment->email );
			if( is_object( $customer ) ){
				$payment->customer_id = $customer->id;
			}
		}

		if ( ! empty( $downloads ) ) {
			foreach ( $downloads as $download ) {

				/**
				 * Filter arguments passed to EDD_Payment->add_download()
				 *
				 * @since 0.0.2
				 */
				$args = apply_filters( 'cf_edd_pro_add_download_to_payment_args', [], $downloads );
				$payment->add_download( trim( $download ), $args );
			}
		}


		$payment->total = edd_sanitize_amount( $total );
		$payment->subotal = edd_sanitize_amount( $total );

		return $payment;
	}

	/**
	 * Save payment
	 *
	 * Sets payment_id property
	 *
	 * @since 0.0.1
	 *
	 * @param \calderawp\cfedd\edd\payment\payment $payment
	 *
	 * @return \calderawp\cfedd\edd\payment\payment
	 */
	public function save_payment( \calderawp\cfedd\edd\payment\payment $payment ) {

		/**
		 * Change EDD payment directly before it is saved
		 *
		 * @since 0.0.1
		 *
		 * @param \EDD_Payment Payment object
		 * @param bundle|regular Object of class creating payment
		 */
		$payment = apply_filters( 'cf_edd_pro_pre_save_payment', $payment, $this );
		$payment->save();

		$this->payment = $payment;
		$this->payment_id = $payment->ID;
		return $payment;

	}

	/**
	 * Checks if payment is object and not WP_Error
	 *
	 * @since 0.0.1
	 *
	 * @param  \EDD_Payment $payment
	 *
	 * @return bool
	 */
	protected function validate_payment_object( $payment ){
		return is_object( $payment ) && ! is_wp_error( $payment );
	}

}