/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/customer/customer',
        'Magestore_Webpos/js/model/resource-model/indexed-db/customer/customer',
        'Magestore_Webpos/js/model/collection/customer/customer',
        'Magestore_Webpos/js/helper/general'
    ],
    function ($, ko, modelAbstract, customerRest, customerIndexedDb, customerCollection, Helper) {
        "use strict";
        var customerSelected = ko.observable(null);
        return modelAbstract.extend({
            customerSelected: customerSelected,
            event_prefix: 'customer',
            sync_id:'customer',
            initialize: function () {
                this._super();
                this.setResource(customerRest(), customerIndexedDb());
                this.setResourceCollection(customerCollection());
                this.mode = (Helper.isUseOnline('customers'))?'online':'offline';
            },
            setSelectedCustomer: function(customerId) {
                customerSelected(customerId);
            },
            getSelectedCustomer: function() {
                return customerSelected;
            },

            prepareBeforeSave: function () {
                var data = this.data;
                delete data['group_label'];
                delete data['telephone'];
                delete data['full_name'];
                //delete data['subscriber_status'];
                delete data['additional_attributes'];
                delete data['indexeddb_id'];
                if (typeof data.addresses !='undefined') {
                    $.each(data.addresses, function (index, value) {
                         if (typeof value.address_type != 'undefined') {
                             delete value['address_type'];
                             data.addresses[index] = value;
                         }
                    });
                }
                this.data = data;
                return this;
            },

            _saveBefore: function(){
                this._super();
                if(this.mode == 'online'){
                    this.prepareBeforeSave();
                }
            }
        });
    }
);