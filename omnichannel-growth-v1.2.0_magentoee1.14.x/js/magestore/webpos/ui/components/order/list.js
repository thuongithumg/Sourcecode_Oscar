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
        'require',
        'jquery',
        'ko',
        'model/sales/order-factory',
        'ui/components/layout',
        'mage/translate',
        'ui/components/base/list/collection-list',
        'model/sales/order/status',
        'eventManager',
        'helper/price',
        'helper/datetime',
        'helper/staff',
        'helper/alert',
        'model/sales/order',
        'helper/general',
        'ui/lib/modal/confirm',
        'model/appConfig',
        'model/container'
    ],
    function (require, $, ko, 
              OrderFactory,
              ViewManager,
              $t,
              listAbstract,
              orderStatus,
              eventManager,
              priceHelper, datetimeHelper,
              staffHelper,
              alertHelper,
              OrderModel,
              generalHelper,
              Confirm,
              AppConfig,
              Container
    ) {
        "use strict";

        return listAbstract.extend({
            items: ko.observableArray([]),
            model: '',
            collection: '',
            isShowHeader: false,
            isSearchable: true,
            pageSize: 10,
            curPage: 1,
            selectedOrder: ko.observable(null),
            searchKey: '',
            groupDays: [],
            isOnline: true,
            currentItemIsExist: false,
            statusObject: orderStatus.getStatusObject(),
            statusBtn: '.wrap-status-orders ul li',
            statusArray: [],
            statusArrayDefault: orderStatus.getStatusArray(),
            viewPermission: [],
            isFirstLoad: true,
            isSearching: ko.observable(false),
            defaults: {
                template: 'ui/order/list',
            },

            initialize: function () {
                this._super();
                this.listenMenuShowContainerAfterEvent();
                this.listenOrderPendingListFilterAfterEvent();
            },

            _processResponse: function (response) {
                var self = this;
                var items = [];
                var orderList = response.items;
                var dayIndex = -1;
                this.currentItemIsExist = false;
                var selectedIndex = 0;
                $.each(orderList, function (index, value) {
                    var createdAt = value.created_at;
                    var day = createdAt.split(' ')[0];
                    if (self.groupDays.indexOf(day.toString()) == -1) {
                        dayIndex++;
                        self.groupDays.push(day);
                        items[dayIndex] = {};
                        items[dayIndex].day = day;
                        items[dayIndex].orderItems = [];
                        items[dayIndex].orderItems.push(value);
                    } else {
                        if (items[self.groupDays.indexOf(day.toString())]) {
                            items[self.groupDays.indexOf(day.toString())].orderItems.push(value);
                        } else {
                            items[self.groupDays.indexOf(day.toString())] = {};
                            items[self.groupDays.indexOf(day.toString())].day = day;
                            items[self.groupDays.indexOf(day.toString())].orderItems = [];
                            items[self.groupDays.indexOf(day.toString())].orderItems.push(value);
                        }
                    }
                    if (self.selectedOrder() == value.entity_id) {
                        self.currentItemIsExist = true;
                        selectedIndex = index;
                    }
                });
                this.loadItem(orderList[selectedIndex]);
                return items;
            },

            _prepareCollection: function () {
                if (this.collection == null) {
                    if (generalHelper.isOnlineCheckout()) {
                        $('#order-list-overlay').show();
                        this.collection =  OrderFactory.get().setMode('online').getCollection();
                    } else {
                        $('#order-list-overlay').show();
                        this.collection =  OrderFactory.get().setMode('offline').getCollection();
                    }
                } else {
                    var mode = true;
                    if (generalHelper.isOnlineCheckout()) {
                        mode = 'online';
                    } else {
                        mode = 'offline';
                    }
                    if (OrderFactory.get().getMode() != mode) {
                        $('#order-list-overlay').show();
                        this.collection =  OrderFactory.get().setMode(mode).getCollection();
                    }
                }
                this.collection.addFieldToFilter('status', 'onhold', 'neq');
                if (staffHelper.isHavePermission('Magestore_Webpos::manage_order_me'))
                    this.viewPermission.push(1);
                if (staffHelper.isHavePermission('Magestore_Webpos::manage_order_location'))
                    this.viewPermission.push(2);
                if (staffHelper.isHavePermission('Magestore_Webpos::manage_all_order'))
                    this.viewPermission.push(3);
                if (staffHelper.isHavePermission('Magestore_Webpos::manage_order_other_staff'))
                    this.viewPermission.push(4);

                if (this.viewPermission.indexOf(3) >= 0) {
                } else {
                    if(!(this.viewPermission.indexOf(1) >= 0 && this.viewPermission.indexOf(4) >= 0)){
                        if (this.viewPermission.indexOf(1) >= 0)
                            this.collection.addFieldToFilter('webpos_staff_id', staffHelper.getStaffId(), 'eq');
                        if (this.viewPermission.indexOf(4) >= 0)
                            this.collection.addFieldToFilter('webpos_staff_id', staffHelper.getStaffId(), 'neq');
                    }
                    if (this.viewPermission.indexOf(2) >= 0)
                        this.collection.addFieldToFilter('webpos_staff_id', window.webposConfig.locationId, 'eq');
                }
                this.collection.setPageSize(this.pageSize);
                this.collection.setCurPage(this.curPage);
                this.collection.setOrder('created_at', 'DESC');
                if (this.statusArray.length > 0) {
                    this.collection.addFieldToFilter('status', this.statusArray, 'in');
                }
                // else {
                //     this.collection.addFieldToFilter('status', this.statusArrayDefault, 'in');
                // }
                if (this.searchKey) {
                    this.collection.addFieldToFilter(
                        [
                            ['increment_id',"%" + this.searchKey.toLowerCase() + "%", 'like'],
                            ['customer_email',"%" + this.searchKey.toLowerCase() + "%", 'like'],
                            ['customer_firstname',"%" + this.searchKey.toLowerCase() + "%", 'like'],
                            ['customer_lastname',"%" + this.searchKey.toLowerCase() + "%", 'like']
                        ]
                    );
                }
            },

            _prepareItems: function () {
                var self = this;
                this.groupDays = [];
                var deferred = $.Deferred();
                this.getCollection().load(deferred);
                self.isSearching(true);
                deferred.done(function (response) {
                    $('#order-list-overlay').hide();
                    var items = self._processResponse(response);
                    self.items(items);
                    self.finishLoading();
                }).always(function(){
                    self.isSearching(false);
                });
            },

            lazyload: function (element, event) {
                var scrollHeight = event.target.scrollHeight;
                var clientHeight = event.target.clientHeight;
                var scrollTop = event.target.scrollTop;
                if (scrollHeight - (clientHeight + scrollTop) <= 0 && this.canLoad() === true) {
                    this.startLoading();
                    this.pageSize += 10;
                    this.refresh = false;
                    this._prepareItems();
                }
            },

            formatDateGroup: function (dateString) {
                var date = "";
                if (!dateString) {
                    date = new Date();
                } else {
                    date = new Date(dateString);
                }
                var month = date.getMonth() + 1;
                if (month < 10) {
                    month = "0" + month;
                }
                return date.getDate() + '/' + month + '/' + date.getFullYear();
            },

            orderSearch: function (data, event) {
                this.collection.reset();
                this.searchKey = event.target.value;
                this._prepareItems();
            },

            filterStatus: function (data, event) {
                this.isOnline = false;
                var el = $(event.currentTarget);
                if (el.hasClass('selected')) {
                    el.removeClass('selected');
                    this.statusArray.splice(this.statusArray.indexOf(el.attr('status')), 1);
                }
                else {
                    el.addClass('selected');
                    this.statusArray.push(el.attr('status'));
                }
                this._prepareItems();
            },

            resetFilterStatus: function () {
                var self = this;
                this.statusArray = [];
                $.each($(this.statusBtn), function (index, value) {
                    $(value).removeClass('selected');
                })
            },

            loadItem: function (data, event) {
                var viewManager = require('ui/components/layout');
                eventManager.dispatch('sales_order_list_load_order', {'order': data});
                if (!this.orderViewObject) {
                    this.orderViewObject = viewManager.getSingleton('ui/components/order/detail');
                }
                this.orderViewObject.setData(data, this);
                viewManager.getSingleton('ui/components/order/action').setData(data, this);
                this.selectedOrder(data ? data.entity_id : null);
            },

            updateOrderListData: function (item) {
                this._prepareItems();
                return true;
                var items = this.items();
                for (var index in items) {
                    var createdAt = item.created_at;
                    var day = createdAt.split(' ')[0];
                    if (day == items[index].day) {
                        for (var i in items[index].orderItems) {
                            if (item.entity_id == items[index].orderItems[i].entity_id) {
                                items[index].orderItems[i] = item;
                                this.resetData();
                                this.setItems(items);
                                this.loadItem(null);
                                this.loadItem(item);
                            }
                        }
                    }
                }
            },

            getCustomerName: function (data) {
                if (data.customer_firstname && data.customer_lastname)
                    return data.customer_firstname + ' ' + data.customer_lastname;
                if (data.customer_email)
                    return data.customer_email;
                if (data.billing_address) {
                    if (data.billing_address.firstname && data.billing_address.lastname)
                        return data.billing_address.firstname + ' ' + data.billing_address.lastname;
                    if (data.billing_address.email)
                        return data.billing_address.email;
                }

            },

            getGrandTotal: function (data) {
                return priceHelper.convertAndFormat(data.base_grand_total);
            },

            getCreatedAt: function (data) {
                return this.getTime(data.created_at);
            },

            /**
             * return a date time with format: 15:26 PM
             * @param dateString
             * @returns {string}
             */
            getTime: function (dateString) {
                var currentTime = datetimeHelper.stringToCurrentTime(dateString);
                return datetimeHelper.getTime(currentTime);
            },

            render: function () {
                var self = this;
                self._render();
                eventManager.observer('order_pull_after', function (event, data) {
                    if (data && data.status == 'notsync')
                        self.loadItem(null);
                    self.isOnline = false;
                    self._prepareItems();
                });
                eventManager.observer('show_container_after', function (event, id) {
                    if (id == "orders_history") {
                        self._prepareItems();
                    }
                });
                if (staffHelper.isHavePermission('Magestore_Webpos::view_order_me'))
                    this.viewPermission.push(1);
                if (staffHelper.isHavePermission('Magestore_Webpos::manage_order_location'))
                    this.viewPermission.push(2);
                if (staffHelper.isHavePermission('Magestore_Webpos::view_all_order'))
                    this.viewPermission.push(3);
                if (this.isFirstLoad) {
                    this._prepareItems();
                    this.isFirstLoad = false;
                }
            },

            listenMenuShowContainerAfterEvent: function () {
                var self = this;
                eventManager.observer('orders_history_show_container_after', function (event, eventData) {
                    self._prepareItems();
                });
            },

            listenOrderPendingListFilterAfterEvent: function () {
                var self = this;
                eventManager.observer('show_customer_pending_orders', function (event, eventData) {
                    var containerId = 'orders_history';
                    var isShowing = Container.toggleArea(containerId);
                    if(isShowing){
                        var el = $( "[status='pending']" );
                        $('#search-header-order').val(eventData);
                        self.searchKey = '';
                        self.searchKey = eventData;
                        var pendingStatusEl = $("#webpos_order_list .wrap-status-orders li[status='pending']");
                        if(!pendingStatusEl.hasClass('selected')){
                            pendingStatusEl.click();
                        }else{
                            if (self.isFirstLoad) {
                                self.isFirstLoad = false;
                            }
                            self._prepareItems();
                        }
                    }
                });
            }

        });
    }
);