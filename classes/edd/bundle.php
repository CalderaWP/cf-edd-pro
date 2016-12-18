<?php
/**
 * DB abstraction for custom bundles by payment
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
namespace calderawp\cfedd\edd;


class bundle {

	/**
	 * The name of the meta key for payments that stores custom bundle contents
	 *
	 * @since 0.0.1
	 */
	const METAKEY = '_cdedd_custom_bundle_contents';

	/**
	 * The download for bundle
	 *
	 * @since 0.0.1
	 *
	 * @var \EDD_Download
	 */
	protected $download;

	/**
	 * The payment to get/save custom contents to
	 *
	 * @since 0.0.1
	 *
	 * @var \EDD_Download
	 */
	protected $payment;

	/**
	 *  The custom bundle contents
	 *
	 * @since 0.0.1
	 *
	 * @var array
	 */
	protected $bundled_downloads;

	/**
	 * Object that controls filters
	 *
	 * @since 0.0.1
	 *
	 * @var filter
	 */
	protected $filterer;

	/**
	 * bundle constructor.
	 *
	 * @since 0.0.1
	 *
	 * @param \EDD_Download $download The download object representation of custom bundle
	 * @param \EDD_Payment $payment Payment this object is related to
	 */
	public function __construct( \EDD_Download $download, \EDD_Payment $payment ) {
		$this->download = $download;
		$this->payment = $payment;
	}

	/**
	 * Add filters so EDD_Download shows right bundle contents
	 *
	 * @since 0.0.1
	 */
	public function add_filters(){
		$this->filterer = new filter( $this->download->ID, $this->get_bundled_downloads() );
		$this->filterer->add_hooks();
	}

	/**
	 * Remove filters
	 *
	 * @since 0.0.1
	 *
	 */
	public function remove_filters(){
		if( null != $this->filterer ){
			$this->filterer->remove_hooks();
		}
	}

	/**
	 * Add one download to this custom bundle
	 *
	 * @param int $id Download ID
	 * @param bool $save Optional. If true, change is stored. Default is true.
	 *
	 * @return bool
	 */
	public function add_download_to_bundle( $id, $save = true ){
		if ( is_numeric( $id ) && is_object( $download = get_post( $id ) ) && 'download' === $download->post_type ) {
			$this->get_bundled_downloads();
			$this->bundled_downloads[] = $id;
			if ( $save ) {
				$this->save_bundled_downloads();
			}

			return true;
		}

		return false;

	}

	/**
	 * Get the bundled downloads
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function get_bundled_downloads(){
		if( ! is_array( $this->bundled_downloads ) ){
			$this->get_saved_bundled_downloads();
		}

		return $this->bundled_downloads;
	}

	/**
	 * Save the downloads for this custom beta
	 *
	 * @since 0.0.1
	 */
	protected function save_bundled_downloads(){
		$this->payment->update_meta( self::METAKEY, $this->bundled_downloads  );
	}

	/**
	 * Get the saved bundles
	 *
	 * @since 0.0.1
	 *
	 * @return array|mixed
	 */
	protected function get_saved_bundled_downloads(){
		$saved = $this->payment->get_meta( self::METAKEY );
		if( empty( $saved ) ){
			return [];
		}
		return $saved;

	}

}