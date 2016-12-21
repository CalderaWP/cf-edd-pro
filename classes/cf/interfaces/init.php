<?php
/**
 * Interface for processor setups
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */


namespace calderawp\cfedd\cf\interfaces;


interface init {

	/**
	 * Create the processor
	 *
	 * @uses "caldera_forms_pre_load_processors" filter
	 *
	 * @since 0.0.1
	 *
	 * @param null $form_id Optional. Form ID - used in admin - will be pulled from URL if not passed.
	 */
	public static function create_processor( $form_id = null );

	/**
	 * Config for processor
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public static function processor_config();

	/**
	 * Processor fields
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public static function processor_fields();

}