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
            interfaceName:'giftvoucher_template',
            type:'giftvoucher_template',
            keyPath: 'id',
            initialize: function () {
                this._super();
                this.setSearchApiUrl('/webpos/integration/giftcard/template/search');
            },
        });
    }
);