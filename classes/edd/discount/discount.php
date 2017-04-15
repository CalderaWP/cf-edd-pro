<?php


namespace calderawp\cfedd\edd\discount;


/**
 * Class discount
 * @package calderawp\cfedd
 */
class discount extends \EDD_Discount {

	/**
	 * Create instance by code
	 *
	 * @since 1.1.0
	 *
	 * @param $code
	 *
	 * @return static
	 */
	public static function factory( $code ){
		if( is_numeric( $code ) ){
			return new static( $code, false );
		}
		return new static( $code, true );
	}

	/**
	 * Check if code is valid
	 *
	 * @param array $items
	 * @param null $user
	 * @param null $price
	 *
	 * @return bool|\WP_Error
	 */
	public function check_valid( array $items = [], $user = null, $price = null ){
		if ( ! $this->get_ID() ) {
			return new \WP_Error( 'not-found', __( 'Discount code not found.', 'cf-edd-pro' ) );
		}

		if( null === $user ){
			$user = get_user_by( 'ID', get_current_user_id() );
			if( $user ){
				$user = $user->user_email;
			}
		}

		if( ! is_email( $user ) ){
			$user = null;
		}

		if( $this->is_used( $user, false ) ){
			return new \WP_Error( 'used', __( 'This discount has already been redeemed.', 'cf-edd-pro' ) );
		}

		if ( ! $this->is_started( false ) ) {
			return new \WP_Error( 'not-started', __( sprintf( 'Discount code can not be used until.', $this->start ), 'cf-edd-pro' ) );
		}

		if ( $this->is_expired( false ) ) {
			return new \WP_Error( 'expired', __( 'Discount code is expired.', 'cf-edd-pro' ) );
		}


		if ( $this->is_maxed_out( false ) ) {
			return  new \WP_Error( 'maxed-out', __( 'Discount has been used too many times.', 'cf-edd-pro' ) );
		}

		if ( ! $this->check_price( $price ) ) {
			return new \WP_Error( 'min-price-not-met', __( 'Discount minimum price not met.', 'cf-edd-pro' ) );
		}

		if ( ! $this->check_requirements( $items ) ) {
			return  new \WP_Error( 'requirements-not-met', __( 'Discount requirements are not met.', 'cf-edd-pro' )  );
		}

		return true;

	}

	/**
	 * Check discount against an array of Download IDs
	 *
	 * @since 1.1.0
	 *
	 * @param string $user Optional. User ID or email to check against
	 * @param array $items Optional. Items to check requirements against
	 * @param float $price Optional. Price to check against
	 * @param bool $update Optional. If code should be updated on is_active() check. Default is false
	 *
	 * @return bool
	 */
	public function _check_valid($user = '', array $items = [], $price = 0.00, $update = false ){
		$set_error = false;
		$return = false;
		$user = trim( $user );

		if (
			$this->is_active( $update, $set_error ) &&
			$this->is_started( $set_error ) &&
			! $this->is_maxed_out( $set_error ) &&
			! $this->is_used( $user, $set_error ) &&
			$this->check_price( $price ) &&
			$this->check_requirements( $items )
		) {
			$return = true;
		}

		/**
		 * This is copied from EDD_Discount::is_valid()
		 *
		 * @param bool   $return If the discount is used or not.
		 * @param int    $ID     Discount ID.
		 * @param string $code   Discount code.
		 * @param string $user   User info.
		 */
		return (bool) apply_filters( 'edd_is_discount_valid', $return, $this->ID, $this->code, $user  );
	}

	/**
	 * Check if an array of download IDs matches requirements
	 *
	 * @since 1.1.0
	 *
	 * @param array $items Optional. Array of IDs to check against.
	 *
	 * @return bool
	 */
	public function check_requirements( array $items = [] ){

		$product_reqs = $this->get_product_reqs();
		$excluded     = $this->excluded_products;
		$return       = false;
		if ( empty( $product_reqs ) && empty( $excluded ) ) {
			$return = true;
		}

		$product_reqs = array_map( 'absint', $product_reqs );
		asort( $product_reqs );
		$product_reqs = array_values( $product_reqs );

		$excluded = array_map( 'absint', $excluded );
		asort( $excluded );
		$excluded = array_values( $excluded );
		if ( ! $return && ! empty( $product_reqs ) ) {
			switch ( $this->product_condition ) {
				case 'all' :
					// Default back to true
					$return = true;
					foreach ( $product_reqs as $download_id ) {
						if ( ! in_array( $download_id, $items ) ) {
							$return = false;
							break;
						}

					}
					break;
				default :
					foreach ( $product_reqs as $download_id ) {
						if ( in_array( $download_id, $product_reqs ) ) {
							$return = true;
							break;
						}

					}
					break;
			}

		} else {
			$return = true;
		}

		if ( ! empty( $excluded ) ) {
			foreach ( $excluded as $download_id ) {
				if ( in_array( $download_id, $items ) ) {
					$return = false;
				}
			}
		}

		/**
		 * Copied from EDD_Discount::is_product_requirements_met()
		 *
		 * @param bool $return Are the product requirements met or not.
		 * @param int $ID Discount ID.
		 * @param string $product_condition Product condition.
		 */
		return (bool) apply_filters( 'edd_is_discount_products_req_met', $return, $this->ID, $this->product_condition );

	}

	/**
	 * Check price requirements
	 *
	 * @param $price
	 *
	 * @return bool
	 */
	public function check_price( $price ){

		$return = false;
		if ( (float) $price >= (float) $this->min_price ) {
			$return = true;
		}


		/**
		 * Copied from EDD_Discount::is_min_price_met()
		 *
		 * @param bool $return Is the minimum cart amount met or not.
		 * @param int  $ID     Discount ID.
		 */
		return (bool) apply_filters( 'edd_is_discount_min_met', $return, $this->ID );

	}

}





