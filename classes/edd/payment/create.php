<?php
/**
 * Created by PhpStorm.
 * User: josh
 * Date: 12/17/16
 * Time: 6:03 PM
 */

namespace calderawp\cfedd\edd\payment;


use calderawp\cfedd\edd\bundle;
use calderawp\cfedd\edd\payment;

class create {

	const GATEWAY = 		'cf_edd_pro';

	/**
	 * @var bundle
	 */
	protected $bundle;

	protected $bundle_id;

	protected $payment_id;

	public function __construct( $total, $bundle_id, $bundled_downloads, array $payment_details = array() ) {
		$this->bundle_id = $bundle_id;
		$payment = $this->create_payment( $total, $payment_details );
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


	public function create_payment( $total, $payment_details, $status = 'pending' ) {
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

		$payment->add_download( $this->bundle_id, [
			'item_price' => $total
		] );


		if( ! $payment->customer_id ){
			$customer = $this->find_customer( $payment->user_id, $payment->email );
			if( is_object( $customer ) ){
				$payment->customer_id = $customer->id;
			}
		}

		$payment->save();

		$this->payment_id = $this->payment->ID;
		return $payment;

	}

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

	public function set_bundle_contents( \EDD_Payment $payment, $bundle_id, array  $bundled_downloads ){
		if( is_object( $download = get_post( $bundle_id ) ) && 'download' == get_post_type( $download ) ){
			$this->bundle = new bundle(  new \EDD_Download( $download ), $payment );
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



}