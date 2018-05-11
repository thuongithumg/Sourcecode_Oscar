/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/settings/general/abstract',
        'Magestore_Webpos/js/model/directory/currency',
        'mage/url',
        'Magestore_Webpos/js/action/notification/add-notification',
        'Magestore_Webpos/js/model/event-manager',
        'mage/translate'
    ],
    function ($, ko, Component, currency, mageUrl, addNotification, eventManager, Translate) {
        "use strict";

        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/settings/general/currency/change-currency'
            },
            elementName: 'currency',
            value: ko.observable(''),
            optionsArray: ko.observableArray([]),
            initialize: function () {
                this._super();
                var self = this;
                var currencyData = currency().getCollection().load();
                currencyData.done(function (data) {
                    self.optionsArray([]);
                    $.each(data.items, function (index, value) {
                        self.optionsArray.push({
                            value: value.code,
                            text: value.currency_name
                        });
                    });
                    self.value(window.webposConfig.currentCurrencyCode);
                });
                eventManager.observer('currency_pull_after', function () {
                    currencyData = currency().getCollection().load();
                    currencyData.done(function (data) {
                        self.optionsArray([]);
                        $.each(data.items, function (index, value) {
                            self.optionsArray.push({
                                value: value.code,
                                text: value.currency_name
                            });
                        });
                        self.value(window.webposConfig.currentCurrencyCode);
                    });
                });
            },
            saveConfig: function (data) {
                var self = this;
                if (!checkNetWork) {
                    addNotification(Translate('Cannot connect to your server!'), true, 'danger', 'Error');
                    self.value(window.webposConfig.currentCurrencyCode);
                    return false;
                }
                var value = $('select[name="' + data.elementName + '"]').val();
                if (value) {
                    var url = mageUrl.build('directory/currency/switch/currency/' + value);
                    location.href = url;
                }
            }
        });
    }
);