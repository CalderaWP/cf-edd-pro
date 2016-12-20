<?php
/**
 * Create a non-bundle payment
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

namespace calderawp\cfedd\edd\create\payment;

class regular extends payment {

	/**
	 * @var \EDD_Payment
	 */
	protected $payment;

	/**
	 * regular constructor.
	 *
	 * @since 0.0.1
	 *
	 * @param  string|float $total Total charge
	 * @param array $downloads Downloads in payment
	 * @param array $payment_details Optional. Payment details
	 */
	public function __construct( $total, $downloads = [], array $payment_details = []) {
		$this->payment = $this->setup_payment( $total, $downloads, $payment_details );
		if( is_object( $this->payment ) && ! is_wp_error( $this->payment ) ){
			$this->payment = $this->save_payment( $this->payment );
		}
	}

	/**
	 * Change status of created payment
	 *
	 * @since 0.0.1
	 *
	 * @param string $status
	 */
	public function change_payment_status( $status = 'complete' ){
		$this->payment->update_status( $status );
	}

	/**
	 * Get ID of created payment
	 *
	 * @since 0.0.1
	 *
	 * @return int
	 */
	public function get_payment_id(){
		if( $this->validate_payment_object( $this->payment ) ){
			return $this->payment->ID;
		}
		return 0;
	}

}