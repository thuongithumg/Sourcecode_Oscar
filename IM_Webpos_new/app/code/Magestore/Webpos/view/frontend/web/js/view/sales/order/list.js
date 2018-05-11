/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'require',
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/sales/order-factory',
        'Magestore_Webpos/js/view/layout',
        'mage/translate',
        'Magestore_Webpos/js/view/base/list/collection-list',
        'Magestore_Webpos/js/model/sales/order/status',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/datetime',
        'Magestore_Webpos/js/helper/staff',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/view/layout'
    ],
    function (require, $, ko, OrderFactory, ViewManager, $t,
              listAbstract,
              orderStatus, eventManager,
              priceHelper, datetimeHelper, staffHelper,
              Helper, Layout) {
        "use strict";

        var hadObserver = ko.observable(false);

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
            statusObject: ko.observableArray(orderStatus.getStatusObject()),
            statusBtn: '.wrap-status-orders ul li',
            statusArray: [],
            statusArrayDefault: orderStatus.getStatusArray(),
            viewPermission: [],
            isFirstLoad: true,
            isSearching: ko.observable(false),
            defaults: {
                template: 'Magestore_Webpos/sales/order/list',
            },

            initialize: function () {
                this._super();
                this.render();
                if (hadObserver() == false) {
                    hadObserver(true);
                    this.listenOnHoldAfterEvent();
                }
                this.listenOrderPendingListFilterAfterEvent();
            },

            _processResponse: function (response) {
                var self = this;
                var items = [];
                var orderList = response.items;
                var dayIndex = -1;
                this.currentItemIsExist = false;
                $.each(orderList, function (index, value) {
                    var createdAt = new Date(value.created_at.split(' ').join('T'));
                    var day = createdAt.toLocaleDateString();
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
                    }
                });
                if (!this.currentItemIsExist)
                    this.loadItem(orderList[0]);
                return items;
            },

            _prepareItems: function () {
                var self = this;
                this.startLoading();
                if (self.isSearching()) {
                    return false;
                }
                self.isSearching(true);
                this.groupDays = [];
                var mode = (Helper.isUseOnline('orders')) ? 'online' : 'offline';
                this.collection = OrderFactory.get().setMode(mode).getCollection().reset();
                if (this.viewPermission.length == 0) {
                    this.setItems([]);
                    self.isSearching(false);
                    return;
                }
                if (this.searchKey)
                    this.collection.addFieldToFilter(
                        [
                            ['increment_id', '%' + this.searchKey.toLowerCase() + '%', 'like'],
                            ['customer_email', '%' + this.searchKey.toLowerCase() + '%', 'like'],
                            ['customer_firstname', '%' + this.searchKey.toLowerCase() + '%', 'like'],
                            ['customer_lastname', '%' + this.searchKey.toLowerCase() + '%', 'like'],
                            ['customer_fullname', '%' + this.searchKey.toLowerCase() + '%', 'like']
                        ]
                    );
                if (this.statusArray.length > 0)
                    this.collection.addFieldToFilter('status', this.statusArray, 'in');
                else
                    this.collection.addFieldToFilter('status', this.statusArrayDefault, 'in');
                if (this.viewPermission.indexOf('manage_all_order') >= 0) {
                } else {
                    if (this.viewPermission.indexOf('manage_order_me') >= 0 && this.viewPermission.indexOf('manage_order_location') >= 0)
                        this.collection.addFieldToFilter(
                            [
                                ['webpos_staff_id', staffHelper.getStaffId(), 'eq'],
                                ['location_id', window.webposConfig.locationId, 'eq'],
                            ]
                        );
                    else if (this.viewPermission.indexOf('manage_order_me') >= 0)
                        this.collection.addFieldToFilter('webpos_staff_id', staffHelper.getStaffId(), 'eq');
                    else if (this.viewPermission.indexOf('manage_order_location') >= 0)
                        this.collection.addFieldToFilter('location_id', window.webposConfig.locationId, 'eq');
                }

                this.collection.setOrder('created_at', 'DESC');
                this.collection.setPageSize(this.pageSize).setCurPage(this.curPage);
                var deferred = $.Deferred();
                this.collection.load(deferred);
                deferred.done(function (response) {
                    self.isOnline = true;
                    var items = self._processResponse(response);
                    self.items(items);
                    self.finishLoading();
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
                return dateString;
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
                if (data != null) {
                    data.forced_can_creditmemo = false;
                }
                if (!ViewManager) {
                    ViewManager = require('Magestore_Webpos/js/view/layout');
                }
                var eventData = {order: data};
                eventManager.dispatch('sales_order_list_load_order', eventData);
                if (!this.orderViewObject) {
                    this.orderViewObject = ViewManager.getSingleton('view/sales/order/view');
                }
                this.orderViewObject.setData(eventData.order, this);
                ViewManager.getSingleton('view/sales/order/action').setData(eventData.order, this);
                this.selectedOrder(eventData.order ? eventData.order.entity_id : null);
            }
            ,

            updateOrderListData: function (item) {
                var items = this.items();
                for (var index in items) {
                    var createdAt = item.created_at;
                    var createdAt = new Date(createdAt.split(' ').join('T'));
                    var day = createdAt.toLocaleDateString();
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
                var self = this;
                var grandTotal = 0;
                if (window.webposConfig.currentCurrencyCode == data.order_currency_code) {
                    grandTotal = data.grand_total;
                } else {
                    if (window.webposConfig.currentCurrencyCode == data.base_currency_code) {
                        grandTotal = data.base_grand_total;
                    } else {
                        grandTotal = priceHelper.currencyConvert(data.base_grand_total, data.base_currency_code, window.webposConfig.currentCurrencyCode);
                    }
                }
                return priceHelper.formatPrice(grandTotal);
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
                eventManager.observer('add_order_status_object', function (event, newStatus) {
                    if (newStatus) {
                        var addedStatus = ko.utils.arrayFirst(self.statusObject(), function (status) {
                            return (newStatus.statusClass == status.statusClass);
                        });
                        if (!addedStatus) {
                            self.statusObject.push(newStatus);
                        }
                    }
                });
                if (staffHelper.isHavePermission('Magestore_Webpos::manage_order_me'))
                    this.viewPermission.push('manage_order_me');
                if (staffHelper.isHavePermission('Magestore_Webpos::manage_order_location'))
                    this.viewPermission.push('manage_order_location');
                if (staffHelper.isHavePermission('Magestore_Webpos::manage_all_order'))
                    this.viewPermission.push('manage_all_order');
                if (this.isFirstLoad) {
                    this._prepareItems();
                    this.isFirstLoad = false;
                }
                eventManager.dispatch('order_list_render_after', '');
            },

            listenOnHoldAfterEvent: function () {
                var self = this;
                eventManager.observer('orders_history_show_container_after', function (event, eventData) {
                    self.render();
                });
            },

            listenOrderPendingListFilterAfterEvent: function () {
                var self = this;
                if (!ViewManager) {
                    try {
                        ViewManager = require('Magestore_Webpos/js/view/layout');
                    } catch (err) {
                        location.reload();
                    }
                }
                var container = ViewManager.getSingleton('view/container');
                eventManager.observer('show_customer_pending_orders', function (event, eventData) {
                    var containerId = 'orders_history_container';
                    container.init(containerId);
                    var isShowing = container.toggleArea();
                    if (isShowing) {
                        ViewManager.getSingleton('view/checkout/cart').switchToCart();
                        var el = $("[status='pending']");
                        $('#search-header-order').val(eventData).trigger("change");
                        self.searchKey = '';
                        self.searchKey = eventData;
                        /*$('#webpos_order_list .pending').click();*/
                        /*var pendingStatusEl = $("#webpos_order_list .wrap-status-orders li[status='pending']");
                         if(!pendingStatusEl.hasClass('selected')){
                         pendingStatusEl.click();
                         }else{
                         if (self.isFirstLoad) {
                         self.isFirstLoad = false;
                         }
                         self._prepareItems();
                         }*/
                    }
                });
            }

        });
    }
);