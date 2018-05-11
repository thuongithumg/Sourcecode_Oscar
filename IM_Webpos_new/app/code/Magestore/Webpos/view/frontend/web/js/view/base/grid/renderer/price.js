/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'ko',
            'Magestore_Webpos/js/view/base/grid/renderer/abstract',
        ],
        function ($, ko, renderAbstract) {
            "use strict";
            return renderAbstract.extend({
                render: function (item) {
                    return "$" + item.price.toFixed(2);
                }
            });
        }
);