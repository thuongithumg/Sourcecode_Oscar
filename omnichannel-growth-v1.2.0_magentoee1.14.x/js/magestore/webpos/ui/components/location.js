/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'ko',
        'posComponent',
        'model/session',
        'model/resource-model/magento-rest/abstract',
        'mage/url',
        'mage/storage',
        'helper/full-screen-loader',
        'helper/general',
        'ui/lib/modal/confirm',
        'mage/validation',
        'lib/jquery.toaster',
        'model/config/local-config',
        'model/synchronization/synchronization-factory'
    ],
    function ($, ko, Component, Session, restAbstract, mageUrl, storage, fullScreenLoader, Helper, confirm, validation, toaster, localConfig, SynchronizationFactory) {
        "use strict";

        return Component.extend({


            initialize: function () {
                var self = this;
                this._super();
                this.synchronization = SynchronizationFactory.get(),
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

            ajaxChangeLocation: function () {
                var self = this;
                $('#webpos-location').validation();
                if ($('#webpos-location').validation('isValid')) {
                    var value = this.locationId();
                    var pos = this.posId();
                    if (value) {
                        if(!Helper.isOnlineCheckout() && (!localConfig.get('current_location_id') || localConfig.get('current_location_id')!=value)){
                            /* var deferred = this.synchronization.delete('webpos/install/product');
                            // var deferred = this.synchronization.delete('webpos/updated/product');
                            // var deferred = this.synchronization.delete('webpos/curpage/category');
                            // var deferred = this.synchronization.delete('webpos/updated/category');
                            */
                            indexedDB.deleteDatabase('magestore_webpos')
                        }

                        fullScreenLoader.startLoader();
                        storage.get(
                            'webpos/index/changeLocation?location_id=' + value + '&' + 'pos_id=' + pos,
                            {

                            }
                        ).done(
                            function (response) {
                                Helper.saveLocalConfig('current_store_full_name', response.store_name);
                                if (response && typeof response.message !== 'undefined') {
                                    fullScreenLoader.stopLoader();
                                    $.toaster(
                                        {
                                            priority: 'danger',
                                            title: Helper.__("Warning"),
                                            message: response.message
                                        }
                                    );
                                    self.showLogout(true);
                                } else {
                                    var storeUrl = response.store_url;
                                    if(storeUrl){
                                        window.location.href = storeUrl;
                                    }else{
                                        window.location.reload();
                                    }
                                }

                            }
                        );
                    }
                }
            },
            /**
             * Logout function
             */
            logout: function(){
                var self = this;
                var deferredSession = Session.getId();
                deferredSession.done(function (response) {
                    var sessionId = response;
                    confirm({
                        content: self.__('Are you sure you want to logout?'),
                        actions: {
                            confirm: function () {
                                var apiUrl = '/webpos/staff/logout';
                                var deferred = $.Deferred();
                                Session.clear();
                                fullScreenLoader.startLoader();
                                restAbstract().setPush(true).setLog(false).callRestApi(
                                    apiUrl + '?session=' + sessionId,
                                    'post',
                                    {},
                                    {
                                    },
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
            }

        });
    }
);