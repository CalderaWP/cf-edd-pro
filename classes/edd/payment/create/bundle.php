<?php
/**
 * Create a custom bundles payment
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

namespace calderawp\cfedd\edd\payment\create;




use calderawp\cfedd\edd\object\user_meta;
use calderawp\cfedd\init;

class bundle extends payment {

	/**
	 * bundle constructor.
	 *
	 * @since 0.0.1
	 *
	 * @param string|float $total Total price
	 * @param int $bundle_id Download ID of the custom bundle
	 * @param array $bundled_downloads IDs of downloads to add to bundle
	 * @param array $payment_details Payment details to save
	 */
	public function __construct( $total, $bundle_id, $bundled_downloads, array $payment_details = array() ) {
		$this->bundle_id = absint(  trim ( $bundle_id ) );
		$payment = $this->setup_payment( $total, array_merge( [ $this->bundle_id ], $bundled_downloads ), $payment_details );
		if( $this->validate_payment_object( $payment ) ){
			$this->save_payment( $payment );
		}

	}


	/**
	 * Get necessary user info by user ID
	 *
	 * @since 0.0.1
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public function user_info_from_id( $id ){
		$user = get_user_by( 'ID', $id );
		if( $user ){
			return [
				'first_name' => $user->user_firstname,
				'last_name'  => $user->user_lastname,
				'email' => $user->user_email,
			];
		}

	}

	/**
	 * Find EDD customer by user ID or email
	 *
	 * @since 0.0.1
	 *
	 * @param int $user_id User ID
	 * @param string $email Email
	 *
	 * @return bool|\EDD_Customer
	 */
	public function find_customer( $user_id = 0, $email = '' ){
		if( $user_id && is_object( $user = get_user_by( 'ID', $user_id ) )){
			$_customer = new \EDD_Customer( $user_id, true );
			if( $_customer ){
				return $_customer;
			}

		}

		$_customer = new \EDD_Customer( $email, false );

		if( $_customer ){
			return $_customer;
		}

		return false;
	}



}