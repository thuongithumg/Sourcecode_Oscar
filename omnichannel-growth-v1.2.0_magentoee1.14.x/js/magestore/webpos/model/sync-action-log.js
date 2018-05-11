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

/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'model/log/action-log',
        'model/resource-model/magento-rest/abstract',
        'eventManager',
        'lib/cookie'
    ],
    function ($, actionLog, onlineAbstract, eventManager,Cookies) {
        return {
            items: [],
            is_running: false,
            processActionLog: function () {
                var self = this;
                window.setInterval(
                    function () {
                        if (!self.is_running && !reloading) {
                            self.is_running = true;
                            var actions = actionLog().getCollection().reset().addFieldToFilter('number', '5', 'lt').setOrder('order', 'DESC').load();
                            actions.done(function (data) {
                                self.items = data.items;
                                self.callRestApi();
                            });

                        }
                    },
                    5000);
            },
            callRestApi: function () {
                var self = this;
                var item = self.items.pop();
                if (typeof item == 'undefined' || !item.api_url) {
                    self.is_running = false;
                    return self;
                }
                var deferred = $.Deferred();
                if (item.api_url && item.method) {
                    onlineAbstract().setPush(true).setLog(false).callRestApi(
                        item.api_url, item.method, item.params, item.payload, deferred, item.callBack
                    );
                } else {
                    deferred.resolve(false);
                }
                deferred.done(function (response) {
                    checkNetWork = true;
                    /** call function. **/
                    var changeRequireAction = actionLog().getCollection().reset().addFieldToFilter('require_action_id', item.action_id, 'eq').setOrder('order', 'DESC').load();
                    changeRequireAction.done(function (dataLog) {
                        if (dataLog.total_count) {
                            $.each(dataLog.items, function (index, value) {
                                value.require_action_id = response[item.key_path];
                                if (value.requireActionIdPath) {
                                    if (value.interfaceName) {
                                        value.payload.interfaceName[value.requireActionIdPath] = response[item.key_path];
                                    } else {
                                        value.payload[value.requireActionIdPath] = response[item.key_path];
                                    }
                                }
                                var changeLog = actionLog().setData(value).save();
                                changeLog.done(function () {
                                    var deleteLog = actionLog().delete(item.action_id);
                                    // self.callRestApi();
                                    self.is_running = false;
                                    return self;
                                });
                            });
                            /** end function. **/
                        } else {
                            var deleteLog = actionLog().delete(item.action_id);
                            // self.callRestApi();
                            self.is_running = false;
                            return self;
                        }
                    });
                });
                deferred.fail(function (response) {
                    /** lost internet -> stop proccess **/
                    if (response.statusText == 'error' && response.status == 0) {
                        self.is_running = false;
                        checkNetWork = false;
                        return self;
                    }
                    checkNetWork = true;
                    var error = {};
                    if (response.responseText) {
                        try {
                            error = JSON.parse(response.responseText);
                            if(typeof error.messages.error[0] != 'undefined'){
                                error = error.messages.error[0];
                            }
                        } catch (err) {
                            console.log(err);
                        }
                    }
                    if (typeof error != 'undefined' && error.message) {
                        var message = String(error.message).substr(0, 150);
                    } else {
                        var message = String(error).substr(0, 150);
                    }
                    /** duplicate data -> continue proccess **/
                    if ((response.status == 500 && error.code == 1)) {
                        // self.callRestApi();
                        eventManager.dispatch(item.callBack + '_duplicate', [{'action': item, 'response': response}]);
                        self.is_running = false;
                        return self;
                    }
                    /** error -> stop proccess **/
                    //var removeStatusArray = ['400','403','404','405','406','500'];
                    if (!item.number) {
                        item.number = 1;
                    } else {
                        item.number = 1 + parseInt(item.number);
                    }
                    item.error = message;
                    var actionLogDefered = actionLog().setData(item).save();
                    if (item.number >= 5) {
                        actionLogDefered.done(function () {
                            eventManager.dispatch('reload_action_error', []);
                            eventManager.dispatch(item.callBack + '_error', [{'action': item, 'response': response}]);
                        });
                    }
                    self.is_running = false;
                    return self;
                });
            }
        };
    }
);
