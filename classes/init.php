<?php
/**
 * Initialize plugin
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

namespace calderawp\cfedd;





use calderawp\cfedd\edd\filter;
use calderawp\cfedd\edd\user;

class init {

	/**
	 * @var static
	 */
	protected static $instance;

	/**
	 * @var meta
	 */
	protected $meta_tracker;

	/**
	 * @var filter
	 */
	protected $edd_filter;

	/**
	 * Get instance
	 */
	public static function get_instance(){
		if( null == static::$instance ){
			static::$instance = new static();
		}

		return static::$instance;

	}

	/**
	 * @return meta|null
	 */
	public function get_meta_tracker( \WP_User $user = null ){
		if( is_object( $user ) ){
			if( null != $this->meta_tracker && $user->ID == $this->meta_tracker->get_user_id()  ){
				return $this->meta_tracker;
			}else{
				return new meta( $user );
			}
		}
		if (  is_user_logged_in() ) {
			if ( null == $this->meta_tracker ) {
				$this->init_meta_tracker();
			}

			return $this->meta_tracker;
		}

		return null;
	}

	/**
	 * Add Caldera Forms processors
	 */
	public function add_cf_hooks(){
		add_filter( 'caldera_forms_pre_load_processors', function() {
			\calderawp\cfedd\cf\init\bundler::create_processor();
			\calderawp\cfedd\cf\init\payment::create_processor();
		});

	}

	public function add_edd_hooks(){
		if( is_user_logged_in() ){
			$this->init_meta_tracker();
			if( $this->meta_tracker ){
				$this->edd_filter = new user( $this->meta_tracker );
				$this->edd_filter->add_filters();
				return true;
			}
		}


		return false;
	}

	/**
	 * Sets the meta_tracker property based on current user
	 *
	 * @since 1.5.0
	 */
	protected function init_meta_tracker(){
		$this->meta_tracker = new meta( get_user_by( 'ID', get_current_user_id() ) );

	}

}