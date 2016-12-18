<?php
/**
 * Created by PhpStorm.
 * User: josh
 * Date: 12/17/16
 * Time: 10:43 PM
 */

namespace calderawp\cfedd\edd;





class init {

	public function add_hooks(){
		add_filter( 'caldera_forms_pre_load_processors', function() {
			\calderawp\cfedd\cf\init\bundler::create_processor();
			\calderawp\cfedd\cf\init\payment::create_processor();
		});

	}

}