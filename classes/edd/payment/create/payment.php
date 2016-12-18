<?php
/**
 * Created by PhpStorm.
 * User: josh
 * Date: 12/17/16
 * Time: 11:18 PM
 */

namespace calderawp\cfedd\edd\create\payment;


abstract  class payment {

	/**
	 * @var int
	 */
	protected $payment_id;

	/**
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

		return $payment;
	}

	public function save_payment( \EDD_Payment $payment ) {

		$payment->save();

		$this->payment_id = $this->payment->ID;
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