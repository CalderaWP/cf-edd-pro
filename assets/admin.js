/**
 * Caldera Forms Discount Codes - admin
 * https://calderawp.com
 *
 */

var cf_edd_pro_group, cf_edd_pro_cleanup, cf_edd_pro_dynamic_pricing_group, cf_edd_pro_dynamic_pricing_cleanup;

jQuery( document ).ready(function($) {
    var group_field_bundler = new CF_Processor_Group_Field( $, 'cf-edd-pro' );

    cf_edd_pro_group = function ( obj ) {
        return group_field_bundler.group( obj );
    };

    cf_edd_pro_cleanup = function ( obj ) {
        return group_field_bundler.cleanup( obj );
    };

    var group_field_pricer = new CF_Processor_Group_Field( $, 'cf-edd-pro-dynamic-pricing' );

    cf_edd_pro_dynamic_pricing_group = function ( obj ) {
        return group_field_pricer.group( obj );
    };

    cf_edd_pro_dynamic_pricing_cleanup = function ( obj ) {
        return group_field_pricer.cleanup( obj );
    };
});


