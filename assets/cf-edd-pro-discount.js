jQuery( function($){
    /** Init discount code check **/
    var $fields = $( "input[data-edd-discount]" );
    if( $fields.length ){
        $fields.each( function( i, feild){
            new CFEDDProDiscountField( $( feild ).data( 'edd-discount'), $ );
        });
    }

    /** If dynamic pricer is being used apply those downloads to discount code check **/
    $( document ).on( 'cf.edd.cartItems', function (e,obj) {
        var form = obj.form,
            downloads;
        if( undefined != window.cf_edd_pro && undefined != window.cf_edd_pro[ form ] ){
            downloads = window.cf_edd_pro[ form ].getDownloads();
            if( downloads.length ){
                obj.self.setCartItems(downloads);
            }else{
                obj.self.setCartItems([]);
            }
        }
    });

});

/**
 * The EDD Discount system
 *
 * @since 1.1.0
 *
 * @param {object} config
 * @param  {jQuery} $
 * @constructor
 */
function CFEDDProDiscountField( config, $ ) {
    /**
     * Alias of this
     *
     * @since 1.1.0
     *
     * @type {CFEDDProDiscountField}
     */
    var self = this;

    var $field = $( document.getElementById( config.id_attr ) ),
        $form = $( 'form#' + config.form + '_' + config.form_count ),
        $priceField = $form.find( '[data-field="' + config.price_field + '"]' ),
        $priceLabel = $form.find( '#' + config.price_field + '_' + config.form_count ),
        discount = {},
        cartItems,
        internalPriceChange = false;



    /**
     * Handle changes to discount field
     *
     * @since 1.1.0
     */
    this.fieldChange = function () {
        checkDiscount();
    };

    /**
     * Handle changes to price field
     *
     * @since 1.1.0
     */
    this.priceChange = function () {
        if( false === internalPriceChange ){
            applyDiscount();
        }
    };

    /** Init handlers **/
    //Josh - Don't move this to top again.
    $field.on( 'change', self.fieldChange );
    $priceField.on( 'change',  self.priceChange );


    /**
     * Set the cart items
     *
     * @since 1.1.0
     *
     * @param {Array} items
     */
    this.setCartItems = function (items) {
      cartItems = items;
    };

    /**
     * Report success/error for disocunt code
     *
     * @since 1.1.0
     *
     * @param {String} message
     * @param {Bool} error
     */
    this.report = function( message, error ){
        removeReport();
        var $parent = $field.parent();

        var classes = 'help-block';
        if( error ){
            classes += ' alert alert-danger  caldera_ajax_error_block';
        }else{
            classes += ' alert alert-success';
        }
        var $span = $("<span>", { "class": classes } );
        $span.html( message );
        $parent.append( $span );


    };

    /**
     * Remove error or success report
     *
     * @since 1.1.0
     */
    function removeReport() {
        $field.parent().find( 'span' ).remove();
    }

    /**
     * Calculate the discount
     *
     * @since 1.1.0
     *
     * @returns {string|Bool}
     */
    function calcDiscount() {
        if( discount.hasOwnProperty( 'amount' ) && discount.hasOwnProperty( 'type' ) ){

            var price = getPrice(),
                newPrice = price;
            if( 'percent' == discount.type ){
                if( 0 == discount.amount ){
                    newPrice = price;
                } else if( 100 == discount.amount ){
                    newPrice = 0;
                }else{
                    newPrice = price - ((discount.amount  / 100) * price);
                }
            }else{
                newPrice = price - discount.amount;

            }

            if( 0 > newPrice ){
                newPrice = 0;
            }
            return newPrice.toFixed(2);
        }
        return false;
    }

    /**
     * Remove discount
     *
     * @since 1.1.0
     */
    function removeDiscount() {
        discount = {};
        removeReport();
        internalPriceChange = true;
        $priceField.trigger('cf.add');
        window.setTimeout(function () {
            internalPriceChange = false;
        }, 100);
    }

    /**
     * Apply discount
     *
     * @since 1.1.0
     */
    function applyDiscount() {
        var discountedPrice = calcDiscount();
        if (false === discountedPrice) {
            removeDiscount();
        }else{
            $priceField.val(discountedPrice);
            $priceLabel.html(discountedPrice);
        }
    }

    /**
     * Get the dicount code
     *
     * @since 1.1.0
     *
     * @returns {String}
     */
    function getCode() {
        return $field.val();
    }

    /**
     * Get current value for the price field
     *
     * @since 1.1.0
     *
     * @returns {Number}
     */
    function getPrice() {
        return parseFloat( $priceField.val() );
    }

    /**
     * Check discount via AJAX
     *
     * @since 1.1.0
     */
    function checkDiscount() {
        if( '' == getCode() ){
            removeDiscount();
            return;
        }

        /**
         * Use this event to modify which downloads are used in the discount code validation for product requirements.
         */
        $( document ).trigger( 'cf.edd.cartItems', {
            self: self,
            cartItems: cartItems,
            form: config.form
        } );
        $.ajax({
            url: config.url,
            data: {
                total: getPrice(),
                code: getCode(),
                nonce: config.nonce,
                _wpnonce: config.rest_nonce,
                form: config.form,
                items: cartItems
            },
            success: function (r,status,xhr) {
                if( r.hasOwnProperty( 'amount' ) && r.hasOwnProperty( 'type' ) ){
                    discount.amount = r.amount;
                    discount.type = r.type;
                    self.report( r.message, false );
                    applyDiscount();
                }else{
                    removeDiscount();
                }
            },
            error: function (r,status,xhr) {
                removeDiscount();
                if( r.hasOwnProperty( 'responseJSON' ) && r.responseJSON.hasOwnProperty( 'message' ) ){
                    self.report( r.responseJSON.message, true );
                }else{
                    self.report( 'Error', true );

                }

            }
        })
    }

}