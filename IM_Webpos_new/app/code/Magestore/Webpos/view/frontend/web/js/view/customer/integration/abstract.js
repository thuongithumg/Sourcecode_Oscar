/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/customer/customer-factory',
        'Magestore_Webpos/js/view/abstract',
        'Magestore_Webpos/js/helper/general',
        
    ],
    function ($, ko, CustomerFactory, Abstract, Helper) {
        "use strict";
        return Abstract.extend({
            defaults: {
                template: ''
            },
            visible: ko.observable(false),
            initialize: function () {
                this._super();
            },
            initData: function(){
                var self = this;
                self.addedData = true;
                self.balance = ko.pureComputed(function(){
                    return self.convertAndFormatPrice(self.model.balance());
                });
                self.updatingBalance = self.model.updatingBalance;
                self.visible = ko.pureComputed(function(){
                    return (CustomerFactory.get().customerSelected())?true:false;
                });
                CustomerFactory.get().customerSelected.subscribe(function(customerId){
                    self.updateStorageBalance(customerId);
                });
                Helper.observerEvent('customer_list_show_container_after', function(){
                    if(self.visible()){
                        self.updateStorageBalance(CustomerFactory.get().customerSelected());
                    }
                })
            },
            updateBalance: function(){
                var customerId = CustomerFactory.get().customerSelected();
                if(this.updatingBalance() == false && customerId){
                    this.model.updateBalance(customerId);
                }
            },
            updateStorageBalance: function(customerId){
                this.model.loadStorageBalanceByCustomerId(customerId);
            }
        });
    }
);
