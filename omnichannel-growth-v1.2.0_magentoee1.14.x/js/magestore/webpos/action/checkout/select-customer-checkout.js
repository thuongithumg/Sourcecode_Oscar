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

/*global define*/
define(
    [
        'ko',
        'model/customer/current-customer',
        'model/checkout/cart',
        'eventManager',
        'dataManager',
        'model/appConfig',
        'model/checkout/shopping-cart'
    ],
    function (
              ko,
              currentCustomer,
              CartModel,
              eventManager,
              DataManager,
              AppConfig,
              ShoppingCartModel
              ) {
        'use strict';


        var SelectCustomer = function (data) {
            currentCustomer.setCustomerId(data.id);
            currentCustomer.setCustomerEmail(data.email);
            currentCustomer.setFullName(data.full_name);
            currentCustomer.setData(data);
            CartModel.addCustomer(getCustomerData(data));
            /* fire select_customer_after event*/
            var eventData = {'customer': data};
            eventManager.dispatch('checkout_select_customer_after', eventData);
        }

        function getCustomerData(object) {
            var keys = ["id", "email", "firstname", "lastname", "full_name", "group_id", "telephone"];
            var data = {};
            ko.utils.arrayForEach(keys, function (key) {
                data[key] = (typeof object[key] != "undefined") ? object[key] : "";
            });
            return data;
        }
        var selectedCustomer = false;
        var customerData = CartModel.getCustomerInitParams();
        if (customerData) {
            if (customerData.data && customerData.data.id) {
                SelectCustomer(customerData.data);
                selectedCustomer = true;
            }
        }
         var observedEvent = false;
        eventManager.observer(AppConfig.EVENT.DATA_MANAGER_SET_DATA_AFTER, function(){
            var defaultCustomer = DataManager.getData('default_customer');
            if(!observedEvent){
                observedEvent = true;
                eventManager.observer('cart_remove_customer_after', function(){
                    if(defaultCustomer){
                        SelectCustomer(defaultCustomer);
                        selectedCustomer = true;
                    }
                });
            }
            if(!selectedCustomer && defaultCustomer){
                SelectCustomer(defaultCustomer);
                selectedCustomer = true;
            }
        });

        return SelectCustomer;
    }
);
