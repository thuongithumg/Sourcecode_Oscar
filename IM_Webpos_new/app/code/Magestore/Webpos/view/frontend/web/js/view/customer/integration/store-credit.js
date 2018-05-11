/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
    
define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/customer/integration/abstract',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/integration/store-credit-factory',
    ],
    function ($,ko, Abstract, Helper, StoreCreditFactory) {
        "use strict";
        return Abstract.extend({
            defaults: {
                template: 'Magestore_Webpos/customer/integration/storecredit'
            },
            initialize: function () {
                this._super();
                this.model = StoreCreditFactory.get();
                if(!this.addedData && Helper.isStoreCreditEnable()){
                    this.initData();
                }
            }
        });
    }
);
