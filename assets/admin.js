/**
 * Caldera Forms Discount Codes - admin
 * https://calderawp.com
 *
 */

var cf_edd_pro_group, cf_edd_pro_cleanup;

jQuery( document ).ready(function($) {
    var discount_group_field = new CF_Processor_Group_Field( $, 'cf-edd-pro' );

    cf_edd_pro_group = function ( obj ) {
        return discount_group_field.group( obj );
    };

    cf_edd_pro_cleanup = function ( obj ) {
        return discount_group_field.cleanup( obj );
    };
});