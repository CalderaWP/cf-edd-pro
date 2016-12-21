<?php
/**
 * Populate EDD auto-populate fields
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

namespace calderawp\cfedd\cf\populate;


class query {

	protected $prices;


	/**
	 * Add Hooks
	 *
	 * @since 0.0.2
	 */
	public function add_hooks(){
		add_filter( 'caldera_forms_render_get_field', [ $this, 'populate_field' ], 11, 2 );
	}

	/**
	 * Remove Hooks
	 *
	 * @since 0.0.2
	 */
	public function remove_hooks(){
		remove_filter( 'caldera_forms_render_get_field', [ $this, 'populate_field' ], 11 );

	}

	/**
	 * Auto-populate fields
	 *
	 * @since 0.0.2
	 *
	 * @uses "caldera_forms_render_get_field" filter
	 *
	 * @param $field
	 * @param $form
	 *
	 * @return array
	 */
	public function populate_field( $field, $form ){
		if ( $this->is_field( $field ) ) {
			$posts = $this->get_posts( $field, $form );
			if( ! empty( $posts ) ){
				foreach($posts as $post ){
					$field['config']['option'][$post->ID] = array(
						'value'	=>	$post->ID,
						'label' =>	$post->post_title
					);
					$this->add_price( $field[ 'ID' ], $post->ID, $form[ 'ID' ] );

				}
				//$field = \Caldera_Forms::format_select_options( $field );
			}


		}

		return $field;

	}

	protected function add_price( $field_id, $download_id, $form_id ){
		$download = new \EDD_Download( $download_id );
		if( $download->has_variable_prices() ){
			$price_id = edd_get_default_variable_price( $download_id);
			$prices = $download->get_prices();
			if( isset( $prices[ $price_id ] ) ){
				$price = $prices[ $price_id ][ 'amount' ];
			}else{
				$price = $prices[ key($prices ) ][ 'amount' ];
			}
		}else{
			$price = $download->get_price();
		}

		if( ! isset( $this->prices[ $form_id ] ) ){
			$this->prices[ $form_id ] = [];
		}

		if( ! isset( $this->prices[ $form_id ][ $field_id ] ) ){
			$this->prices[ $form_id ][ $field_id ] = [];
		}

		$this->prices[ $form_id ][ $field_id ][ $download_id ] = $price;

	}

	/**
	 * Query for download posts
	 *
	 * @since 0.0.2
	 * @param $field
	 * @param $form
	 *
	 * @return array
	 */
	protected function get_posts( $field, $form ){
		/**
		 * Set arguments for EDD auto-populate field
		 *
		 * @since 0.2.0
		 *
		 * @param array $args WP_Query args to use
		 * @param array $field Config for the field.
		 * @param array $form Config for the form.
		 *
		 */
		$args = apply_filters( 'cf_edd_pro_autopopulate_options', [
			'post_type' => 'download'
		], $field, $form  );
		return get_posts( $args );

	}

	/**
	 * Check if this class should affect a field
	 *
	 * @since 0.0.2
	 *
	 * @param  array $field
	 *
	 * @return bool
	 */
	protected function is_field( $field ) {
		return ( ! empty( $field[ 'config' ][ 'auto' ] ) && 'edd' == $field[ 'config' ][ 'auto_type' ] );

	}


}