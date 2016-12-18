<?php
/**
Plugin name: Caldera Forms EDD Pro
 */


add_action( 'plugins_loaded', 'cf_edd_pro_init' );
function cf_edd_pro_init(){
	include  dirname( __FILE__ ) . '/bootstrap.php';
}
