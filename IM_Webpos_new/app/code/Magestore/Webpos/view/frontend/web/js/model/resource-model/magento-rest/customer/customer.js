/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/resource-model/magento-rest/abstract'
    ],
    function ($, onlineAbstract) {
        "use strict";

        return onlineAbstract.extend({
            interfaceName:'customer',
            type:'customer',
            keyPath: 'id',
            initialize: function () {
                this._super();
                this.setLoadApi('/webpos/customers/:id?customerId=');
                this.setCreateApiUrl('/webpos/customers');
                this.setUpdateApiUrl('/webpos/customers/');
                this.setDeleteApiUrl('/webpos/customers/:customerId?customerId=');
                this.setSearchApiUrl('/webpos/customers/search');
            },
            /* save*/
            save : function(model, deferred){
                if(!deferred) {
                    deferred = $.Deferred();
                }
                var postData = {};
                var customer = model.getData();
                var addressData = customer.addresses;
                var newAddressData = [];

                if (addressData instanceof Array) {
                    $.each(addressData, function (index, value) {
                        var addressId = value.id.toString();

                        if (addressId.indexOf('nsync') !== -1) {
                            delete value['id'];
                        }

                        if (value['region_id'] == '') {
                            value['region_id'] = 0;
                        }
                        newAddressData.push(value);

                    });
                }

                customer.addresses = newAddressData;

                if(this.interfaceName){
                    var customerData = this.prepareSaveData(customer);
                    customerData.id = (parseInt(customerData.id))?parseInt(customerData.id):0;
                    postData[this.interfaceName] = customerData;
                }
                else{
                    postData =  this.prepareSaveData(customer);
                    postData.id = (parseInt(postData.id))?parseInt(postData.id):0;
                }

                this.callRestApi(
                    this.createApiUrl,
                    'post',
                    {},
                    postData,
                    deferred,
                    this.interfaceName + '_afterSave'
                );
                return deferred;
            }
        });
    }
);