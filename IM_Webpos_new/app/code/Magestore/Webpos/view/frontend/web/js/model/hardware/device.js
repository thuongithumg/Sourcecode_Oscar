/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/action/hardware/connect'
    ],
    function ($, connect) {
        "use strict";
        var DEVICE_BARCODE = 'barcode';
        return {
            processConnect: function () {
                //setInterval(this.sendRequest, 500, 'barcode');
            },

            sendRequest: function (type) {
                connect(type);
            }
        }
    }
);