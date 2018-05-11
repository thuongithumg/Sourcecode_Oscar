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
        'model/object-manager',
        'ui/components/layout',
        'eventManager',
        'action/notification/add-notification',
        'mage/translate',
        'model/synchronization/synchronization-factory',
        'model/config/config-factory',
        'helper/general'
    ],
    function ($, ko, ObjectManager, ViewManager, eventManager, addNotification, Translate, SynchronizationFactory, ConfigFactory, Helper) {
        "use strict";
        var MODE_INSTALL = 'install';
        var MODE_UPDATE = 'update';
        var MODE_FINISH = 'finish';
        return function (model) {
            this.label = model.label,
            this.actionText = ko.observable(''),
            this.model = ObjectManager.get(model.model),
            this.id = this.model.sync_id,
            this.config = ConfigFactory.get(),
            this.synchronization = SynchronizationFactory.get(),
            this.mode = '',
            this.curPage = 1,
            this.timeAuto = 0,
            this.updated = 0,
            this.total = 0,
            this.interval = '',
            this.limitPageInstall = model.limitPage,
            this.pageSize = 200,
            this.percent = ko.observable(0),
            this.isVisibility = ko.observable(''),
            this.running = ko.observable(false),
            this.finishedUpdate = ko.observable(false),
            this.updated_date = ko.observable('N/A'),
            this.next_date = ko.observable('N/A'),
            this.doneDeferred = '',
            this.setMode = function (mode) {
                this.mode = mode;
                return this;
            },
            this.initialize = function (endDeferred) {
                var self = this;

                if (model.pageSize) {
                    self.pageSize = model.pageSize;
                }
                self.doneDeferred = endDeferred;
                eventManager.observer('synchronization_show_container_after', function () {
                    self.finishedUpdate(false);
                });

                if (self.id == 'stock_item') {
                    eventManager.observer('product_proccess_update', function () {
                        self.processUpdate($.Deferred());
                    });
                }
                if (self.id == 'product') {
                    eventManager.observer('stock_item_proccess_update', function () {
                        self.processUpdate($.Deferred());
                    });
                }

                /** auto update data **/
                var idTimeAuto = 'webpos/offline/' + self.id + '_time';
                var timeAutoDeferred = self.config.load(idTimeAuto);
                timeAutoDeferred.done(function (data) {
                    if (data.value && parseInt(data.value) > 0) {
                        self.timeAuto = parseInt(data.value);
                        self.interval = window.setInterval(self.checkUpdate.bind(self), parseInt(self.timeAuto) * 60000);
                    }
                });
            },

            this.install = function () {
                var self = this;
                /** check data and install **/
                var id = 'webpos/install/' + self.id;
                var deferred = self.synchronization.load(id);
                deferred.fail(function (err) {
                    self.doneDeferred.reject(err);
                });
                deferred.done(function (data) {
                    if (!data.value) {
                        if (!self.running()) {
                            self.mode = MODE_INSTALL;
                            self.processUpdate(self.doneDeferred);
                        }
                    } else {
                        self.doneDeferred.resolve(true);
                    }
                });
            },
            this.update = function () {
                var self = this;
                /** check data and update **/
                var id = 'webpos/install/' + self.id;
                var deferred = self.synchronization.load(id);
                deferred.fail(function (err) {
                    self.doneDeferred.reject(err);
                });
                deferred.done(function (data) {
                    if (data.value == MODE_FINISH) {
                        self.isVisibility('hidden');
                        self.mode = data.value;
                        self.doneDeferred.resolve(true);
                        /** get updated time **/
                        var id = 'webpos/updated/' + self.id;
                        var updatedDeferred = self.synchronization.load(id);
                        updatedDeferred.done(function (updated) {
                            if (updated.value) {
                                self.updated_date(updated.value);
                                var now = new Date();
                                var updatedTime = new Date(updated.value);
                                updatedTime.setDate(updatedTime.getDate() + 1);
                                if (updatedTime <= now) {
                                    self.processUpdate($.Deferred());
                                }
                            }
                        });
                    } else if (data.value == MODE_UPDATE) {
                        self.doneDeferred.resolve(true);
                        if (!self.running()) {
                            self.mode = MODE_UPDATE;
                            var curpageDeferred = self.synchronization.load('webpos/curpage/' + self.id);
                            curpageDeferred.done(function (data) {
                                if (data.value) {
                                    self.curPage = data.value + 1;
                                    self.updated = data.value * self.pageSize;
                                }
                                self.pullData();
                            });
                            curpageDeferred.fail(function () {
                                self.pullData();
                            });
                        }
                    } else if (!data.value) {
                        if (!self.running()) {
                            self.mode = MODE_INSTALL;
                            self.processUpdate(self.doneDeferred);
                        }
                    } else {
                        self.doneDeferred.resolve(true);
                    }
                });
            },
            this.checkUpdate = function () {
                var self = this;
                if(Helper.isOnlineCheckout()){
                    return false;
                }
                if (self.mode == MODE_FINISH) {
                    var id = 'webpos/next/' + self.id;
                    var deferred = self.synchronization.load(id);
                    var time = '';
                    deferred.done(function (data) {
                        if (data.value) {
                            self.next_date(data.value);
                            time = data.value;
                        }
                        var now = new Date();
                        var next = new Date(time);
                        if (!time || next <= now) {
                            self.processUpdate($.Deferred());
                        }
                    });
                }
            },
            this.processUpdate = function (processUpdateDeferred) {
                var self = this;
                if (!processUpdateDeferred || typeof processUpdateDeferred.resolve != 'function') {
                    processUpdateDeferred = $.Deferred();
                    if (self.id == 'product') {
                        eventManager.dispatch('product_proccess_update', []);
                    }
                    if (self.id == 'stock_item') {
                        eventManager.dispatch('stock_item_proccess_update', []);
                    }
                    self.actionText('Updating...');
                    if (!checkNetWork) {
                        addNotification(Translate('Cannot connect to your server!'), true, 'danger', 'Error');
                    }
                }
                if (self.running() || !checkNetWork) {
                    processUpdateDeferred.reject({});
                    return processUpdateDeferred;
                }
                self.percent(0);
                self.isVisibility('');
                self.finishedUpdate(false);
                self.running(true);
                window.synchrunning = true;
                clearInterval(self.interval);
                var curpageDeferred = self.synchronization.load('webpos/curpage/' + self.id);
                curpageDeferred.done(function (data) {
                    if (data.value) {
                        self.curPage = data.value + 1;
                        self.updated = data.value * self.pageSize;
                    }
                    self.pullData(processUpdateDeferred);
                });
                curpageDeferred.fail(function (err) {
                    self.pullData(processUpdateDeferred);
                });
                return processUpdateDeferred;
            },
            this.pullData = function (pullDataDeferred) {
                var self = this;
                if (!pullDataDeferred) {
                    pullDataDeferred = $.Deferred();
                }
                if (self.curPage == 1) {
                    self.updated = 1;
                    self.total = 100;
                    self.percent(1);
                    // self.increasePecent();
                }
                var pulldeferred = $.Deferred();
                /** put filter value to get data from server **/
                if (model.filter && (!model.filter.mode || self.mode == model.filter.mode)) {
                    var id = model.filter.config;
                    var dateFilter = {
                        field: '',
                        value: '',
                        condition: 'gteq'
                    };
                    if (model.filter.is_config) {
                        var updatedeferred = self.config.load(id);
                    } else {
                        var updatedeferred = self.synchronization.load(id);
                    }
                    updatedeferred.fail(function (err) {
                        self.running(false);
                        window.synchrunning = false;
                        self.isVisibility('hidden');
                        self.doneDeferred.reject(err);
                        pullDataDeferred.reject(err);
                    });
                    updatedeferred.done(function (data) {
                        if (data.value && model.filter.datetime === true) {
                            dateFilter.value = self.formatDate(new Date(data.value));
                        } else if (data.value && data.value > 0) {
                            var timePeriod = new Date();
                            timePeriod.setDate(timePeriod.getDate() - parseInt(data.value));
                            dateFilter.value = timePeriod.toDateString() + ' ' + timePeriod.toLocaleTimeString();
                            dateFilter.value = self.formatDate(new Date(timePeriod.getTime()));
                        }
                        if (model.filter.field) {
                            dateFilter.field = model.filter.field;
                        }
                        if (model.filter.condition) {
                            dateFilter.condition = model.filter.condition;
                        }
                        self.model.pullData(pulldeferred, self.pageSize, self.curPage, dateFilter);
                    });
                } else {
                    self.model.pullData(pulldeferred, self.pageSize, self.curPage);
                }
                pulldeferred.done(function (data) {
                    self.total = data.total;
                    checkNetWork = true;
                    self.updated = self.updated + data.updated;
                    if (self.id == 'product' && self.mode == MODE_UPDATE || ViewManager.getSingleton('ui/components/catalog/product-list').syncPercent() != 100) {
                        ViewManager.getSingleton('ui/components/catalog/product-list').setSyncPercent(parseInt(self.updated * 100 / self.total));
                    }
                    self.increasePecent();
                    /** finish when:
                     * There is no item to update
                     * Update full item
                     * install with limit page
                     * **/
                    if (self.total <= 0 || parseInt(self.updated * 100 / self.total) >= 100) {
                        self.mode = MODE_FINISH;
                        self.synchronization.setData({
                            id: 'webpos/curpage/' + self.id,
                            value: 0
                        }).save();
                        if (self.total > 0) {
                            eventManager.dispatch(self.id + '_finish_pull_after', []);
                            // window.synchrunning = false;
                        }
                        self.finishPullData(pullDataDeferred);
                    } else if ((self.mode == MODE_INSTALL && self.limitPageInstall && self.limitPageInstall <= self.curPage)) {
                        self.mode = MODE_UPDATE;
                        self.synchronization.setData({
                            id: 'webpos/curpage/' + self.id,
                            value: self.curPage
                        }).save();
                        eventManager.observer('finish_install', function () {
                            if (self.mode == MODE_UPDATE) {
                                self.curPage = self.limitPageInstall + 1;
                                self.updated = self.limitPageInstall * self.pageSize;
                                self.pullData(pullDataDeferred);
                            }
                        });
                        self.finishPullData(pullDataDeferred);
                    } else {
                        self.synchronization.setData({
                            id: 'webpos/curpage/' + self.id,
                            value: self.curPage
                        }).save();
                        self.curPage = self.curPage + 1;
                        self.status = 'sync';
                        self.pullData(pullDataDeferred);
                    }
                });
                pulldeferred.fail(function (err) {
                    self.running(false);
                    window.synchrunning = false;
                    self.isVisibility('hidden');
                    if (err.statusText == 'error' && err.status == 0) {
                        checkNetWork = false;
                    }
                    self.doneDeferred.reject(err);
                    var error = JSON.parse(err.responseText);
                    var message = 'Have error while syncing ${self.id}.';

                    if(error && error.messages !== undefined){
                        message = error.messages.error[0].message;
                    }

                    Helper.alert({
                        priority:"danger",
                        title: Helper.__("Error"),
                        message: message
                    });
                });
                return pullDataDeferred;
            },
            this.finishPullData = function (finishPullDataDeferred) {
                var self = this;
                if (!finishPullDataDeferred) {
                    finishPullDataDeferred = $.Deferred();
                }
                /** save current mode **/
                var modedeferred = self.synchronization.setData({
                    id: 'webpos/install/' + self.id,
                    value: self.mode
                }).save();

                /** save updated time **/
                modedeferred.fail(function (err) {
                    self.doneDeferred.resolve(err);
                });
                modedeferred.done(function (data) {
                    /** set update time **/
                    var now = new Date();
                    var nowdeferred = self.synchronization.setData({
                        id: 'webpos/updated/' + self.id,
                        value: now.toDateString() + ' ' + now.toLocaleTimeString()
                    }).save(nowdeferred);
                    nowdeferred.done(function (data) {
                        if (data.value) {
                            self.updated_date(data.value);
                        }
                        if (self.doneDeferred && typeof self.doneDeferred.resolve == 'function') {
                            self.doneDeferred.resolve(true);
                        }
                        if (self.timeAuto && self.mode == MODE_FINISH) {
                            var next = now;
                            next.setMinutes(next.getMinutes() + parseInt(self.timeAuto));
                            /** set next update time **/
                            var nextdeferred = self.synchronization.setData({
                                id: 'webpos/next/' + self.id,
                                value: next.toDateString() + ' ' + next.toLocaleTimeString()
                            }).save(nextdeferred);
                            nextdeferred.done(function (data) {
                                if (data.value) {
                                    self.next_date(data.value);
                                    self.interval = window.setInterval(self.checkUpdate.bind(self), parseInt(self.timeAuto) * 60000);
                                }
                            });
                        }
                        finishPullDataDeferred.resolve(true);
                    });
                });
                return finishPullDataDeferred;
            },
            this.clearData = function (clearDataDeferred) {
                var self = this;
                if (!clearDataDeferred || typeof clearDataDeferred.resolve != 'function') {
                    clearDataDeferred = $.Deferred();
                    if (self.id == 'product') {
                        eventManager.dispatch('product_proccess_update', []);
                    }
                    if (self.id == 'stock_item') {
                        eventManager.dispatch('stock_item_proccess_update', []);
                    }
                    if (!checkNetWork) {
                        addNotification(Translate('Cannot connect to your server!'), true, 'danger', 'Error');
                    }
                }
                if (self.running() || !checkNetWork) {
                    clearDataDeferred.reject({});
                    return clearDataDeferred;
                }
                self.actionText('Reloading...');
                var deferred = self.model.clear();
                deferred.done(function () {
                    var id = 'webpos/install/' + self.id;
                    var cleardeferred = self.synchronization.delete(id);
                    cleardeferred.done(function (data) {
                        self.mode = MODE_UPDATE,
                            self.curPage = 1;
                        self.updated = 0;
                        self.total = 0;
                        self.percent(0);
                        var deleteDeferred = self.synchronization.delete('webpos/curpage/' + self.id);
                        deleteDeferred.always(function () {
                            self.processUpdate(clearDataDeferred);
                        });
                    });
                });
                return clearDataDeferred;
            },
            this.formatDate = function (dateTime) {
                var self = this;
                return dateTime.getUTCFullYear() + "-" + self.twoDigits(1 + dateTime.getUTCMonth()) + "-" + self.twoDigits(dateTime.getUTCDate()) + " " + self.twoDigits(dateTime.getUTCHours()) + ":" + self.twoDigits(dateTime.getUTCMinutes()) + ":" + self.twoDigits(dateTime.getUTCSeconds());
            },
            this.twoDigits = function (n) {
                return n > 9 ? "" + n : "0" + n;
            },
            this.increasePecent = function () {
                var self = this;
                var from = parseInt(self.percent());
                var to = 0;
                if (self.total) {
                    to = parseInt(self.updated * 100 / self.total);
                } else {
                    to = 100;
                }
                if (to) {
                    var increase = window.setInterval(
                        function () {
                            if ((from + 5) < to && from < 100) {
                                from = from + 5;
                                self.percent(from);
                            } else {
                                if (to < 100) {
                                    self.percent(to);
                                } else {
                                    self.percent(100);
                                }
                                clearInterval(increase);
                            }
                            if (self.percent() >= 100) {
                                self.finishedUpdate(true);
                                self.synchronization.setData({
                                    id: 'webpos/curpage/' + self.id,
                                    value: 0
                                }).save();
                                setTimeout(function () {
                                    self.isVisibility('hidden');
                                    self.curPage = 1;
                                    self.running(false);
                                    //window.synchrunning = false;
                                }, 5000);
                            }
                        },
                        100
                    );
                }
            }
        }
    }
);