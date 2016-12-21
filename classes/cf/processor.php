<?php
/**
 * Caldera Forms EDD base class for processors
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
namespace calderawp\cfedd\cf;



use calderawp\cfedd\edd\payment\payment;

abstract class processor extends \Caldera_Forms_Processor_Processor {

	/**
	 * Magic tags, with defaults
	 *
	 * @since 0.0.1
	 *
	 * @var array
	 */
	const TAGS = [
		'payment_id' => false,
		'first_name'=> false,
		'last_name'=> false,
		'email'=> false,
		'user_id'=> false,
		'customer_id'=> false,
		'total' => false,
		'subtotal' => 0,
		'tax' => 0,
	];

	/**
	 * Sets up array to be returned by processor callback
	 *
	 * @since 0.0.1
	 *
	 * @param \EDD_Payment $payment
	 * @param array $transdata
	 * @param $proccesid
	 *
	 * @return array
	 */
	public function prepare_return( \EDD_Payment $payment, array $transdata,$proccesid ){
		$return = [];
		foreach ( self::TAGS as $field => $default ){
			if( 'payment_id' == $field ){
				$pf = 'ID';
			}else{
				$pf = $field;
			}
			if (  isset( $payment->$pf ) ) {
				$transdata[ $proccesid ][ 'meta' ] [ $this->slug ][ $field ] = $payment->$pf;
				$return[ $field ] = $payment->$pf;
			}else{
				$transdata[ $proccesid ][ 'meta' ] [ $this->slug ][ $field ] = $default;
			}

		}

		add_action( 'caldera_forms_submit_post_process', [ $this, 'add_entry_to_payment_meta' ], 10, 4 );
		return $return;
	}

	/**
	 *  Add downloads to transdata
	 *
	 * @since 0.0.1
	 *
	 * @param array $downloads Download IDs
	 * @param string $processid Current process ID
	 */
	public function add_downloads_to_transdata( array $downloads, $processid  ){
		global $transdata;
		if( ! isset( $transdata[ $processid ]  ) ){
			$transdata[ $processid ] = [];
		}
		$transdata[ $processid ][ 'downloads' ] = $downloads;
	}

	/**
	 *  Get  downloads from transdata
	 *
	 * @since 0.0.1
	 *
	 * @param string $processid Current process ID
	 *
	 * @return array
	 */
	public function get_downloads_from_transdata( $processid ){
		global $transdata;
		if( isset( $transdata[ $processid ] ) && ! empty( $transdata[$processid  ] ) && ! empty(  $transdata[ $processid ][ 'downloads' ] )){
			array_walk( $transdata[ $processid ][ 'downloads' ], 'absint' );
			return $transdata[ $processid ][ 'downloads' ];
		}

		return [];
	}

	/**
	 *  Add downloads to transdata
	 *
	 * @since 0.0.1
	 *
	 * @param string $bundle_id Bundle ID
	 * @param string $processid Current process ID
	 */
	public function add_bundle_id_to_transdata( $bundle_id, $processid  ){
		global $transdata;
		$transdata[ $processid ][ 'bundle' ] = $bundle_id;
	}

	/**
	 *  Get  bundle ID from transdata
	 *
	 * @since 0.0.1
	 *
	 * @param string $processid Current process ID
	 *
	 * @return int
	 */
	public function get_bundle_id_from_transdata( $processid ){
		global $transdata;
		if( isset( $transdata[ $processid ] ) && ! empty( $transdata[$processid  ] ) && ! empty(  $transdata[ $processid ][ 'bundle' ] )){

			return absint( $transdata[ $processid ][ 'bundle' ] );
		}

		return 0;
	}

	/**
	 *  Add payment ID to transdata
	 *
	 * @since 0.0.1
	 *
	 * @param string $payment_id Payment ID
	 * @param string $processid Current process ID
	 */
	public function add_payment_id_to_transdata( $payment_id, $processid  ){
		global $transdata;
		$transdata[ $processid ][ 'payment' ] = $payment_id;
	}

	/**
	 *  Get  payment ID from transdata
	 *
	 * @since 0.0.1
	 *
	 * @param string $processid Current process ID
	 *
	 * @return int
	 */
	public function get_payment_id_from_transdata( $processid ){
		global $transdata;
		if( isset( $transdata[ $processid ] ) && ! empty( $transdata[ $processid  ] ) && ! empty(  $transdata[ $processid ][ 'payment' ] )){

			return absint( $transdata[ $processid ][ 'payment' ] );
		}

		return 0;
	}

	/**
	 * Store price in transdata
	 *
	 * @since 0.0.2
	 *
	 * @param $price
	 * @param $processid
	 */
	public function add_price_to_transdata( $price, $processid ){
		global $transdata;
		$transdata[ $processid ][ 'price' ] = $price;
	}

	/**
	 * Get price from transdata
	 *
	 * @since 0.0.2
	 *
	 * @param $processid
	 *
	 * @return mixed
	 */
	public function get_price_from_transdata( $processid ){
		global $transdata;
		if( isset( $transdata[ $processid ] ) && ! empty( $transdata[ $processid  ] ) && ! empty(  $transdata[ $processid ][ 'price' ] )){

			return  $transdata[ $processid ][ 'price' ];
		}
	}

	/**
	 * After submission update EDD Payment meta with entry ID
	 *
	 * @since 0.0.2
	 *
	 * @uses "caldera_forms_submit_post_process"
	 *
	 * @param $form
	 * @param $referrer
	 * @param $process_id
	 * @param $entry_id
	 */
	public function add_entry_to_payment_meta( $form, $referrer, $process_id, $entry_id ){
		$payment = $this->get_payment_id_from_transdata( $process_id );
		if( is_numeric( $payment ) ){
			$payment = new payment( $payment );
			$payment->update_meta( 'caldera_forms_entry_id', $entry_id );
		}
	}


}