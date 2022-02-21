define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'razorpaymanual',
                component: 'Test_Testpayment/js/view/payment/method-renderer/razorpaymanual'
            }
        );
        return Component.extend({});
    }
);
