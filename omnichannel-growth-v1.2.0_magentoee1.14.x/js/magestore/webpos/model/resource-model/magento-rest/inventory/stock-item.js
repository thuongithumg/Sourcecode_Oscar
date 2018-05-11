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
        'model/resource-model/magento-rest/abstract'
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