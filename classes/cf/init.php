<?php
/**
 * Creates EDD Bundle builder processor
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

namespace calderawp\cfedd\cf;


class init {

	/**
	 * The slug for this processor
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	protected static $slug = 'cf-edd-pro';

	/**
	 * Create the processor
	 *
	 * @uses "caldera_forms_pre_load_processors" filter
	 *
	 * @since 0.0.1
	 *
	 * @param null $form_id Optional. Form ID - used in admin - will be pulled from URL if not passed.
	 */
	public static function create_processor( $form_id = null ) {
		new processor( self::processor_config(), self::processor_fields(), self::$slug );
		if ( is_admin() ) {
			if ( null == $form_id && isset( $_GET[ 'page' ], $_GET[ 'edit' ] ) && 'caldera-forms' == $_GET[ 'page' ] ) {
				$form_id = $_GET[ 'edit' ];
			}
			$form = \Caldera_Forms_Forms::get_form( $form_id );

			if ( is_array( $form ) ) {
				new \calderawp\cf\groupconfig\ui( $form, self::$slug, self::translation_strings(), self::download_group_fields()  );

				wp_enqueue_script( 'cf-edd-pro', CF_EDD_PRO_URL . '/assets/admin.js', [
					'jquery',
					'cf-group-config'
				], CF_EDD_PRO_VER );
			}
		}

	}


	/**
	 * Config for processor
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public static function processor_config(){
		return [
			'name' => __( 'Easy Digital Downloads Bundle', 'cf-edd-pro' ),
			'description' => __( 'Sell dynamically created bundles', 'cf-edd-pro' ),
			'cf_ver' => '1.4.6',
			'author' => 'Josh Pollock',
			'template' => __DIR__ . '/includes/bundle-config.php'
		];
	}

	/**
	 * Processor fields
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public static function processor_fields(){
		return [
			[
				'id' => 'cf-edd-pro-total',
				'label' => __( 'Price For Bundle', 'cf-edd-pro' ),
				'type' => 'text',
				'required' => true,
				'magic' => true,
			],
			[
				'id' => 'cf-edd-pro-redirect',
				'label' => __( 'Redirect To Cart?', 'edd-pro'),
				'desc' => __( 'Redirect to EDD cart after form is completed', 'cf-edd-pro' ),
				'type' => 'checkbox',
				'options' => [
					'value' => '1',
					'label' => __( 'Yes', 'cf-edd-pro' )
				]
 			],
			[
				'id' => 'cf-edd-pro-min',
				'label' => __( 'Minimum Size', 'cf-edd-pro' ),
				'desc' => __( 'Minimum number of downloads to qualify for bundle price', 'cf-edd-pro' ),
				'type' => 'number',
				'required' => true,
				'magic' => true,
			],
			[
				'id' => 'cf-edd-pro-max',
				'label' => __( 'Maximum Size', 'cf-edd-pro' ),
				'desc' => __( 'Maximum number of downloads to qualify for bundle price', 'cf-edd-pro' ),
				'type' => 'number',
				'required' => true,
				'magic' => true,
			],
			[
				'id' => 'cf-edd-bundle-id',
				'label' => __( 'Bundle to customize', 'cf-edd-pro' ),
				'desc' => __( 'The ID of an EDD Download Bundle to be customized by this processor', 'cf-edd-pro' ),
				'type' => 'text',
				'required' => true,
				'magic' => true,
			],
			[
				'id' => 'cf-edd-pro-downloads',
				'label' => __( 'Downloads', 'cf-edd-pro' ),
				'desc' => __( 'Fields for selecting downloads or download IDs', 'cf-edd-pro' ),
				'type' => 'group',
				'required' => true,
				'group-slug' => self::$slug
			]
		];

	}

	/**
	 * Group field sub-fields
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public static function download_group_fields(){
		return [
			'fields' => [
				[
					'id'       => 'download',
					'label'    => __( 'Download', 'cf-discount' ),
					'required' => true,
					'type'     => 'text',
					'magic'    => true,
				]
			]
		];
	}


	/**
	 * Translation strings for the group field
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function translation_strings(){

		return [
			'remove_confirm' => __( 'Are you sure you want to remove this download field?', 'cf-edd-pro' ),
			'remove_title' => __( 'Click to remove download field', 'cf-edd-pro' ),
			'remove_text' => __( 'Remove Field', 'cf-edd-pro' ),
			'add_text' => __( 'Add field', 'cf-edd-pro' ),
			'add_title' => __( 'Click to add new download field', 'cf-edd-pro' )
		];

	}
}