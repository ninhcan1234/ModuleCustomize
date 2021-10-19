define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total'
    ],
    function ($, Component) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Mageplaza_GiftCard/checkout/summary/giftcard'
            },
            isDisplayed: function () {
                return true;
            },
            getGiftCardValue: function () {
                return '$100';
            }
        });
    }
);