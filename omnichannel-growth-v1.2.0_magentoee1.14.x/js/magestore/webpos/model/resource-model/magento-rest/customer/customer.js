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
        'model/resource-model/magento-rest/abstract'
    ],
    function ($, restAbstract) {
        "use strict";

        return restAbstract.extend({
            interfaceName:'customer',
            type:'customer',
            keyPath: 'id',
            initialize: function () {
                this._super();
                this.setSearchApiUrl('/webpos/customers/find');
                this.setLoadApi('/webpos/customers/load?customerId=');
                this.setCreateApiUrl('/webpos/customer/create');
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

                        // if (addressId.indexOf('nsync') !== -1) {
                        //     delete value['id'];
                        // }

                        if (value['region_id'] == '') {
                            value['region_id'] = 0;
                        }
                        newAddressData.push(value);

                    });
                }

                customer.addresses = newAddressData;

                if(this.interfaceName){
                    postData[this.interfaceName] = this.prepareSaveData(customer);
                }
                else{
                    postData =  this.prepareSaveData(customer);
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