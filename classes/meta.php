<?php
/**
 * Gets the meta for
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
namespace calderawp\cfedd;


use calderawp\cfedd\edd\object\user_meta;

class meta {

	/**
	 * The user whose meta we are using
	 *
	 * @since 0.0.1
	 *
	 * @var \WP_User
	 */
	protected $user;

	/**
	 * User meta key for tracking user's custom bundles
	 *
	 * @since 0.0.1
	 */
	const BUNDLE_META_KEY = '_cf_edd_custom_bundles';

	/**
	 * meta constructor.
	 *
	 * @since 0.0.1
	 *
	 * @param \WP_User $user User to track meta of
	 */
	public function __construct( \WP_User $user ) {
		$this->user = $user;
	}

	/**
	 * Get ID of user tracked by this object
	 *
	 * @since 1.5.0
	 *
	 * @return int
	 */
	public function get_user_id(){
		return $this->user->ID;
	}

	/**
	 * Add an ID to those owned by user
	 *
	 * @since 0.0.1
	 *
	 * @param int $bundle_id Bundle ID to add
	 * @param int $payment_id Payment ID to add
	 */
	public function add_bundle( $bundle_id, $payment_id ){
		$bundles = $this->get_custom_bundles();
		$value = user_meta::factory( $bundle_id, $payment_id );
		if ( is_object( $value ) ) {
			$bundles[] = $value;
			update_user_meta( $this->user->ID, self::BUNDLE_META_KEY, $bundles );
		}
	}

	/**
	 * Get IDs of custom bundles owned by user
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function get_custom_bundles(){
		$bundles = [];
		$meta = get_user_meta( $this->user->ID, self::BUNDLE_META_KEY );
		if( ! empty( $meta  ) && is_array( $meta )){
			foreach ( $meta as $value  ) {
				if ( $this->validate_meta_value( $value ) ) {
					$bundles[] = $value;
				}
			}
		}else{
			if ( $this->validate_meta_value( $meta ) ) {
				$bundles[] = $meta;
			}
		}

		return $bundles;
	}

	/**
	 * Check that meta value is the correct type of object.
	 *
	 * Will convert stdClass to user_meta object if needed
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $value Value to check
	 *
	 * @return bool|user_meta
	 */
	protected function validate_meta_value( $value ){
		if ( is_object( $value ) ){
			if( is_a( $value, 'stdClass' ) && isset(  $value->download_id, $value->payment_id ) ){
				$v = user_meta::factory( $value->download_id, $value->payment_id );
				if( is_object( $v ) ){
					$value = $v;
				}

			}

			return $value;
		}
	}
}