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
	 * Custom payment gateway ID
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	const GATEWAY = 		'cf_edd_pro';

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
	 * @return \EDD_Payment
	 */
	public function setup_payment( $total, array $downloads = [], $payment_details, $status = 'pending' ){
		$payment        = new \EDD_Payment();
		$payment->total = floatval( $total );
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
				$payment->add_download( trim( $download ) );
			}
		}

		return $payment;
	}

	/**
	 * Save payment
	 *
	 * Sets payment_id property
	 *
	 * @since 0.0.1
	 *
	 * @param \EDD_Payment $payment
	 *
	 * @return \EDD_Payment
	 */
	public function save_payment( \EDD_Payment $payment ) {

		$payment->save();

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