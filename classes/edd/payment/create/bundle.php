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

namespace calderawp\cfedd\edd\create\payment;




use calderawp\cfedd\edd\object\user_meta;
use calderawp\cfedd\init;

class bundle extends payment {



	/**
	 * Bundle DB abstraction
	 *
	 * @since 0.0.1
	 *
	 * @var \calderawp\cfedd\edd\bundle
	 */
	protected $bundle;

	/**
	 * The bundle ID
	 *
	 * @since 0.0.1
	 *
	 * @var int
	 */
	protected $bundle_id;


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
		$this->bundle_id = $bundle_id;
		$payment = $this->setup_payment( $total, [ $bundle_id ], $payment_details );
		if( $this->validate_payment_object( $payment ) ){
			$this->save_payment( $payment );
			$this->add_to_user_meta();
		}
		$this->set_bundle_contents( $payment, $bundle_id, $bundled_downloads);

	}

	/**
	 * Get created payment ID
	 *
	 * @since 0.0.1
	 *
	 * @return int
	 */
	public function get_payment_id(){
		return $this->bundle->get_payment()->get_ID();
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

	/**
	 * Set up our bundle abstraction
	 *
	 * @since 0.0.1
	 *
	 * @param \EDD_Payment $payment Payment object
	 * @param int $bundle_id Bundle ID
	 * @param array $bundled_downloads Array of downloads IDs to add
	 */
	public function set_bundle_contents( \EDD_Payment $payment, $bundle_id, array  $bundled_downloads ){
		if( is_object( $download = get_post( $bundle_id ) ) && 'download' == get_post_type( $download ) ){
			$this->bundle = new \calderawp\cfedd\edd\bundle(  new \EDD_Download( $download ), $payment );
			if( ! empty( $bundled_downloads ) ){
				foreach ( array_values ($bundled_downloads  ) as $i => $download ){
					$save = false;
					if( $i + 1 == count( $bundled_downloads ) ){
						$save = true;
					}
					$this->bundle->add_download_to_bundle( $download, $save  );
				}

			}

		}

	}

	/**
	 * Track payment in meta for user
	 *
	 * @since 1.5.0
	 *
	 * @param \WP_User|null $user Optional. User who is making purchase. Default is current user.
	 */
	public function add_to_user_meta( \WP_User $user = null ){

		$meta_tracker = init::get_instance()->get_meta_tracker( $user );
		$meta_tracker->add_bundle(  $this->bundle_id, $this->payment_id );

	}



}