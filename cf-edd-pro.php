<?php
/**
Plugin name: Caldera Forms EDD Pro
 */


define( 'CF_EDD_PRO_VER', '0.).1' );
define( 'CF_EDD_PRO_URL',     plugin_dir_url( __FILE__ ) );
define( 'CF_EDD_PRO_PATH',    dirname( __FILE__ ) . '/' );
define( 'CF_EDD_PRO_CORE',    dirname( __FILE__ )  );

add_action( 'plugins_loaded', 'cf_edd_pro_init', 0 );
function cf_edd_pro_init(){
	include  dirname( __FILE__ ) . '/bootstrap.php';
}
