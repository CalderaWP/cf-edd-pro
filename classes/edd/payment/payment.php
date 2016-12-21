<?php
/**
 * Provides a fluent interface for the EDD_Payment methods we need.
 *
 * @see https://github.com/easydigitaldownloads/easy-digital-downloads/issues/5310
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

namespace calderawp\cfedd\edd\payment;


class payment extends \EDD_Payment {

	/**
	 * @inheritdoc
	 * @return payment
	 */
	public function save() {
		parent::save();
		return $this;

	}

	/**
	 * @inheritdoc
	 * @return payment
	 */
	public function update_status( $status = false ) {
		if( parent::update_status( $status ) ){
			$this->status = $status;
			$this->post_status = get_post_status( $this->ID );
		}

		return $this;

	}

}