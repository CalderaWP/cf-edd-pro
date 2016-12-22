<?php
/**
 * Populate EDD auto-populate fields
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

namespace calderawp\cfedd\cf\populate;


class query extends \calderawp\cfeddfields\fields\populate\query {

	/**
	 * @var array
	 */
	protected $prices;


	/**
	 * @inheritdoc
	 */
	protected function add_price( $field_id, $download_id, $form_id ){
		$download = new \EDD_Download( $download_id );
		if( $download->has_variable_prices() ){
			$price_id = edd_get_default_variable_price( $download_id);
			$prices = $download->get_prices();
			if( isset( $prices[ $price_id ] ) ){
				$price = $prices[ $price_id ][ 'amount' ];
			}else{
				$price = $prices[ key($prices ) ][ 'amount' ];
			}
		}else{
			$price = $download->get_price();
		}

		if( ! isset( $this->prices[ $form_id ] ) ){
			$this->prices[ $form_id ] = [];
		}

		if( ! isset( $this->prices[ $form_id ][ $field_id ] ) ){
			$this->prices[ $form_id ][ $field_id ] = [];
		}

		$this->prices[ $form_id ][ $field_id ][ $download_id ] = $price;

	}



}