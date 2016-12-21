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
			$this->add_price_to_transdata( $proccesid, $pricer->do_math() );
		}
	}

	public function processor( array $config, array $form, $proccesid ) {

	}
}