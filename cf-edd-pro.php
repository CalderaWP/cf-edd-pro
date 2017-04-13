<?php
/**
Plugin name: Caldera Forms EDD Pro
Version: 1.0.4-b-3
Plugin URI:  https://calderaforms.com/downloads/easy-digital-downloads-for-caldera-forms-pro
Description: Sell Easy Digital Downloads products with Caldera Forms
Author:      Josh Pollock for CalderaWP LLC
Author URI:  https://CalderaForms.com
License:     GPLv2+
Text Domain: cf-edd-pro
Domain Path: /languages
 */


/**
 * Copyright (c) 2016 Josh Pollock for CalderaWP LLC (email : Josh@CalderaWP.com) for CalderaWP LLC
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

define( 'CF_EDD_PRO_VER', '1.0.4-b-3' );
define( 'CF_EDD_PRO_URL',     plugin_dir_url( __FILE__ ) );
define( 'CF_EDD_PRO_PATH',    dirname( __FILE__ ) . '/' );
define( 'CF_EDD_PRO_CORE',    dirname( __FILE__ )  );


// Load instance
add_action( 'plugins_loaded', 'cf_edd_pro_init', 0 );
function cf_edd_pro_init(){
	global $wp_version;
	$edd_version = $cf_version = false;
	$php_check = version_compare( PHP_VERSION, '5.4.0', '>=' );
	$wp_check = version_compare( $wp_version, '4.5', '>=' );
	$edd_installed = defined( 'EDD_VERSION' );
	if ( $edd_installed ) {
		$edd_version = version_compare( '2.5', EDD_VERSION, '<=');
	}
	$cf_installed = defined( 'CFCORE_VER' );
	if( $cf_installed ){
		$cf_version = version_compare( '1.4.7', CFCORE_VER, '<=' );
	}
	if ( ! $php_check  || !  $wp_check || ! $edd_version || ! $cf_version ) {

		//you are not going to space today!
		if ( is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			include_once CF_EDD_PRO_PATH . 'vendor/calderawp/dismissible-notice/src/functions.php';
		}

		if ( is_admin() ) {
			if( $cf_installed ){
				if( ! $cf_version ){
					$message = __( sprintf( 'Caldera Forms For Easy Digital Downloads requires Caldera Forms version 1.4.7 or later. Current version is %1s.', CFCORE_VER ), 'cf-edd-pro' );
					echo caldera_warnings_dismissible_notice( $message, true, 'activate_plugins', 'cf_edd_pro_check_cf' );
				}
			}else{
				$message = __( 'Please activate Caldera Forms to use the Caldera Forms For Easy Digital Downloads add-on.', 'cf-edd-pro' );
				echo caldera_warnings_dismissible_notice( $message, true, 'activate_plugins', 'cf_edd_pro_activate_cf' );
				return;
			}

			if( $edd_installed ){
				if( ! $edd_version ){
					$message = __( sprintf( 'Caldera Forms For Easy Digital Downloads requires EDD version 2.5 or later. Current version is %1s.', EDD_VERSION ), 'cf-edd-pro' );
					echo caldera_warnings_dismissible_notice( $message, true, 'activate_plugins', 'cf_edd_pro_check_edd' );
				}
			}else{
				$message = __( 'Please activate Easy Digital Downloads to use the Caldera Forms For Easy Digital Downloads add-on.', 'cf-edd-pro' );
				echo caldera_warnings_dismissible_notice( $message, true, 'activate_plugins', 'cf_edd_pro_activate_edd' );
				return;
			}

			if( ! $edd_version || ! $cf_version ){
				return;
			}

			if ( ! $php_check ) {
				$message = __( sprintf( 'Caldera Forms For Easy Digital Downloads requires PHP version 5.4 or later. Current version is %1s.', PHP_VERSION ), 'cf-edd-pro' );
				echo caldera_warnings_dismissible_notice( $message, true, 'activate_plugins', 'cf_edd_pro_check_php' );
			}

			if ( ! $wp_check ) {
				$message = __( sprintf( 'Caldera Forms For Easy Digital Downloads requires WordPress version 4.5 or later. Current version is %s.', $wp_version ), 'cf-edd-pro' );
				echo caldera_warnings_dismissible_notice( $message, true, 'activate_plugins', 'ceq_wp_check' );

			}

		}

	}else{
		//bootstrap plugin
		require_once( CF_EDD_PRO_PATH . '/bootstrap.php' );

	}

}
