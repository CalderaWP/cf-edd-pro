<?php
/**
 * Filters for custom bundles
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
namespace calderawp\cfedd\edd;


class filter {

	/**
	 *
	 *
	 * @since 0.0.1
	 *
	 * @var array
	 */
	protected $bundled_downloads;

	/**
	 *
	 * @since 0.0.1
	 *
	 * @var
	 */
	protected $bundle_id;

	/**
	 * filter constructor.
	 *
	 * @since 0.0.1
	 *
	 * @param $bundle_id
	 * @param array $bundled_downloads
	 */
	public function __construct( $bundle_id, array $bundled_downloads ) {
		$this->bundle_id = $bundle_id;
		$this->bundled_downloads = $bundled_downloads;
	}

	/**
	 * Add the filters
	 *
	 * @since 0.0.1
	 */
	public function add_hooks(){
		add_filter( 'edd_get_bundled_products', [ $this, 'filter_bundled_downloads' ], 50, 2  );
		add_filter( 'edd_get_download_type', [ $this, 'set_bundle' ], 50, 2 );
	}

	/**
	 * Remove the filters
	 *
	 * @since 0.0.1
	 */
	public function remove_hooks(){
		remove_filter( 'edd_get_bundled_products', [ $this, 'filter_bundled_downloads' ], 50 );
		remove_filter( 'edd_get_download_type', [ $this, 'set_bundle' ], 50 );
	}

	/**
	 * Ensure download shows as a bundle type
	 *
	 * @uses "edd_get_download_type" filter
	 *
	 * @since 0.0.1
	 *
	 * @param $type
	 * @param $id
	 *
	 * @return string
	 */
	public function set_type( $type, $id ){
		if( $id == $this->bundle_id ){
			return 'bundle';
		}

		return $type;
	}

	/**
	 * Ensure download shows the right bundle contents
	 *
	 * @since 0.0.1
	 *
	 * @uses "edd_get_bundled_products" filter
	 *
	 * @param $downloads
	 * @param $id
	 *
	 * @return array
	 */
	public function filter_bundled_downloads( $downloads, $id ){
		if( $id == $this->bundle_id ){
			return $this->bundled_downloads;
		}

		return $downloads;
	}

}