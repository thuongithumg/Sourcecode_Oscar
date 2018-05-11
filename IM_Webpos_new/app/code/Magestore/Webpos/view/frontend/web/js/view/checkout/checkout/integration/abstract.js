/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/abstract'
    ],
    function ($,ko, Abstract) {
        "use strict";
        return Abstract.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/checkout/integration/default'
            },
            visible: ko.observable(true),
            initialize: function () {
                this._super();
                this.visible(true);
                //this.setModel('model/abstract');
            }
        });
    }
);
