var config = {
    'config': {
        'mixins': {
           'Magento_Checkout/js/view/shipping': {
               'AHT_UiComponent/js/view/shipping-payment-mixin': true
           },
           'Magento_Checkout/js/view/payment': {
               'AHT_UiComponent/js/view/shipping-payment-mixin': true
           }
       }
    }
}