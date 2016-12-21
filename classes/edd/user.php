<?php
/**
 * Sets up filters for current user, so custom bundle contents are correct
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */


namespace calderawp\cfedd\edd;


use calderawp\cfedd\edd\object\user_meta;
use calderawp\cfedd\meta;

class user {

	/**
	 * Meta tracker object for this user
	 *
	 * @since 0.0.1
	 *
	 * @var meta
	 */
	protected $user_meta;

	/**
	 * Bundles to track
	 *
	 * @since 0.0.1
	 *
	 *
	 * @var array
	 */
	protected $bundles;

	/**
	 * user constructor.
	 *
	 * @since 0.0.1
	 *
	 * @param meta $user_meta
	 */
	public function __construct( meta $user_meta ) {
		$this->user_meta = $user_meta;
	}

	/**
	 * Add EDD filters for this user
	 *
	 * @since 0.0.1
	 */
	public function add_filters(){
		$this->maybe_init_bundles();
		if( ! empty( $this->bundles ) ){
			/** @var bundle $bundle */
			foreach ( $this->bundles as $bundle ){
				$bundle->add_filters();
			}

		}

	}

	/**
	 * Remove EDD filters for this user
	 *
	 * @since 0.0.1
	 */
	public function remove_filters(){
		$this->maybe_init_bundles();
		if( ! empty( $this->bundles ) ){
			/** @var bundle $bundle */
			foreach ( $this->bundles as $bundle ){
				$bundle->remove_filters();
			}

		}
	}

	/**
	 * Setup bundle objects for user
	 *
	 * @since 0.0.1
	 */
	protected function get_bundles(){
		$tracked = $this->user_meta->get_custom_bundles();
		if( ! empty( $tracked ) ){
			/** @var user_meta $user_meta */
			foreach ( $tracked as $user_meta ){
				$download = new \EDD_Download( $user_meta->get_download_id() );
				$payment = new \EDD_Payment( $user_meta->get_payment_id() );
				if ( is_a( $download, 'EDD_Download' ) && is_a( $payment, 'EDD_Payment') ) {
					$this->bundles[] = new bundle( $download, $payment );
				}

			}

		}

	}

	/**
	 * If bundles property !isset loads it.
	 *
	 * @since 0.0.1
	 */
	protected function maybe_init_bundles() {
		if ( empty( $this->bundles ) ) {
			$this->get_bundles();
		}
	}

}