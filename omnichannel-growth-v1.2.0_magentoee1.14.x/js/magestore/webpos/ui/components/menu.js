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
define([
    'jquery',
    'posComponent',
    'model/appConfig',
    'model/session',
    'model/resource-model/magento-rest/abstract',
    'helper/full-screen-loader',
    'helper/general',
    'ui/lib/modal/confirm',
], function ($, Component, AppConfig, Session, restAbstract, fullScreenLoader, generalHelper, confirm) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'ui/menu'
        },
        generalHelper: generalHelper,

        initialize: function () {
            var self = this;
            self._super();
            self.observerEvent(AppConfig.EVENT.LOGOUT_WITHOUT_CONFIRM, function(){
                self._logoutWithoutConfirm();
            });
        },
        /**
         * Get menu element id
         * @returns {string}
         */
        getMenuId: function(){
            return AppConfig.ELEMENT_IDS.MENU;
        },
        /**
         * Logout without confirm
         * @private
         */
        _logoutWithoutConfirm: function(){
            var deferredSession = Session.getId();
            deferredSession.done(function (response) {
                var sessionId = response;
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
            });
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
});
