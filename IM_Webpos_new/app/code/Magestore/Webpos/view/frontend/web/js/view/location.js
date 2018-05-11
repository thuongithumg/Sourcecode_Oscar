/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'mage/url',
        'Magestore_Webpos/js/model/resource-model/magento-rest/abstract',
        'mage/storage',
        'Magestore_Webpos/js/lib/cookie',
        'Magestore_Webpos/js/helper/full-screen-loader',
        'mage/translate',
        'Magento_Ui/js/modal/confirm',
        'mage/validation'
    ],
    function ($, ko, Component, mageUrl, restAbstract, storage, Cookies, fullScreenLoader, Translate, confirm) {
        "use strict";

        return Component.extend({


            initialize: function () {
                var self = this;
                this._super();
                this.posArray = ko.computed(function () {
                    var result = [];
                    var locationId = self.locationId();
                    $.each(window.posConfig, function (index, value) {
                        if (value.location_id === locationId) {
                            result.push(value);
                        }
                    });
                    if (result.length === 0 && self.locationId()) {
                        self.showLogout(true);
                    }
                    return result;
                });
                $('#checkout-loader').hide();
            },

            locationId: ko.observable(),

            posId: ko.observable(),

            showLogout: ko.observable(false),

            locationArray: ko.observable(window.locationConfig),

            formLocationAfterRender:function() {
                if((window.locationConfig.length === 1)
                    && !($('#webpos-location').find('select#pos').is(":visible"))) {
                    var value = window.locationConfig[0].location_id;
                    if (value) {
                        fullScreenLoader.startLoader();
                        storage.get(
                            'webpos/index/changeLocation?location_id=' + value + '&' + 'pos_id=',
                            {

                            }
                        ).done(
                            function (response) {
                                var deleteRequest = window.indexedDB.deleteDatabase('magestore_webpos');
                                var url = mageUrl.build("webpos/index/changeStore?store_id=" + response.storeViewId);
                                window.location.href = url;
                            }
                        );
                    }
                }
            },

            ajaxChangeLocation: function () {
                $('#webpos-location').validation();
                if ($('#webpos-location').validation('isValid')) {
                    var value = this.locationId();
                    var pos = this.posId();
                    if (value) {
                        fullScreenLoader.startLoader();
                        storage.get(
                            'webpos/index/changeLocation?location_id=' + value + '&' + 'pos_id=' + pos,
                            {

                            }
                        ).done(
                            function (response) {
                                var deleteRequest = window.indexedDB.deleteDatabase('magestore_webpos');
                                var url = mageUrl.build("webpos/index/changeStore?store_id=" + response.storeViewId);
                                window.location.href = url;
                            }
                        );
                    }
                }
            },

            logout: function () {
                var deferredSession = this.getSessionId();
                deferredSession.done(function (response) {
                    var sessionId = response;
                    confirm({
                        content: Translate('Are you sure you want to logout?'),
                        actions: {
                            confirm: function () {
                                var apiUrl = '/webpos/staff/logout';
                                var deferred = $.Deferred();
                                Cookies.remove('WEBPOSSESSION');
                                fullScreenLoader.startLoader();

                                restAbstract().setPush(true).setLog(false).callRestApi(
                                    apiUrl + '?session=' + sessionId,
                                    'post',
                                    {},
                                    {},
                                    deferred
                                );

                                deferred.always(function (data) {
                                    window.location.reload();
                                });
                            },
                            always: function (event) {
                                event.stopImmediatePropagation();
                            }
                        }
                    });
                });

            },

            getSessionId: function () {
                var deferred = $.Deferred();
                deferred.resolve(Cookies.get('WEBPOSSESSION'));
                return deferred;
            }

        });
    }
);