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
            interfaceName:'complain',
            initialize: function () {
                this._super();
                this.setSearchApiUrl('/webpos/customers/complain/search');
                this.setCreateApiUrl('/webpos/customers/complain');
                this.setUpdateApiUrl('/webpos/customers/complain/');
            }
        });
    }
);