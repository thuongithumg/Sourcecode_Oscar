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
                this.keyPath = 'location_id';
                this.interfaceName = 'location';
                this.interfaceNames = 'locations';
                this._super();
                this.setLoadApi('/webpos/locations/');
                //this.setCreateApiUrl('/webpos/locations');
                //this.setUpdateApiUrl('/webpos/locations/');
                //this.setMassUpdateApiUrl('/webpos/locations/');
                //this.setDeleteApiUrl('/webpos/locations/');
                this.setSearchApiUrl('/webpos/locations')
            }
        });
    }
);