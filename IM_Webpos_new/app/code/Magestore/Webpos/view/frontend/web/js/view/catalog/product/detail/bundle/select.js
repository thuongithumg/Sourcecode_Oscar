/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/catalog/product/detail/bundle'
    ],
    function ($,ko, bundleDetail) {
        "use strict";
        return bundleDetail.extend({
            defaults: {
                template: 'Magestore_Webpos/catalog/product/detail/bundle/select'
            },
            initialize: function () {
                this._super();
            }
        });
    }
);