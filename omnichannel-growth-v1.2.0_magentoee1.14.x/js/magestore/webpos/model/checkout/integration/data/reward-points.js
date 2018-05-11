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
        'model/abstract',
        'model/resource-model/magento-rest/integration/rewardpoints/reward-points',
        'model/resource-model/indexed-db/integration/rewardpoints/reward-points',
        'model/collection/integration/reward-points'
    ],
    function (modelAbstract, onlineResource, offlineResource, collection) {
        "use strict";
        return modelAbstract.extend({
            sync_id:'customer_points',
            initialize: function () {
                this._super();
                this.setResource(onlineResource(), offlineResource());
                this.setResourceCollection(collection());
            }
        });
    }
);