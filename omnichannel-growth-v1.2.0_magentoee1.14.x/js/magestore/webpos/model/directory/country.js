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
        'model/abstract',
        'model/resource-model/magento-rest/directory/currency',
        'model/resource-model/indexed-db/directory/country',
        'model/collection/directory/country'
    ],
    function ($, modelAbstract, customerRest, customerIndexedDb, customerCollection) {
        "use strict";
        return modelAbstract.extend({
            sync_id:'country',
            initialize: function () {
                this._super();
                this.setResource(customerRest(), customerIndexedDb());
                this.setResourceCollection(customerCollection());
            }
        });
    }
);