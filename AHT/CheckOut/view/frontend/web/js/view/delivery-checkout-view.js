define([
    'jquery',
    'ko',
    'uiComponent',
    'underscore',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote',
    'mage/url',
    'mage/storage'
], function ($, ko, Component, _, stepNavigator, customer, quote, urlBuilder, storage) {
    'use strict';

    /**
     * mystep - is the name of the component's .html template,
     * <Vendor>_<Module>  - is the name of your module directory.
     */
    return Component.extend({
        defaults: {
            template: 'AHT_CheckOut/delivery-checkout-form'
        },

        // add here your logic to display step,
        isVisible: ko.observable(true),
        // isLoggedIn: customer.isLogegdIn(),

        deliverCode: 'delivery_wrapper',
        deliverTitle: 'Deliver Step',

        /**
         * @returns {*}
         */
        initialize: function () {
            this._super();

            // register your step
            stepNavigator.registerStep(
                // step code will be used as step content id in the component template
                this.deliverCode,
                // step alias
                null,
                // step title value
                this.deliverTitle,
                // observable property with logic when display step or hide step
                this.isVisible,

                _.bind(this.navigate, this),

                /**
                 * sort order value
                 * 'sort order value' < 10: step displays before shipping step;
                 * 10 < 'sort order value' < 20 : step displays between shipping and payment step
                 * 'sort order value' > 20 : step displays after payment step
                 */
                15
            );

            return this;
        },

        /**
         * The navigate() method is responsible for navigation between checkout steps
         * during checkout. You can add custom logic, for example some conditions
         * for switching to your custom step
         * When the user navigates to the custom step via url anchor or back button we_must show step manually here
         */
        navigate: function () {
            this.isVisible(true);
        },

        /**
         * @returns void
         */
        navigateToNextStep: function () {
            var date = $("input[name=date]").val();
            var comment = $("input[name=comment]").val();
            var quoteId = quote.getQuoteId();
            //
            var url = urlBuilder.build('step/quote/save');
            storage.post(
                url,
                JSON.stringify({quoteId: quoteId, date: date, comment: comment}),
                //
                false
            ).done(
                function (respone){
                    stepNavigator.next();
                }
            ).fail();
        }
    });
});