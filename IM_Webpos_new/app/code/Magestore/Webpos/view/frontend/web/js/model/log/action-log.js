/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/indexed-db/log/action-log',
        'Magestore_Webpos/js/model/collection/log/action-log'
    ],
    function ($, ko, modelAbstract, logIndexedDb, logCollection) {
        "use strict";
        return modelAbstract.extend({
            initialize: function () {
                this._super();
                this.setResource('', logIndexedDb());
                this.setResourceCollection(logCollection());
            }
        });
    }
);