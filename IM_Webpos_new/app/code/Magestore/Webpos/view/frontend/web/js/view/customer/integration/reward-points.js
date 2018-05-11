/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'Magestore_Webpos/js/model/checkout/integration/reward-points',
    ]);
    
define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/customer/integration/abstract',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/integration/reward-points-factory',
    ],
    function ($,ko, Abstract, Helper, RewardPointFactory) {
        "use strict";
        return Abstract.extend({
            defaults: {
                template: 'Magestore_Webpos/customer/integration/rewardpoints'
            },
            initialize: function () {
                this._super();
                this.model = RewardPointFactory.get();
                if(!this.addedData && Helper.isRewardPointsEnable()){
                    this.initData();
                }
            }
        });
    }
);
