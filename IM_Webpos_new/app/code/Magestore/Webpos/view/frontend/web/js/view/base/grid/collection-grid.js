/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'ko',
            'Magestore_Webpos/js/view/base/list/collection-list',
        ],
        function ($, ko, collectionList) {
            "use strict";

            return collectionList.extend({
                defaults: {
                    template: 'Magestore_Webpos/base/grid/abstract',
                },
                
                initialize: function () {
                    this.isShowHeader = true;
                    this._super();
                },
            });
        }
);