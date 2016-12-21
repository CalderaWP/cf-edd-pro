<?php

/**
 * Load autoloader and initializing class
 */
add_action( 'plugins_loaded', function(){
	include __DIR__ . '/vendor/autoload.php';
	add_filter( 'caldera_forms_pre_load_processors', function() {
		\calderawp\cfedd\cf\init\bundler::create_processor();
		\calderawp\cfedd\cf\init\payment::create_processor();
	});

	add_action( 'caldera_forms_admin_init', function(){
		new \calderawp\cfedd\cf\populate\admin();

	});

	add_action( 'caldera_forms_core_init', function(){
		( new  \calderawp\cfedd\cf\populate\query() )->add_hooks();
	});
}, 2 );
