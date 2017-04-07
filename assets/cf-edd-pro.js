function CFEDDProDynPrice(config, $) {
    var $price = $("[data-field='" + config.price_field + "']");
    var self = this;

    this.getFields = function () {
        return config.download_fields;
    };

    this.getCount = function () {
        var count = 0;
        $.each(config.download_fields, function (i, field) {
            if ($(document.getElementById(field)).val()) {
                count++;
            }

        });

        return count;
    };

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

    this.bindHandlers = function () {
        $.each( config.download_fields, function (i, field) {
            $(document.getElementById(field)).on('change', function () {
                self.getPrice();
            } );
        });
    };

    this.bindHandlers();
    this.getPrice();
}

window.addEventListener("load", function () {
    if ('object' == typeof  CF_EDD_PRO) {
        if( 'object' != typeof  window.cf_edd_pro ){
            window.cf_edd_pro = {};
        }

        window.cf_edd_pro[ CF_EDD_PRO.form_id ] = new CFEDDProDynPrice( CF_EDD_PRO, jQuery);
    }
});