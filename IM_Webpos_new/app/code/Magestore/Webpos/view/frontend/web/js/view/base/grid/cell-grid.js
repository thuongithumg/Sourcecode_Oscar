/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'ko',
            'Magestore_Webpos/js/view/base/grid/collection-grid',
        ],
        function ($, ko, colGrid) {
            "use strict";

            return colGrid.extend({
                defaults: {
                    template: 'Magestore_Webpos/base/grid/cell-grid',
                },                
                initialize: function () {
                    this._super();
                },
            });
        }
);