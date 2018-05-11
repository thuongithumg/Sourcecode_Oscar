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
                this.keyPath = 'rate_id';
                this.interfaceName = 'rate';
                this.interfaceNames = 'rates';
                this._super();
                this.setSearchApiUrl('/webpos/integration/getPointRates/')
            }
        });
    }
);