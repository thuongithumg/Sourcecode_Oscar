/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/settings/general/abstract',
        'mage/storage',
        'Magestore_Webpos/js/model/directory/currency',
        'mage/url',
        'Magestore_Webpos/js/lib/cookie',
        'Magestore_Webpos/js/action/notification/add-notification',
        'Magestore_Webpos/js/model/event-manager',
        'mage/translate'
    ],
    function ($, ko, Component, storage, currency, mageUrl, Cookies, addNotification, eventManager, Translate) {
        "use strict";

        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/settings/general/store/store-view'
            },

            elementName: 'store',
            value: ko.observable(window.webposConfig.storeCode),
            optionsArray: ko.observableArray(window.webposConfig.storeView),

            changeStore: function (data) {
                var value = $('select[name="' + data.elementName + '"]').val();
                if (value) {
                    $('#checkout-loader').show();
                    Cookies.set('check_login', 1, { expires: parseInt(window.webposConfig.timeoutSession) });
                    var deleteRequest = window.indexedDB.deleteDatabase('magestore_webpos');
                    var url = mageUrl.build("webpos/index/changeStore?store_code=" + value)  ;
                    $('#checkout-loader').hide();
                    window.location.href = url ;
                }

            }
        });
    }
);