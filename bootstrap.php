<?php

/**
 * Load autoloader and initializing class
 */
add_action( 'plugins_loaded', function(){
	include __DIR__ . '/vendor/autoload.php';

	/** Load payment/bundle/pricing processors */
	add_filter( 'caldera_forms_pre_load_processors', function() {
		\calderawp\cfedd\cf\init\bundler::create_processor();
		\calderawp\cfedd\cf\init\payment::create_processor();
		\calderawp\cfedd\cf\init\pricing::create_processor();
	});

	/** Add EDD auto-population option */
	add_action( 'caldera_forms_admin_init', function(){
		new \calderawp\cfedd\cf\populate\admin();

	});

	/** Add query for EDD auto-population */
	add_action( 'init', function(){
		( new  \calderawp\cfedd\cf\populate\query() )->add_hooks();

	});

	/** Setup dynamic pricing in form */
	add_action( 'caldera_forms_render_start', function( $form ){
		if( is_array( $form ) ){
			\calderawp\cfedd\cf\pricing\factory::pricing_field( $form );
		}
	});

	/** Add REST API endpoint for dynamic pricing */
	add_action( 'rest_api_init', function(){
		//backwards compat for 1.4.x
		//@TODO remove
		if( ! did_action( 'caldera_forms_rest_api_init' ) ){
			add_action( 'rest_api_init', [ 'Caldera_Forms', 'init_rest_api' ], 25 );
		}

		add_action( 'caldera_forms_rest_api_pre_init', function( $api ){
			\calderawp\cfedd\cf\pricing\factory::create_route( $api );
		});
	});

	/**  Add REST API endpoint for discount check*/
	add_action( 'caldera_forms_rest_api_pre_init', function( $api ){
		$api->add_route( new \calderawp\cfedd\edd\discount\endpoint() );
	}, 25 );

	add_action( 'init', function(){
		$discount_field = new \calderawp\cfedd\edd\discount\field();
		$discount_field->add_hooks();
	});

	/** Setup EDD SL integration */
	\calderawp\cfeddfields\setup::add_hooks();

    //software licensing
    add_action( 'admin_init', function(){
        $plugin = array(
            'name'      => 'Easy Digital Downloads For Caldera Forms',
            'slug'      => 'easy-digital-downloads-for-caldera-forms-pro',
            'url'       => 'https://calderaforms.com/',
            'version'   => CF_EDD_PRO_VER,
            'key_store' => 'cf_users_license',
            'file'      => CF_EDD_PRO_CORE
        );

        new \calderawp\licensing_helper\licensing( $plugin );
    } );
}, 2 );


/**
 * Find field ID by magic tag
 *
 * @since 0.2.0
 *
 * @param string $magic_slug Magic tag representation of field slug
 * @param array $form Form to check in
 *
 * @return mixed
 */
function cf_edd_pro_find_by_magic_slug( $magic_slug, array $form ){
	$slug = str_replace( '%', '', $magic_slug );
	foreach ( Caldera_Forms_Forms::get_fields( $form, false ) as $field ){
		if( $slug === $field[ 'slug' ] ){
			return $field[ 'ID' ];
		}
	}
}