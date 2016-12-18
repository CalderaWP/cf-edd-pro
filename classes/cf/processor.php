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



abstract class processor extends \Caldera_Forms_Processor_Processor {


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
		if( isset( $transdata[ $processid ] ) && ! empty( $transdata[$processid  ] ) && ! empty(  $transdata[ $processid ][ 'payment' ] )){

			return absint( $transdata[ $processid ][ 'payment' ] );
		}

		return 0;
	}


}