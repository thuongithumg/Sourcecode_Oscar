/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'Magestore_Webpos/js/model/customer/customer',
    ]);
    
define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/model/customer/customer-factory',
        'Magestore_Webpos/js/view/base/list/collection-list',
        'Magestore_Webpos/js/model/customer/complain',
        'Magestore_Webpos/js/action/customer/select-customer',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/directory/country',
        'Magestore_Webpos/js/helper/staff',
        'Magestore_Webpos/js/helper/alert',
        'Magestore_Webpos/js/helper/general',
        'mage/translate',
        'Magestore_Webpos/js/region-updater'
    ],
    function (
        $,
        ko,
        ViewManager,
        CustomerFactory,
        listAbstract,
        complainModel,
        selectCustomer,
        eventManager,
        countryModel,
        staffHelper,
        alertHelper,
        Helper,
        $t
    ) {
        "use strict";

        return listAbstract.extend({
            /* Ko JS for customer*/
            items: ko.observableArray([]),
            columns: ko.observableArray([]),
            isShowHeader: false,
            isSearchable: true,
            isShowCreateForm: ko.observable(false),
            isSearching: ko.observable(false),
            newCustomer: ko.observable(false),
            /* End ko JS for customer list*/

            /* Set Template For Customer List*/
            defaults: {
                template: 'Magestore_Webpos/customer/customer-list'
            },

            /* Automatically run when init JS*/
            initialize: function () {
                this._super();
                this.listenOnHoldAfterEvent();
            },

            /* Prepare Collection for Customer*/
            _prepareCollection: function () {
                var customerGroupOfStaff = staffHelper.getCustomerGroupOfStaff();
                var getCustomerGroupOfStaffNumber = staffHelper.getCustomerGroupOfStaffNumber();

                if(this.collection == null) {
                    if ($.inArray('all', customerGroupOfStaff) > -1) {
                        this.collection = CustomerFactory.get().getCollection().setOrder('full_name', 'ASC');
                    } else {
                        this.collection = CustomerFactory.get().getCollection()
                            .setOrder('full_name', 'ASC')
                            .addFieldToFilter('group_id',getCustomerGroupOfStaffNumber,'in');
                    }
                }
                var mode = (Helper.isUseOnline('customers'))?'online':'offline';
                this.collection.setMode(mode);
                this.pageSize = 20;
                if ($.inArray('all', customerGroupOfStaff) == -1) {
                    this.collection = CustomerFactory.get().getCollection()
                        .setOrder('full_name', 'ASC')
                        .addFieldToFilter('group_id',getCustomerGroupOfStaffNumber,'in');
                }
                this.collection.setPageSize(this.pageSize);
                this.collection.setCurPage(this.curPage);
                this.collection.setOrder('full_name', 'ASC');
                if (this.searchKey) {
                    this.collection.addFieldToFilter(
                        [
                            ['email', "%" + this.searchKey + "%", 'like'],
                            ['telephone', "%" + this.searchKey + "%", 'like'],
                            ['full_name', "%" + this.searchKey + "%", 'like']
                        ]
                    );
                }
            },

            /* Prepare Items for list*/
            _prepareItems: function () {
                var self = this;
                if (this.refresh) {
                    this.curPage = 1;
                }
                var deferred = self.getCollection().load();
                self.startLoading();
                self.isSearching(true);
                deferred.done(function (data) {
                    self.finishLoading();
                    self.isSearching(false);
                    self.setItems(data.items);
                    var count = data.items.length;
                    if(count==0 && self.searchKey){
                        alertHelper({title:'Error', content: $t('Can not find any item with key %1').replace('%1', '\<b>'+self.searchKey+'\</b>')});
                    } else {
                        if (!self.newCustomer() && data.items.length && !$('.list-customer .selected').length) {
                            selectCustomer(data.items[0]);
                        } else if (self.newCustomer()){
                            $.each(data.items, function (index, item) {
                                if (item.id == self.newCustomer()) {
                                    selectCustomer(item);
                                    return false;
                                }
                            });
                        }
                        self.newCustomer(false);
                    }
                });
                deferred.fail(function (error) {

                });
            },

            /* Load One Customer*/
            loadItem: function(data){
                selectCustomer(data);
            },

            /* Get Selected Customer Id*/
            getSelectId: ko.pureComputed(function () {
                return CustomerFactory.get().customerSelected() ? CustomerFactory.get().customerSelected() : null;
            }),
            

            /* Show Address Popup Or Not*/
            isShowAddress: ko.pureComputed(function () {
                return ViewManager.getSingleton('view/customer/customer-view').isShowAddress();
            }),

            /* Hide Address*/
            hideAddress: function () {
                ViewManager.getSingleton('view/customer/customer-view').isShowAddress(false);
            },

            /* Hide Overlay*/
            hideOverlay: function () {
                $('.notification-bell').show();
                $('#c-button--push-left').show();
            },
            /* Reset Form*/
            resetFormInfo: function (form) {
                document.getElementById(form).reset();
            },
            /* Show Overlay*/
            showOverlay: function () {
                $('.wrap-backover').show();
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            },
            
            /* Show Create Form Or Not*/
            showCreateForm: function () {
                ViewManager.getSingleton('view/customer/customer-view').isShowCreateForm(true);
                this.resetFormInfo('form-add-customer-customer');
                this.showOverlay();
            },

            /* Get Status Sync Or Not*/
            getStatus: function (data) {
                var customerId = data.id.toString();
                if (customerId.indexOf('notsync') > -1) {
                    return 'notsync';
                } else {
                    return 'sync';
                }
            },
            
            listenOnHoldAfterEvent: function () {
                var self = this;
                eventManager.observer('customer_list_show_container_after', function (event, eventData) {
                    self._render();
                });
            }            
        });
    }
);
