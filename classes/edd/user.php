<?php
/**
 * Created by PhpStorm.
 * User: josh
 * Date: 12/19/16
 * Time: 11:09 PM
 */

namespace calderawp\cfedd\edd;


use calderawp\cfedd\edd\object\user_meta;
use calderawp\cfedd\meta;

class user {

	protected $user_meta;

	protected $bundles;
	public function __construct( meta $user_meta ) {
		$this->user_meta = $user_meta;
	}

	public function add_filters(){
		if( ! empty( $this->bundles ) ){
			/** @var bundle $bundle */
			foreach ( $this->bundles as $bundle ){
				$bundle->add_filters();
			}

		}

	}

	public function remove_filters(){
		if( ! empty( $this->bundles ) ){
			/** @var bundle $bundle */
			foreach ( $this->bundles as $bundle ){
				$bundle->remove_filters();
			}

		}
	}

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
}