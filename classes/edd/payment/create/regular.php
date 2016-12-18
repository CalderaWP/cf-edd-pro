<?php
/**
 * Created by PhpStorm.
 * User: josh
 * Date: 12/17/16
 * Time: 11:30 PM
 */

namespace calderawp\cfedd\edd\create\payment;



class regular extends payment {

	/**
	 * @var \EDD_Payment
	 */
	protected $payment;

	public function __construct( $total, $downloads = [], array $payment_details = []) {
		$this->payment = $this->setup_payment( $total, $downloads, $payment_details );
		if( is_object( $this->payment ) && ! is_wp_error( $this->payment ) ){
			$this->payment = $this->save_payment( $this->payment );
		}
	}

	/**
	 * Change status of created payment
	 *
	 * @param string $status
	 */
	public function change_payment_status( $status = 'complete' ){
		$this->payment->update_status( $status );
	}

	public function get_payment_id(){
		if( $this->validate_payment_object( $this->payment ) ){
			return $this->payment->ID;
		}
		return 0;
	}

}