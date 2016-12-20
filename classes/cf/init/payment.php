<?php
/**
 * EDD payment proccesor config
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */


namespace calderawp\cfedd\cf\init;


use calderawp\cfedd\cf\interfaces\init;

class payment implements init{

	protected static $slug = 'cf-edd-pro-payment';

	/**
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public static function create_processor( $form_id = null ) {
		new \calderawp\cfedd\cf\payment( self::processor_config(),  self::processor_fields(), self::$slug );

	}

	/**
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public static function processor_config(){
		return [
			'name' => __( 'Easy Digital Downloads Payment', 'cf-edd-pro' ),
			'description' => __( 'Sell an EDD download', 'cf-edd-pro' ),
			'cf_ver' => '1.4.6',
			'author' => 'Josh Pollock',
			'template' => CF_EDD_PRO_PATH . '/includes/payment-config.php',
			'magic_tags' => [
				'payment_id',
				'first_name',
				'last_name',
				'email',
				'user_id',
				'customer_id'
			]
		];
	}

	/**
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public static function processor_fields(){
		return [
			[
				'id' => 'cf-edd-pro-payment-total',
				'label' => __( 'Total Price', 'cf-edd-pro' ),
				'type' => 'text',
				'required' => true,
				'magic' => true,
			],
			[
				'id' => 'cf-edd-pro-payment-status',
				'label' => __( 'Payment status', 'cf-edd-pro' ),
				'desc' => __( 'Leave blank for completed payments', 'cf-edd-pro' ),
				'type' => 'text',
				'default' => 'pending',
				'required' => false,
				'magic' => true,
			],
			[
				'id' => 'cf-edd-pro-payment-download',
				'label' => __( 'Download ID', 'cf-edd-pro' ),
				'type' => 'text',
				'required' => true,
				'magic' => true,
			],
			[
				'id' => 'cf-edd-pro-payment-redirect',
				'label' => __( 'Redirect To Payment Details?', 'cf-edd-pro'),
				'type' => 'checkbox',
				'options' => [
					'value' => '1',
					'label' => __( 'Yes', 'cf-edd-pro' )
				],
				'default' => '1'
			],
			[
				'id' => 'cf-edd-use-bundle-builder',
				'label' => __( 'Sell created bundle?', 'edd-pro'),
				'desc' => __( 'If used, must run after bundle builder, and will ignore set download', 'cf-edd-pro' ),
				'type' => 'checkbox',
				'options' => [
					'value' => '1',
					'label' => __( 'Yes', 'cf-edd-pro' )
				],
				'default' => '1'
			],
		];

	}

}