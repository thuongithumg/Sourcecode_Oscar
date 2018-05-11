/*
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

define(
    [
        'jquery',
        'ko',
        'ui/components/settings/general/abstract',
        'model/directory/currency',
        'mage/url',
        'action/notification/add-notification',
        'eventManager',
        'mage/translate'
    ],
    function ($, ko, Component, currency, mageUrl, addNotification, eventManager, Translate) {
        "use strict";

        return Component.extend({
            defaults: {
                template: 'ui/settings/general/currency/change-currency'
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
                    self.value(value);
                    window.webposConfig.currentCurrencyCode = value;
                    addNotification(Translate('Save configuration successfully!'), true, 'success', Translate('Completed'));
                    var url = mageUrl.build('directory/currency/switch/currency/' + value);
                    location.href = url;
                }
            }
        });
    }
);