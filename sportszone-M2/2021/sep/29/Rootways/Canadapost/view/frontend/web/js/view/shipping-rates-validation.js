define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        'Rootways_Canadapost/js/model/shipping-rates-validator',
        'Rootways_Canadapost/js/model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        canadapostShippingRatesValidator,
        canadapostShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('rwcanadapost', canadapostShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('rwcanadapost', canadapostShippingRatesValidationRules);
        return Component;
    }
);
