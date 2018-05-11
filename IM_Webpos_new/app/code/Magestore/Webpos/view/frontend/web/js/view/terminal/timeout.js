/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/model/event-manager'
    ],
    function ($, ko, Component, Event) {
        "use strict";

        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/terminal/timeout'
            },

            initialize: function () {
                this._super();

            },

            afterRender: function () {


            },

            cancelSaleRequest: function () {
                Event.dispatch('cancel_sales_request', {})
            }
        });
    }
);