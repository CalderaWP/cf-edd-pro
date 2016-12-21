<?php
/**
 *  EDD Bundle Dynamic Pricing processor
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
namespace calderawp\cfedd\cf;


use calderawp\cfedd\cf\pricing\factory;

class pricing extends  processor {

	public function pre_processor( array $config, array $form, $proccesid ) {
		$pricer = factory::pricer( $form );
		if( is_object( $pricer ) ){
			$price = $pricer->do_math();
			$price = edd_sanitize_amount( $price );

			$this->add_price_to_transdata( $proccesid, $price );
			$field_id = cf_edd_pro_find_by_magic_slug( $config[ 'cf-edd-pro-dynamic-pricing-price-field' ], $form );
			\Caldera_Forms::set_field_data(  $field_id, $price, $form );
		}
	}

	public function processor( array $config, array $form, $proccesid ) {

	}
}