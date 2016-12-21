<?php
/**
 * Object abstraction for user meta meta values stored to track bundle/payment IDs toghether
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

namespace calderawp\cfedd\edd\object;


class user_meta {

	/** @var  int */
	protected $payment_id;

	/** @var  int */
	protected $download_id;

	/** @return  int */
	public function get_download_id(){
		return $this->download_id;
	}

	/** @return  int */
	public function get_payment_id(){
		return $this->payment_id;
	}

	/**
	 * Factory, with value validation, for this object
	 *
	 * @since 0.0.1
	 *
	 * @param int $download_id Download ID
	 * @param int $payment_id Payment ID
	 *
	 * @return static
	 */
	public static function factory( $download_id, $payment_id ){
		$obj = new static();
		if( $obj->set_download_id( $download_id ) && $obj->set_payment_id( $payment_id ) ){
			return $obj;
		}
	}

	/**
	 * Set download ID
	 *
	 * @since 0.0.1
	 *
	 * @param int $id Download ID
	 *
	 * @return bool
	 */
	public function set_download_id( $id ){
		if( is_numeric( $id ) ){
			$this->download_id = $id;
			return true;
		}

		return false;
	}

	/**
	 * Set payment ID
	 *
	 * @since 0.0.1
	 *
	 * @param int $id payment ID
	 *
	 * @return bool
	 */
	public function set_payment_id( $id ){
		if( is_numeric( $id ) ){
			$this->payment_id = $id;
			return true;
		}

		return false;
	}
}