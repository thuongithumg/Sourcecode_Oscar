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
        'uiComponent',
        'model/log/action-log',
        'helper/datetime',
        'model/resource-model/magento-rest/abstract',
        'helper/full-screen-loader',
        'action/notification/add-notification',
        'eventManager',
        'mage/translate',
        'ui/lib/modal/confirm'
    ],
    function ($, ko, Component, actionLog, datetime, onlineAbstract, loader, addNotification, eventManager, Translate, confirm) {
        "use strict";

        return Component.extend({
            errorList: ko.observableArray([]),
            types: ko.observableArray([]),
            typeData: ['All'],
            type: ko.observable('All'),
            data: '',
            initialize: function () {
                this._super();
                var self = this;
                eventManager.observer('reload_action_error', function () {
                    self.filterActionLog();
                });
                self.filterActionLog();
            },
            filterActionLog: function (self, type) {
                if (!self)
                    self = this;
                if (type) {
                    self.type(type);
                }
                self.errorList([]);
                self.types([]);
                var collection = actionLog().getCollection().reset();
                // collection.addFieldToFilter('number', '5', 'gteq');
                if (self.type() && self.type() != 'All') {
                    collection.addFieldToFilter('type', self.type(), 'eq');
                }
                var actionLogDefered = collection.load();
                actionLogDefered.done(function (data) {
                    self.data = data.items;
                    $.each(data.items, function (index, value) {
                        if (value.type && self.typeData.indexOf(value.type) < 0) {
                            self.typeData.push(value.type);
                        }
                        var data = {};
                        data.index = index;
                        data.visibleView = (value.type =='customer' || value.type =='order');
                        data.actionId = value.action_id;
                        if (value.error) {
                            data.error = '' + value.error;
                        } else {
                            data.error = 'N/A';
                        }
                        if (value.key_path && value.interfaceName && value.payload[value.interfaceName] && value.payload[value.interfaceName][value.key_path]) {
                            data.actionId = value.payload[value.interfaceName][value.key_path];
                        }
                        data.createdAt = datetime.getFullDate(value.order);
                        self.errorList.push(data);
                    });
                    $.each(self.typeData, function (index, value) {
                        self.types.push(value);
                    });

                });
            },
            viewDetail: function (data) {
                var self = this;
                var item = self.data[data.index];
                if (item.type == 'customer') {
                    $('#synchronization_container').removeClass('active');
                    $('#customer_list_container').addClass('active');
                    $('#customer_list_container #search-header-customer').val(item.payload[item.interfaceName].email);
                    eventManager.dispatch('customer_search_after', [item.payload[item.interfaceName].email]);
                }
                if (item.type == 'order') {
                    $('#synchronization_container').removeClass('active');
                    $('#orders_history_container').addClass('active');
                    $('#orders_history_container #search-header-order').val(item.action_id);
                    eventManager.dispatch('order_search_after', [item.action_id]);
                }
            },
            delete: function (data) {
                var self = this;
                var item = self.data[data.index];
                confirm({
                    content: Translate("Are you sure you want to delete?"),
                    actions: {
                        confirm: function () {
                            if (item.action_id) {
                                var deleteDefered = actionLog().delete(item.action_id);
                                deleteDefered.done(function () {
                                    self.filterActionLog();
                                });
                            }
                        }
                    }
                });
            },
            tryAgain: function (data) {
                var self = this;
                var item = self.data[data.index];
                var deferred = $.Deferred();
                if (item.api_url && item.method) {
                    loader.startLoader();
                    onlineAbstract().setPush(true).setLog(false).callRestApi(
                        item.api_url, item.method, item.params, item.payload, deferred, item.callBack
                    );
                    deferred.done(function (response) {
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
                                    });
                                });
                            } else {
                                var deleteLog = actionLog().delete(item.action_id);
                            }
                        });
                    });
                    deferred.fail(function (response) {
                        var error = {};
                        if (response.responseText) {
                            try {
                                error = JSON.parse(response.responseText);
                                if(typeof error.messages.error[0] != 'undefined'){
                                    error = error.messages.error[0];
                                }
                            } catch (err) {
                                error = response.responseText
                            }
                        }
                        if (typeof error != 'undefined' && error.message) {
                            var message = String(error.message).substr(0, 255);
                        } else {
                            var message = String(error).substr(0, 255);
                        }
                        addNotification(message, true, 'danger', 'Error');
                    });
                    deferred.always(function () {
                        loader.stopLoader();
                    });
                }
            }
        });
    }
);