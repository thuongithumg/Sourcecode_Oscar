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