<?php
/**
 * Setup EDD auto-populate option in admin
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

namespace calderawp\cfedd\cf\populate;


class admin extends  \Caldera_Forms_Admin_APSetup {


	/**
	 * @inheritdoc
	 */
	public function add_type() {

		printf( '<option value="edd"{{#is auto_type value="edd"}} selected="selected"{{/is}}>%s</option>', esc_html__( 'Easy Digital Downloads', 'cf-eddpro' ) );

	}

	/**
	 * @inheritdoc
	 */
	public function add_options() {
		return '<p class="description">' . esc_html__( 'Will use all downloads', 'cf-eddpro' ) . '</p>';

	}

}