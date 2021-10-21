define(
    [
        'Mageplaza_GiftCard/js/view/checkout/summary/giftcard-fee'
    ],
    function (Component) {
        'use strict';
 
        return Component.extend({
 
            /**
             * @override
             */
            isDisplayed: function () {
                return this.getPureValue() !== 0;
            }
        });
    }
);