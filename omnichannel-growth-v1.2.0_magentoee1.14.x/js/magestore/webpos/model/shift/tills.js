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
        'ko',
        'jquery',
        'helper/general',
        'model/session',
        'dataManager',
        'model/resource-model/magento-rest/shift/tills',
        'helper/staff'
    ],
    function (ko, $, Helper, Session, DataManager, OnlineResource, Staff) {
        "use strict";

        var Tills = {
            DATA_MANAGER_TILL_ID: 'till_id',
            LOCAL_CURRENT_TILL_ID: 'current_till_id',
            LOCAL_AVAILABLE_TILLS_KEY: 'available_tills',
            ENABLE_CONFIG_PATH: 'webpos/general/enable_tills',
            ENABLE: '1',
            /**
             * List available cash drawer
             */
            tills: ko.observableArray([]),
            /**
             * Current cash drawer
             */
            currentTill: ko.observable(),
            /**
             * Init data from local
             */
            initData: function () {
                var self = this;
                self.isEnable = ko.pureComputed(function () {
                    var isEnable = Helper.getBrowserConfig(self.ENABLE_CONFIG_PATH);
                    return (isEnable == self.ENABLE) ? true : false;
                });
                self.tills(self.getLocalTills());
                self.currentTill(self.getCurrentLocalTill());
                self.isTillsEmpty = ko.pureComputed(function () {
                    return (self.tills().length == 0) ? true : false;
                });
                if (self.isEnable()) {
                    DataManager.setData(self.DATA_MANAGER_TILL_ID, self.currentTill().id);
                } else {
                    self.clear();
                }
                return self;
            },
            /**
             * Get list available cash drawer from local
             * @returns {Array}
             */
            getLocalTills: function () {
                var self = this;
                var selectedId = Helper.getLocalConfig(self.LOCAL_CURRENT_TILL_ID);
                var availableTills = Helper.getLocalConfig(self.LOCAL_AVAILABLE_TILLS_KEY);
                var tills = [];
                if (availableTills) {
                    availableTills = JSON.parse(availableTills);
                    $.each(availableTills, function (id, title) {
                        tills.push({
                            id: id,
                            title: title,
                            selected: (selectedId && (id == selectedId)) ? true : false
                        });
                    })
                    if (tills.length == 1) {
                        tills[0].selected = true;
                        Helper.saveLocalConfig(self.LOCAL_CURRENT_TILL_ID, tills[0].id)
                    }
                }
                return tills;
            },
            /**
             * Get current till
             * @returns {{id: string, title: string}}
             */
            getCurrentLocalTill: function () {
                var self = this;
                var tills = self.getLocalTills();
                var tillId = Helper.getLocalConfig(self.LOCAL_CURRENT_TILL_ID);
                var till = {id: '', title: ''};
                if (tills.length > 0) {
                    var selectedTill = ko.utils.arrayFirst(tills, function (data) {
                        return (tillId && tillId == data.id) ? true : false;
                    });
                    if (selectedTill) {
                        till = selectedTill;
                    }
                }
                return till;
            },
            /**
             * Check and show popup select cash drawer if needed
             */
            validate: function () {
                var self = this;
                var currentTill = self.currentTill();
                var tills = self.tills();
                var session = Session.getId();
                return ((!currentTill.id || tills.length == 0) && session) ? true : false;
            },
            /**
             * Clear local data
             */
            clear: function () {
                var self = this;
                Helper.saveLocalConfig(self.LOCAL_CURRENT_TILL_ID, '');
                Helper.saveLocalConfig(self.LOCAL_AVAILABLE_TILLS_KEY, '');
            },
            /**
             * Select till
             * @param id
             */
            select: function (id) {
                if (id) {
                    var self = this;
                    Helper.saveLocalConfig(self.LOCAL_CURRENT_TILL_ID, id);
                    self.initData();
                    var assignRequest = $.Deferred();
                    var params = {
                        pos_id: id,
                        staff_id: Staff.getStaffId(),
                    };
                    OnlineResource().setPush(true).assign(params, assignRequest);
                    return (self.currentTill().id) ? true : false;
                }
                return false;
            }
        };
        return Tills.initData();
    }
);