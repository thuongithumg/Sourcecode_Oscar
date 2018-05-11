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
                this._super();
                this.setLoadApi('/webpos/categories/:categoryId?categoryId=');
                //this.setCreateApiUrl('/webpos/products');
                //this.setUpdateApiUrl('/webpos/customers/');
                //this.setDeleteApiUrl('/webpos/customers/:customerId?customerId=');
                this.setSearchApiUrl('/webpos/categories')
            }
        });
    }
);