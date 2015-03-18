
var PostCodeFilter = Class.create();
PostCodeFilter.prototype = {
    initialize: function (baseUrl, country, postcode, validator) {
        this.baseUrl = baseUrl;
        this.country = country;
        this.postcode = postcode;
        this.validator = validator;
    },
    checkPostCode: function () {
        if (this.country.value && this.postcode.value) {
            var may_order = true;
            new Ajax.Request(this.baseUrl + '/postcodefilter/check', {
                method: 'get',
                parameters: {'country': this.country.value, 'postcode': this.postcode.value},
                onSuccess: function (transport) {
                    var response = transport.responseText.evalJSON();
                    may_order = response.may_order;
                    Validation.add('validate-postcode-delivery', response.message, function() {
                        return may_order;
                    });

                    this.validator.validate();
                }
            });
        }
    }
};


document.observe('dom:loaded', function () {

    var pos = window.location.href.search(/\/checkout\/cart/) + 1;
    var baseUrl = window.location.href.substr(0, pos);
    if (baseUrl !== '') {
        
        if (typeof coShippingMethodForm === 'undefined' || typeof Validation === 'undefined') {
            return;
        }
        var country = $('country');
        var region_id = $('region_id');
        var postcode = $('postcode');
        
        var postCodeFilter = new PostCodeFilter(baseUrl, country, postcode, coShippingMethodForm.validator);
    
        country && country.observe('blur', postCodeFilter.checkPostCode.bind(postCodeFilter));
        postcode && postcode.observe('blur', postCodeFilter.checkPostCode.bind(postCodeFilter));
        postcode.addClassName('validate-postcode-delivery');
        
        coShippingMethodForm.validator.options.focusOnError = false;
        region_id && region_id.observe('blur', coShippingMethodForm.validator.validate.bind(
            coShippingMethodForm.validator
        ));

        postCodeFilter.checkPostCode();
    }
    //pos = window.location.href.search(/\/checkout\/onepage/) + 1;
    //if (pos >= 0) {
    //    
    //}
    
    
});
