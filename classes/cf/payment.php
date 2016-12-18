<?php
/**
 * Created by PhpStorm.
 * User: josh
 * Date: 12/17/16
 * Time: 10:59 PM
 */

namespace calderawp\cfedd\cf;


use calderawp\cfedd\edd\create\payment\regular;

class payment extends processor {


	/**
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public function pre_processor( array $config, array $form, $proccesid ) {
		$this->set_data_object_initial( $config, $form );
		if ( ! empty( $errors ) ) {
			return $errors;
		}

		if( 1 == $this->data_object->get_value( 'cf-edd-use-bundle-builder' ) ){
			$bundler = true;
			$download_id = $this->get_bundle_id_from_transdata( $proccesid );
			$payment_id = $this->get_payment_id_from_transdata( $proccesid );
			if( false == $download_id ){
				$this->data_object->add_error( __( 'EDD Payment processor could not detect bundle properly (download id)', 'cf-edd-pro' ) );
			}
			if( false == $payment_id ){
				$this->data_object->add_error( __( 'EDD Payment processor could not detect bundle properly (payment id)', 'cf-edd-pro' ) );
			}
		}else{
			$bundler = false;
			$download_id = $this->data_object->get_value( 'cf-edd-pro-payment-download' );
			$payment_details = [];
			$total = $this->data_object->get_value( 'cf-edd-pro-payment-total' );
			$payment_id = ( new regular( $total, [ $download_id ], $payment_details ) )->get_payment_id();
			$this->add_payment_id_to_transdata( $payment_id, $proccesid );

		}

		if ( ! empty( $errors ) ) {
			return $errors;
		}



	}

	/**
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public function processor( array $config, array $form, $proccesid ) {
		$payment_id = $this->get_payment_id_from_transdata( $proccesid );
		if( 1 == $this->data_object->get_value( 'cf-edd-use-bundle-builder' ) ){
			$payment = new \EDD_Payment( $payment_id );
		}else{
			$payment = new \EDD_Payment( $payment_id );
		}

		$new_status = $this->data_object->get_value( 'cf-edd-pro-payment-status' );
		if( $new_status !== $payment->status ){
			$payment->update_status( $new_status );
			$payment->save();
			$return = [
				'payment_id' => false,
				'first_name'=> false,
				'last_name'=> false,
				'email'=> false,
				'user_id'=> false,
				'customer_id'=> false,
			];
			foreach ( $return as $field ){
				$return[ $field ] = $payment->$field;
				$transdata[ $proccesid ][ 'meta'] [ $this->slug ][ $field ] = $payment->$field;
			}
		}

		return $return;
	}
}