function CFEDDProDynPrice(config, $) {
    var $price = $("[data-field='" + config.price_field + "']");

    var getPrice = function () {
        var count = 0;
        $.each(CF_EDD_PRO.download_fields, function (i, field) {
            if ($(document.getElementById(field)).val()) {
                count++;
            }

        });
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
    $.each(config.download_fields, function (i, field) {
        $(document.getElementById(field)).on('change', getPrice);
    });
}

window.addEventListener("load", function () {
    if ('object' == typeof  CF_EDD_PRO) {
        new CFEDDProDynPrice( CF_EDD_PRO, jQuery);
    }
});