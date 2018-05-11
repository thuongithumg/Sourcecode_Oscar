/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/checkout/checkout/payment_selected'
    ],
    function ($,ko, SelectedPayment) {
        "use strict";
        return SelectedPayment.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/checkout/payment_creditcard'
            },
            visibilityPaymentType: function () {
                return [1];
            },
            getCcYearsValues: function() {
                return _.map(WEBPOS.config.cc_years, function(value, key) {
                    return {
                        'value': key,
                        'year': value
                    }
                });
            },
            getCcMonthsValues: function() {
                return _.map(WEBPOS.config.cc_months, function(value, key) {
                    return {
                        'value': key,
                        'month': value
                    }
                });
            },
            getCcTypesValues: function() {
                return _.map(WEBPOS.config.cc_types, function(value, key) {
                    return {
                        'value': key,
                        'type': value
                    }
                });
            },
        });
    }
);
