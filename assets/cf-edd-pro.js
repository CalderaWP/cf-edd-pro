/**
 * Dynamic pricing system
 *
 * @since 0.0.1
 *
 * @param {object} config
 * @param  {jQuery} $
 * @constructor
 */
function CFEDDProDynPrice(config, $) {

    var $price = $("[data-field='" + config.price_field + "']");
    var self = this;

    /**
     * Get the fields used for dynamic pricing
     *
     * @since 1.1.0
     *
     * @returns {*}
     */
    this.getFields = function () {
        return config.download_fields;
    };

    /**
     * Get download IDs currently selected by field
     *
     * @since 1.1.0
     *
     * @returns {Array}
     */
    this.getDownloads = function () {
        var downloads = [],
            id;
        $.each( config.download_fields, function (i, field) {
            id = $(document.getElementById(field)).val();
            if( id ){
                downloads.push(id);
            }
        });
        return downloads;
    };

    /**
     * Get number of downloads selected
     *
     * @since 1.1.0
     *
     * @returns {number}
     */
    this.getCount = function () {
        var count = 0;
        $.each(config.download_fields, function (i, field) {
            if ($(document.getElementById(field)).val()) {
                count++;
            }

        });

        return count;
    };

    /**
     * Get the price via AJAX
     *
     * @since 1.1.0 in this form
     * @since 0.0.1
     */
    this.getPrice = function () {
        var count = self.getCount();

        $.post({
            url: config.api,
            data: {
                form_id: config.form_id,
                count: count
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', config.nonce);
            }

        }).success(function (r) {
            $price.val(r.price);
            $( document ).trigger( 'cf.add' );
        });
    };

    /**
     * Setup change handlers
     *
     * @since 1.1.0 in this form
     * @since 0.0.1
     */
    this.bindHandlers = function () {
        $.each( config.download_fields, function (i, field) {
            $(document.getElementById(field)).on('change', function () {
                self.getPrice();
            } );
        });
    };

    //On load bind and get price
    this.bindHandlers();
    this.getPrice();
}

/** Init system **/
window.addEventListener("load", function () {
    if ('object' == typeof  CF_EDD_PRO) {
        if( 'object' != typeof  window.cf_edd_pro ){
            window.cf_edd_pro = {};
        }

        window.cf_edd_pro[ CF_EDD_PRO.form_id ] = new CFEDDProDynPrice( CF_EDD_PRO, jQuery);
    }
});