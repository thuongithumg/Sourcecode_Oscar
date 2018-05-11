/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/settings/general/abstract',
        'Magestore_WebposBambora/js/action/send-request',
        'Magestore_Webpos/js/helper/alert',
        'Magento_Checkout/js/model/full-screen-loader',
        'mage/translate'

    ],
    function ($, ko, Component, sendRequest, Alert, fullScreenLoader, $t) {
        "use strict";

        return Component.extend({
            CHECK_CONNECTION: 2,

            defaults: {
                template: 'Magestore_WebposBambora/settings/general/bambora/check-connection'
            },

            initialize: function () {
                this._super();
            },

            checkConnection: function () {
                var self = this;
                if ($('#ip_address').val() && $('#ip_port').val()) {
                    $('#checkout-loader').show();
                    sendRequest(self.CHECK_CONNECTION, 0, "").done(function (response) {
                        if (response) {
                            if (response === "success") {
                                Alert({
                                    priority:'success',
                                    title: $t('Message'),
                                    message: $t('Connection succeeded.')
                                });
                            } else {
                                Alert({
                                    priority:'danger',
                                    title: $t('Message'),
                                    message: $t('Connection failed.')
                                });
                            }
                        }
                        $('#checkout-loader').hide();
                    }).fail(function (response) {
                        Alert({
                            priority:'danger',
                            title: $t('Message'),
                            message: $t('Please make sure the POSHub is enabled!')
                        });
                        $('#checkout-loader').hide();
                    });
                } else {
                    Alert({
                        priority:'danger',
                        title: $t('Message'),
                        message: $t('Connection failed. Please fill the information to check the connection!')
                    });
                }
            }
        });
    }
);