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


}