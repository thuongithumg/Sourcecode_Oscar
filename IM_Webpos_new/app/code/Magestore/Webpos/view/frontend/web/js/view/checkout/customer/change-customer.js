/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
    
define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/model/customer/customer-factory',
        'Magestore_Webpos/js/view/base/list/collection-list',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/action/checkout/select-customer-checkout',
        'Magestore_Webpos/js/action/checkout/select-billing-address',
        'Magestore_Webpos/js/action/checkout/select-shipping-address',
        'Magestore_Webpos/js/helper/staff',
        'Magestore_Webpos/js/helper/general',
        'mage/translate',
        
    ],
    function ($,
              ko,
              ViewManager,
              CustomerFactory,
              colGrid,
              CheckoutModel,
              selectCustomerToCheckout,
              selectBilling,
              selectShipping,
              staffHelper,
              Helper,
              Translate) {
        "use strict";

        return colGrid.extend({
            items: ko.observableArray([]),
            columns: ko.observableArray([]),

            /* Template for koJS*/
            defaults: {
                template: 'Magestore_Webpos/checkout/customer/change-customer'
            },

            /* Assign customer model*/
            initialize: function () {
                this._super();
                this.linstenEventToRender();
                this.mode = (Helper.isUseOnline('customers'))?'online':'offline';
            },

            startLoading: function() {
                this.isLoading = true;
                $('#customer-overlay').show();
            },
            finishLoading: function() {
                this.isLoading = false;
                $('#customer-overlay').hide();
            },

            /* Prepare customer collection*/
            _prepareCollection: function () {
                var customerGroupOfStaff = staffHelper.getCustomerGroupOfStaff();
                var getCustomerGroupOfStaffNumber = staffHelper.getCustomerGroupOfStaffNumber();
                if (this.collection == null) {
                    if ($.inArray('all', customerGroupOfStaff) > -1) {
                        this.collection = CustomerFactory.get().getCollection().setOrder('full_name', 'ASC');
                    } else {
                        this.collection = CustomerFactory.get().getCollection()
                            .setOrder('full_name', 'ASC')
                            .addFieldToFilter('group_id', getCustomerGroupOfStaffNumber, 'in');
                    }
                }
                this.mode = (Helper.isUseOnline('customers'))?'online':'offline';
                this.collection.setMode(this.mode);
                this.pageSize = 20;
                if ($.inArray('all', customerGroupOfStaff) == -1) {
                    this.collection = CustomerFactory.get().getCollection()
                        .setOrder('full_name', 'ASC')
                        .addFieldToFilter('group_id', getCustomerGroupOfStaffNumber, 'in');
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

            /* Select customer to checkout */
            selectCustomer: function (object) {
                var formEditCustomer = $('#form-edit-customer');
                var customerModel = CustomerFactory.get().setMode(this.mode).load(object.id);
                customerModel.done(function (data) {
                    selectCustomerToCheckout(data);
                    var addressData = data.addresses;
                    var isSetBilling = false;
                    var isSetShipping = false;
                    if(addressData && addressData.length > 0) {
                        $.each(addressData, function (index, value) {
                            if (value.default_billing) {
                                ViewManager.getSingleton('view/checkout/customer/edit-customer').billingAddressId(value.id);
                                ViewManager.getSingleton('view/checkout/customer/edit-customer').setBillingPreviewData(value);
                                ViewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewBilling(true);
                                isSetBilling = true;
                            }
                            if (value.default_shipping) {
                                ViewManager.getSingleton('view/checkout/customer/edit-customer').shippingAddressId(value.id);
                                ViewManager.getSingleton('view/checkout/customer/edit-customer').setShippingPreviewData(value);
                                ViewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewShipping(true);
                                isSetShipping = true;
                            }
                        });
                    }
                    if (!isSetBilling) {
                        ViewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewBilling(false);
                    }

                    if (!isSetShipping) {
                        ViewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewShipping(false);
                    }

                    $('#popup-change-customer').removeClass('fade-in');
                    $('.wrap-backover').hide();
                    $('.notification-bell').show();
                    $('#c-button--push-left').show();
                    // formEditCustomer.removeClass('fade');
                    // formEditCustomer.addClass('fade-in');
                    // formEditCustomer.addClass('show');
                    ViewManager.getSingleton('view/checkout/checkout/payment').collection.reset();
                    ViewManager.getSingleton('view/checkout/checkout/payment').saveDefaultPaymentMethod();

                });
            },

            /* Show create customer form */
            showCreateCustomerForm: function () {
                var addCustomerCheckout = $('#form-customer-add-customer-checkout');
                ViewManager.getSingleton('view/checkout/customer/add-shipping-address').isSameBillingShipping(true);
                $('#popup-change-customer').removeClass('fade-in');
                addCustomerCheckout.addClass('fade-in');
                addCustomerCheckout.addClass('show');
                addCustomerCheckout.removeClass('fade');
                $('.wrap-backover').show();
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            },

            /* Use guest customer to checkout*/
            useGuestCustomer: function () {
                var data= {
                    id: 0,
                    full_name: Translate('Guest')
                };
                selectCustomerToCheckout(data);
                selectBilling(0);
                selectShipping(0);
                $('#popup-change-customer').removeClass('fade-in');
                $('.wrap-backover').hide();
                $('.notification-bell').show();
                $('#c-button--push-left').show();
            },
            /**
             * Only render list when show popup
             */
            linstenEventToRender: function(){
                var self = this;
                Helper.observerEvent('checkout_customer_list_show_after', function(){
                    self._render();
                })
            }
        });
    }
);