<?php
/**
 * Created by PhpStorm.
 * User: josh
 * Date: 12/17/16
 * Time: 10:38 PM
 */

namespace calderawp\cfedd\cf\interfaces;


interface init {


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