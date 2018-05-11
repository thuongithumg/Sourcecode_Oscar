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
        'ko',
        'model/abstract',
        'model/resource-model/magento-rest/customer/customer',
        'model/resource-model/indexed-db/customer/customer',
        'model/collection/customer/customer'
    ],
    function ($, ko, modelAbstract, customerRest, customerIndexedDb, customerCollection) {
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
            }
        });
    }
);