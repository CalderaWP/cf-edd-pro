<?php
/**
 * Creates EDD Bundle Dynamic Pricing Processor
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
namespace calderawp\cfedd\cf\init;

class pricing extends config {

	/**
	 * The slug for this processor
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	protected static $slug = 'cf-edd-pro-dynamic-pricing';


	/**
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public static function create_processor( $form_id = null ) {
		new \calderawp\cfedd\cf\pricing( self::processor_config(), self::processor_fields(), self::$slug );
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
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public static function processor_config(){
		return [
			'name' => __( 'EDD Bundle Pricing', 'cf-edd-pro' ),
			'description' => __( 'Set Pricing Dynamically For The EDD Bundle Builder', 'cf-edd-pro' ),
			'cf_ver' => '1.4.6',
			'author' => 'Josh Pollock',
			'icon' => CF_EDD_PRO_URL . '/icon.png',
			'template' => CF_EDD_PRO_PATH . '/includes/dynamic-pricing-config.php',
			'single' => true,

		];
	}

	/**
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public static function processor_fields(){
		return [
			[
				'id'          => 'cf-edd-pro-dynamic-pricing-price-field',
				'type'        => 'text',
				'label'       => __( 'Price Field', 'cf-dynamic-pricing' ),
				'description' => __( 'Field that sets price. Should be hidden or calculation field.', 'cf-edd-pro' ),
				'required'    => true,
			],
			[
				'id' => 'cf-edd-pro-dynamic-pricing-prices',
				'label' => __( 'Prices', 'cf-edd-pro' ),
				'desc' => __( 'Set Price Per Number of Downloads', 'cf-edd-pro' ),
				'type' => 'group',
				'required' => false,
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
					'id'       => 'num_downloads',
					'label'    => __( 'Number of Downloads', 'cf-edd-pro' ),
					'required' => true,
					'type'     => 'number',
					'magic'    => false,
				],
				[
					'id'       => 'cost',
					'label'    => __( 'Cost', 'cf-edd-pro' ),
					'required' => true,
					'type'     => 'text',
					'magic'    => false,
				]
			]
		];
	}

	/**


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