var currencyrate = {
    par: {},
    setup: function() {
        currencyrate.par.select = 'select[name="currency_rate"]';
    },
    initialize: function() {
        currencyrate.setup();
        // listeners
        $(document).on('change', currencyrate.par.select, function(e) {
            var charcode = $(currencyrate.par.select + ' option:selected').attr('value');
            document.cookie = 'currency=' + charcode + '; path=/';
            location.reload();
        });
    }
};

currencyrate.initialize();
