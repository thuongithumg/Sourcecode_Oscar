/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    ['jquery'],
    function ($) {
        'use strict';

        var containerId = '.loading-mask';

        return {

            /**
             * Start full page loader action
             */
            startLoader: function () {
                $(containerId).show();
                $('#c-mask').hide();
            },

            /**
             * Stop full page loader action
             */
            stopLoader: function () {
                $(containerId).hide();
                $('#c-mask').show();
            }
        };
    }
);
