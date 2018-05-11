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
            initialize: function () {
                this.keyPath = 'item_id';
                this.interfaceName = 'stockItem';
                this.interfaceNames = 'stockItems';
                this._super();
                //this.setLoadApi('/webpos/stockItems/');
                //this.setCreateApiUrl('/webpos/products');
                this.setUpdateApiUrl('/webpos/stockItems/');
                this.setMassUpdateApiUrl('/webpos/stockItems/');
                //this.setDeleteApiUrl('/webpos/customers/:customerId?customerId=');
                this.setSearchApiUrl('/webpos/stockItems')
            }
        });
    }
);