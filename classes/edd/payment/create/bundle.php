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





}