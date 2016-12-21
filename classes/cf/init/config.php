<?php
/**
 * Base class for processor configs
 **
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */


namespace calderawp\cfedd\cf\init;


use calderawp\cfedd\cf\interfaces\init;

abstract  class config implements init{

	/**
	 * The slug for this processor
	 *
	 * @since 0.0.2
	 *
	 * @var string
	 */
	protected static $slug = '';

	/**
	 * Get slug for processor
	 *
	 * @since 0.0.2
	 *
	 * @return string
	 */
	public static function get_slug(){
		return static::$slug;
	}
}